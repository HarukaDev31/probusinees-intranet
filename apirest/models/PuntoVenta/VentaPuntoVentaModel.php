<?php
class VentaPuntoVentaModel extends CI_Model{
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
        $iTipoRecepcionCliente=$arrParams['iTipoRecepcionCliente'];
        $iEstadoPago=$arrParams['iEstadoPago'];
        $sGlosa=$arrParams['sGlosa'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND VC.ID_Entidad = ' . $iIdCliente : "";
        $cond_tipo_recepcion_cliente = $iTipoRecepcionCliente != "0" ? 'AND VC.Nu_Tipo_Recepcion = ' . $iTipoRecepcionCliente : "";
		$cond_glosa = $sGlosa != "-" ? "AND VC.Txt_Glosa LIKE '%" . $sGlosa . "%'" : "";
        
        $cond_fecha_matricula_empleado = "AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'";
        if ( $iTipoConsultaFecha=='0' ) {//0=Actual
            $cond_fecha_matricula_empleado = "
AND VC.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . "
AND VC.Fe_Emision_Hora>='" . $this->session->userdata['arrDataPersonal']['arrData'][0]->Fe_Matricula . "'";
        }

        $cond_estado_pago = '';
        if ( $iEstadoPago == "1" )// Pendiente
            $cond_estado_pago = 'AND VC.Ss_Total_Saldo > 0.00';
        else if ( $iEstadoPago == "2" )// Cancelado
			$cond_estado_pago = 'AND VC.Ss_Total_Saldo = 0.00';
			
        $query = "SELECT
VC.ID_Empresa,
TD.No_Tipo_Documento_Breve,
EMPLE.No_Entidad AS No_Empleado,
VC.ID_Documento_Cabecera,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TD.Nu_Sunat_Codigo,
SD.Nu_Cantidad_Caracteres,
VC.Fe_Emision,
VC.Fe_Emision_Hora,
VC.Fe_Vencimiento,
'' AS ID_Moneda,
'' AS No_Signo,
'' AS Nu_Sunat_Codigo_Moneda,
CLI.ID_Entidad,
CLI.ID_Tipo_Documento_Identidad,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
CLI.Txt_Email_Entidad,
CLI.Nu_Celular_Entidad,
CLI.Nu_Estado AS Nu_Estado_Entidad,
'' AS Ss_Tipo_Cambio,
'' AS Ss_Tipo_Cambio_Modificar,
VC.Ss_Total,
VC.Ss_Total_Saldo,
VC.Nu_Estado,
VC.Nu_Estado_Lavado,
VC.Nu_Estado_Lavado_Recepcion_Cliente,
VC.Nu_Tipo_Recepcion,
VC.Fe_Entrega,
VC.ID_Transporte_Delivery,
VC.Nu_Descargar_Inventario,
VC.Txt_Url_CDR,
VC.Txt_Url_PDF,
VC.Nu_Transporte_Lavanderia_Hoy,
VC.Txt_Glosa,
VC.Txt_Garantia,
VC.Ss_Detraccion
FROM
documento_cabecera AS VC
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN matricula_empleado AS MEMPLE ON(VC.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
JOIN entidad AS EMPLE ON(MEMPLE.ID_Entidad = EMPLE.ID_Entidad)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
" . $cond_fecha_matricula_empleado . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_cliente . "
" . $cond_tipo_recepcion_cliente . "
" . $cond_estado_pago . "
" . $cond_glosa . "
ORDER BY
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
            'sMessage' => 'No se encontráron registro',
        );
    }

	public function cobrarVenta($arrPost){
		if ( empty($arrPost['fPagoCliente']) ) {
			return array('sStatus' => 'danger', 'sMessage' => 'No existe pago');
		}  else if ( !isset($this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado) ) {
			return array('sStatus' => 'danger', 'sMessage' => 'Debe aperturar caja para cobrar, sin sesión');
		} else {
			$this->db->trans_begin();

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
					'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
					'ID_Documento_Medio_Pago_Enlace' => 1,//$arrPost['iIdDocumentoMedioPago'] = lo uso para lavanería pero no tiene lógica, xq tu tienes que hacer referencia al documento de cabecera los abonos no al medio de pago
				);
				$this->db->insert('documento_medio_pago', $documento_medio_pago);
				//PAGAR DETRACCION
				if(isset($arrPost['iCobrarModalDetraccion']) && $arrPost['iCobrarModalDetraccion']==0) {
					$data = array( 'Ss_Total_Saldo' => $arrPost['fSaldoCliente'] - $arrPost['fPagoCliente'] );
				}
			}
			
