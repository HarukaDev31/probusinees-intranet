<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImportacionGrupal extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Importacion/ImportacionGrupalModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('Importacion/ImportacionGrupalView');
			$this->load->view('footer_v2', array("js_importacion_grupal" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->ImportacionGrupalModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();
            $rows[] = $row->No_Moneda;
            $rows[] = $row->No_Importacion_Grupal;
            $rows[] = ToDateBD($row->Fe_Inicio);
            $rows[] = ToDateBD($row->Fe_Fin);
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCliente(\'' . $row->ID_Importacion_Grupal . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCliente(\'' . $row->ID_Importacion_Grupal . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->ImportacionGrupalModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCliente(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'				=> $this->user->ID_Empresa,
			'ID_Organizacion'			=> $this->user->ID_Organizacion,//Organizacion
			'No_Importacion_Grupal'		=> $this->input->post('No_Importacion_Grupal'),
			'Fe_Inicio'					=> ToDate($this->input->post('Fe_Inicio')),
			'Fe_Fin'					=> ToDate($this->input->post('Fe_Fin')),
			'ID_Moneda'					=> $this->input->post('ID_Moneda'),
			'Txt_Importacion_Grupal'	=> $this->input->post('Txt_Importacion_Grupal'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
			'No_Usuario' 				=> $this->user->No_Usuario
		);
		echo json_encode(
		$this->input->post('EID_Importacion_Grupal') != '' ?
			$this->ImportacionGrupalModel->actualizarCliente(array('ID_Importacion_Grupal' => $this->input->post('EID_Importacion_Grupal')), $data, $this->input->post('addProducto'))
		:
			$this->ImportacionGrupalModel->agregarCliente($data, $this->input->post('addProducto'))
		);
	}
    
	public function eliminarCliente($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ImportacionGrupalModel->eliminarCliente($this->security->xss_clean($ID)));
	}
}
