<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->library('encryption');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PanelAcceso/UsuarioModel');
		$this->load->model('HelperModel');
	}

	public function listarUsuarios($sUsuario=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('inicio');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('PanelAcceso/UsuarioView', array('sUsuario' => $sUsuario));
			$this->load->view('footer_v2', array("js_usuario" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->UsuarioModel->get_datatables();
        $data = array();
        $action = 'delete';
        foreach ($arrData as $row) {
            $rows = array();
			if ( $this->user->ID_Usuario == 1 ){
				$rows[] = $row->No_Empresa;
				$rows[] = $row->No_Organizacion;
			}
            $rows[] = $row->No_Grupo;
            $rows[] = $row->No_Usuario;
            $rows[] = $row->No_Nombres_Apellidos;
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

            $rows[] = (!empty($row->No_Grupo) ? '<button class="btn btn-xs btn-link btn-upd" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verUsuario(\'' . $row->ID_Usuario . '\')"><i class="far fa-2x fa-edit" aria-hidden="true"></i></button>' : '');

			$btn_elminar = '<button class="btn btn-xs btn-link btn-upd" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarUsuario(\'' . $row->ID_Usuario . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $rows[] = ($row->ID_Usuario != 1 ? $btn_elminar : '');
            $data[] = $rows;
        }
        $output = array(
	        "data" => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        $_data = $this->UsuarioModel->get_by_id($this->security->xss_clean($ID));
        $data = array(
        	'ID_Empresa' => $_data->ID_Empresa,
        	'ID_Organizacion' => $_data->ID_Organizacion,
        	'ID_Grupo'	=> $_data->ID_Grupo,
        	'ID_Usuario' => $_data->ID_Usuario,
        	'No_Usuario' => $_data->No_Usuario,
        	'No_Nombres_Apellidos' => $_data->No_Nombres_Apellidos,
        	'No_Password' => $this->encryption->decrypt($_data->No_Password),
        	'Nu_Celular' => $_data->Nu_Celular,
        	'Txt_Email' => $_data->Txt_Email,
        	'Nu_Estado' => $_data->Nu_Estado
        );
        echo json_encode($data);
    }
    
	public function crudUsuario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$Nu_Celular = $this->input->post('Nu_Celular');
		if ( $this->input->post('Nu_Celular') && strlen($this->input->post('Nu_Celular')) == 11){
	        $Nu_Celular = explode(' ', $this->input->post('Nu_Celular'));
	        $Nu_Celular = $Nu_Celular[0].$Nu_Celular[1].$Nu_Celular[2];
		}

		//validacion de email
		$sEmail = trim($this->input->post('No_Usuario'));
		$sEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);// Remove all illegal characters from email
		$regex = '/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/';// regular expression for email check
		//echo $sEmail;echo "<br>";
		//var_dump(filter_var($sEmail, FILTER_VALIDATE_EMAIL));
		//var_dump(preg_match($regex, $sEmail));
		if ( $sEmail != 'root' && !filter_var($sEmail, FILTER_VALIDATE_EMAIL) && !preg_match($regex, $sEmail) ) {
			echo json_encode(array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Debes ingresar un email válido.'));
			exit();
		}

		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Organizacion' => $this->input->post('ID_Organizacion'),
			'ID_Grupo'				=> $this->input->post('ID_Grupo'),
			'No_Usuario'			=> $sEmail,
			'No_Nombres_Apellidos'	=> $this->input->post('No_Nombres_Apellidos'),
			'No_Password'			=> $this->encryption->encrypt($this->input->post('No_Password')),
			'Txt_Email'				=> $sEmail,
			'Txt_Token_Activacion'	=> $this->encryption->encrypt($this->input->post('Txt_Token_Activacion')),
			'No_IP'					=> $this->input->ip_address(),
			'Nu_Estado'				=> $this->input->post('Nu_Estado'),
		);
		if ( !empty($Nu_Celular) && $Nu_Celular >= 9 ){
			$data = array_merge($data, array('Nu_Celular' => $Nu_Celular));
		}
		echo json_encode(
		($_POST['EID_Organizacion'] != '' || $_POST['EID_Grupo'] != '' && $_POST['EID_Usuario'] != '') ?
			$this->UsuarioModel->actualizarUsuario(array('ID_Organizacion' => $this->input->post('EID_Organizacion'), 'ID_Grupo' => $this->input->post('EID_Grupo'), 'ID_Usuario' => $this->input->post('EID_Usuario')), $data, $this->input->post('EID_Grupo'), $this->input->post('ENo_Usuario'), $this->input->post('ENu_Celular'), $this->input->post('ETxt_Email'), $this->input->post('ENu_Estado'))
		:
			$this->UsuarioModel->agregarUsuario($data)
		);
	}
    
	public function eliminarUsuario($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->UsuarioModel->eliminarUsuario($this->security->xss_clean($ID)));
	}
}
