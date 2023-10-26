<?php
class SaldoClienteModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $iIdTipoDocumento=$arrParams['iIdTipoDocumento'];
        $iIdSerieDocumento=$arrParams['iIdSerieDocumento'];
        $iNumeroDocumento=$arrParams['iNumeroDocumento'];
        $iEstadoPago=$arrParams['iEstadoPago'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];
        $ID_Almacen=$arrParams['ID_Almacen'];

        $cond_tipo = $iIdTipoDocumento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $iIdTipoDocumento : 'AND VC.ID_Tipo_Documento IN(2,3,4,6)';
        $cond_serie = $iIdSerieDocumento != "0" ? "AND VC.ID_Serie_Documento = '" . $iIdSerieDocumento . "'" : "";
        $cond_numero = $iNumeroDocumento != "-" ? "AND VC.ID_Numero_Documento = '" . $iNumeroDocumento . "'" : "";

        $cond_estado_pago = '';
        if ( $iEstadoPago == "1" )// Pendiente
            $cond_estado_pago = 'AND VC.Ss_Total_Saldo > 0.00';
        else if ( $iEstadoPago == "2" )// Cancelado
            $cond_estado_pago = 'AND VC.Ss_Total_Saldo = 0.00';

        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";
        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');

        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
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
VC.Fe_Emision,
VC.Fe_Vencimiento,
MP.Nu_Tipo,
VC.Nu_Estado,
VC.Ss_Retencion
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(VMP.ID_Medio_Pago = MP.ID_Medio_Pago)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND MP.Nu_Tipo = 1
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_pago . "
" . $cond_cliente . "
ORDER BY
ALMA.ID_Almacen DESC,
VC.ID_Documento_Cabecera DESC;";
        
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
            'sql' => $query,
        );
    }
}
