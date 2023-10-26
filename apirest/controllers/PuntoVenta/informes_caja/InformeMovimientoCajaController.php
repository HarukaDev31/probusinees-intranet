<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class InformeMovimientoCajaController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PuntoVenta/informes_caja/MovimientoCajaModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('PuntoVenta/informes_caja/MovimientoCajaView');
			$this->load->view('footer', array("js_informe_movimiento_caja" => true));
		}
	}	
	
  private function getReporte($arrParams){
    $arrResponseModal = $this->MovimientoCajaModel->getReporte($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
      
      $fDiferencia = 0.00;
      foreach ($arrResponseModal['arrData'] as $row) {
        $rows['No_Almacen'] = $row->No_Almacen;
        $rows['ID_Empleado'] = $row->ID_Empleado;
        $rows['No_Empleado'] = $row->No_Empleado;
        $rows['No_Tipo_Operacion_Caja'] = $row->No_Tipo_Operacion_Caja;
        $rows['Fe_Movimiento'] = allTypeDate($row->Fe_Movimiento, '-', 0);
        $rows['No_Signo'] = $row->No_Signo;
        $rows['Ss_Total'] = $row->Ss_Total;
        $rows['Txt_Nota'] = $row->Txt_Nota;
        $rows['Nu_Tipo'] = $row->Nu_Tipo;

        $rows['sImpresion'] = (($row->Nu_Tipo == 5 || $row->Nu_Tipo==6) ? '<button type="button" class="btn btn-xs btn-link" alt="Imprimir" title="Imprimir" href="javascript:void(0)" onclick="imprimirMovimientoCaja(\'' . $row->ID_Caja_Pos . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>' : '');
        $sClassEstadoSpan='success';
        if ( $row->Nu_Tipo == 4 || $row->Nu_Tipo == 6 )
          $sClassEstadoSpan='danger';
        $rows['No_Class_Estado'] = $sClassEstadoSpan;
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
      'ID_Tipo_Operacion_Caja' => $this->input->post('ID_Tipo_Operacion_Caja'),
      'iIdEmpleado' => $this->input->post('iIdEmpleado'),
      'sNombreEmpleado' => $this->input->post('sNombreEmpleado'),
    );
    echo json_encode($this->getReporte($arrParams));
  }
    
	public function sendReportePDF($ID_Almacen, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Operacion_Caja, $iIdEmpleado, $sNombreEmpleado){
    $this->load->library('FormatoLibroSunatPDF');

    $ID_Almacen = $this->security->xss_clean($ID_Almacen);
    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Operacion_Caja = $this->security->xss_clean($ID_Tipo_Operacion_Caja);
    $iIdEmpleado = $this->security->xss_clean($iIdEmpleado);
    $sNombreEmpleado = $this->security->xss_clean($sNombreEmpleado);
        
		$fileNamePDF = "Informe_Movimiento_Caja_PV_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $arrCabecera = array (
      "Fe_Inicio" => ToDateBD($Fe_Inicio),
      "Fe_Fin" => ToDateBD($Fe_Fin),
    );
    
    $arrParams = array(
      'ID_Almacen' => $ID_Almacen,
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Operacion_Caja' => $ID_Tipo_Operacion_Caja,
      'iIdEmpleado' => $iIdEmpleado,
      'sNombreEmpleado' => $sNombreEmpleado,
    );
  
		ob_start();
		$file = $this->load->view('PuntoVenta/informes_caja/pdf/MovimientoCajaViewPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('LAE');
		$pdf->SetTitle('LAE - Informe Movimiento Caja PV');
	
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    
    $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($ID_Almacen, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Operacion_Caja, $iIdEmpleado, $sNombreEmpleado){
    $this->load->library('Excel');

    $ID_Almacen = $this->security->xss_clean($ID_Almacen);
    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Operacion_Caja = $this->security->xss_clean($ID_Tipo_Operacion_Caja);
    $iIdEmpleado = $this->security->xss_clean($iIdEmpleado);
    $sNombreEmpleado = $this->security->xss_clean($sNombreEmpleado);
        
    $fileNameExcel = "Informe_Movimiento_Caja_PV_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";

    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Movimiento Caja PV');
      
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
    ->setCellValue('B2', 'Informe de Ingresos y Egresos de Caja POS')
    ->setCellValue('B3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B2:D2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B3:D3');
    $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("25");
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");

    $objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($BStyle_top);
    $objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($BStyle_bottom);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getFont()->setBold(true);

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Almacen')
    ->setCellValue('B5', 'Tipo Operación')
    ->setCellValue('C5', 'Fe. Movimiento')
    ->setCellValue('D5', 'Moneda')
    ->setCellValue('E5', 'Total')
    ->setCellValue('F5', 'Nota')    
    ;

    $objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($style_align_center);
    
    $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
    
    $fila = 6;
    
    $arrParams = array(
      'ID_Almacen' => $ID_Almacen,
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Operacion_Caja' => $ID_Tipo_Operacion_Caja,
      'iIdEmpleado' => $iIdEmpleado,
      'sNombreEmpleado' => $sNombreEmpleado,
    );
    $arrData = $this->getReporte($arrParams);

    if ( $arrData['sStatus'] == 'success' ) {
      $iCounter = 0; $ID_Empleado = ''; $fTotal = 0.00; $fTotalIngresos = 0.00; $fTotalEgresos = 0.00; $fSumGeneralTotalIngresos = 0.00; $fSumGeneralTotalEgresos = 0.00;
      foreach($arrData['arrData'] as $row) {
        if ($ID_Empleado != $row->ID_Empleado) {
          if ($iCounter != 0) {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('C' . $fila, 'Total Ingresos')
            ->setCellValue('D' . $fila, numberFormat($fTotalIngresos, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);
            
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'D' . $fila)
            ->applyFromArray(
              array(
                'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => array('rgb' => 'E7E7E7')
                )
              )
            );
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
            $fila++;
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('C' . $fila, 'Total Ingresos')
            ->setCellValue('D' . $fila, numberFormat($fTotalEgresos, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);
            
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'D' . $fila)
            ->applyFromArray(
              array(
                'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => array('rgb' => 'E7E7E7')
                )
              )
            );
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
            $fila++;
            
            $fTotalIngresos = 0.00;
            $fTotalEgresos = 0.00;
          }
          
          $objPHPExcel->setActiveSheetIndex($hoja_activa)
          ->setCellValue('A' . $fila, 'Personal')
          ->setCellValue('B' . $fila, $row->No_Empleado)
          ;
          
          $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
          $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
          
          $objPHPExcel->getActiveSheet()
          ->getStyle('A' . $fila . ':' . 'E' . $fila)
          ->applyFromArray(
            array(
              'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F2F5F5')
              )
            )
          );
          $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'B' . $fila)->getFont()->setBold(true);
          
          $ID_Empleado = $row->ID_Empleado;
          $fila++;
        }
        
        if($row->Nu_Tipo == '5') {//Ingresos
          $fTotalIngresos += (!empty($row->Ss_Total) ? $row->Ss_Total : 0.00);
          $fSumGeneralTotalIngresos += $row->Ss_Total;
        } else {
          $fTotalEgresos += (!empty($row->Ss_Total) ? $row->Ss_Total : 0.00);
          $fSumGeneralTotalEgresos += $row->Ss_Total;
        }
                
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->No_Almacen)
        ->setCellValue('B' . $fila, $row->No_Tipo_Operacion_Caja)
        ->setCellValue('C' . $fila, $row->Fe_Movimiento)
        ->setCellValue('D' . $fila, $row->No_Signo)
        ->setCellValue('E' . $fila, numberFormat((!empty($row->Ss_Total) ? $row->Ss_Total : 0.00), 2, '.', ','))
        ->setCellValue('F' . $fila, $row->Txt_Nota)
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
        
        $fila++;
        $iCounter++;
      }
      //Totales
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('C' . $fila, 'Total Ingresos')
      ->setCellValue('D' . $fila, numberFormat($fTotalIngresos, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'E' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->getFont()->setBold(true);
      
      $fila++;
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('C' . $fila, 'Total Egresos')
      ->setCellValue('D' . $fila, numberFormat($fTotalEgresos, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'E' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->getFont()->setBold(true);
      
      $fila++;
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('C' . $fila, 'Total General Ingresos')
      ->setCellValue('D' . $fila, numberFormat($fSumGeneralTotalIngresos, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'E' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->getFont()->setBold(true);
      
      $fila++;
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('C' . $fila, 'Total General Egresos')
      ->setCellValue('D' . $fila, numberFormat($fSumGeneralTotalEgresos, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'E' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->getFont()->setBold(true);
    } else {

    }// /. if - else arrData

    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
  }
}
