<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProvinciaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/ProvinciaModel');
	}

	public function listarProvincias(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/ProvinciaView');
			$this->load->view('footer', array("js_provincia" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ProvinciaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Pais;
            $rows[] = $row->No_Departamento;
            $rows[] = $row->No_Provincia;
            $rows[] = $row->No_Provincia_Breve;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verProvincia(\'' . $row->ID_Provincia . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarProvincia(\'' . $row->ID_Provincia . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ProvinciaModel->count_all(),
	        'recordsFiltered' => $this->ProvinciaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->ProvinciaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudProvincia(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Departamento'		=> $this->input->post('ID_Departamento'),
			'No_Provincia'			=> $this->input->post('No_Provincia'),
			'No_Provincia_Breve'	=> $this->input->post('No_Provincia_Breve'),
			'Nu_Estado'				=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Departamento') != '') && ($this->input->post('EID_Provincia') != '') ?
			$this->ProvinciaModel->actualizarProvincia(array('ID_Departamento' => $this->input->post('EID_Departamento'), 'ID_Provincia' => $this->input->post('EID_Provincia')), $data, $this->input->post('EID_Departamento'), $this->input->post('ENo_Provincia'))
		:
			$this->ProvinciaModel->agregarProvincia($data)
		);
	}
    
	public function eliminarProvincia($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ProvinciaModel->eliminarProvincia($this->security->xss_clean($ID)));
	}
}
