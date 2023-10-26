<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RedesSocialesTiendaVirtualController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('TiendaVirtual/Configuracion/RedesSocialesModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('TiendaVirtual/Configuracion/RedesSocialesView');
			$this->load->view('footer', array("js_redes_sociales_tienda_virtual" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->RedesSocialesModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMedioPago(\'' . $row->ID_Configuracion . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
            $rows[] = $row->No_Red_Social_Facebook;
            $rows[] = $row->No_Red_Social_Instagram;
            $rows[] = $row->No_Red_Social_Tiktok;
            $rows[] = $row->No_Red_Social_Youtube;
            $rows[] = $row->No_Red_Social_Linkedin;
            $rows[] = $row->No_Red_Social_Twitter;
            $rows[] = $row->No_Red_Social_Pinterest;	
			
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->RedesSocialesModel->count_all(),
	        'recordsFiltered' => $this->RedesSocialesModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->RedesSocialesModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMedioPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Red_Social_Facebook' => $this->input->post('No_Red_Social_Facebook'),
			'No_Red_Social_Instagram' => $this->input->post('No_Red_Social_Instagram'),
			'No_Red_Social_Tiktok' => $this->input->post('No_Red_Social_Tiktok'),
			'No_Red_Social_Youtube' => $this->input->post('No_Red_Social_Youtube'),
			'No_Red_Social_Linkedin' => $this->input->post('No_Red_Social_Linkedin'),
			'No_Red_Social_Twitter' => $this->input->post('No_Red_Social_Twitter'),
			'No_Red_Social_Pinterest' => $this->input->post('No_Red_Social_Pinterest'),
		);
		echo json_encode($this->RedesSocialesModel->actualizarMedioPago(array('ID_Configuracion' => $this->input->post('EID_Configuracion')), $data));
	}
}
