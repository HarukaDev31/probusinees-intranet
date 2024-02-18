<?php
class ProveedorModel extends CI_Model{
	var $table                          = 'entidad';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_distrito                 = 'distrito';
	var $table_documento_cabecera       = 'documento_cabecera';
	
    var $column_order = array('TDI.No_Tipo_Documento_Identidad_Breve', 'Nu_Documento_Identidad', 'No_Entidad');
    var $column_search = array('Nu_Documento_Identidad', 'No_Entidad');
    var $order = array('No_Entidad' => 'asc');
	
	private $_batchImport;
	
	public function __construct(){
		parent::__construct();
	}
 
    public function setBatchImport($arrProducto) {
        $this->_batchImport = $arrProducto;
    }
    
    public function importData() {
	    $ID_Empresa = $this->user->ID_Empresa;
	    $ID_Pais = 0;
	    $ID_Departamento = 0;
	    $ID_Provincia = 0;
	    $ID_Distrito = 0;
        
        foreach ($this->_batchImport as $row) {
        	if ( !empty($row['No_Pais']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM pais WHERE No_Pais='" . $row['No_Pais'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Pais = $this->db->query("SELECT ID_Pais FROM pais WHERE No_Pais='" . $row['No_Pais'] . "' LIMIT 1")->row()->ID_Pais;
        	}
        	
        	if ( !empty($row['No_Departamento']) ) {	
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM departamento WHERE No_Departamento='" . $row['No_Departamento'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Departamento = $this->db->query("SELECT ID_Departamento FROM departamento WHERE No_Departamento = '" . $row['No_Departamento'] . "' LIMIT 1")->row()->ID_Departamento;
        	}
        	
        	if ( !empty($row['No_Provincia']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM provincia WHERE No_Provincia='" . $row['No_Provincia'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Provincia = $this->db->query("SELECT ID_Provincia FROM provincia WHERE No_Provincia = '" . $row['No_Provincia'] . "' LIMIT 1")->row()->ID_Provincia;
        	}
        	
        	if ( !empty($row['No_Distrito']) ) {
	        	if ($this->db->query("SELECT COUNT(*) AS existe FROM distrito WHERE No_Distrito='" . $row['No_Distrito'] . "' LIMIT 1")->row()->existe > 0)
	        		$ID_Distrito = $this->db->query("SELECT ID_Distrito FROM distrito WHERE No_Distrito = '" . $row['No_Distrito'] . "' LIMIT 1")->row()->ID_Distrito;
        	}
        	
        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Tipo_Entidad=1 AND Nu_Documento_Identidad='" . $row['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
            	$_arrProveedor = array(
					'ID_Empresa' => $ID_Empresa,
					'ID_Organizacion' => $this->user->ID_Organizacion,
			        'Nu_Tipo_Entidad' => 1,
					'ID_Tipo_Documento_Identidad' => $row['ID_Tipo_Documento_Identidad'],
					'Nu_Documento_Identidad' => $row['Nu_Documento_Identidad'],
					'No_Entidad' => $row['No_Entidad'],
					'Txt_Direccion_Entidad' => $row['Txt_Direccion_Entidad'],
					'Nu_Telefono_Entidad' => $row['Nu_Telefono_Entidad'],
					'Nu_Celular_Entidad' => $row['Nu_Celular_Entidad'],
					'Txt_Email_Entidad' => $row['Txt_Email_Entidad'],
					'No_Contacto' => $row['No_Contacto'],
					'Nu_Celular_Contacto' => $row['Nu_Celular_Contacto'],
					'Txt_Email_Contacto' => $row['Txt_Email_Contacto'],
					'Txt_Descripcion' => $row['Txt_Descripcion'],
					'Nu_Estado' => 1,
            	);
				if ( !empty($ID_Pais) ){
					$_arrProveedor = array_merge($_arrProveedor, array("ID_Pais" => $ID_Pais));
				}
				if ( !empty($ID_Departamento) ){
					$_arrProveedor = array_merge($_arrProveedor, array("ID_Departamento" => $ID_Departamento));
				}
				if ( !empty($ID_Provincia) ){
					$_arrProveedor = array_merge($_arrProveedor, array("ID_Provincia" => $ID_Provincia));
				}
				if ( !empty($ID_Distrito) ){
					$_arrProveedor = array_merge($_arrProveedor, array("ID_Distrito" => $ID_Distrito));
				}
				$arrProveedor[] = $_arrProveedor;
        	} else {
        		$ID_Entidad = $this->db->query("SELECT ID_Entidad FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Tipo_Entidad=1 AND Nu_Documento_Identidad='" . $row['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->ID_Entidad;
        		$_arrProveedorUPD = array(
					'ID_Entidad' => $ID_Entidad,
					'ID_Empresa' => $ID_Empresa,
					'ID_Organizacion' => $this->user->ID_Organizacion,
			        'Nu_Tipo_Entidad' => 1,
					'ID_Tipo_Documento_Identidad' => $row['ID_Tipo_Documento_Identidad'],
					'Nu_Documento_Identidad' => $row['Nu_Documento_Identidad'],
					'No_Entidad' => $row['No_Entidad'],
					'Txt_Direccion_Entidad' => $row['Txt_Direccion_Entidad'],
					'Nu_Telefono_Entidad' => $row['Nu_Telefono_Entidad'],
					'Nu_Celular_Entidad' => $row['Nu_Celular_Entidad'],
					'Txt_Email_Entidad' => $row['Txt_Email_Entidad'],
					'No_Contacto' => $row['No_Contacto'],
					'Nu_Celular_Contacto' => $row['Nu_Celular_Contacto'],
					'Txt_Email_Contacto' => $row['Txt_Email_Contacto'],
					'Txt_Descripcion' => $row['Txt_Descripcion'],
					'Nu_Estado' => 1,
            	);
				if ( !empty($ID_Pais) ){
					$_arrProveedorUPD = array_merge($_arrProveedorUPD, array("ID_Pais" => $ID_Pais));
				}
				if ( !empty($ID_Departamento) ){
					$_arrProveedorUPD = array_merge($_arrProveedorUPD, array("ID_Departamento" => $ID_Departamento));
				}
				if ( !empty($ID_Provincia) ){
					$_arrProveedorUPD = array_merge($_arrProveedorUPD, array("ID_Provincia" => $ID_Provincia));
				}
				if ( !empty($ID_Distrito) ){
					$_arrProveedorUPD = array_merge($_arrProveedorUPD, array("ID_Distrito" => $ID_Distrito));
				}
				$arrProveedorUPD[] = $_arrProveedorUPD;
        	}
        }
        
        $bStatus=false;
        if (isset($arrProveedor) && is_array($arrProveedor))
    		$this->db->insert_batch($this->table, $arrProveedor);
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;
    	if (isset($arrProveedorUPD) && is_array($arrProveedorUPD))
    		$this->db->update_batch($this->table, $arrProveedorUPD, 'ID_Entidad');
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;
    	
    	unset($arrProveedor);
    	unset($arrProveedorUPD);
    	
    	return $bStatus;
    }
	
	public function _get_datatables_query(){
        $this->db->select('ID_Entidad, TDI.No_Tipo_Documento_Identidad_Breve, Nu_Documento_Identidad, No_Entidad, Nu_Celular_Entidad, Txt_Email_Entidad, Nu_Dias_Credito, Txt_Direccion_Entidad, DISTRI.No_Distrito, ' . $this->table . '.Nu_Estado')
		->from($this->table)
    	->join($this->table_distrito . ' AS DISTRI', 'DISTRI.ID_Distrito = ' . $this->table . '.ID_Distrito', 'left')
    	->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = ' . $this->table . '.ID_Tipo_Documento_Identidad', 'join')
    	->where('ID_Empresa', $this->user->ID_Empresa)
    	->where('Nu_Tipo_Entidad', 1);

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
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
        $this->db->where('ID_Entidad',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarProveedor($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Tipo_Entidad=1 AND Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarProveedor($where, $data, $ENu_Documento_Identidad){
		if( strtoupper($ENu_Documento_Identidad) != $data['Nu_Documento_Identidad'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Tipo_Entidad=1 AND Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarProveedor($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Entidad=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El proveedor tiene movimiento(s)');
		}else{
			$this->db->where('ID_Entidad', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
	}
}
