<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class IngresoInventarioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Logistica/IngresoInventarioModel');
		$this->load->model('Logistica/CompraModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
	}

	public function listarIngresos(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			//obtener proveedor creado por empresa
			$arrParamsProveedor = array(
				'Nu_Tipo_Entidad' => 1,//1=proveedor
				'ID_Tipo_Documento_Identidad' => $this->empresa->ID_Tipo_Documento_Identidad,
				'Nu_Documento_Identidad' => $this->empresa->Nu_Documento_Identidad
			);
			$arrDataProveedor = $this->CompraModel->getEntidadProveedorInterno($arrParamsProveedor);
			$this->load->view('header');
			$this->load->view('Logistica/IngresoInventarioView', array("arrDataProveedor"=>$arrDataProveedor));
			$this->load->view('footer', array("js_ingreso_inventario" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->IngresoInventarioModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
		$action_anular = 'procesar_stock';
        $action_anular = 'anular';
        $action_delete = 'delete';
        foreach ($arrData as $row) {
			//if ( ($row->Nu_Tipo_Movimiento == 1 && $row->ID_Almacen_Transferencia != 0) || ($row->Nu_Tipo_Movimiento == 0 && $row->Nu_Proceso_Transferencia_Almacen == 0) ) {
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

				$arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
				$rows[] = '<span class="label label-' . $arrEstadoDocumento['No_Class_Estado'] . '">' . $arrEstadoDocumento['No_Estado'] . '</span>';

				$arrParams = array('ID_Guia_Cabecera' => $row->ID_Guia_Cabecera);
				$arrResponseDocument = $this->HelperModel->getGuianEnlaceOrigen($arrParams);
				$iEnlace=0;
				$span_enlace = '';
				if ($arrResponseDocument['sStatus'] == 'success') {
					$iEnlace=1;
					$span_enlace = '';
					foreach ($arrResponseDocument['arrData'] as $rowEnlace)
						$span_enlace .= '<span class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>";
				}
				$rows[] = $span_enlace;

				$btn_modificar = '';
				$btn_anular = '';
				$btn_eliminar = '';
				$btn_facturar='';
				$bProcesar=false;
				//if ( $row->ID_Almacen_Transferencia == 0 || $row->Nu_Proceso_Transferencia_Almacen == 0 ) {//El ingreso fue en el mismo almacen de la empresa, no fue transferencia
				//if ( $row->ID_Almacen_Transferencia == 0) {//El ingreso fue en el mismo almacen de la empresa, no fue transferencia
					if ($row->Nu_Estado == 6 || $row->Nu_Estado == 8) {
						if ( ($row->ID_Almacen_Transferencia == 0 && $row->Nu_Proceso_Transferencia_Almacen == 0) || ($row->ID_Almacen_Transferencia > 0 && $row->Nu_Proceso_Transferencia_Almacen == 1) ) {
							$btn_modificar = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCompra(\'' . $row->ID_Guia_Cabecera . '\', \'' . $iEnlace . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';
						} else {
							$bProcesar=true;
							$btn_modificar = '<button class="btn btn-xs btn-link" alt="Procesar stock" title="Procesar stock" href="javascript:void(0)" onclick="procesarStockSalida(\'' . $row->ID_Guia_Cabecera . '\', \'' . $row->ID_Tipo_Documento . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->ID_Almacen_Transferencia . '\', \'' . $action_anular . '\')">Procesar stock</button>';
						}
					}
					
					if ( $iEnlace==0 && ($row->Nu_Estado == 6 || $row->Nu_Estado == 8) && $bProcesar == false) {
						$btn_anular = '<button class="btn btn-xs btn-link" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularCompra(\'' . $row->ID_Guia_Cabecera . '\', \'' . $iEnlace . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action_anular . '\')"><i class="fa fa-minus-circle fa-2x" aria-hidden="true"></i></button>';
						$btn_facturar = '<button class="btn btn-xs btn-link" alt="Facturar" title="Facturar" href="javascript:void(0)" onclick="facturarGuia(\'' . $row->ID_Guia_Cabecera . '\')"><i class="fa fa-2x fa-book" aria-hidden="true"></i></button>';
					}
				//} else {
					//$btn_modificar = '<button class="btn btn-xs btn-link" alt="Procesar stock" title="Procesar stock" href="javascript:void(0)" onclick="procesarStockSalida(\'' . $row->ID_Guia_Cabecera . '\', \'' . $row->ID_Tipo_Documento . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->ID_Almacen_Transferencia . '\', \'' . $action_anular . '\')">Procesar stock</button>';
				//}
				$rows[] = $btn_facturar;
				$rows[] = $btn_modificar;
				$rows[] = $btn_anular . $btn_eliminar;

				//PDF
				$btn_pdf_interno = '<button type="button" class="btn btn-xs btn-link" alt="Representación Interna PDF" title="Representación Interna PDF" href="javascript:void(0)" onclick="verRepresentacionInternaPDF(\'' . $row->ID_Guia_Cabecera . '\')"><span class="label label-danger">PDF</span></button>';
				if ( $row->Nu_Estado != 6 )
					$btn_pdf_interno = '';
				$rows[] = $btn_pdf_interno;

				$data[] = $rows;
			//}
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->IngresoInventarioModel->count_all(),
	        'recordsFiltered' => $this->IngresoInventarioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
	public function ajax_edit($ID){
        $data = $this->IngresoInventarioModel->get_by_id($this->security->xss_clean($ID));
        $arrImpuesto = $this->HelperModel->getImpuestos($arrPost = '');
        $output = array(
        	'arrEdit' => $data,
        	'arrImpuesto' => $arrImpuesto,
        );
        echo json_encode($output);
    }
    
	public function crudCompra(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		
		$arrProveedorNuevo = '';
		if (isset($_POST['arrProveedorNuevo'])){
			$arrProveedorNuevo = array(
				'ID_Tipo_Documento_Identidad' => $this->security->xss_clean($_POST['arrProveedorNuevo']['ID_Tipo_Documento_Identidad']),
				'Nu_Documento_Identidad' => $this->security->xss_clean(strtoupper($_POST['arrProveedorNuevo']['Nu_Documento_Identidad'])),
				'No_Entidad' => $this->security->xss_clean($_POST['arrProveedorNuevo']['No_Entidad']),
				'Txt_Direccion_Entidad' => $this->security->xss_clean($_POST['arrProveedorNuevo']['Txt_Direccion_Entidad']),
				'Nu_Telefono_Entidad' => $this->security->xss_clean($_POST['arrProveedorNuevo']['Nu_Telefono_Entidad']),
				'Nu_Celular_Entidad' => $this->security->xss_clean($_POST['arrProveedorNuevo']['Nu_Celular_Entidad']),
			);
		}

		$response['ID_Guia_Cabecera_Enlace'] = '';
		$_POST['arrCompraCabecera']['esEnlace'] = 0;//Se usa para relacionar pero actualmente no se ha implementado por eso estará en 0
		$arrCompraCabecera = array(
			'ID_Empresa' => $this->empresa->ID_Empresa,
			'ID_Organizacion' => $this->empresa->ID_Organizacion,
			'ID_Almacen' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Almacen']),
			'ID_Tipo_Asiento' => 3,//Guías
			'ID_Tipo_Documento' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Tipo_Documento']),
			'ID_Serie_Documento' => $this->security->xss_clean(strtoupper($_POST['arrCompraCabecera']['ID_Serie_Documento'])),
			'ID_Numero_Documento' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Numero_Documento']),
			'Fe_Emision' => ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Emision'])),
			'Fe_Periodo' => ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Emision'])),
			'ID_Moneda' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Moneda']),
			'Nu_Descargar_Inventario' => $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Descargar_Inventario']),
			'ID_Tipo_Movimiento' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Tipo_Movimiento']),
			'ID_Entidad' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Entidad']),
			'iFlete' => $this->security->xss_clean($_POST['arrCompraCabecera']['iFlete']),
			'ID_Entidad_Transportista' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Entidad_Transportista']),
			'No_Placa' => $this->security->xss_clean(strtoupper($_POST['arrCompraCabecera']['No_Placa'])),
			'Fe_Traslado' => ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Traslado'])),
			'ID_Motivo_Traslado' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Motivo_Traslado']),
			'No_Licencia' => $this->security->xss_clean($_POST['arrCompraCabecera']['No_Licencia']),
			'No_Certificado_Inscripcion' => $this->security->xss_clean($_POST['arrCompraCabecera']['No_Certificado_Inscripcion']),
			'Txt_Glosa' => $this->security->xss_clean($_POST['arrCompraCabecera']['Txt_Glosa']),
			'Ss_Total' => $this->security->xss_clean($_POST['arrCompraCabecera']['Ss_Total']),
			'Nu_Estado' => 6,
		);
		
		if ( $_POST['arrCompraCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
			$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Lista_Precio_Cabecera'])));
		
		echo json_encode(
		($this->security->xss_clean($_POST['arrCompraCabecera']['EID_Guia_Cabecera']) != '') ?
			$this->actualizarCompra_Inventario(array('ID_Guia_Cabecera' => $this->security->xss_clean($_POST['arrCompraCabecera']['EID_Guia_Cabecera'])), $arrCompraCabecera, $_POST['arrDetalleCompra'], $_POST['arrCompraCabecera']['esEnlace'], $response['ID_Guia_Cabecera_Enlace'], $arrCompraCabecera['Nu_Descargar_Inventario'], $arrProveedorNuevo)
		:
			$this->agregarCompra_Inventario($arrCompraCabecera, $_POST['arrDetalleCompra'], $_POST['arrCompraCabecera']['esEnlace'], $response['ID_Guia_Cabecera_Enlace'], $arrCompraCabecera['Nu_Descargar_Inventario'], $arrProveedorNuevo)
		);
	}

	public function agregarCompra_Inventario($arrCompraCabecera = '', $arrDetalleCompra = '', $esEnlace = '', $ID_Guia_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrProveedorNuevo = ''){
		$responseCompra = $this->IngresoInventarioModel->agregarCompra($arrCompraCabecera, $arrDetalleCompra, $esEnlace, $ID_Guia_Cabecera_Enlace, $arrProveedorNuevo);
		if ($responseCompra['status'] == 'success') {
			if ($Nu_Descargar_Inventario == '1')//1 = Si
				return $this->MovimientoInventarioModel->crudMovimientoInventario($arrCompraCabecera['ID_Almacen'], 0, $responseCompra['Last_ID_Guia_Cabecera'], $arrDetalleCompra, $arrCompraCabecera['ID_Tipo_Movimiento'], 0, '', 0, 1);
			return $responseCompra;
		} else {
			return $responseCompra;
		}
	}

	public function actualizarCompra_Inventario($arrWhereCompra = '', $arrCompraCabecera = '', $arrDetalleCompra = '', $esEnlace = '', $ID_Guia_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrProveedorNuevo = ''){
		$responseCompra = $this->IngresoInventarioModel->actualizarCompra($arrWhereCompra, $arrCompraCabecera, $arrDetalleCompra, $esEnlace, $ID_Guia_Cabecera_Enlace, $arrProveedorNuevo);
		if ($responseCompra['status'] == 'success') {
			if ($Nu_Descargar_Inventario == '1')//1 = Si
				return $this->MovimientoInventarioModel->crudMovimientoInventario($arrCompraCabecera['ID_Almacen'], 0, $responseCompra['Last_ID_Guia_Cabecera'], $arrDetalleCompra, $arrCompraCabecera['ID_Tipo_Movimiento'], 1, $arrWhereCompra, 0, 1);
			return $responseCompra;
		} else {
			return $responseCompra;
		}
	}
    
	public function anularCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->IngresoInventarioModel->anularCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function eliminarCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->IngresoInventarioModel->eliminarCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
    
	public function procesarStockTransferencia(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$arrResponse = $this->IngresoInventarioModel->procesarStockTransferencia($this->input->post());
		if ($arrResponse['status'] == 'success') {
			if ($arrResponse['sDescargarStock'] == 1){//Descarga stock
				echo json_encode($this->MovimientoInventarioModel->crudMovimientoInventario($this->input->post('ID_Almacen_Modal'), 0, $arrResponse['Last_ID_Guia_Cabecera'], $arrResponse['arrDetalle'], 13, 0, '', 0, 1));
				exit();
			}
			echo json_encode($arrResponse);
			exit();
		} else {
			echo json_encode($arrResponse);
			exit();
		}
	}
    
	public function getSalidaInventarioTransferencia(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->IngresoInventarioModel->getSalidaInventarioTransferencia($this->input->post('ID')));
	}

	public function generarRepresentacionInternaPDF($ID){
        $arrData = $this->IngresoInventarioModel->get_by_id($this->security->xss_clean($ID));
		if ( !empty($arrData) ) {
			$this->generarRepresentacionInternaA4PDF($arrData);
		} else {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe registro'));
			exit();
		}
	}

	public function generarRepresentacionInternaA4PDF($arrData){			
		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();
		
		$this->load->library('Pdf');
		
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		
		ob_start();
		$file = $this->load->view('Logistica/pdf/RepresentacionInternaViewEntradaPDF', array(
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
					$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . strtoupper($arrData[0]->No_Tipo_Documento) . '</b></label>';
				$format_header .= '</td>';
			$format_header .= '</tr>';
			$format_header .= '<tr>';
				$format_header .= '<td style="text-align: center; background-color:#F2F5F5;">';
					$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>RUC ' . $this->empresa->Nu_Documento_Identidad . '</b></label>';
				$format_header .= '</td>';
				$format_header .= '<td style="text-align: center; background-color:#F2F5F5;">';
					$format_header .= '<label style="color: #2a2a2a ; font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento . '</b></label>';
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
}
