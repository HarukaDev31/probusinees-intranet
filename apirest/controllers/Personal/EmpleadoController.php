<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpleadoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Personal/EmpleadoModel');
		$this->load->model('HelperModel');
	}

	public function listarEmpleados(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Personal/EmpleadoView');
			$this->load->view('footer', array("js_empleado" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->EmpleadoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			$rows[] = $row->No_Entidad;
			$rows[] = $row->Nu_Pin_Caja;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verEmpleado(\'' . $row->ID_Entidad . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarEmpleado(\'' . $row->ID_Entidad . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->EmpleadoModel->count_all(),
	        'recordsFiltered' => $this->EmpleadoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }

	public function ajax_edit($ID){
        echo json_encode($this->EmpleadoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudEmpleado(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$Nu_Celular_Entidad = '';
		if ( isset($_POST['Nu_Celular_Entidad']) && strlen($_POST['Nu_Celular_Entidad']) == 11){
	        $Nu_Celular_Entidad = explode(' ', $this->input->post('Nu_Celular_Entidad'));
	        $Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
		}
		$data = array(
			'ID_Empresa'					=> $this->empresa->ID_Empresa,
			'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
			'Nu_Tipo_Entidad'				=> 4,//Personal
			//'ID_Tipo_Documento_Identidad'	=> (strlen($this->input->post('Nu_Documento_Identidad')) == 8 ? 2 : 1),//DNI / OTROS
			'ID_Tipo_Documento_Identidad' => $this->input->post('ID_Tipo_Documento_Identidad'),
			'Nu_Documento_Identidad'		=> $this->input->post('Nu_Documento_Identidad'),
			'No_Entidad'					=> $this->input->post('No_Entidad'),
			'Fe_Nacimiento'					=> (!empty($this->input->post('Fe_Nacimiento')) ? ToDate($this->input->post('Fe_Nacimiento')) : ''),
			'Nu_Tipo_Sexo'					=> $this->input->post('Nu_Tipo_Sexo'),
			'Nu_Celular_Entidad'			=> $Nu_Celular_Entidad,
			'Nu_Pin_Caja'					=> $this->input->post('Nu_Pin_Caja'),
			'Txt_Email_Entidad'			=> $this->input->post('Txt_Email_Entidad'),
			'Txt_Direccion_Entidad'			=> $this->input->post('Txt_Direccion_Entidad'),
			'Nu_Estado'						=> $this->input->post('Nu_Estado'),
		);
		if ( !empty($this->input->post('ID_Distrito')) ){
			$data = array_merge($data, array("ID_Distrito" => $this->input->post('ID_Distrito')));
		}
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Entidad') != '') ?
			$this->EmpleadoModel->actualizarEmpleado(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Entidad' => $this->input->post('EID_Entidad')), $data, $this->input->post('ENu_Documento_Identidad'), $this->input->post('ENu_Pin_Caja'))
		:
			$this->EmpleadoModel->agregarEmpleado($data)
		);
	}
    
	public function eliminarEmpleado($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->EmpleadoModel->eliminarEmpleado($this->security->xss_clean($ID)));
	}
}
