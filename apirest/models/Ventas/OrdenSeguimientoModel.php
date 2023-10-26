<?php
class OrdenSeguimientoModel extends CI_Model{
	var $table                          = 'orden_seguimiento';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_documento_cabecera       = 'documento_cabecera';
	var $table_usuario                  = 'usuario';
	var $table_entidad                  = 'entidad';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_almacen				= 'almacen';
	
    var $column_order = array('OS.Fe_Registro', null, 'VC.ID_Documento_Cabecera');
    var $column_search = array('');
    var $order = array('OS.Fe_Registro' => 'desc', 'VC.ID_Documento_Cabecera' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if(!empty($this->input->post('Filtro_Contacto')))
        	$this->db->where('CONTACT.No_Entidad', $this->input->post('Filtro_Contacto'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Documento_Cabecera', $this->input->post('Filtro_NumeroDocumento'));
        
        if(!empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('CLI.No_Entidad', $this->input->post('Filtro_Entidad'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

    	$this->db->where("OS.Fe_Registro BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");

		$this->db->select('VC.ID_Empresa, ALMA.No_Almacen, OS.ID_Orden_Seguimiento, OS.Fe_Registro, TDESTADO.No_Descripcion, VC.ID_Documento_Cabecera, CLI.No_Entidad, OS.Nu_Tipo_Contacto, CONTACT.No_Entidad AS No_Contacto, OS.No_Contacto AS No_Contacto_Seguimiento, OS.Txt_Observacion')
		->from($this->table . ' AS OS')
		->join($this->table_documento_cabecera . ' AS VC', 'VC.ID_Documento_Cabecera = OS.ID_Documento_Cabecera', 'join')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_entidad . ' AS CONTACT', 'CONTACT.ID_Entidad = VC.ID_Contacto', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = OS.ID_Tipo_Orden_Seguimiento AND TDESTADO.No_Relacion = "Tipos_Orden_Seguimiento"', 'join')
        ->where('VC.ID_Empresa', $this->user->ID_Empresa)
        ->where('VC.ID_Organizacion', $this->user->ID_Organizacion);
		
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
        if(!empty($this->input->post('Filtro_Contacto')))
        	$this->db->where('CONTACT.No_Entidad', $this->input->post('Filtro_Contacto'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Documento_Cabecera', $this->input->post('Filtro_NumeroDocumento'));
        
        if(!empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('CLI.No_Entidad', $this->input->post('Filtro_Entidad'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

    	$this->db->where("OS.Fe_Registro BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");

		$this->db->select('VC.ID_Empresa, ALMA.No_Almacen, OS.ID_Orden_Seguimiento, OS.Fe_Registro, TDESTADO.No_Descripcion, VC.ID_Documento_Cabecera, CLI.No_Entidad, OS.Nu_Tipo_Contacto, CONTACT.No_Entidad AS No_Contacto, OS.No_Contacto AS No_Contacto_Seguimiento, OS.Txt_Observacion')
		->from($this->table . ' AS OS')
		->join($this->table_documento_cabecera . ' AS VC', 'VC.ID_Documento_Cabecera = OS.ID_Documento_Cabecera', 'join')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_entidad . ' AS CONTACT', 'CONTACT.ID_Entidad = VC.ID_Contacto', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = OS.ID_Tipo_Orden_Seguimiento AND TDESTADO.No_Relacion = "Tipos_Orden_Seguimiento"', 'join')
        ->where('VC.ID_Empresa', $this->user->ID_Empresa)
        ->where('VC.ID_Organizacion', $this->user->ID_Organizacion);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID_Orden_Seguimiento){
        $this->db->select('OS.ID_Tipo_Orden_Seguimiento, OS.ID_Orden_Seguimiento, OS.Fe_Registro, OS.ID_Documento_Cabecera, VC.ID_Documento_Cabecera, OS.Nu_Tipo_Contacto, CONTACT.No_Entidad AS No_Contacto, OS.No_Contacto AS No_Contacto_Seguimiento, OS.ID_Tipo_Documento_Identidad, OS.Nu_Documento_Identidad, OS.Txt_Email_Contacto, OS.Nu_Celular_Contacto, OS.Nu_Telefono_Contacto, OS.Txt_Observacion')
        ->from($this->table . ' AS OS')
        ->join($this->table_documento_cabecera . ' AS VC', 'VC.ID_Documento_Cabecera = OS.ID_Documento_Cabecera', 'join')
		->join($this->table_entidad . ' AS CONTACT', 'CONTACT.ID_Entidad = VC.ID_Contacto', 'join')
        ->where('ID_Orden_Seguimiento', $ID_Orden_Seguimiento);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarOrdenSeguimiento($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if ( $this->db->insert($this->table, $data) > 0 )
			$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		return $response;
    }
    
    public function actualizarOrdenSeguimiento($where, $data){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
	    if ( $this->db->update($this->table, $data, $where) > 0 )
	        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return $response;
    }
    
	public function eliminarOrdenSeguimiento($ID_Orden_Seguimiento){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		
		$this->db->where('ID_Orden_Seguimiento', $ID_Orden_Seguimiento);
        $query = $this->db->delete($this->table);
        if ($query)
            $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        return $response;
	}
}
