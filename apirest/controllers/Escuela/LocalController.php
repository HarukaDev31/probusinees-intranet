<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LocalController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Escuela/LocalModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Escuela/LocalView');
			$this->load->view('footer', array("js_escuela_sede" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->LocalModel->get_datatables();
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
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPos(\'' . $row->ID_Sede_Musica . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPos(\'' . $row->ID_Sede_Musica . '\', \'' . $action . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->LocalModel->count_all(),
	        'recordsFiltered' => $this->LocalModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->LocalModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Sede_Musica' => $this->input->post('No_Sede_Musica'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Sede_Musica') != '') ?
			$this->LocalModel->actualizarPos(array('ID_Sede_Musica' => $this->input->post('EID_Sede_Musica')), $data, $this->input->post('ENo_Sede_Musica'))
		:
			$this->LocalModel->agregarPos($data)
		);
	}
    
	public function eliminarPos($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->LocalModel->eliminarPos($this->security->xss_clean($ID)));
	}
}
