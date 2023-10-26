<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class BorradoArchivo
{
    private $patronCarpeta          = '/^[0-9a-zA-z][0-9a-zA-z\/\_\-]+$/i';
    private $patronArchivo          = '/^[0-9a-zA-z][0-9a-zA-z\/\_\-]+$/i';
    private $debug                  = true;
    private $Carpeta                = "";
    private $CarpetaNoBorrado       = ["apirest","assets","backups","bower_components","codeigniter_3.1.11","dist","plugins",".",".."];

    public function setDebug($debug){
        $this->debug = $debug;
    }

    public function getDebug(){
        return $this->debug;
    }

    public function getCarpeta(){
        return $this->Carpeta;
    }

    public function setCarpeta($Carpeta){
        $this->Carpeta = $Carpeta;
    }

    public function getFileLocation($Archivo){
        $this->setArchivo($Archivo);
        
        $this->ValidarCarpeta();

        $this->ValidarArchivo();

        return FCPATH.$this->getCarpeta()."/".$this->archivo;
    }

    public function ValidarCarpeta(){
        $Carpeta = explode("/", $this->getCarpeta());
        
        if(strlen($Carpeta[0])==0)
            throw new Exception("\nCarpeta Invalida\n"); 

        if( in_array($Carpeta[0], $this->CarpetaNoBorrado))
            throw new Exception("\nCarpeta Invalida\n"); 
    }

    public function ValidarArchivo(){
        $CarpetaFileName = $this->getCarpeta()."/".$this->getArchivo();

        if(!file_exists($CarpetaFileName))
            throw new Exception("\nArchivo No Existe : ".FCPATH.$CarpetaFileName."\n"); 
    }

    public function setArchivo($FileName){
        $this->archivo = str_replace("/", "", $FileName);
    }

    public function getArchivo(){
        return $this->archivo;
    }

    public function BorrarArchivo($FileName)
    {
         $this->Archivo = $this->getFileLocation($FileName);

        if($this->getDebug())
          echo "\nBorrado Archivo: ".$this->Archivo."\n";

        if (file_exists($this->Archivo))
        {
            if (unlink($this->Archivo))
                return true;
            else
                return false;
        }else 
            throw new Exception('No Existe Archivo -- Borrado');
     }

    public function test(){
        echo "\n BASEPATH: ".BASEPATH."\n";
        echo "\n FCPATH: ".FCPATH."\n";
    }
}