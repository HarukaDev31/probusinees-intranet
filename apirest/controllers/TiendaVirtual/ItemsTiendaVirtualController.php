<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 900); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class ItemsTiendaVirtualController extends CI_Controller {
	
	private $upload_path = '../assets/images/productos/';
	private $upload_path_table = '../assets/images/productos';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('TiendaVirtual/ProductoModel');
		$this->load->model('HelperDropshippingModel');
		$this->load->model('ConfiguracionModel');
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
		                'CODIGO_BARRA'			=> 'B',
		                'NOMBRE'				=> 'C',
		                'GRUPO_IMPUESTO'		=> 'D',
						'PRECIO' 				=> 'E',
						'PRECIO_OFERTA' 		=> 'F',
		                'CATEGORIA'				=> 'G',
		                'SUB_CATEGORIA'			=> 'H',
		                'MARCA'					=> 'I',
		                'UNIDAD_MEDIDA'			=> 'J',
						'ESTADO' 				=> 'K',
						'ESTADO_DESTACADO'		=> 'L',
						'DESCRIPCION' 			=> 'M',
		            );
		            
	                $arrProducto = array();
	                $iCantidadNoProcesados = 0;
                	for ($i = 2; $i <= $iCantidadRegistros; $i++) {
	                	$iID_Grupo_Producto = $objPHPExcel->getActiveSheet()->getCell($column['GRUPO_PRODUCTO'] . $i)->getCalculatedValue();
	                	$iID_Grupo_Producto = filter_var(trim($iID_Grupo_Producto));

	                	$Nu_Codigo_Barra = $objPHPExcel->getActiveSheet()->getCell($column['CODIGO_BARRA'] . $i)->getCalculatedValue();
	                	$Nu_Codigo_Barra = quitarCaracteresEspeciales(strtoupper(filter_var(trim($Nu_Codigo_Barra))));
	                							
	                	$No_Producto = trim($objPHPExcel->getActiveSheet()->getCell($column['NOMBRE'] . $i)->getCalculatedValue());
                        $No_Producto = quitarCaracteresEspeciales($No_Producto);
                        
	                	$No_Impuesto = $objPHPExcel->getActiveSheet()->getCell($column['GRUPO_IMPUESTO'] . $i)->getCalculatedValue();
	                	$No_Impuesto = filter_var(trim($No_Impuesto));
						
	                	$fPrecio = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PRECIO'] . $i)->getCalculatedValue()));
						$fPrecio = quitarCaracteresEspeciales($fPrecio);
						
	                	$fPrecioOferta = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PRECIO_OFERTA'] . $i)->getCalculatedValue()));
						$fPrecioOferta = quitarCaracteresEspeciales($fPrecioOferta);
	                	
						settype($fPrecio, "double");

	                	$No_Familia = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CATEGORIA'] . $i)->getCalculatedValue()));
	                	$No_Familia = quitarCaracteresEspeciales($No_Familia);
	                	
	                	$No_Sub_Familia = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['SUB_CATEGORIA'] . $i)->getCalculatedValue()));
	                	$No_Sub_Familia = quitarCaracteresEspeciales($No_Sub_Familia);
	                	
	                	$No_Marca = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['MARCA'] . $i)->getCalculatedValue()));
	                	$No_Marca = quitarCaracteresEspeciales($No_Marca);
	                	
	                	$No_Unidad_Medida = $objPHPExcel->getActiveSheet()->getCell($column['UNIDAD_MEDIDA'] . $i)->getCalculatedValue();
	                	$No_Unidad_Medida = filter_var(trim($No_Unidad_Medida));
					
	                	$iEstado = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['ESTADO'] . $i)->getCalculatedValue()));
						$iEstado = quitarCaracteresEspeciales($iEstado);
					
	                	$iEstadoDestacado = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['ESTADO_DESTACADO'] . $i)->getCalculatedValue()));
						$iEstadoDestacado = quitarCaracteresEspeciales($iEstadoDestacado);
						
	                	$Txt_Producto = trim($objPHPExcel->getActiveSheet()->getCell($column['DESCRIPCION'] . $i)->getCalculatedValue());
                        $Txt_Producto = quitarCaracteresEspeciales($Txt_Producto);
	                	
	                	if ( 
							($iID_Grupo_Producto == 0 || $iID_Grupo_Producto == 1 || $iID_Grupo_Producto == 2)
							&& !empty($Nu_Codigo_Barra)
							&& !empty($No_Producto)
							&& !empty($No_Impuesto)
							&& $fPrecio > 0.00
							&& !empty($No_Familia)
							&& !empty($No_Unidad_Medida)
						) {
		                	$arrProducto[] = array(
								'Nu_Tipo_Producto' => $iID_Grupo_Producto,
								'ID_Tipo_Producto' => 2,
								'Nu_Codigo_Barra' => $Nu_Codigo_Barra,
								'No_Producto' => $No_Producto,
								'No_Impuesto' => $No_Impuesto,
								'fPrecio' => $fPrecio,
								'fPrecioOferta' => $fPrecioOferta,
								'No_Familia' => $No_Familia,
								'No_Sub_Familia' => $No_Sub_Familia,
								'No_Marca' => $No_Marca,
								'No_Unidad_Medida' => $No_Unidad_Medida,
								'iEstado' => $iEstado,
								'iEstadoDestacado' => $iEstadoDestacado,
								'Txt_Producto' => $Txt_Producto,
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
						redirect('TiendaVirtual/ItemsTiendaVirtualController/listar/' . $sStatus . '/' . 0 . '/Error sin datos');
                		exit();
                	}
                	
            		unlink($destino);
                	unset($arrProducto);
                	
					if ($arrResponseProducto['status'] == 'success') {
                		$sStatus = 'success';
						redirect('TiendaVirtual/ItemsTiendaVirtualController/listar/' . $sStatus . '/' . $iCantidadNoProcesados . '/' . $arrResponseProducto['message']);
                	} else {
                		$sStatus = 'error-bd';
						redirect('TiendaVirtual/ItemsTiendaVirtualController/listar/' . $sStatus . '/' . 0 . '/' . $arrResponseProducto['message']);
                	}
		        } else {
        	        $sStatus = 'error-archivo_no_existe';
					redirect('TiendaVirtual/ItemsTiendaVirtualController/listar/' . $sStatus . '/' . 0 . '/No existe archivo');
		        }
		    } else {
		        $sStatus = 'error-copiar_archivo';
				redirect('TiendaVirtual/ItemsTiendaVirtualController/listar/' . $sStatus . '/' . 0 . '/Error al copiar archivo');
		    }
		}
	}

	public function listar($sStatus='', $iCantidadNoProcesados='', $sMessageErrorBD=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('TiendaVirtual/ProductoView', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados, 'sMessageErrorBD' => $sMessageErrorBD, 'NDI' => $this->empresa->Nu_Documento_Identidad));
			$this->load->view('footer', array("js_producto_tienda_virtual" => true));
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
					$bStatusFileImage = fopen($sPathImgProducto, "r");
					if ($bStatusFileImage) {
						$img_binary = fread($bStatusFileImage, filesize($sPathImgProducto));
						$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
						$image = '<img class="img-fluid" data-url_img="' . $row->No_Imagen_Item . '" src="' . $base64 . '" title="' . limpiarCaracteresEspeciales($row->No_Producto) . '" alt="' . limpiarCaracteresEspeciales($row->No_Producto) . '" style="cursor:pointer; max-height:100px;" />';
					}
				}
			}

        	settype($row->Qt_Producto, "double");
            $no++;
            $rows = array();
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verProducto(\'' . $row->ID_Producto . '\', \'' . $row->No_Imagen_Item . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
            $rows[] = $row->Nu_Codigo_Barra;
			$rows[] = $row->No_Producto;
			if($this->empresa->Nu_Proveedor_Dropshipping == 1 || $this->empresa->Nu_Tienda_Virtual_Propia == 1) {
				if($row->ID_Producto_Relacion_Producto_Dropshipping > 0) {
					$fStockProducto = $this->ProductoModel->getStockProveedorRelacionProducto($row->ID_Producto_Relacion_Producto_Dropshipping);
            		$rows[] = round($fStockProducto, 2);
				} else {
					$rows[] = round($row->Qt_Producto, 2);
				}
			}
			
			if($this->empresa->Nu_Proveedor_Dropshipping == 1 || $this->empresa->Nu_Vendedor_Dropshipping == 1) {
				
				if($row->ID_Producto_Relacion_Producto_Dropshipping > 0) {
					//buscar precio de proveedor por el id de relacion de producto
					//ID_Producto_Relacion_Producto_Dropshipping
					$Ss_Precio_Proveedor_Dropshipping = $this->ProductoModel->getPrecioProveedorRelacionProducto($row->ID_Producto_Relacion_Producto_Dropshipping);
					$rows[] = $this->user->No_Signo . ' ' . numberFormat($Ss_Precio_Proveedor_Dropshipping, 2, '.', ',');
				} else {
					//buscar precio de proveedor por el id de relacion de producto
					//ID_Producto_Relacion_Producto_Dropshipping
					$rows[] = $this->user->No_Signo . ' ' . numberFormat($row->Ss_Precio_Proveedor_Dropshipping, 2, '.', ',');
				}
			}

			if($this->empresa->Nu_Proveedor_Dropshipping == 1) {
				$rows[] = $this->user->No_Signo . ' ' . numberFormat($row->Ss_Precio_Vendedor_Dropshipping, 2, '.', ',');
			}

			if($this->empresa->Nu_Vendedor_Dropshipping == 1 || $this->empresa->Nu_Tienda_Virtual_Propia == 1) {
				$rows[] = $this->user->No_Signo . ' ' . numberFormat($row->Ss_Precio_Ecommerce_Online_Regular, 2, '.', ',');
				$rows[] = $this->user->No_Signo . ' ' . numberFormat($row->Ss_Precio_Ecommerce_Online, 2, '.', ',');
			}
			
			$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Estado);
			$dropdown_estado_tienda = '<div class="dropdown">
			<button style="width: 100%;" class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . ($row->Nu_Estado == 1 ? 'Visible' : 'Oculto') . ' <span class="caret"></span></button>
			<ul class="dropdown-menu" style="width: 100%; position: sticky;">
				<li><a alt="Mostrar item en tienda" title="Mostrar item en tienda" href="javascript:void(0)" onclick="cambiarEstadoTienda(\'' . $row->ID_Producto . '\',1);">Visible</a></li>
				<li><a alt="Ocultar item en tienda" title="Ocultar item en tienda" href="javascript:void(0)" onclick="cambiarEstadoTienda(\'' . $row->ID_Producto . '\',0);">Oculto</a></li>
			</ul>
			</div>';
			$rows[] = $dropdown_estado_tienda;
            //$rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$arrEstadoDestacado = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Destacado_Item_Lae_Shop );
			$dropdown = '<div class="dropdown">
			<button style="width: 100%;" class="btn btn-' . $arrEstadoDestacado['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoDestacado['No_Estado'] . ' <span class="caret"></span></button>
			<ul class="dropdown-menu" style="width: 100%; position: sticky;">
				<li><a alt="Mostrar destacado en tienda" title="Mostrar destacado en tienda" href="javascript:void(0)" onclick="cambiarEstadoDestacado(\'' . $row->ID_Producto . '\',1);">Visible</a></li>
				<li><a alt="Ocultar destacado en tienda" title="Ocultar destacado en tienda" href="javascript:void(0)" onclick="cambiarEstadoDestacado(\'' . $row->ID_Producto . '\',0);">Oculto</a></li>
			</ul>
			</div>';
			$rows[] = $dropdown;

			$rows[] = !empty($image) ? $image : 'Sin imagen';
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarProducto(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Producto . '\', \'' . $row->Nu_Codigo_Barra . '\', \'' . 0 . '\', \'' . $action . '\', \'' . $row->No_Imagen_Item . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
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
				
				$imagen = cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
				$arrResponse = array(
					'sStatus' => 'error',
					'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén' . $path,
					'sClassModal' => 'modal-danger',
				);
        	} else {
				$UploadData = $this->upload->data();
				$id_imagen = 0;
				if( strlen($this->input->post('iIdProducto')>0)){
					$data = array('No_Producto_Imagen' => $UploadData["file_name"], "ID_Producto" => $this->input->post('iIdProducto'), "Imagen_Tamano" => $UploadData["file_size"]);
					$id_imagen = $this->ProductoModel->AgregarImagen($data);
					$productosHijos = $this->ProductoModel->getProductosHijos($this->input->post('iIdProducto'), 1);
					if(count($productosHijos) > 0) {
						$arrImagenProductosHijos = [];
						for ($i = 0; $i < count($productosHijos); $i++) { 
							$arrImagenProductosHijos[$i]['No_Producto_Imagen'] = $UploadData["file_name"];
							$arrImagenProductosHijos[$i]['ID_Producto'] = $productosHijos[$i]->ID_Producto;
							$arrImagenProductosHijos[$i]['Imagen_Tamano'] = $UploadData["file_size"];
						}
						$this->ProductoModel->AgregarImagenProductoHijo($arrImagenProductosHijos);
					}
				}

				$arrUrlImagePath = explode('..', $path);
				$arrUrlImage = explode('/principal',base_url());
				$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
				$arrResponse = array(
					'sStatus' => 'success',
					'sMessage' => 'imagén guardada',
					'sClassModal' => 'modal-success',
					'sNombreImagenItem' => $url_image . '/' . $UploadData["file_name"],
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
					'sNombreImagenItem' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
				);
			}
		}
		echo json_encode($arrResponse);
    }

    public function get_image(){
    $path         = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
    $arrUrlImagePath  = explode('..', $path);
    $arrUrlImage    = explode('/principal',base_url());
    $url_image      = $arrUrlImage[0] . $arrUrlImagePath[1];
    $rows         = $this->ProductoModel->getImagenes($this->input->post("iIdProducto"),$url_image);
    echo json_encode($rows);
  }

	public function removeFileImage(){
    $data = array(
		"ID_Producto"=>$this->input->post('iIdProducto'),
		"ID_Producto_Imagen"=>$this->input->post('IdImagen'),
		"Predeterminado"=>$this->input->post('Predeterminado')
	);
    $resultado = json_decode($this->ProductoModel->RemoverImagen($data));
	if($resultado->status == 'success') {
		$productosHijos = $this->ProductoModel->getProductosHijos($this->input->post('iIdProducto'), 1);
		if(count($productosHijos) > 0) {
			$arrIDProductosHijos = [];
			$arrIDProductosHijosPredeterminados = [];
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
			$arrUrlImagePath  = explode('..', $path);
			$arrUrlImage    = explode('/principal',base_url());
			$url_image      = $arrUrlImage[0] . $arrUrlImagePath[1];
			for ($i=0; $i < count($productosHijos); $i++) { 
				$arrIDProductosHijos[] = $productosHijos[$i]->ID_Producto;
				$ID_Predeterminado = 0;
				if($url_image.'/'.$resultado->No_Producto_Imagen == $productosHijos[$i]->No_Imagen_Item){
					$ID_Predeterminado = 1;
				}
				$this->ProductoModel->RemoverImagenProductoHijo($productosHijos[$i]->ID_Producto, $ID_Predeterminado, $resultado->No_Producto_Imagen);
			}
			
			
		}
	}
	//echo $this->ProductoModel->RemoverImagen($data);
	echo json_encode($resultado);
	}

	public function DefaultImagen(){

    $data = array(
          "ID_Producto"=>$this->input->post('iIdProducto'),
          "ID_Producto_Imagen"=>$this->input->post('IdImagen')
          );
    echo $this->ProductoModel->DefaultImagen($data);

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
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
		$arrUrlImagePath = explode('..', $path);
		$arrUrlImage = explode('/principal',base_url());
		$url_image = $arrUrlImage[0] . $arrUrlImagePath[1]."/";
		//$_POST["arrProducto"]["No_Imagen_Item"]=$url_image.$_POST["arrProducto"]["No_Imagen_Item"];
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		
		$sUrlProductoImagen = '';
		if ( !empty($_POST['arrProducto']['No_Imagen_Item']) ){
			$sUrlProductoImagen = $url_image.$this->security->xss_clean($_POST['arrProducto']['No_Imagen_Item']);
		}

        settype($_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular'], "double");
		settype($_POST['arrProducto']['Ss_Precio_Ecommerce_Online'], "double");
		
		if( $_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular'] < 0.10 && $this->empresa->Nu_Proveedor_Dropshipping == 0 ) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar precio'));
			exit();
		}

		if($_POST['arrProducto']['Ss_Precio_Ecommerce_Online'] > 0.00 && $_POST['arrProducto']['Ss_Precio_Ecommerce_Online'] >= $_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular']) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'El precio de oferta no puede ser mayor o igual al precio'));
			exit();
		}

		$Nu_Activar_Precio_x_Mayor = ($_POST['arrProducto']['Nu_Activar_Precio_x_Mayor'] == "true" ? 1 : 0);

		$codigo = rand(12345678910,10987654321);
		if ($this->security->xss_clean($_POST['arrProducto']['EID_Producto']) != ''){
			$codigo = $this->security->xss_clean(strtoupper($_POST['arrProducto']['Nu_Codigo_Barra']));			
		}

		$data_producto = array(
			'ID_Empresa' => $this->empresa->ID_Empresa,
			'Nu_Tipo_Producto' => $this->security->xss_clean($_POST['arrProducto']['Nu_Tipo_Producto']),
			'ID_Tipo_Producto' => 2,
			'Nu_Codigo_Barra' => $codigo,
			'ID_Impuesto' => $this->security->xss_clean($_POST['arrProducto']['ID_Impuesto']),

			'No_Producto' => $_POST['arrProducto']['No_Producto'],
			'Ss_Precio_Ecommerce_Online_Regular' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular']),
			'Ss_Precio_Ecommerce_Online' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Ecommerce_Online']),

			'ID_Familia' => $this->security->xss_clean($_POST['arrProducto']['ID_Familia']),
			'ID_Unidad_Medida' => $this->security->xss_clean($_POST['arrProducto']['ID_Unidad_Medida']),
			'Nu_Activar_Item_Lae_Shop' => $this->security->xss_clean($_POST['arrProducto']['Nu_Estado']),
			'Txt_Producto' => $_POST['arrProducto']['Txt_Producto'],
			'Txt_Url_Video_Lae_Shop' => $_POST['arrProducto']['Txt_Url_Video_Lae_Shop'],

			'Nu_Activar_Precio_x_Mayor' => $Nu_Activar_Precio_x_Mayor,
			
			'Ss_Precio_Proveedor_Dropshipping' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Proveedor_Dropshipping']),
			'Ss_Precio_Vendedor_Dropshipping' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Vendedor_Dropshipping']),
			'Txt_Url_Recurso_Drive' => $this->security->xss_clean($_POST['arrProducto']['Txt_Url_Recurso_Drive']),
			'Nu_Estado_Variantes' => $this->security->xss_clean($_POST['arrProducto']['Nu_Estado_Variantes']),
			'Nu_Estado_Productos_Relacionados' => $this->security->xss_clean($_POST['arrProducto']['Nu_Estado_Productos_Relacionados']),
			'Nu_Tipo_Productos_Relacionados' => $this->security->xss_clean($_POST['arrProducto']['Nu_Tipo_Productos_Relacionados']),
			'Nu_Cantidad_Productos_Relacionados' => $this->security->xss_clean($_POST['arrProducto']['Nu_Cantidad_Productos_Relacionados'])
		);

		if ( !empty($_POST['arrProducto']['No_Imagen_Item']) ){
			$data_producto["No_Imagen_Item"]= $sUrlProductoImagen;
		}

		if ( !empty($_POST['arrProducto']['ID_Sub_Familia']) ){
			$data_producto = array_merge($data_producto, array("ID_Sub_Familia" => $_POST['arrProducto']['ID_Sub_Familia']));
		}

		if ( !empty($_POST['arrProducto']['ID_Marca']) ){
			$data_producto = array_merge($data_producto, array("ID_Marca" => $_POST['arrProducto']['ID_Marca']));
		}

		$arrProductoImagen = (isset($_POST['arrProductoImagen']) ? $_POST['arrProductoImagen'] : '');

		$arrProductoPrecioxMayor = (isset($_POST['arrProductoPrecioxMayor']) && !empty($_POST['arrProductoPrecioxMayor']) ? $_POST['arrProductoPrecioxMayor'] : '');

		if($this->security->xss_clean($_POST['arrProducto']['EID_Producto']) != '') {
			$resultado = $this->ProductoModel->actualizarProducto(array('ID_Empresa' => $this->security->xss_clean($_POST['arrProducto']['EID_Empresa']), 'ID_Producto' => $this->security->xss_clean($_POST['arrProducto']['EID_Producto'])), $data_producto, $this->security->xss_clean($_POST['arrProducto']['ENu_Codigo_Barra']), $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor);
		} else {
			$resultado = $this->ProductoModel->agregarProducto($data_producto, $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor, $arrProductoImagen);
		}

		if($resultado['status'] == 'success' && $_POST['arrProducto']['Nu_Estado_Variantes'] == 1) {
			//AQUI SE MANEJAN LAS VARIANTES
			$arrVariantes = (isset($_POST['arrVariantes']) ? $_POST['arrVariantes'] : []);
			$this->administracionVariantes($arrVariantes, $resultado['iIDItem']);

			//AQUI SE MANEJAN LOS PRODUCTOS HIJOS
			$arrProductosVarianteValores = (isset($_POST['arrProductosVarianteValores']) ? $_POST['arrProductosVarianteValores'] : []);
			$this->administracionProductosHijos($arrProductosVarianteValores, $resultado['iIDItem']);

			//AQUI SE MANEJAN LOS PRODUCTOS RELACIONADOS
			if($_POST['arrProducto']['Nu_Tipo_Productos_Relacionados'] == 2){
				$arrProductosRelacionados = (isset($_POST['arrProductosRelacionados']) ? $_POST['arrProductosRelacionados'] : []);
				$this->administracionProductosRelacionados($arrProductosRelacionados, $resultado['iIDItem']);
			}
		}
		echo json_encode($resultado);
	}
    
	public function eliminarProducto($ID_Empresa, $ID, $Nu_Codigo_Barra, $Nu_Compuesto=0, $sNombreImagenItem=''){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ProductoModel->eliminarProducto($this->security->xss_clean($ID_Empresa), $this->security->xss_clean($ID), $this->security->xss_clean($Nu_Codigo_Barra), $this->security->xss_clean($Nu_Compuesto), $this->security->xss_clean($sNombreImagenItem)));
	}
	
	public function cambiarEstadoTienda($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoModel->cambiarEstadoTienda($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoDestacado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoModel->cambiarEstadoDestacado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
  
	public function updActivarMasivamenteProductos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoModel->updActivarMasivamenteProductos($this->input->post()));
	}

	public function CrearCatalogo(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->ProductoModel->CrearCatalogo());
	}

	public function VerificarCatalogo(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->ProductoModel->VerificarCatalogo());
	}

	// ------------------ VARIANTES ---------------------- //

	public function getVariantes($ID_Producto) {
		$variantes = [];
		$variantes['variantes'] = $this->ProductoModel->getVariantes($ID_Producto, 1);	
		for ($i=0; $i < count($variantes['variantes']); $i++) {
			$variantes['variantes'][$i]->valores = $this->ProductoModel->getVarianteValores($variantes['variantes'][$i]->ID_Variante, 1);
		}
		$variantes['productosVarianteValores'] = $this->ProductoModel->getProductosHijos($ID_Producto, 1);
		echo json_encode($variantes);	
	}

	public function administracionVariantes($data_variantes, $ID_Producto) {
		$this->ProductoModel->inactivarVariante($ID_Producto);
		$this->inactivarVariantesValores($ID_Producto);
		for ($i=0; $i < count($data_variantes); $i++) { 
			if($data_variantes[$i]['ID_Variante'] > 0) {
				if($this->ProductoModel->actualizarVariante($data_variantes[$i]['No_Variante'], $data_variantes[$i]['ID_Variante'])) {
					$ID_Variante = $data_variantes[$i]['ID_Variante'];
				}
			}else {
				$ID_Variante = $this->ProductoModel->agregarVariante(['No_Variante' => $data_variantes[$i]['No_Variante'],'ID_Producto' => $ID_Producto]);
			}
			if($ID_Variante > 0){
				$this->administracionVarianteValores($data_variantes[$i]['valores'],$ID_Variante);
			}
		}
		$this->eliminarVariantesValoresInactivos($ID_Producto);
		$this->ProductoModel->eliminarVariantesInactivas($ID_Producto);
	}

	// ------------------ VARIANTES VALORES ---------------------- //

	function inactivarVariantesValores($ID_Producto) {
		$variantesValores = $this->ProductoModel->getVarianteValoresByIDProducto($ID_Producto, 1);
		if(count($variantesValores) > 0) {
			$IDVarianteValores = [];
			for ($i=0; $i < count($variantesValores); $i++) { 
				$IDVarianteValores[$i] = $variantesValores[$i]->ID_Variante_Valor;
			}
			if(count($IDVarianteValores) > 0) {
				$this->ProductoModel->actualizarEstadoVariantesValores($IDVarianteValores, 0);
			}
		}
	}

	public function administracionVarianteValores($data_variante_valores, $ID_Variante) {
		$campos = [];
		$IDVarianteValores = [];
		for ($i = 0; $i < count($data_variante_valores); $i++) { 
			if($data_variante_valores[$i]['ID_Variante_Valor'] > 0) {
				array_push($IDVarianteValores, $data_variante_valores[$i]['ID_Variante_Valor']);
			} else {
				array_push($campos, ['No_Variante_Valor' => $data_variante_valores[$i]['No_Variante_Valor'], 'ID_Variante' => $ID_Variante]);
			}
		}
		if(count($campos) > 0) {
			$this->ProductoModel->agregarVarianteValor($campos);
		}
		if(count($IDVarianteValores) > 0) {
			$this->ProductoModel->actualizarEstadoVariantesValores($IDVarianteValores, 1);
		}
	}

	function eliminarVariantesValoresInactivos($ID_Producto) {
		$variantesValores = $this->ProductoModel->getVarianteValoresByIDProducto($ID_Producto);
		if(count($variantesValores) > 0) {
			$IDVarianteValores = [];
			for ($i=0; $i < count($variantesValores); $i++) { 
				$IDVarianteValores[$i] = $variantesValores[$i]->ID_Variante_Valor;
			}
			if(count($IDVarianteValores) > 0) {
				$this->ProductoModel->eliminarVariantesValoresInactivos($IDVarianteValores);
			}
		}
	}

	// ------------------ PRODUCTOS HIJOS ---------------------- //

	public function administracionProductosHijos($data_productos_variante_valores, $ID_Producto_Padre) {
		$this->ProductoModel->inactivarProductosHijos($ID_Producto_Padre);
		if(count($data_productos_variante_valores) > 0) {
			$arrVarianteValores = $this->ProductoModel->getVarianteValoresByIDProducto($ID_Producto_Padre, 1);
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
			$arrUrlImagePath = explode('..', $path);
			$arrUrlImage = explode('/principal',base_url());
			$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
			$arrImagenesProductoPadre = $this->ProductoModel->getImagenes($ID_Producto_Padre, $url_image);
			for ($i = 0; $i < count($data_productos_variante_valores); $i++) {				
				$sUrlProductoImagen = '';
				$NoProductoImagen = '';

				settype($data_productos_variante_valores[$i]['Ss_Precio_Ecommerce_Online_Regular_Variante_Valores'], "double");
				settype($data_productos_variante_valores[$i]['Ss_Precio_Ecommerce_Online_Variante_Valores'], "double");
				
				if( $data_productos_variante_valores[$i]['Ss_Precio_Ecommerce_Online_Regular_Variante_Valores'] < 0.10 && $this->empresa->Nu_Proveedor_Dropshipping == 0 ) {
					echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar precio'));
					exit();
				}

				$codigo = rand(12345678910,10987654321);
				if ($this->security->xss_clean($data_productos_variante_valores[$i]['ID_Producto_Variante_Valores'])>0) {
					$codigo = $this->security->xss_clean(strtoupper($data_productos_variante_valores[$i]['Nu_Codigo_Barra_Variante_Valores']));			
				}

				$Nu_Activar_Precio_x_Mayor = ($_POST['arrProducto']['Nu_Activar_Precio_x_Mayor'] == "true" ? 1 : 0);

				$data_producto = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'Nu_Tipo_Producto' => 3,//3=Grupo de variantes
					'ID_Tipo_Producto' => 2,
					'Nu_Codigo_Barra' => $codigo,
					'ID_Impuesto' => $this->security->xss_clean($_POST['arrProducto']['ID_Impuesto']),
		
					'No_Producto' => $_POST['arrProducto']['No_Producto'] . ' | ' . $data_productos_variante_valores[$i]['No_Producto_Variante_Valores'],
					'Ss_Precio_Ecommerce_Online_Regular' => $this->security->xss_clean($data_productos_variante_valores[$i]['Ss_Precio_Ecommerce_Online_Regular_Variante_Valores']),
					'Ss_Precio_Ecommerce_Online' => 0,
		
					'ID_Familia' => $this->security->xss_clean($_POST['arrProducto']['ID_Familia']),
					'ID_Unidad_Medida' => $this->security->xss_clean($_POST['arrProducto']['ID_Unidad_Medida']),
					'Nu_Activar_Item_Lae_Shop' => $this->security->xss_clean($data_productos_variante_valores[$i]['Nu_Estado_Variante_Valores']),
					'Txt_Producto' => $_POST['arrProducto']['Txt_Producto'],
					'Txt_Url_Video_Lae_Shop' => $_POST['arrProducto']['Txt_Url_Video_Lae_Shop'],
		
					'Nu_Activar_Precio_x_Mayor' => $Nu_Activar_Precio_x_Mayor,
					
					'Ss_Precio_Proveedor_Dropshipping' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Proveedor_Dropshipping']),
					'Ss_Precio_Vendedor_Dropshipping' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Vendedor_Dropshipping']),
					'Txt_Url_Recurso_Drive' => $this->security->xss_clean($_POST['arrProducto']['Txt_Url_Recurso_Drive']),

					'ID_Producto_Padre' => $ID_Producto_Padre,
					'Nu_Estado_Producto_Hijo' => 1
				);

				$indice = 0;
				if ( $data_productos_variante_valores[$i]['Nu_Imagen_Producto_Variante_Valores'] >= 0) {
					$indice = $this->security->xss_clean($data_productos_variante_valores[$i]['Nu_Imagen_Producto_Variante_Valores']);
					$data_producto["No_Imagen_Item"] = NULL;
					if(count($arrImagenesProductoPadre) > $indice){
						$data_producto["No_Imagen_Item"] = $url_image.'/'.$arrImagenesProductoPadre[$indice]->No_Producto_Imagen;
					}
				}
		
				if ( !empty($_POST['arrProducto']['ID_Sub_Familia']) ){
					$data_producto = array_merge($data_producto, array("ID_Sub_Familia" => $_POST['arrProducto']['ID_Sub_Familia']));
				}
		
				if ( !empty($_POST['arrProducto']['ID_Marca']) ){
					$data_producto = array_merge($data_producto, array("ID_Marca" => $_POST['arrProducto']['ID_Marca']));
				}

				$arrProductoPrecioxMayor = (isset($_POST['arrProductoPrecioxMayor']) && !empty($_POST['arrProductoPrecioxMayor']) ? $_POST['arrProductoPrecioxMayor'] : '');				
				if($data_productos_variante_valores[$i]['ID_Producto_Variante_Valores'] > 0) {
					$resultado = $this->ProductoModel->actualizarProducto(array('ID_Empresa' => $this->security->xss_clean($_POST['arrProducto']['EID_Empresa']), 'ID_Producto' => $this->security->xss_clean($data_productos_variante_valores[$i]['ID_Producto_Variante_Valores'])), $data_producto, $this->security->xss_clean($data_productos_variante_valores[$i]['Nu_Codigo_Barra_Variante_Valores']), $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor);
				} else {
					$resultado = $this->ProductoModel->agregarProducto($data_producto, $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor, []);
					if($resultado['status'] == 'success') {
						$this->agregarImagenProductoHijo($arrImagenesProductoPadre, $resultado['iIDItem'], $indice);
						$this->agregarProductoVarianteValores($data_productos_variante_valores[$i]['No_Producto_Variante_Valores'], $arrVarianteValores, $resultado['iIDItem']);
					}
				}
			}
		}
		$this->eliminarProductosHijosInactivos($ID_Producto_Padre);
	}

	public function agregarImagenProductoHijo($arrImagenesProductoPadre, $IDProducto, $indice) {
		if(count($arrImagenesProductoPadre) > 0){
			$campos = [];
			for ($i = 0; $i < count($arrImagenesProductoPadre); $i++) { 
				$campos[$i]['No_Producto_Imagen'] = $arrImagenesProductoPadre[$i]->No_Producto_Imagen;
				$campos[$i]['ID_Producto'] = $IDProducto;
				$campos[$i]['Imagen_Tamano'] = $arrImagenesProductoPadre[$i]->Imagen_Tamano;
				$campos[$i]['ID_Predeterminado'] = $i == $indice ? 1 : 0;
			}
			if(count($campos) > 0){
				$this->ProductoModel->AgregarImagenProductoHijo($campos);
			}
		}
	}

	public function eliminarProductosHijosInactivos($ID_Producto_Padre) {
		$productos = $this->ProductoModel->getProductosHijos($ID_Producto_Padre);
		if(count($productos) > 0) {
			$IDProductos = [];
			for ($i=0; $i < count($productos); $i++) { 
				$IDProductos[$i] = $productos[$i]->ID_Producto;
			}
			$this->ProductoModel->eliminarProductosHijosInactivos($IDProductos);
			$this->ProductoModel->eliminarProductosVarianteValores($IDProductos);
		}		
	}

	// ------------------PRODUCTOS VARIANTES VALORES ---------------------- //

	public function agregarProductoVarianteValores($NoProducto, $arrVarianteValores, $IDProducto) {
		$arrNoVarianteValores = explode("|", $NoProducto);
		if(count($arrNoVarianteValores) > 0) {
			$campos = [];
			for($i = 0; $i < count($arrNoVarianteValores); $i++) {
				$NoProducto = strtolower(mb_convert_encoding($arrNoVarianteValores[$i], "UTF-8", "auto"));
				for ($j = 0; $j < count($arrVarianteValores); $j++) {
					$NoVarianteValor = strtolower(mb_convert_encoding($arrVarianteValores[$j]->No_Variante_Valor, "UTF-8", "auto"));
					if($NoProducto == $NoVarianteValor) {
						array_push($campos, ['ID_Producto' => $IDProducto, 'ID_Variante_Valor' => $arrVarianteValores[$j]->ID_Variante_Valor]);
					}
				}
			}
			if(count($campos) > 0) {
				$this->ProductoModel->agregarProductoVarianteValores($campos);
			}
		}
	}



	// ------------------PRODUCTOS RELACIONADOS ---------------------- //

	public function getProductosRelacionados($ID_Producto_Principal) {
		$productosRelacionados = $this->ProductoModel->getProductosRelacionados($ID_Producto_Principal);
		if(count($productosRelacionados) > 0) {
			$resultado = ['status' => 'success', 'result' => $productosRelacionados];
		} else {
			$resultado = ['status' => 'error', 'message' => 'no hay productos relacionados'];
		}
		echo json_encode($resultado);
	}

	public function administracionProductosRelacionados($arrProductosRelacionados, $ID_Producto_Principal){
		$this->ProductoModel->eliminarProductosRelacionados($ID_Producto_Principal);
		if(count($arrProductosRelacionados) > 0) {
			$arrData = [];
			for ($i=0; $i < count($arrProductosRelacionados); $i++) { 
				$arrData[$i][ 'ID_Producto_Principal' ] = $ID_Producto_Principal;
				$arrData[$i][ 'ID_Producto' ] = $arrProductosRelacionados[$i];
			}
			$this->ProductoModel->AgregarProductosRelacionados($arrData);
		}
	}

}