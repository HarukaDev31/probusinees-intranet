<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HelperDropshippingController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperDropshippingModel');
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
	
	public function listarBanco(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperDropshippingModel->listarBanco($this->input->post()));
	}
	
	public function getProveedoresDropshipping(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperDropshippingModel->getProveedoresDropshipping($this->input->post()));
	}
	
	public function getProveedoresDropshippingSinPais(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperDropshippingModel->getProveedoresDropshippingSinPais($this->input->post()));
	}
	
	public function getProveedoresAlmacenesDropshipping(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperDropshippingModel->getProveedoresAlmacenesDropshipping($this->input->post()));
	}
	
	public function globalAutocomplete(){
		if ($this->input->is_ajax_request() && strlen($this->input->post('global_search'))>2){
			$global_search = $this->input->post('global_search');
			echo json_encode($this->HelperDropshippingModel->getDataAutocompleteProduct($global_search));
		}
	}
	
	public function listarUsuarioDelivery(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperDropshippingModel->listarUsuarioDelivery($this->input->post()));
	}
	
	public function globalAutocompleteMisProductos(){
		if ($this->input->is_ajax_request() && strlen($this->input->post('global_search'))>2){
			$global_search = $this->input->post('global_search');
			echo json_encode($this->HelperDropshippingModel->getDataAutocompleteMisProductos($global_search));
		}
	}
	
	public function listarTodosPaises(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperDropshippingModel->listarTodosPaises($this->input->post()));
	}
}
