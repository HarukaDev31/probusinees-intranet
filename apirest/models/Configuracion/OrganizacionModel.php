<?php
class OrganizacionModel extends CI_Model{
	var $table = 'organizacion';
	var $table_empresa = 'empresa';
	var $table_tabla_dato = 'tabla_dato';
	var $table_documento_cabecera = 'documento_cabecera';
	
    var $column_order = array('No_Organizacion', 'Txt_Organizacion');
    var $column_search = array('No_Organizacion');
    var $order = array('No_Organizacion' => 'asc');
	
    private $upload_path = '../librerias/apirest/libraries/sunat_facturador/certificado_digital/';
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Organizaciones') == 'Empresa' )
            $this->db->like('No_Empresa', $this->input->post('Global_Filter'));

        if( $this->input->post('Filtros_Organizaciones') == 'Organizacion' )
            $this->db->like('No_Organizacion', $this->input->post('Global_Filter'));
        
		if ( $this->user->No_Usuario != 'root' ){
            $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Organizacion, No_Organizacion, Txt_Organizacion, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado, TDESTADOSISTEMA.No_Class AS No_Class_Estado_Sistema, TDESTADOSISTEMA.No_Descripcion AS No_Descripcion_Estado_Sistema, Nu_Estado_Sistema')
            ->from($this->table)
            ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
            ->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor=' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join')
            ->join($this->table_tabla_dato . ' AS TDESTADOSISTEMA', 'TDESTADOSISTEMA.Nu_Valor=' . $this->table . '.Nu_Estado_Sistema AND TDESTADOSISTEMA.No_Relacion = "Tipos_EstadoSistema"', 'join')
            ->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        } else {
            $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Organizacion, No_Organizacion, Txt_Organizacion, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado, TDESTADOSISTEMA.No_Class AS No_Class_Estado_Sistema, TDESTADOSISTEMA.No_Descripcion AS No_Descripcion_Estado_Sistema, Nu_Estado_Sistema')
            ->from($this->table)
            ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
            ->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor=' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join')
            ->join($this->table_tabla_dato . ' AS TDESTADOSISTEMA', 'TDESTADOSISTEMA.Nu_Valor=' . $this->table . '.Nu_Estado_Sistema AND TDESTADOSISTEMA.No_Relacion = "Tipos_EstadoSistema"', 'join');
        }

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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Organizacion', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarOrganizacion($data){
		if($this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Organizacion='" . $data['No_Organizacion'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarOrganizacion($where, $data, $ENo_Organizacion){
		if( $ENo_Organizacion != $data['No_Organizacion'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Organizacion='" . $data['No_Organizacion'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarOrganizacion($iIdEmpresa, $ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM documento_cabecera WHERE ID_Organizacion=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('sStatus' => 'warning', 'sMessage' => 'La organizacion tiene movimiento(s)');
		} else {
            $this->db->where('ID_Empresa', $iIdEmpresa);
            $this->db->delete('menu_acceso');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('grupo_usuario');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('pos');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('usuario');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('grupo');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('entidad');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('tipo_operacion_caja');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('serie_documento');

            $this->db->where('ID_Organizacion', $ID);
            $this->db->delete('almacen');

            $arrPost = array(
                'iEstadoSistema' => 0,
                'iIdEmpresa' => $iIdEmpresa,
                'iIdOrganizacion' => $ID,
            );
            $arrResponse = $this->limpiarData( $arrPost );
            if ( $arrResponse['sStatus'] == 'success' ) {
                $this->db->where('ID_Organizacion', $ID);
                $this->db->delete($this->table);
                if ( $this->db->affected_rows() > 0 )
                    return array('sStatus' => 'success', 'sMessage' => 'Registro eliminado');
            } else {
                return $arrResponse;
            }
		}
        return array('sStatus' => 'danger', 'sMessage' => 'Error al eliminar');
	}
    
	public function limpiarData($arrPost){
        /*
        if ( $arrPost['iEstadoSistema'] == 1 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'El sistema se encuentra en producción, no se limpiará la información');
        } else if ( $this->db->query("SELECT E.ID_Empresa FROM empresa AS E JOIN organizacion AS O ON(E.ID_Empresa = O.ID_Empresa) WHERE O.ID_Organizacion = " . $arrPost['iIdOrganizacion'] . " LIMIT 1;")->row()->ID_Empresa==27 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'Esta EMPRESA ES DE PRUEBAS no puedes ELIMINAR DATA');
        } else {
        */
            $this->db->trans_begin();
            
            $Nu_Documento_Identidad = $this->db->query("SELECT E.Nu_Documento_Identidad FROM empresa AS E JOIN organizacion AS O ON(E.ID_Empresa = O.ID_Empresa) WHERE O.ID_Organizacion = " . $arrPost['iIdOrganizacion'] . " LIMIT 1;")->row()->Nu_Documento_Identidad;
            
            /*
            $ruta = $this->upload_path . 'BETA/' . $Nu_Documento_Identidad . '/*';
            $files = glob($ruta);
            foreach($files as $file){
                if(is_file($file))
                    unlink($file);
            }
            */

			$sql = "DELETE FROM pedido_detalle WHERE ID_Pedido_Cabecera IN(SELECT ID_Pedido_Cabecera FROM pedido_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'pedido_cabecera' );

			$sql = "DELETE FROM documento_medio_pago WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
			$sql = "DELETE FROM documento_detalle_lote WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

			$sql = "DELETE FROM documento_detalle WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

			$sql = "DELETE FROM documento_enlace WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
			$sql = "DELETE FROM documento_detalle_estado_lavado WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
			$sql = "DELETE FROM orden_seguimiento WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'stock_producto' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'movimiento_inventario' );
                        
			$sql = "DELETE FROM guia_detalle WHERE ID_Guia_Cabecera IN(SELECT ID_Guia_Cabecera FROM guia_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

			$sql = "DELETE FROM guia_enlace WHERE ID_Guia_Cabecera IN(SELECT ID_Guia_Cabecera FROM guia_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'guia_cabecera' );

            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'caja_pos' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'documento_cabecera' );
            
            $data = array( 'Nu_Numero_Documento' => 1 );
            $where = array('ID_Empresa' => $arrPost['iIdEmpresa'], 'ID_Organizacion' => $arrPost['iIdOrganizacion']);
            $this->db->update( 'serie_documento' , $data, $where);

            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->delete( 'correlativo_tipo_asiento' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->delete( 'correlativo_tipo_asiento_pendiente' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'matricula_empleado' );

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al limpiar información de prueba');
			} else {
				$this->db->trans_commit();
                return array('sStatus' => 'success', 'sMessage' => 'Se eliminó información de prueba');
            }
        //}
	}

    public function activarSistema($arrPost){
        if ( $arrPost['iEstadoSistema'] == 1 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'El sistema se encuentra en producción, no se limpiará la información');
        } else if ( $this->db->query("SELECT E.ID_Empresa FROM empresa AS E JOIN organizacion AS O ON(E.ID_Empresa = O.ID_Empresa) WHERE O.ID_Organizacion = " . $arrPost['iIdOrganizacion'] . " LIMIT 1;")->row()->ID_Empresa==27 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'Esta EMPRESA ES DE PRUEBAS no se puede pasar a PRODUCCIÓN');
        } else {
            $this->db->trans_begin();
            
            $Nu_Documento_Identidad = $this->db->query("SELECT E.Nu_Documento_Identidad FROM empresa AS E JOIN organizacion AS O ON(E.ID_Empresa = O.ID_Empresa) WHERE O.ID_Organizacion = " . $arrPost['iIdOrganizacion'] . " LIMIT 1;")->row()->Nu_Documento_Identidad;
            $ruta = $this->upload_path . 'BETA/' . $Nu_Documento_Identidad . '/*';
            $files = glob($ruta);
            foreach($files as $file){
                if(is_file($file))
                    unlink($file);
            }
            
			$sql = "DELETE FROM pedido_detalle WHERE ID_Pedido_Cabecera IN(SELECT ID_Pedido_Cabecera FROM pedido_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'pedido_cabecera' );
            
			$sql = "DELETE FROM documento_medio_pago WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
			$sql = "DELETE FROM documento_detalle_lote WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
			$sql = "DELETE FROM documento_detalle WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

			$sql = "DELETE FROM documento_enlace WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
			$sql = "DELETE FROM documento_detalle_estado_lavado WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
			$sql = "DELETE FROM orden_seguimiento WHERE ID_Documento_Cabecera IN(SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'stock_producto' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'movimiento_inventario' );
            
			$sql = "DELETE FROM guia_detalle WHERE ID_Guia_Cabecera IN(SELECT ID_Guia_Cabecera FROM guia_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);

			$sql = "DELETE FROM guia_enlace WHERE ID_Guia_Cabecera IN(SELECT ID_Guia_Cabecera FROM guia_cabecera WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND ID_Organizacion = " . $arrPost['iIdOrganizacion'] . ")";
            $this->db->query($sql);
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'guia_cabecera' );

            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'caja_pos' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'documento_cabecera' );
            
            $data = array( 'Nu_Numero_Documento' => 1 );
            $where = array('ID_Empresa' => $arrPost['iIdEmpresa'], 'ID_Organizacion' => $arrPost['iIdOrganizacion']);
            $this->db->update( 'serie_documento' , $data, $where);

            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->delete( 'correlativo_tipo_asiento' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->delete( 'correlativo_tipo_asiento_pendiente' );
            
            $this->db->where('ID_Empresa', $arrPost['iIdEmpresa']);
            $this->db->where('ID_Organizacion', $arrPost['iIdOrganizacion']);
            $this->db->delete( 'matricula_empleado' );

            $data = array( 'Nu_Estado_Sistema' => 1 );
            $where = array('ID_Empresa' => $arrPost['iIdEmpresa'], 'ID_Organizacion' => $arrPost['iIdOrganizacion']);
            $this->db->update($this->table, $data, $where);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al activar sistema a modo producción');
			} else {
				$this->db->trans_commit();
                return array('sStatus' => 'success', 'sMessage' => 'Modo de producción activado');
            }
        }
    }


    public function activarSistemaSinBorrar($arrPost){
        if ( $arrPost['iEstadoSistema'] == 1 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'El sistema se encuentra en producción, no se limpiará la información');
        } else if ( $this->db->query("SELECT E.ID_Empresa FROM empresa AS E JOIN organizacion AS O ON(E.ID_Empresa = O.ID_Empresa) WHERE O.ID_Organizacion = " . $arrPost['iIdOrganizacion'] . " LIMIT 1;")->row()->ID_Empresa==27 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'Esta EMPRESA ES DE PRUEBAS no se puede pasar a PRODUCCIÓN');
        } else {
            $this->db->trans_begin();
            $data = array( 'Nu_Estado_Sistema' => 1 );
            $where = array('ID_Empresa' => $arrPost['iIdEmpresa'], 'ID_Organizacion' => $arrPost['iIdOrganizacion']);
            $this->db->update($this->table, $data, $where);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('sStatus' => 'danger', 'sMessage' => 'Problemas al activar sistema a modo producción');
            } else {
                $this->db->trans_commit();
                return array('sStatus' => 'success', 'sMessage' => 'Se activo sistema a PRODUCCIÓN');
            }
        }
    }
}
