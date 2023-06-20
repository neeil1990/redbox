<?php

namespace App\Classes\SimpleHtmlDom;

include_once 'constants.php';
include_once 'HtmlNode.php';
include_once 'HtmlElement.php';

if (!defined('DEFAULT_TARGET_CHARSET')) {
    define('DEFAULT_TARGET_CHARSET', \simplehtmldom\DEFAULT_TARGET_CHARSET);
}

if (!defined('DEFAULT_BR_TEXT')) {
    define('DEFAULT_BR_TEXT', \simplehtmldom\DEFAULT_BR_TEXT);
}

if (!defined('DEFAULT_SPAN_TEXT')) {
    define('DEFAULT_SPAN_TEXT', \simplehtmldom\DEFAULT_SPAN_TEXT);
}

if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', \simplehtmldom\MAX_FILE_SIZE);
}

class HtmlDocument
{
    public $root = null;
    public $nodes = array();
    public $callback = null;
    public $lowercase = false;
    public $original_size;
    public $size;

    protected $pos;
    protected $doc;
    protected $char;

    protected $cursor;
    protected $parent;
    protected $noise = array();
    protected $token_blank = " \t\r\n";

    public $_charset = '';
    public $_target_charset = '';

    public $default_br_text = '';
    public $default_span_text = '';

    protected $block_tags = array(
        'body' => 1,
        'div' => 1,
        'form' => 1,
        'root' => 1,
        'span' => 1,
        'table' => 1
    );

    protected $optional_closing_tags = array(
        'b' => array('b' => 1),
        'dd' => array('dd' => 1, 'dt' => 1),
        'dl' => array('dd' => 1, 'dt' => 1),
        'dt' => array('dd' => 1, 'dt' => 1),
        'li' => array('li' => 1),
        'optgroup' => array('optgroup' => 1, 'option' => 1),
        'option' => array('optgroup' => 1, 'option' => 1),
        'p' => array('p' => 1),
        'rp' => array('rp' => 1, 'rt' => 1),
        'rt' => array('rp' => 1, 'rt' => 1),
        'td' => array('td' => 1, 'th' => 1),
        'th' => array('td' => 1, 'th' => 1),
        'tr' => array('td' => 1, 'th' => 1, 'tr' => 1),
    );

    public function __call($func, $args)
    {
        switch ($func) {
            case 'load_file':
                $actual_function = 'loadFile';
                break;
            case 'clear':
                return;
            default:
                trigger_error(
                    'Call to undefined method ' . __CLASS__ . '::' . $func . '()',
                    E_USER_ERROR
                );
        }

        return call_user_func_array(array($this, $actual_function), $args);
    }

    public function __construct(
        $str = null,
        $lowercase = true,
        $forceTagsClosed = true,
        $target_charset = DEFAULT_TARGET_CHARSET,
        $stripRN = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT,
        $options = 0)
    {
        if ($str) {
            if (preg_match('/^http:\/\//i', $str) || strlen($str) <= PHP_MAXPATHLEN && is_file($str)) {
                $this->loadFile($str);
            } else {
                $this->load(
                    $str,
                    $lowercase,
                    $stripRN,
                    $defaultBRText,
                    $defaultSpanText,
                    $options
                );
            }
        } else {
            $this->prepare($str, $lowercase, $defaultBRText, $defaultSpanText);
        }
        if (!$forceTagsClosed) {
            $this->optional_closing_tags = array();
        }

        $this->_target_charset = $target_charset;
    }

    public function __debugInfo()
    {
        return array(
            'root' => $this->root,
            'noise' => empty($this->noise) ? 'none' : $this->noise,
            'charset' => $this->_charset,
            'target charset' => $this->_target_charset,
            'original size' => $this->original_size
        );
    }

    public function __destruct()
    {
        if (isset($this->nodes)) {
            foreach ($this->nodes as $n) {
                $n->clear();
            }
        }
    }

