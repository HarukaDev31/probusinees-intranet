<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BackupController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/BackupModel');
	}

	public function listarBackups(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$data = array(
				'title' => 'Copias de seguridad',
				'copias' => $this->BackupModel->listarBackups()
			);
			$this->load->view('header');
			$this->load->view('Configuracion/BackupView', $data);
			$this->load->view('footer', array("js_backup" => true));
		}
	}

	public function generarBackup(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		ini_set('max_execution_time', 600);//10 Minutos
		echo json_encode($this->BackupModel->generarBackup());
	}
}
