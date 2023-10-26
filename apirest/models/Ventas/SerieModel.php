<?php
class SerieModel extends CI_Model{
	var $table                      = 'serie_documento';
	var $table_empresa              = 'empresa';
	var $table_organizacion	        = 'organizacion';
	var $table_almacen		        = 'almacen';
	var $table_pos	        		= 'pos';
	var $table_tipo_documento	    = 'tipo_documento';
	var $table_tabla_dato           = 'tabla_dato';
	var $table_documento_cabecera   = 'documento_cabecera';
	
    var $column_order = array('ALMA.No_Almacen', 'TDOCU.No_Tipo_Documento_Breve', 'ID_Serie_Documento', 'Nu_Numero_Documento', 'POS.Nu_Pos');
    var $column_search = array('');
    var $order = array('ID_Serie_Documento_PK' => 'DESC');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if ( $this->user->No_Usuario == 'root' ){
			$this->column_order = array('EMP.No_Empresa', 'ORG.No_Organizacion', 'ALMA.No_Almacen', 'TDOCU.No_Tipo_Documento_Breve', 'ID_Serie_Documento', 'Nu_Numero_Documento', 'POS.Nu_Pos', null);
		}

        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('filtro_organizacion') )
			$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
        
		if( $this->input->post('filtro_almacen') )
			$this->db->where('ALMA.ID_Almacen', $this->input->post('filtro_almacen'));

        if($this->input->post('Filtro_TiposDocumento'))
        	$this->db->where('TDOCU.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
        
        if($this->input->post('Filtro_SeriesDocumento'))
        	$this->db->where('ID_Serie_Documento', $this->input->post('Filtro_SeriesDocumento'));
        
		$this->db->select('ID_Serie_Documento_PK, EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ALMA.ID_Almacen, ALMA.No_Almacen, TDOCU.ID_Tipo_Documento, TDOCU.No_Tipo_Documento_Breve, ID_Serie_Documento, Nu_Numero_Documento, POS.ID_POS, POS.Nu_Pos, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Empresa = ' . $this->table . '.ID_Empresa AND ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Organizacion = ' . $this->table . '.ID_Organizacion AND ALMA.ID_Almacen = ' . $this->table . '.ID_Almacen', 'left')
		->join($this->table_pos . ' AS POS', 'POS.ID_POS = ' . $this->table . '.ID_POS', 'left')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = ' . $this->table . '.ID_Tipo_Documento', 'join')
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
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('filtro_organizacion') )
			$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
        
		if( $this->input->post('filtro_almacen') )
			$this->db->where('ALMA.ID_Almacen', $this->input->post('filtro_almacen'));

        if($this->input->post('Filtro_TiposDocumento'))
        	$this->db->where('TDOCU.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
        
        if($this->input->post('Filtro_SeriesDocumento'))
        	$this->db->where('ID_Serie_Documento', $this->input->post('Filtro_SeriesDocumento'));
        
		$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ALMA.ID_Almacen, ALMA.No_Almacen, TDOCU.ID_Tipo_Documento, TDOCU.No_Tipo_Documento_Breve, ID_Serie_Documento, Nu_Numero_Documento, POS.ID_POS, POS.Nu_Pos, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Empresa = ' . $this->table . '.ID_Empresa AND ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Organizacion = ' . $this->table . '.ID_Organizacion AND ALMA.ID_Almacen = ' . $this->table . '.ID_Almacen', 'left')
		->join($this->table_pos . ' AS POS', 'POS.ID_POS = ' . $this->table . '.ID_POS', 'left')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = ' . $this->table . '.ID_Tipo_Documento', 'join')
		->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');
		
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID_Serie_Documento_PK){
        $this->db->from($this->table);
		$this->db->where('ID_Serie_Documento_PK', $ID_Serie_Documento_PK);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarSerie($data, $iSerieIgual){
		if ($iSerieIgual == 0 && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La serie ya existe');
		} else {
			$this->db->trans_begin();
			if ( $this->db->insert($this->table, $data) > 0 ) {
				//validacion para los que desean manejar la misma serie para todas sus cajas
				if ($iSerieIgual == 1 && $this->db->query("SELECT COUNT(*) AS existe FROM serie_documento WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' AND Nu_Estado=1")->row()->existe > 0) {
					$arrDataSerieIguales = $this->db->query("SELECT ID_Serie_Documento, Nu_Numero_Documento FROM serie_documento WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' AND Nu_Estado=1")->result();
					//$arrDataSerieIguales2 = $this->db->query("SELECT ID_Serie_Documento, Nu_Numero_Documento FROM serie_documento WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' AND Nu_Estado=1")->result();
					
					foreach ($arrDataSerieIguales as $row) {
						$Nu_Numero_Documento = $row->Nu_Numero_Documento;
						if($Nu_Numero_Documento!=$data['Nu_Numero_Documento'] && $data['Nu_Estado']==1){
							$this->db->trans_rollback();
							return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Si utilizará misma SERIE los correlativos deben de ser iguales');
							//return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La serie: ' . $row->ID_Serie_Documento . ' y correlativo: ' . $Nu_Numero_Documento . ' es diferente a la serie: ' . $data['ID_Serie_Documento'] . ' y correlativo: ' . $data['Nu_Numero_Documento'] . '. Solución deben de ser iguales');
						}
					}// ./ foreach enlace de items
				}
				
				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
			}
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarSerie($where, $data, $EID_Organizacion, $EID_Almacen, $EID_Tipo_Documento, $EID_Serie_Documento, $iSerieIgual){
		if ($iSerieIgual == 0 && ($EID_Tipo_Documento != $data['ID_Tipo_Documento'] || $EID_Serie_Documento != $data['ID_Serie_Documento']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La serie ya existe');
		} else {
			$this->db->trans_begin();
		    if ( $this->db->update($this->table, $data, $where) > 0 ) {
				//validacion para los que desean manejar la misma serie para todas sus cajas
				if ($iSerieIgual == 1 && $this->db->query("SELECT COUNT(*) AS existe FROM serie_documento WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' AND Nu_Estado=1")->row()->existe > 0) {
					$arrDataSerieIguales = $this->db->query("SELECT ID_Serie_Documento, Nu_Numero_Documento FROM serie_documento WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' AND Nu_Estado=1")->result();
					//$arrDataSerieIguales2 = $this->db->query("SELECT ID_Serie_Documento, Nu_Numero_Documento FROM serie_documento WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Tipo_Documento=" . $data['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $data['ID_Serie_Documento'] . "' AND Nu_Estado=1")->result();
					
					foreach ($arrDataSerieIguales as $row) {
						$Nu_Numero_Documento = $row->Nu_Numero_Documento;
						if($Nu_Numero_Documento!=$data['Nu_Numero_Documento'] && $data['Nu_Estado']==1){
							$this->db->trans_rollback();
							return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Si utilizará misma SERIE los correlativos deben de ser iguales');
							//return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La serie: ' . $row->ID_Serie_Documento . ' y correlativo: ' . $Nu_Numero_Documento . ' es diferente a la serie: ' . $data['ID_Serie_Documento'] . ' y correlativo: ' . $data['Nu_Numero_Documento'] . '. Solución deben de ser iguales');
						}
					}// ./ foreach enlace de items
				}

				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
			}
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarSerie($ID_Serie_Documento_PK){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Serie_Documento_PK='" . $ID_Serie_Documento_PK . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La serie tiene movimiento(s)');
		}else{
			$this->db->where('ID_Serie_Documento_PK', $ID_Serie_Documento_PK);
            $this->db->delete($this->table);
            
		    if ( $this->db->affected_rows() > 0 ) {
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
