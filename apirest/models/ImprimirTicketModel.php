<?php
class ImprimirTicketModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function obtenerComprobanteMedioPago($ID_Documento_Cabecera){
		$query = "SELECT
MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE,
MP.No_Medio_Pago, MP.Txt_Medio_Pago,
VMP.Ss_Total AS Ss_Total_Medio_Pago
FROM
documento_cabecera AS VC
JOIN documento_medio_pago AS VMP ON(VC.ID_Documento_Cabecera = VMP.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
WHERE
VC.ID_Documento_Cabecera = " . $ID_Documento_Cabecera;

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener medio(s) de pago',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				//'sql' => $query,
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
	
	public function formatoImpresionTicket($ID_Documento_Cabecera){
		$query = "SELECT
CONFI.Txt_Url_Logo_Lae_Shop,
CONFI.No_Tienda_Lae_Shop,
CONFI.Txt_Email_Lae_Shop,
EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
EMP.No_Empresa,
EMP.No_Empresa_Comercial,
EMP.Txt_Direccion_Empresa,
ALMA.Txt_Direccion_Almacen,
TDOCU.No_Tipo_Documento,
VC.ID_Documento_Cabecera,
VD.ID_Documento_Detalle,
VC.No_Formato_PDF,
VC.Fe_Emision_Hora,
VC.Fe_Emision,
VC.Fe_Vencimiento,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDOCUIDE.No_Tipo_Documento_Identidad_Breve,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
CLI.Nu_Celular_Entidad,
CLI.Txt_Direccion_Entidad,
CLI.Nu_Dias_Credito,
PRO.Nu_Codigo_Barra,
PRO.No_Codigo_Interno,
PRO.No_Producto,
'' AS No_Laboratorio,
'' AS Qt_CO2_Producto,
'' AS ID_Impuesto_Icbper,
VD.Qt_Producto,
ROUND(VD.Ss_Precio, 3) AS ss_precio_unitario,
IMP.Nu_Tipo_Impuesto,
ROUND(VD.Ss_SubTotal, 2) AS Ss_SubTotal_Producto,
ROUND(VD.Ss_Impuesto, 2) AS Ss_Impuesto_Producto,
ROUND(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto, 2) AS Ss_Descuento_Producto,
ROUND(VD.Ss_Descuento, 2) AS Ss_Descuento_Producto_SinImpuesto,
ROUND(VD.Ss_Total, 2) AS Ss_Total_Producto,
ICDOCU.Ss_Impuesto,
TDOCU.Nu_Impuesto,
ICDOCU.Po_Impuesto,
ROUND(VC.Ss_Total, 2) AS Ss_Total,
MONE.No_Moneda,
MONE.No_Signo,
CONFI.No_Dominio_Empresa,
'' AS No_Empleado,
VD.Txt_Nota AS Txt_Nota_Item,
VC.Txt_Hash,
MP.No_Medio_Pago,
MP.Txt_Medio_Pago,
MP.No_Codigo_Sunat_FE AS No_Codigo_Sunat_FE_MP,
MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE,
VC.Ss_Descuento AS Ss_Descuento_Total,
VC.Po_Descuento AS Po_Descuento_Total,
UM.Nu_Sunat_Codigo AS nu_codigo_unidad_medida_sunat,
'' AS No_Mesero,
VC.Txt_QR,
VC.Fe_Entrega,
VC.Ss_Total_Saldo,
EMP.Nu_Tipo_Proveedor_FE,
ORG.Nu_Estado_Sistema,
VC.Txt_Glosa AS Txt_Glosa_Global,
VC.Nu_Tipo_Recepcion,
VC.Ss_Vuelto,
VC.No_Orden_Compra_FE,
VC.No_Placa_FE,
VC.Txt_Garantia,
VC.Nu_Detraccion
FROM
documento_cabecera AS VC
JOIN empresa AS EMP ON(EMP.ID_Empresa = VC.ID_Empresa)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN organizacion AS ORG ON(VC.ID_Organizacion = ORG.ID_Organizacion)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN entidad AS CLI ON (CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDE ON (TDOCUIDE.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN tipo_documento AS TDOCU ON (TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN documento_detalle AS VD ON (VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON (ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON (IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN producto AS PRO ON (PRO.ID_Producto = VD.ID_Producto)
JOIN moneda AS MONE ON (VC.ID_Moneda = MONE.ID_Moneda)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
JOIN unidad_medida AS UM ON (UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
WHERE
VC.ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
		return $this->db->query($query)->result();
	}
	
	public function formatoImpresionTicketPreCuenta($ID_Pedido_Cabecera){
		$query = "SELECT
CONFI.No_Logo_Empresa,
CONFI.Nu_Height_Logo_Ticket,
CONFI.Nu_Width_Logo_Ticket,
CONFI.No_Dominio_Empresa,
CONFI.Nu_Tipo_Rubro_Empresa,
CONFI.Txt_Cuentas_Bancarias,
CONFI.Txt_Nota,
CONFI.Txt_Terminos_Condiciones,
CONFI.No_Imagen_Logo_Empresa,
CONFI.Txt_Slogan_Empresa,
ALMA.No_Logo_Url_Almacen,
EMP.Nu_MultiAlmacen,
ALMA.No_Logo_Almacen,
CONFI.Nu_Celular_Empresa,
CONFI.Nu_Telefono_Empresa,
CONFI.Txt_Email_Empresa,
CONFI.Nu_Logo_Empresa_Ticket,
EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
EMP.No_Empresa,
EMP.No_Empresa_Comercial,
EMP.Txt_Direccion_Empresa,
PC.ID_Pedido_Cabecera,
PD.ID_Pedido_Detalle,
PC.Fe_Emision_Hora,
TDOCUIDE.No_Tipo_Documento_Identidad_Breve,
PC.ID_Tipo_Documento,
CLI.ID_Entidad AS ID_Cliente,
CLI.ID_Tipo_Documento_Identidad,
CLI.Nu_Documento_Identidad,
CLI.Nu_Celular_Entidad,
CLI.Txt_Email_Entidad,
CLI.No_Entidad,
CLI.Txt_Direccion_Entidad,
PRO.ID_Producto,
PRO.No_Codigo_Interno,
PRO.No_Producto,
'' AS ID_Impuesto_Icbper,
PD.Qt_Producto,
ROUND(PD.Ss_Precio, 3) AS ss_precio_unitario,
IMP.Nu_Tipo_Impuesto,
ROUND(PD.Ss_SubTotal, 2) AS Ss_SubTotal_Producto,
ROUND(PD.Ss_Impuesto, 2) AS Ss_Impuesto_Producto,
ROUND(PD.Po_Descuento, 2) AS Po_Descuento_Producto,
ROUND(PD.Ss_Descuento, 2) AS Ss_Sub_Descuento_Producto,
ROUND(PD.Ss_Descuento + PD.Ss_Descuento_Impuesto, 2) AS Ss_Descuento_Producto,
ROUND(PD.Ss_Descuento_Impuesto, 2) AS Ss_Descuento_Impuesto_Producto,
ROUND(PD.Ss_Total, 2) AS Ss_Total_Producto,
ICDOCU.ID_Impuesto_Cruce_Documento,
ICDOCU.Ss_Impuesto,
ICDOCU.Po_Impuesto,
ROUND(PC.Ss_Total, 2) AS Ss_Total,
MONE.No_Moneda,
MONE.No_Signo,
CONFI.No_Dominio_Empresa,
EMPLE.No_Entidad AS No_Empleado,
PD.Txt_Nota AS Txt_Nota_Item,
PC.Ss_Descuento AS Ss_Descuento_Total,
UM.Nu_Sunat_Codigo AS nu_codigo_unidad_medida_sunat,
UM.No_Unidad_Medida,
MOZO.No_Nombres_Apellidos AS No_Mesero,
MESA.No_Mesa_Restaurante,
EMP.Nu_Tipo_Proveedor_FE,
ORG.Nu_Estado_Sistema,
PC.Txt_Glosa AS Txt_Glosa_Global,
ALMA.Txt_Direccion_Almacen,
PC.Nu_Tipo_Recepcion,
PC.Fe_Entrega,
0 AS Ss_Icbper_Item,
PD.Ss_Icbper,
PC.ID_Lista_Precio_Cabecera,
PC.ID_Canal_Venta_Tabla_Dato,
PC.No_Orden_Compra_FE,
PC.No_Formato_PDF,
PC.Nu_Retencion,
PC.Ss_Retencion,
PC.Ss_Detraccion,
PC.Po_Detraccion,
PC.Ss_Vuelto,
PC.Po_Descuento AS Po_Descuento_Total,
PC.Ss_Descuento_Impuesto,
PC.Nu_Cantidad_Personas_Restaurante,
PC.Txt_Url_PDF AS enlace_del_pdf,
ERESTA.No_Escenario_Restaurante,
FAMI.Nu_Imprimir_Comanda_Restaurante
FROM
pedido_cabecera AS PC
JOIN empresa AS EMP ON(EMP.ID_Empresa = PC.ID_Empresa)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN organizacion AS ORG ON(PC.ID_Organizacion = ORG.ID_Organizacion)
JOIN almacen AS ALMA ON(PC.ID_Almacen = ALMA.ID_Almacen)
JOIN entidad AS CLI ON (CLI.ID_Entidad = PC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDE ON (TDOCUIDE.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN pedido_detalle AS PD ON (PD.ID_Pedido_Cabecera = PC.ID_Pedido_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON (ICDOCU.ID_Impuesto_Cruce_Documento = PD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON (IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN producto AS PRO ON (PRO.ID_Producto = PD.ID_Producto)
JOIN familia AS FAMI ON(PRO.ID_Familia = FAMI.ID_Familia)
JOIN moneda AS MONE ON (PC.ID_Moneda = MONE.ID_Moneda)
JOIN matricula_empleado AS MEMPLE ON(PC.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
JOIN entidad AS EMPLE ON(MEMPLE.ID_Entidad = EMPLE.ID_Entidad)
JOIN unidad_medida AS UM ON (UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN usuario AS MOZO ON(MOZO.ID_Usuario = PC.ID_Mesero)
LEFT JOIN mesa_restaurante AS MESA ON(MESA.ID_Mesa_Restaurante = PC.ID_Mesa)
LEFT JOIN escenario_restaurante AS ERESTA ON(ERESTA.ID_Escenario_Restaurante = MESA.ID_Escenario_Restaurante)
WHERE
PC.ID_Pedido_Cabecera = " . $ID_Pedido_Cabecera;
		return $this->db->query($query)->result();
	}

	public function formatoImpresionTicketOrden($ID_Venta_Temporal_Cabecera){
		$query = "SELECT
EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
EMP.No_Empresa,
EMP.Txt_Direccion_Empresa,
VC.ID_Pedido_Cabecera AS ID_Venta_Temporal_Cabecera,
VC.Fe_Emision_Hora,
EMPLE.No_Entidad AS No_Empleado,
MOZO.No_Entidad AS No_Mesero,
MESA.No_Mesa,
PRO.No_Producto,
VD.Qt_Producto,
ROUND(VD.Ss_Precio, 3) AS ss_precio_unitario,
IMP.Nu_Tipo_Impuesto,
0 AS Ss_Descuento,
VD.Ss_SubTotal AS Ss_SubTotal_Producto,
ICDOCU.Ss_Impuesto AS Ss_Impuesto_Producto,
ICDOCU.Po_Impuesto,
ROUND(VC.Ss_Total, 2) AS Ss_Total,
MONE.No_Moneda,
MONE.No_Signo,
CONFI.No_Dominio_Empresa,
TDRECEPCION.No_Descripcion AS No_Recepcion,
VD.Txt_Nota
FROM
pedido_cabecera AS VC
JOIN empresa AS EMP ON (EMP.ID_Empresa = VC.ID_Empresa)
JOIN configuracion AS CONFI ON (CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN pedido_detalle AS VD ON (VD.ID_Pedido_Cabecera = VC.ID_Pedido_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON (ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON (IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN producto AS PRO ON (PRO.ID_Producto = VD.ID_Producto)
JOIN moneda AS MONE ON (VC.ID_Moneda = MONE.ID_Moneda)
JOIN matricula_empleado AS MEMPLE ON(VC.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
JOIN entidad AS EMPLE ON(MEMPLE.ID_Entidad = EMPLE.ID_Entidad)
JOIN entidad AS MOZO ON(VC.ID_Mesero = MOZO.ID_Entidad)
JOIN mesa AS MESA ON(MESA.ID_Mesa = VC.ID_Mesa)
JOIN tabla_dato AS TDRECEPCION ON(TDRECEPCION.Nu_Valor = VC.Nu_Tipo_Recepcion AND TDRECEPCION.No_Relacion = 'Tipos_recepcion')
WHERE
VC.ID_Pedido_Cabecera = " . $ID_Venta_Temporal_Cabecera;
		return $this->db->query($query)->result();
	}
	
	public function formatoImpresionTicketComandaLavado($arrPost){
		//TEEPEDIDOLAVA.No_Descripcion AS No_Estado_Pedido_Lavado,
		//JOIN tabla_dato AS TEEPEDIDOLAVA ON(TEEPEDIDOLAVA.Nu_Valor = VC.Nu_Transporte_Lavanderia_Hoy AND TEEPEDIDOLAVA.No_Relacion = 'Tipos_EstadoEnvioPedidoLavado')
		$iIdDocumentoCabecera = $arrPost['iIdDocumentoCabecera'];
		$query = "SELECT
CONFI.No_Logo_Empresa,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision_Hora,
VC.Fe_Entrega,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
CLI.Nu_Celular_Entidad,
VD.Qt_Producto,
PRO.No_Producto,
VD.Txt_Nota AS Txt_Nota_Item,
EMPLE.No_Entidad AS No_Empleado,
VC.Nu_Transporte_Lavanderia_Hoy
FROM
documento_cabecera AS VC
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = VC.ID_Empresa)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN documento_detalle AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN matricula_empleado AS MEMPLE ON(VC.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
JOIN entidad AS EMPLE ON(MEMPLE.ID_Entidad = EMPLE.ID_Entidad)
WHERE
VC.ID_Documento_Cabecera = " . $iIdDocumentoCabecera;
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query,
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
		);
	}
	
	public function formatoImpresionTicketGuia($ID_Documento_Cabecera){
		$query = "SELECT
CONFI.Nu_Celular_Empresa,
CONFI.Nu_Telefono_Empresa,
CONFI.Txt_Email_Empresa,
CONFI.Nu_Logo_Empresa_Ticket,
EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
EMP.No_Empresa,
EMP.No_Empresa_Comercial,
EMP.Txt_Direccion_Empresa,
ALMA.Txt_Direccion_Almacen,
VC.ID_Documento_Cabecera,
VD.ID_Documento_Detalle,
VC.Fe_Emision_Hora,
VC.Fe_Emision,
VC.Fe_Vencimiento,
VC.ID_Tipo_Documento,
TDG.No_Tipo_Documento,
GC.ID_Serie_Documento,
GC.ID_Numero_Documento,
TDOCUIDE.No_Tipo_Documento_Identidad_Breve,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
CLI.Nu_Celular_Entidad,
CLI.Txt_Direccion_Entidad,
PRO.No_Codigo_Interno,
PRO.No_Producto,
LAB.No_Laboratorio_Breve,
VD.Qt_Producto,
VC.Ss_Total,
MONE.No_Moneda,
MONE.No_Signo,
CONFI.No_Dominio_Empresa,
VD.Txt_Nota AS Txt_Nota_Item,
MP.No_Medio_Pago,
MP.No_Codigo_Sunat_FE AS No_Codigo_Sunat_FE_MP,
MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE,
UM.Nu_Sunat_Codigo AS nu_codigo_unidad_medida_sunat,
VC.Txt_QR,
VC.Txt_Hash,
VC.Fe_Entrega,
EMP.Nu_Tipo_Proveedor_FE,
ORG.Nu_Estado_Sistema,
VC.Ss_Vuelto,
VC.Nu_Tipo_Recepcion,
TDOCUIDEN.No_Tipo_Documento_Identidad_Breve AS No_Tipo_Documento_Identidad_Breve_Transporte,
TRANS.Nu_Documento_Identidad AS Nu_Documento_Identidad_Transportista,
TRANS.No_Entidad AS No_Entidad_Transportista,
F.No_Placa
FROM
documento_cabecera AS VC
JOIN empresa AS EMP ON(EMP.ID_Empresa = VC.ID_Empresa)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN organizacion AS ORG ON(VC.ID_Organizacion = ORG.ID_Organizacion)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN entidad AS CLI ON (CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDE ON (TDOCUIDE.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN documento_detalle AS VD ON (VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN producto AS PRO ON (PRO.ID_Producto = VD.ID_Producto)
LEFT JOIN laboratorio AS LAB ON (LAB.ID_Laboratorio = PRO.ID_Laboratorio)
JOIN moneda AS MONE ON (VC.ID_Moneda = MONE.ID_Moneda)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
JOIN unidad_medida AS UM ON (UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN guia_enlace AS GE ON(GE.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN guia_cabecera AS GC ON(GC.ID_Guia_Cabecera = GE.ID_Guia_Cabecera)
JOIN tipo_documento AS TDG ON(TDG.ID_Tipo_Documento = GC.ID_Tipo_Documento)
JOIN flete AS F ON(F.ID_Guia_Cabecera = GC.ID_Guia_Cabecera)
JOIN entidad AS TRANS ON(TRANS.ID_Entidad = F.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDEN ON(TRANS.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
WHERE
VC.ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
		return $this->db->query($query)->result();
	}
}
