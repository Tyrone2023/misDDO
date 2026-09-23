<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Renders a one-page Word (.docx) form as an HTML page that follows the
 * template: page size and margins, header/footer (letterheads), content
 * controls, text boxes, anchored images, fonts, styles, indents, spacing,
 * tab stops and tables. Tab widths are finished in the browser (they depend
 * on where the text lands), using the stops written on each paragraph.
 *
 * Units: Word stores twips (1/20 pt) and EMU (1/12700 pt); output is in pt.
 */
class Docx_page_renderer
{
    const W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    const R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    private $zip;
    private $styles = [];
    private $defaultParagraphStyle = 'Normal';
    private $docDefaultsP = null;
    private $docDefaultsR = null;
    private $themeFonts = ['minor' => 'Calibri', 'major' => 'Calibri Light'];
    private $defaultTab = 720;
    private $page = [];
    private $rels = [];
    private $part = 'word/document.xml';
    private $pageAnchors = '';

    public function __construct($path)
    {
        $this->zip = new ZipArchive();
        if ($this->zip->open($path) !== true) {
            throw new RuntimeException('The Word file could not be opened.');
        }
        $this->load_theme();
        $this->load_styles();
        $settings = $this->zip->getFromName('word/settings.xml');
        if ($settings !== false && preg_match('/<w:defaultTabStop w:val="([0-9]+)"/', $settings, $m)) {
            $this->defaultTab = max(1, (int) $m[1]);
        }
    }

    public function __destruct()
    {
        if ($this->zip) {
            @$this->zip->close();
        }
    }

    /** @return array{html:string,width:float,height:float} sizes in pt */
    public function render()
    {
        $xml = $this->zip->getFromName('word/document.xml');
        if ($xml === false) {
            throw new RuntimeException('The Word file has no document body.');
        }
        list($dom, $xp) = $this->parse($xml);
        $body = $xp->query('//w:body')->item(0);
        if (!$body) {
            throw new RuntimeException('The Word file has no document body.');
        }
        $sect = $xp->query('./w:sectPr', $body)->item(0) ?: $xp->query('//w:sectPr')->item(0);
        $this->page = $this->page_setup($xp, $sect);
        $p = $this->page;

        $this->rels = $this->load_rels('word/document.xml');
        $this->part = 'word/document.xml';
        $bodyHtml = $this->blocks($xp, $body);
        $bodyAnchors = $this->pageAnchors;
        $this->pageAnchors = '';

        $header = $this->header_footer($xp, $sect, 'header');
        $footer = $this->header_footer($xp, $sect, 'footer');

        $font = $this->font_stack($this->run_props($xp, null, null)['font']);
        $html = '<div class="dx-page" style="position:relative;z-index:0;box-sizing:border-box;background:#fff;color:#000;'
            . 'width:' . $p['w'] . 'pt;min-height:' . $p['h'] . 'pt;'
            . 'padding:' . $p['top'] . 'pt ' . $p['right'] . 'pt ' . $p['bottom'] . 'pt ' . $p['left'] . 'pt;'
            . 'font-family:' . $font . ';" data-tab="' . $this->pt($this->defaultTab) . '">';
        if ($header !== '') {
            $html .= '<div class="dx-header" style="position:absolute;z-index:0;left:' . $p['left'] . 'pt;right:' . $p['right'] . 'pt;top:' . $p['header'] . 'pt;">' . $header . '</div>';
        }
        if ($footer !== '') {
            $html .= '<div class="dx-footer" style="position:absolute;z-index:0;left:' . $p['left'] . 'pt;right:' . $p['right'] . 'pt;bottom:' . $p['footer'] . 'pt;">' . $footer . '</div>';
        }
        $html .= $bodyAnchors;
        $html .= '<div class="dx-body" style="position:relative;z-index:1;">' . $bodyHtml . '</div>';
        $html .= '</div>';

        return ['html' => $html, 'width' => $p['w'], 'height' => $p['h']];
    }

    // ------------------------------------------------------------------ setup

