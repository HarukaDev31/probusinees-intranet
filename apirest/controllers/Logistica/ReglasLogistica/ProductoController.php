<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class ProductoController extends CI_Controller {
	
	private $upload_path = '../assets/images/productos/';
	private $upload_path_table = '../assets/images/productos';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/ProductoModel');
		$this->load->model('HelperModel');
	}
	
	public function importarExcelProductos(){
		if (isset($_FILES['excel-archivo_producto']['name']) && isset($_FILES['excel-archivo_producto']['type']) && isset($_FILES['excel-archivo_producto']['tmp_name'])) {
		    $archivo = $_FILES['excel-archivo_producto']['name'];
		    $tipo = $_FILES['excel-archivo_producto']['type'];
		    $destino = "bak_" . $archivo;
		    
		    if (copy($_FILES['excel-archivo_producto']['tmp_name'], $destino)) {
		        if (file_exists($destino)) {
					$this->load->library('Excel');
		    		$objReader = new PHPExcel_Reader_Excel2007();
		    		$objPHPExcel = $objReader->load($destino);
		            $objPHPExcel->setActiveSheetIndex(0);
		            
		            $iCantidadRegistros = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
		            
		            $column = array(
		                'GRUPO_PRODUCTO' 		=> 'A',
		                'TIPO_PRODUCTO'			=> 'B',
		                'CODIGO_BARRA'	=> 'C',
		                'CODIGO_PRODUCTO'			=> 'D',
		                'NOMBRE'		=> 'E',
		                'GRUPO_IMPUESTO'				=> 'F',
		                'UNIDAD_MEDIDA'		=> 'G',
		                'MARCA'			=> 'H',
		                'CATEGORIA'					=> 'I',
		                'SUB_CATEGORIA'				=> 'J',
		                'CODIGO_PRODUCTO_SUNAT'			=> 'K',
		                'CANTIDAD_CO2_PRODUCTO'	=> 'L',
						'LABORATORIO'	=> 'M',
						'PRECIO' => 'N',
						'COSTO' => 'O',
						'STOCK_MINIMO'	=> 'P',
						'STOCK_MAXIMO' => 'Q',
						'TIPO_LAVADO' => 'R',
						'ESTADO' => 'S',
						'DESCRIPCION' => 'T',
						'VARIANTE_NOMBRE_1' => 'U',
						'VARIANTE_VALOR_1' => 'V',
						'VARIANTE_NOMBRE_2' => 'W',
						'VARIANTE_VALOR_2' => 'X',
						'VARIANTE_NOMBRE_3' => 'Y',
						'VARIANTE_VALOR_3' => 'Z',
						'PRECIO_ECOMMERCE_ONLINE_REGULAR' => 'AA',
						'PRECIO_ECOMMERCE_ONLINE' => 'AB',
						'CATEGORIA_MARKETPLACE' => 'AC',
						'SUB_CATEGORIA_MARKETPLACE' => 'AD',
						'MARCA_MARKETPLACE' => 'AE',
		            );
		            
	                $arrProducto = array();
	                $iCantidadNoProcesados = 0;
                	for ($i = 2; $i <= $iCantidadRegistros; $i++) {
	                	$iID_Grupo_Producto = $objPHPExcel->getActiveSheet()->getCell($column['GRUPO_PRODUCTO'] . $i)->getCalculatedValue();
	                	$iID_Grupo_Producto = filter_var(trim($iID_Grupo_Producto));
	                	
	                	$ID_Tipo_Producto = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['TIPO_PRODUCTO'] . $i)->getCalculatedValue()));
	                	
	                	$Nu_Codigo_Barra = $objPHPExcel->getActiveSheet()->getCell($column['CODIGO_BARRA'] . $i)->getCalculatedValue();
	                	$Nu_Codigo_Barra = quitarCaracteresEspeciales(strtoupper(filter_var(trim($Nu_Codigo_Barra))));
	                	
	                	$Nu_Codigo_Producto = $objPHPExcel->getActiveSheet()->getCell($column['CODIGO_PRODUCTO'] . $i)->getCalculatedValue();
	                	$Nu_Codigo_Producto = strtoupper(filter_var(trim($Nu_Codigo_Producto)));
						
	                	$No_Producto = trim($objPHPExcel->getActiveSheet()->getCell($column['NOMBRE'] . $i)->getCalculatedValue());
                        $No_Producto = quitarCaracteresEspeciales($No_Producto);
                        
	                	$No_Impuesto = $objPHPExcel->getActiveSheet()->getCell($column['GRUPO_IMPUESTO'] . $i)->getCalculatedValue();
	                	$No_Impuesto = filter_var(trim($No_Impuesto));
	                	
	                	$No_Unidad_Medida = $objPHPExcel->getActiveSheet()->getCell($column['UNIDAD_MEDIDA'] . $i)->getCalculatedValue();
	                	$No_Unidad_Medida = filter_var(trim($No_Unidad_Medida));
	                	
	                	$No_Marca = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['MARCA'] . $i)->getCalculatedValue()));
	                	$No_Marca = quitarCaracteresEspeciales($No_Marca);
	                	
	                	$No_Familia = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CATEGORIA'] . $i)->getCalculatedValue()));
	                	$No_Familia = quitarCaracteresEspeciales($No_Familia);
	                	
	                	$No_Sub_Familia = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['SUB_CATEGORIA'] . $i)->getCalculatedValue()));
	                	$No_Sub_Familia = quitarCaracteresEspeciales($No_Sub_Familia);
	                	
	                	$Nu_Codigo_Producto_Sunat = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CODIGO_PRODUCTO_SUNAT'] . $i)->getCalculatedValue()));
	                	$Nu_Codigo_Producto_Sunat = quitarCaracteresEspeciales($Nu_Codigo_Producto_Sunat);
	                	
	                	$Qt_CO2_Producto = $objPHPExcel->getActiveSheet()->getCell($column['CANTIDAD_CO2_PRODUCTO'] . $i)->getCalculatedValue();
						$Qt_CO2_Producto = strtoupper(filter_var(trim($Qt_CO2_Producto)));
	                	
	                	$No_Laboratorio = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['LABORATORIO'] . $i)->getCalculatedValue()));
	                	$No_Laboratorio = quitarCaracteresEspeciales($No_Laboratorio);
						
	                	$fPrecio = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PRECIO'] . $i)->getCalculatedValue()));
						$fPrecio = quitarCaracteresEspeciales($fPrecio);
						
	                	$fCosto = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['COSTO'] . $i)->getCalculatedValue()));
						$fCosto = quitarCaracteresEspeciales($fCosto);
						
	                	$iStockMinimo = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['STOCK_MINIMO'] . $i)->getCalculatedValue()));
						$iStockMinimo = quitarCaracteresEspeciales($iStockMinimo);
						
	                	$iStockMaximo = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['STOCK_MAXIMO'] . $i)->getCalculatedValue()));
						$iStockMaximo = quitarCaracteresEspeciales($iStockMaximo);
						
	                	$iTipoLavado = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['TIPO_LAVADO'] . $i)->getCalculatedValue()));
						$iTipoLavado = quitarCaracteresEspeciales($iTipoLavado);
						
	                	$iEstado = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['ESTADO'] . $i)->getCalculatedValue()));
						$iEstado = quitarCaracteresEspeciales($iEstado);
						
	                	$Txt_Producto = trim($objPHPExcel->getActiveSheet()->getCell($column['DESCRIPCION'] . $i)->getCalculatedValue());
                        $Txt_Producto = quitarCaracteresEspeciales($Txt_Producto);

	                	$ID_Variante_Item_1 = trim($objPHPExcel->getActiveSheet()->getCell($column['VARIANTE_NOMBRE_1'] . $i)->getCalculatedValue());
                        $ID_Variante_Item_1 = quitarCaracteresEspeciales($ID_Variante_Item_1);

	                	$ID_Variante_Item_Detalle_1 = trim($objPHPExcel->getActiveSheet()->getCell($column['VARIANTE_VALOR_1'] . $i)->getCalculatedValue());
                        $ID_Variante_Item_Detalle_1 = quitarCaracteresEspeciales($ID_Variante_Item_Detalle_1);

	                	$ID_Variante_Item_2 = trim($objPHPExcel->getActiveSheet()->getCell($column['VARIANTE_NOMBRE_2'] . $i)->getCalculatedValue());
                        $ID_Variante_Item_2 = quitarCaracteresEspeciales($ID_Variante_Item_2);

	                	$ID_Variante_Item_Detalle_2 = trim($objPHPExcel->getActiveSheet()->getCell($column['VARIANTE_VALOR_2'] . $i)->getCalculatedValue());
                        $ID_Variante_Item_Detalle_2 = quitarCaracteresEspeciales($ID_Variante_Item_Detalle_2);

	                	$ID_Variante_Item_3 = trim($objPHPExcel->getActiveSheet()->getCell($column['VARIANTE_NOMBRE_3'] . $i)->getCalculatedValue());
                        $ID_Variante_Item_3 = quitarCaracteresEspeciales($ID_Variante_Item_3);

	                	$ID_Variante_Item_Detalle_3 = trim($objPHPExcel->getActiveSheet()->getCell($column['VARIANTE_VALOR_3'] . $i)->getCalculatedValue());
                        $ID_Variante_Item_Detalle_3 = quitarCaracteresEspeciales($ID_Variante_Item_Detalle_3);

	                	$Ss_Precio_Ecommerce_Online_Regular = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PRECIO_ECOMMERCE_ONLINE_REGULAR'] . $i)->getCalculatedValue()));
	                	$Ss_Precio_Ecommerce_Online_Regular = quitarCaracteresEspeciales($Ss_Precio_Ecommerce_Online_Regular);
	                	
	                	$Ss_Precio_Ecommerce_Online = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PRECIO_ECOMMERCE_ONLINE'] . $i)->getCalculatedValue()));
	                	$Ss_Precio_Ecommerce_Online = quitarCaracteresEspeciales($Ss_Precio_Ecommerce_Online);
	                	
	                	$No_Familia_Marketplace = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CATEGORIA_MARKETPLACE'] . $i)->getCalculatedValue()));
	                	$No_Familia_Marketplace = quitarCaracteresEspeciales($No_Familia_Marketplace);
	                	
	                	$No_Sub_Familia_Marketplace = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['SUB_CATEGORIA_MARKETPLACE'] . $i)->getCalculatedValue()));
	                	$No_Sub_Familia_Marketplace = quitarCaracteresEspeciales($No_Sub_Familia_Marketplace);
	                	
	                	$No_Marca_Marketplace = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['MARCA_MARKETPLACE'] . $i)->getCalculatedValue()));
	                	$No_Marca_Marketplace = quitarCaracteresEspeciales($No_Marca_Marketplace);
	                	
	                	if ( 
							($iID_Grupo_Producto == 0 || $iID_Grupo_Producto == 1 || $iID_Grupo_Producto == 2)
							&& !empty($ID_Tipo_Producto)
							&& !empty($Nu_Codigo_Barra)
							&& !empty($No_Producto)
							&& !empty($No_Impuesto)
							&& !empty($No_Unidad_Medida)
							&& !empty($No_Familia)
						) {
		                	$arrProducto[] = array(
								'Nu_Tipo_Producto' => $iID_Grupo_Producto,
								'ID_Tipo_Producto' => $ID_Tipo_Producto,
								'Nu_Codigo_Barra' => $Nu_Codigo_Barra,
								'Nu_Codigo_Producto' => $Nu_Codigo_Producto,
								'No_Producto' => $No_Producto,
								'No_Impuesto' => $No_Impuesto,
								'No_Marca' => $No_Marca,
								'No_Unidad_Medida' => $No_Unidad_Medida,
								'No_Familia' => $No_Familia,
								'No_Sub_Familia' => $No_Sub_Familia,
								'Nu_Codigo_Producto_Sunat' => $Nu_Codigo_Producto_Sunat,
								'Qt_CO2_Producto' => $Qt_CO2_Producto,
								'No_Laboratorio' => $No_Laboratorio,
								'fPrecio' => $fPrecio,
								'fCosto' => $fCosto,
								'iStockMinimo' => $iStockMinimo,
								'iStockMaximo' => $iStockMaximo,
								'iTipoLavado' => $iTipoLavado,
								'iEstado' => $iEstado,
								'Txt_Producto' => $Txt_Producto,
								'ID_Variante_Item_1' => $ID_Variante_Item_1,
								'ID_Variante_Item_Detalle_1' => $ID_Variante_Item_Detalle_1,
								'ID_Variante_Item_2' => $ID_Variante_Item_2,
								'ID_Variante_Item_Detalle_2' => $ID_Variante_Item_Detalle_2,
								'ID_Variante_Item_3' => $ID_Variante_Item_3,
								'ID_Variante_Item_Detalle_3' => $ID_Variante_Item_Detalle_3,
								'Ss_Precio_Ecommerce_Online_Regular' => $Ss_Precio_Ecommerce_Online_Regular,
								'Ss_Precio_Ecommerce_Online' => $Ss_Precio_Ecommerce_Online,
								'No_Familia_Marketplace' => $No_Familia_Marketplace,
								'No_Sub_Familia_Marketplace' => $No_Sub_Familia_Marketplace,
								'No_Marca_Marketplace' => $No_Marca_Marketplace,
		                	);
	                	} else {
                        	$iCantidadNoProcesados++;
                        }
                	}
                	
                	$arrResponseProducto=false;
                	if ( count($arrProducto) > 0 ) {
		                $this->ProductoModel->setBatchImport($arrProducto);
		                $arrResponseProducto = $this->ProductoModel->importData();
                	} else {
	            		unlink($destino);
	                	unset($arrProducto);
                	
                		$sStatus = 'error-sindatos';
						redirect('Logistica/ReglasLogistica/ProductoController/listarProductos/' . $sStatus . '/' . 0 . '/Error sin datos');
                		exit();
                	}
                	
            		unlink($destino);
                	unset($arrProducto);
                	
					if ($arrResponseProducto['status'] == 'success') {
                		$sStatus = 'success';
						redirect('Logistica/ReglasLogistica/ProductoController/listarProductos/' . $sStatus . '/' . $iCantidadNoProcesados . '/' . $arrResponseProducto['message']);
                	} else {
                		$sStatus = 'error-bd';
						redirect('Logistica/ReglasLogistica/ProductoController/listarProductos/' . $sStatus . '/' . 0 . '/' . $arrResponseProducto['message']);
                	}
		        } else {
        	        $sStatus = 'error-archivo_no_existe';
					redirect('Logistica/ReglasLogistica/ProductoController/listarProductos/' . $sStatus . '/' . 0 . '/Archivo no existe');
		        }
		    } else {
		        $sStatus = 'error-copiar_archivo';
				redirect('Logistica/ReglasLogistica/ProductoController/listarProductos/' . $sStatus . '/' . 0 . '/Error al copiar archivo');
		    }
		}
	}

	public function listarProductos($sStatus='', $iCantidadNoProcesados='', $sMessageErrorBD=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/ProductoView', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados, 'sMessageErrorBD' => $sMessageErrorBD));
			$this->load->view('footer', array("js_producto" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ProductoModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action = 'delete';
        foreach ($arrData as $row) {
			$arrImgProducto = explode('productos',$row->No_Imagen_Item);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->No_Imagen_Item)){
				if ( file_exists($sPathImgProducto) ) {
					$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					//$image = '<div class="thumbnail">';
					$image = '<img class="img-fluid" data-url_img="' . $row->No_Imagen_Item . '" src="' . $base64 . '" title="' . limpiarCaracteresEspeciales($row->No_Producto) . '" alt="' . limpiarCaracteresEspeciales($row->No_Producto) . '" style="cursor:pointer; max-height:40px;" />';
					//$image .= '</div>';
				}
			}

        	settype($row->Qt_Producto, "double");
            $no++;
            $rows = array();
			
			$arrTiposItem = $this->HelperModel->obtenerTiposItemArray($row->Nu_Tipo_Producto);
            $rows[] =  $arrTiposItem['No_Tipo_Item'];

            $rows[] = $row->No_Unidad_Medida;
            $rows[] = $row->No_Familia;
			$rows[] = $row->No_Sub_Familia;
            $rows[] = $row->No_Marca;
            $rows[] = $row->Nu_Codigo_Barra;
            $rows[] = $row->No_Codigo_Interno;
			$rows[] = $row->No_Producto;
			$rows[] = $row->No_Impuesto_Breve;
            $rows[] = numberFormat($row->Qt_Producto, 3, '.', '');
			$rows[] = numberFormat($row->Ss_Precio, 2, '.', ',');
			$rows[] = numberFormat($row->Ss_Costo, 2, '.', ',');
            $rows[] = numberFormat($row->Ss_Costo_Promedio, 2, '.', ',');
            $rows[] = $row->Nu_Stock_Minimo;
			$rows[] = $row->Nu_Stock_Maximo;
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verProducto(\'' . $row->ID_Producto . '\', \'' . $row->No_Imagen_Item . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarProducto(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Producto . '\', \'' . $row->Nu_Codigo_Barra . '\', \'' . $row->Nu_Compuesto . '\', \'' . $action . '\', \'' . $row->No_Imagen_Item . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
			
			$btn_estado_stock='';
			$estado_stock='';
			$union_estado_stock='-';
			
			if($row->Nu_Tipo_Producto==1 && $row->Nu_Estado_Stock!='') {
				$btn_estado_stock='<button class="btn btn-xs btn-link" alt="Estado por almacén" title="Estado por almacén" href="javascript:void(0)" onclick="estadoxAlmacen(\'' . $row->ID_Producto . '\', \'' . $row->Nu_Estado_Stock . '\')">Estado x Almacen</button>';
				$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado_Stock);
				$estado_stock = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
				$union_estado_stock = $btn_estado_stock . '<br>' . $estado_stock;
			}
			$rows[] = $union_estado_stock;

			$rows[] = '<button class="btn btn-xs btn-link" alt="Imprimir Código de Barra" title="Imprimir Código de Barra" href="javascript:void(0)" onclick="generarBarcode(\'' . $row->ID_Producto . '\')"><i class="fa fa-2x fa-barcode" aria-hidden="true"></i></button>';
			$rows[] = !empty($image) ? $image : 'Sin imagen';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Agregar Variante" title="Agregar Variante" href="javascript:void(0)" onclick="agregarVarianteItem(\'' . $row->ID_Producto . '\')"><i class="fa fa-2x fa-plus-square" aria-hidden="true"></i></button>';
			
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ProductoModel->count_all(),
	        'recordsFiltered' => $this->ProductoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
    public function uploadMultiple(){
    	$arrResponse = array(
			'sStatus' => 'error',
			'sMessage' => 'problemas con imagén',
			'sClassModal' => 'modal-danger',
		);
		
    	if (!empty($_FILES)){
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
			if(!is_dir($path)){
			    mkdir($path,0755,TRUE);
			}
			
			if ( !file_exists($path . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name'])) ){
				$sNombreImagenMasExtension = $_FILES['file']['name'];
				$sExtensionNombreImagen = pathinfo($sNombreImagenMasExtension, PATHINFO_EXTENSION);

				$str = $sNombreImagenMasExtension;
				$arrNombre=(explode("jpeg",$str));
				$sNombreImagen = substr($arrNombre[0], 0, -1);

				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
				$config['max_size'] = 400;//400 KB
				$config['file_name'] = cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen;

				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imágen' . $path,
						'sClassModal' => 'modal-danger',
					);
				} else {
					$UploadData = $this->upload->data();
					$data = array('No_Producto_Imagen' => $UploadData["file_name"],"ID_Producto"=>$this->input->post('iIdProducto'),"Imagen_Tamano"=>$UploadData["file_size"],'ID_Predeterminado'=> 1);
					
					$id_imagen = 0;
					if( strlen($this->input->post('iIdProducto')>0))
						$id_imagen = $this->ProductoModel->AgregarImagen($data);
					
					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

					if($id_imagen > 0) {
						$resultado = $this->ProductoModel->AsignarImagenProducto(array(
							'No_Producto_Imagen' => $url_image . '/' . $UploadData["file_name"],
							'ID_Producto' => $this->input->post('iIdProducto')
						));
					}

					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenItem' => $url_image . '/' . $UploadData["file_name"],
						'sTamanoImagenItem' => $UploadData["file_size"],
						'NombreImagen' => $UploadData["file_name"],
						'id_imagen'=>$id_imagen
					);
				}
			} else {
				$arrUrlImagePath = explode('..', $path);
				$arrUrlImage = explode('/principal',base_url());
				$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];				
				$arrResponse = array(
					'sStatus' => 'success',
					'sMessage' => 'La imagen ya fue guardada',
					'sClassModal' => 'modal-success',
					'sTamanoImagenItem' => $_FILES['file']['size'],
					'sNombreImagenItem' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
				);
			}
		}
		echo json_encode($arrResponse);
    }
    
	public function removeFileImage(){
		$data = array(
		"ID_Producto"=>$this->input->post('iIdProducto'),
		"ID_Producto_Imagen"=>$this->input->post('iIdImagen'),
		"Predeterminado"=>1
		);

		echo $this->ProductoModel->RemoverImagen($data);
	}
	
	public function get_image(){
		$path         = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
		$arrUrlImagePath  = explode('..', $path);
		$arrUrlImage    = explode('/principal',base_url());
		$url_image      = $arrUrlImage[0] . $arrUrlImagePath[1];
		$row         = $this->ProductoModel->getImagenByIdProducto($this->input->post("iIdProducto"),$url_image);
		echo json_encode($row);
	}
	
	public function ajax_edit($ID){
        echo json_encode($this->ProductoModel->get_by_id($this->security->xss_clean($ID)));
    }
	
	public function ajax_edit_enlace($ID){
        echo json_encode($this->ProductoModel->get_by_id_enlace($this->security->xss_clean($ID)));
    }
	
	public function ajax_edit_precios_por_mayor($ID){
        echo json_encode($this->ProductoModel->get_by_id_precios_x_mayor($this->security->xss_clean($ID)));
    }
    
	public function crudProducto(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		$sComposicion = '';
		if ( isset($_POST['arrProducto']['Txt_Composicion']) ) {
			$arrComposicion = $this->security->xss_clean($_POST['arrProducto']['Txt_Composicion']);
			$iCount = count($arrComposicion);		
			for ($i=0; $i<$iCount; $i++)
				$sComposicion .= $arrComposicion[$i].',';
			$sComposicion = substr($sComposicion, 0, -1);
		}

		$sUrlProductoImagen = '';
		if ( !empty($_POST['arrProducto']['No_Imagen_Item']) )
			$sUrlProductoImagen = $this->security->xss_clean($_POST['arrProducto']['No_Imagen_Item']);

		$sSizeProductoImagen = '';
		if ( !empty($_POST['arrProducto']['Size_Imagen_Item']) )
			$sSizeProductoImagen = $this->security->xss_clean($_POST['arrProducto']['Size_Imagen_Item']);

		$Nu_Activar_Precio_x_Mayor = 0;
		if (isset($_POST['arrProducto']['Nu_Activar_Precio_x_Mayor']))
			$Nu_Activar_Precio_x_Mayor = ($_POST['arrProducto']['Nu_Activar_Precio_x_Mayor'] == "true" ? 1 : 0);
		
		
		$str = strtoupper($_POST['arrProducto']['No_Producto']);
		$pattern = "/[A-Z]/";
		if (preg_match($pattern, $str) == 0){//no existe ninguna letra del campo nombre de producto
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes de ingresar al menos una LETRA'));
			exit();
		}

		$data_producto = array(
			'ID_Empresa'				=> $this->user->ID_Empresa,
			'Nu_Tipo_Producto'			=> $this->security->xss_clean($_POST['arrProducto']['Nu_Tipo_Producto']),
			'ID_Tipo_Producto'			=> (!empty($_POST['arrProducto']['ID_Tipo_Producto']) ? $this->security->xss_clean($_POST['arrProducto']['ID_Tipo_Producto']) : 2),
			'ID_Ubicacion_Inventario'	=> 1,
			'Nu_Codigo_Barra'			=> $this->security->xss_clean(strtoupper($_POST['arrProducto']['Nu_Codigo_Barra'])),
			'ID_Producto_Sunat'			=> $this->security->xss_clean($_POST['arrProducto']['ID_Producto_Sunat']),
			'No_Producto'				=> $_POST['arrProducto']['No_Producto'],
			'Ss_Precio'	=> $this->security->xss_clean($_POST['arrProducto']['Ss_Precio']),
			'Ss_Costo' => $this->security->xss_clean($_POST['arrProducto']['Ss_Costo']),
			'No_Codigo_Interno'			=> $this->security->xss_clean(strtoupper($_POST['arrProducto']['No_Codigo_Interno'])),
			'ID_Impuesto'				=> $this->security->xss_clean($_POST['arrProducto']['ID_Impuesto']),
			'Nu_Lote_Vencimiento'		=> $this->security->xss_clean($_POST['arrProducto']['Nu_Lote_Vencimiento']),
			'ID_Unidad_Medida'			=> $this->security->xss_clean($_POST['arrProducto']['ID_Unidad_Medida']),
			'ID_Impuesto_Icbper'		=> $this->security->xss_clean($_POST['arrProducto']['ID_Impuesto_Icbper']),
			'Nu_Compuesto'				=> $this->security->xss_clean($_POST['arrProducto']['Nu_Compuesto']),
			'Nu_Estado'					=> $this->security->xss_clean($_POST['arrProducto']['Nu_Estado']),
			'Txt_Ubicacion_Producto_Tienda'	=> $this->security->xss_clean($_POST['arrProducto']['Txt_Ubicacion_Producto_Tienda']),
			'Txt_Producto' => $_POST['arrProducto']['Txt_Producto'],
			'Nu_Stock_Minimo' => $this->security->xss_clean($_POST['arrProducto']['Nu_Stock_Minimo']),
			'Nu_Stock_Maximo' => $this->security->xss_clean($_POST['arrProducto']['Nu_Stock_Maximo']),
			'Qt_CO2_Producto' => $this->security->xss_clean($_POST['arrProducto']['Qt_CO2_Producto']),
			'Nu_Receta_Medica' => $this->security->xss_clean($_POST['arrProducto']['Nu_Receta_Medica']),
			'ID_Laboratorio' => $this->security->xss_clean($_POST['arrProducto']['ID_Laboratorio']),
			'ID_Tipo_Pedido_Lavado'	=> $this->security->xss_clean($_POST['arrProducto']['ID_Tipo_Pedido_Lavado']),
			'Txt_Composicion' => $sComposicion,
			'No_Imagen_Item' => $sUrlProductoImagen,
			'Ss_Precio_Ecommerce_Online_Regular' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular']),
			'Ss_Precio_Ecommerce_Online' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Ecommerce_Online']),
			'ID_Familia_Marketplace' => $this->security->xss_clean($_POST['arrProducto']['ID_Familia_Marketplace']),
			'ID_Sub_Familia_Marketplace' => $this->security->xss_clean($_POST['arrProducto']['ID_Sub_Familia_Marketplace']),
			'ID_Marca_Marketplace' => $this->security->xss_clean($_POST['arrProducto']['ID_Marca_Marketplace']),
			'Nu_Activar_Precio_x_Mayor' => $Nu_Activar_Precio_x_Mayor
		);

		if ( !empty($_POST['arrProducto']['ID_Marca']) ){
			$data_producto = array_merge($data_producto, array("ID_Marca" => $_POST['arrProducto']['ID_Marca']));
		}
		if ( !empty($_POST['arrProducto']['ID_Familia']) ){
			$data_producto = array_merge($data_producto, array("ID_Familia" => $_POST['arrProducto']['ID_Familia']));
		}
		if ( !empty($_POST['arrProducto']['ID_Sub_Familia']) ){
			$data_producto = array_merge($data_producto, array("ID_Sub_Familia" => $_POST['arrProducto']['ID_Sub_Familia']));
		}
		if ( $_POST['arrProducto']['Nu_Tipo_Producto'] == 1 && $_POST['arrProducto']['ID_Impuesto_Icbper'] == 1 ){
			$data_producto = array_merge($data_producto, array("ID_Tabla_Dato_Icbper" => 2070));
		} else {
			$data_producto = array_merge($data_producto, array("ID_Tabla_Dato_Icbper" => 0));
		}

		if ( isset($_POST['arrProducto']['Nu_Favorito']) && $_POST['arrProducto']['Nu_Favorito'] != '' ){
			$data_producto = array_merge($data_producto, array("Nu_Favorito" => $_POST['arrProducto']['Nu_Favorito']));
		}
		
		if ( isset($_POST['arrProducto']['ID_Variante_Item_1']) ){
			$data_producto = array_merge($data_producto, array("ID_Variante_Item_1" => $_POST['arrProducto']['ID_Variante_Item_1']));
		}
		
		if ( isset($_POST['arrProducto']['ID_Variante_Item_Detalle_1']) ){
			$data_producto = array_merge($data_producto, array("ID_Variante_Item_Detalle_1" => $_POST['arrProducto']['ID_Variante_Item_Detalle_1']));
		}
		
		if ( isset($_POST['arrProducto']['ID_Variante_Item_2']) ){
			$data_producto = array_merge($data_producto, array("ID_Variante_Item_2" => $_POST['arrProducto']['ID_Variante_Item_2']));
		}
		
		if ( isset($_POST['arrProducto']['ID_Variante_Item_Detalle_2']) ){
			$data_producto = array_merge($data_producto, array("ID_Variante_Item_Detalle_2" => $_POST['arrProducto']['ID_Variante_Item_Detalle_2']));
		}
		
		if ( isset($_POST['arrProducto']['ID_Variante_Item_3']) ){
			$data_producto = array_merge($data_producto, array("ID_Variante_Item_3" => $_POST['arrProducto']['ID_Variante_Item_3']));
		}
		
		if ( isset($_POST['arrProducto']['ID_Variante_Item_Detalle_3']) ){
			$data_producto = array_merge($data_producto, array("ID_Variante_Item_Detalle_3" => $_POST['arrProducto']['ID_Variante_Item_Detalle_3']));
		}

		$data_imagen = array(
			'No_Producto_Imagen' => $sUrlProductoImagen,
			'Imagen_Tamano'	=> $_POST['arrProducto']["Imagen_Tamano"],
		);

		$arrProductoPrecioxMayor = 0;
		if (isset($_POST['arrProductoPrecioxMayor']))
			$arrProductoPrecioxMayor = $_POST['arrProductoPrecioxMayor'];

		echo json_encode(
		( $this->security->xss_clean($_POST['arrProducto']['EID_Empresa']) != '' && $this->security->xss_clean($_POST['arrProducto']['EID_Producto']) != '') ?
			$this->ProductoModel->actualizarProducto(array('ID_Empresa' => $this->security->xss_clean($_POST['arrProducto']['EID_Empresa']), 'ID_Producto' => $this->security->xss_clean($_POST['arrProducto']['EID_Producto'])), $data_producto, $this->security->xss_clean($_POST['arrProducto']['ENu_Codigo_Barra']), $this->security->xss_clean($_POST['arrProducto']['ENo_Codigo_Interno']), $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor)
		:
			$this->ProductoModel->agregarProducto($data_producto, $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor,$data_imagen)
		);
	}
    
	public function eliminarProducto($ID_Empresa, $ID, $Nu_Codigo_Barra, $Nu_Compuesto, $sNombreImagenItem=''){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ProductoModel->eliminarProducto($this->security->xss_clean($ID_Empresa), $this->security->xss_clean($ID), $this->security->xss_clean($Nu_Codigo_Barra), $this->security->xss_clean($Nu_Compuesto), $this->security->xss_clean($sNombreImagenItem)));
	}
    
	public function estadoxAlmacen($ID, $Nu_Estado_Stock){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ProductoModel->estadoxAlmacen($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado_Stock)));
	}

	public function generarBarcode($ID, $iTipoFormatoPrint, $iNumeroColuma, $iPrintSku){
		$ID = $this->security->xss_clean($ID);
		$iTipoFormatoPrint = $this->security->xss_clean($iTipoFormatoPrint);
		$iNumeroColuma = $this->security->xss_clean($iNumeroColuma);
		$iPrintSku = $this->security->xss_clean($iPrintSku);
		$arrData = $this->ProductoModel->get_by_id($ID);
		
		$this->load->library('FormatoLibroSunatPDF');

		$sCodigoBarra = trim($arrData->Nu_Codigo_Barra);
		$fileNamePDF = $sCodigoBarra . ".pdf";

		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$this->load->library('Barcode');
		$Barcode = new Barcode();		
		$path = '../assets/images/' . $this->empresa->Nu_Documento_Identidad . '/';//cuando sea localhost crear la carpeta con el RUC de prueba
		if(!is_dir($path)){
			mkdir($path,0755,TRUE);
		}
		$sTypeImage='jpg';//jpeg
		$filepath_barcode= $path . $sCodigoBarra . '.' . $sTypeImage;
		$Barcode->barcodeGenerate($filepath_barcode, $sCodigoBarra, '20', 'horizontal', 'Code128', true, 1, $sTypeImage);

		$arrCabecera = array ('arrData' => $arrData, 'filepath_barcode' => $filepath_barcode, 'iTipoFormatoPrint' => $iTipoFormatoPrint, 'iNumeroColuma' => $iNumeroColuma, 'iPrintSku' => $iPrintSku);

		ob_start();
		$file = $this->load->view('Logistica/ReglasLogistica/pdf/ProductoCodigoBarraViewPDF', array('arrCabecera' => $arrCabecera) );
		$html = ob_get_contents();
		ob_end_clean();

		$pdf->SetAuthor('laesystems');
		$pdf->SetTitle('laesystems - Código de barra');

		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);

		/*MEDIDAS DE TICKET*/
		/*
		$pdf->SetMargins(PDF_MARGIN_LEFT-13, PDF_MARGIN_TOP-25, PDF_MARGIN_RIGHT-13);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$page_format = array(
			'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 74.1, 'ury' => 229.3),
		);
		*/
		//MINIMO PARA GENERAR CODIGO DE BARRA ES 5 CARACTERES
		$pdf->SetMargins(PDF_MARGIN_LEFT-17, PDF_MARGIN_TOP-25, PDF_MARGIN_RIGHT-17);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$page_format = array(
			//'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 50, 'ury' => 55),//original
			'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 50, 'ury' => 55),
		);
		$pdf->AddPage('P', $page_format, false, false);

		$pdf->setFont('helvetica', '', 7);
		
		$pdf->writeHTML($html, true, false, true, false, '');

		unlink($filepath_barcode);

		$pdf->Output($fileNamePDF, 'I');
	}

	public function generarBarcodeHTML($ID, $iTipoFormatoPrint, $iNumeroColuma, $iPrintSku){
		$ID = $this->security->xss_clean($ID);
		$iTipoFormatoPrint = $this->security->xss_clean($iTipoFormatoPrint);
		$iNumeroColuma = $this->security->xss_clean($iNumeroColuma);
		$iPrintSku = $this->security->xss_clean($iPrintSku);
		$arrData = $this->ProductoModel->get_by_id($ID);
		
		$this->load->library('FormatoLibroSunatPDF');

		$sCodigoBarra = trim($arrData->Nu_Codigo_Barra);

		$this->load->library('Barcode');
		$Barcode = new Barcode();		
		$path = '../assets/images/' . $this->empresa->Nu_Documento_Identidad . '/';//cuando sea localhost crear la carpeta con el RUC de prueba
		if(!is_dir($path)){
			mkdir($path,0755,TRUE);
		}
		$sTypeImage='png';//jpeg //jpg
		$filepath_barcode= $path . $sCodigoBarra . '.' . $sTypeImage;

		if (file_exists($filepath_barcode))
			unlink($filepath_barcode);
			
		$Barcode->barcodeGenerate($filepath_barcode, $sCodigoBarra, '20', 'horizontal', 'code128', true, 1, $sTypeImage);

		$arrUrlImagePath = explode('..', $path);
		$arrUrlImage = explode('/principal',base_url());
		$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];	

		$arrCabecera = array ('arrData' => $arrData, 'filepath_barcode' => $filepath_barcode, 'filepath_barcode_url' => $url_image . $sCodigoBarra . '.' . $sTypeImage, 'iTipoFormatoPrint' => $iTipoFormatoPrint, 'iNumeroColuma' => $iNumeroColuma, 'iPrintSku' => $iPrintSku);
		
		ob_start();
		$file = $this->load->view('Logistica/ReglasLogistica/pdf/ProductoCodigoBarraViewHTML', array('arrCabecera' => $arrCabecera) );
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		exit();		
	}
	
	public function crudProductoxVarianteModal(){
        echo json_encode($this->ProductoModel->crudProductoxVarianteModal($this->input->post()));
    }
}