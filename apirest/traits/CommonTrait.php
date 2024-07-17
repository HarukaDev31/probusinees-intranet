<?php
require_once APPPATH . 'third_party/PHPExcel.php';

Trait CommonTrait{
    function cleanText($html) {
        // Reemplazar &amp;lt; y &amp;gt; con < y >
        $replacedText = str_replace(['&amp;lt;', '&amp;gt;'], ['<', '>'], $html);
        
        // Reemplazar <br /> con saltos de línea \n
        $textWithLineBreaks = str_replace('<br />', "\n", $replacedText);
        
        // Decodificar entidades HTML
        $decodedText = html_entity_decode($textWithLineBreaks, ENT_QUOTES | ENT_HTML5);
    
        return $decodedText;
    }
    function htmlToTextAndLineBreaks($html) {
        // Reemplazar &amp;lt; y &amp;gt; con < y >
        $replacedText = str_replace(['&amp;lt;', '&amp;gt;'], ['<', '>'], $html);
        
        // Reemplazar <br /> y variantes con saltos de línea \n
        $textWithLineBreaks = preg_replace('/<br\s*\/?>/i', "\n", $replacedText);
        
        // Decodificar entidades HTML
        $decodedText = html_entity_decode($textWithLineBreaks, ENT_QUOTES | ENT_HTML5);
    
        return $decodedText;
    }
    function htmlToRichText($html) {
   
        $richText = new PHPExcel_RichText();
        $paragraphs = preg_split('/<\/p>\s*<p[^>]*>/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($paragraphs as $index => $p) {
            if (!preg_match('/^<p/i', $p)) {
                $p = '<p>' . $p;
            }
            if (!preg_match('/<\/p>$/i', $p)) {
                $p .= '</p>';
            }

            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($p, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            foreach ($dom->getElementsByTagName('p') as $pElement) {
                foreach ($pElement->childNodes as $node) {
                    if ($node->nodeType === XML_TEXT_NODE) {
                        $text = new PHPExcel_RichText_TextElement($node->textContent);
                    } elseif ($node->nodeType === XML_ELEMENT_NODE) {
                        $style = $node->getAttribute('style');
        
                        $text = new PHPExcel_RichText_Run($node->textContent);
        
                        // Handle color
                        if (strpos($style, 'color:') !== false) {
                            preg_match('/color:\s*rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $style, $matches);
                            if ($matches) {
                                $text->getFont()->getColor()->setRGB(sprintf('%02X%02X%02X', $matches[1], $matches[2], $matches[3]));
                            }
                        }
        
                        // Handle bold
                        if ($node->nodeName === 'strong') {
                            $text->getFont()->setBold(true);
                        }
                    }
                    $richText->addText($text);
                }
            }
            $richText->addText(new PHPExcel_RichText_Run("\n"));

            
        }
    
        return $richText;
    }
}