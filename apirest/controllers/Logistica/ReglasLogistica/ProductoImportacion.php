<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 900); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class ProductoImportacion extends CI_Controller {
	
	//private $upload_path = '../assets/images/productos/';
	//private $upload_path_table = '../assets/images/productos';
	
	private $upload_path = 'assets/images/productos/';
	private $upload_path_table = 'assets/images/productos';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/ProductoImportacionModel');
		$this->load->model('HelperDropshippingModel');
		$this->load->model('ConfiguracionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/ProductoImportacionView', array('NDI' => $this->empresa->Nu_Documento_Identidad));
			$this->load->view('footer', array("js_producto_importacion" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ProductoImportacionModel->get_datatables();
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
			
			$rows[] = numberFormat($row->Ss_Precio_Ecommerce_Online_Regular, 2, '.', ',');
			$rows[] = numberFormat($row->Ss_Precio_Ecommerce_Online, 2, '.', ',');
			
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

			$rows[] = !empty($image) ? $image : 'Sin imagen';
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarProducto(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Producto . '\', \'' . $row->Nu_Codigo_Barra . '\', \'' . 0 . '\', \'' . $action . '\', \'' . $row->No_Imagen_Item . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ProductoImportacionModel->count_all(),
	        'recordsFiltered' => $this->ProductoImportacionModel->count_filtered(),
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
				$config['max_size'] = 1024;//400 KB
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
					$id_imagen = $this->ProductoImportacionModel->AgregarImagen($data);
					$productosHijos = $this->ProductoImportacionModel->getProductosHijos($this->input->post('iIdProducto'), 1);
					if(count($productosHijos) > 0) {
						$arrImagenProductosHijos = [];
						for ($i = 0; $i < count($productosHijos); $i++) { 
							$arrImagenProductosHijos[$i]['No_Producto_Imagen'] = $UploadData["file_name"];
							$arrImagenProductosHijos[$i]['ID_Producto'] = $productosHijos[$i]->ID_Producto;
							$arrImagenProductosHijos[$i]['Imagen_Tamano'] = $UploadData["file_size"];
						}
						$this->ProductoImportacionModel->AgregarImagenProductoHijo($arrImagenProductosHijos);
					}
				}

				$arrUrlImagePath = explode('..', $path);
				$arrUrlImage = explode('/principal',base_url());
				//$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
				$url_image = $path;
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
				//$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];       
				$url_image = $path;
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
    //$arrUrlImagePath  = explode('..', $path);
    //$arrUrlImage    = explode('/principal',base_url());
    //$url_image      = $arrUrlImage[0] . $arrUrlImagePath[1];
	$url_image = $path;
    $rows         = $this->ProductoImportacionModel->getImagenes($this->input->post("iIdProducto"),$url_image);
    echo json_encode($rows);
  }

	public function removeFileImage(){
    $data = array(
		"ID_Producto"=>$this->input->post('iIdProducto'),
		"ID_Producto_Imagen"=>$this->input->post('IdImagen'),
		"Predeterminado"=>$this->input->post('Predeterminado')
	);
    $resultado = json_decode($this->ProductoImportacionModel->RemoverImagen($data));
	if($resultado->status == 'success') {
		$productosHijos = $this->ProductoImportacionModel->getProductosHijos($this->input->post('iIdProducto'), 1);
		if(count($productosHijos) > 0) {
			$arrIDProductosHijos = [];
			$arrIDProductosHijosPredeterminados = [];
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
			/*
			$arrUrlImagePath  = explode('..', $path);
			$arrUrlImage    = explode('/principal',base_url());
			$url_image      = $arrUrlImage[0] . $arrUrlImagePath[1];
			*/
			$url_image = $path;
			for ($i=0; $i < count($productosHijos); $i++) { 
				$arrIDProductosHijos[] = $productosHijos[$i]->ID_Producto;
				$ID_Predeterminado = 0;
				if($url_image.'/'.$resultado->No_Producto_Imagen == $productosHijos[$i]->No_Imagen_Item){
					$ID_Predeterminado = 1;
				}
				$this->ProductoImportacionModel->RemoverImagenProductoHijo($productosHijos[$i]->ID_Producto, $ID_Predeterminado, $resultado->No_Producto_Imagen);
			}
			
			
		}
	}
	//echo $this->ProductoImportacionModel->RemoverImagen($data);
	echo json_encode($resultado);
	}

	public function DefaultImagen(){

    $data = array(
          "ID_Producto"=>$this->input->post('iIdProducto'),
          "ID_Producto_Imagen"=>$this->input->post('IdImagen')
          );
    echo $this->ProductoImportacionModel->DefaultImagen($data);

  }
	
	public function ajax_edit($ID){
        echo json_encode($this->ProductoImportacionModel->get_by_id($this->security->xss_clean($ID)));
    }
	
	public function ajax_edit_enlace($ID){
        echo json_encode($this->ProductoImportacionModel->get_by_id_enlace($this->security->xss_clean($ID)));
    }
	
	public function ajax_edit_precios_por_mayor($ID){
        echo json_encode($this->ProductoImportacionModel->get_by_id_precios_x_mayor($this->security->xss_clean($ID)));
    }
    
	public function crudProducto(){
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
		/*
		$arrUrlImagePath = explode('..', $path);
		$arrUrlImage = explode('/principal',base_url());
		$url_image = $arrUrlImage[0] . $arrUrlImagePath[1]."/";
		*/
		$server_addr = $_SERVER['HTTP_HOST'];
		$base_url = (is_https() ? 'https' : 'http').'://'.$server_addr.'/';
		$url_image = $base_url . $path;
		//$_POST["arrProducto"]["No_Imagen_Item"]=$url_image.$_POST["arrProducto"]["No_Imagen_Item"];
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		
		$sUrlProductoImagen = '';
		if ( !empty($_POST['arrProducto']['No_Imagen_Item']) ){
			$sUrlProductoImagen = $url_image.$this->security->xss_clean($_POST['arrProducto']['No_Imagen_Item']);
		}

        settype($_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular'], "double");
		settype($_POST['arrProducto']['Ss_Precio_Ecommerce_Online'], "double");
		
		if( $_POST['arrProducto']['Ss_Precio_Ecommerce_Online_Regular'] < 0.10) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar precio'));
			exit();
		}

		if($_POST['arrProducto']['Ss_Precio_Ecommerce_Online'] < 0.10) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar precio'));
			exit();
		}

		if($_POST['arrProducto']['Qt_Unidad_Medida'] < 1) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar Unidad'));
			exit();
		}

		if($_POST['arrProducto']['Qt_Unidad_Medida_2'] < 1) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar Unidad'));
			exit();
		}

		if($_POST['arrProducto']['Qt_Pedido_Minimo_Proveedor'] < 1) {
			echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes agregar Pedido'));
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
			
			'Txt_Url_Recurso_Drive' => $this->security->xss_clean($_POST['arrProducto']['Txt_Url_Recurso_Drive']),
			'Nu_Estado_Variantes' => $this->security->xss_clean($_POST['arrProducto']['Nu_Estado_Variantes']),
			'Nu_Estado_Productos_Relacionados' => $this->security->xss_clean($_POST['arrProducto']['Nu_Estado_Productos_Relacionados']),
			'Nu_Tipo_Productos_Relacionados' => $this->security->xss_clean($_POST['arrProducto']['Nu_Tipo_Productos_Relacionados']),
			'Nu_Cantidad_Productos_Relacionados' => $this->security->xss_clean($_POST['arrProducto']['Nu_Cantidad_Productos_Relacionados']),
			'ID_Unidad_Medida_Precio' => $this->security->xss_clean($_POST['arrProducto']['ID_Unidad_Medida_Precio']),
			'Qt_Unidad_Medida' => $this->security->xss_clean($_POST['arrProducto']['Qt_Unidad_Medida']),
			'Qt_Unidad_Medida_2' => $this->security->xss_clean($_POST['arrProducto']['Qt_Unidad_Medida_2']),
			'Qt_Pedido_Minimo_Proveedor' => $this->security->xss_clean($_POST['arrProducto']['Qt_Pedido_Minimo_Proveedor']),
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
			$resultado = $this->ProductoImportacionModel->actualizarProducto(array('ID_Empresa' => $this->security->xss_clean($_POST['arrProducto']['EID_Empresa']), 'ID_Producto' => $this->security->xss_clean($_POST['arrProducto']['EID_Producto'])), $data_producto, $this->security->xss_clean($_POST['arrProducto']['ENu_Codigo_Barra']), $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor);
		} else {
			$resultado = $this->ProductoImportacionModel->agregarProducto($data_producto, $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor, $arrProductoImagen);
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
		echo json_encode($this->ProductoImportacionModel->eliminarProducto($this->security->xss_clean($ID_Empresa), $this->security->xss_clean($ID), $this->security->xss_clean($Nu_Codigo_Barra), $this->security->xss_clean($Nu_Compuesto), $this->security->xss_clean($sNombreImagenItem)));
	}
	
	public function cambiarEstadoTienda($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoImportacionModel->cambiarEstadoTienda($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoDestacado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoImportacionModel->cambiarEstadoDestacado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
  
	public function updActivarMasivamenteProductos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->ProductoImportacionModel->updActivarMasivamenteProductos($this->input->post()));
	}

	public function CrearCatalogo(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->ProductoImportacionModel->CrearCatalogo());
	}

	public function VerificarCatalogo(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
			echo json_encode($this->ProductoImportacionModel->VerificarCatalogo());
	}

	// ------------------ VARIANTES ---------------------- //

	public function getVariantes($ID_Producto) {
		$variantes = [];
		$variantes['variantes'] = $this->ProductoImportacionModel->getVariantes($ID_Producto, 1);	
		for ($i=0; $i < count($variantes['variantes']); $i++) {
			$variantes['variantes'][$i]->valores = $this->ProductoImportacionModel->getVarianteValores($variantes['variantes'][$i]->ID_Variante, 1);
		}
		$variantes['productosVarianteValores'] = $this->ProductoImportacionModel->getProductosHijos($ID_Producto, 1);
		echo json_encode($variantes);	
	}

	public function administracionVariantes($data_variantes, $ID_Producto) {
		$this->ProductoImportacionModel->inactivarVariante($ID_Producto);
		$this->inactivarVariantesValores($ID_Producto);
		for ($i=0; $i < count($data_variantes); $i++) { 
			if($data_variantes[$i]['ID_Variante'] > 0) {
				if($this->ProductoImportacionModel->actualizarVariante($data_variantes[$i]['No_Variante'], $data_variantes[$i]['ID_Variante'])) {
					$ID_Variante = $data_variantes[$i]['ID_Variante'];
				}
			}else {
				$ID_Variante = $this->ProductoImportacionModel->agregarVariante(['No_Variante' => $data_variantes[$i]['No_Variante'],'ID_Producto' => $ID_Producto]);
			}
			if($ID_Variante > 0){
				$this->administracionVarianteValores($data_variantes[$i]['valores'],$ID_Variante);
			}
		}
		$this->eliminarVariantesValoresInactivos($ID_Producto);
		$this->ProductoImportacionModel->eliminarVariantesInactivas($ID_Producto);
	}

	// ------------------ VARIANTES VALORES ---------------------- //

	function inactivarVariantesValores($ID_Producto) {
		$variantesValores = $this->ProductoImportacionModel->getVarianteValoresByIDProducto($ID_Producto, 1);
		if(count($variantesValores) > 0) {
			$IDVarianteValores = [];
			for ($i=0; $i < count($variantesValores); $i++) { 
				$IDVarianteValores[$i] = $variantesValores[$i]->ID_Variante_Valor;
			}
			if(count($IDVarianteValores) > 0) {
				$this->ProductoImportacionModel->actualizarEstadoVariantesValores($IDVarianteValores, 0);
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
			$this->ProductoImportacionModel->agregarVarianteValor($campos);
		}
		if(count($IDVarianteValores) > 0) {
			$this->ProductoImportacionModel->actualizarEstadoVariantesValores($IDVarianteValores, 1);
		}
	}

	function eliminarVariantesValoresInactivos($ID_Producto) {
		$variantesValores = $this->ProductoImportacionModel->getVarianteValoresByIDProducto($ID_Producto);
		if(count($variantesValores) > 0) {
			$IDVarianteValores = [];
			for ($i=0; $i < count($variantesValores); $i++) { 
				$IDVarianteValores[$i] = $variantesValores[$i]->ID_Variante_Valor;
			}
			if(count($IDVarianteValores) > 0) {
				$this->ProductoImportacionModel->eliminarVariantesValoresInactivos($IDVarianteValores);
			}
		}
	}

	// ------------------ PRODUCTOS HIJOS ---------------------- //

	public function administracionProductosHijos($data_productos_variante_valores, $ID_Producto_Padre) {
		$this->ProductoImportacionModel->inactivarProductosHijos($ID_Producto_Padre);
		if(count($data_productos_variante_valores) > 0) {
			$arrVarianteValores = $this->ProductoImportacionModel->getVarianteValoresByIDProducto($ID_Producto_Padre, 1);
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
			/*
			$arrUrlImagePath = explode('..', $path);
			$arrUrlImage = explode('/principal',base_url());
			$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
			*/
			$url_image = $path;
			$arrImagenesProductoPadre = $this->ProductoImportacionModel->getImagenes($ID_Producto_Padre, $url_image);
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
					$resultado = $this->ProductoImportacionModel->actualizarProducto(array('ID_Empresa' => $this->security->xss_clean($_POST['arrProducto']['EID_Empresa']), 'ID_Producto' => $this->security->xss_clean($data_productos_variante_valores[$i]['ID_Producto_Variante_Valores'])), $data_producto, $this->security->xss_clean($data_productos_variante_valores[$i]['Nu_Codigo_Barra_Variante_Valores']), $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor);
				} else {
					$resultado = $this->ProductoImportacionModel->agregarProducto($data_producto, $_POST['arrProductoEnlace'], $arrProductoPrecioxMayor, []);
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
				$this->ProductoImportacionModel->AgregarImagenProductoHijo($campos);
			}
		}
	}

	public function eliminarProductosHijosInactivos($ID_Producto_Padre) {
		$productos = $this->ProductoImportacionModel->getProductosHijos($ID_Producto_Padre);
		if(count($productos) > 0) {
			$IDProductos = [];
			for ($i=0; $i < count($productos); $i++) { 
				$IDProductos[$i] = $productos[$i]->ID_Producto;
			}
			$this->ProductoImportacionModel->eliminarProductosHijosInactivos($IDProductos);
			$this->ProductoImportacionModel->eliminarProductosVarianteValores($IDProductos);
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
				$this->ProductoImportacionModel->agregarProductoVarianteValores($campos);
			}
		}
	}



	// ------------------PRODUCTOS RELACIONADOS ---------------------- //

	public function getProductosRelacionados($ID_Producto_Principal) {
		$productosRelacionados = $this->ProductoImportacionModel->getProductosRelacionados($ID_Producto_Principal);
		if(count($productosRelacionados) > 0) {
			$resultado = ['status' => 'success', 'result' => $productosRelacionados];
		} else {
			$resultado = ['status' => 'error', 'message' => 'no hay productos relacionados'];
		}
		echo json_encode($resultado);
	}

	public function administracionProductosRelacionados($arrProductosRelacionados, $ID_Producto_Principal){
		$this->ProductoImportacionModel->eliminarProductosRelacionados($ID_Producto_Principal);
		if(count($arrProductosRelacionados) > 0) {
			$arrData = [];
			for ($i=0; $i < count($arrProductosRelacionados); $i++) { 
				$arrData[$i][ 'ID_Producto_Principal' ] = $ID_Producto_Principal;
				$arrData[$i][ 'ID_Producto' ] = $arrProductosRelacionados[$i];
			}
			$this->ProductoImportacionModel->AgregarProductosRelacionados($arrData);
		}
	}

}