<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosGrupal extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImportacionGrupal/PedidosGrupalModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('ImportacionGrupal/PedidosGrupalView');
			$this->load->view('footer_v2', array("js_pedidos_grupal" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosGrupalModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();
            $rows[] = $row->ID_Pedido_Cabecera;
            $rows[] = ToDateBD($row->Fe_Emision);
            $rows[] = $row->No_Entidad;
            $rows[] = $row->No_Moneda;
            $rows[] = round($row->Ss_Total, 2);
            $rows[] = round($row->Qt_Total, 2);
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoArray($row->Nu_Estado);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)" onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->PedidosGrupalModel->get_by_id($this->security->xss_clean($ID)));
    }
}
