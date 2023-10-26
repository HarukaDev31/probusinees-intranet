<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FacturaVentaLaeController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Ventas/FacturaVentaLaeModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/FacturaVentaLaeView');
			$this->load->view('footer', array("js_factura_venta_lae" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->FacturaVentaLaeModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
			$rows[] = ToDateBD($row->Fe_Emision);
			$rows[] = $row->No_Tipo_Documento_Breve;
			$rows[] = $row->ID_Serie_Documento;
			$rows[] = $row->ID_Numero_Documento;
			$rows[] = $row->Ss_Total;
			$rows[] = $row->Ss_Total_Saldo;
			$sEstadoPago = 'pendiente';
			$sEstadoPagoClass = 'warning';
			if ($row->Ss_Total_Saldo == 0.00) {
				$sEstadoPago = 'cancelado';
				$sEstadoPagoClass = 'success';
			}				
            $rows[] = '<span class="label label-' . $sEstadoPagoClass . '">' . $sEstadoPago . '</span>';
			$rows[] = $this->HelperModel->obtenerEstadoDocumento($row->Nu_Estado);
			$rows[] = (!empty($row->Txt_Url_PDF) ? '<a alt="Descargar PDF" title="Descargar PDF" href="' . $row->Txt_Url_PDF . '" target="_blank"><span class="label label-danger">PDF</span></a>' : '');
			$rows[] = (!empty($row->Txt_Url_XML) ? '<a alt="Descargar XML" title="Descargar XML" href="' . $row->Txt_Url_XML . '" target="_blank"><span class="label label-primary" style="border-radius: 50px;">XML</span></a>' : '');
			$rows[] = (!empty($row->Txt_Url_CDR) ? '<a alt="Descargar CDR" title="Descargar CDR" href="' . $row->Txt_Url_CDR . '" target="_blank"><span class="label label-dark">CDR</span></a>' : '');
			$data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->FacturaVentaLaeModel->count_all(),
	        'recordsFiltered' => $this->FacturaVentaLaeModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
}
