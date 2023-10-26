<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BilleteraVirtualController extends CI_Controller {

	private $upload_path = '../assets/images/marcas/';
	private $upload_path_table = '../assets/images/marcas';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Billetera/BilleteraVirtualModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);

			//cargar saldo de cuenta por usuario de pedidos solo entregados
			//$arrSaldoPedidoEntregados = $this->BilleteraVirtualModel->obtenerSaldoPedidoEntregados();
			//cargar saldo de cuenta por usuario de pedidos solo falsa parada
			//$arrSaldoPedidoFalsaParada = $this->BilleteraVirtualModel->obtenerSaldoPedidoFalsaParada();
			
			$this->load->view('Billetera/BilleteraVirtualView');
			$this->load->view('footer', array("js_billetera" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->BilleteraVirtualModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->ID_Usuario == 1 ){
				$rows[] = $row->No_Empresa;
			}
			
			$sTipoCuentaBancaria='';
			if($row->ID_Banco != 8) {
				$sTipoCuentaBancaria = ($row->Nu_Tipo_Cuenta==1 ? 'Cuenta de Ahorros' : 'Cuenta Corriente');
			}
            $rows[] = $row->No_Banco_Siglas;
            $rows[] = $sTipoCuentaBancaria;
			$rows[] = ($row->ID_Banco != 8 ? $row->No_Moneda : '');
			$rows[] = $row->No_Cuenta_Bancaria;
			$rows[] = $row->No_Cuenta_Interbancario;
			$rows[] = $row->No_Titular_Cuenta;
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCuentaBancaria(\'' . $row->ID_Cuenta_Bancaria_Billetera . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BilleteraVirtualModel->count_all(),
	        'recordsFiltered' => $this->BilleteraVirtualModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->BilleteraVirtualModel->get_by_id($this->security->xss_clean($ID)));
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
			$this->BilleteraVirtualModel->actualizar(array('ID_Cuenta_Bancaria_Billetera' => $this->input->post('EID_Cuenta_Bancaria_Billetera')), $data)
		:
			$this->BilleteraVirtualModel->agregar($data)
		);
	}

	//LISTAR DESEMBOLSOS PENDIENTES	
	public function ajax_list_desembolso_pendiente(){
		$arrData = $this->BilleteraVirtualModel->get_datatables_desembolso_pendiente();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->ID_Usuario == 1 ){
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
	        'recordsTotal' => $this->BilleteraVirtualModel->count_all_desembolso_pendiente(),
	        'recordsFiltered' => $this->BilleteraVirtualModel->count_filtered_desembolso_pendiente(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	//LISTAR DESEMBOLSOS PAGOS
	public function ajax_list_desembolso_pago(){
		$arrData = $this->BilleteraVirtualModel->get_datatables_desembolso_pago();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->ID_Usuario == 1 ){
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
	        'recordsTotal' => $this->BilleteraVirtualModel->count_all_desembolso_pago(),
	        'recordsFiltered' => $this->BilleteraVirtualModel->count_filtered_desembolso_pago(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
	public function eliminarCuentaBancaria($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->BilleteraVirtualModel->eliminarCuentaBancaria($this->security->xss_clean($ID)));
	}
}
