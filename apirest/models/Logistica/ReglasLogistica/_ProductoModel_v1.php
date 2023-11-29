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
	
    var $column_order = array('Nu_Tipo_Producto', 'No_Unidad_Medida', 'No_Familia', 'No_Sub_Familia', 'No_Marca', 'Nu_Codigo_Barra', 'No_Codigo_Interno', 'No_Producto', 'No_Impuesto_Breve', 'Qt_Producto', 'Ss_Precio', 'Ss_Costo', 'Ss_Costo_Promedio', 'Nu_Stock_Minimo', 'Nu_Stock_Maximo');
    var $column_search = array();
    var $order = array('Fe_Registro' => 'desc');
    
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
		$ID_Ubicacion_Inventario = 1;
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
        	
        	if ( !empty($row['Nu_Codigo_Producto_Sunat']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_tabla_dato . " WHERE No_Relacion='Catalogo_Producto_Sunat' AND Nu_Valor='" . $row['Nu_Codigo_Producto_Sunat'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Producto_Sunat = $this->db->query("SELECT ID_Tabla_Dato FROM " . $this->table_tabla_dato . " WHERE No_Relacion='Catalogo_Producto_Sunat' AND Nu_Valor='" . $row['Nu_Codigo_Producto_Sunat'] . "' LIMIT 1")->row()->ID_Tabla_Dato;
        	}
        	
        	if ( !empty($row['No_Laboratorio']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_laboratorio . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Laboratorio='" . $row['No_Laboratorio'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Laboratorio = $this->db->query("SELECT ID_Laboratorio FROM " . $this->table_laboratorio . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Laboratorio= '" . $row['No_Laboratorio'] . "' LIMIT 1")->row()->ID_Laboratorio;
        	}
        	
        	if ( !empty($row['iTipoLavado']) ) {
				if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_tabla_dato . " WHERE No_Relacion='Tipos_PedidoLavado' AND Nu_Valor='" . $row['iTipoLavado'] . "' LIMIT 1")->row()->existe > 0)
					$iIdTipoLavado = $this->db->query("SELECT Nu_Valor FROM " . $this->table_tabla_dato . " WHERE No_Relacion='Tipos_PedidoLavado' AND Nu_Valor='" . $row['iTipoLavado'] . "' LIMIT 1")->row()->Nu_Valor;
			}
			
			$ID_Variante_Item_1=0;
        	if ( !empty($row['ID_Variante_Item_1']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_variante_item . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Variante='" . $row['ID_Variante_Item_1'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Variante_Item_1 = $this->db->query("SELECT ID_Variante_Item FROM " . $this->table_variante_item . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Variante='" . $row['ID_Variante_Item_1'] . "' LIMIT 1")->row()->ID_Variante_Item;
        	}
			
			$ID_Variante_Item_Detalle_1=0;
        	if ( !empty($row['ID_Variante_Item_Detalle_1']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_variante_item_detalle . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Valor='" . $row['ID_Variante_Item_Detalle_1'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Variante_Item_Detalle_1 = $this->db->query("SELECT ID_Variante_Item_Detalle FROM " . $this->table_variante_item_detalle . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Valor='" . $row['ID_Variante_Item_Detalle_1'] . "' LIMIT 1")->row()->ID_Variante_Item_Detalle;
        	}
			
			$ID_Variante_Item_2=0;
        	if ( !empty($row['ID_Variante_Item_2']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_variante_item . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Variante='" . $row['ID_Variante_Item_2'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Variante_Item_2 = $this->db->query("SELECT ID_Variante_Item FROM " . $this->table_variante_item . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Variante='" . $row['ID_Variante_Item_2'] . "' LIMIT 1")->row()->ID_Variante_Item;
        	}
			
			$ID_Variante_Item_Detalle_2=0;
        	if ( !empty($row['ID_Variante_Item_Detalle_2']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_variante_item_detalle . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Valor='" . $row['ID_Variante_Item_Detalle_2'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Variante_Item_Detalle_2 = $this->db->query("SELECT ID_Variante_Item_Detalle FROM " . $this->table_variante_item_detalle . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Valor='" . $row['ID_Variante_Item_Detalle_2'] . "' LIMIT 1")->row()->ID_Variante_Item_Detalle;
        	}
			
			$ID_Variante_Item_3=0;
        	if ( !empty($row['ID_Variante_Item_3']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_variante_item . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Variante='" . $row['ID_Variante_Item_3'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Variante_Item_3 = $this->db->query("SELECT ID_Variante_Item FROM " . $this->table_variante_item . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Variante='" . $row['ID_Variante_Item_3'] . "' LIMIT 1")->row()->ID_Variante_Item;
        	}
			
			$ID_Variante_Item_Detalle_3=0;
        	if ( !empty($row['ID_Variante_Item_Detalle_3']) ) {
	       		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_variante_item_detalle . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Valor='" . $row['ID_Variante_Item_Detalle_3'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Variante_Item_Detalle_3 = $this->db->query("SELECT ID_Variante_Item_Detalle FROM " . $this->table_variante_item_detalle . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND No_Valor='" . $row['ID_Variante_Item_Detalle_3'] . "' LIMIT 1")->row()->ID_Variante_Item_Detalle;
        	}

        	if ( !empty($row['No_Familia_Marketplace']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_familia . " WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . " AND No_Familia='" . $row['No_Familia_Marketplace'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Familia_Marketplace = $this->db->query("SELECT ID_Familia FROM " . $this->table_familia . " WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . " AND No_Familia= '" . $row['No_Familia_Marketplace'] . "' LIMIT 1")->row()->ID_Familia;
        	}
        	
        	if ( !empty($row['No_Sub_Familia_Marketplace']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_subfamilia . " WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . " AND No_Sub_Familia='" . $row['No_Sub_Familia_Marketplace'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Sub_Familia_Marketplace = $this->db->query("SELECT ID_Sub_Familia FROM " . $this->table_subfamilia . " WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . " AND No_Sub_Familia= '" . $row['No_Sub_Familia_Marketplace'] . "' LIMIT 1")->row()->ID_Sub_Familia;
			}
        	
        	if ( !empty($row['No_Marca_Marketplace']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_marca . " WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . " AND No_Marca='" . $row['No_Marca_Marketplace'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Marca_Marketplace = $this->db->query("SELECT ID_Marca FROM " . $this->table_marca . " WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . " AND No_Marca= '" . $row['No_Marca_Marketplace'] . "' LIMIT 1")->row()->ID_Marca;
			}
			
        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe == 0){
            	$_arrProducto = array(
					'ID_Empresa' => $ID_Empresa,
					'Nu_Tipo_Producto' => $row['Nu_Tipo_Producto'],
					'ID_Tipo_Producto' => $row['ID_Tipo_Producto'],
					'Nu_Codigo_Barra' => $row['Nu_Codigo_Barra'],
					'No_Codigo_Interno' => $row['Nu_Codigo_Producto'],
					'No_Producto' => $row['No_Producto'],
					'ID_Impuesto' => $ID_Impuesto,
					'ID_Ubicacion_Inventario' => $ID_Ubicacion_Inventario,
					'ID_Unidad_Medida' => $ID_Unidad_Medida,
					'ID_Producto_Sunat' => $ID_Producto_Sunat,
					'Nu_Compuesto' => 0,
					'Qt_CO2_Producto' => $row['Qt_CO2_Producto'],
					'Ss_Precio' => $row['fPrecio'],
					'Ss_Costo' => $row['fCosto'],
					'Nu_Stock_Minimo' => $row['iStockMinimo'],
					'Nu_Stock_Maximo' => $row['iStockMaximo'],
					'ID_Tipo_Pedido_Lavado' => $iIdTipoLavado,
					'Nu_Estado' => (($row['iEstado'] != 0 || $row['iEstado'] == '' || $row['iEstado'] == NULL ) ? 1 : 0),
					'Txt_Producto' => $row['Txt_Producto'],
					'ID_Familia' => $ID_Familia,
					'ID_Sub_Familia' => (!empty($ID_Sub_Familia) ? $ID_Sub_Familia : NULL),
					'ID_Marca' => (!empty($ID_Marca) ? $ID_Marca : NULL),
					'ID_Variante_Item_1' => (!empty($ID_Variante_Item_1) ? $ID_Variante_Item_1 : NULL),
					'ID_Variante_Item_Detalle_1' => (!empty($ID_Variante_Item_Detalle_1) ? $ID_Variante_Item_Detalle_1 : NULL),
					'ID_Variante_Item_2' => (!empty($ID_Variante_Item_2) ? $ID_Variante_Item_2 : NULL),
					'ID_Variante_Item_Detalle_2' => (!empty($ID_Variante_Item_Detalle_2) ? $ID_Variante_Item_Detalle_2 : NULL),
					'ID_Variante_Item_3' => (!empty($ID_Variante_Item_3) ? $ID_Variante_Item_3 : NULL),
					'ID_Variante_Item_Detalle_3' => (!empty($ID_Variante_Item_Detalle_3) ? $ID_Variante_Item_Detalle_3 : NULL),
            	);
				if ( !empty($ID_Laboratorio) )
					$_arrProducto = array_merge($_arrProducto, array("ID_Laboratorio" => $ID_Laboratorio));
				$arrProducto[] = $_arrProducto;
        	} else {
        		$ID_Producto = $this->db->query("SELECT ID_Producto FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->ID_Producto;
				$Nu_Compuesto = $this->db->query("SELECT Nu_Compuesto FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->Nu_Compuesto;
        		$_arrProductoUPD = array(
					'ID_Producto' => $ID_Producto,
					'ID_Empresa' => $ID_Empresa,
					'Nu_Tipo_Producto' => $row['Nu_Tipo_Producto'],
					'ID_Tipo_Producto' => $row['ID_Tipo_Producto'],
					'Nu_Codigo_Barra' => $row['Nu_Codigo_Barra'],
					'No_Codigo_Interno' => $row['Nu_Codigo_Producto'],
					'No_Producto' => $row['No_Producto'],
					'ID_Impuesto' => $ID_Impuesto,
					'ID_Ubicacion_Inventario' => $ID_Ubicacion_Inventario,
					'ID_Unidad_Medida' => $ID_Unidad_Medida,
					'ID_Producto_Sunat' => $ID_Producto_Sunat,
					'Nu_Compuesto' => $Nu_Compuesto,
					'Qt_CO2_Producto' => $row['Qt_CO2_Producto'],
					'Ss_Precio' => $row['fPrecio'],
					'Ss_Costo' => $row['fCosto'],
					'Nu_Stock_Minimo' => $row['iStockMinimo'],
					'Nu_Stock_Maximo' => $row['iStockMaximo'],
					'ID_Tipo_Pedido_Lavado' => $iIdTipoLavado,
					'Txt_Producto' => $row['Txt_Producto'],
					'Nu_Estado' => (($row['iEstado'] != 0 || $row['iEstado'] == '' || $row['iEstado'] == NULL ) ? 1 : 0),
					'ID_Familia' => $ID_Familia,
					'ID_Sub_Familia' => (!empty($ID_Sub_Familia) ? $ID_Sub_Familia : NULL),
					'ID_Marca' => (!empty($ID_Marca) ? $ID_Marca : NULL),
					'ID_Variante_Item_1' => (!empty($ID_Variante_Item_1) ? $ID_Variante_Item_1 : NULL),
					'ID_Variante_Item_Detalle_1' => (!empty($ID_Variante_Item_Detalle_1) ? $ID_Variante_Item_Detalle_1 : NULL),
					'ID_Variante_Item_2' => (!empty($ID_Variante_Item_2) ? $ID_Variante_Item_2 : NULL),
					'ID_Variante_Item_Detalle_2' => (!empty($ID_Variante_Item_Detalle_2) ? $ID_Variante_Item_Detalle_2 : NULL),
					'ID_Variante_Item_3' => (!empty($ID_Variante_Item_3) ? $ID_Variante_Item_3 : NULL),
					'ID_Variante_Item_Detalle_3' => (!empty($ID_Variante_Item_Detalle_3) ? $ID_Variante_Item_Detalle_3 : NULL),
            	);
				if ( !empty($ID_Laboratorio) )
					$_arrProductoUPD = array_merge($_arrProductoUPD, array("ID_Laboratorio" => $ID_Laboratorio));
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
        if( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'Producto' ){
            $this->db->like('No_Producto', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'CodigoBarra' ){
        	$this->db->like('Nu_Codigo_Barra', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'CodigoSKU' ){
        	$this->db->like('No_Codigo_Interno', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Grupo' && strpos("SERVICIO", strtoupper($this->input->post('Global_Filter'))) !== false ){
        	$this->db->where('Nu_Tipo_Producto', 0);
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Grupo' && strpos("PRODUCTO", strtoupper($this->input->post('Global_Filter'))) !== false ){
        	$this->db->where('Nu_Tipo_Producto', 1);
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Grupo' && strpos("INTERNO", strtoupper($this->input->post('Global_Filter'))) !== false ){
        	$this->db->where('Nu_Tipo_Producto', 2);
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'Categoria' ){
        	$this->db->like('F.No_Familia', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'SubCategoria' ){
        	$this->db->like('SF.No_Sub_Familia', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'UnidadMedida' ){
        	$this->db->like('UM.No_Unidad_Medida', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'Marca' ){
        	$this->db->like('M.No_Marca', $this->input->post('Global_Filter'));
		} else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Impuesto' ){
        	$this->db->like('IMP.No_Impuesto_Breve', $this->input->post('Global_Filter'));
		}

		if ( $this->input->post('Filtro_Nu_Estado') != '-' )
        	$this->db->like('PRO.Nu_Estado', $this->input->post('Filtro_Nu_Estado'));

		$this->db->select('PRO.ID_Empresa, PRO.ID_Producto, Nu_Codigo_Barra, No_Codigo_Interno, No_Producto, STOCK.Qt_Producto AS Qt_Producto, Nu_Stock_Minimo, Ss_Precio, Nu_Compuesto, No_Imagen_Item, PRO.Nu_Version_Imagen, IMP.No_Impuesto_Breve, PRO.Ss_Costo, F.No_Familia, UM.No_Unidad_Medida, M.No_Marca, STOCK.Ss_Costo_Promedio, PRO.Nu_Stock_Maximo, SF.No_Sub_Familia, PRO.No_Imagen_Item, PRO.Nu_Version_Imagen, PRO.Nu_Estado, PRO.Nu_Tipo_Producto, STOCK.Nu_Estado AS Nu_Estado_Stock')
		->from($this->table . ' AS PRO')
		->join($this->table_impuesto . ' AS IMP', 'IMP.ID_Impuesto = PRO.ID_Impuesto', 'join')
		->join($this->table_unidad_medida . ' AS UM', 'UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida', 'join')
		->join($this->table_stock_producto . ' AS STOCK', 'STOCK.ID_Almacen = ' . $this->session->userdata['almacen']->ID_Almacen . ' AND STOCK.ID_Producto = PRO.ID_Producto', 'left')
		//->join($this->table_stock_producto . ' AS STOCK', 'STOCK.ID_Organizacion = ' . $this->empresa->ID_Organizacion . ' AND STOCK.ID_Almacen = ' . $this->session->userdata['almacen']->ID_Almacen . ' AND STOCK.ID_Producto = PRO.ID_Producto', 'left')
		->join($this->table_familia . ' AS F', 'F.ID_Familia = PRO.ID_Familia', 'left')
		->join($this->table_subfamilia . ' AS SF', 'SF.ID_Sub_Familia = PRO.ID_Sub_Familia', 'left')
		->join($this->table_marca . ' AS M', 'M.ID_Marca = PRO.ID_Marca', 'left')
		->where('PRO.ID_Empresa', $this->empresa->ID_Empresa);
		
        if(isset($_POST['order'])){
        	if ( $_POST['order']['0']['column'] != 5)//5=codigo upc
            	$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            else
            	$this->db->order_by( 'CONVERT(Nu_Codigo_Barra, SIGNED INTEGER) ' . $_POST['order']['0']['dir'] );
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
        if( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'Producto' ){
            $this->db->like('No_Producto', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'CodigoBarra' ){
        	$this->db->like('Nu_Codigo_Barra', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'CodigoSKU' ){
        	$this->db->like('No_Codigo_Interno', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Grupo' && strpos("SERVICIO", strtoupper($this->input->post('Global_Filter'))) !== false ){
        	$this->db->where('Nu_Tipo_Producto', 0);
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Grupo' && strpos("PRODUCTO", strtoupper($this->input->post('Global_Filter'))) !== false ){
        	$this->db->where('Nu_Tipo_Producto', 1);
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Grupo' && strpos("INTERNO", strtoupper($this->input->post('Global_Filter'))) !== false ){
        	$this->db->where('Nu_Tipo_Producto', 2);
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>3 && $this->input->post('Filtros_Productos') == 'Categoria' ){
        	$this->db->like('F.No_Familia', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'SubCategoria' ){
        	$this->db->like('SF.No_Sub_Familia', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'UnidadMedida' ){
        	$this->db->like('UM.No_Unidad_Medida', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Filtros_Productos') == 'Marca' ){
        	$this->db->like('M.No_Marca', $this->input->post('Global_Filter'));
		} else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Impuesto' ){
        	$this->db->like('IMP.No_Impuesto_Breve', $this->input->post('Global_Filter'));
		}

		if ( $this->input->post('Filtro_Nu_Estado') != '-' )
        	$this->db->like('PRO.Nu_Estado', $this->input->post('Filtro_Nu_Estado'));

		$this->db->select('PRO.ID_Empresa, PRO.ID_Producto, Nu_Codigo_Barra, No_Codigo_Interno, No_Producto, STOCK.Qt_Producto AS Qt_Producto, Nu_Stock_Minimo, Ss_Precio, Nu_Compuesto, No_Imagen_Item, PRO.Nu_Version_Imagen, IMP.No_Impuesto_Breve, PRO.Ss_Costo, F.No_Familia, UM.No_Unidad_Medida, M.No_Marca, STOCK.Ss_Costo_Promedio, PRO.Nu_Stock_Maximo, SF.No_Sub_Familia, PRO.No_Imagen_Item, PRO.Nu_Version_Imagen, PRO.Nu_Estado, PRO.Nu_Tipo_Producto, STOCK.Nu_Estado AS Nu_Estado_Stock')
		->from($this->table . ' AS PRO')
		->join($this->table_impuesto . ' AS IMP', 'IMP.ID_Impuesto = PRO.ID_Impuesto', 'join')
		->join($this->table_unidad_medida . ' AS UM', 'UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida', 'join')
		//->join($this->table_stock_producto . ' AS STOCK', 'STOCK.ID_Organizacion = ' . $this->empresa->ID_Organizacion . ' AND STOCK.ID_Almacen = ' . $this->session->userdata['almacen']->ID_Almacen . ' AND STOCK.ID_Producto = PRO.ID_Producto', 'left')
		->join($this->table_stock_producto . ' AS STOCK', 'STOCK.ID_Almacen = ' . $this->session->userdata['almacen']->ID_Almacen . ' AND STOCK.ID_Producto = PRO.ID_Producto', 'left')
		->join($this->table_familia . ' AS F', 'F.ID_Familia = PRO.ID_Familia', 'left')
		->join($this->table_subfamilia . ' AS SF', 'SF.ID_Sub_Familia = PRO.ID_Sub_Familia', 'left')
		->join($this->table_marca . ' AS M', 'M.ID_Marca = PRO.ID_Marca', 'left')
		->where('PRO.ID_Empresa', $this->empresa->ID_Empresa);
		
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
    	$this->db->select('PRO.*');
        $this->db->from($this->table . ' AS PRO');
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

    public function agregarProducto($data, $data_enlace, $data_precio_x_mayor,$data_imagen){
		if($data['Nu_Codigo_Barra'] != '' && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Codigo_Barra='" . $data['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Código de Barra ' . $data['Nu_Codigo_Barra'] . ' ya existe');
		} else if( $data['No_Codigo_Interno'] != '' && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Codigo_Interno='" . $data['No_Codigo_Interno'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Código SKU ' . $data['No_Codigo_Interno'] . ' ya existe');
		} else {
			$this->db->trans_begin();
			
			$Ss_Icbper = 0.00;
			//if ($data['ID_Tabla_Dato_Icbper'] == 2070)
				//$Ss_Icbper = $this->db->query("SELECT Nu_Valor FROM tabla_dato WHERE ID_Tabla_Dato=2070 LIMIT 1")->row()->Nu_Valor;
			//$data = array_merge($data, array("Ss_Icbper" => $Ss_Icbper));

			$this->db->insert($this->table, $data);
			$Last_ID_Producto = $this->db->insert_id();
			if ( $data['Nu_Compuesto'] == 1 ){//Enlaces con producto
				for($i = 0; $i < count($data_enlace); $i++){
					$enlace_producto[] = array(
						'ID_Producto'			=> $this->security->xss_clean($data_enlace[$i]['ID_Producto_Enlace']),
						'ID_Producto_Enlace'	=> $Last_ID_Producto,
						'Qt_Producto_Descargar'	=> $this->security->xss_clean($data_enlace[$i]['Qt_Producto_Descargar']),
					);
				}
				$this->db->insert_batch($this->table_enlace_producto, $enlace_producto);
			}

			if ( $data['Nu_Activar_Precio_x_Mayor'] == 1 ){//Enlaces con producto
				if(!empty($data_precio_x_mayor)) {
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
			}

			if (!empty($data_imagen)) {
				$arrUrlImage    = explode($this->empresa->Nu_Documento_Identidad."/",$data_imagen["No_Producto_Imagen"]);
				$table_imagen = array(
					'No_Producto_Imagen' => $arrUrlImage[1],
					'ID_Producto' => $Last_ID_Producto,
					'Imagen_Tamano'	=> $data_imagen["Imagen_Tamano"],
					'ID_Predeterminado'	=> 1
				);
				$this->db->insert('producto_imagen', $table_imagen);
			}

			//modificar precio de tienda virtual solo si esta activado el parametro Nu_Activar_Precio_Centralizado_Laeshop=1
			if($this->empresa->Nu_Activar_Precio_Centralizado_Laeshop==1)
				$this->db->query("UPDATE producto SET Ss_Precio_Ecommerce_Online_Regular=Ss_Precio WHERE ID_Producto=" . $Last_ID_Producto);

	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
	            $this->db->trans_commit();

				/* TOUR GESTION */
				$where_tour = array('ID_Empresa' => $data['ID_Empresa'], 'Nu_ID_Interno' => 5);
				//validamos que si complete los siguientes datos
				if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Estado=1 LIMIT 1")->row()->cantidad > 0){
					//Cambiar estado a completado para el tour
					$data_tour = array('Nu_Estado_Proceso' => 1);
				} else {
					//Cambiar estado a completado para el tour
					$data_tour = array('Nu_Estado_Proceso' => 0);
				}
				$this->db->update('tour_gestion', $data_tour, $where_tour);
				/* END TOUR GESTION */

	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'iIDItem' => $Last_ID_Producto);
	        }
		}
    }
    
    public function actualizarProducto($where, $data, $ENu_Codigo_Barra, $ENo_Codigo_Interno, $data_enlace, $data_precio_x_mayor){
		if( $ENu_Codigo_Barra != $data['Nu_Codigo_Barra'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Codigo_Barra='" . $data['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Código de Barra ' . $data['Nu_Codigo_Barra'] . ' ya existe');
		} else if( $ENo_Codigo_Interno != $data['No_Codigo_Interno'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Codigo_Interno='" . $data['No_Codigo_Interno'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Código SKU ' . $data['No_Codigo_Interno'] . ' ya existe');
		} else{
			$this->db->trans_begin();
			
			$Ss_Icbper = 0.00;
			//if ($data['ID_Tabla_Dato_Icbper'] == 2070)
				//$Ss_Icbper = $this->db->query("SELECT Nu_Valor FROM tabla_dato WHERE ID_Tabla_Dato=2070 LIMIT 1")->row()->Nu_Valor;
			//$data = array_merge($data, array("Ss_Icbper" => $Ss_Icbper));
			
		    $this->db->update($this->table, $data, $where);

	    	$this->db->where('ID_Producto_Enlace', $where['ID_Producto']);
        	$this->db->delete($this->table_enlace_producto);
        	if ( $data['Nu_Compuesto'] == 1 ){//Enlaces con producto
				for($i = 0; $i < count($data_enlace); $i++){
					$enlace_producto[] = array(
						'ID_Producto'			=> $this->security->xss_clean($data_enlace[$i]['ID_Producto_Enlace']),
						'ID_Producto_Enlace'	=> $where['ID_Producto'],
						'Qt_Producto_Descargar'	=> $this->security->xss_clean($data_enlace[$i]['Qt_Producto_Descargar']),
					);
				}
				$this->db->insert_batch($this->table_enlace_producto, $enlace_producto);
			}

	    	$this->db->where('ID_Producto', $where['ID_Producto']);
        	$this->db->delete('producto_precio_x_mayor');
			if ( $data['Nu_Activar_Precio_x_Mayor'] == 1 ){//Enlaces con producto
				if(!empty($data_precio_x_mayor)) {
					for($i = 0; $i < count($data_precio_x_mayor); $i++){
						$table_precio_x_mayor[] = array(
							'ID_Empresa' => $data['ID_Empresa'],
							'ID_Producto' => $where['ID_Producto'],
							'Qt_Producto_x_Mayor'	=> $this->security->xss_clean($data_precio_x_mayor[$i]['Qt_Producto_x_Mayor']),
							'Ss_Precio_x_Mayor'	=> $this->security->xss_clean($data_precio_x_mayor[$i]['Ss_Precio_x_Mayor']),
						);
					}
					$this->db->insert_batch('producto_precio_x_mayor', $table_precio_x_mayor);
				}
			}

			//modificar precio de tienda virtual solo si esta activado el parametro Nu_Activar_Precio_Centralizado_Laeshop=1
			if($this->empresa->Nu_Activar_Precio_Centralizado_Laeshop==1 && $data['Ss_Precio'] > 0.10)
				$this->db->query("UPDATE producto SET Ss_Precio_Ecommerce_Online_Regular=Ss_Precio WHERE ID_Producto=" . $where['ID_Producto']);

			//Inactivar item por almacen con stock
			/*
			if ($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Producto='" . $where['ID_Producto'] . "' LIMIT 1")->row()->existe > 0 ){
				$data_stock_producto = array("Nu_Estado" => $data['Nu_Estado']);
				$where_stock_producto = array("ID_Almacen" => $this->session->userdata['almacen']->ID_Almacen, "ID_Producto" => $where['ID_Producto']);			
		   		$this->db->update('stock_producto', $data_stock_producto, $where_stock_producto);
			}
			*/
	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
	        } else {
	            $this->db->trans_commit();

				/* TOUR GESTION */
				$where_tour = array('ID_Empresa' => $data['ID_Empresa'], 'Nu_ID_Interno' => 5);
				//validamos que si complete los siguientes datos
				if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Estado=1 LIMIT 1")->row()->cantidad > 0){
					//Cambiar estado a completado para el tour
					$data_tour = array('Nu_Estado_Proceso' => 1);
				} else {
					//Cambiar estado a completado para el tour
					$data_tour = array('Nu_Estado_Proceso' => 0);
				}
				$this->db->update('tour_gestion', $data_tour, $where_tour);
				/* END TOUR GESTION */
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

	public function AsignarImagenProducto($data) {
		$where = array('ID_Producto' => $data['ID_Producto']);
		$arrData = array( 'No_Imagen_Item' => $data['No_Producto_Imagen'] );

		return $this->db->update('producto', $arrData, $where);
	}

	public function RemoverImagen($data){
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
		$row = $this->getImagenById($data["ID_Producto_Imagen"],$path);
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

	public function getImagenByIdProducto($ID_Producto,$path){
		$this->db->select('ID_Producto_Imagen,
		CONCAT("'.$path.'/", No_Producto_Imagen) No_Producto_Imagen_url,
		No_Producto_Imagen,
		Imagen_Tamano,
		ID_Predeterminado,
		ID_Estatus')
		->from("producto_imagen")
		->where('ID_Producto', $ID_Producto)
		->where('ID_Predeterminado', 1); 
		$query = $this->db->get();
		return $query->row();
	}

	public function getImagenById($ID_Producto_Imagen,$path){
		$this->db->select('ID_Producto_Imagen,
		CONCAT("'.$path.'/", No_Producto_Imagen) No_Producto_Imagen_url,
		No_Producto_Imagen,
		Imagen_Tamano,
		ID_Predeterminado,
		ID_Estatus')
		->from("producto_imagen")
		->where('ID_Producto_Imagen', $ID_Producto_Imagen)
		->where('ID_Predeterminado', 1); 
		$query = $this->db->get();
		return $query->row();
	}
	
    public function estadoxAlmacen($ID, $Nu_Estado_Stock){		
		//Inactivar item por almacen con stock
		if ($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Producto='" . $ID . "' LIMIT 1")->row()->existe > 0 ){
			$Nu_Estado_Stock = ($Nu_Estado_Stock == 1 ? 0 : 1);
			$data_stock_producto = array("Nu_Estado" => $Nu_Estado_Stock);
			$where_stock_producto = array("ID_Almacen" => $this->session->userdata['almacen']->ID_Almacen, "ID_Producto" => $ID);			
			
			if ( $this->db->update('stock_producto', $data_stock_producto, $where_stock_producto) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado');
		}
        return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No hay stock para cambio estado');
    }

    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Producto imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error de producto imagen modificada');
    }

    public function crudProductoxVarianteModal($arrPost){
		$this->db->trans_begin();
		
		$iIdItem = $arrPost['iIdItem'];
		$query="SELECT
ID_Empresa,
Nu_Tipo_Producto,
No_Producto,
ID_Tipo_Producto,
ID_Ubicacion_Inventario,
ID_Producto_Sunat,
Ss_Costo,
ID_Impuesto,
ID_Unidad_Medida,
ID_Impuesto_Icbper,
Txt_Producto,
Nu_Stock_Minimo,
Nu_Stock_Maximo,
Nu_Favorito,
ID_Marca,
ID_Familia,
ID_Sub_Familia
FROM
producto
WHERE
ID_Producto = " . $iIdItem . " LIMIT 1";
		$objRowItem = $this->db->query($query)->row();
		
		$arrVarianteModal = $arrPost['arrVarianteModal'];
		$arrInsertItemMasivo = array();
		foreach($arrVarianteModal as $row){
			$Nu_Codigo_Barra = trim($row['Nu_Codigo_Barra']);
			$No_Codigo_Interno = trim($row['No_Codigo_Interno']);
			//validacion de codigo x item
			if($row['Nu_Codigo_Barra'] != '' && $this->db->query("SELECT COUNT(*) AS existe FROM producto WHERE ID_Empresa=" . $objRowItem->ID_Empresa . " AND Nu_Codigo_Barra='" . $Nu_Codigo_Barra . "' LIMIT 1")->row()->existe > 0){
				$this->db->trans_rollback();
				return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Código ' . $Nu_Codigo_Barra . ' ya existe');
			} else if( $row['No_Codigo_Interno'] != '' && $this->db->query("SELECT COUNT(*) existe FROM producto WHERE ID_Empresa=" . $objRowItem->ID_Empresa . " AND No_Codigo_Interno='" . $No_Codigo_Interno . "' LIMIT 1")->row()->existe > 0 ){
				$this->db->trans_rollback();
				return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'SKU ' . $No_Codigo_Interno . ' ya existe');
			} else {
				$_arrInsertItemMasivo = array(
					'Nu_Codigo_Barra' => $this->security->xss_clean(strtoupper($Nu_Codigo_Barra)),
					'No_Codigo_Interno' => $this->security->xss_clean(strtoupper($No_Codigo_Interno)),
					'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
					'ID_Variante_Item_1' => $row['ID_Variante_Item_1'],
					'ID_Variante_Item_Detalle_1' => $row['ID_Variante_Item_Detalle_1'],
					'ID_Variante_Item_2' => $row['ID_Variante_Item_2'],
					'ID_Variante_Item_Detalle_2' => $row['ID_Variante_Item_Detalle_2'],
					'ID_Variante_Item_3' => $row['ID_Variante_Item_3'],
					'ID_Variante_Item_Detalle_3' => $row['ID_Variante_Item_Detalle_3'],
					'ID_Empresa' => $objRowItem->ID_Empresa,
					'Nu_Tipo_Producto' => $objRowItem->Nu_Tipo_Producto,
					'No_Producto' => $objRowItem->No_Producto,
					'ID_Tipo_Producto' => $objRowItem->ID_Tipo_Producto,
					'ID_Ubicacion_Inventario' => $objRowItem->ID_Ubicacion_Inventario,
					'Ss_Costo' => $objRowItem->Ss_Costo,
					'ID_Impuesto' => $objRowItem->ID_Impuesto,
					'ID_Unidad_Medida' => $objRowItem->ID_Unidad_Medida,
					'ID_Impuesto_Icbper' => $objRowItem->ID_Impuesto_Icbper,
					'Nu_Compuesto' => 0,
					'Nu_Estado' => 1,
					'Txt_Producto' => $objRowItem->Txt_Producto,
					'Nu_Stock_Minimo' => $objRowItem->Nu_Stock_Minimo,
					'Nu_Stock_Maximo' => $objRowItem->Nu_Stock_Maximo,
					'Nu_Favorito' => $objRowItem->Nu_Favorito
				);
				
				if ( !empty($objRowItem->ID_Marca) ){
					$_arrInsertItemMasivo = array_merge($_arrInsertItemMasivo, array("ID_Marca" => $objRowItem->ID_Marca));
				}
				if ( !empty($objRowItem->ID_Familia) ){
					$_arrInsertItemMasivo = array_merge($_arrInsertItemMasivo, array("ID_Familia" => $objRowItem->ID_Familia));
				}
				if ( !empty($objRowItem->ID_Sub_Familia) ){
					$_arrInsertItemMasivo = array_merge($_arrInsertItemMasivo, array("ID_Sub_Familia" => $objRowItem->ID_Sub_Familia));
				}
				if ( $objRowItem->Nu_Tipo_Producto == 1 && $objRowItem->ID_Impuesto_Icbper == 1 ){
					$_arrInsertItemMasivo = array_merge($_arrInsertItemMasivo, array("ID_Tabla_Dato_Icbper" => 2070));
				} else {
					$_arrInsertItemMasivo = array_merge($_arrInsertItemMasivo, array("ID_Tabla_Dato_Icbper" => 0));
				}

				$arrInsertItemMasivo[] = $_arrInsertItemMasivo;
			}
		}
		if(!empty($arrInsertItemMasivo))
			$this->db->insert_batch('producto', $arrInsertItemMasivo);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
    }
}