    public function load($str, $lowercase = true, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT, $options = 0): HtmlDocument
    {
        $this->prepare($str, $lowercase, $defaultBRText, $defaultSpanText);

        $this->remove_noise("'(<\?)(.*?)(\?>)'s", true);
        if (count($this->noise)) {
        }

        if ($options & 1) {
            $this->remove_noise("'({\w)(.*?)(})'s", true);
        }

        $this->parse($stripRN);
        $this->root->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
        $this->parse_charset();
        unset($this->doc);

        return $this;
    }


    public function save($filepath = '')
    {
        $ret = $this->root->innertext();

        if ($filepath !== '') {
            file_put_contents($filepath, $ret, LOCK_EX);
        }

        return $ret;
    }

    public function find($selector, $idx = null, $lowercase = false)
    {
        return $this->root->find($selector, $idx, $lowercase);
    }

    public function expect($selector, $idx = null, $lowercase = false)
    {
        return $this->root->expect($selector, $idx, $lowercase);
    }

    public function dump($show_attr = true)
    {
        $this->root->dump($show_attr);
    }

    protected function prepare(
        $str, $lowercase = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT)
    {
        $this->doc = isset($str) ? trim($str) : '';
        $this->size = strlen($this->doc);
        $this->original_size = $this->size;
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = array();
        $this->nodes = array();
        $this->lowercase = $lowercase;
        $this->default_br_text = $defaultBRText;
        $this->default_span_text = $defaultSpanText;
        $this->root = new HtmlNode($this);
        $this->root->tag = 'root';
        $this->root->_[HtmlNode::HDOM_INFO_BEGIN] = -1;
        $this->root->nodetype = HtmlNode::HDOM_TYPE_ROOT;
        $this->parent = $this->root;
        if ($this->size > 0) {
            $this->char = $this->doc[0];
        }
    }

    protected function parse($trim = false)
    {
        while (true) {

            if ($this->char !== '<') {
                $content = $this->copy_until_char('<');

                if ($content !== '') {

                    if ($trim && trim($content) === '') {
                        continue;
                    }

                    $node = new HtmlNode($this);
                    ++$this->cursor;
                    $node->_[HtmlNode::HDOM_INFO_TEXT] = html_entity_decode(
                        $this->restore_noise($content),
                        ENT_QUOTES | ENT_HTML5,
                        $this->_target_charset
                    );
                    $this->link_nodes($node, false);

                }
            }

            if ($this->read_tag($trim) === false) {
                break;
            }
        }
    }

    protected function parse_charset()
    {
        $charset = null;

        if (function_exists('get_last_retrieve_url_contents_content_type')) {
            $contentTypeHeader = get_last_retrieve_url_contents_content_type();
            $success = preg_match('/charset=(.+)/', $contentTypeHeader, $matches);
            if ($success) {
                $charset = $matches[1];
            }

        }

        if (empty($charset)) {
            $el = $this->root->find('meta[http-equiv=Content-Type]', 0, true);

            if (!empty($el)) {
                $fullValue = $el->content;

                if (!empty($fullValue)) {
                    $success = preg_match(
                        '/charset=(.+)/i',
                        $fullValue,
                        $matches
                    );

                    if ($success) {
                        $charset = $matches[1];
                    }
                }
            }
        }

        if (empty($charset)) {
            if ($meta = $this->root->find('meta[charset]', 0)) {
                $charset = $meta->charset;
            }
        }

        if (empty($charset)) {
            if (function_exists('mb_detect_encoding')) {
                $encoding = mb_detect_encoding(
                    $this->doc,
                    array('UTF-8', 'CP1252', 'ISO-8859-1')
                );

                if ($encoding === 'CP1252' || $encoding === 'ISO-8859-1') {
                    try {
                        if (!iconv('CP1252', 'UTF-8', $this->doc)) {
                            $encoding = 'CP1251';
                        }
                    } catch (\Throwable $t) {
                        $encoding = 'CP1251';
                    }
                }

                if ($encoding !== false) {
                    $charset = $encoding;
                }
            }
        }

        if (empty($charset)) {
            $charset = 'UTF-8';
        }

        if ((strtolower($charset) == 'iso-8859-1')
            || (strtolower($charset) == 'latin1')
            || (strtolower($charset) == 'latin-1')) {
            $charset = 'CP1252';
        }

        return $this->_charset = $charset;
    }

