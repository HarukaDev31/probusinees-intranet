<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImprimirLiquidacionCajaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImprimirLiquidacionCajaModel');

		if(!isset($this->session->userdata['usuario'])) {
			exit();	
		}
	}
	
	public function formatoImpresionLiquidacionCaja(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->ImprimirLiquidacionCajaModel->formatoImpresionLiquidacionCaja($this->input->post()));
	}
}
