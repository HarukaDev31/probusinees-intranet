<?php
class ReporteFormaPagoModel extends CI_Model{
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
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];
        $iIdPersonal=$arrParams['iIdPersonal'];
        $sNombrePersonal=$arrParams['sNombrePersonal'];
        $iTipoVenta=$arrParams['iTipoVenta'];
        $iMedioPago=$arrParams['iMedioPago'];
        $iTipoTarjeta=$arrParams['iTipoTarjeta'];
        $ID_Almacen=$arrParams['ID_Almacen'];

        $Nu_Tipo_Impuesto=$arrParams['Nu_Tipo_Impuesto'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";
        $cond_personal = ( $iIdPersonal != '-' && $sNombrePersonal != '-' ) ? 'AND EMPLE.ID_Entidad = ' . $iIdPersonal : "";
        $cond_medio_pago = $iMedioPago != "0" ? 'AND MP.ID_Medio_Pago = ' . $iMedioPago : "";
        $cond_tipo_tarjeta = $iTipoTarjeta != "0" ? 'AND TMP.ID_Tipo_Medio_Pago = ' . $iTipoTarjeta : "";
        $cond_tipo_venta = '';
        if ( $iTipoVenta == 1 )
            $cond_tipo_venta = 'AND SD.ID_POS IS NULL';
        else if ( $iTipoVenta == 2 )
            $cond_tipo_venta = 'AND SD.ID_POS > 0';

        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');
//AND (VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "' OR VMP.Fe_Emision_Hora_Pago BETWEEN '" . $Fe_Inicio . " 00:00:00' AND '" . $Fe_Fin . " 23:59:59')
//AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "

        $where_gratuita = '';
        if ( $Nu_Tipo_Impuesto == 1 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto=4';
        else if ( $Nu_Tipo_Impuesto == 2 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto!=4';

        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Documento_Cabecera,
VC.ID_Empresa,
VC.Fe_Emision_Hora,
VMP.Fe_Emision_Hora_Pago,
EMPLE.No_Entidad AS No_Empleado,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDI.No_Tipo_Documento_Identidad_Breve,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
MONE.ID_Moneda,
MONE.No_Signo,
'0.00' AS Ss_Tipo_Cambio,
'0.00' AS Ss_Tipo_Cambio_Modificar,
MP.No_Medio_Pago,
TMP.No_Tipo_Medio_Pago,
VMP.Nu_Tarjeta,
VMP.Nu_Transaccion,
VMP.Ss_Total,
VC.Ss_Total_Saldo,
VC.Nu_Estado,
VC.Ss_Total AS Ss_Total_Cabecera,
MP.Nu_Tipo_Caja,
MP.Nu_Tipo,
VC.Ss_Vuelto,
VMP.ID_Documento_Medio_Pago,
TMP.ID_Tipo_Medio_Pago,
MP.ID_Medio_Pago,
VC.Fe_Vencimiento
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(VMP.ID_Medio_Pago = MP.ID_Medio_Pago)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN tipo_medio_pago AS TMP ON(TMP.ID_Tipo_Medio_Pago = VMP.ID_Tipo_Medio_Pago)
LEFT JOIN matricula_empleado AS MEMPLE ON(VMP.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
LEFT JOIN entidad AS EMPLE ON(VC.ID_Empresa = EMPLE.ID_Empresa AND VC.ID_Organizacion = EMPLE.ID_Organizacion AND (EMPLE.ID_Entidad = VC.ID_Mesero OR MEMPLE.ID_Entidad = EMPLE.ID_Entidad)) 
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VMP.Fe_Emision_Hora_Pago BETWEEN '" . $Fe_Inicio . " 00:00:00' AND '" . $Fe_Fin . " 23:59:59'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_tipo_venta . "
" . $cond_cliente . "
" . $cond_personal . "
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

        //Aquí buscar si elimine un documento que le pertenece a un documento al crédito cargar ese saldo
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

	public function actualizarMedioPago($arrPost){
        $sql = "UPDATE documento_medio_pago SET Ss_Total=" . $arrPost['fTotalMedioPago'] . " WHERE ID_Documento_Medio_Pago=" . $arrPost['iIdDocumentoMedioPago'];
        $this->db->query($sql);

        //Si cambia de forma de pago ingresa
        if($arrPost['iIdMedioPagoActual'] != $arrPost['iFormaPago']) {
            //si es crédito y quiere cambiar de forma de pago, no debe de tener ningún pago a cuenta o cobros al cliente
            if($arrPost['iTipoMedioPagoOperacionActual']==1) {//1=credito es un valor único
                $sql = "SELECT COUNT(*) as existe FROM documento_medio_pago WHERE ID_Documento_Cabecera=" . $arrPost['iIdDocumentoCabecera'];
                $existe_medio_pago = $this->db->query($sql)->row()->existe;
                //if($arrPost['sTotalSaldo']<$arrPost['sTotalDocumento']){
                if($existe_medio_pago>1){
                    return array('sStatus' => 'warning', 'sMessage' => 'Para cambiar debes de eliminar los cobros a cuenta.');//aquí mejor validar si hay un registro de pagos
                }
            }
        
            $this->db->trans_begin();
            
            $sql = "UPDATE documento_medio_pago SET
ID_Medio_Pago=" . $arrPost['iFormaPago'] . ",
ID_Tipo_Medio_Pago=" . $arrPost['iTipoMedioPago'] . ",
Nu_Tarjeta='" . $arrPost['iNumeroTransaccion'] . "',
Nu_Transaccion='" . $arrPost['iNumeroTarjeta'] . "'
WHERE ID_Documento_Medio_Pago=" . $arrPost['iIdDocumentoMedioPago'];
            $this->db->query($sql);

            $sql = "UPDATE documento_cabecera SET ID_Medio_Pago=" . $arrPost['iFormaPago'] . " WHERE ID_Documento_Cabecera=" . $arrPost['iIdDocumentoCabecera'];
            $this->db->query($sql);

            //si cualquier medio de pago se cambia a crédito deberé actualizar la tabla documento_cabecera y agregarle importe de saldo y fecha de vencimiento
            if($arrPost['iTipoMedioPagoOperacion']==1) {//1=credito es un valor único
                $sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=" . $arrPost['sTotalDocumento'] . ", Fe_Vencimiento='" . ToDate($arrPost['Fe_Vencimiento']) . "' WHERE ID_Documento_Cabecera=" . $arrPost['iIdDocumentoCabecera'];
                $this->db->query($sql);
                //debemos de volver a Ss_Total=0 para cuadre de caja
                $sql = "UPDATE documento_medio_pago SET Ss_Total=" . $arrPost['fTotalMedioPago'] . " WHERE ID_Documento_Medio_Pago=" . $arrPost['iIdDocumentoMedioPago'];
                $this->db->query($sql);
            }

            // si es crédito y se pasa a cualquier medio de pago
            if($arrPost['iTipoMedioPagoOperacionActual']==1) {//1=credito es un valor único
                //a la tabla documento_medio_pago el campo Ss_Total = agregarle el monto importe de crédito porque en BD esta en 0 para cuadre de caja
                $sql = "UPDATE documento_medio_pago SET Ss_Total=" . $arrPost['sTotalDocumento'] . " WHERE ID_Documento_Medio_Pago=" . $arrPost['iIdDocumentoMedioPago'];
                $this->db->query($sql);
                //a la tabla documento_cabecera quitarle monto
                $sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=0, Fe_Vencimiento='" . ToDate($arrPost['Fe_Vencimiento']) . "' WHERE ID_Documento_Cabecera=" . $arrPost['iIdDocumentoCabecera'];
                $this->db->query($sql);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('sStatus' => 'danger', 'sMessage' => 'Problemas al actualizar');
            } else {
                $this->db->trans_commit();
                return array('sStatus' => 'success', 'sMessage' => 'Registro actualizado');
            }
        } else {//no realizo nada
            //si solo cambia el tipo_medio_pago
            
            if($arrPost['iIdTipoMedioPagoActual'] != $arrPost['iTipoMedioPago']) {//1=credito es un valor único            
                $sql = "UPDATE documento_medio_pago SET
ID_Tipo_Medio_Pago=" . $arrPost['iTipoMedioPago'] . ",
Nu_Tarjeta='" . $arrPost['iNumeroTransaccion'] . "',
Nu_Transaccion='" . $arrPost['iNumeroTarjeta'] . "'
WHERE ID_Documento_Medio_Pago=" . $arrPost['iIdDocumentoMedioPago'];
                $this->db->query($sql);
            }

            //si es crédito y quiere cambiar de forma de pago, no debe de tener ningún pago a cuenta o cobros al cliente
            if($arrPost['iTipoMedioPagoOperacionActual']==1) {//1=credito es un valor único
                /*
                $sql = "SELECT COUNT(*) as existe FROM documento_medio_pago WHERE ID_Documento_Cabecera=" . $arrPost['iIdDocumentoCabecera'];
                $existe_medio_pago = $this->db->query($sql)->row()->existe;
                
                if($existe_medio_pago>1){
                    return array('sStatus' => 'warning', 'sMessage' => 'Para cambiar debes de eliminar los cobros a cuenta.');//aquí mejor validar si hay un registro de pagos
                } else {
                */
                    $sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=" . $arrPost['fTotalMedioPago'] . ", Fe_Vencimiento='" . ToDate($arrPost['Fe_Vencimiento']) . "' WHERE ID_Documento_Cabecera=" . $arrPost['iIdDocumentoCabecera'];
                    $this->db->query($sql);
                //}
            }

            return array('sStatus' => 'success', 'sMessage' => 'Registro actualizado.');
        }
    }
}
