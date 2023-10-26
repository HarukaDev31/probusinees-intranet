<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class SalidaInventarioController extends CI_Controller {
	private $file_path = '../assets/images/logos/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Logistica/SalidaInventarioModel');
		$this->load->model('Logistica/CompraModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
		$this->load->model('DocumentoElectronicoModel');
	}

	public function listarSalidas(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			//obtener proveedor creado por empresa
			$arrParamsProveedor = array(
				'Nu_Tipo_Entidad' => 0,//0=cliente
				'ID_Tipo_Documento_Identidad' => $this->empresa->ID_Tipo_Documento_Identidad,
				'Nu_Documento_Identidad' => $this->empresa->Nu_Documento_Identidad
			);
			$arrDataProveedor = $this->CompraModel->getEntidadProveedorInterno($arrParamsProveedor);

			$this->load->view('header');
			$this->load->view('Logistica/SalidaInventarioView', array("arrDataProveedor"=>$arrDataProveedor));
			$this->load->view('footer', array("js_salida_inventario" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->SalidaInventarioModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action_anular = 'anular';
        $action_delete = 'delete';
		$sTipoBajaSunat = '';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = ToDateBD($row->Fe_Emision);
            $rows[] = $row->No_Tipo_Documento_Breve;
            $rows[] = $row->ID_Serie_Documento;
            $rows[] = $row->ID_Numero_Documento;
            $rows[] = $row->No_Entidad;
            $rows[] = $row->No_Signo;
            $rows[] = numberFormat($row->Ss_Total, 2, '.', ',');
			$rows[] = ($row->Nu_Descargar_Inventario == 1 ? 'Si' : 'No');

			$sPrimerCaracterSerie = substr($row->ID_Serie_Documento, 0, 1);

			$arrParamsBuscarGuiaFactura = array('ID_Guia_Cabecera' => $row->ID_Guia_Cabecera);
			$arrResponseDocument = $this->HelperModel->getGuianEnlaceOrigen($arrParamsBuscarGuiaFactura);
			$iEnlace=0;
			$span_enlace = '';
			if ($arrResponseDocument['sStatus'] == 'success') {
				$iEnlace=1;
				$span_enlace = '';
				foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
					$sUrlPDFSunatVenta = (!empty($rowEnlace->Txt_Url_PDF) ? ' <a alt="Descargar PDF" title="Descargar PDF" href="' . $rowEnlace->Txt_Url_PDF . '" target="_blank"><span class="label label-danger"> PDF </span></a>' : '');
					$rowEnlace->ID_Numero_Documento = $rowEnlace->ID_Numero_Documento;
					if($rowEnlace->ID_Tipo_Documento==1)//1=cotización
						$rowEnlace->ID_Numero_Documento = $rowEnlace->ID_Documento_Cabecera;
					$span_enlace .= '<span title="Para ver registro ir a Ventas > Vender" class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento .' ' . $sUrlPDFSunatVenta . "</span><br>";
				}
			}

			$btn_modificar = '';
			$btn_anular = '';
			$btn_facturar = '';
			if ( (($row->Nu_Estado == 6 && $sPrimerCaracterSerie != 'T') || ($row->Nu_Estado == 8 && $sPrimerCaracterSerie == 'T' && !empty($row->Txt_Url_CDR))) ) {
				$btn_anular = '<button class="btn btn-xs btn-link" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularCompra(\'' . $row->ID_Guia_Cabecera . '\', \'' . $iEnlace . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action_anular . '\', \'' . $sPrimerCaracterSerie . '\')"><i class="fa fa-minus-circle fa-2x" aria-hidden="true"></i></button>';
				$btn_facturar = '<button class="btn btn-xs btn-link" alt="Facturar" title="Facturar" href="javascript:void(0)" onclick="facturarGuia(\'' . $row->ID_Guia_Cabecera . '\')"><i class="fa fa-2x fa-shopping-cart" aria-hidden="true"></i></button>';
			}
			if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 && $sPrimerCaracterSerie == 'T' && $row->Nu_Estado == 8)
				$btn_anular = '<button class="btn btn-xs btn-link" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularCompra(\'' . $row->ID_Guia_Cabecera . '\', \'' . $iEnlace . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action_anular . '\', \'' . $sPrimerCaracterSerie . '\')"><i class="fa fa-minus-circle fa-2x" aria-hidden="true"></i></button>';

			if ( $row->Nu_Estado == 6 )
				$btn_modificar = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCompra(\'' . $row->ID_Guia_Cabecera . '\', \'' . $iEnlace . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';

			$rows[] = $btn_facturar . $span_enlace;
			$rows[] = $btn_modificar;
			$rows[] = $btn_anular;
			
			$btn_representacion_interna = '<button type="button" class="btn btn-xs btn-link" alt="Representación Interna PDF" title="Representación Interna PDF" href="javascript:void(0)" onclick="verRepresentacionInternaPDF(\'' . $row->ID_Guia_Cabecera . '\')"><span class="label label-danger">PDF</span></button>';
			if ( $row->Nu_Estado == 8 || $row->Nu_Estado == 7 || $row->Nu_Estado == 10 || $row->Nu_Estado == 11 )
				$btn_representacion_interna = '';
			
			$sUrlPDFElectronico = (!empty($row->Txt_Url_PDF) ? '<a alt="Descargar PDF" title="Descargar PDF" href="' . $row->Txt_Url_PDF . '" target="_blank"><span class="label label-danger">PDF</span></a>' : '');
			$rows[] = (!empty($sUrlPDFElectronico) ? $sUrlPDFElectronico : $btn_representacion_interna);
			
			$cdr ='-';
			/*
			if (empty($row->Txt_Url_CDR) && $this->empresa->Nu_Tipo_Proveedor_FE == 2 && !empty($row->Txt_Respuesta_Sunat_FE)) {
				$objMensaje = json_decode($row->Txt_Respuesta_Sunat_FE);
				if(isset($objMensaje->Mensaje_SUNAT) && isset($objMensaje->Codigo_SUNAT)){
					$cdr = ' <span class="label label-dark" title="' . $objMensaje->Mensaje_SUNAT . '">' . $objMensaje->Codigo_SUNAT . '</span>';
					if (trim($objMensaje->Codigo_SUNAT) != 0)
						$cdr = ' <span class="label label-danger" title="' . $objMensaje->Mensaje_SUNAT . '">ERROR: ' . $objMensaje->Codigo_SUNAT . '</span>';
				}
			}
			*/

			$sCDRSunat = (!empty($row->Txt_Url_CDR) ? '<a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a>' : $cdr);

			//recuperar CDR de NUBEFACT PSE RESELLER
			$sRecuperarCDRPSENubefactReseller='';
			if($row->ID_Tipo_Documento==7 && $this->empresa->Nu_Tipo_Proveedor_FE==1)//1=PSE Nubefact Reseller
				$sRecuperarCDRPSENubefactReseller='<button type="button" id="btn-sunat-cdr-' . $row->ID_Guia_Cabecera . '" style="background-color: transparent;border: 0px;" alt="Recuperar CDR" title="Recuperar CDR" href="javascript:void(0)" onclick="consultarGuiaElectronicoPSENubefactReseller(\'' . $row->ID_Guia_Cabecera . '\', \'' . $row->ID_Tipo_Documento . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->Nu_Estado . '\')"><span class="label label-dark">Recuperar CDR</span><span class="label label-dark" id="span-sunat-cdr-' . $row->ID_Guia_Cabecera . '"></span></button>';
			
			if($row->ID_Tipo_Documento==7 && $this->empresa->Nu_Tipo_Proveedor_FE==2)//2=SUNAT
				$sRecuperarCDRPSENubefactReseller='<button type="button" id="btn-sunat-cdr-' . $row->ID_Guia_Cabecera . '" style="background-color: transparent;border: 0px;" alt="Recuperar CDR" title="Recuperar CDR" href="javascript:void(0)" onclick="consultarGuiaElectronicoSunatV2(\'' . $row->ID_Guia_Cabecera . '\', \'' . $row->ID_Tipo_Documento . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->Nu_Estado . '\')"><span class="label label-dark">Recuperar CDR</span><span class="label label-dark" id="span-sunat-cdr-' . $row->ID_Guia_Cabecera . '"></span></button>';

			$sTipoBajaSunat = 'Interno';
            $btn_send_sunat = '';
            //if ( (($row->Nu_Estado == 6 || $row->Nu_Estado == 7 || $row->Nu_Estado == 9 || $row->Nu_Estado == 11) || ($row->Nu_Estado == 8 && empty($row->Txt_Url_CDR))) && $sPrimerCaracterSerie == 'T' && $row->ID_Tipo_Documento != 2 && $this->empresa->Nu_Tipo_Proveedor_FE != 3 )//Action send SUNAT
			if ( empty($row->Txt_Hash) && $this->empresa->Nu_Tipo_Proveedor_FE==2 && ($row->Nu_Estado == 6 || $row->Nu_Estado == 7 || $row->Nu_Estado == 9 || $row->Nu_Estado == 11) && $sPrimerCaracterSerie == 'T' && $row->ID_Tipo_Documento == 7)//Action send SUNAT
            	$btn_send_sunat = '<button id="btn-sunat-' . $row->ID_Guia_Cabecera . '" class="btn btn-xs btn-link" alt="Enviar a Sunat" title="Enviar a Sunat" href="javascript:void(0)" onclick="sendDocumentoSunat(\'' . $row->ID_Guia_Cabecera . '\', \'' . $row->Nu_Estado . '\', \'' . $sTipoBajaSunat . '\', \'' . $row->ID_Tipo_Documento . '\')"><i class="fa fa-cloud-upload"></i> Sunat</i></button>';
			if ( $this->empresa->Nu_Tipo_Proveedor_FE==1 && ($row->Nu_Estado == 6 || $row->Nu_Estado == 7 || $row->Nu_Estado == 9 || $row->Nu_Estado == 11) && $sPrimerCaracterSerie == 'T' && $row->ID_Tipo_Documento == 7)//Action send SUNAT
            	$btn_send_sunat = '<button id="btn-sunat-' . $row->ID_Guia_Cabecera . '" class="btn btn-xs btn-link" alt="Enviar a Sunat" title="Enviar a Sunat" href="javascript:void(0)" onclick="sendDocumentoSunat(\'' . $row->ID_Guia_Cabecera . '\', \'' . $row->Nu_Estado . '\', \'' . $sTipoBajaSunat . '\', \'' . $row->ID_Tipo_Documento . '\')"><i class="fa fa-cloud-upload"></i> Sunat</i></button>';

			if ($row->ID_Tipo_Documento == 7 && $row->Nu_Estado == 6 && $sPrimerCaracterSerie != 'T')//Imprimir
				$btn_send_sunat = '<button class="btn btn-xs btn-link" alt="Imprimir" title="Imprimir" href="javascript:void(0)" onclick="imprimirRegistro(\'' . $row->ID_Guia_Cabecera . '\')"><i class="fa fa-print" aria-hidden="true"> Imprimir</i></button>';

			if ($this->empresa->Nu_Tipo_Proveedor_FE == 2 && !empty($row->Txt_Respuesta_Sunat_FE)) {
				$objMensaje = json_decode($row->Txt_Respuesta_Sunat_FE);
				if(isset($objMensaje->Mensaje_SUNAT) && isset($objMensaje->Codigo_SUNAT)){
					if (trim($objMensaje->Codigo_SUNAT) == 0)
						$btn_send_sunat .= '<br><span class="label label-dark" title="' . $objMensaje->Mensaje_SUNAT . '">' . $objMensaje->Codigo_SUNAT . '</span>';
					else
						$btn_send_sunat .= '<br><span class="label label-danger" title="' . $objMensaje->Mensaje_SUNAT . '">ERROR: ' . $objMensaje->Codigo_SUNAT . '</span>';
				}
			}

			$rows[] = ($sCDRSunat == '-' ? $sRecuperarCDRPSENubefactReseller : $sCDRSunat) . $btn_send_sunat;

			$btn_opciones = '';
			if ($row->Nu_Estado == 8) {//enviado
				$btn_opciones = '
				<div class="btn-group">
					<button style="width: 100%;" alt="Opciones" title="Opciones" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Opciones <span class="caret"></span></button>
					<ul class="dropdown-menu" style="width: 100%; position: sticky;">
						<li><a alt="Enviar Correo" title="Enviar Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $row->ID_Guia_Cabecera . '\', \'' . $row->ID_Proveedor . '\')">Enviar Correo</a></li>';
						$btn_opciones .= (!empty($row->Txt_Url_XML) ? '<li><a alt="Descargar XML" title="Descargar XML" href="' . $row->Txt_Url_XML . '" target="_blank"><span class="label label-primary">XML</span></a></li>' : '');
						$btn_opciones .= (!empty($row->Txt_Url_CDR) ? '<li><a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a></li>' : '');
						$btn_opciones .= '
					</ul>
				</div>';
			}
			$rows[] = $btn_opciones;

			$arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
			$rows[] = '<span class="label label-' . $arrEstadoDocumento['No_Class_Estado'] . '">' . $arrEstadoDocumento['No_Estado'] . '</span>';

            //$rows[] = $btn_send_sunat;
            $rows[] = $btn_representacion_interna;
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->SalidaInventarioModel->count_all(),
	        'recordsFiltered' => $this->SalidaInventarioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }

	public function ajax_edit($ID){
        $data = $this->SalidaInventarioModel->get_by_id($this->security->xss_clean($ID));
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
    
	public function crudCompra(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		
		$arrClienteNuevo = '';
		if (isset($_POST['arrClienteNuevo'])){
			$arrClienteNuevo = array(
				'ID_Tipo_Documento_Identidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['ID_Tipo_Documento_Identidad']),
				'Nu_Documento_Identidad' => $this->security->xss_clean(strtoupper($_POST['arrClienteNuevo']['Nu_Documento_Identidad'])),
				'No_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['No_Entidad']),
				'Txt_Direccion_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['Txt_Direccion_Entidad']),
				'Nu_Telefono_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['Nu_Telefono_Entidad']),
				'Nu_Celular_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['Nu_Celular_Entidad']),
				'Txt_Email_Entidad' => $this->security->xss_clean($_POST['arrClienteNuevo']['Txt_Email_Entidad']),
			);
		}

		$response['ID_Guia_Cabecera_Enlace'] = '';
		$_POST['arrCompraCabecera']['esEnlace'] = 0;//Se usa para relacionar pero actualmente no se ha implementado por eso estará en 0
		$arrCompraCabecera = array(
			'ID_Empresa' => $this->empresa->ID_Empresa,
			'ID_Organizacion' => $this->empresa->ID_Organizacion,
			'ID_Almacen' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Almacen']),
			'ID_Tipo_Asiento' => 3,//Guías Salida
			'ID_Tipo_Documento' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Tipo_Documento']),
			'ID_Serie_Documento' => $this->security->xss_clean(strtoupper($_POST['arrCompraCabecera']['ID_Serie_Documento'])),
			'ID_Serie_Documento_PK' => $this->security->xss_clean(strtoupper($_POST['arrCompraCabecera']['ID_Serie_Documento_PK'])),
			'ID_Numero_Documento' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Numero_Documento']),
			'Fe_Emision' => ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Emision'])),
			'Fe_Periodo' => ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Emision'])),
			'ID_Moneda' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Moneda']),
			'Nu_Descargar_Inventario' => $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Descargar_Inventario']),
			'ID_Tipo_Movimiento' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Tipo_Movimiento']),
			
			'ID_Entidad' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Entidad']),
			'Txt_Direccion_Entidad' => $this->security->xss_clean($_POST['arrCompraCabecera']['Txt_Direccion_Entidad']),
			'Nu_Celular_Entidad' => $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Celular_Entidad']),
			'Txt_Email_Entidad' => $this->security->xss_clean($_POST['arrCompraCabecera']['Txt_Email_Entidad']),

			'iFlete' => $this->security->xss_clean($_POST['arrCompraCabecera']['iFlete']),
			'ID_Entidad_Transportista' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Entidad_Transportista']),
			'No_Placa' => $this->security->xss_clean(strtoupper($_POST['arrCompraCabecera']['No_Placa'])),
			'Fe_Traslado' => (!empty($_POST['arrCompraCabecera']['Fe_Traslado']) ? ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Traslado'])) : '0000-00-00'),
			'ID_Motivo_Traslado' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Motivo_Traslado']),
			'Ss_Peso_Bruto' => $this->security->xss_clean($_POST['arrCompraCabecera']['Ss_Peso_Bruto']),
			'Nu_Bulto' => $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Bulto']),
			'No_Licencia' => $this->security->xss_clean($_POST['arrCompraCabecera']['No_Licencia']),
			'No_Certificado_Inscripcion' => $this->security->xss_clean($_POST['arrCompraCabecera']['No_Certificado_Inscripcion']),
			'Txt_Glosa' => $this->security->xss_clean($_POST['arrCompraCabecera']['Txt_Glosa']),
			'Ss_Total' => $this->security->xss_clean($_POST['arrCompraCabecera']['Ss_Total']),
			'Nu_Estado' => 6,
			'ID_Almacen_Transferencia' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Almacen_Transferencia']),
			'iTipoCliente' => $this->security->xss_clean($_POST['arrCompraCabecera']['iTipoCliente']),
			'No_Formato_PDF' => $this->security->xss_clean($_POST['arrCompraCabecera']['No_Formato_PDF']),
			'No_Tipo_Transporte' => $this->security->xss_clean($_POST['arrCompraCabecera']['No_Tipo_Transporte']),
			'ID_Ubigeo_Inei_Llegada' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Ubigeo_Inei_Llegada'])//UBIGEO
		);
		
		if ( $_POST['arrCompraCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
			$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Lista_Precio_Cabecera'])));
		
		echo json_encode(
		($this->security->xss_clean($_POST['arrCompraCabecera']['EID_Guia_Cabecera']) != '') ?
			$this->actualizarCompra_Inventario(array('ID_Guia_Cabecera' => $this->security->xss_clean($_POST['arrCompraCabecera']['EID_Guia_Cabecera'])), $arrCompraCabecera, $_POST['arrDetalleCompra'], $_POST['arrCompraCabecera']['esEnlace'], $response['ID_Guia_Cabecera_Enlace'], $arrCompraCabecera['Nu_Descargar_Inventario'], $arrClienteNuevo)
		:
			$this->agregarCompra_Inventario($arrCompraCabecera, $_POST['arrDetalleCompra'], $_POST['arrCompraCabecera']['esEnlace'], $response['ID_Guia_Cabecera_Enlace'], $arrCompraCabecera['Nu_Descargar_Inventario'], $arrClienteNuevo)
		);
	}

	public function agregarCompra_Inventario($arrCompraCabecera = '', $arrDetalleCompra = '', $esEnlace = '', $ID_Guia_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrClienteNuevo = ''){
		$responseCompra = $this->SalidaInventarioModel->agregarCompra($arrCompraCabecera, $arrDetalleCompra, $esEnlace, $ID_Guia_Cabecera_Enlace, $arrClienteNuevo);
		if ($responseCompra['sStatus'] == 'success') {
			if ($Nu_Descargar_Inventario == '1'){//1 = Si
				$response = $this->MovimientoInventarioModel->crudMovimientoInventario($arrCompraCabecera['ID_Almacen'], 0, $responseCompra['Last_ID_Guia_Cabecera'], $arrDetalleCompra, $arrCompraCabecera['ID_Tipo_Movimiento'], 0, '', 1, 1);
				if ( $arrCompraCabecera['ID_Tipo_Documento'] != 2 && substr($arrCompraCabecera['ID_Serie_Documento'], 0, 1) == 'T' ) {
					if ($this->empresa->Nu_Enviar_Sunat_Automatic==0) {
						return $response;
					} else {//1=Enviar a sunat automaticamente
						if ($this->empresa->Nu_Activar_Guia_Electronica==1) {
							if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrCompraCabecera['ID_Tipo_Documento'] != '2') {// cancelado y 2 = Documento interno
								$arrParams = array(
									'iCodigoProveedorDocumentoElectronico' => 1,
									'iEstadoVenta' => $arrCompraCabecera['Nu_Estado'],//6=Completado
									'iIdDocumentoCabecera' =>  $responseCompra['Last_ID_Guia_Cabecera'],
									'sEmailCliente' => ( is_array($arrClienteNuevo) ? $arrClienteNuevo['Txt_Email_Entidad'] : '' ),
									'sTipoRespuesta' => 'php',
								);
								$arrResponseFE = array();
								
								if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
									$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuia( $arrParams );
									$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
									if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
										return $arrResponseFEMensaje;
									}
									if ( $arrResponseFE['sStatus'] != 'success' ) {
										return $arrResponseFE;
									}
									//consultar estado de guia
									$arrParamsConsultaGuia = array(
										'iEstadoVenta' => 6,
										'iIdGuiaCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
										'ID_Tipo_Documento' => $arrCompraCabecera['ID_Tipo_Documento'],
										'ID_Serie_Documento' => $arrCompraCabecera['ID_Serie_Documento'],
										'ID_Numero_Documento' => $arrCompraCabecera['ID_Numero_Documento'],
										'sTipoRespuesta' => 'php'
									);
									$this->DocumentoElectronicoModel->consultarGuiaElectronicoPSENubefactReseller( $arrParamsConsultaGuia );
								} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 ) {//Facturador sunat
									$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuiaSunat( $arrParams );
									$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
									if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
										return $arrResponseFEMensaje;
									}
									if ( $arrResponseFE['sStatus'] != 'success' ) {
										return $arrResponseFE;
									}
								}
									
								return array(
									'sStatus' => 'success',
									'sMessage' => 'Registro enviado',
									'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
									'arrResponseFE' => $arrResponseFE,
								);
							} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrCompraCabecera['ID_Tipo_Documento'] == '2') {// cancelado y 2 = Documento interno
		
								return array(
									'sStatus' => 'success',
									'sMessage' => 'Registro enviado',
									'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
									'arrResponseFE' => '',
								);
							} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrCompraCabecera['ID_Tipo_Documento'] != '2') {// pago pendiente y diferente 2 = Documento interno
		
								return array(
									'sStatus' => 'success',
									'sMessage' => 'Registro enviado pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
									'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
									'arrResponseFE' => '',
								);
							} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrCompraCabecera['ID_Tipo_Documento'] == '2') {// pago pendiente y 2 = Documento interno
								
								return array(
									'sStatus' => 'danger',
									'sMessage' => 'No se guardo venta por falta de pago',
									'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
									'arrResponseFE' => '',
								);
							}
						} else {
							return array(
								'sStatus' => 'success',
								'sMessage' => 'Registro guardado pero no tiene activado guia de remision electronica',
								'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
								'arrResponseFE' => '',
							);
						}// ./ validar si tiene activo guia de remision
					}
				} else {
					return $response;
				}
			}// ./ Generar Inventario
			if ( $arrCompraCabecera['ID_Tipo_Documento'] != 2 && substr($arrCompraCabecera['ID_Serie_Documento'], 0, 1) == 'T' ) {
				if ($this->empresa->Nu_Enviar_Sunat_Automatic==0) {
					return $responseCompra;
				} else {//1=Enviar a sunat automaticamente
					if ($this->empresa->Nu_Activar_Guia_Electronica==1) {
						if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrCompraCabecera['ID_Tipo_Documento'] != '2') {// cancelado y 2 = Documento interno

							$arrParams = array(
								'iCodigoProveedorDocumentoElectronico' => 1,
								'iEstadoVenta' => $arrCompraCabecera['Nu_Estado'],//6=Completado
								'iIdDocumentoCabecera' =>  $responseCompra['Last_ID_Guia_Cabecera'],
								'sEmailCliente' => ( is_array($arrClienteNuevo) ? '' : $arrClienteNuevo['Txt_Email_Entidad'] ),
								'sTipoRespuesta' => 'php',
							);
							$arrResponseFE = array();
							
							if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
								$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuia( $arrParams );
								$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
								if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
									return $arrResponseFEMensaje;
								}
								if ( $arrResponseFE['sStatus'] != 'success' ) {
									return $arrResponseFE;
								}
								//consultar estado de guia
								$arrParamsConsultaGuia = array(
									'iEstadoVenta' => 6,
									'iIdGuiaCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
									'ID_Tipo_Documento' => $arrCompraCabecera['ID_Tipo_Documento'],
									'ID_Serie_Documento' => $arrCompraCabecera['ID_Serie_Documento'],
									'ID_Numero_Documento' => $arrCompraCabecera['ID_Numero_Documento'],
									'sTipoRespuesta' => 'php'
								);
								$this->DocumentoElectronicoModel->consultarGuiaElectronicoPSENubefactReseller( $arrParamsConsultaGuia );
							} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 ) {//Facturador sunat
								$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuiaSunat( $arrParams );
								$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
								if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
									return $arrResponseFEMensaje;
								}
								if ( $arrResponseFE['sStatus'] != 'success' ) {
									return $arrResponseFE;
								}
							}
								
							return array(
								'sStatus' => 'success',
								'sMessage' => 'Registro enviado',
								'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
								'arrResponseFE' => $arrResponseFE,
							);
						} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrCompraCabecera['ID_Tipo_Documento'] == '2') {// cancelado y 2 = Documento interno

							return array(
								'sStatus' => 'success',
								'sMessage' => 'Registro enviado',
								'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
								'arrResponseFE' => '',
							);
						} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrCompraCabecera['ID_Tipo_Documento'] != '2') {// pago pendiente y diferente 2 = Documento interno

							return array(
								'sStatus' => 'success',
								'sMessage' => 'Registro enviado pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
								'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
								'arrResponseFE' => '',
							);
						} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrCompraCabecera['ID_Tipo_Documento'] == '2') {// pago pendiente y 2 = Documento interno
							
							return array(
								'sStatus' => 'danger',
								'sMessage' => 'No se guardo venta por falta de pago',
								'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
								'arrResponseFE' => '',
							);
						}
					} else {
						return array(
							'sStatus' => 'success',
							'sMessage' => 'Registro guardado pero no tiene activado guia de remision electronica',
							'iIdDocumentoCabecera' => $responseCompra['Last_ID_Guia_Cabecera'],
							'arrResponseFE' => '',
						);
					}// ./ validar si tiene activo guia de remision
				}// ./ Enviar automaticamente
			} else {
				return $responseCompra;
			}
		} else {
			return $responseCompra;
		}
	}

	public function actualizarCompra_Inventario($arrWhereCompra = '', $arrCompraCabecera = '', $arrDetalleCompra = '', $esEnlace = '', $ID_Guia_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrClienteNuevo = ''){
		$responseCompra = $this->SalidaInventarioModel->actualizarCompra($arrWhereCompra, $arrCompraCabecera, $arrDetalleCompra, $esEnlace, $ID_Guia_Cabecera_Enlace, $arrClienteNuevo);
		if ($responseCompra['sStatus'] == 'success') {
			if ($Nu_Descargar_Inventario == '1')//1 = Si
				return $this->MovimientoInventarioModel->crudMovimientoInventario($arrCompraCabecera['ID_Almacen'], 0, $responseCompra['Last_ID_Guia_Cabecera'], $arrDetalleCompra, $arrCompraCabecera['ID_Tipo_Movimiento'], 1, $arrWhereCompra, 1, 1);
			return $responseCompra;
		} else {
			return $responseCompra;
		}
	}
    
	public function anularCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario, $sPrimerCaracterSerie){
		//si tiene guia electronica activada se envia a sunat pero antes de anular debo de enviar el documento solo para sunat
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
		if ($sPrimerCaracterSerie != 'T') {
			echo json_encode($this->SalidaInventarioModel->anularCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario), 7));
		} else {
			if ($this->empresa->Nu_Activar_Guia_Electronica==1) {
				$arrParams = array(
					'iCodigoProveedorDocumentoElectronico' => 1,
					'iEstadoVenta' => 7,//6=Completado
					'iIdDocumentoCabecera' => $ID,
					'sEmailCliente' => '',
					'sTipoRespuesta' => 'json',
				);
				$arrResponseFE = array();				
				if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
					$iEstado = 10;
					//Solo anula de manera interna Nubefact PSE Reseller
				} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 ) {//Facturador sunat
					$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuiaSunat( $arrParams );
					$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
					if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
						echo json_encode($arrResponseFEMensaje);
						exit();
					}
					if ( $arrResponseFE['sStatus'] != 'success' ) {
						echo json_encode($arrResponseFE);
						exit();
					}
					$iEstado = ($arrResponseFE['sStatus'] == 'success' ? 10 : 11);
				}

				if ($iEstado == 10) { 
					$arrResponseAnular = $this->SalidaInventarioModel->anularCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario), $iEstado);
					$arrResponseAnular = array_merge($arrResponseAnular, $arrResponseFE);
					echo json_encode($arrResponseAnular);
					exit();
				} else {
					echo json_encode($arrResponseFE);
					exit();
				}
			} else {
				echo json_encode(array(
					'sStatus' => 'success',
					'sMessage' => 'No se puede anular porque no tiene activado guia de remision electronica',
					'iIdDocumentoCabecera' => $ID,
					'arrResponseFE' => '',
				));
				exit();
			}// ./ validar si tiene activo guia de remision
		}
	}
	
	public function eliminarCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->SalidaInventarioModel->eliminarCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}

	public function sendDocumentoSunat($ID, $Nu_Estado, $sTypeResponse, $sTipoBajaSunat, $ID_Tipo_Documento){
		if ($this->empresa->Nu_Activar_Guia_Electronica==1) {
			if ( $this->empresa->Nu_Estado_Pago_Sistema == 1) {// cancelado y 2 = Documento interno
				$arrParams = array(
					'iCodigoProveedorDocumentoElectronico' => 1,
					'iEstadoVenta' => $Nu_Estado,//6=Completado
					'iIdDocumentoCabecera' => $ID,
					'sEmailCliente' => '',
					'sTipoRespuesta' => $sTypeResponse,
				);
				$arrResponseFE = array();				
				if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
					$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuia( $arrParams );
					$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
					if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
						echo json_encode($arrResponseFEMensaje);
						exit();
					}
					if ( $arrResponseFE['sStatus'] != 'success' ) {
						echo json_encode($arrResponseFE);
						exit();
					}
					//consultar estado de guia
					$arrParamsConsultaGuia = array(
						'iEstadoVenta' => 6,
						'iIdGuiaCabecera' => $ID,
						'ID_Tipo_Documento' => '',
						'ID_Serie_Documento' => '',
						'ID_Numero_Documento' => '',
						'sTipoRespuesta' => 'php'
					);
					$this->DocumentoElectronicoModel->consultarGuiaElectronicoPSENubefactReseller( $arrParamsConsultaGuia );
				} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 ) {//Facturador sunat
					$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuiaSunat( $arrParams );
					$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
					if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
						echo json_encode($arrResponseFEMensaje);
						exit();
					}
					if ( $arrResponseFE['sStatus'] != 'success' ) {
						echo json_encode($arrResponseFE);
						exit();
					}
				}
					
				echo json_encode(array(
					'sStatus' => 'success',
					'sMessage' => 'Registro enviado',
					'iIdDocumentoCabecera' => $ID,
					'arrResponseFE' => $arrResponseFE,
				));
				exit();
			} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0) {// pago pendiente y diferente 2 = Documento interno

				echo json_encode(array(
					'sStatus' => 'success',
					'sMessage' => 'Registro enviado pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
					'iIdDocumentoCabecera' => $ID,
					'arrResponseFE' => '',
				));
				exit();
			}
		} else {
			echo json_encode(array(
				'sStatus' => 'success',
				'sMessage' => 'No tiene activado guia de remision electronica',
				'iIdDocumentoCabecera' => $ID,
				'arrResponseFE' => '',
			));
			exit();
		}// ./ validar si tiene activo guia de remision
	}

	public function sendCorreoFacturaVentaSUNAT($id=0, $Txt_Email_Entidad=''){
		// Parametros de entrada
		$iIdDocumentoCabecera = !isset($_POST['ID']) ? $id : $this->input->post('ID');
		$arrData = $this->SalidaInventarioModel->get_by_id($iIdDocumentoCabecera);
		if ( $arrData['sStatus'] == 'success' ) {
			$arrData = $arrData['arrData'];

			$this->load->library('email');

			$data = array();

			$data["No_Documento"]	= 'GUÍA DE REMISIÓN ELECTRÓNICA '  . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento;
			$data["Fe_Emision"] 	= ToDateBD($arrData[0]->Fe_Emision);			
			$data["No_Entidad"] = $arrData[0]->No_Entidad;			
			$data["No_Empresa"] 					= $this->empresa->No_Empresa;
			$data["Nu_Documento_Identidad_Empresa"] = $this->empresa->Nu_Documento_Identidad;
			
			$data["url_comprobante"] = (!empty($arrData[0]->Txt_Url_Comprobante) ? $arrData[0]->Txt_Url_Comprobante : '');
			
			$asunto = 'COPIA DE ' . $data["No_Documento"] . ' ' . $this->empresa->No_Empresa . ' | ' . $this->empresa->Nu_Documento_Identidad;
			
			$message = $this->load->view('correos/GuiaElectronicaEmailView', $data, true);

			$config['protocol'] = 'smtp';
			$config['smtp_host'] = 'ssl://smtp.zoho.com';
			$config['smtp_port'] = 465;
			$config['smtp_user'] = 'noreply@eboomstore.com';
			$config['smtp_pass'] = 'Noreply$%&07081993';
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['smtp_timeout'] = '6';
			$config['wordwrap'] = TRUE;

			$this->email->initialize($config);

			$this->email->from('noreply@eboomstore.com', $this->empresa->No_Empresa);//de
			
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
        $arrData = $this->SalidaInventarioModel->get_by_id($this->security->xss_clean($ID));
		if ( $arrData['sStatus'] == 'success' ) {
			$arrData = $arrData['arrData'];
			if($arrData[0]->No_Formato_PDF=='A4'){
				$this->generarRepresentacionInternaA4PDF($arrData);
			} else {
				$this->generarRepresentacionInternaTicketPDF($arrData);
			}			
		} else {
			echo json_encode($arrData['sMessage']);
			exit();
		}
	}

	public function generarRepresentacionInternaA4PDF($arrData){			
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		
		$this->load->library('Pdf');
		
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		
		ob_start();
		$file = $this->load->view('Logistica/pdf/RepresentacionInternaViewPDF', array(
			'arrData' => $arrData,
			'totalEnLetras'	=> $EnLetras->ValorEnLetras($arrData[0]->Ss_Total, $arrData[0]->No_Moneda),
		));
		$html = ob_get_contents();
		ob_end_clean();
		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('laesystems_Representacion_Interna_' . $arrData[0]->ID_Tipo_Documento . '_' . $arrData[0]->ID_Serie_Documento . '_' . $arrData[0]->ID_Numero_Documento);
	
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		
		$pdf->AddPage();
		
		$sNombreLogo=str_replace(' ', '_', $this->empresa->No_Logo_Empresa);
		if ( !file_exists($this->file_path . $sNombreLogo) ) {
			$sNombreLogo='lae_logo_cotizacion.png';
		}
		$sCssFontFamily='Arial';
		$format_header = '<table border="0" cellspacing="2" cellpadding="0">';
			$format_header .= '<tr>';
				$format_header .= '<td rowspan="8" style="width: 55%; text-align: center;">';
					$format_header .= '<img style="height: ' . $this->empresa->Nu_Height_Logo_Ticket . 'px; width: ' . $this->empresa->Nu_Width_Logo_Ticket . 'px;" src="' . $this->empresa->No_Imagen_Logo_Empresa . '">';
				$format_header .= '</td>';
				$format_header .= '<td colspan="2" style="width: 45%; text-align: center; background-color:#F2F5F5;">';
					$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . strtoupper($arrData[0]->No_Tipo_Documento) . (substr($arrData[0]->ID_Serie_Documento, 0, 1) == 'T' ? ' ELECTRÓNICA' : '') . '</b></label>';
				$format_header .= '</td>';
			$format_header .= '</tr>';
			$format_header .= '<tr>';
				$format_header .= '<td style="text-align: center; background-color:#F2F5F5;">';
					$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>RUC ' . $this->empresa->Nu_Documento_Identidad . '</b></label>';
				$format_header .= '</td>';
				$format_header .= '<td style="text-align: center; background-color:#F2F5F5;">';
					$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT) . '</b></label>';
				$format_header .= '</td>';
			$format_header .= '</tr>';
			$format_header .= '<tr>';
				$format_header .= '<td colspan="2" style="text-align: right;">';
					$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $this->empresa->No_Empresa . '</b></label>';
				$format_header .= '</td>';
			$format_header .= '</tr>';
			if( !empty($this->empresa->Txt_Slogan_Empresa) ) {
				$format_header .= '<tr>';
					$format_header .= '<td colspan="2" style="text-align: right;">';
						$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . $this->empresa->Txt_Slogan_Empresa . '</label>';
					$format_header .= '</td>';
				$format_header .= '</tr>';
			}
			$format_header .= '<tr>';
				$format_header .= '<td colspan="2" style="text-align: right;">';
					$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>DIRECCIÓN:</b> ' . $this->empresa->Txt_Direccion_Empresa . '</label>';
				$format_header .= '</td>';
			$format_header .= '</tr>';
			if( $this->empresa->Txt_Direccion_Empresa != $this->empresa->Txt_Direccion_Almacen ) {
				$format_header .= '<tr>';
					$format_header .= '<td colspan="2" style="text-align: right;">';
						$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>SUCURSAL:</b> ' . $this->empresa->Txt_Direccion_Almacen . '</label>';
					$format_header .= '</td>';
				$format_header .= '</tr>';
			}
			if( !empty($this->empresa->No_Dominio_Empresa) || !empty($this->empresa->Txt_Email_Empresa) || !empty($this->empresa->Nu_Celular_Empresa) || !empty($this->empresa->Nu_Telefono_Empresa) ) {
				$format_header .= '<tr>';
					$format_header .= '<td colspan="2" style="text-align: right;">';
						$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . (!empty($this->empresa->No_Dominio_Empresa) ? '<b>Web: </b>' . $this->empresa->No_Dominio_Empresa . ' ' : '') . (!empty($this->empresa->Txt_Email_Empresa) ? '<b>Email:</b> ' . $this->empresa->Txt_Email_Empresa . ' ' : '') . (!empty($this->empresa->Nu_Celular_Empresa) ? '<b>Celular:</b> ' . $this->empresa->Nu_Celular_Empresa . ' ' : '') . (!empty($this->empresa->Nu_Telefono_Empresa) ? '<b>Teléfono:</b> ' . $this->empresa->Nu_Telefono_Empresa . ' ' : '') . '</label>';
					$format_header .= '</td>';
				$format_header .= '</tr>';
			}
		$format_header .= '</table>';
		
		$pdf->writeHTML($format_header, true, 0, true, 0);
		
		$pdf->setFont('helvetica', '', 7);
		$pdf->writeHTML($html, true, false, true, false, '');

		$file_name = 'laesystems_Representacion_Interna_' . $arrData[0]->ID_Tipo_Documento . '_' . $arrData[0]->ID_Serie_Documento . '_' . $arrData[0]->ID_Numero_Documento . '.pdf';
		$pdf->Output($file_name, 'I');
	}

	public function generarRepresentacionInternaTicketPDF($arrData){
		$this->load->library('Pdf');
		
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		ob_start();
		$file = $this->load->view('Logistica/pdf/RepresentacionInternaTicketViewPDF', array(
			'arrData' => $arrData,
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
	}
	
	public function imprimirRegistro($ID){
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		$data = $this->SalidaInventarioModel->get_by_id($this->security->xss_clean($ID));
		$this->load->view('Logistica/impresiones/impresion_comprobante', array(
			'venta' 		=> $data['arrData'],
			'totalEnLetras'	=> $EnLetras->ValorEnLetras($data['arrData'][0]->Ss_Total, 'Soles'),
		));
	}
}
