<?php
class POSFarmaciaModel extends CI_Model{	
	public function __construct(){
		parent::__construct();
	}
	
	public function agregarVentaPos($arrPost){
		$objAlmacen = $this->db->query("SELECT ALMA.ID_Almacen, ALMA.No_Almacen FROM matricula_empleado AS ME JOIN almacen AS ALMA ON(ALMA.ID_Almacen = ME.ID_Almacen) WHERE ME.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . " LIMIT 1")->row();

		if ($objAlmacen->ID_Almacen != $this->session->userdata['almacen']->ID_Almacen )
			return array('sStatus' => 'danger', 'sMessage' => 'Debes seleccionar el Almacén: ' . $objAlmacen->No_Almacen);

		if (!isset($_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado))
			return array('sStatus' => 'danger', 'sMessage' => 'No existe sesión, volver aperturar caja');

		$query = "SELECT
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . "
AND ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND ID_Almacen=" . $arrPost['arrCabecera']['iIdAlmacen'] . "
AND ID_Tipo_Documento=" . $arrPost['arrCabecera']['ID_Tipo_Documento'] . "
AND Nu_Estado=1
AND ID_POS=".$this->session->userdata['arrDataPersonal']['arrData'][0]->ID_POS." LIMIT 1";
		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Nota de Venta';
		if ( $arrPost['arrCabecera']['ID_Tipo_Documento'] == '4' )
			$sTidoDocumento = 'Boleta';
		else if ( $arrPost['arrCabecera']['ID_Tipo_Documento'] == '3' )
			$sTidoDocumento = 'Factura';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) )
			return array('sStatus' => 'danger', 'sMessage' => 'Configurar en Ventas > Series para ' . $sTidoDocumento . ' y Caja ' . $this->session->userdata['arrDataPersonal']['arrData'][0]->Nu_Caja . ', no existe');

