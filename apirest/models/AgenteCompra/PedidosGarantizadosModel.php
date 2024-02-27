<?php
class PedidosGarantizadosModel extends CI_Model{
	var $table = 'agente_compra_pedido_cabecera';
	var $table_agente_compra_pedido_detalle = 'agente_compra_pedido_detalle';
	var $table_agente_compra_pedido_detalle_producto_proveedor = 'agente_compra_pedido_detalle_producto_proveedor';
	var $table_agente_compra_pedido_detalle_producto_proveedor_imagen = 'agente_compra_pedido_detalle_producto_proveedor_imagen';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_configuracion = 'configuracion';
	var $table_moneda = 'moneda';
	var $table_cliente = 'entidad';
	var $table_producto = 'producto';
	var $table_unidad_medida = 'unidad_medida';
	var $table_medio_pago = 'medio_pago';
	var $table_departamento = 'departamento';
	var $table_provincia = 'provincia';
	var $table_distrito = 'distrito';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_importacion_grupal_cabecera = 'importacion_grupal_cabecera';
	var $table_pais = 'pais';
	var $table_agente_compra_correlativo = 'agente_compra_correlativo';
	var $table_usuario_intero = 'usuario';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, P.No_Pais, 
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		CORRE.Fe_Month, USRCHINA.No_Usuario')
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join')
    	->join($this->table_usuario_intero . ' AS USRCHINA', 'USRCHINA.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_Empresa_China', 'left')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
		->where_in($this->table . '.Nu_Estado', array(2,3,4,8));
        
		$this->db->where("Fe_Emision_Cotizacion BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

		if(!empty($this->input->post('ID_Pedido_Cabecera'))){
        	$this->db->where($this->table . '.ID_Pedido_Cabecera', $this->input->post('ID_Pedido_Cabecera'));
		}

		if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
    }

