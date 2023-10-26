<?php
class ProductoModel extends CI_Model{
	var $table = 'producto';
	var $table_stock_producto = 'stock_producto';
	var $table_enlace_producto = 'enlace_producto';
	var $table_tabla_dato = 'tabla_dato';
	var $table_familia = 'familia';
	var $table_subfamilia = 'subfamilia';
	var $table_impuesto = 'impuesto';
	var $table_impuesto_cruce_documento = 'impuesto_cruce_documento';
	var $table_marca = 'marca';
	var $table_unidad_medida = 'unidad_medida';
	var $table_laboratorio = 'laboratorio';
	var $table_documento_detalle = 'documento_detalle';
	var $table_lista_precio_detalle = 'lista_precio_detalle';
	var $table_variante_item = 'variante_item';
	var $table_variante_item_detalle = 'variante_item_detalle';
	
    var $column_order = array('No_Descripcion_Grupo', 'Nu_Codigo_Barra', 'No_Producto', 'Qt_Producto', 'Ss_Precio_Ecommerce_Online_Regular', 'Ss_Precio_Ecommerce_Online');
    var $column_search = array();
    var $order = array('Fe_Registro' => 'desc', 'Nu_Activar_Item_Lae_Shop' => 'desc');
    
	private $upload_path = '../assets/images/productos/';
	private $_batchImport;
	
	public function __construct(){
		parent::__construct();
	}
 
    public function setBatchImport($arrProducto) {
        $this->_batchImport = $arrProducto;
    }
    
