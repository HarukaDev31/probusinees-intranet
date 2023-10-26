<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DetalleGuiaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/informes_logistica/DetalleGuiaModel');
		$this->load->model('HelperModel');
	}

	public function reporte(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/informes_logistica/DetalleGuiaView');
			$this->load->view('footer', array("js_detalle_guia" => true));
		}
	}
	
    private function getReporte($arrParams){
        $arrData = $this->DetalleGuiaModel->getReporte($arrParams);
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();
            $rows['ID_Almacen'] = $row->ID_Almacen;
            $rows['No_Almacen'] = $row->No_Almacen;
            $rows['ID_Guia_Cabecera'] = $row->ID_Guia_Cabecera;
            $rows['ID_Entidad'] = $row->ID_Entidad;
            $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
            $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
            $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
            $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
            $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
            $rows['No_Entidad'] = $row->No_Entidad;
            $rows['ID_Serie_Documento_Factura'] = $row->ID_Serie_Documento_Factura;
            $rows['ID_Numero_Documento_Factura'] = $row->ID_Numero_Documento_Factura;
            $rows['ID_Moneda'] = $row->ID_Moneda;
            $rows['No_Signo'] = $row->No_Signo;
            $rows['MONE_Nu_Sunat_Codigo'] = $row->MONE_Nu_Sunat_Codigo;
            $rows['Ss_Tipo_Cambio'] = $row->Ss_Tipo_Cambio;
            $rows['Nu_Codigo_Barra'] = $row->Nu_Codigo_Barra;
            $rows['No_Producto'] = $row->No_Producto;
            $rows['Qt_Producto'] = $row->Qt_Producto;
            $rows['Ss_Precio'] = $row->Ss_Precio;
            $rows['Ss_SubTotal'] = $row->Ss_SubTotal;
            $rows['Ss_Impuesto'] = $row->Ss_Impuesto;
            $rows['Ss_Total'] = $row->Ss_Total;
            $rows['Txt_Glosa'] = (!empty($row->Txt_Glosa) ? $row->Txt_Glosa : '');
            
            $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
            $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
            $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];

            $rows['No_Tipo_Movimiento'] = ($row->Nu_Tipo_Movimiento == 0 ? 'Entrada' : 'Salida');
            $rows['No_Tipo_Movimiento_Detallado'] = $row->No_Tipo_Movimiento;
            $data[] = (object)$rows;
        }
        return $data;
    }
    
	public function sendReporte(){
        $arrParams = array(
            'ID_Almacen' => $this->input->post('ID_Almacen'),
            'Fe_Inicio' => $this->input->post('Fe_Inicio'),
            'Fe_Fin' => $this->input->post('Fe_Fin'),
            'ID_Serie_Documento' => $this->input->post('ID_Serie_Documento'),
            'ID_Numero_Documento' => $this->input->post('ID_Numero_Documento'),
            'Nu_Tipo_Movimiento' => $this->input->post('Nu_Tipo_Movimiento'),
            'Nu_Estado_Documento' => $this->input->post('Nu_Estado_Documento'),
            'ID_Proveedor' => $this->input->post('ID_Proveedor'),
            'ID_Producto' => $this->input->post('ID_Producto'),
            'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
            'ID_Almacen_Externo' => $this->input->post('ID_Almacen_Externo'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($ID_Almacen, $Fe_Inicio, $Fe_Fin, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Tipo_Movimiento, $Nu_Estado_Documento, $ID_Proveedor, $ID_Producto, $No_Almacen, $ID_Tipo_Documento, $ID_Almacen_Externo){
        $this->load->library('FormatoLibroSunatPDF');
		
        $ID_Empresa             = $this->user->ID_Empresa;
        $ID_Almacen             = $this->security->xss_clean($ID_Almacen);
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $ID_Serie_Documento     = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento    = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Tipo_Movimiento     = $this->security->xss_clean($Nu_Tipo_Movimiento);
        $Nu_Estado_Documento    = $this->security->xss_clean($Nu_Estado_Documento);
        $ID_Proveedor           = $this->security->xss_clean($ID_Proveedor);
        $ID_Producto            = $this->security->xss_clean($ID_Producto);
        $No_Almacen             = $this->security->xss_clean($No_Almacen);
        $ID_Tipo_Documento      = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Almacen_Externo      = $this->security->xss_clean($ID_Almacen_Externo);
        
		$fileNamePDF = "Reporte_Guias_Entrada_Salida_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $arrCabecera = array (
            "No_Almacen" => $No_Almacen,
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
        );
        
        $arrParams = array(
            'ID_Almacen' => $ID_Almacen,
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'ID_Serie_Documento' => $ID_Serie_Documento,
            'ID_Numero_Documento' => $ID_Numero_Documento,
            'Nu_Tipo_Movimiento' => $Nu_Tipo_Movimiento,
            'Nu_Estado_Documento' => $Nu_Estado_Documento,
            'ID_Proveedor' => $ID_Proveedor,
            'ID_Producto' => $ID_Producto,
            'ID_Tipo_Documento' => $ID_Tipo_Documento,
            'ID_Almacen_Externo' => $ID_Almacen_Externo,
        );

		ob_start();
		$file = $this->load->view('Logistica/informes_logistica/pdf/DetalleGuiaPDF', array(
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
    
	public function sendReporteEXCEL($ID_Almacen, $Fe_Inicio, $Fe_Fin, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Tipo_Movimiento, $Nu_Estado_Documento, $ID_Proveedor, $ID_Producto, $No_Almacen, $ID_Tipo_Documento, $ID_Almacen_Externo){
        $this->load->library('Excel');
	    
        $ID_Empresa             = $this->user->ID_Empresa;
        $ID_Almacen             = $this->security->xss_clean($ID_Almacen);
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $ID_Serie_Documento     = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento    = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Tipo_Movimiento     = $this->security->xss_clean($Nu_Tipo_Movimiento);
        $Nu_Estado_Documento    = $this->security->xss_clean($Nu_Estado_Documento);
        $ID_Proveedor           = $this->security->xss_clean($ID_Proveedor);
        $ID_Producto            = $this->security->xss_clean($ID_Producto);
        $No_Almacen             = $this->security->xss_clean($No_Almacen);
        $ID_Tipo_Documento      = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Almacen_Externo      = $this->security->xss_clean($ID_Almacen_Externo);
        
		$fileNameExcel = "Reporte_Guias_Entrada_Salida_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
        $arrParams = array(
            'ID_Almacen' => $ID_Almacen,
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'ID_Serie_Documento' => $ID_Serie_Documento,
            'ID_Numero_Documento' => $ID_Numero_Documento,
            'Nu_Tipo_Movimiento' => $Nu_Tipo_Movimiento,
            'Nu_Estado_Documento' => $Nu_Estado_Documento,
            'ID_Proveedor' => $ID_Proveedor,
            'ID_Producto' => $ID_Producto,
            'ID_Tipo_Documento' => $ID_Tipo_Documento,
            'ID_Almacen_Externo' => $ID_Almacen_Externo,
        );
        $data = $this->getReporte($arrParams);

	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Detalle Guías Entrada y Salida');
        
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
        ->setCellValue('E1', $this->empresa->No_Empresa)
        ->setCellValue('O1', $No_Almacen)
        ->setCellValue('E2', 'Detalle de Guías de Entrada y Salida')
        ->setCellValue('E3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E2:O2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E3:O3');
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("50");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("6");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("50");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("35");//glosa
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("20");//estado
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth("20");//Tipo entrada o salidad
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth("30");//Tipo entrada o salidad

        $objPHPExcel->getActiveSheet()->getStyle('A5:U5')->applyFromArray($BStyle_top);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:B5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('D5:E5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('F5:G5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('J5:O5')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:Q6')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U5')->applyFromArray($BStyle_right);
        
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
        $objPHPExcel->getActiveSheet()->getStyle('S6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U6')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:U5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A6:U6')->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'Guía');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A5:B5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('C5', 'Fecha');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('D5', 'Proveedor');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D5:E5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('F5', 'Factura');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F5:G5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('H5', 'Moneda')
        ->setCellValue('I5', 'Tipo');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('J5', 'Producto');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('J5:Q5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('R5', 'Glosa');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('S5', 'Estado');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('T5', 'Tipo');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('U5', 'Movimiento');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A6', 'Serie')
        ->setCellValue('B6', 'Número')
        ->setCellValue('C6', 'Emisión')
        ->setCellValue('D6', 'RUC')
        ->setCellValue('E6', 'Razón Social')
        ->setCellValue('F6', 'Serie')
        ->setCellValue('G6', 'Número')
        ->setCellValue('I6', 'Cambio')
        ->setCellValue('J6', 'Código Barra')
        ->setCellValue('K6', 'Descripción')
        ->setCellValue('L6', 'Cantidad')
        ->setCellValue('M6', 'Precio')
        ->setCellValue('N6', 'SubTotal S/')
        ->setCellValue('O6', 'Impuesto S/')
        ->setCellValue('P6', 'Total S/')
        ->setCellValue('Q6', 'Total M. Ext.')
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('Q5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('R5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('S5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('T5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('U5')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->freezePane('A7');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 7;
        
        if ( count($data) > 0) {
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_guia_cantidad = 0.00;
            $sum_almacen_guia_subtotal_s = 0.00; $sum_almacen_guia_impuesto_s = 0.00; $sum_almacen_guia_total_s = 0.00;
            $sum_almacen_guia_total_d = 0.00;
            $ID_Guia_Cabecera = '';
            $counter = 0;
            $sum_guia_cantidad = 0.000000;
            $sum_guia_subtotal_s = 0.00;
            $sum_guia_impuesto_s = 0.00;
            $sum_guia_total_s = 0.00;
            $sum_guia_total_d = 0.00;
            foreach ($data as $row) {
                if ($ID_Tipo_Documento != $row->ID_Tipo_Documento.$row->ID_Serie_Documento.$row->ID_Numero_Documento) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('K' . $fila, 'Total Guía')
                        ->setCellValue('L' . $fila, numberFormat($sum_guia_cantidad, 3, '.', ','))
                        ->setCellValue('N' . $fila, numberFormat($sum_guia_subtotal_s, 2, '.', ','))
                        ->setCellValue('O' . $fila, numberFormat($sum_guia_impuesto_s, 2, '.', ','))
                        ->setCellValue('P' . $fila, numberFormat($sum_guia_total_s, 2, '.', ','))
                        ->setCellValue('Q' . $fila, numberFormat($sum_guia_total_d, 2, '.', ','));
                        
                        $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($style_align_right);
                        $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':' . 'Q' . $fila)->applyFromArray($style_align_right);
                        
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'U' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'E7E7E7')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'U' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                        
                        $sum_guia_cantidad = 0.000000;
                        $sum_guia_subtotal_s = 0.00;
                        $sum_guia_impuesto_s = 0.00;
                        $sum_guia_total_s = 0.00;
                        $sum_guia_total_d = 0.00;
                    }
                    
                    if ($ID_Almacen != $row->ID_Almacen) {
                        if ($counter_almacen != 0) {
                            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                            ->setCellValue('K' . $fila, 'Total Almacén')
                            ->setCellValue('L' . $fila, numberFormat($sum_almacen_guia_cantidad, 3, '.', ','))
                            ->setCellValue('N' . $fila, numberFormat($sum_almacen_guia_subtotal_s, 2, '.', ','))
                            ->setCellValue('O' . $fila, numberFormat($sum_almacen_guia_impuesto_s, 2, '.', ','))
                            ->setCellValue('P' . $fila, numberFormat($sum_almacen_guia_total_s, 2, '.', ','))
                            ->setCellValue('Q' . $fila, numberFormat($sum_almacen_guia_total_d, 2, '.', ','));
                            
                            $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($style_align_right);
                            $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':' . 'Q' . $fila)->applyFromArray($style_align_right);
                            
                            $objPHPExcel->getActiveSheet()
                            ->getStyle('A' . $fila . ':' . 'U' . $fila)
                            ->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'E7E7E7')
                                    )
                                )
                            );
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'U' . $fila)->getFont()->setBold(true);
                        
                            $fila++;
                            
                            $sum_almacen_guia_cantidad = 0.000000;
                            $sum_almacen_guia_subtotal_s = 0.00;
                            $sum_almacen_guia_impuesto_s = 0.00;
                            $sum_almacen_guia_total_s = 0.00;
                            $sum_almacen_guia_total_d = 0.00;
                        }

                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'Almacén')
                        ->setCellValue('B' . $fila, $row->No_Almacen);
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':U'. $fila);

                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'U' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'F2F5F5')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'U' . $fila)->getFont()->setBold(true);
                        $ID_Almacen = $row->ID_Almacen;

                        $fila++;
                    }
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, $row->ID_Serie_Documento)
                    ->setCellValue('B' . $fila, $row->ID_Numero_Documento)
                    ->setCellValue('C' . $fila, $row->Fe_Emision)
                    ->setCellValue('D' . $fila, $row->Nu_Documento_Identidad)
                    ->setCellValue('E' . $fila, $row->No_Entidad)
                    ->setCellValue('F' . $fila, $row->ID_Serie_Documento_Factura !== null ? $row->ID_Serie_Documento_Factura : '')
                    ->setCellValue('G' . $fila, $row->ID_Numero_Documento_Factura !== null ? $row->ID_Numero_Documento_Factura : '')
                    ->setCellValue('R' . $fila, $row->Txt_Glosa)
                    ->setCellValue('S' . $fila, $row->No_Estado)
                    ->setCellValue('T' . $fila, $row->No_Tipo_Movimiento)
                    ->setCellValue('U' . $fila, $row->No_Tipo_Movimiento_Detallado)
                    ;
                    
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($style_align_center);
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('P' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila)->applyFromArray($style_align_center);
                    $objPHPExcel->getActiveSheet()->getStyle('R' . $fila)->applyFromArray($style_align_center);
                    $objPHPExcel->getActiveSheet()->getStyle('S' . $fila)->applyFromArray($style_align_center);
                    $objPHPExcel->getActiveSheet()->getStyle('T' . $fila)->applyFromArray($style_align_center);
                    $objPHPExcel->getActiveSheet()->getStyle('U' . $fila)->applyFromArray($style_align_center);

                    $objPHPExcel->getActiveSheet()
                    ->getStyle('A' . $fila . ':' . 'U' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F2F5F5')
                            )
                        )
                    );
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'U' . $fila)->getFont()->setBold(true);
                    
                    $ID_Tipo_Documento = $row->ID_Tipo_Documento.$row->ID_Serie_Documento.$row->ID_Numero_Documento;
                    $fila++;
                }
                        
                if ($row->Qt_Producto !== '' && $row->Ss_Precio !== '') {                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('H' . $fila, $row->No_Signo)
                    ->setCellValue('I' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','));
                    
                    $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(9, $fila, $row->Nu_Codigo_Barra, PHPExcel_Cell_DataType::TYPE_STRING);

                    //->setCellValue('J' . $fila, $row->Nu_Codigo_Barra)
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('K' . $fila, $row->No_Producto)
                    ->setCellValue('L' . $fila, numberFormat($row->Qt_Producto, 3, '.', ','))
                    ->setCellValue('M' . $fila, numberFormat($row->Ss_Precio, 3, '.', ','))
                    ->setCellValue('N' . $fila, numberFormat($row->Ss_SubTotal, 2, '.', ','))
                    ->setCellValue('O' . $fila, numberFormat($row->Ss_Impuesto, 2, '.', ','))
                    ->setCellValue('P' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                    ->setCellValue('Q' . $fila, $row->MONE_Nu_Sunat_Codigo == 'PEN' ? '0.00' : numberFormat($row->Ss_Total * $row->Ss_Tipo_Cambio, 2, '.', ''))
                    ;

                    $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($style_align_right);
                    $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':' . 'Q' . $fila)->applyFromArray($style_align_right);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($style_align_center);
                
                    $sum_guia_cantidad += $row->Qt_Producto;
                    $sum_guia_subtotal_s += $row->Ss_SubTotal;
                    $sum_guia_impuesto_s += $row->Ss_Impuesto;
                    $sum_guia_total_s += $row->Ss_Total;
                    $sum_guia_total_d += ($row->MONE_Nu_Sunat_Codigo != 'PEN' ? $row->Ss_Total : 0);
    
                    $sum_almacen_guia_cantidad += $row->Qt_Producto;
                    $sum_almacen_guia_subtotal_s += $row->Ss_SubTotal;
                    $sum_almacen_guia_impuesto_s += $row->Ss_Impuesto;
                    $sum_almacen_guia_total_s += $row->Ss_Total;
                    $sum_almacen_guia_total_d += ($row->MONE_Nu_Sunat_Codigo != 'PEN' ? $row->Ss_Total : 0);

                    $fila++;
                }
                $counter++;
                $counter_almacen++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('K' . $fila, 'Total Guía')
            ->setCellValue('L' . $fila, numberFormat($sum_guia_cantidad, 3, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_guia_subtotal_s, 2, '.', ','))
            ->setCellValue('O' . $fila, numberFormat($sum_guia_impuesto_s, 2, '.', ','))
            ->setCellValue('P' . $fila, numberFormat($sum_guia_total_s, 2, '.', ','))
            ->setCellValue('Q' . $fila, numberFormat($sum_guia_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($style_align_right);
            $objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':' . 'Q' . $fila)->applyFromArray($style_align_right);

            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'U' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'U' . $fila)->getFont()->setBold(true);
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('K' . $fila, 'Total Almacén')
            ->setCellValue('L' . $fila, numberFormat($sum_almacen_guia_cantidad, 3, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_almacen_guia_subtotal_s, 2, '.', ','))
            ->setCellValue('O' . $fila, numberFormat($sum_almacen_guia_impuesto_s, 2, '.', ','))
            ->setCellValue('P' . $fila, numberFormat($sum_almacen_guia_total_s, 2, '.', ','))
            ->setCellValue('Q' . $fila, numberFormat($sum_almacen_guia_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($style_align_right);
            $objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':' . 'Q' . $fila)->applyFromArray($style_align_right);

            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'U' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'U' . $fila)->getFont()->setBold(true);
        }
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}