	function get_datatables(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id($ID){
        $this->db->select('CORRE.Fe_Month, Nu_Estado_China,
		(SELECT Ss_Venta_Oficial FROM tasa_cambio WHERE ID_Empresa=1 AND Fe_Ingreso="' . dateNow('fecha') . '") AS yuan_venta,
		' . $this->table . '.*,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		IGPD.ID_Pedido_Detalle, IGPD.Txt_Producto, IGPD.Txt_Descripcion, IGPD.Qt_Producto, IGPD.Txt_Url_Imagen_Producto, IGPD.Txt_Url_Link_Pagina_Producto,
		IGPD.Nu_Envio_Mensaje_Chat_Producto, TDI.No_Tipo_Documento_Identidad_Breve, ' . $this->table . '.Nu_Estado AS Nu_Estado_Pedido');
        $this->db->from($this->table);
    	$this->db->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join');
    	$this->db->join($this->table_agente_compra_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
		$this->db->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id_excel($ID){
        $this->db->select('
		' . $this->table . '.Nu_Correlativo,
		' . $this->table . '.Fe_Emision_Cotizacion,
		' . $this->table . '.Ss_Tipo_Cambio,
		CORRE.Fe_Month,
		CLI.No_Contacto,
		CLI.Txt_Email_Contacto,
		IGPD.Txt_Url_Imagen_Producto,
		IGPD.Txt_Producto,
		IGPD.Txt_Descripcion,
		ACPDPP.Qt_Producto_Caja_Final,
		ACPDPP.Ss_Precio,
		ACPDPP.Qt_Producto_Caja,
		ACPDPP.Qt_Cbm,
		ACPDPP.Nu_Dias_Delivery,
		ACPDPP.Ss_Costo_Delivery');
        $this->db->from($this->table);
    	$this->db->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join');
    	$this->db->join($this->table_agente_compra_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
    	$this->db->join($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP', 'ACPDPP.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera AND IGPD.ID_Pedido_Detalle=ACPDPP.ID_Pedido_Detalle', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
		$this->db->where('ACPDPP.Nu_Selecciono_Proveedor',1);
        $query = $this->db->get();
        return $query->result();
    }

    public function getItemProveedor($ID){
        $this->db->select('
		ACPC.Ss_Tipo_Cambio,
		ACPDPP.ID_Pedido_Detalle,
		ACPDPP.ID_Pedido_Detalle_Producto_Proveedor,
		ACPDPP.Ss_Precio,
		ACPDPP.Qt_Producto_Moq,
		ACPDPP.Qt_Producto_Caja,
		ACPDPP.Qt_Cbm,
		ACPDPP.Nu_Dias_Delivery,
		ACPDPP.Ss_Costo_Delivery,
		ACPDPP.Txt_Nota,
		ACPDPP.Nu_Selecciono_Proveedor,
		ACPDPP.Qt_Producto_Caja_Final,
		ACPDPP.Txt_Nota_Final,
		ACPDPPI.Txt_Url_Imagen_Producto,
		ACPDPP.No_Contacto_Proveedor,
		ACPDPP.Txt_Url_Imagen_Proveedor
		');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP');
		$this->db->join($this->table . ' AS ACPC', 'ACPC.ID_Pedido_Cabecera = ACPDPP.ID_Pedido_Cabecera', 'join');
		$this->db->join($this->table_agente_compra_pedido_detalle_producto_proveedor_imagen . ' AS ACPDPPI', 'ACPDPPI.ID_Pedido_Detalle_Producto_Proveedor = ACPDPP.ID_Pedido_Detalle_Producto_Proveedor', 'join');
        $this->db->where('ACPDPP.ID_Pedido_Detalle',$ID);
        $query = $this->db->get();
        return $query->result();
    }

    public function getItemImagenProveedor($ID){
        $this->db->select('Txt_Url_Imagen_Producto');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor_imagen);
        $this->db->where($this->table_agente_compra_pedido_detalle_producto_proveedor_imagen . '.ID_Pedido_Detalle_Producto_Proveedor',$ID);
        $query = $this->db->get();
        return $query->result();
    }

    public function actualizarElegirItemProductos($arrPost, $data_files){
		$this->db->trans_begin();

		//array_debug($data_files['addProveedor']);

		$path = "assets/images/contacto_proveedores/";
		//foreach ($arrPost['addProducto'] as $row) {
		foreach($arrPost['addProducto'] as $key => $row) {
			$Txt_Url_Imagen_Proveedor='';
			if(isset($data_files['addProveedor']) && !empty($data_files['addProveedor']) && !empty($data_files['addProveedor']['name'][$key])) {
				$_FILES['img_proveedor']['name'] = $data_files['addProveedor']['name'][$key];
				$_FILES['img_proveedor']['type'] = $data_files['addProveedor']['type'][$key];
				$_FILES['img_proveedor']['tmp_name'] = $data_files['addProveedor']['tmp_name'][$key];
				$_FILES['img_proveedor']['error'] = $data_files['addProveedor']['error'][$key];
				$_FILES['img_proveedor']['size'] = $data_files['addProveedor']['size'][$key];

				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
				$config['max_size'] = 3072;//1024 KB = 3 MB
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = '255';
		
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('img_proveedor')){
					$this->db->trans_rollback();
					return array(
						'status' => 'error',
						'message' => 'No se cargo imagen proveedor ' . strip_tags($this->upload->display_errors()),
					);
				} else {
					$arrUploadFile = $this->upload->data();
					$Txt_Url_Imagen_Proveedor = base_url($path . $arrUploadFile['file_name']);
				}
			}


			$cantidad = $row['cantidad_oculta'];
			if(isset($row['cantidad'])){
				$cantidad = $row['cantidad'];
				if($row['cantidad'] < $row['cantidad_oculta'])
					$cantidad = $row['cantidad_oculta'];
			}
			
			$nota = '';
			if(isset($row['nota'])){
				$nota = nl2br($row['nota']);
			}
			
			$precio = $row['precio'];
			if($row['precio'] < $row['precio_oculta'])
				$precio = $row['precio_oculta'];
			
			$moq = $row['moq'];
			if($row['moq'] < $row['moq_oculta'])
				$moq = $row['moq_oculta'];
				
			$caja = $row['caja'];
			if($row['caja'] < $row['caja_oculta'])
				$caja = $row['caja_oculta'];
				
			$cbm = $row['cbm'];
			if($row['cbm'] < $row['cbm_oculta'])
				$cbm = $row['cbm_oculta'];
			
			$delivery = $row['delivery'];
			if($row['delivery'] < $row['delivery_oculta'])
				$delivery = $row['delivery_oculta'];
			
			$costo_delivery = $row['costo_delivery'];
			if($row['costo_delivery'] < $row['costo_delivery_oculta'])
				$costo_delivery = $row['costo_delivery_oculta'];
				
			$nota_historica = $row['nota_historica'];
			if(empty($row['nota_historica']))
				$nota_historica = $row['nota_historica_oculta'];
				
			$contacto_proveedor = $row['contacto_proveedor'];
			if(empty($row['contacto_proveedor']))
				$contacto_proveedor = $row['contacto_proveedor'];

			$arrActualizar[] = array(
				'ID_Pedido_Detalle_Producto_Proveedor' => $row['id_detalle'],
				'Qt_Producto_Caja_Final' => $cantidad,
				'Txt_Nota_Final' => $nota,
				'Ss_Precio' => $precio,
				'Qt_Producto_Moq' => $moq,
				'Qt_Producto_Caja' => $caja,
				'Qt_Cbm' => $cbm,
				'Nu_Dias_Delivery' => $delivery,
				'Ss_Costo_Delivery' => $costo_delivery,
				'Txt_Nota' => nl2br($nota_historica),
				'No_Contacto_Proveedor' => $contacto_proveedor,
				'Txt_Url_Imagen_Proveedor' => $Txt_Url_Imagen_Proveedor,
			);
		}
		
		$this->db->update_batch($this->table_agente_compra_pedido_detalle_producto_proveedor, $arrActualizar, 'ID_Pedido_Detalle_Producto_Proveedor');

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'message' => 'error al actualizar datos');
		} else {
			//registrar evento de notificacion
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'Pedidos Garantizados',
				'Cotización ' . $arrPost['Item_ECorrelativo_Editar'] . ' edito proveedor de ' . $arrPost['Item_Ename_producto_Editar'],
				''
			);

			$this->db->trans_commit();
			return array('status' => 'success', 'message' => 'Datos actualizados');
		}
    }

	public function elegirItemProveedor($id_detalle, $ID, $status, $sCorrelativoCotizacion, $sNameItem){
		$query = "SELECT Nu_Selecciono_Proveedor FROM agente_compra_pedido_detalle_producto_proveedor WHERE ID_Pedido_Detalle = " . $id_detalle . " AND Nu_Selecciono_Proveedor=1";
		$objProveedor = $this->db->query($query)->row();
		$where = array('ID_Pedido_Detalle_Producto_Proveedor' => $ID);
		$data = array( 'Nu_Selecciono_Proveedor' => $status );//1=proveedor seleccionado
		if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0) {
			$sElegirProveedor = ($status == 1 ? 'marco proveedor' : 'desmarco proveedor');
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'Pedidos Garantizados',
				'Cotización ' . $sCorrelativoCotizacion . ' ' . $sElegirProveedor . ' de ' . $sNameItem,
				''
			);

			return array('status' => 'success', 'message' => 'Proveedor seleccionado');
		}
		return array('status' => 'error', 'message' => 'Error al seleccionar proveedor');
	}

