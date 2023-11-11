<?php
class SistemaModel extends CI_Model{
	var $table                          = 'configuracion';
	var $table_empresa                  = 'empresa';
	var $table_organizacion             = 'organizacion';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	
    var $column_order = array('No_Empresa', 'Ss_Total_Pago_Cliente_Servicio', 'Fe_Inicio_Sistema', '', '', 'No_Dominio_Empresa', 'Nu_Celular_Empresa', 'Txt_Email_Empresa');
    var $column_search = array('');
    var $order = array('No_Empresa' => 'asc', 'No_Dominio_Empresa' => 'asc');
    
	private $upload_path = '../assets/images/logos';
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if ( $this->user->No_Usuario != 'root' ){
            $this->column_order = array('', '', 'No_Dominio_Empresa', 'Nu_Celular_Empresa', 'Txt_Email_Empresa');
        }

        if($this->input->post('Filtro_Tipo_Sistema') != '0')
            $this->db->where('EMP.Nu_Tipo_Proveedor_FE', $this->input->post('Filtro_Tipo_Sistema'));

        if( $this->input->post('Filtro_Estado') != '' )
            $this->db->where('EMP.Nu_Estado', $this->input->post('Filtro_Estado'));

        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Configuracion, No_Logo_Empresa, No_Dominio_Empresa, Nu_Celular_Empresa, Txt_Email_Empresa, Fe_Inicio_Sistema, No_Imagen_Logo_Empresa, Nu_Version_Imagen, Nu_Validar_Stock, Ss_Total_Pago_Cliente_Servicio, ' . $this->table . '.Nu_Estado')
        ->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
        
		if ( $this->user->No_Usuario == 'root' ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        }

        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Sistemas') == 'Sistema' )
            $this->db->like('No_Dominio_Empresa', $this->input->post('Global_Filter'));

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
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
        if($this->input->post('Filtro_Tipo_Sistema') != '0')
            $this->db->where('EMP.Nu_Tipo_Proveedor_FE', $this->input->post('Filtro_Tipo_Sistema'));

        if( $this->input->post('Filtro_Estado') != '' )
            $this->db->where('EMP.Nu_Estado', $this->input->post('Filtro_Estado'));

        $this->db->from($this->table);
        $this->db->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
        $this->db->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
        $this->db->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');
        
