<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BilleteraDropshippingController extends CI_Controller {

	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Dropshipping/BilleteraModel');
		$this->load->model('HelperModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$this->load->view('header', array("arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,));
			$this->load->view('Dropshipping/BilleteraView');
			$this->load->view('footer', array("js_billetera_dropshipping" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->BilleteraModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
			
            $rows[] = $row->No_Banco_Siglas;
            $rows[] = ($row->Nu_Tipo_Cuenta==1 ? 'Cuenta de Ahorros' : 'Cuenta Corriente');
			$rows[] = $row->No_Moneda;
			$rows[] = $row->No_Cuenta_Bancaria;
			$rows[] = $row->No_Cuenta_Interbancario;
			$rows[] = $row->No_Titular_Cuenta;
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Editar" title="Editar" href="javascript:void(0)" onclick="verCuentaBancaria(\'' . $row->ID_Cuenta_Bancaria_Billetera . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BilleteraModel->count_all(),
	        'recordsFiltered' => $this->BilleteraModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->BilleteraModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCuentaBancaria(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Banco' => $this->input->post('ID_Banco'),
			'Nu_Tipo_Cuenta' => $this->input->post('Nu_Tipo_Cuenta'),
			'ID_Moneda' => $this->input->post('ID_Moneda'),
			'No_Cuenta_Bancaria' => $this->input->post('No_Cuenta_Bancaria'),
			'No_Cuenta_Interbancario' => $this->input->post('No_Cuenta_Interbancario'),
			'No_Titular_Cuenta' => $this->input->post('No_Titular_Cuenta'),
			'Nu_Estado' => 1
		);

		echo json_encode(
		($this->input->post('EID_Cuenta_Bancaria_Billetera') != '') ?
			$this->BilleteraModel->actualizar(array('ID_Cuenta_Bancaria_Billetera' => $this->input->post('EID_Cuenta_Bancaria_Billetera')), $data)
		:
			$this->BilleteraModel->agregar($data)
		);
	}

	//LISTAR DESEMBOLSOS PENDIENTES	
	public function ajax_list_desembolso_pendiente(){
		$arrData = $this->BilleteraModel->get_datatables_desembolso_pendiente();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
			
            $rows[] = $row->ID_Transaccion_Procesada;
			$rows[] = $row->No_Cuenta_Bancaria;
			$rows[] = $row->Ss_Importe;
			$rows[] = $row->Fe_Registro;

			$rows[] = '<button class="btn btn-xs btn-link" alt="Editar" title="Editar" href="javascript:void(0)" onclick="verCuentaBancaria(\'' . $row->ID_Cuenta_Bancaria_Billetera . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BilleteraModel->count_all_desembolso_pendiente(),
	        'recordsFiltered' => $this->BilleteraModel->count_filtered_desembolso_pendiente(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	//LISTAR DESEMBOLSOS PAGOS
	public function ajax_list_desembolso_pago(){
		$arrData = $this->BilleteraModel->get_datatables_desembolso_pago();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
			
            $rows[] = $row->ID_Transaccion_Procesada;
			$rows[] = $row->No_Cuenta_Bancaria;
			$rows[] = $row->Ss_Importe;
			$rows[] = $row->Fe_Registro;

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BilleteraModel->count_all_desembolso_pago(),
	        'recordsFiltered' => $this->BilleteraModel->count_filtered_desembolso_pago(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
}
