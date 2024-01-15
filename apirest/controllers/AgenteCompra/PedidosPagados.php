<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosPagados extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/PedidosPagadosModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('AgenteCompra/PedidosPagadosView');
			$this->load->view('footer_v2', array("js_pedidos_pagados" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosPagadosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

            $rows[] = $row->No_Pais;
            $rows[] = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);
            $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto;
            //$rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerTipoServicioArray($row->Nu_Tipo_Servicio);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Trading" title="Trading" href="javascript:void(0)" onclick="cambiarTipoServicio(\'' . $row->ID_Pedido_Cabecera . '\',1);">Trading</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="C. Trading" title="C. Trading" href="javascript:void(0)" onclick="cambiarTipoServicio(\'' . $row->ID_Pedido_Cabecera . '\',2);">C. Trading</a></li>';
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
					//$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Enviado" title="Enviado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Enviado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago 30%" title="Pago 30%" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Pago 30%</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago 70%" title="Pago 70%" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',7, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Pago 70%</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago Servicio" title="Pago Servicio" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',9, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Pago Servicio</a></li>';
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
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Producción" title="Producción" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Producción</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Inspección" title="Inspección" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Inspección</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Entregado</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso==1){//no tiene acceso a cambiar status de China
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}
            $rows[] = $dropdown_estado;//china
			
			//Pagos
			$rows[] = '<button class="btn btn-xs btn-link" alt="Pagar proveedor" title="Pagar proveedor" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button>';

			//inspeccion
			$btn_inspeccion = '';
			if($row->Nu_Estado_China==5 || $row->Nu_Estado_China==6)
				$btn_inspeccion = '<button class="btn btn-xs btn-link" alt="Subir inspección" title="Subir inspección" href="javascript:void(0)"  onclick="subirInspeccion(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-search fa-2x" aria-hidden="true"></i></button>';
			$rows[] = $btn_inspeccion;
			
			//entregado
			$btn_entregado = '';
			if($row->Nu_Estado_China==6)
				$btn_entregado = '<button class="btn btn-xs btn-link" alt="Subir documento" title="Subir documento" href="javascript:void(0)" onclick="documentoEntregado(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-folder fa-2x" aria-hidden="true"></i></button>';

			if(!empty($row->Txt_Url_Archivo_Documento_Entrega)) {
				$btn_entregado .= '<br><button class="btn btn-xs btn-link" alt="Subir documento" title="Subir documento" href="javascript:void(0)" onclick="descargarDocumentoEntregado(\'' . $row->ID_Pedido_Cabecera . '\')">Descargar</button>';
			}

			$rows[] = $btn_entregado;

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->PedidosPagadosModel->count_all(),
	        'recordsFiltered' => $this->PedidosPagadosModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->PedidosPagadosModel->get_by_id($this->security->xss_clean($ID)));
    }

	public function cambiarEstado($ID, $Nu_Estado, $id_correlativo){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosPagadosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($id_correlativo)));
	}

	public function crudPedidoGrupal(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
		);
		echo json_encode($this->PedidosPagadosModel->actualizarPedido(
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
    	echo json_encode($this->PedidosPagadosModel->addPagoProveedor($this->input->post(), $_FILES));
	}

	public function downloadImage($id){
		//echo "hola";
		$objPedido = $this->PedidosPagadosModel->getDownloadImage($this->security->xss_clean($id));
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
        $data = $this->PedidosPagadosModel->get_by_id($this->security->xss_clean($ID));
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

	public function cambiarEstadoChina($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosPagadosModel->cambiarEstadoChina($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}

	public function addInspeccionProveedor(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosPagadosModel->addInspeccionProveedor($this->input->post(), $_FILES));
	}
    	
	public function ajax_edit_inspeccion($ID){
        echo json_encode($this->PedidosPagadosModel->get_by_id_inspeccion($this->security->xss_clean($ID)));
    }

	public function addFileProveedor(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosPagadosModel->addFileProveedor($this->input->post(), $_FILES));
	}
    	
	public function descargarDocumentoEntregado($id){
		//echo "hola";
		$objPedido = $this->PedidosPagadosModel->descargarDocumentoEntregado($this->security->xss_clean($id));
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
    	echo json_encode($this->PedidosPagadosModel->addPagoCliente30($this->input->post(), $_FILES));
	}
    	
	public function descargarPago30($id){
		//echo "hola";
		$objPedido = $this->PedidosPagadosModel->descargarPago30($this->security->xss_clean($id));
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
    	echo json_encode($this->PedidosPagadosModel->addPagoCliente100($this->input->post(), $_FILES));
	}
    	
	public function descargarPago100($id){
		//echo "hola";
		$objPedido = $this->PedidosPagadosModel->descargarPago100($this->security->xss_clean($id));
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

	public function cambiarTipoServicio($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosPagadosModel->cambiarTipoServicio($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}

	public function addPagoClienteServicio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosPagadosModel->addPagoClienteServicio($this->input->post(), $_FILES));
	}
    	
	public function descargarPagoServicio($id){
		//echo "hola";
		$objPedido = $this->PedidosPagadosModel->descargarPagoServicio($this->security->xss_clean($id));
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
    	
	public function elminarItemProveedor($id){
		//echo "hola";
		echo json_encode($this->PedidosPagadosModel->elminarItemProveedor($this->security->xss_clean($id)));
	}
}
