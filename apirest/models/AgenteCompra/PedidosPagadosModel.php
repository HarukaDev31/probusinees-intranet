<?php
class PedidosPagadosModel extends CI_Model{
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
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select('CORRE.Fe_Month, Nu_Estado_China,' . $this->table . '.*, P.No_Pais, 
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto')
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
		->where($this->table . '.Nu_Estado>=', 5);
        
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
		ACPDPP.Qt_Producto_Caja_Final AS Qt_Producto,
		ACPDPP.Ss_Precio,
		ACPDPP.Nu_Dias_Delivery,
		ACPDPP.Txt_Url_Archivo_Pago_1_Proveedor,
		ACPDPP.Ss_Pago_1_Proveedor,
		ACPDPP.Txt_Url_Archivo_Pago_2_Proveedor,
		ACPDPP.Ss_Pago_2_Proveedor,
		ACPDPP.Nu_Agrego_Inspeccion');
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

    public function get_by_id_inspeccion($ID){
        $this->db->select('ID_Pedido_Detalle_Producto_Inspeccion, Txt_Url_Imagen_Producto');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor_inspeccion);
        $this->db->where($this->table_agente_compra_pedido_detalle_producto_proveedor_inspeccion . '.ID_Pedido_Detalle_Producto_Proveedor',$ID);
        $query = $this->db->get();
        return $query->result();
    }

	public function cambiarEstado($ID, $Nu_Estado, $id_correlativo){
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
}