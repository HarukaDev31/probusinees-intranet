<?php
class FacturaVentaLaeModel extends CI_Model{
	var $table                      = 'documento_cabecera';
	var $table_tipo_documento	    = 'tipo_documento';
	var $table_tabla_dato           = 'tabla_dato';
	var $table_entidad           = 'entidad';
	
    var $column_order = array('Fe_Emision', 'No_Tipo_Documento_Breve', 'ID_Serie_Documento', 'ID_Numero_Documento', 'Ss_Total', 'Ss_Total_Saldo');
    var $column_search = array('');
    var $order = array('Fe_Emision' => 'ASC', 'TDOCU.No_Tipo_Documento_Breve' => 'desc', 'ID_Serie_Documento' => 'desc', 'ID_Numero_Documento' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
		$this->db->select('VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, ID_Numero_Documento, VC.Ss_Total, VC.Ss_Total_Saldo, VC.Nu_Estado, VC.Txt_Url_PDF, VC.Txt_Url_XML, VC.Txt_Url_CDR')
		->from($this->table . ' AS VC')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->where('VC.ID_Tipo_Asiento', 1)
		->where('VC.ID_Empresa', 1)
		->where('CLI.Nu_Documento_Identidad', $this->empresa->Nu_Documento_Identidad)
		->where_in('VC.ID_Tipo_Documento', array('2', '3','4','5','6'));
		
        $this->db->order_by( 'VC.ID_Documento_Cabecera DESC' );
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
		$this->db->select('VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, ID_Numero_Documento, VC.Ss_Total, VC.Ss_Total_Saldo, VC.Nu_Estado, VC.Txt_Url_PDF, VC.Txt_Url_XML, VC.Txt_Url_CDR')
		->from($this->table . ' AS VC')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->where('VC.ID_Tipo_Asiento', 1)
		->where('VC.ID_Empresa', 1)
		->where('CLI.Nu_Documento_Identidad', $this->empresa->Nu_Documento_Identidad)
		->where_in('VC.ID_Tipo_Documento', array('3','4','5','6'));
        return $this->db->count_all_results();
    }
}
