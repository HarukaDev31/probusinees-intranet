<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VentasTiposDocumentoSunatController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/informes_venta/VentasTiposDocumentoSunatModel');
		$this->load->model('HelperModel');
	}

	public function reporte(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/informes_venta/VentasTiposDocumentoSunatView');
			$this->load->view('footer', array("js_venta_x_tipo_documento_sunat" => true));
		}
	}
	
    private function getReporte($ID_Empresa, $Fe_Inicio, $Fe_Fin, $iDocumentStatus, $ID_Almacen, $Nu_Tipo_Impuesto){
        $arrData = $this->VentasTiposDocumentoSunatModel->getReporte($ID_Empresa, $Fe_Inicio, $Fe_Fin, $iDocumentStatus, $ID_Almacen, $Nu_Tipo_Impuesto);
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();
            $rows['ID_Almacen'] = $row->ID_Almacen;
            $rows['No_Almacen'] = $row->No_Almacen;
            $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
            $rows['Nu_Cantidad_Trans_BOL'] = $row->Nu_Cantidad_Trans_BOL;
            $rows['Ss_Total_BOL'] = $row->Ss_Total_BOL;
            $rows['Nu_Cantidad_Trans_FACT'] = $row->Nu_Cantidad_Trans_FACT;
            $rows['Ss_Total_FACT'] = $row->Ss_Total_FACT;
            $rows['Nu_Cantidad_Trans_NC'] = $row->Nu_Cantidad_Trans_NC;
            $rows['Ss_Total_NC'] = $row->Ss_Total_NC;
            $rows['Nu_Cantidad_Trans_ND'] = $row->Nu_Cantidad_Trans_ND;
            $rows['Ss_Total_ND'] = $row->Ss_Total_ND;
            $data[] = (object)$rows;
        }
        return $data;
    }
    
	public function sendReporte(){
        echo json_encode(
            $this->getReporte(
                $this->user->ID_Empresa,
                $this->input->post('Fe_Inicio'),
                $this->input->post('Fe_Fin'),
                $this->input->post('iDocumentStatus'),
                $this->input->post('ID_Almacen'),
                $this->input->post('Nu_Tipo_Impuesto')
            )
        );
    }
    
	public function sendReportePDF($Fe_Inicio, $Fe_Fin, $iDocumentStatus, $ID_Almacen, $Nu_Tipo_Impuesto){
        $this->load->library('FormatoLibroSunatPDF');
		
        $ID_Empresa         = $this->user->ID_Empresa;
        $Fe_Inicio          = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin             = $this->security->xss_clean($Fe_Fin);
        $iDocumentStatus    = $this->security->xss_clean($iDocumentStatus);
        $ID_Almacen    = $this->security->xss_clean($ID_Almacen);
        $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
        
		$fileNamePDF = "Reporte_Tipo_Documento_Sunat_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
        );
        
		ob_start();
		$file = $this->load->view('Ventas/informes_venta/pdf/VentasTiposDocumentoSunatPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($ID_Empresa, $Fe_Inicio, $Fe_Fin, $iDocumentStatus, $ID_Almacen, $Nu_Tipo_Impuesto),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $iDocumentStatus, $ID_Almacen, $Nu_Tipo_Impuesto){
        $this->load->library('Excel');
	    
        $ID_Empresa         = $this->user->ID_Empresa;
        $Fe_Inicio          = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin             = $this->security->xss_clean($Fe_Fin);
        $iDocumentStatus    = $this->security->xss_clean($iDocumentStatus);
        $ID_Almacen    = $this->security->xss_clean($ID_Almacen);
        $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
        
		$fileNameExcel = "Reporte_Tipo_Documento_Sunat_" . ToDateBD($Fe_Inicio) . "_" . ToDateBD($Fe_Fin) . ".xls";
		
        $data = $this->getReporte($ID_Empresa, $Fe_Inicio, $Fe_Fin, $iDocumentStatus, $ID_Almacen, $Nu_Tipo_Impuesto);

	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Reporte por Tipos de Documento');
        
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
        ->setCellValue('D2', 'Reporte por Tipos de Documento')
        ->setCellValue('D3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('D2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D2:F2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D3:F3');
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        
	    //Header
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'Fecha')
        ->setCellValue('B5', 'Boleta');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B5:C5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('D5', 'Factura');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D5:E5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('F5', 'N/Crédito');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F5:G5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('H5', 'N/Débito');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('H5:I5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('J5', 'N/Débito');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('J5:K5');
        

        $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray($BStyle_top);
        $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($style_align_center);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A6', 'Emisión')
        ->setCellValue('B6', 'Trans.')
        ->setCellValue('C6', 'Importe')
        ->setCellValue('D6', 'Trans.')
        ->setCellValue('E6', 'Importe')
        ->setCellValue('F6', 'Trans.')
        ->setCellValue('G6', 'Importe')
        ->setCellValue('H6', 'Trans.')
        ->setCellValue('I6', 'Importe')
        ->setCellValue('J6', 'Trans.')
        ->setCellValue('K6', 'Importe')
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:K6')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:K6')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K6')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->freezePane('A7');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 7;
        
        if ( count($data) > 0) {
            $sum_cantidad_trans_b = 0;
            $sum_total_b = 0;
            $sum_cantidad_trans_f = 0;
            $sum_total_f = 0;
            $sum_cantidad_trans_nc = 0;
            $sum_total_nc = 0;
            $sum_cantidad_trans_nd = 0;
            $sum_total_nd = 0;
            $ID_Almacen = 0; $counter_almacen = 0; $sum_cantidad_trans_b_almacen = 0; $sum_total_b_almacen = 0.00; $sum_cantidad_trans_f_almacen = 0; $sum_total_f_almacen = 0.00; $sum_cantidad_trans_nc_almacen = 0; $sum_total_nc_almacen = 0.00; $sum_cantidad_trans_nd_almacen = 0; $sum_total_nd_almacen = 0.00;
            foreach ($data as $row) {
                if ($ID_Almacen != $row->ID_Almacen) {
                    if ($counter_almacen != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'Total Almacém')
                        ->setCellValue('B' . $fila, $sum_cantidad_trans_b)
                        ->setCellValue('C' . $fila, numberFormat($sum_total_b, 2, '.', ','))
                        ->setCellValue('D' . $fila, $sum_cantidad_trans_f)
                        ->setCellValue('E' . $fila, numberFormat($sum_total_f, 2, '.', ','))
                        ->setCellValue('F' . $fila, $sum_cantidad_trans_nc)
                        ->setCellValue('G' . $fila, numberFormat($sum_total_nc, 2, '.', ','))
                        ->setCellValue('H' . $fila, $sum_cantidad_trans_nd)
                        ->setCellValue('I' . $fila, numberFormat($sum_total_nd, 2, '.', ','))
                        ->setCellValue('J' . $fila, ($sum_cantidad_trans_b + $sum_cantidad_trans_f + $sum_cantidad_trans_nc + $sum_cantidad_trans_nd))
                        ->setCellValue('K' . $fila, numberFormat($sum_total_b + $sum_total_f - $sum_total_nc + $sum_total_nd, 2, '.', ','))
                        ;
                        
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_right);
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);

                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'K' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'E7E7E7')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
                        
                        $fila++;

                        $sum_cantidad_trans_b_almacen = 0;
                        $sum_total_b_almacen = 0;
                        $sum_cantidad_trans_f_almacen = 0;
                        $sum_total_f_almacen = 0;
                        $sum_cantidad_trans_nc_almacen = 0;
                        $sum_total_nc_almacen = 0;
                        $sum_cantidad_trans_nd_almacen = 0;
                        $sum_total_nd_almacen = 0;
                    }

                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Almacén')
                    ->setCellValue('B' . $fila, $row->No_Almacen);

                    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':K'. $fila);
                    
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
                    
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('A' . $fila . ':' . 'K' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F2F5F5')
                            )
                        )
                    );
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
                    
                    $ID_Almacen = $row->ID_Almacen;
                    $fila++;
                }

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->Fe_Emision)
                ->setCellValue('B' . $fila, $row->Nu_Cantidad_Trans_BOL)
                ->setCellValue('C' . $fila, numberFormat($row->Ss_Total_BOL, 2, '.', ','))
                ->setCellValue('D' . $fila, $row->Nu_Cantidad_Trans_FACT)
                ->setCellValue('E' . $fila, numberFormat($row->Ss_Total_FACT, 2, '.', ','))
                ->setCellValue('F' . $fila, $row->Nu_Cantidad_Trans_NC)
                ->setCellValue('G' . $fila, numberFormat($row->Ss_Total_NC, 2, '.', ','))
                ->setCellValue('H' . $fila, $row->Nu_Cantidad_Trans_ND)
                ->setCellValue('I' . $fila, numberFormat($row->Ss_Total_ND, 2, '.', ','))
                ->setCellValue('J' . $fila, $row->Nu_Cantidad_Trans_BOL + $row->Nu_Cantidad_Trans_FACT + $row->Nu_Cantidad_Trans_NC + $row->Nu_Cantidad_Trans_ND)
                ->setCellValue('K' . $fila, numberFormat($row->Ss_Total_BOL + $row->Ss_Total_FACT - $row->Ss_Total_NC + $row->Ss_Total_ND, 2, '.', ','))
                ;
                
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                
                if (!empty($row->Nu_Cantidad_Trans_BOL))
                    $sum_cantidad_trans_b += $row->Nu_Cantidad_Trans_BOL;
                if (!empty($row->Ss_Total_BOL))
                    $sum_total_b += $row->Ss_Total_BOL;
                if (!empty($row->Nu_Cantidad_Trans_FACT))
                    $sum_cantidad_trans_f += $row->Nu_Cantidad_Trans_FACT;
                if (!empty($row->Ss_Total_FACT))
                    $sum_total_f += $row->Ss_Total_FACT;
                if (!empty($row->Nu_Cantidad_Trans_NC))
                    $sum_cantidad_trans_nc += $row->Nu_Cantidad_Trans_NC;
                if (!empty($row->Ss_Total_NC))
                    $sum_total_nc += $row->Ss_Total_NC;
                if (!empty($row->Nu_Cantidad_Trans_ND))
                    $sum_cantidad_trans_nd += $row->Nu_Cantidad_Trans_ND;
                if (!empty($row->Ss_Total_ND))
                    $sum_total_nd += $row->Ss_Total_ND;
                    
                if (!empty($row->Nu_Cantidad_Trans_BOL))
                    $sum_cantidad_trans_b_almacen += $row->Nu_Cantidad_Trans_BOL;
                if (!empty($row->Ss_Total_BOL))
                    $sum_total_b_almacen += $row->Ss_Total_BOL;
                if (!empty($row->Nu_Cantidad_Trans_FACT))
                    $sum_cantidad_trans_f_almacen += $row->Nu_Cantidad_Trans_FACT;
                if (!empty($row->Ss_Total_FACT))
                    $sum_total_f_almacen += $row->Ss_Total_FACT;
                if (!empty($row->Nu_Cantidad_Trans_NC))
                    $sum_cantidad_trans_nc_almacen += $row->Nu_Cantidad_Trans_NC;
                if (!empty($row->Ss_Total_NC))
                    $sum_total_nc_almacen += $row->Ss_Total_NC;
                if (!empty($row->Nu_Cantidad_Trans_ND))
                    $sum_cantidad_trans_nd_almacen += $row->Nu_Cantidad_Trans_ND;
                if (!empty($row->Ss_Total_ND))
                    $sum_total_nd_almacen += $row->Ss_Total_ND;

                $counter_almacen++;
                $fila++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('A' . $fila, 'Total Almacém')
            ->setCellValue('B' . $fila, $sum_cantidad_trans_b_almacen)
            ->setCellValue('C' . $fila, numberFormat($sum_total_b_almacen, 2, '.', ','))
            ->setCellValue('D' . $fila, $sum_cantidad_trans_f_almacen)
            ->setCellValue('E' . $fila, numberFormat($sum_total_f_almacen, 2, '.', ','))
            ->setCellValue('F' . $fila, $sum_cantidad_trans_nc_almacen)
            ->setCellValue('G' . $fila, numberFormat($sum_total_nc_almacen, 2, '.', ','))
            ->setCellValue('H' . $fila, $sum_cantidad_trans_nd_almacen)
            ->setCellValue('I' . $fila, numberFormat($sum_total_nd_almacen, 2, '.', ','))
            ->setCellValue('J' . $fila, ($sum_cantidad_trans_b_almacen + $sum_cantidad_trans_f_almacen + $sum_cantidad_trans_nc_almacen + $sum_cantidad_trans_nd))
            ->setCellValue('K' . $fila, numberFormat($sum_total_b_almacen + $sum_total_f_almacen - $sum_total_nc_almacen + $sum_total_nd_almacen, 2, '.', ','))
            ;
            
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_right);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);

            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'K' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('A' . $fila, 'Total')
            ->setCellValue('B' . $fila, $sum_cantidad_trans_b)
            ->setCellValue('C' . $fila, numberFormat($sum_total_b, 2, '.', ','))
            ->setCellValue('D' . $fila, $sum_cantidad_trans_f)
            ->setCellValue('E' . $fila, numberFormat($sum_total_f, 2, '.', ','))
            ->setCellValue('F' . $fila, $sum_cantidad_trans_nc)
            ->setCellValue('G' . $fila, numberFormat($sum_total_nc, 2, '.', ','))
            ->setCellValue('H' . $fila, $sum_cantidad_trans_nd)
            ->setCellValue('I' . $fila, numberFormat($sum_total_nd, 2, '.', ','))
            ->setCellValue('J' . $fila, ($sum_cantidad_trans_b + $sum_cantidad_trans_f + $sum_cantidad_trans_nc + $sum_cantidad_trans_nd))
            ->setCellValue('K' . $fila, numberFormat($sum_total_b + $sum_total_f - $sum_total_nc + $sum_total_nd, 2, '.', ','))
            ;
            
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_right);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);

            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'K' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
        }
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}
