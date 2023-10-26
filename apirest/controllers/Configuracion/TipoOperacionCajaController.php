<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoOperacionCajaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/TipoOperacionCajaModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/TipoOperacionCajaView');
			$this->load->view('footer', array("js_tipo_operacion_caja" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->TipoOperacionCajaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
				$rows[] = $row->No_Organizacion;
			}
			$rows[] = $row->No_Almacen;
            $rows[] = '<span class="label label-' . $row->No_Class_Grupo_Operacion_Caja . '">' . $row->No_Descripcion_Grupo_Operacion_Caja . '</span>';
			$rows[] = $row->No_Tipo_Operacion_Caja;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verTipoOperacionCaja(\'' . $row->ID_Tipo_Operacion_Caja . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarTipoOperacionCaja(\'' . $row->ID_Tipo_Operacion_Caja . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->TipoOperacionCajaModel->count_all(),
	        'recordsFiltered' => $this->TipoOperacionCajaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->TipoOperacionCajaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudTipoOperacionCaja(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Organizacion' => $this->input->post('ID_Organizacion'),
			'ID_Almacen' => $this->input->post('ID_Almacen'),
			'No_Tipo_Operacion_Caja' => $this->input->post('No_Tipo_Operacion_Caja'),
			'Nu_Tipo' => $this->input->post('Nu_Tipo'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Organizacion') != '' && $this->input->post('EID_Almacen') != '' && $this->input->post('EID_Tipo_Operacion_Caja') != '') ?
			$this->TipoOperacionCajaModel->actualizarTipoOperacionCaja(array('ID_Tipo_Operacion_Caja' => $this->input->post('EID_Tipo_Operacion_Caja')), $data, $this->input->post('ENo_Tipo_Operacion_Caja'))
		:
			$this->TipoOperacionCajaModel->agregarTipoOperacionCaja($data)
		);
	}
    
	public function eliminarTipoOperacionCaja($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->TipoOperacionCajaModel->eliminarTipoOperacionCaja($this->security->xss_clean($ID)));
	}
}
