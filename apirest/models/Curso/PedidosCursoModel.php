<?php
class PedidosCursoModel extends CI_Model{
	var $table = 'pedido_curso';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_configuracion = 'configuracion';
	var $table_moneda = 'moneda';
	var $table_cliente = 'entidad';
	var $table_medio_pago = 'medio_pago';
	var $table_departamento = 'departamento';
	var $table_provincia = 'provincia';
	var $table_distrito = 'distrito';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_usuario = 'usuario';
	var $table_pais = 'pais';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, TDI.No_Tipo_Documento_Identidad_Breve, P.No_Pais, CLI.No_Entidad, CLI.Nu_Documento_Identidad, CLI.Nu_Celular_Entidad, CLI.Txt_Email_Entidad, M.No_Signo, USR.ID_Usuario, USR.No_Usuario, USR.No_Password')
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join')
    	->join($this->table_moneda . ' AS M', 'M.ID_Moneda = ' . $this->table . '.ID_Moneda', 'join')
    	->join($this->table_usuario . ' AS USR', 'USR.ID_Entidad = CLI.ID_Entidad', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa);

		if(!empty($this->input->post('estado_pago')))
			$this->db->where( $this->table . ".Nu_Estado=", $this->input->post('estado_pago'));

		$this->db->where("Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");

		if(isset($this->order)) {
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

    public function actualizarPedido($where, $data){
        if ( $this->db->update('pedido_curso', $data, $where) > 0 )
            return array('status' => 'success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'message' => 'Error al modificar');
    }

	public function getUsuario($id){
		$query = "SELECT No_Usuario, No_Password, No_Nombres_Apellidos FROM usuario WHERE ID_Usuario = " . $id . " LIMIT 1";
		
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}
}
