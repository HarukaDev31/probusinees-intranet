<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UbicacionInventarioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/UbicacionInventarioModel');
	}
	
	public function listarUbicacionesInventario(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/UbicacionInventarioView');
			$this->load->view('footer', array("js_ubicacion_inventario" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->UbicacionInventarioModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = $row->No_Ubicacion_Inventario;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verUbicacionInventario(\'' . $row->ID_Ubicacion_Inventario . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarUbicacionInventario(\'' . $row->ID_Ubicacion_Inventario . '\', \'' . $row->ID_Almacen . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->UbicacionInventarioModel->count_all(),
	        'recordsFiltered' => $this->UbicacionInventarioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->UbicacionInventarioModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudUbicacionInventario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Almacen'				=> $this->input->post('ID_Almacen'),
			'No_Ubicacion_Inventario'	=> $this->input->post('No_Ubicacion_Inventario'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
		);
		print(json_encode(
			($this->input->post('EID_Almacen') != '' && $this->input->post('EID_Ubicacion_Inventario') != '') ?
				$this->UbicacionInventarioModel->actualizarUbicacionInventario(array('ID_Almacen' => $this->input->post('EID_Almacen'), 'ID_Ubicacion_Inventario' => $this->input->post('EID_Ubicacion_Inventario')), $data, $this->input->post('ENo_Ubicacion_Inventario'))
			:
				$this->UbicacionInventarioModel->agregarUbicacionInventario($data)
			)
		);
	}
    
	public function eliminarUbicacionInventario($ID, $ID_Almacen){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->UbicacionInventarioModel->eliminarUbicacionInventario($this->security->xss_clean($ID), $this->security->xss_clean($ID_Almacen)));
	}
}
