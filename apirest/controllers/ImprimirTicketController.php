<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImprimirTicketController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImprimirTicketModel');

		if(!isset($this->session->userdata['usuario'])) {
			exit();	
		}
	}
	
	public function formatoImpresionTicket(){
		//if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		$data = $this->ImprimirTicketModel->formatoImpresionTicket($this->input->post('ID_Documento_Cabecera'));
		
        echo json_encode(
        	array (
        		'totalEnLetras' => $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),
				'arrTicket' => $data,
        	)
        );
	}
	
	public function formatoImpresionTicketPreCuenta(){
		//if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		$data = $this->ImprimirTicketModel->formatoImpresionTicketPreCuenta($this->input->post('ID_Pedido_Cabecera'));
		if ( !empty($data) ) {
			echo json_encode(array ('totalEnLetras' => $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),'arrTicket' => $data,));
			exit();
		} else {
			echo json_encode(array ('arrTicket' => 0,));
			exit();
		}
	}
	
	public function formatoImpresionTicketOrden(){
		//if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		$data = $this->ImprimirTicketModel->formatoImpresionTicketOrden($this->input->post('ID_Venta_Temporal_Cabecera'));
        echo json_encode(
        	array (
        		'totalEnLetras' => $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),
        		'arrTicket' => $data
        	)
        );
	}

	public function formatoImpresionTicketComandaLavado(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->ImprimirTicketModel->formatoImpresionTicketComandaLavado($this->input->post()));
	}
	
	public function formatoImpresionTicketGuia(){
		$data = $this->ImprimirTicketModel->formatoImpresionTicketGuia($this->input->post('ID_Documento_Cabecera'));
		
        echo json_encode(
        	array (
				'arrTicket' => $data,
        	)
        );
	}
}
