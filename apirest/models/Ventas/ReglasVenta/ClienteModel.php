<?php
class ClienteModel extends CI_Model{
	var $table                          = 'entidad';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_distrito                 = 'distrito';
	var $table_documento_cabecera       = 'documento_cabecera';
	
    var $column_order = array('TDI.No_Tipo_Documento_Identidad_Breve', 'Nu_Documento_Identidad', 'No_Entidad', 'Nu_Celular_Entidad', 'Txt_Email_Entidad', 'Nu_Dias_Credito', 'Txt_Direccion_Entidad', 'No_Contacto', 'Txt_Descripcion', 'Fe_Registro');
    var $column_search = array('');
    var $order = array('Fe_Registro' => 'desc');
	
	private $upload_path = '../assets/images/clientes/';
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
        	
        	if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Tipo_Entidad=0 AND Nu_Documento_Identidad='" . $row['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
            	$_arrCliente = array(
					'ID_Empresa' => $ID_Empresa,
					'ID_Organizacion' => $this->user->ID_Organizacion,
			        'Nu_Tipo_Entidad' => 0,
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
					'Nu_Dias_Credito' => $row['Nu_Dias_Credito'],
					'Nu_Estado' => 1,
            	);
				if ( !empty($ID_Pais) ){
					$_arrCliente = array_merge($_arrCliente, array("ID_Pais" => $ID_Pais));
				}
				if ( !empty($ID_Departamento) ){
					$_arrCliente = array_merge($_arrCliente, array("ID_Departamento" => $ID_Departamento));
				}
				if ( !empty($ID_Provincia) ){
					$_arrCliente = array_merge($_arrCliente, array("ID_Provincia" => $ID_Provincia));
				}
				if ( !empty($ID_Distrito) ){
					$_arrCliente = array_merge($_arrCliente, array("ID_Distrito" => $ID_Distrito));
				}
				$arrCliente[] = $_arrCliente;
        	} else {
        		$ID_Entidad = $this->db->query("SELECT ID_Entidad FROM " . $this->table . " WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Tipo_Entidad=0 AND Nu_Documento_Identidad='" . $row['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->ID_Entidad;
        		$_arrClienteUPD = array(
					'ID_Entidad' => $ID_Entidad,
					'ID_Empresa' => $ID_Empresa,
					'ID_Organizacion' => $this->user->ID_Organizacion,
			        'Nu_Tipo_Entidad' => 0,
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
					'Nu_Dias_Credito' => $row['Nu_Dias_Credito'],
					'Nu_Estado' => 1,
            	);
				if ( !empty($ID_Pais) ){
					$_arrClienteUPD = array_merge($_arrClienteUPD, array("ID_Pais" => $ID_Pais));
				}
				if ( !empty($ID_Departamento) ){
					$_arrClienteUPD = array_merge($_arrClienteUPD, array("ID_Departamento" => $ID_Departamento));
				}
				if ( !empty($ID_Provincia) ){
					$_arrClienteUPD = array_merge($_arrClienteUPD, array("ID_Provincia" => $ID_Provincia));
				}
				if ( !empty($ID_Distrito) ){
					$_arrClienteUPD = array_merge($_arrClienteUPD, array("ID_Distrito" => $ID_Distrito));
				}
				$arrClienteUPD[] = $_arrClienteUPD;
        	}
        }
        
        $bStatus=false;
        if (isset($arrCliente) && is_array($arrCliente))
    		$this->db->insert_batch($this->table, $arrCliente);
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;
    	if (isset($arrClienteUPD) && is_array($arrClienteUPD))
    		$this->db->update_batch($this->table, $arrClienteUPD, 'ID_Entidad');
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;
    	
    	unset($arrCliente);
    	unset($arrClienteUPD);
    	
    	return $bStatus;
    }
	
	public function _get_datatables_query(){
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Entidades') == 'Cliente' ){
            $this->db->like('No_Entidad', $this->input->post('Global_Filter'));
        } else if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Entidades') == 'NumeroDocumentoIdentidad' ){
        	$this->db->like('Nu_Documento_Identidad', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Empresa, ID_Entidad, TDI.No_Tipo_Documento_Identidad_Breve, Nu_Documento_Identidad, No_Entidad, Nu_Celular_Entidad, Txt_Email_Entidad, Nu_Dias_Credito, Txt_Direccion_Entidad, No_Contacto, DISTRI.No_Distrito, ' . $this->table . '.Nu_Estado, Txt_Descripcion, Fe_Registro, Nu_Agente_Compra, Nu_Carga_Consolidada, Nu_Importacion_Grupal, Nu_Curso, Nu_Viaje_Negocios')
		->from($this->table)
    	->join($this->table_distrito . ' AS DISTRI', 'DISTRI.ID_Distrito = ' . $this->table . '.ID_Distrito', 'left')
    	->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = ' . $this->table . '.ID_Tipo_Documento_Identidad', 'join')
    	->where('ID_Empresa', $this->user->ID_Empresa)
		->where_in('Nu_Tipo_Entidad', array('0','2','3'));
         
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
        $this->db->where('ID_Entidad',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarCliente($data){
		if ( $data['Nu_Documento_Identidad'] != '' && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Tipo_Entidad=0 AND Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarCliente($where, $data, $ENu_Documento_Identidad, $ENo_Entidad){
		if( strtoupper($ENu_Documento_Identidad) != $data['Nu_Documento_Identidad'] && $data['Nu_Documento_Identidad'] != '' && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Tipo_Entidad=0 AND Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if((strtoupper($ENu_Documento_Identidad) != $data['Nu_Documento_Identidad'] || $ENo_Entidad != $data['No_Entidad']) && $this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado == $where['ID_Entidad']){
				return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No se puede modificar Nombre o Documento Identidad de CLIENTES VARIOS');
			}

		    if ( $this->db->update($this->table, $data, $where) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarCliente($ID_Empresa, $ID, $Nu_Documento_Identidad){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Empresa=" . $ID_Empresa . " AND ID_Entidad=" . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El cliente tiene movimiento(s)');
		} else if($this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado == $ID){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No se puede eliminar CLIENTES VARIOS');
		} else {
			$this->db->where('ID_Entidad', $ID);
            $this->db->delete($this->table);
            
		    if ( $this->db->affected_rows() > 0 ) {
				if ( file_exists($this->upload_path . $Nu_Documento_Identidad . '.png') )
					unlink($this->upload_path . $Nu_Documento_Identidad . '.png');
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
