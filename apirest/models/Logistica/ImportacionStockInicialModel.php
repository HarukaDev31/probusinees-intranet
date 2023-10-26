<?php
class ImportacionStockInicialModel extends CI_Model{
	var $table = 'movimiento_inventario';
	var $table_documento_cabecera = 'documento_cabecera';
	var $table_documento_detalle = 'documento_detalle';
	var $table_entidad = 'entidad';
	var $table_impuesto = 'impuesto';
	var $table_producto = 'producto';
    var $column_order = array('ALMA.No_Almacen', 'Fe_Emision', 'Tipo_Operacion_Sunat_Codigo', 'No_Tipo_Movimiento', 'No_Entidad', 'Nu_Codigo_Barra', 'No_Producto', 'Ss_Precio', 'Qt_Producto');
    var $column_search = array('ALMA.No_Almacen', 'Fe_Emision', 'Tipo_Operacion_Sunat_Codigo', 'No_Tipo_Movimiento', 'No_Entidad', 'Nu_Codigo_Barra', 'No_Producto', 'Ss_Precio', 'Qt_Producto');
	var $order = array('Fe_Emision' => 'desc');

	private $_batchImport;

	public function __construct(){
		parent::__construct();
	}
	
    public function setBatchImport($arrStockInicialProductos) {
        $this->_batchImport = $arrStockInicialProductos;
    }
    
