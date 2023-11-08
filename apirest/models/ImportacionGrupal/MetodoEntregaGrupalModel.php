<?php
class MetodoEntregaGrupalModel extends CI_Model{
	var $table = 'metodo_entrega_tienda_virtual';
    var $table_empresa = 'empresa';
    var $table_distrito = 'distrito_tienda_virtual';
	var $table_pais         = 'pais';
	var $table_departamento = 'departamento';
	var $table_provincia    = 'provincia';
	var $table_entidad      = 'entidad';
	
    var $column_order = array('No_Metodo_Entrega_Tienda_Virtual','Nu_Estado');
    var $column_search = array('');
    var $order = array('');
	
    var $column_order_distrito = array('No_Departamento', 'No_Provincia', 'No_Distrito', 'Nu_Habilitar_Ecommerce');
    var $order_distrito = array('Nu_Habilitar_Ecommerce' => 'desc', 'No_Distrito' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select('ID_Metodo_Entrega_Tienda_Virtual, No_Metodo_Entrega_Tienda_Virtual, Nu_Estado')
        ->from($this->table)
		->where('ID_Empresa', $this->user->ID_Empresa);
         
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
        $this->db->where('ID_Metodo_Entrega_Tienda_Virtual', $ID);
        $query = $this->db->get();
        return $query->row();
    }
        
