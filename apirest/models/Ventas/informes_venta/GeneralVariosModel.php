<?php
class GeneralVariosModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
	public function ReporteDiarioDetalle($fecha, $Nu_Tipo_Producto, $iImpuesto){
		$column_tipo_producto = "";
		$cond_tipo_producto = "";
		$cond_tipo_productoC = "";
		$group_by = "2";
		
		if($Nu_Tipo_Producto == '0' || $Nu_Tipo_Producto == '1'){
			$column_tipo_producto = "ITEM.Nu_Tipo_Producto,";
			$cond_tipo_producto = "AND ITEM.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$group_by = "2, 3";
		}
        
        $column_total = "VD.Ss_SubTotal";
        $column_total_descuento_impuesto = "";
		if($iImpuesto == '1'){//Si
			$column_total = "VD.Ss_Total";
            $column_total_descuento_impuesto = " + COALESCE(VC.Ss_Descuento_Impuesto, 0) ";
		}

            $query = "SELECT
MONE.No_Signo,
VD.ID_Producto,
" . $column_tipo_producto . "
VD.No_Producto,
COALESCE(SUM(VD.Qt_Producto), 0) AS Qt_Producto,
(COALESCE(SUM(VD.total_interno), 0) + COALESCE(SUM(VD.total_bfnd), 0)) - COALESCE(SUM(VD.total_nc), 0) AS Ss_Vendido
FROM
documento_cabecera VC
JOIN moneda MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN (
SELECT
VD.ID_Documento_Cabecera,
ITEM.ID_Producto,
" . $column_tipo_producto . "
ITEM.No_Producto,
SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
(CASE WHEN VC.ID_Tipo_Documento=2 THEN SUM(VD.Ss_Total) - ((COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) / (SELECT COUNT(*) FROM documento_detalle AS VDPRIN WHERE VDPRIN.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)) END) AS total_interno,
(CASE WHEN VC.ID_Tipo_Documento IN(3,4,6) THEN SUM(" . $column_total . ") - ((COALESCE(VC.Ss_Descuento, 0) " . $column_total_descuento_impuesto . ") / (SELECT COUNT(*) FROM documento_detalle AS VDPRIN WHERE VDPRIN.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)) END) AS total_bfnd,
(CASE WHEN VC.ID_Tipo_Documento=5 THEN SUM(" . $column_total . ") - ((COALESCE(VC.Ss_Descuento, 0) " . $column_total_descuento_impuesto . ") / (SELECT COUNT(*) FROM documento_detalle AS VDPRIN WHERE VDPRIN.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)) END) AS total_nc
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
VC.ID_Empresa=" . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.Fe_Emision='" . $fecha . "'
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Tipo_Asiento=1
" . $cond_tipo_producto . "
GROUP BY
1,
" . $group_by . "
) AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa=" . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.Fe_Emision='" . $fecha . "'
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento=1
GROUP BY
1,
" . $group_by . "
ORDER BY
1,
" . $group_by;

		return $this->db->query($query)->result();
	}
	
	public function ReporteDiario($mes, $anio, $ID_Moneda, $Nu_Tipo_Producto, $iImpuesto){
		$column_tipo_producto = "";
		$cond_tipo_producto = "";
		$group_by = "";
		
		$where_id_moneda = ( !empty($ID_Moneda) ? "AND VC.ID_Moneda = " . $ID_Moneda : '' );

		if($Nu_Tipo_Producto == '0' || $Nu_Tipo_Producto == '1'){
			$column_tipo_producto = "ITEM.Nu_Tipo_Producto,";
			$cond_tipo_producto = "AND ITEM.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$group_by = ", 2";
        }
        
        $column_total = "VD.Ss_SubTotal";
        $column_total_descuento_impuesto = "";
		if($iImpuesto == '1'){//Si
			$column_total = "VD.Ss_Total";
            $column_total_descuento_impuesto = " + COALESCE(VC.Ss_Descuento_Impuesto, 0) ";
        }
        
		$sql = "SELECT
VC.Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo,
COALESCE(SUM(VD.Interno), 0) AS Interno,
COALESCE(SUM(VD.Boleta), 0) AS Boleta,
COALESCE(SUM(VD.Factura), 0) AS Factura,
COALESCE(SUM(VD.NCredito), 0) AS NCredito,
COALESCE(SUM(VD.NDebito), 0) AS NDebito,
(COALESCE(SUM(VD.Interno), 0) + COALESCE(SUM(VD.Boleta), 0) + COALESCE(SUM(VD.Factura), 0) + COALESCE(SUM(VD.NDebito), 0)) - COALESCE(SUM(VD.NCredito), 0) AS Vendido
FROM
documento_cabecera VC
JOIN moneda MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN (
SELECT
VD.ID_Documento_Cabecera,
" . $column_tipo_producto . "
(CASE WHEN VC.ID_Tipo_Documento=2 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS Interno,
(CASE WHEN VC.ID_Tipo_Documento=4 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) " . $column_total_descuento_impuesto . ") END) AS Boleta,
(CASE WHEN VC.ID_Tipo_Documento=3 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) " . $column_total_descuento_impuesto . ") END) AS Factura,
(CASE WHEN VC.ID_Tipo_Documento=5 THEN SUM(" . $column_total . ") END) AS NCredito,
(CASE WHEN VC.ID_Tipo_Documento=6 THEN SUM(" . $column_total . ") END) AS NDebito
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND YEAR(VC.Fe_Emision) = " . $anio . "
AND MONTH(VC.Fe_Emision) = " . $mes . "
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Tipo_Asiento = 1
$cond_tipo_producto
GROUP BY
1" . $group_by . "
) AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND YEAR(VC.Fe_Emision) = " . $anio . "
AND MONTH(VC.Fe_Emision) = " . $mes . "
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
GROUP BY
VC.Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo
ORDER BY
VC.Fe_Emision DESC;";
 
		$r['Tabla'] = $this->db->query($sql)->result();
		
		// Reporte Grafico
		$r['Grafica'] = array('Categoria' => '', 'Moneda' => '', 'Vendido' => '');
		$i = 0;
		$x = 0;
		
		for($i = 0; $i <= date('t', strtotime("$anio/$mes/01")); $i++){
			$encontrado = true;
			foreach($r['Tabla'] as $t){
				$d = date('d', strtotime($t->Fe_Emision));
				
				if($i == $d){
					$r['Grafica']['Categoria'] .= "'" . $i . "'" . ($i!=0 ? ',' : '');
					$r['Grafica']['Moneda'] .= $t->No_Signo . ($i!=0 ? ',' : '');
					$r['Grafica']['Vendido'] .= $t->Vendido . ($i!=0 ? ',' : '');
					
					$encontrado = false;
					break;
				}
			}
			
			if($encontrado == true && $i > 0){
				$r['Grafica']['Categoria'] .= $i . ',';
				$r['Grafica']['Moneda'] .= '0' . ',';
				$r['Grafica']['Vendido'] .= '0' . ',';
			}
		}
		return $r;
	}
	
	public function ReporteMensual($anio, $ID_Moneda, $Nu_Tipo_Producto, $iImpuesto){
		$column_tipo_producto = "";
		$cond_tipo_producto = "";
		$group_by = "";
		
		$where_id_moneda = ( !empty($ID_Moneda) ? "AND VC.ID_Moneda = " . $ID_Moneda : '' );

		if($Nu_Tipo_Producto == '0' || $Nu_Tipo_Producto == '1'){
			$column_tipo_producto = "ITEM.Nu_Tipo_Producto,";
			$cond_tipo_producto = "AND ITEM.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$group_by = ", 2";
		}
		
        $column_total = "VD.Ss_SubTotal";
		if($iImpuesto == '1')//Si
            $column_total = "VD.Ss_Total";
            
		$sql = "SELECT
MONTH(VC.Fe_Emision) AS Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo,
COALESCE(SUM(VD.Interno), 0) AS Interno,
COALESCE(SUM(VD.Boleta), 0) AS Boleta,
COALESCE(SUM(VD.Factura), 0) AS Factura,
COALESCE(SUM(VD.NCredito), 0) AS NCredito,
COALESCE(SUM(VD.NDebito), 0) AS NDebito,
(COALESCE(SUM(VD.Interno), 0) + COALESCE(SUM(VD.Boleta), 0) + COALESCE(SUM(VD.Factura), 0) + COALESCE(SUM(VD.NDebito), 0)) - COALESCE(SUM(VD.NCredito), 0) AS Vendido
FROM
documento_cabecera VC
JOIN moneda MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN (
SELECT
VD.ID_Documento_Cabecera,
" . $column_tipo_producto . "
(CASE WHEN VC.ID_Tipo_Documento=2 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS Interno,
(CASE WHEN VC.ID_Tipo_Documento=4 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS Boleta,
(CASE WHEN VC.ID_Tipo_Documento=3 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS Factura,
(CASE WHEN VC.ID_Tipo_Documento=5 THEN SUM(" . $column_total . ") END) AS NCredito,
(CASE WHEN VC.ID_Tipo_Documento=6 THEN SUM(" . $column_total . ") END) AS NDebito
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND IMP.Nu_Tipo_Impuesto!=4
AND YEAR(VC.Fe_Emision) = " . $anio . "
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
$cond_tipo_producto
GROUP BY
1" . $group_by . "
) AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND YEAR(VC.Fe_Emision) = " . $anio . "
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
GROUP BY
YEAR(VC.Fe_Emision),
MONTH(VC.Fe_Emision),
MONE.ID_Moneda,
MONE.No_Signo
ORDER BY
YEAR(VC.Fe_Emision),
MONTH(VC.Fe_Emision) DESC;";

		$r['Tabla'] = $this->db->query($sql)->result();
		
		// Reporte Grafico
		$r['Grafica'] = array('Categoria' => '', 'Moneda' => '', 'Vendido' => '');
		$i = 0;
		$x = 0;
		
		for($i = 1; $i <= 12; $i++){
			$encontrado = true;
			foreach($r['Tabla'] as $t){
				if($i == $t->Fe_Emision){
					$r['Grafica']['Categoria'] .= "'" . MonthToSpanish($i, true) . "'" . ($i!=0 ? ',' : '');
					$r['Grafica']['Moneda'] .= $t->No_Signo . ($i!=0 ? ',' : '');
					$r['Grafica']['Vendido'] .= $t->Vendido . ($i!=0 ? ',' : '');
					
					$encontrado = false;
					break;
				}
			}
			
			if($encontrado == true && $i > 0){
				$r['Grafica']['Categoria'] .= "'" . MonthToSpanish($i, true) . "',";
				$r['Grafica']['Moneda'] .= '0' . ',';
				$r['Grafica']['Vendido'] .= '0' . ',';
			}
		}
		return $r;
	}
	
	public function ReporteAnual($ID_Moneda, $Nu_Tipo_Producto, $iImpuesto){
		$column_tipo_producto = "";
		$cond_tipo_producto = "";
		$group_by = "";
		
		$where_id_moneda = ( !empty($ID_Moneda) ? "AND VC.ID_Moneda = " . $ID_Moneda : '' );

		if($Nu_Tipo_Producto == '0' || $Nu_Tipo_Producto == '1'){
			$column_tipo_producto = "ITEM.Nu_Tipo_Producto,";
			$cond_tipo_producto = "AND ITEM.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$group_by = ", 2";
		}
		
        $column_total = "VD.Ss_SubTotal";
		if($iImpuesto == '1')//Si
            $column_total = "VD.Ss_Total";
		
		$sql = "SELECT
YEAR(VC.Fe_Emision) AS Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo,
COALESCE(SUM(VD.Interno), 0) AS Interno,
COALESCE(SUM(VD.Boleta), 0) AS Boleta,
COALESCE(SUM(VD.Factura), 0) AS Factura,
COALESCE(SUM(VD.NCredito), 0) AS NCredito,
COALESCE(SUM(VD.NDebito), 0) AS NDebito,
(COALESCE(SUM(VD.Interno), 0) + COALESCE(SUM(VD.Boleta), 0) + COALESCE(SUM(VD.Factura), 0) + COALESCE(SUM(VD.NDebito), 0)) - COALESCE(SUM(VD.NCredito), 0) AS Vendido
FROM
documento_cabecera VC
JOIN moneda MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN (
SELECT
VD.ID_Documento_Cabecera,
" . $column_tipo_producto . "
(CASE WHEN VC.ID_Tipo_Documento=2 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS Interno,
(CASE WHEN VC.ID_Tipo_Documento=4 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS Boleta,
(CASE WHEN VC.ID_Tipo_Documento=3 THEN SUM(" . $column_total . ") - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS Factura,
(CASE WHEN VC.ID_Tipo_Documento=5 THEN SUM(" . $column_total . ") END) AS NCredito,
(CASE WHEN VC.ID_Tipo_Documento=6 THEN SUM(" . $column_total . ") END) AS NDebito
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Tipo_Asiento = 1
$cond_tipo_producto
GROUP BY
1" . $group_by . "
) AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
GROUP BY 
YEAR(VC.Fe_Emision),
MONE.ID_Moneda,
MONE.No_Signo
ORDER BY
YEAR(VC.Fe_Emision) DESC;";
 
		$r['Tabla'] = $this->db->query($sql)->result();
		
		// Reporte Grafico
		$r['Grafica'] = array('Categoria' => '', 'Moneda' => '', 'Vendido' => '');
		$i = 0;
        $x = 0;
        
        $arrYearSystemBD = (array)YearsYMD($this->empresa->Fe_Inicio_Sistema);
        for($i = $arrYearSystemBD[0]->year; $i <= date('Y'); $i++){
			$encontrado = true;
			foreach($r['Tabla'] as $t){
				if($i == $t->Fe_Emision){
					$r['Grafica']['Categoria'] .= "'" . $i . "'" . ($i!=0 ? ',' : '');
					$r['Grafica']['Moneda'] .= $t->No_Signo . ($i!=0 ? ',' : '');
					$r['Grafica']['Vendido'] .= $t->Vendido . ($i!=0 ? ',' : '');
					
					$encontrado = false;
					break;
				}
			}
			
			if($encontrado == true && $i > 0){
				$r['Grafica']['Categoria'] .= "'" . $i . "',";
				$r['Grafica']['Moneda'] .= '0' . ',';
				$r['Grafica']['Vendido'] .= '0' . ',';
			}
        }
		return $r;
	}
	
	public function MejoresClientes($m, $y, $ID_Moneda, $Nu_Tipo_Producto, $iOrder, $iImpuesto){
		$column_tipo_producto = "";
		$cond_tipo_producto = "";
		$group_by = "";
		
		$where_id_moneda = ( !empty($ID_Moneda) ? "AND VC.ID_Moneda = " . $ID_Moneda : '' );

		if($Nu_Tipo_Producto == '0' || $Nu_Tipo_Producto == '1'){
			$column_tipo_producto = "ITEM.Nu_Tipo_Producto,";
			$cond_tipo_producto = "AND ITEM.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$group_by = ", 2";
		}
		
		$order_by = " ORDER BY Vendido DESC;";
		if( $iOrder == 2 )
			$order_by = " ORDER BY Qt_Producto_2 DESC;";
	
        $column_total = "VD.Ss_SubTotal";
		if($iImpuesto == '1')//Si
            $column_total = "VD.Ss_Total";

        //filtro por un mes o todos
        $where_mes = ($m > 0 ? ' AND MONTH(VC.Fe_Emision)=' . $m : "");

		$query = "SELECT
VDBFND.Cantidad,
CLI.No_Entidad AS No_Razsocial,
MONE.No_Signo,
VDBFND.Cantidad AS Qt_Producto,
(COALESCE(VDBFND.Qt_Producto, 0) - COALESCE(VDNC.Qt_Producto, 0)) AS Qt_Producto_2,
(COALESCE(VDBFND.Vendido, 0) - COALESCE(VDNC.Vendido, 0)) AS Vendido
FROM
documento_cabecera AS VC
JOIN entidad AS CLI ON(VC.ID_Entidad = CLI.ID_Entidad)
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
LEFT JOIN (
SELECT
VC.ID_Entidad,
" . $column_tipo_producto . "
COUNT(VC.ID_Documento_Cabecera) AS Cantidad,
SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
YEAR(VC.Fe_Emision) = " . $y . "
" . $where_mes . "
AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,6)
" . $where_id_moneda . "  
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Tipo_Asiento = 1
" . $cond_tipo_producto . "
GROUP BY
1" . $group_by . "
) AS VDBFND ON (VDBFND.ID_Entidad = VC.ID_Entidad)
LEFT JOIN (
SELECT
VC.ID_Entidad,
" . $column_tipo_producto . "
SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
YEAR(VC.Fe_Emision) = " . $y . "
" . $where_mes . "
AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento=5
" . $where_id_moneda . "  
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Tipo_Asiento = 1
" . $cond_tipo_producto . "
GROUP BY
1" . $group_by . "
) AS VDNC ON (VDNC.ID_Entidad = VC.ID_Entidad)
WHERE
YEAR(VC.Fe_Emision) = $y
" . ($m > 0 ? " AND MONTH(VC.Fe_Emision) = $m" : "") . "
AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
GROUP BY
CLI.ID_Entidad,
MONE.ID_Moneda
" . $order_by;

		return $this->db->query($query)->result();
	}
	
	public function ProductosMasVendidos($m, $y, $ID_Moneda, $Nu_Tipo_Producto, $iOrder, $iImpuesto){
		$column_tipo_producto = "";
		$cond_tipo_producto = "";
		$group_by = "";
		
		$where_id_moneda = ( !empty($ID_Moneda) ? "AND VC.ID_Moneda = " . $ID_Moneda : '' );

		if($Nu_Tipo_Producto == '0' || $Nu_Tipo_Producto == '1'){
			$column_tipo_producto = "ITEM.Nu_Tipo_Producto,";
			$cond_tipo_producto = "AND ITEM.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$group_by = ", 3";
		}
		
		$order_by = " ORDER BY Vendido DESC;";
		if( $iOrder == 2 )
			$order_by = " ORDER BY Qt_Producto DESC;";
	
        $column_total = "VD.Ss_SubTotal";
		if($iImpuesto == '1')//Si
            $column_total = "VD.Ss_Total";
		
        //filtro por un mes o todos
        $where_mes = ($m > 0 ? ' AND MONTH(VC.Fe_Emision)=' . $m : "");

		$query = "SELECT
MONE.No_Signo,
VD.ID_Producto,
" . $column_tipo_producto . "
M.No_Marca,
UM.No_Unidad_Medida_Breve,
ITEM.No_Producto,
(COALESCE(VDBFND.Qt_Producto, 0) - COALESCE(VDNC.Qt_Producto, 0)) AS Qt_Producto,
(COALESCE(VDBFND.Vendido, 0) - COALESCE(VDNC.Vendido, 0)) AS Vendido
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
LEFT JOIN marca AS M ON(ITEM.ID_Marca = M.ID_Marca)
LEFT JOIN (
SELECT
VD.ID_Producto,
SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
YEAR(VC.Fe_Emision) = " . $y . "
" . $where_mes . "
AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,6)
" . $where_id_moneda . "  
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Tipo_Asiento = 1
" . $cond_tipo_producto . "
GROUP BY
1
) AS VDBFND ON(VDBFND.ID_Producto = VD.ID_Producto)
LEFT JOIN (
SELECT
VD.ID_Producto,
SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
YEAR(VC.Fe_Emision) = " . $y . "
" . $where_mes . "
AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento=5
" . $where_id_moneda . "  
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Tipo_Asiento = 1
" . $cond_tipo_producto . "
GROUP BY
1
) AS VDNC ON(VDNC.ID_Producto = VD.ID_Producto)
WHERE
YEAR(VC.Fe_Emision) = " . $y . "
" . ($m > 0 ? " AND MONTH(VC.Fe_Emision) = $m" : "") . "
AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
" . $where_id_moneda . "
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
" . $cond_tipo_producto . "
GROUP BY
1,
2" . $group_by . $order_by;
 
		return $this->db->query($query)->result();
	}
	
	public function ProductosRentablesPorTrimestre($year, $ID_Moneda, $Nu_Tipo_Producto, $iOrder, $iImpuesto){
		$estaciones = array('1er Trimestre' => array(), '2do Trimestre' => array(), '3er Trimestre' => array(), '4to Trimestre' => array());
		
		$column_tipo_producto = "";
		$cond_tipo_producto = "";
		$cond_tipo_productoV = "";
		$group_by_tipo_producto = "";
		
		if($Nu_Tipo_Producto == '0' || $Nu_Tipo_Producto == '1'){
			$column_tipo_producto = "ITEM.Nu_Tipo_Producto,";
			$cond_tipo_producto = "AND ITEM.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$cond_tipo_productoV = "AND ITEM2.Nu_Tipo_Producto = " . $Nu_Tipo_Producto;
			$group_by_tipo_producto = ", 3";
		}

		$order_by = " ORDER BY Vendido DESC;";
		if( $iOrder == 2 )
			$order_by = " ORDER BY Qt_Producto DESC;";
	
        $column_total = "VD.Ss_SubTotal";
		if($iImpuesto == '1')//Si
            $column_total = "VD.Ss_Total";

		$sql = "SELECT
 M.No_Marca,
 UM.No_Unidad_Medida_Breve,
 " . $column_tipo_producto . "
 ITEM.No_Producto,
 MONE.No_Signo,
 (COALESCE(VDBFND.Qt_Producto, 0) - COALESCE(VDNC.Qt_Producto, 0)) AS Qt_Producto,
 (COALESCE(VDBFND.Vendido, 0) - COALESCE(VDNC.Vendido, 0)) AS Vendido
FROM
 documento_cabecera AS VC
 JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
 JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
 JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
 JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
 LEFT JOIN marca AS M ON(ITEM.ID_Marca = M.ID_Marca)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento IN(2,3,4,6)
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 1 AND 3
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 3))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento IN(2,3,4,6)
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 1 AND 3
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 3))
 GROUP BY
  1
 ) AS VDBFND ON(VDBFND.ID_Producto = VD.ID_Producto)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento = 5
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 1 AND 3
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 3))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento=5
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 1 AND 3
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 3))
 GROUP BY
  1
 ) AS VDNC ON(VDNC.ID_Producto = VD.ID_Producto)
