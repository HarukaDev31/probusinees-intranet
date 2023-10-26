<?php
class EstadoLavadoDeliveryModel extends CI_Model{
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
        $iTipoRecepcionCliente=$arrParams['iTipoRecepcionCliente'];
        $iEstadoLavado=$arrParams['iEstadoLavado'];

        $cond_tipo = $iIdTipoDocumento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $iIdTipoDocumento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $iIdSerieDocumento != "0" ? "AND VC.ID_Serie_Documento = '" . $iIdSerieDocumento . "'" : "";
        $cond_numero = $iNumeroDocumento != "-" ? "AND VC.ID_Numero_Documento = '" . $iNumeroDocumento . "'" : "";
        $cond_estado_documento = $iEstado != "0" ? 'AND VC.Nu_Estado = ' . $iEstado : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";
        $cond_tipo_recepcion_cliente = $iTipoRecepcionCliente != "0" ? 'AND VC.Nu_Tipo_Recepcion = ' . $iTipoRecepcionCliente : "";
        $cond_estado_lavado = $iEstadoLavado != "0" ? 'AND VC.Nu_Estado_Lavado = ' . $iEstadoLavado : "";

        $query = "
SELECT DISTINCT
 VC.ID_Empresa,
 VC.ID_Documento_Cabecera,
 EMPLE.No_Entidad AS No_Empleado,
 TDESTADORECEP.No_Descripcion AS No_Tipo_Recepcion,
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
 TDESTADO.No_Descripcion AS No_Estado,
 TDESTADO.No_Class AS No_Class_Estado,
 TDESTADOLAVA.No_Descripcion AS No_Estado_Lavado,
 TDESTADOLAVA.No_Class AS No_Class_Estado_Lavado,
 VC.Nu_Estado,
 TDESTADOENVIOPEDIDOLAVA.No_Descripcion AS No_Estado_Envio_Pedido_Lavado,
 TDESTADOENVIOPEDIDOLAVA.No_Class AS No_Class_Estado_Envio_Pedido_Lavado,
 VC.Nu_Estado_Lavado,
 TRALAV.No_Entidad AS No_Entidad_Lavado,
 VC.Nu_Estado_Lavado_Recepcion_Cliente,
 TDESTADOLAVARECEP.No_Descripcion AS No_Estado_Lavado_Recepcion_Cliente,
 TDESTADOLAVARECEP.No_Class AS No_Class_Estado_Lavado_Recepcion_Cliente,
 CLIRECEPLAVA.No_Entidad AS No_Entidad_Recepcion_Lavado,
 CLI.ID_Entidad,
 DDEL.Txt_Final_Prelavado,
 DDEL.Txt_Planchado,
 DDEL.Txt_Doblado,
 DDEL.Txt_Embolsado
FROM
 documento_cabecera AS VC
 LEFT JOIN documento_detalle_estado_lavado AS DDEL ON(DDEL.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
 JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK = VC.ID_Serie_Documento_PK)
 JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
 JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
 LEFT JOIN entidad AS TRALAV ON(TRALAV.ID_Entidad = DDEL.ID_Entidad_Lavado)
 LEFT JOIN entidad AS CLIRECEPLAVA ON(CLIRECEPLAVA.ID_Entidad = VC.ID_Mesero)
 JOIN matricula_empleado AS MEMPLE ON(VC.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
 JOIN entidad AS EMPLE ON(MEMPLE.ID_Entidad = EMPLE.ID_Entidad)
 JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
 JOIN tabla_dato AS TDESTADO ON(TDESTADO.Nu_Valor = VC.Nu_Estado AND TDESTADO.No_Relacion = 'Tipos_EstadoDocumento')
 JOIN tabla_dato AS TDESTADORECEP ON(TDESTADORECEP.Nu_Valor = VC.Nu_Tipo_Recepcion AND TDESTADORECEP.No_Relacion = 'Tipos_Recepcion')
 JOIN tabla_dato AS TDESTADOENVIOPEDIDOLAVA ON(TDESTADOENVIOPEDIDOLAVA.Nu_Valor = VC.Nu_Transporte_Lavanderia_Hoy AND TDESTADOENVIOPEDIDOLAVA.No_Relacion = 'Tipos_EstadoEnvioPedidoLavado')
 JOIN tabla_dato AS TDESTADOLAVA ON(TDESTADOLAVA.Nu_Valor = VC.Nu_Estado_Lavado AND TDESTADOLAVA.No_Relacion = 'Tipos_EstadoLavado')
 JOIN tabla_dato AS TDESTADOLAVARECEP ON(TDESTADOLAVARECEP.Nu_Valor = VC.Nu_Estado_Lavado_Recepcion_Cliente AND TDESTADOLAVARECEP.No_Relacion = 'Tipos_EstadoLavadoRecepcionCliente')
WHERE
 VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
 AND VC.ID_Tipo_Asiento = 1
 AND VC.Nu_Tipo_Recepcion = 2
 AND VC.Nu_Transporte_Lavanderia_Hoy IN(1,2)
 AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
 " . $cond_tipo . "
 " . $cond_serie . "
 " . $cond_numero . "
 " . $cond_estado_documento . "
 " . $cond_tipo_recepcion_cliente . "
 " . $cond_estado_lavado . "
 " . $cond_cliente . "
ORDER BY
 VC.Fe_Emision_Hora DESC,
 VC.ID_Tipo_Documento DESC,
 VC.ID_Serie_Documento DESC,
 CONVERT(VC.ID_Numero_Documento, SIGNED INTEGER) DESC
LIMIT 100;";
        
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

	public function cambiarEstadoLavado($arrPost){
        $this->db->trans_begin();
        
		foreach ($arrPost['arrIdDocumentoCabecera'] as $key => $value){
            $arrCambiarEstadoDocumento[] = array(
              'ID_Documento_Cabecera' => $key,
              'Nu_Estado_Lavado' => 16,
              'ID_Transporte_Sede_Planta' => $arrPost['ID_Transporte_Sede_Planta']
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

	public function entregarPedidoLavado($arrPost){
        $this->db->trans_begin();
        //ID_Comision => es el id del transporte del delivery que esta llevando su pedido al cliente
        $data = array( 'ID_Comision' => $arrPost['iTransporteDeliveryCliente'], 'Nu_Estado_Lavado_Recepcion_Cliente' => $arrPost['iEstadoLavadoRecepcionCliente'] );
        if ( isset($arrPost['iCrearEntidad']) && $arrPost['iCrearEntidad'] == 0 ) {// 0 Crear entidad recepcion lavado cliente = 9
            $arrEntidadLavado = array(
                'ID_Empresa' => $this->empresa->ID_Empresa,
                'ID_Organizacion' => $this->empresa->ID_Organizacion,
                'Nu_Tipo_Entidad' => 9,
                'ID_Tipo_Documento_Identidad' => 1,
                'No_Entidad' => $arrPost['sNombreRecepcion'],
            );
            $this->db->insert('entidad', $arrEntidadLavado);
            $iIdEntidad = $this->db->insert_id();
            $data = array( 'ID_Comision' => $arrPost['iTransporteDeliveryCliente'], 'Nu_Estado_Lavado_Recepcion_Cliente' => $arrPost['iEstadoLavadoRecepcionCliente'], 'ID_Mesero' => $iIdEntidad );
        }

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
            );
            $this->db->insert('documento_medio_pago', $documento_medio_pago);
            $data = array_merge( $data, array( 'Ss_Total_Saldo' => 0 ) );
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
