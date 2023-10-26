<?php
class BilleteraVirtualModel extends CI_Model{
    var $table = 'dropshipping_cuenta_billetera';
    var $table_banco = 'dropshipping_banco';
    var $table_moneda = 'moneda';
    var $table_empresa = 'empresa';
    var $table_dropshipping_transaccion_pendiente_cuenta_billetera = 'dropshipping_transaccion_pendiente_cuenta_billetera';
    var $table_dropshipping_transaccion_procesada_cuenta_billetera = 'dropshipping_transaccion_procesada_cuenta_billetera';
    
    var $column_order = array('No_Banco_Siglas','Nu_Tipo_Cuenta','No_Moneda','No_Cuenta_Bancaria','No_Cuenta_Interbancario','No_Titular_Cuenta','Nu_Estado');
    var $column_search = array('');
    var $order = array('dropshipping_cuenta_billetera.Fe_Registro' => 'desc');
    
    private $upload_path = '../assets/images/logos';
    
    public function __construct(){
        parent::__construct();
    }
    
    public function _get_datatables_query(){
        if ( $this->user->ID_Usuario != 1 ){
            $this->column_order = array('No_Empresa', 'No_Banco_Siglas','Nu_Tipo_Cuenta','No_Moneda','No_Cuenta_Bancaria','No_Cuenta_Interbancario','No_Titular_Cuenta','Nu_Estado');
        }

        $this->db->select('B.ID_Banco, ID_Cuenta_Bancaria_Billetera, No_Empresa, No_Banco_Siglas, Nu_Tipo_Cuenta, No_Moneda, No_Cuenta_Bancaria, No_Cuenta_Interbancario, No_Titular_Cuenta, ' . $this->table . '.Nu_Estado')
        ->from($this->table)
        ->join($this->table_banco . ' AS B', 'B.ID_Banco = ' . $this->table . '.ID_Banco', 'join')
        ->join($this->table_moneda . ' AS M', 'M.ID_Moneda = ' . $this->table . '.ID_Moneda', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
        
        if ( $this->user->ID_Usuario == 1 ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        }

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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->select('C.*, STV.No_Subdominio_Tienda_Virtual,STV.ID_Subdominio_Tienda_Virtual,concat_ws(".",STV.No_Subdominio_Tienda_Virtual,STV.No_Dominio_Tienda_Virtual) AS DominioActual');
        $this->db->from($this->table . ' AS C');
        $this->db->join('subdominio_tienda_virtual AS STV', 'STV.ID_Empresa = C.ID_Empresa', 'join');
        $this->db->where('ID_Configuracion',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregar($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND No_Cuenta_Bancaria='" . $data['No_Cuenta_Bancaria'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya existe el Nro. Cuenta de Bancaria: ' . $data['No_Cuenta_Bancaria']);
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Cuenta Bancaria registrada correctamente');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al guardar');
    }
    
    public function actualizar($where, $data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Cuenta_Bancaria_Billetera=".$where['ID_Cuenta_Bancaria_Billetera']." AND ID_Empresa =".$data['ID_Empresa']." AND No_Cuenta_Bancaria='" . $data['No_Cuenta_Bancaria'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya existe el Nro. Cuenta de Bancaria: ' . $data['No_Cuenta_Bancaria']);
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Cuenta Bancaria actualizada correctamente');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarCuentaBancaria($ID){
        /*
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Marca=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La marca tiene producto(s)');
		}else{
            */
			$this->db->where('ID_Cuenta_Bancaria_Billetera', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
            }
		//}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}

	//LISTAR DESEMBOLSOS PENDIENTES
    public function _get_datatables_query_desembolso_pendiente(){
        $this->column_order = array('ID_Transaccion_Pendiente', 'DCB.No_Cuenta_Bancaria', 'Ss_Importe', 'Fe_Registro');
        if ( $this->user->ID_Usuario != 1 ){
            $this->column_order = array('No_Empresa', 'ID_Transaccion_Pendiente', 'DCB.No_Cuenta_Bancaria', 'Ss_Importe', 'Fe_Registro');
        }

        $this->order = array('TP.Fe_Registro' => 'desc');

        $this->db->select('No_Empresa, ID_Transaccion_Pendiente, DCB.No_Cuenta_Bancaria, Ss_Importe, TP.Fe_Registro')
        ->from($this->table_dropshipping_transaccion_pendiente_cuenta_billetera . ' AS TP')
        ->join($this->table . ' AS DCB', 'DCB.ID_Cuenta_Bancaria_Billetera = TP.ID_Cuenta_Bancaria_Billetera', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = TP.ID_Empresa', 'join');
        
        if ( $this->user->ID_Usuario == 1 ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        }

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function get_datatables_desembolso_pendiente(){
        $this->_get_datatables_query_desembolso_pendiente();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered_desembolso_pendiente(){
        $this->_get_datatables_query_desembolso_pendiente();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_desembolso_pendiente(){
        $this->db->from($this->table_dropshipping_transaccion_pendiente_cuenta_billetera);
        return $this->db->count_all_results();
    }

	//LISTAR DESEMBOLSOS PAGOS
    public function _get_datatables_query_desembolso_pago(){
        $this->column_order = array('ID_Transaccion_Procesada', 'DCB.No_Cuenta_Bancaria', 'Ss_Importe', 'Fe_Registro');
        if ( $this->user->ID_Usuario != 1 ){
            $this->column_order = array('No_Empresa', 'ID_Transaccion_Procesada', 'DCB.No_Cuenta_Bancaria', 'Ss_Importe', 'Fe_Registro');
        }
        $this->order = array('TP.Fe_Registro' => 'desc');

        $this->db->select('ID_Transaccion_Procesada, DCB.No_Cuenta_Bancaria, Ss_Importe, TP.Fe_Registro')
        ->from($this->table_dropshipping_transaccion_procesada_cuenta_billetera . ' AS TP')
        ->join($this->table . ' AS DCB', 'DCB.ID_Cuenta_Bancaria_Billetera = TP.ID_Cuenta_Bancaria_Billetera', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = TP.ID_Empresa', 'join');
        
        if ( $this->user->ID_Usuario == 1 ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        }

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function get_datatables_desembolso_pago(){
        $this->_get_datatables_query_desembolso_pago();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered_desembolso_pago(){
        $this->_get_datatables_query_desembolso_pago();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_desembolso_pago(){
        $this->db->from($this->table_dropshipping_transaccion_procesada_cuenta_billetera);
        return $this->db->count_all_results();
    }

	public function obtenerSaldoPedidoEntregados(){
	    $query = "SELECT
SUM(PC.Ss_Total) AS Ss_Total,
SUM(CASE WHEN PC.Nu_Servicio_Transportadora_Dropshipping=1 THEN 4 ELSE 0 END) AS Ss_Callcenter,
SUM(CASE WHEN PC.Nu_Forma_Pago_Dropshipping=2 THEN 5 ELSE 0 END) AS Ss_Dropshipping,
SUM(PC.Ss_Precio_Delivery) AS Ss_Precio_Delivery,
(SELECT SUM(PD.Ss_Precio_Empresa_Proveedor * PD.Qt_Producto) AS Ss_Total FROM pedido_detalle AS PD WHERE PD.ID_Pedido_Cabecera = PC.ID_Pedido_Cabecera) AS Ss_Total_Proveedor
FROM
pedido_cabecera AS PC
WHERE
PC.ID_Empresa=" . $this->user->ID_Empresa . "
AND PC.Nu_Estado_Pedido_Empresa=1
AND PC.Nu_Estado=5";
//array_debug($query);
//Nu_Estado_Pedido_Empresa = 1 pedido completados porque Nu_Estado_Pedido_Empresa 2 = pedidos ya cancelados
	    return $this->db->query($query)->row();
	}

	public function obtenerSaldoPedidoFalsaParada(){
	    $query = "SELECT
SUM(CASE WHEN PC.Nu_Forma_Pago_Dropshipping=2 THEN 5 ELSE 0 END) AS Ss_Dropshipping,
SUM(PC.Ss_Precio_Delivery) AS Ss_Precio_Delivery
FROM
pedido_cabecera AS PC
WHERE
PC.ID_Empresa=" . $this->user->ID_Empresa . "
AND PC.Nu_Estado_Pedido_Empresa=3";
	    return $this->db->query($query)->row();
	}
}
