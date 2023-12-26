<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HistorialPagos extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/HistorialPagosModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('AgenteCompra/HistorialPagosView');
			$this->load->view('footer_v2', array("js_historial_pagos" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->HistorialPagosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();
            
			$rows[] = $row->No_Pais;
            $rows[] = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);

			$rows[] = (!empty($row->Txt_Url_Pago_Garantizado) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_30_Cliente . '" src="' . $row->Txt_Url_Pago_Garantizado . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');

            $rows[] = $row->No_Pais_2;
            $rows[] = (!empty($row->Fe_Pago_30_Cliente) ? ToDateBD($row->Fe_Pago_30_Cliente) : '');
            $rows[] = $row->Ss_Pago_30_Cliente;
            $rows[] = $row->Nu_Operacion_Pago_30_Cliente;
            $rows[] = (!empty($row->Txt_Url_Pago_30_Cliente) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_30_Cliente . '" src="' . $row->Txt_Url_Pago_30_Cliente . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');
			
            $rows[] = $row->No_Pais_3;
            $rows[] = (!empty($row->Fe_Pago_100_Cliente) ? ToDateBD($row->Fe_Pago_100_Cliente) : '');
            $rows[] = $row->Ss_Pago_100_Cliente;
            $rows[] = $row->Nu_Operacion_Pago_100_Cliente;
            $rows[] = (!empty($row->Txt_Url_Pago_100_Cliente) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_100_Cliente . '" src="' . $row->Txt_Url_Pago_100_Cliente . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');
			
            $rows[] = $row->No_Pais_4;
            $rows[] = (!empty($row->Fe_Pago_Servicio_Cliente) ? ToDateBD($row->Fe_Pago_Servicio_Cliente) : '');
            $rows[] = $row->Ss_Pago_Servicio_Cliente;
            $rows[] = $row->Nu_Operacion_Pago_Servicio_Cliente;
            $rows[] = (!empty($row->Txt_Url_Pago_Servicio_Cliente) ? '<img class="img-fluid img-table_item" data-url_img="' . $row->Txt_Url_Pago_Servicio_Cliente . '" src="' . $row->Txt_Url_Pago_Servicio_Cliente . '" title="" alt="" style="cursor:pointer; max-height:100px;" />' : '');
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
			$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            $rows[] = $dropdown_estado;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($row->Nu_Estado_China);
			$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            $rows[] = $dropdown_estado;

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->HistorialPagosModel->count_all(),
	        'recordsFiltered' => $this->HistorialPagosModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->HistorialPagosModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCliente(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'				=> $this->user->ID_Empresa,
			'ID_Organizacion'			=> $this->user->ID_Organizacion,//Organizacion
			'No_Importacion_Grupal'		=> $this->input->post('No_Importacion_Grupal'),
			'Fe_Inicio'					=> ToDate($this->input->post('Fe_Inicio')),
			'Fe_Fin'					=> ToDate($this->input->post('Fe_Fin')),
			'ID_Moneda'					=> $this->input->post('ID_Moneda'),
			'Txt_Importacion_Grupal'	=> $this->input->post('Txt_Importacion_Grupal'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
			'No_Usuario' 				=> $this->user->No_Usuario
		);
		echo json_encode(
		$this->input->post('EID_Importacion_Grupal') != '' ?
			$this->HistorialPagosModel->actualizarCliente(array('ID_Importacion_Grupal' => $this->input->post('EID_Importacion_Grupal')), $data, $this->input->post('addProducto'))
		:
			$this->HistorialPagosModel->agregarCliente($data, $this->input->post('addProducto'))
		);
	}
    
	public function eliminarCliente($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->HistorialPagosModel->eliminarCliente($this->security->xss_clean($ID)));
	}
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin){
		$_POST['Filtro_Fe_Inicio'] = $Fe_Inicio;
		$_POST['Filtro_Fe_Fin'] = $Fe_Fin;
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);

        $this->load->library('Excel');
        
		$fileNameExcel = "historial_pagos_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Historial de Pagos');
        
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
        ->setCellValue('C2', 'Informe de Historial de Pagos')
        ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:H2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:H3');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");//PAIS 30%
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");//FECHA 30%
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");//IMPORTE 30%
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");//Operacion 30%
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");//PAIS 70%
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");//FECHA 70%
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("15");//IMPORTE 70%
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15");//Operacion 70%
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("15");//PAIS Servicio
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("15");//FECHA Servicio
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("15");//IMPORTE Servicio
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("15");//Operacion Servicio
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth("15");

        $iFila = 5;
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U' . $iFila)->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':U' . $iFila)->applyFromArray($BStyle_top);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':U' . $iFila)->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':U' . $iFila)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':U' . $iFila)->applyFromArray($style_align_center);
        
        $fila = 5;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, 'País')
        ->setCellValue('B' . $fila, 'ID')
        ->setCellValue('C' . $fila, 'F. Emisión')
        ->setCellValue('D' . $fila, 'Garantizado')
		
		->setCellValue('E' . $fila, 'Pais 30%')
		->setCellValue('F' . $fila, 'F. Pago 30%')
		->setCellValue('G' . $fila, 'Importe 30%')
		->setCellValue('H' . $fila, 'Operacion 30%')
		->setCellValue('I' . $fila, 'Voucher')

		->setCellValue('J' . $fila, 'Pais 70%')
		->setCellValue('K' . $fila, 'F. Pago 70%')
		->setCellValue('L' . $fila, 'Importe 70%')
		->setCellValue('M' . $fila, 'Operacion 70%')
		->setCellValue('N' . $fila, 'Voucher')
	
		->setCellValue('O' . $fila, 'Pais Servicio')
		->setCellValue('P' . $fila, 'F. Pago Servicio')
		->setCellValue('Q' . $fila, 'Importe Servicio')
		->setCellValue('R' . $fila, 'Operacion Servicio')
		->setCellValue('S' . $fila, 'Voucher')
		
		->setCellValue('T' . $fila, 'Perú')
		->setCellValue('U' . $fila, 'China')
        ;
        
        //$objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;

		$arrData = $this->HistorialPagosModel->get_datatables_excel();
        if ( !empty($arrData) ) {
			foreach ($arrData as $row) {
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('A' . $fila, $row->No_Pais)
				->setCellValue('B' . $fila, strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT))
				->setCellValue('C' . $fila, ToDateBD($row->Fe_Emision_Cotizacion))
				;

				if( !empty($row->Txt_Url_Pago_Garantizado) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();

					//pruebas localhost
					//$row->Txt_Url_Pago_Garantizado = 'assets/img/unicpn.png';

					//cloud
					$row->Txt_Url_Pago_Garantizado = str_replace("https://", "../../", $row->Txt_Url_Pago_Garantizado);
					$row->Txt_Url_Pago_Garantizado = str_replace("assets","public_html/assets", $row->Txt_Url_Pago_Garantizado);
					if ( file_exists($row->Txt_Url_Pago_Garantizado) ) {
						$objDrawing->setPath($row->Txt_Url_Pago_Garantizado);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('D' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('D' . $fila, '');
				}
				
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('E' . $fila, $row->No_Pais_2)
				->setCellValue('F' . $fila, (!empty($row->Fe_Pago_30_Cliente) ? ToDateBD($row->Fe_Pago_30_Cliente) : ''))
				->setCellValue('G' . $fila, $row->Ss_Pago_30_Cliente)
				->setCellValue('H' . $fila, $row->Nu_Operacion_Pago_30_Cliente)
				;
				
				if( !empty($row->Txt_Url_Pago_30_Cliente) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();

					//pruebas localhost
					//$row->Txt_Url_Pago_30_Cliente = 'assets/img/unicpn.png';

					//cloud
					$row->Txt_Url_Pago_30_Cliente = str_replace("https://", "../../", $row->Txt_Url_Pago_30_Cliente);
					$row->Txt_Url_Pago_30_Cliente = str_replace("assets","public_html/assets", $row->Txt_Url_Pago_30_Cliente);
					if ( file_exists($row->Txt_Url_Pago_30_Cliente) ) {
						$objDrawing->setPath($row->Txt_Url_Pago_30_Cliente);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('I' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('I' . $fila, '');
				}
				
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('J' . $fila, $row->No_Pais_3)
				->setCellValue('K' . $fila, (!empty($row->Fe_Pago_100_Cliente) ? ToDateBD($row->Fe_Pago_100_Cliente) : ''))
				->setCellValue('L' . $fila, $row->Ss_Pago_100_Cliente)
				->setCellValue('M' . $fila, $row->Nu_Operacion_Pago_100_Cliente)
				;
				
				if( !empty($row->Fe_Pago_100_Cliente) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();

					//pruebas localhost
					//$row->Fe_Pago_100_Cliente = 'assets/img/unicpn.png';

					//cloud
					$row->Fe_Pago_100_Cliente = str_replace("https://", "../../", $row->Fe_Pago_100_Cliente);
					$row->Fe_Pago_100_Cliente = str_replace("assets","public_html/assets", $row->Fe_Pago_100_Cliente);
					if ( file_exists($row->Fe_Pago_100_Cliente) ) {
						$objDrawing->setPath($row->Fe_Pago_100_Cliente);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('N' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('N' . $fila, '');
				}
				
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('O' . $fila, $row->No_Pais_4)
				->setCellValue('P' . $fila, (!empty($row->Fe_Pago_Servicio_Cliente) ? ToDateBD($row->Fe_Pago_Servicio_Cliente) : ''))
				->setCellValue('Q' . $fila, $row->Ss_Pago_Servicio_Cliente)
				->setCellValue('R' . $fila, $row->Nu_Operacion_Pago_Servicio_Cliente)
				;
				
				if( !empty($row->Fe_Pago_100_Cliente) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();

					//pruebas localhost
					//$row->Fe_Pago_100_Cliente = 'assets/img/unicpn.png';

					//cloud
					$row->Fe_Pago_100_Cliente = str_replace("https://", "../../", $row->Fe_Pago_100_Cliente);
					$row->Fe_Pago_100_Cliente = str_replace("assets","public_html/assets", $row->Fe_Pago_100_Cliente);
					if ( file_exists($row->Fe_Pago_100_Cliente) ) {
						$objDrawing->setPath($row->Fe_Pago_100_Cliente);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('S' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('S' . $fila, '');
				}

				$arrEstadoRegistroPeru = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
				$arrEstadoRegistroChina = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($row->Nu_Estado_China);
				
				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('T' . $fila, $arrEstadoRegistroPeru['No_Estado'])
				->setCellValue('U' . $fila, $arrEstadoRegistroChina['No_Estado']);

                $fila++;
			}
		} else {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('E' . $fila, 'No hay registros');

            $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_center);
        }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}
