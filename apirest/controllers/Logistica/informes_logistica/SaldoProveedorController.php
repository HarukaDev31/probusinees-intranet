<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class SaldoProveedorController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/informes_logistica/SaldoProveedorModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/informes_logistica/SaldoProveedorView');
			$this->load->view('footer', array("js_saldo_proveedor" => true));
		}
	}	
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->SaldoProveedorModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            $sEstadoPago = '';
            $sEstadoPagoClass = '';
            $sAccionVer='ver';
            $sAccionImprimir='imprimir';
            $sVacio='';
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Almacen'] = $row->ID_Almacen;
                $rows['No_Almacen'] = $row->No_Almacen;
                //$rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
                $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
                $rows['Fe_Vencimiento'] = ToDateBD($row->Fe_Vencimiento);
                $dActual=date_create($row->Fe_Emision);
                $dEmision=date_create($row->Fe_Vencimiento);
                $iDiferenciaDias=date_diff($dActual,$dEmision);
                $rows['Dias_Vencimiento'] = '<span class="label label-danger">' . $iDiferenciaDias->format("%a") . ' día(s)</span>';
                $rows['Dias_Vencimiento_Excel'] = $iDiferenciaDias->format("%a") . ' día(s)';
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['No_Signo'] = $row->No_Signo;
                $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total : -$row->Ss_Total);
                $rows['Ss_Total_Saldo'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total_Saldo : -$row->Ss_Total_Saldo);
                $sEstadoPago = 'pendiente';
                $sEstadoPagoClass = 'warning';
                if ($row->Ss_Total_Saldo == 0.00) {
                    $sEstadoPago = 'cancelado';
                    $sEstadoPagoClass = 'success';
                }
                $rows['No_Estado_Pago'] = $sEstadoPago;
                $rows['No_Class_Estado_Pago'] = $sEstadoPagoClass;

                $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
                
                $data[] = (object)$rows;
            }
            return array(
                'sStatus' => 'success',
                'arrData' => $data,
            );
        } else {
            return $arrResponseModal;
        }
    }
    
	public function sendReporte(){
        $arrParams = array(
            'Fe_Inicio' => $this->input->post('Fe_Inicio'),
            'Fe_Fin' => $this->input->post('Fe_Fin'),
            'iIdTipoDocumento' => $this->input->post('iIdTipoDocumento'),
            'iIdSerieDocumento' => $this->input->post('iIdSerieDocumento'),
            'iNumeroDocumento' => $this->input->post('iNumeroDocumento'),
            'iEstadoPago' => $this->input->post('iEstadoPago'),
            'iIdProveedor' => $this->input->post('iIdProveedor'),
            'sNombreProveedor' => $this->input->post('sNombreProveedor'),
            'ID_Almacen' => $this->input->post('ID_Almacen'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($Fe_Inicio, $Fe_Fin, $iIdTipoDocumento, $iIdSerieDocumento, $iNumeroDocumento, $iEstadoPago, $iIdProveedor, $sNombreProveedor, $ID_Almacen){
        $this->load->library('FormatoLibroSunatPDF');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdTipoDocumento = $this->security->xss_clean($iIdTipoDocumento);
        $iIdSerieDocumento = $this->security->xss_clean($iIdSerieDocumento);
        $iNumeroDocumento = $this->security->xss_clean($iNumeroDocumento);
        $iEstadoPago = $this->security->xss_clean($iEstadoPago);
        $iIdProveedor = $this->security->xss_clean($iIdProveedor);
        $sNombreProveedor = $this->security->xss_clean($sNombreProveedor);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        
		$fileNamePDF = "reporte_saldo_proveedor_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
        );
        
        $arrParams = array(
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'iIdTipoDocumento' => $iIdTipoDocumento,
            'iIdSerieDocumento' => $iIdSerieDocumento,
            'iNumeroDocumento' => $iNumeroDocumento,
            'iEstadoPago' => $iEstadoPago,
            'iIdProveedor' => $iIdProveedor,
            'sNombreProveedor' => $sNombreProveedor,
            'ID_Almacen' => $ID_Almacen,
        );

		ob_start();
		$file = $this->load->view('Logistica/informes_logistica/pdf/SaldoProveedorViewPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('Laesystems - Informes de Saldo de Proveedor');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $iIdTipoDocumento, $iIdSerieDocumento, $iNumeroDocumento, $iEstadoPago, $iIdProveedor, $sNombreProveedor, $ID_Almacen){
        $this->load->library('Excel');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdTipoDocumento = $this->security->xss_clean($iIdTipoDocumento);
        $iIdSerieDocumento = $this->security->xss_clean($iIdSerieDocumento);
        $iNumeroDocumento = $this->security->xss_clean($iNumeroDocumento);
        $iEstadoPago = $this->security->xss_clean($iEstadoPago);
        $iIdProveedor = $this->security->xss_clean($iIdProveedor);
        $sNombreProveedor = $this->security->xss_clean($sNombreProveedor);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        
		$fileNameExcel = "reporte_saldo_proveedor_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Informe de Saldo de Proveedor');
        
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
        ->setCellValue('C2', 'Informe de Saldo de Proveedor')
        ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:H2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:H3');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");

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
        
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':L' . $iFila)->applyFromArray($BStyle_top);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $iFila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':L' . $iFila)->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':L' . $iFila)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $iFila . ':L' . $iFila)->applyFromArray($style_align_center);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'F. Emisión')
        ->setCellValue('B5', 'F. Vencimiento')
        ->setCellValue('C5', 'Vence')
        ->setCellValue('D5', 'Tipo')
        ->setCellValue('E5', 'Serie')
        ->setCellValue('F5', 'Número')
        ->setCellValue('G5', 'Proveedor')
        ->setCellValue('H5', 'M')
        ->setCellValue('I5', 'Total')
        ->setCellValue('J5', 'Total Saldo')
        ->setCellValue('K5', 'Estado Pago')
        ->setCellValue('L5', 'Estado')
        ;
        
        $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;

        $arrParams = array(
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'iIdTipoDocumento' => $iIdTipoDocumento,
            'iIdSerieDocumento' => $iIdSerieDocumento,
            'iNumeroDocumento' => $iNumeroDocumento,
            'iEstadoPago' => $iEstadoPago,
            'iIdProveedor' => $iIdProveedor,
            'sNombreProveedor' => $sNombreProveedor,
            'ID_Almacen' => $ID_Almacen,
        );
        $arrData = $this->getReporte($arrParams);
        if ( $arrData['sStatus'] == 'success' ) {
            $total_s = 0.00; $total_s_saldo = 0.00; $sum_total_s = 0.00; $sum_total_s_saldo = 0.00;
            $ID_Almacen = 0; $sum_almacen_total_s = 0.00; $sum_almacen_total_s_saldo = 0.00; $counter_almacen = 0;
            foreach($arrData['arrData'] as $row) {
                if ($ID_Almacen != $row->ID_Almacen) {
                    if ($counter_almacen != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('H' . $fila, 'Total Almacén')
                        ->setCellValue('I' . $fila, numberFormat($sum_almacen_total_s, 2, '.', ','))
                        ->setCellValue('J' . $fila, numberFormat($sum_almacen_total_s_saldo, 2, '.', ','));
                        
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'J' . $fila)->applyFromArray($style_align_right);
                                    
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'J' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'E7E7E7')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'J' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                        
                        $sum_almacen_total_s = 0.00;
                        $sum_almacen_total_s_saldo = 0.000000;
                    }

                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Almacén')
                    ->setCellValue('B' . $fila, $row->No_Almacen);

                    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':J'. $fila);
                    
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
                    
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('A' . $fila . ':' . 'Q' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F2F5F5')
                            )
                        )
                    );
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'J' . $fila)->getFont()->setBold(true);
                    
                    $ID_Almacen = $row->ID_Almacen;
                    $fila++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'F' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'J' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':' . 'L' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->Fe_Emision)
                ->setCellValue('B' . $fila, $row->Fe_Vencimiento)
                ->setCellValue('C' . $fila, $row->Dias_Vencimiento_Excel)
                ->setCellValue('D' . $fila, $row->No_Tipo_Documento_Breve)
                ->setCellValue('E' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('F' . $fila, $row->ID_Numero_Documento)
                ->setCellValue('G' . $fila, $row->No_Entidad)
                ->setCellValue('H' . $fila, $row->No_Signo)
                ->setCellValue('I' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                ->setCellValue('J' . $fila, numberFormat($row->Ss_Total_Saldo, 2, '.', ','))
                ->setCellValue('K' . $fila, $row->No_Estado_Pago)
                ->setCellValue('L' . $fila, $row->No_Estado)
                ;
                $fila++;

                $sum_total_s += $row->Ss_Total;
                $sum_total_s_saldo += $row->Ss_Total_Saldo;

                $sum_almacen_total_s += $row->Ss_Total;
                $sum_almacen_total_s_saldo += $row->Ss_Total_Saldo;

                $counter_almacen++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Total Almacén')
            ->setCellValue('I' . $fila, numberFormat($sum_almacen_total_s, 2, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_almacen_total_s_saldo, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'J' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'J' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'J' . $fila)->getFont()->setBold(true);
            
            ++$fila;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Total')
            ->setCellValue('I' . $fila, numberFormat($sum_total_s, 2, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_total_s_saldo, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'J' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'J' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'J' . $fila)->getFont()->setBold(true);
        } else {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('E' . $fila, $arrData['sMessage']);

            $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_center);
        }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}
