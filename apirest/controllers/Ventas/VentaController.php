<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class VentaController extends CI_Controller {
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Ventas/VentaModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
		$this->load->model('DocumentoElectronicoModel');
		$this->load->model('ImprimirTicketModel');
		$this->load->model('ImprimirLiquidacionCajaModel');
	}

	public function listarVentas($iNumeroDocumento=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/VentaView', array("iNumeroDocumento" => $iNumeroDocumento));
			$this->load->view('footer', array("js_venta" => true));
		}
	}
	
	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->VentaModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action_anular = 'anular';
        $action_delete = 'delete';
		
		$upload_path = '../librerias/apirest/libraries/sunat_facturador/certificado_digital/';
    	//$upload_path = '../librerias.laesystems.com/apirest/libraries/sunat_facturador/certificado_digital/';//localhost

        foreach ($arrData as $row) {
			$path = $upload_path . ($row->Nu_Estado_Sistema == 1 ? 'PRODUCCION/' : 'BETA/') . $row->Nu_Documento_Identidad . "/R-" . $row->Nu_Documento_Identidad . "-" . $row->Nu_Sunat_Codigo . "-" . $row->ID_Serie_Documento . "-" . autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT) . ".XML";
			$path_v2 = $upload_path . ($row->Nu_Estado_Sistema == 1 ? 'PRODUCCION/' : 'BETA/') . $row->Nu_Documento_Identidad . "/R-" . $row->Nu_Documento_Identidad . "-" . $row->Nu_Sunat_Codigo . "-" . $row->ID_Serie_Documento . "-" . autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT) . ".xml";

			$path_pdf = $upload_path . ($row->Nu_Estado_Sistema == 1 ? 'PRODUCCION/' : 'BETA/') . $row->Nu_Documento_Identidad . "/" . $row->Nu_Documento_Identidad . "-" . $row->Nu_Sunat_Codigo . "-" . $row->ID_Serie_Documento . "-" . autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT) . ".pdf";

            $no++;
			$rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
			$rows[] = $row->No_Almacen;
			$rows[] = ToDateBD($row->Fe_Emision);
			$rows[] = $row->No_Tipo_Documento_Breve;
            $rows[] = $row->ID_Serie_Documento;
            $rows[] = $row->ID_Numero_Documento;
			$rows[] = $row->Nu_Documento_Identidad_Cliente;
			$rows[] = $row->No_Entidad;
			$rows[] = $row->No_Medio_Pago;
            $rows[] = $row->No_Signo;
            
			$fTotal = $row->Ss_Total;
			$fTotalGratuita = 0.00;
			if ($fTotal > 0.00) {
				$objImporteDetalleDocumento = $this->HelperModel->obtenerImporteDetalleDocumentoGratuita($row->ID_Documento_Cabecera);
				$fTotalGratuita = $objImporteDetalleDocumento->Ss_Total;
				$fTotal -= $fTotalGratuita;
			}

            $rows[] = numberFormat($fTotal, 2, '.', ',');
            $rows[] = numberFormat($row->Ss_Total_Saldo, 2, '.', ',');
			$rows[] = ($row->Nu_Descargar_Inventario == 1 ? 'Si' : 'No');
			
			$rows[] = $row->Txt_Glosa;
			$rows[] = $row->Txt_Garantia;//guia escrita serie y numero
			$rows[] = $row->No_Orden_Compra_FE;
			$rows[] = $row->No_Placa_FE;
			$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
			$arrResponseDocument = $this->HelperModel->getDocumentoEnlaceOrigen($arrParams);
			$iEnlace=0;
			if ($arrResponseDocument['sStatus'] == 'success')
				$iEnlace=1;
				
			$span_enlace_documentos = '';
			if ( $iEnlace == 1 ) {
				$span_enlace_documentos = '';
				$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
				$arrResponseDocument = $this->HelperModel->getDocumentoEnlaceOrigen($arrParams);
				if ($arrResponseDocument['sStatus'] == 'success') {
					$span_enlace_documentos = '';
					foreach ($arrResponseDocument['arrData'] as $rowEnlace)
						$span_enlace_documentos .= '<span class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>";
				}
			}

			$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
			$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParams);
			$iEnlace=0;
			if ($arrResponseDocument['sStatus'] == 'success')
				$iEnlace=1;
				
			$span_enlace_guias = '';
			if ( $iEnlace == 1 ) {
				$span_enlace_guias = '';
				$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
				$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParams);
				if ($arrResponseDocument['sStatus'] == 'success') {
					$span_enlace_guias = '';
					foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
						$sUrlPDFSunatGuia = (!empty($rowEnlace->Txt_Url_PDF) ? ' <a alt="Descargar PDF" title="Descargar PDF" href="' . $rowEnlace->Txt_Url_PDF . '" target="_blank"><span class="label label-danger"> PDF </span></a>' : '');
						$span_enlace_guias .= '<span title="Ver guias en Logistica > Guia / Salida de Inventario" class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . $sUrlPDFSunatGuia . "</span><br>";
					}
				}
			}

			$sTipoBajaSunat = 'Interno';
			if (($row->ID_Tipo_Documento == 4 || $row->ID_Tipo_Documento == 5 || $row->ID_Tipo_Documento == 6) && substr($row->ID_Serie_Documento,0,1) == 'B' )
				$sTipoBajaSunat = 'RC';
			else if (($row->ID_Tipo_Documento == 3 || $row->ID_Tipo_Documento == 5 || $row->ID_Tipo_Documento == 6) && substr($row->ID_Serie_Documento,0,1) == 'F' ) 
				$sTipoBajaSunat = 'RA';

			/*
            $btn_send_sunat = '';
            if ( ($row->Nu_Estado == 6 || $row->Nu_Estado == 7 || $row->Nu_Estado == 9 || $row->Nu_Estado == 11) && $row->ID_Tipo_Documento != 2 && $this->empresa->Nu_Tipo_Proveedor_FE != 3 )//Action send SUNAT
            	$btn_send_sunat = '<button id="btn-sunat-' . $row->ID_Documento_Cabecera . '" type="button" class="btn btn-xs btn-link" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" alt="Enviar a Sunat" title="Enviar a Sunat" href="javascript:void(0)" onclick="sendFacturaVentaSunat(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Estado . '\', \'' . $sTipoBajaSunat . '\')"><i class="fa fa-cloud-upload">Sunat</i></button>';
            $rows[] = $btn_send_sunat;
			*/
            
			$btn_modificar = '';
            //if ( $row->ID_POS=='' && ($row->Nu_Estado == 6 || $row->Nu_Estado == 9) && ($row->No_Codigo_Medio_Pago_Sunat_PLE != '0' || ($row->No_Codigo_Medio_Pago_Sunat_PLE == '0' && $row->Ss_Total == $row->Ss_Total_Saldo)) )
			if ( ($row->Nu_Estado == 6 || $row->Nu_Estado == 9) && ($row->No_Codigo_Medio_Pago_Sunat_PLE != '0' || ($row->No_Codigo_Medio_Pago_Sunat_PLE == '0' && $row->Ss_Total == $row->Ss_Total_Saldo)) )
				$btn_modificar = '<button type="button" class="btn btn-xs btn-link" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verFacturaVenta(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';
				
			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 0)
				$btn_modificar='';
			$rows[] = $btn_modificar;
			
			$btn_anular = '<span class="label label-warning">Anulado</span>';

			$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
			$arrResponseDocument = $this->HelperModel->getDocumentoEnlace($arrParams);
			$iEnlace=0;
			if ($arrResponseDocument['sStatus'] == 'success')
				$iEnlace=1;

			if ( $row->Ss_Total > 0.00 && ($row->ID_Tipo_Documento == 2 && $row->Nu_Estado == 6) || ($iEnlace == 0 && $row->Nu_Estado == 8) )
				$btn_anular = '<button type="button" class="btn btn-xs btn-link" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularFacturaVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $iEnlace . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action_anular . '\', \'' . $row->Nu_Estado . '\', \'' . $sTipoBajaSunat . '\', \'' . $row->Fe_Emision . '\', \'' . $row->ID_Serie_Documento . '\')"><i class="fa fa-minus-circle fa-2x" aria-hidden="true"></i></button>';
			else {
				$btn_anular = '';
				if ($arrResponseDocument['sStatus'] == 'success') {
					$btn_anular = '';
					foreach ($arrResponseDocument['arrData'] as $rowEnlace)
						$btn_anular .= '<span class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>";
				}
			}

			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Eliminar == 0)
				$btn_anular='';
			$rows[] = $btn_anular;

			$btn_recuperar_pdf = '<button type="button" id="btn-sunat-pdf-' . $row->ID_Documento_Cabecera . '" style="background-color: transparent;border: 0px;" alt="Recuperar PDF" title="Recuperar PDF" href="javascript:void(0)" onclick="recuperarPDFVentaSunat(\'' . $row->ID_Documento_Cabecera . '\')"><span class="label label-danger">Recuperar PDF</span><span class="label label-dark" id="span-sunat-pdf-' . $row->ID_Documento_Cabecera . '"></span></button>';
			if ($row->ID_Tipo_Documento == 2 && $row->Ss_Total == 0.00) {
				$btn_recuperar_pdf = '';
			}

			$icon_pdf_whatsapp_correo = ( ($row->Nu_Tipo_Proveedor_FE != 2 && !empty($row->Txt_Url_PDF)) || ($row->Nu_Tipo_Proveedor_FE == 2 && file_exists($path_pdf)) ? '<a alt="Descargar PDF" title="Descargar PDF" href="' . $row->Txt_Url_PDF . '" target="_blank"><span class="label label-danger">PDF</span></a>' : $btn_recuperar_pdf);
			if ($row->Nu_Estado == 8)
				$icon_pdf_whatsapp_correo .= '<br><button type="button" id="whatsapp-' . $row->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button><br><button type="button" class="btn btn-xs btn-link" alt="Correo" title="Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\')"><i class="fa fa-fw fa-envelope fa-2x" style="color: #2d2d2d;"></i></button>';
			if ($row->ID_Tipo_Documento == 2 && $row->Ss_Total > 0.00) {
				$icon_pdf_whatsapp_correo = '<button type="button" id="whatsapp-' . $row->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button>';
				if(!empty($row->Txt_Url_PDF))
					$icon_pdf_whatsapp_correo .= '<br><button type="button" class="btn btn-xs btn-link" alt="Correo" title="Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\')"><i class="fa fa-fw fa-envelope fa-2x" style="color: #2d2d2d;"></i></button>';
				$icon_pdf_whatsapp_correo .= '<br><button type="button" class="btn btn-xs btn-link" alt="Representación Interna PDF" title="Representación Interna PDF" href="javascript:void(0)" onclick="verRepresentacionInternaPDF(\'' . $row->ID_Documento_Cabecera . '\')"><span class="label label-danger">PDF</span></a></button>';
			}

			$rows[] = $icon_pdf_whatsapp_correo;

			$cdr = '';
			if (($row->Nu_Estado == 8 || $row->Nu_Estado == 10) && $row->Nu_Tipo_Proveedor_FE == 2 && $row->ID_Tipo_Documento != 2)
				$cdr = (((file_exists($path) || file_exists($path_v2)) && !empty($row->Txt_Url_CDR)) ? '<a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a>' : '<button type="button" id="btn-sunat-cdr-' . $row->ID_Documento_Cabecera . '" style="background-color: transparent;border: 0px;" alt="Recuperar CDR" title="Recuperar CDR" href="javascript:void(0)" onclick="consultarDocumentoElectronicoSunat(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Estado . '\')"><span class="label label-dark">Recuperar CDR</span><span class="label label-dark" id="span-sunat-cdr-' . $row->ID_Documento_Cabecera . '"></span></button>');
			else if (($row->Nu_Estado == 8 || $row->Nu_Estado == 10) && $row->Nu_Tipo_Proveedor_FE == 1 && ($row->ID_Tipo_Documento != 2 && $row->ID_Tipo_Documento != 4) && substr($row->ID_Serie_Documento,0,1) == 'F'){
				$cdr = (!empty($row->Txt_Url_CDR) ? '<a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a>' : '-');
			} else if ($row->Nu_Tipo_Proveedor_FE == 3)
				$cdr = '-';
			else
				$cdr = '';

			if ($this->empresa->Nu_Tipo_Proveedor_FE == 2 && $row->Nu_Estado==8 && !empty($row->Txt_Respuesta_Sunat_FE)) {
				$objMensaje = json_decode($row->Txt_Respuesta_Sunat_FE);
				$cdr .= ' <span class="label label-dark" title="' . $objMensaje->Mensaje_SUNAT . '">' . $objMensaje->Codigo_SUNAT . '</span>';
				if (trim($objMensaje->Codigo_SUNAT) != 0)
					$cdr .= ' <span class="label label-danger" title="' . $objMensaje->Mensaje_SUNAT . '">ERROR: ' . $objMensaje->Codigo_SUNAT . '</span>';
			}

			$btn_send_sunat = '';
            if ( ($row->Nu_Estado == 6 || $row->Nu_Estado == 7 || $row->Nu_Estado == 9 || $row->Nu_Estado == 11) && $row->ID_Tipo_Documento != 2 && $this->empresa->Nu_Tipo_Proveedor_FE != 3 )//Action send SUNAT
            	$btn_send_sunat = '<button id="btn-sunat-' . $row->ID_Documento_Cabecera . '" type="button" class="btn btn-xs btn-link" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" alt="Enviar a Sunat" title="Enviar a Sunat" href="javascript:void(0)" onclick="sendFacturaVentaSunat(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Estado . '\', \'' . $sTipoBajaSunat . '\')"><i class="fa fa-cloud-upload">Sunat</i></button>';

			$rows[] = $btn_send_sunat . $cdr;
			
			$arrParamsGuia = json_encode(array(
				'sTipoCodificacion' => 'json',
				'ID_Empresa' => $row->ID_Empresa,
				'ID_Organizacion' => $row->ID_Organizacion,
				'ID_Almacen' => $row->ID_Almacen,
				'ID_Moneda' => $row->ID_Moneda,
				'ID_Documento_Cabecera' => $row->ID_Documento_Cabecera,
				'ID_Entidad' => $row->id_cliente,
				'ID_Tipo_Documento' => $row->ID_Tipo_Documento,
				'ID_Lista_Precio_Cabecera' => $row->ID_Lista_Precio_Cabecera,
				'Fe_Emision' => $row->Fe_Emision,
				'ID_Serie_Documento' => $row->ID_Serie_Documento,
				'ID_Numero_Documento' => $row->ID_Numero_Documento,
				'Ss_Total' => $row->Ss_Total,
				'Nu_Descargar_Inventario' => $row->Nu_Descargar_Inventario
			));

			$btn_opciones = '';
			if ($row->Nu_Estado == 8) {
				$btn_opciones = '
				<div class="btn-group">
					<button style="width: 100%;" alt="Opciones" title="Opciones" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Opciones <span class="caret"></span></button>
					<ul class="dropdown-menu" style="width: 100%; position: sticky;">';
						if ( $row->ID_POS=='' && $row->Nu_Tipo_Medio_Pago == 1 && $row->Ss_Total_Saldo > 0 ) {
							$btn_opciones .= '<li><a alt="Cobrar" title="Cobrar" href="javascript:void(0)" onclick="cobrarCliente(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Ss_Total_Saldo . '\', \'' . $row->No_Entidad . '\', \'' . $row->No_Tipo_Documento_Breve . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->No_Signo . '\', 0)">Cobrar</a></li>';
						}
						if ( $row->ID_POS=='' && $row->Nu_Tipo_Medio_Pago == 1 && $row->Ss_Detraccion > 0 ) {
							$btn_opciones .= '<li><a alt="Cobrar Detraccion" title="Cobrar Detraccion" href="javascript:void(0)" onclick="cobrarCliente(\'' . $row->ID_Documento_Cabecera . '\', \'' . round($row->Ss_Detraccion,0) . '\', \'' . $row->No_Entidad . '\', \'' . $row->No_Tipo_Documento_Breve . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->No_Signo . '\', 1)">Cobrar Detraccion</a></li>';
						}

						$btn_opciones .= '
							<li><a alt="Enviar Correo" title="Enviar Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\')">Enviar Correo</a></li>
							<li><a alt="Enviar WhatsApp" title="Enviar WhatsApp" href="javascript:void(0)" id="whatsapp-' . $row->ID_Documento_Cabecera . '" onclick="sendWhatsapp(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\')">Enviar WhatsApp</a></li>';
						
						if ( $row->ID_Tipo_Documento != 5 && $row->ID_Tipo_Documento != 6 ) {
							$btn_opciones .= '
							<li><a alt="Generar Nota Crédito" title="Generar Nota Crédito" href="javascript:void(0)" onclick="generarDocumentoReferencia(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\', 5, \'' . $row->ID_Tipo_Documento . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->ID_Serie_Documento_PK . '\')">Generar Nota Crédito</a></li>
							<li><a alt="Generar Nota Débito" title="Generar Nota Débito" href="javascript:void(0)" onclick="generarDocumentoReferencia(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->id_cliente . '\', 6, \'' . $row->ID_Tipo_Documento . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->ID_Serie_Documento_PK . '\')">Generar Nota Débito</a></li>
							<li><a alt="Generar Guía" title="Generar Guía" href="javascript:void(0)" onclick=generarGuia(\'' . $arrParamsGuia . '\')>Generar Guía</a></li>';
						}

						$btn_opciones .= (!empty($row->Txt_Url_PDF) ? '<li><a alt="Descargar PDF" title="Descargar PDF" href="' . $row->Txt_Url_PDF . '" target="_blank"><span class="label label-danger">PDF</span></a></li>' : '<li><a href="#">Sin PDF</a></li>');
						$btn_opciones .= (!empty($row->Txt_Url_XML) ? '<li><a alt="Descargar XML" title="Descargar XML" href="' . $row->Txt_Url_XML . '" target="_blank"><span class="label label-primary">XML</span></a></li>' : '<li><a href="#">Sin XML</a></li>');
						$btn_opciones .= (!empty($row->Txt_Url_CDR) ? '<li><a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a></li>' : '<li><a href="#">Sin CDR</a></li>');
						$btn_opciones .= '
					</ul>
				</div>';
			} else if ( ($row->Nu_Estado == 9 || $row->Nu_Estado == 11) && !empty($row->Txt_Respuesta_Sunat_FE)) {
				$btn_opciones = '
				<div class="btn-group">
					<button alt="Opciones" title="Mensaje" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Mensaje <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li>' . $row->Txt_Respuesta_Sunat_FE . '</li>
					</ul>
				</div>';
			} else if ( $row->ID_POS=='' && $row->ID_Tipo_Documento == 2 && $row->Nu_Tipo_Medio_Pago == 1 && $row->Ss_Total_Saldo > 0 ) {
				$btn_opciones = '<button type="button" class="btn btn-xs btn-link" alt="Cobrar" title="Cobrar" href="javascript:void(0)" onclick="cobrarCliente(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Ss_Total_Saldo . '\', \'' . $row->No_Entidad . '\', \'' . $row->No_Tipo_Documento_Breve . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->No_Signo . '\', 0)">Cobrar</button>';
			}
			//generar guía interna
			$btn_opciones_generar_guia = '';
			if($row->ID_POS=='' && $row->ID_Tipo_Documento == 2 && $row->Ss_Total > 0.00)
				$btn_opciones_generar_guia = '<br><button type="button" class="btn btn-xs btn-link" alt="Generar Guía" title="Generar Guía" href="javascript:void(0)" onclick=generarGuia(\'' . $arrParamsGuia . '\')>Generar Guía</button>';

			$rows[] = $btn_opciones . $btn_opciones_generar_guia;

            $btn_representacion_interna = '<button type="button" class="btn btn-xs btn-link" alt="Representación Interna PDF" title="Representación Interna PDF" href="javascript:void(0)" onclick="verRepresentacionInternaPDF(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> Interno</i></button>';
            if ($row->Nu_Estado == 8 || $row->Nu_Estado == 7 || $row->Nu_Estado == 10 || $row->Nu_Estado == 11)
                $btn_representacion_interna = '';
            $rows[] = $btn_representacion_interna;

			$rows[] = $span_enlace_documentos;
			$rows[] = $span_enlace_guias;

			$btn_repetir_mensualmente = '<button type="button" class="btn btn-xs btn-link" alt="Configurar Repetición" title="Configurar Repetición" href="javascript:void(0)" onclick="generarTareaRepetirMensual(\'' . $row->ID_Documento_Cabecera . '\')">Configurar Repetición</button>';
			if ( !empty($row->ID_Referencia) )
				$btn_repetir_mensualmente = '<span class="label label-dark">Se repetirá el ' . $row->Nu_Dia . ' de ' . ($row->ID_Tipo_Tiempo_Repetir == 2 ? MonthToSpanish($row->Nu_Month, false) : '') .  ' cada ' . ($row->ID_Tipo_Tiempo_Repetir == 1 ? 'Mes' : 'Año') . ' <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i></span> <button type="button" class="btn btn-xs btn-link" alt="Eliminar Configurar Repetición" title="Eliminar Configurar Repetición" href="javascript:void(0)" onclick="eliminarTareaRepetirMensual(\'' . $row->ID_Documento_Cabecera . '\')">Eliminar</button>';
			$rows[] = $btn_repetir_mensualmente;
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->VentaModel->count_all(),
	        'recordsFiltered' => $this->VentaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }

	public function ajax_edit($ID){
		$data = $this->VentaModel->get_by_id($this->security->xss_clean($ID));
		if ( $data['sStatus'] == 'success' ) {
			$arrImpuesto = $this->HelperModel->getImpuestos($arrPost = '');
			$output = array(
				'arrEdit' => $data['arrData'],
				'arrImpuesto' => $arrImpuesto,
			);
			echo json_encode($output);
		} else {
			echo json_encode($data);
		}
    }
    
	public function crudVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		$response = array('status' => 'success', 'ID_Documento_Cabecera_Enlace' => '');
		if ( $_POST['arrVentaCabecera']['esEnlace'] == 1 ) {
			$response = $this->HelperModel->documentExistVerify($this->security->xss_clean($_POST['arrVentaModificar']['ID_Documento_Guardado']), $this->security->xss_clean($_POST['arrVentaModificar']['ID_Tipo_Documento_Modificar']), $this->security->xss_clean($_POST['arrVentaModificar']['ID_Serie_Documento_Modificar']), $this->security->xss_clean($_POST['arrVentaModificar']['ID_Numero_Documento_Modificar']), $_POST['arrVentaModificar']);
			$_POST['arrVentaCabecera']['esEnlace'] = ($response['status'] != 'danger' ? 1 : 0);
		}

		if ( $response['status'] != 'danger' && $response['status'] != 'error' ) {
			if( $_POST['arrVentaCabecera']['ID_Tipo_Documento'] != 2 && (double)$_POST['arrVentaCabecera']['Ss_Total'] < 0.10 ) {
				echo json_encode(array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El total no puede ser menor a 0.10 céntimos'));
				exit();
			}
			
			$arrClienteNuevo = '';
			if (isset($_POST['arrClienteNuevo'])){
				$Nu_Celular_Entidad = '';
				if ( strlen($_POST['arrClienteNuevo']['Nu_Celular_Entidad']) == 11 ) {
					$Nu_Celular_Entidad = explode(' ', $_POST['arrClienteNuevo']['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}

				$arrClienteNuevo = array(
					'ID_Tipo_Documento_Identidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['ID_Tipo_Documento_Identidad']),
					'Nu_Documento_Identidad' => $this->security->xss_clean(strtoupper($_POST['arrClienteNuevo']['Nu_Documento_Identidad'])),
					'No_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['No_Entidad']),
					'Txt_Direccion_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['Txt_Direccion_Entidad']),
					'Nu_Telefono_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['Nu_Telefono_Entidad']),
					'Nu_Celular_Entidad' => $this->security->xss_clean($Nu_Celular_Entidad),
					'Txt_Email_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['Txt_Email_Entidad']),
					'ID_Tipo_Cliente_1' => $this->security->xss_clean($_POST['arrClienteNuevo']['ID_Tipo_Cliente_1'])
				);
			}//Cliente nuevo

			$ID_Documento_Cabecera_Enlace = '';
			if ( isset($_POST['arrVentaCabecera']['ID_Documento_Cabecera_Orden']) && !empty($_POST['arrVentaCabecera']['ID_Documento_Cabecera_Orden']) ) {
				$ID_Documento_Cabecera_Enlace = $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Documento_Cabecera_Orden']);
				$_POST['arrVentaCabecera']['esEnlace'] = 1;
			}

			$Fe_Emision = (ToDate($this->security->xss_clean($_POST['arrVentaCabecera']['Fe_Emision'])) > '0000-00-00' ? ToDate($this->security->xss_clean($_POST['arrVentaCabecera']['Fe_Emision'])) : dateNow('fecha'));

			$Fe_Entrega = (!empty($_POST['arrVentaCabecera']['Fe_Entrega']) && ToDate($this->security->xss_clean($_POST['arrVentaCabecera']['Fe_Entrega'])) > '0000-00-00' ? ToDate($this->security->xss_clean($_POST['arrVentaCabecera']['Fe_Entrega'])) : dateNow('fecha'));

			$fTotal = $this->security->xss_clean($_POST['arrVentaCabecera']['Ss_Total']);
			$fRetencion = 0.00;
			$fTotalSaldo = ($_POST['arrVentaCabecera']['iTipoFormaPago'] != '1' ? 0.00 : $fTotal);
			if ($_POST['arrVentaCabecera']['Nu_Retencion'] == '1') {
				$fRetencion = ($fTotal * 0.03);
				if($_POST['arrVentaCabecera']['iTipoFormaPago'] == '1') {//credito
					$fTotalSaldo = ($fTotalSaldo - $fRetencion);
				}
			}

			$fDetraccion = 0.00;
			$fPorcentajeDetraccion = (isset($_POST['arrVentaCabecera']['Po_Detraccion']) ? $_POST['arrVentaCabecera']['Po_Detraccion'] : 0.00);
			if ($_POST['arrVentaCabecera']['Nu_Detraccion'] == '1' && $fTotal >= 700.00 ) {
				$fDetraccion = ($fTotal * ($fPorcentajeDetraccion / 100));
				$fDetraccion = round($fDetraccion, 0, PHP_ROUND_HALF_UP);
				if($_POST['arrVentaCabecera']['iTipoFormaPago'] == '1') {//credito
					$fTotalSaldo = ($fTotalSaldo - $fDetraccion);
				}
			}

			$iIdTipoDocumentoVenta = $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Tipo_Documento']);

			//$dVencimiento = ToDate( ($_POST['arrVentaCabecera']['Fe_Vencimiento'] == '00/00/0000' || empty($_POST['arrVentaCabecera']['Fe_Vencimiento']) || ToDate($_POST['arrVentaCabecera']['Fe_Vencimiento']) < ToDate($_POST['arrVentaCabecera']['Fe_Emision'])) ? $_POST['arrVentaCabecera']['Fe_Emision'] : $_POST['arrVentaCabecera']['Fe_Vencimiento']);
			$dVencimiento = $Fe_Emision;
			if($_POST['arrVentaCabecera']['iTipoFormaPago'] == 1){
				$dVencimiento = ToDate($_POST['arrVentaCabecera']['Fe_Vencimiento']);
			}

			$arrVentaCabecera = array(
				'ID_Empresa' => $this->empresa->ID_Empresa,
				'ID_Organizacion' => $this->empresa->ID_Organizacion,
				'ID_Entidad' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Entidad']),
				'Txt_Direccion_Entidad' => $this->security->xss_clean($_POST['arrVentaCabecera']['Txt_Direccion_Entidad']),
				'Nu_Celular_Entidad' => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Celular_Entidad']),
				'Txt_Email_Entidad' => $this->security->xss_clean($_POST['arrVentaCabecera']['Txt_Email_Entidad']),
				'ID_Tipo_Asiento' => 1,
				'ID_Tipo_Documento' => $iIdTipoDocumentoVenta,
				'ID_Serie_Documento_PK' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Serie_Documento_PK']),
				'ID_Serie_Documento' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Serie_Documento']),
				'Fe_Emision' => $Fe_Emision,
				'ID_Moneda'	=> $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Moneda']),
				'ID_Medio_Pago' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Medio_Pago']),
				'Fe_Vencimiento' => $dVencimiento,
				'Fe_Periodo' => $Fe_Emision,
				'Fe_Emision_Hora' => $Fe_Emision . ' ' . dateNow('hora'),
				'Nu_Descargar_Inventario' => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Descargar_Inventario']),
				'ID_Almacen' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Almacen']),
				'Txt_Glosa' => $this->security->xss_clean($_POST['arrVentaCabecera']['Txt_Glosa']),
				'Nu_Detraccion' => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Detraccion']),
				'Po_Descuento' => $this->security->xss_clean($_POST['arrVentaCabecera']['Po_Descuento']),
				'Ss_Descuento' => $this->security->xss_clean($_POST['arrVentaCabecera']['Ss_Descuento']),
				'Ss_Total' => $fTotal,
				'Ss_Total_Saldo' => $fTotalSaldo,
				'Nu_Estado' => 6,
				'Nu_Codigo_Motivo_Referencia' => (isset($_POST['arrVentaModificar']['Nu_Codigo_Motivo_Referencia']) ? $this->security->xss_clean($_POST['arrVentaModificar']['Nu_Codigo_Motivo_Referencia']) : '' ),
				'No_Formato_PDF' => $this->security->xss_clean($_POST['arrVentaCabecera']['No_Formato_PDF']),
				'Txt_Garantia' => strtoupper($this->security->xss_clean($_POST['arrVentaCabecera']['Txt_Garantia'])),
				'ID_Mesero'	=> $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Mesero']),
				'ID_Comision' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Comision']),
				'iTipoCliente' => $this->security->xss_clean($_POST['arrVentaCabecera']['iTipoCliente']),
				'ID_Tipo_Medio_Pago' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Tipo_Medio_Pago']),
				'Nu_Transaccion' => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Transaccion']),
				'Nu_Tarjeta' => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Tarjeta']),
				'No_Orden_Compra_FE' => $this->security->xss_clean($_POST['arrVentaCabecera']['No_Orden_Compra_FE']),
				'No_Placa_FE' => $this->security->xss_clean($_POST['arrVentaCabecera']['No_Placa_FE']),
				'ID_Sunat_Tipo_Transaction' => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Sunat_Tipo_Transaction']),
				'ID_Documento_Cabecera_Enlace' => $ID_Documento_Cabecera_Enlace,
				'Nu_Tipo_Recepcion' => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Tipo_Recepcion']),
				'Fe_Entrega' => $Fe_Entrega,
				'ID_Transporte_Delivery' => ($_POST['arrVentaCabecera']['Nu_Tipo_Recepcion'] == 6 ? $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Transporte_Delivery']) : 0),
				'Txt_Direccion_Delivery' => ($_POST['arrVentaCabecera']['Nu_Tipo_Recepcion'] == 6 ? $this->security->xss_clean($_POST['arrVentaCabecera']['Txt_Direccion_Delivery']) : ''),
				'iTipoFormaPago' => $_POST['arrVentaCabecera']['iTipoFormaPago'],
				'Nu_Retencion' => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Retencion']),
				'Ss_Retencion' => $fRetencion,
				'Ss_Detraccion' => $fDetraccion,
				'Po_Detraccion' => $fPorcentajeDetraccion,
				'Ss_Descuento_Impuesto' => $_POST['arrVentaCabecera']['Ss_Descuento_Impuesto'],
			);

			if ( $_POST['arrVentaCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Lista_Precio_Cabecera'])));
			
			if ( $_POST['arrVentaCabecera']['esEnlace']==2 )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Guia_Cabecera" => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Guia_Cabecera'])));
			
			if ( isset($_POST['arrVentaCabecera']['ID_Canal_Venta_Tabla_Dato']) )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Canal_Venta_Tabla_Dato" => $this->security->xss_clean($_POST['arrVentaCabecera']['ID_Canal_Venta_Tabla_Dato'])));
			
			if ( isset($_POST['arrVentaCabecera']['Nu_Expediente_FE']) )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("Nu_Expediente_FE" => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Expediente_FE'])));
			
			if ( isset($_POST['arrVentaCabecera']['Nu_Codigo_Unidad_Ejecutora_FE']) )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("Nu_Codigo_Unidad_Ejecutora_FE" => $this->security->xss_clean($_POST['arrVentaCabecera']['Nu_Codigo_Unidad_Ejecutora_FE'])));

			echo json_encode(
			($this->security->xss_clean($_POST['arrVentaCabecera']['EID_Empresa']) != '' && $this->security->xss_clean($_POST['arrVentaCabecera']['EID_Documento_Cabecera']) != '') ?
				$this->actualizarVenta_Inventario(array('ID_Documento_Cabecera' => $this->security->xss_clean($_POST['arrVentaCabecera']['EID_Documento_Cabecera'])), $arrVentaCabecera, $_POST['arrDetalleVenta'], $_POST['arrVentaCabecera']['esEnlace'], $response['ID_Documento_Cabecera_Enlace'], $arrVentaCabecera['Nu_Descargar_Inventario'], $arrClienteNuevo)
			:
				$this->agregarVenta_Inventario($arrVentaCabecera, $_POST['arrDetalleVenta'], $_POST['arrVentaCabecera']['esEnlace'], $response['ID_Documento_Cabecera_Enlace'], $arrVentaCabecera['Nu_Descargar_Inventario'], $arrClienteNuevo)
			);
		} else {
			echo json_encode($response);
		}
	}

	public function agregarVenta_Inventario($arrVentaCabecera = '', $arrDetalleVenta = '', $esEnlace = '', $ID_Documento_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrClienteNuevo = ''){
		$responseVenta = $this->VentaModel->agregarVenta($arrVentaCabecera, $arrDetalleVenta, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrClienteNuevo);
		if ($responseVenta['status'] == 'success') {
			if ($Nu_Descargar_Inventario == '1'){//1 = Si
				$arrVentaCabecera['ID_Tipo_Movimiento'] = 1;//Venta
				if ($arrVentaCabecera['ID_Tipo_Documento'] == '5')//N/C
					$arrVentaCabecera['ID_Tipo_Movimiento'] = 17;//ENTRADA POR DEVOLUCIÓN DEL CLIENTE
				
				$response = $this->MovimientoInventarioModel->crudMovimientoInventario($arrVentaCabecera['ID_Almacen'], $responseVenta['Last_ID_Documento_Cabecera'], 0, $arrDetalleVenta, $arrVentaCabecera['ID_Tipo_Movimiento'], 0, '', 1, 1);
				if ( $arrVentaCabecera['ID_Tipo_Documento'] != 2 && $this->empresa->Nu_Tipo_Proveedor_FE != 3 ) {//3=Interno
					if ($this->empresa->Nu_Enviar_Sunat_Automatic==0) {
						return $response;
					} else {//1=Enviar a sunat automaticamente
						$response = $this->sendFacturaVentaSunat($responseVenta['Last_ID_Documento_Cabecera'], 6, 'php', 'RC');
						return array_merge($response, array('sEnviarSunatAutomatic' => 'Si'));
					}
				} else {
					return $response;
				}
			}// ./ Generar Inventario
			if ( $arrVentaCabecera['ID_Tipo_Documento'] != 2 && $this->empresa->Nu_Tipo_Proveedor_FE != 3 ) {//3=Interno
				if ($this->empresa->Nu_Enviar_Sunat_Automatic==0) {
					return $responseVenta;
				} else {//1=Enviar a sunat automaticamente
					$response = $this->sendFacturaVentaSunat($responseVenta['Last_ID_Documento_Cabecera'], 6, 'php', 'RC');
					return array_merge($response, array('sEnviarSunatAutomatic' => 'Si'));
				}
			} else {
				return $responseVenta;
			}
		} else {
			return $responseVenta;
		}
	}

	public function actualizarVenta_Inventario($arrWhereVenta = '', $arrVentaCabecera = '', $arrDetalleVenta = '', $esEnlace = '', $ID_Documento_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrClienteNuevo = ''){
		$responseVenta = $this->VentaModel->actualizarVenta($arrWhereVenta, $arrVentaCabecera, $arrDetalleVenta, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrClienteNuevo);
		if ($responseVenta['status'] == 'success') {
			if ($Nu_Descargar_Inventario == '1'){//Si
				$arrVentaCabecera['ID_Tipo_Movimiento'] = 1;//Venta
				if ($arrVentaCabecera['ID_Tipo_Documento'] == '5')//N/C
					$arrVentaCabecera['ID_Tipo_Movimiento'] = 17;//ENTRADA POR DEVOLUCIÓN DEL CLIENTE
				return $this->MovimientoInventarioModel->crudMovimientoInventario($arrVentaCabecera['ID_Almacen'], $responseVenta['Last_ID_Documento_Cabecera'], 0, $arrDetalleVenta, $arrVentaCabecera['ID_Tipo_Movimiento'], 1, $arrWhereVenta, 1, 1);
			}
			return $responseVenta;
		} else {
			return $responseVenta;
		}
	}
    
	public function anularVenta($ID, $Nu_Enlace, $Nu_Descargar_Inventario, $iEstado, $sTipoBajaSunat, $Fe_Emision, $sSerieDocumento){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		if ( $sTipoBajaSunat != 'Interno' ) {
			$iEstado = ($iEstado == 8 || $iEstado == 7 ? 7 : $iEstado );
			$iDias = diferenciaFechasMultipleFormato( $Fe_Emision, dateNow('fecha') , 'dias' );
			if ( $iDias <= 7 ){
				if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
					$arrResponseAnular = $this->VentaModel->anularVenta($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario));
					if ($arrResponseAnular['status']=='error') {
						echo json_encode($arrResponseAnular);
						exit();
					}
					$this->sendFacturaVentaSunat($this->security->xss_clean($ID), $iEstado, 'json', $sTipoBajaSunat);
				} else {					
					$arrParams = array('ID_Documento_Cabecera' => $ID);
					if ( !empty($this->VentaModel->verificarSunatCDR($arrParams)) ) {
						$this->sendFacturaVentaSunat($this->security->xss_clean($ID), $iEstado, 'json', $sTipoBajaSunat, $Nu_Enlace, $Nu_Descargar_Inventario);
					} else {						
						$arrResponseFE = array(
							'status' => 'danger',
							'style_modal' => 'modal-danger',
							'message' => 'No se puede anular si no tiene CDR. Recuperar o esperar al día siguiente',
							'message_nubefact' => 'No se puede anular si no tiene CDR. Recuperar o esperar al día siguiente',
							'sStatus' => 'danger',
							'sMessage' => 'No se puede anular si no tiene CDR. Recuperar o esperar al día siguiente',
							'arrMessagePSE' => '',
							'sCodigo' => '',
						);
						echo json_encode($arrResponseFE);
						exit();
					}
				}
			} else {
				$arrResponseFE = array(
					'status' => 'error',
					'style_modal' => 'modal-danger',
					'message' => 'Solo se puede anular hasta 7 días atrás, emite una Nota de Crédito para anular la venta',
					'message_nubefact' => 'Solo se puede anular hasta 7 días atrás, emite una Nota de Crédito para anular la venta',
					'sStatus' => 'danger',
					'sMessage' => 'Solo se puede anular hasta 7 días atrás, emite una Nota de Crédito para anular la venta',
					'arrMessagePSE' => '',
					'sCodigo' => '',
				);
				echo json_encode($arrResponseFE);
				exit();
			}
		} else {			
			echo json_encode($this->VentaModel->anularVenta($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario)));
			exit();
		}
	}
	
	public function eliminarVenta($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VentaModel->eliminarVenta($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function imprimirVenta($ID){
		$data = $this->VentaModel->get_by_id($this->security->xss_clean($ID));
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		$this->load->view('Ventas/impresiones/impresion_comprobante', array(
			'venta' 		=> $data,
			'totalEnLetras'	=> $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),
		));
	}
    
	public function sendFacturaVentaSunat($ID, $Nu_Estado, $sTypeResponse='json', $sTipoBajaSunat = 'RC', $Nu_Enlace='', $Nu_Descargar_Inventario=''){
		if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 && $this->empresa->Nu_Estado_Pago_Sistema == 1 ) {//Sunat
			$arrParams = array(
				'iCodigoProveedorDocumentoElectronico' => 1,
				'iEstadoVenta' => $Nu_Estado,
				'iIdDocumentoCabecera' => $ID,
				'sEmailCliente' => '',
				'sTipoRespuesta' => $sTypeResponse,
				'sTipoBajaSunat' => $sTipoBajaSunat,
			);
			
			$response = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoSunat($arrParams);
			$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $response, $arrParams );
			
			if ( $Nu_Estado == 7 || $Nu_Estado == 11 ) {
				$Nu_Estado_Sunat = ($response['sStatus'] == 'success' ? 10 : 11);

				if ( $Nu_Estado_Sunat==10 ) {
					$arrResponseAnular = $this->VentaModel->anularVenta($ID, $Nu_Enlace, $Nu_Descargar_Inventario);
					if ($arrResponseAnular['status']=='error') {	
						if ($sTypeResponse=='php') {
							echo json_encode($arrResponseAnular);
							exit();
						} else {
							return $arrResponseAnular;
						}
					}
					$arrParams = array_merge($arrParams, array('iEstadoVenta' => $Nu_Estado_Sunat));
					$this->DocumentoElectronicoModel->cambiarEstadoDocumentoElectronico( $arrParams );
				} else {
					$arrParams = array_merge($arrParams, array('iEstadoVenta' => $Nu_Estado_Sunat));
					$this->DocumentoElectronicoModel->cambiarEstadoDocumentoElectronico( $arrParams );
					
					if ($sTypeResponse=='php') {
						return $response;
					} else {
						echo json_encode($response);
						exit();
					}
				}
			}

			if ($sTypeResponse=='php') {
				return $response;
			} else {
				echo json_encode($response);
				exit();
			}
		} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 && $this->empresa->Nu_Estado_Pago_Sistema == 1 ) { //Nubefact
			// Parametros de entrada
			$ID = $this->security->xss_clean($ID);
			$Nu_Estado	= $this->security->xss_clean($Nu_Estado);
			
			if ($Nu_Estado == 7 || $Nu_Estado == 11) {// Estado -> Anulado
				$arrData = $this->VentaModel->get_by_id_anulado($ID);

				$iDays = diferenciaFechasMultipleFormato($arrData[0]->Fe_Emision, dateNow('fecha'), 'dias');
				if ($iDays>7) {
					if ($sTypeResponse=='json') {
						$arrResponseFE = array(
							'status' => 'error',
							'style_modal' => 'modal-danger',
							'message' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
							'message_nubefact' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
							'sStatus' => 'danger',
							'sMessage' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
							'arrMessagePSE' => '',
							'sCodigo' => '',
						);
						echo json_encode($arrResponseFE);
						exit();
					} else {
						return array(
							'status' => 'error',
							'style_modal' => 'modal-danger',
							'message' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
							'message_nubefact' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
							'sStatus' => 'danger',
							'sMessage' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
							'arrMessagePSE' => '',
							'sCodigo' => '',
						);
					}
				}

				$iTipoComprobante = 2;//Boleta
				if ($arrData[0]->ID_Tipo_Documento == 3)//Factura
					$iTipoComprobante = 1;
				else if ($arrData[0]->ID_Tipo_Documento == 5)//N/Crédito
					$iTipoComprobante = 3;
				else if ($arrData[0]->ID_Tipo_Documento == 6)//N/Débito
					$iTipoComprobante = 4;
				
				$data = array(
					"operacion"	=> "generar_anulacion",
					"tipo_de_comprobante" => $iTipoComprobante,
					"serie" => $arrData[0]->ID_Serie_Documento,
					"numero" => $arrData[0]->ID_Numero_Documento,
					"motivo" => "ERROR DEL SISTEMA",
					"codigo_unico" => "",
				);
				
				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;
			} else {
				$arrData = $this->VentaModel->get_by_id($ID);
				if ( $arrData['sStatus'] == 'success' ) {
					$arrData = $arrData['arrData'];

					$iDays = diferenciaFechasMultipleFormato($arrData[0]->Fe_Emision, dateNow('fecha'), 'dias');
					if ($iDays>7) {
						if ($sTypeResponse=='json') {
							$arrResponseFE = array(
								'status' => 'error',
								'style_modal' => 'modal-danger',
								'message' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
								'message_nubefact' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
								'sStatus' => 'danger',
								'sMessage' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
								'arrMessagePSE' => '',
								'sCodigo' => '',
							);
							echo json_encode($arrResponseFE);
							exit();
						} else {
							return array(
								'status' => 'error',
								'style_modal' => 'modal-danger',
								'message' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
								'message_nubefact' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
								'sStatus' => 'danger',
								'sMessage' => 'La fecha de emision debe ser sólo hasta 7 días atrás',
								'arrMessagePSE' => '',
								'sCodigo' => '',
							);
						}
					}
					
					$ruta = $arrData[0]->Txt_FE_Ruta;
					$token = $arrData[0]->Txt_FE_Token;

					$iTipoNC = "";
					$iTipoND = "";
					
					$iTipoComprobante = 2;//Boleta
					if ($arrData[0]->ID_Tipo_Documento == 3) {//Factura
						$iTipoComprobante = 1;
					} else if ($arrData[0]->ID_Tipo_Documento == 5) {//N/Crédito
						$iTipoComprobante = 3;
						$iTipoNC = ($arrData[0]->Nu_Codigo_Motivo_Referencia != 10 ? $arrData[0]->Nu_Codigo_Motivo_Referencia : 1);
					} else if ($arrData[0]->ID_Tipo_Documento == 6) {//N/Débito
						$iTipoComprobante = 4;
						$iTipoND = $arrData[0]->Nu_Codigo_Motivo_Referencia;
					}
					
					$iTipoComprobanteModifica = "";
					if (!empty($arrData[0]->ID_Tipo_Documento_Modificar)) {
						$iTipoComprobanteModifica = 2;//BOLETAS DE VENTA ELECTRÓNICAS
						if ($arrData[0]->ID_Tipo_Documento_Modificar == 3)//Factura
							$iTipoComprobanteModifica = 1;//FACTURAS ELECTRÓNICAS
					}

					// Verificar existencia de comprobante
					$objParams = new stdClass();
					$objParams->iTipoProveedorFE = $this->empresa->Nu_Tipo_Proveedor_FE;
					$objParams->sTipoOperacion = "consultar_comprobante";
					$objParams->ruta = $ruta;
					$objParams->token = $token;
					$objParams->iIdEmpresa = $arrData[0]->ID_Empresa;
					$objParams->iIdDocumentoCabecera = $arrData[0]->ID_Documento_Cabecera;
					$objParams->iTipoDocumento = $iTipoComprobante;
					$objParams->sSerieDocumento = $arrData[0]->ID_Serie_Documento;
					$objParams->iNumeroDocumento = $arrData[0]->ID_Numero_Documento;
					
					$responseExisteComprobante = $this->DocumentoElectronicoModel->consultarComprobanteExistent($objParams);
					if ( $responseExisteComprobante['sStatus'] == 'success' ) {
						if ($sTypeResponse=='php') {
							return $responseExisteComprobante;
						} else {
							echo json_encode($responseExisteComprobante);
							exit();
						}
					}
					// ./ Verificar existencia de comprobante
					
					$i = 0;
					$Ss_SubTotal_Producto = 0.00;
					$Ss_Impuesto_Producto = 0.00;
					$Ss_IGV_Producto = 0.00;
					$Ss_IGV_Producto_Linea = 0.00;
					$Ss_Descuento_Producto = 0.00;
					$Ss_Total_Producto = 0.00;
					$Ss_Gravada = 0.00;
					$Ss_Exonerada = 0.00;
					$Ss_Inafecto = 0.00;
					$Ss_Gratuita = 0.00;
					$Ss_IGV = 0.00;
					$Ss_Total = 0.00;
					$option_impuesto_producto = '';
					
					$fDescuento_Producto = 0;
					$fDescuento_Total_Producto = 0;
					$globalImpuesto = 0;
					$iDescuentoGravada = 0;
					$iDescuentoInafecto = 0;
					$iDescuentoExonerada = 0;
					$iDescuentoGratuita = 0;
					$iDescuentoGlobalImpuesto = 0;
					
					$fTotalIcbper = 0.00;
					$Po_IGV = "";
            		$Ss_Impuesto = 0;
            		$Ss_Gravada = 0.00;
                	
					$iNumImpuestoDescuento = 0;
					$iNumImpuestoDescuentoIGV = 0;
					$iNumImpuestoDescuentoEXO = 0;
					$fImpuestoConfiguracionIGV = 1;
            		$fDescuentoItem = 0;
					$iCapturaI = -1;
					$fTotalCapturaIcbper = 0;
					foreach ($arrData as $row) {
						if ( $row->ID_Impuesto_Icbper == 1 )
							$fTotalIcbper += $row->Ss_Icbper;

						$Ss_Precio_VU = round($row->Ss_Precio, 6);
						if ($row->Nu_Tipo_Impuesto == 1){//IGV
                			$Ss_Impuesto = $row->Ss_Impuesto;
							$Po_IGV = $row->Po_Impuesto;
							$Ss_Precio_VU = round($row->Ss_Precio / $row->Ss_Impuesto, 6);
							$Ss_IGV += $row->Ss_Impuesto_Producto;
						
							$Ss_Gravada += $row->Ss_SubTotal_Producto;

							$iNumImpuestoDescuentoIGV = 1;
							$fImpuestoConfiguracionIGV = $row->Ss_Impuesto;
							$fDescuentoItem += $row->Ss_Descuento_Producto;
						} else if ($row->Nu_Tipo_Impuesto == 2){//Inafecto - Operación Onerosa
							$Ss_Inafecto += $row->Ss_SubTotal_Producto;
						} else if ($row->Nu_Tipo_Impuesto == 3){//Exonerado - Operación Onerosa
							$Ss_Exonerada += $row->Ss_SubTotal_Producto;
                			$iNumImpuestoDescuentoEXO = 1;
							$fDescuentoItem += $row->Ss_Descuento_Producto;
						} else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
							$Ss_Gratuita += $row->Ss_SubTotal_Producto;
						}
						
						$data_detalle["items"][$i]["unidad_de_medida"]			= $row->Nu_Sunat_Codigo_UM;
						$data_detalle["items"][$i]["codigo"]					= $row->Nu_Codigo_Barra;
						$data_detalle["items"][$i]["codigo_producto_sunat"]		= $row->Nu_Codigo_Producto_Sunat;//Nuevo
						$data_detalle["items"][$i]["descripcion"]				= $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
						$data_detalle["items"][$i]["cantidad"]					= $row->Qt_Producto;
						$data_detalle["items"][$i]["valor_unitario"]			= $Ss_Precio_VU;//Precio sin IGV
						$data_detalle["items"][$i]["precio_unitario"]			= $row->Ss_Precio;//Precio con IGV
						$data_detalle["items"][$i]["descuento"] 				= $row->Ss_Descuento_Producto;
						$data_detalle["items"][$i]["subtotal"]					= $row->Ss_SubTotal_Producto;
						$data_detalle["items"][$i]["tipo_de_igv"]				= $row->Nu_Valor_Fe_Impuesto;
						$data_detalle["items"][$i]["igv"]						= $row->Ss_Impuesto_Producto;
						$data_detalle["items"][$i]["total"] 					= $row->Ss_Total_Producto;
						$data_detalle["items"][$i]["anticipo_regularizacion"]	= false;
						$data_detalle["items"][$i]["anticipo_documento_serie"]	= "";
						$data_detalle["items"][$i]["anticipo_documento_numero"] = "";
						$data_detalle["items"][$i]["impuesto_bolsas"] = ($row->ID_Impuesto_Icbper == 0 ? 0.00 : $row->Ss_Icbper);
						$i++;
					}
						
					/*
					Tipo de la GUÍA DE REMISIÓN RELACIONADA. Ejemplo: 1
					1 = GUÍA DE REMISIÓN REMITENTE
					2 = GUÍA DE REMISIÓN TRANSPORTISTA
					*/
					$data_guias = array();
					$cadena_de_texto = trim($arrData[0]->Txt_Garantia);
					if ( substr($cadena_de_texto, -1) == ',' )
						$cadena_de_texto = substr($cadena_de_texto, 0, -1);
					$cadena_buscada = '-';
					$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
					if ( strlen($arrData[0]->Txt_Garantia) > 5 && $posicion_coincidencia !== false) {
						$arrCadena = explode(',',$arrData[0]->Txt_Garantia);
						$i = 0;
						foreach ($arrCadena as $row) {
							$arrSerieNumero = explode('-', $row);
							if ( strlen(trim($arrSerieNumero[0])) == 4 && isset($arrSerieNumero[1]) ) {
								$serie = trim($arrSerieNumero[0]);
								$numero = substr(trim($arrSerieNumero[1]), 0, 8);
								$data_guias["guias"][$i]["guia_tipo"] = 1;
								$data_guias["guias"][$i]["guia_serie_numero"] = $serie . '-' . $numero;
							}
							$i++;
						}
					} else {
						$arrParamsGuia = array('ID_Documento_Cabecera' => $arrData[0]->ID_Documento_Cabecera);
						$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParamsGuia);
						if ($arrResponseDocument['sStatus'] == 'success'){
							$i = 0;
							foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
								$data_guias["guias"][$i]["guia_tipo"] = 1;
								$data_guias["guias"][$i]["guia_serie_numero"] = $rowEnlace->_ID_Serie_Documento . '-' . $rowEnlace->ID_Numero_Documento;
								$i++;
							}
						}
					}

					$iIdClienteTipoDocumentoIdentidad = $arrData[0]->Nu_Sunat_Codigo_TDI;
					$sNombreCliente = $arrData[0]->No_Entidad;
					if ($arrData[0]->ID_Tipo_Documento == 4 && (empty($arrData[0]->Nu_Documento_Identidad) || empty($arrData[0]->No_Entidad)) && ($arrData[0]->Ss_Total) < 700 ) {
						$iIdClienteTipoDocumentoIdentidad = '-';
						$sNombreCliente = 'vacio';
					}

					$sDiasCredito = '';
					$arrVentasCreditoCuotas = array();
					if ( $arrData[0]->No_Codigo_Medio_Pago_Sunat_PLE == '0' ) {
						$arrVentasCreditoCuotas = array(
							'venta_al_credito' => array(
								0 => array(
									'cuota' => 1,
									'fecha_de_pago' => $arrData[0]->Fe_Vencimiento,
									'importe' => $arrData[0]->Ss_Total_Saldo,
									//'importe' => ($arrData[0]->ID_Tipo_Documento != 5 ? $arrData[0]->Ss_Total : $arrData[0]->Ss_Total_Saldo),
								)
							)
						);
					}
					
					//nubefact
					$sConcatenarMultiplesMedioPago = strtoupper($arrData[0]->No_Medio_Pago);
					if ( $arrData[0]->No_Codigo_Medio_Pago_Sunat_PLE == '006' )
						$sConcatenarMultiplesMedioPago = 'PAGO CON TARJETA';//Tarjeta de crédito
					
					$iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);

					$fDescuentoTotalOperacion = 0.00;
					$fDescuentoTotalOperacionIGV = 0.00;
					$fDescuentoTotalOperacionEXO = 0.00;
					if($arrData[0]->Ss_Descuento>0.00){
						$fDescuentoTotalOperacion = ($arrData[0]->Ss_Descuento / $iNumImpuestoDescuento);

						if ( $iNumImpuestoDescuentoEXO == 1 ) {
							$Ss_Exonerada = $Ss_Exonerada - $fDescuentoTotalOperacion;
							$fDescuentoTotalOperacionEXO = $fDescuentoTotalOperacion;
						}

						if ( $iNumImpuestoDescuentoIGV == 1 ) {
							$Ss_Gravada = $Ss_Gravada - $fDescuentoTotalOperacion;
							$Ss_IGV = ($Ss_Gravada * $fImpuestoConfiguracionIGV) - $Ss_Gravada;
							$fDescuentoTotalOperacionIGV = $fDescuentoTotalOperacion;
						}

						$fDescuentoTotalOperacion = $fDescuentoTotalOperacionEXO + $fDescuentoTotalOperacionIGV;
					}

					$Txt_Glosa = $arrData[0]->Txt_Glosa;
					$fTotal = $arrData[0]->Ss_Total;
					if($arrData[0]->Ss_Retencion > 0.00){
						$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Retencion, 2) . ' <br>';
						$Txt_Glosa .= '<b>Base imponible de la retención:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
						$Txt_Glosa .= '<b>Monto de la retención:</b> ' . round($arrData[0]->Ss_Retencion, 2) . ' <br>';
					}

					$iPoDetraccion=0;
					if($arrData[0]->Ss_Detraccion > 0.00){
						$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Detraccion, 2) . ' <br>';
						$Txt_Glosa .= '<b>Base imponible de la detracción:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
						$Txt_Glosa .= '<b>Monto de la detracción:</b> ' . round($arrData[0]->Ss_Detraccion, 2) . ' <br>';
						$iPoDetraccion=$arrData[0]->Po_Detraccion;
					}

					$data_cabecera = array(
						"operacion"							=> "generar_comprobante",
						"tipo_de_comprobante"               => $iTipoComprobante,
						"serie"                             => $arrData[0]->ID_Serie_Documento,
						"numero"							=> $arrData[0]->ID_Numero_Documento,
						"sunat_transaction"					=> $arrData[0]->Nu_Codigo_Pse_Tipo_Transaccion,
						"cliente_tipo_de_documento"			=> $iIdClienteTipoDocumentoIdentidad,
						"cliente_numero_de_documento"		=> $arrData[0]->Nu_Documento_Identidad,
						"cliente_denominacion"              => $sNombreCliente,
						"cliente_direccion"                 => $arrData[0]->Txt_Direccion_Entidad,
						"cliente_email"                     => "",
						"cliente_email_1"                   => "",
						"cliente_email_2"                   => "",
						"fecha_de_emision"                  => $arrData[0]->Fe_Emision,
						"fecha_de_vencimiento"              => $arrData[0]->Fe_Vencimiento,
						"moneda"                            => $arrData[0]->Nu_Valor_Fe_Moneda,
						"tipo_de_cambio"                    => ($arrData[0]->Nu_Valor_Fe_Moneda == 1 ? "" : $arrData[0]->Ss_Tipo_Cambio),
						"porcentaje_de_igv"                 => $Po_IGV,
						"descuento_global"                  => $fDescuentoTotalOperacion,//Este campo solo se usa cuando el descuento es global y no por detalle
						"total_descuento" 					=> $fDescuentoItem,
						"total_anticipo"                    => "",
						"total_gravada"                     => $Ss_Gravada,
						"total_inafecta"                    => $Ss_Inafecto,
						"total_exonerada"                   => $Ss_Exonerada,
						"total_igv"                         => $Ss_IGV,
						"total_gratuita"                    => $Ss_Gratuita,
						"total_otros_cargos"                => "",
						"total"                             => ($arrData[0]->Ss_Total - $Ss_Gratuita),
						"percepcion_tipo"                   => "",
						"percepcion_base_imponible"         => "",
						"total_percepcion"                  => "",
						"total_incluido_percepcion"         => "",
						"retencion_tipo"=> ($arrData[0]->Nu_Retencion == 0 ? "" : "1"),
						"retencion_base_imponible"=> ($arrData[0]->Nu_Retencion == 0 ? "" : ($Ss_Gratuita == 0 ? $arrData[0]->Ss_Total : 0)),
						"total_retencion"=> ($arrData[0]->Nu_Retencion == 0 ? "" : $arrData[0]->Ss_Retencion),
						"total_impuestos_bolsas" => $fTotalIcbper,
						"detraccion_total"                  => ($arrData[0]->Nu_Detraccion == 0 ? "" : $arrData[0]->Ss_Detraccion),
						"detraccion_porcentaje"             => ($arrData[0]->Nu_Detraccion == 0 ? "" : $iPoDetraccion),
						"detraccion"                        => ($arrData[0]->Nu_Detraccion == 0 ? false : true),
						"observaciones"                     => $Txt_Glosa,
						"documento_que_se_modifica_tipo"    => $iTipoComprobanteModifica,
						"documento_que_se_modifica_serie"   => $arrData[0]->ID_Serie_Documento_Modificar,
						"documento_que_se_modifica_numero"  => $arrData[0]->ID_Numero_Documento_Modificar,
						"tipo_de_nota_de_credito"           => $iTipoNC,
						"tipo_de_nota_de_debito"            => $iTipoND,
						"enviar_automaticamente_a_la_sunat" => true,
						"enviar_automaticamente_al_cliente" => false,
						"codigo_unico"                      => $iTipoComprobante . $arrData[0]->ID_Serie_Documento . $arrData[0]->ID_Numero_Documento,
						"condiciones_de_pago"               => $sDiasCredito,
						"medio_de_pago"                     => $sConcatenarMultiplesMedioPago,
						"placa_vehiculo"                    => $arrData[0]->No_Placa_FE,
						"orden_compra_servicio"             => $arrData[0]->No_Orden_Compra_FE,
						"tabla_personalizada_codigo"        => "",
						"formato_de_pdf"                    => $arrData[0]->No_Formato_PDF,
					);
					$data = array_merge($data_cabecera, $data_detalle, $data_guias, $arrVentasCreditoCuotas);
				} else {
					if ($sTypeResponse=='json') {
						echo json_encode($arrData);
						exit();
					} else {
						return $arrData;
					}
				}// if - else arrdata modal get documento
			}// if - else estado de documento
			
			$data_json = json_encode($data);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ruta);
			curl_setopt(
				$ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Token token="'.$token.'"',
				'Content-Type: application/json',
				)
			);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$respuesta = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );
			curl_close($ch);
			//Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
			$accepted_response = array( 200, 301, 302 );
			if( in_array( $httpcode, $accepted_response ) ) {
				$leer_respuesta = json_decode($respuesta, true);
				if ( (!isset($leer_respuesta['errors']) && !empty($leer_respuesta)) || isset($leer_respuesta['codigo']) ) {
					if ($Nu_Estado == 6 || $Nu_Estado == 8 || $Nu_Estado == 9)// 6 = Completado y 8 = Completado enviado
						$iTipoStatus = 8;
					else if ($Nu_Estado == 7 || $Nu_Estado == 10 || $Nu_Estado == 11)
						$iTipoStatus = 10;//10 = Anulado enviado
					
					if ( !isset($leer_respuesta['codigo']) ) {
						$arrParametrosNubefact = array (
							'url_pdf' => $leer_respuesta['enlace_del_pdf'],
							'url_xml' => $leer_respuesta['enlace_del_xml'],
							'url_cdr' => $leer_respuesta['enlace_del_cdr'],
							'url_nubefact' => $leer_respuesta['enlace'],
							'Txt_QR' => isset($leer_respuesta['cadena_para_codigo_qr']) ? $leer_respuesta['cadena_para_codigo_qr'] : '',
							'Txt_Hash' => isset($leer_respuesta['codigo_hash']) ? $leer_respuesta['codigo_hash'] : '',
						);
					} else {
						$arrParametrosNubefact = array ();
					}
					$arrResponseCambiarEstadoDocumento = $this->VentaModel->changeStatusSunat($ID, $iTipoStatus, $arrParametrosNubefact);
					if ( $arrResponseCambiarEstadoDocumento['sStatus'] == 'success' ) {
						if ( $iTipoStatus == 8 && !empty($arrData[0]->Txt_Email_Entidad) )
							$response_email = $this->sendCorreoFacturaVentaSUNAT($ID, $arrData[0]->Txt_Email_Entidad);

						if ($sTypeResponse=='json') {
							$arrResponseFE = array(
								'sStatus' => !isset($leer_respuesta['codigo']) ? 'success' : 'warning',
								'status' => !isset($leer_respuesta['codigo']) ? 'success' : 'warning',
								'style_modal' => !isset($leer_respuesta['codigo']) ? 'modal-success' : 'modal-warning',
								'message' => !isset($leer_respuesta['codigo']) ? 'Comprobante enviado' : 'Venta guardada pero ' . $leer_respuesta['errors'] . ' (JSON)',
								'arrMessagePSE' => !isset($leer_respuesta['errors']) ? 'Comprobante aceptado' : $leer_respuesta['errors'] . ' (JSON)',
								'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
								'arrData' => $data
							);
							if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
								$arrParams['iIdDocumentoCabecera'] = $this->security->xss_clean($ID);
								$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
							} else {
								$arrParams['iIdDocumentoCabecera'] = $this->security->xss_clean($ID);
								$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE='', $arrParams );
							}
							echo json_encode($arrResponseFE);
							exit();
						} else {
							return array(
								'sStatus' => !isset($leer_respuesta['codigo']) ? 'success' : 'warning',
								'status' => !isset($leer_respuesta['codigo']) ? 'success' : 'warning',
								'style_modal' => !isset($leer_respuesta['codigo']) ? 'modal-success' : 'modal-warning',
								'message' => !isset($leer_respuesta['codigo']) ? 'Comprobante enviado' : 'Venta guardada pero ' . $leer_respuesta['errors'],
								'sMessage' => !isset($leer_respuesta['codigo']) ? 'Comprobante enviado' : 'Venta guardada pero ' . $leer_respuesta['errors'],
								'arrMessagePSE' => !isset($leer_respuesta['errors']) ? 'Comprobante aceptado' : $leer_respuesta['errors'],
								'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
								'arrData' => $data
							);
						}
					} else {
						if ($sTypeResponse=='json') {
							echo json_encode($arrResponseCambiarEstadoDocumento);
							exit();
						} else {
							return $arrResponseCambiarEstadoDocumento;
						}
					}// /. if - else cambiar estado documento despues de enviarlo nubefact
				} else {
					if ($sTypeResponse=='json') {
						$arrResponseFE = array(
							'status' => 'error',
							'style_modal' => 'modal-danger',
							'message' => (!empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT (JSON)'),
							'message_nubefact' => $leer_respuesta['errors'],
							'sStatus' => 'danger',
							'sMessage' => (!empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT (JSON)'),
							'arrMessagePSE' => !empty($leer_respuesta['errors']) ? $leer_respuesta['errors'] : 'Venta guardada pero no se envio a SUNAT (JSON)',
							'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
							'arrData' => $data
						);
						if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
							$arrParams['iIdDocumentoCabecera'] = $this->security->xss_clean($ID);
							$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
						} else {
							$arrParams['iIdDocumentoCabecera'] = $this->security->xss_clean($ID);
							$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE='', $arrParams );
						}
						echo json_encode($arrResponseFE);
						exit();
					} else {
						return array(
							'status' => 'error',
							'style_modal' => 'modal-danger',
							'message' => (!empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT'),
							'message_nubefact' => $leer_respuesta['errors'],
							'sStatus' => 'danger',
							'sMessage' => (!empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT'),
							'arrMessagePSE' => !empty($leer_respuesta['errors']) ? $leer_respuesta['errors'] : 'Venta guardada pero no se envio a SUNAT',
							'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
							'arrData' => $data
						);
					}
				}
			} else {
				if ($sTypeResponse=='json') {
					$arrResponseFE = array(
						'status' => 'error',
						'style_modal' => 'modal-danger',
						'message' => 'No hay conexión. ' . ( isset($leer_respuesta) && !empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT (JSON)'),
						'message_nubefact' => $respuesta,
						'sStatus' => 'danger',
						'sMessage' => 'No hay conexión. ' . (isset($leer_respuesta) && !empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT (JSON)'),
						'arrMessagePSE' => 'No hay conexión. Venta guardada pero no se envio a SUNAT (JSON)',
						'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
						'arrData' => $data,
						'allMessagePSE' => (isset($leer_respuesta) ? $leer_respuesta : 'sin respuesta PSE'),
						'httpcode' => $httpcode						
					);
					if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
						$arrParams['iIdDocumentoCabecera'] = $this->security->xss_clean($ID);
						$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
					} else {
						$arrParams['iIdDocumentoCabecera'] = $this->security->xss_clean($ID);
						$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE='', $arrParams );
					}
					echo json_encode($arrResponseFE);
					exit();
				} else {
					return array(
						'status' => 'error',
						'style_modal' => 'modal-danger',
						'message' => 'No hay conexión. ' . (isset($leer_respuesta) && !empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT'),
						'message_nubefact' => $respuesta,
						'sStatus' => 'danger',
						'sMessage' => 'No hay conexión. ' . (isset($leer_respuesta) && !empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT'),
						'arrMessagePSE' => 'No hay conexión. Venta guardada pero no se envio a SUNAT',
						'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
						'arrData' => $data,
						'allMessagePSE' => (isset($leer_respuesta) ? $leer_respuesta : 'sin respuesta PSE'),
						'httpcode' => $httpcode
					);
				}
			}
		} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 ) {
			if ($sTypeResponse=='json') {
				$arrResponseFE = array(
					'status' => 'error',
					'style_modal' => 'modal-danger',
					'message' => 'Corte de servicio por falta de pago, tienen hasta 6 días calendarios para enviar el documento pasada la fecha no se podrá enviar el comprobante',
					'message_nubefact' => 'Corte de servicio por falta de pago, tienen hasta 6 días calendarios para enviar el documento pasada la fecha no se podrá enviar el comprobante',
					'sStatus' => 'danger',
					'sMessage' => 'Corte de servicio por falta de pago, tienen hasta 6 días calendarios para enviar el documento pasada la fecha no se podrá enviar el comprobante',
					'arrMessagePSE' => '',
					'sCodigo' => '',
				);
				echo json_encode($arrResponseFE);
				exit();
			} else {
				return array(
					'status' => 'error',
					'style_modal' => 'modal-danger',
					'message' => 'Corte de servicio por falta de pago, tienen hasta 6 días calendarios para enviar el documento pasada la fecha no se podrá enviar el comprobante',
					'message_nubefact' => 'Corte de servicio por falta de pago, tienen hasta 6 días calendarios para enviar el documento pasada la fecha no se podrá enviar el comprobante',
					'sStatus' => 'danger',
					'sMessage' => 'Corte de servicio por falta de pago, tienen hasta 6 días calendarios para enviar el documento pasada la fecha no se podrá enviar el comprobante',
					'arrMessagePSE' => '',
					'sCodigo' => '',
				);
			}
		} // ./ if - else tipo de envio proveedor fe sunat
    }
    
	public function sendCorreoFacturaVentaSUNAT($id=0, $Txt_Email_Entidad=''){
		// Parametros de entrada
		$iIdDocumentoCabecera = !isset($_POST['ID']) ? $id : $this->input->post('ID');
		$arrData = $this->VentaModel->get_by_id($iIdDocumentoCabecera);
		if ( $arrData['sStatus'] == 'success' ) {
			$arrData = $arrData['arrData'];

			$this->load->library('email');

			$data = array();

			$sNombreTipoDocumentoVenta = strtoupper($arrData[0]->No_Tipo_Documento) . ' ELECTRÓNICA ';
			if($arrData[0]->ID_Tipo_Documento == 2)
				$sNombreTipoDocumentoVenta = strtoupper($arrData[0]->No_Tipo_Documento);

			$data["No_Documento"]	= $sNombreTipoDocumentoVenta . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento;
			$data["Fe_Emision"] 	= ToDateBD($arrData[0]->Fe_Emision);
			$data["No_Signo"]		= $arrData[0]->No_Signo;
			$data["Ss_Total"]		= $arrData[0]->Ss_Total;
			$data["Txt_Medio_Pago"]	= $arrData[0]->No_Medio_Pago;
			$data["Nu_Tipo"]		= $arrData[0]->Nu_Tipo;
			$data["Ss_Total_Saldo"]	= $arrData[0]->Ss_Total_Saldo;
			
			$data["No_Entidad"] = $arrData[0]->No_Entidad;
			
			$data["No_Empresa"] 					= $this->empresa->No_Empresa;
			$data["Nu_Documento_Identidad_Empresa"] = $this->empresa->Nu_Documento_Identidad;
			
			$data["url_comprobante"] = (!empty($arrData[0]->Txt_Url_Comprobante) ? $arrData[0]->Txt_Url_Comprobante : '');
			if($arrData[0]->ID_Tipo_Documento == 2)
				$data["url_comprobante"] = (!empty($arrData[0]->Txt_Url_PDF) ? $arrData[0]->Txt_Url_PDF : '');

			$asunto = $data["No_Documento"] . ' ' . $this->empresa->No_Empresa . ' | ' . $this->empresa->Nu_Documento_Identidad;
			
			if($arrData[0]->ID_Tipo_Documento != 2)
				$message = $this->load->view('correos/documentos_electronicos', $data, true);
			else
				$message = $this->load->view('correos/nota_venta', $data, true);

			$this->email->from('noreply@laesystems.com', $this->empresa->No_Empresa);//de
			
			if ( !isset($_POST['ID']) )
				$this->email->to($Txt_Email_Entidad);//para
			else
				$this->email->to($this->input->post('Txt_Email'));//para
				
			$this->email->subject($asunto);
			$this->email->message($message);
			if (!empty($arrData[0]->Txt_Url_PDF))
				$this->email->attach($arrData[0]->Txt_Url_PDF);
			if (!empty($arrData[0]->Txt_Url_XML))
				$this->email->attach($arrData[0]->Txt_Url_XML);
			if (!empty($arrData[0]->Txt_Url_CDR))
				$this->email->attach($arrData[0]->Txt_Url_CDR);
			$this->email->set_newline("\r\n");

			$isSend = $this->email->send();
			
			if($isSend) {
				$peticion = array(
					'status' => 'success',
					'style_modal' => 'modal-success',
					'message' => 'Correo enviado',
				);
			} else {
				$peticion = array(
					'status' => 'error',
					'style_modal' => 'modal-danger',
					'message' => 'No se pudo enviar el correo, inténtelo más tarde.',
					'sMessageErrorEmail' => $this->email->print_debugger(),
				);
			}// if - else envio email
			if (isset($_POST['ID']))
				echo json_encode($peticion);
		} else {
			echo json_encode($arrData);
		}// if - else arrdata modal get documento
	}

	public function generarRepresentacionInternaPDF($ID){
		//$ID = base64_decode($ID);//encriptacion
		$arrData = $this->ImprimirTicketModel->formatoImpresionTicket($ID);
		if ( $arrData[0]->No_Formato_PDF!='TICKET' ) {
			$arrData = $this->VentaModel->get_by_id($ID);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];
				
				$this->load->library('EnLetras', 'el');
				$EnLetras = new EnLetras();
				
				$this->load->library('Pdf');
				
				$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
				
				// Array para medio de pago
				$No_Codigo_Medio_Pago_Sunat_PLE = '';
				$arrDataMediosPago = $this->ImprimirTicketModel->obtenerComprobanteMedioPago($ID);
				$sConcatenarMultiplesMedioPago = '';
				if ( $arrDataMediosPago['sStatus'] == 'success' ) {
					$No_Codigo_Medio_Pago_Sunat_PLE = $arrDataMediosPago['arrData'][0]->No_Codigo_Medio_Pago_Sunat_PLE;
					foreach ($arrDataMediosPago['arrData'] as $row)
						$sConcatenarMultiplesMedioPago .= $row->No_Medio_Pago . ' [' . $row->Txt_Medio_Pago . ']: ' . ($No_Codigo_Medio_Pago_Sunat_PLE != '0' ? $arrData[0]->No_Signo  . ' ' . $row->Ss_Total_Medio_Pago : '') . ', ';
					$sConcatenarMultiplesMedioPago = substr($sConcatenarMultiplesMedioPago, 0, -2);
				}

				ob_start();
				$file = $this->load->view('Ventas/pdf/RepresentacionInternaView', array(
					'arrDataEmpresa' => $arrData,
					'arrData' => $arrData,
					'totalEnLetras'	=> $EnLetras->ValorEnLetras($arrData[0]->Ss_Total, $arrData[0]->No_Moneda),
					'sConcatenarMultiplesMedioPago' => $sConcatenarMultiplesMedioPago,
				));
				$html = ob_get_contents();
				ob_end_clean();
				
				$pdf->SetAuthor('Laesystems');
				$pdf->SetTitle('laesystems_Representacion_Interna_' . $arrData[0]->ID_Tipo_Documento . '_' . $arrData[0]->ID_Serie_Documento . '_' . $arrData[0]->ID_Numero_Documento);
			
				$pdf->SetPrintHeader(false);
				$pdf->SetPrintFooter(false);
				
				$pdf->AddPage();
					
				$sUrlImagen = '';
				if($arrData[0]->Nu_Logo_Empresa_Ticket==1) {//1=Si muestra logo
					$sNombreLogo=str_replace(' ', '_', $arrData[0]->No_Logo_Empresa);
					$sNombreLogoAlmacen=str_replace(' ', '_', $arrData[0]->No_Logo_Almacen);
					if ($arrData[0]->Nu_MultiAlmacen == 0) {
						$sRutaArchivoLogoCliente = $this->logo_cliente_path . $sNombreLogo;
						$sUrlImagen = $arrData[0]->No_Imagen_Logo_Empresa;
					} else {
						$sRutaArchivoLogoCliente = $this->logo_cliente_logos_empresa_almacen_path . $sNombreLogoAlmacen;
						$sUrlImagen = $arrData[0]->No_Logo_Url_Almacen;
					}
				}

				$sCssFontFamily='Arial';
				$format_header = '<table border="0" cellspacing="0" cellpadding="0">';
					$format_header .= '<tr>';
						$format_header .= '<td rowspan="4" style="width: 55%; text-align: center; align-content: center;"><br><br>';
							if($arrData[0]->Nu_Logo_Empresa_Ticket==1) {//1=Si muestra logo
								$format_header .= '<img style="height: ' . $arrData[0]->Nu_Height_Logo_Ticket . 'px; width: ' . $arrData[0]->Nu_Width_Logo_Ticket . 'px;" src="' . $sUrlImagen . '">';
							} else {
								$format_header .= '&nbsp;';
							}
						$format_header .= '</td>';
						$format_header .= '<td colspan="2" style="width: 45%; text-align: center; background-color:#e4e4e4; border-width: 1px; border-color: #7d7d7d; border-left-color:#7d7d7d; border-right-color:#7d7d7d; border-bottom-color:#f2f2f2;">';
							$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . strtoupper($arrData[0]->No_Tipo_Documento) . ($arrData[0]->ID_Tipo_Documento != 2 ? ' ELECTRÓNICA' : '') . '</b></label>';
						$format_header .= '</td>';
					$format_header .= '</tr>';
					$format_header .= '<tr>';
						$format_header .= '<td style="text-align: center; background-color:#e4e4e4; border-left-color: #000 !important; border-bottom-color: #000 !important; border-right-color:#f2f2f2;">';
							$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>RUC ' . $arrData[0]->Nu_Documento_Identidad_Empresa . '</b></label>';
						$format_header .= '</td>';
						$format_header .= '<td style="text-align: center; background-color:#e4e4e4; border-right-color: #000 !important; border-bottom-color: #000 !important;">';
							$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT) . '</b></label>';
						$format_header .= '</td>';
					$format_header .= '</tr>';
					$format_header .= '<tr>';
						$format_header .= '<td colspan="2" style="text-align: right; line-height: 95%;">';
							$format_header .= '<label style="font-size: 7px; font-family: "' . $sCssFontFamily . '", Times, serif;"><br><b>' . $arrData[0]->No_Empresa . '</b></label>';
						$format_header .= '</td>';
					$format_header .= '</tr>';
					$format_header .= '<tr>';
						$format_header .= '<td colspan="2" style="text-align: right; line-height: 105%;">';
							$format_header .= '<label style="font-size: 7px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>DIRECCIÓN:</b> ' . $arrData[0]->Txt_Direccion_Empresa . '</label>';
							if( $arrData[0]->Txt_Direccion_Empresa != $arrData[0]->Txt_Direccion_Almacen ) 
								$format_header .= '<br><label style="font-size: 7px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>SUCURSAL:</b> ' . $arrData[0]->Txt_Direccion_Almacen . '</label>';
							if( !empty($arrData[0]->Txt_Slogan_Empresa) )
								$format_header .= '<br><label style="font-size: 8px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . $arrData[0]->Txt_Slogan_Empresa . '</label>';
							if( !empty($arrData[0]->No_Dominio_Empresa) || !empty($arrData[0]->Txt_Email_Empresa) )
								$format_header .= '<br><label style="font-size: 8px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . (!empty($arrData[0]->No_Dominio_Empresa) ? $arrData[0]->No_Dominio_Empresa . ' <br>' : '') . (!empty($arrData[0]->Txt_Email_Empresa) ? '<b>Email:</b> ' . $arrData[0]->Txt_Email_Empresa . ' ' : '') . '</label>';
							if( !empty($arrData[0]->Nu_Celular_Empresa) || !empty($arrData[0]->Nu_Telefono_Empresa) )
								$format_header .= '<br><label style="font-size: 8px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . (!empty($arrData[0]->Nu_Celular_Empresa) ? '<b>Celular:</b> ' . $arrData[0]->Nu_Celular_Empresa . ' ' : '') . (!empty($arrData[0]->Nu_Telefono_Empresa) ? '<b>Teléfono:</b> ' . $arrData[0]->Nu_Telefono_Empresa . ' ' : '') . '</label>';
						$format_header .= '</td>';
					$format_header .= '</tr>';

				$format_header .= '</table>';
				
				$pdf->writeHTML($format_header, true, 0, true, 0);
				
				$pdf->setFont('helvetica', '', 7);
				$pdf->writeHTML($html, true, false, true, false, '');

				$file_name = 'laesystems_Representacion_Interna_' . $arrData[0]->ID_Tipo_Documento . '_' . $arrData[0]->ID_Serie_Documento . '_' . $arrData[0]->ID_Numero_Documento . '.pdf';
				$pdf->Output($file_name, 'I');
			} else {
				exit();
			}
		} else {
			$this->generarRepresentacionInternaTicketPDF($ID);
		}
	}

	public function generarRepresentacionInternaTicketPDF($ID){
		$arrData = $this->ImprimirTicketModel->formatoImpresionTicket($ID);
		if ( $arrData[0]->No_Formato_PDF=='TICKET' ) {
			// Array para medio de pago
			$No_Codigo_Medio_Pago_Sunat_PLE = '';
			$arrDataMediosPago = $this->ImprimirTicketModel->obtenerComprobanteMedioPago($ID);
			$sConcatenarMultiplesMedioPago = '';
			if ( $arrDataMediosPago['sStatus'] == 'success' ) {
				$No_Codigo_Medio_Pago_Sunat_PLE = $arrDataMediosPago['arrData'][0]->No_Codigo_Medio_Pago_Sunat_PLE;
				foreach ($arrDataMediosPago['arrData'] as $row)
					$sConcatenarMultiplesMedioPago .= $row->No_Medio_Pago . ' [' . $row->Txt_Medio_Pago . ']: ' . ($No_Codigo_Medio_Pago_Sunat_PLE != '0' ? $arrData[0]->No_Signo  . ' ' . $row->Ss_Total_Medio_Pago : '') . ', ';
				$sConcatenarMultiplesMedioPago = substr($sConcatenarMultiplesMedioPago, 0, -2);
			}

			$this->load->library('EnLetras', 'el');
			$EnLetras = new EnLetras();
			
			$this->load->library('Pdf');
			
			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			/*
			$sNombreLogo=str_replace(' ', '_', $arrData[0]->No_Logo_Empresa);
			$sNombreLogoAlmacen=str_replace(' ', '_', $arrData[0]->No_Logo_Almacen);
			if ($arrData[0]->Nu_MultiAlmacen == 0) {
				$sRutaArchivoLogoCliente = $this->logo_cliente_path . $sNombreLogo;
				$sUrlImagen = $arrData[0]->No_Imagen_Logo_Empresa;
			} else {
				$sRutaArchivoLogoCliente = $this->logo_cliente_logos_empresa_almacen_path . $sNombreLogoAlmacen;
				$sUrlImagen = $arrData[0]->No_Logo_Url_Almacen;
			}
			*/
			
			$sUrlImagen = $arrData[0]->Txt_Url_Logo_Lae_Shop;

			ob_start();
			$file = $this->load->view('Ventas/pdf/DocumentoElectronicoPDF', array(
				'sUrlImagen' => $sUrlImagen,
				'arrDataEmpresa' => $arrData,
				'arrData' => $arrData,
				'totalEnLetras'	=> $EnLetras->ValorEnLetras($arrData[0]->Ss_Total, $arrData[0]->No_Moneda),
				'sConcatenarMultiplesMedioPago' => $sConcatenarMultiplesMedioPago,
			));
			$html = ob_get_contents();
			ob_end_clean();
			
			$pdf->SetAuthor('Laesystems');
			$pdf->SetTitle('PDF ' . $arrData[0]->No_Tipo_Documento . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento);
		
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

			$file_name = 'PDF ' . $arrData[0]->No_Tipo_Documento . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento . '.pdf';
			$pdf->Output($file_name, 'I');
		} else {//A4
			$this->generarRepresentacionInternaPDF($ID);
		}
	}

	public function generarArqueoPOSPDF($iIdMatriculaEmpleado, $iIdEnlaceAperturaCaja, $iIdEnlaceCierreCaja){
		$arrPost = array(
			'sTipoCodificacion' => 'normal',
			'sAccion' => 'imprimir',
			'iIdMatriculaEmpleado' => $iIdMatriculaEmpleado,
			'iIdEnlaceAperturaCaja' => $iIdEnlaceAperturaCaja,
			'iIdEnlaceCierreCaja' => $iIdEnlaceCierreCaja,
			'sUrlAperturaCaja' => base_url() . 'PuntoVenta/AperturaCajaController/listar',
		);

		$arrResponse = $this->ImprimirLiquidacionCajaModel->formatoImpresionLiquidacionCaja($arrPost);
		if ( $arrResponse['sStatus'] == 'success' ) {
			$this->load->library('EnLetras', 'el');
			$EnLetras = new EnLetras();
			
			$this->load->library('Pdf');
			
			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			ob_start();
			$file = $this->load->view('Ventas/pdf/CierreCajaPOSPDF', array(
				'arrData' => $arrResponse['arrData'],
			));
			$html = ob_get_contents();
			ob_end_clean();
			
			$pdf->SetAuthor('Laesystems');
			$pdf->SetTitle('Cierre de Caja');
		
			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);
			
			$pdf->SetMargins(PDF_MARGIN_LEFT-13, PDF_MARGIN_TOP-25, PDF_MARGIN_RIGHT-13);
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			$page_format = array(
				'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 74.1, 'ury' => 229.3),
			);
			$pdf->AddPage('P', $page_format, false, false);			

			$pdf->setFont('helvetica', '', 7);
			$pdf->writeHTML($html, true, false, true, false, '');

			$file_name = 'Cierre_Caja_' . dateNow('fecha') . '.pdf';
			$pdf->Output($file_name, 'I');
		} else {
			echo json_encode($arrResponse);
		}
	}

	public function generarTareaRepetirMensual(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VentaModel->generarTareaRepetirMensual($this->input->post()));
	}

	public function eliminarTareaRepetirMensual($ID){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VentaModel->eliminarTareaRepetirMensual($ID));
	}

	public function generarComandaCocinaPDF($ID){
		$arrData = $this->ImprimirTicketModel->formatoImpresionTicketPreCuenta($ID);
		
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		
		$this->load->library('Pdf');
		
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		ob_start();
		$file = $this->load->view('Ventas/pdf/ComandaCocinaPDF', array(
			'arrData' => $arrData,
			'totalEnLetras'	=> $EnLetras->ValorEnLetras($arrData[0]->Ss_Total, $arrData[0]->No_Moneda),
		));
		$html = ob_get_contents();
		ob_end_clean();
		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('PDF COMANDA-' . $arrData[0]->ID_Pedido_Cabecera);
	
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

		$file_name = 'PDF PRECUENTA-' . $arrData[0]->ID_Pedido_Cabecera . '.pdf';
		$pdf->Output($file_name, 'I');
	}

	public function generarPreCuentaPDF($ID){
		$arrData = $this->ImprimirTicketModel->formatoImpresionTicketPreCuenta($ID);
		
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		
		$this->load->library('Pdf');
		
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$sNombreLogo=str_replace(' ', '_', $arrData[0]->No_Logo_Empresa);
		$sNombreLogoAlmacen=str_replace(' ', '_', $arrData[0]->No_Logo_Almacen);
		if ($arrData[0]->Nu_MultiAlmacen == 0) {
			$sRutaArchivoLogoCliente = $this->logo_cliente_path . $sNombreLogo;
			$sUrlImagen = $arrData[0]->No_Imagen_Logo_Empresa;
		} else {
			$sRutaArchivoLogoCliente = $this->logo_cliente_logos_empresa_almacen_path . $sNombreLogoAlmacen;
			$sUrlImagen = $arrData[0]->No_Logo_Url_Almacen;
		}

		ob_start();
		$file = $this->load->view('Ventas/pdf/PreCuentaPDF', array(
			'sUrlImagen' => $sUrlImagen,
			'arrDataEmpresa' => $arrData,
			'arrData' => $arrData,
			'totalEnLetras'	=> $EnLetras->ValorEnLetras($arrData[0]->Ss_Total, $arrData[0]->No_Moneda),
		));
		$html = ob_get_contents();
		ob_end_clean();
		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('PDF PRECUENTA-' . $arrData[0]->ID_Pedido_Cabecera);
	
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

		$file_name = 'PDF PRECUENTA-' . $arrData[0]->ID_Pedido_Cabecera . '.pdf';
		$pdf->Output($file_name, 'I');
	}
    
	public function generarDocumentoReferencia($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
		if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
			$data = $this->VentaModel->get_by_id($this->security->xss_clean($ID));
			if ( $data['sStatus'] == 'success' ) {
				$arrImpuesto = $this->HelperModel->getImpuestos($arrPost = '');
				$output = array(
					'arrEdit' => $data['arrData'],
					'arrImpuesto' => $arrImpuesto,
					'sStatus' => 'success',
					'status' => 'success',
				);
				echo json_encode($output);
				exit();
			} else {
				echo json_encode($data);
				exit();
			}
		} else {
			$arrParams = array('ID_Documento_Cabecera' => $ID);
			if ( !empty($this->VentaModel->verificarSunatCDR($arrParams)) ) {
				$data = $this->VentaModel->get_by_id($this->security->xss_clean($ID));
				if ( $data['sStatus'] == 'success' ) {
					$arrImpuesto = $this->HelperModel->getImpuestos($arrPost = '');
					$output = array(
						'arrEdit' => $data['arrData'],
						'arrImpuesto' => $arrImpuesto,
						'sStatus' => 'success',
						'status' => 'success',
					);
					echo json_encode($output);
					exit();
				} else {
					echo json_encode($data);
					exit();
				}
			} else {
				$arrResponseFE = array(
					'status' => 'danger',
					'style_modal' => 'modal-danger',
					'message' => 'No se puede anular si no tiene CDR. Recuperar o esperar al día siguiente',
					'message_nubefact' => 'No se puede anular si no tiene CDR. Recuperar o esperar al día siguiente',
					'sStatus' => 'danger',
					'sMessage' => 'No se puede anular si no tiene CDR. Recuperar o esperar al día siguiente',
					'arrMessagePSE' => '',
					'sCodigo' => '',
				);
				echo json_encode($arrResponseFE);
				exit();
			}
		}
	}

	public function generarGuia(){
        echo json_encode($this->VentaModel->generarGuia($this->input->post()));
	}
}