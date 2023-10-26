<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MyLibrary {
 
    function plus($num1, $num2) {
        $sum = $num1 + $num2;
        return "<h2>Sum of $num1 and $num2 is: $sum</h2>";
    }
 
    function minus($large, $small) {
        return "<h2>Result of $large - $small is: " . ($large - $small) . "</h2>";
    }
 
}