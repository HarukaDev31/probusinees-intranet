<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CuponDescuentoTiendaVirtualController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('TiendaVirtual/Configuracion/CuponDescuentoModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('TiendaVirtual/Configuracion/CuponDescuentoView');
			$this->load->view('footer', array("js_cupon_descuento" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->CuponDescuentoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Codigo_Cupon_Descuento;
            $rows[] = $row->Txt_Cupon_Descuento;
			$rows[] = ($row->Nu_Tipo_Cupon_Descuento == 1 ? 'Descuento x Importe' : 'Descuento x Porcentaje');
			$rows[] = numberFormat($row->Ss_Valor_Cupon_Descuento, 2, '.', ',');
            $rows[] = ToDateBD($row->Fe_Inicio);
            $rows[] = ToDateBD($row->Fe_Vencimiento);
            $rows[] = $row->Nu_Total_Uso_Cupon;
			$rows[] = ($row->Fe_Vencimiento < dateNow('fecha') ? '<span class="label label-danger">Vencido</span>' : '<span class="label label-success">Activo</span>');
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCuponDescuento(\'' . $row->ID_Cupon_Descuento . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->CuponDescuentoModel->count_all(),
	        'recordsFiltered' => $this->CuponDescuentoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->CuponDescuentoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCuponDescuento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		//validar que valor sea mayor a cero
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Codigo_Cupon_Descuento' => strtoupper($this->input->post('No_Codigo_Cupon_Descuento')),
			'Txt_Cupon_Descuento' => $this->input->post('Txt_Cupon_Descuento'),
			'Nu_Tipo_Cupon_Descuento' => $this->input->post('Nu_Tipo_Cupon_Descuento'),
			'Ss_Valor_Cupon_Descuento' => $this->input->post('Ss_Valor_Cupon_Descuento'),
			'Ss_Gasto_Minimo_Compra' => $this->input->post('Ss_Gasto_Minimo_Compra'),
			'Fe_Inicio' => ToDate($this->input->post('Fe_Inicio')),
			'Fe_Vencimiento' => ToDate($this->input->post('Fe_Vencimiento')),
		);
		echo json_encode(
		($this->input->post('EID_Cupon_Descuento') != '') ?
			$this->CuponDescuentoModel->actualizarCuponDescuento(array('ID_Cupon_Descuento' => $this->input->post('EID_Cupon_Descuento')), $data, $this->input->post('ENo_Codigo_Cupon_Descuento'))
		:
			$this->CuponDescuentoModel->agregarCuponDescuento($data)
		);
	}
}
