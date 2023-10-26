<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComposicionController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/ComposicionModel');
		$this->load->model('HelperModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/ComposicionView');
			$this->load->view('footer', array("js_composicion" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ComposicionModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Composicion;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verComposicion(\'' . $row->ID_Composicion . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarComposicion(\'' . $row->ID_Composicion . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ComposicionModel->count_all(),
	        'recordsFiltered' => $this->ComposicionModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->ComposicionModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudComposicion(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Composicion' => $this->input->post('No_Composicion'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Composicion') != '') ?
			$this->ComposicionModel->actualizarComposicion(array('ID_Composicion' => $this->input->post('EID_Composicion')), $data, $this->input->post('ENo_Composicion'))
		:
			$this->ComposicionModel->agregarComposicion($data)
		);
	}
    
	public function eliminarComposicion($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ComposicionModel->eliminarComposicion($this->security->xss_clean($ID)));
	}
}
