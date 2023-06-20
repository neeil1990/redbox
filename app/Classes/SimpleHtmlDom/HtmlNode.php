<?php

namespace App\Classes\SimpleHtmlDom;

include_once 'constants.php';

class HtmlNode
{
    const HDOM_TYPE_ELEMENT = 1;
    const HDOM_TYPE_COMMENT = 2;
    const HDOM_TYPE_TEXT = 3;
    const HDOM_TYPE_ROOT = 5;
    const HDOM_TYPE_UNKNOWN = 6;
    const HDOM_TYPE_CDATA = 7;

    const HDOM_QUOTE_DOUBLE = 0;
    const HDOM_QUOTE_SINGLE = 1;
    const HDOM_QUOTE_NO = 3;

    const HDOM_INFO_BEGIN = 0;
    const HDOM_INFO_END = 1;
    const HDOM_INFO_QUOTE = 2;
    const HDOM_INFO_SPACE = 3;
    const HDOM_INFO_TEXT = 4;
    const HDOM_INFO_INNER = 5;
    const HDOM_INFO_OUTER = 6;
    const HDOM_INFO_ENDSPACE = 7;

    public $nodetype = self::HDOM_TYPE_TEXT;
    public $tag = 'text';
    public $attr = array();
    public $children = array();
    public $nodes = array();
    public $parent = null;
    public $_ = array();
    private $dom = null;


