<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class ProductosProveedoresDropshippingController extends CI_Controller {
	
	private $upload_path  		= '../assets/images/productos/';
	private $upload_path_ 		= '/var/www/vhosts/ecxpresslae/public_html/assets/images/productos/';
	private $upload_path_table 	= '../assets/images/productos';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Proveedores/ProductoProveedorModel');
		$this->load->model('HelperModel');
		//$this->load->model('ConfiguracionModel');
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
		                $this->ProductoProveedorModel->setBatchImport($arrProducto);
		                $arrResponseProducto = $this->ProductoProveedorModel->importData();
                	} else {
	            		unlink($destino);
	                	unset($arrProducto);
                	
                		$sStatus = 'error-sindatos';
						redirect('Proveedores/ProductosDropshippingController/listar/' . $sStatus . '/' . 0 . '/Error sin datos');
                		exit();
                	}
                	
            		unlink($destino);
                	unset($arrProducto);
                	
					if ($arrResponseProducto['status'] == 'success') {
                		$sStatus = 'success';
						redirect('Proveedores/ProductosDropshippingController/listar/' . $sStatus . '/' . $iCantidadNoProcesados . '/' . $arrResponseProducto['message']);
                	} else {
                		$sStatus = 'error-bd';
						redirect('Proveedores/ProductosDropshippingController/listar/' . $sStatus . '/' . 0 . '/' . $arrResponseProducto['message']);
                	}
		        } else {
        	        $sStatus = 'error-archivo_no_existe';
					redirect('Proveedores/ProductosDropshippingController/listar/' . $sStatus . '/' . 0 . '/No existe archivo');
		        }
		    } else {
		        $sStatus = 'error-copiar_archivo';
				redirect('Proveedores/ProductosDropshippingController/listar/' . $sStatus . '/' . 0 . '/Error al copiar archivo');
		    }
		}
	}

	public function listar($sStatus='', $iCantidadNoProcesados='', $sMessageErrorBD=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$this->load->view('header', array("arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,));
			//$this->load->view('header', array("arrUrlDropshipping" => $arrUrlDropshipping));
			$this->load->view('Proveedores/ProductoProveedorView', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados, 'sMessageErrorBD' => $sMessageErrorBD));
			$this->load->view('footer', array("js_productos_proveedores_tienda_virtual" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ProductoProveedorModel->get_datatables();
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

            $no++;
            $rows = array();
			
			$rows[] = $row->No_Empresa;
			$rows[] = $row->No_Almacen;
			//$rows[] = $row->Txt_Direccion_Almacen;
			
			$rows[] = !empty($image) ? $image : 'Sin imagen';
			
			//realizar un query para obtener el stock de cada proveedor
			/*
			$arrParamsProductoProveedorDropshipping = array(
				'ID_Empresa' => $row->ID_Empresa,
				'ID_Producto' => $row->ID_Producto
			);
            $fStockActual = $this->ProductoProveedorModel->obtenerStockActualProveedorDropshipping($arrParamsProductoProveedorDropshipping);
			*/
			//$rows[] = $fStockActual->ID_Stock_Producto;
            //$rows[] = numberFormat($fStockActual->Qt_Producto, 0, '.', '');

			$rows[] = $row->ID_Stock_Producto;
			$rows[] = $row->No_Producto;
			$rows[] = numberFormat($row->Qt_Producto, 0, '.', '');

			$rows[] = 'S/ ' . numberFormat($row->Ss_Precio_Proveedor_Dropshipping, 2, '.', ',');
			$rows[] = 'S/ ' . numberFormat($row->Ss_Precio_Vendedor_Dropshipping, 2, '.', ',');
			
			$rows[] = (!empty($row->Txt_Url_Recurso_Drive) ? '<a href="' . $row->Txt_Url_Recurso_Drive . '" style="" class="btn btn-link btn-block" target="_blank" rel="noopener noreferrer"><span class="fa fa-link"></span> Recurso Drive</a>' : '');
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verProducto(\'' . $row->ID_Producto . '\', \'' . $row->No_Imagen_Item . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-eye" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verProducto(\'' . $row->ID_Producto . '\', \'' . $row->No_Imagen_Item . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-plus-square" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ProductoProveedorModel->count_all(),
	        'recordsFiltered' => $this->ProductoProveedorModel->count_filtered(),
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
          $data = array('No_Producto_Imagen' => $UploadData["file_name"],"ID_Producto"=>$this->input->post('iIdProducto'),"Imagen_Tamano"=>$UploadData["file_size"]);
      
		  $id_imagen=0;
          if( strlen($this->input->post('iIdProducto')>0))
          	$id_imagen = $this->ProductoProveedorModel->AgregarImagen($data);

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
		//obtener nuemero de documnto de empresa del producto del proveedor porque se jalara de ese lugar la info
		$arrEmpresaProductoProveedor = $this->ProductoProveedorModel->getEmpresaProductoProveeedor($this->input->post("iIdProducto"));
		$path         		= $this->upload_path . $arrEmpresaProductoProveedor->Nu_Documento_Identidad;
		$arrUrlImagePath  	= explode('..', $path);
		$arrUrlImage    	= explode('/principal',base_url());
		$url_image      	= $arrUrlImage[0] . $arrUrlImagePath[1];
		$rows         		= $this->ProductoProveedorModel->getImagenes($this->input->post("iIdProducto"),$url_image);
		echo json_encode($rows);
		exit();
  	}

  	public function removeFileImage(){
    	$data = array(
			"ID_Producto"=>$this->input->post('iIdProducto'),
			"ID_Producto_Imagen"=>$this->input->post('IdImagen'),
			"Predeterminado"=>$this->input->post('Predeterminado')
		);    
    	echo $this->ProductoProveedorModel->RemoverImagen($data);
		exit();
  	}

  	public function DefaultImagen(){
    	$data = array(
		"ID_Producto"=>$this->input->post('iIdProducto'),
		"ID_Producto_Imagen"=>$this->input->post('IdImagen')
		);
    	echo $this->ProductoProveedorModel->DefaultImagen($data);
  	}
	
	public function ajax_edit($ID){
        echo json_encode($this->ProductoProveedorModel->get_by_id($this->security->xss_clean($ID)));
    }
	
	public function ajax_edit_enlace($ID){
        echo json_encode($this->ProductoProveedorModel->get_by_id_enlace($this->security->xss_clean($ID)));
    }
	
	public function ajax_edit_precios_por_mayor($ID){
        echo json_encode($this->ProductoProveedorModel->get_by_id_precios_x_mayor($this->security->xss_clean($ID)));
    }

    function ImportacionGaleria($ID_Producto_Proveedor,$ID_Producto_Vendedor){

    	$arrEmpresaProductoProveedor 	= $this->ProductoProveedorModel->getEmpresaProductoProveeedor($ID_Producto_Proveedor);
    	$GaleriaProveedor 				= $this->ProductoProveedorModel->getGaleriaProveedor($ID_Producto_Proveedor);
    	$pathProveedor					= $this->upload_path_ . $arrEmpresaProductoProveedor->Nu_Documento_Identidad;
    	$pathVendedor 					= $this->upload_path_ . $this->empresa->Nu_Documento_Identidad;
		$pathVendedor_Local = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
    	
    	if (!is_dir($pathVendedor)) {
		    mkdir($pathVendedor, 0777, true);
		    if (!is_dir($pathVendedor)) 
		        throw new Exception("No se pudo crear el directorio.");
		}
		$data = [];

		for($i=0;$i<count($GaleriaProveedor);$i++){
			$Imagen_Predeterminado =  $GaleriaProveedor[0]->No_Producto_Imagen;
			if($GaleriaProveedor[$i]->ID_Predeterminado==1)
				$Imagen_Predeterminado = $GaleriaProveedor[$i]->No_Producto_Imagen;

			array_push($data, array(
				"No_Producto_Imagen"=>$GaleriaProveedor[$i]->No_Producto_Imagen,
				"ID_Producto"=>$ID_Producto_Vendedor,
				"ID_Estatus"=>$GaleriaProveedor[$i]->ID_Estatus,
				"ID_Predeterminado"=>$GaleriaProveedor[$i]->ID_Predeterminado
				)
			);
		}

		$this->db->insert_batch('producto_imagen', $data);

		for($i=0;$i<count($GaleriaProveedor);$i++){
			$path_imagen_Proveedor = $pathProveedor."/".$GaleriaProveedor[$i]->No_Producto_Imagen;
			$path_imagen_Vendedor  = $pathVendedor."/".$GaleriaProveedor[$i]->No_Producto_Imagen;
			copy($path_imagen_Proveedor, $path_imagen_Vendedor);
		}

		//SET IMAGE CARPETA
		$arrUrlImagePathVendedor  	= explode('..', $pathVendedor_Local);
		$arrUrlImageVendedor    	= explode('/principal',base_url());
		$url_image      			= $arrUrlImageVendedor[0] . $arrUrlImagePathVendedor[1];
		$Imagen_Predeterminado = $url_image . "/" . $Imagen_Predeterminado;

		$this->db->flush_cache();
		$this->db->set('No_Imagen_Item', $Imagen_Predeterminado);
		$this->db->where("ID_Producto",$ID_Producto_Vendedor);
		$this->db->update('producto');
    }
    
	//METODO IMPORTAR PRODUCTO
	public function crudProducto(){
		$sUrlProductoImagen = '';
		/*
		$arrUrlImagePath = explode('..', $path);
		$arrUrlImage = explode('/principal',base_url());
		$url_image = $arrUrlImage[0] . $arrUrlImagePath[1]."/";
		
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		
		$sUrlProductoImagen = '';
		if ( !empty($_POST['arrProducto']['No_Imagen_Item']) ){
			$sUrlProductoImagen = $url_image.$this->security->xss_clean($_POST['arrProducto']['No_Imagen_Item']);
		}
		*/

        settype($_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular'], "double");
		settype($_POST['arrProducto']['Ss_Precio_Ecommerce_Online'], "double");

		if( $_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular'] < 0.10 ) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar precio de tu tienda'));
			exit();
		}

		$Nu_Activar_Precio_x_Mayor = ($_POST['arrProducto']['Nu_Activar_Precio_x_Mayor'] == "true" ? 1 : 0);

		$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTVWXYZ';
		$sCodigoBarraItem = substr(str_shuffle($permitted_chars), 0, 13);

		$data_producto = array(
			'ID_Empresa' => $this->empresa->ID_Empresa,
			'Nu_Tipo_Producto' => $this->security->xss_clean($_POST['arrProducto']['Nu_Tipo_Producto']),
			'ID_Tipo_Producto' => 2,
			'Nu_Codigo_Barra' => $sCodigoBarraItem,
			'ID_Impuesto' => $this->security->xss_clean($_POST['arrProducto']['ID_Impuesto']),

			'No_Producto' => $_POST['arrProducto']['No_Producto'],
			'Ss_Precio_Ecommerce_Online_Regular' => $this->security->xss_clean($_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular']),
			'Ss_Precio_Ecommerce_Online' => 0,

			'ID_Familia' => $this->security->xss_clean($_POST['arrProducto']['ID_Familia']),
			'ID_Unidad_Medida' => $this->security->xss_clean($_POST['arrProducto']['ID_Unidad_Medida']),
			'Nu_Activar_Item_Lae_Shop' => $this->security->xss_clean($_POST['arrProducto']['Nu_Estado']),
			'Txt_Producto' => $_POST['arrProducto']['Txt_Producto'],

			'Nu_Activar_Precio_x_Mayor' => 0,

			'Ss_Precio_Proveedor_Dropshipping' => 0,
			'Ss_Precio_Vendedor_Dropshipping' => 0,
			'ID_Producto_Relacion_Producto_Dropshipping' => $this->security->xss_clean($_POST['arrProducto']['EID_Producto']),
			'Txt_Url_Video_Lae_Shop' => $_POST['arrProducto']['Txt_Url_Video_Lae_Shop']
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

		$arrCrudProducto = $this->ProductoProveedorModel->agregarProducto($data_producto, $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor, $arrProductoImagen);

		if ($arrCrudProducto['status']=='success') {
			//RUTA DESITNO VENDEDOR QUE IMPORTA
			$postGaleria 			= $this->input->post();
			// ID PRODUCTO DEL PROVEEDOR
			$ID_Producto_Proveedor 	= $postGaleria["arrProducto"]["EID_Producto"];
			///ID DEL PRODUCTO NUEVO QUE SE CREO DEBE IR AQUI
			//$ID_Producto_Vendedor   = "546266";//ID_PRODUCTO_NUEVO
			$ID_Producto_Vendedor = $arrCrudProducto['iIDItem'];//ID_PRODUCTO_NUEVO
			
			$this->ImportacionGaleria($ID_Producto_Proveedor,$ID_Producto_Vendedor);
			
			echo json_encode($arrCrudProducto);
			exit();
		} else {
			echo json_encode($arrCrudProducto);
		}

		//echo json_encode($this->ProductoProveedorModel->agregarProducto($data_producto, $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor, $arrProductoImagen));
	}
    
	public function eliminarProducto($ID_Empresa, $ID, $Nu_Codigo_Barra, $Nu_Compuesto, $sNombreImagenItem=''){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ProductoProveedorModel->eliminarProducto($this->security->xss_clean($ID_Empresa), $this->security->xss_clean($ID), $this->security->xss_clean($Nu_Codigo_Barra), $this->security->xss_clean($Nu_Compuesto), $this->security->xss_clean($sNombreImagenItem)));
	}
	
	public function cambiarEstadoTienda($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoProveedorModel->cambiarEstadoTienda($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoDestacado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoProveedorModel->cambiarEstadoDestacado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
  
	public function updActivarMasivamenteProductos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoProveedorModel->updActivarMasivamenteProductos($this->input->post()));
	}
}