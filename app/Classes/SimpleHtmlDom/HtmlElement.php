<?php

namespace App\Classes\SimpleHtmlDom;

class HtmlElement
{
    const A = 'a';
    const ABBR = 'abbr';
    const ADDRESS = 'address';
    const AREA = 'area';
    const ARTICLE = 'article';
    const ASIDE = 'aside';
    const AUDIO = 'audio';
    const B = 'b';
    const BASE = 'base';
    const BDI = 'bdi';
    const BDO = 'bdo';
    const BLOCKQUOTE = 'blockquote';
    const BR = 'br';
    const BUTTON = 'button';
    const CANVAS = 'canvas';
    const CITE = 'cite';
    const CODE = 'code';
    const COL = 'col';
    const DATA = 'data';
    const DATALIST = 'datalist';
    const DEL = 'del';
    const DETAILS = 'details';
    const DFN = 'dfn';
    const DIV = 'div';
    const DL = 'dl';
    const EM = 'em';
    const EMBED = 'embed';
    const FIELDSET = 'fieldset';
    const FIGURE = 'figure';
    const FOOTER = 'footer';
    const FORM = 'form';
    const H1 = 'h1';
    const H2 = 'h2';
    const H3 = 'h3';
    const H4 = 'h4';
    const H5 = 'h5';
    const H6 = 'h6';
    const HEADER = 'header';
    const HGROUP = 'hgroup';
    const HR = 'hr';
    const I = 'i';
    const IFRAME = 'iframe';
    const IMG = 'img';
    const INPUT = 'input';
    const INS = 'ins';
    const KBD = 'kbd';
    const LABEL = 'label';
    const LINK = 'link';
    const MAIN = 'main';
    const MAP = 'map';
    const MARK = 'mark';
    const MATH = 'math';
    const MENU = 'menu';
    const META = 'meta';
    const METER = 'meter';
    const NAV = 'nav';
    const NOSCRIPT = 'noscript';
    const OBJECT = 'object';
    const OL = 'ol';
    const OUTPUT = 'output';
    const P = 'p';
    const PARAM = 'param';
    const PICTURE = 'picture';
    const PRE = 'pre';
    const PROGRESS = 'progress';
    const Q = 'q';
    const RUBY = 'ruby';
    const S = 's';
    const SAMP = 'samp';
    const SCRIPT = 'script';
    const SECTION = 'section';
    const SELECT = 'select';
    const SLOT = 'slot';
    const SMALL = 'small';
    const SOURCE = 'source';
    const SPAN = 'span';
    const STRONG = 'strong';
    const STYLE = 'style';
    const SUB = 'sub';
    const SUP = 'sup';
    const SVG = 'svg';
    const TABLE = 'table';
    const TEMPLATE = 'template';
    const TEXTAREA = 'textarea';
    const TIME = 'time';
    const TITLE = 'title';
    const TRACK = 'track';
    const U = 'u';
    const UL = 'ul';
    const _VAR = 'var';
    const VIDEO = 'video';
    const WBR = 'wbr';

    public static function isEmbeddedContent($element): bool
    {
        $element = strtolower($element);

        return $element === self::AUDIO
            || $element === self::CANVAS
            || $element === self::EMBED
            || $element === self::IFRAME
            || $element === self::IMG
            || $element === self::MATH
            || $element === self::OBJECT
            || $element === self::PICTURE
            || $element === self::SVG
            || $element === self::VIDEO;
    }

    public static function isInteractiveContent($element): bool
    {
        $element = strtolower($element);

        return $element === self::A
            || $element === self::AUDIO
            || $element === self::BUTTON
            || $element === self::DETAILS
            || $element === self::EMBED
            || $element === self::IFRAME
            || $element === self::IMG
            || $element === self::INPUT
            || $element === self::LABEL
            || $element === self::SELECT
            || $element === self::TEXTAREA
            || $element === self::VIDEO;
    }

    public static function isMetadataContent($element): bool
    {
        $element = strtolower($element);

        return $element === self::BASE
            || $element === self::LINK
            || $element === self::META
            || $element === self::NOSCRIPT
            || $element === self::SCRIPT
            || $element === self::STYLE
            || $element === self::TEMPLATE
            || $element === self::TITLE;
    }

