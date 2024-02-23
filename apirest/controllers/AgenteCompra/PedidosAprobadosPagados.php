<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosAprobadosPagados extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/PedidosAprobadosPagadosModel');
		$this->load->model('HelperImportacionModel');
		$this->load->model('NotificacionModel');
		$this->load->model('MenuModel');

		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar($sCorrelativoCotizacion = '', $ID_Pedido_Cabecera = ''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2', array("js_pedidos_aprobados_pagados" => true));
			$this->load->view('AgenteCompra/PedidosAprobadosPagadosView', array(
				'sCorrelativoCotizacion' => $sCorrelativoCotizacion,
				'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera
			));
			$this->load->view('footer_v2', array("js_pedidos_aprobados_pagados" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosAprobadosPagadosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

			$sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
            $rows[] = $row->No_Pais;
            $rows[] = $sCorrelativoCotizacion;
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);
            $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto;
            //$rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad;

			//asignar personal de china desde perú
			$btn_asignar_personal_china = '';
			if($this->user->Nu_Tipo_Privilegio_Acceso==1){//1=probusiness
				$btn_asignar_personal_china = '<button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="asignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->Nu_Estado . '\')"><i class="far fa-user fa-2x" aria-hidden="true"></i></button>';
				if(!empty($row->ID_Usuario_Interno_Empresa_China)){
					$btn_asignar_personal_china = '<span class="badge bg-secondary">' . $row->No_Usuario . '</span>';
					$btn_asignar_personal_china .= '<br><button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="removerAsignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->ID_Usuario_Interno_Empresa_China . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
				}
			}
			$rows[] = $btn_asignar_personal_china;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerTipoServicioArray($row->Nu_Tipo_Servicio);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Trading" title="Trading" href="javascript:void(0)" onclick="cambiarTipoServicio(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $row->ID_Usuario_Interno_Empresa_China . '\');">Trading</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="C. Trading" title="C. Trading" href="javascript:void(0)" onclick="cambiarTipoServicio(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Usuario_Interno_Empresa_China . '\');">C. Trading</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso==2){//no tiene acceso a cambiar status de Perú
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}
			
			$btn_comision_trading = '<button class="btn btn-link" alt="Agregar comisión Trading" title="Agregar comisión Trading" href="javascript:void(0)" onclick="agregarComisionTrading(\'' . $row->ID_Pedido_Cabecera . '\')">(+) Comisión</button>';
			if($row->Ss_Comision_Interna_Trading>0)
				$btn_comision_trading = '$ ' . $row->Ss_Comision_Interna_Trading;
				
            $rows[] = $dropdown_estado . $btn_comision_trading;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerIncoterms($row->Nu_Tipo_Incoterms);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="EXW" title="EXW" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">EXW</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="FOB" title="FOB" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">FOB</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="CIF" title="CIF" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">CIF</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="DDP" title="DDP" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">DDP</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso==2){//no tiene acceso a cambiar status de Perú
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}

            $rows[] = $dropdown_estado;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerTransporteMaritimo($row->Nu_Tipo_Transporte_Maritimo);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="FCL" title="FCL" href="javascript:void(0)" onclick="cambiarTransporte(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">FCL</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="LCL" title="LCL" href="javascript:void(0)" onclick="cambiarTransporte(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">LCL</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso==2){//no tiene acceso a cambiar status de Perú
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}

            $rows[] = $dropdown_estado;

			//EXCEL cliente de pedido
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Orden Tracking" title="Orden Tracking" href="javascript:void(0)" onclick="generarExcelOrderTracking(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-excel text-green fa-2x"></i></button>';

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Enviado" title="Enviado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Volver a Garantizado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago 30%" title="Pago 30%" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Pago 30%</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago 70%" title="Pago 70%" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',7, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Pago 70%</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago Servicio" title="Pago Servicio" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',9, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Pago Servicio</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso==2){//no tiene acceso a cambiar status de Perú
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}

            $rows[] = $dropdown_estado;
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($row->Nu_Estado_China);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Producción" title="Producción" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Producción</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Inspección" title="Inspección" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Inspección</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Entregado</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso==1){//no tiene acceso a cambiar status de China
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}
            $rows[] = $dropdown_estado;//china
			
			//Negociar con proveedores
			$rows[] = '<button class="btn btn-xs btn-link" alt="Negociar con proveedor" title="Negociar con proveedor" href="javascript:void(0)"  onclick="coordinarPagosProveedor(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-handshake fa-2x" aria-hidden="true"></i></button>';

			//Booking
			$rows[] = '<button class="btn btn-xs btn-link" alt="Booking" title="Booking" href="javascript:void(0)"  onclick="booking(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-box fa-2x" aria-hidden="true"></i></button>';

			//Recepcion de carga
			$rows[] = '<button class="btn btn-xs btn-link" alt="Recepcion de carga" title="Recepcion de carga" href="javascript:void(0)"  onclick="recepcionCarga(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';

			//Pagos
			$rows[] = '<button class="btn btn-xs btn-link" alt="Pagar proveedor" title="Pagar proveedor" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button>';

			//inspeccion
			$btn_inspeccion = '';
			if($row->Nu_Estado_China==5 || $row->Nu_Estado_China==6)
				$btn_inspeccion = '<button class="btn btn-xs btn-link" alt="Subir inspección" title="Subir inspección" href="javascript:void(0)"  onclick="subirInspeccion(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-search fa-2x" aria-hidden="true"></i></button>';
			$rows[] = $btn_inspeccion;

			//Invoice proveedor
			$rows[] = '<button class="btn btn-xs btn-link" alt="Invoice proveedor" title="Invoice proveedor" href="javascript:void(0)"  onclick="invoiceProveedor(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-excel fa-2x" aria-hidden="true"></i></button>';
			
			//Despacho
			$btn_despacho = '<button class="btn btn-xs btn-link" alt="Despacho" title="Despacho" href="javascript:void(0)"  onclick="despacho(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-truck fa-2x" aria-hidden="true"></i></button>';
			
			$rows[] = $btn_despacho . '<br>' . (!empty($row->Fe_Entrega_Shipper_Forwarder) ? ToDateBD($row->Fe_Entrega_Shipper_Forwarder) : '');

			//entregado
			$btn_entregado = '';
			if($row->Nu_Estado_China==6)
				$btn_entregado = '<button class="btn btn-xs btn-link" alt="Subir documento" title="Subir documento" href="javascript:void(0)" onclick="documentoEntregado(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-folder fa-2x" aria-hidden="true"></i></button>';

			if(!empty($row->Txt_Url_Archivo_Documento_Entrega)) {
				$btn_entregado .= '<br><button class="btn btn-xs btn-link" alt="Subir documento" title="Subir documento" href="javascript:void(0)" onclick="descargarDocumentoEntregado(\'' . $row->ID_Pedido_Cabecera . '\')">Descargar</button>';
			}

			$rows[] = $btn_entregado;

            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }

	public function ajax_edit($ID){
		$arrReponse = $this->PedidosAprobadosPagadosModel->get_by_id($this->security->xss_clean($ID));
		$sCorrelativoCotizacion = '';
		foreach ($arrReponse as $row) {
			$sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
			$row->sCorrelativoCotizacion = $sCorrelativoCotizacion;
		}
        echo json_encode($arrReponse);
    }

	public function cambiarEstado($ID, $Nu_Estado, $sCorrelativo){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
	}

	public function crudPedidoGrupal(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
		);
		echo json_encode($this->PedidosAprobadosPagadosModel->actualizarPedido(
				array(
					'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
				),
				$data,
				$this->input->post('addProducto')
			)
		);
	}

	public function addPagoProveedor(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addPagoProveedor($this->input->post(), $_FILES));
	}

	public function downloadImage($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->getDownloadImage($this->security->xss_clean($id));
		//array_debug($objPedido);
		
		$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
		$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

		//$file="assets/img/arturo.jpeg";
		if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
			die('file not found');
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($objPedido->Txt_Url_Imagen_Producto);
			exit;
		}
	}

	//generar cotización PDF para pedido de cliente	
	public function generarExcelOrderTracking($ID){
        $data = $this->PedidosAprobadosPagadosModel->get_by_id($this->security->xss_clean($ID));
		//array_debug($data);

		if( !empty($data) ){
			$sCorrelativoCotizacion = strtoupper(substr(getNameMonth($data[0]->Fe_Month), 0 , 3)) . str_pad($data[0]->Nu_Correlativo,3,"0",STR_PAD_LEFT);
			//GENERAR EXCEL
			$this->load->library('Excel');
	  			
			$fileNameExcel = "Orden_Tracking_" . $sCorrelativoCotizacion . ".xls";
				
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->getActiveSheet()->setTitle('Orden Tracking Cot. ' . $sCorrelativoCotizacion);
			
			$hoja_activa = 0;
	
			$BStyle_top_general = array(
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				)
			);
		
			$BStyle_left_general = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				)
			);
		
			$BStyle_right_general = array(
				'borders' => array(
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				)
			);
		
			$BStyle_bottom_general = array(
				'borders' => array(
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				)
			);
		
			$BStyle_top = array(
			'borders' => array(
				'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
			);
			
			$BStyle_left = array(
			'borders' => array(
				'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
			);
			
			$BStyle_right = array(
			'borders' => array(
				'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
			);
			
			$BStyle_bottom = array(
			'borders' => array(
				'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
			);
			
			$style_align_center = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
			);
			
			$style_align_right = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			)
			);
			
			$style_align_left = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
			);
			
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => 'FFFFFF')
					)
				)
			);
			$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

			//Title
			$fila=3;
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_left);
			
			$fila=2;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.($fila+1).':M'.($fila+2));

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B3', 'ORDEN TRACKING');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setSize(16);

			$objPHPExcel->getActiveSheet()
			->getStyle('B3')
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FFFF00')
					)
				)
			);

			$objPHPExcel->getActiveSheet()->getStyle('B3:M3')->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($BStyle_left_general);
			$objPHPExcel->getActiveSheet()->getStyle('M3')->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($BStyle_left_general);
			$objPHPExcel->getActiveSheet()->getStyle('M4')->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_bottom_general);

			$fila = 6;
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':M' . $fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($style_align_center);

			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("30");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("30");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20");//NRO
			
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B' . $fila, 'PRODUCT PHOTO')
			->setCellValue('C' . $fila, 'PRODUCT NAME')
			->setCellValue('D' . $fila, 'ITEM No')
			->setCellValue('E' . $fila, 'QTY')
			->setCellValue('F' . $fila, 'PRICE')
			->setCellValue('G' . $fila, 'AMOUNT')
			->setCellValue('H' . $fila, 'DEPOSIT #1')
			->setCellValue('I' . $fila, 'BALANCE')
			->setCellValue('J' . $fila, 'DEPOSIT #2')
			->setCellValue('K' . $fila, 'DELIVERY DATE')
			->setCellValue('L' . $fila, 'SUPPLIER')
			->setCellValue('M' . $fila, 'PHONE NUMBER')
			;

			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left_general);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_bottom_general);

			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_bottom_general);
			
			$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_top_general);
			$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_bottom_general);

			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'M' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);
			
			$fila = 7;
			
			$fSumGeneralAmount = 0;
			$fSumGeneralPago1 = 0;
			$fSumGeneralBalance = 0;
			$fSumGeneralPago2 = 0;
			$iCounter = 1;
            foreach($data as $row) {
				if( !empty($row->Txt_Url_Imagen_Producto) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName($row->Txt_Producto);
					
					//pruebas localhost
					//$objDrawing->setPath('assets/img/unicpn.png');

					//cloud
					$row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
					$row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
					if ( file_exists($row->Txt_Url_Imagen_Producto) ) {
						$objDrawing->setPath($row->Txt_Url_Imagen_Producto);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(130);
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('B' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('B' . $fila, '');
				}

				$fAmount = ($row->Qt_Producto * $row->Ss_Precio);
				$fBalance = ($fAmount - $row->Ss_Pago_1_Proveedor);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('C' . $fila, $row->Txt_Producto)
				->setCellValue('D' . $fila, '1')
				->setCellValue('E' . $fila, $row->Qt_Producto)
				->setCellValue('F' . $fila, $row->Ss_Precio)
				->setCellValue('G' . $fila, $fAmount)
				->setCellValue('H' . $fila, $row->Ss_Pago_1_Proveedor)
				->setCellValue('I' . $fila, $fBalance)
				->setCellValue('J' . $fila, $row->Ss_Pago_2_Proveedor)
				->setCellValue('K' . $fila, $row->Nu_Dias_Delivery)
				;
				
				$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_top_general);
				$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_left_general);
				$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_right_general);
				$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_bottom_general);

				$objPHPExcel->getActiveSheet()
				->getStyle('H' . $fila)
				->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'D6E3BC')
						)
					)
				);
				
				$objPHPExcel->getActiveSheet()
				->getStyle('I' . $fila)
				->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFFF00')
						)
					)
				);
				
				$objPHPExcel->getActiveSheet()
				->getStyle('J' . $fila)
				->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'F79646')
						)
					)
				);

				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');

				$fSumGeneralAmount += $fAmount;
				$fSumGeneralPago1 += $row->Ss_Pago_1_Proveedor;
				$fSumGeneralBalance += $fBalance;
				$fSumGeneralPago2 += $row->Ss_Pago_2_Proveedor;

				$iCounter++;
				$fila++;
			} // /. foreach data
			
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':J' . $fila)->applyFromArray($style_align_right);

			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left_general);
			$objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right_general);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':J' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('F' . $fila, 'TOTAL')
            ->setCellValue('G' . $fila, numberFormat($fSumGeneralAmount, 2, '.', ','))
            ->setCellValue('H' . $fila, numberFormat($fSumGeneralPago1, 2, '.', ','))
			->setCellValue('I' . $fila, numberFormat($fSumGeneralPago1, 2, '.', ','))
			->setCellValue('J' . $fila, numberFormat($fSumGeneralPago1, 2, '.', ','));

			//SUMAR I y M
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'J' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'F2F2F2')
					)
				)
			);
		
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':J' . $fila)->getFont()->setBold(true);

			header('Content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			//FIN DE GENERAR EXCEL
		} else {
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('A' . $fila, 'No hay registro');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':O' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
		}
	}

	public function cambiarEstadoChina($ID, $Nu_Estado, $sCorrelativo){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->cambiarEstadoChina($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
	}

	public function addInspeccionProveedor(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addInspeccionProveedor($this->input->post(), $_FILES));
	}
    	
	public function ajax_edit_inspeccion($ID){
        echo json_encode($this->PedidosAprobadosPagadosModel->get_by_id_inspeccion($this->security->xss_clean($ID)));
    }

	public function addFileProveedor(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addFileProveedor($this->input->post(), $_FILES));
	}
    	
	public function descargarDocumentoEntregado($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarDocumentoEntregado($this->security->xss_clean($id));
		//array_debug($objPedido);
		
		$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
		$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

		//$file="assets/img/arturo.jpeg";
		if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
			die('file not found');
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($objPedido->Txt_Url_Imagen_Producto);
			exit;
		}
    }

	public function addPagoCliente30(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addPagoCliente30($this->input->post(), $_FILES));
	}
    	
	public function descargarPago30($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPago30($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 30%');
			}
		} else {
			die('No existe registro');
		}
    }

	public function addPagoCliente100(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addPagoCliente100($this->input->post(), $_FILES));
	}
    	
	public function descargarPago100($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPago100($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 100%');
			}
		} else {
			die('No existe registro');
		}
    }

	public function cambiarTipoServicio($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->cambiarTipoServicio($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($ID_Usuario_Interno_Empresa_China)));
	}

	public function addPagoClienteServicio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addPagoClienteServicio($this->input->post(), $_FILES));
	}
    	
	public function descargarPagoServicio($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPagoServicio($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 100%');
			}
		} else {
			die('No existe registro');
		}
    }
    	
	public function elminarItemProveedor($id, $correlativo, $name_item){
		//echo "hola";
		echo json_encode($this->PedidosAprobadosPagadosModel->elminarItemProveedor($this->security->xss_clean($id), $this->security->xss_clean($correlativo), $this->security->xss_clean($name_item)));
	}

	public function cambiarIncoterms($ID, $Nu_Estado, $sCorrelativo){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->cambiarIncoterms($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
	}

	public function cambiarTransporte($ID, $Nu_Estado, $sCorrelativo){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->cambiarTransporte($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
	}

	public function agregarComisionTrading(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		$arrData = $_POST['arrData'];
		$data = array(
			'Ss_Comision_Interna_Trading' => $arrData['precio_comision_trading']
		);
		$response = $this->PedidosAprobadosPagadosModel->agregarComisionTrading(array('ID_Pedido_Cabecera' => $arrData['id_pedido_cabecera']), $data);
		echo json_encode($response);
		exit();
	}

	public function addPagoFlete(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addPagoFlete($this->input->post(), $_FILES));
	}
    	
	public function descargarPagoFlete($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPagoFlete($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 100%');
			}
		} else {
			die('No existe registro');
		}
    }

	public function addPagoCostosOrigen(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addPagoCostosOrigen($this->input->post(), $_FILES));
	}
    	
	public function descargarPagoCostosOrigen($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPagoCostosOrigen($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 100%');
			}
		} else {
			die('No existe registro');
		}
    }

	public function addPagoFta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addPagoFta($this->input->post(), $_FILES));
	}
    	
	public function descargarPagoFTA($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPagoFTA($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 100%');
			}
		} else {
			die('No existe registro');
		}
    }

	public function addOtrosCuadrilla(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addOtrosCuadrilla($this->input->post(), $_FILES));
	}
    	
	public function descargarPagoCuadrilla($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPagoCuadrilla($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 100%');
			}
		} else {
			die('No existe registro');
		}
    }

	public function addOtrosCostos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->addOtrosCostos($this->input->post(), $_FILES));
	}
    	
	public function descargarPagoOtrosCostos($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarPagoOtrosCostos($this->security->xss_clean($id));
		if(is_object($objPedido)){
			if(!empty($objPedido->Txt_Url_Imagen_Producto)) {
				//array_debug($objPedido);
				
				$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
				$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

				//$file="assets/img/arturo.jpeg";
				if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
					die('file not found');
				} else {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					ob_clean();
					flush();
					readfile($objPedido->Txt_Url_Imagen_Producto);
					exit;
				}
			} else {
				die('Primero subir voucher de pago del 100%');
			}
		} else {
			die('No existe registro');
		}
    }

	public function crudProveedor(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			$data = array(
				'No_Wechat' => $this->input->post('proveedor-No_Wechat'),
				'No_Rubro' => $this->input->post('proveedor-No_Rubro'),
				'No_Cuenta_Bancaria' => $this->input->post('proveedor-No_Cuenta_Bancaria'),
				'Ss_Pago_Importe_1' => $this->input->post('proveedor-Ss_Pago_Importe_1'),
			);
			echo json_encode($this->PedidosAprobadosPagadosModel->actualizarProveedor(
					array('ID_Entidad' => $this->input->post('proveedor-ID_Entidad')),
					$data,
					array('ID_Pedido_Detalle_Producto_Proveedor' => $this->input->post('proveedor-ID_Pedido_Detalle_Producto_Proveedor'))
				)
			);
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
		}
	}
	
	public function getPedidoProveedor($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->PedidosAprobadosPagadosModel->getPedidoProveedor($this->security->xss_clean($ID)));
	}

	public function reservaBooking(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			$data = array(
				'Qt_Caja_Total_Booking' => $this->input->post('booking-Qt_Caja_Total_Booking'),
				'Qt_Cbm_Total_Booking' => $this->input->post('booking-Qt_Cbm_Total_Booking'),
				'Qt_Peso_Total_Booking' => $this->input->post('booking-Qt_Peso_Total_Booking')
			);
			echo json_encode($this->PedidosAprobadosPagadosModel->reservaBooking(array('ID_Pedido_Cabecera' => $this->input->post('booking-ID_Pedido_Cabecera')), $data));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
		}
	}
	
	public function getBooking($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->PedidosAprobadosPagadosModel->getBooking($this->security->xss_clean($ID)));
	}

	public function actualizarRecepcionCargaItemProveedor(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			$data = array(
				'Qt_Producto_Caja_Final_Verificada' => $this->input->post('cantidad'),
				'Nu_Estado_Recepcion_Carga_Proveedor_Item' => $this->input->post('estado')
			);
			echo json_encode($this->PedidosAprobadosPagadosModel->actualizarRecepcionCargaItemProveedor(array('ID_Pedido_Detalle_Producto_Proveedor' => $this->input->post('id')), $data));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
		}
	}

	public function actualizarRecepcionCargaProveedor(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			$data = array(
				'Txt_Nota_Recepcion_Carga_Proveedor' => $this->input->post('nota')
			);
			echo json_encode($this->PedidosAprobadosPagadosModel->actualizarRecepcionCargaProveedor(array('ID_Pedido_Detalle_Producto_Proveedor' => $this->input->post('id')), $data));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
		}
	}

	public function subirInvoicePlProveedor(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			
			echo json_encode($this->PedidosAprobadosPagadosModel->subirInvoicePlProveedor($this->input->post(), $_FILES));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
		}
	}
    	
	public function descargarInvoicePlProveedor($id){
		//echo "hola";
		$objPedido = $this->PedidosAprobadosPagadosModel->descargarInvoicePlProveedor($this->security->xss_clean($id));
		//array_debug($objPedido);
		
		$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
		$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

		//$file="assets/img/arturo.jpeg";
		if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
			die('file not found');
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($objPedido->Txt_Url_Imagen_Producto);
			exit;
		}
    }

	public function despacho(){
		if(isset($this->session->userdata['usuario'])) {
			if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			$data = array(
				'Fe_Entrega_Shipper_Forwarder' => ToDate($this->input->post('despacho-Fe_Entrega_Shipper_Forwarder'))
			);
			echo json_encode($this->PedidosAprobadosPagadosModel->despacho(array('ID_Pedido_Cabecera' => $this->input->post('despacho-id_cabecera')), $data));
		} else {
			echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
		}
	}

	public function asignarUsuarioPedidoChina(){
		//array_debug($this->input->post());
		echo json_encode($this->PedidosAprobadosPagadosModel->asignarUsuarioPedidoChina($this->input->post()));
		exit();
	}

	public function removerAsignarPedido($ID, $id_usuario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAprobadosPagadosModel->removerAsignarPedido($this->security->xss_clean($ID), $this->security->xss_clean($id_usuario)));
	}
}
