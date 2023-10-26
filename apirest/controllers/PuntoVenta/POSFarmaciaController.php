<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class POSFarmaciaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('PuntoVenta/POSFarmaciaModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
		$this->load->model('DocumentoElectronicoModel');
		$this->load->model('Ventas/VentaModel');
	}

	public function verPOS(){
		if(isset($this->session->userdata['usuario'])) {
			
			$sRutaOpcion='Logistica/ReglasLogistica/ProductoController/listarProductos';
			$bStatusOpcionProductoAdd = $this->MenuModel->verificarAccesoMenuInternoEstatico($sRutaOpcion)->Nu_Agregar;

			$this->load->view('header', array("iOcultarMenuIzquierdo" => 1));
			$this->load->view('PuntoVenta/POSFarmaciaView',
				array(
					'bStatusOpcionProductoAdd' => $bStatusOpcionProductoAdd
				));
			$this->load->view('footer', array("js_pos_farmacia" => true));
		}
	}

	public function agregarVentaPos(){
		if(isset($this->session->userdata['usuario']) && isset($this->session->userdata['arrDataPersonal'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSFarmaciaModel->agregarVentaPos($this->input->post()));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'No se guardo venta. Primero debes aperturar caja'));
		}
	}
}