    public function importData() {
	    $ID_Empresa = $this->user->ID_Empresa;
		$ID_Organizacion = $this->empresa->ID_Organizacion;
		$ID_Almacen = $this->session->userdata['almacen']->ID_Almacen;

		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_entidad . " WHERE ID_Empresa = " . $ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $this->empresa->ID_Tipo_Documento_Identidad . " AND Nu_Documento_Identidad = '" . $this->empresa->Nu_Documento_Identidad . "' AND Nu_Estado=1 LIMIT 1")->row()->existe > 0)
			$ID_Entidad = $this->db->query("SELECT ID_Entidad FROM " . $this->table_entidad . " WHERE ID_Empresa = " . $ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $this->empresa->ID_Tipo_Documento_Identidad . " AND Nu_Documento_Identidad = '" . $this->empresa->Nu_Documento_Identidad . "' AND Nu_Estado=1 LIMIT 1")->row()->ID_Entidad;

		if ( !empty($ID_Entidad) ) {
			$this->db->trans_begin();

			if ($this->db->query("SELECT COUNT(*) AS existe FROM medio_pago WHERE ID_Empresa = " . $ID_Empresa . " AND No_Medio_Pago='Efectivo' LIMIT 1")->row()->existe > 0)
				$ID_Medio_Pago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $ID_Empresa . " AND No_Medio_Pago='Efectivo' LIMIT 1")->row()->ID_Medio_Pago;
	
			if ($this->db->query("SELECT COUNT(*) AS existe FROM moneda WHERE ID_Empresa = " . $ID_Empresa . " LIMIT 1")->row()->existe > 0)
				$ID_Moneda = $this->db->query("SELECT ID_Moneda FROM moneda WHERE ID_Empresa = " . $ID_Empresa . " LIMIT 1")->row()->ID_Moneda;
	
			$arrStockInicialCabecera = array(
				'ID_Empresa' => $ID_Empresa,
				'ID_Organizacion' => $ID_Organizacion,
				'ID_Almacen' => $ID_Almacen,
				'ID_Entidad' => $ID_Entidad,
				'ID_Tipo_Asiento' => 2,
				'ID_Tipo_Documento' => 2,
				'ID_Serie_Documento' => dateNow('serie_ymd'),
				'ID_Numero_Documento' => dateNow('numero_ymdhms'),
				'Fe_Emision' => dateNow('fecha'),
				'Fe_Emision_Hora' => dateNow('fecha_hora'),
				'ID_Medio_Pago' => $ID_Medio_Pago,
				'ID_Rubro' => 1,
				'ID_Moneda'	=> $ID_Moneda,
				'Fe_Vencimiento' => dateNow('fecha'),
				'Fe_Periodo' => dateNow('fecha'),
				'Nu_Descargar_Inventario' => 1,
				'Ss_Total' => 0.00,
				'Nu_Estado' => 6,
				'Nu_Correlativo' => 0
			);

			if ( $this->db->insert($this->table_documento_cabecera, $arrStockInicialCabecera) > 0 ){
				$Last_ID_Documento_Cabecera = $this->db->insert_id();
				
				foreach ($this->_batchImport as $row) {
					$ID_Producto = 0;
					$ID_Impuesto = 0;
					$ID_Impuesto_Cruce_Documento = 0;

					if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Empresa = " . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe > 0)
						$ID_Producto = $this->db->query("SELECT ID_Producto FROM " . $this->table_producto . " WHERE ID_Empresa = " . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->ID_Producto;

					if ( empty($ID_Producto) ){
						$this->db->trans_rollback();
						return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe producto con codigo -> ' . $row['Nu_Codigo_Barra']);
					}

					//solo si no existe en stock producto puede ingresar
					if($this->db->query("SELECT COUNT(*) AS existe FROM stock_producto WHERE ID_Almacen = " . $ID_Almacen . " AND ID_Producto=" . $ID_Producto . " LIMIT 1")->row()->existe == 0){
						if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_impuesto . " WHERE ID_Empresa = " . $ID_Empresa . " AND No_Impuesto='" . $row['No_Impuesto'] . "' LIMIT 1")->row()->existe > 0) {
							$ID_Impuesto = $this->db->query("SELECT ID_Impuesto FROM " . $this->table_impuesto . " WHERE ID_Empresa = " . $ID_Empresa . " AND No_Impuesto='" . $row['No_Impuesto'] . "' LIMIT 1")->row()->ID_Impuesto;

							if ( empty($ID_Impuesto) ){
								$this->db->trans_rollback();
								return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe impuesto -> ' . $row['No_Impuesto']);
							}

							if ($this->db->query("SELECT COUNT(*) AS existe FROM impuesto_cruce_documento WHERE ID_Impuesto=" . $ID_Impuesto . " AND Nu_Estado = 1 LIMIT 1")->row()->existe > 0) {
								$ID_Impuesto_Cruce_Documento = $this->db->query("SELECT ID_Impuesto_Cruce_Documento FROM impuesto_cruce_documento WHERE ID_Impuesto=" . $ID_Impuesto . " AND Nu_Estado = 1 LIMIT 1")->row()->ID_Impuesto_Cruce_Documento;
								$Ss_Impuesto = $this->db->query("SELECT Ss_Impuesto FROM impuesto_cruce_documento WHERE ID_Impuesto=" . $ID_Impuesto . " AND Nu_Estado = 1 LIMIT 1")->row()->Ss_Impuesto;
							}
							
							if ( empty($ID_Impuesto_Cruce_Documento) ){
								$this->db->trans_rollback();
								return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe monto de impuesto -> ' . $ID_Impuesto);
							}
						}

						if ( $ID_Producto > 0 && $ID_Impuesto_Cruce_Documento > 0 ) {
							$Ss_Precio_Sin_Impuesto = round(($row['Ss_Precio'] / $Ss_Impuesto), 6);
							$fSubtotal = ($row['Qt_Producto'] * $Ss_Precio_Sin_Impuesto);
							$fTotal = ($row['Qt_Producto'] * $row['Ss_Precio']);
							//$fSubtotal = round(($row['Qt_Producto'] * $row['Ss_Precio']), 2);
							//$fTotal = round(($fSubtotal * $Ss_Impuesto), 2);
							$arrStockInicialDetalle[] = array(
								'ID_Empresa' => $ID_Empresa,
								'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
								'ID_Producto' => $ID_Producto,
								'Qt_Producto' => $row['Qt_Producto'],
								'Ss_Precio' => $row['Ss_Precio'],
								'Ss_SubTotal' => $fSubtotal,
								'Ss_Descuento' => 0,
								'Ss_Descuento_Impuesto' => 0,
								'Po_Descuento' => 0,
								'ID_Impuesto_Cruce_Documento' => $ID_Impuesto_Cruce_Documento,
								'Ss_Impuesto' => ($fTotal - $fSubtotal),
								'Ss_Total' => $fTotal,
								'Fe_Emision' => dateNow('fecha')
							);

							//Generar movimiento de inventario
							$_movimiento_inventario = array(
								'ID_Empresa'			=> $ID_Empresa,
								'ID_Organizacion'		=> $ID_Organizacion,
								'ID_Almacen'			=> $ID_Almacen,
								'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
								'ID_Tipo_Movimiento'	=> 7,//7=saldo inicial
								'ID_Producto'			=> $ID_Producto,
								'Qt_Producto'			=> $row['Qt_Producto'],
								'Ss_Precio'				=> $Ss_Precio_Sin_Impuesto,
								'Ss_SubTotal' 			=> $fSubtotal,
								'Ss_Costo_Promedio'		=> $Ss_Precio_Sin_Impuesto,
							);
							$movimiento_inventario[] = $_movimiento_inventario;

							//Generar stock producto
							$_stock_producto = array(
								'ID_Empresa'		=> $ID_Empresa,
								'ID_Organizacion'	=> $ID_Organizacion,
								'ID_Almacen'		=> $ID_Almacen,
								'ID_Producto'		=> $ID_Producto,
								'Qt_Producto'		=> $row['Qt_Producto'],
								'Ss_Costo_Promedio'	=> $Ss_Precio_Sin_Impuesto,
							);
							$stock_producto[] = $_stock_producto;

							if ( !empty($row['dVencimiento']) && !empty($row['sNumeroLote']) ) {
								$arrDocumentoDetalleLote[] = array(
									'ID_Empresa' => $ID_Empresa,
									'ID_Organizacion' => $ID_Organizacion,
									'ID_Almacen' => $ID_Almacen,
									'ID_Producto' => $ID_Producto,
									'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
									'Fe_Lote_Vencimiento' => $this->security->xss_clean($row['dVencimiento']),
									'Nu_Lote_Vencimiento' => $this->security->xss_clean($row['sNumeroLote']),
								);
							}
						}
					}
				}

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
				} else {
					if (isset($arrStockInicialCabecera) && isset($arrStockInicialDetalle)) {
						if (is_array($arrStockInicialDetalle)) {
							//Generar movimiento de inventario							
							$this->db->insert_batch('movimiento_inventario', $movimiento_inventario);

							//Generar stock inicial
							$this->db->insert_batch('stock_producto', $stock_producto);

							//Generar documento detalle
							$this->db->insert_batch($this->table_documento_detalle, $arrStockInicialDetalle);
							$iIdDocumentoDetalleFirst = $this->db->insert_id();
		
							// Generar registros con fecha y lote de vencimiento
							if ( isset($arrDocumentoDetalleLote) ) {
								foreach ($arrDocumentoDetalleLote as $row) {
									$documento_detalle_lote[] = array(
										'ID_Empresa' => $row['ID_Empresa'],
										'ID_Organizacion' => $row['ID_Organizacion'],
										'ID_Almacen' => $row['ID_Almacen'],
										'ID_Producto' => $row['ID_Producto'],
										'ID_Documento_Cabecera'	=> $row['ID_Documento_Cabecera'],
										'ID_Documento_Detalle'	=> $iIdDocumentoDetalleFirst,
										'Fe_Lote_Vencimiento' => $row['Fe_Lote_Vencimiento'],
										'Nu_Lote_Vencimiento' => $row['Nu_Lote_Vencimiento'],
									);
									++$iIdDocumentoDetalleFirst;
								}
								$this->db->insert_batch('documento_detalle_lote', $documento_detalle_lote);
							}

							if ($this->db->trans_status() === FALSE) {
								$this->db->trans_rollback();
								return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
							} else {
								$this->db->trans_commit();
								return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'arrStockInicialCabecera' => $arrStockInicialCabecera, 'arrStockInicialDetalle' => $arrStockInicialDetalle, 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera);
							}
						}
					}
				}
			} else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar cabecera');
			}
		} else {
		 	$sTipoDocumentoIdentidad = 'RUC';
			if($this->empresa->ID_Tipo_Documento_Identidad==2)
				$sTipoDocumentoIdentidad = 'DNI';
			else if($this->empresa->ID_Tipo_Documento_Identidad==1)
				$sTipoDocumentoIdentidad = 'OTROS';
			
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Debes de crear primero un proveedor con T.D.I ' . $sTipoDocumentoIdentidad . ' - ' . $this->empresa->Nu_Documento_Identidad);
		}// if - else validación de entidad proveedor (misma empresa)
	}
	
	public function _get_datatables_query(){
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'Producto' ){
            $this->db->like('ITEM.No_Producto', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Productos') == 'CodigoBarra' ){
        	$this->db->like('ITEM.Nu_Codigo_Barra', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ALMA.No_Almacen, CABSTOCK.Fe_Emision, ITEM.Nu_Codigo_Barra, ITEM.No_Producto, STOCK.Ss_Precio, STOCK.Qt_Producto')
		->from('movimiento_inventario AS STOCK')
		->join('almacen AS ALMA', 'ALMA.ID_Almacen = STOCK.ID_Almacen', 'left')
		->join('documento_cabecera AS CABSTOCK', 'CABSTOCK.ID_Documento_Cabecera = STOCK.ID_Documento_Cabecera', 'left')
		->join('producto AS ITEM', 'ITEM.ID_Producto = STOCK.ID_Producto', 'left')
		->where('STOCK.ID_Empresa', $this->user->ID_Empresa)
		->where('STOCK.ID_Organizacion', $this->user->ID_Organizacion)
		->where('STOCK.ID_Almacen', $this->session->userdata['almacen']->ID_Almacen)
		->where('STOCK.ID_Tipo_Movimiento', 7);

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
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
	}
	
	public function verificarImportacionStockInicial(){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM movimiento_inventario WHERE ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Tipo_Movimiento=7 LIMIT 1")->row()->existe > 0)
			return array('sStatus' => 'warning', 'sMessage' => 'Solo se puede cargar el stock inicial de productos una vez');
		return array('sStatus' => 'success');
	}
}
