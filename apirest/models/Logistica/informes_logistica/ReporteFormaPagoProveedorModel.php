<?php
class ReporteFormaPagoProveedorModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Tipo_Documento=$arrParams['ID_Tipo_Documento'];
        $ID_Serie_Documento=$arrParams['ID_Serie_Documento'];
        $ID_Numero_Documento=$arrParams['ID_Numero_Documento'];
        $Nu_Estado_Documento=$arrParams['Nu_Estado_Documento'];
        $iIdProveedor=$arrParams['iIdProveedor'];
        $sNombreProveedor=$arrParams['sNombreProveedor'];
        $iIdPersonal=$arrParams['iIdPersonal'];
        $sNombrePersonal=$arrParams['sNombrePersonal'];
        $iTipoVenta=$arrParams['iTipoVenta'];
        $iMedioPago=$arrParams['iMedioPago'];
        $iTipoTarjeta=$arrParams['iTipoTarjeta'];
        $ID_Almacen=$arrParams['ID_Almacen'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $ID_Serie_Documento != "-" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_proveedor = ( $iIdProveedor != '-' && $sNombreProveedor != '-' ) ? 'AND PROVEE.ID_Entidad = ' . $iIdProveedor : "";
        $cond_medio_pago = $iMedioPago != "0" ? 'AND MP.ID_Medio_Pago = ' . $iMedioPago : "";
        $cond_tipo_tarjeta = $iTipoTarjeta != "0" ? 'AND TMP.ID_Tipo_Medio_Pago = ' . $iTipoTarjeta : "";

        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');

        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Documento_Cabecera,
VC.Fe_Emision_Hora,
VMP.Fe_Emision_Hora_Pago,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDI.No_Tipo_Documento_Identidad_Breve,
PROVEE.Nu_Documento_Identidad,
PROVEE.No_Entidad,
MONE.ID_Moneda,
MONE.No_Signo,
'0.00' AS Ss_Tipo_Cambio,
'0.00' AS Ss_Tipo_Cambio_Modificar,
MP.No_Medio_Pago,
TMP.No_Tipo_Medio_Pago,
VMP.Nu_Tarjeta,
VMP.Nu_Transaccion,
VMP.Ss_Total,
VC.Nu_Estado,
VC.Ss_Total AS Ss_Total_Cabecera,
MP.Nu_Tipo_Caja,
VC.Ss_Vuelto,
VMP.ID_Documento_Medio_Pago
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(VMP.ID_Medio_Pago = MP.ID_Medio_Pago)
LEFT JOIN tipo_medio_pago AS TMP ON(TMP.ID_Tipo_Medio_Pago = VMP.ID_Tipo_Medio_Pago)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS PROVEE ON(PROVEE.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = PROVEE.ID_Tipo_Documento_Identidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 2
AND (VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "' OR VMP.Fe_Emision_Hora_Pago BETWEEN '" . $Fe_Inicio . " 00:00:00' AND '" . $Fe_Fin . " 23:59:59')
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_proveedor . "
" . $cond_medio_pago . "
" . $cond_tipo_tarjeta . "
ORDER BY
ALMA.ID_Almacen,
VMP.ID_Documento_Medio_Pago DESC;";
        
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

	public function eliminarFormaPago($ID){
		$this->db->trans_begin();
        
		$query = "SELECT ID_Documento_Cabecera, Ss_Total FROM documento_medio_pago WHERE ID_Documento_Medio_Pago=" . $ID . " LIMIT 1";
		$arrMedioPagoReferencia = $this->db->query($query)->row();
        
		$query = "SELECT ID_Medio_Pago FROM documento_medio_pago WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera . " ORDER BY ID_Documento_Cabecera ASC LIMIT 1";
		$arrMedioPagoInicial = $this->db->query($query)->row();
		
		$query = "SELECT Nu_Tipo FROM medio_pago WHERE ID_Medio_Pago=" . $arrMedioPagoInicial->ID_Medio_Pago . " LIMIT 1";
		$arrMedioPago = $this->db->query($query)->row();

        //solo si es 1=crédito modifico saldo
        if($arrMedioPago->Nu_Tipo==1) {
            $sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrMedioPagoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera;
            $this->db->query($sql);
        }
        
        $sql = "DELETE FROM documento_medio_pago WHERE ID_Documento_Medio_Pago=" . $ID;
        $this->db->query($sql);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        }
    }
}
