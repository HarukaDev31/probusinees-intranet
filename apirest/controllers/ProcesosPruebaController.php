<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProcesosPruebaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('MenuModel');
		
		if(!isset($this->session->userdata['usuario'])) {
			exit;
		}
	}
	
	public function generarToken(){
		echo "hola verificarPagoSistemaPrueba";
	}
}
