<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PermisoUsuarioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('PanelAcceso/PermisoUsuarioModel');
	}
	
	public function listarPermisosUsuarios(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('inicio');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2', array("js_permiso_usuario" => true));
			$this->load->view('PanelAcceso/PermisoUsuarioView');
			$this->load->view('footer_v2', array("js_permiso_usuario" => true));
		}
	}
	
	public function getMenuAccesoxGrupo($ID_Empresa, $ID_Organizacion, $ID_Grupo, $iTipoRubroSistema, $iProveedorDropshipping){
		$arrGet = array(
			'ID_Empresa' => $this->security->xss_clean($ID_Empresa),
			'ID_Organizacion' => $this->security->xss_clean($ID_Organizacion),
			'ID_Grupo' => $this->security->xss_clean($ID_Grupo),
			'iTipoRubroSistema' => $this->security->xss_clean($iTipoRubroSistema),
			'iProveedorDropshipping' => $this->security->xss_clean($iProveedorDropshipping)
		);
		echo json_encode($this->PermisoUsuarioModel->getMenuAccesoxGrupo($arrGet));
	}
	
	public function crudPermisoUsuario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		if ( empty($this->input->post('ID_Menu_CRUD')) ){
			echo json_encode($response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Seleccionar una opción del Menú'));
			exit();
		} else {
			$arrPost = array(
				'ID_Empresa' => $this->input->post('ID_Empresa'),
				'ID_Organizacion' => $this->input->post('ID_Organizacion'),
				'ID_Grupo' => $this->input->post('ID_Grupo'),
				'ID_Grupo_'	=> $this->input->post('ID_Grupo_'),
				'ID_Menu' => $this->input->post('ID_Menu'),
				'ID_Menu_CRUD' => $this->input->post('ID_Menu_CRUD'),
				'iProveedorDropshipping' => $this->input->post('iProveedorDropshipping')
			);
			echo json_encode($this->PermisoUsuarioModel->agregarPermisoUsuario($arrPost));
			exit();
		}
	}
	
	public function getMenuPadreID($ID){
		echo json_encode($this->PermisoUsuarioModel->getMenuPadreID($ID));
		exit();
	}
}
