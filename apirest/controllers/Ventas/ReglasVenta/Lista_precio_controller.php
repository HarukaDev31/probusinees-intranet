<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lista_precio_controller extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/ReglasVenta/Lista_precio_model');
		$this->load->model('HelperModel');
	}
	
	public function importarExcelListaPrecios(){
		if (isset($_FILES['excel-archivo_lista_precio']['name']) && isset($_FILES['excel-archivo_lista_precio']['type']) && isset($_FILES['excel-archivo_lista_precio']['tmp_name'])) {
		    $archivo	= $_FILES['excel-archivo_lista_precio']['name'];
		    $tipo		= $_FILES['excel-archivo_lista_precio']['type'];
		    $destino	= "bak_" . $archivo;
		    
		    if (copy($_FILES['excel-archivo_lista_precio']['tmp_name'], $destino)) {
		        if (file_exists($destino)) {
					$this->load->library('Excel');
		    		$objReader = new PHPExcel_Reader_Excel2007();
		    		$objPHPExcel = $objReader->load($destino);
		            $objPHPExcel->setActiveSheetIndex(0);
		            
		            $iCantidadRegistros = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
		            
		            $column = array(
		                'CODIGO_BARRA' 			=> 'A',
		                'PRECIO_INTERNO'		=> 'B',
		                'PORCENTAJE_DESCUENTO'	=> 'C',
		                'PRECIO'				=> 'D',
		            );
	                
	                $arrListaPrecio = array();
	                $iCantidadNoProcesados = 0;
                	for ($i = 2; $i <= $iCantidadRegistros; $i++) {
	                	$Nu_Codigo_Barra = $objPHPExcel->getActiveSheet()->getCell($column['CODIGO_BARRA'] . $i)->getCalculatedValue();
	                	$Nu_Codigo_Barra = strtoupper(filter_var(trim($Nu_Codigo_Barra)));
	                	
                        $Ss_Precio_Interno = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PRECIO_INTERNO'] . $i)->getCalculatedValue()));
	                	settype($Ss_Precio_Interno, "double");
	                	
                        $Po_Descuento = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PORCENTAJE_DESCUENTO'] . $i)->getCalculatedValue()));
	                	settype($Po_Descuento, "double");
	                	
                        $Ss_Precio = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PRECIO'] . $i)->getCalculatedValue()));
	                	settype($Ss_Precio, "double");
	                	
	                	if ( !empty($Nu_Codigo_Barra) && $Ss_Precio > 0.00 ) {
		                	$arrListaPrecio[] = array(
								'Nu_Codigo_Barra'	=> $Nu_Codigo_Barra,
								'Ss_Precio_Interno' => $Ss_Precio_Interno,
								'Po_Descuento'		=> $Po_Descuento,
								'Ss_Precio' 		=> $Ss_Precio,
		                	);
	                	} else {
	                		$iCantidadNoProcesados++;
	                	}
                	}

                	$bResponse=false;
                	if ( count($arrListaPrecio) > 0 ) {
		                $this->Lista_precio_model->setBatchImport($this->input->post('modal-ID_Lista_Precio_Cabecera'), $arrListaPrecio);
		                $bResponse = $this->Lista_precio_model->importData();
                	} else {
	            		unlink($destino);
	                	unset($arrListaPrecio);
                	
                		$sStatus = 'error-sindatos';
						redirect('Ventas/ReglasVenta/Lista_precio_controller/listar/' . $sStatus);
                		exit();
                	}
                	
            		unlink($destino);
                	unset($arrListaPrecio);
                	
                	if ($bResponse){
                		$sStatus = 'success';
						redirect('Ventas/ReglasVenta/Lista_precio_controller/listar/' . $sStatus . '/' . $iCantidadNoProcesados);
                	} else {
                		$sStatus = 'error-bd';
						redirect('Ventas/ReglasVenta/Lista_precio_controller/listar/' . $sStatus);
                	}
		        } else {
        	        $sStatus = 'error-archivo_no_existe';
					redirect('Ventas/ReglasVenta/Lista_precio_controller/listar/' . $sStatus);
		        }
		    } else {
		        $sStatus = 'error-copiar_archivo';
				redirect('Ventas/ReglasVenta/Lista_precio_controller/listar/' . $sStatus);
		    }
		}
	}
	
	public function listar($sStatus='', $iCantidadNoProcesados=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/ReglasVenta/lista_precio_view', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados));
			$this->load->view('footer', array("js_lista_precio" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->Lista_precio_model->get_datatables();
        $data = array();
        $no = $this->input->post('start');
		$action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = $row->No_Lista_Precio;
			$rows[] = $row->No_Signo;
            $rows[] = ($row->Nu_Tipo_Lista_Precio == 1 ? 'Venta' : 'Compra');
			$rows[] = (empty($row->No_Entidad) ? 'Ninguno' : $row->No_Entidad);
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            $rows[] = '<button class="btn btn-xs btn-link" alt="Agregar Precio" title="Agregar Precio" href="javascript:void(0)" onclick="add_lista_precio_producto(\'' . $row->ID_Lista_Precio_Cabecera . '\')"><i class="fa fa-plus" aria-hidden="true"> Precios</i></button>';
            //$rows[] = '<button class="btn btn-xs btn-link" alt="Agregar Precio" title="Agregar Precio" href="javascript:void(0)" onclick="add_lista_precio_producto(\'' . $row->ID_Lista_Precio_Cabecera . '\')"><i class="fa fa-plus" aria-hidden="true"> Precios <span class="badge bg-red">' . $row->Nu_Cantidad_Precios . '</span></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Replicar Lista Precio" title="Replicar Lista Precio" href="javascript:void(0)" onclick="replicarListaPrecio(\'' . $row->ID_Almacen . '\', \'' . $row->ID_Lista_Precio_Cabecera . '\')"><i class="fa fa-2x fa-clone" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verLista_Precio(\'' . $row->ID_Lista_Precio_Cabecera . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarLista_Precio(\'' . $row->ID_Lista_Precio_Cabecera . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->Lista_precio_model->count_all(),
	        'recordsFiltered' => $this->Lista_precio_model->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->Lista_precio_model->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudLista_Precio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'			=> $this->empresa->ID_Empresa,
			'ID_Organizacion'		=> $this->empresa->ID_Organizacion,
			'No_Lista_Precio'		=> $this->input->post('No_Lista_Precio'),
			'Nu_Tipo_Lista_Precio'	=> $this->input->post('Nu_Tipo_Lista_Precio'),
			'ID_Moneda'				=> $this->input->post('ID_Moneda'),
			'Nu_Estado'				=> $this->input->post('Nu_Estado'),
		);
		if ( !empty($this->input->post('ID_Almacen')) ){
			$data = array_merge($data, array("ID_Almacen" => $this->input->post('ID_Almacen')));
		}
		if ( !empty($this->input->post('ID_Entidad')) ){
			$data = array_merge($data, array("ID_Entidad" => $this->input->post('ID_Entidad')));
		}
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Organizacion') != '' && $this->input->post('ENo_Lista_Precio') != '') ?
			$this->Lista_precio_model->actualizarLista_Precio(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Organizacion' => $this->input->post('EID_Organizacion'), 'ID_Lista_Precio_Cabecera' => $this->input->post('EID_Lista_Precio_Cabecera')), $data, $this->input->post('EID_Organizacion'), $this->input->post('ENo_Lista_Precio'))
		:
			$this->Lista_precio_model->agregarLista_Precio($data)
		);
	}
    
	public function eliminarLista_Precio($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->Lista_precio_model->eliminarLista_Precio($this->security->xss_clean($ID)));
	}
	
	public function ajax_list_producto(){
		$arrData = $this->Lista_precio_model->get_datatables_precio();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->Nu_Codigo_Barra;
            $rows[] = $row->No_Producto;
            //$rows[] = numberFormat($row->Ss_Precio_Interno, 2, '.', ',');
            //$rows[] = $row->Po_Descuento;
            $rows[] = numberFormat($row->Ss_Precio, 2, '.', ',');
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verLista_Precio_Producto(\'' . $row->ID_Lista_Precio_Detalle . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarLista_Precio_Producto(\'' . $row->ID_Lista_Precio_Detalle . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->Lista_precio_model->count_all_precio(),
	        'recordsFiltered' => $this->Lista_precio_model->count_filtered_precio(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit_producto($ID){
        echo json_encode($this->Lista_precio_model->get_by_id_precio_producto($this->security->xss_clean($ID)));
    }
    
	public function crudLista_Precio_Producto(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Lista_Precio_Cabecera'	=> $this->input->post('ID_Lista_Precio_Cabecera'),
			'ID_Producto'				=> $this->input->post('ID_Producto'),
			'Ss_Precio_Interno'			=> $this->input->post('Ss_Precio_Interno'),
			'Po_Descuento'				=> $this->input->post('Po_Descuento'),
			'Ss_Precio'					=> $this->input->post('Ss_Precio'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
		);
		echo json_encode($this->Lista_precio_model->agregarLista_Precio_Producto($data));
	}
    
	public function crudLista_Precio_Producto_Update(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Lista_Precio_Cabecera'	=> $this->input->post('ID_Lista_Precio_Cabecera'),
			'ID_Producto'				=> $this->input->post('EID_Producto'),
			'Ss_Precio_Interno'			=> $this->input->post('Ss_Precio_Interno_Editar'),
			'Po_Descuento'				=> $this->input->post('Po_Descuento_Editar'),
			'Ss_Precio'					=> $this->input->post('Ss_Precio_Editar'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
		);
		echo json_encode($this->Lista_precio_model->actualizarLista_Precio_Producto(array('ID_Lista_Precio_Detalle' => $this->input->post('ID_Lista_Precio_Detalle')), $data, $this->input->post('EID_Producto')));
	}
    
	public function eliminarLista_Precio_Producto($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->Lista_precio_model->eliminarLista_Precio_Producto($this->security->xss_clean($ID)));
	}

	public function getListaPrecioxId(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getListaPrecioxId($this->input->post()));
	}
	
	public function replicarListaPrecio(){
        echo json_encode($this->Lista_precio_model->replicarListaPrecio($this->input->post()));
    }
}
