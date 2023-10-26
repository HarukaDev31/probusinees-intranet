<?php
class TipoMedioPagoMarketplaceModel extends CI_Model{
    var $table = 'tipo_medio_pago_marketplace';
    var $table_medio_pago = 'medio_pago_marketplace';
	var $table_tabla_dato = 'tabla_dato';
	var $table_empresa = 'empresa';
	var $table_pedido_cabecera = 'pedido_cabecera';
	var $table_caja_pos = 'caja_pos';
	
    var $column_order = array('No_Empresa', 'No_Medio_Pago_Marketplace', 'No_Tipo_Medio_Pago_Marketplace');
    var $column_search = array('No_Empresa', 'No_Medio_Pago_Marketplace', 'No_Tipo_Medio_Pago_Marketplace');
    var $order = array('No_Empresa' => 'asc', 'No_Medio_Pago_Marketplace' => 'asc', 'No_Tipo_Medio_Pago_Marketplace' => 'asc');
	
    private $upload_path = '../assets/images/medios_pago/';
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('Filtros_TipoMedioPagoMarketplace') == 'TipoMedioPagoMarketplace' )
            $this->db->like('No_Tipo_Medio_Pago_Marketplace', $this->input->post('Global_Filter'));

        if( $this->input->post('Filtros_TipoMedioPagoMarketplace') == 'MedioPago' )
            $this->db->like('No_Medio_Pago_Marketplace', $this->input->post('Global_Filter'));
    
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, MP.No_Medio_Pago_Marketplace, ID_Tipo_Medio_Pago_Marketplace, No_Tipo_Medio_Pago_Marketplace, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado, No_Imagen_Tipo_Medio_Pago_Marketplace, No_Imagen_Url_Tipo_Medio_Pago_Marketplace')
        ->from($this->table)
        ->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago_Marketplace = ' . $this->table . '.ID_Medio_Pago_Marketplace', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = MP.ID_Empresa', 'join')
        ->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');
         
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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago_Marketplace = ' . $this->table . '.ID_Medio_Pago_Marketplace', 'join');
        $this->db->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = MP.ID_Empresa', 'join');
        $this->db->where('ID_Tipo_Medio_Pago_Marketplace', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarTipoMedioPagoMarketplace($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Medio_Pago_Marketplace = ".$data['ID_Medio_Pago_Marketplace']." AND No_Tipo_Medio_Pago_Marketplace='" . $data['No_Tipo_Medio_Pago_Marketplace'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarTipoMedioPagoMarketplace($where, $data, $ENo_Tipo_Medio_Pago_Marketplace){
		if( $ENo_Tipo_Medio_Pago_Marketplace != $data['No_Tipo_Medio_Pago_Marketplace'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Medio_Pago_Marketplace = ".$data['ID_Medio_Pago_Marketplace']." AND No_Tipo_Medio_Pago_Marketplace='" . $data['No_Tipo_Medio_Pago_Marketplace'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarTipoMedioPagoMarketplace($ID){
        $arrTipoMedioPagoMarketplace = $this->db->query("SELECT ID_Medio_Pago_Marketplace, No_Imagen_Tipo_Medio_Pago_Marketplace FROM " . $this->table . " WHERE ID_Tipo_Medio_Pago_Marketplace=" . $ID . " LIMIT 1")->row();
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_pedido_cabecera . " WHERE ID_Medio_Pago_Marketplace=" . $arrTipoMedioPagoMarketplace->ID_Medio_Pago_Marketplace . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El medio de pago tiene movimiento(s)');
		} else {
			$this->db->where('ID_Tipo_Medio_Pago_Marketplace', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
				if ( !empty($sNombreImagen) ) {
					$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $arrTipoMedioPagoMarketplace->No_Imagen_Tipo_Medio_Pago_Marketplace;
					if ( file_exists($path) )
						unlink($path);
				}
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
            }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
