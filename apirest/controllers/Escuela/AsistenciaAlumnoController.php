<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AsistenciaAlumnoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Escuela/AsistenciaAlumnoModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Escuela/AsistenciaAlumnoView');
			$this->load->view('footer', array("js_escuela_asistencia_alumno" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->AsistenciaAlumnoModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Empresa;
            $rows[] = $row->No_Sede_Musica;
			$rows[] = $row->No_Salon;
			$rows[] = $row->No_Profesor;
			$rows[] = ToDateBD($row->Fe_Asistencia);
			$rows[] = $row->No_Alumno;
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoAsistenciaArray($row->Nu_Asistio);
            $span_estado = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			//$span_estado .= ' &nbsp;&nbsp;<button class="btn btn-xs btn-link" alt="Cambiar estado asistencia" title="Cambiar estado asistencia" href="javascript:void(0)" onclick="estadoAsistencia(\'' . $row->ID_Control_Asistencia_Alumno . '\', \'' . $row->Nu_Asistio . '\')">Estado Asistencia</button>';
			$rows[] = $span_estado;

			$rows[] = '';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPos(\'' . $row->ID_Control_Asistencia_Alumno . '\', \'' . $action . '\')"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->AsistenciaAlumnoModel->count_all(),
	        'recordsFiltered' => $this->AsistenciaAlumnoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->AsistenciaAlumnoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		echo json_encode($this->AsistenciaAlumnoModel->agregarPos($_POST));
	}

	
	public function actualizarPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		echo json_encode($this->AsistenciaAlumnoModel->actualizarPos(array('ID_Control_Asistencia_Alumno' => $this->input->post('EID_Control_Asistencia_Alumno')), $data));
	}
    
	public function eliminarPos($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->AsistenciaAlumnoModel->eliminarPos($this->security->xss_clean($ID)));
	}

	public function estadoAsistencia($ID, $Nu_Asistencia){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->AsistenciaAlumnoModel->estadoAsistencia($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Asistencia)));
	}
}
