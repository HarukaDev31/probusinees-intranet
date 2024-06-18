<?php
trait SupplierTraits {
    public function generateSupplierCode($name) {
        //First to letter from name - 4 random numbers and letter -month - year 2 digits
        $code = strtoupper(substr($name, 0, 2)) . rand(1000, 9999) . substr(date('m'), 0, 2 ) . substr(date('Y'), 2, 2);
        return $code;
    }
}
?>