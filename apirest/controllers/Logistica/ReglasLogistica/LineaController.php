<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LineaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/LineaModel');
		$this->load->model('HelperModel');
	}
	
	public function listarLineas(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/LineaView');
			$this->load->view('footer', array("js_linea" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->LineaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action='delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Familia;
            $rows[] = $row->No_Sub_Familia;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verLinea(\'' . $row->ID_Sub_Familia . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarLinea(\'' . $row->ID_Sub_Familia . '\', \''.$action.'\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->LineaModel->count_all(),
	        'recordsFiltered' => $this->LineaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->LineaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudLinea(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'		=> $this->input->post('ID_Empresa'),
			'ID_Familia'		=> $this->input->post('ID_Familia'),
			'No_Sub_Familia'	=> $this->input->post('No_Sub_Familia'),
			'Nu_Estado'			=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Familia') != '' && $this->input->post('EID_Sub_Familia') != '') ?
			$this->LineaModel->actualizarLinea(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Sub_Familia' => $this->input->post('EID_Sub_Familia')), $data, $this->input->post('EID_Familia'), $this->input->post('ENo_Sub_Familia'))
		:
			$this->LineaModel->agregarLinea($data)
		);
	}
    
	public function eliminarLinea($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->LineaModel->eliminarLinea($this->security->xss_clean($ID)));
	}
}
