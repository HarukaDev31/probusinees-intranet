<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Compras_x_proveedor_controller extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/informes_logistica/Compras_x_proveedor_model');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/informes_logistica/compras_x_proveedor_view');
			$this->load->view('footer', array("js_compras_x_proveedor" => true));
		}
	}
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->Compras_x_proveedor_model->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $fSubTotal = 0.00;
            $fIGV = 0.00;
            $fDescuento = 0.00;
            $fTotal = 0.00;
            
            foreach ( $arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Almacen'] = $row->ID_Almacen;
                $rows['No_Almacen'] = $row->No_Almacen;
                $rows['ID_Entidad'] = $row->ID_Entidad;
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
                $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['ID_Moneda'] = $row->ID_Moneda;
                $rows['No_Signo'] = $row->No_Signo;
                $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar);
                $rows['Nu_Codigo_Barra'] = (!empty($row->Nu_Codigo_Barra) ? $row->Nu_Codigo_Barra : '');
                $rows['No_Producto'] = (!empty($row->No_Producto) ? $row->No_Producto : '');
                $rows['Qt_Producto'] = ($row->ID_Tipo_Documento != 5 ? $row->Qt_Producto : -$row->Qt_Producto);
                $rows['Ss_Precio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Precio : -$row->Ss_Precio);
                
                // Calculando x tipo de moneda
                $fSubTotal = $row->Ss_SubTotal;
                $fImpuesto = $row->Ss_Impuesto;
                $fTotal = $row->Ss_Total;
                $fTotalExtranjero = 0.00;
                if ( $row->Nu_Sunat_Codigo_Moneda != 'PEN') {//Moneda extranjera
                    if ( $row->ID_Tipo_Documento != 5 ) {// 5 = N/C
                        $fSubTotal = $fSubTotal * $row->Ss_Tipo_Cambio;
                        $fImpuesto = $fImpuesto * $row->Ss_Tipo_Cambio;
                        $fTotal = $fTotal * $row->Ss_Tipo_Cambio;
                    } else {
                        $fSubTotal = $fSubTotal * $row->Ss_Tipo_Cambio_Modificar;
                        $fImpuesto = $fImpuesto * $row->Ss_Tipo_Cambio_Modificar;
                        $fTotal = $fTotal * $row->Ss_Tipo_Cambio_Modificar;
                    }
                    $fTotalExtranjero = $row->Ss_Total;
                }

                $rows['Ss_SubTotal'] = ($row->ID_Tipo_Documento != 5 ? $fSubTotal : -$fSubTotal);
                $rows['Ss_IGV'] = ($row->ID_Tipo_Documento != 5 ? $fImpuesto : -$fImpuesto);
                $rows['Ss_Descuento'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Descuento : -$row->Ss_Descuento);
                $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $fTotal : -$fTotal);
                $rows['Ss_Total_Extranjero'] = ($row->ID_Tipo_Documento != 5 ? $fTotalExtranjero : -$fTotalExtranjero);
                
                $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];

                //Otros campos
                $rows['Nu_Lote_Vencimiento'] = (!empty($row->Nu_Lote_Vencimiento) ? $row->Nu_Lote_Vencimiento : '');
                $rows['Fe_Lote_Vencimiento'] = (!empty($row->Fe_Lote_Vencimiento) ? ToDateBD($row->Fe_Lote_Vencimiento) : '');

                $rows['Ss_Percepcion'] = $row->Ss_Percepcion;

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
            'iIdProveedor' => $this->input->post('iIdProveedor'),
            'sNombreProveedor' => $this->input->post('sNombreProveedor'),
            'iIdItem' => $this->input->post('iIdItem'),
            'sNombreItem' => $this->input->post('sNombreItem'),
            'ID_Almacen' => $this->input->post('ID_Almacen'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdProveedor, $sNombreProveedor, $iIdItem, $sNombreItem, $iTipoReporte, $ID_Almacen){
        $this->load->library('FormatoLibroSunatPDF');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
        $iIdProveedor = $this->security->xss_clean($iIdProveedor);
        $sNombreProveedor = $this->security->xss_clean($sNombreProveedor);
        $iIdItem = $this->security->xss_clean($iIdItem);
        $sNombreItem = $this->security->xss_clean($sNombreItem);
        $iTipoReporte = $this->security->xss_clean($iTipoReporte);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        
		$fileNamePDF = "Reporte_Compras_x_Proveedor_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
            "iTipoReporte" => $iTipoReporte,
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
            'iIdItem' => $iIdItem,
            'sNombreItem' => $sNombreItem,
            'iTipoReporte' => $iTipoReporte,
            'ID_Almacen' => $ID_Almacen,
        );
        
		ob_start();
		$file = $this->load->view('Logistica/informes_logistica/pdf/compras_x_proveedor_pdf', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
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
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdProveedor, $sNombreProveedor, $iIdItem, $sNombreItem, $iTipoReporte, $ID_Almacen){
        $this->load->library('Excel');

        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
        $iIdProveedor = $this->security->xss_clean($iIdProveedor);
        $sNombreProveedor = $this->security->xss_clean($sNombreProveedor);
        $iIdItem = $this->security->xss_clean($iIdItem);
        $sNombreItem = $this->security->xss_clean($sNombreItem);
        $iTipoReporte = $this->security->xss_clean($iTipoReporte);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        
		$fileNameExcel = "Reporte_Compras_x_Proveedor_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Compras por Proveedor');
        
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
        ->setCellValue('C2', 'Informe de Compras por Proveedor')
        ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:O2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:O3');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("15");

        $objPHPExcel->getActiveSheet()->getStyle('A5:R5')->applyFromArray($BStyle_top);
        
        $objPHPExcel->getActiveSheet()->getStyle('B5:D5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('G5:N5')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:R6')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R5')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($BStyle_right);
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
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:R5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A6:R6')->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'Fecha');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('B5', 'Documento');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B5:D5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('E5', 'Moneda')
        ->setCellValue('F5', 'Tipo');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('G5', 'Producto');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G5:N5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A6', 'Emisión')
        ->setCellValue('B6', 'Tipo')
        ->setCellValue('C6', 'Serie')
        ->setCellValue('D6', 'Número')
        ->setCellValue('F6', 'Cambio')
        ->setCellValue('G6', 'Descripción')
        ->setCellValue('H6', 'Cantidad')
        ->setCellValue('I6', 'Precio')
        ->setCellValue('J6', 'SubTotal S/')
        ->setCellValue('K6', 'I.G.V S/')
        ->setCellValue('L6', 'Dscto. S/')
        ->setCellValue('M6', 'Total S/')
        ->setCellValue('N6', 'Total M. Ex.')
        ->setCellValue('O6', 'Nro. Lote')
        ->setCellValue('P6', 'F. Vencimiento')
        ->setCellValue('Q6', 'Estado')
        ->setCellValue('R6', 'Estado')
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:R6')->applyFromArray($style_align_center);
        
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
            'iIdItem' => $iIdItem,
            'sNombreItem' => $sNombreItem,
            'iTipoReporte' => $iTipoReporte,
            'ID_Almacen' => $ID_Almacen,
        );
        $arrData = $this->getReporte($arrParams);

        if ( $arrData['sStatus'] == 'success' ) {
            $ID_Entidad = '';
            $counter = 0;
            
            $subtotal_s = 0.00;
            $igv_s = 0.00;
            $descuento_s = 0.00;
            $total_s = 0.00;
            
            $sum_compra_cantidad = 0.000000;
            $sum_compra_subtotal_s = 0.00;
            $sum_compra_descuento_s = 0.00;
            $sum_compra_igv_s = 0.00;
            $sum_compra_total_s = 0.00;
            $sum_compra_total_d = 0.00;
            
            $sum_general_compra_cantidad = 0.000000;
            $sum_general_compra_subtotal_s = 0.00;
            $sum_general_compra_descuento_s = 0.00;
            $sum_general_compra_igv_s = 0.00;
            $sum_general_compra_total_s = 0.00;
            $sum_general_compra_total_d = 0.00;
            
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_compras_cantidad = 0.000000; $sum_almacen_compras_subtotal_s = 0.00; $sum_almacen_compras_descuento_s = 0.00; $sum_almacen_compras_igv_s = 0.00; $sum_almacen_compras_total_s = 0.00; $sum_almacen_compras_total_d = 0.00;
            foreach ($arrData['arrData'] as $row) {
                if ($ID_Entidad != $row->ID_Entidad || $ID_Almacen != $row->ID_Almacen) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('G' . $fila, 'Total')
                        ->setCellValue('H' . $fila, numberFormat($sum_compra_cantidad, 3, '.', ','))
                        ->setCellValue('J' . $fila, numberFormat($sum_compra_subtotal_s, 2, '.', ','))
                        ->setCellValue('K' . $fila, numberFormat($sum_compra_igv_s, 2, '.', ','))
                        ->setCellValue('L' . $fila, numberFormat($sum_compra_descuento_s, 2, '.', ','))
                        ->setCellValue('M' . $fila, numberFormat($sum_compra_total_s, 2, '.', ','))
                        ->setCellValue('N' . $fila, numberFormat($sum_compra_total_d, 2, '.', ','));
                        
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'R' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'E7E7E7')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                        
                        $sum_compra_cantidad = 0.000000;
                        $sum_compra_subtotal_s = 0.00;
                        $sum_compra_igv_s = 0.00;
                        $sum_compra_descuento_s = 0.00;
                        $sum_compra_total_s = 0.00;
                        $sum_compra_total_d = 0.00;
                    }
                    
                    if ($ID_Almacen != $row->ID_Almacen) {
                        if ($counter_almacen != 0) {
                            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                            ->setCellValue('G' . $fila, 'Total')
                            ->setCellValue('H' . $fila, numberFormat($sum_almacen_compra_cantidad, 3, '.', ','))
                            ->setCellValue('J' . $fila, numberFormat($sum_almacen_compra_subtotal_s, 2, '.', ','))
                            ->setCellValue('K' . $fila, numberFormat($sum_almacen_compra_igv_s, 2, '.', ','))
                            ->setCellValue('L' . $fila, numberFormat($sum_almacen_compra_descuento_s, 2, '.', ','))
                            ->setCellValue('M' . $fila, numberFormat($sum_almacen_compra_total_s, 2, '.', ','))
                            ->setCellValue('N' . $fila, numberFormat($sum_almacen_compra_total_d, 2, '.', ','));
                            
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                            
                            $objPHPExcel->getActiveSheet()
                            ->getStyle('A' . $fila . ':' . 'R' . $fila)
                            ->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'E7E7E7')
                                    )
                                )
                            );
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                        
                            $fila++;
                            
                            $sum_almacen_compra_cantidad = 0.000000;
                            $sum_almacen_compra_subtotal_s = 0.00;
                            $sum_almacen_compra_igv_s = 0.00;
                            $sum_almacen_compra_descuento_s = 0.00;
                            $sum_almacen_compra_total_s = 0.00;
                            $sum_almacen_compra_total_d = 0.00;
                        }

                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'Almacén')
                        ->setCellValue('B' . $fila, $row->No_Almacen);

                        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':N'. $fila);
                        
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
                        
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'R' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'F2F5F5')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                        
                        $ID_Almacen = $row->ID_Almacen;
                        $fila++;
                    }

                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Proveedor')
                    ->setCellValue('B' . $fila, $row->Nu_Documento_Identidad)
                    ->setCellValue('C' . $fila, $row->No_Entidad)
                    ;
                    
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
                    
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('A' . $fila . ':' . 'R' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F2F5F5')
                            )
                        )
                    );
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                    
                    $ID_Entidad = $row->ID_Entidad;
                    $fila++;
                }

                if ($iTipoReporte==0) {
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, $row->Fe_Emision)
                    ->setCellValue('B' . $fila, $row->No_Tipo_Documento_Breve)
                    ->setCellValue('C' . $fila, $row->ID_Serie_Documento)
                    ->setCellValue('D' . $fila, $row->ID_Numero_Documento)
                    ->setCellValue('E' . $fila, $row->No_Signo)
                    ->setCellValue('F' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
                    ->setCellValue('G' . $fila, $row->No_Producto)
                    ->setCellValue('H' . $fila, numberFormat($row->Qt_Producto, 3, '.', ','))
                    ->setCellValue('I' . $fila, numberFormat($row->Ss_Precio, 3, '.', ','))
                    ->setCellValue('J' . $fila, numberFormat($row->Ss_SubTotal, 2, '.', ','))
                    ->setCellValue('K' . $fila, numberFormat($row->Ss_IGV, 2, '.', ','))
                    ->setCellValue('L' . $fila, numberFormat($row->Ss_Descuento, 2, '.', ','))
                    ->setCellValue('M' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                    ->setCellValue('N' . $fila, numberFormat($row->Ss_Total_Extranjero, 2, '.', ''))
                    ->setCellValue('O' . $fila, $row->Nu_Lote_Vencimiento)
                    ->setCellValue('P' . $fila, $row->Fe_Lote_Vencimiento)
                    ->setCellValue('Q' . $fila, numberFormat($row->Ss_Percepcion, 2, '.', ''))
                    ->setCellValue('R' . $fila, $row->No_Estado)
                    ;
                    $fila++;
                }
                
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'C' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('O' . $fila . ':' . 'P' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('R' . $fila)->applyFromArray($style_align_center);

                $sum_compra_cantidad += $row->Qt_Producto;
                $sum_compra_subtotal_s += $row->Ss_SubTotal;
                $sum_compra_igv_s += $row->Ss_IGV;
                $sum_compra_descuento_s += $row->Ss_Descuento;
                $sum_compra_total_s += $row->Ss_Total;
                $sum_compra_total_d += $row->Ss_Total_Extranjero;
              
                $sum_almacen_compra_cantidad += $row->Qt_Producto;
                $sum_almacen_compra_subtotal_s += $row->Ss_SubTotal;
                $sum_almacen_compra_igv_s += $row->Ss_IGV;
                $sum_almacen_compra_descuento_s += $row->Ss_Descuento;
                $sum_almacen_compra_total_s += $row->Ss_Total;
                $sum_almacen_compra_total_d += $row->Ss_Total_Extranjero;
                
                $sum_general_compra_cantidad += $row->Qt_Producto;
                $sum_general_compra_subtotal_s += $row->Ss_SubTotal;
                $sum_general_compra_igv_s += $row->Ss_IGV;
                $sum_general_compra_descuento_s += $row->Ss_Descuento;
                $sum_general_compra_total_s += $row->Ss_Total;
                $sum_general_compra_total_d += $row->Ss_Total_Extranjero;
                
                $counter++;
                $counter_almacen++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('G' . $fila, 'Total')
            ->setCellValue('H' . $fila, numberFormat($sum_compra_cantidad, 3, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_compra_subtotal_s, 2, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_compra_igv_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_compra_descuento_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_compra_total_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_compra_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'R' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('G' . $fila, 'Total Almacén')
            ->setCellValue('H' . $fila, numberFormat($sum_almacen_compra_cantidad, 3, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_almacen_compra_subtotal_s, 2, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_almacen_compra_igv_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_almacen_compra_descuento_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_almacen_compra_total_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_almacen_compra_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'R' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('G' . $fila, 'Total General')
            ->setCellValue('H' . $fila, numberFormat($sum_general_compra_cantidad, 3, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_general_compra_subtotal_s, 2, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_general_compra_igv_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_general_compra_descuento_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_general_compra_total_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_general_compra_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'R' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
        } else {

        }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}