<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class POSRestauranteController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('PuntoVenta/POSRestauranteModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
		$this->load->model('DocumentoElectronicoModel');
		$this->load->model('Ventas/VentaModel');
	}

	public function verEscenariosRestaurante($iIdEscenarioRestaurante=0){
		if(isset($this->session->userdata['usuario'])) {
			if ($iIdEscenarioRestaurante==0) {
				$arrResponseFirst = $this->POSRestauranteModel->allEscenarioFirst();
				if ($arrResponseFirst['status']=='success') {
					$iIdEscenarioRestaurante = $arrResponseFirst['result'][0]->ID_Escenario_Restaurante;
				}
			}

			$iOcultarMenuIzquierdo = (($this->empresa->ID_Empresa!=346 && $this->empresa->ID_Empresa!=119 && $this->empresa->ID_Empresa != 121) ? 1 : 0);
			$this->load->view('header', array("iOcultarMenuIzquierdo" => $iOcultarMenuIzquierdo));
			$this->load->view('PuntoVenta/EscenariosRestauranteView',
				array(
					'iIdEscenarioRestaurante' => $iIdEscenarioRestaurante
				)
			);
			$this->load->view('footer', array("js_escenarios_restaurante" => true));
		}
	}

	public function allEscenario($iIdEscenarioRestaurante){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->allEscenario($iIdEscenarioRestaurante));
		}
	}

	public function crudEscenario(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			$data = array(
				'ID_Empresa' => $this->empresa->ID_Empresa,
				'ID_Organizacion' => $this->empresa->ID_Organizacion,
				'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
				'No_Escenario_Restaurante' => $this->input->post('No_Escenario_Restaurante'),
				'Nu_Estado'	=> 1,
			);
			echo json_encode(
			($this->input->post('EID_Escenario_Restaurante') != '') ?
				$this->POSRestauranteModel->updEscenario(array('ID_Escenario_Restaurante' => $this->input->post('EID_Escenario_Restaurante')), $data, $this->input->post('ENo_Escenario_Restaurante'))
			:
				$this->POSRestauranteModel->addEscenario($data)
			);
		}
	}

	public function verEscenario($ID){
		if(isset($this->session->userdata['usuario'])) {
        	echo json_encode($this->POSRestauranteModel->verEscenario($this->security->xss_clean($ID)));
		}
    }
    
	public function eliminarEscenario($ID){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->eliminarEscenario($this->security->xss_clean($ID)));
		}
	}

	public function crudEscenarioMesa(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			$data = array(
				'ID_Empresa' => $this->empresa->ID_Empresa,
				'ID_Organizacion' => $this->empresa->ID_Organizacion,
				'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
				'ID_Escenario_Restaurante' => $this->input->post('ID_Escenario_Restaurante'),
				'No_Mesa_Restaurante' => $this->input->post('No_Mesa_Restaurante'),
				'Nu_Estado'	=> $this->input->post('Nu_Estado'),
			);
			echo json_encode(
			($this->input->post('EID_Mesa_Restaurante') != '') ?
				$this->POSRestauranteModel->updEscenarioMesa(array('ID_Mesa_Restaurante' => $this->input->post('EID_Mesa_Restaurante')), $data, $this->input->post('ENo_Mesa_Restaurante'))
			:
				$this->POSRestauranteModel->addEscenarioMesa($data)
			);
		}	
	}

	public function verEscenarioMesa($ID){
		if(isset($this->session->userdata['usuario'])) {
        	echo json_encode($this->POSRestauranteModel->verEscenarioMesa($this->security->xss_clean($ID)));
		}
    }
    
	public function eliminarMesa($ID){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->eliminarMesa($this->security->xss_clean($ID)));
		}
	}

	public function allEscenarioMesas($iIdEscenarioRestaurante){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->allEscenarioMesas($iIdEscenarioRestaurante));
		}
	}

	public function allMesasRestaurante(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->allMesasRestaurante());
		}
	}

	public function verPOSRestaurante($iIdEscenarioRestaurante = 0, $iIdEscenarioMesaRestaurante = 0, $iIdPedidoCabecera = 0){
		if(isset($this->session->userdata['usuario'])) {
			$rowImpuestoGratuita = $this->HelperModel->getImpuestoRegaloSUNAT();

			$sRutaOpcion='Logistica/ReglasLogistica/ProductoController/listarProductos';
			$bStatusOpcionProductoAdd = $this->MenuModel->verificarAccesoMenuInternoEstatico($sRutaOpcion)->Nu_Agregar;

			$iOcultarMenuIzquierdo = (($this->empresa->ID_Empresa!=346 && $this->empresa->ID_Empresa!=119 && $this->empresa->ID_Empresa != 121) ? 1 : 0);
			$this->load->view('header', array("iOcultarMenuIzquierdo" => $iOcultarMenuIzquierdo));
			$this->load->view('PuntoVenta/POSRestauranteView',
				array(
					'iIdEscenarioRestaurante' => $iIdEscenarioRestaurante,
					'iIdEscenarioMesaRestaurante' => $iIdEscenarioMesaRestaurante,
					'iIdPedidoCabecera' => $iIdPedidoCabecera,
					'rowImpuestoGratuita' => $rowImpuestoGratuita,
					'bStatusOpcionProductoAdd' => $bStatusOpcionProductoAdd
				)
			);
			$this->load->view('footer', array("js_pos_restaurante" => true));
		}
	}

	public function imprimirPreCuentaYGuardar(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->imprimirPreCuentaYGuardar($this->input->post()));
		}
	}

	public function agregarVentaPos(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->agregarVentaPos($this->input->post()));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'No se guardo venta. Primero debes aperturar caja'));
		}
	}

	public function liberarMesa(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->POSRestauranteModel->liberarMesa($this->input->post()));
		}
	}
}