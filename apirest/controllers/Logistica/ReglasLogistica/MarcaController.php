<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MarcaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/MarcaModel');
		$this->load->model('HelperModel');
	}
	
	public function listarMarcas(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/MarcaView');
			$this->load->view('footer', array("js_marca" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MarcaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Marca;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMarca(\'' . $row->ID_Marca . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMarca(\'' . $row->ID_Marca . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MarcaModel->count_all(),
	        'recordsFiltered' => $this->MarcaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->MarcaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMarca(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Marca'	=> $this->input->post('No_Marca'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Marca') != '') ?
			$this->MarcaModel->actualizarMarca(array('ID_Marca' => $this->input->post('EID_Marca')), $data, $this->input->post('ENo_Marca'))
		:
			$this->MarcaModel->agregarMarca($data)
		);
	}
    
	public function eliminarMarca($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MarcaModel->eliminarMarca($this->security->xss_clean($ID)));
	}
}
