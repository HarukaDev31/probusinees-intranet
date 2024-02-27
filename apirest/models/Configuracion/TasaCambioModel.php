<?php
class TasaCambioModel extends CI_Model{
	var $table = 'tasa_cambio';
	var $table_moneda = 'moneda';
	var $table_empresa = 'empresa';
	var $table_tabla_dato = 'tabla_dato';
	
    var $column_order = array('No_Empresa', 'No_Moneda', 'No_Signo', 'Fe_Ingreso', 'Ss_Venta_Oficial', 'Ss_Compra_Oficial');
    var $column_search = array();
    var $order = array('No_Empresa' => 'asc', 'No_Moneda' => 'asc', 'Fe_Ingreso' => 'asc');
	
	public function __construct(){
		parent::__construct();

		if ( $this->user->No_Usuario != 'root' ){
			$this->column_order = array('No_Moneda', 'No_Signo', 'Fe_Ingreso', 'Ss_Venta_Oficial', 'Ss_Compra_Oficial');
    		$this->order = array('No_Moneda' => 'asc', 'Fe_Ingreso' => 'asc');
		}
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') != '' )
			$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
			
        if($this->input->post('Filtro_Moneda') != '')
        	$this->db->where('MONE.ID_Moneda', $this->input->post('Filtro_Moneda'));
        
		$this->db->select('ID_Tasa_Cambio, EMP.No_Empresa, No_Moneda, No_Signo, Fe_Ingreso, Ss_Venta_Oficial, Ss_Compra_Oficial')
		->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
    	->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = ' . $this->table . '.ID_Moneda', 'left')
		->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
    	->where("Fe_Ingreso BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

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
        $this->db->where('ID_Tasa_Cambio', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarTasaCambio($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Moneda='" . $data['ID_Moneda'] . "' AND Fe_Ingreso='" . $data['Fe_Ingreso'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarTasaCambio($where, $data, $EID_Moneda, $EFe_Ingreso){
		if( ($EID_Moneda != $data['ID_Moneda'] || $EFe_Ingreso != $data['Fe_Ingreso']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Moneda='" . $data['ID_Moneda'] . "' AND Fe_Ingreso='" . $data['Fe_Ingreso'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarTasaCambio($ID){
		$this->db->where('ID_Tasa_Cambio', $ID);
        $this->db->delete($this->table);
	    if ( $this->db->affected_rows() > 0 )
	        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Error al eliminar');
	}
	
	public function save_exchange_rate($arrPost){
		$iIdEmpresa = $arrPost['iIdEmpresa'];
		$ID_Moneda = $arrPost['ID_Moneda'];
		$arrData = $arrPost['arrData'];
		$this->db->trans_begin();
	    foreach ($arrData as $row){
	        if ( $this->db->query("SELECT COUNT(*) AS existe FROM tasa_cambio WHERE ID_Empresa = " . $iIdEmpresa . " AND ID_Moneda = " . $ID_Moneda . " AND Fe_Ingreso = '" . $row['Fe_Entry'] . "' LIMIT 1")->row()->existe == 0) {
	            $arrDataInserts[] = array(
                	'ID_Empresa'        => $iIdEmpresa,
                	'ID_Moneda'         => $ID_Moneda,
                	'Fe_Ingreso'        => $row['Fe_Entry'],
                	'Ss_Compra_Oficial' => $row['Ss_Purchase_Sunat'],
                	'Ss_Venta_Oficial'  => $row['Ss_Sale_Sunat'],
				);
	        } else {
	        	$ID_Tasa_Cambio = $this->db->query("SELECT ID_Tasa_Cambio FROM " . $this->table . " WHERE ID_Empresa = " . $iIdEmpresa . " AND ID_Moneda = " . $ID_Moneda . " AND Fe_Ingreso = '" . $row['Fe_Entry'] . "' LIMIT 1")->row()->ID_Tasa_Cambio;
	            $arrDataUPD[] = array(
                	'ID_Tasa_Cambio'    => $ID_Tasa_Cambio,
                	'Ss_Compra_Oficial' => $row['Ss_Purchase_Sunat'],
                	'Ss_Venta_Oficial'  => $row['Ss_Sale_Sunat'],
				);
	        }
	    }
	    
        if (isset($arrDataInserts) && is_array($arrDataInserts))
    		$this->db->insert_batch($this->table, $arrDataInserts);
    	if (isset($arrDataUPD) && is_array($arrDataUPD))
    		$this->db->update_batch($this->table, $arrDataUPD, 'ID_Tasa_Cambio');

    	$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problema al guardar tasa de cambio');
        } else {
            $this->db->trans_commit();
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Tasa de cambio guardado');
        }
	}
}
