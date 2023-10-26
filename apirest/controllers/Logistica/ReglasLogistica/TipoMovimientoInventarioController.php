<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoMovimientoInventarioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/TipoMovimientoInventarioModel');
	}
	
	public function listarTiposMovimientoInvetario(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/TipoMovimientoInventarioView');
			$this->load->view('footer', array("js_tipo_movimiento" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->TipoMovimientoInventarioModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Tipo_Movimiento;
            $rows[] = $row->Nu_Sunat_Codigo;
            $rows[] = ($row->Nu_Tipo_Movimiento == 0 ? 'Entrada' : 'Salida');
            $rows[] = ($row->Nu_Costear == 0 ? 'No' : 'Si');
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verTipoMovimientoInventario(\'' . $row->ID_Tipo_Movimiento . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarTipoMovimientoInventario(\'' . $row->ID_Tipo_Movimiento . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->TipoMovimientoInventarioModel->count_all(),
	        'recordsFiltered' => $this->TipoMovimientoInventarioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->TipoMovimientoInventarioModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudTipoMovimientoInventario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'No_Tipo_Movimiento'	=> $this->input->post('No_Tipo_Movimiento'),
			'Nu_Sunat_Codigo'		=> $this->input->post('Nu_Sunat_Codigo'),
			'Nu_Tipo_Movimiento'	=> $this->input->post('Nu_Tipo_Movimiento'),
			'Nu_Costear'			=> $this->input->post('Nu_Costear'),
			'Nu_Estado'				=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Tipo_Movimiento') != '') ?
			$this->TipoMovimientoInventarioModel->actualizarTipoMovimientoInventario(array('ID_Tipo_Movimiento' => $this->input->post('EID_Tipo_Movimiento')), $data, $this->input->post('ENo_Tipo_Movimiento'))
		:
			$this->TipoMovimientoInventarioModel->agregarTipoMovimientoInventario($data)
		);
	}
    
	public function eliminarTipoMovimientoInventario($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->TipoMovimientoInventarioModel->eliminarTipoMovimientoInventario($this->security->xss_clean($ID)));
	}
}
