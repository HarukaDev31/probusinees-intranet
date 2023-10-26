<?php
class ImprimirLiquidacionCajaModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
	public function formatoImpresionLiquidacionCaja($arrPost){
		$arrDataVentasMultiples = array();
		
		$iIdMatriculaPersonal = $arrPost['iIdMatriculaEmpleado'];
		$iIdEnlaceAperturaCaja = $arrPost['iIdEnlaceAperturaCaja'];

		$campo_familia_item = "(CASE WHEN FAMI.No_Familia != '' THEN FAMI.No_Familia ELSE 'SIN CATEGORIA' END) AS No_Familia_Item,";
		$groupby_familia_item = 'FAMI.ID_Familia, FAMI.No_Familia, ';
		$orderby_familia_item = 'FAMI.No_Familia,';
		if ( $this->empresa->Nu_Imprimir_Liquidacion_Caja == 2 ) {// 2 detallado por item
			$campo_familia_item = 'ITEM.No_Producto AS No_Familia_Item,';
			if($this->empresa->ID_Empresa==175)
				$campo_familia_item = 'CONCAT(ITEM.Nu_Codigo_Barra, " - ", ITEM.No_Producto) AS No_Familia_Item,';
			$groupby_familia_item = 'ITEM.ID_Producto, ITEM.No_Producto, ';
			$orderby_familia_item = 'ITEM.No_Producto,';
		}

		$query = "SELECT
" . $campo_familia_item . "
SUM(VD.Qt_Producto) AS Qt_Producto,
MONE.No_Signo,
SUM(VD.Ss_Total) AS Ss_Total
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
LEFT JOIN familia AS FAMI ON(ITEM.ID_Familia = FAMI.ID_Familia)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . " AND VC.ID_Tipo_Documento!=5
GROUP BY
" . $groupby_familia_item . "
VC.ID_Moneda,
MONE.No_Signo
ORDER BY
" . $orderby_familia_item . "
VC.ID_Moneda";
 
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

		$arrDataVentasMultiples['VentasxFamilia'] = array();
		if ( $arrResponseSQL->num_rows() > 0 )
			$arrDataVentasMultiples['VentasxFamilia'] = $arrResponseSQL->result();

		$query = "SELECT
" . $campo_familia_item . "
SUM(VD.Qt_Producto) AS Qt_Producto,
MONE.No_Signo,
SUM(VD.Ss_Total) AS Ss_Total
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
LEFT JOIN familia AS FAMI ON(ITEM.ID_Familia = FAMI.ID_Familia)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . " AND VC.ID_Tipo_Documento=5
GROUP BY
" . $groupby_familia_item . "
VC.ID_Moneda,
MONE.No_Signo
ORDER BY
" . $orderby_familia_item . "
VC.ID_Moneda";
	
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

		$arrDataVentasMultiples['VentasxFamiliaNotaCredito'] = array();
		if ( $arrResponseSQL->num_rows() > 0 )
			$arrDataVentasMultiples['VentasxFamiliaNotaCredito'] = $arrResponseSQL->result();

		//Ventas por REGALO o GRATUITAS
		$query = "SELECT
MONE.No_Signo,
SUM(CASE WHEN IMP.Nu_Tipo_Impuesto=4 THEN VD.Ss_Total ELSE 0 END) AS Ss_Total
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE VC.ID_Matricula_Empleado = " . $iIdMatriculaPersonal . "
GROUP BY
VC.ID_Moneda,
MONE.No_Signo";
	
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

		$arrDataVentasMultiples['VentasxRegaloGratuita'] = array();
		if ( $arrResponseSQL->num_rows() > 0 )
			$arrDataVentasMultiples['VentasxRegaloGratuita'] = $arrResponseSQL->result();
		
		$query = "SELECT
MONE.No_Signo,
ROUND(SUM(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto), 2) AS Ss_Total
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Matricula_Empleado = " . $iIdMatriculaPersonal . "
GROUP BY
VC.ID_Moneda,
MONE.No_Signo";
 
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

		$arrDataVentasMultiples['VentasxDescuento'] = array();
		if ( $arrResponseSQL->num_rows() > 0 )
			$arrDataVentasMultiples['VentasxDescuento'] = $arrResponseSQL->result();
		
$query = "SELECT
MONE.No_Signo,
SUM(VC.Ss_Descuento + VC.Ss_Descuento_Impuesto) AS Ss_Total
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE VC.ID_Matricula_Empleado = " . $iIdMatriculaPersonal . "
GROUP BY
VC.ID_Moneda,
MONE.No_Signo";
	
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

		$arrDataVentasMultiples['VentasxDescuentoTotal'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ) {
			$arrDataVentasMultiples['VentasxDescuentoTotal'] = $arrResponseSQL->result();
			if (count($arrDataVentasMultiples['VentasxDescuento']) > 0) {
				foreach ($arrDataVentasMultiples['VentasxDescuentoTotal'] as $row) {
					$arrDataVentasMultiples['VentasxDescuento'][0]->Ss_Total = $row->Ss_Total + $arrDataVentasMultiples['VentasxDescuento'][0]->Ss_Total;
				}				
			} else {
				$arrDataVentasMultiples['VentasxDescuento'] = $arrDataVentasMultiples['VentasxDescuentoTotal'];
			}
		}

		$query = "SELECT
TOC.No_Tipo_Operacion_Caja,
MONE.No_Signo,
ROUND(SUM(MVPOS.Ss_Total), 2) AS Ss_Total,
TOC.Nu_Tipo
FROM
caja_pos AS CC
JOIN caja_pos AS AC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN caja_pos AS MVPOS ON(MVPOS.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN tipo_operacion_caja AS TOC ON(TOC.ID_Tipo_Operacion_Caja = MVPOS.ID_Tipo_Operacion_Caja)
JOIN moneda AS MONE ON(MONE.ID_Moneda = MVPOS.ID_Moneda)
JOIN matricula_empleado AS ME ON(ME.ID_Matricula_Empleado = MVPOS.ID_Matricula_Empleado)
JOIN entidad AS TRAB ON(TRAB.ID_Entidad = ME.ID_Entidad)
WHERE MVPOS.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND TOC.Nu_Tipo != 4
GROUP BY
MVPOS.ID_Tipo_Operacion_Caja,
MVPOS.ID_Moneda,
TOC.No_Tipo_Operacion_Caja,
MONE.No_Signo,
TOC.Nu_Tipo
ORDER BY
TOC.No_Tipo_Operacion_Caja,
MVPOS.ID_Moneda";

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

		$arrDataVentasMultiples['MovimientosCaja'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['MovimientosCaja'] = $arrResponseSQL->result();
		}

		$query = "SELECT
VC.ID_Documento_Cabecera,
MP.No_Medio_Pago,
MONE.No_Signo,
VMP.Ss_Total AS Ss_Total_VMP
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND MP.Nu_Tipo = 0
AND VC.ID_Tipo_Documento!=5
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
AND (VC.Nu_Transporte_Lavanderia_Hoy = 0 OR VC.Nu_Transporte_Lavanderia_Hoy = 1)
ORDER BY
VC.ID_Documento_Cabecera";

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

		$arrDataVentasMultiples['VentasGeneralesEfectivo'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$fTotalEfectivoxDocumento = 0.00;
			$fTotalOtrosMPxDocumento = 0.00;
			$fTotalxDocumento = 0.00;
			$fSumTotalEfectivo = 0.00;
			$iIdDocumentoCabecera = 0;
			foreach ($arrResponseSQL->result() as $row){
				$fSumTotalEfectivo += $row->Ss_Total_VMP;
			}
			$arrDataVentasMultiples['VentasGeneralesEfectivo'][0] = (object)array(
				'No_Medio_Pago' => 'Efectivo',
				'No_Signo' => 'S/',
				'Ss_Total' => $fSumTotalEfectivo,
				'Nu_Tipo_Caja' => 0,
			);
		}

		$query = "SELECT
MP.No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VMP.Ss_Total), 2) AS Ss_Total,
MP.Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND MP.Nu_Tipo_Caja != 0
AND MP.Nu_Tipo != 1
AND VC.ID_Tipo_Documento!=5
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
AND (VC.Nu_Transporte_Lavanderia_Hoy = 0 OR VC.Nu_Transporte_Lavanderia_Hoy = 1)
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo,
MP.Nu_Tipo_Caja
ORDER BY
MP.No_Medio_Pago,
VC.ID_Moneda";

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

		$arrDataVentasMultiples['VentasGeneralesSinEfectivo'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesSinEfectivo'] = $arrResponseSQL->result();
		}

		// No Suma los clientes con venta al crédito solo muestra
		// ROUND(SUM(VC.Ss_Total_Saldo), 2) AS Ss_Total, antes 01/02/2020
		$query = "SELECT
