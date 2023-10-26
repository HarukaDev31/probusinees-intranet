<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class OrdenCompraController extends CI_Controller {
	private $file_path = '../assets/images/logos/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Logistica/OrdenCompraModel');
	}

	public function listarOrdenesCompra(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/OrdenCompraView');
			$this->load->view('footer', array("js_orden_compra" => true));
		}
	}
	
	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->OrdenCompraModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
		$btn_estados = '';
		$btn_modificar = '';
		$btn_facturar = '';
		$btn_eliminar = '';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = ToDateBD($row->Fe_Emision);
            $rows[] = $row->ID_Serie_Documento;
            $rows[] = $row->ID_Numero_Documento;
            $rows[] = $row->No_Entidad;
            $rows[] = $row->No_Contacto;
            $rows[] = $row->No_Signo;
            $rows[] = numberFormat($row->Ss_Total, 2, '.', ',');
			$btn_estados = '';
			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 1) {
				$btn_estados = '<div class="dropdown">
					<button style="width: 100%;" class="btn btn-' . $row->No_Class_Estado . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $row->No_Descripcion_Estado . '
					<span class="caret"></span></button>
					<ul class="dropdown-menu"  style="width: 100%; position: sticky;">
						<li><a alt="Entregado" title="Modificar" href="javascript:void(0)" onclick="estadoOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 0);">Entregado</a></li>
						<li><a alt="Revisado" title="Revisado" href="javascript:void(0)" onclick="estadoOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 1);">Revisado</a></li>
						<li><a alt="Aceptado" title="Aceptado" href="javascript:void(0)" onclick="estadoOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 2);">Aceptado</a></li>
						<li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="estadoOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\', 3);">Rechazado</a></li>
					</ul>
				</div>';
			}
            $rows[] = $btn_estados;

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
					foreach ($arrResponseDocument['arrData'] as $rowEnlace)
						$span_enlace_documentos .= '<span class="label label-dark">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>";
				}
			}

			$rows[] = $span_enlace_documentos;

            $rows[] = '<button class="btn btn-xs btn-link" alt="PDF" title="PDF" href="javascript:void(0)" onclick="pdfOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-2x fa-file-pdf-o color_icon_pdf"></i></button>';
            $btn_facturar = '';
            if ($row->Nu_Estado != 3) {
				if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 1)
            		$btn_facturar = '<button class="btn btn-xs btn-link" alt="Facturar" title="Facturar" href="javascript:void(0)" onclick="facturarOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-2x fa-book" aria-hidden="true"></i></button>';
			}
			if ($row->Nu_Estado == 5 || $row->Nu_Estado == 0 || $row->Nu_Estado == 1) {
				$rows[] = '<button class="btn btn-xs btn-link" alt="Duplicar" title="Duplicar" href="javascript:void(0)" onclick="duplicarOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa  fa-2x fa-clone" aria-hidden="true"></i></button>';
				if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 0)
					$btn_facturar = '';
				$rows[] = $btn_facturar;
				$btn_modificar = '';
				if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 1)
					$btn_modificar = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';
				$rows[] = $btn_modificar;
				$btn_eliminar = '';
				if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Eliminar == 1)
					$btn_eliminar = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarOrdenCompra(\'' . $row->ID_Documento_Cabecera . '\', \'' . $row->Nu_Descargar_Inventario . '\')"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></button>';
				$rows[] = $btn_eliminar;
			} else {
				$rows[] = '';
				$rows[] = $btn_facturar;
				$rows[] = '';
				$rows[] = '';
			}
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->OrdenCompraModel->count_all(),
	        'recordsFiltered' => $this->OrdenCompraModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
	public function ajax_edit($ID){
        $data = $this->OrdenCompraModel->get_by_id($this->security->xss_clean($ID));
        $arrImpuesto = $this->HelperModel->getImpuestos($arrPost = '');
        $output = array(
        	'arrEdit' => $data,
        	'arrImpuesto' => $arrImpuesto,
        );
        echo json_encode($output);
    }
    
	public function crudOrdenCompra(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');

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
		
		$iDescargarStock = $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Nu_Descargar_Inventario']);

		$arrOrdenCompraCabecera = array(
			'ID_Empresa'					=> $this->empresa->ID_Empresa,
			'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
			'ID_Tipo_Asiento'				=> 2,//Compra
			'ID_Tipo_Documento'				=> 12,//Proforma Compra
			'ID_Serie_Documento'			=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Serie_Documento']),
			'ID_Numero_Documento'			=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Numero_Documento']),
			'Nu_Correlativo'				=> 0,
			'Fe_Emision'					=> ToDate($this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Fe_Emision'])),
			'Fe_Vencimiento'				=> ToDate($this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Fe_Vencimiento'])),
			'Fe_Periodo'					=> ToDate($this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Fe_Entrega'])),
			'ID_Moneda'						=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Moneda']),
			'ID_Medio_Pago'					=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Medio_Pago']),
			'Nu_Descargar_Inventario'		=> $iDescargarStock,
			'ID_Entidad'					=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Entidad']),
			'ID_Contacto'					=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Contacto']),
			'Txt_Garantia'					=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Txt_Garantia']),
			'Txt_Glosa'						=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Txt_Glosa']),
			'Po_Descuento'					=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Po_Descuento']),
			'Ss_Descuento'					=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Ss_Descuento']),
			'Ss_Total'						=> $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['Ss_Total']),
			'Nu_Estado'						=> ($this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ENu_Estado']) != '' ? $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ENu_Estado']) : 5),
		);

		if ( $_POST['arrOrdenCompraCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
			$arrOrdenCompraCabecera = array_merge($arrOrdenCompraCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Lista_Precio_Cabecera'])));

		if ( !empty($_POST['arrOrdenCompraCabecera']['ID_Almacen']) ){
			$arrOrdenCompraCabecera = array_merge($arrOrdenCompraCabecera, array("ID_Almacen" => $_POST['arrOrdenCompraCabecera']['ID_Almacen']));
		}
			
		//if ( $iDescargarStock == 1 ) {
			$arrOrdenCompraCabecera = array_merge($arrOrdenCompraCabecera, array("ID_Almacen" => $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['ID_Almacen'])));
		//}

		echo json_encode(
		( $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['EID_Empresa']) != '' && $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['EID_Documento_Cabecera']) != '') ?
			$this->actualizarCompra_Inventario(array('ID_Empresa' => $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['EID_Empresa']), 'ID_Documento_Cabecera' => $this->security->xss_clean($_POST['arrOrdenCompraCabecera']['EID_Documento_Cabecera'])), $arrOrdenCompraCabecera, $_POST['arrDetalleOrdenCompra'], $arrOrdenCompraCabecera['Nu_Descargar_Inventario'], $arrProveedorNuevo, $arrContactoNuevo)
		:
			$this->agregarCompra_Inventario($arrOrdenCompraCabecera, $_POST['arrDetalleOrdenCompra'], $arrOrdenCompraCabecera['Nu_Descargar_Inventario'], $arrProveedorNuevo, $arrContactoNuevo)
		);
	}

	public function agregarCompra_Inventario($arrOrdenCompraCabecera = '', $arrDetalleOrdenCompra = '', $Nu_Descargar_Inventario = '', $arrProveedorNuevo = '', $arrContactoNuevo = ''){
		return $this->OrdenCompraModel->agregarCompra($arrOrdenCompraCabecera, $arrDetalleOrdenCompra, $arrProveedorNuevo, $arrContactoNuevo);
	}

	public function actualizarCompra_Inventario($arrWhereCompra = '', $arrOrdenCompraCabecera = '', $arrDetalleOrdenCompra = '', $Nu_Descargar_Inventario = '', $arrProveedorNuevo = '', $arrContactoNuevo){
		return $this->OrdenCompraModel->actualizarCompra($arrWhereCompra, $arrOrdenCompraCabecera, $arrDetalleOrdenCompra, $arrProveedorNuevo, $arrContactoNuevo);
	}
	
	public function eliminarOrdenCompra($ID, $Nu_Descargar_Inventario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrdenCompraModel->eliminarOrdenCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Descargar_Inventario)));
	}
	
	public function estadoOrdenCompra($ID, $Nu_Descargar_Inventario, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrdenCompraModel->estadoOrdenCompra($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Descargar_Inventario), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function duplicarOrdenCompra($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrdenCompraModel->duplicarOrdenCompra($this->security->xss_clean($ID)));
	}

	public function getOrdenCompraPDF($ID){
        $data = $this->OrdenCompraModel->get_by_id($this->security->xss_clean($ID));
		$this->load->library('Pdf');
		
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		
		ob_start();
		$file = $this->load->view('Logistica/pdf/orden_compra_view', array(
			'arrData' => $data,
		));
		$html = ob_get_contents();
		ob_end_clean();
		
		$pdf->SetAuthor('laesystems');
		$pdf->SetTitle('laesystems - orden compra ' . $data[0]->ID_Numero_Documento);
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
		$pdf->AddPage();
		
		$sNombreLogo=str_replace(' ', '_', $this->empresa->No_Logo_Empresa);
		if ( !file_exists($this->file_path . $sNombreLogo) ) {
			$sNombreLogo='lae_logo_cotizacion.png';
		}
		$format_header = '<table border="0">';
			$format_header .= '<tr>';
				$format_header .= '<td rowspan="3" style="width: 20%; text-align: left;">';
					$format_header .= '<img style="height: 80px; width: ' . $this->empresa->Nu_Width_Logo_Ticket . 'px;" src="' . $this->file_path . $sNombreLogo . '"><br>';
				$format_header .= '</td>';
				$format_header .= '<td style="width: 80%; text-align: left;">';
					$format_header .= '<p>';
						$format_header .= '<br>';
						if(!empty($this->empresa->No_Dominio_Empresa))
							$format_header .= '<label style="color: #000000; font-size: 12px; font-family: "Times New Roman", Times, serif;">' . $this->empresa->No_Dominio_Empresa . '</label><br>';
						if(!empty($this->empresa->Nu_Celular_Empresa))
							$format_header .= '<label style="color: #868686; font-size: 12px; font-family: "Times New Roman", Times, serif;">' . $this->empresa->Nu_Celular_Empresa . '</label><br>';
						if(!empty($this->empresa->Txt_Email_Empresa))
							$format_header .= '<label style="color: #34bdad; font-size: 12px; font-family: "Times New Roman", Times, serif;">' . $this->empresa->Txt_Email_Empresa . '</label><br>';
						if(!empty($this->empresa->Txt_Slogan_Empresa))
							$format_header .= '<label style="color: #979797; font-size: 12px; font-style: italic; font-family: "Times New Roman", Times, serif;">' . $this->empresa->Txt_Slogan_Empresa . '</label>';
					$format_header .= '</p>';
				$format_header .= '</td>';
			$format_header .= '</tr>';
		$format_header .= '</table>';
		
		$pdf->writeHTML($format_header, true, 0, true, 0);
		
        $pdf->setFont('helvetica', '', 7);
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$file_name = "laesystems_orden_compra_ Nro. " . $data[0]->ID_Numero_Documento . ".pdf";
		$pdf->Output($file_name, 'I');
	}
}