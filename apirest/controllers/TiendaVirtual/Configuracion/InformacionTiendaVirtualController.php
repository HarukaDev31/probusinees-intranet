<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InformacionTiendaVirtualController extends CI_Controller {
	private $upload_path = '../assets/images/logos_tienda/';
	private $upload_path_table = '../assets/images/logos_tienda';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->library('ConfiguracionTienda',NULL,"ConfiguracionTienda");
		$this->load->model('TiendaVirtual/Configuracion/SistemaModel');
		$this->load->model('ConfiguracionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrData = $this->SistemaModel->get_datatables();
			//array_debug($arrData);
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('TiendaVirtual/Configuracion/SistemaView', array(
				'ID' => $arrData[0]->ID_Configuracion,
				'No_Imagen_Logo_Empresa' => $arrData[0]->Txt_Url_Logo_Lae_Shop,
				'Nu_Version_Imagen' => $arrData[0]->Nu_Version_Imagen,
			));
			$this->load->view('footer', array("js_configuracion_tienda_virtual" => true));
		}
	}

	public function ValidarDominioTienda(){
		
		echo $this->SistemaModel->ValidarDominioTienda();

	}
	
	public function ajax_list(){
		$arrData = $this->SistemaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verSistema(\'' . $row->ID_Configuracion . '\', \'' . $row->Txt_Url_Logo_Lae_Shop . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';

			$arrImgProducto = explode('logos_tienda',$row->Txt_Url_Logo_Lae_Shop);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->Txt_Url_Logo_Lae_Shop)){
				if ( file_exists($sPathImgProducto) ) {
					$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					$image = '<img class="img-fluid" data-url_img="' . $row->Txt_Url_Logo_Lae_Shop . '" src="' . $base64 . '" title="' . $row->No_Tienda_Lae_Shop . '" alt="' . $row->No_Tienda_Lae_Shop . '" style="cursor:pointer; max-height:40px;" />';
				}
			}
			
			$rows[] = $image;
            $rows[] = $row->No_Tienda_Lae_Shop;
            $rows[] = $row->Nu_Celular_Lae_Shop;
			$rows[] = $row->Nu_Celular_Whatsapp_Lae_Shop;
			$rows[] = $row->Txt_Email_Lae_Shop;
			$rows[] = $row->Txt_Descripcion_Lae_Shop;

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->SistemaModel->count_all(),
	        'recordsFiltered' => $this->SistemaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
    public function uploadOnly(){
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
				$config['allowed_types'] = 'png|jpg|jpeg';
				$config['max_size'] = 400;//400 KB
				$config['file_name'] = cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen;

				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén',
						'sClassModal' => 'modal-danger',
					);
				} else {
					$data = array('Nu_Version_Imagen' => $this->input->post('iVersionImage'));
					$where = array('ID_Configuracion' => $this->input->post('iIdConfiguracion') );
					$this->SistemaModel->actualizarVersionImagen($where, $data);

					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenCategoria' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
						'sNombreImagenCategoriaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
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
					'sNombreImagenCategoria' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
					'sNombreImagenCategoriaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
				);
			}
    	}
    	echo json_encode($arrResponse);
	}

    public function removeFileImage(){
    	$nameFileImage = $this->input->post('nameFileImage');
		if ( strpos($nameFileImage,"/") > 0 ) {
			if ( file_exists($nameFileImage) ){
				unlink($nameFileImage);
				$data = array('Txt_Url_Logo_Lae_Shop' => '');
				$where = array('ID_Configuracion' => $this->input->post('iIdConfiguracion') );
				echo json_encode($this->SistemaModel->actualizarVersionImagen($where, $data));
			} else {
				echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Imagen no encontrada al eliminar 1'));
			}
		} else {
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/';
			if ( $nameFileImage && file_exists($path . $nameFileImage) ){
				unlink($path . $nameFileImage);
				$data = array('Txt_Url_Logo_Lae_Shop' => '');
				$where = array('ID_Configuracion' => $this->input->post('iIdProducto') );
				echo json_encode($this->SistemaModel->actualizarVersionImagen($where, $data));
			} else {
				echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Imagen no encontrada al eliminar 2'));
			}
		}
    }

	public function get_image(){
		$sUrlImage = $this->input->post('sUrlImage');
		$arrUrlImage = explode($this->empresa->Nu_Documento_Identidad, $sUrlImage);
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . $arrUrlImage[1];
    	$arrfilesImages = array();
		if ( file_exists($path) ){
			$arrfilesImages[] = array(
				'name' => $path,
				'size' => filesize($path),
			);
		}

		echo json_encode($arrfilesImages);
	}
	
	public function ajax_edit($ID){
        echo json_encode($this->SistemaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudSistema(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		$No_Html_Color_Lae_Shop = explode('#', $this->input->post('No_Html_Color_Lae_Shop'));
		$No_Html_Color_Lae_Shop = strtoupper($No_Html_Color_Lae_Shop[1]);

		$Nu_Celular_Lae_Shop=$this->input->post('Nu_Celular_Lae_Shop');
		$Nu_Celular_Whatsapp_Lae_Shop=$this->input->post('Nu_Celular_Whatsapp_Lae_Shop');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'Txt_Url_Logo_Lae_Shop' => $this->input->post('Txt_Url_Logo_Lae_Shop'),
			'No_Tienda_Lae_Shop' => $this->input->post('No_Tienda_Lae_Shop'),
			'Nu_Celular_Lae_Shop' => $Nu_Celular_Lae_Shop,
			'Nu_Celular_Whatsapp_Lae_Shop' => $Nu_Celular_Whatsapp_Lae_Shop,
			'Txt_Email_Lae_Shop' => $this->input->post('Txt_Email_Lae_Shop'),
			'Txt_Descripcion_Lae_Shop' => $this->input->post('Txt_Descripcion_Lae_Shop'),
			'Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop' => $this->input->post('Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop'),
			'Txt_Facebook_Pixel_Lae_Shop' => $this->input->post('Txt_Facebook_Pixel_Lae_Shop'),
			'Txt_Tiktok_Pixel_Lae_Shop' => $this->input->post('Txt_Tiktok_Pixel_Lae_Shop'),
			'Txt_Google_Analytics_Lae_Shop' => $this->input->post('Txt_Google_Analytics_Lae_Shop'),
			'Nu_Validar_Stock_Laeshop' => $this->input->post('Nu_Validar_Stock_Laeshop'),
			'No_Html_Color_Lae_Shop' => $No_Html_Color_Lae_Shop,
			'Nu_Activar_Precio_Centralizado_Laeshop' => $this->input->post('Nu_Activar_Precio_Centralizado_Laeshop'),
			'Nu_Activar_Emitir_Factura_Laeshop' => $this->input->post('Nu_Activar_Emitir_Factura_Laeshop'),
			'Nu_Activar_Formulario_Tienda_Virtual_Ver_Item' => $this->input->post('Nu_Activar_Formulario_Tienda_Virtual_Ver_Item'),
			'Nu_Codigo_Pais_Celular_Lae_Shop' => $this->input->post('Nu_Codigo_Pais_Celular_Lae_Shop'),
			'Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop' => $this->input->post('Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop'),
			'Txt_Page_Landing_Terminos' => $this->input->post('Txt_Page_Landing_Terminos'),
			'Txt_Page_Landing_Politica' => $this->input->post('Txt_Page_Landing_Politica'),
			'Txt_Page_Landing_Devolucion' => $this->input->post('Txt_Page_Landing_Devolucion'),
			'Txt_Page_Landing_Envio' => $this->input->post('Txt_Page_Landing_Envio'),
			'No_Html_Color_HSV_Lae_Shop' => $this->input->post('No_Html_Color_HSV_Lae_Shop'),
			'Txt_Google_Shopping_Dominio_Lae_Shop' => $this->input->post('Txt_Google_Shopping_Dominio_Lae_Shop'),
			'Nu_Tipo_Gestion_Pedido_Tienda_Virtual' => $this->input->post('Nu_Tipo_Gestion_Pedido_Tienda_Virtual'),
			'Nu_Estado_Tienda_Whatsapp_Ver_Producto' => $this->input->post('Nu_Estado_Tienda_Whatsapp_Ver_Producto'),
			'Nu_Tipo_Fomulario_Item_Tienda' => $this->input->post('radio-tipoFormularioItem'),
			'Txt_Titulo_Cabecera_Fomulario_Item_Tienda' => $this->input->post('Txt_Titulo_Cabecera_Fomulario_Item_Tienda'),
			'Txt_Boton_Fomulario_Item_Tienda' => $this->input->post('Txt_Boton_Fomulario_Item_Tienda'),
			'Txt_Titulo_Pie_Pagina_Fomulario_Item_Tienda' => $this->input->post('Txt_Titulo_Pie_Pagina_Fomulario_Item_Tienda'),
			'Nu_Estado_Contador_Item_Tienda' => $this->input->post('Nu_Estado_Contador_Item_Tienda'),
			'Nu_Tiempo_Minutos_Contador_Item_Tienda' => $this->input->post('Nu_Tiempo_Minutos_Contador_Item_Tienda')
		);

		//actulizar integracion de shopify
		if(!empty($this->input->post('No_Dominio_Externo')) && !empty($this->input->post('Txt_Llave_Externa'))){
			$arrEmpresaPost = array(
				"No_Dominio_Externo" => $this->input->post('No_Dominio_Externo'),
				"Txt_Llave_Externa" => $this->input->post('Txt_Llave_Externa')
			);
			$this->SistemaModel->actualizarEmpresaShopify(array('ID_Empresa' => $this->input->post('ID_Empresa')), $arrEmpresaPost);
		}

		echo json_encode($this->SistemaModel->actualizarSistema(array('ID_Configuracion' => $this->input->post('EID_Configuracion')), $data));
	}

	function validarDominioAsociado(){

		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		$domain = $this->input->post('No_Dominio_Tienda_Virtual');
		$ip = '94.23.202.99';
		
		// Obtener los registros DNS del dominio
		$records = dns_get_record($domain, DNS_A);
		
		// Buscar la dirección IP en los registros DNS
		$ip_found = false;
		foreach ($records as $record) {
			if ($record['type'] == 'A' && $record['ip'] == $ip) {
				$ip_found = true;
				break;
			}
		}
		
		// Imprimir el resultado
		if ($ip_found) {
			$resultado = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Dominio Asociado');
		} else {
			$resultado = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Dominio no Asociado');
		}
		echo json_encode($resultado);
	}

	//POST FORMULARIO PARA SUBDOMINIO Y DOMINIO
	//LOS CAMPOS QUE LLEGAN A ESTE METODO SON
	//EID_Configuracion_Dominio = id del registro en tabla (este campo realmente se llama ID_Configuracion)
	//INT_Dominio_Asociado = estatus de dominio asociado (1: asociado, 0: no asociado)
	//ID_Empresa = id de la empresa
	//ID_Subdominio_Tienda_Virtual = id de la tabla subdominio_tienda_virtual
	//Nu_Tipo_Tienda = tipo de registro (1: subdominio, 3: dominio)
	//No_Subdominio_Tienda_Virtual = campo con el valor del subdominio
	//No_Dominio_Tienda_Virtual = campo con el valor del dominio
	public function crudSistemaDominio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		if($this->input->post('Nu_Tipo_Tienda') == 1) { //SUBDOMINIO
			$campo = 'No_Subdominio_Tienda_Virtual';
			$valor = $this->input->post('No_Subdominio_Tienda_Virtual');
			if ( $valor == strtolower($this->input->post('hidden-nombre_subdominio')) ){
				echo json_encode(array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No cambiaste de nombre de subdominio para guardar'));
				exit();
			}
		} elseif ($this->input->post('Nu_Tipo_Tienda') == 3) { //DOMINIO
			$campo = 'No_Dominio_Tienda_Virtual';
			$valor = $this->input->post('No_Dominio_Tienda_Virtual');
			if ( $valor == strtolower($this->input->post('hidden-nombre_dominio')) ){
				echo json_encode(array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No cambiaste de nombre de dominio para guardar'));
				exit();
			}
		}
		$valor = trim($valor);
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Subdominio_Tienda_Virtual' => $this->input->post('ID_Subdominio_Tienda_Virtual'),
			'Nu_Tipo_Tienda' => $this->input->post('Nu_Tipo_Tienda'),
			$campo => strtolower($valor),
			'hidden-tipo_dominio' => $this->input->post('hidden-tipo_dominio'),
			'hidden-nombre_subdominio' => strtolower($this->input->post('hidden-nombre_subdominio')),
			'hidden-nombre_dominio' => strtolower($this->input->post('hidden-nombre_dominio'))
		);
		//EL FRONTEND YA ESTA PREPARADO PARA RECIBIR ESTA RESPUESTA
		echo json_encode($this->SistemaModel->actualizarSistemaDominio(array('ID_Configuracion' => $this->input->post('EID_Configuracion_Dominio')), $data));
	}
	
	public function importarPaginas($ID, $sTipo){
        echo json_encode($this->SistemaModel->importarPaginas($this->security->xss_clean($ID), $this->security->xss_clean($sTipo)));
    }

	public function getAlmacenPrincipal() {
		$row = $this->SistemaModel->getAlmacenPrincipal();
		if($row){
			$resultado = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Tienda Activada');
		} else {
			$resultado = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'La Tienda aun no está Activada');
		}
		echo json_encode($resultado);
	}

}