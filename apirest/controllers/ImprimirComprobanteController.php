<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImprimirComprobanteController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImprimirComprobanteModel');

		if(!isset($this->session->userdata['usuario'])) {
			exit();	
		}
	}
	
	public function formatoImpresionComprobante(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->ImprimirComprobanteModel->formatoImpresionComprobante($this->input->post('ID_Documento_Cabecera'), $this->input->post('ID_Tipo_Documento'), $this->input->post('f')));
	}
}