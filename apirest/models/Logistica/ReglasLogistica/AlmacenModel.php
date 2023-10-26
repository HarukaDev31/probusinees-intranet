<?php
class AlmacenModel extends CI_Model{
	var $table = 'almacen';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_tabla_dato = 'tabla_dato';
	var $table_documento_cabecera = 'documento_cabecera';
	var $table_departamento = 'departamento';
	var $table_provincia = 'provincia';
    var $table_distrito = 'distrito';
	
    var $column_order = array('No_Empresa', 'No_Organizacion', 'No_Almacen', 'No_Departamento', 'No_Provincia', 'No_Distrito', 'Txt_Direccion_Almacen');
    var $column_search = array('');
    var $order = array('almacen.Fe_Registro' => 'desc');
	
    private $upload_path = '../assets/images/logos_empresa_almacen/';
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if ( $this->user->No_Usuario == 'root' ){
            $this->column_order = array('No_Empresa', 'No_Organizacion', 'No_Almacen', 'No_Departamento', 'No_Provincia', 'No_Distrito', 'Txt_Direccion_Almacen', '', 'Nu_Estado_Pago_Sistema_Laeshop', 'Fe_Vencimiento_Laeshop');
        }

		//$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ORG.Nu_Estado_Sistema, ID_Almacen, No_Almacen, Txt_Direccion_Almacen, No_Logo_Almacen, No_Logo_Url_Almacen, Nu_Estado_Pago_Sistema, TIPOPROVEEDORFE.No_Class AS No_Class_Proveedor_FE, TIPOPROVEEDORFE.No_Descripcion AS No_Descripcion_Proveedor_FE, No_Departamento, No_Provincia, No_Distrito, ' . $this->table . '.Nu_Estado, Nu_Estado_Pago_Sistema_Laeshop, Fe_Vencimiento_Laeshop, Fe_Vencimiento_LaeGestion')
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ORG.Nu_Estado_Sistema, ID_Almacen, No_Almacen, Txt_Direccion_Almacen, No_Logo_Almacen, No_Logo_Url_Almacen, Nu_Estado_Pago_Sistema, No_Departamento, No_Provincia, No_Distrito, ' . $this->table . '.Nu_Estado, Fe_Vencimiento_LaeGestion, Nu_Estado_Pago_Sistema_Laeshop, Fe_Vencimiento_Laeshop')
		->from($this->table)
        ->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ORG.ID_Empresa', 'join')
    	->join($this->table_distrito, $this->table_distrito . '.ID_Distrito = ' . $this->table . '.ID_Distrito', 'join')
    	->join($this->table_provincia, $this->table_provincia . '.ID_Provincia = ' . $this->table . '.ID_Provincia', 'join')
    	->join($this->table_departamento, $this->table_departamento . '.ID_Departamento = ' . $this->table_provincia . '.ID_Departamento', 'join');
        //->join($this->table_tabla_dato . ' AS TIPOPROVEEDORFE', 'TIPOPROVEEDORFE.Nu_Valor=EMP.Nu_Tipo_Proveedor_FE AND TIPOPROVEEDORFE.No_Relacion = "Tipos_Proveedor_FE"', 'join');
         
