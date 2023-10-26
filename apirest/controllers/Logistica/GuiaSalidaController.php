<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class GuiaSalidaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Logistica/GuiaSalidaModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
	}

	public function listarGuiasSalida(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/GuiaSalidaView');
			$this->load->view('footer', array("js_guia_salida" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->GuiaSalidaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
        	$btn_imprimir = '';
        	if ($row->No_Tipo_Documento_Breve === 'G/Remisión' && $this->empresa->No_Foto_Guia != '')
				$btn_imprimir = '<button class="btn btn-xs btn-link" alt="Imprimir" title="Imprimir" href="javascript:void(0)" onclick="imprimirVenta(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-print" aria-hidden="true"> Imprimir</i></button>';

            $no++;
            $rows = array();
            $rows[] = ToDateBD($row->Fe_Emision);
            $rows[] = $row->No_Tipo_Documento_Breve;
            $rows[] = $row->ID_Serie_Documento;
            $rows[] = $row->ID_Numero_Documento;
            $rows[] = $row->No_Tipo_Documento_Identidad_Breve;
            $rows[] = $row->No_Entidad;
            $rows[] = $row->No_Signo;
            $rows[] = numberFormat($row->Ss_Total, 2, '.', ',');
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
            $btn_send_sunat = '';
            if ( ($row->Nu_Estado == 6 || $row->Nu_Estado == 7 || $row->Nu_Estado == 9 || $row->Nu_Estado == 11) && $row->ID_Tipo_Documento != 2 && $this->empresa->Nu_Tipo_Proveedor_FE != 3 )//Action send SUNAT
            	$btn_send_sunat = '<button id="btn-sunat-' . $row->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="Enviar a Sunat" title="Enviar a Sunat" href="javascript:void(0)" onclick="sendFacturaVentaSunat(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Estado . '\', \'' . $sTipoBajaSunat . '\')"><i class="fa fa-cloud-upload"></i> Sunat</i></button>';
            $rows[] = $btn_send_sunat;
			$rows[] = $btn_imprimir;
			if ($row->Nu_Estado === '6') {
				if ($row->ID_Guia_Cabecera != '' && empty($row->ID_Documento_Cabecera)) {
					$Nu_Tipo_Operacion = 7;
					$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verGuiaSalida(\'' . $row->ID_Guia_Cabecera . '\', 7)"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
					$rows[] = '
					<button class="btn btn-xs btn-link" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularGuiaSalida(\'' . $row->ID_Guia_Cabecera . '\', \'' . $Nu_Tipo_Operacion . '\', \'' . $row->Nu_Descargar_Inventario . '\')"><i class="fa fa-minus-circle" aria-hidden="true"> Anular</i></button>
					<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarGuiaSalida(\'' . $row->ID_Guia_Cabecera . '\', \'' . $Nu_Tipo_Operacion . '\', \'' . $row->Nu_Descargar_Inventario . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>
					';
				} else {
					$Nu_Tipo_Operacion = 0;
					$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verGuiaSalida(\'' . $row->ID_Documento_Cabecera . '\', 0)"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
					$rows[] = '';
				}
			} else {
				$rows[] = '';
				$rows[] = '';
			}
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->GuiaSalidaModel->count_all(),
	        'recordsFiltered' => $this->GuiaSalidaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
	public function ajax_edit($ID, $Nu_Tipo_Operacion){
        $data = $this->GuiaSalidaModel->get_by_id($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Tipo_Operacion));
        $arrImpuesto = $this->HelperModel->getImpuestos();
        $output = array(
        	'arrEdit' => $data,
        	'arrImpuesto' => $arrImpuesto,
        );
        echo json_encode($output);
    }
    
	public function crudGuiaSalida(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		//ID_Tipo_Operacion es el tipo de documento

		$arrGuiaSalidaCabecera = array(
			'ID_Empresa'						=> $this->user->ID_Empresa,
			'ID_Almacen'						=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Almacen']),
			'ID_Entidad'						=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Entidad']),
			'Txt_Direccion_Llegada'				=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Txt_Direccion_Llegada']),
			'Txt_Referencia_Direccion_Llegada'	=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Txt_Referencia_Direccion_Llegada']),
			'ID_Tipo_Operacion'					=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Tipo_Operacion']),
			'ID_Tipo_Asiento_Factura'			=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Tipo_Asiento_Factura']),
			'ID_Tipo_Documento_Factura'			=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Tipo_Documento_Factura']),
			'ID_Serie_Documento_Factura'		=> $this->security->xss_clean(strtoupper($_POST['arrGuiaSalidaCabecera']['ID_Serie_Documento_Factura'])),
			'ID_Numero_Documento_Factura'		=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Numero_Documento_Factura']),
			'ID_Tipo_Asiento_Guia'				=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Tipo_Asiento_Guia']),
			'ID_Tipo_Documento_Guia'			=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Tipo_Documento_Guia']),
			'ID_Serie_Documento_Guia'			=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Serie_Documento_Guia']),
			'ID_Numero_Documento_Guia'			=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Numero_Documento_Guia']),
			'ID_Tipo_Movimiento'				=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Tipo_Movimiento']),
			'Fe_Emision'						=> ToDate($this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Fe_Emision'])),
			'ID_Moneda'							=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Moneda']),
			'Nu_Descargar_Inventario'			=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Nu_Descargar_Inventario']),
			'Txt_Glosa'							=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Txt_Glosa']),
			'Po_Descuento'						=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Po_Descuento']),
			'Ss_Descuento'						=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Ss_Descuento']),
			'Ss_Total'							=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['Ss_Total']),
			'ID_Lista_Precio_Cabecera'			=> $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['ID_Lista_Precio_Cabecera']),
			'Fe_Creacion'						=> dateNow('fecha_hora'),
			'Nu_Estado'							=> 6,
		);
		
		$arrFlete = array(
			'ID_Empresa'					=> $this->user->ID_Empresa,
			'ID_Entidad'					=> $this->security->xss_clean($_POST['arrFlete']['ID_Entidad']),
			'Fe_Traslado'					=> ToDate($this->security->xss_clean($_POST['arrFlete']['Fe_Traslado'])),
			'Nu_Tipo_Motivo_Traslado'		=> $this->security->xss_clean($_POST['arrFlete']['Nu_Tipo_Motivo_Traslado']),
			'No_Chofer' 					=> $this->security->xss_clean($_POST['arrFlete']['No_Chofer']),
			'No_Placa'						=> $this->security->xss_clean(strtoupper($_POST['arrFlete']['No_Placa'])),
			'Nu_Licencia'					=> $this->security->xss_clean($_POST['arrFlete']['Nu_Licencia']),
			'Txt_Certificado_Inscripcion'	=> $this->security->xss_clean($_POST['arrFlete']['Txt_Certificado_Inscripcion']),
		);
		
		print(json_encode(
			( $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Empresa']) != '' && ($this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Documento_Cabecera']) != '' || $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Guia_Cabecera']) != '') ) ?
				$this->actualizarGuiaSalida_Inventario(
					array('ID_Empresa' => $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Empresa']), 'ID_Guia_Cabecera' => $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Guia_Cabecera'])),
					array('ID_Empresa' => $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Empresa']), 'ID_Documento_Cabecera' => $this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Documento_Cabecera'])),
					$arrGuiaSalidaCabecera, $_POST['arrDetalleGuiaSalida'],
					$this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Tipo_Documento_Guia']),
					$this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Serie_Documento_Guia']),
					$this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Numero_Documento_Guia']),
					$this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Tipo_Documento_Factura']),
					$this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Serie_Documento_Factura']),
					$this->security->xss_clean($_POST['arrGuiaSalidaCabecera']['EID_Numero_Documento_Factura']),
					$arrGuiaSalidaCabecera['Nu_Descargar_Inventario'], $arrGuiaSalidaCabecera['ID_Tipo_Operacion'], $arrFlete)
			:
				$this->agregarGuiaSalida_Inventario($arrGuiaSalidaCabecera, $_POST['arrDetalleGuiaSalida'], $arrGuiaSalidaCabecera['Nu_Descargar_Inventario'], $arrGuiaSalidaCabecera['ID_Tipo_Operacion'], $arrFlete)
			)
		);
	}

	public function agregarGuiaSalida_Inventario($arrGuiaSalidaCabecera = '', $arrGuiaSalidaDetalle = '', $Nu_Descargar_Inventario = '', $ID_Tipo_Operacion = '', $arrFlete){
		$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		$responseGuiaSalida = $this->GuiaSalidaModel->agregarGuiaSalida($arrGuiaSalidaCabecera, $arrGuiaSalidaDetalle, $arrFlete);
		if ($responseGuiaSalida['status'] === 'success') {
			if ($Nu_Descargar_Inventario === '1'){//1 = Si
				$ID_Documento_Cabecera = 0;
				$ID_Guia_Cabecera = $responseGuiaSalida['Last_ID_Guia_Cabecera'];
				if ($ID_Tipo_Operacion === '0'){//Guia y Factura
					$ID_Documento_Cabecera = $responseGuiaSalida['Last_ID_Guia_Cabecera'];
					$ID_Guia_Cabecera = 0;
				}
				$response = $this->MovimientoInventarioModel->crudMovimientoInventario($arrGuiaSalidaCabecera['ID_Almacen'], $ID_Documento_Cabecera, $ID_Guia_Cabecera, $arrGuiaSalidaDetalle, $arrGuiaSalidaCabecera['ID_Tipo_Movimiento'], 0, '', 1, 1);
			}
		} else if ($responseGuiaSalida['status'] == 'error') {
    		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		} else if ($responseGuiaSalida['status'] == 'warning') {
    		$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}
		return $response;
	}

	public function actualizarGuiaSalida_Inventario($arrWhereGuiaSalida = '', $arrWhereFacturaCompra = '', $arrGuiaSalidaCabecera = '', $arrGuiaSalidaDetalle = '', $EID_Tipo_Documento_Guia, $EID_Serie_Documento_Guia, $EID_Numero_Documento_Guia, $EID_Tipo_Documento_Factura, $EID_Serie_Documento_Factura, $EID_Numero_Documento_Factura, $Nu_Descargar_Inventario = '', $ID_Tipo_Operacion = '', $arrFlete){
		$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		$responseGuiaSalida = $this->GuiaSalidaModel->actualizarGuiaSalida($arrWhereGuiaSalida, $arrWhereFacturaCompra, $arrGuiaSalidaCabecera, $arrGuiaSalidaDetalle, $EID_Tipo_Documento_Guia, $EID_Serie_Documento_Guia, $EID_Numero_Documento_Guia, $EID_Tipo_Documento_Factura, $EID_Serie_Documento_Factura, $EID_Numero_Documento_Factura, $arrFlete);
		if ($responseGuiaSalida['status'] === 'success') {
			if ($Nu_Descargar_Inventario === '1'){//1 = Si
				$ID_Documento_Cabecera = 0;
				$ID_Guia_Cabecera = $responseGuiaSalida['Last_ID_Guia_Cabecera'];
				if ($ID_Tipo_Operacion === '0'){//Guia y Factura
					$arrWhereGuiaSalida = $arrWhereFacturaCompra;
					$ID_Documento_Cabecera = $responseGuiaSalida['Last_ID_Guia_Cabecera'];
					$ID_Guia_Cabecera = 0;
				}
				$response = $this->MovimientoInventarioModel->crudMovimientoInventario($arrGuiaSalidaCabecera['ID_Almacen'], $ID_Documento_Cabecera, $ID_Guia_Cabecera, $arrGuiaSalidaDetalle, $arrGuiaSalidaCabecera['ID_Tipo_Movimiento'], 1, $arrWhereGuiaSalida, 1, 1);
			}
		} else if ($responseGuiaSalida['status'] == 'error') {
    		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al actualizar');
		} else if ($responseGuiaSalida['status'] == 'warning') {
    		$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}
		return $response;
	}
    
	public function anularGuiaSalida($ID, $Nu_Tipo_Operacion, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->GuiaSalidaModel->anularGuiaSalida($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Tipo_Operacion), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function eliminarGuiaSalida($ID, $Nu_Tipo_Operacion, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->GuiaSalidaModel->eliminarGuiaSalida($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Tipo_Operacion), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
}
