<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class GuiaEntradaController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Logistica/GuiaEntradaModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
	}

	public function listarGuiasEntrada(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/GuiaEntradaView');
			$this->load->view('footer', array("js_guia_entrada" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->GuiaEntradaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
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
            
			if ($row->Nu_Estado === '6') {
				if ($row->ID_Guia_Cabecera != '' && empty($row->ID_Documento_Cabecera)) {
					$Nu_Tipo_Operacion = 7;//Guia
					$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verGuiaEntrada(\'' . $row->ID_Guia_Cabecera . '\', 7)"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
					$rows[] = '
					<button class="btn btn-xs btn-link" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularGuiaEntrada(\'' . $row->ID_Guia_Cabecera . '\', \'' . $Nu_Tipo_Operacion . '\', \'' . $row->Nu_Descargar_Inventario . '\')"><i class="fa fa-minus-circle" aria-hidden="true"> Anular</i></button>
					<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarGuiaEntrada(\'' . $row->ID_Guia_Cabecera . '\', \'' . $Nu_Tipo_Operacion . '\', \'' . $row->Nu_Descargar_Inventario . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>
					';
				} else {
					$Nu_Tipo_Operacion = 0;//Guia y Factura
					$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verGuiaEntrada(\'' . $row->ID_Documento_Cabecera . '\', 0)"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
					$rows[] = '';
				}
			} else {
				$rows[] = '<label></label>';
				$rows[] = '<label></label>';
			}
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->GuiaEntradaModel->count_all(),
	        'recordsFiltered' => $this->GuiaEntradaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
	public function ajax_edit($ID, $Nu_Tipo_Operacion){
        $data = $this->GuiaEntradaModel->get_by_id($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Tipo_Operacion));
        $arrImpuesto = $this->HelperModel->getImpuestos();
        $output = array(
        	'arrEdit' => $data,
        	'arrImpuesto' => $arrImpuesto,
        );
        echo json_encode($output);
    }
    
	public function crudGuiaEntrada(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		//ID_Tipo_Operacion es el tipo de documento
		
		$arrGuiaEntradaCabecera = array(
			'ID_Empresa'					=> $this->user->ID_Empresa,
			'ID_Almacen'					=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Almacen']),
			'ID_Entidad'					=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Entidad']),
			'ID_Tipo_Operacion'				=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Tipo_Operacion']),
			'ID_Tipo_Asiento_Factura'		=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Tipo_Asiento_Factura']),
			'ID_Tipo_Documento_Factura'		=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Tipo_Documento_Factura']),
			'ID_Serie_Documento_Factura'	=> $this->security->xss_clean(strtoupper($_POST['arrGuiaEntradaCabecera']['ID_Serie_Documento_Factura'])),
			'ID_Numero_Documento_Factura'	=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Numero_Documento_Factura']),
			'ID_Tipo_Asiento_Guia'			=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Tipo_Asiento_Guia']),
			'ID_Tipo_Documento_Guia'		=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Tipo_Documento_Guia']),
			'ID_Serie_Documento_Guia'		=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Serie_Documento_Guia']),
			'ID_Numero_Documento_Guia'		=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Numero_Documento_Guia']),
			'ID_Tipo_Movimiento'			=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Tipo_Movimiento']),
			'Fe_Emision'					=> ToDate($this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['Fe_Emision'])),
			'ID_Moneda'						=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Moneda']),
			'Nu_Descargar_Inventario'		=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['Nu_Descargar_Inventario']),
			'Txt_Glosa'						=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['Txt_Glosa']),
			'Po_Descuento'					=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['Po_Descuento']),
			'Ss_Descuento'					=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['Ss_Descuento']),
			'Ss_Total'						=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['Ss_Total']),
			'ID_Lista_Precio_Cabecera'		=> $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['ID_Lista_Precio_Cabecera']),
			'Fe_Creacion'					=> dateNow('fecha_hora'),
			'Nu_Estado'						=> 6,
		);
		
		print(json_encode(
			( $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Empresa']) != '' && ($this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Documento_Cabecera']) != '' || $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Guia_Cabecera']) != '') ) ?
				$this->actualizarGuiaEntrada_Inventario(
					array('ID_Empresa' => $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Empresa']), 'ID_Guia_Cabecera' => $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Guia_Cabecera'])),
					array('ID_Empresa' => $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Empresa']), 'ID_Documento_Cabecera' => $this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Documento_Cabecera'])),
					$arrGuiaEntradaCabecera, $_POST['arrDetalleGuiaEntrada'],
					$this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Tipo_Documento_Guia']),
					$this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Serie_Documento_Guia']),
					$this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Numero_Documento_Guia']),
					$this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Tipo_Documento_Factura']),
					$this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Serie_Documento_Factura']),
					$this->security->xss_clean($_POST['arrGuiaEntradaCabecera']['EID_Numero_Documento_Factura']),
					$arrGuiaEntradaCabecera['Nu_Descargar_Inventario'], $arrGuiaEntradaCabecera['ID_Tipo_Operacion'])
			:
				$this->agregarGuiaEntrada_Inventario($arrGuiaEntradaCabecera, $_POST['arrDetalleGuiaEntrada'], $arrGuiaEntradaCabecera['Nu_Descargar_Inventario'], $arrGuiaEntradaCabecera['ID_Tipo_Operacion'])
			)
		);
	}

	public function agregarGuiaEntrada_Inventario($arrGuiaEntradaCabecera = '', $arrGuiaEntradaDetalle = '', $Nu_Descargar_Inventario = '', $ID_Tipo_Operacion = ''){
		$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		$responseGuiaEntrada = $this->GuiaEntradaModel->agregarGuiaEntrada($arrGuiaEntradaCabecera, $arrGuiaEntradaDetalle);
		if ($responseGuiaEntrada['status'] === 'success') {
			if ($Nu_Descargar_Inventario === '1'){//1 = Si
				$ID_Documento_Cabecera = 0;
				$ID_Guia_Cabecera = $responseGuiaEntrada['Last_ID_Guia_Cabecera'];
				if ($ID_Tipo_Operacion === '0'){//Guia y Factura
					$ID_Documento_Cabecera = $responseGuiaEntrada['Last_ID_Guia_Cabecera'];
					$ID_Guia_Cabecera = 0;
				}
				$response = $this->MovimientoInventarioModel->crudMovimientoInventario($arrGuiaEntradaCabecera['ID_Almacen'], $ID_Documento_Cabecera, $ID_Guia_Cabecera, $arrGuiaEntradaDetalle, $arrGuiaEntradaCabecera['ID_Tipo_Movimiento'], 0, '', 0, 1);
			}
		} else if ($responseGuiaEntrada['status'] == 'error') {
    		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		} else if ($responseGuiaEntrada['status'] == 'warning') {
    		$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}
		return $response;
	}

	public function actualizarGuiaEntrada_Inventario($arrWhereGuiaEntrada = '', $arrWhereFacturaCompra = '', $arrGuiaEntradaCabecera = '', $arrGuiaEntradaDetalle = '', $EID_Tipo_Documento_Guia, $EID_Serie_Documento_Guia, $EID_Numero_Documento_Guia, $EID_Tipo_Documento_Factura, $EID_Serie_Documento_Factura, $EID_Numero_Documento_Factura, $Nu_Descargar_Inventario = '', $ID_Tipo_Operacion = ''){
		$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		$responseGuiaEntrada = $this->GuiaEntradaModel->actualizarGuiaEntrada($arrWhereGuiaEntrada, $arrWhereFacturaCompra, $arrGuiaEntradaCabecera, $arrGuiaEntradaDetalle, $EID_Tipo_Documento_Guia, $EID_Serie_Documento_Guia, $EID_Numero_Documento_Guia, $EID_Tipo_Documento_Factura, $EID_Serie_Documento_Factura, $EID_Numero_Documento_Factura);
		if ($responseGuiaEntrada['status'] === 'success') {
			if ($Nu_Descargar_Inventario === '1'){//1 = Si
				$ID_Documento_Cabecera = 0;
				$ID_Guia_Cabecera = $responseGuiaEntrada['Last_ID_Guia_Cabecera'];
				if ($ID_Tipo_Operacion === '0'){//Guia y Factura
					$arrWhereGuiaEntrada = $arrWhereFacturaCompra;
					$ID_Documento_Cabecera = $responseGuiaEntrada['Last_ID_Guia_Cabecera'];
					$ID_Guia_Cabecera = 0;
				}
				$response = $this->MovimientoInventarioModel->crudMovimientoInventario($arrGuiaEntradaCabecera['ID_Almacen'], $ID_Documento_Cabecera, $ID_Guia_Cabecera, $arrGuiaEntradaDetalle, $arrGuiaEntradaCabecera['ID_Tipo_Movimiento'], 1, $arrWhereGuiaEntrada, 0, 1);
			}
		} else if ($responseGuiaEntrada['status'] == 'error') {
    		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al actualizar');
		} else if ($responseGuiaEntrada['status'] == 'warning') {
    		$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}
		return $response;
	}
    
	public function anularGuiaEntrada($ID, $Nu_Tipo_Operacion, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->GuiaEntradaModel->anularGuiaEntrada($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Tipo_Operacion), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function eliminarGuiaEntrada($ID, $Nu_Tipo_Operacion, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->GuiaEntradaModel->eliminarGuiaEntrada($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Tipo_Operacion), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
}
