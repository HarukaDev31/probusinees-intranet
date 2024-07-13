<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class InicioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->library('encryption');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ConfiguracionModel');
		$this->load->model('LoginModel');
		//$this->load->model('HelperModel');
		$this->load->model('HelperImportacionModel');

		if(!isset($this->session->userdata['usuario'])) {
			exit();	
		}
	}

	public function index(){
		if(isset($this->session->userdata['usuario'])) {
			//captar las ordenes que están sin asignar
			$arrResponsePedidoSinAsignar = $this->ConfiguracionModel->obtenerPedidosSinAsignar();
			$arrResponseCotizacionPedidoSinAsignar = $this->ConfiguracionModel->obtenerCotizacionesPedidosSinAsignar();
			//captar las ordenes que solo le pertence a ese usuario
			$arrResponsePedidoXUsuario = $this->ConfiguracionModel->obtenerPedidosXUsuario();
			$this->load->view('header_v2', array("js_inicio" => true));
			$this->load->view('Inicio/InicioView',array(
				'arrResponsePedidoXUsuario' => $arrResponsePedidoXUsuario,
				'arrResponsePedidoSinAsignar' => $arrResponsePedidoSinAsignar,
				'countCotizacionPedidosPendientes' => $arrResponseCotizacionPedidoSinAsignar->count	
			));
			$this->load->view('footer_v2', array("js_inicio" => true));
		} else {
			$this->load->view('Login/LoginView');
		}
	}
    
	public function enviarCorreoSoporteMigracionSistema(){
		$this->load->library('email');

		$data = array();
		$data["sRazonSocialEmpresa"] = $this->empresa->No_Empresa;
		$data["sNombreOrganizacion"] = 'Prueba';
		$data["dSolicitud"] = dateNow('fecha');
		$data["sUsuario"] = $this->user->No_Usuario;
		$data["sVersionCliente"] = $this->empresa->No_Version_Sistema;
		$data["sVersionNueva"] = NUEVA_VERSION_SISTEMA;
		$this->email->from('noreply@laesystems.com', $this->empresa->No_Empresa);//de
		$this->email->to('soporte@laesystems.com');//para
		$this->email->subject('Actualización de versión ' . $this->empresa->No_Version_Sistema . ' a ' . NUEVA_VERSION_SISTEMA);
		$message = $this->load->view('correos/actualizacion_sistema', $data, true);
		$this->email->message($message);
		$this->email->set_newline("\r\n");

		$isSend = TRUE; // C9
		
		$peticion = array(
			'status' => 'error',
			'style_modal' => 'modal-danger',
			'message' => 'No se pudo realizar la actualización, inténtelo más tarde.',
		);
		if($isSend) {
			$arrResponseEstadoActualizacionVersionSistema = $this->actualizarEstadoActualizacionVersionSistema($this->user->ID_Empresa, 1);//1=Actualizando
			if ( $arrResponseEstadoActualizacionVersionSistema['status']=='success' ) {
				$peticion = array(
					'status' => 'success',
					'style_modal' => 'modal-success',
					'message' => 'Se esta actualizando la nueva versión del sistema, se le notificará en cuanto haya culminado.',
				);
				echo json_encode($peticion);
				exit();
			}
			$peticion = $arrResponseEstadoActualizacionVersionSistema;
		}
		echo json_encode($peticion);
	}
	
	public function actualizarEstadoActualizacionVersionSistema($iIDEmpresa, $iEstadoActualizacionVersionSistema){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
		    'Nu_Estado_Actualizacion_Version_Sistema' => $iEstadoActualizacionVersionSistema,
		);
		$where = array('ID_Empresa' => $iIDEmpresa);
		return $this->ConfiguracionModel->actualizarEstadoActualizacionVersionSistema($where, $data);
	}
	
	public function Ajax($action){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		switch($action){
			case "Reporte":
				$arrReporteGraficoPOST = $this->input->post('arrReporteGrafico');
				$arrRowGrafico = $this->ConfiguracionModel->reporteGrafico($arrReporteGraficoPOST);
				
				$titulo = 'Reporte Mensual';
				echo $this->load->view('Inicio/_InicioView', array(
					"reporte"   => $arrRowGrafico,
					"dInicial"  => $arrReporteGraficoPOST['dInicial'],
					"dFinal"    => $arrReporteGraficoPOST['dFinal'],
					"iIDMoneda" => $arrReporteGraficoPOST['iIDMoneda'],
					"iImpuesto" => $arrReporteGraficoPOST['iImpuesto'],
				), true);
				$this->load->view('footer_assets_inicio');
				break;
		}
	}

	public function crearCuentaPais(){
		//array_debug($this->input->post('sCodigoPaisCuentaUsuario'));

		$iCodeCountry='51';//peru
		if($this->input->post('sCodigoPaisCuentaUsuario')=='MX'){
			$iCodeCountry='52';
		}

		$iCelular = $this->user->Nu_Celular;
		$sEmail = $this->user->No_Usuario;

		$objUsuarioCuenta = $this->ConfiguracionModel->obtenerDatosUsuarioCreacionNuevaCuenta();
		$sPassword = $this->encryption->decrypt($objUsuarioCuenta->No_Password);

		//CREAR TIENDA por API de librerias.laesystems
		//$arrParamsFE['ruta'] = "http://localhost/librerias.ecxpresslae.com/ApiController/new_user_client_lae";//localhost
		$arrParamsFE['ruta'] = 'https://ecxpresslae.com/librerias/ApiController/new_user_client_lae';//cloud
		$arrParamsFE['token'] = 'mrrN2CoLA3hidRyds5Yi6lJjz7Fep0R03PzurEif';

		$arrPost = array(
			'Nu_Tipo_Proveedor_FE' => 3,//para tiendaris colocamos -> 3=INTERNO
			'ID_Tipo_Documento_Identidad' => 1,//para tiendaris colocamos -> 1=OTROS (2=DNI 4=RUC y 1=OTROS)
			'Nu_Documento_Identidad' => rand(123456,654321),//tiene que ser así porque el sistema valida numero de documento de identidad
			'Nu_Celular' => $iCelular,
			'Txt_Email' => $sEmail,
			'No_Nombres_Apellidos' => '-',//para tiendaris colocamos -> -
			'Nu_Lae_Gestion' => 0,//LaeGestion
			'Nu_Lae_Shop' => 1,//LaeShop
			'Nu_Tipo_Plan_Lae_Gestion' => 0,//para tiendaris colocamos -> 0=Ninguno
			'ID_Tipo_Rubro_Empresa' => 16,//para tiendaris colocamos -> 16=General
			'No_Password' => $sPassword,//Nuevo campo agregado
			'Nu_Vendedor_Dropshipping' => 1,//Vendedor activado para dropshipping
			'iCodeCountry' => $iCodeCountry,//codigo de pais
			'iCodigoReferido'=> '',// Codigo Referido
			'sCuentaUsuarioCountry' => $this->input->post('sCodigoPaisCuentaUsuario'),//cuenta de usuario de pais
			'sVincularUsuarioCountry' => 1
		);
		$data_json = json_encode($arrPost);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $arrParamsFE['ruta']);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$arrParamsFE['token'].'"',
			'Content-Type: application/json',
			'X-API-Key: ' . $arrParamsFE['token'],
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respuesta = curl_exec($ch);
		curl_close($ch);
		
		$respuesta = json_decode($respuesta);
		
		echo json_encode($respuesta);
		exit();
	}

	public function cambioPaisSessionUsuario($sCodigoPaisCuentaUsuario){
		if(strtoupper($this->user->No_Grupo) != 'DELIVERY'){
			$No_Usuario = $this->user->No_Usuario;
			$Nu_Codigo_Sunat_ISO = $sCodigoPaisCuentaUsuario;

			var_dump($No_Usuario);
			var_dump($Nu_Codigo_Sunat_ISO);

			//CREANDO NUEVA SESSION
			$arrParams = array(
				"No_Usuario" => $No_Usuario,
				"Nu_Codigo_Sunat_ISO" => $Nu_Codigo_Sunat_ISO
			);
			$response = $this->LoginModel->creandoNuevaSessionUsuario($arrParams);
			//array_debug($response);
			if($response['sStatus']=='success'){
				$this->empresa = $this->ConfiguracionModel->obtenerEmpresa();
				redirect('InicioController');
			} else {
				array_debug($response);
				//exit('No se puede eliminar y acceder ' . $response['sMessage']);
			}
		} else {
			redirect('DeliveryDropshippingController/listar');
		}
	}

	public function crudCliente(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			//array_debug($this->input->post());
			
			$sNumeroDocumentoIdentidad = strtoupper(trim($this->input->post('Nu_Documento_Identidad')));
			$iTipoDocumentoIdentidad = '4';//4=RUC
			$sTipoDocumentoIdentidad = 'RUC';
			if ( strlen($sNumeroDocumentoIdentidad) != 11 ) {
				$iTipoDocumentoIdentidad = '1';//1=OTROS
				$sTipoDocumentoIdentidad = 'OTROS';
			}
			
			$sNumeroDocumentoIdentidadExterno = strtoupper(trim($this->input->post('Nu_Documento_Identidad_Externo')));
			$iTipoDocumentoIdentidadExterno = '4';//4=RUC
			$sTipoDocumentoIdentidadExterno = 'RUC';
			if ( strlen($sNumeroDocumentoIdentidadExterno) != 11 ) {
				$iTipoDocumentoIdentidadExterno = '1';//1=OTROS
				$sTipoDocumentoIdentidadExterno = 'OTROS';
			}

			$data = array(
				'ID_Tipo_Documento_Identidad'		=> $iTipoDocumentoIdentidad,
				'Nu_Documento_Identidad'			=> $sNumeroDocumentoIdentidad,
				'No_Entidad'						=> $this->input->post('No_Entidad'),
				'No_Contacto'						=> $this->input->post('No_Contacto'),
				'ID_Tipo_Documento_Identidad_Externo' => $iTipoDocumentoIdentidadExterno,
				'Nu_Documento_Identidad_Externo'	=> $sNumeroDocumentoIdentidadExterno,
			);

			echo json_encode($this->LoginModel->actualizarCliente(array('ID_Entidad' => $this->input->post('ID_Entidad')), $data, $this->input->post('ENo_Entidad')));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
		}
	}
}
	