CONCAT('Clientes de Crédito') AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VC.Ss_Total), 2) AS Ss_Total,
1 AS Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND MP.Nu_Tipo = 1
AND (VC.Nu_Transporte_Lavanderia_Hoy = 0 OR VC.Nu_Transporte_Lavanderia_Hoy = 1)
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo";
 
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

		$arrDataVentasMultiples['VentasGeneralesCreditoCreditoNoSuma'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesCreditoCreditoNoSuma'] = $arrResponseSQL->result();
		}

		// Suma de pago unico al momento de generar la venta al crédito
		$arrDataVentasMultiples['VentasGeneralesCreditoAdelanto'] = array();
		/*
		$query = "SELECT
CONCAT('Efectivo - Cliente ', MP.No_Medio_Pago, ' (Pagos Acuenta en la Venta)') AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VC.Ss_Vuelto), 2) AS Ss_Total,
0 AS Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND MP.Nu_Tipo = 1
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
AND (VC.Nu_Transporte_Lavanderia_Hoy = 0 OR VC.Nu_Transporte_Lavanderia_Hoy = 1)
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo";

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

		$arrDataVentasMultiples['VentasGeneralesCreditoAdelanto'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelanto'] = $arrResponseSQL->result();
		}
		*/
		
		// Suma de pagos adelantados del cliente al crédito
		//  AND (VC.Nu_Transporte_Lavanderia_Hoy != 0 OR VC.Nu_Transporte_Lavanderia_Hoy = 1) - hoy 01/08/2022
		$query = "SELECT
