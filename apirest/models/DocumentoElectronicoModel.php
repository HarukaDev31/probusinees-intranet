<?php
class DocumentoElectronicoModel extends CI_Model{
	var $table = 'documento_cabecera';
	var $table_documento_detalle = 'documento_detalle';
	var $table_documento_medio_pago = 'documento_medio_pago';
	var $table_documento_enlace	= 'documento_enlace';
	var $table_tipo_documento = 'tipo_documento';
	var $table_impuesto_cruce_documento	= 'impuesto_cruce_documento';
	var $table_entidad = 'entidad';
	var $table_tipo_documento_identidad	= 'tipo_documento_identidad';
	var $table_moneda = 'moneda';
	var $table_medio_pago = 'medio_pago';

	public function __construct(){
		parent::__construct();
	}

	public function obtenerComprobanteMedioPago($arrParams){
		$query = "SELECT MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE, MP.No_Medio_Pago, MP.Txt_Medio_Pago, VMP.Ss_Total AS Ss_Total_Medio_Pago, MP.Nu_Tipo AS codigo_interno_pago FROM " . $this->table . " AS VC JOIN " . $this->table_documento_medio_pago . " AS VMP ON(VC.ID_Documento_Cabecera = VMP.ID_Documento_Cabecera) JOIN " . $this->table_medio_pago . " AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago) WHERE VC.ID_Documento_Cabecera = " . $arrParams['iIdDocumentoCabecera'];
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener medio(s) de pago',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query,
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
			'sMessage' => 'No hay registro',
		);
	}

    public function obtenerComprobante($arrParams){
        $query = "SELECT
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
VC.ID_Empresa,
VC.ID_Organizacion,
VC.ID_Almacen,
VC.ID_Documento_Cabecera,
CLI.ID_Entidad,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.Txt_Direccion_Entidad,
CLI.Nu_Dias_Credito,
CLI.Nu_Celular_Entidad,
VC.ID_Tipo_Documento,
TDOCU.Nu_Impuesto,
TDOCU.Nu_Enlace,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision,
VC.Fe_Emision_Hora,
VC.ID_Moneda,
VC.ID_Medio_Pago,
VC.Fe_Vencimiento,
VC.Nu_Descargar_Inventario,
VC.ID_Lista_Precio_Cabecera,
VD.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
VD.Ss_Precio,
VD.Qt_Producto,
VD.ID_Impuesto_Cruce_Documento,
VD.Ss_SubTotal AS Ss_SubTotal_Producto,
VD.Ss_Impuesto AS Ss_Impuesto_Producto,
VD.Ss_Descuento AS Ss_Descuento_Producto,
VD.Ss_Total AS Ss_Total_Producto,
PRO.ID_Impuesto_Icbper,
VD.Txt_Nota,
ICDOCU.Ss_Impuesto,
VE.ID_Tipo_Documento_Modificar,
VE.Nu_Sunat_Codigo_Tipo_Documento_Modificar,
VE.ID_Serie_Documento_Modificar,
VE.ID_Numero_Documento_Modificar,
IMP.Nu_Tipo_Impuesto,
IMP.ID_Impuesto,
VC.Txt_Glosa,
VC.Ss_Descuento,
MONE.No_Signo,
VC.Ss_Total,
MONE.No_Moneda,
VC.Po_Descuento,
TDOCUIDEN.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_TDI,
UM.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_UM,
VC.Txt_Url_PDF,
VC.Txt_Url_XML,
VC.Txt_Url_CDR,
VC.Txt_Url_Comprobante,
TDOCU.No_Tipo_Documento,
VC.Nu_Codigo_Motivo_Referencia,
VC.Nu_Detraccion,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio,
VC.No_Formato_PDF,
VD.Txt_Nota AS Txt_Nota_Item,
MONE.Nu_Valor_Fe AS Nu_Valor_Fe_Moneda,
IMP.Nu_Valor_Fe AS Nu_Valor_Fe_Impuesto,
MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
IMP.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Impuesto,
TDOCU.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Tipo_Documento,
SD.Nu_Cantidad_Caracteres,
VC.Txt_Hash,
VDL.Nu_Lote_Vencimiento,
VDL.Fe_Lote_Vencimiento,
CONFI.Nu_Tipo_Rubro_Empresa,
VC.No_Orden_Compra_FE,
VC.No_Placa_FE,
VC.Txt_Garantia,
MP.Nu_Tipo,
VC.Ss_Total_Saldo,
MP.Txt_Medio_Pago,
MP.No_Medio_Pago,
VD.Ss_Icbper,
VC.Nu_Tipo_Recepcion,
VC.ID_Sunat_Tipo_Transaction,
STT.Nu_Codigo_Sunat AS Nu_Codigo_Sunat_Tipo_Transaccion,
STT.Nu_Codigo_Pse AS Nu_Codigo_Pse_Tipo_Transaccion,
VC.Nu_Retencion,
VC.Ss_Retencion,
VC.Ss_Detraccion,
VC.Po_Detraccion,
VC.Ss_Vuelto,
VC.Ss_Descuento_Impuesto,
ICDOCU.Po_Impuesto,
MAR.No_Marca,
VC.Nu_Expediente_FE,
VC.Nu_Codigo_Unidad_Ejecutora_FE,
USER.No_Nombres_Apellidos AS No_Usuario_Venta
FROM
" . $this->table . " AS VC
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = VC.ID_Empresa)
JOIN organizacion AS ORG ON(VC.ID_Organizacion = ORG.ID_Organizacion)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN sunat_tipo_transaction AS STT ON(VC.ID_Sunat_Tipo_Transaction = STT.ID_Sunat_Tipo_Transaction)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN " . $this->table_documento_detalle . " AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN documento_detalle_lote AS VDL ON(VC.ID_Documento_Cabecera = VDL.ID_Documento_Cabecera AND VD.ID_Documento_Detalle = VDL.ID_Documento_Detalle)
JOIN " . $this->table_entidad . " AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDEN ON(CLI.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
LEFT JOIN marca AS MAR ON (MAR.ID_Marca = PRO.ID_Marca)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
LEFT JOIN usuario AS USER ON(USER.ID_Usuario = VC.ID_Mesero)
LEFT JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
LEFT JOIN (
SELECT
VE.ID_Documento_Cabecera,
VC.ID_Tipo_Documento AS ID_Tipo_Documento_Modificar,
TDOCU.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Tipo_Documento_Modificar,
VC.ID_Serie_Documento AS ID_Serie_Documento_Modificar,
VC.ID_Numero_Documento AS ID_Numero_Documento_Modificar
FROM
" . $this->table . " AS VC
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_documento_enlace . " AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera_Enlace)
) AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera)
WHERE
VC.ID_Documento_Cabecera = " . $arrParams['iIdDocumentoCabecera'];
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al generar formato documento electrónico',
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
			'sMessage' => 'No hay registro',
		);
    }

	public function obtenerComprobanteAnulado($arrParams){
		$query = "SELECT
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
WHERE
VC.ID_Documento_Cabecera = " . $arrParams['iIdDocumentoCabecera'] . " LIMIT 1";
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
			'sMessage' => $sMessage,
		);
	}

	// Guia de Remision
	// formato pse / sunat
    public function obtenerGuia($arrParams){
        $query = "SELECT
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
UO.Nu_Valor AS ID_Ubigeo_Inei_Partida,
UO.No_Descripcion AS Valor_Ubigeo_Inei_Partida,
ALMA.Txt_Direccion_Almacen AS Txt_Direccion_Origen,
VC.ID_Serie_Documento,
SD.Nu_Cantidad_Caracteres,
VC.ID_Numero_Documento,
VC.Fe_Emision,
TDOCU.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Tipo_Documento,
VC.Txt_Glosa,
TDMOTIVOTRAS.No_Class AS Nu_Codigo_Motivo_Traslado_Sunat,
TDMOTIVOTRAS.No_Descripcion AS No_Motivo_Traslado_Sunat,
F.Fe_Traslado,
TDOCUIDEN.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_TDI_Transporte,
TRANS.No_Entidad AS No_Entidad_Transportista,
TRANS.Nu_Documento_Identidad AS Nu_Documento_Identidad_Transportista,
F.No_Placa,
TDOCUIDENCLI.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_TDI,
PROVE.No_Entidad,
PROVE.Nu_Documento_Identidad,
PROVE.Txt_Direccion_Entidad AS Txt_Direccion_Destino,
UD.Nu_Valor AS ID_Ubigeo_Inei_Llegada,
UD.No_Descripcion AS Valor_Ubigeo_Inei_Llegada,
UM.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_UM,
VD.Qt_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
VD.Txt_Nota AS Txt_Nota_Item,
VC.Ss_Total,
VC.Ss_Peso_Bruto,
VC.Nu_Bulto,
VC.No_Tipo_Transporte,
F.No_Licencia,
VC.Txt_Hash,
EMP.Txt_Sunat_Token_Guia_Client_ID,
EMP.Txt_Sunat_Token_Guia_Client_Secret
FROM
guia_cabecera AS VC
JOIN empresa AS EMP ON(EMP.ID_Empresa = VC.ID_Empresa)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN tipo_documento AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN guia_detalle AS VD ON(VC.ID_Guia_Cabecera = VD.ID_Guia_Cabecera)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN entidad AS PROVE ON(PROVE.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDENCLI ON(PROVE.ID_Tipo_Documento_Identidad = TDOCUIDENCLI.ID_Tipo_Documento_Identidad)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN flete AS F ON(F.ID_Guia_Cabecera = VC.ID_Guia_Cabecera)
JOIN entidad AS TRANS ON(TRANS.ID_Entidad = F.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDEN ON(TRANS.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
JOIN tabla_dato AS TDMOTIVOTRAS ON(TDMOTIVOTRAS.ID_Tabla_Dato = F.ID_Motivo_Traslado)
LEFT JOIN tabla_dato AS UO ON(UO.ID_Tabla_Dato = ALMA.ID_Ubigeo_Inei_Partida)
LEFT JOIN tabla_dato AS UD ON(UD.ID_Tabla_Dato = F.ID_Ubigeo_Inei_Llegada)
WHERE
VC.ID_Guia_Cabecera = " . $arrParams['iIdDocumentoCabecera'];
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al generar formato documento electrónico',
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
			'sMessage' => 'No hay registro',
		);
    }
	// ./ Guia de Remision

	// Obtener API y TOKEN
    public function obtenerGuiaToken($arrParams){
        $query = "SELECT
EMP.Nu_Documento_Identidad,
EMP.Txt_Usuario_Sunat_Sol,
EMP.Txt_Password_Sunat_Sol,
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
VC.Txt_Hash,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
EMP.Txt_Sunat_Token_Guia_Client_ID,
EMP.Txt_Sunat_Token_Guia_Client_Secret,
SD.Nu_Cantidad_Caracteres
FROM
guia_cabecera AS VC
JOIN empresa AS EMP ON(VC.ID_Empresa = EMP.ID_Empresa)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
WHERE
VC.ID_Guia_Cabecera = " . $arrParams['iIdGuiaCabecera'] . " LIMIT 1";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al generar formato documento electrónico',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'result' => $arrResponseSQL->row(),
			);
		}

		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No hay registro',
		);
    }
	// ./ Guia de Remision

	public function consultarDocumentoElectronicoSunat($arrParams){
		if ($arrParams['iEstadoVenta'] == 8 ) {
			$arrData = $this->DocumentoElectronicoModel->obtenerComprobante($arrParams);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];

				$Ss_SubTotal_Producto = 0.00;
				$Ss_Descuento_Producto = 0.00;
				$Ss_Total_Producto = 0.00;
				$Ss_Gravada = 0.00;
				$Ss_Inafecto = 0.00;
				$Ss_Exonerada = 0.00;
				$Ss_Gratuita = 0.00;
				$Ss_IGV = 0.00;
				$Ss_Total = 0.00;
				
				$i = 0;
				$fTotalIcbper = 0.00;
				$Po_IGV = "";
				$iCounter = 1;
				$sPrecioTipoCodigoDetalle = '01';
				$Ss_Impuesto = 0.00;
            	$Ss_Gravada = 0.00;
				$fTotalIcbperSinImpuesto=0.00;
				$iCapturaI = -1;
				$fTotalCapturaIcbper = 0;
				$iEsIcbper = 0;
				$fCantidadCapturaIcbper = 0;
				$fPrecioCapturaIcbper = 0;	
			
				$iNumImpuestoDescuento = 0;
				$iNumImpuestoDescuentoIGV = 0;
				$iNumImpuestoDescuentoEXO = 0;
				$fImpuestoConfiguracionIGV = 1;
				$fDescuentoTotalOperacionItem=0;
				foreach ($arrData as $row) {			  
					$sPrecioTipoCodigoDetalle = '01';
					$Ss_Precio_VU = $row->Ss_Precio;
					if ($row->Nu_Tipo_Impuesto == 1){//IGV
                		$Ss_Impuesto = $row->Ss_Impuesto;
						$Po_IGV = $row->Po_Impuesto;
						$Ss_Precio_VU = round($row->Ss_Precio / $row->Ss_Impuesto, 6);
						$Ss_IGV += $row->Ss_Impuesto_Producto;
						$Ss_Gravada += $row->Ss_SubTotal_Producto;

						$iNumImpuestoDescuentoIGV = 1;
						$fImpuestoConfiguracionIGV = $row->Ss_Impuesto;
					} else if ($row->Nu_Tipo_Impuesto == 2){//Inafecto - Operación Onerosa
						$Ss_Inafecto += $row->Ss_SubTotal_Producto;
					} else if ($row->Nu_Tipo_Impuesto == 3){//Exonerado - Operación Onerosa
						$iNumImpuestoDescuentoEXO = 1;
						$Ss_Exonerada += $row->Ss_SubTotal_Producto;
					} else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
						$Ss_Gratuita += $row->Ss_SubTotal_Producto;
						$sPrecioTipoCodigoDetalle = '02';
					}

					if ( $row->ID_Impuesto_Icbper == 1 )
						$fTotalIcbper += $row->Ss_Icbper;

					$fDescuentoTotalOperacionItem += $row->Ss_Descuento_Producto;
					
					$data_detalle["detalle"][$i]["ITEM"] = $iCounter;
					$data_detalle["detalle"][$i]["UNIDAD_MEDIDA_DET"] = $row->Nu_Sunat_Codigo_UM;
					$data_detalle["detalle"][$i]["CODIGO_DET"] = $row->Nu_Codigo_Barra;
					$data_detalle["detalle"][$i]["DESCRIPCION_DET"] = $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
					$data_detalle["detalle"][$i]["CANTIDAD_DET"] = $row->Qt_Producto;
					$data_detalle["detalle"][$i]["PRECIO_SIN_IGV_DET"] = $Ss_Precio_VU;
					$data_detalle["detalle"][$i]["PRECIO_DET"] = $row->Ss_Precio;
					$data_detalle["detalle"][$i]["IMPORTE_DET"] = $row->Ss_SubTotal_Producto;
					$data_detalle["detalle"][$i]["COD_TIPO_OPERACION"] = $row->Nu_Sunat_Codigo_Impuesto;
					$data_detalle["detalle"][$i]["IGV"] = $row->Ss_Impuesto_Producto;
					$data_detalle["detalle"][$i]["ISC"] = "0";
					$data_detalle["detalle"][$i]["PRECIO_TIPO_CODIGO"] = $sPrecioTipoCodigoDetalle;
					$data_detalle["detalle"][$i]["TOTAL"] = $row->Ss_Total_Producto;
					$data_detalle["detalle"][$i]["TOTAL_ICBPER"] = ($row->ID_Impuesto_Icbper == 0 ? 0.00 : $row->Ss_Icbper);
					$data_detalle["detalle"][$i]["NUMERO_LOTE_VENCIMIENTO"] = $row->Nu_Lote_Vencimiento;
					$data_detalle["detalle"][$i]["FECHA_LOTE_VENCIMIENTO"] = $row->Fe_Lote_Vencimiento;
					$data_detalle["detalle"][$i]["DESCUENTO"] = $row->Ss_Descuento_Producto;
					$data_detalle["detalle"][$i]["MARCA"] = $row->No_Marca;

					$i++;
					++$iCounter;
				}

           		$iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);

				$fDescuentoTotalOperacion = 0.00;
				$fDescuentoTotalOperacionIGV = 0.00;
				$fDescuentoTotalOperacionEXO = 0.00;
				if($arrData[0]->Ss_Descuento>0.00){
					$fDescuentoTotalOperacion = ($arrData[0]->Ss_Descuento / $iNumImpuestoDescuento);

					if ( $iNumImpuestoDescuentoEXO == 1 ) {
						$Ss_Exonerada = $Ss_Exonerada - $fDescuentoTotalOperacion;
						$fDescuentoTotalOperacionEXO = $fDescuentoTotalOperacion;
					}

					if ( $iNumImpuestoDescuentoIGV == 1 ) {
						$Ss_Gravada = $Ss_Gravada - $fDescuentoTotalOperacion;
						$Ss_IGV = ($Ss_Gravada * $fImpuestoConfiguracionIGV) - $Ss_Gravada;
						$fDescuentoTotalOperacionIGV = $fDescuentoTotalOperacion;
					}

					$fDescuentoTotalOperacion = $fDescuentoTotalOperacionEXO + $fDescuentoTotalOperacionIGV;
				}

				$No_Codigo_Medio_Pago_Sunat_PLE = '';
				$arrDataMediosPago = $this->DocumentoElectronicoModel->obtenerComprobanteMedioPago($arrParams);
				
				$sDiasCredito = '';
				$arrVentasCreditoCuotas = array();

				if ( $arrDataMediosPago['sStatus'] == 'success' ) {
					$sConcatenarMultiplesMedioPago = '';
					$No_Codigo_Medio_Pago_Sunat_PLE = $arrDataMediosPago['arrData'][0]->No_Codigo_Medio_Pago_Sunat_PLE;
					foreach ($arrDataMediosPago['arrData'] as $row) {
						$sConcatenarMultiplesMedioPago .= $row->No_Medio_Pago . ' [' . $row->Txt_Medio_Pago . ']: ' . ($No_Codigo_Medio_Pago_Sunat_PLE != '0' ? $arrData[0]->No_Signo  . ' ' . $row->Ss_Total_Medio_Pago : '') . ', ';
						
						if ( $row->codigo_interno_pago == '1' ) {//1=Credito
							$arrVentasCreditoCuotas = array(
								'venta_al_credito' => array(
									0 => array(
										'cuota' => 1,
										'fecha_de_pago' => $arrData[0]->Fe_Vencimiento,
										'importe' => $arrData[0]->Ss_Total_Saldo,
									)
								)
							);
						}
					}
					$sConcatenarMultiplesMedioPago = substr($sConcatenarMultiplesMedioPago, 0, -2);	
				} else {
					return $arrDataMediosPago;
				}

				$sDiasCredito = '';
				$arrVentasCreditoCuotas = array();
				if ( $No_Codigo_Medio_Pago_Sunat_PLE == '0' ) {
					$arrVentasCreditoCuotas = array(
						'venta_al_credito' => array(
							0 => array(
								'cuota' => 1,
								'fecha_de_pago' => $arrData[0]->Fe_Vencimiento,
								'importe' => $arrData[0]->Ss_Total_Saldo,//Saldo
							)
						)
					);
				}

				$this->load->library('EnLetras', 'el');
				$EnLetras = new EnLetras();

				/*
				Tipo de la GUÍA DE REMISIÓN RELACIONADA. Ejemplo: 1
				1 = GUÍA DE REMISIÓN REMITENTE
				2 = GUÍA DE REMISIÓN TRANSPORTISTA
				*/
				$data_guias = array();
				$cadena_de_texto = trim($arrData[0]->Txt_Garantia);
				if ( substr($cadena_de_texto, -1) == ',' )
					$cadena_de_texto = substr($cadena_de_texto, 0, -1);
    			$cadena_buscada = '-';
    			$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
    			if ( strlen($arrData[0]->Txt_Garantia) > 5 && $posicion_coincidencia !== false) {
					$arrCadena = explode(',',$arrData[0]->Txt_Garantia);
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
					$arrParamsGuia = array('ID_Documento_Cabecera' => $arrData[0]->ID_Documento_Cabecera);
					$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParamsGuia);
					if ($arrResponseDocument['sStatus'] == 'success'){
						$i = 0;
						foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
							$data_guias["guias"][$i]["guia_tipo"] = 1;
							$data_guias["guias"][$i]["guia_serie_numero"] = $rowEnlace->_ID_Serie_Documento . '-' . $rowEnlace->ID_Numero_Documento;
							$i++;
						}
					}
				}
		  
				$sCodigoTipoOperacionSunat = "01" . $arrData[0]->Nu_Codigo_Sunat_Tipo_Transaccion;
				if ($arrData[0]->Nu_Codigo_Sunat_Tipo_Transaccion=='02')
					$sCodigoTipoOperacionSunat = "0200";

				$Txt_Glosa = $arrData[0]->Txt_Glosa;
				
				//$arrData[0]->Ss_Total = (double)$arrData[0]->Ss_Total;
				//$Ss_Gratuita = (double)$Ss_Gratuita;

				//var_dump($arrData[0]->Ss_Total);
				//var_dump($Ss_Gratuita);

				$fTotal = 0.00;
				$fTotal = round(($arrData[0]->Ss_Total - $Ss_Gratuita), 2);
				$fTotal = abs($fTotal);
				//var_dump($fTotal);

				if($arrData[0]->Ss_Retencion > 0.00){
					$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Retencion, 2) . ' <br>';
					$Txt_Glosa .= '<b>Base imponible de la retención:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
					$Txt_Glosa .= '<b>Monto de la retención:</b> ' . round($arrData[0]->Ss_Retencion, 2) . ' <br>';
				}

				$iPoDetraccion=0;
				if($arrData[0]->Ss_Detraccion > 0.00){
					$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Detraccion, 2) . ' <br>';
					$Txt_Glosa .= '<b>Base imponible de la detracción:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
					$Txt_Glosa .= '<b>Monto de la detracción:</b> ' . round($arrData[0]->Ss_Detraccion, 2) . ' <br>';
					$iPoDetraccion=12;
				}
				//echo "==";
				//var_dump($fTotal);
				//echo "==";
				$data_cabecera = array(
					"operacion"	=> "consultar_comprobante_sunat",
					"TIPO_RUBRO_EMPRESA" => $arrData[0]->Nu_Tipo_Rubro_Empresa,
					"TIPO_OPERACION" => $sCodigoTipoOperacionSunat,
					"COD_TIPO_DOCUMENTO" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,
					"NRO_COMPROBANTE" => $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"SERIE_COMPROBANTE" => $arrData[0]->ID_Serie_Documento,
					"NUMERO_COMPROBANTE" => autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"TIPO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Sunat_Codigo_TDI,
					"NRO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Documento_Identidad,
					"RAZON_SOCIAL_CLIENTE" => $arrData[0]->No_Entidad,
					"DIRECCION_CLIENTE" => $arrData[0]->Txt_Direccion_Entidad,
					"CIUDAD_CLIENTE" => "",
					"COD_PAIS_CLIENTE" => "",
					"FECHA_DOCUMENTO" => $arrData[0]->Fe_Emision,
					"FECHA_HORA_DOCUMENTO" => $arrData[0]->Fe_Emision_Hora,
					"FECHA_VTO" => $arrData[0]->Fe_Vencimiento,
					"COD_MONEDA" => $arrData[0]->Nu_Sunat_Codigo_Moneda,
					"POR_IGV" => $Po_IGV,
					"TOTAL_DESCUENTO" => $fDescuentoTotalOperacion,
					"TOTAL_DESCUENTO_ITEM" => $fDescuentoTotalOperacionItem,
					"SUB_TOTAL" => $Ss_Gravada + $Ss_Inafecto + $Ss_Exonerada,
					"TOTAL_GRAVADAS" => $Ss_Gravada,
					"TOTAL_INAFECTA" => $Ss_Inafecto,
					"TOTAL_EXONERADAS" => $Ss_Exonerada,
					"TOTAL_IGV" => $Ss_IGV,
					"TOTAL_GRATUITAS" => $Ss_Gratuita,
					"TOTAL" => $fTotal,
					"ICBP" => $fTotalIcbper,
					"TIPO_COMPROBANTE_MODIFICA" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento_Modificar,
					"NRO_DOCUMENTO_MODIFICA" => $arrData[0]->ID_Serie_Documento_Modificar . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento_Modificar, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"COD_TIPO_MOTIVO" => (strlen($arrData[0]->Nu_Codigo_Motivo_Referencia) == 1 ?  "0" . $arrData[0]->Nu_Codigo_Motivo_Referencia : $arrData[0]->Nu_Codigo_Motivo_Referencia),
					"DESCRIPCION_MOTIVO" => '',
					"TOTAL_LETRAS" => $EnLetras->ValorEnLetras($fTotal, $arrData[0]->No_Moneda),
					"GLOSA" => $Txt_Glosa,
					"ORDEN_COMPRA_SERVICIO" => $arrData[0]->No_Orden_Compra_FE,
					"PLACA_VEHICULO" => $arrData[0]->No_Placa_FE,
					"DETRACCION" => $arrData[0]->Nu_Detraccion,
					"HASH_CPE" => $arrData[0]->Txt_Hash,
					"CONDICIONES_DE_PAGO" => $sDiasCredito,
					"MEDIO_DE_PAGO" => $sConcatenarMultiplesMedioPago,
					"TXT_URL_CDR" => $arrData[0]->Txt_Url_CDR,
					"formato_de_pdf" => $arrData[0]->No_Formato_PDF,
					"tipo_recepcion" => $arrData[0]->Nu_Tipo_Recepcion,
					"celular_cliente" => $arrData[0]->Nu_Celular_Entidad,
					"RETENCION" => $arrData[0]->Nu_Retencion,
					"TOTAL_RETENCION" => $arrData[0]->Ss_Retencion,
					"TOTAL_DETRACCION" => $arrData[0]->Ss_Detraccion,
					"PORCENTAJE_DETRACCION" => $arrData[0]->Po_Detraccion,
					"TOTAL_VUELTO" => (($arrData[0]->Ss_Vuelto > 0.00 && $arrData[0]->Nu_Tipo==0) ? ($arrData[0]->Ss_Vuelto + $Ss_Gratuita) : 0),
					"VENDEDOR" => (isset($_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado) ? $_SESSION['arrDataPersonal']['arrData'][0]->No_Entidad : ''),
					"NUMERO_EXPEDIENTE" => $arrData[0]->Nu_Expediente_FE,
					"CODIGO_UNIDAD_EJECUTORA" => $arrData[0]->Nu_Codigo_Unidad_Ejecutora_FE,
					"USUARIO_VENDEDOR" => $arrData[0]->No_Usuario_Venta
				);
				$data = array_merge($data_cabecera, $data_detalle, $data_guias, $arrVentasCreditoCuotas);

				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;
				
				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"arrData" => $data,
				);
				
				return $this->consultarDocumentoElectronicoSunatApi($arrParamsFE, $arrParams);
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		} else {
			return array(
				'status' => 'warning',
				'style_modal' => 'modal-warning',
				'message' => 'El comprobante debe estar con estado completado enviado',
				'message_nubefact' => 'El comprobante debe estar con estado completado enviado',
				'sStatus' => 'warning',
				'sMessage' => 'El comprobante debe estar con estado completado enviado',
				'iIdDocumentoCabecera' => 0,
				'arrMessagePSE' => 'El comprobante debe estar con estado completado enviado',
				'sCodigo' => '-1001',
			);
		}
	}

	private function consultarDocumentoElectronicoSunatApi($arrParamsFE, $arrParams){
		$arrData = $arrParamsFE['arrData'];
		$data_json = json_encode($arrData);
	
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
			$arrParams = array_merge( $arrParams, array( 'arrMessagePSE' => $leer_respuesta ) );
			$arrResponseEstadoDocumento = $this->cambiarEstadoDocumentoElectronico( $arrParams );
			if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
				if ( $arrResponseEstadoDocumento['iEstadoVenta'] == 8 && !empty($arrParams['sEmailCliente']) ) { 
					$arrParamsEmail = array_merge( $arrParams, array( 'sGenerarRespuestaJson' => false ) );
					$this->sendCorreoFacturaVentaSUNAT($arrParamsEmail);
				}

				$response = array(
					'status' => (!isset($leer_respuesta['codigo']) ? 'success' : 'warning'),
					'style_modal' => (!isset($leer_respuesta['codigo']) ? 'modal-success' : 'modal-warning'),
					'message' => (!isset($leer_respuesta['codigo']) ? 'Se recupero documento' : 'Se recupero documento - ' . $leer_respuesta['message']),
					'message_nubefact' => (!isset($leer_respuesta['codigo']) ? 'Se recupero documento' : 'Se recupero documento - ' . $leer_respuesta['message']),
					'sStatus' => (!isset($leer_respuesta['codigo']) ? 'success' : 'warning'),
					'sMessage' => (!isset($leer_respuesta['codigo']) ? 'Se recupero documento' : 'Se recupero documento - ' . $leer_respuesta['message']),
					'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
					'arrMessagePSE' => (!empty($leer_respuesta) ? $leer_respuesta['message'] : $respuesta),
					'sCodigo' => (!isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo']),
				);

				//Nuevo
				$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $response, $arrParams );

				return $response;
			} else {
				return $arrResponseEstadoDocumento;
			}
		} else {
			$response = array(
				'status' => 'warning',
				'style_modal' => 'modal-warning',
				'message' => 'No se pudo completar operación de consulta',
				'message_nubefact' => 'No se pudo completar operación de consulta',
				'sStatus' => 'warning',
				'sMessage' => 'No se pudo completar operación de consulta',
				'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
				'arrMessagePSE' => 'No existe URL del proveedor para enviar los documentos a SUNAT v5' . $respuesta,
				'sCodigo' => '-1002',
				'httpcode' => $httpcode
			);

			//Nuevo
			$this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $response, $arrParams );

			return $response;
		}
	}

	public function generarFormatoDocumentoElectronicoSunat($arrParams){
		if ($arrParams['iEstadoVenta'] == 6 || $arrParams['iEstadoVenta'] == 9 ) {
			$arrData = $this->DocumentoElectronicoModel->obtenerComprobante($arrParams);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];

				$Ss_SubTotal_Producto = 0.00;
				$Ss_Descuento_Producto = 0.00;
				$Ss_Total_Producto = 0.00;
				$Ss_Gravada = 0.00;
				$Ss_Inafecto = 0.00;
				$Ss_Exonerada = 0.00;
				$Ss_Gratuita = 0.00;
				$Ss_IGV = 0.00;
				$Ss_Total = 0.00;
				
				$i = 0;
				$fTotalIcbper = 0.00;
				$Po_IGV = "";
				$iCounter = 1;
				$sPrecioTipoCodigoDetalle = '01';
				$Ss_Impuesto = 0.00;
            	$Ss_Gravada = 0.00;
				$fTotalIcbperSinImpuesto = 0.00;
				$iCapturaI = -1;
				$fTotalCapturaIcbper = 0;
				$iEsIcbper = 0;
				$fCantidadCapturaIcbper = 0;
				$fPrecioCapturaIcbper = 0;
			
				$iNumImpuestoDescuento = 0;
				$iNumImpuestoDescuentoIGV = 0;
				$iNumImpuestoDescuentoEXO = 0;
				$fImpuestoConfiguracionIGV = 1;
				$fDescuentoTotalOperacionItem=0;
				foreach ($arrData as $row) {			  
					$sPrecioTipoCodigoDetalle = '01';
					$Ss_Precio_VU = $row->Ss_Precio;
					if ($row->Nu_Tipo_Impuesto == 1){//IGV
                		$Ss_Impuesto = $row->Ss_Impuesto;
						$Po_IGV = $row->Po_Impuesto;
						$Ss_Precio_VU = round($row->Ss_Precio / $row->Ss_Impuesto, 6);
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
						$sPrecioTipoCodigoDetalle = '02';
					}

					if ( $row->ID_Impuesto_Icbper == 1 )
						$fTotalIcbper += $row->Ss_Icbper;

					$fDescuentoTotalOperacionItem += $row->Ss_Descuento_Producto;

					$data_detalle["detalle"][$i]["ITEM"] = $iCounter;
					$data_detalle["detalle"][$i]["UNIDAD_MEDIDA_DET"] = $row->Nu_Sunat_Codigo_UM;
					$data_detalle["detalle"][$i]["CODIGO_DET"] = $row->Nu_Codigo_Barra;
					$data_detalle["detalle"][$i]["DESCRIPCION_DET"] = $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
					$data_detalle["detalle"][$i]["CANTIDAD_DET"] = $row->Qt_Producto;
					$data_detalle["detalle"][$i]["PRECIO_SIN_IGV_DET"] = $Ss_Precio_VU;
					$data_detalle["detalle"][$i]["PRECIO_DET"] = $row->Ss_Precio;
					$data_detalle["detalle"][$i]["IMPORTE_DET"] = $row->Ss_SubTotal_Producto;
					$data_detalle["detalle"][$i]["COD_TIPO_OPERACION"] = $row->Nu_Sunat_Codigo_Impuesto;
					$data_detalle["detalle"][$i]["IGV"] = $row->Ss_Impuesto_Producto;
					$data_detalle["detalle"][$i]["ISC"] = "0";
					$data_detalle["detalle"][$i]["PRECIO_TIPO_CODIGO"] = $sPrecioTipoCodigoDetalle;
					$data_detalle["detalle"][$i]["TOTAL"] = $row->Ss_Total_Producto;
					$data_detalle["detalle"][$i]["TOTAL_ICBPER"] = ($row->ID_Impuesto_Icbper == 0 ? 0.00 : $row->Ss_Icbper);
					$data_detalle["detalle"][$i]["NUMERO_LOTE_VENCIMIENTO"] = $row->Nu_Lote_Vencimiento;
					$data_detalle["detalle"][$i]["FECHA_LOTE_VENCIMIENTO"] = $row->Fe_Lote_Vencimiento;
					$data_detalle["detalle"][$i]["DESCUENTO"] = $row->Ss_Descuento_Producto;
					$data_detalle["detalle"][$i]["MARCA"] = $row->No_Marca;

					$i++;
					++$iCounter;
				}

           		$iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);

				$fDescuentoTotalOperacion = 0.00;
				$fDescuentoTotalOperacionIGV = 0.00;
				$fDescuentoTotalOperacionEXO = 0.00;
				if($arrData[0]->Ss_Descuento>0.00){
					$fDescuentoTotalOperacion = ($arrData[0]->Ss_Descuento / $iNumImpuestoDescuento);

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

				$No_Codigo_Medio_Pago_Sunat_PLE = '';
				$arrDataMediosPago = $this->DocumentoElectronicoModel->obtenerComprobanteMedioPago($arrParams);
				
				$sDiasCredito = '';
				$arrVentasCreditoCuotas = array();

				if ( $arrDataMediosPago['sStatus'] == 'success' ) {
					$sConcatenarMultiplesMedioPago = '';
					$No_Codigo_Medio_Pago_Sunat_PLE = $arrDataMediosPago['arrData'][0]->No_Codigo_Medio_Pago_Sunat_PLE;
					foreach ($arrDataMediosPago['arrData'] as $row) {
						$sConcatenarMultiplesMedioPago .= $row->No_Medio_Pago . ' [' . $row->Txt_Medio_Pago . ']: ' . ($No_Codigo_Medio_Pago_Sunat_PLE != '0' ? $arrData[0]->No_Signo  . ' ' . $row->Ss_Total_Medio_Pago : '') . ', ';

						if ( $row->codigo_interno_pago == '1' ) {//1=Credito
							$arrVentasCreditoCuotas = array(
								'venta_al_credito' => array(
									0 => array(
										'cuota' => 1,
										'fecha_de_pago' => $arrData[0]->Fe_Vencimiento,
										'importe' => $arrData[0]->Ss_Total_Saldo,
									)
								)
							);
						}
					}
					$sConcatenarMultiplesMedioPago = substr($sConcatenarMultiplesMedioPago, 0, -2);	
				} else {
					return $arrDataMediosPago;
				}

				$this->load->library('EnLetras', 'el');
				$EnLetras = new EnLetras();

				/*
				Tipo de la GUÍA DE REMISIÓN RELACIONADA. Ejemplo: 1
				1 = GUÍA DE REMISIÓN REMITENTE
				2 = GUÍA DE REMISIÓN TRANSPORTISTA
				*/
				$data_guias = array();
				$cadena_de_texto = trim($arrData[0]->Txt_Garantia);
				if ( substr($cadena_de_texto, -1) == ',' )
					$cadena_de_texto = substr($cadena_de_texto, 0, -1);
    			$cadena_buscada = '-';
    			$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
    			if ( strlen($arrData[0]->Txt_Garantia) > 5 && $posicion_coincidencia !== false) {
					$arrCadena = explode(',',$arrData[0]->Txt_Garantia);
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
					$arrParamsGuia = array('ID_Documento_Cabecera' => $arrData[0]->ID_Documento_Cabecera);
					$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParamsGuia);
					if ($arrResponseDocument['sStatus'] == 'success'){
						$i = 0;
						foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
							$data_guias["guias"][$i]["guia_tipo"] = 1;
							$data_guias["guias"][$i]["guia_serie_numero"] = $rowEnlace->_ID_Serie_Documento . '-' . $rowEnlace->ID_Numero_Documento;
							$i++;
						}
					}
				}

				$sCodigoTipoOperacionSunat = "01" . $arrData[0]->Nu_Codigo_Sunat_Tipo_Transaccion;
				if ($arrData[0]->Nu_Codigo_Sunat_Tipo_Transaccion=='02')
					$sCodigoTipoOperacionSunat = "0200";

				$Txt_Glosa = $arrData[0]->Txt_Glosa;
				$fTotal = 0.00;
				$fTotal = round(($arrData[0]->Ss_Total - $Ss_Gratuita), 2);
				$fTotal = abs($fTotal);	
				if($arrData[0]->Ss_Retencion > 0.00){
					$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Retencion, 2) . ' <br>';
					$Txt_Glosa .= '<b>Base imponible de la retención:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
					$Txt_Glosa .= '<b>Monto de la retención:</b> ' . round($arrData[0]->Ss_Retencion, 2) . ' <br>';
				}
				
				$iPoDetraccion=0;
				if($arrData[0]->Ss_Detraccion > 0.00){
					$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Detraccion, 2) . ' <br>';
					$Txt_Glosa .= '<b>Base imponible de la detracción:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
					$Txt_Glosa .= '<b>Monto de la detracción:</b> ' . round($arrData[0]->Ss_Detraccion, 2) . ' <br>';
					$iPoDetraccion=12;
				}

				$data_cabecera = array(
					"operacion"	=> (empty($arrData[0]->Txt_Hash) ? "generar_comprobante" : "consultar_comprobante_sunat"),
					"TIPO_OPERACION" => $sCodigoTipoOperacionSunat,
					"TIPO_RUBRO_EMPRESA" => $arrData[0]->Nu_Tipo_Rubro_Empresa,
					"COD_TIPO_DOCUMENTO" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,
					"NRO_COMPROBANTE" => $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"SERIE_COMPROBANTE" => $arrData[0]->ID_Serie_Documento,
					"NUMERO_COMPROBANTE" => autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"TIPO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Sunat_Codigo_TDI,
					"NRO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Documento_Identidad,
					"RAZON_SOCIAL_CLIENTE" => $arrData[0]->No_Entidad,
					"DIRECCION_CLIENTE" => $arrData[0]->Txt_Direccion_Entidad,
					"CIUDAD_CLIENTE" => "",
					"COD_PAIS_CLIENTE" => "",
					"FECHA_DOCUMENTO" => $arrData[0]->Fe_Emision,
					"FECHA_HORA_DOCUMENTO" => $arrData[0]->Fe_Emision_Hora,
					"FECHA_VTO" => $arrData[0]->Fe_Vencimiento,
					"COD_MONEDA" => $arrData[0]->Nu_Sunat_Codigo_Moneda,
					"POR_IGV" => $Po_IGV,
					"TOTAL_DESCUENTO" => $fDescuentoTotalOperacion,
					"TOTAL_DESCUENTO_ITEM" => $fDescuentoTotalOperacionItem,
					"SUB_TOTAL" => $Ss_Gravada + $Ss_Inafecto + $Ss_Exonerada,
					"TOTAL_GRAVADAS" => $Ss_Gravada,
					"TOTAL_INAFECTA" => $Ss_Inafecto,
					"TOTAL_EXONERADAS" => $Ss_Exonerada,
					"TOTAL_IGV" => $Ss_IGV,
					"TOTAL_GRATUITAS" => $Ss_Gratuita,
					"TOTAL" => $fTotal,
					"ICBP" => $fTotalIcbper,
					"TIPO_COMPROBANTE_MODIFICA" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento_Modificar,
					"NRO_DOCUMENTO_MODIFICA" => $arrData[0]->ID_Serie_Documento_Modificar . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento_Modificar, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"COD_TIPO_MOTIVO" => (strlen($arrData[0]->Nu_Codigo_Motivo_Referencia) == 1 ?  "0" . $arrData[0]->Nu_Codigo_Motivo_Referencia : $arrData[0]->Nu_Codigo_Motivo_Referencia),
					"DESCRIPCION_MOTIVO" => '',
					"TOTAL_LETRAS" => $EnLetras->ValorEnLetras($fTotal, $arrData[0]->No_Moneda),
					"GLOSA" => $Txt_Glosa,
					"ORDEN_COMPRA_SERVICIO" => $arrData[0]->No_Orden_Compra_FE,
					"PLACA_VEHICULO" => $arrData[0]->No_Placa_FE,
					"DETRACCION" => $arrData[0]->Nu_Detraccion,
					"HASH_CPE" => $arrData[0]->Txt_Hash,
					"CONDICIONES_DE_PAGO" => $sDiasCredito,
					"MEDIO_DE_PAGO" => $sConcatenarMultiplesMedioPago,
					"TXT_URL_CDR" => $arrData[0]->Txt_Url_CDR,
					"formato_de_pdf" => $arrData[0]->No_Formato_PDF,
					"tipo_recepcion" => $arrData[0]->Nu_Tipo_Recepcion,
					"celular_cliente" => $arrData[0]->Nu_Celular_Entidad,
					"RETENCION" => $arrData[0]->Nu_Retencion,
					"TOTAL_RETENCION" => $arrData[0]->Ss_Retencion,
					"TOTAL_DETRACCION" => $arrData[0]->Ss_Detraccion,
					"PORCENTAJE_DETRACCION" => $arrData[0]->Po_Detraccion,
					"TOTAL_VUELTO" => (($arrData[0]->Ss_Vuelto > 0.00 && $arrData[0]->Nu_Tipo==0) ? ($arrData[0]->Ss_Vuelto + $Ss_Gratuita) : 0),
					"VENDEDOR" => (isset($_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado) ? $_SESSION['arrDataPersonal']['arrData'][0]->No_Entidad : ''),
					"NUMERO_EXPEDIENTE" => $arrData[0]->Nu_Expediente_FE,
					"CODIGO_UNIDAD_EJECUTORA" => $arrData[0]->Nu_Codigo_Unidad_Ejecutora_FE,
					"USUARIO_VENDEDOR" => $arrData[0]->No_Usuario_Venta
				);
				$data = array_merge($data_cabecera, $data_detalle, $data_guias, $arrVentasCreditoCuotas);
				
				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;
				
				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"arrData" => $data,
				);
				
				return $this->enviarDocumentoElectronicoProveedorSunat($arrParamsFE, $arrParams);
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		} else {
			$arrData = $this->DocumentoElectronicoModel->obtenerComprobante($arrParams);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];
				
				if ( !empty($arrData[0]->Txt_Url_CDR) ) {
					if ( $arrParams['sTipoBajaSunat'] == 'RC') {
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
						foreach ($arrData as $row) {
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
						if($arrData[0]->Ss_Descuento>0.00){
							$fDescuentoTotalOperacion = ($arrData[0]->Ss_Descuento / $iNumImpuestoDescuento);

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

						$data = array(
							"operacion" => "generar_anulacion",
							"CODIGO" => $arrParams['sTipoBajaSunat'],
							"FECHA_REFERENCIA" => $arrData[0]->Fe_Emision,
							"FECHA_DOCUMENTO" => dateNow('fecha'),
							"TXT_URL_CDR" => $arrData[0]->Txt_Url_CDR,
							"detalle" => array(
								"0" => array(
									"ITEM" => 1,
									"TIPO_COMPROBANTE" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,
									"NRO_COMPROBANTE" => $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
									"TIPO_DOCUMENTO" => $arrData[0]->Nu_Sunat_Codigo_TDI,
									"NRO_DOCUMENTO" => $arrData[0]->Nu_Documento_Identidad,			
									"TIPO_COMPROBANTE_REF" => (empty($arrData[0]->ID_Serie_Documento_Modificar) ? '' : $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento_Modificar),
									"NRO_COMPROBANTE_REF" => (empty($arrData[0]->ID_Serie_Documento_Modificar) ? '' : $arrData[0]->ID_Serie_Documento_Modificar . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento_Modificar, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT)),
									"STATU" => "3",
									"COD_MONEDA" => $arrData[0]->Nu_Sunat_Codigo_Moneda,			
									"GRAVADA" => round($Ss_Gravada, 2),
									"INAFECTO" => $Ss_Inafecto,
									"EXONERADO" => $Ss_Exonerada,
									"EXPORTACION" => 0.00,
									"GRATUITAS" => $Ss_Gratuita,
									"IGV" => round($Ss_IGV, 2),
									"TOTAL" => $Ss_Total,
									"ISC" => "0",
									"OTROS" => "0",
									"CARGO_X_ASIGNACION" => "0",
									"MONTO_CARGO_X_ASIG" => "0",
								),
							),
						);
					} else if ( $arrParams['sTipoBajaSunat'] == 'RA' ) {
						$data = array(
							"operacion" => "generar_anulacion",
							"CODIGO" => $arrParams['sTipoBajaSunat'],
							"FECHA_REFERENCIA" => $arrData[0]->Fe_Emision,
							"FECHA_BAJA" => dateNow('fecha'),
							"TXT_URL_CDR" => $arrData[0]->Txt_Url_CDR,
							"detalle" => array(
								"0" => array(
									"ITEM" => 1,
									"TIPO_COMPROBANTE" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,
									"SERIE" => $arrData[0]->ID_Serie_Documento,
									"NUMERO" => autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
									"DESCRIPCION" => "ERROR DE DIGITACION",
								),
							),
						);
					}

					$ruta = $arrData[0]->Txt_FE_Ruta;
					$token = $arrData[0]->Txt_FE_Token;

					$arrParamsFE = array(
						"ruta" => $ruta,
						"token" => $token,
						"arrData" => $data,
					);
					
					return $this->enviarDocumentoElectronicoProveedorSunat($arrParamsFE, $arrParams);
				} else {
					return array(
						'status' => 'warning',
						'style_modal' => 'modal-warning',
						'message' => 'No se puede anular si no tiene CDR puede recuperar o esperar al día siguiente',
						'message_nubefact' => 'No se puede anular si no tiene CDR puede recuperar o esperar al día siguiente',
						'sStatus' => 'warning',
						'sMessage' => 'No se puede anular si no tiene CDR puede recuperar o esperar al día siguiente',
						'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
						'arrMessagePSE' => 'No se puede anular si no tiene CDR puede recuperar o esperar al día siguiente',
						'sCodigo' => '-501',
						'httpcode' => '200',
						'arrData' => '',
						'arrParams' => '',
					);
				}// ./ validacion de if - else anulacion CDR
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		}// ./ if - else generar formato de documento electronico SUNAT
	} // ./ generarFormatoDocumentoElectronicoSunat

	private function enviarDocumentoElectronicoProveedorSunat($arrParamsFE, $arrParams){
		$arrData = $arrParamsFE['arrData'];
		$data_json = json_encode($arrData);
	
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
			
			if ( ($arrParams['iEstadoVenta'] == 6 || $arrParams['iEstadoVenta'] == 9) && !empty($leer_respuesta) ) {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 8 ));
			} else if ( empty($leer_respuesta) ) {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
			} else {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 10 ));
			}

			$arrParams = array_merge( $arrParams, array( 'arrMessagePSE' => $leer_respuesta ) );
			$arrResponseEstadoDocumento = $this->cambiarEstadoDocumentoElectronico( $arrParams );
			if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
				if ( $arrResponseEstadoDocumento['iEstadoVenta'] == 8 && !empty($arrParams['sEmailCliente']) ) { 
					$arrParamsEmail = array_merge( $arrParams, array( 'sGenerarRespuestaJson' => false ) );
					$this->sendCorreoFacturaVentaSUNAT($arrParamsEmail);
				}
				
				return array(
					'status' => (!isset($leer_respuesta['codigo']) ? 'success' : 'warning'),
					'style_modal' => (!isset($leer_respuesta['codigo']) ? 'modal-success' : 'modal-warning'),
					'message' => (!isset($leer_respuesta['codigo']) ? 'Venta completada' : 'Venta completada - ' . $leer_respuesta['message']),
					'message_nubefact' => (!isset($leer_respuesta['codigo']) ? 'Venta completada' : 'Venta completada - ' . $leer_respuesta['message']),
					'sStatus' => (!isset($leer_respuesta['codigo']) ? 'success' : 'warning'),
					'sMessage' => (!isset($leer_respuesta['codigo']) ? 'Venta completada' : 'Venta completada - ' . $leer_respuesta['message']),
					'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
					'arrMessagePSE' => (isset($leer_respuesta) && !empty($leer_respuesta) ? $leer_respuesta['message'] : $respuesta),
					'sCodigo' => (!isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo']),
					'enlace_del_pdf' => (!isset($leer_respuesta['codigo']) ? $leer_respuesta['enlace_del_pdf'] : ''),
				);
			} else {
				return $arrResponseEstadoDocumento;
			}
		} else {
			$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
			$arrResponseEstadoDocumento = $this->cambiarEstadoDocumentoElectronico( $arrParams );
			return array(
				'status' => 'warning',
				'style_modal' => 'modal-warning',
				'message' => 'Venta completada - No enviada a SUNAT',
				'message_nubefact' => 'Venta completada - No enviada a SUNAT' . $respuesta,
				'sStatus' => 'warning',
				'sMessage' => 'Venta completada - No enviada a SUNAT',
				'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
				'arrMessagePSE' => 'No existe URL del proveedor para enviar los documentos a SUNAT v2' . $respuesta,
				'sCodigo' => '-500',
				'httpcode' => $httpcode,
				'arrData' => $arrData,
				'arrParams' => $arrParams,
			);
		}
	}

	public function generarFormatoDocumentoElectronicoGuiaSunat($arrParams){
		if ($arrParams['iEstadoVenta'] == 6 || $arrParams['iEstadoVenta'] == 9 ) {
			$arrData = $this->DocumentoElectronicoModel->obtenerGuia($arrParams);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];
				
				$i = 0;
				$iCounter = 1;
				foreach ($arrData as $row) {
					$data_detalle["detalle"][$i]["ITEM"] = $iCounter;
					$data_detalle["detalle"][$i]["UNIDAD_MEDIDA"] = $row->Nu_Sunat_Codigo_UM;
					$data_detalle["detalle"][$i]["CANTIDAD"] = $row->Qt_Producto;
					$data_detalle["detalle"][$i]["ORDER_ITEM"] = $iCounter;
					$data_detalle["detalle"][$i]["CODIGO"] = $row->Nu_Codigo_Barra;
					$data_detalle["detalle"][$i]["DESCRIPCION"] = $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
					$i++;
					++$iCounter;
				}
			
				$Valor_Ubigeo_Inei_Partida = 'LIMA - LIMA - LIMA';
				if(!empty($arrData[0]->Valor_Ubigeo_Inei_Partida)) {
					$Valor_Ubigeo_Inei_Partida=$arrData[0]->Valor_Ubigeo_Inei_Partida;
					$Valor_Ubigeo_Inei_Partida=preg_replace('/[0-9]+/', '', $Valor_Ubigeo_Inei_Partida);
					$Valor_Ubigeo_Inei_Partida = strtoupper($Valor_Ubigeo_Inei_Partida);
				}
			
				$Valor_Ubigeo_Inei_Llegada = 'LIMA - LIMA - LIMA';
				if(!empty($arrData[0]->Valor_Ubigeo_Inei_Llegada)) {
					$Valor_Ubigeo_Inei_Llegada=$arrData[0]->Valor_Ubigeo_Inei_Llegada;
					$Valor_Ubigeo_Inei_Llegada=preg_replace('/[0-9]+/', '', $Valor_Ubigeo_Inei_Llegada);
					$Valor_Ubigeo_Inei_Llegada = strtoupper($Valor_Ubigeo_Inei_Llegada);
				}

				$sTipoDocumentoRelacionVenta = '';
				$sSerieDocumentoRelacionVenta = '';
				$sNumeroDocumentoRelacionVenta = '';
				$arrParamsBuscarGuiaFactura = array('ID_Guia_Cabecera' => $arrParams['iIdDocumentoCabecera']);
				$arrResponseRelacionVenta = $this->HelperModel->getGuianEnlaceOrigen($arrParamsBuscarGuiaFactura);
				if ($arrResponseRelacionVenta['sStatus'] == 'success') {
					foreach ($arrResponseRelacionVenta['arrData'] as $rowEnlace) {
						$sTipoDocumentoRelacionVenta = $rowEnlace->No_Tipo_Documento_Breve;
						$sSerieDocumentoRelacionVenta = $rowEnlace->_ID_Serie_Documento;
						$sNumeroDocumentoRelacionVenta = $rowEnlace->ID_Numero_Documento;
					}
				}

				$data_cabecera = array(
					"operacion"	=> "generar_comprobante",
					"NRO_COMPROBANTE" => $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"SERIE_COMPROBANTE" => $arrData[0]->ID_Serie_Documento,
					"NUMERO_COMPROBANTE" => autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"FECHA_DOCUMENTO" => $arrData[0]->Fe_Emision,
					"COD_TIPO_DOCUMENTO" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,
					"GLOSA" => $arrData[0]->Txt_Glosa,
					"ITEM_ENVIO"  => 1,//antes era 1 = Preguntar
    				"COD_MOTIVO_TRASLADO" => $arrData[0]->Nu_Codigo_Motivo_Traslado_Sunat,
    				"DESCRIPCION_MOTIVO_TRASLADO" => $arrData[0]->No_Motivo_Traslado_Sunat,
					"COD_UND_PESO_BRUTO" => "KGM",
					"PESO_BRUTO" => $arrData[0]->Ss_Peso_Bruto,
					"TOTAL_BULTOS" => $arrData[0]->Nu_Bulto,
    				"COD_MODALIDAD_TRASLADO" => $arrData[0]->No_Tipo_Transporte,//TIPO TRANSPORTE 01 PUBLICO 02 PRIVADO
    				"FECHA_INICIO" => $arrData[0]->Fe_Traslado,
					"TIPO_DOCUMENTO_TRANSPORTISTA" => $arrData[0]->Nu_Sunat_Codigo_TDI_Transporte,
					"NRO_DOCUMENTO_TRANSPORTISTA" => $arrData[0]->Nu_Documento_Identidad_Transportista,
					"RAZON_SOCIAL_TRANSPORTISTA" => $arrData[0]->No_Entidad_Transportista,
					"PLACA_VEHICULO" => $arrData[0]->No_Placa,
					"COD_TIPO_DOC_CHOFER" => "0",
					"NRO_DOC_CHOFER" => "0",
					"NOMBRES_CHOFER" => "clientes",
					"APELLIDOS_CHOFER" => "varios",
					"LIC_CONDUCIR_CHOFER" => $arrData[0]->No_Licencia,
					"PLACA_CARRETA" => "",
					"TIPO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Sunat_Codigo_TDI,
					"NRO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Documento_Identidad,
					"RAZON_SOCIAL_CLIENTE" => $arrData[0]->No_Entidad,
					"COD_UBIGEO_ORIGEN" => (!empty($arrData[0]->ID_Ubigeo_Inei_Partida) ? $arrData[0]->ID_Ubigeo_Inei_Partida : "150101"),
					"NOMBRE_UBIGEO_ORIGEN" => $Valor_Ubigeo_Inei_Partida,
					"DIRECCION_ORIGEN" => $arrData[0]->Txt_Direccion_Origen,
					"COD_UBIGEO_DESTINO" => $arrData[0]->ID_Ubigeo_Inei_Llegada,
					"NOMBRE_UBIGEO_DESTINO" => $Valor_Ubigeo_Inei_Llegada,
					"DIRECCION_DESTINO" => $arrData[0]->Txt_Direccion_Destino,
					"TOTAL" => $arrData[0]->Ss_Total,
					"HASH_CPE" => $arrData[0]->Txt_Hash,
					"TOKEN_GUIA_CLIENT_ID" => $arrData[0]->Txt_Sunat_Token_Guia_Client_ID,
					"TOKEN_GUIA_CLIENT_SECRET" => $arrData[0]->Txt_Sunat_Token_Guia_Client_Secret,
					"TIPO_DOCUMENTO_RELACION_VENTA" => $sTipoDocumentoRelacionVenta,
					"SERIE_DOCUMENTO_RELACION_VENTA" => $sSerieDocumentoRelacionVenta,
					"NUMERO_DOCUMENTO_RELACION_VENTA" => $sNumeroDocumentoRelacionVenta
				);
				$data = array_merge($data_cabecera, $data_detalle);

				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;
				
				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"arrData" => $data,
				);
				
				return $this->enviarDocumentoElectronicoProveedorGuiaSunat($arrParamsFE, $arrParams);
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		} else {
			$arrData = $this->DocumentoElectronicoModel->obtenerGuia($arrParams);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];
				
				$i = 0;
				$iCounter = 1;
				foreach ($arrData as $row) {
					$data_detalle["detalle"][$i]["ITEM"] = $iCounter;
					$data_detalle["detalle"][$i]["UNIDAD_MEDIDA"] = $row->Nu_Sunat_Codigo_UM;
					$data_detalle["detalle"][$i]["CANTIDAD"] = $row->Qt_Producto;
					$data_detalle["detalle"][$i]["ORDER_ITEM"] = $iCounter;
					$data_detalle["detalle"][$i]["CODIGO"] = $row->Nu_Codigo_Barra;
					$data_detalle["detalle"][$i]["DESCRIPCION"] = $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
					$i++;
					++$iCounter;
				}
				
				$data_cabecera = array(
					"operacion"	=> "generar_comprobante",
					"NRO_COMPROBANTE" => $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"SERIE_COMPROBANTE" => $arrData[0]->ID_Serie_Documento,
					"NUMERO_COMPROBANTE" => autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
					"FECHA_DOCUMENTO" => $arrData[0]->Fe_Emision,
					"COD_TIPO_DOCUMENTO" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,
					"GLOSA" => $arrData[0]->Txt_Glosa,
					"ITEM_ENVIO"  => 1,//antes era 1 = Preguntar
    				"COD_MOTIVO_TRASLADO" => $arrData[0]->Nu_Codigo_Motivo_Traslado_Sunat,
    				"DESCRIPCION_MOTIVO_TRASLADO" => $arrData[0]->No_Motivo_Traslado_Sunat,
					"COD_UND_PESO_BRUTO" => "KGM",
					"PESO_BRUTO" => $arrData[0]->Ss_Peso_Bruto,
					"TOTAL_BULTOS" => $arrData[0]->Nu_Bulto,
    				"COD_MODALIDAD_TRASLADO" => $arrData[0]->No_Tipo_Transporte,//TIPO TRANSPORTE 01 PUBLICO 02 PRIVADO
    				"FECHA_INICIO" => $arrData[0]->Fe_Traslado,
					"TIPO_DOCUMENTO_TRANSPORTISTA" => $arrData[0]->Nu_Sunat_Codigo_TDI_Transporte,
					"NRO_DOCUMENTO_TRANSPORTISTA" => $arrData[0]->Nu_Documento_Identidad_Transportista,
					"RAZON_SOCIAL_TRANSPORTISTA" => $arrData[0]->No_Entidad_Transportista,
					"PLACA_VEHICULO" => $arrData[0]->No_Placa,
					"COD_TIPO_DOC_CHOFER" => "0",
					"NRO_DOC_CHOFER" => "0",
					"NOMBRES_CHOFER" => "clientes",
					"APELLIDOS_CHOFER" => "varios",
					"LIC_CONDUCIR_CHOFER" => $arrData[0]->No_Licencia,
					"PLACA_CARRETA" => "",
					"TIPO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Sunat_Codigo_TDI,
					"NRO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Documento_Identidad,
					"RAZON_SOCIAL_CLIENTE" => $arrData[0]->No_Entidad,
					"COD_UBIGEO_ORIGEN" => (!empty($arrData[0]->ID_Ubigeo_Inei_Partida) ? $arrData[0]->ID_Ubigeo_Inei_Partida : "150101"),
					"DIRECCION_ORIGEN" => $arrData[0]->Txt_Direccion_Origen,
					"COD_UBIGEO_DESTINO" => $arrData[0]->ID_Ubigeo_Inei_Llegada,
					"DIRECCION_DESTINO" => $arrData[0]->Txt_Direccion_Destino,
					"FLG_ANULADO" => "1",//ANULAR
					"DOC_REFERENCIA_ANU" => $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),//ANULAR
					"COD_TIPO_DOC_REFANU" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,//ANULAR
					"TOTAL" => $arrData[0]->Ss_Total,
					"HASH_CPE" => $arrData[0]->Txt_Hash,
					"TOKEN_GUIA_CLIENT_ID" => $arrData[0]->Txt_Sunat_Token_Guia_Client_ID,
					"TOKEN_GUIA_CLIENT_SECRET" => $arrData[0]->Txt_Sunat_Token_Guia_Client_Secret
				);
				$data = array_merge($data_cabecera, $data_detalle);
				
				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;

				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"arrData" => $data,
				);
				
				return $this->enviarDocumentoElectronicoProveedorGuiaSunat($arrParamsFE, $arrParams);
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		}// ./ if - else generar formato de documento electronico SUNAT
	} // ./ generarFormatoDocumentoElectronicoGuiaSunat

	private function enviarDocumentoElectronicoProveedorGuiaSunat($arrParamsFE, $arrParams){
		$arrData = $arrParamsFE['arrData'];
		$data_json = json_encode($arrData);
	
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
		//array_debug($respuesta);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );	
		curl_close($ch);
		// Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);
			
			if ( ($arrParams['iEstadoVenta'] == 6 || $arrParams['iEstadoVenta'] == 9) && !empty($leer_respuesta) ) {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 8 ));
			} else if ( empty($leer_respuesta) ) {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
			} else {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 10 ));
			}
