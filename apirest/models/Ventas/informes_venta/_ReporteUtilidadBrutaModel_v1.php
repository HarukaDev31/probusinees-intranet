<?php
class ReporteUtilidadBrutaModel extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    public function getReporte($arrParams){
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Moneda=$arrParams['ID_Moneda'];
        $iIdFamilia=$arrParams['iIdFamilia'];
        $iIdItem=$arrParams['iIdItem'];
        $sNombreItem=$arrParams['sNombreItem'];
        $iIdSubFamilia=$arrParams['iIdSubFamilia'];
        $ID_Almacen=$arrParams['ID_Almacen'];
        $cond_familia = $iIdFamilia != "0" ? 'AND ITEM.ID_Familia = ' . $iIdFamilia : "";
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND VD.ID_Producto = ' . $iIdItem : "";
        $cond_sub_familia = $iIdSubFamilia != "0" ? 'AND ITEM.ID_Sub_Familia = ' . $iIdSubFamilia : "";

        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');
        
        $campo_impuesto = 'SUM(ICDOCU.Ss_Impuesto)';
        if($arrParams['Nu_Impuesto']==1)//1=si mostrar impuesto
            $campo_impuesto = '1';

        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
F.ID_Familia,
F.No_Familia,
VDT.ID_Producto,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
MONE.ID_Moneda,
MONE.No_Signo,
VD.Ss_Precio,
ITEM.Ss_Costo,
COALESCE(VD.cantidad_total_bfnd, 0) - COALESCE(VD.cantidad_total_nc, 0) AS Qt_Producto
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VDT ON(VC.ID_Documento_Cabecera = VDT.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VDT.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VDT.ID_Producto)
JOIN familia AS F ON(F.ID_Familia = ITEM.ID_Familia)
JOIN (
SELECT
VD.ID_Producto,
((SUM(VD.Ss_Precio) / CASE WHEN VC.ID_Tipo_Documento != 2 THEN " . $campo_impuesto . " ELSE 1 END) / COUNT(*)) AS Ss_Precio,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN VD.Qt_Producto END)) AS cantidad_total_bfnd,
SUM((CASE WHEN VC.ID_Tipo_Documento = 5 THEN VD.Qt_Producto END)) AS cantidad_total_nc
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Moneda = " . $ID_Moneda . "
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_familia . "
" . $cond_item . "
" . $cond_sub_familia . "
GROUP BY
1
) AS VD ON(VDT.ID_Producto = VD.ID_Producto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Moneda = " . $ID_Moneda . "
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_familia . "
" . $cond_item . "
" . $cond_sub_familia . "
GROUP BY
ALMA.ID_Almacen,
ALMA.No_Almacen,
F.ID_Familia,
F.No_Familia,
VDT.ID_Producto,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
MONE.ID_Moneda,
MONE.No_Signo,
VD.Ss_Precio,
ITEM.Ss_Costo
ORDER BY
ALMA.ID_Almacen,
ALMA.No_Almacen,
F.ID_Familia DESC,
F.No_Familia DESC,
ITEM.ID_Producto DESC,
ITEM.No_Producto DESC;";
//array_debug($query);

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
            'sMessage' => 'No hay registros',
        );
    }
	
	public function getDescuentoDetalle($arrParams){
		$query = "SELECT SUM(VD.Ss_Descuento) AS Ss_Descuento, SUM(VD.Ss_Descuento_Impuesto) AS Ss_Descuento_Impuesto FROM documento_cabecera AS VC JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera) WHERE
        VC.ID_Almacen = " . $arrParams['ID_Almacen'] . "
        AND VC.ID_Moneda = " . $arrParams['ID_Moneda'] . "
        AND VC.Fe_Emision BETWEEN '" . $arrParams['Fe_Inicio'] . "' AND '" . $arrParams['Fe_Fin'] . "'
        AND VD.ID_Producto = " . $arrParams['ID_Producto'];
		return $this->db->query($query)->row();
	}
	
	public function getDescuentoCabecera($arrParams){
		$query = "SELECT VC.Ss_Descuento, VC.Ss_Descuento_Impuesto FROM documento_cabecera AS VC JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera) WHERE
        VC.ID_Almacen = " . $arrParams['ID_Almacen'] . "
        AND VC.ID_Moneda = " . $arrParams['ID_Moneda'] . "
        AND VC.Fe_Emision BETWEEN '" . $arrParams['Fe_Inicio'] . "' AND '" . $arrParams['Fe_Fin'] . "'
        AND VD.ID_Producto = " . $arrParams['ID_Producto'];
		return $this->db->query($query)->row();
	}
}
