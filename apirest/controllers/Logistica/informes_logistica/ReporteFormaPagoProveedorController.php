<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class ReporteFormaPagoProveedorController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/informes_logistica/ReporteFormaPagoProveedorModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/informes_logistica/ReporteFormaPagoProveedorView');
			$this->load->view('footer', array("js_reporte_forma_pago_proveedor" => true));
		}
	}	
	
  private function getReporte($arrParams){
    $arrResponseModal = $this->ReporteFormaPagoProveedorModel->getReporte($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
      
      $fSubTotal = 0.00;
      $fIGV = 0.00;
      $fDescuento = 0.00;
      $fTotal = 0.00;
      $sAccionVer='ver';
      $sAccionImprimir='imprimir';
      $sVacio='';
      
      foreach ($arrResponseModal['arrData'] as $row) {
        $rows = array();
        $rows['ID_Almacen'] = $row->ID_Almacen;
        $rows['No_Almacen'] = $row->No_Almacen;

        $dEmision = allTypeDate($row->Fe_Emision_Hora, '-', 0);
        $rows['Fe_Emision_Hora'] = $dEmision;
        $rows['Fe_Emision_Hora_Pago'] = (!empty($row->Fe_Emision_Hora_Pago) ? allTypeDate($row->Fe_Emision_Hora_Pago, '-', 0) : $dEmision);
        $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
        $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
        $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
        $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
        
        $rows['No_Tipo_Documento_Identidad_Breve'] = $row->No_Tipo_Documento_Identidad_Breve;
        $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
        $rows['No_Entidad'] = $row->No_Entidad;

        $rows['No_Signo'] = $row->No_Signo;
        $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar);

        $rows['No_Medio_Pago'] = $row->No_Medio_Pago;
        $rows['No_Tipo_Medio_Pago'] = !empty($row->No_Tipo_Medio_Pago) ? $row->No_Tipo_Medio_Pago : '';
        $rows['Nu_Tarjeta'] = !empty($row->Nu_Tarjeta) ? $row->Nu_Tarjeta : '';
        $rows['Nu_Transaccion'] = !empty($row->Nu_Transaccion) ? $row->Nu_Transaccion : '';
        $fTotal = ($row->Ss_Total != 0.00 ? $row->Ss_Total : $row->Ss_Total_Cabecera);
        $fTotal = ($row->Nu_Tipo_Caja == 0 ? ($fTotal - $row->Ss_Vuelto) : $fTotal);
        $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $fTotal : -$fTotal);

        $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
        $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
        $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
        $rows['Nu_Estado'] = $row->Nu_Estado;
        //$rows['sAccionVer'] = '<button class="btn btn-xs btn-link" alt="Ver comprobante" title="Ver comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionVer . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-list-alt" aria-hidden="true"> Ver</i></button>';
        //$rows['sAccionImprimir'] = '<button class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-print" aria-hidden="true"> Imprimir</i></button>';
			  if ( $this->MenuModel->verificarAccesoMenuInterno($arrParams['sMethod'])->Nu_Eliminar == 1) {
          $rows['btn_eliminar'] = '<button type="button" class="btn btn-xs btn-link" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick=eliminarFormaPago(\'' . $row->ID_Documento_Medio_Pago . '\')><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></button>';
        } else {
          $rows['btn_eliminar'] = '';
        }

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
    
	public function eliminarFormaPago($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ReporteFormaPagoProveedorModel->eliminarFormaPago($this->security->xss_clean($ID)));
	}
    
	public function sendReporte(){
    $arrParams = array(
      'sMethod' => $this->input->post('sMethod'),
      'Fe_Inicio' => $this->input->post('Fe_Inicio'),
      'Fe_Fin' => $this->input->post('Fe_Fin'),
      'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
      'ID_Serie_Documento' => $this->input->post('ID_Serie_Documento'),
      'ID_Numero_Documento' => $this->input->post('ID_Numero_Documento'),
      'Nu_Estado_Documento' => $this->input->post('Nu_Estado_Documento'),
      'iIdProveedor' => $this->input->post('iIdProveedor'),
      'sNombreProveedor' => $this->input->post('sNombreProveedor'),
      'iIdPersonal' => $this->input->post('iIdPersonal'),
      'sNombrePersonal' => $this->input->post('sNombrePersonal'),
      'iTipoVenta' => $this->input->post('iTipoVenta'),
      'iMedioPago' => $this->input->post('iMedioPago'),
      'iTipoTarjeta' => $this->input->post('iTipoTarjeta'),
      'ID_Almacen' => $this->input->post('ID_Almacen'),
    );
    echo json_encode($this->getReporte($arrParams));
  }
    
  public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdProveedor, $sNombreProveedor, $iTipoVenta, $iMedioPago, $iIdPersonal, $sNombrePersonal, $iTipoTarjeta, $ID_Almacen){
    $this->load->library('FormatoLibroSunatPDF');

    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
    $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
    $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
    $iIdProveedor = $this->security->xss_clean($iIdProveedor);
    $sNombreProveedor = $this->security->xss_clean($sNombreProveedor);
    $iTipoVenta = $this->security->xss_clean($iTipoVenta);
    $iMedioPago = $this->security->xss_clean($iMedioPago);
    $iIdPersonal = $this->security->xss_clean($iIdPersonal);
    $sNombrePersonal = $this->security->xss_clean($sNombrePersonal);
    $iTipoTarjeta = $this->security->xss_clean($iTipoTarjeta);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);

    $fileNamePDF = "reporte_forma_pago_proveedor_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

    $pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $arrCabecera = array (
      "Fe_Inicio" => ToDateBD($Fe_Inicio),
      "Fe_Fin" => ToDateBD($Fe_Fin),
    );

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Documento' => $ID_Tipo_Documento,
      'ID_Serie_Documento' => $ID_Serie_Documento,
      'ID_Numero_Documento' => $ID_Numero_Documento,
      'Nu_Estado_Documento' => $Nu_Estado_Documento,
      'iIdProveedor' => $iIdProveedor,
      'sNombreProveedor' => $sNombreProveedor,
      'iTipoVenta' => $iTipoVenta,
      'iMedioPago' => $iMedioPago,
      'iIdPersonal' => $iIdPersonal,
      'sNombrePersonal' => $sNombrePersonal,
      'iTipoTarjeta' => $iTipoTarjeta,
      'ID_Almacen' => $ID_Almacen,
    );

    ob_start();
    $file = $this->load->view('Logistica/informes_logistica/pdf/ReporteFormaPagoProveedorViewPDF', array(
      'arrCabecera' => $arrCabecera,
      'arrDetalle' => $this->getReporte($arrParams),
    ));
    $html = ob_get_contents();
    ob_end_clean();

    $pdf->SetAuthor('Laesystems');
    $pdf->SetTitle('Laesystems - Reporte Forma Pago');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->setFont('helvetica', '', 4.5);
    
    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdProveedor, $sNombreProveedor, $iTipoVenta, $iMedioPago, $iIdPersonal, $sNombrePersonal, $iTipoTarjeta, $ID_Almacen){
    $this->load->library('Excel');
	  
    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
    $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
    $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
    $iIdProveedor = $this->security->xss_clean($iIdProveedor);
    $sNombreProveedor = $this->security->xss_clean($sNombreProveedor);
    $iTipoVenta = $this->security->xss_clean($iTipoVenta);
    $iMedioPago = $this->security->xss_clean($iMedioPago);
    $iIdPersonal = $this->security->xss_clean($iIdPersonal);
    $sNombrePersonal = $this->security->xss_clean($sNombrePersonal);
    $iTipoTarjeta = $this->security->xss_clean($iTipoTarjeta);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);
    
		$fileNameExcel = "reporte_forma_pago_proveedor__" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Reporte Forma Pago Proveedor');
      
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
    ->setCellValue('B1', $this->empresa->No_Empresa)
    ->setCellValue('C2', 'Informe de Reporte Forma Pago Proveedor')
    ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:N2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:N3');
    $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    // /. Title
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");//F. EMISION
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");//F. Pago
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");//
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("10");
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("12");
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("60");
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("8");
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("8");
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");//Medio Pago
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("15");//Tipo tarjeta
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("18");
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("30");

    $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($BStyle_top);
    
    $objPHPExcel->getActiveSheet()->getStyle('C5:N5')->applyFromArray($BStyle_bottom);
    
    $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_left);
    $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_left);
    $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_left);
    $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_left);
    $objPHPExcel->getActiveSheet()->getStyle('O5')->applyFromArray($BStyle_left);
    $objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($BStyle_left);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('O5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($BStyle_right);

    $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($BStyle_right);
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
    $objPHPExcel->getActiveSheet()->getStyle('L6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('M6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('N6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('O6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('P6')->applyFromArray($BStyle_right);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A6:P6')->getFont()->setBold(true);
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Fecha')
    ->setCellValue('B5', 'Fecha');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('C5', 'Documento');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C5:E5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('F5', 'Proveedor');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F5:H5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('I5', 'Moneda');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I5:J5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('K5', 'Forma Pago');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K5:N5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('O5', 'Total')
    ->setCellValue('P5', 'Estado');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A6', 'Emisión')
    ->setCellValue('B6', 'Pago')
    ->setCellValue('C6', 'Tipo')
    ->setCellValue('D6', 'Serie')
    ->setCellValue('E6', 'Número')
    ->setCellValue('F6', 'Tipo')
    ->setCellValue('G6', '# Documento')
    ->setCellValue('H6', 'Nombre')
    ->setCellValue('I6', 'Tipo')
    ->setCellValue('J6', 'T.C.')
    ->setCellValue('K6', 'Medio Pago')
    ->setCellValue('L6', 'Tipo Tarjeta')
    ->setCellValue('M6', 'Nro. Tarjeta')
    ->setCellValue('N6', 'Nro. Transaccion')
    ;
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($style_align_center);
    
    $objPHPExcel->getActiveSheet()->freezePane('A7');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE

    $fila = 7;

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Documento' => $ID_Tipo_Documento,
      'ID_Serie_Documento' => $ID_Serie_Documento,
      'ID_Numero_Documento' => $ID_Numero_Documento,
      'Nu_Estado_Documento' => $Nu_Estado_Documento,
      'iIdProveedor' => $iIdProveedor,
      'sNombreProveedor' => $sNombreProveedor,
      'iIdPersonal' => $iIdPersonal,
      'sNombrePersonal' => $sNombrePersonal,
      'iTipoVenta' => $iTipoVenta,
      'iMedioPago' => $iMedioPago,
      'iTipoTarjeta' => $iTipoTarjeta,
      'ID_Almacen' => $ID_Almacen,
    );
    $arrData = $this->getReporte($arrParams);

    if ( $arrData['sStatus'] == 'success' ) {
      $fTotalItem = 0.00;
      $fCantidadTotalGeneral = 0.00; $fTotalGeneral = 0.00;
      $ID_Almacen = 0; $fTotalGeneralAlmacen = 0.00; $counter_almacen = 0;
      foreach ($arrData['arrData'] as $row) {
        if ($ID_Almacen != $row->ID_Almacen) {
            if ($counter_almacen != 0) {
              $objPHPExcel->setActiveSheetIndex($hoja_activa)
              ->setCellValue('N' . $fila, 'Total Almacén')
              ->setCellValue('O' . $fila, numberFormat($fTotalGeneralAlmacen, 2, '.', ','));
              
              $objPHPExcel->getActiveSheet()->getStyle('N' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                          
              $objPHPExcel->getActiveSheet()
              ->getStyle('A' . $fila . ':' . 'O' . $fila)
              ->applyFromArray(
                array(
                  'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E7E7E7')
                  )
                )
              );
              $objPHPExcel->getActiveSheet()->getStyle('N' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
            
              $fila++;
              
              $fTotalGeneralAlmacen = 0.00;
            }

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('A' . $fila, 'Almacén')
            ->setCellValue('B' . $fila, $row->No_Almacen);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':O'. $fila);
            
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
            
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'O' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'F2F5F5')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
            
            $ID_Almacen = $row->ID_Almacen;
            $fila++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'B' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'G' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'I' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'J' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('P' . $fila)->applyFromArray($style_align_center);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
        ->setCellValue('B' . $fila, $row->Fe_Emision_Hora_Pago)
        ->setCellValue('C' . $fila, $row->No_Tipo_Documento_Breve)
        ->setCellValue('D' . $fila, $row->ID_Serie_Documento)
        ->setCellValue('E' . $fila, $row->ID_Numero_Documento)
        ->setCellValue('F' . $fila, $row->No_Tipo_Documento_Identidad_Breve)
        ->setCellValue('G' . $fila, $row->Nu_Documento_Identidad)
        ->setCellValue('H' . $fila, $row->No_Entidad)
        ->setCellValue('I' . $fila, $row->No_Signo)
        ->setCellValue('J' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
        ->setCellValue('K' . $fila, $row->No_Medio_Pago)
        ->setCellValue('L' . $fila, $row->No_Tipo_Medio_Pago)
        ->setCellValue('M' . $fila, $row->Nu_Tarjeta)
        ->setCellValue('N' . $fila, $row->Nu_Transaccion)
        ->setCellValue('O' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
        ->setCellValue('P' . $fila, $row->No_Estado)
        ;
        $fila++;
        
        $fTotalGeneral += $row->Ss_Total;
        $fTotalGeneralAlmacen += $row->Ss_Total;

        $counter_almacen++;
      } // /. foreach arrData
      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('N' . $fila, 'Total Almacén')
      ->setCellValue('O' . $fila, numberFormat($fTotalGeneralAlmacen, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('N' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'O' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('N' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
      
      $fila++;
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('N' . $fila, 'Total')
      ->setCellValue('O' . $fila, numberFormat($fTotalGeneral, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('N' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'O' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('N' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
    } else {
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('A' . $fila, $arrData['sMessage']);
      $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':P' . $fila);
      $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
    }// /. if - else arrData

		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
	}
}