		if ($this->db->query("SELECT COUNT(*) AS existe FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND ID_Tipo_Documento = " . $arrPost['arrCabecera']['ID_Tipo_Documento'] . " AND ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			return array('sStatus' => 'warning', 'sMessage' => 'Ya existe venta ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . ' modificar correlativo en la opción Ventas -> Series' );
		} else {
			$this->db->trans_begin();

			$Last_ID_Entidad = $arrPost['arrCabecera']['ID_Entidad'];
			// Cliente ya esta registrado en BD
			if ( !empty($Last_ID_Entidad) ){
				$arrClienteBD = $this->db->query("SELECT Txt_Direccion_Entidad, Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad = " . $Last_ID_Entidad . " LIMIT 1")->result();
				$Nu_Celular_Entidad = '';
				if ( strlen($arrPost['arrCliente']['Nu_Celular_Entidad']) == 11){
					$Nu_Celular_Entidad = explode(' ', $arrPost['arrCliente']['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}
				if ( (!empty($arrPost['arrCabecera']['sDireccionDelivery']) && $arrClienteBD[0]->Txt_Direccion_Entidad != $arrPost['arrCabecera']['sDireccionDelivery']) || (!empty($Nu_Celular_Entidad) && $arrClienteBD[0]->Nu_Celular_Entidad != $Nu_Celular_Entidad) || (!empty($arrPost['arrCliente']['Txt_Email_Entidad']) && $arrClienteBD[0]->Txt_Email_Entidad != $arrPost['arrCliente']['Txt_Email_Entidad']) ) {
					$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrPost['arrCabecera']['sDireccionDelivery'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "', Txt_Email_Entidad = '" . $arrPost['arrCliente']['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $Last_ID_Entidad;
					$this->db->query($sql);
				}// /. if cambiar celular o correo
			} // /. if cliente existe en BD

			if ( empty($arrPost['arrCabecera']['ID_Entidad']) && (!empty($arrPost['arrCliente']['Nu_Documento_Identidad']) || !empty($arrPost['arrCliente']['No_Entidad'])) ) {//3=Cliente nuevo
				$iTipoDocumentoIdentidad = $arrPost['arrCliente']['ID_Tipo_Documento_Identidad'];
				$sNumeroDocumentoIdentidad = trim($arrPost['arrCliente']['Nu_Documento_Identidad']);
				if ( $iTipoDocumentoIdentidad == '2' && strlen($sNumeroDocumentoIdentidad) == '8' )
					$iTipoDocumentoIdentidad = '2';

				if ( ($iTipoDocumentoIdentidad == '1' || $iTipoDocumentoIdentidad == '2') && empty($sNumeroDocumentoIdentidad) ) {
					$iTipoDocumentoIdentidad='1';
					$sNumeroDocumentoIdentidad='0';
				}

				$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $iTipoDocumentoIdentidad . " AND Nu_Documento_Identidad = '" . $sNumeroDocumentoIdentidad . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($arrPost['arrCliente']['No_Entidad']) . "' LIMIT 1";
				$arrResponseSQL = $this->db->query($query);
				if ( $arrResponseSQL->num_rows() > 0 ){
					$arrData = $arrResponseSQL->result();
					$Last_ID_Entidad = $arrData[0]->ID_Entidad;
				} else {
					$Nu_Celular_Entidad = '';
					if (strlen($arrPost['arrCliente']['Nu_Celular_Entidad']) == 11){
						$Nu_Celular_Entidad = explode(' ', $arrPost['arrCliente']['Nu_Celular_Entidad']);
						$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
					}

					$arrCliente = array(
						'ID_Empresa' => $this->empresa->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'Nu_Tipo_Entidad' => 0,//0=Cliente
						'ID_Tipo_Documento_Identidad' => $iTipoDocumentoIdentidad,
						'Nu_Documento_Identidad' => $sNumeroDocumentoIdentidad,
						'No_Entidad' => (!empty($arrPost['arrCliente']['No_Entidad']) ? $arrPost['arrCliente']['No_Entidad'] : $sNumeroDocumentoIdentidad),
						'Nu_Estado' => $arrPost['arrCliente']['Nu_Estado'],
						'Nu_Celular_Entidad' => $Nu_Celular_Entidad,
						'Txt_Email_Entidad'	=> $arrPost['arrCliente']['Txt_Email_Entidad'],
						'Txt_Direccion_Entidad' => $arrPost['arrCabecera']['sDireccionDelivery']
					);
					if ($this->db->insert('entidad', $arrCliente) > 0) {
						$Last_ID_Entidad = $this->db->insert_id();
					} else {
						$this->db->trans_rollback();
						return array('sStatus' => 'danger', 'sMessage' => 'No se puede registrar Cliente');
					}
				}
			}// ./ if cliente nuevo

			if( ($arrPost['arrCabecera']['ID_Tipo_Documento'] == 2 || $arrPost['arrCabecera']['ID_Tipo_Documento'] == 4) && empty($arrPost['arrCliente']['Nu_Documento_Identidad']) && empty($arrPost['arrCliente']['No_Entidad']) )
				$Last_ID_Entidad = $this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado;
	
			if(empty($Last_ID_Entidad)) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Sin entidad');
			}

			//Generar venta
			$Nu_Correlativo = 0;
			$Fe_Year = dateNow('año');
			$Fe_Month = dateNow('mes');
			
			if ( $arrPost['arrCabecera']['ID_Tipo_Documento'] != '2' ) {
				// Obtener correlativo			
				if($this->db->query("SELECT COUNT(*) existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
					$sql_correlativo_libro_sunat = "UPDATE
correlativo_tipo_asiento
SET
Nu_Correlativo=Nu_Correlativo + 1
WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . "
AND ID_Tipo_Asiento=1
AND Fe_Year='" . $Fe_Year. "'
AND Fe_Month='" . $Fe_Month . "'";
				$this->db->query($sql_correlativo_libro_sunat);
			} else {
				$sql_correlativo_libro_sunat = "INSERT INTO correlativo_tipo_asiento (
ID_Empresa,
ID_Tipo_Asiento,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->empresa->ID_Empresa . ",
1,
'" . $Fe_Year . "',
'" . $Fe_Month . "',
1
);";
					$this->db->query($sql_correlativo_libro_sunat);
				}
				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
				// /. Obtener correlativo
			}// if validacion correlativo documento interno
			
			$fTotalxMP = 0.00;
			$fVuelto = 0.00;
			$fDetraccion = 0.00;
			foreach ($arrPost['arrFormaPago'] as $row) {
				$fTotalxMP += $row['Ss_Total'];
				if ( $fTotalxMP > $arrPost['arrCabecera']['Ss_Total'] )
					$fVuelto = $fTotalxMP - $arrPost['arrCabecera']['Ss_Total'];
				if ( $row['iTipoVista'] == '1' ){//Credito
					$fVuelto = $row['Ss_Total'];

					if ($_POST['arrCabecera']['Nu_Detraccion'] == '1' && $arrPost['arrCabecera']['Ss_Total'] > 700) {
						$fDetraccion = ($arrPost['arrCabecera']['Ss_Total'] * 0.12);
						$fDetraccion = round($fDetraccion, 0, PHP_ROUND_HALF_UP);
						$arrPost['arrCabecera']['Ss_Total_Saldo'] = ($arrPost['arrCabecera']['Ss_Total_Saldo'] - $fDetraccion);
					}
				}
			}

			//FECHA DE VENCIMIENTO
			$dVencimiento = $arrPost['arrCabecera']['Fe_Vencimiento'];
			$arrFechaVencimiento = explode('/', $dVencimiento);		
			$dVencimiento = dateNow('fecha');
			if(count($arrFechaVencimiento) == 3 && checkdate($arrFechaVencimiento[1], $arrFechaVencimiento[0], $arrFechaVencimiento[2]))
				$dVencimiento = ToDate($arrPost['arrCabecera']['Fe_Vencimiento']);

			$arrVentaCabecera = array(
				'ID_Empresa'				=> $this->empresa->ID_Empresa,
				'ID_Organizacion'			=> $this->empresa->ID_Organizacion,
				'ID_Almacen'			    => $this->session->userdata['almacen']->ID_Almacen,
				'ID_Entidad'				=> $Last_ID_Entidad,
				//'ID_Matricula_Empleado'	    => $arrPost['arrCabecera']['ID_Matricula_Empleado'],
				'ID_Matricula_Empleado'	    => $_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
				'ID_Tipo_Asiento'			=> 1,//Venta
				'ID_Tipo_Documento'			=> $arrPost['arrCabecera']['ID_Tipo_Documento'],
				'ID_Serie_Documento_PK'		=> $arrSerieDocumento->ID_Serie_Documento_PK,
				'ID_Serie_Documento'		=> $arrSerieDocumento->ID_Serie_Documento,
				'ID_Numero_Documento'		=> $arrSerieDocumento->Nu_Numero_Documento,
				'Fe_Emision'				=> dateNow('fecha'),
				'Fe_Emision_Hora'			=> dateNow('fecha_hora'),
				'ID_Moneda'					=> $arrPost['arrCabecera']['ID_Moneda'],//Soles
				'ID_Medio_Pago'				=> $arrPost['arrFormaPago'][0]['ID_Medio_Pago'],
				'Fe_Vencimiento'			=> $dVencimiento,
				'Fe_Periodo' => dateNow('fecha'),
				'Nu_Descargar_Inventario' => 1,
				'Ss_Total' => $arrPost['arrCabecera']['Ss_Total'],
				'Ss_Total_Saldo' => $arrPost['arrCabecera']['Ss_Total_Saldo'],
				'Ss_Vuelto' => $fVuelto,
				'Nu_Correlativo' => $Nu_Correlativo,
				'Nu_Estado' => 6,//Completado
				'Nu_Transporte_Lavanderia_Hoy' => 0,
				'Nu_Estado_Lavado' => 0,
				'Fe_Entrega' => ToDate($arrPost['arrCabecera']['Fe_Entrega']),
				'Nu_Tipo_Recepcion' => $arrPost['arrCabecera']['Nu_Tipo_Recepcion'],
				'Nu_Estado_Despacho_Pos' => ($arrPost['arrCabecera']['Nu_Tipo_Recepcion'] == 5 ? 3 : 0),
				'ID_Transporte_Delivery' => $arrPost['arrCabecera']['ID_Transporte_Delivery'],
				'Txt_Direccion_Delivery' => $arrPost['arrCabecera']['sDireccionDelivery'],
				'Txt_Glosa' => $arrPost['arrCabecera']['sGlosa'],
				'No_Formato_PDF' => 'TICKET',
				'Po_Descuento' => ($arrPost['arrCabecera']['iTipoDescuento'] == 2 && $arrPost['arrCabecera']['Ss_Descuento_Total_Input'] > 0.00 ? $arrPost['arrCabecera']['Ss_Descuento_Total_Input'] : 0.00),
				'Ss_Descuento' => $arrPost['arrCabecera']['Ss_Descuento_Total'],
				'Ss_Descuento_Impuesto' => $arrPost['arrCabecera']['Ss_Descuento_Impuesto'],
				'No_Orden_Compra_FE' => $arrPost['arrCabecera']['No_Orden_Compra_FE'],
				'No_Placa_FE' => $arrPost['arrCabecera']['No_Placa_FE'],
				'Txt_Garantia' => strtoupper($arrPost['arrCabecera']['Txt_Garantia']),
				'Nu_Detraccion' => $arrPost['arrCabecera']['Nu_Detraccion'],
				'ID_Canal_Venta_Tabla_Dato' => 0,//$arrPost['arrCabecera']['ID_Canal_Venta_Tabla_Dato']
				'Ss_Detraccion' => $fDetraccion,
			);
			
			if ( $_POST['arrCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($_POST['arrCabecera']['ID_Lista_Precio_Cabecera'])));

			$sTidoDocumento .= ($arrPost['arrCabecera']['ID_Tipo_Documento'] != 2 ? ' Electrónica' : '') .  ' - ' . $arrVentaCabecera['ID_Serie_Documento'] . ' - ' . $arrVentaCabecera['ID_Numero_Documento'];

			$this->db->insert('documento_cabecera', $arrVentaCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();
			
			// URL para enviar correo y para consultar por fuera sin session
			// base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/'
			if($arrVentaCabecera['ID_Tipo_Documento']==2) {//2=Nota de venta
				$sUrlPDFNotaVentaInternoLae = base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/' . $Last_ID_Documento_Cabecera;
				$sql = "UPDATE documento_cabecera SET Txt_Url_PDF='" . $sUrlPDFNotaVentaInternoLae . "' WHERE ID_Documento_Cabecera=" . $Last_ID_Documento_Cabecera;
				$this->db->query($sql);
			}

			$fSubTotalItem=0.00;
			$fImpuestoItem=0.00;
			foreach($arrPost['arrDetalle'] as $row) {
				$fSubTotalItem = ($arrPost['arrCabecera']['ID_Tipo_Documento'] != '2' ? $row['fSubtotalItem'] : $row['Ss_Total_Producto']);
				$fImpuestoItem = ($arrPost['arrCabecera']['ID_Tipo_Documento'] != '2' ? $row['fImpuestoItem'] : 0.00);

				$fCalculoTotal = round($row['Ss_Total_Producto'] / $row['fImpuestoConfigurado'], 6);
				if( $fSubTotalItem < $fCalculoTotal ) {
					$fSubTotalItem = $fCalculoTotal;
					$fImpuestoItem = ($row['Ss_Total_Producto'] - $fCalculoTotal);
				}

				$documento_detalle[] = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $row['ID_Producto'],
					'Qt_Producto' => ($row['Qt_Producto'] > 0.00 ? $this->security->xss_clean($row['Qt_Producto']) : 1),
					'Ss_Precio' => $row['Ss_Precio'],
					'Ss_SubTotal' => $fSubTotalItem,
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['fDescuentoPorcentajeItem'],
					'Txt_Nota' => $row['Txt_Nota'],
					'ID_Impuesto_Cruce_Documento' => $row['ID_Impuesto_Cruce_Documento'],
					'Ss_Impuesto' => $fImpuestoItem,
					'Ss_Total' => $row['Ss_Total_Producto'],
					'Nu_Estado_Lavado' => 0,
					'Ss_Icbper' => $row['fIcbperItem'],
				);
			}
			$this->db->insert_batch('documento_detalle', $documento_detalle);
			$iIdDocumentoDetalleFirst = $this->db->insert_id();

			foreach($arrPost['arrDetalle'] as $row) {
				if (!empty($arrPost['arrCabecera']['Nu_Lote_Vencimiento']) && !empty($arrPost['arrCabecera']['Fe_Lote_Vencimiento'])) {
					$documento_detalle_lote[] = array(
						'ID_Empresa' => $this->user->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
						'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
						'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
						'ID_Documento_Detalle' => $iIdDocumentoDetalleFirst,
						'Nu_Lote_Vencimiento' => $this->security->xss_clean($arrPost['arrCabecera']['Nu_Lote_Vencimiento']),
						'Fe_Lote_Vencimiento' => ToDate($this->security->xss_clean($arrPost['arrCabecera']['Fe_Lote_Vencimiento'])),
					);
					++$iIdDocumentoDetalleFirst;
				}
			}
			if ( isset($documento_detalle_lote) )
				$this->db->insert_batch('documento_detalle_lote', $documento_detalle_lote);

			foreach($arrPost['arrFormaPago'] as $row) {
				$documento_medio_pago[] = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
					'ID_Medio_Pago'	=> $this->security->xss_clean($row['ID_Medio_Pago']),
					'Nu_Transaccion' => $this->security->xss_clean($row['Nu_Transaccion']),
					'Nu_Tarjeta' => $this->security->xss_clean($row['Nu_Tarjeta']),
					'Ss_Total' => $this->security->xss_clean($row['Ss_Total']),
					'ID_Tipo_Medio_Pago' => $this->security->xss_clean($row['ID_Tarjeta_Credito']),
					'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
					'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
				);
			}
			$this->db->insert_batch('documento_medio_pago', $documento_medio_pago);
		
			//Si es forma de pago crédito y además deja acuenta insertar
			if ($row['iTipoVista'] == '1' && $fVuelto > 0.00) {
				$ID_Medio_Pago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND (No_Medio_Pago='Efectivo' OR No_Medio_Pago='EFECTIVO' OR No_Medio_Pago='CONTADO') LIMIT 1")->row()->ID_Medio_Pago;

				foreach($arrPost['arrFormaPago'] as $row) {
					$documento_medio_pago = array(
						'ID_Empresa' => $this->empresa->ID_Empresa,
						'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
						'ID_Medio_Pago'	=> $ID_Medio_Pago,//query para obtener medio de pago en efectivo
						'Nu_Transaccion' => '',
						'Nu_Tarjeta' => '',
						'Ss_Total' => $fVuelto + $fVuelto,//Duplicamos el monto por el reporte de forma de pago que necesitamos hacer resta para los casos que pagan en efectivo y tienen vuelto
						'ID_Tipo_Medio_Pago' => '',
						'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
						'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
					);
				}
				$this->db->insert('documento_medio_pago', $documento_medio_pago);
			}
			// fin
			
			$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Serie_Documento_PK=" . $arrSerieDocumento->ID_Serie_Documento_PK);

			$this->MovimientoInventarioModel->crudMovimientoInventario($this->session->userdata['almacen']->ID_Almacen,$Last_ID_Documento_Cabecera,0,$documento_detalle,1,0,'',1,1);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar venta');
			} else {
				$arrResponseWhatsapp = array(
					'No_Empresa_Comercial' => $this->empresa->No_Empresa_Comercial,
					'No_Empresa' => $this->empresa->No_Empresa,
					'Documento' => $sTidoDocumento,
					'Fecha_Emision' => ToDateBD(dateNow('fecha')),
					'Nu_Tipo_Recepcion' => $arrPost['arrCabecera']['Nu_Tipo_Recepcion'],
					'No_Tipo_Recepcion' => $arrPost['arrCabecera']['No_Tipo_Recepcion'],
					'sDireccionDelivery' => $arrPost['arrCabecera']['sDireccionDelivery'],
					'Total' => $arrPost['arrCabecera']['Ss_Total'],
				);
				$arrDetalle = array('arrDetalle' => $arrPost['arrDetalle']);
				$arrResponseWhatsapp = array_merge($arrResponseWhatsapp, $arrDetalle);

				if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['arrCabecera']['ID_Tipo_Documento'] != '2') {// cancelado y ventas electronica
					$this->db->trans_commit();

					$arrParams = array(
						'iCodigoProveedorDocumentoElectronico' => 1,
						'iEstadoVenta' => 6,//6=Completado
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'sEmailCliente' => ( !isset($arrPost['arrCliente']['Txt_Email_Entidad']) ? '' : $arrPost['arrCliente']['Txt_Email_Entidad'] ),
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

					$arrResponseFE = array_merge($arrResponseFE, $arrResponseWhatsapp);
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseFE,
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['arrCabecera']['ID_Tipo_Documento'] == '2') {// cancelado y 2 = Documento interno
					$this->db->trans_commit();

					//Enviar correo
					if (!empty($arrPost['arrCliente']['Txt_Email_Entidad'])) {
						$sEmailCliente = $arrPost['arrCliente']['Txt_Email_Entidad'];
						$this->sendCorreoNotaVenta($Last_ID_Documento_Cabecera, $sEmailCliente);
					}

					//url de pdf nota de venta interna para whatsapp
					$arrResponseCorreo = array('enlace_del_pdf' => $sUrlPDFNotaVentaInternoLae);
					$arrResponseWhatsapp = array_merge($arrResponseWhatsapp, $arrResponseCorreo);

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseWhatsapp,
					);
				} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['arrCabecera']['ID_Tipo_Documento'] != '2') {// pago pendiente y diferente 2 = Documento interno
					$this->db->trans_commit();

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseWhatsapp,
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['arrCabecera']['ID_Tipo_Documento'] == '2') {// pago pendiente y 2 = Documento interno
					$this->db->trans_rollback();
					
					return array(
						'sStatus' => 'danger',
						'sMessage' => 'No se guardo venta por falta de pago',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseWhatsapp,
					);
				}
			}
		}// if - else validacion si existe comprobante
	}
	
	public function sendCorreoNotaVenta($id, $Txt_Email_Entidad){
		// Parametros de entrada
		$iIdDocumentoCabecera = $id;
		$arrData = $this->VentaModel->get_by_id($iIdDocumentoCabecera);
		if ( $arrData['sStatus'] == 'success' ) {
			$arrData = $arrData['arrData'];

			$this->load->library('email');

			$data = array();

			$sNombreTipoDocumentoVenta = strtoupper($arrData[0]->No_Tipo_Documento);

			$data["No_Documento"]	= $sNombreTipoDocumentoVenta . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento;
			$data["Fe_Emision"] 	= ToDateBD($arrData[0]->Fe_Emision);
			$data["No_Signo"]		= $arrData[0]->No_Signo;
			$data["Ss_Total"]		= $arrData[0]->Ss_Total;
			$data["Txt_Medio_Pago"]	= $arrData[0]->No_Medio_Pago;
			$data["Nu_Tipo"]		= $arrData[0]->Nu_Tipo;
			$data["Ss_Total_Saldo"]	= $arrData[0]->Ss_Total_Saldo;
			
			$data["No_Entidad"] = $arrData[0]->No_Entidad;
			
			$data["No_Empresa"] 					= $this->empresa->No_Empresa;
			$data["Nu_Documento_Identidad_Empresa"] = $this->empresa->Nu_Documento_Identidad;
			
			$data["url_comprobante"] = (!empty($arrData[0]->Txt_Url_PDF) ? $arrData[0]->Txt_Url_PDF : '');

			$asunto = $data["No_Documento"] . ' ' . $this->empresa->No_Empresa . ' | ' . $this->empresa->Nu_Documento_Identidad;
			
			$message = $this->load->view('correos/nota_venta', $data, true);

			$this->email->from('noreply@laesystems.com', $this->empresa->No_Empresa);//de
			
			$this->email->to($Txt_Email_Entidad);//para
				
			$this->email->subject($asunto);
			$this->email->message($message);
			if (!empty($arrData[0]->Txt_Url_PDF))
				$this->email->attach($arrData[0]->Txt_Url_PDF);
			$this->email->set_newline("\r\n");

			$isSend = $this->email->send();
			
			if($isSend) {
				$peticion = array(
					'status' => 'success',
					'style_modal' => 'modal-success',
					'message' => 'Correo enviado',
				);
			} else {
				$peticion = array(
					'status' => 'error',
					'style_modal' => 'modal-danger',
					'message' => 'No se pudo enviar el correo, inténtelo más tarde.',
					'sMessageErrorEmail' => $this->email->print_debugger(),
				);
			}// if - else envio email
		}
	}
}
