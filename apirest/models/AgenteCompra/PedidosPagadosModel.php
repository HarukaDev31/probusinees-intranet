<?php
class PedidosPagadosModel extends CI_Model{
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
	var $table_agente_compra_correlativo = 'agente_compra_correlativo';
	
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
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		IGPD.ID_Pedido_Detalle, IGPD.Txt_Producto, IGPD.Txt_Descripcion, IGPD.Qt_Producto, IGPD.Txt_Url_Imagen_Producto, IGPD.Txt_Url_Link_Pagina_Producto,
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

	public function cambiarEstado($ID, $Nu_Estado, $id_correlativo){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			if($Nu_Estado==2 && $id_correlativo==0){
				//si es Nu_Estado=2 Garantizado crear correlativo de mes y año si no existe y asignar al pedido
				$arrCorrelativo = $this->generarCorrelativo();
				if($arrCorrelativo['status']=='success'){
					$ID_Agente_Compra_Correlativo = $arrCorrelativo['result']['id_correlativo'];
					$Nu_Correlativo = $arrCorrelativo['result']['numero_correlativo'];

					//actualizar tabla para agregar correlativo
					$data = array(
						'ID_Agente_Compra_Correlativo' => $ID_Agente_Compra_Correlativo,
						'Nu_Correlativo' => $Nu_Correlativo,
						'Fe_Emision_Cotizacion' => dateNow('fecha'),
						'Fe_Registro_Hora_Cotizacion' => dateNow('fecha_hora')
					);
					if ($this->db->update($this->table, $data, $where) > 0) {
						return array('status' => 'success', 'message' => 'Correlativo generado');
					} else {
						return array('status' => 'error', 'message' => 'Error al asignar correlativo');
					}
				} else {
					return $arrCorrelativo;
				}
			} else {
				return array('status' => 'success', 'message' => 'Actualizado');
			}
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }
}
