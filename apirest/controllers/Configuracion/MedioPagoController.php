<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MedioPagoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/MedioPagoModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/MedioPagoView');
			$this->load->view('footer', array("js_medio_pago" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MedioPagoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Medio_Pago;
            $rows[] = $row->Txt_Medio_Pago;
			$rows[] = $row->No_Codigo_Sunat_PLE;
            $rows[] = $row->No_Codigo_Sunat_FE;
			
			$sTipoFormaPago = '';
			if ($row->Nu_Tipo == 0)
				$sTipoFormaPago = 'Efectivo';
			else if ($row->Nu_Tipo == 1)
				$sTipoFormaPago = 'Crédito';
			else if ($row->Nu_Tipo == 2)
				$sTipoFormaPago = 'Tarjeta / Depósito Bancario';

            $rows[] = $sTipoFormaPago;

			$rows[] = ($row->Nu_Tipo_Caja == 0 ? 'Físico' : 'Virtual');
			$rows[] = $row->Nu_Orden;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMedioPago(\'' . $row->ID_Medio_Pago . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMedioPago(\'' . $row->ID_Medio_Pago . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MedioPagoModel->count_all(),
	        'recordsFiltered' => $this->MedioPagoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->MedioPagoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMedioPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Medio_Pago'	=> $this->input->post('No_Medio_Pago'),
			'Txt_Medio_Pago'	=> $this->input->post('Txt_Medio_Pago'),
			'No_Codigo_Sunat_PLE' => $this->input->post('No_Codigo_Sunat_PLE'),
			'No_Codigo_Sunat_FE' => $this->input->post('No_Codigo_Sunat_FE'),
			'Nu_Tipo' => $this->input->post('Nu_Tipo'),
			'Nu_Tipo_Caja' => $this->input->post('Nu_Tipo_Caja'),
			'Nu_Orden' => $this->input->post('Nu_Orden'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Medio_Pago') != '') ?
			$this->MedioPagoModel->actualizarMedioPago(array('ID_Medio_Pago' => $this->input->post('EID_Medio_Pago')), $data, $this->input->post('ENo_Medio_Pago'))
		:
			$this->MedioPagoModel->agregarMedioPago($data)
		);
	}
    
	public function eliminarMedioPago($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MedioPagoModel->eliminarMedioPago($this->security->xss_clean($ID)));
	}
}