CONCAT(MP.No_Medio_Pago, ' - Cliente crédito (Adicionales)') AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VMP.Ss_Total), 2) AS Ss_Total,
MP.Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_cabecera AS VC ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE VMP.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND VMP.ID_Documento_Medio_Pago_Enlace>0
AND VC.Nu_Transporte_Lavanderia_Hoy NOT IN(2,3)
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo,
MP.Nu_Tipo_Caja";
 
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

		$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionales'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionales'] = $arrResponseSQL->result();
		}
		
		
		$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaEfectivo'] = array();
		$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaEfectivo'] = array();
		$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaSinEfectivo'] = array();
		$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaSinEfectivo'] = array();
		$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaEfectivo'] = array();
		$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaSinEfectivo'] = array();
		$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderia'] = array();
		$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderiaExterno'] = array();

		// LAVANDERIA VAPI 
		if ($this->empresa->Nu_Tipo_Rubro_Empresa == 3) {
			// Lavanderia Interna - Efectivo
			$query = "SELECT
VC.ID_Documento_Cabecera,
MP.No_Medio_Pago,
MONE.No_Signo,
VMP.Ss_Total AS Ss_Total_VMP,
VC.Ss_Total AS Ss_Total_VC,
MP.Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND VC.Nu_Transporte_Lavanderia_Hoy = 3
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
ORDER BY
VC.ID_Documento_Cabecera";

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

		$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaEfectivo'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$fTotalEfectivoxDocumento = 0.00;
			$fTotalOtrosMPxDocumento = 0.00;
			$fTotalxDocumento = 0.00;
			$fSumTotalEfectivo = 0.00;
			$iIdDocumentoCabecera = 0;
			foreach ($arrResponseSQL->result() as $row){
				if ( $iIdDocumentoCabecera != $row->ID_Documento_Cabecera ) {
					$iIdDocumentoCabecera = $row->ID_Documento_Cabecera;
					$fTotalEfectivoxDocumento = 0.00;
					$fTotalOtrosMPxDocumento = 0.00;
					$fTotalxDocumento = 0.00;
				}
				$fTotalxDocumento += $row->Ss_Total_VMP;
				if ( $row->Nu_Tipo_Caja != 0 )
					$fTotalOtrosMPxDocumento += $row->Ss_Total_VMP;
				if ( $row->Nu_Tipo_Caja == 0 )
					$fTotalEfectivoxDocumento += $row->Ss_Total_VMP;
				if ( $fTotalxDocumento > $row->Ss_Total_VC )
					$fSumTotalEfectivo += $row->Ss_Total_VC - $fTotalOtrosMPxDocumento;
				else if ( $fTotalxDocumento == $row->Ss_Total_VC )
					$fSumTotalEfectivo += $fTotalEfectivoxDocumento;
			}
			$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaEfectivo'][0] = (object)array(
				'No_Medio_Pago' => 'Servicio Interno - Efectivo',
				'No_Signo' => 'S/',
				'Ss_Total' => $fSumTotalEfectivo,
				'Nu_Tipo_Caja' => 0,
			);
		}

		// Lavanderia Externa - Efectivo
		$query = "SELECT
