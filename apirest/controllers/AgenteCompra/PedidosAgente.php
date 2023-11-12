<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosAgente extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/PedidosAgenteModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('AgenteCompra/PedidosAgenteView');
			$this->load->view('footer_v2', array("js_pedidos_agente" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosAgenteModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

            $rows[] = $row->No_Pais;
            $rows[] = $row->ID_Pedido_Cabecera;
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);
            $rows[] = $row->No_Entidad . "\n" . $row->Nu_Celular_Entidad;
			
			//EXCEL cliente de pedido
			$rows[] = '<button class="btn btn-xs btn-link" alt="PDF" title="PDF" href="javascript:void(0)" onclick="generarExcelPedidoCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-excel text-green fa-2x"></i></button>';

			//PDF cliente de pedido
			$rows[] = '<button class="btn btn-xs btn-link" alt="PDF" title="PDF" href="javascript:void(0)" onclick="generarPDFPedidoCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-pdf text-danger fa-2x"></i></button>';

            //$rows[] = round($row->Qt_Total, 2);
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoArray($row->Nu_Estado);
            //$rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item"><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',1);">Pendiente</a></li>';
					$dropdown_estado .= '<li class="dropdown-item"><a alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2);">Confirmado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item"><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Entregado</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
            $rows[] = $dropdown_estado;

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
        echo json_encode($this->PedidosAgenteModel->get_by_id($this->security->xss_clean($ID)));
    }

	public function cambiarEstado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAgenteModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}

	public function crudPedidoGrupal(){
		//array_debug($this->input->post());
		
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('EID_Empresa'),
			'ID_Organizacion' => $this->input->post('EID_Organizacion'),
			'Nu_Documento_Identidad' => $this->input->post('Nu_Documento_Identidad'),
			'No_Entidad' => $this->input->post('No_Entidad'),
			'Nu_Celular_Entidad' => $this->input->post('Nu_Celular_Entidad'),
			'Txt_Email_Entidad' => $this->input->post('Txt_Email_Entidad'),
		);
		echo json_encode($this->PedidosAgenteModel->actualizarPedido(
				array(
					'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
					'ID_Entidad' => $this->input->post('EID_Entidad'),
				),
				$data,
				$this->input->post('addProducto')
			)
		);
	}

	//generar cotización PDF para pedido de cliente	
	public function generarPDFPedidoCliente($ID){
        $data = $this->PedidosAgenteModel->get_by_id($this->security->xss_clean($ID));
		//array_debug($data);

		if( !empty($data) ){
			$this->load->library('Pdf');
			
			$this->load->library('EnLetras', 'el');
			$EnLetras = new EnLetras();

			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
			
			ob_start();
			$file = $this->load->view('AgenteCompra/pdf/PedidosAgentePDFView', array(
				'arrDataEmpresa' => $data,
				'arrData' => $data,
				'totalEnLetras'	=> $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),
			));
			$html = ob_get_contents();
			ob_end_clean();
			
			$pdf->SetAuthor('ProBusiness');
			$pdf->SetTitle('ProBusiness_Cotizacion_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Pedido_Cabecera);
		
			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-20, PDF_MARGIN_RIGHT-5);
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			
			$pdf->AddPage();
				
			$sNombreLogo=str_replace(' ', '_', $data[0]->No_Logo_Empresa);
			$sRutaArchivoLogoCliente = $this->logo_cliente_path . $sNombreLogo;
			$sUrlImagen = $data[0]->No_Imagen_Logo_Empresa;

			$sCssFontFamily='Arial';
			$format_header = '<table border="0" cellspacing="1" cellpadding="0">';
				$format_header .= '<tr>';
					$format_header .= '<td style="width: 50%; text-align: left;">';
						$format_header .= '<img style="height: ' . $data[0]->Nu_Height_Logo_Ticket . 'px; width: ' . $data[0]->Nu_Width_Logo_Ticket . 'px;" src="' . $sUrlImagen . '"><br>';
					$format_header .= '</td>';
					$format_header .= '<td style="width: 50%; text-align: right;">';
						if(!empty($data[0]->No_Empresa_Comercial))
							$format_header .= '<label style="font-size: 11px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $data[0]->No_Empresa_Comercial . '</b></label><br>';
						else
							$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $data[0]->No_Empresa . '</b></label><br>';
						$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>RUC: ' . $data[0]->Nu_Documento_Identidad_Empresa . '</b></label><br>';
						if(!empty($data[0]->Txt_Direccion_Empresa))
							$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . $data[0]->Txt_Direccion_Empresa . ' - ' . $data[0]->No_Departamento . ' - ' . $data[0]->No_Provincia . ' - ' . $data[0]->No_Distrito . '</label><br>';
						if(!empty($data[0]->No_Dominio_Empresa))
							$format_header .= '<label style="color: #000000; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $data[0]->No_Dominio_Empresa . '</label><br>';
						if(!empty($data[0]->Nu_Celular_Empresa))
							$format_header .= '<label style="color: #868686; font-size: 10px; font-family: "Times New Roman", Times, serif;">Celular: ' . $data[0]->Nu_Celular_Empresa . '</label><br>';
						if(!empty($data[0]->Txt_Email_Empresa))
							$format_header .= '<label style="color: #34bdad; font-size: 10px; font-family: "Times New Roman", Times, serif;">Correo: ' . $data[0]->Txt_Email_Empresa . '</label><br>';
						if(!empty($data[0]->Txt_Slogan_Empresa))
							$format_header .= '<label style="color: #979797; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $data[0]->Txt_Slogan_Empresa . '</label>';
					$format_header .= '</td>';
				$format_header .= '</tr>';
			$format_header .= '</table>';
			$pdf->writeHTML($format_header, true, 0, true, 0);

			$pdf->setFont('helvetica', '', 7);
			$pdf->writeHTML($html, true, false, true, false, '');
			
			$file_name = 'ProBusiness_Cotizacion_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Pedido_Cabecera . ".pdf";
			$pdf->Output($file_name, 'I');
		} else {
			exit();
			//alert('no existe');
		}
	}

	//generar cotización PDF para pedido de cliente	
	public function generarExcelPedidoCliente($ID){
        $data = $this->PedidosAgenteModel->get_by_id($this->security->xss_clean($ID));
		//array_debug($data);

		if( !empty($data) ){
			//GENERAR EXCEL
			$this->load->library('Excel');
	  			
			$fileNameExcel = "AgenteCompra_Pedido_" . $ID . ".xls";
				
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->getActiveSheet()->setTitle('Pedido Nro. ' . $ID);
			
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
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('A1', $this->empresa->No_Empresa)
			->setCellValue('B2', 'Solicitud de Cotización')
			->setCellValue('B3', 'Pedido Nro. ' . $ID);
			
			$objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B2:F2');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B3:F3');
			$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
			// /. Title
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("10");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("30");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("20");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("30");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("30");//NRO

			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getFont()->setBold(true);
			
			$fila = 5;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('A' . $fila, 'NRO')
			->setCellValue('B' . $fila, 'FOTO DEL PRODUCTO')
			->setCellValue('C' . $fila, 'NOMBRE COMERCIAL')
			->setCellValue('D' . $fila, 'CARACTERÍSTICAS')
			->setCellValue('E' . $fila, 'CANTIDAD')
			->setCellValue('F' . $fila, 'LINK')
			;
			
			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($style_align_center);
			
			$objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
			
			$fila = 6;
				
			$iCounter = 1;
            foreach($data as $row) {
				/*
				$objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'C' . $fila)->applyFromArray($style_align_center);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);
				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_right);
				$objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_center);
				$objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
				$objPHPExcel->getActiveSheet()->getStyle('O' . $fila)->applyFromArray($style_align_left);
				*/
				
				$html_data = array("&nbsp;");
				$row->Txt_Descripcion =str_replace($html_data," ",$row->Txt_Descripcion);
		
				$html_data = array("<br>", "<p>", "<br/>");
				$row->Txt_Descripcion =str_replace($html_data,"\n",$row->Txt_Descripcion);
		
				$row->Txt_Descripcion =strip_tags($row->Txt_Descripcion);

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('A' . $fila, $iCounter);

				if( !empty($row->Txt_Url_Imagen_Producto) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Sample image');
					$objDrawing->setDescription('Sample image');
					//$objDrawing->setPath('assets/img/arturo.jpeg');
					$objDrawing->setPath('./assets/images/productos/04be328a212d1b43ab42fa7565abddb5.jpeg');
					$objDrawing->setHeight(100);
					$objDrawing->setWidth(100);
					$objDrawing->setCoordinates('B' . $fila);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('B' . $fila, '');
				}

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('C' . $fila, $row->Txt_Producto)
				->setCellValue('D' . $fila, $row->Txt_Descripcion)
				->setCellValue('E' . $fila, $row->Qt_Producto)
				->setCellValue('F' . $fila, $row->Txt_Url_Link_Pagina_Producto)
				;

				/*
				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('N' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
				*/

				$iCounter++;
				$fila++;
			} // /. foreach data
				
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
}