			if(isset($data) && !empty($data)) {
				$where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabecera'] );
				$this->db->update('documento_cabecera', $data, $where);
			}

			//PAGAR DETRACCION
			if(isset($arrPost['iCobrarModalDetraccion']) && $arrPost['iCobrarModalDetraccion']==1) {
				$where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabecera'] );
				$data = array('Ss_Detraccion' => 0.00, 'Fe_Detraccion' => dateNow('fecha'));
				$this->db->update('documento_cabecera', $data, $where);
			}

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al cambiar procesar');
			} else {
				$this->db->trans_commit();
				return array('sStatus' => 'success', 'sMessage' => 'Procesado');
			}
		}
    }
    
	public function facturarOrdenLavanderia($arrPost){
		if (!isset($this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado))
			return array('sStatus' => 'danger', 'sMessage' => 'Debes aperturar caja para facturar');

		$objAlmacen = $this->db->query("SELECT ALMA.ID_Almacen, ALMA.No_Almacen FROM matricula_empleado AS ME JOIN almacen AS ALMA ON(ALMA.ID_Almacen = ME.ID_Almacen) WHERE ME.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . " LIMIT 1")->row();

		if ( $objAlmacen->ID_Almacen != $this->session->userdata['almacen']->ID_Almacen )
			return array('sStatus' => 'danger', 'sMessage' => 'Debes seleccionar el Almacén: ' . $objAlmacen->No_Almacen);
			
		$this->db->trans_begin();

		$query = "SELECT
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Empresa=" . $this->user->ID_Empresa . "
AND ID_Organizacion=" . $this->user->ID_Organizacion . "
AND ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . "
AND ID_Tipo_Documento=" . $arrPost['ID_Tipo_Documento'] . "
AND Nu_Estado=1
AND ID_POS=".$this->session->userdata['arrDataPersonal']['arrData'][0]->ID_POS." LIMIT 1";
		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Nota de Venta';
		if ( $arrPost['ID_Tipo_Documento'] == '4' )
			$sTidoDocumento = 'Boleta';
		else if ( $arrPost['ID_Tipo_Documento'] == '3' )
			$sTidoDocumento = 'Factura';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) ) {
			return array('sStatus' => 'danger', 'sMessage' => 'Deben configurar serie para ' . $sTidoDocumento . ', no existe');
		}

		if ($this->db->query("SELECT COUNT(*) AS existe FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND ID_Tipo_Documento = " . $arrPost['ID_Tipo_Documento'] . " AND ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			return array('sStatus' => 'warning', 'sMessage' => 'Ya existe venta ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . ' modificar correlativo en la opción Ventas -> Series' );
		} else {
			$Fe_Emision = ToDate($arrPost['Fe_Emision_Convertir']);
			$Fe_Vencimiento = ToDate($arrPost['Fe_Vencimiento_Convertir']);

			//verificar documento_medio_pago y luego unir medio_pago
			$sql_mdp = "SELECT MP.Nu_Tipo FROM documento_medio_pago AS MDP JOIN medio_pago AS MP ON(MDP.ID_Medio_Pago = MP.ID_Medio_Pago) WHERE ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'];
			$arrResponseSQLDMP = $this->db->query($sql_mdp);
			foreach ($arrResponseSQLDMP->result() as $row) {
				if($row->Nu_Tipo==1){//1=cREDITO
					$dEmision = new DateTime($Fe_Emision . " 00:00:00");
					$dVencimiento_Comparar = new DateTime($Fe_Vencimiento . " 00:00:00");
					if ($dVencimiento_Comparar<=$dEmision) {
						$this->db->trans_rollback();
						return array('sStatus' => 'warning', 'sMessage' => 'La F. Vencimiento debe de ser mayor a la fecha de hoy > ' . ToDateBD($Fe_Emision));
					}
				}
			}

			$Nu_Correlativo = 0;
			$Fe_Year = ToYear($Fe_Emision);
			$Fe_Month = ToMonth($Fe_Emision);
			$arrCorrelativoPendiente = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento_pendiente WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' ORDER BY Nu_Correlativo DESC LIMIT 1")->result();
			
			if ( count($arrCorrelativoPendiente) > 0 ){
				$Nu_Correlativo = $arrCorrelativoPendiente[0]->Nu_Correlativo;
				
				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
				$this->db->where('ID_Tipo_Asiento', 1);
				$this->db->where('Fe_Year', $Fe_Year);
				$this->db->where('Fe_Month', $Fe_Month);
				$this->db->where('Nu_Correlativo', $Nu_Correlativo);
		        $this->db->delete('correlativo_tipo_asiento_pendiente');
			} else {
				if($this->db->query("SELECT COUNT(*) AS existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
					$sql_correlativo_libro_sunat = "UPDATE
correlativo_tipo_asiento
SET
Nu_Correlativo=Nu_Correlativo + 1
WHERE
ID_Empresa=" . $this->user->ID_Empresa . "
AND ID_Tipo_Asiento=1
AND Fe_Year='" . $Fe_Year. "'
AND Fe_Month='" . $Fe_Month . "'";
					$this->db->query($sql_correlativo_libro_sunat);
				} else {
					$sql_correlativo_libro_sunat = "INSERT INTO correlativo_tipo_asiento(
ID_Empresa,
ID_Tipo_Asiento,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->user->ID_Empresa . ",
1,
'" . $Fe_Year . "',
'" . $Fe_Month . "',
1
);";
					$this->db->query($sql_correlativo_libro_sunat);
				}
				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
			}

			$Last_ID_Entidad = $arrPost['AID'];
			// Cliente ya esta registrado en BD
			if ( !empty($Last_ID_Entidad) ){
				$arrClienteBD = $this->db->query("SELECT Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad = " . $Last_ID_Entidad . " LIMIT 1")->result();
				if ( $arrClienteBD[0]->Txt_Email_Entidad != $arrPost['Txt_Email_Entidad'] ) {
					$sql = "UPDATE entidad SET Txt_Email_Entidad = '" . $arrPost['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $Last_ID_Entidad;
					$this->db->query($sql);
				} // /. if cambiar celular o correo
			} // /. if cliente existe en BD

			if ( empty($arrPost['AID']) ) {//3=Cliente nuevo
				$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrPost['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrPost['Nu_Documento_Identidad'] . "' AND No_Entidad = '" . $arrPost['No_Entidad'] . "' LIMIT 1";
				$arrResponseSQL = $this->db->query($query);
				if ( $arrResponseSQL->num_rows() > 0 ){
					$arrData = $arrResponseSQL->result();
					$Last_ID_Entidad = $arrData[0]->ID_Entidad;
				} else {
					$arrCliente = array(
						'ID_Empresa' => $this->empresa->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'Nu_Tipo_Entidad' => 0,//0=Cliente
						'ID_Tipo_Documento_Identidad' => $arrPost['ID_Tipo_Documento_Identidad'],
						'Nu_Documento_Identidad' => $arrPost['Nu_Documento_Identidad'],
						'No_Entidad' => $arrPost['No_Entidad'],
						'Nu_Estado' => $arrPost['Nu_Estado_Entidad'],
						'Txt_Email_Entidad'	=> $arrPost['Txt_Email_Entidad'],
					);
					$this->db->insert('entidad', $arrCliente);
					$Last_ID_Entidad = $this->db->insert_id();
				}
            }// ./ if cliente nuevo

			$sql = "UPDATE documento_cabecera
SET
ID_Tipo_Documento = '" . $arrPost['ID_Tipo_Documento'] . "',
ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "',
ID_Serie_Documento_PK = " . $arrSerieDocumento->ID_Serie_Documento_PK . ",
ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "',
ID_Entidad = " . $Last_ID_Entidad . ",
Fe_Emision = '" . $Fe_Emision . "',
Fe_Vencimiento = '" . $Fe_Vencimiento . "',
Nu_Correlativo = " . $Nu_Correlativo . "
WHERE ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'];
			$this->db->query($sql);
			
			$arrDetalle = $this->db->query("SELECT * FROM documento_detalle WHERE ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'])->result();
			$arrImpuestoDetalle = array();
			$arrImpuesto = array();

			//Validacion previa si algun item tiene descuento total y sus impuestos son diferentes
			$arrCabeceraVenta = $this->db->query("SELECT Ss_Descuento FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'] . " LIMIT 1")->row();
			if ( $arrPost['ID_Tipo_Documento'] != '2' && (double)$arrCabeceraVenta->Ss_Descuento > 0.00) {
				foreach($arrDetalle as $row) {
					$arrImpuestoDetalle = $this->db->query("SELECT ID_Impuesto FROM impuesto_cruce_documento WHERE ID_Impuesto_Cruce_Documento = " . $row->ID_Impuesto_Cruce_Documento . " LIMIT 1")->row();
					$arrImpuesto = $this->db->query("SELECT Nu_Tipo_Impuesto FROM impuesto WHERE ID_Impuesto = " . $arrImpuestoDetalle->ID_Impuesto . " LIMIT 1")->row();

					if ($arrImpuesto->Nu_Tipo_Impuesto!='1') {
						$this->db->trans_rollback();
						return array('sStatus' => 'danger', 'sMessage' => 'No se puede brindar descuento TOTAL solo por ÍTEM. Si es IGV si se puede por TOTAL o ÍTEM.');
					}		
				}
			}

			foreach($arrDetalle as $row) {
            	$arrImpuestoDetalle = $this->db->query("SELECT Ss_Impuesto FROM impuesto_cruce_documento WHERE ID_Impuesto_Cruce_Documento = " . $row->ID_Impuesto_Cruce_Documento . " LIMIT 1")->row();

				$fSubtotal = round($row->Ss_Total / $arrImpuestoDetalle->Ss_Impuesto, 2);
				$sql = "UPDATE documento_detalle SET Ss_SubTotal = " . $fSubtotal . ", Ss_Impuesto=" . ($row->Ss_Total - $fSubtotal) . " WHERE ID_Documento_Detalle=" . $row->ID_Documento_Detalle;
				$this->db->query($sql);				
			}

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar venta');
			} else {
				if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['ID_Tipo_Documento'] != '2') {// cancelado y 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrPost['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();

					$arrParams = array(
						'iCodigoProveedorDocumentoElectronico' => 1,
						'iEstadoVenta' => 6,//6=Completado
						'iIdDocumentoCabecera' => $arrPost['iIdDocumentoCabecera'],
						'sEmailCliente' => ( !isset($arrPost['Txt_Email_Entidad']) ? '' : $arrPost['Txt_Email_Entidad'] ),
						'sTipoRespuesta' => 'php',
					);
					$arrResponseFE = array();
					
					if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
						$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronico( $arrParams );
						$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
						if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
							return $arrResponseFEMensaje;
						}
						if ( $arrResponseFE['sStatus'] != 'success' ) {
							return $arrResponseFE;
						}
					} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 ) {//Facturador sunat
						$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoSunat( $arrParams );
						$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
						if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
							return $arrResponseFEMensaje;
						}
						if ( $arrResponseFE['sStatus'] != 'success' ) {
							return $arrResponseFE;
						}
					}
						
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $arrPost['iIdDocumentoCabecera'],
						'arrResponseFE' => $arrResponseFE,
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['ID_Tipo_Documento'] == '2') {// cancelado y 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrPost['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();
					
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $arrPost['iIdDocumentoCabecera'],
						'arrResponseFE' => '',
					);
				} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['ID_Tipo_Documento'] != '2') {// pago pendiente y diferente 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrPost['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
						'iIdDocumentoCabecera' => $arrPost['iIdDocumentoCabecera'],
						'arrResponseFE' => '',
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['ID_Tipo_Documento'] == '2') {// pago pendiente y 2 = Documento interno
					$this->db->trans_rollback();
					
					return array(
						'sStatus' => 'danger',
						'sMessage' => 'No se guardo venta por falta de pago',
						'iIdDocumentoCabecera' => $arrPost['iIdDocumentoCabecera'],
						'arrResponseFE' => '',
					);
				}
			}
		}// if - else validacion si existe comprobante
    }
    
	public function eliminarVenta($arrParamsDelete){		
		$this->db->where('ID_Documento_Cabecera', $arrParamsDelete['iIdDocumentoCabecera']);
		$this->db->delete('documento_detalle');
		
		$this->db->where('ID_Documento_Cabecera', $arrParamsDelete['iIdDocumentoCabecera']);
        $this->db->delete('documento_medio_pago');
        
        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera=".$arrParamsDelete['iIdDocumentoCabecera'];
        $arrDetalle = $this->db->query($query)->result();
        foreach ($arrDetalle as $row) {
            if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
                $where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
                $Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto)->row()->Qt_Producto;
                
                $stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
                $this->db->update('stock_producto', $stock_producto, $where);

				//actualizar costo promedio
				$arrParamsCostoPromedioStock = array(
					'ID_Almacen' => $row->ID_Almacen,
					'ID_Producto' => $row->ID_Producto
				);
				$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
            }
        }
        
        $this->db->where('ID_Documento_Cabecera', $arrParamsDelete['iIdDocumentoCabecera']);
        $this->db->delete('movimiento_inventario');
        
		$this->db->where('ID_Documento_Cabecera', $arrParamsDelete['iIdDocumentoCabecera']);
        $this->db->delete('documento_cabecera');
	}

	public function cobrarVentaMasiva($arrPost){
		if ( empty($arrPost['fPagoCliente']) ) {
			return array('sStatus' => 'danger', 'sMessage' => 'No existe pago');
		} else if ( !isset($this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado) ) {
			return array('sStatus' => 'danger', 'sMessage' => 'Debe aperturar caja para cobrar, sin sesión');
		} else {
			$this->db->trans_begin();

			$fSaldoOperacion = $arrPost['fPagoCliente'];
			$iLastKey = 0;
			$iKeyCobrado = 0;
			foreach ($arrPost['arrIdDocumentoCabecera'] as $key => $value){
				$fSaldo = $this->db->query("SELECT Ss_Total_Saldo FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $key)->row()->Ss_Total_Saldo;
				$iKeyCobrado = 0;
				if ( $fSaldoOperacion >= $fSaldo && $fSaldoOperacion > 0.00) {
					$fSaldoOperacion -= $fSaldo;
					$documento_medio_pago[] = array(
						'ID_Empresa'			=> $this->empresa->ID_Empresa,
						'ID_Documento_Cabecera'	=> $key,
						'ID_Medio_Pago'		    => $arrPost['iFormaPago'],
						'Nu_Transaccion'		=> $arrPost['iNumeroTransaccion'],
						'Nu_Tarjeta'		    => $arrPost['iNumeroTarjeta'],
						'Ss_Total'		        => $fSaldo,
						'ID_Tipo_Medio_Pago' => isset($arrPost['iTipoMedioPago']) ? $arrPost['iTipoMedioPago'] : 0,
						'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
						'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
						'ID_Documento_Medio_Pago_Enlace' => 1,
					);

					$data = array( 'Ss_Total_Saldo' => 0 );
					$where = array( 'ID_Documento_Cabecera' => $key );
					$this->db->update('documento_cabecera', $data, $where);
					
					$iKeyCobrado = $key;
				}

				if ( $fSaldo > $fSaldoOperacion && $iKeyCobrado != $key) {//Saldo es mayor que el importe que queda restante muere la operación
					$fSaldoMayor = ($fSaldo - $fSaldoOperacion);
					$documento_medio_pago[] = array(
						'ID_Empresa'			=> $this->empresa->ID_Empresa,
						'ID_Documento_Cabecera'	=> $key,
						'ID_Medio_Pago'		    => $arrPost['iFormaPago'],
						'Nu_Transaccion'		=> $arrPost['iNumeroTransaccion'],
						'Nu_Tarjeta'		    => $arrPost['iNumeroTarjeta'],
						'Ss_Total'		        => $fSaldoOperacion,
						'ID_Tipo_Medio_Pago' => isset($arrPost['iTipoMedioPago']) ? $arrPost['iTipoMedioPago'] : 0,
						'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
						'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
						'ID_Documento_Medio_Pago_Enlace' => 1,
					);

					$data = array( 'Ss_Total_Saldo' => $fSaldoMayor );
					$where = array( 'ID_Documento_Cabecera' => $key );
					$this->db->update('documento_cabecera', $data, $where);
					
					$fSaldoOperacion = 0;
					break;
				}

				$iLastKey = $key;
			}// ./ for each

			if ( empty($documento_medio_pago) ){
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Debes de seleccionar más de 1 registro');
			}

			$this->db->insert_batch('documento_medio_pago', $documento_medio_pago);

			if ( $fSaldoOperacion > 0.00) {
				$fSaldo = $this->db->query("SELECT Ss_Total_Saldo FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $iLastKey)->row()->Ss_Total_Saldo;
				$documento_medio_pago = array(
					'ID_Empresa'			=> $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera'	=> $iLastKey,
					'ID_Medio_Pago'		    => $arrPost['iFormaPago'],
					'Nu_Transaccion'		=> $arrPost['iNumeroTransaccion'],
					'Nu_Tarjeta'		    => $arrPost['iNumeroTarjeta'],
					'Ss_Total'		        => $fSaldoOperacion,
					'ID_Tipo_Medio_Pago' => isset($arrPost['iTipoMedioPago']) ? $arrPost['iTipoMedioPago'] : 0,
					'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
					'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
					'ID_Documento_Medio_Pago_Enlace' => 1,
				);
				$this->db->insert('documento_medio_pago', $documento_medio_pago);
				
				$this->db->query("UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $fSaldoOperacion . " WHERE ID_Documento_Cabecera=" . $iLastKey);
			}

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al cambiar procesar');
			} else {
				$this->db->trans_commit();
				return array('sStatus' => 'success', 'sMessage' => 'Procesado');
			}
			
		}
    }

    public function getDocumentoVenta($ID){
		return $this->db->query("SELECT CLI.Txt_Direccion_Entidad, VC.Txt_Glosa FROM documento_cabecera AS VC JOIN entidad AS CLI ON(VC.ID_Entidad = CLI.ID_Entidad) WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1")->row();
	}

	public function modificarVenta($arrPost){
		$this->db->trans_begin();

		$Txt_Direccion_Delivery = $arrPost['Txt_Direccion_Delivery'];
		$ID_Transporte_Delivery = $arrPost['ID_Transporte_Delivery'];
		if( $arrPost['Nu_Tipo_Recepcion'] != 6) {
			$Txt_Direccion_Delivery = '';
			$ID_Transporte_Delivery = 0;
		}

		$data = array(
			'Nu_Transporte_Lavanderia_Hoy' => $arrPost['Nu_Transporte_Lavanderia_Hoy'],
			'Nu_Tipo_Recepcion' => $arrPost['Nu_Tipo_Recepcion'],
			'ID_Transporte_Delivery' => $ID_Transporte_Delivery,
			'Txt_Direccion_Delivery' => $Txt_Direccion_Delivery,
			'Fe_Entrega' => ToDate($arrPost['Fe_Entrega']),
			'Fe_Vencimiento' => ToDate($arrPost['Fe_Vencimiento']),
			'Txt_Glosa' => $arrPost['Txt_Glosa'],
		);
		
		if($arrPost['ID_Tipo_Documento-Modificar']==2){
			$Fe_Emision_Hora = $this->db->query("SELECT Fe_Emision_Hora FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $arrPost['ID_Documento_Cabecera-Modificar'] . " LIMIT 1")->row()->Fe_Emision_Hora;
			$arrFeEmisionHora = explode(' ', $Fe_Emision_Hora);
			$data = array_merge($data, array(
				'Fe_Emision' => ToDate($arrPost['Fe_Emision_Interno']),
				'Fe_Emision_Hora' => ToDate($arrPost['Fe_Emision_Interno']) . ' ' . $arrFeEmisionHora[1],
			));
		}
		
		$where = array('ID_Documento_Cabecera' => $arrPost['ID_Documento_Cabecera-Modificar']);
		$this->db->update('documento_cabecera', $data, $where);

		$data = array('Txt_Direccion_Entidad' => $Txt_Direccion_Delivery);
		$where = array('ID_Entidad' => $arrPost['ID_Entidad-Modificar']);
		$this->db->update('entidad', $data, $where);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al modificar');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Registro modificado');
		}
    }
}
