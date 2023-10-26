<?php

class ComprobanteElectronicoSunatModel extends CI_Model{
	
	// Obtener API y TOKEN
    public function obtenerTokenLaesystems($arrParams){
        $query = "SELECT
Txt_FE_Ruta,
Txt_FE_Token
FROM
almacen
WHERE ID_Almacen = " . $arrParams['id_almacen'] . " LIMIT 1";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'error',
				'message' => 'Problemas al obtener api',
				'sCodeSQL' => $error['code'],
				'messageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->row()
			);
		}

		return array(
			'status' => 'warning',
			'message' => 'No hay registro'
		);
    }
	
	// Obtener enlace de guias generados desde boton de enlace
    public function obtenerGuiaEnlace($arrParams){
		$query = "SELECT TD.No_Tipo_Documento_Breve, VC.ID_Serie_Documento AS _ID_Serie_Documento, SD.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Txt_Url_PDF FROM
guia_enlace AS VE
JOIN guia_cabecera AS VC ON(VE.ID_Guia_Cabecera = VC.ID_Guia_Cabecera)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
LEFT JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
WHERE VE.ID_Documento_Cabecera = " . $arrParams['id_venta'];

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'error',
				'message' => 'Problemas al obtener api',
				'sCodeSQL' => $error['code'],
				'messageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result()
			);
		}

		return array(
			'status' => 'warning',
			'message' => 'No hay registro'
		);
    }

	//Obtener comprobante por BD
    public function obtenerComprobante($arrParams){
        $query = "SELECT
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
EMP.Nu_Tipo_Proveedor_FE AS tipo_conexion_sistema,
VC.ID_Empresa AS id_empresa,
VC.ID_Organizacion AS id_organizacion,
VC.ID_Almacen AS id_almacen,
VC.ID_Documento_Cabecera AS id_documento_cabecera,
VC.ID_Tipo_Documento AS id_tipo_documento,
VC.ID_Serie_Documento AS id_serie_documento,
SD.Nu_Cantidad_Caracteres AS cantidad_caracteres_correlativo,
VC.ID_Numero_Documento AS id_numero_documento,
UM.Nu_Sunat_Codigo AS codigo_unidad_medida_sunat,
PRO.Nu_Codigo_Barra AS codigo_item,
PRO.No_Producto AS nombre_item,
VD.Qt_Producto AS cantidad,
VD.Ss_Precio AS precio,
VD.Txt_Nota AS nota,
IMP.Nu_Tipo_Impuesto AS tipo_impuesto_sunat,
ICDOCU.Ss_Impuesto AS importe_impuesto_sunat,
VD.Ss_SubTotal AS subtotal,
IMP.Nu_Sunat_Codigo AS codigo_impuesto_sunat,
VD.Ss_Impuesto AS impuesto,
VD.Ss_Descuento AS descuento_subtotal,
VD.Ss_Total AS total,
CONFI.Nu_Tipo_Rubro_Empresa AS tipo_rubro_empresa,
TDOCU.Nu_Sunat_Codigo AS codigo_tipo_documento_sunat,
VC.ID_Sunat_Tipo_Transaction AS tipo_operacion_sunat,
TDOCUIDEN.Nu_Sunat_Codigo AS Codigo_Tipo_Documento_Identidad_Sunat,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.Txt_Direccion_Entidad,
VC.Fe_Emision AS fecha_emision,
VC.Fe_Emision_Hora AS fecha_hora_emision,
VC.Fe_Vencimiento AS fecha_vencimiento,
VC.ID_Moneda AS id_moneda,
MONE.No_Moneda AS nombre_moneda,
MONE.No_Signo AS signo_moneda,
MONE.Nu_Sunat_Codigo AS codigo_moneda_sunat,
VC.No_Orden_Compra_FE AS orden_compra_servicio,
VC.No_Placa_FE AS placa,
VC.No_Formato_PDF AS formato_pdf,
VC.Nu_Tipo_Recepcion AS tipo_recepcion,
VC.Nu_Retencion AS retencion,
VC.Ss_Retencion AS retencion_total,
VC.Nu_Detraccion AS detraccion,
VC.Ss_Detraccion AS detraccion_total,
VC.Po_Detraccion AS detraccion_porcentaje,
VC.Ss_Descuento AS descuento_subtotal_cabecera,
VC.Ss_Total AS total_cabecera,
VC.Ss_Total_Saldo AS total_saldo,
VC.Ss_Vuelto AS vuelto,
VC.Txt_Glosa AS glosa,
VC.Nu_Estado AS estado_venta,
VC.Txt_Hash AS hash_cdr,
VC.Txt_Garantia AS guias
FROM
documento_cabecera AS VC
JOIN empresa AS EMP ON(EMP.ID_Empresa = VC.ID_Empresa)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = VC.ID_Empresa)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDEN ON(CLI.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN tipo_documento AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Documento_Cabecera = " . $arrParams['id_venta'];
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'error',
				'message' => 'Problemas al generar formato documento electrónico',
				'sCodeSQL' => $error['code'],
				'messageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result(),
			);
		}

		return array(
			'status' => 'warning',
			'message' => 'No hay registro'
		);
    }

	public function obtenerComprobanteMedioPago($arrParams){
		$query = "SELECT
MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE,
MP.No_Medio_Pago,
MP.Txt_Medio_Pago,
VMP.Ss_Total AS Ss_Total_Medio_Pago,
MP.Nu_Tipo AS Tipo_Medio_Pago
FROM
documento_cabecera AS VC
JOIN documento_medio_pago AS VMP ON(VC.ID_Documento_Cabecera = VMP.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
WHERE VC.ID_Documento_Cabecera = " . $arrParams['id_venta'];
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'error',
				'message' => 'Problemas al obtener medio(s) de pago',
				'sCodeSQL' => $error['code'],
				'messageSQL' => $error['message'],
				'sql' => $query,
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result()
			);
		}

		return array(
			'status' => 'warning',
			'message' => 'No hay registro',
		);
	}

	public function generarFormatoVentaElectronicoSunat($arrPost){
		$arrPost['hash_cdr'] = '';

		if($arrPost['tipo_operacion_envio'] == 'consultar_comprobante_sunat'){
			//Consulto por BD
			//Reemplazo variables POST por BD
			$arrResultComprobante = $this->obtenerComprobante($arrPost);
			if ( $arrResultComprobante['status'] == 'success' ) {
				$arrResultComprobante = $arrResultComprobante['result'];
				$arrResultComprobanteDetalle['detalle_object'] = (array)$arrResultComprobante;
				$arrResultComprobante = (array)$arrResultComprobante[0];
				$id_venta = $arrPost['id_venta'];

				//settear hash_cdr
				$arrPost = $arrResultComprobante;
				$arrPost['nombre_vendedor'] = '';
				$arrPost['tipo_operacion_envio'] = "consultar_comprobante_sunat";

				$arrPost['detalle'] = array();
				foreach ($arrResultComprobanteDetalle['detalle_object'] as $row) {
					$arrPost['detalle'][] = (array)$row;
				}
				$arrPost['id_venta'] = $id_venta;

			} else {
				return $arrResultComprobante; 
			}
		}// por base de datos

		$Ss_Gravada = 0.00;
		$Ss_IGV = 0.00;
		$Ss_Inafecto = 0.00;
		$Ss_Exonerada = 0.00;
		$Ss_Gratuita = 0.00;
		$fDescuentoTotalOperacionItem = 0.00;
		$i=0;
		$iCounter=1;
		$iNumImpuestoDescuento = 0;
		$iNumImpuestoDescuentoIGV = 0;
		$iNumImpuestoDescuentoEXO = 0;
		$Po_IGV='';
		foreach ($arrPost['detalle'] as $row) {
			$sPrecioTipoCodigoDetalle = '01';
			$Ss_Precio_VU = $row['precio'];
			if ($row['tipo_impuesto_sunat'] == 1){//IGV
				$Ss_Impuesto = $row['impuesto'];
				$Po_IGV = substr($row['importe_impuesto_sunat'],-2);
				$Ss_Precio_VU = round($row['precio'] / $row['importe_impuesto_sunat'], 6);
				$Ss_IGV += $row['impuesto'];
				$Ss_Gravada += $row['subtotal'];

				$iNumImpuestoDescuentoIGV = 1;
				$fImpuestoConfiguracionIGV = $row['importe_impuesto_sunat'];
			} else if ($row['tipo_impuesto_sunat'] == 2){//Inafecto - Operación Onerosa
				$Ss_Inafecto += $row['subtotal'];
			} else if ($row['tipo_impuesto_sunat'] == 3){//Exonerado - Operación Onerosa
				$Ss_Exonerada += $row['subtotal'];
				$iNumImpuestoDescuentoEXO = 1;
			} else if ($row['tipo_impuesto_sunat'] == 4){//Gratuita
				$Ss_Gratuita += $row['subtotal'];
				$sPrecioTipoCodigoDetalle = '02';
			}

			$fDescuentoTotalOperacionItem += (float)$row['descuento_subtotal'];

			$data_detalle["detalle"][$i]["ITEM"] = $iCounter;
			$data_detalle["detalle"][$i]["UNIDAD_MEDIDA_DET"] = $row['codigo_unidad_medida_sunat'];
			$data_detalle["detalle"][$i]["CODIGO_DET"] = $row['codigo_item'];
			$data_detalle["detalle"][$i]["DESCRIPCION_DET"] = $row['nombre_item'] . ($row['nota'] != '' ? ' ' . $row['nota'] : '');
			$data_detalle["detalle"][$i]["CANTIDAD_DET"] = $row['cantidad'];
			$data_detalle["detalle"][$i]["PRECIO_SIN_IGV_DET"] = $Ss_Precio_VU;
			$data_detalle["detalle"][$i]["PRECIO_DET"] = $row['precio'];
			$data_detalle["detalle"][$i]["IMPORTE_DET"] = $row['subtotal'];
			$data_detalle["detalle"][$i]["COD_TIPO_OPERACION"] = $row['codigo_impuesto_sunat'];//codigo de impuesto por item
			$data_detalle["detalle"][$i]["IGV"] = $row['impuesto'];
			$data_detalle["detalle"][$i]["ISC"] = '0';
			$data_detalle["detalle"][$i]["PRECIO_TIPO_CODIGO"] = $sPrecioTipoCodigoDetalle;
			$data_detalle["detalle"][$i]["TOTAL"] = $row['total'];
			$data_detalle["detalle"][$i]["TOTAL_ICBPER"] = '0';
			$data_detalle["detalle"][$i]["NUMERO_LOTE_VENCIMIENTO"] = '';
			$data_detalle["detalle"][$i]["FECHA_LOTE_VENCIMIENTO"] = '';
			$data_detalle["detalle"][$i]["DESCUENTO"] = $row['descuento_subtotal'];
			$data_detalle["detalle"][$i]["MARCA"] = '';

			$i++;
			++$iCounter;
		}//GENERANDO DETALLE PARA SUNAT

		$iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);

		$fDescuentoTotalOperacion = 0.00;
		$fDescuentoTotalOperacionIGV = 0.00;
		$fDescuentoTotalOperacionEXO = 0.00;
		if($arrPost['descuento_subtotal']>0.00){
			$fDescuentoTotalOperacion = ($arrPost['descuento_subtotal'] / $iNumImpuestoDescuento);

			if ( $iNumImpuestoDescuentoEXO == 1 ) {
				$Ss_Exonerada = $Ss_Exonerada - $fDescuentoTotalOperacion;
				$fDescuentoTotalOperacionEXO = $fDescuentoTotalOperacion;
			}

			if ( $iNumImpuestoDescuentoIGV == 1 ) {
				$Ss_Gravada = round($Ss_Gravada - $fDescuentoTotalOperacion, 2);
				$Ss_IGV = ($Ss_Gravada * $fImpuestoConfiguracionIGV) - $Ss_Gravada;
				$fDescuentoTotalOperacionIGV = $fDescuentoTotalOperacion;
			}

			$fDescuentoTotalOperacion = $fDescuentoTotalOperacionEXO + $fDescuentoTotalOperacionIGV;
		}

		$arrVentasCreditoCuotas = (!isset($arrPost['venta_al_credito']) ? array() : $arrPost['venta_al_credito']);//nuevo
		$sConcatenarMultiplesMedioPago = $arrPost['cadena_texto_medio_pago_sunat'];//nuevo
		if(empty($arrPost['cadena_texto_medio_pago_sunat'])) {
			$No_Codigo_Medio_Pago_Sunat_PLE = '';
			$arrDataMediosPago = $this->obtenerComprobanteMedioPago($arrPost);
			if ( $arrDataMediosPago['status'] == 'success' ) {
				$sConcatenarMultiplesMedioPago = '';
				$arrVentasCreditoCuotas = array();
				foreach ($arrDataMediosPago['result'] as $row) {
					$sConcatenarMultiplesMedioPago .= $row->No_Medio_Pago . ' [' . $row->Txt_Medio_Pago . ']: ' . ($No_Codigo_Medio_Pago_Sunat_PLE != '0' ? $arrPost['signo_moneda'] . ' ' . $row->Ss_Total_Medio_Pago : '') . ', ';
					
					if ( $row->Tipo_Medio_Pago == '1' ) {//1=Credito
						$arrVentasCreditoCuotas = array(
							'venta_al_credito' => array(
								0 => array(
									'cuota' => 1,
									'fecha_de_pago' => $arrPost['fecha_vencimiento'],
									'importe' => $arrPost['total_saldo']
								)
							)
						);
					}
				}	
				$sConcatenarMultiplesMedioPago = substr($sConcatenarMultiplesMedioPago, 0, -2);	
			} else {
				return $arrDataMediosPago;
			}
		}

		$this->load->library('EnLetras', 'el');
		$EnLetras = new EnLetras();

		/*
		Tipo de la GUÍA DE REMISIÓN RELACIONADA. Ejemplo: 1
		1 = GUÍA DE REMISIÓN REMITENTE
		2 = GUÍA DE REMISIÓN TRANSPORTISTA
		*/
		$data_guias = array();
		$cadena_de_texto = trim($arrPost['guias']);
		if ( substr($cadena_de_texto, -1) == ',' )
			$cadena_de_texto = substr($cadena_de_texto, 0, -1);
		$cadena_buscada = '-';
		$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
		if ( strlen($arrPost['guias']) > 5 && $posicion_coincidencia !== false) {
			$arrCadena = explode(',',$arrPost['guias']);
			$i = 0;
			foreach ($arrCadena as $row) {
				$arrSerieNumero = explode('-', $row);
				if ( strlen(trim($arrSerieNumero[0])) == 4 && isset($arrSerieNumero[1]) ) {
					$serie = trim($arrSerieNumero[0]);
					$numero = substr(trim($arrSerieNumero[1]), 0, 8);
					$data_guias["guias"][$i]["guia_tipo"] = 1;
					$data_guias["guias"][$i]["guia_serie_numero"] = $serie . '-' . $numero;
				}
				$i++;
			}
		} else {
			$arrParamsGuia = array('id_venta' => $arrPost['id_venta']);
			$arrResponseDocument = $this->obtenerGuiaEnlace($arrParamsGuia);
			if ($arrResponseDocument['status'] == 'success'){
				$i = 0;
				foreach ($arrResponseDocument['result'] as $rowEnlace) {
					$data_guias["guias"][$i]["guia_tipo"] = 1;
					$data_guias["guias"][$i]["guia_serie_numero"] = $rowEnlace->_ID_Serie_Documento . '-' . $rowEnlace->ID_Numero_Documento;
					$i++;
				}
			}
		}

		$Txt_Glosa = $arrPost['glosa'];
		$fTotal = ($arrPost['total'] - $Ss_Gratuita);

		$iPoDetraccion=0;
		if($arrPost['detraccion_total'] > 0.00){
			$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrPost['detraccion_total'], 2) . ' <br>';
			$Txt_Glosa .= '<b>Base imponible de la detracción:</b> ' . ($arrPost['total']) . ' <br>';
			$Txt_Glosa .= '<b>Monto de la detracción:</b> ' . round($arrPost['detraccion_total'], 2) . ' <br>';
			$iPoDetraccion=$arrPost['detraccion_porcentaje'];
		}

		if($arrPost['retencion'] > 0.00){
			$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrPost['retencion_total'], 2) . ' <br>';
			$Txt_Glosa .= '<b>Base imponible de la retención:</b> ' . ($arrPost['total']) . ' <br>';
			$Txt_Glosa .= '<b>Monto de la retención:</b> ' . round($arrPost['retencion_total'], 2) . ' <br>';
		}

		if(empty($arrPost['hash_cdr']) && ($arrPost['Nu_Documento_Identidad']=='' && $arrPost['No_Entidad']=='') ) {
			//OBTENER CLIENTE
			$query = "SELECT CLI.*, TDOCUIDEN.Nu_Sunat_Codigo AS Codigo_Tipo_Documento_Identidad_Sunat FROM entidad AS CLI JOIN tipo_documento_identidad AS TDOCUIDEN ON(CLI.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad) WHERE CLI.ID_Entidad = " . $arrPost['id_entidad'] . " LIMIT 1";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'status' => 'error',
					'message' => 'Problemas al generar formato documento electrónico',
					'sCodeSQL' => $error['code'],
					'messageSQL' => $error['message']
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				$objCliente = $arrResponseSQL->row();
				
				$arrPost['Codigo_Tipo_Documento_Identidad_Sunat'] = $objCliente->Codigo_Tipo_Documento_Identidad_Sunat;
				$arrPost['Nu_Documento_Identidad'] = $objCliente->Nu_Documento_Identidad;
				$arrPost['No_Entidad'] = $objCliente->No_Entidad;
				$arrPost['Txt_Direccion_Entidad'] = $objCliente->Txt_Direccion_Entidad;
			} else {
				return array(
					'status' => 'warning',
					'message' => 'No se encontró cliente'
				);
			}
		}

		//SUNAT TIPO TRANSACTION
		$sCodigoTipoOperacionSunat = "0101";//VENTA INTERNA
		if ($arrPost['tipo_operacion_sunat']=='2')
			$sCodigoTipoOperacionSunat = "0200";//EXPORTACION

		$data_cabecera = array(
			"operacion"	=> $arrPost['tipo_operacion_envio'],
			"TIPO_OPERACION" => $sCodigoTipoOperacionSunat,
			"TIPO_RUBRO_EMPRESA" => $arrPost['tipo_rubro_empresa'],
			"COD_TIPO_DOCUMENTO" => $arrPost['codigo_tipo_documento_sunat'],
			"NRO_COMPROBANTE" => $arrPost['id_serie_documento'] . '-' . autocompletarConCeros('', $arrPost['id_numero_documento'], $arrPost['cantidad_caracteres_correlativo'], '0', STR_PAD_LEFT),
			"SERIE_COMPROBANTE" => $arrPost['id_serie_documento'],
			"NUMERO_COMPROBANTE" => autocompletarConCeros('', $arrPost['id_numero_documento'], $arrPost['cantidad_caracteres_correlativo'], '0', STR_PAD_LEFT),
			"TIPO_DOCUMENTO_CLIENTE" => $arrPost['Codigo_Tipo_Documento_Identidad_Sunat'],
			"NRO_DOCUMENTO_CLIENTE" => $arrPost['Nu_Documento_Identidad'],
			"RAZON_SOCIAL_CLIENTE" => $arrPost['No_Entidad'],
			"DIRECCION_CLIENTE" => (empty($arrPost['direccion_delivery']) ? $arrPost['Txt_Direccion_Entidad'] : $arrPost['direccion_delivery']),
			"celular_cliente" => "",
			"CIUDAD_CLIENTE" => "",
			"COD_PAIS_CLIENTE" => "",
			"FECHA_DOCUMENTO" => $arrPost['fecha_emision'],
			"FECHA_HORA_DOCUMENTO" => $arrPost['fecha_hora_emision'],
			"FECHA_VTO" => $arrPost['fecha_vencimiento'],
			"COD_MONEDA" => $arrPost['codigo_moneda_sunat'],
			"POR_IGV" => $Po_IGV,
			"TOTAL_DESCUENTO" => $fDescuentoTotalOperacion,
			"TOTAL_DESCUENTO_ITEM" => $fDescuentoTotalOperacionItem,
			"SUB_TOTAL" => ($Ss_Gravada + $Ss_Inafecto + $Ss_Exonerada),
			"TOTAL_GRAVADAS" => $Ss_Gravada,
			"TOTAL_INAFECTA" => $Ss_Inafecto,
			"TOTAL_EXONERADAS" => $Ss_Exonerada,
			"TOTAL_IGV" => $Ss_IGV,
			"TOTAL_GRATUITAS" => $Ss_Gratuita,
			"TOTAL" => $fTotal,
			"ICBP" => 0,
			"TIPO_COMPROBANTE_MODIFICA" => "",
			"NRO_DOCUMENTO_MODIFICA" => "",
			"COD_TIPO_MOTIVO" => "",
			"DESCRIPCION_MOTIVO" => "",
			"TOTAL_LETRAS" => $EnLetras->ValorEnLetras($fTotal, $arrPost['nombre_moneda']),
			"GLOSA" => $Txt_Glosa,
			"ORDEN_COMPRA_SERVICIO" => $arrPost['orden_compra_servicio'],
			"PLACA_VEHICULO" => $arrPost['placa'],
			"CONDICIONES_DE_PAGO" => "",
			"MEDIO_DE_PAGO" => $sConcatenarMultiplesMedioPago,
			"TXT_URL_CDR" => "",
			"formato_de_pdf" => $arrPost['formato_pdf'],
			"tipo_recepcion" => $arrPost['tipo_recepcion'],
			"RETENCION" => $arrPost['retencion'],
			"TOTAL_RETENCION" => $arrPost['retencion_total'],
			"DETRACCION" => $arrPost['detraccion'],
			"TOTAL_DETRACCION" => $arrPost['detraccion_total'],
			"PORCENTAJE_DETRACCION" => $iPoDetraccion,
			"TOTAL_VUELTO" => ($arrPost['vuelto'] > 0.00 ? ($arrPost['vuelto'] + $Ss_Gratuita) : 0),
			"VENDEDOR" => $arrPost['nombre_vendedor'],
			"HASH_CPE" => $arrPost['hash_cdr']
		);
		$data = array_merge($data_cabecera, $data_detalle, $data_guias, $arrVentasCreditoCuotas);

		if(empty($arrPost['hash_cdr'])) {
			//OBTENER RUTA Y TOKEN PARA ENVIO A SUNAT
			$arrParamsToken = array('id_almacen' => $arrPost['id_almacen']);
			$arrResponseToken = $this->obtenerTokenLaesystems($arrParamsToken);
			if($arrResponseToken['status']=='success'){
				$ruta = $arrResponseToken['result']->Txt_FE_Ruta;
				$token = $arrResponseToken['result']->Txt_FE_Token;
				
				if( empty($ruta) || empty($token) ){
					return array(
						'status' => 'error',
						'message' => 'Faltan configurar ruta / token'
					);
				}

				//ENVIAR A SUNAT
				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"tipo_conexion_sistema" => $arrPost['tipo_conexion_sistema'],
					"id_documento_cabecera" => $arrPost['id_documento_cabecera'],
					"estado_venta" => $arrPost['estado_venta'],
					"data" => $data
				);
				
				return $this->consultarDocumentoElectronicoSunatApiv2($arrParamsFE);
			} else {
				return $arrResponseToken;
			}
		} else {
			$ruta = $arrPost['Txt_FE_Ruta'];
			$token = $arrPost['Txt_FE_Token'];
			
			if( empty($ruta) || empty($token) ){
				return array(
					'status' => 'error',
					'message' => 'Faltan configurar ruta / token'
				);
			}

			//ENVIAR A SUNAT
			$arrParamsFE = array(
				"ruta" => $ruta,
				"token" => $token,
				"tipo_conexion_sistema" => $arrPost['tipo_conexion_sistema'],
				"id_documento_cabecera" => $arrPost['id_venta'],
				"estado_venta" => 6,
				"data" => $data
			);

			return $this->consultarDocumentoElectronicoSunatApiv2($arrParamsFE);
		}
	}
	
	private function consultarDocumentoElectronicoSunatApiv2($arrParamsFE){
		$data = $arrParamsFE['data'];
		$data_json = json_encode($data);
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $arrParamsFE['ruta']);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$arrParamsFE['token'].'"',
			'Content-Type: application/json',
			'X-API-Key: ' . $arrParamsFE['token'],
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Obtener el código de respuesta
		$respuesta = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );	
		curl_close($ch);
		// Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);
			$arrParamsFE = array_merge( $arrParamsFE, array( 'arrMessagePSE' => $leer_respuesta ) );
			
			return $this->cambiarEstadoDocumentoElectronicoApiv2( $arrParamsFE );
		} else {
			$arrParamsFE['estado_venta'] = 9;//9=Completado error
			return $this->cambiarEstadoDocumentoElectronicoApiv2( $arrParamsFE );
		}
	}
	
	public function cambiarEstadoDocumentoElectronicoApiv2( $arrParams ){
		$this->db->trans_begin();
		if ($arrParams['estado_venta'] == 6 || $arrParams['estado_venta'] == 9) {
			if ($arrParams['arrMessagePSE']['status']=='success') {
				$data_mensaje_sunat = json_encode(array(
					'Proveedor' => ($arrParams['tipo_conexion_sistema'] == 1 ? 'PSE N' : 'SUNAT'),
					'Enviada_SUNAT' => 'No',
					'Aceptada_SUNAT' => ((isset($arrParams['arrMessagePSE']['enlace_del_cdr']) && !empty($arrParams['arrMessagePSE']['enlace_del_cdr'])) ? 'Si' : '-'),
					'Codigo_SUNAT' => ((isset($arrParams['arrMessagePSE']['enlace_del_cdr']) && !empty($arrParams['arrMessagePSE']['enlace_del_cdr'])) ? '0' : '-'),
					'Mensaje_SUNAT' => utf8_decode($arrParams['arrMessagePSE']['message']),
					'Fecha_Registro' => dateNow('fecha_hora'),
					'Fecha_Envio' => dateNow('fecha_hora')
				));

				$data = array(
					'Nu_Estado' => 8,
					'Txt_Url_Comprobante' => $arrParams['arrMessagePSE']['enlace'],
					'Txt_Url_PDF' => $arrParams['arrMessagePSE']['enlace_del_pdf'],
					'Txt_Url_XML' => $arrParams['arrMessagePSE']['enlace_del_xml'],
					'Txt_Url_CDR' => $arrParams['arrMessagePSE']['enlace_del_cdr'],
					'Txt_QR' => $arrParams['arrMessagePSE']['cadena_para_codigo_qr'],
					'Txt_Hash' => $arrParams['arrMessagePSE']['codigo_hash'],
					'Txt_Respuesta_Sunat_FE' => $data_mensaje_sunat
				);
				
				$status = 'success';
				$message = 'Comprobante enviado a SUNAT';
			} else {
				$data_mensaje_sunat = json_encode(array(
					'Proveedor' => ($arrParams['tipo_conexion_sistema'] == 1 ? 'PSE N' : 'SUNAT'),
					'Enviada_SUNAT' => 'No',
					'Aceptada_SUNAT' => 'No',
					'Codigo_SUNAT' => '',
					'Mensaje_SUNAT' => '',
					'Fecha_Registro' => dateNow('fecha_hora'),
					'Fecha_Envio' => dateNow('fecha_hora')
				));

				$data = array(
					'Nu_Estado' => 9,
					'Txt_Respuesta_Sunat_FE' => $data_mensaje_sunat
				);
				$status = 'error';
				$message = 'Comprobante con error envío a SUNAT codigo: ' . $arrParams['arrMessagePSE']['codigo'] . ' - ' . $arrParams['arrMessagePSE']['message'] . '. No se envió a SUNAT';
			}
		}//Si es ENVIADO o COMPLETADO ERROR

		$this->db->update('documento_cabecera', $data, array('ID_Documento_Cabecera' => $arrParams['id_documento_cabecera']));

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'error',
				'message' => 'No se envió a SUNAT'
			);
		} else {
			$this->db->trans_commit();
			return array(
				'status' => $status,
				'message' => $message,
				'data' => $data
			);
		}
	}

	public function generarFormatoAnularVentaElectronicoSunat($arrParamsSunat){
		$sPrimerCaracterSerie = $arrParamsSunat['objVenta']->sPrimerCaracterSerie;
		$objVenta = $arrParamsSunat['objVenta'];
		$arrPost = $arrParamsSunat['arrPost'];

		if ( $sPrimerCaracterSerie == 'RC') {//SERIES B
			//OBTENER DETALLE DE VENTA
			$query = "SELECT
VC.ID_Entidad,
IMP.Nu_Tipo_Impuesto,
ICDOCU.Ss_Impuesto,
VD.Ss_SubTotal AS Ss_SubTotal_Producto,
VD.Ss_Impuesto AS Ss_Impuesto_Producto,
VD.Ss_Total AS Ss_Total_Producto,
VC.Ss_Descuento,
MONE.Nu_Sunat_Codigo AS Codigo_Moneda_Sunat
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)						
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera=VD.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
VC.ID_Documento_Cabecera = " . $arrPost['id_venta'];
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'status' => 'error',
					'message' => 'Problemas al generar formato documento electrónico',
					'sCodeSQL' => $error['code'],
					'messageSQL' => $error['message']
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				$arrVentaDetalle = $arrResponseSQL->result();
			} else {
				return array(
					'status' => 'warning',
					'message' => 'No se encontró cliente'
				);
			}

			$Ss_Gravada = 0.00;
			$Ss_Inafecto = 0.00;
			$Ss_Exonerada = 0.00;
			$Ss_Gratuita = 0.00;
			$Ss_IGV = 0.00;
			$fTotalIcbper = 0.00;
			$Ss_Total = 0.00;
			$fTotalIcbperSinImpuesto=0.00;	

			$iNumImpuestoDescuento = 0;
			$iNumImpuestoDescuentoIGV = 0;
			$iNumImpuestoDescuentoEXO = 0;
			$fImpuestoConfiguracionIGV = 1;
			foreach ($arrVentaDetalle as $row) {
				if ($row->Nu_Tipo_Impuesto == 1){//IGV
					$Ss_IGV += $row->Ss_Impuesto_Producto;
					$Ss_Gravada += $row->Ss_SubTotal_Producto;

					$iNumImpuestoDescuentoIGV = 1;
					$fImpuestoConfiguracionIGV = $row->Ss_Impuesto;
				} else if ($row->Nu_Tipo_Impuesto == 2){//Inafecto - Operación Onerosa
					$Ss_Inafecto += $row->Ss_SubTotal_Producto;
				} else if ($row->Nu_Tipo_Impuesto == 3){//Exonerado - Operación Onerosa
					$Ss_Exonerada += $row->Ss_SubTotal_Producto;
					$iNumImpuestoDescuentoEXO = 1;
				} else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
					$Ss_Gratuita += $row->Ss_SubTotal_Producto;
				}
				$Ss_Total += $row->Ss_Total_Producto;
			}

			$iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);

			$fDescuentoTotalOperacion = 0.00;
			$fDescuentoTotalOperacionIGV = 0.00;
			$fDescuentoTotalOperacionEXO = 0.00;
			if($arrVentaDetalle[0]->Ss_Descuento>0.00){
				$fDescuentoTotalOperacion = ($arrVentaDetalle[0]->Ss_Descuento / $iNumImpuestoDescuento);

				if ( $iNumImpuestoDescuentoEXO == 1 ) {
					$Ss_Exonerada = $Ss_Exonerada - $fDescuentoTotalOperacion;
					$fDescuentoTotalOperacionEXO = $fDescuentoTotalOperacion;
				}

				if ( $iNumImpuestoDescuentoIGV == 1 ) {
					$fDescuentoTotalOperacion = round($fDescuentoTotalOperacion / $fImpuestoConfiguracionIGV, 2);
					$Ss_Gravada = $Ss_Gravada - $fDescuentoTotalOperacion;
					$Ss_IGV = ($Ss_Gravada * $fImpuestoConfiguracionIGV) - $Ss_Gravada;
					$fDescuentoTotalOperacionIGV = $fDescuentoTotalOperacion;
				}

				$fDescuentoTotalOperacion = $fDescuentoTotalOperacionEXO + $fDescuentoTotalOperacionIGV;
			}

			//OBTENER CLIENTE
			$query = "SELECT CLI.*, TDOCUIDEN.Nu_Sunat_Codigo AS Codigo_Tipo_Documento_Identidad_Sunat FROM entidad AS CLI JOIN tipo_documento_identidad AS TDOCUIDEN ON(CLI.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad) WHERE CLI.ID_Entidad = " . $arrVentaDetalle[0]->ID_Entidad . " LIMIT 1";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'status' => 'error',
					'message' => 'Problemas al generar formato documento electrónico',
					'sCodeSQL' => $error['code'],
					'messageSQL' => $error['message']
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				$objCliente = $arrResponseSQL->row();
			} else {
				return array(
					'status' => 'warning',
					'message' => 'No se encontró cliente'
				);
			}

			$data = array(
				"operacion" => "generar_anulacion",
				"CODIGO" => $sPrimerCaracterSerie,
				"FECHA_REFERENCIA" => $objVenta->Fe_Emision,
				"FECHA_DOCUMENTO" => dateNow('fecha'),
				"TXT_URL_CDR" => $objVenta->Txt_Url_CDR,
				"detalle" => array(
					"0" => array(
						"ITEM" => 1,
						"TIPO_COMPROBANTE" => $arrPost['codigo_tipo_documento_sunat'],
						"NRO_COMPROBANTE" => $objVenta->ID_Serie_Documento . '-' . autocompletarConCeros('', $objVenta->ID_Numero_Documento, $objVenta->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
						"TIPO_DOCUMENTO" => $objCliente->Codigo_Tipo_Documento_Identidad_Sunat,
						"NRO_DOCUMENTO" => $objCliente->Nu_Documento_Identidad,
						"TIPO_COMPROBANTE_REF" => '',//CUANDO ENVIE ANULACION DE NC O ND
						"NRO_COMPROBANTE_REF" => '',//CUANDO ENVIE ANULACION DE NC O ND
						"STATU" => "3",
						"COD_MONEDA" => $arrVentaDetalle[0]->Codigo_Moneda_Sunat,
						"GRAVADA" => round($Ss_Gravada, 2),
						"INAFECTO" => round($Ss_Inafecto, 2),
						"EXONERADO" => round($Ss_Exonerada, 2),
						"EXPORTACION" => 0.00,
						"GRATUITAS" => round($Ss_Gratuita, 2),
						"IGV" => round($Ss_IGV, 2),
						"TOTAL" => round($Ss_Total, 2),
						"ISC" => "0",
						"OTROS" => "0",
						"CARGO_X_ASIGNACION" => "0",
						"MONTO_CARGO_X_ASIG" => "0"
					),
				),
			);
		} else if ( $sPrimerCaracterSerie == 'RA') {//SERIES F
			$data = array(
				"operacion" => "generar_anulacion",
				"CODIGO" => $sPrimerCaracterSerie,
				"FECHA_REFERENCIA" => $objVenta->Fe_Emision,
				"FECHA_BAJA" => dateNow('fecha'),
				"TXT_URL_CDR" => $objVenta->Txt_Url_CDR,
				"detalle" => array(
					"0" => array(
						"ITEM" => 1,
						"TIPO_COMPROBANTE" => $arrPost['codigo_tipo_documento_sunat'],
						"SERIE" => $objVenta->ID_Serie_Documento,
						"NUMERO" => autocompletarConCeros('', $objVenta->ID_Numero_Documento, $objVenta->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
						"DESCRIPCION" => "ERROR DE DIGITACION"
					),
				),
			);
		}

		//OBTENER RUTA Y TOKEN PARA ENVIO A SUNAT
		$arrParamsToken = array('id_almacen' => $arrPost['id_almacen']);
		$arrResponseToken = $this->obtenerTokenLaesystems($arrParamsToken);
		if($arrResponseToken['status']=='success'){
			$ruta = $arrResponseToken['result']->Txt_FE_Ruta;
			$token = $arrResponseToken['result']->Txt_FE_Token;
			
			if( empty($arrResponseToken['result']->Txt_FE_Ruta) || empty($arrResponseToken['result']->Txt_FE_Token) ){
				return array(
					'status' => 'error',
					'message' => 'Faltan configurar ruta / token'
				);
			}

			//ENVIAR A SUNAT
			$arrParamsFE = array(
				"ruta" => $ruta,
				"token" => $token,
				"tipo_conexion_sistema" => $arrPost['tipo_conexion_sistema'],
				"id_documento_cabecera" => $arrPost['id_venta'],
				"estado_venta" => $objVenta->Nu_Estado,
				"data" => $data
			);
			
			return $this->anularDocumentoElectronicoSunatApiv2($arrParamsFE);
		} else {
			return $arrResponseToken;
		}
	}
	
	private function anularDocumentoElectronicoSunatApiv2($arrParamsFE){
		$data = $arrParamsFE['data'];
		$data_json = json_encode($data);
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $arrParamsFE['ruta']);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$arrParamsFE['token'].'"',
			'Content-Type: application/json',
			'X-API-Key: ' . $arrParamsFE['token'],
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Obtener el código de respuesta
		$respuesta = curl_exec($ch);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );	
		curl_close($ch);
		// Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);
			$arrParamsFE = array_merge( $arrParamsFE, array( 'arrMessagePSE' => $leer_respuesta ) );
			
			return $this->cambiarAnularEstadoDocumentoElectronicoApiv2( $arrParamsFE );
		} else {
			$arrParamsFE['estado_venta'] = 11;//9=Completado error
			return $this->cambiarAnularEstadoDocumentoElectronicoApiv2( $arrParamsFE );
		}
	}
	
	public function cambiarAnularEstadoDocumentoElectronicoApiv2( $arrParams ){
		$this->db->trans_begin();
		
		if ($arrParams['arrMessagePSE']['status']=='success') {
			$data_mensaje_sunat = json_encode(array(
				'Proveedor' => ($arrParams['tipo_conexion_sistema'] == 1 ? 'PSE N' : 'SUNAT'),
				'Enviada_SUNAT' => 'Si',
				'Aceptada_SUNAT' => 'Si',
				'Codigo_SUNAT' => '0',
				'Mensaje_SUNAT' => utf8_decode($arrParams['arrMessagePSE']['message']),
				'Fecha_Registro' => dateNow('fecha_hora'),
				'Fecha_Envio' => dateNow('fecha_hora')
			));

			$data = array(
				'Nu_Estado' => 10,
				'Txt_Respuesta_Sunat_FE' => $data_mensaje_sunat
			);
			
			$status = 'success';
			$message = 'enviada a SUNAT';
		} else {
			$data_mensaje_sunat = json_encode(array(
				'Proveedor' => ($arrParams['tipo_conexion_sistema'] == 1 ? 'PSE N' : 'SUNAT'),
				'Enviada_SUNAT' => 'Si',
				'Aceptada_SUNAT' => 'No',
				'Codigo_SUNAT' => $arrParams['arrMessagePSE']['codigo'],
				'Mensaje_SUNAT' => $arrParams['arrMessagePSE']['message'],
				'Fecha_Registro' => dateNow('fecha_hora'),
				'Fecha_Envio' => dateNow('fecha_hora')
			));

			$data = array(
				'Nu_Estado' => 11,
				'Txt_Respuesta_Sunat_FE' => $data_mensaje_sunat
			);
			$status = 'error';
			$message = 'con error envio a SUNAT codigo: ' . $arrParams['arrMessagePSE']['codigo'] . ' - ' . $arrParams['arrMessagePSE']['message'];
		}

		$this->db->update('documento_cabecera', $data, array('ID_Documento_Cabecera' => $arrParams['id_documento_cabecera']));

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'error',
				'message' => 'No se envió a SUNAT'
			);
		} else {
			$this->db->trans_commit();
			return array(
				'status' => $status,
				'message' => $message,
				'data' => $data
			);
		}
	}
}