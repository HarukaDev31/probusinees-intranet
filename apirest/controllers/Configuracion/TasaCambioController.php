<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TasaCambioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/TasaCambioModel');
	}

	public function listarTasasCambio(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/TasaCambioView');
			$this->load->view('footer', array("js_tasa_cambio_v2" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->TasaCambioModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Moneda;
            $rows[] = $row->No_Signo;
            $rows[] = ToDateBD($row->Fe_Ingreso);
            $rows[] = $row->Ss_Compra_Oficial;
            $rows[] = $row->Ss_Venta_Oficial;
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verTasaCambio(\'' . $row->ID_Tasa_Cambio . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarTasaCambio(\'' . $row->ID_Tasa_Cambio . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->TasaCambioModel->count_all(),
	        'recordsFiltered' => $this->TasaCambioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->TasaCambioModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudTasaCambio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Moneda'	=> $this->input->post('ID_Moneda'),
			'Fe_Ingreso' => ToDate($this->input->post('Fe_Ingreso')),
			'Ss_Venta_Oficial' => $this->input->post('Ss_Venta_Oficial'),
			'Ss_Compra_Oficial'	=> $this->input->post('Ss_Compra_Oficial'),
		);
		echo json_encode(
		($this->input->post('EID_Tasa_Cambio') != '') ?
			$this->TasaCambioModel->actualizarTasaCambio(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Tasa_Cambio' => $this->input->post('EID_Tasa_Cambio')), $data, $this->input->post('EID_Moneda'), $this->input->post('EFe_Ingreso'))
		:
			$this->TasaCambioModel->agregarTasaCambio($data)
		);
	}
    
	public function eliminarTasaCambio($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->TasaCambioModel->eliminarTasaCambio($this->security->xss_clean($ID)));
	}
    
	public function save_exchange_rate(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->TasaCambioModel->save_exchange_rate($this->input->post()));
	}
}
