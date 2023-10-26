<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoMedioPagoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/TipoMedioPagoModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/TipoMedioPagoView');
			$this->load->view('footer', array("js_tipo_medio_pago" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->TipoMedioPagoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Medio_Pago;
            $rows[] = $row->No_Tipo_Medio_Pago;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verTipoMedioPago(\'' . $row->ID_Tipo_Medio_Pago . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarTipoMedioPago(\'' . $row->ID_Tipo_Medio_Pago . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->TipoMedioPagoModel->count_all(),
	        'recordsFiltered' => $this->TipoMedioPagoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->TipoMedioPagoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudTipoMedioPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Medio_Pago' => $this->input->post('ID_Medio_Pago'),
			'No_Tipo_Medio_Pago' => $this->input->post('No_Tipo_Medio_Pago'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Tipo_Medio_Pago') != '') ?
			$this->TipoMedioPagoModel->actualizarTipoMedioPago(array('ID_Tipo_Medio_Pago' => $this->input->post('EID_Tipo_Medio_Pago')), $data, $this->input->post('ENo_Tipo_Medio_Pago'))
		:
			$this->TipoMedioPagoModel->agregarTipoMedioPago($data)
		);
	}
    
	public function eliminarTipoMedioPago($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->TipoMedioPagoModel->eliminarTipoMedioPago($this->security->xss_clean($ID)));
	}
}
