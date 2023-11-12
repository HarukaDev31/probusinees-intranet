<?php
class PedidosAgenteModel extends CI_Model{
	var $table = 'agente_compra_pedido_cabecera';
	var $table_importacion_grupal_pedido_detalle = 'agente_compra_pedido_detalle';
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
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, P.No_Pais, CLI.No_Entidad, CLI.Nu_Celular_Entidad')
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa);
        
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
        $this->db->select('P.No_Pais AS No_Pais_Cliente, DEP.No_Departamento, PRO.No_Provincia, DIS.No_Distrito,
		EMP.No_Empresa, EMP.Txt_Direccion_Empresa, EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
		CONFI.No_Logo_Empresa, CONFI.No_Imagen_Logo_Empresa, CONFI.Nu_Height_Logo_Ticket,
		CONFI.Nu_Width_Logo_Ticket, ' . $this->table . '.*,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad, CLI.Nu_Celular_Entidad, CLI.Txt_Direccion_Entidad, CLI.Txt_Email_Entidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		IGPD.Txt_Producto, IGPD.Txt_Descripcion, IGPD.Qt_Producto, IGPD.Txt_Url_Imagen_Producto, IGPD.Txt_Url_Link_Pagina_Producto,
		TDI.No_Tipo_Documento_Identidad_Breve, ' . $this->table . '.Nu_Estado AS Nu_Estado_Pedido, CONFI.Txt_Cuentas_Bancarias');
        $this->db->from($this->table);
		$this->db->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
		$this->db->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join');
		$this->db->join($this->table_departamento . ' AS DEP', 'DEP.ID_Departamento = EMP.ID_Departamento', 'join');
		$this->db->join($this->table_provincia . ' AS PRO', 'PRO.ID_Provincia = EMP.ID_Provincia', 'join');
		$this->db->join($this->table_distrito . ' AS DIS', 'DIS.ID_Distrito = EMP.ID_Distrito', 'join');
		$this->db->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join');
		$this->db->join($this->table_configuracion . ' AS CONFI', 'CONFI.ID_Empresa = EMP.ID_Empresa', 'join');
    	$this->db->join($this->table_importacion_grupal_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
		$this->db->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
        $query = $this->db->get();
        return $query->result();
    }

	public function cambiarEstado($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0)
			return array('status' => 'success', 'message' => 'Actualizado');
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}
    
    public function actualizarPedido($where, $data, $arrProducto){
		$this->db->trans_begin();
		
		//actualizar cliente
		$data_cliente = array(
			'Nu_Documento_Identidad' => $data['Nu_Documento_Identidad'],
			'No_Entidad' => $data['No_Entidad'],
			'Nu_Celular_Entidad' => $data['Nu_Celular_Entidad'],
			'Txt_Email_Entidad' => $data['Txt_Email_Entidad'],
		);
		$where_cliente = array(
			'ID_Entidad' => $where['ID_Entidad'],
		);
		$this->db->update($this->table_cliente, $data_cliente, $where_cliente);

		$this->db->where('ID_Pedido_Cabecera', $where['ID_Pedido_Cabecera']);
		$this->db->delete($this->table_importacion_grupal_pedido_detalle);
		
		$fImporteTotal = 0;
		$fCantidadTotal = 0;
		foreach($arrProducto as $row) {
			$fImporteTotal += $row['total_item'];
			$fCantidadTotal += $row['cantidad_item'];

			$arrSaleOrderDetail[] = array(
                'ID_Empresa' => $data['ID_Empresa'],
                'ID_Organizacion' => $data['ID_Organizacion'],
				'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
				'ID_Producto' => $row['id_item'],
				'ID_Unidad_Medida' => $row['id_unidad_medida'],
				'ID_Unidad_Medida_Precio' => $row['id_unidad_medida_2'],
				'Qt_Producto' => $row['cantidad_item'],
				'Ss_Precio' => $row['precio_item'],
				'Ss_SubTotal' => $row['total_item'],
				'Ss_Impuesto' => 0,
				'Ss_Total' => $row['total_item'],
			);
		}
		$this->db->insert_batch($this->table_importacion_grupal_pedido_detalle, $arrSaleOrderDetail);

		//actualizar cabecera
		$data_cabecera = array(
			'Ss_Total' => $fImporteTotal,
			'Qt_Total' => $fCantidadTotal,
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
}
