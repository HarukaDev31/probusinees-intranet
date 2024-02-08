<?php
class PedidosCargaConsolidadaModel extends CI_Model{
	var $table = 'carga_consolidada_pedido_cabecera';
	var $table_carga_consolidada_pedido_detalle = 'carga_consolidada_pedido_detalle';
	var $table_carga_consolidada_seguimiento = 'carga_consolidada_seguimiento';
	var $table_cliente = 'entidad';

    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*')
		->from($this->table)
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa);

		$this->db->where("Fe_Registro BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");

		if(isset($this->order)) {
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
        $this->db->select($this->table . '.*, CLI.No_Entidad, CLI.ID_Entidad');
        $this->db->from($this->table);
    	$this->db->join($this->table_carga_consolidada_pedido_detalle . ' AS PD', 'PD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = PD.ID_Entidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function agregarPedido($data, $arrEntidad){
		if ( $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Carga_Consolidada='" . $data['No_Carga_Consolidada'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
			if ( $this->db->insert($this->table, $data) > 0 ) {
				$ID_Pedido_Cabecera = $this->db->insert_id();
				foreach ($arrEntidad as $row) {
					//array_debug($row['ID_Entidad']);
					$detalle[] = array(
						'ID_Empresa'			=> $this->user->ID_Empresa,
						'ID_Organizacion'		=> $this->user->ID_Organizacion,//Organizacion
						'ID_Pedido_Cabecera' 	=> $ID_Pedido_Cabecera,
						'ID_Entidad'			=> $this->security->xss_clean($row['ID_Entidad'])
					);
				}
				$this->db->insert_batch($this->table_carga_consolidada_pedido_detalle, $detalle);

				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
			}
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarPedido($where, $data, $arrEntidad){
		if ( $this->db->update($this->table, $data, $where) > 0 ) {

	    	$this->db->where('ID_Pedido_Cabecera', $where['ID_Pedido_Cabecera']);
        	$this->db->delete($this->table_carga_consolidada_pedido_detalle);
			foreach ($arrEntidad as $row) {
				//array_debug($row['ID_Entidad']);
				$detalle[] = array(
					'ID_Empresa'			=> $this->user->ID_Empresa,
					'ID_Organizacion'		=> $this->user->ID_Organizacion,//Organizacion
					'ID_Pedido_Cabecera' 	=> $where['ID_Pedido_Cabecera'],
					'ID_Entidad'			=> $this->security->xss_clean($row['ID_Entidad'])
				);
			}
			$this->db->insert_batch($this->table_carga_consolidada_pedido_detalle, $detalle);

			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarPedido($ID){
        $query ="SELECT 1 AS existe FROM carga_consolidada_seguimiento WHERE ID_Pedido_Cabecera = " . $ID . " LIMIT 1";
        $objRegistro = $this->db->query($query)->row();
		if(is_object($objRegistro)){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Tiene seguimiento(s)');
		} else {
			$this->db->where('ID_Pedido_Cabecera', $ID);
            $this->db->delete($this->table);
            
		    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
	
	public function cambiarEstado($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Tipo_Canal' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }
	
	public function sendMessage($arrPost){
        $data = array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrPost['enviar_mensaje-id_pedido_cabecera'],
			'No_Seguimiento' => $arrPost['enviar_mensaje-No_Seguimiento']
		);
		if ($this->db->insert($this->table_carga_consolidada_seguimiento, $data) > 0) {
			return array('status' => 'success', 'message' => 'Se envío seguimiento');
		}
		return array('status' => 'error', 'message' => 'Error al enviar seguimiento');
    }
	
	public function obtenerCantidadMensaje($ID_Pedido_Cabecera){
		$query = "SELECT 1 FROM " . $this->table_carga_consolidada_seguimiento . " WHERE ID_Pedido_Cabecera = " . $ID_Pedido_Cabecera;
		$arrData = $this->db->query($query)->result();
		$iCantidadMensaje = 0;
		foreach ($arrData as $row) {
			++$iCantidadMensaje;
		}
		return $iCantidadMensaje;
    }
	
	public function obtenerEntidad($arrPost){
		$query = "SELECT
DET.ID_Entidad AS id,
CLI.No_Entidad AS nombre,
CLI.Txt_Email_Entidad AS correo
FROM
" . $this->table_carga_consolidada_pedido_detalle . " AS DET
JOIN entidad AS CLI ON(CLI.ID_Entidad = DET.ID_Entidad)
WHERE
DET.ID_Pedido_Cabecera = " . $arrPost['enviar_mensaje-id_pedido_cabecera'];
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos entidad',
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
