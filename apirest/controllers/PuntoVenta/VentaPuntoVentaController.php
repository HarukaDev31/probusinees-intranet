<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class VentaPuntoVentaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PuntoVenta/VentaPuntoVentaModel');
		$this->load->model('HelperModel');
		$this->load->model('Ventas/VentaModel');
		$this->load->model('DocumentoElectronicoModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('PuntoVenta/VentaPuntoVentaView');
			$this->load->view('footer', array("js_venta_punto_venta" => true));
		}
	}	
	
    private function getReporte($arrParams){
		$sMethod = $this->input->post('sMethod');
        $arrResponseModal = $this->VentaPuntoVentaModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $fSubTotal = 0.00;
            $fIGV = 0.00;
            $fDescuento = 0.00;
            $fTotal = 0.00;
            $sAccionVer='ver';
            $sAccionImprimir='imprimir';
            $sVacio='mostrar-img-logo_punto_venta';
            $action_anular = 'anular';
            $action_delete = 'delete';
            $fTotalGratuita = 0.00;

            //$upload_path = '../librerias.laesystems.com/apirest/libraries/sunat_facturador/certificado_digital/' . ($this->empresa->Nu_Estado_Sistema == 1 ? 'PRODUCCION/' : 'BETA/') . $this->empresa->Nu_Documento_Identidad; //localhost
		    $upload_path = '../librerias/apirest/libraries/sunat_facturador/certificado_digital/' . ($this->empresa->Nu_Estado_Sistema == 1 ? 'PRODUCCION/' : 'BETA/') . $this->empresa->Nu_Documento_Identidad;
            foreach ($arrResponseModal['arrData'] as $row) {
                $path = $upload_path . "/R-" . $this->empresa->Nu_Documento_Identidad . "-" . $row->Nu_Sunat_Codigo . "-" . $row->ID_Serie_Documento . "-" . autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT) . ".XML";
                $path_v2 = $upload_path . "/R-" . $this->empresa->Nu_Documento_Identidad . "-" . $row->Nu_Sunat_Codigo . "-" . $row->ID_Serie_Documento . "-" . autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT) . ".xml";

                $rows = array();
                $rows['ID_Empresa'] = $row->ID_Empresa;
                $rows['ID_Documento_Cabecera'] = $row->ID_Documento_Cabecera;
                $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
                $rows['No_Empleado'] = $row->No_Empleado;
                
                $arrEstadoRecepcion = $this->HelperModel->obtenerEstadoRecepcionArray($row->Nu_Tipo_Recepcion);
                $rows['No_Tipo_Recepcion'] = $arrEstadoRecepcion['No_Estado'];
                //$sTipoRecepcion = explode('-', $row->No_Tipo_Recepcion);
                //$rows['No_Tipo_Recepcion'] = $sTipoRecepcion[1];

                $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['ID_Entidad'] = $row->ID_Entidad;
                $rows['ID_Tipo_Documento_Identidad'] = $row->ID_Tipo_Documento_Identidad;
                $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['Txt_Email_Entidad'] = $row->Txt_Email_Entidad;
                $rows['Nu_Celular_Entidad'] = $row->Nu_Celular_Entidad;
                $rows['Nu_Estado_Entidad'] = $row->Nu_Estado_Entidad;
                $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar);
                
                $fTotal = $row->Ss_Total;
                $fTotalExtranjera = 0.00;
                if ( $row->Nu_Sunat_Codigo_Moneda != 'PEN' ) {//1=Soles
                    $fTotalExtranjera = $row->Ss_Total;
                }
                
                $fTotalGratuita = 0.00;
                if ($row->Ss_Total > 0.00) {
                    $objImporteDetalleDocumento = $this->HelperModel->obtenerImporteDetalleDocumentoGratuita($row->ID_Documento_Cabecera);
                    $fTotalGratuita = $objImporteDetalleDocumento->Ss_Total;
                    $fTotal -= $fTotalGratuita;
                }

                $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $fTotal : -$fTotal);
                $rows['Ss_Total_Extranjero'] = ($row->ID_Tipo_Documento != 5 ? $fTotalExtranjera : -$fTotalExtranjera);
                $rows['Ss_Total_Saldo'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total_Saldo : -$row->Ss_Total_Saldo);

                $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];

                $sEstadoPago = 'pendiente';
                $sEstadoPagoClass = 'warning';
                if ($row->Ss_Total_Saldo == 0.00) {
                    $sEstadoPago = 'cancelado';
                    $sEstadoPagoClass = 'success';
                }
                $rows['No_Estado_Pago'] = $sEstadoPago;
                $rows['No_Class_Estado_Pago'] = $sEstadoPagoClass;
                $rows['Ss_Detraccion'] = $row->Ss_Detraccion;

                $rows['Nu_Estado'] = $row->Nu_Estado;
                $rows['Nu_Estado_Lavado'] = $row->Nu_Estado_Lavado;
                $rows['Nu_Estado_Lavado_Recepcion_Cliente'] = $row->Nu_Estado_Lavado_Recepcion_Cliente;
                $rows['No_Signo'] = $row->No_Signo;
                //$rows['ID_Documento_Medio_Pago'] = $row->ID_Documento_Medio_Pago;
                
                $sDocumento = $row->No_Tipo_Documento_Breve . ' - ' . $row->ID_Serie_Documento . ' - ' . $row->ID_Numero_Documento;
                $btn_modificar = '';
                if ( !empty($sMethod) && $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 1 ) {
			        if ( $row->Nu_Estado == 6 || $row->Nu_Estado == 8 || $row->Nu_Estado == 9 )
                        $btn_modificar = '<button type="button" class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="modificarVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $sDocumento . '\', \'' . $row->Nu_Tipo_Recepcion . '\', \'' . $row->Fe_Entrega . '\', \'' . $row->ID_Transporte_Delivery . '\', \'' . $row->ID_Entidad . '\', \'' . $row->Fe_Emision . '\', \'' . $row->ID_Tipo_Documento . '\', \'' . $row->Fe_Vencimiento . '\', \'' . $row->Ss_Total_Saldo . '\', \'' . $row->Nu_Transporte_Lavanderia_Hoy . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';
                }
                $rows['btn_modificar'] = $btn_modificar;

                $sTipoBajaSunat = 'Interno';
                if (($row->ID_Tipo_Documento == 4 || $row->ID_Tipo_Documento == 5 || $row->ID_Tipo_Documento == 6) && substr($row->ID_Serie_Documento,0,1) == 'B' )
                    $sTipoBajaSunat = 'RC';
                else if (($row->ID_Tipo_Documento == 3 || $row->ID_Tipo_Documento == 5 || $row->ID_Tipo_Documento == 6) && substr($row->ID_Serie_Documento,0,1) == 'F' ) 
                    $sTipoBajaSunat = 'RA';

                $btn_anular = '';
                if ( !empty($sMethod) && $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Eliminar == 1 ) {
                    $arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
                    $arrResponseDocument = $this->HelperModel->getDocumentoEnlace($arrParams);
                    $iEnlace=0;
                    if ($arrResponseDocument['sStatus'] == 'success')
                        $iEnlace=1;

                    //if ( ($iEnlace == 0 && $row->Nu_Estado == 8) || ($row->Nu_Estado == 6 && $row->ID_Tipo_Documento == 2) || $this->empresa->Nu_Tipo_Proveedor_FE == 3 )
			        if ( $row->Ss_Total > 0.00 && ($row->ID_Tipo_Documento == 2 && $row->Nu_Estado == 6) || ($iEnlace == 0 && $row->Nu_Estado == 8) ) {
                        $btn_anular = '<br><button type="button" class="btn btn-xs btn-link" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularFacturaVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $iEnlace . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action_anular . '\', \'' . $row->Nu_Estado . '\', \'' . $sTipoBajaSunat . '\', \'' . $row->Fe_Emision . '\', \'' . $row->ID_Serie_Documento . '\')"><i class="fa fa-minus-circle fa-2x" aria-hidden="true"></i></button>';
                        if($row->ID_Tipo_Documento == 3 || $row->ID_Tipo_Documento == 4) {
                            $sUrlVentaGenerarNC = base_url() . 'Ventas/VentaController/listarVentas/'.$row->ID_Numero_Documento;
                            $btn_anular .= '<br><button type="button" class="btn btn-xs btn-link" alt="Generar Nota Crédito" title="Generar Nota Crédito" href="javascript:void(0)" onclick="window.location.href=\'' . $sUrlVentaGenerarNC . '\';"><span class="label label-warning">Generar N/C</span></button>';
                        }
                    } else {
				        $btn_anular = '';
                        if ($arrResponseDocument['sStatus'] == 'success') {
                            $btn_anular = '';
                            foreach ($arrResponseDocument['arrData'] as $rowEnlace)
                                $btn_anular .= '<span class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>";
                        }
                    }
                }

                $cdr = '';
                if (($row->Nu_Estado == 8 || $row->Nu_Estado == 10) && $this->empresa->Nu_Tipo_Proveedor_FE == 2 && $row->ID_Tipo_Documento != 2)
                    $cdr = (((file_exists($path) || file_exists($path_v2)) && !empty($row->Txt_Url_CDR)) ? '<a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a>' : '<button type="button" id="btn-sunat-cdr-' . $row->ID_Documento_Cabecera . '" style="background-color: transparent;border: 0px;" alt="Recuperar CDR" title="Recuperar CDR" href="javascript:void(0)" onclick="consultarDocumentoElectronicoSunat(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Estado . '\')"><span class="label label-dark">Recuperar CDR</span><span class="label label-dark" id="span-sunat-cdr-' . $row->ID_Documento_Cabecera . '"></span></button>');
                else if (($row->Nu_Estado == 8 || $row->Nu_Estado == 10) && $this->empresa->Nu_Tipo_Proveedor_FE == 1 && ($row->ID_Tipo_Documento != 2 && $row->ID_Tipo_Documento != 4) && substr($row->ID_Serie_Documento,0,1) == 'F')
                    $cdr = (!empty($row->Txt_Url_CDR) ? '<a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a>' : '-');
                else if ($this->empresa->Nu_Tipo_Proveedor_FE == 3)
                    $cdr = '-';
                else
                    $cdr = '';

                //Mensaje SUNAT
                if ($this->empresa->Nu_Tipo_Proveedor_FE == 2 && $row->Nu_Estado==8 && !empty($row->Txt_Respuesta_Sunat_FE)) {
                    $objMensaje = json_decode($row->Txt_Respuesta_Sunat_FE);
                    if (is_object($objMensaje) && isset($objMensaje->Mensaje_SUNAT) && !empty($objMensaje->Mensaje_SUNAT) && (strpos($objMensaje->Mensaje_SUNAT, 'aceptado') == false && strpos($objMensaje->Mensaje_SUNAT, 'aceptada') == false && strpos($objMensaje->Mensaje_SUNAT, 'guardado') == false))
                        $cdr .= ' <span class="label label-danger" title="' . $objMensaje->Mensaje_SUNAT . '">ERROR: ' . substr($objMensaje->Mensaje_SUNAT, 0, 40) . '...</span>';
                }
                
                $rows['btn_anular'] = $cdr . $btn_anular;

                $rows['ID_Documento_Medio_Pago'] = '';

                $rows['sAccionVer'] = '<button type="button" class="btn btn-xs btn-link" alt="Ver comprobante" title="Ver comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionVer . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $row->Txt_Url_PDF . '\')"><i class="fa fa-list-alt fa-2x" aria-hidden="true"></i></button>';
                                    
                $icon_pdf_whatsapp_correo = '<button type="button" class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $row->Txt_Url_PDF . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button><br>';
                
                if ($row->Nu_Estado==8) {
                    $icon_pdf_whatsapp_correo .= '<button type="button" id="whatsapp-' . $row->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->ID_Entidad . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button>';
                    $icon_pdf_whatsapp_correo .= '<br><button type="button" class="btn btn-xs btn-link" alt="Correo" title="Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->ID_Entidad . '\')"><i class="fa fa-fw fa-envelope fa-2x" style="color: #2d2d2d;"></i></button>';
                }
                if ($row->ID_Tipo_Documento == 2 && $row->Ss_Total > 0.00) {
                    $icon_pdf_whatsapp_correo = '<button type="button" class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $row->Txt_Url_PDF . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button><br><button type="button" id="whatsapp-' . $row->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->ID_Entidad . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button>';
                    if(!empty($row->Txt_Url_PDF))
                        $icon_pdf_whatsapp_correo .= '<br><button type="button" class="btn btn-xs btn-link" alt="Correo" title="Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->ID_Entidad . '\')"><i class="fa fa-fw fa-envelope fa-2x" style="color: #2d2d2d;"></i></button>';
                }

                $rows['sAccionImprimir'] = $icon_pdf_whatsapp_correo;

                $rows['Txt_Glosa'] = $row->Txt_Glosa;
                $rows['Txt_Garantia'] = $row->Txt_Garantia;

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
            'iTipoRecepcionCliente' => $this->input->post('iTipoRecepcionCliente'),
            'iEstadoPago' => $this->input->post('iEstadoPago'),
            'sGlosa' => $this->input->post('sGlosa')
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($iTipoConsultaFecha, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iTipoRecepcionCliente, $iEstadoPago, $sGlosa){
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
        $sGlosa = $this->security->xss_clean($sGlosa);
        
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
            'iEstadoPago' => $iEstadoPago,
            'sGlosa' => $sGlosa
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
        
        $pdf->setFont('helvetica', '', 6);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($iTipoConsultaFecha, $Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iTipoRecepcionCliente, $iEstadoPago, $sGlosa){
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
        $sGlosa = $this->security->xss_clean($sGlosa);
        
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("25");

        $objPHPExcel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($BStyle_top);
        
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
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('A5:N5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($style_align_center);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'F. Emisión')
        ->setCellValue('B5', 'Cajero')
        ->setCellValue('C5', 'Recepción')
        ->setCellValue('D5', 'Tipo')
        ->setCellValue('E5', 'Serie')
        ->setCellValue('F5', 'Número')
        ->setCellValue('G5', 'Cliente')
        ->setCellValue('H5', 'M.')
        ->setCellValue('I5', 'Total')
        ->setCellValue('J5', 'Saldo')
        ->setCellValue('K5', 'Pago')
        ->setCellValue('L5', 'Estado')
        ->setCellValue('M5', 'Glosa')
        ->setCellValue('N5', 'Guia')
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
            'sGlosa' => $sGlosa
        );
        $arrData = $this->getReporte($arrParams);
        
        if ( $arrData['sStatus'] == 'success' ) {
            $subtotal_s = 0.00; $descuento_s = 0.00; $igv_s = 0.00; $total_s = 0.00; $total_d = 0.00;
            $sum_general_subtotal_s=0.00; $sum_general_igv_s=0.00; $sum_general_descuento_s=0.00; $sum_general_total_s=0.00; $sum_general_total_d=0.00;
            foreach($arrData['arrData'] as $row) {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'F' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
                ->setCellValue('B' . $fila, $row->No_Empleado)
                ->setCellValue('C' . $fila, $row->No_Tipo_Recepcion)
                ->setCellValue('D' . $fila, $row->No_Tipo_Documento_Breve)
                ->setCellValue('E' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('F' . $fila, $row->ID_Numero_Documento)
                ->setCellValue('G' . $fila, $row->No_Entidad)
                ->setCellValue('H' . $fila, $row->No_Signo)
                ->setCellValue('I' . $fila, numberFormat($row->Ss_Total, 3, '.', ','))
                ->setCellValue('J' . $fila, numberFormat($row->Ss_Total_Saldo, 3, '.', ','))
                ->setCellValue('K' . $fila, $row->No_Estado_Pago)
                ->setCellValue('L' . $fila, $row->No_Estado)
                ->setCellValue('M' . $fila, $row->Txt_Glosa)
                ->setCellValue('N' . $fila, $row->Txt_Garantia)
                ;
                $sum_general_total_s += $row->Ss_Total;

                $fila++;
            }// /. for each arrData
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Total')
            ->setCellValue('I' . $fila, numberFormat($sum_general_total_s, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'I' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'I' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':' . 'I' . $fila)->getFont()->setBold(true);
        } else {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('A' . $fila, $arrData['sMessage']);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':' . 'N' . $fila);
        }// if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    
	public function cobrarVenta(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VentaPuntoVentaModel->cobrarVenta($this->input->post()));
	}
    
	public function facturarOrdenLavanderia(){
        echo json_encode($this->VentaPuntoVentaModel->facturarOrdenLavanderia($this->input->post()));
	}
    
	public function cobrarVentaMasiva(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VentaPuntoVentaModel->cobrarVentaMasiva($this->input->post()));
	}
    
	public function getDocumentoVenta(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VentaPuntoVentaModel->getDocumentoVenta($this->input->post('ID')));
	}
    
	public function modificarVenta(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VentaPuntoVentaModel->modificarVenta($this->input->post()));
	}
}
