<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class StockxEmpresaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/informes_logistica/StockxEmpresaModel');
	}

	public function reporte(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/informes_logistica/StockxEmpresaView');
			$this->load->view('footer', array("js_stock_x_empresa" => true));
		}
	}
	
    private function getReporte($ID_Empresa, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia){
        if ($iTipoFecha == '0') {//tabla stock_producto
            $arrData = $this->StockxEmpresaModel->getStockValorizado($ID_Empresa, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia);
            $data = array();
            foreach ($arrData as $row) {
                $rows = array();
                $rows['ID_Familia'] = $row->ID_Familia;
                $rows['No_Familia'] = $row->No_Familia;
                $rows['ID_Producto'] = $row->ID_Producto;
                $rows['Nu_Codigo_Barra'] = $row->Nu_Codigo_Barra;
                $rows['No_Producto'] = $row->No_Producto;
                $rows['No_Unidad_Medida'] = $row->No_Unidad_Medida;
                $rows['Qt_Producto'] = $row->Qt_Producto;
                $rows['Ss_Precio'] = $row->Ss_Precio;
                $rows['Ss_Costo'] = $row->Ss_Costo;
                $rows['Ss_Costo_Promedio'] = $row->Ss_Costo_Promedio;
                $rows['Ss_Total_Promedio'] = round(($row->Qt_Producto * $row->Ss_Costo_Promedio), 2);
                if ($iTipoFecha == '0' && $iTipoStock == '0' && $rows['Qt_Producto'] > 0)//Mayor a cero
                    $data[] = (object)$rows;
                else if ($iTipoFecha == '0' && $iTipoStock == '1' && $rows['Qt_Producto'] < 0)//Negativo
                    $data[] = (object)$rows;
                else if ($iTipoFecha == '0' && $iTipoStock == '2' && $rows['Qt_Producto'] == 0)//solo stock Cero
                    $data[] = (object)$rows;
                else if ($iTipoFecha == '0' && $iTipoStock == '3')//todos
                    $data[] = (object)$rows;
            }
        } else {//tabla documento y guia
            $arrData = $this->StockxEmpresaModel->getStockValorizadoxFecha($ID_Empresa, $Fe_Inicio, $Fe_Fin, $ID_Familia, $ID_Producto, $iIdSubFamilia);
            $data = array();
            foreach ($arrData as $row) {
                $rows = array();
                $rows['ID_Familia'] = $row->ID_Familia;
                $rows['No_Familia'] = $row->No_Familia;
                $rows['ID_Producto'] = $row->ID_Producto;
                $rows['Nu_Codigo_Barra'] = $row->Nu_Codigo_Barra;
                $rows['No_Producto'] = $row->No_Producto;
                $rows['No_Unidad_Medida'] = $row->No_Unidad_Medida;
                $rows['Qt_Producto'] = $this->StockxEmpresaModel->getStockValorizadoxProducto($ID_Empresa, $Fe_Inicio, $Fe_Fin, $row->ID_Producto);
                $rows['Ss_Precio'] = $row->Ss_Precio;
                $rows['Ss_Costo'] = $row->Ss_Costo;
                $rows['Ss_Costo_Promedio'] = $row->Ss_Costo_Promedio;
                $rows['Ss_Total_Promedio'] = round(($row->Qt_Producto * $row->Ss_Costo_Promedio), 2);
                if ($iTipoFecha == '1' && $iTipoStock == '0' && $rows['Qt_Producto'] > 0)//Mayor a cero
                    $data[] = (object)$rows;
                else if ($iTipoFecha == '1' && $iTipoStock == '1' && $rows['Qt_Producto'] < 0)//Negativo
                    $data[] = (object)$rows;
                else if ($iTipoFecha == '1' && $iTipoStock == '2' && $rows['Qt_Producto'] == 0)//solo stock Cero
                    $data[] = (object)$rows;
                else if ($iTipoFecha == '1' && $iTipoStock == '3')//todos
                    $data[] = (object)$rows;
            }
        }
        return $data;
    }
    
	public function sendReporte(){
        echo json_encode(
            $this->getReporte(
                $this->user->ID_Empresa,
                $this->input->post('iTipoFecha'),
                $this->input->post('Fe_Inicio'),
                $this->input->post('Fe_Fin'),
                $this->input->post('iTipoStock'),
                $this->input->post('ID_Familia'),
                $this->input->post('ID_Producto'),
                $this->input->post('iIdSubFamilia')
            )
        );
    }
    
	public function sendReportePDF($iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia, $iActionEditar){
        $this->load->library('FormatoLibroSunatPDF');
		
        $ID_Empresa             = $this->user->ID_Empresa;
        $iTipoFecha             = $this->security->xss_clean($iTipoFecha);
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $iTipoStock             = $this->security->xss_clean($iTipoStock);
        $ID_Familia               = $this->security->xss_clean($ID_Familia);
        $ID_Producto            = $this->security->xss_clean($ID_Producto);
        $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
        $iActionEditar = $this->security->xss_clean($iActionEditar);
        
		$fileNamePDF = "Stock_Valorizado_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    	
        $arrCabecera = array (
            "Fe_Inicio" => ($iTipoFecha == '0' ? dateNow('fecha') : ToDateBD($Fe_Inicio)),
            "Fe_Fin" => ($iTipoFecha == '0' ? dateNow('fecha') : ToDateBD($Fe_Fin)),
            "iActionEditar" => $iActionEditar,
        );
        
		ob_start();
		$file = $this->load->view('Logistica/informes_logistica/pdf/StockxEmpresaPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($ID_Empresa, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia),
		));
		$html = ob_get_contents();
		ob_end_clean();

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 7);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia, $iActionEditar){
        $this->load->library('Excel');
	    
        $ID_Empresa             = $this->user->ID_Empresa;
        $iTipoFecha             = $this->security->xss_clean($iTipoFecha);
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $iTipoStock             = $this->security->xss_clean($iTipoStock);
        $ID_Familia               = $this->security->xss_clean($ID_Familia);
        $ID_Producto            = $this->security->xss_clean($ID_Producto);
        $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
        $iActionEditar = $this->security->xss_clean($iActionEditar);
            	
		$fileNameExcel = "Stock_Valorizado_Empresa_" . ($iTipoFecha == '0' ? dateNow('fecha') : ToDateBD($Fe_Inicio)) . "_" . ($iTipoFecha == '0' ? dateNow('fecha') : ToDateBD($Fe_Fin)) . ".xls";
		
        $data = $this->getReporte($ID_Empresa, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia);

	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Stock Valorizado x Empresa');
        
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
        ->setCellValue('B2', 'Stock Valorizado')
        ->setCellValue('B3', 'Desde: ' . ($iTipoFecha == '0' ? ToDateBD(dateNow('fecha')) : ToDateBD($Fe_Inicio)) . ' Hasta: ' . ($iTipoFecha == '0' ? ToDateBD(dateNow('fecha')) : ToDateBD($Fe_Fin)));
        
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B2:E2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B3:E3');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        
	    //Header
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");

        $objPHPExcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($BStyle_top);
        $objPHPExcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
        
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'Código')
        ->setCellValue('B5', 'Nombre')
        ->setCellValue('C5', 'Unidad Medida')
        ->setCellValue('D5', 'Stock')
        ->setCellValue('E5', 'Precio Venta')
        ->setCellValue('F5', 'Precio Compra')
        ->setCellValue('G5', 'Costo promedio')
        ->setCellValue('H5', 'Total')
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;
        
        if ( count($data) > 0) {
            $ID_Familia = '';
            $counter = 0;
            $sum_linea_cantidad = 0.000000;
            $sum_cantidad = 0.000000;
            $sum_linea_importe_promedio = 0.000000;
            $sum_importe_promedio = 0.000000;
            foreach ($data as $row) {
                if ($ID_Familia != $row->ID_Familia) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('C' . $fila, 'Total Categoría')
                        ->setCellValue('D' . $fila, numberFormat($sum_linea_cantidad, 6, '.', ','))
                        ->setCellValue('H' . $fila, numberFormat($sum_linea_importe_promedio, 6, '.', ','));
                        
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_right);
                        
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'H' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'E7E7E7')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'H' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                        
                        $sum_linea_cantidad = 0.000000;
                        $sum_linea_importe_promedio = 0.000000;
                    }
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Categoría')
                    ->setCellValue('B' . $fila, $row->No_Familia)
                    ;
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B' . $fila . ':' . 'F' . $fila);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'F' . $fila)->applyFromArray($style_align_left);
                    
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('A' . $fila . ':' . 'H' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F2F5F5')
                            )
                        )
                    );
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'H' . $fila)->getFont()->setBold(true);
                    
                    $ID_Familia = $row->ID_Familia;
                    $fila++;
                }
                
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $fila, $row->Nu_Codigo_Barra, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $fila, $row->No_Producto, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $fila, $row->No_Unidad_Medida, PHPExcel_Cell_DataType::TYPE_STRING);
                
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, numberFormat($row->Qt_Producto, 3, '.', ','))
                ->setCellValue('E' . $fila, numberFormat(((($this->empresa->ID_Empresa==73 && $iActionEditar == 1) || $this->empresa->ID_Empresa!=73) ? $row->Ss_Precio : ''), 2, '.', ','))
                ->setCellValue('F' . $fila, numberFormat(((($this->empresa->ID_Empresa==73 && $iActionEditar == 1) || $this->empresa->ID_Empresa!=73) ? $row->Ss_Costo : ''), 2, '.', ','))
                ->setCellValue('G' . $fila, numberFormat(((($this->empresa->ID_Empresa==73 && $iActionEditar == 1) || $this->empresa->ID_Empresa!=73) ? $row->Ss_Costo_Promedio : ''), 2, '.', ','))
                ->setCellValue('H' . $fila, numberFormat(((($this->empresa->ID_Empresa==73 && $iActionEditar == 1) || $this->empresa->ID_Empresa!=73) ? $row->Ss_Total_Promedio : ''), 2, '.', ','))
                ;

                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'B' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_right);
            
                $sum_linea_cantidad += $row->Qt_Producto;
                $sum_cantidad += $row->Qt_Producto;

                $sum_linea_importe_promedio += $row->Ss_Total_Promedio;
                $sum_importe_promedio += $row->Ss_Total_Promedio;

                $fila++;
                $counter++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('C' . $fila, 'Total Categoría')
            ->setCellValue('D' . $fila, numberFormat($sum_linea_cantidad, 3, '.', ','))
            ->setCellValue('H' . $fila, numberFormat($sum_linea_importe_promedio, 6, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_right);

            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'H' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'H' . $fila)->getFont()->setBold(true);
                       
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('C' . $fila, 'Total General')
            ->setCellValue('D' . $fila, numberFormat($sum_cantidad, 3, '.', ','))
            ->setCellValue('H' . $fila, numberFormat($sum_importe_promedio, 6, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_right);

            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'H' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'H' . $fila)->getFont()->setBold(true);
        }
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}
