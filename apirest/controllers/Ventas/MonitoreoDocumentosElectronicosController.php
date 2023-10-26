<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MonitoreoDocumentosElectronicosController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/MonitoreoDocumentosElectronicosModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/MonitoreoDocumentosElectronicosView');
			$this->load->view('footer', array("js_monitoreo_documentos_electronicos" => true));
		}
	}
	
	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->MonitoreoDocumentosElectronicosModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        foreach ($arrData as $row) {
            $no++;
			$rows = array();

			$rows[] = '<span class="label label-' . ($row->Nu_Estado_Sistema == 1 ? 'success' : 'danger') . '">' . ($row->Nu_Estado_Sistema == 1 ? 'Producción' : 'Demostración') . '</span>';
			$rows[] = $row->No_Empresa;
			$rows[] = $row->No_Organizacion;
			$rows[] = ToDateBD($row->Fe_Emision);
			$rows[] = $row->No_Tipo_Documento_Breve;
			$rows[] = $row->ID_Serie_Documento;
			$rows[] = $row->ID_Numero_Documento;
			
			$arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
			$rows[] = '<span class="label label-' . $arrEstadoDocumento['No_Class_Estado'] . '">' . $arrEstadoDocumento['No_Estado'] . '</span>';
			
			$dActual=date_create(dateNow('fecha'));
			$dEmision=date_create($row->Fe_Emision);
			$iDiferenciaDias=date_diff($dActual,$dEmision);
			$sDiasXVencer = (6 - (int)$iDiferenciaDias->format("%a"));
			$rows[] = $sDiasXVencer . ' días <br>' . ($sDiasXVencer <= 0 ? '<span class="label label-danger">Comprobante Rechazado (Deben de emitir otro documento, este ya no tiene validez)</span>' : '');
			
			$sMensaje = '';
			if ( !empty($row->Txt_Respuesta_Sunat_FE) ){
				$objMensaje = json_decode($row->Txt_Respuesta_Sunat_FE);
				$sMensaje =
				'<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Ver Mensaje <span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<li><a href="#">Proveedor: ' . ($objMensaje->Proveedor == 'Nubefact' ? 'PSE' : $objMensaje->Proveedor) . '</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">Enviada a SUNAT: ' . $objMensaje->Enviada_SUNAT . '</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">Aceptada en SUNAT: ' . $objMensaje->Aceptada_SUNAT . '</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">Código en SUNAT: ' . $objMensaje->Codigo_SUNAT . '</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">Mensaje de SUNAT: ' . $objMensaje->Mensaje_SUNAT . '</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">Fecha Registro: ' . $objMensaje->Fecha_Registro . '</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">Fecha Envío: ' . $objMensaje->Fecha_Envio . '</a></li>
					</ul>
				</div>';
			}
			$rows[] = $sMensaje;

			$btn_modificar = '<button type="button" class="btn btn-xs btn-link" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="modificarVenta(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-pencil fa-2x" aria-hidden="true"></i></button>';
			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Editar == 0)
				$btn_modificar='';
			$rows[] = $btn_modificar;

			$btn_anular = '<button type="button" class="btn btn-xs btn-link" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" alt="Anular" title="Anular" href="javascript:void(0)" onclick="anularVenta(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-minus-circle fa-2x" aria-hidden="true"></i></button>';
			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Eliminar == 0)
				$btn_anular='';
			$rows[] = $btn_anular;

			$data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => 0,//$this->MonitoreoDocumentosElectronicosModel->count_all(),
	        'recordsFiltered' => $this->MonitoreoDocumentosElectronicosModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function obtenerVenta(){
		echo json_encode($this->MonitoreoDocumentosElectronicosModel->obtenerVenta($this->input->post()));
	}
	
	public function actualizarVenta(){
		echo json_encode($this->MonitoreoDocumentosElectronicosModel->actualizarVenta($this->input->post()));
	}
	
	public function anularVenta($id){
		echo json_encode($this->MonitoreoDocumentosElectronicosModel->anularVenta($this->security->xss_clean($id)));
	}
	
	public function getOrganizacionEmpresa(){
		echo json_encode($this->MonitoreoDocumentosElectronicosModel->getOrganizacionEmpresa($this->input->post()));
	}
	
	public function getAlmacenesEmpresa(){
		echo json_encode($this->MonitoreoDocumentosElectronicosModel->getAlmacenesEmpresa($this->input->post()));
	}
}
