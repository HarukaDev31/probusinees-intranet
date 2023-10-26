<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 400); //300 seconds = 5 minutes y mas
date_default_timezone_set('America/Lima');

class AjusteInventarioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/AjusteInventarioModel');
	}

	public function importarExcelAjusteInventario(){
		if (isset($_FILES['excel-archivo-ajuste_inventario']['name']) && isset($_FILES['excel-archivo-ajuste_inventario']['type']) && isset($_FILES['excel-archivo-ajuste_inventario']['tmp_name'])) {
		    $archivo	= $_FILES['excel-archivo-ajuste_inventario']['name'];
		    $tipo		= $_FILES['excel-archivo-ajuste_inventario']['type'];
		    $destino	= "bak_" . $archivo;
		    
		    if (copy($_FILES['excel-archivo-ajuste_inventario']['tmp_name'], $destino)) {
		        if (file_exists($destino)) {
					$this->load->library('Excel');
		    		$objReader = new PHPExcel_Reader_Excel2007();
		    		$objPHPExcel = $objReader->load($destino);
		            $objPHPExcel->setActiveSheetIndex(0);
		            
		            $iCantidadRegistros = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
		            
		            $column = array(
		                'CODIGO_BARRA'	=> 'A',
		                'STOCK_FISICO'	=> 'B',
		            );
	                
	                $arrAjusteInventarioItem = array();
                	for ($i = 2; $i <= $iCantidadRegistros; $i++) {
	                	$Nu_Codigo_Barra = $objPHPExcel->getActiveSheet()->getCell($column['CODIGO_BARRA'] . $i)->getCalculatedValue();
	                	$Nu_Codigo_Barra = quitarCaracteresEspeciales(strtoupper(filter_var(trim($Nu_Codigo_Barra))));

	                	$fStockFisicoProducto = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['STOCK_FISICO'] . $i)->getCalculatedValue()));
						$fStockFisicoProducto = quitarCaracteresEspeciales($fStockFisicoProducto);
                        
						$arrAjusteInventarioItem[] = array(
							'Nu_Codigo_Barra'	=> $Nu_Codigo_Barra,
							'Qt_Producto'		=> $fStockFisicoProducto,
						);
					}// ./ for arr excel
					
					if(isset($this->session->userdata['usuario'])) {
						$this->load->view('header');
						$this->load->view('Logistica/AjusteInventarioViewExcel', array('sStatusExcel' => 0, 'arrDataExcel' => $arrAjusteInventarioItem));
						$this->load->view('footer', array("js_ajuste_inventario" => true));
					}
		        } else {
					unlink($destino);

        	        $sStatusExcel = 'error-archivo_no_existe';
					redirect('Logistica/AjusteInventarioController/listar/' . $sStatusExcel);
		        }
		    } else {
				unlink($destino);

		        $sStatusExcel = 'error-copiar_archivo';
				redirect('Logistica/AjusteInventarioController/listar/' . $sStatusExcel);
		    }
		}
	}

	public function listar($sStatusExcel=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/AjusteInventarioView', array('sStatusExcel' => $sStatusExcel));
			$this->load->view('footer', array("js_ajuste_inventario" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->AjusteInventarioModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
            $rows[] = $row->Nu_Cantidad;
			$rows[] = '<button class="btn btn-xs btn-link" alt="Ver Ajuste Inventario" title="Ver Ajuste Inventario" href="javascript:void(0)" onclick="verAjusteInventario(\'' . $row->ID_Documento_Cabecera . '\')"><i class="fa fa-2x fa-list" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->AjusteInventarioModel->count_all(),
	        'recordsFiltered' => $this->AjusteInventarioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function verAjusteProcesado($ID){
        echo json_encode($this->AjusteInventarioModel->verAjusteProcesado($this->security->xss_clean($ID)));
	}
	
	public function getItemsAjusteInvetario(){
		echo json_encode($this->AjusteInventarioModel->getItemsAjusteInvetario($this->input->post()));
	}
	
	public function guardarAjusteInventario(){//Recordar que si realizo un cambio para excel o formulario se debe de cambiar la funcion model procesarAjusteInventario
		echo json_encode($this->AjusteInventarioModel->procesarAjusteInventario($this->input->post()));
	}
	
	public function guardarAjusteInventarioExcel(){//Recordar que si realizo un cambio para excel o formulario se debe de cambiar la funcion model procesarAjusteInventario
		echo json_encode($this->AjusteInventarioModel->procesarAjusteInventario($this->input->post()));
	}
}
