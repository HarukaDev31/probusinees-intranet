<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HorarioClaseController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Escuela/HorarioClaseModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Escuela/HorarioClaseView');
			$this->load->view('footer', array("js_escuela_horario_clase" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->HorarioClaseModel->get_datatables();
        $data = array();
		$draw = $this->input->get("draw");
		$no = $this->input->get("start");
		$length = $this->input->get("length");
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Empresa;
            $rows[] = $row->No_Sede_Musica;
            $rows[] = $row->No_Dia;
            $rows[] = $row->Nu_Hora_Desde;
            $rows[] = $row->Nu_Minuto_Desde;
            $rows[] = $row->Nu_Hora_Hasta;
            $rows[] = $row->Nu_Minuto_Hasta;
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPos(\'' . $row->ID_Horario_Clase . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPos(\'' . $row->ID_Horario_Clase . '\', \'' . $action . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->HorarioClaseModel->count_all(),
	        'recordsFiltered' => $this->HorarioClaseModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->HorarioClaseModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Sede_Musica' => $this->input->post('ID_Sede_Musica'),
			'ID_Dia_Semana' => $this->input->post('ID_Dia_Semana'),
			'Nu_Hora_Desde' => $this->input->post('Nu_Hora_Desde'),
			'Nu_Minuto_Desde' => $this->input->post('Nu_Minuto_Desde'),
			'Nu_Hora_Hasta' => $this->input->post('Nu_Hora_Hasta'),
			'Nu_Minuto_Hasta' => $this->input->post('Nu_Minuto_Hasta'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Horario_Clase') != '') ?
			$this->HorarioClaseModel->actualizarPos(array('ID_Horario_Clase' => $this->input->post('EID_Horario_Clase')), $data, array(
				'ID_Sede_Musica' => $this->input->post('EID_Sede_Musica'),
				'ID_Dia_Semana' => $this->input->post('EID_Dia_Semana')
			))
		:
			$this->HorarioClaseModel->agregarPos($data)
		);
	}
    
	public function eliminarPos($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->HorarioClaseModel->eliminarPos($this->security->xss_clean($ID)));
	}
}