//array_debug($leer_respuesta);
			$arrParams = array_merge( $arrParams, array( 'arrMessagePSE' => $leer_respuesta ) );
			$arrResponseEstadoDocumento = $this->cambiarEstadoGuiaElectronico( $arrParams );
			if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
				if ( $arrResponseEstadoDocumento['iEstadoVenta'] == 8 && !empty($arrParams['sEmailCliente']) ) { 
					$arrParamsEmail = array_merge( $arrParams, array( 'sGenerarRespuestaJson' => false ) );
					$this->sendCorreoFacturaVentaSUNAT($arrParamsEmail);
				}
				
				return array(
					'status' => (!isset($leer_respuesta['codigo']) ? 'success' : 'warning'),
					'style_modal' => (!isset($leer_respuesta['codigo']) ? 'modal-success' : 'modal-warning'),
					'message' => (!isset($leer_respuesta['codigo']) ? 'Registro completado ' . $arrParams['iEstadoVenta'] : 'Registro completado ' . $arrParams['iEstadoVenta'] . ' - ' . $leer_respuesta['message']),
					'message_nubefact' => (!isset($leer_respuesta['codigo']) ? 'Registro completado ' . $arrParams['iEstadoVenta'] : 'Registro completado ' . $arrParams['iEstadoVenta'] . ' - ' . $leer_respuesta['message']),
					'sStatus' => (!isset($leer_respuesta['codigo']) ? 'success' : 'warning'),
					'sMessage' => (!isset($leer_respuesta['codigo']) ? 'Registro completado ' . $arrParams['iEstadoVenta'] : 'Registro completado ' . $arrParams['iEstadoVenta'] . ' - ' . $leer_respuesta['message']),
					'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
					//'arrMessagePSE' => (!empty($leer_respuesta) ? $leer_respuesta['message'] : $respuesta),
					'sCodigo' => (!isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo']),
					'arrMessagePSE' => $respuesta,
					'arrData' => $arrData
				);
			} else {
				return $arrResponseEstadoDocumento;
			}
		} else {
			$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
			$arrResponseEstadoDocumento = $this->cambiarEstadoGuiaElectronico( $arrParams );
			return array(
				'status' => 'warning',
				'style_modal' => 'modal-warning',
				'message' => 'Registro completado - No enviada a SUNAT',
				'message_nubefact' => 'Registro completado - No enviada a SUNAT' . $respuesta,
				'sStatus' => 'warning',
				'sMessage' => 'Registro completado - No enviada a SUNAT',
				'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
				'arrMessagePSE' => 'No existe URL del proveedor para enviar los documentos a SUNAT v3' . $respuesta,
				'sCodigo' => '-500',
				'httpcode' => $httpcode,
				'arrData' => $arrData,
			);
		}
	}
	
	//GUIA ELECTRONICA NUBEFACT
	public function generarFormatoDocumentoElectronicoGuia($arrParams){
		if ($arrParams['iEstadoVenta'] == 6 || $arrParams['iEstadoVenta'] == 9 ) {
			$arrData = $this->DocumentoElectronicoModel->obtenerGuia($arrParams);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];

				//validaciones nubefact pse
				if (empty($arrData[0]->Txt_Direccion_Destino)) {
					return array(
						'status' => 'warning',
						'style_modal' => 'modal-warning',
						'message' => 'Cliente no tiene dirección. Ventas > Reglas de Ventas > Clientes',
						'message_nubefact' => 'Cliente no tiene dirección. Ventas > Reglas de Ventas > Clientes',
						'sStatus' => 'warning',
						'sMessage' => 'Cliente no tiene dirección. Ventas > Reglas de Ventas > Clientes',
						'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
						'arrMessagePSE' => 'Cliente no tiene dirección. Ventas > Reglas de Ventas > Clientes',
						'sCodigo' => '1',
						'httpcode' => '200'
					);
				}

				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;
								
				$i = 0;
				foreach ($arrData as $row) {					
					$data_detalle["items"][$i]["unidad_de_medida"] = $row->Nu_Sunat_Codigo_UM;
					$data_detalle["items"][$i]["codigo"] = $row->Nu_Codigo_Barra;
					$data_detalle["items"][$i]["descripcion"] = $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
					$data_detalle["items"][$i]["cantidad"] = $row->Qt_Producto;
					$i++;
				}
//01 = cuando es transporte publico no va ningun dato del transportista
//02 = cuando es privado si se tiene que llenar toda la informacion
				$iIdClienteTipoDocumentoIdentidad = $arrData[0]->Nu_Sunat_Codigo_TDI;
				$sNombreCliente = $arrData[0]->No_Entidad;
				$data_cabecera = array(
					"operacion"							=> "generar_guia",
					"tipo_de_comprobante"               => '7',
					"serie"                             => $arrData[0]->ID_Serie_Documento,
					"numero"							=> $arrData[0]->ID_Numero_Documento,
					"cliente_tipo_de_documento"			=> $iIdClienteTipoDocumentoIdentidad,
					"cliente_numero_de_documento"		=> $arrData[0]->Nu_Documento_Identidad,
					"cliente_denominacion"              => $sNombreCliente,
					"cliente_direccion"                 => $arrData[0]->Txt_Direccion_Destino,
					"cliente_email" => "",
					"cliente_email_1" => "",
					"cliente_email_2" => "",
					"fecha_de_emision" => ToDateBDNubefactPSE($arrData[0]->Fe_Emision),
					"observaciones" => $arrData[0]->Txt_Glosa,
					"motivo_de_traslado" => $arrData[0]->Nu_Codigo_Motivo_Traslado_Sunat,
					"peso_bruto_total" => $arrData[0]->Ss_Peso_Bruto,
					"numero_de_bultos" => $arrData[0]->Nu_Bulto,
					"tipo_de_transporte" => $arrData[0]->No_Tipo_Transporte,
					"fecha_de_inicio_de_traslado" => ToDateBDNubefactPSE($arrData[0]->Fe_Traslado),
					"transportista_documento_tipo" => ($arrData[0]->Nu_Sunat_Codigo_TDI_Transporte == 0 ? '-' : $arrData[0]->Nu_Sunat_Codigo_TDI_Transporte),
					"transportista_documento_numero" => $arrData[0]->Nu_Documento_Identidad_Transportista,
					"transportista_denominacion" => $arrData[0]->No_Entidad_Transportista,
					"transportista_placa_numero" => (!empty($arrData[0]->No_Placa) ? $arrData[0]->No_Placa : '.'),
					"conductor_documento_tipo" => $arrData[0]->Nu_Sunat_Codigo_TDI_Transporte,
					"conductor_documento_numero" => $arrData[0]->Nu_Documento_Identidad_Transportista,
					"conductor_denominacion" => "clientes varios",
					"conductor_nombre" => "clientes varios",
					"conductor_apellidos" => "clientes varios",
					"conductor_numero_licencia" => $arrData[0]->No_Licencia,
					"punto_de_partida_ubigeo" => (!empty($arrData[0]->ID_Ubigeo_Inei_Partida) ? $arrData[0]->ID_Ubigeo_Inei_Partida : "150101"),
					"punto_de_partida_direccion" => $arrData[0]->Txt_Direccion_Origen,
					"punto_de_llegada_ubigeo" => $arrData[0]->ID_Ubigeo_Inei_Llegada,
					"punto_de_llegada_direccion" => $arrData[0]->Txt_Direccion_Destino,
					"enviar_automaticamente_a_la_sunat" => false,
					"enviar_automaticamente_al_cliente" => false,
					"codigo_unico" => "",
					"formato_de_pdf" => "",
				);
				$data = array_merge($data_cabecera, $data_detalle);

				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"arrData" => $data,
				);
				return $this->enviarGuiaElectronicoProveedor($arrParamsFE, $arrParams);
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		} else {
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se puede anular',
			);
		}
		// ./ if de generar estao 6
	} // ./ generarFormatoDocumentoElectronicoGuia

	private function enviarGuiaElectronicoProveedor($arrParamsFE, $arrParams){
		$arrData = $arrParamsFE['arrData'];
		$data_json = json_encode($arrData);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $arrParamsFE['ruta']);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$arrParamsFE['token'].'"',
			'Content-Type: application/json',
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respuesta = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );	
		curl_close($ch);
		// Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);
			if (!isset($leer_respuesta['errors']) && !empty($leer_respuesta) || isset($leer_respuesta['codigo']) ) {
				if ( $arrParams['iEstadoVenta'] == 6 ) {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 8 ));
				} else {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 10 ));
				}

				$arrParams = array_merge( $arrParams, array( 'arrMessagePSE' => $leer_respuesta ) );
				$arrResponseEstadoDocumento = $this->cambiarEstadoGuiaElectronico( $arrParams );
				if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
					if ( $arrResponseEstadoDocumento['iEstadoVenta'] == 8 && !empty($arrParams['sEmailCliente']) ) { 
						$arrParamsEmail = array_merge( $arrParams, array( 'sGenerarRespuestaJson' => false ) );
						$this->sendCorreoFacturaVentaSUNAT($arrParamsEmail);
					}
		
					return array(
						'sStatus' => !isset($leer_respuesta['codigo']) ? 'success' : 'warning',
						'sMessage' => !isset($leer_respuesta['codigo']) ? 'Venta completada' : 'Venta completada - ' . $leer_respuesta['errors'],
						'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
						'arrMessagePSE' => !isset($leer_respuesta['errors']) ? 'Comprobante aceptado' : $leer_respuesta['errors'],
						'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
					);
				} else {
					return $arrResponseEstadoDocumento;
				}
			} else {
				if ( $arrParams['iEstadoVenta'] == 6 ) {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
				} else {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 11 ));
				}
				
				$arrResponseEstadoDocumento = $this->cambiarEstadoGuiaElectronico( $arrParams );
				if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
					return array(
						'sStatus' => 'danger',
						'sMessage' => (!empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT'),
						'iCodigoProveedorDocumentoElectronico' => $arrParams['iCodigoProveedorDocumentoElectronico'],
						'arrMessagePSE' => (!empty($leer_respuesta) ? $leer_respuesta : 'Venta guardada pero no se envio a SUNAT'),
						'sCodigo' => '-200',
					);
				} else {
					return $arrResponseEstadoDocumento;
				}
			}
		} else {
			if ( $arrParams['iEstadoVenta'] == 6 ) {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
			} else {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 11 ));
			}

			$arrResponseEstadoDocumento = $this->cambiarEstadoDocumentoElectronico( $arrParams );
			if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
				return array(
					'status' => 'warning',
					'style_modal' => 'modal-warning',
					'message' => 'No hay conexión. Venta completada - No enviada a SUNAT',
					'message_nubefact' => 'Venta completada - No enviada a SUNAT' . $respuesta,
					'sStatus' => 'warning',
					'sMessage' => 'No hay conexión. Venta completada - No enviada a SUNAT',
					'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
					'arrMessagePSE' => 'No hay conexión. No existe URL del proveedor para enviar los documentos a SUNAT v1' . $respuesta,
					'sCodigo' => '-500',
					'httpcode' => $httpcode,
					'data' => $data_json
				);
			} else {
				return $arrResponseEstadoDocumento;
			}
		}
	}
	// ./ GUIA ELECTRONICA NUBEFACT
	
	//GUIA ELECTRONICA CONSULTAR ESTADO NUBEFACT
	public function consultarGuiaElectronicoPSENubefactReseller($arrParams){
		$arrResponse = $this->DocumentoElectronicoModel->obtenerGuiaToken($arrParams);

		$data = array(
			"operacion"				=> "consultar_guia",
			"tipo_de_comprobante"   => $arrResponse['result']->ID_Tipo_Documento,
			"serie"                 => $arrResponse['result']->ID_Serie_Documento,
			"numero"				=> $arrResponse['result']->ID_Numero_Documento
		);

		$data_json = json_encode($data);

		$ruta = $arrResponse['result']->Txt_FE_Ruta;
		$token = $arrResponse['result']->Txt_FE_Token;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ruta);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$token.'"',
			'Content-Type: application/json',
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respuesta = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );
		curl_close($ch);
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);
			if ( isset($leer_respuesta['aceptada_por_sunat']) && $leer_respuesta['aceptada_por_sunat'] == true ) {
				//ejecuto update en BD
				
				$data = array(
					'Txt_Url_Comprobante' => $leer_respuesta['enlace'],
					'Txt_Url_PDF' => $leer_respuesta['enlace_del_pdf'],
					'Txt_Url_XML' => $leer_respuesta['enlace_del_xml'],
					'Txt_Url_CDR' => $leer_respuesta['enlace_del_cdr'],
					'Txt_QR' => $leer_respuesta['cadena_para_codigo_qr']
				);
				
				if ($this->db->update('guia_cabecera', $data, array('ID_Guia_Cabecera' => $arrParams['iIdGuiaCabecera'])) > 0) {
					return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
				} else {
					return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'no se actualizo registro');
				}
			} else {//aceptada por SUNAT
				return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => $leer_respuesta['sunat_soap_error']);
			}
		} else {
			$leer_respuesta = json_decode($respuesta, true);
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => $leer_respuesta['errors']);
		}//problemas de gatwey

	} // ./ GUIA ELECTRONICA CONSULTAR ESTADO NUBEFACT

	public function generarFormatoDocumentoElectronico($arrParams){
		if ($arrParams['iEstadoVenta'] == 6) {
			$arrData = $this->DocumentoElectronicoModel->obtenerComprobante($arrParams);
			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];

				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;
				
				$Ss_SubTotal_Producto = 0.00;
				$Ss_Descuento_Producto = 0.00;
				$Ss_Total_Producto = 0.00;
				$Ss_Gravada = 0.00;
				$Ss_Inafecto = 0.00;
				$Ss_Exonerada = 0.00;
				$Ss_Gratuita = 0.00;
				$Ss_IGV = 0.00;
				$Ss_Total = 0.00;
				
				$i = 0;
				$fTotalIcbper = 0.00;
				$Po_IGV = "";

				$iNumImpuestoDescuento = 0;
				$iNumImpuestoDescuentoIGV = 0;
				$iNumImpuestoDescuentoEXO = 0;
				$fImpuestoConfiguracionIGV = 1;
				$fDescuentoItem = 0;
				$iCapturaI = -1;
				$fTotalCapturaIcbper = 0;
				foreach ($arrData as $row) {
					if ( $row->ID_Impuesto_Icbper == 1 )
						$fTotalIcbper += $row->Ss_Icbper;
			  
					$Ss_Precio_VU = $row->Ss_Precio;
					if ($row->Nu_Tipo_Impuesto == 1){//IGV
						$Po_IGV = $row->Po_Impuesto;
						$Ss_Precio_VU = round($row->Ss_Precio / $row->Ss_Impuesto, 6);
						$Ss_IGV += $row->Ss_Impuesto_Producto;
						$Ss_Gravada += $row->Ss_SubTotal_Producto;

						$iNumImpuestoDescuentoIGV = 1;
						$fImpuestoConfiguracionIGV = $row->Ss_Impuesto;
						$fDescuentoItem += $row->Ss_Descuento_Producto;
					} else if ($row->Nu_Tipo_Impuesto == 2){//Inafecto - Operación Onerosa
						$Ss_Inafecto += $row->Ss_SubTotal_Producto;
					} else if ($row->Nu_Tipo_Impuesto == 3){//Exonerado - Operación Onerosa
						$Ss_Exonerada += $row->Ss_SubTotal_Producto;
                		$iNumImpuestoDescuentoEXO = 1;
						$fDescuentoItem += $row->Ss_Descuento_Producto;
					} else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
						$Ss_Gratuita += $row->Ss_SubTotal_Producto;
					}
					
					$data_detalle["items"][$i]["unidad_de_medida"] = $row->Nu_Sunat_Codigo_UM;
					$data_detalle["items"][$i]["codigo"] = $row->Nu_Codigo_Barra;
					$data_detalle["items"][$i]["descripcion"] = $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
					$data_detalle["items"][$i]["cantidad"] = $row->Qt_Producto;
					$data_detalle["items"][$i]["valor_unitario"] = $Ss_Precio_VU;
					$data_detalle["items"][$i]["precio_unitario"] = $row->Ss_Precio;
					$data_detalle["items"][$i]["descuento"] = $row->Ss_Descuento_Producto;
					$data_detalle["items"][$i]["subtotal"] = $row->Ss_SubTotal_Producto;
					$data_detalle["items"][$i]["tipo_de_igv"] = $row->Nu_Valor_Fe_Impuesto;
					$data_detalle["items"][$i]["igv"] = $row->Ss_Impuesto_Producto;
					$data_detalle["items"][$i]["total"] = $row->Ss_Total_Producto;
					$data_detalle["items"][$i]["anticipo_regularizacion"]	= false;
					$data_detalle["items"][$i]["anticipo_documento_serie"]	= "";
					$data_detalle["items"][$i]["anticipo_documento_numero"] = "";
					$data_detalle["items"][$i]["impuesto_bolsas"] = ($row->ID_Impuesto_Icbper == 0 ? 0.00 : $row->Ss_Icbper);

					$i++;
				}

				$No_Codigo_Medio_Pago_Sunat_PLE = '';
				$arrDataMediosPago = $this->DocumentoElectronicoModel->obtenerComprobanteMedioPago($arrParams);
				if ( $arrDataMediosPago['sStatus'] == 'success' ) {
					$sConcatenarMultiplesMedioPago = '';
					$No_Codigo_Medio_Pago_Sunat_PLE = $arrDataMediosPago['arrData'][0]->No_Codigo_Medio_Pago_Sunat_PLE;
					foreach ($arrDataMediosPago['arrData'] as $row) {
						if ( $row->No_Codigo_Medio_Pago_Sunat_PLE != '006' )
							$sConcatenarMultiplesMedioPago .= $row->No_Medio_Pago . ': ' . $arrData[0]->No_Signo . ' ' . $row->Ss_Total_Medio_Pago . ', ';
						else
							$sConcatenarMultiplesMedioPago .= 'PAGO CON TARJETA' . ': ' . $arrData[0]->No_Signo . ' ' . $row->Ss_Total_Medio_Pago . ', ';//Nubefact Tarjeta de crédito
					}
					$sConcatenarMultiplesMedioPago = substr($sConcatenarMultiplesMedioPago, 0, -2);	
				} else {
					return $arrDataMediosPago;
				}

				$sDiasCredito = '';
				$arrVentasCreditoCuotas = array();
				if ( $No_Codigo_Medio_Pago_Sunat_PLE == '0' ) {
					$arrVentasCreditoCuotas = array(
						'venta_al_credito' => array(
							0 => array(
								'cuota' => 1,
								'fecha_de_pago' => $arrData[0]->Fe_Vencimiento,
								'importe' => $arrData[0]->Ss_Total_Saldo,//Saldo
							)
						)
					);
				}

				/*
				Tipo de la GUÍA DE REMISIÓN RELACIONADA. Ejemplo: 1
				1 = GUÍA DE REMISIÓN REMITENTE
				2 = GUÍA DE REMISIÓN TRANSPORTISTA
				*/
				$data_guias = array();
				$cadena_de_texto = trim($arrData[0]->Txt_Garantia);
				if ( substr($cadena_de_texto, -1) == ',' )
					$cadena_de_texto = substr($cadena_de_texto, 0, -1);
    			$cadena_buscada = '-';
    			$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
    			if ( strlen($arrData[0]->Txt_Garantia) > 5 && $posicion_coincidencia !== false) {
					$arrCadena = explode(',',$arrData[0]->Txt_Garantia);
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
					$arrParamsGuia = array('ID_Documento_Cabecera' => $arrData[0]->ID_Documento_Cabecera);
					$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParamsGuia);
					if ($arrResponseDocument['sStatus'] == 'success'){
						$i = 0;
						foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
							$data_guias["guias"][$i]["guia_tipo"] = 1;
							$data_guias["guias"][$i]["guia_serie_numero"] = $rowEnlace->_ID_Serie_Documento . '-' . $rowEnlace->ID_Numero_Documento;
							$i++;
						}
					}
				}

				$iTipoNC = "";
				$iTipoND = "";
				
				$iTipoComprobante = 2;//Boleta
				if ($arrData[0]->ID_Tipo_Documento == 3) {//Factura
					$iTipoComprobante = 1;
				} else if ($arrData[0]->ID_Tipo_Documento == 5) {//N/Crédito
					$iTipoComprobante = 3;
					//$iTipoNC = ($arrData[0]->Nu_Codigo_Motivo_Referencia != 10 ? $arrData[0]->Nu_Codigo_Motivo_Referencia : 1);
					$iTipoNC = $arrData[0]->Nu_Codigo_Motivo_Referencia;
				} else if ($arrData[0]->ID_Tipo_Documento == 6) {//N/Débito
					$iTipoComprobante = 4;
					$iTipoND = $arrData[0]->Nu_Codigo_Motivo_Referencia;
				}
				
				$iTipoComprobanteModifica = "";
				if (!empty($arrData[0]->ID_Tipo_Documento_Modificar)) {
					$iTipoComprobanteModifica = 2;//BOLETAS DE VENTA ELECTRÓNICAS
					if ($arrData[0]->ID_Tipo_Documento_Modificar == 3)//Factura
						$iTipoComprobanteModifica = 1;//FACTURAS ELECTRÓNICAS
				}

				$iIdClienteTipoDocumentoIdentidad = $arrData[0]->Nu_Sunat_Codigo_TDI;
				$sNombreCliente = $arrData[0]->No_Entidad;
				if ($arrData[0]->ID_Tipo_Documento == 4 && (empty($arrData[0]->Nu_Documento_Identidad) || empty($arrData[0]->No_Entidad)) && ($arrData[0]->Ss_Total) < 700) {
					$iIdClienteTipoDocumentoIdentidad = '-';
					$sNombreCliente = 'vacio';
				}

           		$iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);

				$fDescuentoTotalOperacion = 0.00;
				$fDescuentoTotalOperacionIGV = 0.00;
				$fDescuentoTotalOperacionEXO = 0.00;
				if($arrData[0]->Ss_Descuento>0.00){
					$fDescuentoTotalOperacion = ($arrData[0]->Ss_Descuento / $iNumImpuestoDescuento);

					if ( $iNumImpuestoDescuentoEXO == 1 ) {
						$Ss_Exonerada = $Ss_Exonerada - $fDescuentoTotalOperacion;
						$fDescuentoTotalOperacionEXO = $fDescuentoTotalOperacion;
					}

					if ( $iNumImpuestoDescuentoIGV == 1 ) {
						$Ss_Gravada = $Ss_Gravada - $fDescuentoTotalOperacion;
						$Ss_IGV = ($Ss_Gravada * $fImpuestoConfiguracionIGV) - $Ss_Gravada;
						$fDescuentoTotalOperacionIGV = $fDescuentoTotalOperacion;
					}

					$fDescuentoTotalOperacion = $fDescuentoTotalOperacionEXO + $fDescuentoTotalOperacionIGV;
				}

				$Txt_Glosa = $arrData[0]->Txt_Glosa;
				$fTotal = $arrData[0]->Ss_Total;				
				if($arrData[0]->Ss_Retencion > 0.00){
					$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Retencion, 2) . ' <br>';
					$Txt_Glosa .= '<b>Base imponible de la retención:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
					$Txt_Glosa .= '<b>Monto de la retención:</b> ' . round($arrData[0]->Ss_Retencion, 2) . ' <br>';
				}

				$iPoDetraccion=0;
				if($arrData[0]->Ss_Detraccion > 0.00){
					$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Detraccion, 2) . ' <br>';
					$Txt_Glosa .= '<b>Base imponible de la detracción:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
					$Txt_Glosa .= '<b>Monto de la detracción:</b> ' . round($arrData[0]->Ss_Detraccion, 2) . ' <br>';
					$iPoDetraccion=$arrData[0]->Po_Detraccion;
				}

				$fTotalNuevo = 0.00;
				$fTotalNuevo = round(($arrData[0]->Ss_Total - $Ss_Gratuita), 2);
				$fTotalNuevo = abs($fTotalNuevo);

				$data_cabecera = array(
					"operacion"							=> "generar_comprobante",
					"tipo_de_comprobante"               => $iTipoComprobante,
					"serie"                             => $arrData[0]->ID_Serie_Documento,
					"numero"							=> $arrData[0]->ID_Numero_Documento,
					"sunat_transaction"					=> "1",
					"cliente_tipo_de_documento"			=> $iIdClienteTipoDocumentoIdentidad,
					"cliente_numero_de_documento"		=> $arrData[0]->Nu_Documento_Identidad,
					"cliente_denominacion"              => $sNombreCliente,
					"cliente_direccion"                 => $arrData[0]->Txt_Direccion_Entidad,
					"cliente_email"                     => "",
					"cliente_email_1"                   => "",
					"cliente_email_2"                   => "",
					"fecha_de_emision"                  => $arrData[0]->Fe_Emision,
					"fecha_de_vencimiento"              => $arrData[0]->Fe_Vencimiento,
					"moneda"                            => $arrData[0]->Nu_Valor_Fe_Moneda,
					"tipo_de_cambio"                    => ($arrData[0]->Nu_Valor_Fe_Moneda == 1 ? "" : $arrData[0]->Ss_Tipo_Cambio),
					"porcentaje_de_igv"                 => $Po_IGV,
					"descuento_global"                  => $fDescuentoTotalOperacion,
					"total_descuento"                   => $fDescuentoItem,
					"total_anticipo"                    => "",
					//"total_gravada"                     => ($Ss_Gratuita == 0 ? $Ss_Gravada : 0),
					//"total_inafecta"                    => ($Ss_Gratuita == 0 ? $Ss_Inafecto : 0),
					//"total_exonerada"                   => ($Ss_Gratuita == 0 ? $Ss_Exonerada : 0),
					//"total_igv"                         => ($Ss_Gratuita == 0 ? $Ss_IGV : 0),
					"total_gravada"                     => $Ss_Gravada,
					"total_inafecta"                    => $Ss_Inafecto,
					"total_exonerada"                   => $Ss_Exonerada,
					"total_igv"                         => $Ss_IGV,
					"total_gratuita"                    => $Ss_Gratuita,
					"total_otros_cargos"                => "",
					"total"                             => ($fTotalNuevo),
					//"total"                             => ($Ss_Gratuita == 0 ? $arrData[0]->Ss_Total : 0),
					"percepcion_tipo"                   => "",
					"percepcion_base_imponible"         => "",
					"total_percepcion"                  => "",
					"total_incluido_percepcion"         => "",
					"retencion_tipo"=> ($arrData[0]->Nu_Retencion == 0 ? "" : "1"),
					"retencion_base_imponible"=> ($arrData[0]->Nu_Retencion == 0 ? "" : ($Ss_Gratuita == 0 ? $arrData[0]->Ss_Total : 0)),
					"total_retencion"=> ($arrData[0]->Nu_Retencion == 0 ? "" : $arrData[0]->Ss_Retencion),
					"total_impuestos_bolsas" => $fTotalIcbper,
					"detraccion_total"                  => ($arrData[0]->Nu_Detraccion == 0 ? "" : $arrData[0]->Ss_Detraccion),
					"detraccion_porcentaje"             => ($arrData[0]->Nu_Detraccion == 0 ? "" : $iPoDetraccion),
					"detraccion"                        => ($arrData[0]->Nu_Detraccion == 0 ? false : true),
					"observaciones"                     => $Txt_Glosa,
					"documento_que_se_modifica_tipo"    => $iTipoComprobanteModifica,
					"documento_que_se_modifica_serie"   => $arrData[0]->ID_Serie_Documento_Modificar,
					"documento_que_se_modifica_numero"  => $arrData[0]->ID_Numero_Documento_Modificar,
					"tipo_de_nota_de_credito"           => $iTipoNC,
					"tipo_de_nota_de_debito"            => $iTipoND,
					"enviar_automaticamente_a_la_sunat" => true,
					"enviar_automaticamente_al_cliente" => false,
					"codigo_unico"                      => $iTipoComprobante . $arrData[0]->ID_Serie_Documento . $arrData[0]->ID_Numero_Documento,
					"condiciones_de_pago"               => $sDiasCredito,
					"medio_de_pago"                     => $sConcatenarMultiplesMedioPago,
					"placa_vehiculo"                    => $arrData[0]->No_Placa_FE,
					"orden_compra_servicio"             => $arrData[0]->No_Orden_Compra_FE,
					"tabla_personalizada_codigo"        => "",
					"formato_de_pdf"                    => $arrData[0]->No_Formato_PDF,
				);
				$data = array_merge($data_cabecera, $data_detalle, $data_guias, $arrVentasCreditoCuotas);

				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"arrData" => $data,
				);
				return $this->enviarDocumentoElectronicoProveedor($arrParamsFE, $arrParams);
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		} else {			
			$arrData = $this->DocumentoElectronicoModel->obtenerComprobanteAnulado($arrParams);

			if ( $arrData['sStatus'] == 'success' ) {
				$arrData = $arrData['arrData'];
				$iTipoComprobante = 2;//Boleta
				if ($arrData[0]->ID_Tipo_Documento == 3)//Factura
					$iTipoComprobante = 1;
				else if ($arrData[0]->ID_Tipo_Documento == 5)//N/Crédito
					$iTipoComprobante = 3;
				else if ($arrData[0]->ID_Tipo_Documento == 6)//N/Débito
					$iTipoComprobante = 4;
								
				$ruta = $arrData[0]->Txt_FE_Ruta;
				$token = $arrData[0]->Txt_FE_Token;

				$data = array(
					"operacion" => "generar_anulacion",
					"tipo_de_comprobante" => $iTipoComprobante,
					"serie" => $arrData[0]->ID_Serie_Documento,
					"numero" => $arrData[0]->ID_Numero_Documento,
					"motivo" => $Txt_Glosa,
					"codigo_unico" => ""
				);

				$arrParamsFE = array(
					"ruta" => $ruta,
					"token" => $token,
					"arrData" => $data,
				);
				return $this->enviarDocumentoElectronicoProveedor($arrParamsFE, $arrParams);
			} else {
				return $arrData;
			}// ./ if - else respuesta de modal del comprobante
		}// ./ if - else generar formato de documento electronico
	} // ./ generarFormatoDocumentoElectronico

	private function enviarDocumentoElectronicoProveedor($arrParamsFE, $arrParams){
		$arrData = $arrParamsFE['arrData'];
		$data_json = json_encode($arrData);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $arrParamsFE['ruta']);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$arrParamsFE['token'].'"',
			'Content-Type: application/json',
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respuesta = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );	
		curl_close($ch);
		// Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);
			if (!isset($leer_respuesta['errors']) && !empty($leer_respuesta) || isset($leer_respuesta['codigo']) ) {
				if ( $arrParams['iEstadoVenta'] == 6 ) {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 8 ));
				} else {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 10 ));
				}

				$arrParams = array_merge( $arrParams, array( 'arrMessagePSE' => $leer_respuesta ) );
				$arrResponseEstadoDocumento = $this->cambiarEstadoDocumentoElectronico( $arrParams );
				if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
					if ( $arrResponseEstadoDocumento['iEstadoVenta'] == 8 && !empty($arrParams['sEmailCliente']) ) { 
						$arrParamsEmail = array_merge( $arrParams, array( 'sGenerarRespuestaJson' => false ) );
						$this->sendCorreoFacturaVentaSUNAT($arrParamsEmail);
					}
		
					return array(
						'sStatus' => !isset($leer_respuesta['codigo']) ? 'success' : 'warning',
						'sMessage' => !isset($leer_respuesta['codigo']) ? 'Venta completada' : 'Venta completada - ' . $leer_respuesta['errors'],
						'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
						'arrMessagePSE' => !isset($leer_respuesta['errors']) ? 'Comprobante aceptado' : $leer_respuesta['errors'],
						'sCodigo' => !isset($leer_respuesta['codigo']) ? '0' : $leer_respuesta['codigo'],
						'enlace_del_pdf' => (!isset($leer_respuesta['codigo']) ? $leer_respuesta['enlace_del_pdf'] : ''),
					);
				} else {
					return $arrResponseEstadoDocumento;
				}
			} else {
				if ( $arrParams['iEstadoVenta'] == 6 ) {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
				} else {
					$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 11 ));
				}
				
				$arrResponseEstadoDocumento = $this->cambiarEstadoDocumentoElectronico( $arrParams );
				if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
					return array(
						'sStatus' => 'danger',
						'sMessage' => (!empty($leer_respuesta) ? 'Problemas al generar documento electrónico' : 'Venta guardada pero no se envio a SUNAT'),
						'iCodigoProveedorDocumentoElectronico' => $arrParams['iCodigoProveedorDocumentoElectronico'],
						'arrMessagePSE' => (!empty($leer_respuesta) ? $leer_respuesta : 'Venta guardada pero no se envio a SUNAT'),
						'sCodigo' => '-200',
					);
				} else {
					return $arrResponseEstadoDocumento;
				}
			}
		} else {
			if ( $arrParams['iEstadoVenta'] == 6 ) {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
			} else {
				$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 11 ));
			}

			$arrResponseEstadoDocumento = $this->cambiarEstadoDocumentoElectronico( $arrParams );
			if ( $arrResponseEstadoDocumento['sStatus'] == 'success' ) {
				return array(
					'status' => 'warning',
					'style_modal' => 'modal-warning',
					'message' => 'No hay conexión. Venta completada - No enviada a SUNAT',
					'message_nubefact' => 'Venta completada - No enviada a SUNAT' . $respuesta,
					'sStatus' => 'warning',
					'sMessage' => 'No hay conexión. Venta completada - No enviada a SUNAT',
					'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
					'arrMessagePSE' => 'No hay conexión. No existe URL del proveedor para enviar los documentos a SUNAT v4' . $respuesta,
					'sCodigo' => '-500',
					'httpcode' => $httpcode
				);
			} else {
				return $arrResponseEstadoDocumento;
			}
		}
	}

	public function cambiarEstadoDocumentoElectronico( $arrParams ){
		$this->db->trans_begin();

		if ($arrParams['iEstadoVenta'] == 8 || $arrParams['iEstadoVenta'] == 9 || $arrParams['iEstadoVenta'] == 10){
			if (!isset($arrParams['arrMessagePSE']['codigo'])) {
				if ($arrParams['iEstadoVenta'] == 8) {
					//array_debug($arrParams);
					//Nuevo creado 11/12/2021
					if ($arrParams['arrMessagePSE']['status']=='error') {
						$this->db->trans_rollback();
						return array(
							'status' => 'danger',
							'style_modal' => 'modal-danger',
							'message' => $arrParams['arrMessagePSE']['message'] . ' No se envió a SUNAT',
							'message_nubefact' => $arrParams['arrMessagePSE']['message'] . ' No se envió a SUNAT',
							'sStatus' => 'danger',
							'sMessage' => $arrParams['arrMessagePSE']['message'] . ' No se envió a SUNAT',
							'iCodigoProveedorDocumentoElectronico' => $arrParams['iCodigoProveedorDocumentoElectronico'],
							'arrMessagePSE' => $arrParams['arrMessagePSE']['message'],
							'sCodigo' => '9000 - codigo: ' . $arrParams['arrMessagePSE']['codigo'],
						);
					}

					$data = array(
						'Nu_Estado' => $arrParams['iEstadoVenta'],
						'Txt_Url_Comprobante' => $arrParams['arrMessagePSE']['enlace'],
						'Txt_Url_PDF' => $arrParams['arrMessagePSE']['enlace_del_pdf'],
						'Txt_Url_XML' => $arrParams['arrMessagePSE']['enlace_del_xml'],
						'Txt_Url_CDR' => $arrParams['arrMessagePSE']['enlace_del_cdr'],
						'Txt_QR' => $arrParams['arrMessagePSE']['cadena_para_codigo_qr'],
						'Txt_Hash' => $arrParams['arrMessagePSE']['codigo_hash'],
					);
				} else {
					$data = array(
						'Nu_Estado' => $arrParams['iEstadoVenta'],
					);
				}
			} else {
				if ( $arrParams['arrMessagePSE']['codigo'] != '0' ) {
					$data = array(
						'Nu_Estado' => ($arrParams['iEstadoVenta'] == 8 ? 9 : 11),
					);
				}
			}
		} else {
			$data = array(
				'Nu_Estado' => $arrParams['iEstadoVenta'],
			);
		}

		$this->db->update($this->table, $data, array('ID_Documento_Cabecera' => $arrParams['iIdDocumentoCabecera']));
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'danger',
				'style_modal' => 'modal-danger',
				'message' => 'No se envió a SUNAT',
				'message_nubefact' => 'No se envió a SUNAT',
				'sStatus' => 'danger',
				'sMessage' => 'No se envió a SUNAT',
				'iCodigoProveedorDocumentoElectronico' => $arrParams['iCodigoProveedorDocumentoElectronico'],
				'arrMessagePSE' => 'No se envió a SUNAT',
				'sCodigo' => '-100',
			);
		} else {
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'style_modal' => 'modal-success',
				'message' => 'Estado cambiado satisfactoriamente',
				'message_nubefact' => 'Estado cambiado satisfactoriamente',
				'sStatus' => 'success',
				'sMessage' => 'Estado cambiado satisfactoriamente',
				'iEstadoVenta' => $arrParams['iEstadoVenta'],
				'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
			);
		}
	}
	
	public function cambiarEstadoGuiaElectronico( $arrParams ){
		$this->db->trans_begin();
		if ($arrParams['iEstadoVenta'] == 8 || $arrParams['iEstadoVenta'] == 10){
			if (!isset($arrParams['arrMessagePSE']['codigo'])) {
				if ($arrParams['iEstadoVenta'] == 8) {
					$data = array(
						'Nu_Estado' => $arrParams['iEstadoVenta'],
						'Txt_Url_Comprobante' => $arrParams['arrMessagePSE']['enlace'],
						'Txt_Url_PDF' => $arrParams['arrMessagePSE']['enlace_del_pdf'],
						'Txt_Url_XML' => $arrParams['arrMessagePSE']['enlace_del_xml'],
						'Txt_Url_CDR' => $arrParams['arrMessagePSE']['enlace_del_cdr'],
						'Txt_QR' => (isset($arrParams['arrMessagePSE']['cadena_para_codigo_qr']) ? $arrParams['arrMessagePSE']['cadena_para_codigo_qr'] : ''),
						'Txt_Hash' => (isset($arrParams['arrMessagePSE']['codigo_hash']) ? $arrParams['arrMessagePSE']['codigo_hash'] : ''),
					);
				} else {
					$data = array(
						'Nu_Estado' => $arrParams['iEstadoVenta'],
					);
				}
			} else {
				$data = array(
					'Nu_Estado' => ($arrParams['iEstadoVenta'] == 8 ? 9 : 11),
				);
			}
		} else {
			$data = array(
				'Nu_Estado' => $arrParams['iEstadoVenta'],
			);
		}

		$this->db->update('guia_cabecera', $data, array('ID_Guia_Cabecera' => $arrParams['iIdDocumentoCabecera']));
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'danger',
				'style_modal' => 'modal-danger',
				'message' => 'No se envió a SUNAT',
				'message_nubefact' => 'No se envió a SUNAT',
				'sStatus' => 'danger',
				'sMessage' => 'No se envió a SUNAT',
				'iCodigoProveedorDocumentoElectronico' => $arrParams['iCodigoProveedorDocumentoElectronico'],
				'arrMessagePSE' => 'No se envió a SUNAT',
				'sCodigo' => '-100',
			);
		} else {
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'style_modal' => 'modal-success',
				'message' => 'Estado cambiado satisfactoriamente',
				'message_nubefact' => 'Estado cambiado satisfactoriamente',
				'sStatus' => 'success',
				'sMessage' => 'Estado cambiado satisfactoriamente',
				'iEstadoVenta' => $arrParams['iEstadoVenta'],
				'iIdDocumentoCabecera' => $arrParams['iIdDocumentoCabecera'],
			);
		}
	}

	public function sendCorreoFacturaVentaSUNAT($arrParams){
		$arrData = $this->obtenerComprobante($arrParams);
		if ( $arrData['sStatus'] == 'success' ) {
			$arrData = $arrData['arrData'];

			$this->load->library('email');

			$data = array();
			
			$data["No_Documento"] = strtoupper($arrData[0]->No_Tipo_Documento) . ' ELECTRÓNICA '  . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento;
			$data["Fe_Emision"] = ToDateBD($arrData[0]->Fe_Emision);
			$data["No_Signo"] = $arrData[0]->No_Signo;
			$data["Ss_Total"] = $arrData[0]->Ss_Total;
			$data["Txt_Medio_Pago"]	= $arrData[0]->No_Medio_Pago;
			$data["Nu_Tipo"] = $arrData[0]->Nu_Tipo;
			$data["Ss_Total_Saldo"]	= $arrData[0]->Ss_Total_Saldo;
			
			$data["No_Entidad"] = $arrData[0]->No_Entidad;
			
			$data["No_Empresa"] = $this->empresa->No_Empresa;
			$data["Nu_Documento_Identidad_Empresa"] = $this->empresa->Nu_Documento_Identidad;
			// Falta agregar sede de donde se envio el correo
			
			$data["url_comprobante"] = (!empty($arrData[0]->Txt_Url_Comprobante) ? $arrData[0]->Txt_Url_Comprobante : '');
			
			$asunto = 'COPIA DE ' . $data["No_Documento"] . ' ' . $this->empresa->No_Empresa . ' | ' . $this->empresa->Nu_Documento_Identidad;
			
			$message = $this->load->view('correos/documentos_electronicos', $data, true);
			
			$this->email->from('noreply@laesystems.com', $this->empresa->No_Empresa);//de
			
			if ( !isset($_POST['ID']) )
				$this->email->to($arrParams['sEmailCliente']);//para
			else
				$this->email->to($this->input->post('Txt_Email'));//para
				
			$this->email->subject($asunto);
			$this->email->message($message);
			if (!empty($arrData[0]->Txt_Url_PDF))
				$this->email->attach($arrData[0]->Txt_Url_PDF);
			if (!empty($arrData[0]->Txt_Url_XML))
				$this->email->attach($arrData[0]->Txt_Url_XML);
			if (!empty($arrData[0]->Txt_Url_CDR))
				$this->email->attach($arrData[0]->Txt_Url_CDR);
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
			}
			if ( $arrParams['sGenerarRespuestaJson'] )
				echo json_encode($peticion);
			else
				return $peticion;
		} else {
			if ( $arrParams['sGenerarRespuestaJson'] )
				echo json_encode($arrData);
			else
				return $peticion;
		}
	}

	public function agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams ){
		$data = json_encode(array(
			'Proveedor' => $this->empresa->Nu_Tipo_Proveedor_FE == 1 ? 'Nubefact' : 'Sunat',
			'Enviada_SUNAT' => $arrResponseFE['sStatus'] == 'success' ? 'Si' : 'No',
			'Aceptada_SUNAT' => $arrResponseFE['sStatus'] == 'success' ? 'Si' : 'No',
			'Codigo_SUNAT' => $arrResponseFE['sCodigo'],
			'Mensaje_SUNAT' => utf8_decode($arrResponseFE['arrMessagePSE']),
			'Fecha_Registro' => dateNow('fecha_hora'),
			'Fecha_Envio' => dateNow('fecha_hora'),
		));

		$sql = "UPDATE guia_cabecera SET Txt_Respuesta_Sunat_FE='" . $data . "' WHERE ID_Guia_Cabecera=" . $arrParams['iIdDocumentoCabecera'];
		if ( $this->db->query($sql) > 0 ) {
			return array(
				'sStatus' => 'success',
			);
		}
		return array(
			'sStatus' => 'danger',
			'sMessage' => 'Problemas al guardar mensaje proveedor FE Guía',
		);
	}

	public function agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams ){
		$data = json_encode(array(
			'Proveedor' => $this->empresa->Nu_Tipo_Proveedor_FE == 1 ? 'Nubefact' : 'Sunat',
			'Enviada_SUNAT' => $arrResponseFE['sStatus'] == 'success' ? 'Si' : 'No',
			'Aceptada_SUNAT' => $arrResponseFE['sStatus'] == 'success' ? 'Si' : 'No',
			'Codigo_SUNAT' => $arrResponseFE['sCodigo'],
			'Mensaje_SUNAT' => utf8_decode($arrResponseFE['arrMessagePSE']),
			'Fecha_Registro' => dateNow('fecha_hora'),
			'Fecha_Envio' => dateNow('fecha_hora'),
		));

		$sql = "UPDATE " . $this->table . " SET Txt_Respuesta_Sunat_FE='" . $data . "' WHERE ID_Documento_Cabecera=" . $arrParams['iIdDocumentoCabecera'];
		if ( $this->db->query($sql) > 0 ) {
			return array(
				'sStatus' => 'success',
			);
		}
		return array(
			'sStatus' => 'danger',
			'sMessage' => 'Problemas al guardar mensaje proveedor FE',
		);
	}

	public function consultarComprobanteExistent( $objParams ){
		if ( $objParams->iTipoProveedorFE == 1 ) {
			$data = array(
				"operacion" => $objParams->sTipoOperacion,
				"tipo_de_comprobante" => $objParams->iTipoDocumento,
				"serie" => $objParams->sSerieDocumento,
				"numero" => $objParams->iNumeroDocumento,
			);
			$data_json = json_encode($data);

			//Invocamos el servicio de NUBEFACT
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $objParams->ruta);
			curl_setopt(
				$ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Token token="'.$objParams->token.'"',
				'Content-Type: application/json',
				)
			);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$respuesta  = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );
			curl_close($ch);
			//Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
			$accepted_response = array( 200, 301, 302 );
			if( in_array( $httpcode, $accepted_response ) ) {
				$leer_respuesta = json_decode($respuesta, true);
				if (isset($leer_respuesta['errors'])) {
					return array(
						'status' => 'danger',
						'style_modal' => 'modal-danger',
						'message' => $leer_respuesta['errors'],
						'message_nubefact' => $leer_respuesta['errors'],
						'sStatus' => 'danger',
						'sMessage' => $leer_respuesta['errors'],
						'iCodigoProveedorDocumentoElectronico' => $objParams->iTipoProveedorFE,
						'arrMessagePSE' => $leer_respuesta['errors'],
						'sCodigo' => '-600',
					);
				} else {
					$data = array(
						'Nu_Estado' => 8,
						'Txt_Url_Comprobante' => $leer_respuesta['enlace'],
						'Txt_QR' => $leer_respuesta['cadena_para_codigo_qr'],
						'Txt_Hash' => $leer_respuesta['codigo_hash'],
						'Txt_Url_PDF' => $leer_respuesta['enlace_del_pdf'],
						'Txt_Url_XML' => $leer_respuesta['enlace_del_xml'],
						'Txt_Url_CDR' => $leer_respuesta['enlace_del_cdr'],
					);
					if ( $this->db->update($this->table, $data, array('ID_Documento_Cabecera' => $objParams->iIdDocumentoCabecera)) > 0 ) {
						return array(
							'status' => 'success',
							'style_modal' => 'modal-success',
							'message' => 'Estado cambiado satisfactoriamente',
							'message_nubefact' => 'Estado cambiado satisfactoriamente',
							'sStatus' => 'success',
							'sMessage' => 'Estado cambiado satisfactoriamente',
							'iEstadoVenta' => 8,
							'iIdDocumentoCabecera' => $objParams->iIdDocumentoCabecera,
						);
					}
					return array(
						'status' => 'danger',
						'style_modal' => 'modal-danger',
						'message' => 'Problemas al actualizar datos de comprobante existente',
						'message_nubefact' => 'Problemas al actualizar datos de comprobante existente',
						'sStatus' => 'danger',
						'sMessage' => 'Problemas al actualizar datos de comprobante existente',
						'iCodigoProveedorDocumentoElectronico' => $objParams->iTipoProveedorFE,
						'arrMessagePSE' => 'Problemas al actualizar datos de comprobante existente',
						'sCodigo' => '-600',
					);
				}
			} else {
				return array(
					'status' => 'danger',
					'style_modal' => 'modal-danger',
					'message' => 'No hay conexión. Problemas al actualizar datos de comprobante existente',
					'message_nubefact' => 'No hay conexión. Problemas al actualizar datos de comprobante existente',
					'sStatus' => 'danger',
					'sMessage' => 'No hay conexión. Problemas al actualizar datos de comprobante existente',
					'iCodigoProveedorDocumentoElectronico' => $objParams->iTipoProveedorFE,
					'arrMessagePSE' => 'No hay conexión. Problemas al actualizar datos de comprobante existente',
					'sCodigo' => '-600',
					'httpcode' => $httpcode
				);
			}
		}
	}
	
	//GUIA ELECTRONICA CONSULTAR ESTADO NUBEFACT
	public function consultarGuiaElectronicoSunatV2($arrParams){
		$arrResponse = $this->DocumentoElectronicoModel->obtenerGuiaToken($arrParams);

		if($arrResponse['result']->ID_Tipo_Documento==7) {
			$iIdTipoDocumento = '09';
		}

		$data = array(
			"operacion"						=> "consultar_guia",
			"NRO_DOCUMENTO_EMPRESA"			=> $arrResponse['result']->Nu_Documento_Identidad,
			"COD_TIPO_DOCUMENTO"			=> $iIdTipoDocumento,
			"SERIE_COMPROBANTE"    			=> $arrResponse['result']->ID_Serie_Documento,
			"NUMERO_COMPROBANTE"			=> autocompletarConCeros('', $arrResponse['result']->ID_Numero_Documento, $arrResponse['result']->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
			"TOKEN_GUIA_CLIENT_ID" 			=> $arrResponse['result']->Txt_Sunat_Token_Guia_Client_ID,
			"TOKEN_GUIA_CLIENT_SECRET"		=> $arrResponse['result']->Txt_Sunat_Token_Guia_Client_Secret,
			"TIPO_PROCESO" 					=> "3",
			"USUARIO_SOL_EMPRESA" 			=> $arrResponse['result']->Txt_Usuario_Sunat_Sol,
			"PASS_SOL_EMPRESA" 				=> $arrResponse['result']->Txt_Password_Sunat_Sol,
			"HASH_CPE" 						=> $arrResponse['result']->Txt_Hash
		);

		$data_json = json_encode($data);

		$ruta = $arrResponse['result']->Txt_FE_Ruta;
		$token = $arrResponse['result']->Txt_FE_Token;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ruta);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$token.'"',
			'Content-Type: application/json',
			'X-API-Key: ' . $token,
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respuesta = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE );
		curl_close($ch);
		$accepted_response = array( 200, 301, 302 );
		if( in_array( $httpcode, $accepted_response ) ) {
			$leer_respuesta = json_decode($respuesta, true);

			$message_sunat = json_encode(array(
				'Proveedor' => ($this->empresa->Nu_Tipo_Proveedor_FE == 1 ? 'Nubefact PSE Reseller' : 'Sunat'),
				'Enviada_SUNAT' => 'Si',
				'Aceptada_SUNAT' => (isset($leer_respuesta['codigo']) && $leer_respuesta['codigo'] == 0 ? 'Si' : 'No'),
				'Codigo_SUNAT' => $leer_respuesta['codigo'],
				'Mensaje_SUNAT' => $leer_respuesta['message'],
				'Fecha_Registro' => dateNow('fecha_hora'),
				'Fecha_Envio' => dateNow('fecha_hora')
			));
			
			$data = array(
				'Txt_Url_CDR' => $leer_respuesta['enlace_del_cdr'],
				'Txt_Respuesta_Sunat_FE' => $message_sunat
			);

			if(isset($leer_respuesta['codigo']) && ($leer_respuesta['codigo'] == 0 || $leer_respuesta['codigo']=='1033' || $leer_respuesta['codigo']=='1033-500')){
				$data = array_merge($data, array(
						'Txt_Hash' => $leer_respuesta['codigo_hash'],
						'Nu_Estado' => 8
					)
				);
			} else{
				$data = array_merge($data, array('Nu_Estado' => 9));
			}
			
			if ($this->db->update('guia_cabecera', $data, array('ID_Guia_Cabecera' => $arrParams['iIdGuiaCabecera'])) > 0) {
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado ' . $leer_respuesta['message'], 'arrResponse' => $respuesta);
			} else {
				return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'no se actualizo registro', 'arrResponse' => $respuesta);
			}
		} else {
			$arrParams = array_merge( $arrParams, array( 'iEstadoVenta' => 9 ));
			
			$data = array('Nu_Estado' => 9);
			$this->db->update('guia_cabecera', $data, array('ID_Guia_Cabecera' => $arrParams['iIdGuiaCabecera']));
				
			return array(
				'status' => 'warning',
				'style_modal' => 'modal-warning',
				'message' => 'Registro completado - No enviada a SUNAT',
				'message_nubefact' => 'Registro completado - No enviada a SUNAT' . $respuesta,
				'sStatus' => 'warning',
				'sMessage' => 'Registro completado - No enviada a SUNAT',
				'iIdDocumentoCabecera' => $arrParams['iIdGuiaCabecera'],
				'arrMessagePSE' => 'No existe URL del proveedor para enviar los documentos a SUNAT v3' . $respuesta,
				'sCodigo' => '9001',
				'httpcode' => $httpcode,
				'arrData' => $data
			);
		}
	} // ./ GUIA ELECTRONICA CONSULTAR ESTADO NUBEFACT

	public function recuperarPDFVentaSunat($arrParams){
		$arrData = $this->DocumentoElectronicoModel->obtenerComprobante($arrParams);
		if ( $arrData['sStatus'] == 'success' ) {
			$arrData = $arrData['arrData'];

			$Ss_SubTotal_Producto = 0.00;
			$Ss_Descuento_Producto = 0.00;
			$Ss_Total_Producto = 0.00;
			$Ss_Gravada = 0.00;
			$Ss_Inafecto = 0.00;
			$Ss_Exonerada = 0.00;
			$Ss_Gratuita = 0.00;
			$Ss_IGV = 0.00;
			$Ss_Total = 0.00;
			
			$i = 0;
			$fTotalIcbper = 0.00;
			$Po_IGV = "";
			$iCounter = 1;
			$sPrecioTipoCodigoDetalle = '01';
			$Ss_Impuesto = 0.00;
			$Ss_Gravada = 0.00;
			$fTotalIcbperSinImpuesto = 0.00;
			$iCapturaI = -1;
			$fTotalCapturaIcbper = 0;
			$iEsIcbper = 0;
			$fCantidadCapturaIcbper = 0;
			$fPrecioCapturaIcbper = 0;
		
			$iNumImpuestoDescuento = 0;
			$iNumImpuestoDescuentoIGV = 0;
			$iNumImpuestoDescuentoEXO = 0;
			$fImpuestoConfiguracionIGV = 1;
			$fDescuentoTotalOperacionItem=0;
			foreach ($arrData as $row) {			  
				$sPrecioTipoCodigoDetalle = '01';
				$Ss_Precio_VU = $row->Ss_Precio;
				if ($row->Nu_Tipo_Impuesto == 1){//IGV
					$Ss_Impuesto = $row->Ss_Impuesto;
					$Po_IGV = $row->Po_Impuesto;
					$Ss_Precio_VU = round($row->Ss_Precio / $row->Ss_Impuesto, 6);
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
					$sPrecioTipoCodigoDetalle = '02';
				}

				if ( $row->ID_Impuesto_Icbper == 1 )
					$fTotalIcbper += $row->Ss_Icbper;

				$fDescuentoTotalOperacionItem += $row->Ss_Descuento_Producto;

				$data_detalle["detalle"][$i]["ITEM"] = $iCounter;
				$data_detalle["detalle"][$i]["UNIDAD_MEDIDA_DET"] = $row->Nu_Sunat_Codigo_UM;
				$data_detalle["detalle"][$i]["CODIGO_DET"] = $row->Nu_Codigo_Barra;
				$data_detalle["detalle"][$i]["DESCRIPCION_DET"] = $row->No_Producto . ($row->Txt_Nota_Item != '' ? ' ' . $row->Txt_Nota_Item : '');
				$data_detalle["detalle"][$i]["CANTIDAD_DET"] = $row->Qt_Producto;
				$data_detalle["detalle"][$i]["PRECIO_SIN_IGV_DET"] = $Ss_Precio_VU;
				$data_detalle["detalle"][$i]["PRECIO_DET"] = $row->Ss_Precio;
				$data_detalle["detalle"][$i]["IMPORTE_DET"] = $row->Ss_SubTotal_Producto;
				$data_detalle["detalle"][$i]["COD_TIPO_OPERACION"] = $row->Nu_Sunat_Codigo_Impuesto;
				$data_detalle["detalle"][$i]["IGV"] = $row->Ss_Impuesto_Producto;
				$data_detalle["detalle"][$i]["ISC"] = "0";
				$data_detalle["detalle"][$i]["PRECIO_TIPO_CODIGO"] = $sPrecioTipoCodigoDetalle;
				$data_detalle["detalle"][$i]["TOTAL"] = $row->Ss_Total_Producto;
				$data_detalle["detalle"][$i]["TOTAL_ICBPER"] = ($row->ID_Impuesto_Icbper == 0 ? 0.00 : $row->Ss_Icbper);
				$data_detalle["detalle"][$i]["NUMERO_LOTE_VENCIMIENTO"] = $row->Nu_Lote_Vencimiento;
				$data_detalle["detalle"][$i]["FECHA_LOTE_VENCIMIENTO"] = $row->Fe_Lote_Vencimiento;
				$data_detalle["detalle"][$i]["DESCUENTO"] = $row->Ss_Descuento_Producto;
				$data_detalle["detalle"][$i]["MARCA"] = $row->No_Marca;

				$i++;
				++$iCounter;
			}

			$iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);

			$fDescuentoTotalOperacion = 0.00;
			$fDescuentoTotalOperacionIGV = 0.00;
			$fDescuentoTotalOperacionEXO = 0.00;
			if($arrData[0]->Ss_Descuento>0.00){
				$fDescuentoTotalOperacion = ($arrData[0]->Ss_Descuento / $iNumImpuestoDescuento);

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

			$No_Codigo_Medio_Pago_Sunat_PLE = '';
			$arrDataMediosPago = $this->DocumentoElectronicoModel->obtenerComprobanteMedioPago($arrParams);
			
			$sDiasCredito = '';
			$arrVentasCreditoCuotas = array();

			if ( $arrDataMediosPago['sStatus'] == 'success' ) {
				$sConcatenarMultiplesMedioPago = '';
				$No_Codigo_Medio_Pago_Sunat_PLE = $arrDataMediosPago['arrData'][0]->No_Codigo_Medio_Pago_Sunat_PLE;
				foreach ($arrDataMediosPago['arrData'] as $row) {
					$sConcatenarMultiplesMedioPago .= $row->No_Medio_Pago . ' [' . $row->Txt_Medio_Pago . ']: ' . ($No_Codigo_Medio_Pago_Sunat_PLE != '0' ? $arrData[0]->No_Signo  . ' ' . $row->Ss_Total_Medio_Pago : '') . ', ';
					
					if ( $row->codigo_interno_pago == '1' ) {//1=Credito
						$arrVentasCreditoCuotas = array(
							'venta_al_credito' => array(
								0 => array(
									'cuota' => 1,
									'fecha_de_pago' => $arrData[0]->Fe_Vencimiento,
									'importe' => $arrData[0]->Ss_Total_Saldo,
								)
							)
						);
					}
				}
				$sConcatenarMultiplesMedioPago = substr($sConcatenarMultiplesMedioPago, 0, -2);	
			} else {
				return $arrDataMediosPago;
			}

			$sDiasCredito = '';
			$arrVentasCreditoCuotas = array();
			if ( $No_Codigo_Medio_Pago_Sunat_PLE == '0' ) {
				$arrVentasCreditoCuotas = array(
					'venta_al_credito' => array(
						0 => array(
							'cuota' => 1,
							'fecha_de_pago' => $arrData[0]->Fe_Vencimiento,
							'importe' => $arrData[0]->Ss_Total_Saldo,
						)
					)
				);
			}

			$this->load->library('EnLetras', 'el');
			$EnLetras = new EnLetras();

			/*
			Tipo de la GUÍA DE REMISIÓN RELACIONADA. Ejemplo: 1
			1 = GUÍA DE REMISIÓN REMITENTE
			2 = GUÍA DE REMISIÓN TRANSPORTISTA
			*/
			$data_guias = array();
			$cadena_de_texto = trim($arrData[0]->Txt_Garantia);
			if ( substr($cadena_de_texto, -1) == ',' )
				$cadena_de_texto = substr($cadena_de_texto, 0, -1);
			$cadena_buscada = '-';
			$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
			if ( strlen($arrData[0]->Txt_Garantia) > 5 && $posicion_coincidencia !== false) {
				$arrCadena = explode(',',$arrData[0]->Txt_Garantia);
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
				$arrParamsGuia = array('ID_Documento_Cabecera' => $arrData[0]->ID_Documento_Cabecera);
				$arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParamsGuia);
				if ($arrResponseDocument['sStatus'] == 'success'){
					$i = 0;
					foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
						$data_guias["guias"][$i]["guia_tipo"] = 1;
						$data_guias["guias"][$i]["guia_serie_numero"] = $rowEnlace->_ID_Serie_Documento . '-' . $rowEnlace->ID_Numero_Documento;
						$i++;
					}
				}
			}

			$sCodigoTipoOperacionSunat = "01" . $arrData[0]->Nu_Codigo_Sunat_Tipo_Transaccion;
			if ($arrData[0]->Nu_Codigo_Sunat_Tipo_Transaccion=='02')
				$sCodigoTipoOperacionSunat = "0200";

			$Txt_Glosa = $arrData[0]->Txt_Glosa;
			$fTotal = 0.00;
			$fTotal = round(($arrData[0]->Ss_Total - $Ss_Gratuita), 2);
			$fTotal = abs($fTotal);
			if($arrData[0]->Ss_Retencion > 0.00){
				$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Retencion, 2) . ' <br>';
				$Txt_Glosa .= '<b>Base imponible de la retención:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
				$Txt_Glosa .= '<b>Monto de la retención:</b> ' . round($arrData[0]->Ss_Retencion, 2) . ' <br>';
			}
			
			$iPoDetraccion=0;
			if($arrData[0]->Ss_Detraccion > 0.00){
				$Txt_Glosa .= '<br><b>Monto neto de pago:</b> ' . round($fTotal - $arrData[0]->Ss_Detraccion, 2) . ' <br>';
				$Txt_Glosa .= '<b>Base imponible de la detracción:</b> ' . ($arrData[0]->Ss_Total) . ' <br>';
				$Txt_Glosa .= '<b>Monto de la detracción:</b> ' . round($arrData[0]->Ss_Detraccion, 2) . ' <br>';
				$iPoDetraccion=12;
			}

			$data_cabecera = array(
				"operacion"	=> "recuperar_pdf_venta",
				"TIPO_OPERACION" => $sCodigoTipoOperacionSunat,
				"TIPO_RUBRO_EMPRESA" => $arrData[0]->Nu_Tipo_Rubro_Empresa,
				"COD_TIPO_DOCUMENTO" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento,
				"NRO_COMPROBANTE" => $arrData[0]->ID_Serie_Documento . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
				"TIPO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Sunat_Codigo_TDI,
				"NRO_DOCUMENTO_CLIENTE" => $arrData[0]->Nu_Documento_Identidad,
				"RAZON_SOCIAL_CLIENTE" => $arrData[0]->No_Entidad,
				"DIRECCION_CLIENTE" => $arrData[0]->Txt_Direccion_Entidad,
				"CIUDAD_CLIENTE" => "",
				"COD_PAIS_CLIENTE" => "",
				"FECHA_DOCUMENTO" => $arrData[0]->Fe_Emision,
				"FECHA_HORA_DOCUMENTO" => $arrData[0]->Fe_Emision_Hora,
				"FECHA_VTO" => $arrData[0]->Fe_Vencimiento,
				"COD_MONEDA" => $arrData[0]->Nu_Sunat_Codigo_Moneda,
				"POR_IGV" => $Po_IGV,
				"TOTAL_DESCUENTO" => $fDescuentoTotalOperacion,
				"TOTAL_DESCUENTO_ITEM" => $fDescuentoTotalOperacionItem,
				"SUB_TOTAL" => $Ss_Gravada + $Ss_Inafecto + $Ss_Exonerada,
				"TOTAL_GRAVADAS" => $Ss_Gravada,
				"TOTAL_INAFECTA" => $Ss_Inafecto,
				"TOTAL_EXONERADAS" => $Ss_Exonerada,
				"TOTAL_IGV" => $Ss_IGV,
				"TOTAL_GRATUITAS" => $Ss_Gratuita,
				"TOTAL" => $fTotal,
				"ICBP" => $fTotalIcbper,
				"TIPO_COMPROBANTE_MODIFICA" => $arrData[0]->Nu_Sunat_Codigo_Tipo_Documento_Modificar,
				"NRO_DOCUMENTO_MODIFICA" => $arrData[0]->ID_Serie_Documento_Modificar . '-' . autocompletarConCeros('', $arrData[0]->ID_Numero_Documento_Modificar, $arrData[0]->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT),
				"COD_TIPO_MOTIVO" => (strlen($arrData[0]->Nu_Codigo_Motivo_Referencia) == 1 ?  "0" . $arrData[0]->Nu_Codigo_Motivo_Referencia : $arrData[0]->Nu_Codigo_Motivo_Referencia),
				"DESCRIPCION_MOTIVO" => '',
				"TOTAL_LETRAS" => $EnLetras->ValorEnLetras($fTotal, $arrData[0]->No_Moneda),
				"GLOSA" => $Txt_Glosa,
				"ORDEN_COMPRA_SERVICIO" => $arrData[0]->No_Orden_Compra_FE,
				"PLACA_VEHICULO" => $arrData[0]->No_Placa_FE,
				"DETRACCION" => $arrData[0]->Nu_Detraccion,
				"HASH_CPE" => $arrData[0]->Txt_Hash,
				"CONDICIONES_DE_PAGO" => $sDiasCredito,
				"MEDIO_DE_PAGO" => $sConcatenarMultiplesMedioPago,
				"TXT_URL_CDR" => $arrData[0]->Txt_Url_CDR,
				"formato_de_pdf" => $arrData[0]->No_Formato_PDF,
				"tipo_recepcion" => $arrData[0]->Nu_Tipo_Recepcion,
				"celular_cliente" => $arrData[0]->Nu_Celular_Entidad,
				"RETENCION" => $arrData[0]->Nu_Retencion,
				"TOTAL_RETENCION" => $arrData[0]->Ss_Retencion,
				"TOTAL_DETRACCION" => $arrData[0]->Ss_Detraccion,
				"PORCENTAJE_DETRACCION" => $arrData[0]->Po_Detraccion,
				"TOTAL_VUELTO" => (($arrData[0]->Ss_Vuelto > 0.00 && $arrData[0]->Nu_Tipo==0) ? ($arrData[0]->Ss_Vuelto + $Ss_Gratuita) : 0),
				"VENDEDOR" => (isset($_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado) ? $_SESSION['arrDataPersonal']['arrData'][0]->No_Entidad : ''),
				"ID_DOCUMENTO_CABECERA" => $arrParams['iIdDocumentoCabecera'],
				"NUMERO_EXPEDIENTE" => $arrData[0]->Nu_Expediente_FE,
				"CODIGO_UNIDAD_EJECUTORA" => $arrData[0]->Nu_Codigo_Unidad_Ejecutora_FE,
				"USUARIO_VENDEDOR" => $arrData[0]->No_Usuario_Venta
			);
			$data = array_merge($data_cabecera, $data_detalle, $data_guias, $arrVentasCreditoCuotas);
			
			$ruta = $arrData[0]->Txt_FE_Ruta;
			$token = $arrData[0]->Txt_FE_Token;
			
			$arrParamsFE = array(
				"ruta" => $ruta,
				"token" => $token,
				"data" => $data
			);
			
			return $this->enviarApirecuperarPDFVentaSunat($arrParamsFE);
		} else {
			return $arrData;
		}// ./ if - else respuesta de modal del comprobante
	} // ./ generarFormatoDocumentoElectronicoSunat

	private function enviarApirecuperarPDFVentaSunat($arrParamsFE){
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
			
			return $leer_respuesta;
		} else {
			$arrParamsFE['estado_venta'] = 9;//9=Completado error
			return $respuesta;
		}
	}	
}
