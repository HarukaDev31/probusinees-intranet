<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosPagados extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/PedidosPagadosModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('AgenteCompra/PedidosPagadosView');
			$this->load->view('footer_v2', array("js_pedidos_pagados" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosPagadosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

            $rows[] = $row->No_Pais;
            $rows[] = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . '-' . $row->Nu_Correlativo;
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);
            $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto;
            $rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);

			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',1,0);">Pendiente</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Garantizado</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
            $rows[] = $dropdown_estado;

			$rows[] = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->PedidosPagadosModel->get_by_id($this->security->xss_clean($ID)));
    }

	public function cambiarEstado($ID, $Nu_Estado, $id_correlativo){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosPagadosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($id_correlativo)));
	}

	public function crudPedidoGrupal(){
		//array_debug($this->input->post());		
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('EID_Empresa'),
			'ID_Organizacion' => $this->input->post('EID_Organizacion'),
			'Nu_Documento_Identidad' => $this->input->post('Nu_Documento_Identidad'),
			'No_Entidad' => $this->input->post('No_Entidad'),
			'Nu_Celular_Entidad' => $this->input->post('Nu_Celular_Entidad'),
			'Txt_Email_Entidad' => $this->input->post('Txt_Email_Entidad'),
		);
		echo json_encode($this->PedidosPagadosModel->actualizarPedido(
				array(
					'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
					'ID_Entidad' => $this->input->post('EID_Entidad'),
				),
				$data,
				$this->input->post('addProducto')
			)
		);
	}
}
