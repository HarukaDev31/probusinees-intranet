<?php
class EstadoCuentaCorrienteClienteModel extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	
    public function getReporte($arrParams){
        $iTipoConsultaFecha=$arrParams['iTipoConsultaFecha'];
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Tipo_Documento=$arrParams['ID_Tipo_Documento'];
        $ID_Serie_Documento=$arrParams['ID_Serie_Documento'];
        $ID_Numero_Documento=$arrParams['ID_Numero_Documento'];
        $Nu_Estado_Documento=$arrParams['Nu_Estado_Documento'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];
        $iMedioPago=$arrParams['iMedioPago'];
        $iTipoTarjeta=$arrParams['iTipoTarjeta'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";
		$cond_medio_pago = $iMedioPago != "0" ? 'AND MP.ID_Medio_Pago = ' . $iMedioPago : "";
		$cond_tipo_tarjeta = $iTipoTarjeta != "0" ? 'AND TMP.ID_Tipo_Medio_Pago = ' . $iTipoTarjeta : "";

        $cond_fecha_matricula_empleado = "AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'";
        if ( $iTipoConsultaFecha=='0' ) {//0=Actual
            $cond_fecha_matricula_empleado = "
AND VC.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . "
AND VC.Fe_Emision_Hora>='" . $this->session->userdata['arrDataPersonal']['arrData'][0]->Fe_Matricula . "'";
        }

        $query = "
SELECT
 TD.No_Tipo_Documento_Breve,
 VC.ID_Documento_Cabecera,
 VC.ID_Tipo_Documento,
 VC.ID_Serie_Documento,
 VC.ID_Numero_Documento,
 VC.Fe_Emision_Hora,
 MONE.ID_Moneda,
 MONE.No_Signo,
 MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
 CLI.No_Entidad,
 TC.Ss_Compra_Oficial AS Ss_Tipo_Cambio,
 VE.Ss_Tipo_Cambio_Modificar,
 MP.Nu_Tipo_Caja,
 MP.No_Medio_Pago,
 TMP.No_Tipo_Medio_Pago,
 VMP.Nu_Tarjeta,
 VMP.Nu_Transaccion,
 VMP.Ss_Total AS Ss_Total_VMP,
 TDESTADO.No_Descripcion AS No_Estado,
 TDESTADO.No_Class AS No_Class_Estado,
 VC.Nu_Estado
FROM
 documento_medio_pago AS VMP
 JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
 LEFT JOIN tipo_medio_pago AS TMP ON(MP.ID_Medio_Pago = TMP.ID_Medio_Pago)
 JOIN documento_cabecera AS VC ON(VC.ID_Documento_Cabecera = VMP.ID_Documento_Cabecera)
 JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
 JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
 JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
 JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
 JOIN tabla_dato AS TDESTADO ON(TDESTADO.Nu_Valor = VC.Nu_Estado AND TDESTADO.No_Relacion = 'Tipos_EstadoDocumento')
 LEFT JOIN tasa_cambio AS TC ON(VC.ID_Empresa = TC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND VC.Fe_Emision = TC.Fe_Ingreso)
 LEFT JOIN (
  SELECT
   VE.ID_Documento_Cabecera,
   TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio_Modificar
  FROM
   documento_cabecera AS VC
   JOIN documento_enlace AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera_Enlace)
   JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
 ) AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera)
WHERE
 VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
 AND VC.ID_Tipo_Asiento = 1
 AND SD.ID_POS > 0
 AND VMP.Ss_Total > 0.00
 " . $cond_fecha_matricula_empleado . "
 " . $cond_tipo . "
 " . $cond_serie . "
 " . $cond_numero . "
 " . $cond_estado_documento . "
 " . $cond_cliente . "
 " . $cond_medio_pago . "
 " . $cond_tipo_tarjeta . "
ORDER BY
 VMP.Fe_Emision_Hora_Pago ASC,
 VC.Fe_Emision_Hora DESC,
 VC.ID_Tipo_Documento DESC,
 VC.ID_Serie_Documento DESC,
 CONVERT(VC.ID_Numero_Documento, SIGNED INTEGER) DESC;";
 
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
            'sMessage' => 'No se encontráron registro',
        );
    }
}
