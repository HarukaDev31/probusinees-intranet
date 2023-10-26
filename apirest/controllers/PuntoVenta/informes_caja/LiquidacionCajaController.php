<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class LiquidacionCajaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PuntoVenta/informes_caja/LiquidacionCajaModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('PuntoVenta/informes_caja/LiquidacionCajaView');
			$this->load->view('footer', array("js_liquidacion_caja" => true));
		}
	}	
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->LiquidacionCajaModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $fDiferencia = 0.00;
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['No_Organizacion'] = $row->No_Organizacion;
                $rows['No_Almacen'] = $row->No_Almacen;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['Fe_Apertura'] = allTypeDate($row->Fe_Apertura, '-', 0);
                $rows['Fe_Cierre'] = allTypeDate($row->Fe_Cierre, '-', 0);
                $rows['No_Signo'] = $row->No_Signo;
                $rows['Ss_Expectativa'] = $row->Ss_Expectativa;
                $rows['Ss_Total'] = $row->Ss_Total;
                $fDiferencia = ($row->Ss_Total - $row->Ss_Expectativa);
                $rows['Ss_Diferencia'] = round($fDiferencia, 2);
                $sNoDiferencia = '';
                if ( $fDiferencia > 0 )
                    $sNoDiferencia = 'success';
                else if ( $fDiferencia < 0 )
                    $sNoDiferencia = 'danger';
                $rows['No_Diferencia'] = $sNoDiferencia;
                $rows['Txt_Nota'] = $row->Txt_Nota;
                
 
                $arrParams = json_encode(array(
                    'sTipoCodificacion' => 'json',
                    'sAccion' => 'ver',
                    'iIdMatriculaEmpleado' => $row->ID_Matricula_Empleado,
                    'iIdEnlaceAperturaCaja' => $row->ID_Caja_Pos_Apertura,
                    'iIdEnlaceCierreCaja' => $row->ID_Caja_Pos_Cierre,
                ));
                $rows['sAccionVer'] = '<button class="btn btn-xs btn-link" alt="Ver liquidación" title="Ver liquidación" href="javascript:void(0)" onclick=formatoImpresionLiquidacionCaja(\''.$arrParams.'\')><i class="fa fa-2x fa-list-alt" aria-hidden="true"></i></button>';
                
                $arrParams = json_encode(array(
                    'sTipoCodificacion' => 'json',
                    'sAccion' => 'imprimir',
                    'iIdMatriculaEmpleado' => $row->ID_Matricula_Empleado,
                    'iIdEnlaceAperturaCaja' => $row->ID_Caja_Pos_Apertura,
                    'iIdEnlaceCierreCaja' => $row->ID_Caja_Pos_Cierre,
                ));
                $rows['sAccionImprimir'] = '<button class="btn btn-xs btn-link" alt="Imprimir liquidación" title="Imprimir liquidación" href="javascript:void(0)" onclick=formatoImpresionLiquidacionCaja(\'' . $arrParams . '\')><i class="fa fa-2x fa-print" aria-hidden="true"></i></button>';
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
            'ID_Almacen' => $this->input->post('ID_Almacen'),
            'Fe_Inicio' => $this->input->post('Fe_Inicio'),
            'Fe_Fin' => $this->input->post('Fe_Fin'),
            'iIdEmpleado' => $this->input->post('iIdEmpleado'),
            'sNombreEmpleado' => $this->input->post('sNombreEmpleado'),
            'ID_Organizacion' => $this->input->post('ID_Organizacion'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($ID_Almacen, $Fe_Inicio, $Fe_Fin, $iIdEmpleado, $sNombreEmpleado, $ID_Organizacion){
        $this->load->library('FormatoLibroSunatPDF');
		
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdEmpleado = $this->security->xss_clean($iIdEmpleado);
        $sNombreEmpleado = $this->security->xss_clean($sNombreEmpleado);
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        
		$fileNamePDF = "reporte_liquidacion_caja_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
        $pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
        );

        $arrParams = array(
            'ID_Almacen' => $ID_Almacen,
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'iIdEmpleado' => $iIdEmpleado,
            'sNombreEmpleado' => $sNombreEmpleado,
            'ID_Organizacion' => $ID_Organizacion,
        );
        
		ob_start();
		$file = $this->load->view('PuntoVenta/informes_caja/pdf/LiquidacionCajaViewPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('Laesystems - Reporte Liquidacion de Caja');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($ID_Almacen, $Fe_Inicio, $Fe_Fin, $iIdEmpleado, $sNombreEmpleado, $ID_Organizacion){
        $this->load->library('Excel');
		
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdEmpleado = $this->security->xss_clean($iIdEmpleado);
        $sNombreEmpleado = $this->security->xss_clean($sNombreEmpleado);
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        
		$fileNameExcel = "reporte_liquidacion_caja_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Reporte Liquidacion de Caja');
        
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
        ->setCellValue('C2', 'Informe de Liquidación de Caja')
        ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:H2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:H3');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("5");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("30");

        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($BStyle_top);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($BStyle_bottom);

        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($style_align_center);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'Organizacion')
        ->setCellValue('B5', 'Almacen')
        ->setCellValue('C5', 'Personal')
        ->setCellValue('D5', 'F. Apertura')
        ->setCellValue('E5', 'F. Cierre')
        ->setCellValue('F5', 'M')
        ->setCellValue('G5', 'Total a Liquidar')
        ->setCellValue('H5', 'Total Depositado')
        ->setCellValue('I5', 'Diferencia')
        ->setCellValue('J5', 'Nota')
        ;
        
        $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;

        $arrParams = array(
            'ID_Almacen' => $ID_Almacen,
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'iIdEmpleado' => $iIdEmpleado,
            'sNombreEmpleado' => $sNombreEmpleado,
            'ID_Organizacion' => $ID_Organizacion,
        );
        $arrData = $this->getReporte($arrParams);

        if ( $arrData['sStatus'] == 'success' ) {
            foreach($arrData['arrData'] as $row) {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'C' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'I' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($style_align_left);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->No_Organizacion)
                ->setCellValue('B' . $fila, $row->No_Almacen)
                ->setCellValue('C' . $fila, $row->No_Entidad)
                ->setCellValue('D' . $fila, $row->Fe_Apertura)
                ->setCellValue('E' . $fila, $row->Fe_Cierre)
                ->setCellValue('F' . $fila, $row->No_Signo)
                ->setCellValue('G' . $fila, numberFormat($row->Ss_Expectativa, 2, '.', ','))
                ->setCellValue('H' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                ->setCellValue('I' . $fila, numberFormat($row->Ss_Diferencia, 2, '.', ','))
                ->setCellValue('J' . $fila, $row->Txt_Nota)
                ;
                $fila++;
            }
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