    public function importData() {
	    $ID_Empresa = $this->user->ID_Empresa;
		$iIdTipoLavado = 0;
    	$ID_Impuesto = 0;
    	$ID_Marca = 0;
    	$ID_Unidad_Medida = 0;
    	$ID_Familia = 0;
    	$ID_Sub_Familia = 0;
		$ID_Producto_Sunat = 0;
		$ID_Laboratorio = 0;
    	$ID_Familia_Marketplace = 0;
    	$ID_Sub_Familia_Marketplace = 0;
    	$ID_Marca_Marketplace = 0;
        
		$this->db->trans_begin();
        foreach ($this->_batchImport as $row) {
    		$ID_Impuesto = 0;
        	if ( !empty($row['No_Impuesto']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_impuesto . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Impuesto='" . $row['No_Impuesto'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Impuesto = $this->db->query("SELECT ID_Impuesto FROM " . $this->table_impuesto . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Impuesto='" . $row['No_Impuesto'] . "' LIMIT 1")->row()->ID_Impuesto;
			}
			
			if ( empty($ID_Impuesto) ){
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe impuesto -> ' . $row['No_Impuesto']);
			}

    		$ID_Familia = 0;
        	if ( !empty($row['No_Familia']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_familia . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Familia='" . $row['No_Familia'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Familia = $this->db->query("SELECT ID_Familia FROM " . $this->table_familia . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Familia= '" . $row['No_Familia'] . "' LIMIT 1")->row()->ID_Familia;
        	}
        	
			if ( empty($ID_Familia) ){
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe Categoria -> ' . $row['No_Familia']);
			}
        	
    		$ID_Sub_Familia = 0;
        	if ( !empty($row['No_Sub_Familia']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_subfamilia . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Familia=" . $ID_Familia . " AND No_Sub_Familia='" . $row['No_Sub_Familia'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Sub_Familia = $this->db->query("SELECT ID_Sub_Familia FROM " . $this->table_subfamilia . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Familia=" . $ID_Familia . " AND No_Sub_Familia= '" . $row['No_Sub_Familia'] . "' LIMIT 1")->row()->ID_Sub_Familia;
        	}
			
    		$ID_Marca = 0;
        	if ( !empty($row['No_Marca']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_marca . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Marca='" . $row['No_Marca'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Marca = $this->db->query("SELECT ID_Marca FROM " . $this->table_marca . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Marca='" . $row['No_Marca'] . "' LIMIT 1")->row()->ID_Marca;
        	}
        	
    		$ID_Unidad_Medida = 0;
        	if ( !empty($row['No_Unidad_Medida']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_unidad_medida . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Unidad_Medida='" . $row['No_Unidad_Medida'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Unidad_Medida = $this->db->query("SELECT ID_Unidad_Medida FROM " . $this->table_unidad_medida . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Unidad_Medida='" . $row['No_Unidad_Medida'] . "' LIMIT 1")->row()->ID_Unidad_Medida;
        	}
        	
			if ( empty($ID_Unidad_Medida) ){
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe Unidad de Medida -> ' . $row['No_Unidad_Medida']);
			}
        				
        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe == 0){
            	$_arrProducto = array(
					'ID_Empresa' => $ID_Empresa,
					'Nu_Tipo_Producto' => $row['Nu_Tipo_Producto'],
					'ID_Tipo_Producto' => $row['ID_Tipo_Producto'],
					'Nu_Codigo_Barra' => $row['Nu_Codigo_Barra'],
					'No_Producto' => $row['No_Producto'],
					'ID_Impuesto' => $ID_Impuesto,
					'Ss_Precio_Ecommerce_Online_Regular' => $row['fPrecio'],
					'Ss_Precio_Ecommerce_Online' => ($row['fPrecioOferta'] < $row['fPrecio'] ? $row['fPrecioOferta'] : 0),
					'ID_Familia' => $ID_Familia,
					'ID_Unidad_Medida' => $ID_Unidad_Medida,
					'Nu_Activar_Item_Lae_Shop' => (($row['iEstado'] != 0 || $row['iEstado'] == '' || $row['iEstado'] == NULL ) ? 1 : 0),
					'Nu_Destacado_Item_Lae_Shop' => (($row['iEstadoDestacado'] != 0 || $row['iEstadoDestacado'] == '' || $row['iEstadoDestacado'] == NULL ) ? 1 : 0),
					'Txt_Producto' => $row['Txt_Producto'],
					'ID_Sub_Familia' => (!empty($ID_Sub_Familia) ? $ID_Sub_Familia : NULL),
					'ID_Marca' => (!empty($ID_Marca) ? $ID_Marca : NULL),
            	);
				$arrProducto[] = $_arrProducto;
        	} else {
        		$ID_Producto = $this->db->query("SELECT ID_Producto FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->ID_Producto;
        		$_arrProductoUPD = array(
					'ID_Producto' => $ID_Producto,
					'ID_Empresa' => $ID_Empresa,
					'Nu_Tipo_Producto' => $row['Nu_Tipo_Producto'],
					'ID_Tipo_Producto' => $row['ID_Tipo_Producto'],
					'Nu_Codigo_Barra' => $row['Nu_Codigo_Barra'],
					'No_Producto' => $row['No_Producto'],
					'ID_Impuesto' => $ID_Impuesto,
					'Ss_Precio_Ecommerce_Online_Regular' => $row['fPrecio'],
					'Ss_Precio_Ecommerce_Online' => ($row['fPrecioOferta'] < $row['fPrecio'] ? $row['fPrecioOferta'] : 0),
					'ID_Familia' => $ID_Familia,
					'ID_Unidad_Medida' => $ID_Unidad_Medida,
					'Nu_Activar_Item_Lae_Shop' => (($row['iEstado'] != 0 || $row['iEstado'] == '' || $row['iEstado'] == NULL ) ? 1 : 0),
					'Nu_Destacado_Item_Lae_Shop' => (($row['iEstadoDestacado'] != 0 || $row['iEstadoDestacado'] == '' || $row['iEstadoDestacado'] == NULL ) ? 1 : 0),
					'Txt_Producto' => $row['Txt_Producto'],
					'ID_Sub_Familia' => (!empty($ID_Sub_Familia) ? $ID_Sub_Familia : NULL),
					'ID_Marca' => (!empty($ID_Marca) ? $ID_Marca : NULL),
            	);
				$arrProductoUPD[] = $_arrProductoUPD;
        	}
        }

        $bStatus=false;
        if (isset($arrProducto) && is_array($arrProducto))
    		$this->db->insert_batch($this->table, $arrProducto);
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;
    	if (isset($arrProductoUPD) && is_array($arrProductoUPD))
    		$this->db->update_batch($this->table, $arrProductoUPD, 'ID_Producto');
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;

    	unset($arrProducto);
    	unset($arrProductoUPD);
    	
		if ( $bStatus ){
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Datos cargados satisfactoriamente');
		} else {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al obtener familia -> ' . $row['No_Familia']);
		}
	}
	
	public function _get_datatables_query(){
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Producto' ){
            $this->db->like('No_Producto', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'CodigoBarra' ){
        	$this->db->like('Nu_Codigo_Barra', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Categoria' ){
        	$this->db->like('F.No_Familia', $this->input->post('Global_Filter'));
        }

		if ( $this->input->post('Filtro_Nu_Estado') != '-' )
        	$this->db->where('PRO.Nu_Activar_Item_Lae_Shop', $this->input->post('Filtro_Nu_Estado'));

		$this->db->select('PRO.ID_Empresa, PRO.ID_Producto, Nu_Codigo_Barra, No_Producto, STOCK.Qt_Producto AS Qt_Producto, PRO.No_Imagen_Item, PRO.Nu_Version_Imagen, PRO.Nu_Activar_Item_Lae_Shop AS Nu_Estado, PRO.Ss_Precio_Ecommerce_Online_Regular, PRO.Ss_Precio_Ecommerce_Online, Nu_Destacado_Item_Lae_Shop, PRO.Ss_Precio_Vendedor_Dropshipping, PRO.Ss_Precio_Proveedor_Dropshipping, PRO.ID_Producto_Relacion_Producto_Dropshipping')
		->from($this->table . ' AS PRO')
		->join($this->table_stock_producto . ' AS STOCK', 'STOCK.ID_Organizacion = ' . $this->empresa->ID_Organizacion . ' AND STOCK.ID_Almacen = ' . $this->session->userdata['almacen']->ID_Almacen . ' AND STOCK.ID_Producto = PRO.ID_Producto', 'left')
		->where('PRO.ID_Empresa', $this->empresa->ID_Empresa)
		->where('PRO.ID_Tipo_Producto !=', 3);
				
        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	function get_datatables(){
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all(){
		return 0;
		/*
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Producto' ){
            $this->db->like('No_Producto', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'CodigoBarra' ){
        	$this->db->like('Nu_Codigo_Barra', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Categoria' ){
        	$this->db->like('F.No_Familia', $this->input->post('Global_Filter'));
        }

		if ( $this->input->post('Filtro_Nu_Estado') != '-' )
        	$this->db->where('PRO.Nu_Estado', $this->input->post('Filtro_Nu_Estado'));

		$this->db->select('PRO.ID_Empresa, PRO.ID_Producto, Nu_Codigo_Barra, No_Producto, STOCK.Qt_Producto AS Qt_Producto, PRO.No_Imagen_Item, PRO.Nu_Version_Imagen, PRO.Nu_Activar_Item_Lae_Shop AS Nu_Estado, PRO.Ss_Precio_Ecommerce_Online_Regular, PRO.Ss_Precio_Ecommerce_Online, Nu_Destacado_Item_Lae_Shop, PRO.Ss_Precio_Vendedor_Dropshipping, PRO.Ss_Precio_Proveedor_Dropshipping, PRO.ID_Producto_Relacion_Producto_Dropshipping')
		->from($this->table . ' AS PRO')
		->join($this->table_stock_producto . ' AS STOCK', 'STOCK.ID_Organizacion = ' . $this->empresa->ID_Organizacion . ' AND STOCK.ID_Almacen = ' . $this->session->userdata['almacen']->ID_Almacen . ' AND STOCK.ID_Producto = PRO.ID_Producto', 'left')
		->where('PRO.ID_Empresa', $this->empresa->ID_Empresa);
		
        return $this->db->count_all_results();
		*/
    }
    
    public function get_by_id($ID){
    	$this->db->select('PRO.*, 0 AS ID_Producto_Sunat, "" AS No_Producto_Sunat');
        $this->db->from($this->table . ' AS PRO');
        //$this->db->join($this->table_tabla_dato . ' AS ITEMSUNAT', 'ITEMSUNAT.ID_Tabla_Dato = PRO.ID_Producto_Sunat AND ITEMSUNAT.No_Relacion="Catalogo_Producto_Sunat"', 'left');
        $this->db->where('PRO.ID_Producto',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function get_by_id_enlace($ID){
        $query = "SELECT
ENLAPRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
ENLAPRO.Qt_Producto_Descargar
FROM
" . $this->table_enlace_producto . " AS ENLAPRO
JOIN " . $this->table . " AS PRO ON(PRO.ID_Producto = ENLAPRO.ID_Producto)
WHERE
ENLAPRO.ID_Producto_Enlace = " . $ID;
        return $this->db->query($query)->result();
    }
    
    public function get_by_id_precios_x_mayor($ID){
        $query = "SELECT Qt_Producto_x_Mayor, Ss_Precio_x_Mayor FROM producto_precio_x_mayor WHERE ID_Producto = " . $ID . " ORDER BY Qt_Producto_x_Mayor ASC";
        return $this->db->query($query)->result();
    }
    
    public function setPredeterminado_nuevo(&$Data){
    	if(!empty($Data)) {//si es array pasa
			$Predeterminado = false;
			for($i=0;$i<count($Data);$i++){
				if($Data[$i]["ID_Predeterminado"]==1)
					$Predeterminado = true;
			}

			$Data[0]["ID_Predeterminado"]=1;

			// print_r($Data[0]);
			// echo "\nactualizar producot con default";
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
			$arrUrlImagePath = explode('..', $path);
			$arrUrlImage = explode('/principal',base_url());
			$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

			return $url_image."/".$Data[0]["No_Producto_Imagen"];
		} else {
			return false;
		}
    }

    public function setPredeterminado_actualizar($ID_Producto){
    	$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
		$arrUrlImagePath = explode('..', $path);
		$arrUrlImage = explode('/principal',base_url());
		$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
    	$query = $this->db->query('SELECT ID_Producto_Imagen,CONCAT("'.$url_image.'/", No_Producto_Imagen) No_Producto_Imagen_url,No_Producto_Imagen,Imagen_Tamano,ID_Predeterminado,ID_Estatus FROM producto_imagen WHERE ID_Producto ='.$ID_Producto.' ORDER BY ID_Predeterminado DESC limit 1');
		$row = $query->row();

		if(!$row)
			return false;

		$where = array('ID_Producto_Imagen' => $row->ID_Producto_Imagen);
        $arrData = array( 'ID_Predeterminado' => 1 );

    	$this->db->update('producto_imagen', $arrData, $where);
		return $row;

    }


    public function agregarProducto($data, $data_enlace, $data_precio_x_mayor,$data_imagen){
		if($data['Nu_Codigo_Barra'] != '' && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Codigo_Barra='" . $data['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Código de Barra ' . $data['Nu_Codigo_Barra'] . ' ya existe');
		} else {
			$this->db->trans_begin();
			if($No_Imagen_Item = $this->setPredeterminado_nuevo($data_imagen))
				$data["No_Imagen_Item"] = $No_Imagen_Item;
			
			$this->db->insert($this->table, $data);
			$Last_ID_Producto = $this->db->insert_id();

			if ( $data['Nu_Activar_Precio_x_Mayor'] == 1 ){//Enlaces con producto
				for($i = 0; $i < count($data_precio_x_mayor); $i++){
					$table_precio_x_mayor[] = array(
						'ID_Empresa' => $data['ID_Empresa'],
						'ID_Producto' => $Last_ID_Producto,
						'Qt_Producto_x_Mayor'	=> $this->security->xss_clean($data_precio_x_mayor[$i]['Qt_Producto_x_Mayor']),
						'Ss_Precio_x_Mayor'	=> $this->security->xss_clean($data_precio_x_mayor[$i]['Ss_Precio_x_Mayor']),
					);
				}
				$this->db->insert_batch('producto_precio_x_mayor', $table_precio_x_mayor);
			}
			if (!empty($data_imagen)) {
				for($i = 0; $i < count($data_imagen); $i++){
					$table_imagen[] = array(
						'No_Producto_Imagen' => $data_imagen[$i]["No_Producto_Imagen"],
						'ID_Producto' => $Last_ID_Producto,
						'Imagen_Tamano'	=> $data_imagen[$i]["Imagen_Tamano"],
						'ID_Predeterminado'	=> $data_imagen[$i]["ID_Predeterminado"]
					);
				}

				if(count($data_imagen)>0)
					$this->db->insert_batch('producto_imagen', $table_imagen);
			}

			/* TOUR TIENDA VIRTUAL */
			$where_tour = array('ID_Empresa' => $data['ID_Empresa'], 'Nu_ID_Interno' => 3);
			//validamos que si complete los siguientes datos
			if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Activar_Item_Lae_Shop=1 LIMIT 1")->row()->cantidad > 0){
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 1);
			} else {
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 0);
			}
			$this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
			/* END TOUR TIENDA VIRTUAL */
			
	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'iIDItem' => $Last_ID_Producto);
	        }
		}
    }
    
    public function actualizarProducto($where, $data, $ENu_Codigo_Barra, $data_enlace, $data_precio_x_mayor){
		if( $ENu_Codigo_Barra != $data['Nu_Codigo_Barra'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Codigo_Barra='" . $data['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Código de Barra ' . $data['Nu_Codigo_Barra'] . ' ya existe');
		} else{
			$this->db->trans_begin();
			
			if($imagen = $this->setPredeterminado_actualizar($where['ID_Producto']))
				$data["No_Imagen_Item"]=$imagen->No_Producto_Imagen_url;

		    $this->db->update($this->table, $data, $where);

	    	$this->db->where('ID_Producto', $where['ID_Producto']);
        	$this->db->delete('producto_precio_x_mayor');
			if ( $data['Nu_Activar_Precio_x_Mayor'] == 1 ){//Enlaces con producto
				for($i = 0; $i < count($data_precio_x_mayor); $i++){
					$table_precio_x_mayor[] = array(
						'ID_Empresa' => $data['ID_Empresa'],
						'ID_Producto' => $where['ID_Producto'],
						'Qt_Producto_x_Mayor' => $this->security->xss_clean($data_precio_x_mayor[$i]['Qt_Producto_x_Mayor']),
						'Ss_Precio_x_Mayor'	=> $this->security->xss_clean($data_precio_x_mayor[$i]['Ss_Precio_x_Mayor']),
					);
				}
				$this->db->insert_batch('producto_precio_x_mayor', $table_precio_x_mayor);
			}

			/* TOUR TIENDA VIRTUAL */
			$where_tour = array('ID_Empresa' => $data['ID_Empresa'], 'Nu_ID_Interno' => 3);
			//validamos que si complete los siguientes datos
			if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Activar_Item_Lae_Shop=1 LIMIT 1")->row()->cantidad > 0){
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 1);
			} else {
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 0);
			}
			$this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
			/* END TOUR TIENDA VIRTUAL */



	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
	        } else {
				$this->revisarArchivoFacebookCron($data['ID_Empresa']);
				$this->revisarArchivoGoogleCron($data['ID_Empresa']);
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado', 'iIDItem'=>$where['ID_Producto']);
	        }
		}
    }
    
	public function eliminarProducto($ID_Empresa, $ID, $Nu_Codigo_Barra, $Nu_Compuesto, $sNombreImagenItem){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_detalle . " WHERE ID_Producto=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El producto tiene movimiento(s)');
		} else if ($this->db->query("SELECT COUNT(*) AS existe FROM pedido_detalle WHERE ID_Producto=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El producto tiene pedido(s)');
		} else if ($this->db->query("SELECT COUNT(*) AS existe
FROM
 " . $this->table_lista_precio_detalle . " AS LPD
 JOIN lista_precio_cabecera AS LPC ON(LPC.ID_Lista_Precio_Cabecera = LPD.ID_Lista_Precio_Cabecera)
WHERE
 LPC.ID_Empresa = " . $ID_Empresa . "
 AND LPD.ID_Producto = " . $ID . "
LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El producto tiene precio(s) asignados');
		} else {
			$this->db->trans_begin();
			
			$objImage = $this->db->query("SELECT No_Imagen_Item FROM producto WHERE ID_Producto=" . $ID . " LIMIT 1")->row();
			$sUrlImage = (is_object($objImage) ? $objImage->No_Imagen_Item : '');

			$this->db->where('ID_Producto_Enlace', $ID);
            $this->db->delete($this->table_enlace_producto);
            
			$this->db->where('ID_Producto', $ID);
            $this->db->delete($this->table);
            
	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
	        } else {
	            $this->db->trans_commit();
				
				if ( !empty($sUrlImage) ) {
					$arrUrlImage = explode($this->empresa->Nu_Documento_Identidad, $sUrlImage);
					$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . $arrUrlImage[1];
					if ( file_exists($path) )
						unlink($path);
				}
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
	        }
		}
	}

	 public function AgregarImagen($data){
        $this->db->insert("producto_imagen", $data);
    return $this->db->insert_id();    
  }

  public function RemoverImagen($data){
    
    $path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
    $row = $this->getImagen($data["ID_Producto_Imagen"],$path);
    $this->db->where('ID_Producto_Imagen', $data["ID_Producto_Imagen"]);
    $this->db->where('ID_Producto', $data["ID_Producto"]);
    $this->db->delete("producto_imagen");

    // print_r($this->db->last_query());
    if ( file_exists($row->No_Producto_Imagen_url) ){
      unlink($row->No_Producto_Imagen_url);

      if($row->ID_Predeterminado==1){
        $where = array('ID_Producto' => $data['ID_Producto']);
        $arrData = array( 'No_Imagen_Item' => NULL );

        if (!($this->db->update('producto', $arrData, $where) > 0))
          return json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error'));
      }
      return json_encode(array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Borrado satisfactoriamente', 'No_Producto_Imagen' => $row->No_Producto_Imagen));
    }
    else
      return json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al Borrar'));

  }

  function DefaultImagen($data){
    $where = array('ID_Producto' => $data['ID_Producto']);
        $arrData = array( 'ID_Predeterminado' => 0 );

    if (!($this->db->update('producto_imagen', $arrData, $where) > 0))
      return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error');

    $where = array('ID_Producto_Imagen' => $data['ID_Producto_Imagen']);
        $arrData = array( 'ID_Predeterminado' => 1 );

    if (!($this->db->update('producto_imagen', $arrData, $where) > 0))
      return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error');

    $path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
    $arrUrlImagePath = explode('..', $path);
    $arrUrlImage = explode('/principal',base_url());
    $url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

    $row = $this->getImagen($data['ID_Producto_Imagen'],$url_image);
    
    $where = array('ID_Producto' => $data['ID_Producto']);
        $arrData = array( 'No_Imagen_Item' =>  $row->No_Producto_Imagen_url);

    if (!($this->db->update('producto', $arrData, $where) > 0))
      return json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error'));
    else
      return json_encode(array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Estado cambiado satisfactoriamente'));
  }

  public function getImagenes($ID_Producto,$path){
        $this->db->select('ID_Producto_Imagen,
               CONCAT("'.$path.'/", No_Producto_Imagen) No_Producto_Imagen_url,
               No_Producto_Imagen,
               Imagen_Tamano,
               ID_Predeterminado,
                 ID_Estatus')

    ->from("producto_imagen")
    ->where('ID_Producto', $ID_Producto); 
    $query = $this->db->get();
    //print_r($this->db->last_query());
    return $query->result();
  }

  public function getImagen($ID_Producto_Imagen,$path){
    
     $this->db->select('ID_Producto_Imagen,
               CONCAT("'.$path.'/", No_Producto_Imagen) No_Producto_Imagen_url,
               No_Producto_Imagen,
               Imagen_Tamano,
               ID_Predeterminado,
                 ID_Estatus')

    ->from("producto_imagen")
    ->where('ID_Producto_Imagen', $ID_Producto_Imagen); 
    $query = $this->db->get();
    return $query->row();

  }
	
    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Producto imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error de producto imagen modificada');
    }

	public function cambiarEstadoTienda($ID, $Nu_Estado){
        $where = array('ID_Producto' => $ID);
        $arrData = array( 'Nu_Activar_Item_Lae_Shop' => $Nu_Estado );
		if ($this->db->update('producto', $arrData, $where) > 0){
			$arrProducto =$this->get_by_id($ID);
			$this->revisarArchivoFacebookCron($arrProducto->ID_Empresa);
			$this->revisarArchivoGoogleCron($arrProducto->ID_Empresa);
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado satisfactoriamente');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}

	public function cambiarEstadoDestacado($ID, $Nu_Estado){
        $where = array('ID_Producto' => $ID);
        $arrData = array( 'Nu_Destacado_Item_Lae_Shop' => $Nu_Estado );
		if ($this->db->update('producto', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado satisfactoriamente');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}

	public function updActivarMasivamenteProductos($arrPost){
        $where = array('ID_Empresa' => $arrPost['ID_Empresa']);
        $arrData = array( 'Nu_Activar_Item_Lae_Shop' => $arrPost['iEstado'] );
		if ($this->db->update('producto', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado satisfactoriamente');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al actualizar');
	}

	public function CrearCatalogo(){
		$data = array(
	        'ID_Empresa' => $this->user->ID_Empresa,
	        'Nu_Estado' => 0
		);

		$this->db->flush_cache();
		$this->db->set('No_Estado_Catalogo_Lae_Shop', 1, FALSE);
		$this->db->where("ID_Empresa",$this->user->ID_Empresa);
		$this->db->update('configuracion');
		$this->db->flush_cache();

		if ( $this->db->insert('catalogo_producto_cron', $data) > 0 ) 
           return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Operacion Exitosa');
       else
       	  return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al Crear Catalogo');

	}


	function VerificarCatalogo(){

		$this->db->select('*');
		$this->db->from("configuracion");
		$this->db->where('ID_Empresa',$this->user->ID_Empresa);
		$query = $this->db->get();
		//print_r($this->db->last_query());
		$row = $query->row();
		if($row)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Operacion Exitosa',"v"=>$row->No_Estado_Catalogo_Lae_Shop,"e"=>$this->user->ID_Empresa);
		else
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al Crear Catalogo');
     }

	public function getUltimoArchivoFacebookCron($ID_Empresa) {
		$this->db->select('*');
		$this->db->from('archivo_facebook_cron');
		$this->db->where('ID_Empresa', $ID_Empresa);
		$this->db->order_by('ID_Archivo_Facebook_Cron', 'DESC');
		$query = $this->db->get();
        return $query->row();
	}

	public function agregarArchivoFacebookCron($ID_Empresa) {
		$this->db->insert("archivo_facebook_cron", ['ID_Empresa'=> $ID_Empresa]);
	}

	public function revisarArchivoFacebookCron($ID_Empresa) {
		$archivo = $this->getUltimoArchivoFacebookCron($ID_Empresa);
		if(count($archivo) > 0) {
			if($archivo->Nu_Estado > 0) {
				$this->agregarArchivoFacebookCron($ID_Empresa);
			}
		} else {
			$this->agregarArchivoFacebookCron($ID_Empresa);
		}
	}

	public function getUltimoArchivoGoogleCron($ID_Empresa) {
		$this->db->select('*');
		$this->db->from('archivo_google_cron');
		$this->db->where('ID_Empresa', $ID_Empresa);
		$this->db->order_by('ID_Archivo_Google_Cron', 'DESC');
		$query = $this->db->get();
        return $query->row();
	}

	public function agregarArchivoGoogleCron($ID_Empresa) {
		$this->db->insert("archivo_google_cron", ['ID_Empresa'=> $ID_Empresa]);
	}

	public function revisarArchivoGoogleCron($ID_Empresa) {
		$archivo = $this->getUltimoArchivoGoogleCron($ID_Empresa);
		if(count($archivo) > 0) {
			if($archivo->Nu_Estado > 0) {
				$this->agregarArchivoGoogleCron($ID_Empresa);
			}
		} else {
			$this->agregarArchivoGoogleCron($ID_Empresa);
		}
	}

    public function getPrecioProveedorRelacionProducto($ID_Producto_Relacion_Producto_Dropshipping){
		$fPrecioProveedor = 0;
        $query ="SELECT Ss_Precio_Proveedor_Dropshipping FROM producto WHERE ID_Producto = " . $ID_Producto_Relacion_Producto_Dropshipping . " LIMIT 1";
		$objPrecioProveedor = $this->db->query($query)->row();
		if(is_object($objPrecioProveedor)){
			$fPrecioProveedor = $objPrecioProveedor->Ss_Precio_Proveedor_Dropshipping;
		}
        return $fPrecioProveedor;
    }

	// ------------------VARIANTES ---------------------- //

	public function getVariantes($ID_Producto, $Nu_Estado = 0) {
		$this->db->select('ID_Variante, No_Variante');
		$this->db->from('variante');
		$this->db->where('ID_Producto', $ID_Producto);
		$this->db->where('Nu_Estado', $Nu_Estado);
		$query = $this->db->get();
        return $query->result();
	}
	
	public function agregarVariante($variante) {
		$this->db->insert('variante', $variante);
		return $this->db->insert_id();
	}

	public function actualizarVariante($No_Variante, $ID_Variante) {
		$this->db->set('No_Variante', $No_Variante);
		$this->db->set('Nu_Estado', 1);
		$this->db->where('ID_Variante', $ID_Variante);
		return $this->db->update('variante') > 0;
	}

	public function inactivarVariante($ID_producto) {
		$this->db->set('Nu_Estado', 0);
		$this->db->where('ID_Producto', $ID_producto);
		return $this->db->update('variante') > 0;
	}

	public function eliminarVariantesInactivas($ID_Producto) {
		$this->db->where('ID_Producto', $ID_Producto);
		$this->db->where('Nu_Estado ', 0);
        return $this->db->delete('variante');
	}

	// ------------------VARIANTES VALORES ---------------------- //

	public function getVarianteValores($ID_Variante, $Nu_Estado = 0) {
		$this->db->select('ID_Variante_Valor, No_Variante_Valor');
		$this->db->from('variante_valor');
		$this->db->where('ID_Variante', $ID_Variante);
		$this->db->where('Nu_Estado', $Nu_Estado);
		$query = $this->db->get();
        return $query->result();
	}

	public function getVarianteValoresByIDProducto($ID_Producto, $Nu_Estado = 0) {
		$this->db->select('VV.ID_Variante_Valor, VV.No_Variante_Valor');
        $this->db->from('variante_valor AS VV');
		$this->db->join('variante AS V','V.ID_Variante = VV.ID_Variante', 'join');
        $this->db->where('V.ID_Producto', $ID_Producto);
		$this->db->where('VV.Nu_Estado', $Nu_Estado);
		$this->db->order_by('VV.ID_Variante', 'ASC');
        $query = $this->db->get();
        return $query->result();
	}
	
	public function agregarVarianteValor($data) {
		$this->db->insert_batch('variante_valor', $data);
	}

	public function actualizarEstadoVariantesValores($ID_Variante_Valores, $Nu_Estado) {
		$this->db->set('Nu_Estado', $Nu_Estado);
		$this->db->where_in('ID_Variante_Valor', $ID_Variante_Valores);
		return $this->db->update('variante_valor');
	}

	public function eliminarVariantesValoresInactivos($ID_Variante_Valores) {
		$this->db->where_in('ID_Variante_Valor', $ID_Variante_Valores);
		$this->db->where('Nu_Estado', 0);
		return $this->db->delete('variante_valor');
	}

	// ------------------PRODUCTOS HIJOS ---------------------- //

	public function getProductosHijos($ID_Producto_Padre, $Nu_Estado_Producto_Hijo = 0) {
		$this->db->select('PRO.*');
        $this->db->from($this->table . ' AS PRO');
        $this->db->where('PRO.ID_Producto_Padre', $ID_Producto_Padre);
		$this->db->where('PRO.Nu_Estado_Producto_Hijo', $Nu_Estado_Producto_Hijo);
        $query = $this->db->get();
        return $query->result();
	}

	public function inactivarProductosHijos($ID_Producto_Padre) {
		$this->db->set('Nu_Estado_Producto_Hijo', 0);
		$this->db->set('Nu_Activar_Item_Lae_Shop', 0);
		$this->db->where('ID_Producto_Padre', $ID_Producto_Padre);
		return $this->db->update('producto');
	}

	public function eliminarProductosHijosInactivos($ID_Productos) {
		$this->db->where_in('ID_Producto', $ID_Productos);
		$this->db->where('Nu_Estado_Producto_Hijo', 0);
		return $this->db->delete('producto');
	}

	public function AgregarImagenProductoHijo($data) {
		$this->db->insert_batch('producto_imagen', $data);
	}

	public function RemoverImagenProductoHijo($ID_Producto, $ID_Predeterminado, $No_Producto_Imagen) {
		$this->db->where_in('ID_Producto', $ID_Producto);
		$this->db->where('No_Producto_Imagen', $No_Producto_Imagen);
		$this->db->delete('producto_imagen');
		if($ID_Predeterminado == 1) {
			$this->db->set('No_Imagen_Item', NULL);
			$this->db->where_in('ID_Producto', $ID_Producto);
			$this->db->update('producto');
		}
	}

	// ------------------PRODUCTOS VARIANTES VALORES ---------------------- //

	public function agregarProductoVarianteValores($data) {
		$this->db->insert_batch('producto_variante_valor', $data);
	}

	public function eliminarProductosVarianteValores($ID_Productos) {
		$this->db->where_in('ID_Producto', $ID_Productos);
        return $this->db->delete('producto_variante_valor');
	}

	// ------------------PRODUCTOS RELACIONADOS ---------------------- //

	public function getProductosRelacionados($ID_Producto_Principal) {
		$this->db->select('PRO.ID_Producto, PRO.Nu_Codigo_Barra AS No_Codigo_Barra, PRO.No_Producto');
		$this->db->from('producto_relacionado AS PR');
		$this->db->join('producto AS PRO','PRO.ID_Producto = PR.ID_Producto', 'join');
		$this->db->where('PR.ID_Producto_Principal', $ID_Producto_Principal);
		$query = $this->db->get();
        return $query->result();
	}

	public function AgregarProductosRelacionados($data) {
		$this->db->insert_batch('producto_relacionado', $data);
	}

	public function eliminarProductosRelacionados($ID_Producto_Principal) {
		$this->db->where('ID_Producto_Principal', $ID_Producto_Principal);
		$this->db->delete('producto_relacionado');
	}

}