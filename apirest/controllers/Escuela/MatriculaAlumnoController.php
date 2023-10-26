<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MatriculaAlumnoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Escuela/MatriculaModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Escuela/MatriculaView');
			$this->load->view('footer', array("js_escuela_matricula" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MatriculaModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Empresa;
            $rows[] = ToDateBD($row->Fe_Matricula);
			$rows[] = $row->No_Sede_Musica;
			$rows[] = $row->No_Salon;
			$rows[] = $row->No_Dia;
			$rows[] = $row->Nu_Hora_Desde.':'.$row->Nu_Minuto_Desde . ' - ' . $row->Nu_Hora_Hasta.':'.$row->Nu_Minuto_Hasta;
			$rows[] = $row->No_Profesor;
			$rows[] = $row->No_Alumno;
			$rows[] = $row->No_Familia;
			$rows[] = $row->No_Grupo_Clase;
			$rows[] = $row->No_Tipo_Clase;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPos(\'' . $row->ID_Matricula_Alumno . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPos(\'' . $row->ID_Matricula_Alumno . '\', \'' . $action . '\')"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MatriculaModel->count_all(),
	        'recordsFiltered' => $this->MatriculaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->MatriculaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Sede_Musica' => $this->input->post('ID_Sede_Musica'),
			'ID_Salon' => $this->input->post('ID_Salon'),
			'ID_Horario_Clase' => $this->input->post('ID_Horario_Clase'),
			'ID_Entidad_Profesor' => $this->input->post('ID_Entidad_Profesor'),
			'ID_Entidad_Alumno' => $this->input->post('ID_Entidad_Alumno'),
			'ID_Familia' => $this->input->post('ID_Familia'),
			'ID_Grupo_Clase' => $this->input->post('ID_Grupo_Clase'),
			'ID_Tipo_Clase' => $this->input->post('ID_Tipo_Clase'),
			'Fe_Matricula' => ToDate($this->input->post('Fe_Matricula')),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Matricula_Alumno') != '') ?
			$this->MatriculaModel->actualizarPos(array('ID_Matricula_Alumno' => $this->input->post('EID_Matricula_Alumno')), $data, array(
				'ID_Horario_Clase' => $this->input->post('EID_Horario_Clase'),
				'ID_Entidad_Alumno' => $this->input->post('EID_Entidad_Alumno'),
			))
		:
			$this->MatriculaModel->agregarPos($data)
		);
	}
    
	public function crudPosMultiple(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
		$data = array(
			'ID_Empresa' => $_POST['arrHeader']['ID_Empresa2'],
			'ID_Sede_Musica' => $_POST['arrHeader']['ID_Sede_Musica2'],
			'ID_Salon' => $_POST['arrHeader']['ID_Salon2'],
			'ID_Entidad_Profesor' => $_POST['arrHeader']['ID_Entidad_Profesor2'],
			'ID_Familia' => $_POST['arrHeader']['ID_Familia2'],
			'ID_Grupo_Clase' => $_POST['arrHeader']['ID_Grupo_Clase2'],
			'ID_Tipo_Clase' => $_POST['arrHeader']['ID_Tipo_Clase2'],
			'Fe_Matricula' => ToDate($_POST['arrHeader']['Fe_Matricula2']),
			'Nu_Estado' => $_POST['arrHeader']['Nu_Estado2'],
			'Txt_Glosa' => $_POST['arrHeader']['Txt_Glosa2'],
		);
		echo json_encode($this->MatriculaModel->agregarPosMultiple($data, $_POST['arrAlumnoHorario']));
	}
    
	public function eliminarPos($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MatriculaModel->eliminarPos($this->security->xss_clean($ID)));
	}
}
