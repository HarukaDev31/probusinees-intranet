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
        $textWithLineBreaks = preg_replace('/<br\s*\/?>/i', "", $replacedText);
        
        // Decodificar entidades HTML
        $decodedText = html_entity_decode($textWithLineBreaks, ENT_QUOTES | ENT_HTML5);
    
        return $decodedText;
    }

    function htmlToRichText($html, $inputEncoding = 'UTF-8') {
        $richText = new PHPExcel_RichText();
        
        // Limpiar y preparar el HTML
        $html = preg_replace('/\s+/', ' ', $html);
        $html = strip_tags($html, '<ol><ul><li><span><strong><em><u>');
        
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $inputEncoding), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
    
        $xpath = new DOMXPath($dom);
        $listItems = $xpath->query('//li');
    
        foreach ($listItems as $index => $li) {
            // Añadir un bullet point o número de lista
            $prefix = new PHPExcel_RichText_Run('• ');
            $richText->addText($prefix);
    
            $this->processNode($li, $richText);
            
            // Añadir un salto de línea después de cada elemento de la lista
            if ($index < $listItems->length - 1) {
                // $richText->addText(new PHPExcel_RichText_Run("\n"));
            }
        }
        $paragraphs = $xpath->query('//p');
        foreach ($paragraphs as $index => $p) {
            $this->processNode($p, $richText);
            $richText->addText(new PHPExcel_RichText_Run("\n"));
        }
  
        return $richText;
    }
    
    private function processNode($node, $richText) {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE
            ) {
                $richText->addText(new PHPExcel_RichText_TextElement($child->textContent));
                $richText->addText(new PHPExcel_RichText_Run("\n"));

            }
            else if($child->nodeName==="strong" ){
                $text = new PHPExcel_RichText_Run($child->textContent);
                $text->getFont()->setBold(true);
                $richText->addText($text);
                $richText->addText(new PHPExcel_RichText_Run("\n"));

            }
            else if($child->nodeName==="em" ){
                $text = new PHPExcel_RichText_Run($child->textContent);
                $text->getFont()->setItalic(true);
                $richText->addText($text);
            }
            else if($child->nodeName==="u" ){
                $text = new PHPExcel_RichText_Run($child->textContent);
                $text->getFont()->setUnderline(true);
                $richText->addText($text);
            }//manage br
            
             elseif ($child->nodeType === XML_ELEMENT_NODE) {
                if ($child->nodeName === 'span' && $child->hasAttribute('class') && $child->getAttribute('class') === 'ql-ui') {
                    // Ignorar los spans con clase ql-ui
                    continue;
                }

                $text = new PHPExcel_RichText_Run($child->textContent);
                $this->applyStyles($child, $text);
                $richText->addText($text);
            }
        }
    }
    
    private function applyStyles($node, $text) {
        $style = $node->getAttribute('style');
        
        // Color
        if (preg_match('/color:\s*rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $style, $matches)) {
            $text->getFont()->getColor()->setRGB(sprintf('%02X%02X%02X', $matches[1], $matches[2], $matches[3]));
        }
        
        // Bold
        if ($node->nodeName === 'strong' || strpos($style, 'font-weight: bold') !== false) {
            $text->getFont()->setBold(true);
        }
        
        // Italic
        if ($node->nodeName === 'em' || strpos($style, 'font-style: italic') !== false) {
            $text->getFont()->setItalic(true);
        }
        
        // Underline
        if ($node->nodeName === 'u' || strpos($style, 'text-decoration: underline') !== false) {
            $text->getFont()->setUnderline(true);
        }
        
        // Font size
        if (preg_match('/font-size:\s*(\d+)pt/', $style, $matches)) {
            $text->getFont()->setSize($matches[1]);
        }
    }   
}