VC.ID_Documento_Cabecera,
MP.No_Medio_Pago,
MONE.No_Signo,
CASE WHEN VC.ID_Tipo_Documento!=5 THEN VMP.Ss_Total ELSE 0 END AS Ss_Total_VMP,
CASE WHEN VC.ID_Tipo_Documento!=5 THEN VC.Ss_Total - (
	SELECT SUM(CASE WHEN IMP.Nu_Tipo_Impuesto = 4 THEN VD.Ss_Total ELSE 0 END) AS Ss_Total FROM
documento_detalle AS VD
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera
) ELSE 0 END AS Ss_Total_VC,
CASE WHEN VC.ID_Tipo_Documento=5 THEN VMP.Ss_Total ELSE 0 END AS Ss_Total_VMP_NC,
CASE WHEN VC.ID_Tipo_Documento=5 THEN VC.Ss_Total - (
	SELECT SUM(CASE WHEN IMP.Nu_Tipo_Impuesto = 4 THEN VD.Ss_Total ELSE 0 END) AS Ss_Total FROM
documento_detalle AS VD
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera
) ELSE 0 END AS Ss_Total_VC_NC,
VC.Ss_Vuelto,
VC.Ss_Total_Saldo,
MP.Nu_Tipo_Caja,
MP.Nu_Tipo
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND VC.Nu_Transporte_Lavanderia_Hoy = 2
AND MP.Nu_Tipo = 0
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
ORDER BY
VC.ID_Documento_Cabecera";

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

		$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaEfectivo'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$fTotalEfectivoxDocumento = 0.00;
			$fTotalOtrosMPxDocumento = 0.00;
			$fTotalxDocumento = 0.00;
			$fSumTotalEfectivo = 0.00;
			$iIdDocumentoCabecera = 0;
			foreach ($arrResponseSQL->result() as $row){
				$fSumTotalEfectivo += ($row->Ss_Total_VMP - $row->Ss_Total_VMP_NC);
				/*
				if ( $iIdDocumentoCabecera != $row->ID_Documento_Cabecera ) {
					$iIdDocumentoCabecera = $row->ID_Documento_Cabecera;
					$fTotalEfectivoxDocumento = 0.00;
					$fTotalOtrosMPxDocumento = 0.00;
					$fTotalxDocumento = 0.00;
				}
				$fTotalxDocumento += $row->Ss_Total_VMP;
				if ( $row->Nu_Tipo_Caja != 0 )
					$fTotalOtrosMPxDocumento += $row->Ss_Total_VMP;
				if ( $row->Nu_Tipo_Caja == 0 )
					$fTotalEfectivoxDocumento += $row->Ss_Total_VMP;
				if ( $fTotalxDocumento > $row->Ss_Total_VC )
					$fSumTotalEfectivo += $row->Ss_Total_VC - $fTotalOtrosMPxDocumento;
				else if ( $fTotalxDocumento == $row->Ss_Total_VC )
					$fSumTotalEfectivo += $fTotalEfectivoxDocumento;
				*/
			}
			$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaEfectivo'][0] = (object)array(
				'No_Medio_Pago' => 'Servicio Externa - Efectivo',
				'No_Signo' => 'S/',
				'Ss_Total' => $fSumTotalEfectivo,
				'Nu_Tipo_Caja' => 0,
			);
		}

		// Lavanderia Interna - Efectivo
		$query = "SELECT
