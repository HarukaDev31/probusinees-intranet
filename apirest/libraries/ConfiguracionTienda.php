<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class ConfiguracionTienda
{

    private $handle;    
    private $Archivo;
    private $constante              = [];
    private $patronValor            = '/^[a-zA-Z0-9\.\s]*$/i';
    private $patronConstante        = '/[^a-zA-Z0-9\_]+/i';
    private $patronFolder           = '/^[a-zA-Z\_]+$/i';
    private $patronFileUsuario      = '/^[a-zA-Z0-9]+$/i';
    private $rootFolderTienda       = "/var/www/vhosts/compramaz/";
    private $folderUsuarios         = "usuarios";
    private $debug                  = false;

    public function Carga($FileUsuario)
    {
       $this->Archivo = $this->getFileUrl($FileUsuario);

       if($this->getDebug())
          echo "\nArchivo: ".$this->Archivo."\n";

        if ($this->handle = fopen($this->Archivo, 'c+'))
        {
            return $this;
        }
        else throw new Exception('No se puede Crear o Abrir Archivo -- Carga');
    }

    public function setDebug($debug){
        $this->debug = $debug;
    }

    public function getDebug(){
        return $this->debug;
    }

    public function SetFolderUsuarios($folderUsuarios){

        if (preg_match($this->patronFolder, $folderUsuarios))
            $this->folderUsuarios = $folderUsuarios;
    }

    public function getFolderUsuarios(){
        return $this->folderUsuarios."/";
    }

    public function getrootFolderTienda(){
        return $this->rootFolderTienda;
    }
    
    public function Escribir()
    {
        $texto = implode("\n", $this->constante);
        $texto = "<?php\n{$texto}\n?>";

        if($this->getDebug())
          echo "\nContenido: ".$texto."\n";

        if (fwrite($this->handle, $texto))
        {
             $this->constante = array();
            fclose($this->handle);
            return true;
        }
        else
        {
            $this->constante = array();
            fclose($this->handle);
            return false;
        }
    }

    public function Constante($constante,$valor){
        $constante  = $this->ConstanteFilter($constante);
        $valor      = $this->ValorFilter($valor);
        array_push($this->constante, "define('{$constante}', '{$valor}');");
        return $this;
    }

    public function ConstanteFilter($constante){

         $constante  = strip_tags($constante);
         $constante  = preg_replace($this->patronConstante,"", $constante);

         if($this->getDebug())
            echo "\nConstante: ".$constante."\n";

         if(strlen($constante)<=0)
            throw new Exception('Invalido Constante -- ConstanteFilter');

         return $constante;
    }

    public function getFileUrl($FileName){

        return $this->getrootFolderTienda().$this->getFolderUsuarios().$this->FileUsuarioFilter($FileName);
    }

    public function ValorFilter($valor){

        if($this->getDebug())
            echo "\nValor: ".$valor."\n";

        if(strlen($valor)===0)
            return $valor;

        if (!preg_match($this->patronValor, $valor))
             throw new Exception('Invalido Valor -- ValorFilter');
        
         return $valor;
    }

     public function FileUsuarioFilter($FileUsuario){

         if (preg_match($this->patronFileUsuario, $FileUsuario)) 
            return $FileUsuario.".php";
        else
            throw new Exception('Invalido Nombre Archivo -- FileUsuarioFilter');
    }

    public function Lectura($nl2br = false)
    {
        if ($read = fread($this->handle, filesize($this->Archivo)))
        {
            if ($nl2br == true)
            {
                fclose($this->handle);
                return nl2br($read);
            }

            fclose($this->handle);
            return $read;
        }
        else
        {
            fclose($this->handle);
            return false;
        }
    }

    public function Borrado($FileName)
    {
        $this->Archivo = $this->getFileUrl($FileName);

        if($this->getDebug())
          echo "\nBorrado Archivo: ".$this->Archivo."\n";

        if (file_exists($this->Archivo))
        {
            if (unlink($this->Archivo))
                return true;
            else
                return false;
        }else 
            //throw new Exception('No Existe Archivo -- Borrado');
        	return false;
     }

    public  function uniqID($lenght = 13) {
       
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("Ningun funciona para Random");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }

    public function test(){
        echo "test";
    }
}