	public function cambiarEstado($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array(
			'Nu_Estado' => $Nu_Estado,
			'ID_Usuario_Interno_China' => $ID_Usuario_Interno_Empresa_China
		);

		if($Nu_Estado==5){//aprobado creo nueva fecha de emision para O.C. Aprobadas
			//generar proceso de estado de checklist del chinito
			$arrDataTour = array(
				'ID_Pedido_Cabecera' => $ID,
				'ID_Usuario_Interno_Empresa_China' => $ID_Usuario_Interno_Empresa_China
			);
			$arrTour = $this->generarEstadoProcesoAgenteCompra($arrDataTour);

			$data = array_merge($data, array(
				'Fe_Emision_OC_Aprobada' => dateNow('fecha')
			));

			//marcar progreso de pedido completado 2/3
			$where_progreso = array(
				'ID_Pedido_Cabecera' => $ID,
				'Nu_ID_Interno' => 2
			);
			$data_progreso = array('Nu_Estado_Proceso' => 1);
			$this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);
		}

		if ($this->db->update($this->table, $data, $where) > 0) {
			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}

	public function cambiarEstadoChina($ID, $Nu_Estado, $sCorrelativo){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado_China' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($Nu_Estado);
			//registrar evento de notificacion
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'Pedidos Garantizados',
				'Cotización ' . $sCorrelativo . ' cambio estado a ' . $arrEstadoRegistro['No_Estado'],
				''
			);

			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}
    
    public function actualizarPedido($where, $data, $arrProducto, $arrProductoTable, $sCorrelativo){
		$this->db->trans_begin();

		if (!empty($arrProducto)) {
			//localhost
			$path = "assets/images/productos/";
			//$path = "../../agentecompra.probusiness.pe/public_html/assets/images/productos/";
			$iCounter=0;
			$_FILES['tmp_voucher'] = $_FILES['voucher'];
			foreach($arrProducto as $row) {
				//SET IMAGEN
				$_FILES['voucher']['name'] = $_FILES['tmp_voucher']['name'][$iCounter];
				$_FILES['voucher']['type'] = $_FILES['tmp_voucher']['type'][$iCounter];
				$_FILES['voucher']['tmp_name'] = $_FILES['tmp_voucher']['tmp_name'][$iCounter];
				$_FILES['voucher']['error'] = $_FILES['tmp_voucher']['error'][$iCounter];
				$_FILES['voucher']['size'] = $_FILES['tmp_voucher']['size'][$iCounter];

				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
				$config['max_size'] = 3096;//1024 KB = 1 MB
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = '255';
		
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('voucher')){
					$this->db->trans_rollback();
					return array(
						'status' => 'error',
						'message' => 'No se cargo imagen ' . $row['nombre_comercial'] . ' ' . strip_tags($this->upload->display_errors()),
					);
				} else {
					$arrUploadFile = $this->upload->data();
					$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

					$Txt_Url_Imagen_Producto = str_replace("https://intranet.probusiness.pe/../../", "https://", $Txt_Url_Imagen_Producto);
					$Txt_Url_Imagen_Producto = str_replace("public_html/", "", $Txt_Url_Imagen_Producto);
				}

				$arrSaleOrderDetail[] = array(
					'ID_Empresa' => $data['ID_Empresa'],
					'ID_Organizacion' => $data['ID_Organizacion'],
					'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
					'Txt_Producto' => $row['nombre_comercial'],
					'Txt_Descripcion' => nl2br($row['caracteristicas']),
					'Qt_Producto' => $row['cantidad'],
					'Txt_Url_Imagen_Producto' => $Txt_Url_Imagen_Producto,
					'Txt_Url_Link_Pagina_Producto' => $row['link'],
				);
				++$iCounter;
			}
			$this->db->insert_batch('agente_compra_pedido_detalle', $arrSaleOrderDetail);
		}
		
		//actualizar productos de tabla de cliente
		if (!empty($arrProductoTable)) {
			foreach($arrProductoTable as $row) {
				//array_debug($row);
				$arrSaleOrderDetailUPD[] = array(
					'ID_Pedido_Detalle' => $row['id_item'],
					'Qt_Producto' => $row['cantidad'],//agergar input de cantidad
					'Txt_Descripcion' => nl2br($row['caracteristicas']),
				);
			}
    		$this->db->update_batch('agente_compra_pedido_detalle', $arrSaleOrderDetailUPD, 'ID_Pedido_Detalle');
		}

		$where_cabecera = array(
			'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
		);
		$this->db->update($this->table, $data, $where_cabecera);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		} else {
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'Pedidos Garantizados',
				'Cotización ' . $sCorrelativo . ' se actualizo',
				''
			);

			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
    }
    
    public function addPedidoItemProveedor($data, $data_files){
		$this->db->trans_begin();

		//actualizar cabecera
		$path = "assets/images/contacto_proveedores/";
		foreach($data['addProducto'] as $key => $row) {
			$Txt_Url_Imagen_Proveedor='';
			if(isset($data_files['proveedor']) && !empty($data_files['proveedor']) && !empty($data_files['proveedor']['name'][$key])) {
				$_FILES['img_proveedor']['name'] = $data_files['proveedor']['name'][$key];
				$_FILES['img_proveedor']['type'] = $data_files['proveedor']['type'][$key];
				$_FILES['img_proveedor']['tmp_name'] = $data_files['proveedor']['tmp_name'][$key];
				$_FILES['img_proveedor']['error'] = $data_files['proveedor']['error'][$key];
				$_FILES['img_proveedor']['size'] = $data_files['proveedor']['size'][$key];

				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
				$config['max_size'] = 3072;//1024 KB = 3 MB
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = '255';
		
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('img_proveedor')){
					$this->db->trans_rollback();
					return array(
						'status' => 'error',
						'message' => 'No se cargo imagen proveedor ' . strip_tags($this->upload->display_errors()),
					);
				} else {
					$arrUploadFile = $this->upload->data();
					$Txt_Url_Imagen_Proveedor = base_url($path . $arrUploadFile['file_name']);
				}
			}

			//insertar proveedor agente de compra
			$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = 1 AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = 1 AND No_Entidad = '" . limpiarCaracteresEspeciales($row['contacto_proveedor']) . "' LIMIT 1";
			$objVerificarEntidad = $this->db->query($query)->row();
			if (!is_object($objVerificarEntidad)){
				$arrEntidad = array(
					'ID_Empresa' => 1,
					'ID_Organizacion' => 1,
					'Nu_Tipo_Entidad' => 1,//1=Proveedor
					'ID_Tipo_Documento_Identidad' => 1,
					'Nu_Documento_Identidad' => '-',
					'No_Entidad' => $row['contacto_proveedor'],
					'Nu_Estado' => 1,
					'ID_Pais' => 55,//buscar id china
					'Nu_Agente_Compra' => 1,
					'Txt_Url_Imagen_Proveedor' => $Txt_Url_Imagen_Proveedor
				);
	
				if ($this->db->insert('entidad', $arrEntidad) > 0) {
		    		$ID_Entidad_Proveedor = $this->db->insert_id();
				} else {
					$this->db->trans_rollback();
					return array(
						'status' => 'error',
						'message' => 'No registro proveedor'
					);
				}
			} else {
				$ID_Entidad_Proveedor = $objVerificarEntidad->ID_Entidad;
			}

			$arrDetalle[] = array(
                'ID_Empresa' => $data['EID_Empresa_item'],
                'ID_Organizacion' => $data['EID_Organizacion_item'],
				'ID_Pedido_Cabecera' => $data['EID_Pedido_Cabecera_item'],
				'ID_Pedido_Detalle' => $data['EID_Pedido_Detalle_item'],
				'Ss_Precio' => $row['precio'],
				'Qt_Producto_Moq' => $row['moq'],
				'Qt_Producto_Caja' => $row['qty_caja'],
				'Qt_Cbm' => $row['cbm'],
				'Nu_Dias_Delivery' => $row['delivery'],
				'Ss_Costo_Delivery' => $row['costo_delivery'],
				'Txt_Nota' => nl2br($row['nota']),
				'No_Contacto_Proveedor' => $row['contacto_proveedor'],
				'Txt_Url_Imagen_Proveedor' => $Txt_Url_Imagen_Proveedor,
				'ID_Entidad_Proveedor' => $ID_Entidad_Proveedor
			);
		}

		$this->db->insert_batch('agente_compra_pedido_detalle_producto_proveedor', $arrDetalle);
		$id_detalle = $this->db->insert_id();

		if($id_detalle>0){
			if(!empty($data_files) && isset($data_files)){
				//array_debug($data_files);
				//capturando arreglo de images o videos
				$arrImagenTmp = array();
				for ($i=1; $i < count($data_files['voucher']); $i++) {
					//array_debug($data_files['voucher']);
					if(isset($data_files['voucher']['name'][$i])) {
						foreach ($data_files['voucher']['name'][$i] as $row_imagen) {
							$arrImagenTmp['images']['name'][] = $row_imagen;
							$arrImagenTmp['images']['id_detalle'][] = $id_detalle;
						}
						foreach ($data_files['voucher']['type'][$i] as $row_imagen) {
							$arrImagenTmp['images']['type'][] = $row_imagen;
						}
						foreach ($data_files['voucher']['tmp_name'][$i] as $row_imagen) {
							$arrImagenTmp['images']['tmp_name'][] = $row_imagen;
						}
						foreach ($data_files['voucher']['error'][$i] as $row_imagen) {
							$arrImagenTmp['images']['error'][] = $row_imagen;
						}
						foreach ($data_files['voucher']['size'][$i] as $row_imagen) {
							$arrImagenTmp['images']['size'][] = $row_imagen;
						}
						++$id_detalle;
						//echo "valor de i >>>>>>> " . $i . "<br>";
					}
				}

				if(isset($arrImagenTmp) && !empty($arrImagenTmp)){
					//echo "hola > " . count($arrImagenTmp['images']);
					$path = "assets/images/productos_proveedores/";
					//capturando multiples imagenes por producto de proveedor
					for ($i=0; $i < count($arrImagenTmp['images']['name']); $i++) {
						$_FILES['img_proveedor']['name'] = $arrImagenTmp['images']['name'][$i];
						$_FILES['img_proveedor']['type'] = $arrImagenTmp['images']['type'][$i];
						$_FILES['img_proveedor']['tmp_name'] = $arrImagenTmp['images']['tmp_name'][$i];
						$_FILES['img_proveedor']['error'] = $arrImagenTmp['images']['error'][$i];
						$_FILES['img_proveedor']['size'] = $arrImagenTmp['images']['size'][$i];
			
						$config['upload_path'] = $path;
						$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
						$config['max_size'] = 10240;//1024 KB = 10 MB
						$config['encrypt_name'] = TRUE;
						$config['max_filename'] = '255';
				
						$this->load->library('upload', $config);
			
						if (!$this->upload->do_upload('img_proveedor')){
							$this->db->trans_rollback();
							return array(
								'status' => 'error',
								'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
							);
						} else {
							$arrUploadFile = $this->upload->data();
							$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

							$arrDetalleImagen[] = array(
								'ID_Empresa' => $data['EID_Empresa_item'],
								'ID_Organizacion' => $data['EID_Organizacion_item'],
								'ID_Pedido_Cabecera' => $data['EID_Pedido_Cabecera_item'],
								'ID_Pedido_Detalle' => $data['EID_Pedido_Detalle_item'],
								'ID_Pedido_Detalle_Producto_Proveedor' => $arrImagenTmp['images']['id_detalle'][$i],
								'Txt_Url_Imagen_Producto' => $Txt_Url_Imagen_Producto,
							);
							//array_debug($arrDetalleImagen);
						}
					}

					if ($this->db->insert_batch('agente_compra_pedido_detalle_producto_proveedor_imagen', $arrDetalleImagen)<=0){
						$this->db->trans_rollback();
						return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar imagen');
					}
				}
				//array_debug($arrDetalleImagen);
			} else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe imagen');
			}
		} else {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		}
		
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		} else {
			//$this->db->trans_rollback();
			$this->db->trans_commit();
			
			//registrar evento de notificacion
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'Pedidos Garantizados',
				'Cotización ' . $data['Item_ECorrelativo'] . ' nuevo proveedor de ' . $data['Item_Ename_producto'],
				''
			);

			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
    }
	
	public function getDownloadImage($id){
		$query = "SELECT Txt_Url_Imagen_Producto FROM agente_compra_pedido_detalle WHERE ID_Pedido_Detalle = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function generarCorrelativo(){
		$Nu_Correlativo = 0;
		$Fe_Year = ToYear(dateNow('fecha'));
		$Fe_Month = ToMonth(dateNow('fecha'));

		$objCorrelativo = $this->db->query("SELECT ID_Agente_Compra_Correlativo FROM agente_compra_correlativo WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row();
		if(is_object($objCorrelativo)){
			$ID_Agente_Compra_Correlativo = $objCorrelativo->ID_Agente_Compra_Correlativo;
			$query = "UPDATE agente_compra_correlativo SET Nu_Correlativo=Nu_Correlativo + 1 WHERE ID_Agente_Compra_Correlativo=" . $ID_Agente_Compra_Correlativo;
			$this->db->query($query);
		} else {
			$query = "INSERT INTO agente_compra_correlativo(
ID_Empresa,
ID_Organizacion,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->user->ID_Empresa . ",
" . $this->user->ID_Organizacion . ",
" . $Fe_Year . ",
" . $Fe_Month . ",
1
);";
			$this->db->query($query);
			$ID_Agente_Compra_Correlativo = $this->db->insert_id();
		}
		$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM agente_compra_correlativo WHERE ID_Agente_Compra_Correlativo = " . $ID_Agente_Compra_Correlativo . " LIMIT 1")->row()->Nu_Correlativo;
		if($Nu_Correlativo>0){
			return array(
				'status' => 'success',
				'result' => array(
					'id_correlativo' => $ID_Agente_Compra_Correlativo,
					'numero_correlativo' => $Nu_Correlativo
				)
			);
		}
		return array(
			'status' => 'error',
			'message' => 'Correlativo es: ' . $Nu_Correlativo
		);
	}

	public function addFileProveedor($arrPost, $data_files){
		if (isset($data_files['image_documento']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/voucher_pagos_garantizado/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('image_documento')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['documento_pago_garantizado-id_cabecera']);
				$data = array( 'Txt_Url_Pago_Garantizado' => $Txt_Url_Imagen_Producto );//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				//registrar evento de notificacion
				$notificacion = $this->NotificacionModel->procesarNotificacion(
					$this->user->No_Usuario,
					'Pedidos Garantizados',
					'Cotización ' . $arrPost['documento_pago_garantizado-correlativo'] . ' se realizo pago garantía',
					''
				);

				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Voucher guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarDocumentoPagoGarantizado($id){
		$query = "SELECT Txt_Url_Pago_Garantizado AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function sendMessage($data){
		$this->db->trans_begin();
	
		$arrMessage = array(
			'ID_Empresa' => $data['chat_producto-ID_Empresa_item'],
			'ID_Organizacion' => $data['chat_producto-ID_Organizacion_item'],
			'ID_Pedido_Cabecera' => $data['chat_producto-ID_Pedido_Cabecera_item'],
			'ID_Pedido_Detalle' => $data['chat_producto-ID_Pedido_Detalle_item'],
		);
		
		if($this->user->Nu_Tipo_Privilegio_Acceso==1){//1peru
			$arrMessageUser = array(
				'ID_Usuario_Remitente' => $this->user->ID_Usuario,
				'Txt_Usuario_Remitente' => nl2br($data['message_chat'])
			);
		}
		
		if($this->user->Nu_Tipo_Privilegio_Acceso==2){//china
			$arrMessageUser = array(
				'ID_Usuario_Destino' => $this->user->ID_Usuario,
				'Txt_Usuario_Destino' => nl2br($data['message_chat'])
			);
		}

		$arrMessage = array_merge($arrMessage, $arrMessageUser);

		$this->db->insert('agente_compra_pedido_detalle_chat_producto', $arrMessage);
		
		$sql = "UPDATE agente_compra_pedido_detalle SET Nu_Envio_Mensaje_Chat_Producto=Nu_Envio_Mensaje_Chat_Producto+1 WHERE ID_Pedido_Detalle=" . $data['chat_producto-ID_Pedido_Detalle_item'];
		$this->db->query($sql);

		//$where = array('ID_Pedido_Detalle' => $data['chat_producto-ID_Pedido_Detalle_item']);
		//$data = array( 'Nu_Envio_Mensaje_Chat_Producto' => 'Nu_Envio_Mensaje_Chat_Producto+1');//1=SI
		//$this->db->update('agente_compra_pedido_detalle', $data, $where);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'message' => 'Error al enviar');
		} else {
			//$this->db->trans_rollback();
			$this->db->trans_commit();
			return array('status' => 'success', 'message' => 'Mensaje enviado');
		}
	}
	
	public function viewChatItem($id){
		$query = "SELECT CHAT.*, USRR.No_Nombres_Apellidos AS No_Nombres_Apellidos_Remitente, USRD.No_Nombres_Apellidos AS No_Nombres_Apellidos_Destinatario FROM
agente_compra_pedido_detalle_chat_producto AS CHAT
LEFT JOIN usuario AS USRR ON(USRR.ID_Usuario = CHAT.ID_Usuario_Remitente)
LEFT JOIN usuario AS USRD ON(USRD.ID_Usuario = CHAT.ID_Usuario_Destino)
WHERE ID_Pedido_Detalle = " . $id . " ORDER BY CHAT.Fe_Registro ASC";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos'
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}

	public function asignarUsuarioPedidoChina($arrPost){
        $where = array('ID_Pedido_Cabecera' => $arrPost['guardar_personal_china-ID_Pedido_Cabecera']);
        $data = array( 'ID_Usuario_Interno_Empresa_China' => $arrPost['cbo-guardar_personal_china-ID_Usuario']);
		if ($this->db->update($this->table, $data, $where) > 0) {
			//agregar tour para chinito
			/*
			$arrDataTour = array(
				'ID_Pedido_Cabecera' => $ID
			);
			$arrTour = $this->generarEstadoProcesoAgenteCompra($arrDataTour);
*/
			$where_progreso = array(
				'ID_Pedido_Cabecera' => $arrPost['guardar_personal_china-ID_Pedido_Cabecera'],
				'Nu_ID_Interno' => 1
			);
			$data_progreso = array('Nu_Estado_Proceso' => 1);
			if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
				return array('status' => 'success', 'message' => 'Actualizado');
			} else {
				return array('status' => 'error', 'message' => 'Error al actualizar y agregar progreso compra');
			}
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}

	public function removerAsignarPedido($ID, $id_usuario){
		$where = array('ID_Pedido_Cabecera' => $ID);
		$data = array( 'ID_Usuario_Interno_Empresa_China' => 0 );
		if ($this->db->update($this->table, $data, $where) > 0) {
			return array('status' => 'success', 'message' => 'Se quitó asignación');
		}
		return array('status' => 'error', 'message' => 'Error al eliminar asignación pedido');
	}

	public function cambiarEstadoImpotacionIntegral($ID, $Nu_Estado, $sCorrelativo){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Importacion_Integral' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}
	
	public function generarEstadoProcesoAgenteCompra($arrDataTour){
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '1. Coordinación con Proveedores <br> A. Negociación',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '5',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '2. Reserva de Booking',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '6',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '3. Recepción de carga',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '7',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '4. Inspección',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '8',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '5. Docs Exportación',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '9',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '6. Despacho al Shipper / Forwarder',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '10',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		if ($this->db->insert_batch('proceso_agente_compra_pedido', $proceso_agente_compra_pedido)>0)
			return array('status' => 'success', 'message' => 'Registro guardado');
		return array('status' => 'error', 'message' => 'Error al guardar');
	}
}