		if ( $this->user->No_Usuario == 'root' ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
            
            if( $this->input->post('filtro_organizacion') )
                $this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
                
            if($this->input->post('filtro_estado_laegestion') != '-')
                $this->db->where('EMP.Nu_Lae_Gestion', $this->input->post('filtro_estado_laegestion'));

            if($this->input->post('filtro_estado_laeshop') != '-')
                $this->db->where('EMP.Nu_Lae_Shop', $this->input->post('filtro_estado_laeshop'));

            if($this->input->post('filtro_estado_sistema') != '-')
                $this->db->where('ORG.Nu_Estado_Sistema', $this->input->post('filtro_estado_sistema'));
                    
            if(!empty($this->input->post('filtro_tipo_sistema')))
                $this->db->where('EMP.Nu_Tipo_Proveedor_FE', $this->input->post('filtro_tipo_sistema'));

            if($this->input->post('filtro_estado_pago') != '-')
                $this->db->where( $this->table . '.Nu_Estado_Pago_Sistema', $this->input->post('filtro_estado_pago'));

            if($this->input->post('filtro_estado_pago_laeshop') != '-')
                $this->db->where( $this->table . '.Nu_Estado_Pago_Sistema_Laeshop', $this->input->post('filtro_estado_pago_laeshop'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
            $this->db->where('ORG.ID_Organizacion', $this->empresa->ID_Organizacion);
        }

        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Almacenes') == 'Almacen' )
            $this->db->like('No_Almacen', $this->input->post('Global_Filter'));

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
        if ( $this->user->No_Usuario == 'root' ){
            $this->column_order = array('Nu_Estado_Sistema','Nu_Tipo_Proveedor_FE', 'No_Empresa', 'No_Organizacion', 'No_Almacen', 'No_Departamento', 'No_Provincia', 'No_Distrito', 'Txt_Direccion_Almacen', 'Nu_Estado_Pago_Sistema');
        }

		//$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ORG.Nu_Estado_Sistema, ID_Almacen, No_Almacen, Txt_Direccion_Almacen, No_Logo_Almacen, No_Logo_Url_Almacen, Nu_Estado_Pago_Sistema, TIPOPROVEEDORFE.No_Class AS No_Class_Proveedor_FE, TIPOPROVEEDORFE.No_Descripcion AS No_Descripcion_Proveedor_FE, No_Departamento, No_Provincia, No_Distrito, ' . $this->table . '.Nu_Estado, Nu_Estado_Pago_Sistema_Laeshop, Fe_Vencimiento_Laeshop')
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ORG.Nu_Estado_Sistema, ID_Almacen, No_Almacen, Txt_Direccion_Almacen, No_Logo_Almacen, No_Logo_Url_Almacen, Nu_Estado_Pago_Sistema, No_Departamento, No_Provincia, No_Distrito, ' . $this->table . '.Nu_Estado, Fe_Vencimiento_LaeGestion, Nu_Estado_Pago_Sistema_Laeshop, Fe_Vencimiento_Laeshop')
		->from($this->table)
        ->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ORG.ID_Empresa', 'join')
    	->join($this->table_distrito, $this->table_distrito . '.ID_Distrito = ' . $this->table . '.ID_Distrito', 'join')
    	->join($this->table_provincia, $this->table_provincia . '.ID_Provincia = ' . $this->table . '.ID_Provincia', 'join')
    	->join($this->table_departamento, $this->table_departamento . '.ID_Departamento = ' . $this->table_provincia . '.ID_Departamento', 'join');
        
		if ( $this->user->No_Usuario == 'root' ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
            
            if( $this->input->post('filtro_organizacion') )
                $this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
                
            if($this->input->post('filtro_estado_laegestion') != '-')
                $this->db->where('EMP.Nu_Lae_Gestion', $this->input->post('filtro_estado_laegestion'));

            if($this->input->post('filtro_estado_laeshop') != '-')
                $this->db->where('EMP.Nu_Lae_Shop', $this->input->post('filtro_estado_laeshop'));

            if($this->input->post('filtro_estado_sistema') != '-')
                $this->db->where('ORG.Nu_Estado_Sistema', $this->input->post('filtro_estado_sistema'));
                    
            if(!empty($this->input->post('filtro_tipo_sistema')))
                $this->db->where('EMP.Nu_Tipo_Proveedor_FE', $this->input->post('filtro_tipo_sistema'));

            if($this->input->post('filtro_estado_pago') != '-')
                $this->db->where( $this->table . '.Nu_Estado_Pago_Sistema', $this->input->post('filtro_estado_pago'));

            if($this->input->post('filtro_estado_pago_laeshop') != '-')
                $this->db->where( $this->table . '.Nu_Estado_Pago_Sistema_Laeshop', $this->input->post('filtro_estado_pago_laeshop'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
            $this->db->where('ORG.ID_Organizacion', $this->empresa->ID_Organizacion);
        }

        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Almacenes') == 'Almacen' )
            $this->db->like('No_Almacen', $this->input->post('Global_Filter'));

        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->select('EMP.ID_Empresa, EMP.Nu_Tipo_Proveedor_FE, EMP.ID_Empresa_Marketplace, ORG.ID_Organizacion, ' . $this->table . '.*' );
        $this->db->from($this->table);
        $this->db->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join');
        $this->db->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ORG.ID_Empresa', 'join');
        $this->db->where('ID_Almacen', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarAlmacen($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Almacen='" . $data['No_Almacen'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarAlmacen($where, $data, $ENo_Almacen){
		if( $ENo_Almacen != $data['No_Almacen'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Almacen='" . $data['No_Almacen'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarAlmacen($ID){
	    if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Almacen = " . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El almacén tiene movimiento(s)');
		}else{
			$this->db->where('ID_Almacen', $ID);
            $this->db->delete($this->table);
            if ( $this->db->affected_rows() > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}

	public function cambiarEstadoPago($ID, $Nu_Estado){
        $iTipoPlanLaeshop = $this->db->query("SELECT E.Nu_Tipo_Plan_Lae_Gestion
FROM empresa AS E
JOIN organizacion AS O ON(O.ID_Empresa = E.ID_Empresa)
JOIN almacen AS A ON(A.ID_Organizacion = O.ID_Organizacion)
WHERE
A.ID_Almacen=" . $ID . " LIMIT 1")->row()->Nu_Tipo_Plan_Lae_Gestion;

        if ($iTipoPlanLaeshop>0) {
            $where = array('ID_Almacen' => $ID);
            $arrData = array( 'Nu_Estado_Pago_Sistema' => $Nu_Estado );

            $iDiasAgregar = 0;
            if($Nu_Estado==1){//cancelado
                if ($iTipoPlanLaeshop==1)//mensual
                    $iDiasAgregar = 30;
                if ($iTipoPlanLaeshop==2)//Trimestral
                    $iDiasAgregar = 90;
                if ($iTipoPlanLaeshop==3)//Anual
                    $iDiasAgregar = 365;
                $this->db->query("UPDATE almacen SET Fe_Vencimiento_LaeGestion=ADDDATE(Fe_Vencimiento_LaeGestion, INTERVAL " . $iDiasAgregar . " DAY) WHERE ID_Almacen=" . $ID);
            }
            
    		if ($this->db->update('almacen', $arrData, $where) > 0)
    			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
    		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
        } else {
            return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Primero debe de configurar plan en Configuracion > Monitoreo de Empresas');
        }
	}

	public function cambiarEstadoPagoLaeshop($ID, $Nu_Estado){
        $iTipoPlanLaeshop = $this->db->query("SELECT E.Nu_Tipo_Plan_Lae_Shop
FROM empresa AS E
JOIN organizacion AS O ON(O.ID_Empresa = E.ID_Empresa)
JOIN almacen AS A ON(A.ID_Organizacion = O.ID_Organizacion)
WHERE
A.ID_Almacen=" . $ID . " LIMIT 1")->row()->Nu_Tipo_Plan_Lae_Shop;

        if ($iTipoPlanLaeshop>0) {
            $where = array('ID_Almacen' => $ID);
            $arrData = array('Nu_Estado_Pago_Sistema_Laeshop' => $Nu_Estado);

            $iDiasAgregar = 0;
            if($Nu_Estado==1){//cancelado
                if ($iTipoPlanLaeshop==1)//mensual
                    $iDiasAgregar = 30;
                if ($iTipoPlanLaeshop==2)//Trimestral
                    $iDiasAgregar = 90;
                if ($iTipoPlanLaeshop==3)//Anual
                    $iDiasAgregar = 365;
			    $this->db->query("UPDATE almacen SET Fe_Vencimiento_Laeshop=ADDDATE(Fe_Vencimiento_Laeshop, INTERVAL " . $iDiasAgregar . " DAY) WHERE ID_Almacen=" . $ID);
            }
            
            if ($this->db->update('almacen', $arrData, $where) > 0)
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
        } else {
            return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Primero debe de configurar plan en Configuracion > Monitoreo de Empresas');
        }
	}
}
