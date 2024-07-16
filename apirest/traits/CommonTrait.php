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
        $dom = new DOMDocument();
        $dom->loadHTML(html_entity_decode($html), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
        $richText = new PHPExcel_RichText();
        $text="";
        foreach ($dom->getElementsByTagName('p') as $p) {
            foreach ($p->childNodes as $node) {
                if ($node->nodeType === 3) {
                    // $text = new PHPExcel_RichText_TextElement($node->textContent);
                } elseif ($node->nodeType === 1 && ($node->nodeName=="span" || $node->nodeName=="p")) {
                    $style = $node->getAttribute('style');
    
                    $text = new PHPExcel_RichText_Run($node->textContent);
    
                    // Handle color
                    if (strpos($style, 'color:') !== false) {
                        preg_match('/color:\s*rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $style, $matches);
                        if ($matches) {
                            $text->getFont()->getColor()->setRGB(sprintf('%02X%02X%02X', $matches[1], $matches[2], $matches[3]));
                        }
                    }
    
                    // Handle background color
                    // if (strpos($style, 'background-color:') !== false) {
                    //     preg_match('/background-color:\s*rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $style, $matches);
                    //     if ($matches) {
                    //         $text->getFont()->setColor(new PHPExcel_Style_Color('00000000')); // Set font color to black for better visibility
                    //         // $text->getFont()->getFill()->getStartColor()->setRGB(sprintf('%02X%02X%02X', $matches[1], $matches[2], $matches[3]));
                    //     }
                    // }
    
                    // Handle bold
                    if ($node->nodeName === 'strong') {
                        $text->getFont()->setBold(true);
                    }
                }
    
                $richText->addText($text);
            }
            $richText->addText(new PHPExcel_RichText_Run("\n"));
        }
    
        return $richText;
    }
}