    private function parse($xml)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        if (!@$dom->loadXML($xml, LIBXML_NONET)) {
            throw new RuntimeException('The Word file is damaged.');
        }
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('w', self::W);
        $xp->registerNamespace('r', self::R);
        $xp->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');
        $xp->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xp->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
        $xp->registerNamespace('wps', 'http://schemas.microsoft.com/office/word/2010/wordprocessingShape');
        $xp->registerNamespace('mc', 'http://schemas.openxmlformats.org/markup-compatibility/2006');
        $xp->registerNamespace('v', 'urn:schemas-microsoft-com:vml');
        return [$dom, $xp];
    }

    private function page_setup(DOMXPath $xp, $sect)
    {
        $page = ['w' => 612, 'h' => 792, 'top' => 72, 'right' => 72, 'bottom' => 72, 'left' => 72, 'header' => 36, 'footer' => 36];
        if (!$sect) {
            return $page;
        }
        $size = $xp->query('./w:pgSz', $sect)->item(0);
        if ($size) {
            $page['w'] = $this->pt($this->attr($size, 'w'), 612);
            $page['h'] = $this->pt($this->attr($size, 'h'), 792);
        }
        $mar = $xp->query('./w:pgMar', $sect)->item(0);
        if ($mar) {
            foreach (['top', 'right', 'bottom', 'left', 'header', 'footer'] as $side) {
                $value = $this->attr($mar, $side);
                if ($value !== '') {
                    $page[$side] = abs($this->pt($value));
                }
            }
        }
        return $page;
    }

    private function load_theme()
    {
        $xml = $this->zip->getFromName('word/theme/theme1.xml');
        if ($xml === false) {
            return;
        }
        if (preg_match('#<a:minorFont>\s*<a:latin typeface="([^"]*)"#', $xml, $m) && $m[1] !== '') {
            $this->themeFonts['minor'] = $m[1];
        }
        if (preg_match('#<a:majorFont>\s*<a:latin typeface="([^"]*)"#', $xml, $m) && $m[1] !== '') {
            $this->themeFonts['major'] = $m[1];
        }
    }

    private function load_styles()
    {
        $xml = $this->zip->getFromName('word/styles.xml');
        if ($xml === false) {
            return;
        }
        list($dom, $xp) = $this->parse($xml);
        $this->docDefaultsP = $xp->query('//w:docDefaults/w:pPrDefault/w:pPr')->item(0);
        $this->docDefaultsR = $xp->query('//w:docDefaults/w:rPrDefault/w:rPr')->item(0);
        foreach ($xp->query('//w:style') as $style) {
            $id = $this->attr($style, 'styleId');
            $basedOn = $xp->query('./w:basedOn', $style)->item(0);
            $this->styles[$id] = [
                'type' => $this->attr($style, 'type'),
                'basedOn' => $basedOn ? $this->attr($basedOn, 'val') : '',
                'pPr' => $xp->query('./w:pPr', $style)->item(0),
                'rPr' => $xp->query('./w:rPr', $style)->item(0),
            ];
            if ($this->attr($style, 'type') === 'paragraph' && in_array($this->attr($style, 'default'), ['1', 'true'], true)) {
                $this->defaultParagraphStyle = $id;
            }
        }
        // Keep the styles DOM alive for the stored nodes.
        $this->stylesDom = $dom;
    }

    private $stylesDom;

    private function style_chain($id)
    {
        $chain = [];
        $guard = 0;
        while ($id !== '' && isset($this->styles[$id]) && $guard++ < 20) {
            array_unshift($chain, $this->styles[$id]);
            $id = $this->styles[$id]['basedOn'];
        }
        return $chain;
    }

    private function load_rels($part)
    {
        $relsPath = dirname($part) . '/_rels/' . basename($part) . '.rels';
        $xml = $this->zip->getFromName($relsPath);
        $rels = [];
        if ($xml !== false && preg_match_all('/<Relationship\b[^>]*>/', $xml, $matches)) {
            foreach ($matches[0] as $tag) {
                if (preg_match('/\bId="([^"]*)"/', $tag, $id) && preg_match('/\bTarget="([^"]*)"/', $tag, $target)) {
                    $external = strpos($tag, 'TargetMode="External"') !== false;
                    $rels[$id[1]] = $external ? '' : $this->resolve_path(dirname($part), html_entity_decode($target[1]));
                }
            }
        }
        return $rels;
    }

    private function resolve_path($base, $target)
    {
        if (strpos($target, '/') === 0) {
            return ltrim($target, '/');
        }
        $parts = explode('/', $base . '/' . $target);
        $out = [];
        foreach ($parts as $segment) {
            if ($segment === '..') {
                array_pop($out);
            } elseif ($segment !== '.' && $segment !== '') {
                $out[] = $segment;
            }
        }
        return implode('/', $out);
    }

    private function header_footer(DOMXPath $xp, $sect, $kind)
    {
        if (!$sect) {
            return '';
        }
        $ref = $xp->query('./w:' . $kind . 'Reference[@w:type="default"]', $sect)->item(0)
            ?: $xp->query('./w:' . $kind . 'Reference', $sect)->item(0);
        if (!$ref) {
            return '';
        }
        $id = $ref->getAttributeNS(self::R, 'id');
        $target = $this->rels[$id] ?? '';
        $xml = $target !== '' ? $this->zip->getFromName($target) : false;
        if ($xml === false) {
            return '';
        }
        $savedRels = $this->rels;
        $savedPart = $this->part;
        $this->rels = $this->load_rels($target);
        $this->part = $target;
        list($dom, $hxp) = $this->parse($xml);
        $html = $this->blocks($hxp, $dom->documentElement);
        // Anchors placed against the page from a header/footer stay behind
        // the body, like Word draws them.
        $html .= $this->pageAnchors;
        $this->pageAnchors = '';
        $this->rels = $savedRels;
        $this->part = $savedPart;
        return $html;
    }

    // ----------------------------------------------------------------- blocks

    private function blocks(DOMXPath $xp, DOMElement $container)
    {
        $html = '';
        foreach ($container->childNodes as $node) {
            if (!($node instanceof DOMElement)) {
                continue;
            }
            switch ($node->localName) {
                case 'p':
                    $html .= $this->paragraph($xp, $node);
                    break;
                case 'tbl':
                    $html .= $this->table($xp, $node);
                    break;
                case 'sdt':
                    $content = $xp->query('./w:sdtContent', $node)->item(0);
                    if ($content) {
                        $html .= $this->blocks($xp, $content);
                    }
                    break;
                case 'customXml':
                case 'ins':
                    $html .= $this->blocks($xp, $node);
                    break;
            }
        }
        return $html;
    }

    private function paragraph(DOMXPath $xp, DOMElement $p)
    {
        $pPr = $xp->query('./w:pPr', $p)->item(0);
        $styleId = $this->paragraph_style_id($xp, $pPr);
        $props = $this->paragraph_props($xp, $pPr, $styleId);
        $markRun = $pPr ? $xp->query('./w:rPr', $pPr)->item(0) : null;
        $mark = $this->run_props($xp, $markRun, $styleId);

        $anchors = '';
        $inner = $this->inline_content($xp, $p, $styleId, $props, $anchors);

        $css = 'position:relative;margin-top:' . $props['before'] . 'pt;margin-bottom:' . $props['after'] . 'pt;'
            . 'margin-left:' . $props['left'] . 'pt;margin-right:' . $props['right'] . 'pt;'
            . 'text-indent:' . $props['indent'] . 'pt;text-align:' . $props['align'] . ';'
            . 'white-space:pre-wrap;font-size:' . $mark['size'] . 'pt;'
            . 'font-family:' . $this->font_stack($mark['font']) . ';';
        if ($props['lineRule'] === 'exact') {
            $css .= 'line-height:' . $props['line'] . 'pt;';
        } elseif ($props['lineRule'] === 'atLeast') {
            $css .= 'line-height:' . max($props['line'], $mark['size'] * 1.15) . 'pt;';
        } else {
            $css .= 'line-height:' . round(1.15 * $props['line'], 3) . ';';
        }
        if ($props['shading'] !== '') {
            $css .= 'background:' . $props['shading'] . ';';
        }
        $css .= $props['borders'];

        $tabs = [];
        foreach ($props['tabs'] as $pos => $type) {
            $tabs[] = $pos . ':' . $type;
        }
        $data = ' data-left="' . $props['left'] . '" data-indent="' . $props['indent'] . '"';
        if (!empty($tabs)) {
            $data .= ' data-tabs="' . implode(',', $tabs) . '"';
        }
        if (trim(strip_tags($inner, '<img><span><br>')) === '' && strpos($inner, '<img') === false && strpos($inner, 'dx-tab') === false && strpos($inner, 'dx-shape') === false) {
            $inner .= '<br>';
        }
        return '<div class="dx-p" style="' . $css . '"' . $data . '>' . $anchors . $inner . '</div>';
    }

    private function paragraph_style_id(DOMXPath $xp, $pPr)
    {
        $style = $pPr ? $xp->query('./w:pStyle', $pPr)->item(0) : null;
        $id = $style ? $this->attr($style, 'val') : '';
        return ($id !== '' && isset($this->styles[$id])) ? $id : $this->defaultParagraphStyle;
    }

    private function paragraph_props(DOMXPath $xp, $pPr, $styleId)
    {
        $props = [
            'align' => 'left', 'left' => 0.0, 'right' => 0.0, 'indent' => 0.0,
            'before' => 0.0, 'after' => 0.0, 'line' => 1.0, 'lineRule' => 'auto',
            'tabs' => [], 'shading' => '', 'borders' => '',
        ];
        $sources = [];
        if ($this->docDefaultsP) {
            $sources[] = $this->docDefaultsP;
        }
        foreach ($this->style_chain($styleId) as $style) {
            if ($style['pPr']) {
                $sources[] = $style['pPr'];
            }
        }
        if ($pPr) {
            $sources[] = $pPr;
        }
        foreach ($sources as $source) {
            $this->apply_paragraph($source, $props);
        }
        ksort($props['tabs'], SORT_NUMERIC);
        return $props;
    }

    private function apply_paragraph(DOMElement $pPr, array &$props)
    {
        foreach ($pPr->childNodes as $el) {
            if (!($el instanceof DOMElement)) {
                continue;
            }
            switch ($el->localName) {
                case 'jc':
                    $map = ['center' => 'center', 'right' => 'right', 'end' => 'right', 'both' => 'justify', 'distribute' => 'justify', 'left' => 'left', 'start' => 'left'];
                    $props['align'] = $map[$this->attr($el, 'val')] ?? $props['align'];
                    break;
                case 'ind':
                    $left = $this->attr($el, 'left') !== '' ? $this->attr($el, 'left') : $this->attr($el, 'start');
                    $right = $this->attr($el, 'right') !== '' ? $this->attr($el, 'right') : $this->attr($el, 'end');
                    if ($left !== '') {
                        $props['left'] = $this->pt($left);
                    }
                    if ($right !== '') {
                        $props['right'] = $this->pt($right);
                    }
                    if ($this->attr($el, 'firstLine') !== '') {
                        $props['indent'] = $this->pt($this->attr($el, 'firstLine'));
                    }
                    if ($this->attr($el, 'hanging') !== '') {
                        $props['indent'] = -$this->pt($this->attr($el, 'hanging'));
                    }
                    break;
                case 'spacing':
                    if ($this->attr($el, 'before') !== '') {
                        $props['before'] = $this->pt($this->attr($el, 'before'));
                    }
                    if ($this->attr($el, 'after') !== '') {
                        $props['after'] = $this->pt($this->attr($el, 'after'));
                    }
                    if ($this->attr($el, 'line') !== '') {
                        $rule = $this->attr($el, 'lineRule') ?: 'auto';
                        $props['lineRule'] = $rule;
                        $props['line'] = $rule === 'auto' ? ((float) $this->attr($el, 'line')) / 240 : $this->pt($this->attr($el, 'line'));
                    }
                    break;
                case 'tabs':
                    foreach ($el->childNodes as $tab) {
                        if (!($tab instanceof DOMElement) || $tab->localName !== 'tab') {
                            continue;
                        }
                        $pos = (string) $this->pt($this->attr($tab, 'pos'));
                        $type = $this->attr($tab, 'val');
                        if ($type === 'clear') {
                            unset($props['tabs'][$pos]);
                        } elseif ($type !== 'bar') {
                            $props['tabs'][$pos] = in_array($type, ['center', 'right', 'end', 'decimal'], true) ? ($type === 'end' ? 'right' : $type) : 'left';
                        }
                    }
                    break;
                case 'shd':
                    $fill = $this->attr($el, 'fill');
                    if ($fill !== '' && $fill !== 'auto') {
                        $props['shading'] = '#' . $fill;
                    }
                    break;
                case 'pBdr':
                    $props['borders'] = $this->border_css($el, ['top', 'left', 'bottom', 'right']);
                    break;
            }
        }
    }

    // ------------------------------------------------------------------- runs

    private function inline_content(DOMXPath $xp, DOMElement $node, $styleId, array $para, &$anchors)
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }
            switch ($child->localName) {
                case 'r':
                    $html .= $this->run($xp, $child, $styleId, $para, $anchors);
                    break;
                case 'hyperlink':
                case 'ins':
                case 'smartTag':
                case 'customXml':
                case 'fldSimple':
                case 'dir':
                case 'bdo':
                    $html .= $this->inline_content($xp, $child, $styleId, $para, $anchors);
                    break;
                case 'sdt':
                    $content = $xp->query('./w:sdtContent', $child)->item(0);
                    if ($content) {
                        $html .= $this->inline_content($xp, $content, $styleId, $para, $anchors);
                    }
                    break;
                case 'AlternateContent':
                    $html .= $this->inline_content($xp, $this->alternate($xp, $child), $styleId, $para, $anchors);
                    break;
            }
        }
        return $html;
    }

    /** mc:AlternateContent — the DrawingML choice when present, else the fallback. */
    private function alternate(DOMXPath $xp, DOMElement $node)
    {
        foreach ($node->childNodes as $choice) {
            if ($choice instanceof DOMElement && $choice->localName === 'Choice') {
                return $choice;
            }
        }
        foreach ($node->childNodes as $fallback) {
            if ($fallback instanceof DOMElement && $fallback->localName === 'Fallback') {
                return $fallback;
            }
        }
        return $node->ownerDocument->createElement('empty');
    }

    private function run(DOMXPath $xp, DOMElement $r, $styleId, array $para, &$anchors)
    {
        $rPr = $xp->query('./w:rPr', $r)->item(0);
        $props = $this->run_props($xp, $rPr, $styleId);
        if ($props['hidden']) {
            return '';
        }
        $out = '';
        $this->run_children($xp, $r, $para, $anchors, $out);
        if ($out === '') {
            return '';
        }
        $css = 'font-family:' . $this->font_stack($props['font']) . ';font-size:' . $props['size'] . 'pt;';
        if ($props['bold']) {
            $css .= 'font-weight:bold;';
        }
        if ($props['italic']) {
            $css .= 'font-style:italic;';
        }
        $decor = [];
        if ($props['underline']) {
            $decor[] = 'underline';
        }
        if ($props['strike']) {
            $decor[] = 'line-through';
        }
        if ($decor) {
            $css .= 'text-decoration:' . implode(' ', $decor) . ';';
        }
        if ($props['caps']) {
            $css .= 'text-transform:uppercase;';
        } elseif ($props['smallCaps']) {
            $css .= 'font-variant:small-caps;';
        }
        if ($props['color'] !== '') {
            $css .= 'color:' . $props['color'] . ';';
        }
        if ($props['highlight'] !== '') {
            $css .= 'background:' . $props['highlight'] . ';';
        }
        if ($props['vertAlign'] === 'superscript') {
            $css .= 'vertical-align:super;font-size:' . round($props['size'] * 0.65, 2) . 'pt;';
        } elseif ($props['vertAlign'] === 'subscript') {
            $css .= 'vertical-align:sub;font-size:' . round($props['size'] * 0.65, 2) . 'pt;';
        }
        return '<span style="' . $css . '">' . $out . '</span>';
    }

    private function run_children(DOMXPath $xp, DOMElement $r, array $para, &$anchors, &$out)
    {
        foreach ($r->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }
            switch ($child->localName) {
                case 't':
                    $out .= htmlspecialchars($child->textContent, ENT_QUOTES, 'UTF-8');
                    break;
                case 'tab':
                    if ($child->namespaceURI === self::W) {
                        $out .= '<span class="dx-tab"></span>';
                    }
                    break;
                case 'br':
                case 'cr':
                    $out .= '<br>';
                    break;
                case 'noBreakHyphen':
                    $out .= '&#8209;';
                    break;
                case 'softHyphen':
                    $out .= '&shy;';
                    break;
                case 'sym':
                    $code = hexdec(substr($this->attr($child, 'char'), -4));
                    $out .= $code ? '&#' . $code . ';' : '';
                    break;
                case 'drawing':
                    $out .= $this->drawing($xp, $child, $para, $anchors);
                    break;
                case 'pict':
                case 'object':
                    $out .= $this->vml($xp, $child, $para, $anchors);
                    break;
                case 'AlternateContent':
                    $this->run_children($xp, $this->alternate($xp, $child), $para, $anchors, $out);
                    break;
            }
        }
    }

    private function run_props(DOMXPath $xp, $rPr, $styleId)
    {
        $props = [
            'font' => 'Times New Roman', 'size' => 12.0, 'bold' => false, 'italic' => false,
            'underline' => false, 'strike' => false, 'caps' => false, 'smallCaps' => false,
            'color' => '', 'highlight' => '', 'vertAlign' => '', 'hidden' => false,
        ];
        $sources = [];
        if ($this->docDefaultsR) {
            $sources[] = $this->docDefaultsR;
        }
        if ($styleId !== null) {
            foreach ($this->style_chain($styleId) as $style) {
                if ($style['rPr']) {
                    $sources[] = $style['rPr'];
                }
            }
        }
        if ($rPr) {
            $runStyle = $xp->query('./w:rStyle', $rPr)->item(0);
            if ($runStyle) {
                foreach ($this->style_chain($this->attr($runStyle, 'val')) as $style) {
                    if ($style['rPr']) {
                        $sources[] = $style['rPr'];
                    }
                }
            }
            $sources[] = $rPr;
        }
        foreach ($sources as $source) {
            $this->apply_run($source, $props);
        }
        return $props;
    }

    private function apply_run(DOMElement $rPr, array &$props)
    {
        $on = function (DOMElement $el) {
            $val = $this->attr($el, 'val');
            return !in_array($val, ['0', 'false', 'off', 'none'], true);
        };
        foreach ($rPr->childNodes as $el) {
            if (!($el instanceof DOMElement)) {
                continue;
            }
            switch ($el->localName) {
                case 'rFonts':
                    $font = $this->attr($el, 'ascii') ?: $this->attr($el, 'hAnsi');
                    $theme = $this->attr($el, 'asciiTheme') ?: $this->attr($el, 'hAnsiTheme');
                    if ($font === '' && $theme !== '') {
                        $font = strpos($theme, 'major') === 0 ? $this->themeFonts['major'] : $this->themeFonts['minor'];
                    }
                    if ($font !== '') {
                        $props['font'] = $font;
                    }
                    break;
                case 'sz':
                    $size = (float) $this->attr($el, 'val');
                    if ($size > 0) {
                        $props['size'] = $size / 2;
                    }
                    break;
                case 'b':
                    $props['bold'] = $on($el);
                    break;
                case 'i':
                    $props['italic'] = $on($el);
                    break;
                case 'u':
                    $props['underline'] = $on($el);
                    break;
                case 'strike':
                case 'dstrike':
                    $props['strike'] = $on($el);
                    break;
                case 'caps':
                    $props['caps'] = $on($el);
                    break;
                case 'smallCaps':
                    $props['smallCaps'] = $on($el);
                    break;
                case 'vanish':
                    $props['hidden'] = $on($el);
                    break;
                case 'color':
                    $color = $this->attr($el, 'val');
                    $props['color'] = ($color === '' || $color === 'auto') ? '' : '#' . $color;
                    break;
                case 'highlight':
                    $colors = ['yellow' => '#ffff00', 'green' => '#00ff00', 'cyan' => '#00ffff', 'magenta' => '#ff00ff',
                        'blue' => '#0000ff', 'red' => '#ff0000', 'lightGray' => '#d3d3d3', 'darkGray' => '#a9a9a9'];
                    $props['highlight'] = $colors[$this->attr($el, 'val')] ?? '';
                    break;
                case 'vertAlign':
                    $props['vertAlign'] = $this->attr($el, 'val');
                    break;
            }
        }
    }

    // --------------------------------------------------------------- drawings

    private function drawing(DOMXPath $xp, DOMElement $drawing, array $para, &$anchors)
    {
        $frame = $xp->query('./wp:anchor|./wp:inline', $drawing)->item(0);
        if (!$frame) {
            return '';
        }
        $extent = $xp->query('./wp:extent', $frame)->item(0);
        $w = $extent ? $this->emu($extent->getAttribute('cx')) : 0;
        $h = $extent ? $this->emu($extent->getAttribute('cy')) : 0;
        $content = $this->graphic($xp, $frame, $w, $h);
        if ($content === '') {
            return '';
        }
        if ($frame->localName === 'inline') {
            return '<span class="dx-shape" style="display:inline-block;vertical-align:bottom;position:relative;text-indent:0;width:' . $w . 'pt;height:' . $h . 'pt;">' . $content . '</span>';
        }

        $behind = in_array($frame->getAttribute('behindDoc'), ['1', 'true'], true);
        $z = $behind ? -1 : 2;
        $posH = $xp->query('./wp:positionH', $frame)->item(0);
        $posV = $xp->query('./wp:positionV', $frame)->item(0);
        $hFrom = $posH ? $posH->getAttribute('relativeFrom') : 'column';
        $vFrom = $posV ? $posV->getAttribute('relativeFrom') : 'paragraph';
        $x = $this->position($xp, $posH, $w, $hFrom, true);
        $y = $this->position($xp, $posV, $h, $vFrom, false);

        $box = 'position:absolute;z-index:' . $z . ';width:' . $w . 'pt;height:' . $h . 'pt;text-indent:0;white-space:normal;';
        if (in_array($vFrom, ['paragraph', 'line'], true)) {
            if ($hFrom === 'character') {
                return '<span class="dx-shape" style="position:relative;display:inline-block;width:0;height:0;vertical-align:top;text-indent:0;">'
                    . '<span style="' . $box . 'display:block;left:' . $x . 'pt;top:' . $y . 'pt;">' . $content . '</span></span>';
            }
            // Offsets are measured from the column; the paragraph box starts at its indent.
            $left = $x - $para['left'];
            if (in_array($hFrom, ['page', 'leftMargin'], true)) {
                $left = $x - $this->page['left'] - $para['left'];
            }
            $anchors .= '<span class="dx-shape" style="' . $box . 'display:block;left:' . $left . 'pt;top:' . $y . 'pt;">' . $content . '</span>';
            return '';
        }
        // Page / margin relative: placed on the page itself.
        $left = in_array($hFrom, ['page', 'leftMargin'], true) ? $x : $x + $this->page['left'];
        $top = in_array($vFrom, ['page', 'topMargin'], true) ? $y : $y + $this->page['top'];
        $this->pageAnchors .= '<span class="dx-shape" style="' . $box . 'display:block;z-index:' . ($behind ? 0 : 2) . ';left:' . $left . 'pt;top:' . $top . 'pt;">' . $content . '</span>';
        return '';
    }

    private function position(DOMXPath $xp, $pos, $size, $from, $horizontal)
    {
        if (!$pos) {
            return 0;
        }
        $offset = $xp->query('./wp:posOffset', $pos)->item(0);
        if ($offset) {
            return $this->emu(trim($offset->textContent));
        }
        $align = $xp->query('./wp:align', $pos)->item(0);
        if (!$align) {
            return 0;
        }
        $p = $this->page;
        if ($horizontal) {
            $room = in_array($from, ['page'], true) ? $p['w'] : $p['w'] - $p['left'] - $p['right'];
        } else {
            $room = in_array($from, ['page'], true) ? $p['h'] : $p['h'] - $p['top'] - $p['bottom'];
        }
        switch (trim($align->textContent)) {
            case 'center':
                return ($room - $size) / 2;
            case 'right':
            case 'bottom':
            case 'outside':
                return $room - $size;
            default:
                return 0;
        }
    }

    private function graphic(DOMXPath $xp, DOMElement $frame, $w, $h)
    {
        $blip = $xp->query('.//pic:pic//a:blip', $frame)->item(0);
        if ($blip) {
            $src = $this->image_uri($blip->getAttributeNS(self::R, 'embed'));
            if ($src === '') {
                return '';
            }
            // Cropping (a:srcRect, in 1/1000 %): only the kept part of the
            // picture fills the frame, e.g. a full-page letterhead scan whose
            // top strip is used as the header.
            $crop = $xp->query('.//pic:blipFill/a:srcRect', $frame)->item(0);
            $cut = ['l' => 0, 't' => 0, 'r' => 0, 'b' => 0];
            if ($crop) {
                foreach ($cut as $side => $unused) {
                    $cut[$side] = ((float) $crop->getAttribute($side)) / 100000;
                }
            }
            $keepW = 1 - $cut['l'] - $cut['r'];
            $keepH = 1 - $cut['t'] - $cut['b'];
            if ($keepW <= 0.001 || $keepH <= 0.001 || ($cut['l'] == 0 && $cut['t'] == 0 && $cut['r'] == 0 && $cut['b'] == 0)) {
                return '<img src="' . $src . '" alt="" style="display:block;width:100%;height:100%;">';
            }
            $imgW = round(100 / $keepW, 4);
            $imgH = round(100 / $keepH, 4);
            return '<span style="display:block;position:relative;overflow:hidden;width:100%;height:100%;">'
                . '<img src="' . $src . '" alt="" style="display:block;position:absolute;max-width:none;'
                . 'width:' . $imgW . '%;height:' . $imgH . '%;'
                . 'left:' . round(-$cut['l'] * $imgW, 4) . '%;top:' . round(-$cut['t'] * $imgH, 4) . '%;">'
                . '</span>';
        }
        $shape = $xp->query('.//wps:wsp', $frame)->item(0);
        if ($shape) {
            return $this->shape($xp, $shape);
        }
        return '';
    }

    private function shape(DOMXPath $xp, DOMElement $shape)
    {
        $css = 'box-sizing:border-box;width:100%;height:100%;overflow:hidden;';
        $spPr = $xp->query('./wps:spPr', $shape)->item(0);
        if ($spPr) {
            $fill = $xp->query('./a:solidFill/a:srgbClr', $spPr)->item(0);
            if ($fill) {
                $css .= 'background:#' . $fill->getAttribute('val') . ';';
            }
            $line = $xp->query('./a:ln', $spPr)->item(0);
            if ($line && $xp->query('./a:noFill', $line)->length === 0) {
                $color = $xp->query('./a:solidFill/a:srgbClr', $line)->item(0);
                $width = $line->getAttribute('w') !== '' ? max(0.5, $this->emu($line->getAttribute('w'))) : 0.75;
                $css .= 'border:' . $width . 'pt solid ' . ($color ? '#' . $color->getAttribute('val') : '#000') . ';';
            }
        }
        $bodyPr = $xp->query('./wps:bodyPr', $shape)->item(0);
        $ins = ['tIns' => 45720, 'rIns' => 91440, 'bIns' => 45720, 'lIns' => 91440];
        foreach ($ins as $key => $default) {
            $value = $bodyPr ? $bodyPr->getAttribute($key) : '';
            $ins[$key] = $this->emu($value !== '' ? $value : $default);
        }
        $css .= 'padding:' . $ins['tIns'] . 'pt ' . $ins['rIns'] . 'pt ' . $ins['bIns'] . 'pt ' . $ins['lIns'] . 'pt;';
        $inner = '';
        $text = $xp->query('./wps:txbx/w:txbxContent', $shape)->item(0);
        if ($text) {
            $inner = $this->blocks($xp, $text);
        }
        return '<div class="dx-textbox" style="' . $css . '">' . $inner . '</div>';
    }

    /** Legacy VML picture/text box (only reached when no DrawingML choice exists). */
    private function vml(DOMXPath $xp, DOMElement $pict, array $para, &$anchors)
    {
        $shape = $xp->query('.//v:shape|.//v:rect|.//v:roundrect', $pict)->item(0);
        if (!$shape) {
            return '';
        }
        $style = [];
        foreach (explode(';', $shape->getAttribute('style')) as $pair) {
            $bits = explode(':', $pair, 2);
            if (count($bits) === 2) {
                $style[trim($bits[0])] = trim($bits[1]);
            }
        }
        $w = $this->css_pt($style['width'] ?? '0');
        $h = $this->css_pt($style['height'] ?? '0');
        $content = '';
        $image = $xp->query('.//v:imagedata', $shape)->item(0);
        if ($image) {
            $src = $this->image_uri($image->getAttributeNS(self::R, 'id'));
            $content = $src === '' ? '' : '<img src="' . $src . '" alt="" style="display:block;width:100%;height:100%;">';
        } else {
            $text = $xp->query('.//w:txbxContent', $shape)->item(0);
            if ($text) {
                $border = $shape->getAttribute('stroked') === 'f' ? '' : 'border:0.75pt solid #000;';
                $content = '<div class="dx-textbox" style="box-sizing:border-box;width:100%;height:100%;overflow:hidden;padding:3.6pt 7.2pt;' . $border . '">' . $this->blocks($xp, $text) . '</div>';
            }
        }
        if ($content === '') {
            return '';
        }
        if (($style['position'] ?? '') !== 'absolute') {
            return '<span class="dx-shape" style="display:inline-block;vertical-align:bottom;text-indent:0;width:' . $w . 'pt;height:' . $h . 'pt;">' . $content . '</span>';
        }
        $x = $this->css_pt($style['margin-left'] ?? ($style['left'] ?? '0'));
        $y = $this->css_pt($style['margin-top'] ?? ($style['top'] ?? '0'));
        $z = (int) ($style['z-index'] ?? 0) < 0 ? -1 : 2;
        $anchors .= '<span class="dx-shape" style="position:absolute;display:block;z-index:' . $z . ';text-indent:0;white-space:normal;left:' . ($x - $para['left']) . 'pt;top:' . $y . 'pt;width:' . $w . 'pt;height:' . $h . 'pt;">' . $content . '</span>';
        return '';
    }

    private function image_uri($relId)
    {
        $target = $this->rels[$relId] ?? '';
        if ($target === '') {
            return '';
        }
        $data = $this->zip->getFromName($target);
        if ($data === false) {
            return '';
        }
        $types = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'bmp' => 'image/bmp', 'svg' => 'image/svg+xml'];
        $type = $types[strtolower(pathinfo($target, PATHINFO_EXTENSION))] ?? '';
        return $type === '' ? '' : 'data:' . $type . ';base64,' . base64_encode($data);
    }

    // ----------------------------------------------------------------- tables

    private function table(DOMXPath $xp, DOMElement $tbl)
    {
        $tblPr = $xp->query('./w:tblPr', $tbl)->item(0);
        $grid = [];
        foreach ($xp->query('./w:tblGrid/w:gridCol', $tbl) as $col) {
            $grid[] = $this->pt($this->attr($col, 'w'));
        }
        $tableBorders = $tblPr ? $xp->query('./w:tblBorders', $tblPr)->item(0) : null;
        $inside = ['insideH' => '', 'insideV' => ''];
        $css = 'border-collapse:collapse;table-layout:fixed;';
        if (!empty($grid)) {
            $css .= 'width:' . array_sum($grid) . 'pt;';
        }
        if ($tblPr) {
            $indent = $xp->query('./w:tblInd', $tblPr)->item(0);
            if ($indent && $this->attr($indent, 'type') !== 'pct') {
                $css .= 'margin-left:' . $this->pt($this->attr($indent, 'w')) . 'pt;';
            }
            $jc = $xp->query('./w:jc', $tblPr)->item(0);
            if ($jc && $this->attr($jc, 'val') === 'center') {
                $css .= 'margin-left:auto;margin-right:auto;';
            }
        }
        if ($tableBorders) {
            $css .= $this->border_css($tableBorders, ['top', 'left', 'bottom', 'right']);
            foreach (['insideH' => 'top', 'insideV' => 'left'] as $key => $unused) {
                $edge = $xp->query('./w:' . $key, $tableBorders)->item(0);
                $inside[$key] = $edge ? $this->single_border($edge) : '';
            }
        }
        $cellMargin = ['left' => 5.4, 'right' => 5.4, 'top' => 0, 'bottom' => 0];
        $mar = $tblPr ? $xp->query('./w:tblCellMar', $tblPr)->item(0) : null;
        if ($mar) {
            foreach (['left', 'right', 'top', 'bottom'] as $side) {
                $edge = $xp->query('./w:' . $side, $mar)->item(0) ?: ($side === 'left' ? $xp->query('./w:start', $mar)->item(0) : ($side === 'right' ? $xp->query('./w:end', $mar)->item(0) : null));
                if ($edge) {
                    $cellMargin[$side] = $this->pt($this->attr($edge, 'w'));
                }
            }
        }

        $rows = [];
        foreach ($xp->query('./w:tr', $tbl) as $tr) {
            $cells = [];
            $col = 0;
            foreach ($xp->query('./w:tc', $tr) as $tc) {
                $tcPr = $xp->query('./w:tcPr', $tc)->item(0);
                $span = 1;
                $merge = '';
                if ($tcPr) {
                    $gridSpan = $xp->query('./w:gridSpan', $tcPr)->item(0);
                    $span = $gridSpan ? max(1, (int) $this->attr($gridSpan, 'val')) : 1;
                    $vMerge = $xp->query('./w:vMerge', $tcPr)->item(0);
                    if ($vMerge) {
                        $merge = $this->attr($vMerge, 'val') === 'restart' ? 'restart' : 'continue';
                    }
                }
                $cells[] = ['tc' => $tc, 'tcPr' => $tcPr, 'col' => $col, 'span' => $span, 'merge' => $merge];
                $col += $span;
            }
            $height = $xp->query('./w:trPr/w:trHeight', $tr)->item(0);
            $rows[] = ['cells' => $cells, 'height' => $height ? $this->pt($this->attr($height, 'val')) : 0];
        }
        // Row spans from vertical merges.
        foreach ($rows as $ri => $row) {
            foreach ($row['cells'] as $ci => $cell) {
                if ($cell['merge'] !== 'restart') {
                    continue;
                }
                $span = 1;
                for ($next = $ri + 1; $next < count($rows); $next++) {
                    $found = false;
                    foreach ($rows[$next]['cells'] as $other) {
                        if ($other['col'] === $cell['col'] && $other['merge'] === 'continue') {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        break;
                    }
                    $span++;
                }
                $rows[$ri]['cells'][$ci]['rowspan'] = $span;
            }
        }

        $html = '<table class="dx-table" style="' . $css . '">';
        if (!empty($grid)) {
            $html .= '<colgroup>';
            foreach ($grid as $width) {
                $html .= '<col style="width:' . $width . 'pt;">';
            }
            $html .= '</colgroup>';
        }
        foreach ($rows as $row) {
            $html .= '<tr' . ($row['height'] ? ' style="height:' . $row['height'] . 'pt;"' : '') . '>';
            foreach ($row['cells'] as $cell) {
                if ($cell['merge'] === 'continue') {
                    continue;
                }
                $cellCss = 'vertical-align:top;padding:' . $cellMargin['top'] . 'pt ' . $cellMargin['right'] . 'pt ' . $cellMargin['bottom'] . 'pt ' . $cellMargin['left'] . 'pt;';
                if ($inside['insideH'] !== '') {
                    $cellCss .= 'border-top:' . $inside['insideH'] . ';border-bottom:' . $inside['insideH'] . ';';
                }
                if ($inside['insideV'] !== '') {
                    $cellCss .= 'border-left:' . $inside['insideV'] . ';border-right:' . $inside['insideV'] . ';';
                }
                if ($cell['tcPr']) {
                    $borders = $xp->query('./w:tcBorders', $cell['tcPr'])->item(0);
                    if ($borders) {
                        $cellCss .= $this->border_css($borders, ['top', 'left', 'bottom', 'right']);
                    }
                    $shd = $xp->query('./w:shd', $cell['tcPr'])->item(0);
                    if ($shd && $this->attr($shd, 'fill') !== '' && $this->attr($shd, 'fill') !== 'auto') {
                        $cellCss .= 'background:#' . $this->attr($shd, 'fill') . ';';
                    }
                    $vAlign = $xp->query('./w:vAlign', $cell['tcPr'])->item(0);
                    if ($vAlign) {
                        $map = ['center' => 'middle', 'bottom' => 'bottom'];
                        $cellCss .= 'vertical-align:' . ($map[$this->attr($vAlign, 'val')] ?? 'top') . ';';
                    }
                }
                $attrs = $cell['span'] > 1 ? ' colspan="' . $cell['span'] . '"' : '';
                if (!empty($cell['rowspan']) && $cell['rowspan'] > 1) {
                    $attrs .= ' rowspan="' . $cell['rowspan'] . '"';
                }
                $html .= '<td' . $attrs . ' style="' . $cellCss . '">' . $this->blocks($xp, $cell['tc']) . '</td>';
            }
            $html .= '</tr>';
        }
        return $html . '</table>';
    }

    private function border_css(DOMElement $borders, array $sides)
    {
        $css = '';
        foreach ($sides as $side) {
            $edge = null;
            foreach ($borders->childNodes as $child) {
                if ($child instanceof DOMElement && ($child->localName === $side
                    || ($side === 'left' && $child->localName === 'start')
                    || ($side === 'right' && $child->localName === 'end'))) {
                    $edge = $child;
                }
            }
            if ($edge) {
                $value = $this->single_border($edge);
                $css .= 'border-' . $side . ':' . ($value === '' ? 'none' : $value) . ';';
            }
        }
        return $css;
    }

    private function single_border(DOMElement $edge)
    {
        $val = $this->attr($edge, 'val');
        if (in_array($val, ['', 'nil', 'none'], true)) {
            return '';
        }
        $size = max(0.5, ((float) $this->attr($edge, 'sz')) / 8);
        $color = $this->attr($edge, 'color');
        $style = in_array($val, ['double', 'triple'], true) ? 'double' : (in_array($val, ['dotted'], true) ? 'dotted' : (strpos($val, 'dash') !== false ? 'dashed' : 'solid'));
        if ($style === 'double') {
            $size = max($size, 2.25);
        }
        return $size . 'pt ' . $style . ' ' . (($color === '' || $color === 'auto') ? '#000' : '#' . $color);
    }

    // ---------------------------------------------------------------- helpers

    private function attr(DOMElement $el, $name)
    {
        return $el->hasAttributeNS(self::W, $name) ? $el->getAttributeNS(self::W, $name) : $el->getAttribute($name);
    }

    private function pt($twips, $fallback = 0)
    {
        return $twips === '' ? $fallback : round(((float) $twips) / 20, 3);
    }

    private function emu($value)
    {
        return round(((float) $value) / 12700, 3);
    }

    private function css_pt($value)
    {
        $value = trim((string) $value);
        $number = (float) $value;
        if (substr($value, -2) === 'in') {
            return $number * 72;
        }
        if (substr($value, -2) === 'px') {
            return $number * 0.75;
        }
        if (substr($value, -2) === 'cm') {
            return $number * 28.3465;
        }
        if (substr($value, -2) === 'mm') {
            return $number * 2.83465;
        }
        return $number;
    }

    private function font_stack($font)
    {
        $font = trim((string) $font);
        $generic = preg_match('/arial|calibri|helvetica|tahoma|verdana|segoe|century gothic|franklin|trebuchet/i', $font) ? 'sans-serif' : 'serif';
        $stack = ["'" . str_replace(["'", '"', ';'], '', $font) . "'"];
        if (stripos($font, 'bookman') !== false) {
            $stack[] = "'Bookman'";
            $stack[] = "'URW Bookman'";
            $stack[] = "'URW Bookman L'";
        }
        if (stripos($font, 'calibri') !== false) {
            $stack[] = "'Carlito'";
        }
        $stack[] = $generic;
        return implode(',', $stack);
    }
}
