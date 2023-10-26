<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HelperTiendaVirtualController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperTiendaVirtualModel');
		$this->load->model('MenuModel');

		if(!isset($this->session->userdata['usuario'])) {
			exit();	
		}
	}
	
	public function getReporteAlumnosMatriculadosxParams(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
			echo json_encode($this->HelperTiendaVirtualModel->getReporteAlumnosMatriculadosxParams($this->input->post()));
		} else {
			redirect('logout');
		}
	}
	
	public function getCategorias(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperTiendaVirtualModel->getCategorias($this->input->post()));
	}
	
	public function getSubCategorias(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperTiendaVirtualModel->getSubCategorias($this->input->post()));
	}

	public function getMarcas(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperTiendaVirtualModel->getMarcas());
	}

	public function getMedioPagoPagoTransferencia(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperTiendaVirtualModel->getMedioPagoPagoTransferencia());
	}

	public function getBancos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperTiendaVirtualModel->getBancos());
	}
	
	public function getAlmacenxTokenTienda(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperTiendaVirtualModel->getAlmacenxTokenTienda($this->input->post()));
	}
}
