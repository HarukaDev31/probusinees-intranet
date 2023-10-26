<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class OrganizacionController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/OrganizacionModel');
	}

	public function listarOrganizaciones(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/OrganizacionView');
			$this->load->view('footer', array("js_organizacion" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->OrganizacionModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Empresa;
            $rows[] = $row->No_Organizacion;
            //$rows[] = '<span class="label label-' . $row->No_Class_Estado_Sistema . '">' . $row->No_Descripcion_Estado_Sistema . '</span>';
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Limpiar - solo se eliminarán los documentos de ventas y compras, para las series se reiniciará el correlativo a 1" title="Limpiar - solo se eliminarán los documentos de ventas y compras, para las series se reiniciará el correlativo a 1" href="javascript:void(0)" onclick="limpiarData(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Organizacion . '\', \'' . $row->Nu_Estado_Sistema . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
			//$rows[] = ($row->Nu_Estado_Sistema == 1 ? '' : '<button class="btn btn-xs btn-link" alt="Limpiar - solo se eliminarán los documentos de ventas y compras, para las series se reiniciará el correlativo a 1" title="Limpiar - solo se eliminarán los documentos de ventas y compras, para las series se reiniciará el correlativo a 1" href="javascript:void(0)" onclick="limpiarData(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Organizacion . '\', \'' . $row->Nu_Estado_Sistema . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>');
			//$rows[] = ($row->Nu_Estado_Sistema == 1 ? '' : '<button type="button" class="btn btn-xs btn-link" alt="Pasar a producción" title="Pasar a producción" href="javascript:void(0)" onclick="activarSistema(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Organizacion . '\', \'' . $row->Nu_Estado_Sistema . '\')"><i class="fa fa-check" aria-hidden="true"> Pasar a producción</i></button>');
			//$rows[] = ($row->Nu_Estado_Sistema == 1 ? '' : '<button type="button" class="btn btn-xs btn-link" alt="Pasar a producción sin borrar COMPRAS y VENTAS" title="Pasar a producción sin borrar COMPRAS y VENTAS" href="javascript:void(0)" onclick="activarSistemaSinBorrar(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Organizacion . '\', \'' . $row->Nu_Estado_Sistema . '\')"><i class="fa fa-check" aria-hidden="true"> Pasar a producción sin borrar</i></button>');
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verOrganizacion(\'' . $row->ID_Organizacion . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$btn_delete = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarOrganizacion(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Organizacion . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
			if ( $row->Nu_Estado_Sistema == 1 )
				$btn_delete = '';
			$rows[] = $btn_delete;
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->OrganizacionModel->count_all(),
	        'recordsFiltered' => $this->OrganizacionModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->OrganizacionModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudOrganizacion(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Organizacion' => $this->input->post('No_Organizacion'),
			'Txt_Organizacion' => $this->input->post('Txt_Organizacion'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
			'Txt_FE_Ruta' => $this->input->post('Txt_FE_Ruta'),
			'Txt_FE_Token' => $this->input->post('Txt_FE_Token'),
			'Txt_Autorizacion_Venta_Localhost_Hostname' => $this->input->post('Txt_Autorizacion_Venta_Localhost_Hostname'),
			'Txt_Autorizacion_Venta_Localhost_User' => $this->input->post('Txt_Autorizacion_Venta_Localhost_User'),
			'Txt_Autorizacion_Venta_Localhost_Password' => $this->input->post('Txt_Autorizacion_Venta_Localhost_Password'),
			'Txt_Autorizacion_Venta_Localhost_Database' => $this->input->post('Txt_Autorizacion_Venta_Localhost_Database'),
		);
		echo json_encode(
		($this->input->post('EID_Organizacion') != '') ?
			$this->OrganizacionModel->actualizarOrganizacion(array('ID_Empresa' => $this->input->post('ID_Empresa'), 'ID_Organizacion' => $this->input->post('EID_Organizacion')), $data, $this->input->post('ENo_Organizacion'))
		:
			$this->OrganizacionModel->agregarOrganizacion($data)
		);
	}
    
	public function eliminarOrganizacion($iIdEmpresa, $ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrganizacionModel->eliminarOrganizacion($this->security->xss_clean($iIdEmpresa), $this->security->xss_clean($ID)));
	}
    
	public function limpiarData(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrganizacionModel->limpiarData($this->input->post()));
	}
    
	public function activarSistema(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrganizacionModel->activarSistema($this->input->post()));
	}
    
	public function activarSistemaSinBorrar(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrganizacionModel->activarSistemaSinBorrar($this->input->post()));
	}
}
