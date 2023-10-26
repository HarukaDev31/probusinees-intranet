<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class ComprasDetalladasGeneralesController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/informes_logistica/ComprasDetalladasGeneralesModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/informes_logistica/ComprasDetalladasGeneralesView');
			$this->load->view('footer', array("js_compras_detalladas_generales" => true));
		}
	}	
	
  private function getReporte($arrParams){
    $arrResponseModal = $this->ComprasDetalladasGeneralesModel->getReporte($arrParams);
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
        $rows['Fe_Emision_Hora'] = ToDateBD(allTypeDate($row->Fe_Emision_Hora, ' ', 2));
        $rows['Fe_Hora'] = allTypeDate($row->Fe_Emision_Hora, ' ', 3);
        $rows['No_Empleado'] = '';
        $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
        $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
        $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
        $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
        
        $rows['No_Tipo_Documento_Identidad_Breve'] = $row->No_Tipo_Documento_Identidad_Breve;
        $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
        $rows['No_Entidad'] = $row->No_Entidad;

        $rows['No_Signo'] = $row->No_Signo;
        $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar);
        
        $rows['No_Marca'] = (!empty($row->No_Marca) ? $row->No_Marca : '');
        $rows['No_Familia'] = (!empty($row->No_Familia) ? $row->No_Familia : '');
        $rows['No_Sub_Familia'] = (!empty($row->No_Sub_Familia) ? $row->No_Sub_Familia : '');
        $rows['No_Unidad_Medida'] = (!empty($row->No_Unidad_Medida) ? $row->No_Unidad_Medida : '');
        $rows['Nu_Codigo_Barra'] = (!empty($row->Nu_Codigo_Barra) ? $row->Nu_Codigo_Barra : '');
        $rows['No_Producto'] = (!empty($row->No_Producto) ? $row->No_Producto : '');
        $rows['Txt_Nota_Item'] = (!empty($row->Txt_Nota_Item) ? $row->Txt_Nota_Item : '');
        $rows['Qt_Producto'] = ($row->ID_Tipo_Documento != 5 ? $row->Qt_Producto : -$row->Qt_Producto);
        $rows['Qt_CO2_Producto'] = (!empty($row->Qt_CO2_Producto) ? $row->Qt_CO2_Producto : '');
        $rows['Ss_Precio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Precio : -$row->Ss_Precio);

        if ( $row->ID_Tipo_Documento != 2 )
          $rows['Ss_Subtotal'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Subtotal : -$row->Ss_Subtotal);
        else
          $rows['Ss_Subtotal'] = $row->Ss_Total;

        if ( $row->ID_Tipo_Documento != 2 )
          $rows['Ss_Impuesto'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Impuesto : -$row->Ss_Impuesto);
        else
          $rows['Ss_Impuesto'] = 0;

        $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total : -$row->Ss_Total);
        $rows['Txt_Nota'] = $row->Txt_Nota;

        $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
        $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
        $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
        $rows['Nu_Estado'] = $row->Nu_Estado;
        $rows['sAccionVer'] = '<button class="btn btn-xs btn-link" alt="Ver comprobante" title="Ver comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionVer . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-list-alt" aria-hidden="true"> Ver</i></button>';
        $rows['sAccionImprimir'] = '<button class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-print" aria-hidden="true"> Imprimir</i></button>';
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
      'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
      'ID_Serie_Documento' => $this->input->post('ID_Serie_Documento'),
      'ID_Numero_Documento' => $this->input->post('ID_Numero_Documento'),
      'Nu_Estado_Documento' => $this->input->post('Nu_Estado_Documento'),
      'iIdCliente' => $this->input->post('iIdCliente'),
      'sNombreCliente' => $this->input->post('sNombreCliente'),
      'iIdItem' => $this->input->post('iIdItem'),
      'sNombreItem' => $this->input->post('sNombreItem'),
      'iTipoVenta' => $this->input->post('iTipoVenta'),
      'ID_Almacen' => $this->input->post('ID_Almacen'),
    );
    echo json_encode($this->getReporte($arrParams));
  }
    
  public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iIdItem, $sNombreItem, $iTipoVenta, $ID_Almacen){
    $this->load->library('FormatoLibroSunatPDF');

    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
    $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
    $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
    $iIdCliente = $this->security->xss_clean($iIdCliente);
    $sNombreCliente = $this->security->xss_clean($sNombreCliente);
    $iIdItem = $this->security->xss_clean($iIdItem);
    $sNombreItem = $this->security->xss_clean($sNombreItem);
    $iTipoVenta = $this->security->xss_clean($iTipoVenta);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);

    $fileNamePDF = "compras_detalladas_generales" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

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
      'iIdCliente' => $iIdCliente,
      'sNombreCliente' => $sNombreCliente,
      'iIdItem' => $iIdItem,
      'sNombreItem' => $sNombreItem,
      'iTipoVenta' => $iTipoVenta,
      'ID_Almacen' => $ID_Almacen,
    );

    ob_start();
    $file = $this->load->view('Logistica/informes_logistica/pdf/ComprasDetalladasGeneralesViewPDF', array(
      'arrCabecera' => $arrCabecera,
      'arrDetalle' => $this->getReporte($arrParams),
    ));
    $html = ob_get_contents();
    ob_end_clean();

    $pdf->SetAuthor('laesystems');
    $pdf->SetTitle('laesystems - Compras Detalladas Generales');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->setFont('helvetica', '', 5);
    
		$pdf->AddPage('L', ['format' => 'A4', 'Rotate' => 90]);
		$pdf->writeHTML($html, true, false, true, false, '');
		
    $pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iIdItem, $sNombreItem, $iTipoVenta, $ID_Almacen){
    $this->load->library('Excel');
	  
    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
    $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
    $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
    $iIdCliente = $this->security->xss_clean($iIdCliente);
    $sNombreCliente = $this->security->xss_clean($sNombreCliente);
    $iIdItem = $this->security->xss_clean($iIdItem);
    $sNombreItem = $this->security->xss_clean($sNombreItem);
    $iTipoVenta = $this->security->xss_clean($iTipoVenta);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);
    
		$fileNameExcel = "compras_detalladas_generales_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Compras Detalladas Generales');
      
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
    ->setCellValue('C2', 'Informe de Compras Detalladas Generales')
    ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:W2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:W3');
    $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    // /. Title
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");//FECHA
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("12");//HORA
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("12");//TIPO
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("12");//SERIE
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("50");//CLIENTE NRO. DOC IDENTI
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");//CLIENTE RAZON S
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("12");
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("20");//U.M.
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("20");//ITEM UPC / CODIGO DE BARRA
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("50");//ITEM NOMBRE
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("20");//ITEM NOTA
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth("30");
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth("30");//NOTA GLOBAL
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth("40");//ESTADO

    $objPHPExcel->getActiveSheet()->getStyle('A5:Y5')->applyFromArray($BStyle_top);
    
    $objPHPExcel->getActiveSheet()->getStyle('C5:W5')->applyFromArray($BStyle_bottom);
    
    $objPHPExcel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($BStyle_bottom);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('T5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('U5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('V5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('W5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('X5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('Y5')->applyFromArray($BStyle_right);

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
    $objPHPExcel->getActiveSheet()->getStyle('Q6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('R6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('S6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('T6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('U6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('V6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('W6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('X6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('Y6')->applyFromArray($BStyle_right);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:Y5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A6:Y6')->getFont()->setBold(true);
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Fecha')
    ->setCellValue('B5', 'Hora');
    
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
    ->setCellValue('K5', 'Producto');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K5:W5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('X5', 'Nota');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('Y5', 'Estado');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A6', 'Emisión')
    ->setCellValue('B6', 'Emisión')
    ->setCellValue('C6', 'Tipo')
    ->setCellValue('D6', 'Serie')
    ->setCellValue('E6', 'Número')
    ->setCellValue('F6', 'Tipo')
    ->setCellValue('G6', '# Documento')
    ->setCellValue('H6', 'Nombre')
    ->setCellValue('I6', 'Tipo')
    ->setCellValue('J6', 'T.C.')
    ->setCellValue('K6', 'Marca')
    ->setCellValue('L6', 'Categoría')
    ->setCellValue('M6', 'Sub Categoría')
    ->setCellValue('N6', 'Unidad Medida')
    ->setCellValue('O6', 'Código de Barra')
    ->setCellValue('P6', 'Nombre')
    ->setCellValue('Q6', 'Nota')
    ->setCellValue('R6', 'Cantidad')
    ->setCellValue('S6', 'CO2')
    ->setCellValue('T6', 'Precio')
    ->setCellValue('U6', 'SubTotal')
    ->setCellValue('V6', 'Impuesto')
    ->setCellValue('W6', 'Total')
    ->setCellValue('X6', 'Global')
    ->setCellValue('Y6', 'Documento')
    ;
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:Y5')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('A6:Y6')->applyFromArray($style_align_center);
    
    $objPHPExcel->getActiveSheet()->freezePane('A7');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
    
    $fila = 7;

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Documento' => $ID_Tipo_Documento,
      'ID_Serie_Documento' => $ID_Serie_Documento,
      'ID_Numero_Documento' => $ID_Numero_Documento,
      'Nu_Estado_Documento' => $Nu_Estado_Documento,
      'iIdCliente' => $iIdCliente,
      'sNombreCliente' => $sNombreCliente,
      'iIdItem' => $iIdItem,
      'sNombreItem' => $sNombreItem,
      'iTipoVenta' => $iTipoVenta,
      'ID_Almacen' => $ID_Almacen,
    );
    $arrData = $this->getReporte($arrParams);
        
    if ( $arrData['sStatus'] == 'success' ) {
      $fCantidadItem = 0.00; $fPrecioItem = 0.00; $fSubtotalItem = 0.00; $fImpuestoItem = 0.00; $fTotalItem = 0.00;
      $fCantidadTotalGeneral = 0.00; $fSubtotalGeneral = 0.00; $fImpuestoGeneral = 0.00; $fTotalGeneral = 0.00;
      $ID_Almacen = 0; $counter_almacen=0; $fCantidadTotalGeneralAlmacen = 0.00; $fSubtotalGeneralAlmacen = 0.00; $fImpuestoGeneralAlmacen = 0.00; $fTotalGeneralAlmacen = 0.00;
      foreach ($arrData['arrData'] as $row) {
        if ($ID_Almacen != $row->ID_Almacen) {
            if ($counter_almacen != 0) {
              $objPHPExcel->setActiveSheetIndex($hoja_activa)
              ->setCellValue('Q' . $fila, 'Total Almacén')
              ->setCellValue('R' . $fila, numberFormat($fCantidadTotalGeneralAlmacen, 6, '.', ','))
              ->setCellValue('U' . $fila, numberFormat($fSubtotalGeneralAlmacen, 2, '.', ','))
              ->setCellValue('V' . $fila, numberFormat($fImpuestoGeneralAlmacen, 2, '.', ','))
              ->setCellValue('W' . $fila, numberFormat($fTotalGeneralAlmacen, 2, '.', ','));
              
              $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'W' . $fila)->applyFromArray($style_align_right);
                          
              $objPHPExcel->getActiveSheet()
              ->getStyle('A' . $fila . ':' . 'W' . $fila)
              ->applyFromArray(
                array(
                  'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E7E7E7')
                  )
                )
              );
              $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'W' . $fila)->getFont()->setBold(true);
            
              $fila++;
              
              $fCantidadTotalGeneralAlmacen = 0.00;
              $fSubtotalGeneralAlmacen = 0.00;
              $fImpuestoGeneralAlmacen = 0.00;
              $fTotalGeneralAlmacen = 0.00;
            }

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('A' . $fila, 'Almacén')
            ->setCellValue('B' . $fila, $row->No_Almacen);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':W'. $fila);
            
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
            
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'W' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'F2F5F5')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'W' . $fila)->getFont()->setBold(true);
            
            $ID_Almacen = $row->ID_Almacen;
            $fila++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'B' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'G' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':' . 'Q' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('R' . $fila . ':' . 'X' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y' . $fila)->applyFromArray($style_align_center);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
        ->setCellValue('B' . $fila, $row->Fe_Hora)
        ->setCellValue('C' . $fila, $row->No_Tipo_Documento_Breve)
        ->setCellValue('D' . $fila, $row->ID_Serie_Documento)
        ->setCellValue('E' . $fila, $row->ID_Numero_Documento)
        ->setCellValue('F' . $fila, $row->No_Tipo_Documento_Identidad_Breve)
        ->setCellValue('G' . $fila, $row->Nu_Documento_Identidad)
        ->setCellValue('H' . $fila, $row->No_Entidad)
        ->setCellValue('I' . $fila, $row->No_Signo)
        ->setCellValue('J' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
        ->setCellValue('K' . $fila, $row->No_Marca)
        ->setCellValue('L' . $fila, $row->No_Familia)
        ->setCellValue('M' . $fila, $row->No_Sub_Familia)
        ->setCellValue('N' . $fila, $row->No_Unidad_Medida)
        ->setCellValue('O' . $fila, $row->Nu_Codigo_Barra)
        ->setCellValue('P' . $fila, $row->No_Producto)
        ->setCellValue('Q' . $fila, $row->Txt_Nota_Item)
        ->setCellValue('R' . $fila, numberFormat($row->Qt_Producto, 6, '.', ','))
        ->setCellValue('S' . $fila, $row->Qt_CO2_Producto)
        ->setCellValue('T' . $fila, numberFormat($row->Ss_Precio, 2, '.', ','))
        ->setCellValue('U' . $fila, numberFormat($row->Ss_Subtotal, 2, '.', ','))
        ->setCellValue('V' . $fila, numberFormat($row->Ss_Impuesto, 2, '.', ','))
        ->setCellValue('W' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
        ->setCellValue('X' . $fila, $row->Txt_Nota)
        ->setCellValue('Y' . $fila, $row->No_Estado)
        ;
        $fila++;
        
        $fCantidadTotalGeneral += $row->Qt_Producto;
        $fSubtotalGeneral += $row->Ss_Subtotal;
        $fImpuestoGeneral += $row->Ss_Impuesto;
        $fTotalGeneral += $row->Ss_Total;
        
        $fCantidadTotalGeneralAlmacen += $row->Qt_Producto;
        $fSubtotalGeneralAlmacen += $row->Ss_Subtotal;
        $fImpuestoGeneralAlmacen += $row->Ss_Impuesto;
        $fTotalGeneralAlmacen += $row->Ss_Total;

        $counter_almacen++;
      } // /. foreach arrData
      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('Q' . $fila, 'Total Almacén')
      ->setCellValue('R' . $fila, numberFormat($fCantidadTotalGeneralAlmacen, 6, '.', ','))
      ->setCellValue('U' . $fila, numberFormat($fSubtotalGeneralAlmacen, 2, '.', ','))
      ->setCellValue('V' . $fila, numberFormat($fImpuestoGeneralAlmacen, 2, '.', ','))
      ->setCellValue('W' . $fila, numberFormat($fTotalGeneralAlmacen, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'W' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'W' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'W' . $fila)->getFont()->setBold(true);

      $fila++;
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('Q' . $fila, 'Total')
      ->setCellValue('R' . $fila, numberFormat($fCantidadTotalGeneral, 6, '.', ','))
      ->setCellValue('U' . $fila, numberFormat($fSubtotalGeneral, 2, '.', ','))
      ->setCellValue('V' . $fila, numberFormat($fImpuestoGeneral, 2, '.', ','))
      ->setCellValue('W' . $fila, numberFormat($fTotalGeneral, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'W' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'W' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'W' . $fila)->getFont()->setBold(true);
    } else {
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('A' . $fila, $arrData['sMessage']);
      $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':Y' . $fila);
      $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
    }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
	}
}
