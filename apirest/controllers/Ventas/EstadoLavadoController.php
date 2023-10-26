<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class EstadoLavadoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/EstadoLavadoModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/EstadoLavadoView');
			$this->load->view('footer', array("js_estado_lavado" => true));
		}
	}
	
	public function cambiarEstadoLavado(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		if ( !isset($_POST['arrIdDocumentoCabecera']) ){
			echo json_encode($response = array('sStatus' => 'danger', 'sMessage' => 'Debe seleccionar al menos 1 fila'));
			exit();
		} else {
			echo json_encode($this->EstadoLavadoModel->cambiarEstadoLavado($this->input->post()));
			exit();
        }
	}
	
	public function entregarPedidoLavado(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->EstadoLavadoModel->entregarPedidoLavado($this->input->post()));
	}
	
	public function enviarAlertaPedido(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->EstadoLavadoModel->enviarAlertaPedido($this->input->post()));
	}
	
	public function agregarNotaOrdenLavado(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->EstadoLavadoModel->agregarNotaOrdenLavado($this->input->post()));
	}
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->EstadoLavadoModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            $sAccionVer='ver';
            $sAccionImprimir='imprimir';
            $sVacio='mostrar-img-logo_punto_venta';
            $iCantidadMensajeNota = 1;
            $iIdCliente = 0;
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Empresa'] = $row->ID_Empresa;
                $rows['ID_Documento_Cabecera'] = $row->ID_Documento_Cabecera;
                $rows['No_Empleado'] = $row->No_Empleado;
                $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
                $rows['Fe_Entrega'] = ToDateBD($row->Fe_Entrega);
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['Nu_Celular_Entidad'] = $row->Nu_Celular_Entidad;                
                $rows['No_Signo'] = $row->No_Signo;
                $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total : -$row->Ss_Total);
                $rows['Ss_Total_Saldo'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total_Saldo : -$row->Ss_Total_Saldo);
                
                $rows['Txt_Glosa'] = $row->Txt_Glosa;

                $rows['Nu_Estado'] = $row->Nu_Estado;
                $rows['No_Estado'] = '';
                $rows['No_Class_Estado'] = '';

                $rows['Nu_Estado_Lavado'] = $row->Nu_Estado_Lavado;
                $rows['No_Entidad_Lavado'] = !empty($row->No_Entidad_Lavado) ? $row->No_Entidad_Lavado : '';
                
                $arrEstadoLavadoRecepcion = $this->HelperModel->obtenerEstadoLavadoRecepcionArray($row->Nu_Estado_Lavado_Recepcion_Cliente);
                $rows['No_Estado_Lavado_Recepcion_Cliente'] = $arrEstadoLavadoRecepcion['No_Estado'];
                $rows['No_Class_Estado_Lavado_Recepcion_Cliente'] = $arrEstadoLavadoRecepcion['No_Class_Estado'];

                $rows['Nu_Estado_Lavado_Recepcion_Cliente'] = $row->Nu_Estado_Lavado_Recepcion_Cliente;
                $sEntidadRecepcionLavado = '';
                if ( $row->Nu_Estado_Lavado_Recepcion_Cliente == 3 )
                    $sEntidadRecepcionLavado = (!empty($row->No_Entidad_Recepcion_Lavado) ? $row->No_Entidad_Recepcion_Lavado : 'La misma persona');
                $rows['No_Entidad_Recepcion_Lavado'] = $sEntidadRecepcionLavado;                
                $rows['ID_Documento_Medio_Pago'] = $row->ID_Documento_Medio_Pago;
                $rows['Txt_Garantia'] = $row->Txt_Garantia;
                $rows['Nu_Transporte_Lavanderia_Hoy'] = $row->Nu_Transporte_Lavanderia_Hoy;
                
                $rows['sAccionVer'] = '<button type="button" class="btn btn-xs btn-link" alt="Ver comprobante" title="Ver comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionVer . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-list-alt" aria-hidden="true"> Ver</i></button>';
                $rows['sAccionImprimir'] = '<button type="button" class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-print" aria-hidden="true"> Imprimir</i></button>';
                
                $arrParams = json_encode(array(
                    'sTipoCodificacion' => 'json',
                    'sAccion' => 'ver',
                    'iIdDocumentoCabecera' => $row->ID_Documento_Cabecera,
                    'sMostrarOcultarImagen' => 'mostrar-img-logo_punto_venta',
                ));
                $rows['sAccionVerComanda'] = '<button type="button" class="btn btn-xs btn-link" alt="Ver Comanda" title="Ver Comanda" href="javascript:void(0)" onclick=formatoImpresionTicketComandaLavado(\'' . $arrParams . '\')><i class="fa fa-list-alt" aria-hidden="true"> Ver Comanda</i></button>';
                
                $arrParams = json_encode(array(
                    'sTipoCodificacion' => 'json',
                    'sAccion' => 'imprimir',
                    'iIdDocumentoCabecera' => $row->ID_Documento_Cabecera,
                    'sMostrarOcultarImagen' => 'mostrar-img-logo_punto_venta',
                ));
                $rows['sAccionImprimirComanda'] = '<button type="button" class="btn btn-xs btn-link" alt="Imprimir Comanda" title="Imprimir Comanda" href="javascript:void(0)" onclick=formatoImpresionTicketComandaLavado(\'' . $arrParams . '\')><i class="fa fa-print" aria-hidden="true"> Imprimir Comanda</i></button>';
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
            'iEstado' => $this->input->post('iEstado'),
            'iIdCliente' => $this->input->post('iIdCliente'),
            'sNombreCliente' => $this->input->post('sNombreCliente'),
            'iTipoRecepcionCliente' => $this->input->post('iTipoRecepcionCliente'),
            'iEstadoLavado' => $this->input->post('iEstadoLavado'),
            'Nu_Transporte_Lavanderia_Hoy' => $this->input->post('Nu_Transporte_Lavanderia_Hoy'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($Fe_Inicio, $Fe_Fin, $iIdTipoDocumento, $iIdSerieDocumento, $iNumeroDocumento, $iEstado, $iIdCliente, $sNombreCliente, $iTipoRecepcionCliente, $iEstadoLavado, $Nu_Transporte_Lavanderia_Hoy){
        $this->load->library('FormatoLibroSunatPDF');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdTipoDocumento = $this->security->xss_clean($iIdTipoDocumento);
        $iIdSerieDocumento = $this->security->xss_clean($iIdSerieDocumento);
        $iNumeroDocumento = $this->security->xss_clean($iNumeroDocumento);
        $iEstado = $this->security->xss_clean($iEstado);
        $iIdCliente = $this->security->xss_clean($iIdCliente);
        $sNombreCliente = $this->security->xss_clean($sNombreCliente);
        $iTipoRecepcionCliente = $this->security->xss_clean($iTipoRecepcionCliente);
        $iEstadoLavado = $this->security->xss_clean($iEstadoLavado);
        $Nu_Transporte_Lavanderia_Hoy = $this->security->xss_clean($Nu_Transporte_Lavanderia_Hoy);
        
		$fileNamePDF = "estado_lavado_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
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
            'iEstado' => $iEstado,
            'iIdCliente' => $iIdCliente,
            'sNombreCliente' => $sNombreCliente,
            'iTipoRecepcionCliente' => $iTipoRecepcionCliente,
            'iEstadoLavado' => $iEstadoLavado,
            'Nu_Transporte_Lavanderia_Hoy' => $Nu_Transporte_Lavanderia_Hoy,
        );

		ob_start();
		$file = $this->load->view('Ventas/pdf/EstadoLavadoViewPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('Laesystems - Estado de Lavado');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $iIdTipoDocumento, $iIdSerieDocumento, $iNumeroDocumento, $iEstado, $iIdCliente, $sNombreCliente, $iTipoRecepcionCliente, $iEstadoLavado, $Nu_Transporte_Lavanderia_Hoy){
        $this->load->library('Excel');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $iIdTipoDocumento = $this->security->xss_clean($iIdTipoDocumento);
        $iIdSerieDocumento = $this->security->xss_clean($iIdSerieDocumento);
        $iNumeroDocumento = $this->security->xss_clean($iNumeroDocumento);
        $iEstado = $this->security->xss_clean($iEstado);
        $iIdCliente = $this->security->xss_clean($iIdCliente);
        $sNombreCliente = $this->security->xss_clean($sNombreCliente);
        $iTipoRecepcionCliente = $this->security->xss_clean($iTipoRecepcionCliente);
        $iEstadoLavado = $this->security->xss_clean($iEstadoLavado);
        $Nu_Transporte_Lavanderia_Hoy = $this->security->xss_clean($Nu_Transporte_Lavanderia_Hoy);
        
		$fileNameExcel = "estado_lavado_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Estado de Lavado');
        
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
        ->setCellValue('C2', 'Estado de Lavado')
        ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:J2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:J3');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20");

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
        ->setCellValue('A5', 'Cajero')
        ->setCellValue('B5', 'F. Emisión')
        ->setCellValue('C5', 'F. Entrega')
        ->setCellValue('D5', 'Tipo')
        ->setCellValue('E5', 'Serie')
        ->setCellValue('F5', 'Número')
        ->setCellValue('G5', 'Cliente')
        ->setCellValue('H5', 'Celular')
        ->setCellValue('I5', 'M')
        ->setCellValue('J5', 'Total')
        ->setCellValue('K5', 'Total Saldo')
        ->setCellValue('L5', 'Glosa')
        ->setCellValue('M5', 'Estado Recepción')
        ;
        
        $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 6;

        $arrParams = array(
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'iIdTipoDocumento' => $iIdTipoDocumento,
            'iIdSerieDocumento' => $iIdSerieDocumento,
            'iNumeroDocumento' => $iNumeroDocumento,
            'iEstado' => $iEstado,
            'iIdCliente' => $iIdCliente,
            'sNombreCliente' => $sNombreCliente,
            'iTipoRecepcionCliente' => $iTipoRecepcionCliente,
            'iEstadoLavado' => $iEstadoLavado,
            'Nu_Transporte_Lavanderia_Hoy' => $Nu_Transporte_Lavanderia_Hoy
        );
        $arrData = $this->getReporte($arrParams);
        if ( $arrData['sStatus'] == 'success' ) {
            $total_s = 0.00; $total_s_saldo = 0.00; $sum_total_s = 0.00; $sum_total_s_saldo = 0.00;
            foreach($arrData['arrData'] as $row) {                
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'F' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'I' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->No_Empleado)
                ->setCellValue('B' . $fila, $row->Fe_Emision_Hora)
                ->setCellValue('C' . $fila, $row->Fe_Entrega)
                ->setCellValue('D' . $fila, $row->No_Tipo_Documento_Breve)
                ->setCellValue('E' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('F' . $fila, $row->ID_Numero_Documento)
                ->setCellValue('G' . $fila, $row->No_Entidad)
                ->setCellValue('H' . $fila, $row->Nu_Celular_Entidad)
                ->setCellValue('I' . $fila, $row->No_Signo)
                ->setCellValue('J' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                ->setCellValue('K' . $fila, numberFormat($row->Ss_Total_Saldo, 2, '.', ','))
                ->setCellValue('L' . $fila, $row->Txt_Glosa)
                ->setCellValue('M' . $fila, $row->No_Estado_Lavado_Recepcion_Cliente)
                ;
                $fila++;

                $sum_total_s += $row->Ss_Total;
                $sum_total_s_saldo += $row->Ss_Total_Saldo;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('I' . $fila, 'Total')
            ->setCellValue('J' . $fila, numberFormat($sum_total_s, 2, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_total_s_saldo, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'K' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
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
