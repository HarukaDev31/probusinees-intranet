<?php
class ProductoProveedorModel extends CI_Model{
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
    //var $order = array('rand()' => 'desc');
    var $order = array('PRO.Fe_Registro' => 'desc');
    
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
        if( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>2 && $this->input->post('Filtros_Productos') == 'Producto' ){
            $this->db->like('No_Producto', $this->input->post('Global_Filter'));
        }
		
		if ( !empty($this->input->post('ID_Filtro_Proveedor')) )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('ID_Filtro_Proveedor'));
		
		if ( !empty($this->input->post('ID_Filtro_Almacen')) )
			$this->db->where('ALMA.ID_Almacen', $this->input->post('ID_Filtro_Almacen'));

		$this->db->select('STOCK.Qt_Producto, STOCK.ID_Stock_Producto, PRO.Nu_Codigo_Barra, PRO.Txt_Url_Recurso_Drive, EMP.No_Empresa, ALMA.No_Almacen, ALMA.Txt_Direccion_Almacen, PRO.ID_Empresa, PRO.ID_Producto, No_Producto, PRO.Ss_Precio_Ecommerce_Online_Regular, PRO.Ss_Precio_Vendedor_Dropshipping, No_Imagen_Item, PRO.Nu_Version_Imagen, PRO.Ss_Precio_Proveedor_Dropshipping')
		->from('producto AS PRO')
		->join('empresa AS EMP', 'EMP.ID_Empresa = PRO.ID_Empresa', 'join')
		->join('organizacion AS ORG', 'ORG.ID_Empresa = PRO.ID_Empresa', 'join')
		->join('almacen AS ALMA', 'ALMA.ID_Organizacion = ORG.ID_Organizacion', 'join')
		->join('stock_producto AS STOCK', 'STOCK.ID_Almacen = ALMA.ID_Almacen AND STOCK.ID_Producto = PRO.ID_Producto', 'join')
		->where('EMP.Nu_Proveedor_Dropshipping', 1)
		->where('EMP.Nu_Estado', 1)
		->where('EMP.ID_Pais',  $this->user->ID_Pais)
		->where('ALMA.Nu_Estado', 1)
		->where('PRO.Nu_Activar_Item_Lae_Shop', 1);
		/*
		$this->db->select('EMP.No_Empresa, ALMA.No_Almacen, ALMA.Txt_Direccion_Almacen, PRO.ID_Empresa, PRO.ID_Producto, No_Producto, STOCK.Qt_Producto AS Qt_Producto, PRO.Ss_Precio_Ecommerce_Online_Regular, PRO.Ss_Precio_Vendedor_Dropshipping, No_Imagen_Item, PRO.Nu_Version_Imagen, PRO.Ss_Precio_Proveedor_Dropshipping')
		->from('stock_producto AS STOCK')
		->join('almacen AS ALMA', 'STOCK.ID_Almacen = ALMA.ID_Almacen', 'join')
		->join('producto AS PRO', 'STOCK.ID_Empresa = PRO.ID_Empresa AND STOCK.ID_Producto = PRO.ID_Producto', 'right')
		->join('empresa AS EMP', 'EMP.ID_Empresa = PRO.ID_Empresa', 'join')
		->where('EMP.Nu_Proveedor_Dropshipping', 1)
		->where('EMP.Nu_Estado', 1)
		->where('PRO.Nu_Activar_Item_Lae_Shop', 1);
		*/
		
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
        }

		if ( !empty($this->input->post('ID_Filtro_Proveedor')) )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('ID_Filtro_Proveedor'));
		
		if ( !empty($this->input->post('ID_Filtro_Almacen')) )
			$this->db->where('ALMA.ID_Almacen', $this->input->post('ID_Filtro_Almacen'));
			
		$this->db->select('EMP.No_Empresa, ALMA.No_Almacen, ALMA.Txt_Direccion_Almacen, PRO.ID_Empresa, PRO.ID_Producto, No_Producto, STOCK.Qt_Producto AS Qt_Producto, PRO.Ss_Precio_Ecommerce_Online_Regular, PRO.Ss_Precio_Vendedor_Dropshipping, No_Imagen_Item, PRO.Nu_Version_Imagen, PRO.Ss_Precio_Proveedor_Dropshipping')
		->from('stock_producto AS STOCK')
		->join('almacen AS ALMA', 'STOCK.ID_Almacen = ALMA.ID_Almacen', 'join')
		->join('producto AS PRO', 'STOCK.ID_Producto = PRO.ID_Producto', 'right')
		->join('empresa AS EMP', 'EMP.ID_Empresa = PRO.ID_Empresa', 'join')
		->where('EMP.Nu_Proveedor_Dropshipping', 1)
		->where('EMP.Nu_Estado', 1)
		->where('PRO.Nu_Activar_Item_Lae_Shop', 1);
		
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
		/*
		if($data['Nu_Codigo_Barra'] != '' && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Codigo_Barra='" . $data['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Código de Barra ' . $data['Nu_Codigo_Barra'] . ' ya existe');
		} else {
		*/
		if($this->db->query("SELECT COUNT(*) AS existe FROM producto WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Producto_Relacion_Producto_Dropshipping='" . $data['ID_Producto_Relacion_Producto_Dropshipping'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya registraste el producto > ' . $data['No_Producto'] . '. Debes elegir otro');
		} else {
			$this->db->trans_begin();
			if($No_Imagen_Item = $this->setPredeterminado_nuevo($data_imagen))
				$data["No_Imagen_Item"] = $No_Imagen_Item;
			
			$this->db->insert($this->table, $data);
			$Last_ID_Producto = $this->db->insert_id();

			/*
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
			*/
			
			if (!empty($data_imagen)) {
				for($i = 0; $i < count($data_imagen); $i++){
					$table_imagen[] = array(
						'No_Producto_Imagen' => $data_imagen[$i]["No_Producto_Imagen"],
						'ID_Producto' => $Last_ID_Producto,
						'Imagen_Tamano'	=> $data_imagen[$i]["Imagen_Tamano"],
						'ID_Predeterminado'	=> $data_imagen[$i]["ID_Predeterminado"]
					);
				}

				if(count($data_imagen)>0) {
					$this->db->insert_batch('producto_imagen', $table_imagen);
				}
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
				$this->revisarArchivoFacebookCron($data['ID_Empresa']);
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

			/*
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
			*/

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
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
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
      return json_encode(array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Borrado satisfactoriamente'));
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
		if ($this->db->update('producto', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado satisfactoriamente');
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

	public function obtenerStockActualProveedorDropshipping($arrParamsProductoProveedorDropshipping){
		$query = "SELECT ID_Stock_Producto, Qt_Producto FROM stock_producto WHERE ID_Producto = " . $arrParamsProductoProveedorDropshipping['ID_Producto'] . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function getEmpresaProductoProveeedor($ID_Producto_Imagen){
		$query = "SELECT EMP.Nu_Documento_Identidad FROM
producto AS ITEM
JOIN empresa AS EMP ON(ITEM.ID_Empresa=EMP.ID_Empresa)
WHERE
ITEM.ID_Producto = " . $ID_Producto_Imagen . " LIMIT 1";
		return $this->db->query($query)->row();  

	}

	function getGaleriaProveedor($ID_Producto){
		$this->db->select('*');
	    $this->db->from("producto_imagen");
	    $this->db->where("ID_Producto",$ID_Producto);
	    $query = $this->db->get();
	    return $query->result();
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

}