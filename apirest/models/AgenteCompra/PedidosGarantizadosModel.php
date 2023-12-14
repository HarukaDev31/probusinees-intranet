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
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, P.No_Pais, 
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		CORRE.Fe_Month')
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
		->where($this->table . '.Nu_Estado>=', 2)
		->where($this->table . '.Nu_Estado<', 5);
        
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
		EMP.No_Empresa, EMP.Txt_Direccion_Empresa, EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
		CONFI.No_Logo_Empresa, CONFI.No_Imagen_Logo_Empresa, CONFI.Nu_Height_Logo_Ticket,
		CONFI.Nu_Width_Logo_Ticket, ' . $this->table . '.*,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		IGPD.ID_Pedido_Detalle, IGPD.Txt_Producto, IGPD.Txt_Descripcion, IGPD.Qt_Producto, IGPD.Txt_Url_Imagen_Producto, IGPD.Txt_Url_Link_Pagina_Producto,
		TDI.No_Tipo_Documento_Identidad_Breve, ' . $this->table . '.Nu_Estado AS Nu_Estado_Pedido, CONFI.Txt_Cuentas_Bancarias');
        $this->db->from($this->table);
    	$this->db->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join');
		$this->db->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
		$this->db->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join');
		$this->db->join($this->table_configuracion . ' AS CONFI', 'CONFI.ID_Empresa = EMP.ID_Empresa', 'join');
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
		ACPDPP.Nu_Dias_Delivery');
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
		ACPDPP.Txt_Nota,
		ACPDPP.Nu_Selecciono_Proveedor,
		ACPDPP.Qt_Producto_Caja_Final,
		ACPDPP.Txt_Nota_Final,
		ACPDPPI.Txt_Url_Imagen_Producto
		');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP');
		$this->db->join($this->table . ' AS ACPC', 'ACPC.ID_Pedido_Cabecera = ACPDPP.ID_Pedido_Cabecera', 'join');
		$this->db->join($this->table_agente_compra_pedido_detalle_producto_proveedor_imagen . ' AS ACPDPPI', 'ACPDPPI.ID_Pedido_Detalle_Producto_Proveedor = ACPDPP.ID_Pedido_Detalle_Producto_Proveedor', 'join');
        $this->db->where('ACPDPP.ID_Pedido_Cabecera',$ID);
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

    public function actualizarElegirItemProductos($arrPost){
		$this->db->trans_begin();

		foreach ($arrPost['addProducto'] as $row) {
			$cantidad = $row['cantidad'];
			if($row['cantidad'] < $row['cantidad_oculta'])
				$cantidad = $row['cantidad_oculta'];
			$arrActualizar[] = array(
				'ID_Pedido_Detalle_Producto_Proveedor' => $row['id_detalle'],
				'Qt_Producto_Caja_Final' => $cantidad,
				'Txt_Nota_Final' => $row['nota'],
			);
		}
		
		$this->db->update_batch($this->table_agente_compra_pedido_detalle_producto_proveedor, $arrActualizar, 'ID_Pedido_Detalle_Producto_Proveedor');

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'message' => 'error al actualizar datos');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'message' => 'Datos actualizados');
		}
    }

	public function elegirItemProveedor($id_detalle, $ID, $status){
		$query = "SELECT Nu_Selecciono_Proveedor FROM agente_compra_pedido_detalle_producto_proveedor WHERE ID_Pedido_Detalle = " . $id_detalle . " AND Nu_Selecciono_Proveedor=1";
		$objProveedor = $this->db->query($query)->row();
		//if(!is_object($objProveedor)){
			$where = array('ID_Pedido_Detalle_Producto_Proveedor' => $ID);
			$data = array( 'Nu_Selecciono_Proveedor' => $status );//1=proveedor seleccionado
			if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0) {
				return array('status' => 'success', 'message' => 'Proveedor seleccionado');
			}
			return array('status' => 'error', 'message' => 'Error al seleccionar proveedor');
		/*
		} else {
			return array('status' => 'error', 'message' => 'Primero desmarcar proveedor');
		}
		*/
	}

	public function cambiarEstado($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}

	public function cambiarEstadoChina($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado_China' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}
    
    public function actualizarPedido($where, $data, $arrProducto){
		$this->db->trans_begin();

		//actualizar cabecera
		$data_cabecera = array(
			'Ss_Tipo_Cambio' => $data['Ss_Tipo_Cambio'],
		);
		$where_cabecera = array(
			'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
		);
		$this->db->update($this->table, $data_cabecera, $where_cabecera);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		} else {
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
				'Txt_Nota' => $row['nota'],
				'No_Contacto_Proveedor' => $row['contacto_proveedor'],
				'Txt_Url_Imagen_Proveedor' => $Txt_Url_Imagen_Proveedor,
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
}
