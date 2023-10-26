<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Matricular_empleado_controller extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Personal/Matricular_empleado_model');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Personal/matricular_empleado_view');
			$this->load->view('footer', array("js_matricular_empleado" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->Matricular_empleado_model->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = allTypeDate($row->Fe_Matricula, '-', 0);
            $rows[] = $row->No_Entidad;
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMatricula_Empleado(\'' . $row->ID_Matricula_Empleado . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMatricula_Empleado(\'' . $row->ID_Matricula_Empleado . '\', \'' . $action . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->Matricular_empleado_model->count_all(),
	        'recordsFiltered' => $this->Matricular_empleado_model->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->Matricular_empleado_model->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMatricula_Empleado(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Entidad'	=> $this->input->post('ID_Entidad'),
			'Fe_Matricula'	=> ToDate($this->input->post('Fe_Matricula')) . ' ' . $this->input->post('ID_Hora') . ':' . $this->input->post('ID_Minuto') . ':00',
		);
		
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Matricula_Empleado') != '') ?
			$this->Matricular_empleado_model->actualizarMatricula_Empleado(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Matricula_Empleado' => $this->input->post('EID_Matricula_Empleado')), $data)
		:
			$this->Matricular_empleado_model->agregarMatricula_Empleado($data)
		);
	}
    
	public function eliminarMatricula_Empleado($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->Matricular_empleado_model->eliminarMatricula_Empleado($this->security->xss_clean($ID)));
	}
}
