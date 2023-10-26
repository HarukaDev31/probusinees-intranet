<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class LoginController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->library('encryption');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('LoginModel');
		$this->load->model('HelperModel');
	}

	public function index(){
		if(isset($this->session->userdata['usuario'])) {
			redirect('InicioController/index');
		} else {
			$this->load->view('Login/LoginView');
		}
	}
	
	public function recuperar_cuenta($Txt_Token_Activacion){
		$data = array();
		$data["token"] = $Txt_Token_Activacion;
		$this->load->view('Login/recuperar_cuenta', $data);
	}
	
	public function post(){
		if($this->input->is_ajax_request()){
			$data = array(
				'No_Usuario' => strtolower($this->input->post('No_Usuario')),
				'No_Password' => $this->input->post('No_Password'),
				'ID_Empresa' => $this->input->post('ID_Empresa'),
				'ID_Organizacion' => $this->input->post('ID_Organizacion'),
			);
			
			$arrModelLogin = $this->LoginModel->verificarAccesoLogin($data);
			echo json_encode($arrModelLogin);
			exit();
		}else{
			show_404();
		}
	}
	
	private function token(){
        return sha1(uniqid(rand(),true));
    }
    
	public function verificar_email(){
		if($this->input->is_ajax_request()){
			$sendPost = array(
				'Txt_Email' => $this->input->post('Txt_Email_Recovery'),
				'Txt_Token_Activacion' => $this->token(),
			);
			
			$peticion = $this->LoginModel->verificar_email($sendPost);

			if($peticion['status'] == 'success' ) {
				$this->load->library('email');

				$data = array();
				$data["token"] = $sendPost['Txt_Token_Activacion'];
                $message = $this->load->view('correos/recuperar_cuenta', $data, true);
                
				$this->email->from('noreply@lae.one', 'Ecxlae');//de
				$this->email->to($this->input->post('Txt_Email_Recovery'));//para
				$this->email->subject('Recuperar cuenta');
				$this->email->message($message);
				$this->email->set_newline("\r\n");

				$isSend = $this->email->send();
				//log_message('error', $this->email->print_debugger());
				//$isSend = TRUE;
				
				if($isSend) {
					$peticion = array(
						'status' => 'success',
						'type' => 'user',
						'message' => 'Correo enviado. Si no se encuentra en tu bandeja de entrada, revisar en correo no deseado o spam, luego confirmar como seguro.',
					);
				} else {
					$peticion = array(
						'status' => 'error',
						'type' => 'user',
						'message' => 'No se pudo enviar el correo, inténtelo más tarde.',
						'eror' => $this->email->print_debugger()
					);
				}
			}
			echo json_encode($peticion);
		}else{
			show_404();
		}
	}
    
	public function cambiar_clave(){
		if($this->input->is_ajax_request()){
			$data = array(
				'No_Password' => $this->encryption->encrypt($this->input->post('No_Password')),
				'Txt_Token_Activacion' => $this->input->post('Txt_Token_Activacion'),
			);
			echo json_encode($this->LoginModel->cambiar_clave($data));
		} else{
			show_404();
		}
	}
	
	public function logout(){
		$this->session->unset_userdata('almacen');
		unset(
			$_SESSION['almacen']->ID_Empresa,
			$_SESSION['almacen']->ID_Organizacion,
			$_SESSION['almacen']->ID_Almacen,
			$_SESSION['almacen']->No_Almacen
		);
		$this->session->sess_destroy();

		$this->session->unset_userdata('usuario');
		unset(
			$_SESSION['usuario']->ID_Empresa,
			$_SESSION['usuario']->ID_Organizacion,
			$_SESSION['usuario']->ID_Usuario,
			$_SESSION['usuario']->No_Usuario,
			$_SESSION['usuario']->No_Nombres_Apellidos,
			$_SESSION['usuario']->Txt_Email,
			$_SESSION['usuario']->Txt_Token_Activacion,
			$_SESSION['usuario']->ID_Grupo,
			$_SESSION['usuario']->No_IP,
			$_SESSION['usuario']->Fe_Creacion,
			$_SESSION['usuario']->Nu_Estado,
			$_SESSION['usuario']->No_Grupo,
			$_SESSION['usuario']->No_Grupo_Descripcion,
			$_SESSION['usuario']->ID_Grupo_Usuario
		);
		$this->session->sess_destroy();
		redirect('');
	}

	public function getTiposDocumentoIdentidad(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->LoginModel->getTiposDocumentoIdentidad());
	}
	
	public function getValoresTablaDato(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->LoginModel->getValoresTablaDato($this->input->post()));
	}

	public function crear_cuenta(){
        //$arrParamsFE['ruta'] = "http://localhost/librerias.laesystems.com/ApiController/new_user_client_lae";//localhost
        $arrParamsFE['ruta'] = 'https://laesystems.com/librerias/ApiController/new_user_client_lae';//cloud
        $arrParamsFE['token'] = 'mrrN2CoLA3hidRyds5Yi6lJjz7Fep0R03PzurEif';

		$Nu_Tipo_Proveedor_FE = $this->input->post('Nu_Tipo_Proveedor_FE');
		$ID_Tipo_Documento_Identidad = $this->input->post('ID_Tipo_Documento_Identidad');
		$Nu_Documento_Identidad = $this->input->post('Nu_Documento_Identidad');
		$Txt_Email = $this->input->post('Txt_Email');
		$No_Nombres_Apellidos = $this->input->post('No_Nombres_Apellidos');
		$Nu_Tipo_Plan_Lae_Gestion = $this->input->post('Nu_Tipo_Plan_Lae_Gestion');
		$ID_Tipo_Rubro_Empresa = $this->input->post('ID_Tipo_Rubro_Empresa');

		$Nu_Celular = '';
		if ( strlen($this->input->post('Nu_Celular')) == 11){
	        $Nu_Celular = explode(' ', $this->input->post('Nu_Celular'));
	        $Nu_Celular = $Nu_Celular[0].$Nu_Celular[1].$Nu_Celular[2];
		}

        $arrPost = array(
            'Nu_Tipo_Proveedor_FE' => $Nu_Tipo_Proveedor_FE,//para tiendaris colocamos -> 3=INTERNO
            'ID_Tipo_Documento_Identidad' => $ID_Tipo_Documento_Identidad,//para tiendaris colocamos -> 1=OTROS (2=DNI 4=RUC y 1=OTROS)
            'Nu_Documento_Identidad' => strtoupper($Nu_Documento_Identidad),//para tiendaris colocamos -> -
            'Nu_Celular' => $Nu_Celular,
			'Txt_Email' => $Txt_Email,
            'No_Nombres_Apellidos' => $No_Nombres_Apellidos,//para tiendaris colocamos -> -
            'Nu_Lae_Gestion' => 1,//LaeGestion
			'Nu_Lae_Shop' => 0,//LaeShop
            'Nu_Tipo_Plan_Lae_Gestion' => $Nu_Tipo_Plan_Lae_Gestion,//para tiendaris colocamos -> 0=Ninguno
            'ID_Tipo_Rubro_Empresa' => $ID_Tipo_Rubro_Empresa,//para tiendaris colocamos -> 16=General
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

        echo $respuesta;
	}
}
