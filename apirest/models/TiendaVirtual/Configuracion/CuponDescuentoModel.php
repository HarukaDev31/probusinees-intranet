<?php
class CuponDescuentoModel extends CI_Model{
	var $table = 'cupon_descuento';
	var $table_empresa = 'empresa';
	
    var $column_order = array('No_Codigo_Cupon_Descuento', 'Txt_Cupon_Descuento', 'Nu_Tipo_Cupon_Descuento', 'Ss_Valor_Cupon_Descuento', 'Fe_Inicio', 'Fe_Vencimiento', 'Nu_Total_Uso_Cupon');
    var $column_search = array('');
    var $order = array('Fe_Inicio' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
    
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_CuponDescuento') == 'CuponDescuento' ){
            $this->db->like('No_Codigo_Cupon_Descuento', $this->input->post('Global_Filter'));
        }

        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Cupon_Descuento, No_Codigo_Cupon_Descuento, Txt_Cupon_Descuento, Nu_Tipo_Cupon_Descuento, Ss_Valor_Cupon_Descuento, Fe_Inicio, Fe_Vencimiento, Nu_Total_Uso_Cupon')
        ->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
         
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
        $this->db->where('ID_Cupon_Descuento', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarCuponDescuento($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND No_Codigo_Cupon_Descuento='" . $data['No_Codigo_Cupon_Descuento'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El código ya existe: ' . $data['No_Codigo_Cupon_Descuento']);
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarCuponDescuento($where, $data, $ENo_Codigo_Cupon_Descuento){
        if($ENo_Codigo_Cupon_Descuento != $data['No_Codigo_Cupon_Descuento'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND No_Codigo_Cupon_Descuento='" . $data['No_Codigo_Cupon_Descuento'] . "' LIMIT 1")->row()->existe > 0){
            return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El código ya existe: ' . $data['No_Codigo_Cupon_Descuento']);
        }else{
            if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
}
