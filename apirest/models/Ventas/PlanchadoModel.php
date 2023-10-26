<?php
class PlanchadoModel extends CI_Model{
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
        $iEstadoLavado=$arrParams['iEstadoLavado'];
        $iIdFamilia=$arrParams['iIdFamilia'];
        $iIdItem=$arrParams['iIdItem'];
        $sNombreItem=$arrParams['sNombreItem'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];

        $cond_tipo = $iIdTipoDocumento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $iIdTipoDocumento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $iIdSerieDocumento != "0" ? "AND VC.ID_Serie_Documento = '" . $iIdSerieDocumento . "'" : "";
        $cond_numero = $iNumeroDocumento != "-" ? "AND VC.ID_Numero_Documento = '" . $iNumeroDocumento . "'" : "";
        $cond_estado_documento = $iEstado != "0" ? 'AND VC.Nu_Estado = ' . $iEstado : "";
       
        //AND VC.Nu_Estado_Lavado IN(2,4,7)
        if ( $iEstadoLavado == "0" ) {
            $cond_estado_orden_lavado = "AND (VD.Nu_Estado_Lavado IN(2,4,7,8) OR DDEL.Nu_Estado_Lavado IN(2,4,7,8))";
        } else if ( $iEstadoLavado == "16" ) {
            $cond_estado_orden_lavado = "AND (VD.Nu_Estado_Lavado IN(2,4,7) OR DDEL.Nu_Estado_Lavado IN(2,4,7))";
        } else if ( $iEstadoLavado == "18" ) {
            $cond_estado_orden_lavado = "AND (VD.Nu_Estado_Lavado IN(8) OR DDEL.Nu_Estado_Lavado IN(8))";
        }
        $cond_familia = $iIdFamilia != "0" ? 'AND ITEM.ID_Familia = ' . $iIdFamilia : "";
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND VD.ID_Producto = ' . $iIdItem : "";

        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";

        $query = "SELECT DISTINCT
VC.ID_Documento_Cabecera,
VD.ID_Documento_Detalle,
VD.Qt_Producto,
ITEM.ID_Producto,
ITEM.No_Producto,
VC.Fe_Emision_Hora,
VC.Fe_Entrega,
VC.ID_Tipo_Documento,
TD.No_Tipo_Documento_Breve,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
CLI.No_Entidad,
MONE.No_Signo,
VC.Ss_Total,
VC.Ss_Total_Saldo,
VC.Nu_Estado_Lavado,
VC.Txt_Glosa,
DDEL.Nu_Estado_Lavado AS Nu_Estado_Lavado_Detalle
FROM
documento_detalle AS VD
LEFT JOIN documento_detalle_estado_lavado AS DDEL ON(DDEL.ID_Documento_Cabecera = VD.ID_Documento_Cabecera AND DDEL.ID_Documento_Detalle = VD.ID_Documento_Detalle)
JOIN documento_cabecera AS VC ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK = VC.ID_Serie_Documento_PK)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Empresa=" . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento=1
AND VC.Nu_Estado_Lavado_Recepcion_Cliente IN(1,2)
AND ITEM.ID_Tipo_Pedido_Lavado=1
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_estado_orden_lavado . "
" . $cond_familia . "
" . $cond_item . "
" . $cond_cliente . "
ORDER BY VC.ID_Documento_Cabecera DESC;";
        
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
        
        if (
            $this->db->query("SELECT COUNT(*) AS cantidad_registros FROM documento_detalle WHERE ID_Documento_Cabecera = " . $arrPost['arrCabecera']['iIdDocumentoCabecera'])->row()->cantidad_registros
            ==
            $this->db->query("SELECT COUNT(*) AS cantidad_registros FROM documento_detalle_estado_lavado WHERE ID_Documento_Cabecera = " . $arrPost['arrCabecera']['iIdDocumentoCabecera'])->row()->cantidad_registros
        ) {
            $data = array( 'Nu_Estado_Lavado' => $arrPost['arrCabecera']['iEstadoLavado'] );
            $where = array( 'ID_Documento_Cabecera' => $arrPost['arrCabecera']['iIdDocumentoCabecera'] );
            $this->db->update('documento_cabecera', $data, $where);
        }

        $data = array( 'Nu_Estado_Lavado' => $arrPost['arrCabecera']['iEstadoLavado'] );
        $where = array( 'ID_Documento_Cabecera' => $arrPost['arrCabecera']['iIdDocumentoCabecera'], 'ID_Documento_Detalle' => $arrPost['arrCabecera']['iIdDocumentoDetalle'] );
        $this->db->update('documento_detalle', $data, $where);

        $data = array( 'ID_Entidad_Lavado_Planchado' => $arrPost['arrCabecera']['iIdEntidad'], 'ID_Entidad_Lavado' => $arrPost['arrCabecera']['iIdEntidad'], 'Txt_Planchado' => $arrPost['arrCabecera']['sFinalPlanchado'], 'Nu_Estado_Lavado' => $arrPost['arrCabecera']['iEstadoLavado'] );
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
