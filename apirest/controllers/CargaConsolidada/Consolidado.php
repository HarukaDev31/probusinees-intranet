<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consolidado extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('CargaConsolidada/ConsolidadoModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('CargaConsolidada/ConsolidadoView');
			$this->load->view('footer_v2', array("js_consolidado" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->ConsolidadoModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {//falta colocar cantidad de checlisk pendientes o finalizados
			$rows = array();
            $rows[] = $row->No_Carga_Consolidada;
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoConsolidadoArray($row->Nu_Estado);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            $rows[] = $row->Nu_Estado == 0 ? '<a href="' .  base_url() . 'CargaConsolidada/PedidosCargaConsolidada/listar/' . $row->ID_Carga_Consolidada . '">Generar</a>' : '<span class="badge bg-success">Finalizado</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCliente(\'' . $row->ID_Carga_Consolidada . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCliente(\'' . $row->ID_Carga_Consolidada . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->ConsolidadoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCliente(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'				=> $this->user->ID_Empresa,
			'ID_Organizacion'			=> $this->user->ID_Organizacion,//Organizacion
			'No_Carga_Consolidada'		=> $this->input->post('No_Carga_Consolidada'),
			'Txt_Nota'					=> $this->input->post('Txt_Nota'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		$this->input->post('EID_Carga_Consolidada') != '' ?
			$this->ConsolidadoModel->actualizarCliente(array('ID_Carga_Consolidada' => $this->input->post('EID_Carga_Consolidada')), $data, $this->input->post('ENo_Carga_Consolidada'))
		:
			$this->ConsolidadoModel->agregarCliente($data, $this->input->post('addProducto'))
		);
	}
    
	public function eliminarCliente($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ConsolidadoModel->eliminarCliente($this->security->xss_clean($ID)));
	}
}