		if ( $this->user->No_Usuario == 'root' ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
            
            if( $this->input->post('filtro_organizacion') )
                $this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        }

        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Sistemas') == 'Sistema' )
            $this->db->like('No_Dominio_Empresa', $this->input->post('Global_Filter'));

        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Configuracion',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarSistema($data, $arrFiles){
		if($this->db->query("SELECT COUNT(*) AS existe FROM configuracion WHERE ID_Empresa = " . $data['ID_Empresa'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La configuracion se encuentra enlazada a una empresa');
		} else {
            // Formatos de documentos
            $config = array(
                'upload_path'   => $this->upload_path,
                'allowed_types' => 'jpg|jpeg|png',
                'max_size'      => 1024,//1024 KB = 1 MB
                'overwrite'     => true
            );
            $this->load->library('upload', $config);
            
            if ( $arrFiles['No_Foto_Boleta']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_Boleta']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_Boleta']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_Boleta']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_Boleta']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            } else if ( $arrFiles['No_Foto_Factura']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_Factura']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_Factura']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_Factura']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_Factura']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            } else if ( $arrFiles['No_Foto_NCredito']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_NCredito']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_NCredito']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_NCredito']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_NCredito']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            } else if ( $arrFiles['No_Foto_Guia']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_Guia']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_Guia']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_Guia']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_Guia']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            }
            // ./ Formatos de documentos

            $query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $data['ID_Empresa'] . " AND Nu_Tipo_Entidad = 0 AND No_Entidad LIKE '%clientes varios%' LIMIT 1"; //1 = ID_Entidad -> Cliente varios
            if ( !$this->db->simple_query($query) ){
                $this->db->trans_rollback();
                $error = $this->db->error();
                return array(
                    'status' => 'error',
                    'style_modal' => 'modal-danger',
                    'message' => 'Error en BD clientes varios',
                );
            }
            $arrResponseSQL = $this->db->query($query);
            if ( $arrResponseSQL->num_rows() > 0 ){
                $arrData = $arrResponseSQL->result();
                $ID_Entidad = $arrData[0]->ID_Entidad;
            } else {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'style_modal' => 'modal-warning',
                    'message' => 'No se encontro clientes varios',
                );
            }
            $data = array_merge($data, array('ID_Entidad_Clientes_Varios_Venta_Predeterminado' => $ID_Entidad));

            if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
            else
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
        }
    }
    
    public function actualizarSistema($where, $data, $ENo_Dominio_Empresa, $arrFiles){
		if( $where['ID_Empresa'] != $data['ID_Empresa'] && $this->db->query("SELECT COUNT(*) AS existe FROM configuracion WHERE ID_Empresa = " . $data['ID_Empresa'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La configuracion se encuentra enlazada a una empresa');
		} else {
            // Formatos de documentos
            $config = array(
                'upload_path'   => $this->upload_path,
                'allowed_types' => 'jpg|jpeg|png',
                'max_size'      => 1024,//1024 KB = 1 MB
                'overwrite'     => true
            );
            $this->load->library('upload', $config);
            
            if ( $arrFiles['No_Foto_Boleta']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_Boleta']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_Boleta']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_Boleta']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_Boleta']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            } else if ( $arrFiles['No_Foto_Factura']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_Factura']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_Factura']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_Factura']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_Factura']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            } else if ( $arrFiles['No_Foto_NCredito']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_NCredito']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_NCredito']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_NCredito']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_NCredito']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            } else if ( $arrFiles['No_Foto_Guia']['name'] != '' ) {
                $this->reemplazarImagen($arrFiles['No_Foto_Guia']['name']);
                $_FILES['No_Formatos_Documentos']['name']		= $arrFiles['No_Foto_Guia']['name'];
                $_FILES['No_Formatos_Documentos']['tmp_name']	= $arrFiles['No_Foto_Guia']['tmp_name'];
                $_FILES['No_Formatos_Documentos']['size']		= $arrFiles['No_Foto_Guia']['size'];
                
                $this->upload->initialize($config);
        
                if($this->upload->do_upload('No_Formatos_Documentos'))
                    $fileData = $this->upload->data();
            }
            // ./ Formatos de documentos
            
            /*
            $query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $data['ID_Empresa'] . " AND Nu_Tipo_Entidad = 0 AND No_Entidad LIKE '%clientes varios%' LIMIT 1"; //1 = ID_Entidad -> Cliente varios
            if ( !$this->db->simple_query($query) ){
                $this->db->trans_rollback();
                $error = $this->db->error();
                return array(
                    'status' => 'error',
                    'style_modal' => 'modal-danger',
                    'message' => 'Error en BD clientes varios',
                );
            }
            $arrResponseSQL = $this->db->query($query);
            if ( $arrResponseSQL->num_rows() > 0 ){
                $arrData = $arrResponseSQL->result();
                $ID_Entidad = $arrData[0]->ID_Entidad;
            } else {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'style_modal' => 'modal-warning',
                    'message' => 'No se encontro clientes varios',
                );
            }
            $data = array_merge($data, array('ID_Entidad_Clientes_Varios_Venta_Predeterminado' => $ID_Entidad));
            */

            if ( $this->db->update($this->table, $data, $where) > 0 ) {
                /* TOUR GESTION */
                $where_tour = array('ID_Empresa' => $where['ID_Empresa'], 'Nu_ID_Interno' => 3);
                //validamos que si complete los siguientes datos
                if($this->db->query("SELECT COUNT(*) AS cantidad FROM " . $this->table . " WHERE ID_Empresa=" . $where['ID_Empresa'] . " AND (No_Dominio_Empresa!='' OR Txt_Email_Empresa!='' OR Nu_Celular_Empresa!='' OR Nu_Telefono_Empresa!='' OR Txt_Slogan_Empresa!='' OR No_Imagen_Logo_Empresa!='' OR Txt_Terminos_Condiciones_Ticket!='' OR Txt_Cuentas_Bancarias!='') LIMIT 1")->row()->cantidad > 0){
                    //Cambiar estado a completado para el tour
                    $data_tour = array('Nu_Estado_Proceso' => 1);
                } else {
                    //Cambiar estado a completado para el tour
                    $data_tour = array('Nu_Estado_Proceso' => 0);
                }
                $this->db->update('tour_gestion', $data_tour, $where_tour);
                /* END TOUR GESTION */

                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
            } else
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
        }
    }
    
	public function eliminarSistema($ID_Empresa, $ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_empresa . " WHERE ID_Empresa = " . $ID_Empresa . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La configuracion se encuentra enlazada a una empresa');
		} else {
			$sUrlImageLogoEmpresa = $this->db->query("SELECT No_Imagen_Logo_Empresa FROM configuracion WHERE ID_Configuracion=" . $ID . " LIMIT 1")->row()->No_Imagen_Logo_Empresa;

			$this->db->where('ID_Configuracion', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
				if ( !empty($sUrlImageLogoEmpresa) ) {
					$arrUrlImage = explode($this->empresa->Nu_Documento_Identidad, $sUrlImageLogoEmpresa);
					$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . $arrUrlImage[1];
					if ( file_exists($path) )
						unlink($path);
				}
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
            }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
	
	public function reemplazarImagen($No_File){
		$verify_No_File = false;
        if(file_exists($this->upload_path . $No_File)) {//mientras el archivo exista entramos
			unlink($this->upload_path . $No_File);
			$verify_No_File = true;
        }
        return $verify_No_File;
    }
    
    public function changeNamePictureLogo($arrDataGET){
        $data = array("No_Logo_Empresa" => $arrDataGET["arrFile"]["file"]["name"]);
        $where = array("ID_Configuracion" => $arrDataGET['ID_Configuracion']);
        $response = array('sStatus' => 'error', 'sMessage' => 'error al cambiar logo', 'sClassModal' => 'modal-danger');
        if ($this->db->update("configuracion", $data, $where) > 0)
            $response = array('sStatus' => 'success', 'sMessage' => 'logo guardado', 'sClassModal' => 'modal-success');
        return $response;
    }

    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Version de imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error Version de imagen modificada');
    }
}