CONCAT('Servicio Interno - Cliente ', MP.No_Medio_Pago, ' (Pagos Acuenta en la Venta)') AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VMP.Ss_Total), 2) AS Ss_Total,
0 AS Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND MP.Nu_Tipo_Caja != 0
AND VC.Nu_Transporte_Lavanderia_Hoy = 3
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo,
MP.Nu_Tipo_Caja
ORDER BY
MP.No_Medio_Pago,
VC.ID_Moneda";

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

		$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaSinEfectivo'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaSinEfectivo'] = $arrResponseSQL->result();
		}

		// Lavanderia Externa - Efectivo
		$query = "SELECT
CONCAT('Servicio Externo - Cliente ', MP.No_Medio_Pago, ' (Pagos Acuenta en la Venta)') AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VMP.Ss_Total), 2) AS Ss_Total,
MP.Nu_Tipo_Caja AS Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND MP.Nu_Tipo_Caja != 0
AND VC.Nu_Transporte_Lavanderia_Hoy = 2
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo,
MP.Nu_Tipo_Caja
ORDER BY
MP.No_Medio_Pago,
VC.ID_Moneda";

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

		$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaSinEfectivo'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaSinEfectivo'] = $arrResponseSQL->result();
		}
		
		// Suma de pagos adelantados del cliente al crédito
		$query = "SELECT
CONCAT('Servicio Interno - ', MP.No_Medio_Pago, ' - Cliente crédito (Adicionales)') AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VMP.Ss_Total), 2) AS Ss_Total,
MP.Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_cabecera AS VC ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE VMP.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND VMP.ID_Documento_Medio_Pago_Enlace>0
AND VC.Nu_Transporte_Lavanderia_Hoy = 3
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo,
MP.Nu_Tipo_Caja";
 
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

		$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderia'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderia'] = $arrResponseSQL->result();
		}
		
		// Suma de pagos adelantados del cliente al crédito
		$query = "SELECT
CONCAT('Servicio Externo - ', MP.No_Medio_Pago, ' - Cliente crédito (Adicionales)') AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VMP.Ss_Total), 2) AS Ss_Total,
MP.Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_cabecera AS VC ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE VMP.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND VMP.ID_Documento_Medio_Pago_Enlace>0
AND VC.Nu_Transporte_Lavanderia_Hoy = 2
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo,
MP.Nu_Tipo_Caja";
 
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

		$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderiaExterno'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderiaExterno'] = $arrResponseSQL->result();
		}

		// Empresas - Lavanderia
		$query = "SELECT
VC.ID_Documento_Cabecera,
MP.No_Medio_Pago,
MONE.No_Signo,
VMP.Ss_Total AS Ss_Total_VMP,
VC.Ss_Total AS Ss_Total_VC,
MP.Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND VC.Nu_Transporte_Lavanderia_Hoy = 4
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
ORDER BY
VC.ID_Documento_Cabecera";

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

		$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaEfectivo'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$fTotalEfectivoxDocumento = 0.00;
			$fTotalOtrosMPxDocumento = 0.00;
			$fTotalxDocumento = 0.00;
			$fSumTotalEfectivo = 0.00;
			$iIdDocumentoCabecera = 0;
			foreach ($arrResponseSQL->result() as $row){
				if ( $iIdDocumentoCabecera != $row->ID_Documento_Cabecera ) {
					$iIdDocumentoCabecera = $row->ID_Documento_Cabecera;
					$fTotalEfectivoxDocumento = 0.00;
					$fTotalOtrosMPxDocumento = 0.00;
					$fTotalxDocumento = 0.00;
				}
				$fTotalxDocumento += $row->Ss_Total_VMP;
				if ( $row->Nu_Tipo_Caja != 0 )
					$fTotalOtrosMPxDocumento += $row->Ss_Total_VMP;
				if ( $row->Nu_Tipo_Caja == 0 )
					$fTotalEfectivoxDocumento += $row->Ss_Total_VMP;
				if ( $fTotalxDocumento > $row->Ss_Total_VC )
					$fSumTotalEfectivo += $row->Ss_Total_VC - $fTotalOtrosMPxDocumento;
				else if ( $fTotalxDocumento == $row->Ss_Total_VC )
					$fSumTotalEfectivo += $fTotalEfectivoxDocumento;
			}
			$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaEfectivo'][0] = (object)array(
				'No_Medio_Pago' => 'Empresas - Efectivo',
				'No_Signo' => 'S/',
				'Ss_Total' => $fSumTotalEfectivo,
				'Nu_Tipo_Caja' => 0,
			);
		}

		$query = "SELECT