    public function actualizarMetodoEntrega($where, $data, $ENo_Metodo_Entrega_Tienda_Virtual, $Nu_Tipo, $data_recojo_tienda){
        if ( $this->db->update($this->table, $data, $where) > 0 ) {
            if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Estado=1 LIMIT 1")->row()->existe == 0){
                $data = array('Nu_Estado' => '1');
                $this->db->update($this->table, $data, $where);
                return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Debes tener al menos 1 método de entrega');
            }
            
            //Modificar datos de la tabla almacen - solo si el tipo = 7 que es Recojo en tienda
            if($Nu_Tipo==7){
                $where_almacen = array('ID_Almacen' => $data_recojo_tienda['ID_Almacen']);
                unset($data_recojo_tienda['ID_Almacen']);
                $this->db->update('almacen', $data_recojo_tienda, $where_almacen);
            }

			/* TOUR TIENDA VIRTUAL */
			$where_tour = array('ID_Empresa' => $this->empresa->ID_Empresa, 'Nu_ID_Interno' => 4);
			//validamos que si complete los siguientes datos
			if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE Nu_Estado=1 LIMIT 1")->row()->cantidad > 0){
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 1);
			} else {
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 0);
			}
			$this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
			/* END TOUR TIENDA VIRTUAL */

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    //DISRITO DELIVERY
	public function _get_datatables_query_distrito(){
        if( !empty($this->input->post('Global_Filter')) &&  $this->input->post('Filtros_Distritos') == 'Departamento' ){
            $this->db->like('No_Departamento', $this->input->post('Global_Filter'));
        }
        
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Distritos') == 'Provincia' ){
            $this->db->like('No_Provincia', $this->input->post('Global_Filter'));
        }
        
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Distritos') == 'Distrito' ){
            $this->db->like('No_Distrito', $this->input->post('Global_Filter'));
        }
        
		$this->db->select('ID_Distrito, No_Departamento, No_Provincia, No_Distrito, Ss_Delivery, Nu_Habilitar_Ecommerce AS Nu_Estado')
		->from($this->table_distrito)
    	->join($this->table_provincia, $this->table_provincia . '.ID_Provincia = ' . $this->table_distrito . '.ID_Provincia', 'join')
    	->join($this->table_departamento, $this->table_departamento . '.ID_Departamento = ' . $this->table_provincia . '.ID_Departamento', 'join')
		->where($this->table_distrito . '.ID_Empresa', $this->user->ID_Empresa);
         
        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order_distrito[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order_distrito)) {
            $order = $this->order_distrito;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	function get_datatables_distrito(){
        $this->_get_datatables_query_distrito();
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id_distrito($ID){
        $this->db->from($this->table_distrito);
    	$this->db->join($this->table_provincia, $this->table_provincia . '.ID_Provincia = ' . $this->table_distrito . '.ID_Provincia', 'join');
    	$this->db->join($this->table_departamento, $this->table_departamento . '.ID_Departamento = ' . $this->table_provincia . '.ID_Departamento', 'join');
    	$this->db->join($this->table_pais, $this->table_pais . '.ID_Pais = ' . $this->table_departamento . '.ID_Pais', 'join');
        $this->db->where('ID_Distrito', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function actualizarDistrito($where, $data){
        if ( $this->db->update($this->table_distrito, $data, $where) > 0 ) {
			/* TOUR TIENDA VIRTUAL */
			$where_tour = array('ID_Empresa' => $this->empresa->ID_Empresa, 'Nu_ID_Interno' => 4);
			//validamos que si complete los siguientes datos
			if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE Nu_Estado=1 LIMIT 1")->row()->cantidad > 0){
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 1);
			} else {
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 0);
			}
			$this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
			/* END TOUR TIENDA VIRTUAL */

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
    public function updPrecioEstandarDelivery($arrPost){
        $where = array('ID_Empresa' => $arrPost['ID_Empresa']);
        $data = array(
            'Ss_Delivery' => $arrPost['Ss_Precio'],
            'Nu_Habilitar_Ecommerce' => 1,
        );
        if ( $this->db->update($this->table_distrito, $data, $where) > 0 ) {
			/* TOUR TIENDA VIRTUAL */
			$where_tour = array('ID_Empresa' => $this->empresa->ID_Empresa, 'Nu_ID_Interno' => 4);
			//validamos que si complete los siguientes datos
			if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE Nu_Estado=1 LIMIT 1")->row()->cantidad > 0){
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 1);
			} else {
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 0);
			}
			$this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
			/* END TOUR TIENDA VIRTUAL */

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Precio y destinos actualizados');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function updatePromoDelivery($arrPost){
        $where = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'Nu_Tipo_Metodo_Entrega_Tienda_Virtual' => 6
        );
        $data = array('ID_Estatus_Promo' => $arrPost['ID_Estatus_Promo']);
        if($arrPost['ID_Estatus_Promo']==1) {
            $data['Nu_Monto_Compra'] = $arrPost['Nu_Monto_Compra'];
			$data['Nu_Costo_Envio'] = $arrPost['Nu_Costo_Envio'];
			$data['Txt_Terminos'] = $arrPost['Txt_Terminos'];
        }
        if ( $this->db->update($this->table, $data, $where) > 0 ) {
			/* TOUR TIENDA VIRTUAL */
			$where_tour = array('ID_Empresa' => $this->empresa->ID_Empresa, 'Nu_ID_Interno' => 4);
			//validamos que si complete los siguientes datos
			if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE Nu_Estado=1 LIMIT 1")->row()->cantidad > 0){
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 1);
			} else {
				//Cambiar estado a completado para el tour
				$data_tour = array('Nu_Estado_Proceso' => 0);
			}
			$this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
			/* END TOUR TIENDA VIRTUAL */
            
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Promoción de envío actualizada');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function get_promoDelivery() {
        $this->db->select('ID_Estatus_Promo, Nu_Monto_Compra, Nu_Costo_Envio, Txt_Terminos')
        ->from($this->table)
        ->where('ID_Empresa', $this->user->ID_Empresa)
        ->where('Nu_Tipo_Metodo_Entrega_Tienda_Virtual', 6);
        $query = $this->db->get();
        $data = $query->row();
        if ( $data != null )
            return array('status' => 'success', 'data' => $data);
        return array('status' => 'error', 'message' => 'Error al consultar');
    }

	public function cambiarEstadoTienda($ID, $Nu_Estado){
        $where = array('ID_Distrito' => $ID);
        $arrData = array( 'Nu_Habilitar_Ecommerce' => $Nu_Estado );
		if ($this->db->update('distrito_tienda_virtual', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado satisfactoriamente');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
}
