<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MonedaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/MonedaModel');
	}

	public function listarMonedas(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/MonedaView');
			$this->load->view('footer', array("js_moneda" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MonedaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Moneda;
            $rows[] = $row->No_Signo;
            $rows[] = $row->Nu_Sunat_Codigo;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMoneda(\'' . $row->ID_Moneda . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMoneda(\'' . $row->ID_Moneda . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MonedaModel->count_all(),
	        'recordsFiltered' => $this->MonedaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->MonedaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMoneda(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Moneda'	=> $this->input->post('No_Moneda'),
			'No_Signo' => $this->input->post('No_Signo'),
			'Nu_Sunat_Codigo' => $this->input->post('Nu_Sunat_Codigo'),
			'Nu_Valor_FE' => $this->input->post('Nu_Valor_FE'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Moneda') != '') ?
			$this->MonedaModel->actualizarMoneda(array('ID_Moneda' => $this->input->post('EID_Moneda')), $data, $this->input->post('ENo_Moneda'))
		:
			$this->MonedaModel->agregarMoneda($data)
		);
	}
    
	public function eliminarMoneda($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MonedaModel->eliminarMoneda($this->security->xss_clean($ID)));
	}
}
