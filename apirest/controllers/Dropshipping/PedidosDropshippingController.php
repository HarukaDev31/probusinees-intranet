<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class PedidosDropshippingController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Dropshipping/PedidosModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
		$this->load->model('DocumentoElectronicoModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  $arrUrlDropshipping = $this->ConfiguracionModel->obtenerUrlDropshippingTiendaVirtual();
			$this->load->view('header', array("arrUrlDropshipping" => $arrUrlDropshipping));
			$this->load->view('Dropshipping/PedidosView');
			$this->load->view('footer', array("js_pedidos_dropshipping" => true));
		}
	}	
	
	public function verPedido($iIdPedido){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->PedidosModel->verPedido($this->security->xss_clean($iIdPedido)));
  }
  
  private function getReporte($arrParams){
    $arrResponseModal = $this->PedidosModel->getReporte($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
            
      $sRelacionData='';
      $sAccionImprimir='imprimir';
      $sVacio='mostrar-img-logo_punto_venta';
      foreach ($arrResponseModal['arrData'] as $row) {
        $rows = array();
        $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
        $rows['ID_Pedido_Cabecera'] = $row->ID_Pedido_Cabecera;
        $rows['No_Entidad'] = $row->No_Entidad;
        $rows['Ss_Total'] = $row->Ss_Total;
        $rows['No_Estado_Recepcion'] = $row->No_Estado_Recepcion;
        $rows['No_Class_Estado_Recepcion'] = ($row->Nu_Tipo_Metodo_Entrega_Tienda_Virtual == 6 ? 'danger' : 'success');
        
			  $arrEstadoPedidoTienda = $this->HelperModel->obtenerEstadoOrdenPedidoTienda($row->Nu_Estado);
        //SHOP
        $dropdown = '<div class="dropdown">
          <button style="width: 100%;" class="btn btn-' . $arrEstadoPedidoTienda['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoPedidoTienda['No_Estado'] . ' <span class="caret"></span></button>
          <ul class="dropdown-menu" style="width: 100%; position: sticky;">
            <li><a alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2);">Confirmado</a></li>
            <li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Preparando</a></li>
            <li><a alt="En Camino" title="En Camino" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4);">En Camino</a></li>
            <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5);">Entregado</a></li>
            <li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6);">Rechazado</a></li>
          </ul>
        </div>';
        $rows['No_Estado_Pedido'] = $dropdown;

        $rows['sAccionVer'] = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)" onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-list-alt" aria-hidden="true"></i></button>';
                
        $sAccionFacturar = '<button class="btn btn-xs btn-link" alt="Generar Venta" title="Generar Venta" href="javascript:void(0)" onclick="generarVenta(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-shopping-cart" aria-hidden="true"></i></button>';
				$arrParams = array('ID_Pedido_Cabecera' => $row->ID_Pedido_Cabecera);
				$arrResponseDocument = $this->PedidosModel->getRelacionPedidoVenta($arrParams);
				$sRelacionData = '';
				if ($arrResponseDocument['sStatus'] == 'success') {
          $sAccionFacturar = '';
					foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
            $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($rowEnlace->Nu_Estado);
					  $sRelacionData .= '<span class="label label-' . $arrEstadoDocumento['No_Class_Estado'] . '">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . " (" . $arrEstadoDocumento['No_Estado'] . ")</span>";
            $sRelacionData .= '<br><button type="button" class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $rowEnlace->Txt_Url_PDF . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>';
            
            if ($rowEnlace->Nu_Estado==6 || $rowEnlace->Nu_Estado==8) {
              $sRelacionData .= '<br><button type="button" id="whatsapp-' . $rowEnlace->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $rowEnlace->ID_Entidad . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button>';
              $sRelacionData .= '<br><button type="button" class="btn btn-xs btn-link" alt="Correo" title="Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $rowEnlace->ID_Entidad . '\')"><i class="fa fa-fw fa-envelope fa-2x" style="color: #2d2d2d;"></i></button>';
            }
          }
				}
        $rows['sAccionFacturar'] = $sAccionFacturar . $sRelacionData;
      
        $rows['sAccionEliminar'] = ($row->Nu_Estado!=7 ? '<button class="btn btn-xs btn-link" alt="Eliminar pedido" title="Eliminar pedido" href="javascript:void(0)" onclick="eliminarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>' : '');

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
  
	public function cambiarEstado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
  
	public function eliminarPedido($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidosModel->eliminarPedido($this->security->xss_clean($ID)));
	}
  
	public function generarVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidosModel->generarVenta($this->input->post()));
	}

	public function sendReporte(){
    $arrParams = array(
      'Fe_Inicio' => $this->input->post('Fe_Inicio'),
      'Fe_Fin' => $this->input->post('Fe_Fin'),
      'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
      'ID_Pedido_Cabecera' => $this->input->post('ID_Pedido_Cabecera'),
      'Nu_Estado_Pedido' => $this->input->post('Nu_Estado_Pedido'),
      'iIdCliente' => $this->input->post('iIdCliente'),
      'sNombreCliente' => $this->input->post('sNombreCliente'),
      'Nu_Tipo_Recepcion' => $this->input->post('Nu_Tipo_Recepcion'),
    );
    echo json_encode($this->getReporte($arrParams));
  }
    
  public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Pedido_Cabecera, $Nu_Estado_Pedido, $iIdCliente, $sNombreCliente, $Nu_Tipo_Recepcion){
    $this->load->library('FormatoLibroSunatPDF');

    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Pedido_Cabecera = $this->security->xss_clean($ID_Pedido_Cabecera);
    $Nu_Estado_Pedido = $this->security->xss_clean($Nu_Estado_Pedido);
    $iIdCliente = $this->security->xss_clean($iIdCliente);
    $sNombreCliente = $this->security->xss_clean($sNombreCliente);
    $Nu_Tipo_Recepcion = $this->security->xss_clean($Nu_Tipo_Recepcion);

    $fileNamePDF = "ventas_detalladas_generales" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

    $pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $arrCabecera = array (
      "Fe_Inicio" => ToDateBD($Fe_Inicio),
      "Fe_Fin" => ToDateBD($Fe_Fin),
    );

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Documento' => $ID_Tipo_Documento,
      'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
      'Nu_Estado_Pedido' => $Nu_Estado_Pedido,
      'iIdCliente' => $iIdCliente,
      'sNombreCliente' => $sNombreCliente,
      'Nu_Tipo_Recepcion' => $Nu_Tipo_Recepcion,
    );

    ob_start();
    $file = $this->load->view('Dropshipping/pdf/PedidosMarketplaceViewPDF', array(
      'arrCabecera' => $arrCabecera,
      'arrDetalle' => $this->getReporte($arrParams),
    ));
    $html = ob_get_contents();
    ob_end_clean();

    $pdf->SetAuthor('LAE');
    $pdf->SetTitle('LAE - Dropshipping Detalladas Generales');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->setFont('helvetica', '', 7);
    
    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Pedido_Cabecera, $Nu_Estado_Pedido, $iIdCliente, $sNombreCliente, $Nu_Tipo_Recepcion){
    $this->load->library('Excel');
	  
    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Pedido_Cabecera = $this->security->xss_clean($ID_Pedido_Cabecera);
    $Nu_Estado_Pedido = $this->security->xss_clean($Nu_Estado_Pedido);
    $iIdCliente = $this->security->xss_clean($iIdCliente);
    $sNombreCliente = $this->security->xss_clean($sNombreCliente);
    $Nu_Tipo_Recepcion = $this->security->xss_clean($Nu_Tipo_Recepcion);
    
		$fileNameExcel = "ventas_detalladas_generales_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('PedidosMarketplace online');
      
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
    ->setCellValue('E2', 'Informe de Dropshipping')
    ->setCellValue('E3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E2:K2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E3:K3');
    $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
    // /. Title
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");//Fecha Emisión
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("12");//Doc. Tipo
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("12");//Doc. Nro.
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("10");//Cliente Tipo
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");//Cliente Nro.
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("40");//Cliente Nombre
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");//Total
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("12");//Recepción
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("12");//Estado

    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->applyFromArray($BStyle_top);
        
    $objPHPExcel->getActiveSheet()->getStyle('B5:F5')->applyFromArray($BStyle_bottom);
    $objPHPExcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($BStyle_bottom);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($BStyle_right);
    
    $objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($BStyle_right);

    $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($BStyle_right);
    
    $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($BStyle_right);
    
    $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($BStyle_right);

    $objPHPExcel->getActiveSheet()->getStyle('A5:T5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A6:T6')->getFont()->setBold(true);
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Fecha');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('B5', 'Documento');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B5:C5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('D5', 'Cliente');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D5:F5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A6', 'Emisión')
    ->setCellValue('B6', 'Tipo')
    ->setCellValue('C6', 'Número')
    ->setCellValue('D6', 'Tipo')
    ->setCellValue('E6', '# Documento')
    ->setCellValue('F6', 'Nombre')
    ->setCellValue('G6', 'Total')
    ->setCellValue('H6', 'Recepción')
    ->setCellValue('I6', 'Estado')
    ;
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($style_align_center);
    
    $objPHPExcel->getActiveSheet()->freezePane('A7');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
    
    $fila = 7;

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Documento' => $ID_Tipo_Documento,
      'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
      'Nu_Estado_Pedido' => $Nu_Estado_Pedido,
      'iIdCliente' => $iIdCliente,
      'sNombreCliente' => $sNombreCliente,
      'Nu_Tipo_Recepcion' => $Nu_Tipo_Recepcion,
    );
    $arrData = $this->getReporte($arrParams);
        
    if ( $arrData['sStatus'] == 'success' ) {
      $iCounter = 0; $fTotal = 0.00; $fTotalGeneral = 0.00;
      foreach ($arrData['arrData'] as $row) {
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'I' . $fila)->applyFromArray($style_align_center);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
        ->setCellValue('B' . $fila, $row->No_Tipo_Documento_Breve)
        ->setCellValue('C' . $fila, $row->ID_Pedido_Cabecera)
        ->setCellValue('D' . $fila, $row->No_Tipo_Documento_Identidad_Breve)
        ->setCellValue('E' . $fila, $row->Nu_Documento_Identidad)
        ->setCellValue('F' . $fila, $row->No_Entidad)
        ->setCellValue('G' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
        ->setCellValue('H' . $fila, $row->No_Estado_Recepcion)
        ->setCellValue('I' . $fila, $row->No_Estado)
        ;

        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');

        $fila++;
        
        $fTotalGeneral += $row->Ss_Total;
      } // /. foreach arrData
      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('F' . $fila, 'Total')
      ->setCellValue('G' . $fila, numberFormat($fTotalGeneral, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'G' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'G' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'G' . $fila)->getFont()->setBold(true);
    } else {
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('A' . $fila, $arrData['sMessage']);
      $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':I' . $fila);
      $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
    }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
	}
}