    public static function isPalpableContent($element): bool
    {
        $element = strtolower($element);

        return $element === self::A
            || $element === self::ABBR
            || $element === self::ADDRESS
            || $element === self::ARTICLE
            || $element === self::ASIDE
            || $element === self::AUDIO
            || $element === self::B
            || $element === self::BDI
            || $element === self::BDO
            || $element === self::BLOCKQUOTE
            || $element === self::BUTTON
            || $element === self::CANVAS
            || $element === self::CITE
            || $element === self::CODE
            || $element === self::DATA
            || $element === self::DETAILS
            || $element === self::DFN
            || $element === self::DIV
            || $element === self::DL
            || $element === self::EM
            || $element === self::EMBED
            || $element === self::FIELDSET
            || $element === self::FIGURE
            || $element === self::FOOTER
            || $element === self::FORM
            || $element === self::H1
            || $element === self::H2
            || $element === self::H3
            || $element === self::H4
            || $element === self::H5
            || $element === self::H6
            || $element === self::HEADER
            || $element === self::HGROUP
            || $element === self::I
            || $element === self::IFRAME
            || $element === self::IMG
            || $element === self::INPUT
            || $element === self::INS
            || $element === self::KBD
            || $element === self::LABEL
            || $element === self::MAIN
            || $element === self::MAP
            || $element === self::MARK
            || $element === self::MATH
            || $element === self::MENU
            || $element === self::METER
            || $element === self::NAV
            || $element === self::OBJECT
            || $element === self::OL
            || $element === self::OUTPUT
            || $element === self::P
            || $element === self::PRE
            || $element === self::PROGRESS
            || $element === self::Q
            || $element === self::RUBY
            || $element === self::S
            || $element === self::SAMP
            || $element === self::SECTION
            || $element === self::SELECT
            || $element === self::SMALL
            || $element === self::SPAN
            || $element === self::STRONG
            || $element === self::SUB
            || $element === self::SUP
            || $element === self::SVG
            || $element === self::TABLE
            || $element === self::TEXTAREA
            || $element === self::TIME
            || $element === self::U
            || $element === self::UL
            || $element === self::_VAR
            || $element === self::VIDEO;
    }

    public static function isPhrasingContent($element): bool
    {
        $element = strtolower($element);

        return $element === self::A
            || $element === self::ABBR
            || $element === self::AREA
            || $element === self::AUDIO
            || $element === self::B
            || $element === self::BDI
            || $element === self::BDO
            || $element === self::BR
            || $element === self::BUTTON
            || $element === self::CANVAS
            || $element === self::CITE
            || $element === self::CODE
            || $element === self::DATA
            || $element === self::DATALIST
            || $element === self::DEL
            || $element === self::DFN
            || $element === self::EM
            || $element === self::EMBED
            || $element === self::I
            || $element === self::IFRAME
            || $element === self::IMG
            || $element === self::INPUT
            || $element === self::INS
            || $element === self::KBD
            || $element === self::LABEL
            || $element === self::LINK
            || $element === self::MAP
            || $element === self::MARK
            || $element === self::MATH
            || $element === self::META
            || $element === self::METER
            || $element === self::NOSCRIPT
            || $element === self::OBJECT
            || $element === self::OUTPUT
            || $element === self::PICTURE
            || $element === self::PROGRESS
            || $element === self::Q
            || $element === self::RUBY
            || $element === self::S
            || $element === self::SAMP
            || $element === self::SCRIPT
            || $element === self::SELECT
            || $element === self::SLOT
            || $element === self::SMALL
            || $element === self::SPAN
            || $element === self::STRONG
            || $element === self::SUB
            || $element === self::SUP
            || $element === self::SVG
            || $element === self::TEMPLATE
            || $element === self::TEXTAREA
            || $element === self::TIME
            || $element === self::U
            || $element === self::_VAR
            || $element === self::VIDEO
            || $element === self::WBR;
    }

    public static function isRawTextElement($element): bool
    {
        $element = strtolower($element);

        return $element === self::SCRIPT
            || $element === self::STYLE;
    }

    public static function isVoidElement($element): bool
    {
        $element = strtolower($element);

        return $element === self::AREA
            || $element === self::BASE
            || $element === self::BR
            || $element === self::COL
            || $element === self::EMBED
            || $element === self::HR
            || $element === self::IMG
            || $element === self::INPUT
            || $element === self::LINK
            || $element === self::META
            || $element === self::PARAM
            || $element === self::SOURCE
            || $element === self::TRACK
            || $element === self::WBR;
    }
}
