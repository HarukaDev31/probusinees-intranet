<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class EstadoCuentaCorrienteClienteController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PuntoVenta/EstadoCuentaCorrienteClienteModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('PuntoVenta/EstadoCuentaCorrienteClienteView');
			$this->load->view('footer', array("js_estado_cuenta_corriente_cliente" => true));
		}
	}	
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->EstadoCuentaCorrienteClienteModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $fSubTotal = 0.00;
            $fIGV = 0.00;
            $fDescuento = 0.00;
            $fTotal = 0.00;
            $sAccionVer='ver';
            $sAccionImprimir='imprimir';
            $sVacio='mostrar-img-logo_punto_venta';
            
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Documento_Cabecera'] = $row->ID_Documento_Cabecera;
                $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
                $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar);
                
                $rows['No_Medio_Pago'] = $row->No_Medio_Pago;
                $rows['No_Tipo_Medio_Pago'] = (empty($row->No_Tipo_Medio_Pago) ? '' : $row->No_Tipo_Medio_Pago);
                $rows['Nu_Tarjeta'] = $row->Nu_Tarjeta;
                $rows['Nu_Transaccion'] = $row->Nu_Transaccion;

                $fTotal = $row->Ss_Total_VMP;
                $fTotalExtranjera = 0.00;
                if ( $row->Nu_Sunat_Codigo_Moneda != 'PEN' ) {//1=Soles
                    $fTotalExtranjera = $row->Ss_Total_VMP;
                }
                $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $fTotal : -$fTotal);
                $rows['Ss_Total_Extranjero'] = ($row->ID_Tipo_Documento != 5 ? $fTotalExtranjera : -$fTotalExtranjera);

                $rows['No_Estado'] = $row->No_Estado;
                $rows['No_Class_Estado'] = $row->No_Class_Estado;
                $rows['Nu_Estado'] = $row->Nu_Estado;
                $rows['No_Signo'] = $row->No_Signo;
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
            'iTipoConsultaFecha'  => $this->input->post('iTipoConsultaFecha'),
            'Fe_Inicio'  => $this->input->post('Fe_Inicio'),
            'Fe_Fin'  => $this->input->post('Fe_Fin'),
            'ID_Tipo_Documento'  => $this->input->post('ID_Tipo_Documento'),
            'ID_Serie_Documento'  => $this->input->post('ID_Serie_Documento'),
            'ID_Numero_Documento'  => $this->input->post('ID_Numero_Documento'),
            'Nu_Estado_Documento'  => $this->input->post('Nu_Estado_Documento'),
            'iIdCliente' => $this->input->post('iIdCliente'),
            'sNombreCliente' => $this->input->post('sNombreCliente'),
            'iMedioPago' => $this->input->post('iMedioPago'),
            'iTipoTarjeta' => $this->input->post('iTipoTarjeta'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($iTipoConsultaFecha, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iMedioPago, $iTipoTarjeta){
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
        $iMedioPago = $this->security->xss_clean($iMedioPago);
        $iTipoTarjeta = $this->security->xss_clean($iTipoTarjeta);
        
		$fileNamePDF = "estado_cuenta_corriente" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
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
            'iMedioPago' => $iMedioPago,
            'iTipoTarjeta' => $iTipoTarjeta,
        );

		ob_start();
		$file = $this->load->view('PuntoVenta/pdf/EstadoCuentaCorrienteClienteViewPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('LAE');
		$pdf->SetTitle('LAE - Estado Cuenta Corriente Cliente');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($iTipoConsultaFecha, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iMedioPago, $iTipoTarjeta){
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
        $iMedioPago = $this->security->xss_clean($iMedioPago);
        $iTipoTarjeta = $this->security->xss_clean($iTipoTarjeta);
        
		$fileNameExcel = "estado_cuenta_corriente" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Estado Cuenta Corriente Cliente');
        
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
        ->setCellValue('C2', 'Informe de Estado Cuenta Corriente del Cliente')
        ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:K2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:K3');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("40");//CLIENTE
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("6");//T.C
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15");

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
        ->setCellValue('B5', 'Tipo')
        ->setCellValue('C5', 'Serie')
        ->setCellValue('D5', 'Número')
        ->setCellValue('E5', 'Cliente')
        ->setCellValue('F5', 'T.C.')
        ->setCellValue('G5', 'Medio Pago')
        ->setCellValue('H5', 'Tipo Tarjeta')
        ->setCellValue('I5', 'Nro. Tarjeta')
        ->setCellValue('J5', 'Nro. Voucher')
        ->setCellValue('K5', 'Total S/')
        ->setCellValue('L5', 'Total M. Ex.')
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
            'iMedioPago' => $iMedioPago,
            'iTipoTarjeta' => $iTipoTarjeta,
        );
        $arrData = $this->getReporte($arrParams);
        
        if ( $arrData['sStatus'] == 'success' ) {
            $subtotal_s = 0.00; $descuento_s = 0.00; $igv_s = 0.00; $total_s = 0.00; $total_d = 0.00;
            $sum_general_subtotal_s=0.00; $sum_general_igv_s=0.00; $sum_general_descuento_s=0.00; $sum_general_total_s=0.00; $sum_general_total_d=0.00;
            foreach($arrData['arrData'] as $row) {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'D' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'J' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':' . 'L' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
                ->setCellValue('B' . $fila, $row->No_Tipo_Documento_Breve)
                ->setCellValue('C' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('D' . $fila, $row->ID_Numero_Documento)
                ->setCellValue('E' . $fila, $row->No_Entidad)
                ->setCellValue('F' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
                ->setCellValue('G' . $fila, $row->No_Medio_Pago)
                ->setCellValue('H' . $fila, $row->No_Tipo_Medio_Pago)
                ->setCellValue('I' . $fila, $row->Nu_Tarjeta)
                ->setCellValue('J' . $fila, $row->Nu_Transaccion)
                ->setCellValue('K' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                ->setCellValue('L' . $fila, numberFormat($row->Ss_Total_Extranjero, 2, '.', ','))
                ->setCellValue('M' . $fila, $row->No_Estado)
                ;
                $sum_general_total_s += $row->Ss_Total;
                $sum_general_total_d += $row->Ss_Total_Extranjero;

                $fila++;
            }// /. for each arrData
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('J' . $fila, 'Total')
            ->setCellValue('K' . $fila, numberFormat($sum_general_total_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_general_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':' . 'L' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'L' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':' . 'L' . $fila)->getFont()->setBold(true);
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
}
