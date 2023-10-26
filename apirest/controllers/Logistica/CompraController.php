<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class CompraController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Logistica/CompraModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
	}

	public function listarCompras(){
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
			$this->load->view('Logistica/CompraView', array("arrDataProveedor"=>$arrDataProveedor));
			$this->load->view('footer', array("js_compra" => true));
		}
	}
	
	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->CompraModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action_anular = 'anular';
        $action_delete = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = ToDateBD($row->Fe_Emision);
            $rows[] = $row->No_Tipo_Documento_Breve;
            $rows[] = $row->ID_Serie_Documento;
            $rows[] = $row->ID_Numero_Documento;
            $rows[] = $row->Nu_Documento_Identidad_Proveedor;
            $rows[] = $row->No_Entidad;
			$rows[] = $row->No_Medio_Pago;
            $rows[] = $row->No_Signo;
            $rows[] = numberFormat($row->Ss_Percepcion, 2, '.', ',');
			
			$objImporteDetalleDocumento = $this->CompraModel->obtenerImporteDetalleDocumento($row->ID_Documento_Cabecera);
            $rows[] = numberFormat($objImporteDetalleDocumento->Ss_SubTotal, 2, '.', ',');//subtotal
            $rows[] = numberFormat($objImporteDetalleDocumento->Ss_Impuesto, 2, '.', ',');//impuestos
            $rows[] = numberFormat($row->Ss_Total, 2, '.', ',');
            $rows[] = numberFormat($row->Ss_Total_Saldo, 2, '.', ',');
			$rows[] = ($row->Nu_Descargar_Inventario == 1 ? 'Si' : 'No');
			$rows[] = $row->Txt_Glosa;
			$rows[] = (!empty($row->Fe_Detraccion) ? ToDateBD($row->Fe_Detraccion) : '');
			$rows[] = $row->Nu_Detraccion;
			$sEstadoPago = 'pendiente';
			$sEstadoPagoClass = 'warning';
			if ($row->Ss_Total_Saldo == 0.00) {
				$sEstadoPago = 'cancelado';
				$sEstadoPagoClass = 'success';
			}				
            $rows[] = '<span class="label label-' . $sEstadoPagoClass . '">' . $sEstadoPago . '</span>';
			
			$arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoDocumento['No_Class_Estado'] . '">' . $arrEstadoDocumento['No_Estado'] . '</span>';
			
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

			$rows[] = $span_enlace_documentos;

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
					foreach ($arrResponseDocument['arrData'] as $rowEnlace)
						$span_enlace_guias .= '<span class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>";
				}
			}

			$rows[] = $span_enlace_guias;

			$btn_pagar_proveedor = '';
			if ( $row->Nu_Tipo_Medio_Pago == 1 && $row->Ss_Total_Saldo > 0 ) {
				$btn_pagar_proveedor = '<button type="button" class="btn btn-xs btn-link" alt="Pagar a Proveedor" title="Pagar a Proveedor" href="javascript:void(0)" onclick="pagarProveedor(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Ss_Total_Saldo . '\', \'' . $row->No_Entidad . '\', \'' . $row->No_Tipo_Documento_Breve . '\', \'' . $row->ID_Serie_Documento . '\', \'' . $row->ID_Numero_Documento . '\', \'' . $row->No_Signo . '\')">Pagar</button>';
			}
			$rows[] = $btn_pagar_proveedor;

			$btn_modificar = '';
			$btn_anular = '';
			$btn_eliminar = '';
			
			if ($row->Nu_Estado == 6)
				$btn_modificar = '<button type="button" class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCompra(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';
				
			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 0)
				$btn_modificar='';
			$rows[] = $btn_modificar;
			
			$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
			$arrResponseDocument = $this->HelperModel->getDocumentoEnlace($arrParams);
			$iEnlace=0;
			if ($arrResponseDocument['sStatus'] == 'success')
				$iEnlace=1;

			if ($iEnlace == 0 && $row->Nu_Estado == 6) {
				$btn_anular = '<button type="button" class="btn btn-xs btn-link" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularCompra(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Enlace . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action_anular . '\')"><i class="fa fa-minus-circle fa-2x" aria-hidden="true"></i></button>';
				$btn_eliminar = '<button type="button" class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCompra(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Enlace . '\', \'' . $row->Nu_Descargar_Inventario . '\', \'' . $action_delete . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
			} else {
				if ( $iEnlace == 1 ) {
					$btn_anular = '';
					$btn_eliminar = '';
					$arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
					$arrResponseDocument = $this->HelperModel->getDocumentoEnlace($arrParams);
					if ($arrResponseDocument['sStatus'] == 'success') {
						$btn_anular = '';
						$btn_eliminar = '';
						foreach ($arrResponseDocument['arrData'] as $rowEnlace)
							$btn_anular .= '<span class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>";
					}
				}
			}
			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Eliminar == 0){
				$btn_anular='';
				$btn_eliminar='';
			}
			$rows[] = $btn_anular;
			$rows[] = $btn_eliminar;
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->CompraModel->count_all(),
	        'recordsFiltered' => $this->CompraModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
	public function ajax_edit($ID){
        $data = $this->CompraModel->get_by_id($this->security->xss_clean($ID));		
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
		$response = array('status' => 'success', 'ID_Documento_Cabecera_Enlace' => '');
		if ( $_POST['arrCompraCabecera']['esEnlace'] == 1 ) {
			$response = $this->HelperModel->documentExistVerify($this->security->xss_clean($_POST['arrCompraModificar']['ID_Documento_Guardado']), $this->security->xss_clean($_POST['arrCompraModificar']['ID_Tipo_Documento_Modificar']), $this->security->xss_clean($_POST['arrCompraModificar']['ID_Serie_Documento_Modificar']), $this->security->xss_clean($_POST['arrCompraModificar']['ID_Numero_Documento_Modificar']), $_POST['arrCompraModificar']);
			$_POST['arrCompraCabecera']['esEnlace'] = ($response['status'] != 'danger' ? 1 : 0);
		}

		if ( $response['status'] != 'danger' && $response['status'] != 'error' ) {
			$arrProveedorNuevo = '';
			if (isset($_POST['arrProveedorNuevo'])){
				$arrProveedorNuevo = array(
					'ID_Tipo_Documento_Identidad'	=> $this->security->xss_clean($_POST['arrProveedorNuevo']['ID_Tipo_Documento_Identidad']),
					'Nu_Documento_Identidad'		=> $this->security->xss_clean(strtoupper($_POST['arrProveedorNuevo']['Nu_Documento_Identidad'])),
					'No_Entidad'					=> $this->security->xss_clean($_POST['arrProveedorNuevo']['No_Entidad']),
					'Txt_Direccion_Entidad'			=> $this->security->xss_clean($_POST['arrProveedorNuevo']['Txt_Direccion_Entidad']),
					'Nu_Telefono_Entidad'			=> $this->security->xss_clean($_POST['arrProveedorNuevo']['Nu_Telefono_Entidad']),
					'Nu_Celular_Entidad'			=> $this->security->xss_clean($_POST['arrProveedorNuevo']['Nu_Celular_Entidad']),
				);
			}

			$ID_Documento_Cabecera_Enlace = '';
			if ( isset($_POST['arrCompraCabecera']['ID_Documento_Cabecera_Orden']) && !empty($_POST['arrCompraCabecera']['ID_Documento_Cabecera_Orden']) ) {
				$ID_Documento_Cabecera_Enlace = $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Documento_Cabecera_Orden']);
				$_POST['arrCompraCabecera']['esEnlace'] = 1;
			}
			
			$Fe_Emision = ((!empty($_POST['arrCompraCabecera']['Fe_Emision']) && ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Emision'])) > '0000-00-00') ? ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Emision'])) : dateNow('fecha'));

			$arrCompraCabecera = array(
				'ID_Empresa'				=> $this->empresa->ID_Empresa,
				'ID_Organizacion'			=> $this->empresa->ID_Organizacion,
				'ID_Entidad'				=> $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Entidad']),
				'ID_Tipo_Asiento'			=> 2,
				'ID_Tipo_Documento'			=> $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Tipo_Documento']),
				'ID_Serie_Documento'		=> $this->security->xss_clean(strtoupper($_POST['arrCompraCabecera']['ID_Serie_Documento'])),
				'ID_Numero_Documento'		=> $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Numero_Documento']),
				'Fe_Emision'				=> $Fe_Emision,
				'Fe_Emision_Hora'			=> dateNow('fecha_hora'),
				'ID_Medio_Pago'				=> $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Medio_Pago']),
				'ID_Rubro'					=> 1,//Compras
				'ID_Moneda'					=> $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Moneda']),
				'Fe_Vencimiento' => ToDate( ($_POST['arrCompraCabecera']['Fe_Vencimiento'] == '00/00/0000' || empty($_POST['arrCompraCabecera']['Fe_Vencimiento']) || ToDate($_POST['arrCompraCabecera']['Fe_Vencimiento']) < ToDate($_POST['arrCompraCabecera']['Fe_Emision'])) ? $_POST['arrCompraCabecera']['Fe_Emision'] : $_POST['arrCompraCabecera']['Fe_Vencimiento']),
				'Fe_Periodo'				=> ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Periodo'])),
				'Nu_Descargar_Inventario'	=> $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Descargar_Inventario']),
				'Txt_Glosa'					=> $this->security->xss_clean($_POST['arrCompraCabecera']['Txt_Glosa']),
				'Po_Descuento'				=> $this->security->xss_clean($_POST['arrCompraCabecera']['Po_Descuento']),
				'Ss_Descuento'				=> $this->security->xss_clean($_POST['arrCompraCabecera']['Ss_Descuento']),
				'Ss_Total'					=> $this->security->xss_clean($_POST['arrCompraCabecera']['Ss_Total']),
				'Ss_Total_Saldo' => ($_POST['arrCompraCabecera']['iTipoFormaPago'] != '1' ? 0.00 : $this->security->xss_clean($_POST['arrCompraCabecera']['Ss_Total'])),
				'Ss_Percepcion'				=> $this->security->xss_clean($_POST['arrCompraCabecera']['Ss_Percepcion']),
				'Fe_Detraccion'				=> ToDate($this->security->xss_clean($_POST['arrCompraCabecera']['Fe_Detraccion'])),
				'Nu_Detraccion'				=> $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Detraccion']),
				'Nu_Estado'					=> 6,
				'ID_Almacen' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Almacen']),
				'iTipoCliente' => $this->security->xss_clean($_POST['arrCompraCabecera']['iTipoCliente']),
				'ID_Tipo_Medio_Pago' => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Tipo_Medio_Pago']),
				'Nu_Transaccion' => $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Transaccion']),
				'Nu_Tarjeta' => $this->security->xss_clean($_POST['arrCompraCabecera']['Nu_Tarjeta']),
				'ID_Documento_Cabecera_Enlace' => $ID_Documento_Cabecera_Enlace,
			);
			
			if ( $_POST['arrCompraCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
				$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Lista_Precio_Cabecera'])));
			
			if ( $_POST['arrCompraCabecera']['esEnlace']==2 )
				$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Guia_Cabecera" => $this->security->xss_clean($_POST['arrCompraCabecera']['ID_Guia_Cabecera'])));
			
			echo json_encode(
			($this->security->xss_clean($_POST['arrCompraCabecera']['EID_Empresa']) != '' && $this->security->xss_clean($_POST['arrCompraCabecera']['EID_Documento_Cabecera']) != '') ?
				$this->actualizarCompra_Inventario(array('ID_Empresa' => $this->security->xss_clean($_POST['arrCompraCabecera']['EID_Empresa']), 'ID_Documento_Cabecera' => $this->security->xss_clean($_POST['arrCompraCabecera']['EID_Documento_Cabecera'])), $arrCompraCabecera, $_POST['arrDetalleCompra'], $_POST['arrCompraCabecera']['esEnlace'], $response['ID_Documento_Cabecera_Enlace'], $arrCompraCabecera['Nu_Descargar_Inventario'], $arrProveedorNuevo)
			:
				$this->agregarCompra_Inventario($arrCompraCabecera, $_POST['arrDetalleCompra'], $_POST['arrCompraCabecera']['esEnlace'], $response['ID_Documento_Cabecera_Enlace'], $arrCompraCabecera['Nu_Descargar_Inventario'], $arrProveedorNuevo)
			);
		} else {
			echo json_encode($response);
		}
	}

	public function agregarCompra_Inventario($arrCompraCabecera = '', $arrDetalleCompra = '', $esEnlace = '', $ID_Documento_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrProveedorNuevo = ''){
		$responseCompra = $this->CompraModel->agregarCompra($arrCompraCabecera, $arrDetalleCompra, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrProveedorNuevo);
		if ($responseCompra['status'] == 'success') {
			if ($Nu_Descargar_Inventario == '1'){//1 = Si
				$arrCompraCabecera['ID_Tipo_Movimiento'] = 2;//Compra
				if ($arrCompraCabecera['ID_Tipo_Documento'] == 5)//NC
					$arrCompraCabecera['ID_Tipo_Movimiento'] = 18;//SALIDA POR DEVOLUCIÓN AL PROVEEDOR
				return $this->MovimientoInventarioModel->crudMovimientoInventario($arrCompraCabecera['ID_Almacen'], $responseCompra['Last_ID_Documento_Cabecera'], 0, $arrDetalleCompra, $arrCompraCabecera['ID_Tipo_Movimiento'], 0, '', 0, 1);
			}
			return $responseCompra;
		} else {
			return $responseCompra;
		}
	}

	public function actualizarCompra_Inventario($arrWhereCompra = '', $arrCompraCabecera = '', $arrDetalleCompra = '', $esEnlace = '', $ID_Documento_Cabecera_Enlace = '', $Nu_Descargar_Inventario = '', $arrProveedorNuevo = ''){
		$responseCompra = $this->CompraModel->actualizarCompra($arrWhereCompra, $arrCompraCabecera, $arrDetalleCompra, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrProveedorNuevo);
		if ($responseCompra['status'] == 'success') {
			if ($Nu_Descargar_Inventario == '1'){//1 = Si
				$arrCompraCabecera['ID_Tipo_Movimiento'] = 2;//Compra
				if ($arrCompraCabecera['ID_Tipo_Documento'] == 5)//NC
					$arrCompraCabecera['ID_Tipo_Movimiento'] = 18;//SALIDA POR DEVOLUCIÓN AL PROVEEDOR
				return $this->MovimientoInventarioModel->crudMovimientoInventario($arrCompraCabecera['ID_Almacen'], $responseCompra['Last_ID_Documento_Cabecera'], 0, $arrDetalleCompra, $arrCompraCabecera['ID_Tipo_Movimiento'], 1, $arrWhereCompra, 0, 1);
			}
			return $responseCompra;
		} else {
			return $responseCompra;
		}
	}
    
	public function anularCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->CompraModel->anularCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function eliminarCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->CompraModel->eliminarCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Enlace), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function obtenerImporteDetalleDocumento($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->CompraModel->obtenerImporteDetalleDocumento($ID));
	}
}
