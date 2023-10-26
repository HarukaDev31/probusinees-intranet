<?php
class ServicioTercerizadoModel extends CI_Model{
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
        $iEstadoLavado=$arrParams['iEstadoLavado'];
        $iIdProveedor=$arrParams['iIdProveedor'];

        $cond_tipo = $iIdTipoDocumento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $iIdTipoDocumento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $iIdSerieDocumento != "0" ? "AND VC.ID_Serie_Documento = '" . $iIdSerieDocumento . "'" : "";
        $cond_numero = $iNumeroDocumento != "-" ? "AND VC.ID_Numero_Documento = '" . $iNumeroDocumento . "'" : "";
        $cond_estado_documento = $iEstado != "0" ? 'AND VC.Nu_Estado = ' . $iEstado : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";
        $cond_estado_lavado = $iEstadoLavado != "0" ? 'AND VC.Nu_Estado_Lavado = ' . $iEstadoLavado : "";
        $cond_proveedor = $iIdProveedor != "0" ? 'AND PROVE.ID_Entidad = ' . $iIdProveedor : "";

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
VC.Nu_Estado_Lavado,
PROVE.No_Entidad AS No_Entidad_Proveedor,
VC.Txt_Glosa
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK = VC.ID_Serie_Documento_PK)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN entidad AS PROVE ON(PROVE.ID_Entidad = VC.ID_Contacto)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.Nu_Transporte_Lavanderia_Hoy = 2
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_estado_lavado . "
" . $cond_cliente . "
" . $cond_proveedor . "
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

	public function getDocumento($arrPost){
        $query = "
SELECT
 VD.ID_Producto,
 VD.Qt_Producto,
 ITEM.No_Producto,
 VD.Txt_Nota AS Txt_Nota_Detalle
FROM
 documento_cabecera AS VC
 JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
 JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
WHERE
 VC.ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'];

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

	public function modificarPedido($arrPost){
        $this->db->trans_begin();
        
        $arrCabecera = $arrPost['arrCabecera'];
        $arrDetalle = $arrPost['arrDetalleNuevosItems'];

        $data = array(
            'ID_Contacto' => $arrCabecera['iIdProveedor'],
            'Fe_Vencimiento' => $arrCabecera['dEntrega'],
            'Nu_Estado_Lavado' => $arrCabecera['iEstadoLavado'],
        );
        $where = array( 'ID_Documento_Cabecera' => $arrCabecera['iIdDocumentoCabecera'] );
        $this->db->update('documento_cabecera', $data, $where);

		foreach ($arrDetalle as $row){
            $arrDocumentoDetalleEstadoLavado[] = array(
                'ID_Empresa' => $this->empresa->ID_Empresa,
                'ID_Documento_Cabecera' => $arrCabecera['iIdDocumentoCabecera'],
                'ID_Entidad_Lavado' => $arrCabecera['iIdProveedor'],
                'Qt_Producto' => $row['fCantidadItemNuevo'],
                'Txt_Item' => $row['sNombreItemNuevo'],
                'Nu_Estado_Lavado' => $arrCabecera['iEstadoLavado'],
            );
        }
        $this->db->insert_batch('documento_detalle_estado_lavado', $arrDocumentoDetalleEstadoLavado);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al cambiar estado');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Procesado');
        }
    }

	public function cambiarEstadoLavado($arrPost){
        $this->db->trans_begin();
        
		foreach ($arrPost['arrIdDocumentoCabecera'] as $key => $value){
            $arrCambiarEstadoDocumento[] = array(
              'ID_Documento_Cabecera' => $key,
              'Nu_Estado_Lavado' => 13,
            );
        }
        $this->db->update_batch('documento_cabecera', $arrCambiarEstadoDocumento, 'ID_Documento_Cabecera');

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al cambiar estado');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Registro(s) enviado(s)');
		}
    }

	public function getDocumentoProcesadoLavado($arrPost){
        $query = "
SELECT
 VDELAVA.ID_Documento_Estado_Lavado,
 VDELAVA.Qt_Producto,
 VDELAVA.Txt_Item AS No_Producto,
 VDELAVA.Nu_Estado_Verificacion
FROM
 documento_cabecera AS VC
 JOIN documento_detalle_estado_lavado AS VDELAVA ON(VC.ID_Documento_Cabecera = VDELAVA.ID_Documento_Cabecera)
WHERE
 VC.ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'];
 /*AND VDELAVA.Nu_Estado_Verificacion = 0 - modificado dia 09/04/2020 */

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

	public function verificarPedido($arrPost){
        $this->db->trans_begin();
        
        $iTotalItemsSeleccionado = count($arrPost['arrIdItem']);
        $iEstadoLavado = ( ($arrPost['iTotalItemDetalle'] == $iTotalItemsSeleccionado) ? 15 : 14);
        $iEstadoLavadoRecepcionCliente = ( ($arrPost['iTotalItemDetalle'] == $iTotalItemsSeleccionado) ? 4 : 1);
        
        $data = array(
            'Nu_Estado_Lavado' => $iEstadoLavado,
            'Nu_Estado_Lavado_Recepcion_Cliente' => $iEstadoLavadoRecepcionCliente,
        );
        $where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabeceraVerificar'] );
        $this->db->update('documento_cabecera', $data, $where);

        $data = array(
            'Nu_Estado_Lavado' => $iEstadoLavado,
        );
        $where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabeceraVerificar'] );
        $this->db->update('documento_detalle', $data, $where);

		foreach ($arrPost['arrIdItem'] as $key => $value){
            $arrCambiarEstadoDocumento[] = array(
              'ID_Documento_Estado_Lavado' => $key,
              'Nu_Estado_Lavado' => $iEstadoLavado,
              'Nu_Estado_Verificacion' => 1
            );
        }
        $this->db->update_batch('documento_detalle_estado_lavado', $arrCambiarEstadoDocumento, 'ID_Documento_Estado_Lavado');

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al procesar');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Procesado');
		}
    }
}
