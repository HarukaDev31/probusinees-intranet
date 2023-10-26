<?php
class BlogPostModel extends CI_Model{
	var $table = 'blog_post';
	var $table_tabla_dato = 'tabla_dato';
	
    var $column_order = array('No_Tag_Blog', 'No_Titulo_Blog');
    var $column_search = array();
    var $order = array('ID_Post_Blog' => 'desc');
    
	private $upload_path = '../assets/images/blog_posts/';
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_BlogPost') == 'Titulo' ){
            $this->db->like('POST.No_Titulo_Blog', $this->input->post('Global_Filter'));
        }
        
		$this->db->select('POST.ID_Empresa, POST.ID_Post_Blog, TAG.No_Descripcion AS No_Tag_Blog, POST.No_Titulo_Blog, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table . ' AS POST')
    	->join($this->table_tabla_dato . ' AS TAG', 'TAG.ID_Tabla_Dato = POST.ID_Tag_Blog', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = POST.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join')
		->where('POST.ID_Empresa', $this->empresa->ID_Empresa);
         
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
    	$this->db->select('POST.*, IMG.ID_Gallery');
        $this->db->from($this->table . ' AS POST');
        $this->db->join('gallery AS IMG', 'IMG.ID_Relacion_Gallery = POST.ID_Post_Blog', 'left');
        $this->db->where('POST.ID_Post_Blog',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarBlogPost($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Titulo_Blog='" . $data['No_Titulo_Blog'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			$this->db->trans_begin();
			
			$iIdsGallery = $data['EID_Gallery'];
			unset($data['EID_Post_Blog']);
			unset($data['EID_Gallery']);
			unset($data['ENo_Titulo_Blog']);
			unset($data['ENo_Imagen_Gallery']);
			unset($data['ENo_Url_Imagen_Gallery']);

			$this->db->insert($this->table, $data);
			$Last_ID_Post_Blog = $this->db->insert_id();

			if ( !empty($iIdsGallery) )//Solo si tiene al menos una imagen
				$this->db->query("UPDATE gallery SET ID_Relacion_Gallery = " . $Last_ID_Post_Blog . " WHERE ID_Gallery IN('" . $iIdsGallery . "')");

        	$this->db->trans_complete();
	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'iIDItem' => $Last_ID_Post_Blog);
	        }
		}
    }
    
    public function actualizarBlogPost($where, $data){
		if( $data['ENo_Titulo_Blog'] != $data['No_Titulo_Blog'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Titulo_Blog='" . $data['No_Titulo_Blog'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			$this->db->trans_begin();
			
			$iIdsGallery = $data['EID_Gallery'];
			$Last_ID_Post_Blog = $data['EID_Post_Blog'];
			unset($data['EID_Post_Blog']);
			unset($data['EID_Gallery']);
			unset($data['ENo_Titulo_Blog']);
			unset($data['ENo_Imagen_Gallery']);
			unset($data['ENo_Url_Imagen_Gallery']);

			$this->db->update($this->table, $data, $where);

			if ( !empty($iIdsGallery) )//Solo si tiene al menos una imagen
				$this->db->query("UPDATE gallery SET ID_Relacion_Gallery = " . $Last_ID_Post_Blog . " WHERE ID_Gallery IN('" . $iIdsGallery . "')");

        	$this->db->trans_complete();
	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'iIDItem' => $Last_ID_Post_Blog);
	        }
		}
    }
    
	public function eliminarPostBlog($ID){
		$this->db->trans_begin();
		
		$arrDataGallery = $this->db->query("SELECT No_Imagen_Gallery FROM gallery WHERE ID_Relacion_Gallery=".$ID)->result();

		$this->db->where('ID_Ruta_Enlace_Gallery', 1);//1 = Blog y 2 = Producto
		$this->db->where('ID_Relacion_Gallery', $ID);
		$this->db->delete('gallery');
		
		$this->db->where('ID_Post_Blog', $ID);
		$this->db->delete($this->table);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
		} else {
			$this->db->trans_commit();
			
			if (!empty($arrDataGallery)) {//Verifica si un array esta vacio o no
				foreach ($arrDataGallery as $row){
					$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $row->No_Imagen_Gallery;
					if ( file_exists($path) )
						unlink($path);
				}
			}
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
	}

	public function getGalleryImage($iIdRelacionGallery){
		$query = "SELECT ID_Gallery, No_Imagen_Gallery, No_Url_Imagen_Gallery FROM gallery WHERE ID_Relacion_Gallery=".$iIdRelacionGallery;
		
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ( $arrResponseSQL->num_rows() > 0 ){
            return array(
                'sStatus' => 'success',
                'arrData' => $arrResponseSQL->result(),
            );
        }
        
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No hay registros',
        );
	}

    public function agregarImagenGallery($data){
        if ( $this->db->insert('gallery', $data) > 0 )
			return array('sStatus' => 'success', 'sClassModal' => 'modal-success', 'sMessage' => 'Se guardo imagen', 'sNombreImagenGallery' => $data['No_Imagen_Gallery'], 'sNombreImagenGalleryUrl' => $data['No_Url_Imagen_Gallery'], 'iLastIdGallery' => $this->db->insert_id());
        return array('sStatus' => 'error', 'sClassModal' => 'modal-danger', 'sMessage' => 'Error al insertar imagen a la galería de fotos');
    }

    public function deleteImagenGallery($where){
        if ( $this->db->delete('gallery', $where) > 0 )
			return array('sStatus' => 'success', 'sClassModal' => 'modal-success', 'sMessage' => 'Se eliminó relación de imagen');
        return array('sStatus' => 'error', 'sClassModal' => 'modal-danger', 'sMessage' => 'Error al eliminar relación de imagen');
    }
}