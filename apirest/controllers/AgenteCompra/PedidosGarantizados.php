<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosGarantizados extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/PedidosGarantizadosModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('AgenteCompra/PedidosGarantizadosView');
			$this->load->view('footer_v2', array("js_pedidos_garantizados" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosGarantizadosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

            $rows[] = $row->No_Pais;
            $rows[] = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . '-' . $row->Nu_Correlativo;
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);
            $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto;
            $rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad;
			
			//EXCEL cliente de pedido
			$excel_consolida_trading = 'P.C.T. <button class="btn btn-xs btn-link" alt="Proforma C. Trading" title="Proforma C. Trading" href="javascript:void(0)" onclick="generarConsolidaTrading(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-excel text-green fa-2x"></i></button>';
			$excel_agente_compra = 'P.T. <button class="btn btn-xs btn-link" alt="Proforma Trading" title="Proforma Trading" href="javascript:void(0)" onclick="generarAgenteCompra(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-excel text-green fa-2x"></i></button>';
			$rows[] = $excel_consolida_trading . '<br>' . $excel_agente_compra;

            //$rows[] = round($row->Qt_Total, 2);
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
            //$rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2);">Garantizado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Enviado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4);">Rechazado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5);">Confirmado</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso==2){//no tiene acceso a cambiar status de Perú
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}
            $rows[] = $dropdown_estado;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($row->Nu_Estado_China);
			$dropdown_estado_china = '<div class="dropdown">';
				$dropdown_estado_china .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado_china .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado_china .= '<span class="caret"></span></button>';
				$dropdown_estado_china .= '<ul class="dropdown-menu">';
					$dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',1);">Pendiente</a></li>';
					$dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="En proceso" title="En proceso" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',2);">En proceso</a></li>';
					$dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Cotizado" title="Cotizado" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',3);">Cotizado</a></li>';
				$dropdown_estado_china .= '</ul>';
			$dropdown_estado_china .= '</div>';

			//if($this->user->Nu_Tipo_Privilegio_Acceso==1){//no tiene acceso a cambiar status de China
				//$dropdown_estado_china = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			//}

            $rows[] = $dropdown_estado_china;

			$rows[] = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->PedidosGarantizadosModel->get_by_id($this->security->xss_clean($ID)));
    }
    	
	public function getItemProveedor($ID){
        echo json_encode($this->PedidosGarantizadosModel->getItemProveedor($this->security->xss_clean($ID)));
    }
    	
	public function getItemImagenProveedor($ID){
        echo json_encode($this->PedidosGarantizadosModel->getItemImagenProveedor($this->security->xss_clean($ID)));
    }
    	
	public function elegirItemProveedor($id_detalle, $ID, $status){
        echo json_encode($this->PedidosGarantizadosModel->elegirItemProveedor($this->security->xss_clean($id_detalle), $this->security->xss_clean($ID), $this->security->xss_clean($status)));
    }

	public function actualizarElegirItemProductos(){
		//array_debug($this->input->post());
        echo json_encode($this->PedidosGarantizadosModel->actualizarElegirItemProductos($this->input->post()));
    }

	public function cambiarEstado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGarantizadosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}

	public function cambiarEstadoChina($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGarantizadosModel->cambiarEstadoChina($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}

	public function crudPedidoGrupal(){
		//array_debug($this->input->post());
		
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'Ss_Tipo_Cambio' => $this->input->post('Ss_Tipo_Cambio'),
		);
		echo json_encode($this->PedidosGarantizadosModel->actualizarPedido(
				array(
					'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
				),
				$data,
				$this->input->post('addProducto')
			)
		);
	}

	public function addPedidoItemProveedor(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		echo json_encode($this->PedidosGarantizadosModel->addPedidoItemProveedor($this->input->post(), $_FILES));
		exit();
	}

	//generar cotización PDF para pedido de cliente	
	public function generarAgenteCompra($ID){
        $data = $this->PedidosGarantizadosModel->get_by_id_excel($this->security->xss_clean($ID));
		//array_debug($data);

		if( !empty($data) ){
			$sCorrelativoCotizacion = strtoupper(substr(getNameMonth($data[0]->Fe_Month), 0 , 3)) . '-' . $data[0]->Nu_Correlativo;
			//GENERAR EXCEL
			$this->load->library('Excel');
	  			
			$fileNameExcel = "Proforma_Trading_" . $sCorrelativoCotizacion . ".xls";
				
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->getActiveSheet()->setTitle('Cot. ' . $sCorrelativoCotizacion);
			
			$hoja_activa = 0;
		
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
			
			//Title
			$fila=1;
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B2', 'COTIZACIÓN DE PRODUCTOS');
			$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setSize(18);

			$fila=2;
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo ProBusiness');
			$objDrawing->setDescription('Comunidad de Importadores');
			$objDrawing->setPath('assets/img/logos/logo_horizontal_probusiness_claro.png');
			$objDrawing->setHeight(210);
			$objDrawing->setWidth(200);
			$objDrawing->setCoordinates('B' . $fila);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_center);

			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':N'.$fila);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->getFont()->setBold(true);
			// /. Title

			$fila=3;
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('C'.$fila, 'Cliente: ' . $data[0]->No_Contacto);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C'.$fila.':D'.$fila);
			
			$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'Correo: ' . $data[0]->Txt_Email_Contacto);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E'.$fila.':F'.$fila);
			
			$fila=4;
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('C'.$fila, 'N° COTIZACIÓN: ' . $sCorrelativoCotizacion);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C'.$fila.':D'.$fila);
			
			$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'AGENTE');
			
			$objPHPExcel->getActiveSheet()->getStyle('F'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F'.$fila, 'FECHA');
			
			$objPHPExcel->getActiveSheet()->getStyle('H'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('H'.$fila, ToDateBD($data[0]->Fe_Emision_Cotizacion));

			$objPHPExcel->getActiveSheet()->getStyle('I'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I'.$fila, 'VALIDEZ 7 DÍAS');//PREGUNTAR
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.$fila.':J'.$fila);

			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'N' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);
		
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->getFont()->setBold(true);

			$fila = 6;
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("5");
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("7");
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("40");
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("40");
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("40");
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("30");
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("30");
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("40");

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B' . $fila, 'N')
			->setCellValue('C' . $fila, 'FOTO DEL PRODUCTO')
			->setCellValue('D' . $fila, 'NOMBRE COMERCIAL')
			->setCellValue('E' . $fila, 'CARACTERÍSTICAS')
			->setCellValue('F' . $fila, 'CANTIDAD TOTAL')
			->setCellValue('G' . $fila, 'PRECIO UNITARIO EXW (RMB)')//PRECIO EN YUANES
			->setCellValue('H' . $fila, 'PRECIO UNITARIO EXW (USD)')
			->setCellValue('I' . $fila, 'COSTO TOTAL')
			->setCellValue('J' . $fila, 'PCS / CAJA')
			->setCellValue('K' . $fila, 'TOTAL CAJAS')
			->setCellValue('L' . $fila, 'CBM / CAJA')
			->setCellValue('M' . $fila, 'CBM TOTAL')
			->setCellValue('N' . $fila, 'TIEMPO PRODUCCION')
			;

			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'N' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'D9D9D9')
					)
				)
			);

			$fila = 7;
			$iCounter=1;
			$fCostoTotalYuanesGeneral = 0;
			$fCostoTotalGeneral = 0;
			$fCbmTotal = 0;
			$fCbmTotalGeneral = 0;
            foreach($data as $row) {

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('B' . $fila, $iCounter);

				if( !empty($row->Txt_Url_Imagen_Producto) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName($row->Txt_Producto);
					$objDrawing->setDescription($row->Txt_Descripcion);
					
					//pruebas localhost
					$row->Txt_Url_Imagen_Producto = 'assets/img/cristina.jpeg';

					//cloud
					/*
					$row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
					$row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
					*/
					if ( file_exists($row->Txt_Url_Imagen_Producto) ) {
						$objDrawing->setPath($row->Txt_Url_Imagen_Producto);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(150);
						//$objDrawing->setHeight($objDrawing->getHeight() - ($objDrawing->getHeight() * .25));
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('C' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('C' . $fila, '');
				}

				$fPrecioYuanes = $row->Ss_Precio;
				$fPrecioDolares = ($row->Ss_Precio * $row->Ss_Tipo_Cambio);
				$fTotalCajas = ($row->Qt_Producto_Caja_Final / $row->Qt_Producto_Caja);//TOTAL CAJAS
				$fCostoTotal = ($fPrecioDolares * $row->Qt_Producto_Caja_Final);
				$fCostoTotalYuanes = ($fPrecioYuanes * $row->Qt_Producto_Caja_Final);
				$fCbmTotal = ($fTotalCajas * $row->Qt_Cbm);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('D' . $fila, $row->Txt_Producto)
				->setCellValue('E' . $fila, $row->Txt_Descripcion)
				->setCellValue('F' . $fila, $row->Qt_Producto_Caja_Final)
				->setCellValue('G' . $fila, $row->Ss_Precio)//precio yuanes
				->setCellValue('H' . $fila, $fPrecioDolares)
				->setCellValue('I' . $fila, $fCostoTotal)
				->setCellValue('J' . $fila, $row->Qt_Producto_Caja)
				->setCellValue('K' . $fila, $fTotalCajas)
				->setCellValue('L' . $fila, $row->Qt_Cbm)
				->setCellValue('M' . $fila, $fCbmTotal)
				->setCellValue('N' . $fila, $row->Nu_Dias_Delivery)
				;

				$fCostoTotalGeneral += $fCostoTotal;//precio en dolares
				$fCostoTotalYuanesGeneral += $fCostoTotalYuanes;//precio en dolares
				$fCbmTotalGeneral += $fCbmTotal;

				$iCounter++;
				$fila++;
			}

			$fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'TOTAL')
            ->setCellValue('I' . $fila, numberFormat($fCostoTotalGeneral, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($fCbmTotalGeneral, 2, '.', ','));

			//SUMAR I y M
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'N' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);
		
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->getFont()->setBold(true);

			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('D' . $fila, 'USD')
			->setCellValue('E' . $fila, 'RMB')
			;
			
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'TOTAL DE LA COMPRA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, numberFormat($fCostoTotalGeneral, 2, '.', ','))
            ->setCellValue('E' . $fila, numberFormat($fCostoTotalYuanesGeneral, 2, '.', ','));

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('H' . $fila, 'PAGOS');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('H'.$fila.':K'.$fila);
			$objPHPExcel->getActiveSheet()
			->getStyle('H' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'COMISION');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('H' . $fila, '1er PAGO');
			$objPHPExcel->getActiveSheet()
			->getStyle('H' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FCE4D6')
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I' . $fila, '0.00');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.$fila.':K'.$fila);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'GASTOS ORIGEN');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('H' . $fila, '2do PAGO');
			$objPHPExcel->getActiveSheet()
			->getStyle('H' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FCE4D6')
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I' . $fila, '0.00');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.$fila.':K'.$fila);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'FTA X1');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('H' . $fila, '3er PAGO');
			$objPHPExcel->getActiveSheet()
			->getStyle('H' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FCE4D6')
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I' . $fila, '0.00');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.$fila.':K'.$fila);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'FLETE');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'TOTAL A PAGAR CHINA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'F2F2F2')
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, $fCostoTotalGeneral);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E' . $fila, $fCostoTotalYuanesGeneral);

			$fila++;
			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'PAIS DE ORIGEN: NINGBO, CHINA');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'IMPORTANTE');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, ' - La comision por el servicio de compra es del 5%, pago minimo $500.');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- A la hora de hacer el pago indicar al banco pago OUR.');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- Este servicio incluye compra la carga, revisar y realizar la documentacion de exportacion.');

			//DIBUJAR SELLO
			$objDrawing = new PHPExcel_Worksheet_Drawing();
					
			$objDrawing->setName('Sello ProBusiness China');
			$sSelloEmpresa = 'assets/img/sello_probusiness_china.png';
			if ( file_exists($sSelloEmpresa) ) {
				$objDrawing->setPath($sSelloEmpresa);
				$objDrawing->setWidthAndHeight(150,600);
				$objDrawing->setResizeProportional(true);

				$sello_fila = $fila;
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.($sello_fila+1).':I'.($sello_fila+8));
				$objDrawing->setCoordinates('I' . ($sello_fila+1));
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			}

			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'CUENTA BANCARIA CHINA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':E'.$fila);
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);
		   
			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '** Beneficiary Bank: ZHEJIANG CHOUZHOU COMMERCIAL BANK');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- SWIFT BIC: CZCBCN2X');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- City: YIWU');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- Province: ZHEJIANG');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- Country: CHINA');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- Bank Address: No. 1401 North Chouzhou Road Yiwu Zhejiang China');

			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '** Beneficiary Name:   CHRIS FACTORY LIMITED');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- Beneficiary Account:   NRA15602002010590009448');
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- Company Address: Room 2107 21/F CC Wu Building  302-308 Henessy Road, Wanchai, Hong Kong');

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
	

	//generar cotización PDF para pedido de cliente	
	public function generarConsolidaTrading($ID){
        $data = $this->PedidosGarantizadosModel->get_by_id_excel($this->security->xss_clean($ID));
		//array_debug($data);

		if( !empty($data) ){
			$sCorrelativoCotizacion = strtoupper(substr(getNameMonth($data[0]->Fe_Month), 0 , 3)) . '-' . $data[0]->Nu_Correlativo;
			//GENERAR EXCEL
			$this->load->library('Excel');
	  			
			$fileNameExcel = "Proforma_C_Trading_" . $sCorrelativoCotizacion . ".xls";
				
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->getActiveSheet()->setTitle('Cot. ' . $sCorrelativoCotizacion);
			
			$hoja_activa = 0;
		
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
			
			//Title
			$fila=1;
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B2', 'COTIZACIÓN DE PRODUCTOS');
			$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setSize(18);

			$fila=2;
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo ProBusiness');
			$objDrawing->setDescription('Comunidad de Importadores');
			$objDrawing->setPath('assets/img/logos/logo_horizontal_probusiness_claro.png');
			$objDrawing->setHeight(210);
			$objDrawing->setWidth(200);
			$objDrawing->setCoordinates('B' . $fila);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_center);

			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':N'.$fila);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->getFont()->setBold(true);
			// /. Title

			$fila=3;
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('C'.$fila, 'Cliente: ' . $data[0]->No_Contacto);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C'.$fila.':D'.$fila);
			
			$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'Correo: ' . $data[0]->Txt_Email_Contacto);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E'.$fila.':F'.$fila);
			
			$fila=4;
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('C'.$fila, 'N° COTIZACIÓN: ' . $sCorrelativoCotizacion);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C'.$fila.':D'.$fila);
			
			$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'AGENTE');
			
			$objPHPExcel->getActiveSheet()->getStyle('F'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F'.$fila, 'FECHA');
			
			$objPHPExcel->getActiveSheet()->getStyle('H'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('H'.$fila, ToDateBD($data[0]->Fe_Emision_Cotizacion));

			$objPHPExcel->getActiveSheet()->getStyle('I'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I'.$fila, 'VALIDEZ 7 DÍAS');//PREGUNTAR
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.$fila.':J'.$fila);

			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'N' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);
		
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->getFont()->setBold(true);

			$fila = 6;
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("5");
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("7");
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("40");
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("40");
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("40");
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("30");
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("30");
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20");
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("40");

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B' . $fila, 'N')
			->setCellValue('C' . $fila, 'FOTO DEL PRODUCTO')
			->setCellValue('D' . $fila, 'NOMBRE COMERCIAL')
			->setCellValue('E' . $fila, 'CARACTERÍSTICAS')
			->setCellValue('F' . $fila, 'CANTIDAD TOTAL')
			->setCellValue('G' . $fila, 'PRECIO UNITARIO EXW (RMB)')//PRECIO EN YUANES
			->setCellValue('H' . $fila, 'PRECIO UNITARIO EXW (USD)')
			->setCellValue('I' . $fila, 'COSTO TOTAL')
			->setCellValue('J' . $fila, 'PCS / CAJA')
			->setCellValue('K' . $fila, 'TOTAL CAJAS')
			->setCellValue('L' . $fila, 'CBM / CAJA')
			->setCellValue('M' . $fila, 'CBM TOTAL')
			->setCellValue('N' . $fila, 'TIEMPO PRODUCCION')
			;

			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'N' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'D9D9D9')
					)
				)
			);

			$fila = 7;
			$iCounter=1;
			$fCostoTotalYuanesGeneral = 0;
			$fCostoTotalGeneral = 0;
			$fCbmTotal = 0;
			$fCbmTotalGeneral = 0;
            foreach($data as $row) {

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('B' . $fila, $iCounter);

				if( !empty($row->Txt_Url_Imagen_Producto) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName($row->Txt_Producto);
					$objDrawing->setDescription($row->Txt_Descripcion);
					
					//pruebas localhost
					$row->Txt_Url_Imagen_Producto = 'assets/img/cristina.jpeg';

					//cloud
					/*
					$row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
					$row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
					*/
					if ( file_exists($row->Txt_Url_Imagen_Producto) ) {
						$objDrawing->setPath($row->Txt_Url_Imagen_Producto);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(150);
						//$objDrawing->setHeight($objDrawing->getHeight() - ($objDrawing->getHeight() * .25));
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('C' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('C' . $fila, '');
				}

				$fPrecioYuanes = $row->Ss_Precio;
				$fPrecioDolares = ($row->Ss_Precio * $row->Ss_Tipo_Cambio);
				$fTotalCajas = ($row->Qt_Producto_Caja_Final / $row->Qt_Producto_Caja);//TOTAL CAJAS
				$fCostoTotal = ($fPrecioDolares * $row->Qt_Producto_Caja_Final);
				$fCostoTotalYuanes = ($fPrecioYuanes * $row->Qt_Producto_Caja_Final);
				$fCbmTotal = ($fTotalCajas * $row->Qt_Cbm);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('D' . $fila, $row->Txt_Producto)
				->setCellValue('E' . $fila, $row->Txt_Descripcion)
				->setCellValue('F' . $fila, $row->Qt_Producto_Caja_Final)
				->setCellValue('G' . $fila, $row->Ss_Precio)//precio yuanes
				->setCellValue('H' . $fila, $fPrecioDolares)
				->setCellValue('I' . $fila, $fCostoTotal)
				->setCellValue('J' . $fila, $row->Qt_Producto_Caja)
				->setCellValue('K' . $fila, $fTotalCajas)
				->setCellValue('L' . $fila, $row->Qt_Cbm)
				->setCellValue('M' . $fila, $fCbmTotal)
				->setCellValue('N' . $fila, $row->Nu_Dias_Delivery)
				;

				$fCostoTotalGeneral += $fCostoTotal;//precio en dolares
				$fCostoTotalYuanesGeneral += $fCostoTotalYuanes;//precio en dolares
				$fCbmTotalGeneral += $fCbmTotal;

				$iCounter++;
				$fila++;
			}

			$fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'TOTAL')
            ->setCellValue('I' . $fila, numberFormat($fCostoTotalGeneral, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($fCbmTotalGeneral, 2, '.', ','));

			//SUMAR I y M
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'N' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);
		
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':N' . $fila)->getFont()->setBold(true);

			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('D' . $fila, 'USD')
			->setCellValue('E' . $fila, 'RMB')
			;
			
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'TOTAL DE LA COMPRA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, numberFormat($fCostoTotalGeneral, 2, '.', ','))
            ->setCellValue('E' . $fila, numberFormat($fCostoTotalYuanesGeneral, 2, '.', ','));

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('H' . $fila, 'PAGOS');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('H'.$fila.':K'.$fila);
			$objPHPExcel->getActiveSheet()
			->getStyle('H' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'COMISION');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('H' . $fila, '1er PAGO');
			$objPHPExcel->getActiveSheet()
			->getStyle('H' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FCE4D6')
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I' . $fila, '0.00');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.$fila.':K'.$fila);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'TOTAL A PAGAR CHINA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':C'.$fila);
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'F2F2F2')
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, $fCostoTotalGeneral);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E' . $fila, $fCostoTotalYuanesGeneral);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('H' . $fila, '2do PAGO');
			$objPHPExcel->getActiveSheet()
			->getStyle('H' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FCE4D6')
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I' . $fila, '0.00');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'.$fila.':K'.$fila);

			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'CUENTA  CORRIENTE EN DOLARES BCP');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':E'.$fila);
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);
		   
			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '** Beneficiary Bank: ZHEJIANG CHOUZHOU COMMERCIAL BANK');
			$fila++;$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- BANCO: BCP');
			$fila++;$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- TITULAR: GRUPO PROBUSINESS SAC ');
			$fila++;$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- NRO.CUENTA EN DOLARES: 191-9840556-1-63');

			$fila++;
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, 'CUENTA  CORRIENTE EN DOLARES INTERBANK');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'.$fila.':E'.$fila);
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila . ':' . 'E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '009999')
					)
				)
			);
			//DIBUJAR SELLO
			$objDrawing = new PHPExcel_Worksheet_Drawing();
					
			$objDrawing->setName('Sello ProBusiness China');
			$sSelloEmpresa = 'assets/img/sello_probusiness_china.png';
			if ( file_exists($sSelloEmpresa) ) {
				$objDrawing->setPath($sSelloEmpresa);
				$objDrawing->setWidthAndHeight(200,600);
				$objDrawing->setResizeProportional(true);

				$sello_fila = $fila;
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('H'.($sello_fila+1).':H'.($sello_fila+8));
				$objDrawing->setCoordinates('H' . ($sello_fila+1));
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			}

			$fila++;$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- BANCO: INTERBANK');
			$fila++;$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- TITULAR: GRUPO PROBUSINESS SAC ');
			$fila++;$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B' . $fila, '- NRO.CUENTA EN DOLARES: 200-3001727696');

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

	public function downloadImage($id){
		//echo "hola";
		$objPedido = $this->PedidosGarantizadosModel->getDownloadImage($this->security->xss_clean($id));
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
}