WHERE
 (COALESCE(VDBFND.Qt_Producto, 0) - COALESCE(VDNC.Qt_Producto, 0)) > 0
 AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
 AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
 AND VC.ID_Tipo_Asiento = 1
 AND VC.ID_Tipo_Documento IN(2,3,4,6)
 AND VC.Nu_Estado IN(6,8)
 AND YEAR(VC.Fe_Emision) = " . $year . "
 AND MONTH(VC.Fe_Emision) BETWEEN 1 AND 3
 AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 3))
 " . $cond_tipo_producto . "
GROUP BY
 MONE.ID_Moneda,
 ITEM.ID_Producto" . $group_by_tipo_producto . $order_by;
 
		$estaciones['1er Trimestre'] = $this->db->query($sql)->result();
		
		$sql = "
SELECT
 M.No_Marca,
 UM.No_Unidad_Medida_Breve,
 " . $column_tipo_producto . "
 ITEM.No_Producto,
 MONE.No_Signo,
 SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
 SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
FROM
 documento_cabecera AS VC
 JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
 JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
 JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
 JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
 LEFT JOIN marca AS M ON(ITEM.ID_Marca = M.ID_Marca)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento IN(2,3,4,6)
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 4 AND 6
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 6))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento IN(2,3,4,6)
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 4 AND 6
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 6))
 GROUP BY
  1
 ) AS VDBFND ON(VDBFND.ID_Producto = VD.ID_Producto)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento = 5
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 4 AND 6
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 6))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento=5
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 4 AND 6
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 6))
 GROUP BY
  1
 ) AS VDNC ON(VDNC.ID_Producto = VD.ID_Producto)
