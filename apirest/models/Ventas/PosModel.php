<?php
class PosModel extends CI_Model{
	var $table = 'pos';
	var $table_empresa = 'empresa';
	var $table_organizacion	= 'organizacion';
	var $table_serie_documento = 'serie_documento';
	var $table_tabla_dato = 'tabla_dato';
	var $table_documento_cabecera   = 'documento_cabecera';
	var $table_almacen				= 'almacen';
	
    var $column_order = array('EMP.No_Empresa', 'ORG.No_Organizacion', 'ID_Pos', 'Nu_Pos', 'No_Pos');
    var $column_search = array('');
    var $order = array('EMP.No_Empresa' => 'asc', 'ORG.No_Organizacion' => 'asc', 'Nu_Pos' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') != '0')
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('filtro_organizacion') != '0')
			$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
			     
		$this->db->select('ORG.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ID_Pos, Nu_Pos, No_Pos, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
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
        if( $this->input->post('filtro_empresa') != '0')
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('filtro_organizacion') != '0')
			$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
			     
		$this->db->select('ORG.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ID_Pos, Nu_Pos, No_Pos, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID_Pos){
        $this->db->from($this->table);
		$this->db->where('ID_POS', $ID_Pos);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarPos($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Pos=" . $data['Nu_Pos'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El número ya existe');
		} else if(!empty($data['No_Pos']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Pos='" . $data['No_Pos'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El nombre ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 ) {
				/*
		    	$ID_Pos = $this->db->insert_id();
		    	if ($this->user->No_Usuario != 'root') {
			    	$ID_Empresa = $data['ID_Empresa'];
			    	$ID_Organizacion = $data['ID_Organizacion'];
			    	$ID_Almacen = $this->session->userdata['almacen']->ID_Almacen;
					if ( $this->empresa->Nu_Tipo_Proveedor_FE != 3 ) {
						$sSiglasSerie = ($this->empresa->Nu_Tipo_Proveedor_FE == '1' ? 'PP' : 'LA');// PP= Nubefact Reseller y LA = Sunat
						$sql = "INSERT INTO serie_documento(ID_Empresa, ID_Organizacion, ID_Almacen, ID_Tipo_Documento, ID_Serie_Documento, Nu_Numero_Documento, Nu_Cantidad_Caracteres, ID_POS, Nu_Estado)
VALUES
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0001', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0001', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 7, '0001', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 3, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 4, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 7, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 3, 'F" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 4, 'B" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'F" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'B" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'F" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'B" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1)";
					} else {
						$sql = "INSERT INTO serie_documento(ID_Empresa, ID_Organizacion, ID_Almacen, ID_Tipo_Documento, ID_Serie_Documento, Nu_Numero_Documento, Nu_Cantidad_Caracteres, ID_POS, Nu_Estado)
VALUES
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0001', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0001', 1, 6, NULL, 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $ID_Empresa . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0002', 1, 6, " . $ID_Pos . ", 1)";
					}
					$this->db->query($sql);
				}//if usuario root
				*/
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
			}
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarPos($where, $data, $EID_Organizacion, $ENu_Pos, $ENo_Pos){
		if( ($EID_Organizacion != $data['ID_Organizacion'] || $ENu_Pos != $data['Nu_Pos']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Pos=" . $data['Nu_Pos'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El número ya existe');
		} else if(!empty($data['No_Pos']) &&  ($EID_Organizacion != $data['ID_Organizacion'] || $ENo_Pos != $data['No_Pos']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Pos='" . $data['No_Pos'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El nombre ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarPos($ID_Pos){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_serie_documento . " WHERE ID_Pos='" . $ID_Pos . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El pos tiene asignada series');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK = VC.ID_Serie_Documento_PK) WHERE SD.ID_POS='" . $ID_Pos . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El pos tiene movimiento(s)');
		}else{
			$this->db->where('ID_Pos', $ID_Pos);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
