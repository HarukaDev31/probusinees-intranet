<?php
class MetodoPagoGrupalModel extends CI_Model{
	var $table = 'medio_pago';
	var $table_tabla_dato = 'tabla_dato';
	var $table_empresa = 'empresa';
	var $table_documento_cabecera = 'documento_cabecera';
	var $table_documento_medio_pago = 'documento_medio_pago';
	var $table_caja_pos = 'caja_pos';
    var $table_cuenta_bancaria = 'cuenta_bancaria';
    var $table_banco = 'banco';
    var $table_moneda = 'moneda';
	
    var $column_order = array('No_Empresa', 'No_Medio_Pago_Tienda_Virtual');
    var $column_search = array('');
    var $order = array('Nu_Activar_Medio_Pago_Lae_Shop' => 'desc', 'No_Medio_Pago_Tienda_Virtual' => 'asc');

    
    var $column_order_cuentas_bancarias = array('No_Empresa', 'No_Medio_Pago_Tienda_Virtual', 'No_Banco_Siglas', 'Nu_Tipo_Cuenta', 'No_Moneda', 'No_Titular_Cuenta', 'No_Cuenta_Bancaria', 'No_Cuenta_Interbancario');
    var $column_search_cuentas_bancarias = array('');
    var $order_cuentas_bancarias = array('No_Empresa' => 'desc', 'No_Banco' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
    
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Medio_Pago, No_Medio_Pago, No_Medio_Pago_Tienda_Virtual, Nu_Activar_Medio_Pago_Lae_Shop AS Nu_Estado, Nu_Tipo_Forma_Pago_Lae_Shop, Nu_Cierre_Venta_Pago_Lae_Shop')
        ->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
    	->where_in('Nu_Tipo_Forma_Pago_Lae_Shop', array('1', '2', '3', '4'));
         
        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
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
        $this->db->from($this->table);
        $this->db->where('ID_Medio_Pago', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarMedioPago($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = ".$data['ID_Empresa']." AND No_Medio_Pago_Tienda_Virtual='" . $data['No_Medio_Pago_Tienda_Virtual'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarMedioPago($where, $data, $ENo_Medio_Pago_Tienda_Virtual){
		if( $ENo_Medio_Pago_Tienda_Virtual != $data['No_Medio_Pago_Tienda_Virtual'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = ".$data['ID_Empresa']." AND No_Medio_Pago_Tienda_Virtual='" . $data['No_Medio_Pago_Tienda_Virtual'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 ) {
                
                /* TOUR TIENDA VIRTUAL */
                $where_tour = array('ID_Empresa' => $data['ID_Empresa'], 'Nu_ID_Interno' => 5);
                //validamos que si complete los siguientes datos
                if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE Nu_Activar_Medio_Pago_Lae_Shop=1 LIMIT 1")->row()->cantidad > 0){
                    //Cambiar estado a completado para el tour
                    $data_tour = array('Nu_Estado_Proceso' => 1);
                } else {
                    //Cambiar estado a completado para el tour
                    $data_tour = array('Nu_Estado_Proceso' => 0);
                }
                $this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
                /* END TOUR TIENDA VIRTUAL */

                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
            }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarMedioPago($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Medio_Pago=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El medio de pago tiene movimiento(s)');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_medio_pago . " WHERE ID_Medio_Pago=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El medio de pago tiene movimiento(s)');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM pedido_cabecera WHERE ID_Medio_Pago=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El medio de pago tiene pedido(s)');
		} else {
			$this->db->where('ID_Medio_Pago', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}

    /* Cuentas Bancarias */
	public function _get_datatables_query_cuentas_bancarias(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
    
        $this->db->select('ID_Cuenta_Bancaria, EMP.ID_Empresa, EMP.No_Empresa, MP.No_Medio_Pago_Tienda_Virtual, No_Banco_Siglas, Nu_Tipo_Cuenta, No_Moneda, No_Titular_Cuenta, No_Cuenta_Bancaria, No_Cuenta_Interbancario')
        ->from($this->table_cuenta_bancaria)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table_cuenta_bancaria . '.ID_Empresa', 'join')
        ->join($this->table . ' AS MP', 'MP.ID_Medio_Pago = ' . $this->table_cuenta_bancaria . '.ID_Medio_Pago', 'join')
        ->join($this->table_banco . ' AS B', 'B.ID_Banco = ' . $this->table_cuenta_bancaria . '.ID_Banco', 'join')
        ->join($this->table_moneda . ' AS M', 'M.ID_Moneda = ' . $this->table_cuenta_bancaria . '.ID_Moneda', 'join');
         
        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order_cuentas_bancarias[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order_cuentas_bancarias)) {
            $order = $this->order_cuentas_bancarias;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
	
	function get_datatables_cuentas_bancarias(){
        $this->_get_datatables_query_cuentas_bancarias();
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id_cuentas_bancarias($ID){
        $this->db->from($this->table_cuenta_bancaria);
        $this->db->where('ID_Cuenta_Bancaria', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarMedioPago_cuentas_bancarias($data){
        if ( $this->db->insert($this->table_cuenta_bancaria, $data) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarMedioPago_cuentas_bancarias($where, $data){
        if ( $this->db->update($this->table_cuenta_bancaria, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarMedioPago_cuentas_bancarias($ID){
        $this->db->where('ID_Cuenta_Bancaria', $ID);
        $this->db->delete($this->table_cuenta_bancaria);
        if ( $this->db->affected_rows() > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
    /* FIN Cuentas Bancarias */
   
	public function buscarPasarelaPagoActivadas($iTipoPasarelaPago){
        #$iTipoPasarelaPago = 2 => MERCADO PAGO - Nu_Tipo_Forma_Pago_Lae_Shop
        return $this->db->query("SELECT COUNT(*) AS existe FROM medio_pago WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Tipo_Forma_Pago_Lae_Shop=".$iTipoPasarelaPago." LIMIT 1")->row()->existe;
	}

	public function activarMercadoPago(){
        #Nu_Tipo_Forma_Pago_Lae_Shop = 2  => MERCADO PAGO
        $sql = "INSERT INTO medio_pago(
ID_Empresa,
No_Medio_Pago,
Txt_Medio_Pago,
No_Codigo_Sunat_PLE,
No_Codigo_Sunat_FE,
Nu_Tipo,
Nu_Tipo_Caja,
Nu_Orden,
Nu_Estado,
No_Medio_Pago_Tienda_Virtual,
Nu_Activar_Medio_Pago_Lae_Shop,
Nu_Tipo_Forma_Pago_Lae_Shop,
Txt_Url_Imagen,
Nu_Cierre_Venta_Pago_Lae_Shop
)VALUES(
".$this->empresa->ID_Empresa.",
'Mercado Pago',
'CONTADO',
'006',
'48',
2,
1,
10,
0,
'Pagar con Tarjeta de crédito/débito',
1,
2,
'https://laesystems.com/assets/images/mercado_pago.png',
2);";
        if ($this->db->query($sql) > 0)
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Activado, modificar valores con columna EDITAR');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al activar Mercado Pago');
    }
}