WHERE
 (COALESCE(VDBFND.Qt_Producto, 0) - COALESCE(VDNC.Qt_Producto, 0)) > 0
 AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
 AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
 AND VC.ID_Tipo_Asiento = 1
 AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
 AND VC.Nu_Estado IN(6,8)
 AND YEAR(VC.Fe_Emision) = " . $year . "
 AND MONTH(VC.Fe_Emision) BETWEEN 4 AND 6
 AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 6))
 " . $cond_tipo_producto . "
GROUP BY
 MONE.ID_Moneda,
 ITEM.ID_Producto" . $group_by_tipo_producto . $order_by;
		
		$estaciones['2do Trimestre'] = $this->db->query($sql)->result();
		
		$sql = "
SELECT
 M.No_Marca,
 UM.No_Unidad_Medida_Breve,
 " . $column_tipo_producto . "
 ITEM.No_Producto,
 MONE.No_Signo,
 SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
 SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
FROM
 documento_cabecera AS VC
 JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
 JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
 JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
 JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
 LEFT JOIN marca AS M ON(ITEM.ID_Marca = M.ID_Marca)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento IN(2,3,4,6)
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 7 AND 9
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 9))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento IN(2,3,4,6)
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 7 AND 9
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 9))
 GROUP BY
  1
 ) AS VDBFND ON(VDBFND.ID_Producto = VD.ID_Producto)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento = 5
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 7 AND 9
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 9))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento=5
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 7 AND 9
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 9))
 GROUP BY
  1
 ) AS VDNC ON(VDNC.ID_Producto = VD.ID_Producto)
