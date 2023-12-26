<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HistorialPagos extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/HistorialPagosModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('AgenteCompra/HistorialPagosView');
			$this->load->view('footer_v2', array("js_historial_pagos" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->HistorialPagosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();
            
			$rows[] = $row->No_Pais;
            $rows[] = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);

			$rows[] = (!empty($row->Txt_Url_Pago_Garantizado) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_30_Cliente . '" src="' . $row->Txt_Url_Pago_Garantizado . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');

            $rows[] = $row->No_Pais_2;
            $rows[] = (!empty($row->Fe_Pago_30_Cliente) ? ToDateBD($row->Fe_Pago_30_Cliente) : '');
            $rows[] = $row->Ss_Pago_30_Cliente;
            $rows[] = $row->Nu_Operacion_Pago_30_Cliente;
            $rows[] = (!empty($row->Txt_Url_Pago_30_Cliente) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_30_Cliente . '" src="' . $row->Txt_Url_Pago_30_Cliente . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');
			
            $rows[] = $row->No_Pais_3;
            $rows[] = (!empty($row->Fe_Pago_100_Cliente) ? ToDateBD($row->Fe_Pago_100_Cliente) : '');
            $rows[] = $row->Ss_Pago_100_Cliente;
            $rows[] = $row->Nu_Operacion_Pago_100_Cliente;
            $rows[] = (!empty($row->Txt_Url_Pago_30_Cliente) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_100_Cliente . '" src="' . $row->Txt_Url_Pago_100_Cliente . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');
			
            $rows[] = $row->No_Pais_4;
            $rows[] = (!empty($row->Fe_Pago_Servicio_Cliente) ? ToDateBD($row->Fe_Pago_Servicio_Cliente) : '');
            $rows[] = $row->Ss_Pago_Servicio_Cliente;
            $rows[] = $row->Nu_Operacion_Pago_Servicio_Cliente;
            $rows[] = (!empty($row->Txt_Url_Pago_30_Cliente) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_Servicio_Cliente . '" src="' . $row->Txt_Url_Pago_Servicio_Cliente . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerTipoServicioArray($row->Nu_Tipo_Servicio);
			$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            $rows[] = $dropdown_estado;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerTipoServicioArray($row->Nu_Tipo_Servicio);
			$dropdown_estado_china = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            $rows[] = $dropdown_estado;

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->HistorialPagosModel->count_all(),
	        'recordsFiltered' => $this->HistorialPagosModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->HistorialPagosModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCliente(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'				=> $this->user->ID_Empresa,
			'ID_Organizacion'			=> $this->user->ID_Organizacion,//Organizacion
			'No_Importacion_Grupal'		=> $this->input->post('No_Importacion_Grupal'),
			'Fe_Inicio'					=> ToDate($this->input->post('Fe_Inicio')),
			'Fe_Fin'					=> ToDate($this->input->post('Fe_Fin')),
			'ID_Moneda'					=> $this->input->post('ID_Moneda'),
			'Txt_Importacion_Grupal'	=> $this->input->post('Txt_Importacion_Grupal'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
			'No_Usuario' 				=> $this->user->No_Usuario
		);
		echo json_encode(
		$this->input->post('EID_Importacion_Grupal') != '' ?
			$this->HistorialPagosModel->actualizarCliente(array('ID_Importacion_Grupal' => $this->input->post('EID_Importacion_Grupal')), $data, $this->input->post('addProducto'))
		:
			$this->HistorialPagosModel->agregarCliente($data, $this->input->post('addProducto'))
		);
	}
    
	public function eliminarCliente($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->HistorialPagosModel->eliminarCliente($this->security->xss_clean($ID)));
	}
}
