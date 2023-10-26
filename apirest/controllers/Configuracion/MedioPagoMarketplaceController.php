<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MedioPagoMarketplaceController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/MedioPagoMarketplaceModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/MedioPagoMarketplaceView');
			$this->load->view('footer', array("js_medio_pago_marketplace" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MedioPagoMarketplaceModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Medio_Pago_Marketplace;
            $rows[] = $row->Txt_Medio_Pago_Marketplace;
			$rows[] = $row->Nu_Orden;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMedioPagoMarketplace(\'' . $row->ID_Medio_Pago_Marketplace . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMedioPagoMarketplace(\'' . $row->ID_Medio_Pago_Marketplace . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MedioPagoMarketplaceModel->count_all(),
	        'recordsFiltered' => $this->MedioPagoMarketplaceModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->MedioPagoMarketplaceModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMedioPagoMarketplace(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Medio_Pago_Marketplace'	=> $this->input->post('No_Medio_Pago_Marketplace'),
			'Txt_Medio_Pago_Marketplace' => $this->input->post('Txt_Medio_Pago_Marketplace'),
			'Nu_Orden' => $this->input->post('Nu_Orden'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Medio_Pago_Marketplace') != '') ?
			$this->MedioPagoMarketplaceModel->actualizarMedioPagoMarketplace(array('ID_Medio_Pago_Marketplace' => $this->input->post('EID_Medio_Pago_Marketplace')), $data, $this->input->post('ENo_Medio_Pago_Marketplace'))
		:
			$this->MedioPagoMarketplaceModel->agregarMedioPagoMarketplace($data)
		);
	}
    
	public function eliminarMedioPagoMarketplace($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MedioPagoMarketplaceModel->eliminarMedioPagoMarketplace($this->security->xss_clean($ID)));
	}
}
