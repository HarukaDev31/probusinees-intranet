<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PaisController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/PaisModel');
	}

	public function listarPaises(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/PaisView');
			$this->load->view('footer', array("js_pais" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->PaisModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Pais;
            $rows[] = $row->Nu_Codigo_Sunat_Pais;
            $rows[] = $row->Nu_Codigo_Sunat_ISO;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPais(\'' . $row->ID_Pais . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPais(\'' . $row->ID_Pais . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->PaisModel->count_all(),
	        'recordsFiltered' => $this->PaisModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->PaisModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPais(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'No_Pais'				=> $this->input->post('No_Pais'),
			'Nu_Codigo_Sunat_Pais'	=> $this->input->post('Nu_Codigo_Sunat_Pais'),
			'Nu_Codigo_Sunat_ISO'	=> $this->input->post('Nu_Codigo_Sunat_ISO'),
			'Nu_Estado'				=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Pais') != '') ?
			$this->PaisModel->actualizarPais(array('ID_Pais' => $this->input->post('EID_Pais')), $data, $this->input->post('ENo_Pais'))
		:
			$this->PaisModel->agregarPais($data)
		);
	}
    
	public function eliminarPais($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->PaisModel->eliminarPais($this->security->xss_clean($ID)));
	}
}
