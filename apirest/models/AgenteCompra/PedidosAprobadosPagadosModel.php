<?php
class PedidosAprobadosPagadosModel extends CI_Model{
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
	var $table_agente_compra_pedido_detalle_producto_proveedor_inspeccion = 'agente_compra_pedido_detalle_producto_proveedor_inspeccion';
	var $table_usuario_intero = 'usuario';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select('CORRE.Fe_Month, Nu_Estado_China,' . $this->table . '.*, P.No_Pais, 
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto, USRCHINA.No_Usuario')
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join')
    	->join($this->table_usuario_intero . ' AS USRCHINA', 'USRCHINA.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_Empresa_China', 'left')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
		->where_in($this->table . '.Nu_Estado', array(5,6,7,9));
        
		//$this->db->where("Fe_Emision_Cotizacion BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
		$this->db->where($this->table . ".Nu_Estado_Pago_Proveedor=", 0);
		$this->db->where("Fe_Emision_OC_Aprobada BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        
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
        $this->db->select($this->table . '.ID_Pedido_Cabecera,
		' . $this->table . '.ID_Entidad,
		' . $this->table . '.ID_Empresa,
		' . $this->table . '.ID_Organizacion,
		' . $this->table . '.Nu_Correlativo,
		' . $this->table . '.Fe_Emision_Cotizacion,
		' . $this->table . '.Ss_Tipo_Cambio,
		' . $this->table . '.Nu_Estado AS Nu_Estado_Pedido,
		' . $this->table . '.Txt_Url_Pago_30_Cliente,
		' . $this->table . '.Txt_Url_Pago_100_Cliente,
		' . $this->table . '.Txt_Url_Pago_Servicio_Cliente,
		' . $this->table . '.Txt_Url_Pago_Otros_Flete,
		' . $this->table . '.Txt_Url_Pago_Otros_Costo_Origen,
		' . $this->table . '.Txt_Url_Pago_Otros_Costo_Fta,
		' . $this->table . '.Txt_Url_Pago_Otros_Cuadrilla,
		' . $this->table . '.Txt_Url_Pago_Otros_Costos,
		' . $this->table . '.Ss_Pago_30_Cliente,
		' . $this->table . '.Ss_Pago_100_Cliente,
		' . $this->table . '.Ss_Pago_Servicio_Cliente,
		' . $this->table . '.Ss_Pago_Otros_Flete,
		' . $this->table . '.Ss_Pago_Otros_Costo_Origen,
		' . $this->table . '.Ss_Pago_Otros_Costo_Fta,
		' . $this->table . '.Ss_Pago_Otros_Cuadrilla,
		' . $this->table . '.Ss_Pago_Otros_Costos,
		CORRE.Fe_Month,
		CLI.No_Contacto,
		CLI.Txt_Email_Contacto,
		CLI.Nu_Celular_Contacto,
		CLI.No_Entidad,
		CLI.Nu_Documento_Identidad,
		ACPDPP.ID_Pedido_Detalle_Producto_Proveedor,
		IGPD.ID_Pedido_Detalle,
		IGPD.Txt_Url_Imagen_Producto,
		IGPD.Txt_Producto,
		ACPDPP.ID_Entidad_Proveedor,
		ACPDPP.Qt_Producto_Caja_Final AS Qt_Producto,
		ACPDPP.Ss_Precio,
		ACPDPP.Nu_Dias_Delivery,
		ACPDPP.Txt_Url_Archivo_Pago_1_Proveedor,
		ACPDPP.Ss_Pago_1_Proveedor,
		ACPDPP.Txt_Url_Archivo_Pago_2_Proveedor,
		ACPDPP.Ss_Pago_2_Proveedor,
		ACPDPP.Nu_Agrego_Inspeccion,
		ACPDPP.Ss_Costo_Delivery,
		ACPDPP.No_Contacto_Proveedor,
		ACPDPP.Txt_Url_Imagen_Proveedor,
		ACPDPP.Fe_Entrega_Proveedor,
		ACPDPP.Nu_Visualizacion_Item,
		ACPDPP.Qt_Producto_Caja_Final_Verificada,
		ACPDPP.Nu_Estado_Recepcion_Carga_Proveedor_Item,
		ACPDPP.Txt_Nota_Recepcion_Carga_Proveedor,
		ACPDPP.Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor,
		ACPDPP.Fe_Pago_Importe_1,
		ACPDPP.Ss_Pago_Importe_1,
		ACPDPP.Fe_Pago_Importe_2,
		ACPDPP.Ss_Pago_Importe_2
		');
        $this->db->from($this->table);
    	$this->db->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join');
    	$this->db->join($this->table_agente_compra_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');		
    	$this->db->join($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP', 'ACPDPP.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera AND IGPD.ID_Pedido_Detalle=ACPDPP.ID_Pedido_Detalle', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
		$this->db->where('ACPDPP.Nu_Selecciono_Proveedor',1);
		$this->db->where('ACPDPP.Nu_Visualizacion_Item',1);
		$this->db->order_by( 'ACPDPP.ID_Entidad_Proveedor ASC' );
        $query = $this->db->get();
        return $query->result();
    }

	public function elminarItemProveedor($ID, $correlativo, $name_item){
        $where = array('ID_Pedido_Detalle_Producto_Proveedor' => $ID);
        $data = array( 'Nu_Visualizacion_Item' => 0 );
		if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0) {
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'Pedidos Pagados',
				'O.C. ' . $correlativo . ' se eliminó producto ' . $name_item,
				''
			);

			return array('status' => 'success', 'message' => 'Eliminar');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function get_by_id_inspeccion($ID){
        $this->db->select('ID_Pedido_Detalle_Producto_Inspeccion, Txt_Url_Imagen_Producto');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor_inspeccion);
        $this->db->where($this->table_agente_compra_pedido_detalle_producto_proveedor_inspeccion . '.ID_Pedido_Detalle_Producto_Proveedor',$ID);
        $query = $this->db->get();
        return $query->result();
    }

	public function cambiarEstado($ID, $Nu_Estado, $sCorrelativo){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($Nu_Estado);
			//registrar evento de notificacion
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'O.C.',
				$sCorrelativo . ' cambio estado a ' . $arrEstadoRegistro['No_Estado'],
				''
			);

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
				'O.C.',
				$sCorrelativo . ' cambio estado a ' . $arrEstadoRegistro['No_Estado'],
				''
			);
			return array('status' => 'success', 'message' => 'Actualizado', 'notificacion' => $notificacion);
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}
    
    public function addPagoProveedor($arrPost, $data_files){
		if(!empty($arrPost)) {
			if(isset($data_files['voucher_proveedor']) && !empty($data_files['voucher_proveedor']) && !empty($data_files['voucher_proveedor']['name'])) {
				$this->db->trans_begin();

				$path = "assets/images/pagos_proveedores/";
				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
				$config['max_size'] = 3072;//1024 KB = 3 MB
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = '255';
		
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('voucher_proveedor')){
					$this->db->trans_rollback();
					return array(
						'status' => 'error',
						'message' => 'No se cargo archivo proveedor ' . strip_tags($this->upload->display_errors()),
					);
				} else {
					$arrUploadFile = $this->upload->data();
					$Txt_Url_Imagen_Proveedor = base_url($path . $arrUploadFile['file_name']);

					if($arrPost['proveedor-tipo_pago']==1) {
						//actualizar tabla
						$data = array(
							'Txt_Url_Archivo_Pago_1_Proveedor' => $Txt_Url_Imagen_Proveedor,
							'Ss_Pago_1_Proveedor' => $arrPost['amount_proveedor'],
						);
					} else if($arrPost['proveedor-tipo_pago']==2) {
						//actualizar tabla
						$data = array(
							'Txt_Url_Archivo_Pago_2_Proveedor' => $Txt_Url_Imagen_Proveedor,
							'Ss_Pago_2_Proveedor' => $arrPost['amount_proveedor'],
						);
					} else {
						return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe tipo de pago');
					}

					$where = array('ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['proveedor-id']);
					$this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where);
				}
			} else {
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No hay archivo');
			}
		} else {
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No hay datos');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		} else {
			//$this->db->trans_rollback();
			//registrar evento de notificacion
			$notificacion = $this->NotificacionModel->procesarNotificacion(
				$this->user->No_Usuario,
				'O.C.',
				$arrPost['proveedor-correlativo'] . ' se agrego pago a proveedor',
				''
			);

			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
	}
	
	public function getDownloadImage($id){
		$query = "SELECT Txt_Url_Archivo_Pago_1_Proveedor AS Txt_Url_Imagen_Producto FROM " . $this->table_agente_compra_pedido_detalle_producto_proveedor . " WHERE ID_Pedido_Detalle_Producto_Proveedor = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addInspeccionProveedor($arrPost, $data_files){
		if (isset($data_files['image_inspeccion']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/productos_proveedores_inspeccion/";
			//capturando multiples imagenes por producto de proveedor
			for ($i=0; $i < count($data_files['image_inspeccion']['name']); $i++) {
				$_FILES['img_proveedor']['name'] = $data_files['image_inspeccion']['name'][$i];
				$_FILES['img_proveedor']['type'] = $data_files['image_inspeccion']['type'][$i];
				$_FILES['img_proveedor']['tmp_name'] = $data_files['image_inspeccion']['tmp_name'][$i];
				$_FILES['img_proveedor']['error'] = $data_files['image_inspeccion']['error'][$i];
				$_FILES['img_proveedor']['size'] = $data_files['image_inspeccion']['size'][$i];

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
						'ID_Empresa' => $arrPost['proveedor-id_empresa'],
						'ID_Organizacion' => $arrPost['proveedor-id_organizacion'],
						'ID_Pedido_Cabecera' => $arrPost['proveedor-id_cabecera'],
						'ID_Pedido_Detalle' => $arrPost['proveedor-id_detalle'],
						'ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['proveedor-id'],
						'Txt_Url_Imagen_Producto' => $Txt_Url_Imagen_Producto,
					);
				}
			}

			$where = array('ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['proveedor-id']);
			$data = array( 'Nu_Agrego_Inspeccion' => 1 );//1=SI
			$this->db->update('agente_compra_pedido_detalle_producto_proveedor', $data, $where);

			$this->db->insert_batch('agente_compra_pedido_detalle_producto_proveedor_inspeccion', $arrDetalleImagen);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				
				$notificacion = $this->NotificacionModel->procesarNotificacion(
					$this->user->No_Usuario,
					'O.C.',
					$arrPost['proveedor-correlativo'] . ' se subió fotos de productos de inspección',
					''
				);

				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
			}
		} else {
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe archivo(s)');
		}
	}

