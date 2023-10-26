<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class ConsistenciaStockController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/informes_logistica/ConsistenciaStockModel');
	}

	public function reporte(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/informes_logistica/ConsistenciaStockView');
			$this->load->view('footer', array("js_consistencia_stock" => true));
		}
	}
	
    private function getReporte($ID_Empresa, $ID_Almacen, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia, $iAgruparxCategoria, $Nu_Estado_Item){
        if ($iTipoFecha == '0') {//tabla stock_producto
            $arrData = $this->ConsistenciaStockModel->getStockValorizado($ID_Empresa, $ID_Almacen, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia, $iAgruparxCategoria, $Nu_Estado_Item);
            $data = array();
            $iCantidadKardex=0;
            $Qt_Producto=0;
            foreach ($arrData as $row) {
                $iCantidadKardex = $this->ConsistenciaStockModel->getStockValorizadoxProducto($ID_Empresa, $row->ID_Almacen, $Fe_Inicio, $Fe_Fin, $row->ID_Producto, $Nu_Estado_Item);

                settype($iCantidadKardex, "double");
                settype($row->Qt_Producto, "double");

                $iCantidadKardex = round($iCantidadKardex, 3);
                $Qt_Producto = round($row->Qt_Producto, 3);

                if($iCantidadKardex!=$Qt_Producto) {
                    $rows = array();
    
                    $rows['ID_Almacen'] = $row->ID_Almacen;
                    $rows['No_Almacen'] = $row->No_Almacen;
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
                    $rows['No_Sub_Familia'] = (!empty($row->No_Sub_Familia) ? $row->No_Sub_Familia : '');
                    $rows['No_Marca'] = (!empty($row->No_Marca) ? $row->No_Marca : '');
                    $rows['Qt_Producto_Kardex'] = $iCantidadKardex;
    
                    if ($iTipoFecha == '0' && $iTipoStock == '0' && $rows['Qt_Producto'] > 0)//Mayor a cero
                        $data[] = (object)$rows;
                    else if ($iTipoFecha == '0' && $iTipoStock == '1' && $rows['Qt_Producto'] < 0)//Negativo
                        $data[] = (object)$rows;
                    else if ($iTipoFecha == '0' && $iTipoStock == '2' && $rows['Qt_Producto'] == 0)//solo stock Cero
                        $data[] = (object)$rows;
                    else if ($iTipoFecha == '0' && $iTipoStock == '3')//todos
                        $data[] = (object)$rows;
                }
            }
        } else {//tabla documento y guia
            $arrData = $this->ConsistenciaStockModel->getStockValorizadoxFecha($ID_Empresa, $ID_Almacen, $Fe_Inicio, $Fe_Fin, $ID_Familia, $ID_Producto, $iIdSubFamilia);
            $data = array();
            foreach ($arrData as $row) {
                $rows = array();
                $rows['ID_Almacen'] = $row->ID_Almacen;
                $rows['No_Almacen'] = $row->No_Almacen;
                $rows['ID_Familia'] = $row->ID_Familia;
                $rows['No_Familia'] = $row->No_Familia;
                $rows['ID_Producto'] = $row->ID_Producto;
                $rows['Nu_Codigo_Barra'] = $row->Nu_Codigo_Barra;
                $rows['No_Producto'] = $row->No_Producto;
                $rows['No_Unidad_Medida'] = $row->No_Unidad_Medida;
                $rows['Qt_Producto'] = $this->ConsistenciaStockModel->getStockValorizadoxProducto($ID_Empresa, $row->ID_Almacen, $Fe_Inicio, $Fe_Fin, $row->ID_Producto);
                $rows['Ss_Precio'] = $row->Ss_Precio;
                $rows['Ss_Costo'] = $row->Ss_Costo;
                $rows['Ss_Costo_Promedio'] = $row->Ss_Costo_Promedio;
                $rows['No_Sub_Familia'] = (!empty($row->No_Sub_Familia) ? $row->No_Sub_Familia : '');
                $rows['No_Marca'] = (!empty($row->No_Marca) ? $row->No_Marca : '');
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
                $this->input->post('ID_Almacen'),
                $this->input->post('iTipoFecha'),
                $this->input->post('Fe_Inicio'),
                $this->input->post('Fe_Fin'),
                $this->input->post('iTipoStock'),
                $this->input->post('ID_Familia'),
                $this->input->post('ID_Producto'),
                $this->input->post('iIdSubFamilia'),
                $this->input->post('iAgruparxCategoria'),
                $this->input->post('Nu_Estado_Item')
            )
        );
    }
    
	public function sendReportePDF($ID_Almacen, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $No_Almacen, $iIdSubFamilia, $iAgruparxCategoria, $Nu_Estado_Item){
        $this->load->library('FormatoLibroSunatPDF');
		
        $ID_Empresa             = $this->user->ID_Empresa;
        $ID_Almacen             = $this->security->xss_clean($ID_Almacen);
        $iTipoFecha             = $this->security->xss_clean($iTipoFecha);
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $iTipoStock             = $this->security->xss_clean($iTipoStock);
        $ID_Familia               = $this->security->xss_clean($ID_Familia);
        $ID_Producto            = $this->security->xss_clean($ID_Producto);
        $No_Almacen             = $this->security->xss_clean($No_Almacen);
        $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
        $iAgruparxCategoria = $this->security->xss_clean($iAgruparxCategoria);
        $Nu_Estado_Item = $this->security->xss_clean($Nu_Estado_Item);
        
		$fileNamePDF = "Stock_Valorizado_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$arrFecha = localtime(time(),true);

		$iYear = (1900 + $arrFecha['tm_year']);
		$iMonth = (strlen(1 + $arrFecha['tm_mon']) > 1 ? $arrFecha['tm_mon'] : '0' . (1 + $arrFecha['tm_mon']));
		$iDay = (strlen($arrFecha['tm_mday']) > 1 ? $arrFecha['tm_mday'] : '0' . $arrFecha['tm_mday']);
		
    	$Fe_Hoy = $iDay . '/' . $iMonth . '/' . $iYear;
    	
        $arrCabecera = array (
            "No_Almacen" => $No_Almacen,
            "Fe_Inicio" => ($iTipoFecha === '0' ? $Fe_Hoy : ToDateBD($Fe_Inicio)),
            "Fe_Fin" => ($iTipoFecha === '0' ? $Fe_Hoy : ToDateBD($Fe_Fin)),
            "iAgruparxCategoria" => $iAgruparxCategoria,
        );
        
		ob_start();
		$file = $this->load->view('Logistica/informes_logistica/pdf/ConsistenciaStockPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($ID_Empresa, $ID_Almacen, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia, $iAgruparxCategoria, $Nu_Estado_Item),
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
    
	public function sendReporteEXCEL($ID_Almacen, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $No_Almacen, $iIdSubFamilia, $iAgruparxCategoria, $Nu_Estado_Item){
        $this->load->library('Excel');
	    
        $ID_Empresa             = $this->user->ID_Empresa;
        $ID_Almacen             = $this->security->xss_clean($ID_Almacen);
        $iTipoFecha             = $this->security->xss_clean($iTipoFecha);
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $iTipoStock             = $this->security->xss_clean($iTipoStock);
        $ID_Familia               = $this->security->xss_clean($ID_Familia);
        $ID_Producto            = $this->security->xss_clean($ID_Producto);
        $No_Almacen             = $this->security->xss_clean($No_Almacen);
        $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
        $iAgruparxCategoria = $this->security->xss_clean($iAgruparxCategoria);
        $Nu_Estado_Item = $this->security->xss_clean($Nu_Estado_Item);
        
		$arrFecha = localtime(time(),true);

		$iYear = (1900 + $arrFecha['tm_year']);
		$iMonth = (strlen(1 + $arrFecha['tm_mon']) > 1 ? $arrFecha['tm_mon'] : '0' . (1 + $arrFecha['tm_mon']));
		$iDay = (strlen($arrFecha['tm_mday']) > 1 ? $arrFecha['tm_mday'] : '0' . $arrFecha['tm_mday']);
		
    	$Fe_Hoy = $iDay . '/' . $iMonth . '/' . $iYear;
    	
		$fileNameExcel = "Consistencia_Stock_" . ($iTipoFecha === '0' ? $Fe_Hoy : ToDateBD($Fe_Inicio)) . "_" . ($iTipoFecha === '0' ? $Fe_Hoy : ToDateBD($Fe_Fin)) . ".xls";
		
        $data = $this->getReporte($ID_Empresa, $ID_Almacen, $iTipoFecha, $Fe_Inicio, $Fe_Fin, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia, $iAgruparxCategoria, $Nu_Estado_Item);

	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Consistencia de Stock');
        
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
        ->setCellValue('F1', $No_Almacen)
        ->setCellValue('B2', 'Consistencia de Stock')
        ->setCellValue('B3', 'Desde: ' . ($iTipoFecha === '0' ? $Fe_Hoy : ToDateBD($Fe_Inicio)) . ' Hasta: ' . ($iTipoFecha === '0' ? $Fe_Hoy : ToDateBD($Fe_Fin)));
        
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B2:H2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B3:H3');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        
	    //Header
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");

        $objPHPExcel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($BStyle_top);
        $objPHPExcel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
        
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'Código')
        ->setCellValue('B5', 'Nombre')
        ->setCellValue('C5', 'Stock')
        ->setCellValue('D5', 'Kardex')
        ->setCellValue('E5', 'Categoría')
        ->setCellValue('F5', 'SubCategoría')
        ->setCellValue('G5', 'Marca')
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;
        
        if ( count($data) > 0) {
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_linea_cantidad = 0.00; $sum_almacen_linea_cantidad_kardex = 0.00;
            $ID_Familia = '';
            $counter = 0;
            $sum_linea_cantidad = 0.000000;
            $sum_linea_cantidad_kardex = 0.000000;
            $sum_cantidad = 0.000000;
            $sum_cantidad_kardex = 0.000000;
            foreach ($data as $row) {
                if (($ID_Familia != $row->ID_Familia || $ID_Almacen != $row->ID_Almacen) && $iAgruparxCategoria==1) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('B' . $fila, 'Total Categoría')
                        ->setCellValue('C' . $fila, numberFormat($sum_linea_cantidad, 3, '.', ','))
                        ->setCellValue('D' . $fila, numberFormat($sum_linea_cantidad_kardex, 3, '.', ','));
                        
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);
                        
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
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                        
                        $sum_linea_cantidad = 0.000000;
                        $sum_linea_cantidad_kardex = 0.000000;
                    }
                    
                    if ($ID_Almacen != $row->ID_Almacen) {
                        if ($counter_almacen != 0) {
                            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                            ->setCellValue('B' . $fila, 'Total Almacén')
                            ->setCellValue('C' . $fila, numberFormat($sum_almacen_linea_cantidad, 3, '.', ','))
                            ->setCellValue('D' . $fila, numberFormat($sum_almacen_linea_cantidad_kardex, 3, '.', ','));
                            
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);
                            
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
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
                        
                            $fila++;
                            
                            $sum_almacen_linea_cantidad = 0.000000;
                            $sum_almacen_linea_cantidad_kardex = 0.000000;
                        }
                    
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'Almacén')
                        ->setCellValue('B' . $fila, $row->No_Almacen)
                        ;
                        
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B' . $fila . ':' . 'G' . $fila);
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'G' . $fila)->applyFromArray($style_align_left);
                        
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'D' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'F2F5F5')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
                        
                        $ID_Almacen = $row->ID_Almacen;
                        $fila++;
                    }//if almacen
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Categoría')
                    ->setCellValue('B' . $fila, $row->No_Familia)
                    ;
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B' . $fila . ':' . 'G' . $fila);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'G' . $fila)->applyFromArray($style_align_left);
                    
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('A' . $fila . ':' . 'D' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F2F5F5')
                            )
                        )
                    );
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
                    
                    $ID_Familia = $row->ID_Familia;
                    $fila++;
                }
                
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $fila, $row->Nu_Codigo_Barra, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $fila, $row->No_Producto, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $fila, $row->No_Unidad_Medida, PHPExcel_Cell_DataType::TYPE_STRING);
                
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('C' . $fila, numberFormat($row->Qt_Producto, 3, '.', ','))
                ->setCellValue('D' . $fila, numberFormat($row->Qt_Producto_Kardex, 3, '.', ','))
                ;
                
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $fila, $row->No_Familia, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $fila, $row->No_Sub_Familia, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $fila, $row->No_Marca, PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'B' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':' . 'F' . $fila)->applyFromArray($style_align_left);
            
                $sum_linea_cantidad += $row->Qt_Producto;
                $sum_almacen_linea_cantidad += $row->Qt_Producto;
                $sum_cantidad += $row->Qt_Producto;

                $sum_linea_cantidad_kardex += $row->Qt_Producto_Kardex;
                $sum_almacen_linea_cantidad_kardex += $row->Qt_Producto_Kardex;
                $sum_cantidad_kardex += $row->Qt_Producto_Kardex;

                $fila++;
                $counter++;
                $counter_almacen++;
            }
            
            if($iAgruparxCategoria==1) {
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('B' . $fila, 'Total Categoría')
                ->setCellValue('C' . $fila, numberFormat($sum_linea_cantidad, 3, '.', ','))
                ->setCellValue('D' . $fila, numberFormat($sum_linea_cantidad_kardex, 3, '.', ','));
                
                $objPHPExcel->getActiveSheet()->getStyle('b' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);

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
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
                
                $fila++;
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('B' . $fila, 'Total Almacén')
                ->setCellValue('C' . $fila, numberFormat($sum_almacen_linea_cantidad, 3, '.', ','))
                ->setCellValue('D' . $fila, numberFormat($sum_almacen_linea_cantidad_kardex, 3, '.', ','));
                
                $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);

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
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
            }
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('B' . $fila, 'Total General')
            ->setCellValue('C' . $fila, numberFormat($sum_cantidad, 3, '.', ','))
            ->setCellValue('D' . $fila, numberFormat($sum_cantidad, 3, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_right);

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
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->getFont()->setBold(true);
        }
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}
