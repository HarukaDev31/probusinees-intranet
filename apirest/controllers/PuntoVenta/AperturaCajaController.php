<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class AperturaCajaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('PuntoVenta/AperturaCajaModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('PuntoVenta/AperturaCajaView');
			$this->load->view('footer', array("js_apertura_caja" => true));
		}
	}
	
	public function addMatriculaPersonal(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			
			$arrReponseVerificarPersonal = $this->HelperModel->getPersonal($this->input->post());
			if ( $arrReponseVerificarPersonal['sStatus'] == 'success') {
				$arrData = array(
					'ID_POS' => $this->input->post('iIdPos'),
					'ID_Entidad' => $arrReponseVerificarPersonal['arrData'][0]->ID_Entidad,
					'ID_Tipo_Operacion_Caja' => $this->input->post('iIdTipoOperacionCaja'),
					'ID_Moneda' => $this->input->post('iIdMoneda'),
					'Ss_Total' => $this->input->post('fAperturaCaja'),
					'Txt_Nota' => $this->input->post('sNotaCaja'),
				);
				
				//MP = Matricula Personal
				//AC = Apertura de Caja
				$arrResponseModal = $this->AperturaCajaModel->agregarMPyAC($arrData);
				if ( $arrResponseModal['sStatus']=='success' ) {
					$this->iniciarSesionTemporalxCajaPersonal($arrResponseModal['ID_Matricula_Empleado'], $this->input->post('iIdMoneda'));
				}
				echo json_encode($arrResponseModal);
				exit();
			} else {
				echo json_encode($arrReponseVerificarPersonal);
			}
		}
	}

	// Crear sesion temporal por punto de venta
	public function verificarPersonalxPIN(){
		if(isset($this->session->userdata['usuario'])) {
			$iIdMatriculaEmpleado=$this->input->post('iIdMatriculaEmpleado');
			$iIdMonedaCajaPos=$this->input->post('iIdMonedaCajaPos');		
			$arrResponsePIN = $this->HelperModel->getMatriculaPersonal($iIdMatriculaEmpleado, $iIdMonedaCajaPos);
			if ( $arrResponsePIN['sStatus'] == 'success' ) {
				if ($arrResponsePIN['arrData'][0]->Nu_Pin_Caja == $this->input->post('iPin') ) {
					$arrResponseModalPersonalPIN = $this->HelperModel->getPersonal($this->input->post());
					if ( $arrResponseModalPersonalPIN['sStatus'] == 'success' ) {
						$iIdMatriculaEmpleado=$this->input->post('iIdMatriculaEmpleado');
						$iIdMonedaCajaPos=$this->input->post('iIdMonedaCajaPos');
						
						$arrResponseiniciarSesionTemporalxCajaPersonal = $this->iniciarSesionTemporalxCajaPersonal($iIdMatriculaEmpleado, $iIdMonedaCajaPos);
						
						$arrResponseModalPersonalPIN = array(
							'sStatus' => 'success',
							'sMessage' => 'Proceso exitoso',
						);
						echo json_encode($arrResponseModalPersonalPIN);
						exit();
					} else {
						echo json_encode($arrResponseModalPersonalPIN);
					}
				} else {
					$arrResponseModalPersonalPIN = array(
						'sStatus' => 'warning',
						'sMessage' => 'El PIN ingresado es diferente al PIN registrado en la Apertura de Caja, corregir.',
					);
					echo json_encode($arrResponseModalPersonalPIN);
					exit();
				}
			} else {
				echo json_encode($arrResponsePIN);
			}
		}
	}

	public function iniciarSesionTemporalxCajaPersonal($iIdMatriculaEmpleado, $iIdMoneda){
		$arrReponseModalMatriculaPersonal = $this->HelperModel->getMatriculaPersonal($iIdMatriculaEmpleado, $iIdMoneda);
		$arrDataPersonal = array(
			'arrDataPersonal' => $arrReponseModalMatriculaPersonal,
		);
		$this->session->set_userdata($arrDataPersonal);
	}
}
