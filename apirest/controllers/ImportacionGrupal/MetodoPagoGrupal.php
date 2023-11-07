<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MetodoPagoGrupal extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImportacionGrupal/MetodoPagoGrupalModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			//VERIFICAR SI TIENE ACTIVADO MERCADO PAGO O NO
        	#Nu_Tipo_Forma_Pago_Lae_Shop = 2  => MERCADO PAGO
			$iEstadoPasarelaPagoMercadoPago = $this->MetodoPagoGrupalModel->buscarPasarelaPagoActivadas(2);			
			$this->load->view('header_v2');
			$this->load->view('ImportacionGrupal/MetodoPagoGrupalView', array(
				"iEstadoPasarelaPagoMercadoPago" => $iEstadoPasarelaPagoMercadoPago
			));
			$this->load->view('footer_v2', array("js_metodo_pago_grupal" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MetodoPagoGrupalModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            //$rows[] = $row->No_Medio_Pago;
            $rows[] = $row->No_Medio_Pago_Tienda_Virtual;
			
			$sTipoFormaPago = 'Ninguno';
			if ($row->Nu_Tipo_Forma_Pago_Lae_Shop == 1)
				$sTipoFormaPago = 'Pago por WhatsApp';
			else if ($row->Nu_Tipo_Forma_Pago_Lae_Shop == 2)
				$sTipoFormaPago = 'Pago Online - MERCADO PAGO';
			else if ($row->Nu_Tipo_Forma_Pago_Lae_Shop == 3)
				$sTipoFormaPago = 'Pago Contra entrega Efectivo';
			else if ($row->Nu_Tipo_Forma_Pago_Lae_Shop == 4)
				$sTipoFormaPago = 'Pago por Transferencia';

            $rows[] = $sTipoFormaPago;

			/*
			$sCierreVenta = 'Ninguno';
			if ($row->Nu_Cierre_Venta_Pago_Lae_Shop == 1)
				$sCierreVenta = 'Por WhatsApp';
			else if ($row->Nu_Cierre_Venta_Pago_Lae_Shop == 2)
				$sCierreVenta = 'Por Web';

            $rows[] = $sCierreVenta;
			*/

			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
		
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMedioPago(\'' . $row->ID_Medio_Pago . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMedioPago(\'' . $row->ID_Medio_Pago . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';

			$sConfiguracionPagoTransferencia = '';
			if ($row->Nu_Tipo_Forma_Pago_Lae_Shop == 4)
				$sConfiguracionPagoTransferencia = '<button class="btn btn-xs btn-link" alt="Configurar Cuentas Transferencia" title="Configurar Cuentas Transferencia" href="javascript:void(0)" onclick="agregarMedioPago_cuentas_bancarias(\'' . $row->ID_Medio_Pago . '\', \'' . $row->No_Medio_Pago_Tienda_Virtual . '\')"><i class="fas fa-university fa-2x" aria-hidden="true"></i></button>';
			$rows[] = $sConfiguracionPagoTransferencia;

            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->MetodoPagoGrupalModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMedioPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Medio_Pago_Tienda_Virtual'	=> $this->input->post('No_Medio_Pago_Tienda_Virtual'),
			'Nu_Activar_Medio_Pago_Lae_Shop' => $this->input->post('Nu_Estado'),
			'Nu_Cierre_Venta_Pago_Lae_Shop' => $this->input->post('Nu_Cierre_Venta_Pago_Lae_Shop'),
			'Nu_Tipo_Forma_Pago_Lae_Shop' => $this->input->post('Nu_Tipo_Forma_Pago_Lae_Shop'),
			'Txt_Pasarela_Pago_Key' => $this->input->post('Txt_Pasarela_Pago_Key'),
			'Txt_Pasarela_Pago_Token' => $this->input->post('Txt_Pasarela_Pago_Token'),
		);
		echo json_encode(
		($this->input->post('EID_Medio_Pago') != '') ?
			$this->MetodoPagoGrupalModel->actualizarMedioPago(array('ID_Medio_Pago' => $this->input->post('EID_Medio_Pago')), $data, $this->input->post('ENo_Medio_Pago_Tienda_Virtual'))
		:
			$this->MetodoPagoGrupalModel->agregarMedioPago($data)
		);
	}
    
	public function eliminarMedioPago($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MetodoPagoGrupalModel->eliminarMedioPago($this->security->xss_clean($ID)));
	}

	//NRO. CUENTAS BANCARIAS
	public function ajax_list_cuentas_bancarias(){
		$arrData = $this->MetodoPagoGrupalModel->get_datatables_cuentas_bancarias();
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
			$rows[] = $row->No_Medio_Pago_Tienda_Virtual;
            $rows[] = $row->No_Banco_Siglas;
			
			$sTipoCuenta = 'Ninguno';
			if ($row->Nu_Tipo_Cuenta==1)
				$sTipoCuenta = 'Cuenta Corriente';
			else if ($row->Nu_Tipo_Cuenta==2)
				$sTipoCuenta = 'Cuenta Ahorros';
			$rows[] = $sTipoCuenta;

			$rows[] = $row->No_Moneda;
			$rows[] = $row->No_Titular_Cuenta;
			$rows[] = $row->No_Cuenta_Bancaria;
			$rows[] = $row->No_Cuenta_Interbancario;
		
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMedioPago_cuentas_bancarias(\'' . $row->ID_Cuenta_Bancaria . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMedioPago_cuentas_bancarias(\'' . $row->ID_Cuenta_Bancaria . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';

            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit_cuentas_bancarias($ID){
        echo json_encode($this->MetodoPagoGrupalModel->get_by_id_cuentas_bancarias($this->security->xss_clean($ID)));
    }
    
	public function crudMedioPago_cuentas_bancarias(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Medio_Pago'	=> $this->input->post('EID_Medio_Pago'),
			'ID_Tipo_Medio_Pago' => $this->input->post('ID_Tipo_Medio_Pago'),
			'ID_Banco'	=> $this->input->post('ID_Banco'),
			'Nu_Tipo_Cuenta' => $this->input->post('Nu_Tipo_Cuenta'),
			'ID_Moneda' => $this->input->post('ID_Moneda'),
			'No_Titular_Cuenta' => $this->input->post('No_Titular_Cuenta'),
			'No_Cuenta_Bancaria' => $this->input->post('No_Cuenta_Bancaria'),
			'No_Cuenta_Interbancario' => $this->input->post('No_Cuenta_Interbancario'),
		);
		echo json_encode(
		($this->input->post('EID_Cuenta_Bancaria') != '') ?
			$this->MetodoPagoGrupalModel->actualizarMedioPago_cuentas_bancarias(array('ID_Cuenta_Bancaria' => $this->input->post('EID_Cuenta_Bancaria')), $data)
		:
			$this->MetodoPagoGrupalModel->agregarMedioPago_cuentas_bancarias($data)
		);
	}
    
	public function eliminarMedioPago_cuentas_bancarias($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MetodoPagoGrupalModel->eliminarMedioPago_cuentas_bancarias($this->security->xss_clean($ID)));
	}
	//FIN - NRO. CUENTAS BANCARIAS
    
	public function activarMercadoPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MetodoPagoGrupalModel->activarMercadoPago());
	}
}
