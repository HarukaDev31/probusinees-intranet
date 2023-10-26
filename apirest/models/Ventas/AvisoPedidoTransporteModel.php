<?php
class AvisoPedidoTransporteModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $iIdTipoDocumento=$arrParams['iIdTipoDocumento'];
        $iIdSerieDocumento=$arrParams['iIdSerieDocumento'];
        $iNumeroDocumento=$arrParams['iNumeroDocumento'];
        $iEstado=$arrParams['iEstado'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];

        $cond_tipo = $iIdTipoDocumento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $iIdTipoDocumento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $iIdSerieDocumento != "0" ? "AND VC.ID_Serie_Documento = '" . $iIdSerieDocumento . "'" : "";
        $cond_numero = $iNumeroDocumento != "-" ? "AND VC.ID_Numero_Documento = '" . $iNumeroDocumento . "'" : "";
        $cond_estado_documento = $iEstado != "0" ? 'AND VC.Nu_Estado = ' . $iEstado : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";

        $query = "SELECT DISTINCT
VC.ID_Documento_Cabecera,
VC.Fe_Emision_Hora,
VC.ID_Tipo_Documento,
TD.No_Tipo_Documento_Breve,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
CLI.No_Entidad,
MONE.No_Signo,
VC.Ss_Total,
VC.Ss_Total_Saldo,
VC.Nu_Estado_Lavado
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK = VC.ID_Serie_Documento_PK)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.Nu_Estado_Lavado IN (1,17)
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_cliente . "
ORDER BY CONVERT(VC.ID_Numero_Documento, SIGNED INTEGER) DESC;";
        
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

	public function actualizarPedido($arrPost){
        $this->db->trans_begin();
        
        $data = array( 'Nu_Estado_Lavado' => 8 );
        $where = array( 'ID_Documento_Cabecera' => $arrPost['arrCabecera']['iIdDocumentoCabecera'] );
        $this->db->update('documento_cabecera', $data, $where);

        $data = array( 'ID_Entidad_Lavado_AvisoPedidoTransporte' => $arrPost['arrCabecera']['iIdEntidad'], 'ID_Entidad_Lavado' => $arrPost['arrCabecera']['iIdEntidad'], 'Txt_AvisoPedidoTransporte' => $arrPost['arrCabecera']['sFinalAvisoPedidoTransporte'], 'Nu_Estado_Lavado' => 8 );
        $where = array( 'ID_Documento_Cabecera' => $arrPost['arrCabecera']['iIdDocumentoCabecera'] );
        $this->db->update('documento_detalle_estado_lavado', $data, $where);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al finalizar pedido');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Pedido finalizado');
		}
    }
}
