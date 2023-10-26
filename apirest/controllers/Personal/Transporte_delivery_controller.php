<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transporte_delivery_controller extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Personal/Transporte_delivery_model');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Personal/transporte_delivery_view');
			$this->load->view('footer', array("js_transporte_delivery" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->Transporte_delivery_model->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Tipo_Documento_Identidad_Breve;
            $rows[] = $row->Nu_Documento_Identidad;
            $rows[] = $row->No_Transportista;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verTransporte_Delivery(\'' . $row->ID_Transporte_Delivery . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarTransporte_Delivery(\'' . $row->ID_Transporte_Delivery . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->Transporte_delivery_model->count_all(),
	        'recordsFiltered' => $this->Transporte_delivery_model->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        $data = $this->Transporte_delivery_model->get_by_id($this->security->xss_clean($ID));
        print(json_encode($data));
    }
    
	public function crudTransporte_Delivery(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$Nu_Celular = '';
		if ( isset($_POST['Nu_Celular']) && strlen($_POST['Nu_Celular']) == 11){
	        $Nu_Celular = explode(' ', $this->input->post('Nu_Celular'));
	        $Nu_Celular = $Nu_Celular[0].$Nu_Celular[1].$Nu_Celular[2];
		}
		$data = array(
			'ID_Empresa'					=> $this->user->ID_Empresa,
			'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
			'ID_Tipo_Documento_Identidad'	=> 2,//DNI
			'Nu_Documento_Identidad'		=> $this->input->post('Nu_Documento_Identidad'),
			'No_Transportista'				=> $this->input->post('No_Transportista'),
			'Nu_Celular'					=> $Nu_Celular,
			'Txt_Direccion'					=> $this->input->post('Txt_Direccion'),
			'Nu_Estado'						=> $this->input->post('Nu_Estado'),
		);
		print(json_encode(
			($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Transporte_Delivery') != '') ?
				$this->Transporte_delivery_model->actualizarTransporte_Delivery(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Transporte_Delivery' => $this->input->post('EID_Transporte_Delivery')), $data, $this->input->post('ENu_Documento_Identidad'))
			:
				$this->Transporte_delivery_model->agregarTransporte_Delivery($data)
			)
		);
	}
    
	public function eliminarTransporte_Delivery($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		print(json_encode($this->Transporte_delivery_model->eliminarTransporte_Delivery($this->security->xss_clean($ID))));
	}
}
