<?php
class LavanderiaExternasModel extends CI_Model{
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

        $query = "SELECT
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
VC.Nu_Estado_Lavado_Recepcion_Cliente,
VMP.ID_Documento_Medio_Pago,
VC.Txt_Glosa
FROM
documento_cabecera AS VC
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK = VC.ID_Serie_Documento_PK)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.Nu_Transporte_Lavanderia_Hoy = 3
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
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

	public function entregarPedidoLavado($arrPost){
        $this->db->trans_begin();

        $data = array( 'Nu_Estado_Lavado_Recepcion_Cliente' => $arrPost['iEstadoLavadoRecepcionCliente'] );
        if ( !empty($arrPost['fPagoCliente']) ) {
            $documento_medio_pago = array(
                'ID_Empresa'			=> $this->empresa->ID_Empresa,
                'ID_Documento_Cabecera'	=> $arrPost['iIdDocumentoCabecera'],
                'ID_Medio_Pago'		    => $arrPost['iFormaPago'],
                'Nu_Transaccion'		=> $arrPost['iNumeroTransaccion'],
                'Nu_Tarjeta'		    => $arrPost['iNumeroTarjeta'],
                'Ss_Total'		        => $arrPost['fPagoCliente'],                
                'ID_Tipo_Medio_Pago' => isset($arrPost['iTipoMedioPago']) ? $arrPost['iTipoMedioPago'] : 0,
                'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
                'ID_Matricula_Empleado' => (isset($this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado) ? $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado : 0),
                'ID_Documento_Medio_Pago_Enlace' => $arrPost['iIdDocumentoMedioPago'],
            );
            $this->db->insert('documento_medio_pago', $documento_medio_pago);
            $data = array_merge( $data, array( 'Ss_Total_Saldo' => $arrPost['fSaldoCliente'] - $arrPost['fPagoCliente'] ) );
        }
        
        $where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabecera'] );
        $this->db->update('documento_cabecera', $data, $where);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al cambiar procesar');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Procesado');
        }
    }
}