CONCAT('Empresas - ', MP.No_Medio_Pago) AS No_Medio_Pago,
MONE.No_Signo,
ROUND(SUM(VMP.Ss_Total), 2) AS Ss_Total,
MP.Nu_Tipo_Caja
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN documento_cabecera AS VC ON(VC.ID_Matricula_Empleado=CC.ID_Matricula_Empleado)
JOIN documento_medio_pago AS VMP ON(VMP.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE CC.ID_Matricula_Empleado = " . $arrPost['iIdMatriculaEmpleado'] . "
AND MP.Nu_Tipo_Caja != 0
AND VC.Nu_Transporte_Lavanderia_Hoy = 4
AND VMP.ID_Documento_Medio_Pago_Enlace IS NULL
GROUP BY
VMP.ID_Medio_Pago,
VC.ID_Moneda,
MP.No_Medio_Pago,
MONE.No_Signo,
MP.Nu_Tipo_Caja
ORDER BY
MP.No_Medio_Pago,
VC.ID_Moneda";
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

			$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaSinEfectivo'] = array();
			if ( $arrResponseSQL->num_rows() > 0 ){
				$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaSinEfectivo'] = $arrResponseSQL->result();
			}
			// Empresas - Lavanderia
		}// rubro = 3 lavanaderia VAPI

		// Obtener totales a liquidar y depositar
		$query = "SELECT
P.Nu_Pos AS ID_POS,
TRAB.No_Entidad,
AC.Fe_Movimiento AS Fe_Apertura,
CC.Fe_Movimiento AS Fe_Cierre,
SUM(CC.Ss_Expectativa) AS Ss_Expectativa,
SUM(CC.Ss_Total) AS Ss_Total
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN pos AS P ON(P.ID_POS = AC.ID_POS)
JOIN matricula_empleado AS ME ON(ME.ID_Matricula_Empleado = CC.ID_Matricula_Empleado)
JOIN entidad AS TRAB ON(TRAB.ID_Entidad = ME.ID_Entidad)
WHERE CC.ID_Matricula_Empleado=" . $arrPost['iIdMatriculaEmpleado'];

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				//'sql' => $query,
			);
		}
		$arrResponseSQL = $this->db->query($query);

		$arrDataVentasMultiples['TotalesLiquidacionCaja'] = array();
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataVentasMultiples['TotalesLiquidacionCaja'] = $arrResponseSQL->result();
		}

		$arrDataVentasMultiples['VentasGenerales'] = array_merge(
			$arrDataVentasMultiples['VentasGeneralesEfectivo'],
			$arrDataVentasMultiples['VentasGeneralesSinEfectivo'],
			$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaEfectivo'],
			$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaEfectivo'],			
			$arrDataVentasMultiples['VentasGeneralesServicioInternoLavanderiaSinEfectivo'],
			$arrDataVentasMultiples['VentasGeneralesServicioExternoLavanderiaSinEfectivo'],			
			$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaEfectivo'],
			$arrDataVentasMultiples['VentasGeneralesEmpresasLavanderiaSinEfectivo'],
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelanto'],
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionales'],
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderia'],
			$arrDataVentasMultiples['VentasGeneralesCreditoAdelantoAdicionalesLavanderiaExterno']
		);

		if (
			count( $arrDataVentasMultiples['VentasxFamilia'] ) > 0
			|| count( $arrDataVentasMultiples['MovimientosCaja'] ) > 0
			|| count( $arrDataVentasMultiples['VentasGenerales'] ) > 0
		){
			return array(
				'sStatus' => 'success',
				'arrData' => $arrDataVentasMultiples,
			);
		}
        
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No se encontro registro',
        );
	}
}
