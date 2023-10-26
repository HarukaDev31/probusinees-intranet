<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class VentasxFamiliaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/informes_venta/VentasxFamiliaModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/informes_venta/VentasxFamiliaView');
			$this->load->view('footer', array("js_ventas_x_familia" => true));
		}
	}	
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->VentasxFamiliaModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $fCantidad = 0.00;
            $fTotal = 0.00;
            $fTotalExtranjera = 0.00;
            $sAccionVer='ver';
            $sAccionImprimir='imprimir';
            $sVacio='';            
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Almacen'] = $row->ID_Almacen;
                $rows['No_Almacen'] = $row->No_Almacen;
                $rows['ID_Familia'] = $row->ID_Familia;
                $rows['No_Familia'] = $row->No_Familia;
                $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
                $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['No_Unidad_Medida'] = $row->No_Unidad_Medida;
                $rows['No_Producto'] = $row->No_Producto;
                $rows['No_Signo'] = $row->No_Signo;
                //Obtener tipo de cambio solo si no es moneda nacional PEN = 1 Nu_Valor_FE_Moneda
                $rows['Ss_Tipo_Cambio'] = 0.00;
                if($row->Nu_Valor_FE_Moneda!=1) {
                    $arrParamsMoneda = array(
                        "ID_Empresa" => $row->ID_Empresa,
                        "ID_Moneda" => $row->ID_Moneda,
                        "Fe_Emision" => $row->Fe_Emision
                    );
                    $arrTipoCambio = $this->HelperModel->obtenerTipoCambio($arrParamsMoneda);
                    if(is_object($arrTipoCambio))
                        $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $arrTipoCambio->Ss_Venta_Oficial : $arrTipoCambio->Ss_Venta_Oficial);
                }

                //OBTENER ITEMS GRATUITOS EN DETALLE DE VENTA
                /*
                if ($row->Ss_Total > 0.00) {
                    $arrParamsGratuita = array(
                        'ID_Documento_Cabecera' => $row->ID_Documento_Cabecera,
                        'ID_Producto' => $row->ID_Producto
                    );
                    $objImporteDetalleDocumento = $this->HelperModel->obtenerImporteDetalleDocumentoGratuitaxIdItem($arrParamsGratuita);
                    if(is_object($objImporteDetalleDocumento)) {
                        $row->Qt_Producto -= $objImporteDetalleDocumento->Qt_Producto;
                        $row->Ss_Subtotal -= $objImporteDetalleDocumento->Ss_Subtotal;
                        $row->Ss_Impuesto -= $objImporteDetalleDocumento->Ss_Impuesto;
                        $row->Ss_Total -= $objImporteDetalleDocumento->Ss_Total;
                    }
                }
                */

                $rows['Qt_Producto'] = ($row->ID_Tipo_Documento != 5 ? $row->Qt_Producto : -$row->Qt_Producto);
                $rows['Ss_Precio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Precio : -$row->Ss_Precio);

                //OBTENER CANTIDAD DE REGISTROS X DOCUMENTO DE VENTA DETALLE
                $arrCantidadItemDocumentoVentaDetalle = $this->HelperModel->getCantidadItemDocumentoVentaDetalle($row->ID_Documento_Cabecera);
                if($arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento > 0 && $row->Ss_Descuento > 0.00) {
                    $row->Ss_Descuento_Global = ($row->Ss_Descuento_Global / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);
                    $row->Ss_Descuento = ($row->Ss_Descuento / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);
                    $row->Ss_Descuento_Impuesto = ($row->Ss_Descuento_Impuesto / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);

                    $row->Ss_Subtotal = $row->Ss_Subtotal - $row->Ss_Descuento;
                    $row->Ss_Impuesto = $row->Ss_Impuesto - $row->Ss_Descuento_Impuesto;
                    $row->Ss_Total = $row->Ss_Total - $row->Ss_Descuento_Global;
                }
                
                if ( $row->ID_Tipo_Documento != 2 )
                    $rows['Ss_Subtotal'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Subtotal : -$row->Ss_Subtotal);
                else
                    $rows['Ss_Subtotal'] = $row->Ss_Total;

                if ( $row->ID_Tipo_Documento != 2 )
                    $rows['Ss_Impuesto'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Impuesto : -$row->Ss_Impuesto);
                else
                    $rows['Ss_Impuesto'] = 0;

                $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total : -$row->Ss_Total);

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
            'iIdMoneda' => $this->input->post('iIdMoneda'),
            'iIdFamilia' => $this->input->post('iIdFamilia'),
            'iIdItem' => $this->input->post('iIdItem'),
            'sNombreItem' => $this->input->post('sNombreItem'),
            'iIdSubFamilia' => $this->input->post('iIdSubFamilia'),
            'ID_Almacen' => $this->input->post('ID_Almacen'),
            'Nu_Agrupar_Empresa' => $this->input->post('Nu_Agrupar_Empresa'),
            "iFiltroBusquedaNombre" => $this->input->post('iFiltroBusquedaNombre'),
            "ID_Marca" => $this->input->post('ID_Marca'),
            "ID_Variante_Item" => $this->input->post('ID_Variante_Item'),
            "ID_Variante_Item_Detalle_1" => $this->input->post('ID_Variante_Item_Detalle_1'),
            "ID_Variante_Item2" => $this->input->post('ID_Variante_Item2'),
            "ID_Variante_Item_Detalle_2" => $this->input->post('ID_Variante_Item_Detalle_2'),
            "ID_Variante_Item3" => $this->input->post('ID_Variante_Item3'),
            "ID_Variante_Item_Detalle_3" => $this->input->post('ID_Variante_Item_Detalle_3'),
            'Nu_Tipo_Impuesto' => $this->input->post('Nu_Tipo_Impuesto')
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($Fe_Inicio, $Fe_Fin, $iIdMoneda, $iIdFamilia, $iIdItem, $sNombreItem, $iIdSubFamilia, $ID_Almacen, $Nu_Agrupar_Empresa,
    $iFiltroBusquedaNombre,
    $ID_Marca,
    $ID_Variante_Item,
    $ID_Variante_Item_Detalle_1,
    $ID_Variante_Item2,
    $ID_Variante_Item_Detalle_2,
    $ID_Variante_Item3,
    $ID_Variante_Item_Detalle_3, $Nu_Tipo_Impuesto
    ){
        $this->load->library('FormatoLibroSunatPDF');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdMoneda = $this->security->xss_clean($iIdMoneda);
        $iIdFamilia = $this->security->xss_clean($iIdFamilia);
        $iIdItem = $this->security->xss_clean($iIdItem);
        $sNombreItem = $this->security->xss_clean($sNombreItem);
        $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        $Nu_Agrupar_Empresa = $this->security->xss_clean($Nu_Agrupar_Empresa);
        $iFiltroBusquedaNombre   = $this->security->xss_clean($iFiltroBusquedaNombre);
        $ID_Marca   = $this->security->xss_clean($ID_Marca);
        $ID_Variante_Item   = $this->security->xss_clean($ID_Variante_Item);
        $ID_Variante_Item_Detalle_1   = $this->security->xss_clean($ID_Variante_Item_Detalle_1);
        $ID_Variante_Item2   = $this->security->xss_clean($ID_Variante_Item2);
        $ID_Variante_Item_Detalle_2   = $this->security->xss_clean($ID_Variante_Item_Detalle_2);
        $ID_Variante_Item3   = $this->security->xss_clean($ID_Variante_Item3);
        $ID_Variante_Item_Detalle_3   = $this->security->xss_clean($ID_Variante_Item_Detalle_3);
        $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
        
		$fileNamePDF = "reporte_ventas_x_familia_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
        );
        
        $arrParams = array(
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'iIdMoneda' => $iIdMoneda,
            'iIdFamilia' => $iIdFamilia,
            'iIdItem' => $iIdItem,
            'sNombreItem' => $sNombreItem,
            'iIdSubFamilia' => $iIdSubFamilia,
            'ID_Almacen' => $ID_Almacen,
            'Nu_Agrupar_Empresa' => $Nu_Agrupar_Empresa,
            "iFiltroBusquedaNombre" => $iFiltroBusquedaNombre,
            "ID_Marca" => $ID_Marca,
            "ID_Variante_Item" => $ID_Variante_Item,
            "ID_Variante_Item_Detalle_1" => $ID_Variante_Item_Detalle_1,
            "ID_Variante_Item2" => $ID_Variante_Item2,
            "ID_Variante_Item_Detalle_2" => $ID_Variante_Item_Detalle_2,
            "ID_Variante_Item3" => $ID_Variante_Item3,
            "ID_Variante_Item_Detalle_3" => $ID_Variante_Item_Detalle_3,
            'Nu_Tipo_Impuesto' => $Nu_Tipo_Impuesto
        );

		ob_start();
		$file = $this->load->view('Ventas/informes_venta/pdf/VentasxFamiliaViewPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('laesystems');
		$pdf->SetTitle('laesystems - Informes de Ventas x Familia');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $iIdMoneda, $iIdFamilia, $iIdItem, $sNombreItem, $iIdSubFamilia, $ID_Almacen, $Nu_Agrupar_Empresa,
    $iFiltroBusquedaNombre,
    $ID_Marca,
    $ID_Variante_Item,
    $ID_Variante_Item_Detalle_1,
    $ID_Variante_Item2,
    $ID_Variante_Item_Detalle_2,
    $ID_Variante_Item3,
    $ID_Variante_Item_Detalle_3, $Nu_Tipo_Impuesto
    ){
        $this->load->library('Excel');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdMoneda = $this->security->xss_clean($iIdMoneda);
        $iIdFamilia = $this->security->xss_clean($iIdFamilia);
        $iIdItem = $this->security->xss_clean($iIdItem);
        $sNombreItem = $this->security->xss_clean($sNombreItem);
        $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        $Nu_Agrupar_Empresa = $this->security->xss_clean($Nu_Agrupar_Empresa);
        $iFiltroBusquedaNombre   = $this->security->xss_clean($iFiltroBusquedaNombre);
        $ID_Marca   = $this->security->xss_clean($ID_Marca);
        $ID_Variante_Item   = $this->security->xss_clean($ID_Variante_Item);
        $ID_Variante_Item_Detalle_1   = $this->security->xss_clean($ID_Variante_Item_Detalle_1);
        $ID_Variante_Item2   = $this->security->xss_clean($ID_Variante_Item2);
        $ID_Variante_Item_Detalle_2   = $this->security->xss_clean($ID_Variante_Item_Detalle_2);
        $ID_Variante_Item3   = $this->security->xss_clean($ID_Variante_Item3);
        $ID_Variante_Item_Detalle_3   = $this->security->xss_clean($ID_Variante_Item_Detalle_3);
        $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
        
		$fileNameExcel = "reporte_ventas_x_familia_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Informe de Ventas x Familia');
        
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
        ->setCellValue('D2', 'Informe de Ventas por Familia')
        ->setCellValue('D3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('D2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D2:J2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D3:J3');
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("60");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("25");

        $objPHPExcel->getActiveSheet()->getStyle('A5:O5')->applyFromArray($BStyle_top);
        
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
        $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O5')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:O5')->applyFromArray($BStyle_bottom);

        $objPHPExcel->getActiveSheet()->getStyle('A5:O5')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:O5')->applyFromArray($style_align_center);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'F. Emisión')
        ->setCellValue('B5', 'Tipo')
        ->setCellValue('C5', 'Serie')
        ->setCellValue('D5', 'Número')
        ->setCellValue('E5', 'Cliente')
        ->setCellValue('F5', 'M')
        ->setCellValue('G5', 'T.C.')
        ->setCellValue('H5', 'U.M.')
        ->setCellValue('I5', 'Item')
        ->setCellValue('J5', 'Cantidad')
        ->setCellValue('K5', 'Precio')
        ->setCellValue('L5', 'SubTotal')
        ->setCellValue('M5', 'Impuesto')
        ->setCellValue('N5', 'Total')
        ->setCellValue('O5', 'Estado')
        ;
        
        $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;

        $arrParams = array(
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'iIdMoneda' => $iIdMoneda,
            'iIdFamilia' => $iIdFamilia,
            'iIdItem' => $iIdItem,
            'sNombreItem' => $sNombreItem,
            'iIdSubFamilia' => $iIdSubFamilia,
            'ID_Almacen' => $ID_Almacen,
            'Nu_Agrupar_Empresa' => $Nu_Agrupar_Empresa,
            "iFiltroBusquedaNombre" => $iFiltroBusquedaNombre,
            "ID_Marca" => $ID_Marca,
            "ID_Variante_Item" => $ID_Variante_Item,
            "ID_Variante_Item_Detalle_1" => $ID_Variante_Item_Detalle_1,
            "ID_Variante_Item2" => $ID_Variante_Item2,
            "ID_Variante_Item_Detalle_2" => $ID_Variante_Item_Detalle_2,
            "ID_Variante_Item3" => $ID_Variante_Item3,
            "ID_Variante_Item_Detalle_3" => $ID_Variante_Item_Detalle_3,
            'Nu_Tipo_Impuesto' => $Nu_Tipo_Impuesto
        );
        $arrData = $this->getReporte($arrParams);
        
        if ( $arrData['sStatus'] == 'success' ) {
            $counter = 0; $ID_Familia = ''; $cantidad = 0.00; $subtotal = 0.00; $impuesto = 0.00; $total_s = 0.00;
            $sum_cantidad = 0.00; $sum_subtotal = 0.00; $sum_impuesto = 0.00; $sum_total_s = 0.00;
            $sum_general_cantidad = 0.00; $sum_general_subtotal = 0.00; $sum_general_impuesto = 0.00; $sum_general_total_s = 0.00;
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_cantidad = 0.000000; $sum_almacen_subtotal = 0.00; $sum_impuesto = 0.00; $sum_almacen_total_s = 0.00;
            foreach($arrData['arrData'] as $row) {
                if ($ID_Familia != $row->ID_Familia || $ID_Almacen != $row->ID_Almacen) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('H' . $fila, 'Total')
                        ->setCellValue('I' . $fila, numberFormat($sum_cantidad, 3, '.', ','))
                        ->setCellValue('J' . $fila, numberFormat($sum_subtotal, 2, '.', ','))
                        ->setCellValue('J' . $fila, numberFormat($sum_impuesto, 2, '.', ','))
                        ->setCellValue('J' . $fila, numberFormat($sum_total_s, 2, '.', ','));
                        
                        $sum_cantidad = 0.000000;
                        $sum_subtotal = 0.00;
                        $sum_impuesto = 0.00;
                        $sum_total_s = 0.00;
                        
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                        
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
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                    }
                    
                    if ($ID_Almacen != $row->ID_Almacen) {
                        if ($counter_almacen != 0) {
                            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                            ->setCellValue('I' . $fila, 'Total')
                            ->setCellValue('J' . $fila, numberFormat($sum_almacen_cantidad, 3, '.', ','))
                            ->setCellValue('L' . $fila, numberFormat($sum_almacen_subtotal, 2, '.', ','))
                            ->setCellValue('M' . $fila, numberFormat($sum_almacen_impuesto, 2, '.', ','))
                            ->setCellValue('N' . $fila, numberFormat($sum_almacen_total_s, 2, '.', ','));
                            
                            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                                        
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
                            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
                        
                            $fila++;
                            
                            $sum_almacen_cantidad = 0.000000;
                            $sum_almacen_subtotal = 0.00;
                            $sum_almacen_impuesto = 0.00;
                            $sum_almacen_total_s = 0.00;
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
                                        
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Familia')
                    ->setCellValue('B' . $fila, $row->No_Familia)
                    ;
                    
                    $ID_Familia = $row->ID_Familia;
                    
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
                    $fila++;
                }
                
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('O' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
                ->setCellValue('B' . $fila, $row->No_Tipo_Documento_Breve)
                ->setCellValue('C' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('D' . $fila, $row->ID_Numero_Documento)
                ->setCellValue('E' . $fila, $row->No_Entidad)
                ->setCellValue('F' . $fila, $row->No_Signo)
                ->setCellValue('G' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
                ->setCellValue('H' . $fila, $row->No_Unidad_Medida)
                ->setCellValue('I' . $fila, $row->No_Producto)
                ->setCellValue('J' . $fila, numberFormat($row->Qt_Producto, 3, '.', ','))
                ->setCellValue('K' . $fila, numberFormat($row->Ss_Precio, 2, '.', ','))
                ->setCellValue('L' . $fila, numberFormat($row->Ss_Subtotal, 2, '.', ','))
                ->setCellValue('M' . $fila, numberFormat($row->Ss_Impuesto, 2, '.', ','))
                ->setCellValue('N' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                ->setCellValue('O' . $fila, $row->No_Estado)
                ;
                $fila++;
              
                $sum_cantidad += $row->Qt_Producto;
                $sum_subtotal += $row->Ss_Subtotal;
                $sum_impuesto += $row->Ss_Impuesto;
                $sum_total_s += $row->Ss_Total;
              
                $sum_almacen_cantidad += $row->Qt_Producto;
                $sum_almacen_subtotal += $row->Ss_Subtotal;
                $sum_almacen_impuesto += $row->Ss_Impuesto;
                $sum_almacen_total_s += $row->Ss_Total;
                
                $sum_general_cantidad += $row->Qt_Producto;
                $sum_general_subtotal += $row->Ss_Subtotal;
                $sum_general_impuesto += $row->Ss_Impuesto;
                $sum_general_total_s += $row->Ss_Total;
                
                $counter++;
                $counter_almacen++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('I' . $fila, 'Total')
            ->setCellValue('J' . $fila, numberFormat($sum_cantidad, 3, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_subtotal, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_impuesto, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_total_s, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                        
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
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('I' . $fila, 'Total Almacén')
            ->setCellValue('J' . $fila, numberFormat($sum_almacen_cantidad, 3, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_almacen_subtotal, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_almacen_impuesto, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_almacen_total_s, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                        
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
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('I' . $fila, 'Total General')
            ->setCellValue('J' . $fila, numberFormat($sum_general_cantidad, 3, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_general_subtotal, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_general_impuesto, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_general_total_s, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                        
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
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->getFont()->setBold(true);
        } else {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('A' . $fila, $arrData['sMessage']);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':O' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
        }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
}