    protected function read_tag($trim)
    {
        if ($this->char !== '<') {
            $this->root->_[HtmlNode::HDOM_INFO_END] = $this->cursor;

            do {
                if (isset($this->optional_closing_tags[strtolower($this->parent->tag)])) {
                    $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
                }
            } while ($this->parent = $this->parent->parent);

            return false;
        }

        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        if ($trim && strpos($this->token_blank, $this->char) !== false) { // "<   /html>"
            $this->pos += strspn($this->doc, $this->token_blank, $this->pos);
            $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        }

        if ($this->char === '/') {
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

            $tag = $this->copy_until_char('>');
            $tag = $trim ? trim($tag, $this->token_blank) : $tag;

            if ($trim && $this->char !== '>' && ($pos = strpos($tag, ' ')) !== false) {
                $tag = substr($tag, 0, $pos);
            }

            if (strcasecmp($this->parent->tag, $tag)) {
                $parent_lower = strtolower($this->parent->tag);
                $tag_lower = strtolower($tag);
                if (isset($this->optional_closing_tags[$parent_lower]) && isset($this->block_tags[$tag_lower])) {
                    $org_parent = $this->parent;

                    while (($this->parent->parent) && strtolower($this->parent->tag) !== $tag_lower) {
                        if (isset($this->optional_closing_tags[strtolower($this->parent->tag)]))
                            $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
                        $this->parent = $this->parent->parent;
                    }

                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent;

                        if ($this->parent->parent) {
                            $this->parent = $this->parent->parent;
                        }

                        $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                } elseif (($this->parent->parent) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[HtmlNode::HDOM_INFO_END] = 0;
                    $org_parent = $this->parent;

                    while (($this->parent->parent) && strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $this->parent->parent;
                    }

                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent;
                        $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                } elseif (($this->parent->parent) && strtolower($this->parent->parent->tag) === $tag_lower) {
                    $this->parent->_[HtmlNode::HDOM_INFO_END] = 0;
                    $this->parent = $this->parent->parent;
                } else {
                    return $this->as_text_node($tag);
                }
            }

            $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor - 1;

            if ($this->parent->parent) {
                $this->parent = $this->parent->parent;
            }

            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        $node = new HtmlNode($this);
        $node->_[HtmlNode::HDOM_INFO_BEGIN] = $this->cursor++;

        $tag = $this->copy_until(" />\r\n\t");

        if (isset($tag[0]) && $tag[0] === '!') {
            if (isset($tag[2]) && $tag[1] === '-' && $tag[2] === '-') {
                while (strlen($tag) > 3) {
                    $this->char = $this->doc[--$this->pos]; // previous
                    $tag = substr($tag, 0, strlen($tag) - 1);
                }

                $node->nodetype = HtmlNode::HDOM_TYPE_COMMENT;
                $node->tag = 'comment';

                $data = '';

                while (true) {
                    $data .= $this->copy_until_char('-');

                    if (($this->pos + 3) > $this->size) { // End of document
                        break;
                    } elseif (substr($this->doc, $this->pos, 3) === '-->') { // end
                        $data .= $this->copy_until_char('>');
                        break;
                    }

                    $data .= $this->char;
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                }

                if (substr($data, 0, 1) === '>') { // "<!-->"
                    $this->pos -= strlen($data);
                    $this->char = $this->doc[$this->pos];
                    $data = '';
                }

                if (substr($data, 0, 2) === '->') { // "<!--->"
                    $this->pos -= strlen($data);
                    $this->char = $this->doc[$this->pos];
                    $data = '';
                }

                $tag .= $data;
                $tag = $this->restore_noise($tag);

                $node->_[HtmlNode::HDOM_INFO_INNER] = substr($tag, 3, strlen($tag) - 5);
            } elseif (substr($tag, 1, 7) === '[CDATA[') {

                while (strlen($tag) > 8) {
                    $this->char = $this->doc[--$this->pos]; // previous
                    $tag = substr($tag, 0, strlen($tag) - 1);
                }

                $node->nodetype = HtmlNode::HDOM_TYPE_CDATA;
                $node->tag = 'cdata';

                $data = '';

                while (true) {
                    $data .= $this->copy_until_char(']');

                    if (($this->pos + 3) > $this->size) { // End of document
                        break;
                    } elseif (substr($this->doc, $this->pos, 3) === ']]>') { // end
                        $data .= $this->copy_until_char('>');
                        break;
                    }

                    $data .= $this->char;
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                }

                $tag .= $data;
                $tag = $this->restore_noise($tag);

                $node->_[HtmlNode::HDOM_INFO_INNER] = substr($tag, 8, strlen($tag) - 10);
            } else {
                $node->nodetype = HtmlNode::HDOM_TYPE_UNKNOWN;
                $node->tag = 'unknown';
            }

            $node->_[HtmlNode::HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until_char('>');

            if ($this->char === '>') {
                $node->_[HtmlNode::HDOM_INFO_TEXT] .= '>';
            }

            $this->link_nodes($node, true);
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null;
            return true;
        }

        if (!ctype_alnum(str_replace([':', '-'], '', $tag))) {
            $node->_[HtmlNode::HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until('<>');

            if ($this->char === '>') {
                $node->_[HtmlNode::HDOM_INFO_TEXT] .= '>';
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null;
            }

            $this->link_nodes($node, false);
            return true;
        }

        $node->nodetype = HtmlNode::HDOM_TYPE_ELEMENT;
        $tag_lower = strtolower($tag);
        $node->tag = ($this->lowercase) ? $tag_lower : $tag;

        if (isset($this->optional_closing_tags[$tag_lower])) { // Optional closing tag
            while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
                $this->parent->_[HtmlNode::HDOM_INFO_END] = $node->_[HtmlNode::HDOM_INFO_BEGIN] - 1;
                $this->parent = $this->parent->parent;
            }
            $node->parent = $this->parent;
        }

        $guard = 0;

        $space = array($this->copy_skip($this->token_blank), '', '');

        if ($this->char !== '/' && $this->char !== '>') {
            do {
                $name = $this->copy_until(' =/>');

                if ($name === '' && $this->char !== null && $space[0] === '') {
                    break;
                }

                if ($guard === $this->pos) {
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                    continue;
                }

                $guard = $this->pos;

                if ($this->pos >= $this->size - 1 && $this->char !== '>') {
                    $node->nodetype = HtmlNode::HDOM_TYPE_TEXT;
                    $node->_[HtmlNode::HDOM_INFO_END] = 0;
                    $node->_[HtmlNode::HDOM_INFO_TEXT] = '<' . $tag . $space[0] . $name;
                    $node->tag = 'text';
                    $this->link_nodes($node, false);
                    return true;
                }

                if ($name === '/' || $name === '') {
                    break;
                }

                $space[1] = (strpos($this->token_blank, $this->char) === false) ? '' : $this->copy_skip($this->token_blank);

                $name = $this->restore_noise($name);

                if ($this->lowercase) {
                    $name = strtolower($name);
                }

                if ($this->char === '=') {
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                    $this->parse_attr($node, $name, $space, $trim);
                } else {
                    $node->_[HtmlNode::HDOM_INFO_QUOTE][$name] = HtmlNode::HDOM_QUOTE_NO;
                    $node->attr[$name] = true;
                    if ($this->char !== '>') {
                        $this->char = $this->doc[--$this->pos];
                    }
                }

                if (!$trim && $space !== array(' ', '', '')) {
                    $node->_[HtmlNode::HDOM_INFO_SPACE][$name] = $space;
                }

                $space = array(
                    ((strpos($this->token_blank, $this->char) === false) ? '' : $this->copy_skip($this->token_blank)),
                    '',
                    ''
                );
            } while ($this->char !== '>' && $this->char !== '/');
        }

        $this->link_nodes($node, true);

        if (!$trim && $space[0] !== '') {
            $node->_[HtmlNode::HDOM_INFO_ENDSPACE] = $space[0];
        }

        $rest = ($this->char === '>') ? '' : $this->copy_until_char('>');
        $rest = ($trim) ? trim($rest) : $rest; // <html   /   >

        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        if (trim($rest) === '/') {
            if ($rest !== '') {
                if (isset($node->_[HtmlNode::HDOM_INFO_ENDSPACE])) {
                    $node->_[HtmlNode::HDOM_INFO_ENDSPACE] .= $rest;
                } else {
                    $node->_[HtmlNode::HDOM_INFO_ENDSPACE] = $rest;
                }
            }
            $node->_[HtmlNode::HDOM_INFO_END] = 0;
        }

        if ($node->tag === HtmlElement::BR) {
            $node->_[HtmlNode::HDOM_INFO_INNER] = $this->default_br_text;
        }

        if (HtmlElement::isRawTextElement($node->tag)) {
            $node->_[HtmlNode::HDOM_INFO_INNER] = '';

            while (true) {
                $node->_[HtmlNode::HDOM_INFO_INNER] .= $this->copy_until_char('<');

                if (($this->pos + strlen("</$node->tag>")) > $this->size) { // End of document
                    break;
                }

                if (substr($this->doc, $this->pos, strlen("</$node->tag")) === "</$node->tag") {
                    break;
                }

                $node->_[HtmlNode::HDOM_INFO_INNER] .= $this->char;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            }

            $this->parent = $node;
        } elseif (!HtmlElement::isVoidElement($node->tag)) {
            $innertext = $this->copy_until_char('<');

            if ($trim) {
                $innertext = ltrim($innertext);
            }

            if ($innertext !== '') {
                $node->_[HtmlNode::HDOM_INFO_INNER] = html_entity_decode(
                    $this->restore_noise($innertext),
                    ENT_QUOTES | ENT_HTML5,
                    $this->_target_charset
                );
            }

            $this->parent = $node;
        }

        return true;
    }

    protected function parse_attr($node, $name, &$space, $trim)
    {
        $is_duplicate = isset($node->attr[$name]);

        if (!$is_duplicate)
            $space[2] = (strpos($this->token_blank, $this->char) === false) ? '' : $this->copy_skip($this->token_blank);

        switch ($this->char) {
            case '"':
                $quote_type = HtmlNode::HDOM_QUOTE_DOUBLE;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null;
                $value = $this->copy_until_char('"');
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null;
                break;
            case '\'':
                $quote_type = HtmlNode::HDOM_QUOTE_SINGLE;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null;
                $value = $this->copy_until_char('\'');
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null;
                break;
            default:
                $quote_type = HtmlNode::HDOM_QUOTE_NO;
                $value = $this->copy_until(' >');
        }

        $value = $this->restore_noise($value);

        if ($trim) {
            $value = str_replace(["\r", "\n", "\t"], ' ', $value);
            $value = trim($value);
        }

        if (!$is_duplicate) {
            if ($quote_type !== HtmlNode::HDOM_QUOTE_DOUBLE) {
                $node->_[HtmlNode::HDOM_INFO_QUOTE][$name] = $quote_type;
            }
            $node->attr[$name] = html_entity_decode(
                $value,
                ENT_QUOTES | ENT_HTML5,
                $this->_target_charset
            );
        }
    }

    protected function link_nodes($node, $is_child)
    {
        $node->parent = $this->parent;
        $this->parent->nodes[] = $node;
        if ($is_child) {
            $this->parent->children[] = $node;
        }
    }

    protected function as_text_node($tag)
    {
        $node = new HtmlNode($this);
        ++$this->cursor;
        $node->_[HtmlNode::HDOM_INFO_TEXT] = '</' . $tag . '>';
        $this->link_nodes($node, false);
        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        return true;
    }

    protected function copy_skip($chars)
    {
        $pos = $this->pos;
        $len = strspn($this->doc, $chars, $pos);
        if ($len === 0) {
            return '';
        }
        $this->pos += $len;
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        return substr($this->doc, $pos, $len);
    }

    protected function copy_until($chars)
    {
        $pos = $this->pos;
        $len = strcspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        if ($len === 0) {
            return '';
        }
        return substr($this->doc, $pos, $len);
    }

    protected function copy_until_char($char)
    {
        if ($this->char === $char) {
            return '';
        }
        if ($this->char === null) {
            return '';
        }

        if (($pos = strpos($this->doc, $char, $this->pos)) === false) {
            $ret = substr($this->doc, $this->pos);
            $this->char = null;
            $this->pos = $this->size;
            return $ret;
        }

        $pos_old = $this->pos;
        $this->char = $this->doc[$pos];
        $this->pos = $pos;
        return substr($this->doc, $pos_old, $pos - $pos_old);
    }

    protected function remove_noise($pattern, $remove_tag = false)
    {
        $count = preg_match_all(
            $pattern,
            $this->doc,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        for ($i = $count - 1; $i > -1; --$i) {
            $key = '___noise___' . sprintf('% 5d', count($this->noise) + 1000);

            $idx = ($remove_tag) ? 0 : 1;
            $this->noise[$key] = $matches[$i][$idx][0];
            $this->doc = substr_replace($this->doc, $key, $matches[$i][$idx][1], strlen($matches[$i][$idx][0]));
        }

        $this->size = strlen($this->doc);

        if ($this->size > 0) {
            $this->char = $this->doc[0];
        }
    }

    public function restore_noise($text)
    {
        if (empty($this->noise)) return $text;
        $pos = 0;
        while (($pos = strpos($text, '___noise___', $pos)) !== false) {
            if (strlen($text) > $pos + 15) {
                $key = '___noise___'
                    . $text[$pos + 11]
                    . $text[$pos + 12]
                    . $text[$pos + 13]
                    . $text[$pos + 14]
                    . $text[$pos + 15];

                if (isset($this->noise[$key])) {
                    $text = substr($text, 0, $pos)
                        . $this->noise[$key]
                        . substr($text, $pos + 16);

                    unset($this->noise[$key]);
                } else {
                    $text = substr($text, 0, $pos)
                        . 'UNDEFINED NOISE FOR KEY: '
                        . $key
                        . substr($text, $pos + 16);
                }
            } else {
                $text = substr($text, 0, $pos)
                    . 'NO NUMERIC NOISE KEY'
                    . substr($text, $pos + 11);
            }
        }
        return $text;
    }

    public function search_noise($text)
    {
        foreach ($this->noise as $noiseElement) {
            if (strpos($noiseElement, $text) !== false) {
                return $noiseElement;
            }
        }
    }

    public function __toString()
    {
        return $this->root->innertext();
    }

    public function __get($name)
    {
        switch ($name) {
            case 'innertext':
            case 'outertext':
                return $this->root->innertext();
            case 'plaintext':
                return $this->root->text();
            case 'charset':
                return $this->_charset;
            case 'target_charset':
                return $this->_target_charset;
        }
    }

    public function childNodes($idx = -1)
    {
        return $this->root->childNodes($idx);
    }

    public function firstChild()
    {
        return $this->root->firstChild();
    }

    public function createElement($name, $value = null): HtmlNode
    {
        $node = new HtmlNode(null);
        $node->nodetype = HtmlNode::HDOM_TYPE_ELEMENT;
        $node->_[HtmlNode::HDOM_INFO_BEGIN] = 1;
        $node->_[HtmlNode::HDOM_INFO_END] = 1;

        if ($value !== null) {
            $node->_[HtmlNode::HDOM_INFO_INNER] = $value;
        }

        $node->tag = $name;

        return $node;
    }

    public function getElementById($id)
    {
        return $this->find("#$id", 0);
    }

    public function getElementsByTagName($name, $idx = null)
    {
        return $this->find($name, $idx);
    }

    public function loadFile($file)
    {
        $args = func_get_args();

        if (($doc = call_user_func_array('file_get_contents', $args)) !== false) {
            $this->load($doc);
        } else {
            return false;
        }
    }

    public function removeElements($searchElements)
    {
        foreach ($this->find($searchElements) as $element) {
            $element->outertext = '';
        }

        return $this->outertext;
    }
}
