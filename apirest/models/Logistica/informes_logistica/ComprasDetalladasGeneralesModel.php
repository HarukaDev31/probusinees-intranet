<?php
class ComprasDetalladasGeneralesModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Tipo_Documento=$arrParams['ID_Tipo_Documento'];
        $ID_Serie_Documento=$arrParams['ID_Serie_Documento'];
        $ID_Numero_Documento=$arrParams['ID_Numero_Documento'];
        $Nu_Estado_Documento=$arrParams['Nu_Estado_Documento'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];
        $iIdItem=$arrParams['iIdItem'];
        $sNombreItem=$arrParams['sNombreItem'];
        $iTipoVenta=$arrParams['iTipoVenta'];
        $ID_Almacen=$arrParams['ID_Almacen'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6,8,9,10,11)';
        $cond_serie = $ID_Serie_Documento != "-" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND VD.ID_Producto = ' . $iIdItem : "";

        $where_id_almacen = ($ID_Almacen > 0 ? 'AND ALMA.ID_Almacen = ' . $ID_Almacen : '');
//AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Documento_Cabecera,
VC.Fe_Emision_Hora,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDI.No_Tipo_Documento_Identidad_Breve,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
MONE.ID_Moneda,
MONE.No_Signo,
TC.Ss_Compra_Oficial AS Ss_Tipo_Cambio,
VE.Ss_Tipo_Cambio_Modificar,
MC.No_Marca,
F.No_Familia,
SF.No_Sub_Familia,
UM.No_Unidad_Medida,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
VD.Txt_Nota AS Txt_Nota_Item,
VD.Qt_Producto,
ITEM.Qt_CO2_Producto,
VD.Ss_Precio,
VD.Ss_Subtotal,
VD.Ss_Impuesto,
VD.Ss_Total,
VC.Nu_Estado,
VC.Txt_Glosa AS Txt_Nota
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
LEFT JOIN documento_detalle AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
LEFT JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
LEFT JOIN marca AS MC ON(MC.ID_Marca = ITEM.ID_Marca)
LEFT JOIN familia AS F ON(F.ID_Familia = ITEM.ID_Familia)
LEFT JOIN subfamilia AS SF ON(SF.ID_Sub_Familia = ITEM.ID_Sub_Familia)
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = ITEM.ID_Unidad_Medida)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN tasa_cambio AS TC ON(VC.ID_Empresa = TC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND VC.Fe_Emision = TC.Fe_Ingreso)
LEFT JOIN (SELECT
VE.ID_Documento_Cabecera,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio_Modificar
FROM
documento_cabecera AS VC
JOIN documento_enlace AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera_Enlace)
LEFT JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
) AS VE ON (VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 2
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_cliente . "
" . $cond_item;
        
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query)->result();


        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Guia_Cabecera AS ID_Documento_Cabecera,
CONCAT(VC.Fe_Emision, ' 00:00:00') AS Fe_Emision_Hora,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDI.No_Tipo_Documento_Identidad_Breve,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
MONE.ID_Moneda,
MONE.No_Signo,
TC.Ss_Compra_Oficial AS Ss_Tipo_Cambio,
VE.Ss_Tipo_Cambio_Modificar,
MC.No_Marca,
F.No_Familia,
SF.No_Sub_Familia,
UM.No_Unidad_Medida,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
VD.Txt_Nota AS Txt_Nota_Item,
VD.Qt_Producto,
ITEM.Qt_CO2_Producto,
VD.Ss_Precio,
VD.Ss_Subtotal,
VD.Ss_Impuesto,
VD.Ss_Total,
VC.Nu_Estado,
VC.Txt_Glosa AS Txt_Nota
FROM
guia_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
LEFT JOIN guia_detalle AS VD ON(VD.ID_Guia_Cabecera = VC.ID_Guia_Cabecera)
LEFT JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
LEFT JOIN marca AS MC ON(MC.ID_Marca = ITEM.ID_Marca)
LEFT JOIN familia AS F ON(F.ID_Familia = ITEM.ID_Familia)
LEFT JOIN subfamilia AS SF ON(SF.ID_Sub_Familia = ITEM.ID_Sub_Familia)
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = ITEM.ID_Unidad_Medida)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN tasa_cambio AS TC ON(VC.ID_Empresa = TC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND VC.Fe_Emision = TC.Fe_Ingreso)
LEFT JOIN (SELECT
VE.ID_Guia_Cabecera,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio_Modificar
FROM
guia_cabecera AS VC
JOIN guia_enlace AS VE ON(VE.ID_Guia_Cabecera = VC.ID_Guia_Cabecera)
LEFT JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
) AS VE ON (VC.ID_Guia_Cabecera = VE.ID_Guia_Cabecera)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_cliente . "
" . $cond_item;
        
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQLGuias = $this->db->query($query)->result();
        $arrData = array_merge($arrResponseSQL, $arrResponseSQLGuias);

        $orderID_Almacen = array();
        $orderFe_Emision_Hora = array();
        foreach ($arrData as $key => $row) {
            $orderID_Almacen[$key] = $row->ID_Almacen;
            $orderFe_Emision_Hora[$key] = $row->Fe_Emision_Hora;
        }
        array_multisort($orderID_Almacen, SORT_ASC, $orderFe_Emision_Hora, SORT_ASC, $arrData);

        if ( count($arrData) > 0 ){
            return array(
                'sStatus' => 'success',
                'arrData' => $arrData,
            );
        }
        
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No se encontro registro',
        );
    }
}
