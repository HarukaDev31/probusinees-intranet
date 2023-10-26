<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SalonController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Escuela/SalonModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Escuela/SalonView');
			$this->load->view('footer', array("js_escuela_salon" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->SalonModel->get_datatables();
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
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPos(\'' . $row->ID_Salon . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPos(\'' . $row->ID_Salon . '\', \'' . $action . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->SalonModel->count_all(),
	        'recordsFiltered' => $this->SalonModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->SalonModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Sede_Musica' => $this->input->post('ID_Sede_Musica'),
			'No_Salon' => $this->input->post('No_Salon'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Salon') != '') ?
			$this->SalonModel->actualizarPos(array('ID_Salon' => $this->input->post('EID_Salon')), $data, $this->input->post('ENo_Salon'))
		:
			$this->SalonModel->agregarPos($data)
		);
	}
    
	public function eliminarPos($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->SalonModel->eliminarPos($this->security->xss_clean($ID)));
	}
}
