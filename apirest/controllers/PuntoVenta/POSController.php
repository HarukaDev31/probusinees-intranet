<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//ini_set('memory_limit', '-1');
//ini_set('max_execution_time', 300); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class POSController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('PuntoVenta/POSModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
		$this->load->model('DocumentoElectronicoModel');
		$this->load->model('Ventas/VentaModel');
		$this->load->model('ComprobanteElectronicoSunatModel');
	}

	public function verPOS(){
		if(isset($this->session->userdata['usuario'])) {
			$rowImpuestoGratuita = $this->HelperModel->getImpuestoRegaloSUNAT();

			//verificar acceso a crear producto
			$sRutaOpcion='Logistica/ReglasLogistica/ProductoController/listarProductos';
			$bStatusOpcionProductoAdd = $this->MenuModel->verificarAccesoMenuInternoEstatico($sRutaOpcion)->Nu_Agregar;
			$iOcultarMenuIzquierdo = (($this->empresa->ID_Empresa!=346 && $this->empresa->ID_Empresa!=119 && $this->empresa->ID_Empresa != 121) ? 1 : 0);
			$this->load->view('header', array("iOcultarMenuIzquierdo" => $iOcultarMenuIzquierdo));
			$this->load->view('PuntoVenta/POSView', array('rowImpuestoGratuita' => $rowImpuestoGratuita, 'bStatusOpcionProductoAdd' => $bStatusOpcionProductoAdd));
			$this->load->view('footer', array("js_pos" => true));
		}
	}

	public function agregarVentaPos(){
		if(isset($this->session->userdata['usuario']) && isset($this->session->userdata['arrDataPersonal'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSModel->agregarVentaPos($this->input->post()));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'No se guardo venta. Primero debes aperturar caja'));
		}
	}
}
