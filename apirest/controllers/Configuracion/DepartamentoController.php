<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DepartamentoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/DepartamentoModel');
	}

	public function listarDepartamentos(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/DepartamentoView');
			$this->load->view('footer', array("js_departamento" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->DepartamentoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Pais;
            $rows[] = $row->No_Departamento;
            $rows[] = $row->No_Departamento_Breve;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verDepartamento(\'' . $row->ID_Departamento . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarDepartamento(\'' . $row->ID_Departamento . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->DepartamentoModel->count_all(),
	        'recordsFiltered' => $this->DepartamentoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->DepartamentoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudDepartamento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Pais'				=> $this->input->post('ID_Pais'),
			'No_Departamento'		=> $this->input->post('No_Departamento'),
			'No_Departamento_Breve'	=> $this->input->post('No_Departamento_Breve'),
			'Nu_Estado'				=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Pais') != '') && ($this->input->post('EID_Departamento') != '') ?
			$this->DepartamentoModel->actualizarDepartamento(array('ID_Pais' => $this->input->post('EID_Pais'), 'ID_Departamento' => $this->input->post('EID_Departamento')), $data, $this->input->post('EID_Pais'), $this->input->post('ENo_Departamento'))
		:
			$this->DepartamentoModel->agregarDepartamento($data)
		);
	}
    
	public function eliminarDepartamento($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->DepartamentoModel->eliminarDepartamento($this->security->xss_clean($ID)));
	}
}
