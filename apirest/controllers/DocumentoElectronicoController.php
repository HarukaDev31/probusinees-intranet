<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentoElectronicoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('DocumentoElectronicoModel');

		if(!isset($this->session->userdata['usuario'])) {
			$sapi_type = php_sapi_name();
			if (substr($sapi_type, 0, 3) == 'cgi')
				header("Status: 404 Not Found");
			else
				header("HTTP/1.1 404 Not Found");
			exit();
		}
	}
	
	function consultarDocumentoElectronicoSunat() {
		$arrParams = array(
			'iCodigoProveedorDocumentoElectronico' => 1,
			'iEstadoVenta' => $this->input->post('iEstado'),
			'iIdDocumentoCabecera' => $this->input->post('ID'),
			'sEmailCliente' => '',
			'sTipoRespuesta' => $this->input->post('sTipoRespuesta'),
			'sTipoBajaSunat' => 'RC',
		);
		$response = $this->DocumentoElectronicoModel->consultarDocumentoElectronicoSunat($arrParams);
		if ($this->input->post('sTipoRespuesta')=='json') {
			echo json_encode($response);
			exit();
		} else {
			return $response;
		}
	}
	
	function consultarGuiaElectronicoPSENubefactReseller() {
		$arrParams = array(
			'iEstadoVenta' => $this->input->post('iEstado'),
			'iIdGuiaCabecera' => $this->input->post('ID'),
			'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
			'ID_Serie_Documento' => $this->input->post('ID_Serie_Documento'),
			'ID_Numero_Documento' => $this->input->post('ID_Numero_Documento')
		);
		$response = $this->DocumentoElectronicoModel->consultarGuiaElectronicoPSENubefactReseller($arrParams);
		if ($this->input->post('sTipoRespuesta')=='json') {
			echo json_encode($response);
			exit();
		} else {
			return $response;
		}
	}
	
	function consultarGuiaElectronicoSunatV2() {
		$arrParams = array(
			'iEstadoVenta' => $this->input->post('iEstado'),
			'iIdGuiaCabecera' => $this->input->post('ID'),
			'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
			'ID_Serie_Documento' => $this->input->post('ID_Serie_Documento'),
			'ID_Numero_Documento' => $this->input->post('ID_Numero_Documento')
		);
		$response = $this->DocumentoElectronicoModel->consultarGuiaElectronicoSunatV2($arrParams);
		if ($this->input->post('sTipoRespuesta')=='json') {
			echo json_encode($response);
			exit();
		} else {
			return $response;
		}
	}
	
	function recuperarPDFVentaSunat() {
		$arrParams = array(
			'iIdDocumentoCabecera' => $this->input->post('iIdDocumentoCabecera')
		);
		$response = $this->DocumentoElectronicoModel->recuperarPDFVentaSunat($arrParams);
		if ($this->input->post('sTipoRespuesta')=='json') {
			echo json_encode($response);
			exit();
		} else {
			return $response;
		}
	}
}