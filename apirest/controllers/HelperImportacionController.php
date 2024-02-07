<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HelperImportacionController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperImportacionModel');
		$this->load->model('MenuModel');

		if(!isset($this->session->userdata['usuario'])) {
			$sapi_type = php_sapi_name();
			if (substr($sapi_type, 0, 3) == 'cgi')
				header("Status: 404 Not Found");
			else
				header("HTTP/1.1 404 Not Found");
			exit();
		}
	}
	
	public function getCategorias(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperImportacionModel->getCategorias());
	}
	
	public function getUsuarioChina(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperImportacionModel->getUsuarioChina());
	}
}