	public function addFileProveedor($arrPost, $data_files){
		if (isset($data_files['image_documento']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/documento_entrega_cotizacion/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
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

				$where = array('ID_Pedido_Cabecera' => $arrPost['documento-id_cabecera']);
				$data = array( 'Txt_Url_Archivo_Documento_Entrega' => $Txt_Url_Imagen_Producto );//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$notificacion = $this->NotificacionModel->procesarNotificacion(
					$this->user->No_Usuario,
					'O.C.',
					$arrPost['documento-correlativo'] . ' invoice se guardo documento',
					''
				);

				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarDocumentoEntregado($id){
		$query = "SELECT Txt_Url_Archivo_Documento_Entrega AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addPagoCliente30($arrPost, $data_files){
		if (isset($data_files['pago_cliente_30']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('pago_cliente_30')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_30-id_cabecera']);
				$data = array(
					'Txt_Url_Pago_30_Cliente' => $Txt_Url_Imagen_Producto,
					'ID_Pais_30_Cliente' => $arrPost['ID_Pais_30_Cliente'],
					'Fe_Pago_30_Cliente' => ToDate($arrPost['Fe_Pago_30_Cliente']),
					'Ss_Pago_30_Cliente' => $arrPost['Ss_Pago_30_Cliente'],
					'Nu_Operacion_Pago_30_Cliente' => $arrPost['Nu_Operacion_Pago_30_Cliente']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_30-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPago30($id){
		$query = "SELECT Txt_Url_Pago_30_Cliente AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addPagoCliente100($arrPost, $data_files){
		if (isset($data_files['pago_cliente_100']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('pago_cliente_100')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_100-id_cabecera']);
				$data = array(
					'Txt_Url_Pago_100_Cliente' => $Txt_Url_Imagen_Producto,
					'ID_Pais_100_Cliente' => $arrPost['ID_Pais_100_Cliente'],
					'Fe_Pago_100_Cliente' => ToDate($arrPost['Fe_Pago_100_Cliente']),
					'Ss_Pago_100_Cliente' => $arrPost['Ss_Pago_100_Cliente'],
					'Nu_Operacion_Pago_100_Cliente' => $arrPost['Nu_Operacion_Pago_100_Cliente']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_100-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPago100($id){
		$query = "SELECT Txt_Url_Pago_100_Cliente AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addPagoClienteServicio($arrPost, $data_files){
		if (isset($data_files['pago_cliente_servicio']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('pago_cliente_servicio')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_servicio-id_cabecera']);
				$data = array(
					'Txt_Url_Pago_Servicio_Cliente' => $Txt_Url_Imagen_Producto,
					'ID_Pais_Servicio_Cliente' => $arrPost['ID_Pais_Servicio_Cliente'],
					'Fe_Pago_Servicio_Cliente' => ToDate($arrPost['Fe_Pago_Servicio_Cliente']),
					'Ss_Pago_Servicio_Cliente' => $arrPost['Ss_Pago_Servicio_Cliente'],
					'Nu_Operacion_Pago_Servicio_Cliente' => $arrPost['Nu_Operacion_Pago_Servicio_Cliente']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_servicio-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPagoServicio($id){
		$query = "SELECT Txt_Url_Pago_Servicio_Cliente AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}
    
    public function actualizarPedido($where, $data, $arrProducto){
		//actualizar productos de tabla de cliente
		if (!empty($arrProducto)) {
			foreach($arrProducto as $row) {
				//array_debug($row);
				$arrSaleOrderDetailUPD[] = array(
					'ID_Pedido_Detalle_Producto_Proveedor' => $row['id_item'],
					'Fe_Entrega_Proveedor' => ToDate($row['fecha_entrega_proveedor']),
				);
			}

    		$this->db->update_batch($this->table_agente_compra_pedido_detalle_producto_proveedor, $arrSaleOrderDetailUPD, 'ID_Pedido_Detalle_Producto_Proveedor');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'message' => 'Error al modificar');
		} else {
			//$this->db->trans_rollback();
			$this->db->trans_commit();
			return array('status' => 'success', 'message' => 'Registro modificado');
		}
    }

	public function cambiarTipoServicio($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array(
			'Nu_Tipo_Servicio' => $Nu_Estado,
			'ID_Usuario_Interno_Jefe_China' => $ID_Usuario_Interno_Empresa_China
		);
		if ($this->db->update($this->table, $data, $where) > 0) {
			$arrParams = array('ID_Pedido_Cabecera' => $ID);
			$this->actualizarProgresoPedido($arrParams);

			//crear completar pasos de configuracion jefe
			$arrDataTour = array(
				'ID_Pedido_Cabecera' => $ID,
				'ID_Usuario_Interno_Empresa_China' => $ID_Usuario_Interno_Empresa_China
			);
			$arrTour = $this->generarEstadoProcesoAgenteCompra($arrDataTour);

			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}

    public function agregarComisionTrading($where, $data){
		if ( $this->db->update($this->table, $data, $where) > 0 ) {
			$arrParams = array('ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera']);
			$this->actualizarProgresoPedido($arrParams);

			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Se agrego comisión');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al agregar comisión');
    }

	public function cambiarIncoterms($ID, $Nu_Estado, $sCorrelativo){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Tipo_Incoterms' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			$arrParams = array('ID_Pedido_Cabecera' => $ID);
			$this->actualizarProgresoPedido($arrParams);

			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

	public function cambiarTransporte($ID, $Nu_Estado, $sCorrelativo){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Tipo_Transporte_Maritimo' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			$arrParams = array('ID_Pedido_Cabecera' => $ID);
			$this->actualizarProgresoPedido($arrParams);

			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

	public function actualizarProgresoPedido($arrParams){
        $query ="SELECT Nu_Tipo_Servicio, Ss_Comision_Interna_Trading, Nu_Tipo_Incoterms, Nu_Tipo_Transporte_Maritimo FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $arrParams['ID_Pedido_Cabecera'] . " LIMIT 1";
        $objPedidoTienda = $this->db->query($query)->row();		
		if($objPedidoTienda->Nu_Tipo_Servicio!=0 && $objPedidoTienda->Ss_Comision_Interna_Trading > 0.00 && $objPedidoTienda->Nu_Tipo_Incoterms!=0 && $objPedidoTienda->Nu_Tipo_Transporte_Maritimo!=0) {
			$where_progreso = array(
				'ID_Pedido_Cabecera' => $arrParams['ID_Pedido_Cabecera'],
				'Nu_ID_Interno' => 3
			);
			$data_progreso = array('Nu_Estado_Proceso' => 1);
			if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
				return array('status' => 'success', 'message' => 'Actualizado');
			}
			return array('status' => 'error', 'message' => 'Error al actualizar y agregar progreso compra');
		}
	}

	public function actualizarProgresoPedidoFinal($arrParams){
        $query ="SELECT Ss_Pago_30_Cliente, Ss_Pago_100_Cliente, Ss_Pago_Servicio_Cliente, Ss_Pago_Otros_Flete, Ss_Pago_Otros_Costo_Origen, Ss_Pago_Otros_Costo_Fta, Ss_Pago_Otros_Cuadrilla, Ss_Pago_Otros_Costos FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $arrParams['ID_Pedido_Cabecera'] . " LIMIT 1";
        $objPedidoTienda = $this->db->query($query)->row();
		if(
			$objPedidoTienda->Ss_Pago_30_Cliente > 0.00 &&
			$objPedidoTienda->Ss_Pago_100_Cliente > 0.00 &&
			$objPedidoTienda->Ss_Pago_Servicio_Cliente > 0.00 &&
			$objPedidoTienda->Ss_Pago_Otros_Flete > 0.00 &&
			$objPedidoTienda->Ss_Pago_Otros_Costo_Origen > 0.00 &&
			$objPedidoTienda->Ss_Pago_Otros_Costo_Fta > 0.00
		) {
			$where_progreso = array(
				'ID_Pedido_Cabecera' => $arrParams['ID_Pedido_Cabecera'],
				'Nu_ID_Interno' => 4
			);
			$data_progreso = array('Nu_Estado_Proceso' => 1);
			if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
				$where = array('ID_Pedido_Cabecera' => $arrParams['ID_Pedido_Cabecera']);
				$data = array('Nu_Etapa_Pedido' => 1);
				if ($this->db->update($this->table, $data, $where) > 0) {
					return array('status' => 'success', 'message' => 'Actualizado');
				} else {
					return array('status' => 'error', 'message' => 'Error al finalizar etapa');
				}
			}
			return array('status' => 'error', 'message' => 'Error al actualizar y agregar finalizar etapa');
		}
	}

	public function addPagoFlete($arrPost, $data_files){
		if (isset($data_files['pago_flete']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('pago_flete')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['pago_flete-id_cabecera']);
				$data = array(
					'Txt_Url_Pago_Otros_Flete' => $Txt_Url_Imagen_Producto,
					'ID_Pais_Otros_Flete' => $arrPost['pago_flete-ID_Pais_Otros_Flete'],
					'Fe_Pago_Otros_Flete' => ToDate($arrPost['pago_flete-Fe_Pago']),
					'Ss_Pago_Otros_Flete' => $arrPost['pago_flete-Ss_Pago_Otros_Flete'],
					'Nu_Operacion_Pago_Otros_Flete' => $arrPost['pago_flete-Nu_Operacion_Pago_Otros_Flete']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_flete-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPagoFlete($id){
		$query = "SELECT Txt_Url_Pago_Otros_Flete AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addPagoCostosOrigen($arrPost, $data_files){
		if (isset($data_files['costos_origen']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('costos_origen')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['costos_origen-id_cabecera']);
				$data = array(
					'Txt_Url_Pago_Otros_Costo_Origen' => $Txt_Url_Imagen_Producto,
					'ID_Pais_Otros_Costo_Origen' => $arrPost['costos_origen-ID_Pais_Otros_Costo_Origen'],
					'Fe_Pago_Otros_Costo_Origen' => ToDate($arrPost['costos_origen-Fe_Pago_Otros_Costo_Origen']),
					'Ss_Pago_Otros_Costo_Origen' => $arrPost['costos_origen-Ss_Pago_Otros_Costo_Origen'],
					'Nu_Operacion_Pago_Otros_Costo_Origen' => $arrPost['costos_origen-Nu_Operacion_Pago_Otros_Costo_Origen']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['costos_origen-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPagoCostosOrigen($id){
		$query = "SELECT Txt_Url_Pago_Otros_Costo_Origen AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addPagoFta($arrPost, $data_files){
		if (isset($data_files['pago_fta']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('pago_fta')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['pago_fta-id_cabecera']);
				$data = array(
					'Txt_Url_Pago_Otros_Costo_Fta' => $Txt_Url_Imagen_Producto,
					'ID_Pais_Otros_Costo_Fta' => $arrPost['pago_fta-ID_Pais_Otros_Costo_Fta'],
					'Fe_Pago_Otros_Costo_Fta' => ToDate($arrPost['pago_fta-Fe_Pago_Otros_Costo_Fta']),
					'Ss_Pago_Otros_Costo_Fta' => $arrPost['pago_fta-Ss_Pago_Otros_Costo_Fta'],
					'Nu_Operacion_Pago_Otros_Costo_Fta' => $arrPost['pago_fta-Nu_Operacion_Pago_Otros_Costo_Fta']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_fta-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPagoFTA($id){
		$query = "SELECT Txt_Url_Pago_Otros_Costo_Fta AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addOtrosCuadrilla($arrPost, $data_files){
		if (isset($data_files['otros_cuadrilla']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('otros_cuadrilla')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['otros_cuadrilla-id_cabecera']);
				$data = array(
					'No_Concepto_Pago_Cuadrilla' => $arrPost['otros_cuadrilla-No_Concepto_Pago_Cuadrilla'],
					'Txt_Url_Pago_Otros_Cuadrilla' => $Txt_Url_Imagen_Producto,
					'ID_Pais_Otros_Cuadrilla' => $arrPost['otros_cuadrilla-ID_Pais_Otros_Cuadrilla'],
					'Fe_Pago_Otros_Cuadrilla' => ToDate($arrPost['otros_cuadrilla-Fe_Pago_Otros_Cuadrilla']),
					'Ss_Pago_Otros_Cuadrilla' => $arrPost['otros_cuadrilla-Ss_Pago_Otros_Cuadrilla'],
					'Nu_Operacion_Pago_Otros_Cuadrilla' => $arrPost['otros_cuadrilla-Nu_Operacion_Pago_Otros_Cuadrilla']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['otros_cuadrilla-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPagoCuadrilla($id){
		$query = "SELECT Txt_Url_Pago_Otros_Cuadrilla AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function addOtrosCostos($arrPost, $data_files){
		if (isset($data_files['otros_costos']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/pagos_clientes/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
			$config['max_size'] = 3072;//1024 KB = 10 MB
			$config['encrypt_name'] = TRUE;
			$config['max_filename'] = '255';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('otros_costos')){
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
				);
			} else {
				$arrUploadFile = $this->upload->data();
				$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

				$where = array('ID_Pedido_Cabecera' => $arrPost['otros_costos-id_cabecera']);
				$data = array(
					'Txt_Url_Pago_Otros_Costos' => $Txt_Url_Imagen_Producto,
					'ID_Pais_Otros_Costos' => $arrPost['otros_costos-ID_Pais_Otros_Costos'],
					'Fe_Pago_Otros_Costos' => ToDate($arrPost['otros_costos-Fe_Pago_Otros_Costos']),
					'Ss_Pago_Otros_Costos' => $arrPost['otros_costos-Ss_Pago_Otros_Costos'],
					'Nu_Operacion_Pago_Otros_Costos' => $arrPost['otros_costos-Nu_Operacion_Pago_Otros_Costos']
				);//1=SI
				$this->db->update($this->table, $data, $where);
			}
			
			$arrParams = array('ID_Pedido_Cabecera' => $arrPost['otros_costos-id_cabecera']);
			$this->actualizarProgresoPedidoFinal($arrParams);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarPagoOtrosCostos($id){
		$query = "SELECT Txt_Url_Pago_Otros_Costos AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

    public function actualizarProveedor($where_entidad, $data, $where_detalle_item){
		if ( $this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where_detalle_item) > 0 ) {
			$data = array(
				'No_Wechat' => $data['No_Wechat']
			);
			if ( $this->db->update('entidad', $data, $where_entidad) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		} else {
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al item proveedor');
		}
    }
	
	public function getPedidoProveedor($ID){
		$query = "SELECT No_Wechat, No_Rubro, No_Cuenta_Bancaria, Ss_Pago_Importe_1 FROM agente_compra_pedido_detalle_producto_proveedor WHERE ID_Pedido_Detalle_Producto_Proveedor = " . $ID . " LIMIT 1";
		return $this->db->query($query)->row();
	}

    public function reservaBooking($where, $data){
		if ( $this->db->update($this->table, $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
	
	public function getBooking($ID){
		$query = "SELECT Qt_Caja_Total_Booking, Qt_Cbm_Total_Booking, Qt_Peso_Total_Booking FROM agente_compra_pedido_cabecera WHERE ID_Pedido_Cabecera = " . $ID . " LIMIT 1";
		return $this->db->query($query)->row();
	}

    public function actualizarRecepcionCargaItemProveedor($where, $data){
		if ( $this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function actualizarRecepcionCargaProveedor($where, $data){
		if ( $this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

	public function subirInvoicePlProveedor($arrPost, $data_files){
		if (isset($data_files['image_documento']['name'])) {
			$this->db->trans_begin();
			$path = "assets/images/invoice_pl_proveedor/";

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
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

				$where = array('ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['documento-id']);
				$data = array( 'Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor' => $Txt_Url_Imagen_Producto );//1=SI
				$this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where);
			}
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message' => 'Error al insertar');
			} else {
				$this->db->trans_commit();
				return array('status' => 'success', 'message' => 'Documento guardado');
			}
		} else {
			return array('status' => 'error', 'message' => 'No existe archivo');
		}
	}
	
	public function descargarInvoicePlProveedor($id){
		$query = "SELECT Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor AS Txt_Url_Imagen_Producto FROM " . $this->table_agente_compra_pedido_detalle_producto_proveedor . " WHERE ID_Pedido_Detalle_Producto_Proveedor = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}

    public function despacho($where, $data){
		if ( $this->db->update($this->table, $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

	public function asignarUsuarioPedidoChina($arrPost){
        $where = array('ID_Pedido_Cabecera' => $arrPost['guardar_personal_china-ID_Pedido_Cabecera']);
        $data = array( 'ID_Usuario_Interno_Empresa_China' => $arrPost['cbo-guardar_personal_china-ID_Usuario']);
		if ($this->db->update($this->table, $data, $where) > 0) {
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
	
	public function generarEstadoProcesoAgenteCompra($arrDataTour){
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '1. Verificar datos de exportación',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '11',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '2. Pago a Proveedor (Inicial)',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '12',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '3. Reserva de Booking',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '13',
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
			'Nu_ID_Interno' => '14',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '5. Pago a Proveedor (Final)',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '15',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '6. Docs Exportación',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '16',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '7. Verificar llenado de contenedor',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '17',
			'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
		);
		
		if ($this->db->insert_batch('proceso_agente_compra_pedido', $proceso_agente_compra_pedido)>0)
			return array('status' => 'success', 'message' => 'Registro guardado');
		return array('status' => 'error', 'message' => 'Error al guardar');
	}
}
