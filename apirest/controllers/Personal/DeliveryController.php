<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Personal/DeliveryModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Personal/DeliveryView');
			$this->load->view('footer', array("js_delivery" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->DeliveryModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Tipo_Documento_Identidad_Breve;
            $rows[] = $row->Nu_Documento_Identidad;
            $rows[] = $row->No_Entidad;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verDelivery(\'' . $row->ID_Entidad . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarDelivery(\'' . $row->ID_Entidad . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->DeliveryModel->count_all(),
	        'recordsFiltered' => $this->DeliveryModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }

	public function ajax_edit($ID){
        echo json_encode($this->DeliveryModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudDelivery(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$Nu_Celular_Entidad = '';
		if ( isset($_POST['Nu_Celular_Entidad']) && strlen($_POST['Nu_Celular_Entidad']) == 11){
	        $Nu_Celular_Entidad = explode(' ', $this->input->post('Nu_Celular_Entidad'));
	        $Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
		}

		$data = array(
			'ID_Empresa'					=> $this->empresa->ID_Empresa,
			'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
			'Nu_Tipo_Entidad'				=> 6,//Personal delivery
			//'ID_Tipo_Documento_Identidad'	=> $iIdTipoDocumentoIdentidad,
			'ID_Tipo_Documento_Identidad' => $this->input->post('ID_Tipo_Documento_Identidad'),
			'Nu_Documento_Identidad'		=> $this->input->post('Nu_Documento_Identidad'),
			'No_Entidad'					=> $this->input->post('No_Entidad'),
			'Nu_Celular_Entidad'			=> $Nu_Celular_Entidad,
			'Txt_Direccion_Entidad'			=> $this->input->post('Txt_Direccion_Entidad'),
			'Nu_Estado'						=> $this->input->post('Nu_Estado'),
		);
		if ( !empty($this->input->post('ID_Distrito')) ){
			$data = array_merge($data, array("ID_Distrito" => $this->input->post('ID_Distrito')));
		}
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Entidad') != '') ?
			$this->DeliveryModel->actualizarDelivery(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Entidad' => $this->input->post('EID_Entidad')), $data, $this->input->post('ENu_Documento_Identidad'))
		:
			$this->DeliveryModel->agregarDelivery($data)
		);
	}
    
	public function eliminarDelivery($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->DeliveryModel->eliminarDelivery($this->security->xss_clean($ID)));
	}
}
