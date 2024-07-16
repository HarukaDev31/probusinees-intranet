<?php
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
}