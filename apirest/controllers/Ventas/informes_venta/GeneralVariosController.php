<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GeneralVariosController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/informes_venta/GeneralVariosModel');
		$this->load->model('HelperModel');
	}

	public function reporte(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/informes_venta/GeneralVariosView');
			$this->load->view('footer', array("js_general_varios" => true));
		}
	}
	
	public function Ajax($action){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		switch($action){
			case 'SubReporte':
				/* SubReporte para el Reporte de Venta Diario */
				if($this->input->post('tipo') == 'reportediariodetalle'){
					$reporte = $this->GeneralVariosModel->ReporteDiarioDetalle($this->input->post('fecha'), $this->input->post('tipo_producto'), $this->input->post('iImpuesto'));
					echo $this->load->view('Ventas/sub_reportes/ReporteDiarioDetalleView', array(
						'reporte' => $reporte
					), true);
				}
				break;

			case 'Reporte':
				$reporte = null;
				$titulo  = '';
				
				/* Reporte de Venta Diario */
				if($this->input->post('tipo') == '1'){
					$reporte = $this->GeneralVariosModel->ReporteDiario($this->input->post('m'), $this->input->post('y'), $this->input->post('ID_Moneda'), $this->input->post('Nu_Tipo_Producto'), $this->input->post('iImpuesto'));
					$titulo = 'Reporte Diario';
				}
				
				/* Reporte de Venta Mensual */
				if($this->input->post('tipo') === '2'){
					$reporte = $this->GeneralVariosModel->ReporteMensual($this->input->post('y'), $this->input->post('ID_Moneda'), $this->input->post('Nu_Tipo_Producto'), $this->input->post('iImpuesto'));
					$titulo = 'Reporte Mensual';
				}
				
				/* Reporte de Venta Anual */
				if($this->input->post('tipo') == '3'){
 					$reporte = $this->GeneralVariosModel->ReporteAnual($this->input->post('ID_Moneda'), $this->input->post('Nu_Tipo_Producto'), $this->input->post('iImpuesto'));
 					$titulo = 'Reporte Anual';
				}
				
				/* Mejores Clientes */
				if($this->input->post('tipo') == '5'){
 					$reporte = $this->GeneralVariosModel->MejoresClientes($this->input->post('m'), $this->input->post('y'), $this->input->post('ID_Moneda'), $this->input->post('Nu_Tipo_Producto'), $this->input->post('iOrder'), $this->input->post('iImpuesto'));
 					$titulo = 'Top de Clientes';
				}
				
				/* Productos mas vendidos */
				if($this->input->post('tipo') == '4'){
 					$reporte = $this->GeneralVariosModel->ProductosMasVendidos($this->input->post('m'), $this->input->post('y'), $this->input->post('ID_Moneda'), $this->input->post('Nu_Tipo_Producto'), $this->input->post('iOrder'), $this->input->post('iImpuesto'));
 					$titulo = 'Top de Productos';
				}
				
				/* Analisis de Venta por Estacion del año */
				if($this->input->post('tipo') == '6'){
 					$reporte = $this->GeneralVariosModel->ProductosRentablesPorTrimestre($this->input->post('y'), $this->input->post('ID_Moneda'), $this->input->post('Nu_Tipo_Producto'), $this->input->post('iOrder'), $this->input->post('iImpuesto'));
 					$titulo = 'Rentabilidad de Producto Trimestral';
				}
				
				$ID_Moneda = 0;
				if(isset($_POST['ID_Moneda']))
					$ID_Moneda = $this->input->post('ID_Moneda');
					
				$Nu_Tipo_Producto = "";
				if(isset($_POST['Nu_Tipo_Producto']))
					$Nu_Tipo_Producto = $this->input->post('Nu_Tipo_Producto');

				$iOrder = 1;//1 = importe
				if(isset($_POST['iOrder']))
					$iOrder = $this->input->post('iOrder');
				
				$iImpuesto = 0;//1 = importe
				if(isset($_POST['iImpuesto']))
					$iImpuesto = $this->input->post('iImpuesto');

				echo $this->load->view('Ventas/informes_venta/_GeneralVariosView', array(
					'reporte'           => $reporte,
					'tipo'              => $this->input->post('tipo'),
					'm'                 => $this->input->post('m'),
					'y'                 => $this->input->post('y'),
					'titulo'            => $titulo,
					'arrMonedas'        => $this->HelperModel->getMonedas(),
					'ID_Moneda'         => $ID_Moneda,
					'Nu_Tipo_Producto'  => $Nu_Tipo_Producto,
					'iOrder'  => $iOrder,
					'iImpuesto'  => $iImpuesto,
				), true);
				
				break;
		}
	}
}
