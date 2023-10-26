<?php
class MonitoreoDocumentosElectronicosModel extends CI_Model{
	var $table                      = 'documento_cabecera';
	var $table_empresa              = 'empresa';
	var $table_organizacion	        = 'organizacion';
	var $table_tipo_documento	    = 'tipo_documento';
	
    var $column_order = array('');
    var $column_search = array('');
    var $order = array('');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
		$this->db->select('VC.ID_Documento_Cabecera, ORG.Nu_Estado_Sistema, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Nu_Estado, Txt_Respuesta_Sunat_FE')
		->from($this->table . ' AS VC')
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = VC.ID_Empresa', 'join')
		->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = VC.ID_Organizacion', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->where('VC.ID_Tipo_Asiento', 1)
		->where('VC.ID_Tipo_Documento != ', 2)
		->where_in('VC.Nu_Estado', array(6,7,9,11));
		
		if ( $this->user->No_Usuario == 'root' ){
			if(!empty($this->input->post('filtro_empresa')))
				$this->db->where('VC.ID_Empresa', $this->input->post('filtro_empresa'));
			
			//if( $this->input->post('filtro_organizacion') )
				//$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
		} else {
            $this->db->where('VC.ID_Empresa', $this->empresa->ID_Empresa);
            //$this->db->where('ORG.ID_Organizacion', $this->empresa->ID_Organizacion);
        }

		//$this->db->where('VC.ID_Empresa != ', 232);
				
        //if($this->input->post('filtro_estado_sistema') != '-')
			//$this->db->where('ORG.Nu_Estado_Sistema', $this->input->post('filtro_estado_sistema'));
			//$this->db->where('ORG.Nu_Estado_Sistema', 1);//luego quitar comentario
			
		$this->db->where('ORG.Nu_Estado_Sistema', 1);

        if(!empty($this->input->post('filtro_tipo_sistema')))
			$this->db->where('EMP.Nu_Tipo_Proveedor_FE', $this->input->post('filtro_tipo_sistema'));
			
		if(!empty($this->input->post('filtro_tipo_documento')))
			$this->db->where('VC.ID_Tipo_Documento', $this->input->post('filtro_tipo_documento'));

    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
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
        return 0;
    }
	
	public function obtenerVenta($arrPost){
		$id=$arrPost['id'];
		$query = "SELECT
ID_Empresa,
ID_Organizacion,
ID_Almacen,
Fe_Emision,
Fe_Emision_Hora,
Fe_Vencimiento,
ID_Tipo_Documento,
ID_Serie_Documento,
ID_Numero_Documento
FROM
documento_cabecera
WHERE
ID_Documento_Cabecera = " . $id . " LIMIT 1";
		
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
				'result' => $arrResponseSQL->row()
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}
	
	public function actualizarVenta($arrPost){
		if(empty($arrPost['ID_Organizacion_Modificar'])){
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No tiene organizacion');
		}

		if(empty($arrPost['ID_Almacen_Modificar'])){
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No tiene almacén');
		}

		$arrPost['Fe_Emision_Modificar'] = ToDate($arrPost['Fe_Emision_Modificar']);
		$arrPost['Fe_Vencimiento_Modificar'] = ToDate($arrPost['Fe_Vencimiento_Modificar']);

		$arrHora = explode(' ', $arrPost['Fe_Hora_Modificar']);
		$Fe_Emision_Hora = $arrPost['Fe_Emision_Modificar'];
		if(!empty($arrHora) && count($arrHora)>1){
			$Fe_Emision_Hora = $arrPost['Fe_Emision_Modificar'] . " " . $arrHora[1];
		}

		$this->db->trans_begin();
		$sql = "UPDATE documento_cabecera SET ID_Organizacion=" . $arrPost['ID_Organizacion_Modificar'] . ", ID_Almacen=" . $arrPost['ID_Almacen_Modificar'] . ", Fe_Emision='" . $arrPost['Fe_Emision_Modificar'] . "', Fe_Emision_Hora='" . $Fe_Emision_Hora . "', Fe_Periodo=Fe_Emision, Fe_Vencimiento='" . $arrPost['Fe_Vencimiento_Modificar'] . "' WHERE ID_Documento_Cabecera=" . $arrPost['ID_Venta_Modificar'];
		$this->db->query($sql);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
	}
	
	public function anularVenta($id){
		$this->db->trans_begin();
		$sql = "UPDATE documento_cabecera SET Nu_Estado=10, Txt_Url_CDR='-', Ss_Total=0, Ss_Total_Saldo=0, Ss_Descuento=0, Ss_Descuento_Impuesto=0, Ss_Vuelto=0, Ss_Detraccion=0, Ss_Retencion=0 WHERE ID_Documento_Cabecera=" . $id;
		$this->db->query($sql);

		//No se debe de ejecutar porque malogra el kardex y el stock actual
		//Cuando realizamos el canje debemos de marcar sin generar STOCK

		/*
		$sql = "DELETE FROM documento_detalle WHERE ID_Documento_Cabecera WHERE ID_Documento_Cabecera=" . $arrPost['ID_Venta_Modificar'];
		$this->db->query($sql);
		
		$sql = "DELETE FROM documento_medio_pago WHERE ID_Documento_Cabecera WHERE ID_Documento_Cabecera=" . $arrPost['ID_Venta_Modificar'];
		$this->db->query($sql);
		
		$sql = "DELETE FROM documento_detalle_lote WHERE ID_Documento_Cabecera WHERE ID_Documento_Cabecera=" . $arrPost['ID_Venta_Modificar'];
		$this->db->query($sql);
		
		$sql = "DELETE FROM documento_detalle_estado_lavado WHERE ID_Documento_Cabecera WHERE ID_Documento_Cabecera=" . $arrPost['ID_Venta_Modificar'];
		$this->db->query($sql);
		
		$sql = "DELETE FROM documento_enlace WHERE ID_Documento_Cabecera WHERE ID_Documento_Cabecera=" . $arrPost['ID_Venta_Modificar'];
		$this->db->query($sql);
		*/

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al anular');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro anulado');
		}
	}
	
	public function getOrganizacionEmpresa($arrPost){
		$ID_Empresa = $arrPost['ID_Empresa'];
		$query = "SELECT ID_Organizacion, No_Organizacion FROM organizacion WHERE ID_Empresa = " . $ID_Empresa . " AND Nu_Estado = 1 ORDER BY No_Organizacion";
		
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
			'sMessage' => 'No se encontro registro',
		);
	}
	
	public function getAlmacenesEmpresa($arrPost){
		$ID_Organizacion = $arrPost['ID_Organizacion'];
		$query = "SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Organizacion = " . $ID_Organizacion . " AND Nu_Estado = 1 ORDER BY No_Almacen";
		
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
			'sMessage' => 'No se encontro registro',
		);
	}
}
