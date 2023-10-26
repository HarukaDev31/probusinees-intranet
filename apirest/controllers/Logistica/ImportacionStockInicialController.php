<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 400); //300 seconds = 5 minutes y mas
date_default_timezone_set('America/Lima');

class ImportacionStockInicialController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('Logistica/ImportacionStockInicialModel');
		$this->load->model('Logistica/MovimientoInventarioModel');
	}
	
	public function importarExcelStockInicialProductos(){
		if (isset($_FILES['excel-archivo_stock_inicial_productos']['name']) && isset($_FILES['excel-archivo_stock_inicial_productos']['type']) && isset($_FILES['excel-archivo_stock_inicial_productos']['tmp_name'])) {
		    $archivo = $_FILES['excel-archivo_stock_inicial_productos']['name'];
		    $tipo = $_FILES['excel-archivo_stock_inicial_productos']['type'];
		    $destino = "bak_" . $archivo;
		    
		    if (copy($_FILES['excel-archivo_stock_inicial_productos']['tmp_name'], $destino)) {
		        if (file_exists($destino)) {
					$this->load->library('Excel');
		    		$objReader = new PHPExcel_Reader_Excel2007();
		    		$objPHPExcel = $objReader->load($destino);
		            $objPHPExcel->setActiveSheetIndex(0);
		            
		            $iCantidadRegistros = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
		            
		            $column = array(
		                'CODIGO_BARRA_PRODUCTO' => 'A',
		                'NOMBRE_PRODUCTO' => 'B',
		                'STOCK_INICIAL_FISICO_PRODUCTO' => 'C'
		            );
		            
	                $arrStockInicialProductos = array();
	                $iCantidadNoProcesados = 0;
                	for ($i = 2; $i <= $iCantidadRegistros; $i++) {						
	                	$sCodigoBarraProducto = $objPHPExcel->getActiveSheet()->getCell($column['CODIGO_BARRA_PRODUCTO'] . $i)->getCalculatedValue();
	                	$sCodigoBarraProducto = quitarCaracteresEspeciales(strtoupper(filter_var(trim($sCodigoBarraProducto))));
	                	$fStockInicialFisicoProducto = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['STOCK_INICIAL_FISICO_PRODUCTO'] . $i)->getCalculatedValue()));
						$fStockInicialFisicoProducto = quitarCaracteresEspeciales($fStockInicialFisicoProducto);
						
	                	$sNombreImpuesto = 'Gravado - Operación Onerosa';//NO IMPORTA
	                	$fPrecioCompra = 0;//NO IMPORTA
	                	$dVencimiento = '';
	                	$sNumeroLote = '';

	                	if (!empty($sCodigoBarraProducto) && $fStockInicialFisicoProducto!='') {
		                	$arrStockInicialProductos[] = array(
								'Nu_Codigo_Barra' => $sCodigoBarraProducto,
								'No_Impuesto' => $sNombreImpuesto,
								'Ss_Precio' => $fPrecioCompra,
								'Qt_Producto' => $fStockInicialFisicoProducto,
								'dVencimiento' => $dVencimiento,
								'sNumeroLote' => $sNumeroLote,
		                	);
	                	} else {
                        	$iCantidadNoProcesados++;
                        }
                	}// /. for arrExcel
                	
					unlink($destino);
                	$bResponse=false;
                	if ( count($arrStockInicialProductos) > 0 ) {
						$this->ImportacionStockInicialModel->setBatchImport($arrStockInicialProductos);
						$arrResponse = $this->ImportacionStockInicialModel->importData();
						//$arrStockInicialCabecera = $arrResponse['arrStockInicialCabecera'];
						
						unset($arrStockInicialProductos);
						if ($arrResponse['status'] == 'success') {
							redirect('Logistica/ImportacionStockInicialController/listar/' . $arrResponse['status'] . '/' . 0 .  '/' . $arrResponse['message']);
							unset($arrResponseMovimientoInventario);
							exit();

							/*
							$arrStockInicialCabecera['ID_Tipo_Movimiento'] = 7;//Saldo Inicial
							
							//$arrResponseMovimientoInventario['status'] = 'success';
							//$arrResponseMovimientoInventario['message'] = 'cargado';

							
							$arrResponseMovimientoInventario = $this->MovimientoInventarioModel->crudMovimientoInventario(
								$arrStockInicialCabecera['ID_Almacen'],
								$arrResponse['Last_ID_Documento_Cabecera'],
								0,
								$arrResponse['arrStockInicialDetalle'],
								$arrStockInicialCabecera['ID_Tipo_Movimiento'],
								0,
								'',
								0,
								1
							);


							if ($arrResponseMovimientoInventario['status'] == 'success') {
								redirect('Logistica/ImportacionStockInicialController/listar/' . $arrResponseMovimientoInventario['status'] . '/' . $iCantidadNoProcesados .  '/' . $arrResponseMovimientoInventario['message']);
								unset($arrResponseMovimientoInventario);
								exit();
							} else {
								unset($arrResponse);
								$sStatus = 'error-bd';
								redirect('Logistica/ImportacionStockInicialController/listar/' . $sStatus . '/' . 0 . '/' . $arrResponseMovimientoInventario['message']);
								unset($arrResponseMovimientoInventario);
								exit();
							}
							*/
						} else {
							$sStatus = 'error-bd';
							redirect('Logistica/ImportacionStockInicialController/listar/' . $sStatus . '/' . 0 . '/' . $arrResponse['message']);
							unset($arrResponse);
							exit();
						}
                	} else {
	                	unset($arrStockInicialProductos);
                	
                		$sStatus = 'error-sindatos';
						redirect('Logistica/ImportacionStockInicialController/listar/' . $sStatus . '/' . 0 . '/Sin datos');
                		exit();
                	}
		        } else {
        	        $sStatus = 'error-archivo_no_existe';
					redirect('Logistica/ImportacionStockInicialController/listar/' . $sStatus . '/' . 0 . '/Archivo no existe');
					exit();
		        }
		    } else {
		        $sStatus = 'error-copiar_archivo';
				redirect('Logistica/ImportacionStockInicialController/listar/' . $sStatus . '/' . 0 . '/Error al copiar archivo');
				exit();
		    }
		}
	}

	public function listar($sStatus='', $iCantidadNoProcesados='', $sMessageErrorBD=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ImportacionStockInicialView', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados, 'sMessageErrorBD' => $sMessageErrorBD));
			$this->load->view('footer', array("js_importacion_stock_inicial" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ImportacionStockInicialModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = ToDateBD($row->Fe_Emision);
            //$rows[] = $row->Tipo_Operacion_Sunat_Codigo;
            //$rows[] = $row->No_Tipo_Movimiento;
            //$rows[] = $row->No_Entidad;
            $rows[] = $row->Nu_Codigo_Barra;
            $rows[] = $row->No_Producto;
            //$rows[] = $row->Ss_Precio;
            $rows[] = numberFormat($row->Qt_Producto, 0, '.', ',');
           // $rows[] = $row->Fe_Lote_Vencimiento;
            //$rows[] = $row->Nu_Lote_Vencimiento;
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ImportacionStockInicialModel->count_all(),
	        'recordsFiltered' => $this->ImportacionStockInicialModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
	}
	
	public function verificarImportacionStockInicial(){
		echo json_encode($this->ImportacionStockInicialModel->verificarImportacionStockInicial());
	}
}
