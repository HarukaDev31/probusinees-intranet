<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoDocumentoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/TipoDocumentoModel');
	}

	public function listarTiposDocumento(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/TipoDocumentoView');
			$this->load->view('footer', array("js_tipo_documento" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->TipoDocumentoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Tipo_Documento;
            //$rows[] = $row->No_Tipo_Documento_Breve;
            $rows[] = ($row->Nu_Es_Sunat == 1 ? 'Si' : 'No');
            $rows[] = $row->Nu_Sunat_Codigo;
            $rows[] = ($row->Nu_Impuesto == 1 ? 'Si' : 'No');
            $rows[] = ($row->Nu_Cotizacion_Venta == 1 ? 'Si' : 'No');
            $rows[] = ($row->Nu_Venta == 1 ? 'Si' : 'No');
            $rows[] = ($row->Nu_Orden_Compra == 1 ? 'Si' : 'No');
            $rows[] = ($row->Nu_Compra == 1 ? 'Si' : 'No');
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verTipo_Documento(\'' . $row->ID_Tipo_Documento . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarTipo_Documento(\'' . $row->ID_Tipo_Documento . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->TipoDocumentoModel->count_all(),
	        'recordsFiltered' => $this->TipoDocumentoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->TipoDocumentoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudTipo_Documento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'No_Tipo_Documento'			=> $this->input->post('No_Tipo_Documento'),
			'No_Tipo_Documento_Breve'	=> $this->input->post('No_Tipo_Documento_Breve'),
			'Nu_Es_Sunat'				=> $this->input->post('Nu_Es_Sunat'),
			'Nu_Sunat_Codigo'			=> $this->input->post('Nu_Sunat_Codigo'),
			'Nu_Impuesto'				=> $this->input->post('Nu_Impuesto'),
			'Nu_Venta'					=> $this->input->post('Nu_Venta'),
			'Nu_Compra'					=> $this->input->post('Nu_Compra'),
			'Nu_Cotizacion_Venta' => $this->input->post('Nu_Cotizacion_Venta'),
			'Nu_Orden_Compra' => $this->input->post('Nu_Orden_Compra'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Tipo_Documento') != '') ?
			$this->TipoDocumentoModel->actualizarTipo_Documento(array('ID_Tipo_Documento' => $this->input->post('EID_Tipo_Documento')), $data, $this->input->post('ENo_Tipo_Documento'))
		:
			$this->TipoDocumentoModel->agregarTipo_Documento($data)
		);
	}
    
	public function eliminarTipo_Documento($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->TipoDocumentoModel->eliminarTipo_Documento($this->security->xss_clean($ID)));
	}
}
