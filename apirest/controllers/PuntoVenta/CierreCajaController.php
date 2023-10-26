<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 400); //300 seconds = 5 minutes y mas
date_default_timezone_set('America/Lima');

class CierreCajaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('PuntoVenta/CierreCajaModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			//Verificamos si la caja ya esta cerrada, si esta aperturada permite cerrar
			$iStatusAperturaCaja = '-';//Nada
			$arrValidacionCajaCerrada = array();
			if ( isset($this->session->userdata['arrDataPersonal']) )  {
				$arrValidacionCajaCerrada = $this->CierreCajaModel->validacionCajaCerrada();
				$iStatusAperturaCaja = $arrValidacionCajaCerrada['result']->Nu_Tipo;
			}

			$arrModalVentasMultiples = array();
			if ($iStatusAperturaCaja=='3') {//3=Si la caja esta aperturada
				if ( isset($this->session->userdata['arrDataPersonal']) && $this->session->userdata['arrDataPersonal']['sStatus']=='success' ) {
					$arrParams = array(
						'iIdMatriculaPersonal' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
						'dMatricula' => $this->session->userdata['arrDataPersonal']['arrData'][0]->Fe_Matricula,
					);
					$arrModalVentasMultiples = $this->CierreCajaModel->obtenerVentasMultiples($arrParams);
				}
			}
			$this->load->view('header');
			$this->load->view('PuntoVenta/CierreCajaView', array(
				'arrModalVentasMultiples' => $arrModalVentasMultiples,
				'arrValidacionCajaCerrada' => $arrValidacionCajaCerrada
			));
			$this->load->view('footer', array("js_cierre_caja" => true));
		}
	}

	public function addCierreCaja(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
					
			$arrResponseModal = $this->CierreCajaModel->addCierreCaja($this->input->post());
			if ( $arrResponseModal['sStatus']=='success' ) {
				$keys = array(
					'arrDataPersonal',
				);
				$this->session->unset_userdata($keys);
			}
			echo json_encode($arrResponseModal);
		}
	}
}
