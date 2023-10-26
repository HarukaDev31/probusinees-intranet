<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class DespachoPOSController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PuntoVenta/DespachoPOSModel');
		$this->load->model('HelperModel');
		$this->load->model('DocumentoElectronicoModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('PuntoVenta/DespachoPOSView');
			$this->load->view('footer', array("js_despacho_pos" => true));
		}
	}

    private function getReporte($arrParams){
        $arrResponseModal = $this->DespachoPOSModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();            
            $fSubTotal = 0.00;
            $fIGV = 0.00;
            $fDescuento = 0.00;
            $fTotal = 0.00;
            $sAccionVer='ver';
            $sAccionImprimir='imprimir';
            $sVacio='mostrar-img-logo_punto_venta';
            $h='';
            $m='';
            $s='';
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Empresa'] = $row->ID_Empresa;
                $rows['ID_Organizacion'] = $row->ID_Organizacion;
                $rows['ID_Almacen'] = $row->ID_Almacen;
                $rows['ID_Moneda'] = $row->ID_Moneda;
                $rows['ID_Lista_Precio_Cabecera'] = $row->ID_Lista_Precio_Cabecera;
                $rows['ID_Transporte_Delivery'] = $row->ID_Transporte_Delivery;
                $rows['Fe_Emision'] = $row->Fe_Emision;
                $rows['Fe_Emision_Hora_Hidden'] = $row->Fe_Emision_Hora;
                $rows['Fe_Entrega'] = ToDateBD($row->Fe_Entrega);
                $rows['ID_Documento_Cabecera'] = $row->ID_Documento_Cabecera;
                
                $arrEstadoRecepcion = $this->HelperModel->obtenerEstadoRecepcionArray($row->Nu_Tipo_Recepcion);
                $rows['No_Tipo_Recepcion'] = $arrEstadoRecepcion['No_Estado'];
                /*
                $sTipoRecepcion = explode('-', $row->No_Tipo_Recepcion);
                $rows['No_Tipo_Recepcion'] = $sTipoRecepcion[1];
                */

                $rows['No_Delivery'] = (!empty($row->No_Delivery) ? $row->No_Delivery : '');
                $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
                
                $sMensajeVencimiento = '';
                if ( dateNow('fecha') > $row->Fe_Entrega ) {
                    $dActual=date_create($row->Fe_Entrega);
                    $dEmision=date_create(dateNow('fecha'));
                    $iDiferenciaDias=date_diff($dActual,$dEmision);
                    $sMensajeVencimiento = $iDiferenciaDias->format("%a") . ' día(s)';
                    $sMensajeVencimiento = '<span class="label label-danger">' . $sMensajeVencimiento . '</span>';
                }                
                $rows['Dias_Transcurridos'] = $sMensajeVencimiento;

                /*
                $h = diferenciaFechasMultipleFormato($row->Fe_Emision_Hora, dateNow('fecha_hora'), 'horas');
                $m = diferenciaFechasMultipleFormato($row->Fe_Emision_Hora, dateNow('fecha_hora'), 'minutos');
                $s = diferenciaFechasMultipleFormato($row->Fe_Emision_Hora, dateNow('fecha_hora'), 'segundos');
                $rows['Fe_Transcurrida'] = ($h > 0 ? $h . ' H ' : '') . ($m > 0 ? $m . ' min ' : '') . $s . ' seg';
                */
                $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['Documento'] = $row->No_Tipo_Documento_Breve . '-' . $row->ID_Serie_Documento . '-' . $row->ID_Numero_Documento;

                $rows['ID_Entidad'] = $row->ID_Entidad;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['Nu_Celular_Entidad'] = $row->Nu_Celular_Entidad;
                $rows['Txt_Direccion_Entidad'] = $row->Txt_Direccion_Entidad;
                $rows['No_Signo'] = $row->No_Signo;
                $rows['Ss_Total'] = $row->Ss_Total;
                $rows['Nu_Estado'] = $row->Nu_Estado;
                $rows['No_Estado'] = $row->No_Estado;
                $rows['No_Class_Estado'] = $row->No_Class_Estado;
                
                $rows['Nu_Estado_Despacho_Pos'] = $row->Nu_Estado_Despacho_Pos;
                $arrEstadoDespacho = $this->HelperModel->obtenerEstadoDespachoArray($row->Nu_Estado_Despacho_Pos);
                $sNombreEstado = '';
                $sClaseEstado = '';
                if(!empty($arrEstadoDespacho)) {
                    $sNombreEstado = $arrEstadoDespacho['No_Estado'];
                    $sClaseEstado = $arrEstadoDespacho['No_Class_Estado'];
                }
                $rows['No_Estado_Delivery'] = $sNombreEstado;
                $rows['No_Class_Estado_Delivery'] = $sClaseEstado;

                $dropdownEstadoPedido = '<div class="btn-group">
                    <button style="width: 100%;" class="btn btn-' . $sClaseEstado . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $sNombreEstado . '
                    <span class="caret"></span></button>
                    <ul class="dropdown-menu" style="width: 100%; position: sticky;">';
                    if ($row->Nu_Estado_Despacho_Pos!='0')
                        $dropdownEstadoPedido .= '<li><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="estadoPedido(\'' . $row->ID_Documento_Cabecera . '\', 0);">Pendiente</a></li>';
                    if ($row->Nu_Estado_Despacho_Pos!='1')
                        $dropdownEstadoPedido .= '<li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="estadoPedido(\'' . $row->ID_Documento_Cabecera . '\', 1);">Preparando</a></li>';
                    if ($row->Nu_Estado_Despacho_Pos!='2')
                        $dropdownEstadoPedido .= '<li><a alt="Enviado" title="Enviado" href="javascript:void(0)" onclick="estadoPedido(\'' . $row->ID_Documento_Cabecera . '\', 2);">Enviado</a></li>';
                    if ($row->Nu_Estado_Despacho_Pos!='3')
                        $dropdownEstadoPedido .= '<li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="estadoPedido(\'' . $row->ID_Documento_Cabecera . '\', 3);">Entregado</a></li>';
                    if ($row->Nu_Estado_Despacho_Pos!='4')
                        $dropdownEstadoPedido .= '<li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="estadoPedido(\'' . $row->ID_Documento_Cabecera . '\', 4);">Rechazado</a></li>';
                    $dropdownEstadoPedido .= '
                    </ul>
                </div>';
                $rows['No_Estado_Delivery'] = $dropdownEstadoPedido;

                //Guía
                $rows['No_Tipo_Documento_Breve_Guia'] = $row->No_Tipo_Documento_Breve_Guia;
                $rows['ID_Serie_Documento_Guia'] = $row->ID_Serie_Documento_Guia;
                $rows['ID_Numero_Documento_Guia'] = $row->ID_Numero_Documento_Guia;

                if ( !empty($rows['No_Tipo_Documento_Breve_Guia']) ) {
                    $rows['sAccionVer'] = '<button type="button" class="btn btn-xs btn-link" alt="Ver Guía" title="Ver Guía" href="javascript:void(0)" onclick="formatoImpresionTicketGuia(\'' . $sAccionVer . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-list-alt fa-2x" aria-hidden="true"></i></button>';
                    $rows['sAccionImprimir'] = '<button type="button" class="btn btn-xs btn-link" alt="Imprimir Guía" title="Imprimir Guía" href="javascript:void(0)" onclick="formatoImpresionTicketGuia(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>';
                } else {
                    $rows['sAccionVer'] = '<button type="button" class="btn btn-xs btn-link" alt="Ver comprobante" title="Ver comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionVer . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $row->Txt_Url_PDF . '\')"><i class="fa fa-list-alt fa-2x" aria-hidden="true"></i></button>';
                    $rows['sAccionImprimir'] = '<button type="button" class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $row->Txt_Url_PDF . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>';
                }
                $rows['Nu_Tipo_Recepcion'] = $row->Nu_Tipo_Recepcion;
                $data[] = (object)$rows;
            }
            return array(
                'sStatus' => 'success',
                'arrData' => $data,
                'arrDataTotal' => $arrResponseModal['arrDataTotal'],
            );
        } else {
            return $arrResponseModal;
        }
    }
    
	public function sendReporte(){
        $arrParams = array(
            'iTipoConsultaFecha'  => $this->input->post('iTipoConsultaFecha'),
            'Fe_Inicio'  => $this->input->post('Fe_Inicio'),
            'Fe_Fin'  => $this->input->post('Fe_Fin'),
            'ID_Tipo_Documento'  => $this->input->post('ID_Tipo_Documento'),
            'ID_Serie_Documento'  => $this->input->post('ID_Serie_Documento'),
            'ID_Numero_Documento'  => $this->input->post('ID_Numero_Documento'),
            'Nu_Estado_Documento'  => $this->input->post('Nu_Estado_Documento'),
            'iIdCliente' => $this->input->post('iIdCliente'),
            'sNombreCliente' => $this->input->post('sNombreCliente'),
            'iTipoRecepcionCliente' => $this->input->post('iTipoRecepcionCliente'),
            'iEstadoPago' => $this->input->post('iEstadoPago'),
            'iTipoRecepcionClienteEstado' => $this->input->post('iTipoRecepcionClienteEstado'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($iTipoConsultaFecha, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iTipoRecepcionCliente, $iEstadoPago){
        $this->load->library('FormatoLibroSunatPDF');
        
        $iTipoConsultaFecha = $this->security->xss_clean($iTipoConsultaFecha);
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
        $iIdCliente = $this->security->xss_clean($iIdCliente);
        $sNombreCliente = $this->security->xss_clean($sNombreCliente);
        $iTipoRecepcionCliente = $this->security->xss_clean($iTipoRecepcionCliente);
        $iEstadoPago = $this->security->xss_clean($iEstadoPago);
        
		$fileNamePDF = "ventas_x_punto_venta" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
        $pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
        );
        
        $arrParams = array(
            'iTipoConsultaFecha' => $iTipoConsultaFecha,
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'ID_Tipo_Documento' => $ID_Tipo_Documento,
            'ID_Serie_Documento' => $ID_Serie_Documento,
            'ID_Numero_Documento' => $ID_Numero_Documento,
            'Nu_Estado_Documento' => $Nu_Estado_Documento,
            'iIdCliente' => $iIdCliente,
            'sNombreCliente' => $sNombreCliente,
            'iTipoRecepcionCliente' => $iTipoRecepcionCliente,
        );

		ob_start();
		$file = $this->load->view('PuntoVenta/pdf/VentaPuntoVentaViewPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('Laesystems - Ventas por Punto de Venta');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($iTipoConsultaFecha, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iTipoRecepcionCliente, $iEstadoPago){
        $this->load->library('Excel');
	    
        $iTipoConsultaFecha = $this->security->xss_clean($iTipoConsultaFecha);
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
        $iIdCliente = $this->security->xss_clean($iIdCliente);
        $sNombreCliente = $this->security->xss_clean($sNombreCliente);
        $iTipoRecepcionCliente = $this->security->xss_clean($iTipoRecepcionCliente);
        $iEstadoPago = $this->security->xss_clean($iEstadoPago);
        
		$fileNameExcel = "ventas_x_punto_venta" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Ventas por Punto de Venta');
        
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
        ->setCellValue('C2', 'Informe de Ventas por Punto de Venta')
        ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:J2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:J3');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("25");

        $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($BStyle_top);
        
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
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($style_align_center);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'F. Emisión')
        ->setCellValue('B5', 'Cajero')
        ->setCellValue('C5', 'Recepción')
        ->setCellValue('D5', 'Tipo')
        ->setCellValue('E5', 'Serie')
        ->setCellValue('F5', 'Número')
        ->setCellValue('G5', 'Cliente')
        ->setCellValue('H5', 'T.C.')
        ->setCellValue('I5', 'Total S/')
        ->setCellValue('J5', 'Total M. Ex.')
        ->setCellValue('K5', 'Saldo')
        ->setCellValue('L5', 'Pago')
        ->setCellValue('M5', 'Estado')
        ;
        
        $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;

        $arrParams = array(
            'iTipoConsultaFecha'  => $iTipoConsultaFecha,
            'Fe_Inicio'  => $Fe_Inicio,
            'Fe_Fin'  => $Fe_Fin,
            'ID_Tipo_Documento'  => $ID_Tipo_Documento,
            'ID_Serie_Documento'  => $ID_Serie_Documento,
            'ID_Numero_Documento'  => $ID_Numero_Documento,
            'Nu_Estado_Documento'  => $Nu_Estado_Documento,
            'iIdCliente' => $iIdCliente,
            'sNombreCliente' => $sNombreCliente,
            'iTipoRecepcionCliente' => $iTipoRecepcionCliente,
            'iEstadoPago' => $iEstadoPago,
        );
        $arrData = $this->getReporte($arrParams);
        
        if ( $arrData['sStatus'] == 'success' ) {
            $subtotal_s = 0.00; $descuento_s = 0.00; $igv_s = 0.00; $total_s = 0.00; $total_d = 0.00;
            $sum_general_subtotal_s=0.00; $sum_general_igv_s=0.00; $sum_general_descuento_s=0.00; $sum_general_total_s=0.00; $sum_general_total_d=0.00;
            foreach($arrData['arrData'] as $row) {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'F' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':' . 'M' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
                ->setCellValue('B' . $fila, $row->No_Empleado)
                ->setCellValue('C' . $fila, $row->No_Tipo_Recepcion)
                ->setCellValue('D' . $fila, $row->No_Tipo_Documento_Breve)
                ->setCellValue('E' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('F' . $fila, $row->ID_Numero_Documento)
                ->setCellValue('G' . $fila, $row->No_Entidad)
                ->setCellValue('H' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
                ->setCellValue('I' . $fila, numberFormat($row->Ss_Total, 3, '.', ','))
                ->setCellValue('J' . $fila, numberFormat($row->Ss_Total_Extranjero, 3, '.', ','))
                ->setCellValue('K' . $fila, numberFormat($row->Ss_Total_Saldo, 3, '.', ','))
                ->setCellValue('L' . $fila, $row->No_Estado_Pago)
                ->setCellValue('M' . $fila, $row->No_Estado)
                ;
                $sum_general_total_s += $row->Ss_Total;
                $sum_general_total_d += $row->Ss_Total_Extranjero;

                $fila++;
            }// /. for each arrData
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Total')
            ->setCellValue('I' . $fila, numberFormat($sum_general_total_s, 2, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_general_total_d, 2, '.', ','));
            
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
            ->setCellValue('A' . $fila, $arrData['sMessage']);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':' . 'M' . $fila);
        }// if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
	
	public function estadoPedido($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->DespachoPOSModel->estadoPedido($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}

	public function generarGuia(){
        echo json_encode($this->DespachoPOSModel->generarGuia($this->input->post()));
	}
}
