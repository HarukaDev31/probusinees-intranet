<?php
class ImprimirComprobanteModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
	public function formatoImpresionComprobante($ID_Documento_Cabecera, $ID_Tipo_Documento, $formato){
		$this->db->trans_begin();
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar configuración formato');
		
		// Actualizamos el formato de impresion
		if($ID_Tipo_Documento === '3') $conf['Txt_Formato_Factura'] = str_replace(' background: none repeat scroll 0% 0% transparent;', '', $formato);
		if($ID_Tipo_Documento === '4') $conf['Txt_Formato_Boleta'] = str_replace(' background: none repeat scroll 0% 0% transparent;', '', $formato);
		if($ID_Tipo_Documento === '5') $conf['Txt_Formato_NCredito'] = str_replace(' background: none repeat scroll 0% 0% transparent;', '', $formato);
		if($ID_Tipo_Documento === '7') $conf['Txt_Formato_Guia'] = str_replace(' background: none repeat scroll 0% 0% transparent;', '', $formato);

		// Actualizamos la configuracion
		$this->db->where('ID_Empresa', $this->user->ID_Empresa);
		$this->db->update('configuracion', $conf);
		
    	$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
        }
		return $response;
	}
}
