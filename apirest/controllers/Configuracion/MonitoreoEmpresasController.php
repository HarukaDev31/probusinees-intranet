<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MonitoreoEmpresasController extends CI_Controller {
	private $upload_path = '../librerias/apirest/libraries/sunat_facturador/certificado_digital/';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->library('encryption');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/MonitoreoEmpresasModel');
		$this->load->library('ConfiguracionTienda',NULL,"ConfiguracionTienda");
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/MonitoreoEmpresasView');
			$this->load->view('footer', array("js_monitoreo_empresas" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MonitoreoEmpresasModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
		$btn_configuracion_automatica = '';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);
            $rows[] = $row->No_Pais;
            $rows[] = $row->No_Empresa;
			
			$sStatusEmpresa='<span class="label label-success">Activo</span>';
			if ($row->Nu_Estado==0)
				$sStatusEmpresa='<span class="label label-danger">Inactivo</span>';
				
			$rows[] = $sStatusEmpresa;
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verEmpresa(\'' . $row->ID_Empresa . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarEmpresa(\'' . $row->ID_Empresa . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MonitoreoEmpresasModel->count_all(),
	        'recordsFiltered' => $this->MonitoreoEmpresasModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
		$arrData = $this->MonitoreoEmpresasModel->get_by_id($this->security->xss_clean($ID));
		if (file_exists($this->upload_path . 'FIRMA/' . $arrData->Nu_Documento_Identidad . '.pfx')) {
			$arrData = array_merge((array)$arrData, array('sNombreArchivoCertificadoDigital' => $arrData->Nu_Documento_Identidad . '.pfx'));
		}
        echo json_encode($arrData);
    }
    
	public function crudEmpresa(){		
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'Nu_Tipo_Proveedor_FE' => $this->input->post('Nu_Tipo_Proveedor_FE'),
			'Nu_Activar_Guia_Electronica' => $this->input->post('Nu_Activar_Guia_Electronica'),
			'ID_Tipo_Documento_Identidad' => $this->input->post('ID_Tipo_Documento_Identidad'),
			'Nu_Documento_Identidad' => $this->input->post('Nu_Documento_Identidad'),
			'No_Empresa' => $this->input->post('No_Empresa'),
			'No_Empresa_Comercial' => $this->input->post('No_Empresa_Comercial'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
			'Nu_Agregar_Almacen_Virtual'	=> $this->input->post('Nu_Agregar_Almacen_Virtual'),
			'Txt_Direccion_Empresa'	=> $this->input->post('Txt_Direccion_Empresa'),
			'Nu_MultiAlmacen'	=> $this->input->post('Nu_MultiAlmacen'),
			'ID_Ubigeo_Inei' => $this->input->post('ID_Ubigeo_Inei'),
			'ID_Pais' => $this->input->post('ID_Pais'),
			'ID_Departamento' => $this->input->post('ID_Departamento'),
			'ID_Provincia' => $this->input->post('ID_Provincia'),
			'ID_Distrito' => $this->input->post('ID_Distrito'),
			'Txt_Usuario_Sunat_Sol'	=> strtoupper($this->input->post('Txt_Usuario_Sunat_Sol')),
			'Txt_Password_Sunat_Sol' => $this->input->post('Txt_Password_Sunat_Sol'),
			'Txt_Password_Firma_Digital' => $this->input->post('Txt_Password_Firma_Digital'),
			'Nu_Tipo_Ecommerce_Empresa' => $this->input->post('Nu_Tipo_Ecommerce_Empresa'),
			'ID_Empresa_Marketplace' => $this->input->post('ID_Empresa_Marketplace'),
			'Txt_Sunat_Token_Guia_Client_ID' => $this->input->post('Txt_Sunat_Token_Guia_Client_ID'),
			'Txt_Sunat_Token_Guia_Client_Secret' => $this->input->post('Txt_Sunat_Token_Guia_Client_Secret')
		);
		
		if ( $this->input->post('Nu_Tipo_Proveedor_FE') == 2 ) {// 2 = Facturador de Sunat
			if ( isset($_FILES['certificado_digital']['tmp_name']) && !empty($_FILES['certificado_digital']['tmp_name']) ) {
				$destino = $_FILES['certificado_digital']['name'];
				if (copy($_FILES['certificado_digital']['tmp_name'], $this->upload_path . 'FIRMA/' . $destino)) {
					if (file_exists($this->upload_path . 'FIRMA/' . $destino)) {
					}
				}	
			}
			$path = $this->upload_path . "BETA/" . $this->input->post('Nu_Documento_Identidad');
			if(!is_dir($path)){
			    mkdir($path,0755,TRUE);
			}
			$path = $this->upload_path . "PRODUCCION/" . $this->input->post('Nu_Documento_Identidad');
			if(!is_dir($path)){
			    mkdir($path,0755,TRUE);
			}
		}

		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('ENu_Documento_Identidad') != '') ?
			$this->MonitoreoEmpresasModel->actualizarEmpresa(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'Nu_Documento_Identidad' => $this->input->post('ENu_Documento_Identidad')), $data, $this->input->post('ENu_Documento_Identidad'))
		:
			$this->MonitoreoEmpresasModel->agregarEmpresa($data)
		);
	}
    
	public function eliminarEmpresa($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->MonitoreoEmpresasModel->eliminarEmpresa($this->security->xss_clean($ID)));
	}
	
	public function getDistritos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->getDistritos());
	}
	
	public function getEmpresas(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->getEmpresas());
	}
    
	public function configuracionAutomaticaOpciones(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode( $this->MonitoreoEmpresasModel->configuracionAutomaticaOpciones($this->input->post()));
	}
	
	public function getPrimerUsuarioLaeGestionxEmpresa(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->getPrimerUsuarioLaeGestionxEmpresa($this->input->post()));
	}
	
	public function cambiarEstadoLaeGestion($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarEstadoLaeGestion($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarPlanLaeGestion($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarPlanLaeGestion($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoLaeShop($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarEstadoLaeShop($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarPlanLaeShop($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarPlanLaeShop($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function configurarTiendaVirtual(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->configurarTiendaVirtual($this->input->post()));
	}
	
	public function cambiarEstadoEmpresa($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarEstadoEmpresa($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoVendedorDrop($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarEstadoVendedorDrop($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoProveedorDrop($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarEstadoProveedorDrop($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoTiendaPropia($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->cambiarEstadoTiendaPropia($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function verProgresoTienda(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MonitoreoEmpresasModel->verProgresoTienda($this->input->post()));
	}

	public function actualizarDepositoBilletera(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		$arrDataDepositoBilletera = $_POST['arrDataDepositoBilletera'];
		$data = array('Ss_Deposito_Pago_Billetera' => $arrDataDepositoBilletera['importe_deposito']);
		$where = array('ID_Empresa' => $arrDataDepositoBilletera['id_empresa']);
		$response = $this->MonitoreoEmpresasModel->actualizarDepositoBilletera($where, $data);
		echo json_encode($response);
		exit();
	}

	public function actualizarSaldoAcumuladoBilletera(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		$arrDataDepositoBilletera = $_POST['arrDataDepositoBilletera'];
		$data = array('Ss_Saldo_Acumulado_Billetera' => $arrDataDepositoBilletera['importe_deposito']);
		$where = array('ID_Empresa' => $arrDataDepositoBilletera['id_empresa']);
		$response = $this->MonitoreoEmpresasModel->actualizarSaldoAcumuladoBilletera($where, $data);
		echo json_encode($response);
		exit();
	}
}
