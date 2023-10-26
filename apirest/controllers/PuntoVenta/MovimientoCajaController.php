<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class MovimientoCajaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PuntoVenta/MovimientoCajaModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('PuntoVenta/MovimientoCajaView');
			$this->load->view('footer', array("js_movimiento_caja" => true));
		}
    }

	public function addMovimientoCaja(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
        $arrPost = array(
            'ID_Empresa' => $this->empresa->ID_Empresa,
            'ID_Organizacion' => $this->empresa->ID_Organizacion,
            'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
            'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
            'ID_POS' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_POS,
            'Fe_Movimiento' => dateNow('fecha_hora'),
            'ID_Tipo_Operacion_Caja' => $this->input->post('ID_Tipo_Operacion_Caja'),
            'ID_Moneda' => $this->input->post('ID_Moneda'),
            'Ss_Total' => $this->input->post('Ss_Total'),
            'Txt_Nota' => $this->input->post('Txt_Nota'),
            'Nu_Estado' => 0,
            'ID_Enlace_Apertura_Caja_Pos' => 0,
        );
        
        $arrResponseModal = $this->MovimientoCajaModel->addMovimientoCaja($arrPost);
        echo json_encode($arrResponseModal);
	}
	
    private function getReporte(){
        $arrResponseModal = $this->MovimientoCajaModel->getReporte();
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $sEstadoCaja = '';
            $sClassEstadoSpan = '';
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['No_Tipo_Operacion_Caja'] = $row->No_Tipo_Operacion_Caja;
                $rows['Fe_Movimiento'] = allTypeDate($row->Fe_Movimiento, '-', 0);
                $rows['No_Signo'] = $row->No_Signo;
                $rows['Ss_Total'] = $row->Ss_Total;
                $rows['Txt_Nota'] = $row->Txt_Nota;
                $rows['Nu_Tipo'] = $row->Nu_Tipo;
                $rows['sImpresion'] = '<button type="button" class="btn btn-xs btn-link" alt="Imprimir" title="Imprimir" href="javascript:void(0)" onclick="imprimirMovimientoCaja(\'' . $row->ID_Caja_Pos . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>';

                $sClassEstadoSpan='success';
                if ( $row->Nu_Tipo == '6' ) {
                    $sClassEstadoSpan='danger';
                }

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
        $arrParams = array();
        echo json_encode($this->getReporte());
    }
    
	public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento){
        $this->load->library('FormatoLibroSunatPDF');
		
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento      = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento     = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento    = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento    = $this->security->xss_clean($Nu_Estado_Documento);
        
		$fileNamePDF = "ventas_x_punto_venta" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $arrParams = array(
            'Fe_Inicio'  => $Fe_Inicio,
            'Fe_Fin'  => $Fe_Fin,
            'ID_Tipo_Documento'  => $ID_Tipo_Documento,
            'ID_Serie_Documento'  => $ID_Serie_Documento,
            'ID_Numero_Documento'  => $ID_Numero_Documento,
            'Nu_Estado_Documento'  => $Nu_Estado_Documento,
        );

        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
        );
        
		ob_start();
		$file = $this->load->view('PuntoVenta/pdf/ventas_x_cliente_pdf', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('LAE');
		$pdf->SetTitle('LAE - Ventas por Punto de Venta');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento){
        $this->load->library('Excel');
	    
        $Fe_Inicio              = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin                 = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento      = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento     = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento    = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento    = $this->security->xss_clean($Nu_Estado_Documento);
        
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
        ->setCellValue('B1', $this->empresa->No_Empresa)
        ->setCellValue('E2', 'Informe de Ventas por Cliente')
        ->setCellValue('E3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E2:K2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E3:K3');
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("20");

        $objPHPExcel->getActiveSheet()->getStyle('A5:O5')->applyFromArray($BStyle_top);
        
        $objPHPExcel->getActiveSheet()->getStyle('B5:D5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('G5:N5')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:O6')->applyFromArray($BStyle_bottom);
        
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
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:O5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A6:O6')->getFont()->setBold(true);
        
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
        ->setCellValue('O6', 'Estado')
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:O6')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->freezePane('A7');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 7;

        $arrParams = array(
            'Fe_Inicio'  => $Fe_Inicio,
            'Fe_Fin'  => $Fe_Fin,
            'ID_Tipo_Documento'  => $ID_Tipo_Documento,
            'ID_Serie_Documento'  => $ID_Serie_Documento,
            'ID_Numero_Documento'  => $ID_Numero_Documento,
            'Nu_Estado_Documento'  => $Nu_Estado_Documento,
        );

        $data = $this->getReporte($arrParams);
        
        if ( count($data) > 0) {
            $ID_Entidad = '';
            $counter = 0;
            
            $subtotal_s = 0.00;
            $igv_s = 0.00;
            $descuento_s = 0.00;
            $total_s = 0.00;
            
            $sum_cantidad = 0.000000;
            $sum_subtotal_s = 0.00;
            $sum_descuento_s = 0.00;
            $sum_igv_s = 0.00;
            $sum_total_s = 0.00;
            $sum_total_d = 0.00;
            
            $sum_general_cantidad = 0.000000;
            $sum_general_subtotal_s = 0.00;
            $sum_general_descuento_s = 0.00;
            $sum_general_igv_s = 0.00;
            $sum_general_total_s = 0.00;
            $sum_general_total_d = 0.00;
            
            foreach ($data as $row) {
                if ($ID_Entidad != $row->ID_Entidad) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('G' . $fila, 'Total')
                        ->setCellValue('H' . $fila, numberFormat($sum_cantidad, 6, '.', ','))
                        ->setCellValue('J' . $fila, numberFormat($sum_subtotal_s, 2, '.', ','))
                        ->setCellValue('K' . $fila, numberFormat($sum_igv_s, 2, '.', ','))
                        ->setCellValue('L' . $fila, numberFormat($sum_descuento_s, 2, '.', ','))
                        ->setCellValue('M' . $fila, numberFormat($sum_total_s, 2, '.', ','))
                        ->setCellValue('N' . $fila, numberFormat($sum_total_d, 2, '.', ','));
                        
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
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
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                        
                        $sum_cantidad = 0.000000;
                        $sum_subtotal_s = 0.00;
                        $sum_igv_s = 0.00;
                        $sum_descuento_s = 0.00;
                        $sum_total_s = 0.00;
                        $sum_total_d = 0.00;
                    }
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Cliente')
                    ->setCellValue('B' . $fila, $row->Nu_Documento_Identidad)
                    ->setCellValue('C' . $fila, $row->No_Entidad)
                    ;
                    
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
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                    
                    $ID_Entidad = $row->ID_Entidad;
                    $fila++;
                }

                $subtotal_s = ($row->ID_Moneda == 1 ? $row->Ss_SubTotal : ($row->Ss_SubTotal * $row->Ss_Tipo_Cambio));
                $igv_s = ($row->ID_Moneda == 1 ? $row->Ss_IGV : ($row->Ss_IGV * $row->Ss_Tipo_Cambio));
                $descuento_s = ($row->ID_Moneda == 1 ? $row->Ss_Descuento : ($row->Ss_Descuento * $row->Ss_Tipo_Cambio));
                $total_s = ($row->ID_Moneda == 1 ? $row->Ss_Total : ($row->Ss_Total * $row->Ss_Tipo_Cambio));
                
                if ($iTipoReporte==0) {
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, $row->Fe_Emision)
                    ->setCellValue('B' . $fila, $row->No_Tipo_Documento_Breve)
                    ->setCellValue('C' . $fila, $row->ID_Serie_Documento)
                    ->setCellValue('D' . $fila, $row->ID_Numero_Documento)
                    ->setCellValue('E' . $fila, $row->No_Signo)
                    ->setCellValue('F' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
                    ->setCellValue('G' . $fila, $row->No_Producto)
                    ->setCellValue('H' . $fila, numberFormat($row->Qt_Producto, 6, '.', ','))
                    ->setCellValue('I' . $fila, numberFormat($row->Ss_Precio, 6, '.', ','))
                    ->setCellValue('J' . $fila, numberFormat($subtotal_s, 2, '.', ','))
                    ->setCellValue('K' . $fila, numberFormat($igv_s, 2, '.', ','))
                    ->setCellValue('L' . $fila, numberFormat($descuento_s, 2, '.', ','))
                    ->setCellValue('M' . $fila, numberFormat($total_s, 2, '.', ','))
                    ->setCellValue('N' . $fila, $row->ID_Moneda == 1 ? '0.00' : numberFormat($row->Ss_Total, 2, '.', ''))
                    ->setCellValue('O' . $fila, $row->No_Estado)
                    ;
                    $fila++;
                }
                
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'C' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('O' . $fila)->applyFromArray($style_align_center);

                $sum_cantidad += $row->Qt_Producto;
                $sum_subtotal_s += $subtotal_s;
                $sum_igv_s += $igv_s;
                $sum_descuento_s += $descuento_s;
                $sum_total_s += $total_s;
                $sum_total_d += ($row->ID_Moneda != 1) ? $row->Ss_Total : 0;
                
                $sum_general_cantidad += $row->Qt_Producto;
                $sum_general_subtotal_s += $subtotal_s;
                $sum_general_igv_s += $igv_s;
                $sum_general_descuento_s += $descuento_s;
                $sum_general_total_s += $total_s;
                $sum_general_total_d += ($row->ID_Moneda != 1) ? $row->Ss_Total : 0;
                
                $counter++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('G' . $fila, 'Total')
            ->setCellValue('H' . $fila, numberFormat($sum_cantidad, 6, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_subtotal_s, 2, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_igv_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_descuento_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_total_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
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
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('G' . $fila, 'Total General')
            ->setCellValue('H' . $fila, numberFormat($sum_general_cantidad, 6, '.', ','))
            ->setCellValue('J' . $fila, numberFormat($sum_general_subtotal_s, 2, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_general_igv_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_general_descuento_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_general_total_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_general_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
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
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
        }
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}

	public function imprimirMovimientoCaja($ID){
		$arrData = $this->MovimientoCajaModel->getMovimientoCaja($ID);
		
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		
		$this->load->library('Pdf');
		
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		ob_start();
		$file = $this->load->view('PuntoVenta/pdf/TicketMovimientoCajaViewPDF', array(
			'arrData' => $arrData,
		));
		$html = ob_get_contents();
		ob_end_clean();
		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('TICKET CAJA -' . $arrData[0]->ID_Caja_Pos);
	
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		
		$pdf->SetMargins(PDF_MARGIN_LEFT-13, PDF_MARGIN_TOP-25, PDF_MARGIN_RIGHT-13);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$page_format = array(
			'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 74.1, 'ury' => 229.3),
		);
		$pdf->AddPage('P', $page_format, false, false);
		 

		$pdf->setFont('helvetica', '', 5);
		$pdf->writeHTML($html, true, false, true, false, '');

		$file_name = 'PDF TICKET CAJA-' . $arrData[0]->ID_Caja_Pos . '.pdf';
		$pdf->Output($file_name, 'I');
	}
}
