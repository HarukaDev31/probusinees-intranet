<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LaboratorioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/LaboratorioModel');
		$this->load->model('HelperModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/LaboratorioView');
			$this->load->view('footer', array("js_laboratorio" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->LaboratorioModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Laboratorio;
            
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verLaboratorio(\'' . $row->ID_Laboratorio . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarLaboratorio(\'' . $row->ID_Laboratorio . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->LaboratorioModel->count_all(),
	        'recordsFiltered' => $this->LaboratorioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->LaboratorioModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudLaboratorio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Laboratorio'	=> $this->input->post('No_Laboratorio'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Laboratorio') != '') ?
			$this->LaboratorioModel->actualizarLaboratorio(array('ID_Laboratorio' => $this->input->post('EID_Laboratorio')), $data, $this->input->post('ENo_Laboratorio'))
		:
			$this->LaboratorioModel->agregarLaboratorio($data)
		);
	}
    
	public function eliminarLaboratorio($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->LaboratorioModel->eliminarLaboratorio($this->security->xss_clean($ID)));
	}
}