    public function __get($name)
    {
        if (isset($this->attr[$name])) {
            return $this->convert_text($this->attr[$name]);
        }

        switch ($name) {
            case 'outertext':
                return $this->outertext();
            case 'innertext':
                return $this->innertext();
            case 'plaintext':
                return $this->text();
            case 'xmltext':
                return $this->xmltext();
        }

        return false;
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'outertext':
                $this->_[self::HDOM_INFO_OUTER] = $value;
                break;
            case 'innertext':
                if (isset($this->_[self::HDOM_INFO_TEXT])) {
                    $this->_[self::HDOM_INFO_TEXT] = '';
                }
                $this->_[self::HDOM_INFO_INNER] = $value;
                break;
            default:
                $this->attr[$name] = $value;
        }
    }

    public function __isset($name)
    {
        switch ($name) {
            case 'innertext':
            case 'plaintext':
            case 'outertext':
                return true;
        }

        return isset($this->attr[$name]);
    }

    public function __unset($name)
    {
        if (isset($this->attr[$name])) {
            unset($this->attr[$name]);
        }
    }
    public function __call($func, $args)
    {
        switch ($func) {
            case 'children':
                $actual_function = 'childNodes';
                break;
            case 'first_child':
                $actual_function = 'firstChild';
                break;
            case 'has_child':
                $actual_function = 'hasChildNodes';
                break;
            case 'last_child':
                $actual_function = 'lastChild';
                break;
            case 'next_sibling':
                $actual_function = 'nextSibling';
                break;
            case 'prev_sibling':
                $actual_function = 'previousSibling';
                break;
            default:
                trigger_error(
                    'Call to undefined method ' . __CLASS__ . '::' . $func . '()',
                    E_USER_ERROR
                );
        }

        return call_user_func_array(array($this, $actual_function), $args);
    }

    public function __construct($dom)
    {
        if ($dom instanceof HtmlDocument) {
            $this->dom = $dom;
            $dom->nodes[] = $this;
        }
    }

    public function __debugInfo()
    {
        switch ($this->nodetype) {
            case self::HDOM_TYPE_ELEMENT:
                $nodetype = "HDOM_TYPE_ELEMENT ($this->nodetype)";
                break;
            case self::HDOM_TYPE_COMMENT:
                $nodetype = "HDOM_TYPE_COMMENT ($this->nodetype)";
                break;
            case self::HDOM_TYPE_TEXT:
                $nodetype = "HDOM_TYPE_TEXT ($this->nodetype)";
                break;
            case self::HDOM_TYPE_ROOT:
                $nodetype = "HDOM_TYPE_ROOT ($this->nodetype)";
                break;
            case self::HDOM_TYPE_CDATA:
                $nodetype = "HDOM_TYPE_CDATA ($this->nodetype)";
                break;
            case self::HDOM_TYPE_UNKNOWN:
            default:
                $nodetype = "HDOM_TYPE_UNKNOWN ($this->nodetype)";
        }

        return array(
            'nodetype' => $nodetype,
            'tag' => $this->tag,
            'attributes' => empty($this->attr) ? 'none' : $this->attr,
            'nodes' => empty($this->nodes) ? 'none' : $this->nodes
        );
    }

    public function __toString()
    {
        return $this->outertext();
    }

    public function clear()
    {
        unset($this->dom);
        unset($this->parent);
    }

    public function dump($show_attr = true, $depth = 0)
    {
        echo str_repeat("\t", $depth) . $this->tag;

        if ($show_attr && count($this->attr) > 0) {
            echo '(';
            foreach ($this->attr as $k => $v) {
                echo "[$k]=>\"$v\", ";
            }
            echo ')';
        }

        echo "\n";

        if ($this->nodes) {
            foreach ($this->nodes as $node) {
                $node->dump($show_attr, $depth + 1);
            }
        }
    }

    public function parent($parent = null)
    {
        if ($parent !== null) {
            $this->parent = $parent;
            $this->parent->nodes[] = $this;
            $this->parent->children[] = $this;
        }

        return $this->parent;
    }

    public function innertext()
    {
        if (isset($this->_[self::HDOM_INFO_INNER])) {
            $ret = $this->_[self::HDOM_INFO_INNER];
        } elseif (isset($this->_[self::HDOM_INFO_TEXT])) {
            $ret = $this->_[self::HDOM_INFO_TEXT];
        } else {
            $ret = '';
        }

        foreach ($this->nodes as $n) {
            $ret .= $n->outertext();
        }

        return $this->convert_text($ret);
    }

    public function outertext()
    {
        if ($this->tag === 'root') {
            return $this->innertext();
        }

        if ($this->dom && $this->dom->callback !== null) {
            call_user_func_array($this->dom->callback, array($this));
        }

        if (isset($this->_[self::HDOM_INFO_OUTER])) {
            return $this->convert_text($this->_[self::HDOM_INFO_OUTER]);
        }

        if (isset($this->_[self::HDOM_INFO_TEXT])) {
            return $this->convert_text($this->_[self::HDOM_INFO_TEXT]);
        }

        $ret = '';

        if (isset($this->_[self::HDOM_INFO_BEGIN])) {
            $ret = $this->makeup();
        }

        if (isset($this->_[self::HDOM_INFO_INNER]) && $this->tag !== HtmlElement::BR) {
            if (HtmlElement::isRawTextElement($this->tag)) {
                $ret .= $this->_[self::HDOM_INFO_INNER];
            } else {
                if ($this->dom && $this->dom->targetCharset) {
                    $charset = $this->dom->targetCharset;
                } else {
                    $charset = DEFAULT_TARGET_CHARSET;
                }
                $ret .= htmlentities($this->_[self::HDOM_INFO_INNER], ENT_QUOTES | ENT_SUBSTITUTE, $charset);
            }
        }

        if ($this->nodes) {
            foreach ($this->nodes as $n) {
                $ret .= $n->outertext();
            }
        }

        if (isset($this->_[self::HDOM_INFO_END]) && $this->_[self::HDOM_INFO_END] != 0) {
            $ret .= '</' . $this->tag . '>';
        }

        return $this->convert_text($ret);
    }

    protected function is_block_element($node): bool
    {
        return HtmlElement::isPalpableContent($node->tag) &&
            !HtmlElement::isMetadataContent($node->tag) &&
            !HtmlElement::isPhrasingContent($node->tag) &&
            !HtmlElement::isEmbeddedContent($node->tag) &&
            !HtmlElement::isInteractiveContent($node->tag);
    }

    public function text($trim = true): string
    {
        if (HtmlElement::isRawTextElement($this->tag)) {
            return '';
        }

        $ret = '';

        switch ($this->nodetype) {
            case self::HDOM_TYPE_COMMENT:
            case self::HDOM_TYPE_UNKNOWN:
                return '';
            case self::HDOM_TYPE_TEXT:
                $ret = $this->_[self::HDOM_INFO_TEXT];
                break;
            default:
                if (isset($this->_[self::HDOM_INFO_INNER])) {
                    $ret = $this->_[self::HDOM_INFO_INNER];
                }
                break;
        }

        // Replace and collapse whitespace
        $ret = preg_replace('/\s+/u', ' ', $ret);

        // Reduce whitespace at start/end to a single (or none) space
        $ret = preg_replace('/[ \t\n\r\0\x0B\xC2\xA0]+$/u', $trim ? '' : ' ', $ret);
        $ret = preg_replace('/^[ \t\n\r\0\x0B\xC2\xA0]+/u', $trim ? '' : ' ', $ret);

        // TODO: Remove BR_TEXT customization.
        //		 It has no practical use and only makes the code harder to read.
        if ($this->dom) { // for the root node, ->dom is undefined.
            $br_text = $this->dom->default_br_text ?: DEFAULT_BR_TEXT;
        }

        foreach ($this->nodes as $n) {

            if ($this->is_block_element($n)) {
                $block = $this->convert_text($n->text($trim));

                if ($block === '') {
                    $ret = rtrim($ret) . "\n\n";
                    continue;
                }

                if ($ret === '') {
                    $ret = $block . "\n\n";
                    continue;
                }

                $ret = rtrim($ret) . "\n\n" . $block . "\n\n";
                continue;
            }

            if (strtolower($n->tag) === HtmlElement::BR) {

                if ($ret === '') {
                    // Don't start with a line break.
                    continue;
                }

                $ret .= $br_text;
                continue;
            }

            $text = $this->convert_text($n->text($trim));

            if ($text === '') {
                continue;
            }

            if ($ret === '') {
                $ret = ltrim($text);
                continue;
            }

            if (substr($ret, -1) === "\n" ||
                substr($ret, -1) === ' ' ||
                substr($ret, -strlen($br_text)) === $br_text) {
                $ret .= ltrim($text);
                continue;
            }

            $ret .= ' ' . ltrim($text);
        }

        return trim($ret);
    }

    public function xmltext()
    {
        $ret = $this->innertext();
        $ret = str_ireplace('<![CDATA[', '', $ret);
        $ret = str_replace(']]>', '', $ret);
        return $ret;
    }

    public function makeup()
    {
        // text, comment, unknown
        if (isset($this->_[self::HDOM_INFO_TEXT])) {
            return $this->_[self::HDOM_INFO_TEXT];
        }

        $ret = '<' . $this->tag;

        foreach ($this->attr as $key => $val) {

            // skip removed attribute
            if ($val === null || $val === false) {
                continue;
            }

            if (isset($this->_[self::HDOM_INFO_SPACE][$key])) {
                $ret .= $this->_[self::HDOM_INFO_SPACE][$key][0];
            } else {
                $ret .= ' ';
            }

            //no value attr: nowrap, checked selected...
            if ($val === true) {
                $ret .= $key;
            } else {
                if (isset($this->_[self::HDOM_INFO_QUOTE][$key])) {
                    $quote_type = $this->_[self::HDOM_INFO_QUOTE][$key];
                } else {
                    $quote_type = self::HDOM_QUOTE_DOUBLE;
                }

                switch ($quote_type) {
                    case self::HDOM_QUOTE_SINGLE:
                        $quote = '\'';
                        break;
                    case self::HDOM_QUOTE_NO:
                        if (strpos($val, ' ') !== false ||
                            strpos($val, "\t") !== false ||
                            strpos($val, "\f") !== false ||
                            strpos($val, "\r") !== false ||
                            strpos($val, "\n") !== false) {
                            $quote = '"';
                        } else {
                            $quote = '';
                        }
                        break;
                    case self::HDOM_QUOTE_DOUBLE:
                    default:
                        $quote = '"';
                }

                $ret .= $key
                    . (isset($this->_[self::HDOM_INFO_SPACE][$key]) ? $this->_[self::HDOM_INFO_SPACE][$key][1] : '')
                    . '='
                    . (isset($this->_[self::HDOM_INFO_SPACE][$key]) ? $this->_[self::HDOM_INFO_SPACE][$key][2] : '')
                    . $quote
                    . htmlentities($val, ENT_COMPAT, $this->dom->target_charset)
                    . $quote;
            }
        }

        if (isset($this->_[self::HDOM_INFO_ENDSPACE])) {
            $ret .= $this->_[self::HDOM_INFO_ENDSPACE];
        }

        return $ret . '>';
    }

    public function find($selector, $idx = null, $lowercase = false)
    {
        $selectors = $this->parse_selector($selector);
        if (($count = count($selectors)) === 0) {
            return array();
        }
        $found_keys = array();

        for ($c = 0; $c < $count; ++$c) {
            if (($level = count($selectors[$c])) === 0) {
                return array();
            }

            if (!isset($this->_[self::HDOM_INFO_BEGIN])) {
                return array();
            }

            $head = array($this->_[self::HDOM_INFO_BEGIN] => 1);
            $cmd = ' ';

            for ($l = 0; $l < $level; ++$l) {
                $ret = array();

                foreach ($head as $k => $v) {
                    $n = ($k === -1) ? $this->dom->root : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret, $cmd, $lowercase);
                }

                $head = $ret;
                $cmd = $selectors[$c][$l][6];
            }

            foreach ($head as $k => $v) {
                if (!isset($found_keys[$k])) {
                    $found_keys[$k] = 1;
                }
            }
        }

        ksort($found_keys);

        $found = array();
        foreach ($found_keys as $k => $v) {
            $found[] = $this->dom->nodes[$k];
        }

        if (is_null($idx)) {
            return $found;
        } elseif ($idx < 0) {
            $idx = count($found) + $idx;
        }
        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    public function expect($selector, $idx = null, $lowercase = false)
    {
        return $this->find($selector, $idx, $lowercase) ?: null;
    }

    protected function seek($selector, &$ret, $parent_cmd, $lowercase = false)
    {
        list($ps_selector, $tag, $ps_element, $id, $class, $attributes, $cmb) = $selector;
        $nodes = array();

        if ($parent_cmd === ' ') {
            $end = (!empty($this->_[self::HDOM_INFO_END])) ? $this->_[self::HDOM_INFO_END] : 0;
            if ($end == 0 && $this->parent) {
                $parent = $this->parent;
                while ($parent !== null && !isset($parent->_[self::HDOM_INFO_END])) {
                    $end -= 1;
                    $parent = $parent->parent;
                }
                $end += $parent->_[self::HDOM_INFO_END];
            }

            if ($end === 0) {
                $end = count($this->dom->nodes);
            }

            $nodes_start = $this->_[self::HDOM_INFO_BEGIN] + 1;

            $nodes = array_intersect_key(
                $this->dom->nodes,
                array_flip(range($nodes_start, $end))
            );
        } elseif ($parent_cmd === '>') {
            $nodes = $this->children;
        } elseif ($parent_cmd === '+'
            && $this->parent
            && in_array($this, $this->parent->children)) {
            $index = array_search($this, $this->parent->children, true) + 1;
            if ($index < count($this->parent->children))
                $nodes[] = $this->parent->children[$index];
        } elseif ($parent_cmd === '~'
            && $this->parent
            && in_array($this, $this->parent->children)) {
            $index = array_search($this, $this->parent->children, true);
            $nodes = array_slice($this->parent->children, $index);
        }

        foreach ($nodes as $node) {

            if (!$node->parent) {
                unset($node);
                continue;
            }

            if ($tag === 'text') {
                if ($node->tag === 'text') {
                    $ret[array_search($node, $this->dom->nodes, true)] = 1;
                }

                if (isset($node->_[self::HDOM_INFO_INNER])) {
                    $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
                }

                unset($node);
                continue;
            }

            if ($tag === 'cdata') {
                if ($node->tag === 'cdata') {
                    $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
                }
                unset($node);
                continue;
            }

            if ($tag === 'comment' && $node->tag === 'comment') {
                $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
                unset($node);
                continue;
            }

            if (!in_array($node, $node->parent->children, true)) {
                unset($node);
                continue;
            }

            $pass = true;

            if ($tag !== '' && $tag !== $node->tag && $tag !== '*') {
                $pass = false;
            }

            if ($pass && $id !== '' && !isset($node->attr['id'])) {
                $pass = false;
            }

            if ($pass && $id !== '' && isset($node->attr['id'])) {
                $node_id = explode(' ', trim($node->attr['id']))[0];

                if ($id !== $node_id) {
                    $pass = false;
                }
            }

            if ($pass && $class !== '' && is_array($class) && !empty($class)) {
                if (isset($node->attr['class'])) {
                    $node_classes = preg_replace("/[\r\n\t\s]+/u", ' ', $node->attr['class']);
                    $node_classes = trim($node_classes);
                    $node_classes = explode(' ', $node_classes);

                    if ($lowercase) {
                        $node_classes = array_map('strtolower', $node_classes);
                    }

                    foreach ($class as $c) {
                        if (!in_array($c, $node_classes)) {
                            $pass = false;
                            break;
                        }
                    }
                } else {
                    $pass = false;
                }
            }

            if ($pass
                && $attributes !== ''
                && is_array($attributes)
                && !empty($attributes)) {
                foreach ($attributes as $a) {
                    list (
                        $att_name,
                        $att_expr,
                        $att_val,
                        $att_inv,
                        $att_case_sensitivity
                        ) = $a;

                    if (is_numeric($att_name)
                        && $att_expr === ''
                        && $att_val === '') {
                        $count = 0;

                        foreach ($node->parent->children as $c) {
                            if ($c->tag === $node->tag) ++$count;
                            if ($c === $node) break;
                        }

                        if ($count === (int)$att_name) continue;
                    }

                    if ($att_inv) {
                        if (isset($node->attr[$att_name])) {
                            $pass = false;
                            break;
                        }
                    } else {
                        if ($att_name !== 'plaintext'
                            && !isset($node->attr[$att_name])) {
                            $pass = false;
                            break;
                        }
                    }

                    if ($att_expr === '') continue;

                    if ($att_name === 'plaintext') {
                        $nodeKeyValue = $node->text();
                    } else {
                        $nodeKeyValue = $node->attr[$att_name];
                    }

                    if ($lowercase) {
                        $check = $this->match(
                            $att_expr,
                            strtolower($att_val),
                            strtolower($nodeKeyValue),
                            $att_case_sensitivity
                        );
                    } else {
                        $check = $this->match(
                            $att_expr,
                            $att_val,
                            $nodeKeyValue,
                            $att_case_sensitivity
                        );
                    }

                    $check = $ps_element === 'not' ? !$check : $check;

                    if (!$check) {
                        $pass = false;
                        break;
                    }
                }
            }

            $pass = $ps_selector === 'not' ? !$pass : $pass;
            if ($pass) $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
            unset($node);
        }
    }

    protected function match($exp, $pattern, $value, $case_sensitivity)
    {
        if ($case_sensitivity === 'i') {
            $pattern = strtolower($pattern);
            $value = strtolower($value);
        }

        $pattern = preg_replace("/[\r\n\t\s]+/u", ' ', $pattern);
        $pattern = trim($pattern);

        $value = preg_replace("/[\r\n\t\s]+/u", ' ', $value);
        $value = trim($value);

        switch ($exp) {
            case '=':
                return ($value === $pattern);
            case '!=':
                return ($value !== $pattern);
            case '^=':
                return preg_match('/^' . preg_quote($pattern, '/') . '/', $value);
            case '$=':
                return preg_match('/' . preg_quote($pattern, '/') . '$/', $value);
            case '*=':
                return preg_match('/' . preg_quote($pattern, '/') . '/', $value);
            case '|=':
                return strpos($value, $pattern) === 0;
            case '~=':
                return in_array($pattern, explode(' ', trim($value)), true);
        }

        return false;
    }

    protected function parse_selector($selector_string): array
    {
        $pattern = "/(?::(\w+)\()?([\w:*-]*)(?::(\w+)\()?(?:#([\w-]+))?(?:|\.([\w.-]+))?((?:\[@?!?[\w:-]+(?:[!*^$|~]?=(?![\"']).*?(?![\"'])|[!*^$|~]?=[\"'].*?[\"'])?(?:\s*?[iIsS]?)?])+)?\)?\)?([\/, >+~]+)/is";

        preg_match_all(
            $pattern,
            trim($selector_string) . ' ',
            $matches,
            PREG_SET_ORDER
        );

        $selectors = array();
        $result = array();

        foreach ($matches as $m) {
            $m[0] = trim($m[0]);

            if ($m[0] === '' || $m[0] === '/' || $m[0] === '//') {
                continue;
            }

            array_shift($m);

            if ($this->dom->lowercase) {
                $m[1] = strtolower($m[1]);
            }

            if ($m[4] !== '') {
                $m[4] = explode('.', $m[4]);
            }

            if ($m[5] !== '') {
                preg_match_all(
                    "/\[@?(!?[\w:-]+)(?:([!*^$|~]?=)((?![\"']).*?(?![\"'])|[\"'].*?[\"']))?(?:\s+?([iIsS])?)?]/is",
                    trim($m[5]),
                    $attributes,
                    PREG_SET_ORDER
                );

                $m[5] = array();

                foreach ($attributes as $att) {
                    if (trim($att[0]) === '') {
                        continue;
                    }

                    if (isset($att[3]) && $att[3] !== '' && ($att[3][0] === '"' || $att[3][0] === "'")) {
                        $att[3] = substr($att[3], 1, strlen($att[3]) - 2);
                    }

                    $inverted = (isset($att[1][0]) && $att[1][0] === '!');
                    $m[5][] = array(
                        $inverted ? substr($att[1], 1) : $att[1], // Name
                        (isset($att[2])) ? $att[2] : '',
                        (isset($att[3])) ? $att[3] : '',
                        $inverted,
                        (isset($att[4])) ? strtolower($att[4]) : '',
                    );
                }
            }

            if ($m[6] !== '' && trim($m[6]) === '') {
                $m[6] = ' ';
            } else {
                $m[6] = trim($m[6]);
            }

            if ($is_list = ($m[6] === ',')) {
                $m[6] = '';
            }

            $result[] = $m;

            if ($is_list) {
                $selectors[] = $result;
                $result = array();
            }
        }

        if (count($result) > 0) {
            $selectors[] = $result;
        }
        return $selectors;
    }

    public function convert_text($text)
    {
        $converted_text = $text;

        $sourceCharset = '';
        $targetCharset = '';

        if ($this->dom) {
            $sourceCharset = strtoupper($this->dom->_charset);
            $targetCharset = strtoupper($this->dom->_target_charset);
        }

        if ($sourceCharset !== '' &&
            $targetCharset !== '' &&
            $sourceCharset !== $targetCharset &&
            !($targetCharset === 'UTF-8' && self::is_utf8($text))) {
            $converted_text = iconv($sourceCharset, $targetCharset, $text);
        }

        // Let's make sure that we don't have that silly BOM issue with any of the utf-8 text we output.
        if ($targetCharset === 'UTF-8') {
            if (substr($converted_text, 0, 3) === "\xef\xbb\xbf") {
                $converted_text = substr($converted_text, 3);
            }
        }

        return $converted_text;
    }

    static function is_utf8($str): bool
    {
        if (extension_loaded('mbstring')) {
            return mb_detect_encoding($str, ['UTF-8'], true) === 'UTF-8';
        }

        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if ($c > 128) {
                if (($c >= 254)) {
                    return false;
                } elseif ($c >= 252) {
                    $bits = 6;
                } elseif ($c >= 248) {
                    $bits = 5;
                } elseif ($c >= 240) {
                    $bits = 4;
                } elseif ($c >= 224) {
                    $bits = 3;
                } elseif ($c >= 192) {
                    $bits = 2;
                } else {
                    return false;
                }
                if (($i + $bits) > $len) {
                    return false;
                }
                while ($bits > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || $b > 191) {
                        return false;
                    }
                    $bits--;
                }
            }
        }
        return true;
    }

    public function save($filepath = '')
    {
        $ret = $this->outertext();

        if ($filepath !== '') {
            file_put_contents($filepath, $ret, LOCK_EX);
        }

        return $ret;
    }

    public function addClass($class)
    {
        if (is_string($class)) {
            $class = explode(' ', $class);
        }

        if (is_array($class)) {
            foreach ($class as $c) {
                if (isset($this->class)) {
                    if ($this->hasClass($c)) {
                        continue;
                    } else {
                        $this->class .= ' ' . $c;
                    }
                } else {
                    $this->class = $c;
                }
            }
        }
    }

    public function hasClass($class): bool
    {
        if (is_string($class)) {
            if (isset($this->class)) {
                return in_array($class, explode(' ', $this->class), true);
            }
        }

        return false;
    }

    public function removeClass($class = null)
    {
        if (!isset($this->class)) {
            return;
        }

        if (is_null($class)) {
            $this->removeAttribute('class');
            return;
        }

        if (is_string($class)) {
            $class = explode(' ', $class);
        }

        if (is_array($class)) {
            $class = array_diff(explode(' ', $this->class), $class);
            if (empty($class)) {
                $this->removeAttribute('class');
            } else {
                $this->class = implode(' ', $class);
            }
        }
    }

    public function getAttribute($name)
    {
        return $this->$name;
    }

    public function setAttribute($name, $value)
    {
        $this->$name = $value;
    }

    public function hasAttribute($name): bool
    {
        return isset($this->$name);
    }

    public function removeAttribute($name)
    {
        unset($this->$name);
    }

    public function remove()
    {
        if ($this->parent) {
            $this->parent->removeChild($this);
        }
    }

    public function removeChild($node)
    {
        foreach ($node->children as $child) {
            $node->removeChild($child);
        }

        foreach ($node->nodes as $entity) {
            $enidx = array_search($entity, $node->nodes, true);
            $edidx = array_search($entity, $node->dom->nodes, true);

            if ($enidx !== false) {
                unset($node->nodes[$enidx]);
            }

            if ($edidx !== false) {
                unset($node->dom->nodes[$edidx]);
            }
        }

        $nidx = array_search($node, $this->nodes, true);
        $cidx = array_search($node, $this->children, true);
        $didx = array_search($node, $this->dom->nodes, true);

        if ($nidx !== false) {
            unset($this->nodes[$nidx]);
        }

        $this->nodes = array_values($this->nodes);

        if ($cidx !== false) {
            unset($this->children[$cidx]);
        }

        $this->children = array_values($this->children);

        if ($didx !== false) {
            unset($this->dom->nodes[$didx]);
        }

        $node->clear();
    }

    public function getElementById($id)
    {
        return $this->find("#$id", 0);
    }

    public function getElementsByTagName($name, $idx = null)
    {
        return $this->find($name, $idx);
    }

    public function parentNode()
    {
        return $this->parent();
    }

    public function childNodes($idx = -1)
    {
        if ($idx === -1) {
            return $this->children;
        }

        if (isset($this->children[$idx])) {
            return $this->children[$idx];
        }

        return null;
    }

    public function firstChild()
    {
        if (count($this->children) > 0) {
            return $this->children[0];
        }
        return null;
    }

    public function hasChildNodes(): bool
    {
        return !empty($this->children);
    }

    public function nodeName()
    {
        return $this->tag;
    }

    public function appendChild($node): HtmlNode
    {
        $node->parent = $this;
        $this->nodes[] = $node;
        $this->children[] = $node;

        if ($this->dom) {
            $children = array($node);

            while ($children) {
                $child = array_pop($children);
                $children = array_merge($children, $child->children);

                $this->dom->nodes[] = $child;
                $child->dom = $this->dom;
                $child->_[self::HDOM_INFO_BEGIN] = count($this->dom->nodes) - 1;
                $child->_[self::HDOM_INFO_END] = $child->_[self::HDOM_INFO_BEGIN];
            }

            $this->dom->root->_[self::HDOM_INFO_END] = count($this->dom->nodes) - 1;
        }

        return $this;
    }

}
