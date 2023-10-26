<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UnidadMedidaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/UnidadMedidaModel');
		$this->load->model('HelperModel');
	}
	
	public function listarUnidadesMedida(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/UnidadMedidaView');
			$this->load->view('footer', array("js_unidad_medida" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->UnidadMedidaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Unidad_Medida;
            $rows[] = $row->Nu_Sunat_Codigo;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verUnidadMedida(\'' . $row->ID_Unidad_Medida . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarUnidadMedida(\'' . $row->ID_Unidad_Medida . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->UnidadMedidaModel->count_all(),
	        'recordsFiltered' => $this->UnidadMedidaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->UnidadMedidaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudUnidadMedida(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'				=> $this->user->ID_Empresa,
			'No_Unidad_Medida'			=> $this->input->post('No_Unidad_Medida'),
			'No_Unidad_Medida_Breve'	=> $this->input->post('No_Unidad_Medida_Breve'),
			'Nu_Sunat_Codigo'			=> $this->input->post('Nu_Sunat_Codigo'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Unidad_Medida') != '' && $this->input->post('ENu_Sunat_Codigo') != '') ?
			$this->UnidadMedidaModel->actualizarUnidadMedida(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Unidad_Medida' => $this->input->post('EID_Unidad_Medida')), $data, $this->input->post('ENo_Unidad_Medida'), $this->input->post('ENu_Sunat_Codigo'))
		:
			$this->UnidadMedidaModel->agregarUnidadMedida($data)
		);
	}
    
	public function eliminarUnidadMedida($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->UnidadMedidaModel->eliminarUnidadMedida($this->security->xss_clean($ID)));
	}
}
