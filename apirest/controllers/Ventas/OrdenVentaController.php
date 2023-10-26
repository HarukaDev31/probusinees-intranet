<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class OrdenVentaController extends CI_Controller {
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Ventas/OrdenVentaModel');
	}

	public function listarOrdenesVenta(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/OrdenVentaView');
			$this->load->view('footer', array("js_orden_venta" => true));
		}
	}
	
	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->OrdenVentaModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = ToDateBD($row->Fe_Emision);
            //$rows[] = $row->No_Tipo_Documento;
            $rows[] = $row->ID_Documento_Cabecera;
            $rows[] = $row->No_Entidad;
            //$rows[] = $row->No_Contacto;
            $rows[] = $row->No_Signo;
			
			$fTotal = $row->Ss_Total;
			$fTotalGratuita = 0.00;
			if ($fTotal > 0.00) {
				$objImporteDetalleDocumento = $this->HelperModel->obtenerImporteDetalleDocumentoGratuita($row->ID_Documento_Cabecera);
				$fTotalGratuita = $objImporteDetalleDocumento->Ss_Total;
				$fTotal -= $fTotalGratuita;
			}

            $rows[] = numberFormat($fTotal, 2, '.', ',');
				
			$arrEstadoDocumento = $this->HelperModel->obtenerEstadoCotizacionArray($row->Nu_Estado);
            $rows[] = '<div class="btn-group">
				<button style="width: 100%;" class="btn btn-' . $arrEstadoDocumento['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoDocumento['No_Estado'] . '
				<span class="caret"></span></button>
				<ul class="dropdown-menu" style="width: 100%; position: sticky;">
				  <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="estadoOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 0);">Entregado</a></li>
				  <li><a alt="Revisado" title="Revisado" href="javascript:void(0)" onclick="estadoOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 1);">Revisado</a></li>
				  <li><a alt="Aceptado" title="Aceptado" href="javascript:void(0)" onclick="estadoOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 2);">Aceptado</a></li>
				  <li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="estadoOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 3);">Rechazado</a></li>
				</ul>
			</div>';

            $rows[] = $row->No_Personal;

			$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
			$arrResponseDocument = $this->HelperModel->getDocumentoEnlace($arrParams);
			$iEnlace=0;
			if ($arrResponseDocument['sStatus'] == 'success')
				$iEnlace=1;
				
			$span_enlace_documentos = '';
			if ( $iEnlace == 1 ) {
				$span_enlace_documentos = '';
				$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
				$arrResponseDocument = $this->HelperModel->getDocumentoEnlace($arrParams);
				if ($arrResponseDocument['sStatus'] == 'success') {
					$span_enlace_documentos = '';
					$iContadorEnlaceVender=1;
					foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
						$sUrlPDFSunatVenta = (!empty($rowEnlace->Txt_Url_PDF) ? ' <a alt="Descargar PDF" title="Descargar PDF" href="' . $rowEnlace->Txt_Url_PDF . '" target="_blank"><span class="label label-danger"> PDF </span></a>' : '');
						
						$span_enlace_documentos .= ($iContadorEnlaceVender == 1 ? '<br>' : '');
						$span_enlace_documentos .= '<span title="Para ver registro ir a Ventas > Vender" class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento  . ' ' . $sUrlPDFSunatVenta . "</span><br>";
						++$iContadorEnlaceVender;
					}
				}
			}

			$asunto = $row->No_Tipo_Documento . ' Nro. ' . $row->ID_Documento_Cabecera . ' ' . $this->empresa->No_Empresa . ' | ' . $this->empresa->Nu_Documento_Identidad;

			$icon_send_whatsapp = '<br><button type="button" id="whatsapp-' . $row->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->ID_Entidad . '\', \'' . base64_encode($row->ID_Documento_Cabecera) . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="PDF" title="PDF" href="javascript:void(0)" onclick="pdfOrdenVenta(\'' . base64_encode($row->ID_Documento_Cabecera) . '\')"><i class="fa fa-2x fa-file-pdf-o color_icon_pdf"></i></button>
			' . $icon_send_whatsapp . '<br><button class="btn btn-xs btn-link" alt="Enviar Correo" title="Enviar Correo" href="javascript:void(0)" onclick="enviarCorreo(\'' . $row->ID_Documento_Cabecera . '\', \'' . $this->user->Txt_Email . '\', \'' . $row->Txt_Email_Contacto . '\', \'' . $asunto . '\')"><i class="fa fa-2x fa-envelope" aria-hidden="true"></i></button>';
			
			$span_enlace_guias = '';
			$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
			$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParams);
			if ($arrResponseDocument['sStatus'] == 'success') {
				$span_enlace_guias = '';
				foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
					$sUrlPDFSunatGuia = (!empty($rowEnlace->Txt_Url_PDF) ? ' <a alt="Descargar PDF" title="Descargar PDF" href="' . $rowEnlace->Txt_Url_PDF . '" target="_blank"><span class="label label-danger"> PDF </span></a>' : '');
					$span_enlace_guias .= '<span title="Ver guias en Logistica > Guia / Salida de Inventario" class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . ' ' . $sUrlPDFSunatGuia . "</span><br>";
				}
			}

			$arrParamsGuia = json_encode(array(
				'sTipoCodificacion' => 'json',
				'ID_Empresa' => $row->ID_Empresa,
				'ID_Organizacion' => $row->ID_Organizacion,
				'ID_Almacen' => $row->ID_Almacen,
				'ID_Moneda' => $row->ID_Moneda,
				'ID_Documento_Cabecera' => $row->ID_Documento_Cabecera,
				'ID_Entidad' => $row->ID_Entidad,
				'ID_Tipo_Documento' => $row->ID_Tipo_Documento,
				'ID_Lista_Precio_Cabecera' => 0,
				'Fe_Emision' => $row->Fe_Emision,
				'ID_Serie_Documento' => '',
				'ID_Numero_Documento' => $row->ID_Documento_Cabecera,
				'Ss_Total' => $row->Ss_Total
			));

			$btn_facturar = '';
			$btn_guia = '';
            if ($row->Nu_Estado != 3) {
				$btn_facturar='<button class="btn btn-xs btn-link" alt="Facturar" title="Facturar" href="javascript:void(0)" onclick="facturarOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-2x fa-shopping-cart" aria-hidden="true"></i></button>';
				$btn_guia='<button type="button" class="btn btn-xs btn-link" alt="Generar Guía" title="Generar Guía" href="javascript:void(0)" onclick=generarGuia(\'' . $arrParamsGuia . '\')><i class="fa fa-2x fa-truck" aria-hidden="true"></i></button>';
				if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Agregar == 0) {
					$btn_facturar='';
					$btn_guia='';
				}
			}

			if ($row->Nu_Estado == 5 || $row->Nu_Estado == 0 || $row->Nu_Estado == 1 || $row->Nu_Estado == 2) {
				//$rows[] = '<button class="btn btn-xs btn-link" alt="Enviar Correo" title="Enviar Correo" href="javascript:void(0)" onclick="enviarCorreo(\'' . $row->ID_Documento_Cabecera . '\', \'' . $this->user->Txt_Email . '\', \'' . $row->Txt_Email_Contacto . '\', \'' . $asunto . '\')"><i class="fa fa-2x fa-envelope" aria-hidden="true"></i></button>';
				$btn_duplicar='<button class="btn btn-xs btn-link" alt="Duplicar" title="Duplicar" href="javascript:void(0)" onclick="duplicarOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-2x fa-clone" aria-hidden="true"></i></button>';
				if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Agregar == 0)
					$btn_duplicar='';
				$rows[] = $btn_duplicar;
			} else {
				$rows[] = '';
				$rows[] = '';
			}

			if ($row->Nu_Estado == 5 || $row->Nu_Estado == 0 || $row->Nu_Estado == 1) {
				//$rows[] = '';
				$rows[] = $btn_facturar . $span_enlace_documentos;
				$rows[] = $btn_guia . $span_enlace_guias;
				//$rows[] = $span_enlace_documentos;

				$btn_modificar='<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
				if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 0)
					$btn_modificar='';
				$rows[] = $btn_modificar;
				//$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarOrdenVenta(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
			} else {
				//$rows[] = '';
				$rows[] = $btn_facturar . $span_enlace_documentos;
				$rows[] = $btn_guia . $span_enlace_guias;
				//$rows[] = $span_enlace_documentos;
				$rows[] = '';
				//$rows[] = '';
			}
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->OrdenVentaModel->count_all(),
	        'recordsFiltered' => $this->OrdenVentaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
	public function ajax_edit($ID){
        $data = $this->OrdenVentaModel->get_by_id($this->security->xss_clean($ID));
        $arrImpuesto = $this->HelperModel->getImpuestos($arrPost = '');
		if (!empty($data[0]->Txt_Glosa)) {
			if ( base64_encode(base64_decode($data[0]->Txt_Glosa, true)) === $data[0]->Txt_Glosa){
				$data[0]->Txt_Glosa = base64_decode($data[0]->Txt_Glosa);
				$data[0]->Txt_Glosa = utf8_encode($data[0]->Txt_Glosa);
			}
		}
        $output = array(
        	'arrEdit' => $data,
        	'arrImpuesto' => $arrImpuesto,
        );
        echo json_encode($output);
    }
    
	public function crudOrdenVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');

		$arrClienteNuevo = '';
		if (isset($_POST['arrClienteNuevo'])){
			$Nu_Telefono_Cliente = '';
			if ( $_POST['arrClienteNuevo']['Nu_Telefono_Entidad'] && strlen($_POST['arrClienteNuevo']['Nu_Telefono_Entidad']) === 8){
		        $Nu_Telefono_Cliente = explode(' ', $_POST['arrClienteNuevo']['Nu_Telefono_Entidad']);
		        $Nu_Telefono_Cliente = $Nu_Telefono_Contacto[0].$Nu_Telefono_Contacto[1];
			}
			
			$Nu_Celular_Cliente = '';
			if ( $_POST['arrClienteNuevo']['Nu_Celular_Entidad'] && strlen($_POST['arrClienteNuevo']['Nu_Celular_Entidad']) === 11){
		        $Nu_Celular_Cliente = explode(' ', $_POST['arrClienteNuevo']['Nu_Celular_Entidad']);
		        $Nu_Celular_Cliente = $Nu_Celular_Cliente[0].$Nu_Celular_Cliente[1].$Nu_Celular_Cliente[2];
			}
			$arrClienteNuevo = array(
				'ID_Tipo_Documento_Identidad'	=> $this->security->xss_clean($_POST['arrClienteNuevo']['ID_Tipo_Documento_Identidad']),
				'Nu_Documento_Identidad'		=> $this->security->xss_clean(strtoupper($_POST['arrClienteNuevo']['Nu_Documento_Identidad'])),
				'No_Entidad'					=> $this->security->xss_clean($_POST['arrClienteNuevo']['No_Entidad']),
				'Txt_Direccion_Entidad'			=> $this->security->xss_clean($_POST['arrClienteNuevo']['Txt_Direccion_Entidad']),
				'Nu_Telefono_Entidad'			=> $Nu_Telefono_Cliente,
				'Nu_Celular_Entidad'			=> $Nu_Celular_Cliente,
				'Txt_Email_Entidad'			=> $this->security->xss_clean($_POST['arrClienteNuevo']['Txt_Email_Entidad']),
			);
		}
		
		$arrContactoNuevo = '';
		if (isset($_POST['arrContactoNuevo'])){
			$Nu_Telefono_Contacto = '';
			if ( $_POST['arrContactoNuevo']['Nu_Telefono_Entidad'] && strlen($_POST['arrContactoNuevo']['Nu_Telefono_Entidad']) === 8){
		        $Nu_Telefono_Contacto = explode(' ', $_POST['arrContactoNuevo']['Nu_Telefono_Entidad']);
		        $Nu_Telefono_Contacto = $Nu_Telefono_Contacto[0].$Nu_Telefono_Contacto[1];
			}
			
			$Nu_Celular_Contacto = '';
			if ( $_POST['arrContactoNuevo']['Nu_Celular_Entidad'] && strlen($_POST['arrContactoNuevo']['Nu_Celular_Entidad']) === 11){
		        $Nu_Celular_Contacto = explode(' ', $_POST['arrContactoNuevo']['Nu_Celular_Entidad']);
		        $Nu_Celular_Contacto = $Nu_Celular_Contacto[0].$Nu_Celular_Contacto[1].$Nu_Celular_Contacto[2];
			}
			$arrContactoNuevo = array(
				'ID_Tipo_Documento_Identidad'	=> $this->security->xss_clean($_POST['arrContactoNuevo']['ID_Tipo_Documento_Identidad']),
				'Nu_Documento_Identidad'		=> $this->security->xss_clean(strtoupper($_POST['arrContactoNuevo']['Nu_Documento_Identidad'])),
				'No_Entidad'					=> $this->security->xss_clean($_POST['arrContactoNuevo']['No_Entidad']),
				'Nu_Telefono_Entidad'			=> $Nu_Telefono_Contacto,
				'Nu_Celular_Entidad'			=> $Nu_Celular_Contacto,
				'Txt_Email_Entidad'				=> $this->security->xss_clean($_POST['arrContactoNuevo']['Txt_Email_Entidad']),
			);
		}
		
		$iDescargarStock = $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Nu_Descargar_Inventario']);

		$sGlosa = $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Txt_Glosa']);
		mb_internal_encoding('UTF-8');    
		$sGlosa = html_entity_decode($sGlosa, ENT_QUOTES, "UTF-8");
		$sGlosaLength = mb_strlen($sGlosa);
		if( $sGlosaLength > 10000 ){
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'La glosa puede tener maximo 6000 caracteres ' . strlen($sGlosa)));
			exit();
		}

		$arrOrdenVentaCabecera = array(
			'ID_Empresa'					=> $this->empresa->ID_Empresa,
			'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
			'ID_Tipo_Asiento'				=> 1,//Venta
			'ID_Tipo_Documento'	=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Tipo_Documento']),//Proforma
			'Nu_Correlativo'				=> 0,
			'Fe_Emision'					=> ToDate($this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Fe_Emision'])),
			'Fe_Vencimiento'				=> ToDate($this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Fe_Vencimiento'])),
			'Fe_Periodo'					=> ToDate($this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Fe_Entrega'])),
			'ID_Moneda'						=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Moneda']),
			'ID_Medio_Pago'					=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Medio_Pago']),
			'Nu_Descargar_Inventario'		=> $iDescargarStock,
			'ID_Entidad'					=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Entidad']),
			'ID_Contacto'					=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Contacto']),
			'Txt_Garantia'					=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Txt_Garantia']),
			'Txt_Glosa'						=> base64_encode($sGlosa),
			'Po_Descuento'					=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Po_Descuento']),
			'Ss_Descuento'					=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Ss_Descuento']),
			'Ss_Total'						=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Ss_Total']),
			'Nu_Estado'						=> ($this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ENu_Estado']) != '' ? $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ENu_Estado']) : 5),
			'ID_Mesero'	=> $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Mesero']),
			'ID_Comision' => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Comision']),
			'No_Formato_PDF' => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['No_Formato_PDF']),
			'Ss_Descuento_Impuesto' => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['Ss_Descuento_Impuesto']),
			'addCliente' => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['addCliente']),
			'addContacto' => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['addContacto']),
		);

		if ( $_POST['arrOrdenVentaCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
			$arrOrdenVentaCabecera = array_merge($arrOrdenVentaCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Lista_Precio_Cabecera'])));

		//if ( $iDescargarStock == 1 ) {
			$arrOrdenVentaCabecera = array_merge($arrOrdenVentaCabecera, array("ID_Almacen" => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['ID_Almacen'])));
		//}

		echo json_encode(
		( $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['EID_Empresa']) != '' && $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['EID_Documento_Cabecera']) != '') ?
			$this->actualizarVenta_Inventario(array('ID_Empresa' => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['EID_Empresa']), 'ID_Documento_Cabecera' => $this->security->xss_clean($_POST['arrOrdenVentaCabecera']['EID_Documento_Cabecera'])), $arrOrdenVentaCabecera, $_POST['arrDetalleOrdenVenta'], $arrOrdenVentaCabecera['Nu_Descargar_Inventario'], $arrClienteNuevo, $arrContactoNuevo)
		:
			$this->agregarVenta_Inventario($arrOrdenVentaCabecera, $_POST['arrDetalleOrdenVenta'], $arrOrdenVentaCabecera['Nu_Descargar_Inventario'], $arrClienteNuevo, $arrContactoNuevo)
		);
	}

	public function agregarVenta_Inventario($arrOrdenVentaCabecera = '', $arrDetalleOrdenVenta = '', $Nu_Descargar_Inventario = '', $arrClienteNuevo = '', $arrContactoNuevo = ''){
		return $this->OrdenVentaModel->agregarVenta($arrOrdenVentaCabecera, $arrDetalleOrdenVenta, $arrClienteNuevo, $arrContactoNuevo);
	}

	public function actualizarVenta_Inventario($arrWhereVenta = '', $arrOrdenVentaCabecera = '', $arrDetalleOrdenVenta = '', $Nu_Descargar_Inventario = '', $arrClienteNuevo = '', $arrContactoNuevo = ''){
		return $this->OrdenVentaModel->actualizarVenta($arrWhereVenta, $arrOrdenVentaCabecera, $arrDetalleOrdenVenta, $arrClienteNuevo, $arrContactoNuevo);
	}
	
	public function eliminarOrdenVenta($ID, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrdenVentaModel->eliminarOrdenVenta($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function estadoOrdenVenta($ID, $Nu_Descargar_Inventario, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrdenVentaModel->estadoOrdenVenta($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Descargar_Inventario), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function duplicarOrdenVenta($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrdenVentaModel->duplicarOrdenVenta($this->security->xss_clean($ID)));
	}

	public function getOrdenVentaPDF($ID){
		$ID = base64_decode($ID);//encriptacion
        $data = $this->OrdenVentaModel->get_by_id($this->security->xss_clean($ID));

		if ( $data[0]->No_Formato_PDF!='TICKET' ) {
			$this->load->library('Pdf');
			
			$this->load->library('EnLetras', 'el');
			$EnLetras = new EnLetras();

			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
			
			ob_start();
			$file = $this->load->view('Ventas/pdf/orden_venta_view', array(
				'arrDataEmpresa' => $data,
				'arrData' => $data,
				'totalEnLetras'	=> $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),
			));
			$html = ob_get_contents();
			ob_end_clean();
			
			$pdf->SetAuthor('laesystems');
			$pdf->SetTitle('laesystems_' . $data[0]->No_Tipo_Documento . '_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Documento_Cabecera);
		
			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-20, PDF_MARGIN_RIGHT-5);
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			
			$pdf->AddPage();
				
			$sNombreLogo=str_replace(' ', '_', $data[0]->No_Logo_Empresa);
			$sNombreLogoAlmacen=str_replace(' ', '_', $data[0]->No_Logo_Almacen);
			if ($data[0]->Nu_MultiAlmacen == 0) {
				$sRutaArchivoLogoCliente = $this->logo_cliente_path . $sNombreLogo;
				$sUrlImagen = $data[0]->No_Imagen_Logo_Empresa;
			} else {
				$sRutaArchivoLogoCliente = $this->logo_cliente_logos_empresa_almacen_path . $sNombreLogoAlmacen;
				$sUrlImagen = $data[0]->No_Logo_Url_Almacen;
			}

			$sCssFontFamily='Arial';
			$format_header = '<table border="0" cellspacing="1" cellpadding="0">';
				$format_header .= '<tr>';
					$format_header .= '<td style="width: 50%; text-align: left;">';
						$format_header .= '<img style="height: ' . $data[0]->Nu_Height_Logo_Ticket . 'px; width: ' . $data[0]->Nu_Width_Logo_Ticket . 'px;" src="' . $sUrlImagen . '"><br>';
					$format_header .= '</td>';
					$format_header .= '<td style="width: 50%; text-align: right;">';
						if(!empty($data[0]->No_Empresa_Comercial))
							$format_header .= '<label style="font-size: 11px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $data[0]->No_Empresa_Comercial . '</b></label><br>';
						else
							$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $data[0]->No_Empresa . '</b></label><br>';
						$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>RUC: ' . $data[0]->Nu_Documento_Identidad_Empresa . '</b></label><br>';
						if(!empty($data[0]->Txt_Direccion_Empresa))
							$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . $data[0]->Txt_Direccion_Empresa . ' - ' . $data[0]->No_Departamento . ' - ' . $data[0]->No_Provincia . ' - ' . $data[0]->No_Distrito . '</label><br>';
						if( $data[0]->Txt_Direccion_Empresa != $data[0]->Txt_Direccion_Almacen ) 
							$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . $data[0]->Txt_Direccion_Almacen . '</label><br>';
						if(!empty($data[0]->No_Dominio_Empresa))
							$format_header .= '<label style="color: #000000; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $data[0]->No_Dominio_Empresa . '</label><br>';
						if(!empty($data[0]->Nu_Celular_Empresa))
							$format_header .= '<label style="color: #868686; font-size: 10px; font-family: "Times New Roman", Times, serif;">Celular: ' . $data[0]->Nu_Celular_Empresa . '</label><br>';
						if(!empty($data[0]->Txt_Email_Empresa))
							$format_header .= '<label style="color: #34bdad; font-size: 10px; font-family: "Times New Roman", Times, serif;">Correo: ' . $data[0]->Txt_Email_Empresa . '</label><br>';
						if(!empty($data[0]->Txt_Slogan_Empresa))
							$format_header .= '<label style="color: #979797; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $data[0]->Txt_Slogan_Empresa . '</label>';
					$format_header .= '</td>';
				$format_header .= '</tr>';
			$format_header .= '</table>';
			$pdf->writeHTML($format_header, true, 0, true, 0);

			$pdf->setFont('helvetica', '', 7);
			$pdf->writeHTML($html, true, false, true, false, '');
			
			$file_name = "laesystems_" . $data[0]->No_Tipo_Documento . '_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Documento_Cabecera . ".pdf";
			$pdf->Output($file_name, 'I');
		} else {
			$this->getOrdenVentaTicketPDF($ID);
		}
	}

	public function getOrdenVentaTicketPDF($ID){
        $data = $this->OrdenVentaModel->get_by_id($this->security->xss_clean($ID));
		$this->load->library('Pdf');
		
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();

		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$sNombreLogo=str_replace(' ', '_', $data[0]->No_Logo_Empresa);
		$sNombreLogoAlmacen=str_replace(' ', '_', $data[0]->No_Logo_Almacen);
		if ($data[0]->Nu_MultiAlmacen == 0) {
			$sRutaArchivoLogoCliente = $this->logo_cliente_path . $sNombreLogo;
			$sUrlImagen = $data[0]->No_Imagen_Logo_Empresa;
		} else {
			$sRutaArchivoLogoCliente = $this->logo_cliente_logos_empresa_almacen_path . $sNombreLogoAlmacen;
			$sUrlImagen = $data[0]->No_Logo_Url_Almacen;
		}

		ob_start();
		$file = $this->load->view('Ventas/pdf/orden_venta_ticket_view', array(
			'sUrlImagen' => $sUrlImagen,
			'arrDataEmpresa' => $data,
			'arrData' => $data,
			'totalEnLetras'	=> $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),
		));
		$html = ob_get_contents();
		ob_end_clean();
		
		$pdf->SetAuthor('laesystems');
		$pdf->SetTitle('laesystems_' . $data[0]->No_Tipo_Documento . '_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Documento_Cabecera);
	
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
		
		$file_name = "laesystems_" . $data[0]->No_Tipo_Documento . '_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Documento_Cabecera . ".pdf";
		$pdf->Output($file_name, 'I');
	}

	public function enviarCorreo(){
        $arrData = $this->OrdenVentaModel->get_by_id($this->input->post('iIdOrden'));

		$this->load->library('email');

		$data = array();

		$data["No_Documento"] = $arrData[0]->No_Tipo_Documento . ' Nro. ' . $this->input->post('iIdOrden');
		$data["Fe_Emision"] = ToDateBD($arrData[0]->Fe_Emision);
		$data["Fe_Vencimiento"] = ToDateBD($arrData[0]->Fe_Vencimiento);
		$data["No_Signo"] = $arrData[0]->No_Signo;
		$data["Ss_Total"] = $arrData[0]->Ss_Total;
		
		$data["No_Entidad"] = $arrData[0]->No_Entidad;
		
		$data["No_Empresa"]	= $this->empresa->No_Empresa;
		$data["Nu_Documento_Identidad_Empresa"] = $this->empresa->Nu_Documento_Identidad;
		
		$message = $this->load->view('correos/orden_venta', $data, true);
		$asunto = $this->input->post('sAsunto');
		
		$this->email->from('noreply@laesystems.com', $this->empresa->No_Empresa);//de
		$this->email->to($this->input->post('sCorreoPara'));//para
		$this->email->subject($asunto);
		$this->email->message($message);

		$this->load->library('Pdf');
		
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();

		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		
		ob_start();
		$file = $this->load->view('Ventas/pdf/orden_venta_view', array(
			'arrData' => $arrData,
			'totalEnLetras'	=> $EnLetras->ValorEnLetras($arrData[0]->Ss_Total, $arrData[0]->No_Moneda),
		));
		$html = ob_get_contents();
		ob_end_clean();
		
		$pdf->SetAuthor('laesystems');
		$pdf->SetTitle('laesystems_' . $arrData[0]->No_Tipo_Documento . '_' . $arrData[0]->Nu_Documento_Identidad . '_' . $arrData[0]->ID_Documento_Cabecera);
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
		$pdf->AddPage();
		
		$sCssFontFamily='Arial';
		$sNombreLogo=str_replace(' ', '_', $this->empresa->No_Logo_Empresa);
		if ( !file_exists($this->file_path . $sNombreLogo) ) {
			$sNombreLogo='lae_logo_cotizacion.png';
		}
		$format_header = '<table border="0" cellspacing="1" cellpadding="0">';
			$format_header .= '<tr>';
				$format_header .= '<td style="width: 50%; text-align: left;">';
					$format_header .= '<img style="height: ' . $this->empresa->Nu_Height_Logo_Ticket . 'px; width: ' . $this->empresa->Nu_Width_Logo_Ticket . 'px;" src="' . $this->empresa->No_Imagen_Logo_Empresa . '"><br>';
				$format_header .= '</td>';
				$format_header .= '<td style="width: 50%; text-align: right;">';
					if(!empty($this->empresa->No_Empresa_Comercial))
						$format_header .= '<label style="font-size: 11px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $this->empresa->No_Empresa_Comercial . '</b></label><br>';
					else
						$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $this->empresa->No_Empresa . '</b></label><br>';
					$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>RUC: ' . $this->empresa->Nu_Documento_Identidad . '</b></label><br>';
					if(!empty($this->empresa->Txt_Direccion_Empresa))
						$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . $this->empresa->Txt_Direccion_Empresa . ' - ' . $data[0]->No_Provincia . ' - ' . $data[0]->No_Distrito . '</label><br>';
					if(!empty($this->empresa->No_Dominio_Empresa))
						$format_header .= '<label style="color: #000000; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $this->empresa->No_Dominio_Empresa . '</label><br>';
					if(!empty($this->empresa->Nu_Celular_Empresa))
						$format_header .= '<label style="color: #868686; font-size: 10px; font-family: "Times New Roman", Times, serif;">Celular: ' . $this->empresa->Nu_Celular_Empresa . '</label><br>';
					if(!empty($this->empresa->Txt_Email_Empresa))
						$format_header .= '<label style="color: #34bdad; font-size: 10px; font-family: "Times New Roman", Times, serif;">Correo: ' . $this->empresa->Txt_Email_Empresa . '</label><br>';
					if(!empty($this->empresa->Txt_Slogan_Empresa))
						$format_header .= '<label style="color: #979797; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $this->empresa->Txt_Slogan_Empresa . '</label>';
				$format_header .= '</td>';
			$format_header .= '</tr>';
		$format_header .= '</table>';
		$pdf->writeHTML($format_header, true, 0, true, 0);
		
        $pdf->setFont('helvetica', '', 7);
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$file_name = "laesystems_" . $arrData[0]->No_Tipo_Documento . "_" . $arrData[0]->Nu_Documento_Identidad . "_" . $arrData[0]->ID_Documento_Cabecera . ".pdf";
		$pdfdoc = $pdf->Output(__DIR__ . $file_name, "F");
		
		$this->email->attach(__DIR__ . $file_name);

		$this->email->set_newline("\r\n");

		$isSend = $this->email->send();
		
		if($isSend) {
			unlink(__DIR__ . $file_name);
			$peticion = array(
				'status' => 'success',
				'style_modal' => 'modal-success',
				'message' => 'Correo enviado',
			);
			echo json_encode($peticion);
			exit();
		} else {
			unlink(__DIR__ . $file_name);
			$peticion = array(
				'status' => 'error',
				'style_modal' => 'modal-danger',
				'message' => 'No se pudo enviar el correo, inténtelo más tarde.',
				'sMessageErrorEmail' => $this->email->print_debugger(),
			);
			echo json_encode($peticion);
			exit();
		}// if - else envio email
	}

	public function generarPDFCotizacionPublica($arrParamsApi){
		$arrData = $arrParamsApi['arrData'];
		$data_json = json_encode($arrData);
	
		array_debug($arrParamsApi);
		array_debug($data_json);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $arrParamsApi['ruta']);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$arrParamsApi['token'].'"',
			'Content-Type: application/json',
			'X-API-Key: ' . $arrParamsApi['token'],
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Obtener el código de respuesta
		$respuesta = curl_exec($ch);
		
		var_dump($respuesta);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );	
		curl_close($ch);
		// Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);
		} else {
			$response = array(
				'status' => 'warning',
				'message' => 'Problemas con la api - cotizacion',
			);
		}
	}
}