WHERE
 (COALESCE(VDBFND.Qt_Producto, 0) - COALESCE(VDNC.Qt_Producto, 0)) > 0
 AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
 AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
 AND VC.ID_Tipo_Asiento = 1
 AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
 AND VC.Nu_Estado IN(6,8)
 AND YEAR(VC.Fe_Emision) = " . $year . "
 AND MONTH(VC.Fe_Emision) BETWEEN 7 AND 9
 AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) > 9))
 " . $cond_tipo_producto . "
GROUP BY
 MONE.ID_Moneda,
 ITEM.ID_Producto" . $group_by_tipo_producto . $order_by;
		
		$estaciones['3er Trimestre'] = $this->db->query($sql)->result();
		
		$sql = "
SELECT
 M.No_Marca,
 UM.No_Unidad_Medida_Breve,
 " . $column_tipo_producto . "
 ITEM.No_Producto,
 MONE.No_Signo,
 SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
 SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
FROM
 documento_cabecera AS VC
 JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
 JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
 JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
 JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
 LEFT JOIN marca AS M ON(ITEM.ID_Marca = M.ID_Marca)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento IN(2,3,4,6)
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 10 AND 12
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) = 12 AND DAY(CURDATE()) = 31))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento IN(2,3,4,6)
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 10 AND 12
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) = 12 AND DAY(CURDATE()) = 31))
 GROUP BY
  1
 ) AS VDBFND ON(VDBFND.ID_Producto = VD.ID_Producto)
 LEFT JOIN (
 SELECT
  VD.ID_Producto,
  SUM(COALESCE(VD.Qt_Producto, 0)) AS Qt_Producto,
  SUM(COALESCE(" . $column_total . ", 0)) AS Vendido
 FROM
  documento_cabecera AS VC
  JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
  JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
  JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
  JOIN unidad_medida AS UM ON(ITEM.ID_Unidad_Medida = UM.ID_Unidad_Medida)
  JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
  JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
 WHERE
  VD.Qt_Producto >= (
  SELECT
   AVG(COALESCE(VD2.Qt_Producto, 0))
  FROM
   documento_cabecera VC2
   JOIN documento_detalle AS VD2 ON(VC2.ID_Documento_Cabecera = VD2.ID_Documento_Cabecera)
   JOIN producto AS ITEM2 ON(ITEM2.ID_Producto = VD2.ID_Producto)
   JOIN unidad_medida AS UM2 ON(UM2.ID_Unidad_Medida = ITEM2.ID_Unidad_Medida)
   JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD2.ID_Impuesto_Cruce_Documento)
   JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
  WHERE
   UM2.ID_Unidad_Medida = UM.ID_Unidad_Medida
   AND VC2.ID_Empresa = " . $this->user->ID_Empresa . "
   AND VC2.ID_Organizacion = " . $this->user->ID_Organizacion . "
   AND VC2.ID_Tipo_Asiento = 1
   AND VC2.ID_Tipo_Documento = 5
   AND IMP.Nu_Tipo_Impuesto!=4
   AND VC2.Nu_Estado IN(6,8)
   AND YEAR(VC2.Fe_Emision) = " . $year . "
   AND MONTH(VC2.Fe_Emision) BETWEEN 10 AND 12
   AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) = 12 AND DAY(CURDATE()) = 31))
  )
  AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento=5
  AND IMP.Nu_Tipo_Impuesto!=4
  AND VC.Nu_Estado IN(6,8)
  AND YEAR(VC.Fe_Emision) = " . $year . "
  AND MONTH(VC.Fe_Emision) BETWEEN 10 AND 12
  AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) = 12 AND DAY(CURDATE()) = 31))
 GROUP BY
  1
 ) AS VDNC ON(VDNC.ID_Producto = VD.ID_Producto)
WHERE
 (COALESCE(VDBFND.Qt_Producto, 0) - COALESCE(VDNC.Qt_Producto, 0)) > 0
 AND VC.ID_Empresa = " . $this->user->ID_Empresa . "
 AND VC.ID_Organizacion=" . $this->user->ID_Organizacion . "
 AND VC.ID_Tipo_Asiento = 1
 AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
 AND VC.Nu_Estado IN(6,8)
 AND YEAR(VC.Fe_Emision) = " . $year . "
 AND MONTH(VC.Fe_Emision) BETWEEN 10 AND 12
 AND (YEAR(CURDATE()) > " . $year . " OR (MONTH(CURDATE()) = 12 AND DAY(CURDATE()) = 31))
 " . $cond_tipo_producto . "
GROUP BY
 MONE.ID_Moneda,
 ITEM.ID_Producto" . $group_by_tipo_producto . $order_by;
		
		$estaciones['4to Trimestre'] = $this->db->query($sql)->result();
		
		return (object)$estaciones;
	}
}
