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
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND ITEM.ID_Producto = ' . $iIdItem : "";
        $cond_sub_familia = $iIdSubFamilia != "0" ? 'AND ITEM.ID_Sub_Familia = ' . $iIdSubFamilia : "";

        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');
        
        $query = "SELECT
VC.ID_Empresa,
VC.ID_Organizacion,
ALMA.ID_Almacen,
ALMA.No_Almacen,
F.ID_Familia,
F.No_Familia,
VDT.ID_Producto,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
MONE.ID_Moneda,
MONE.No_Signo,
ITEM.Ss_Precio,
ITEM.Ss_Costo,
0 AS Qt_Producto,
SUM(VC.Ss_Descuento) AS Ss_Descuento_Cabecera,
SUM(VC.Ss_Descuento_Impuesto) AS Ss_Descuento_Impuesto_Cabecera
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VDT ON(VC.ID_Documento_Cabecera = VDT.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VDT.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN producto AS ITEM ON(ITEM.ID_Producto = VDT.ID_Producto)
JOIN familia AS F ON(F.ID_Familia = ITEM.ID_Familia)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento != 1
AND VC.Nu_Estado IN(6,8)
AND IMP.Nu_Tipo_Impuesto!=4
AND VC.ID_Moneda = " . $ID_Moneda . "
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_familia . "
" . $cond_item . "
" . $cond_sub_familia . "
GROUP BY
VC.ID_Empresa,
VC.ID_Organizacion,
ALMA.ID_Almacen,
ALMA.No_Almacen,
F.ID_Familia,
F.No_Familia,
VDT.ID_Producto,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
MONE.ID_Moneda,
MONE.No_Signo,
ITEM.Ss_Precio,
ITEM.Ss_Costo
ORDER BY
ALMA.ID_Almacen,
F.ID_Familia DESC,
ITEM.ID_Producto DESC;";
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
        VC.ID_Empresa = " . $arrParams['ID_Empresa'] . "
        AND VC.ID_Organizacion = " . $arrParams['ID_Organizacion'] . "
        AND VC.ID_Almacen = " . $arrParams['ID_Almacen'] . "
        AND VC.ID_Tipo_Asiento = 1
        AND VC.ID_Tipo_Documento != 1
        AND VC.Nu_Estado IN(6,8)
        AND VC.ID_Moneda = " . $arrParams['ID_Moneda'] . "
        AND VC.Fe_Emision BETWEEN '" . $arrParams['Fe_Inicio'] . "' AND '" . $arrParams['Fe_Fin'] . "'
        AND VD.ID_Producto = " . $arrParams['ID_Producto'];
		return $this->db->query($query)->row();
	}
	
	public function getDescuentoCabecera($arrParams){
		$query = "SELECT VC.Ss_Descuento, VC.Ss_Descuento_Impuesto FROM documento_cabecera AS VC JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera) WHERE
        VC.ID_Empresa = " . $arrParams['ID_Empresa'] . "
        AND VC.ID_Tipo_Asiento = 1
        AND VC.ID_Tipo_Documento != 1
        AND VC.Nu_Estado IN(6,8)
        AND VC.ID_Moneda = " . $arrParams['ID_Moneda'] . "
        AND VC.Fe_Emision BETWEEN '" . $arrParams['Fe_Inicio'] . "' AND '" . $arrParams['Fe_Fin'] . "'
        AND VD.ID_Producto = " . $arrParams['ID_Producto'];
		return $this->db->query($query)->row();
	}
	
	public function obtenerPrecioCantidadVentaDetalle($arrParams){
        $campo_impuesto = 'ICDOCU.Ss_Impuesto';
        $table_impuesto = 'JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)';
        if($arrParams['Nu_Impuesto']==1){//1=si mostrar impuesto
            $campo_impuesto = '1 AS Ss_Impuesto';
            $table_impuesto = '';
        }

		$query = "SELECT
VC.ID_Tipo_Documento,
VD.Ss_Precio,
VD.Qt_Producto,
" . $campo_impuesto . "
FROM
documento_cabecera AS VC
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
" . $table_impuesto . "
WHERE
        VC.ID_Empresa = " . $arrParams['ID_Empresa'] . "
        AND VC.ID_Organizacion = " . $arrParams['ID_Organizacion'] . "
        AND VC.ID_Almacen = " . $arrParams['ID_Almacen'] . "
        AND VC.ID_Tipo_Asiento = 1
        AND VC.ID_Tipo_Documento != 1
        AND VC.Nu_Estado IN(6,8)
        AND VC.ID_Moneda = " . $arrParams['ID_Moneda'] . "
        AND VC.Fe_Emision BETWEEN '" . $arrParams['Fe_Inicio'] . "' AND '" . $arrParams['Fe_Fin'] . "'
        AND VD.ID_Producto = " . $arrParams['ID_Producto'];
		
        $arrResponseSQL = $this->db->query($query);
        if ( $arrResponseSQL->num_rows() > 0 ){
            $arrData = $arrResponseSQL->result();
            
            $fPrecio = 0.00;
            $fCantidadVenta = 0.00;
            $fImpuesto = 0.00;
            $iCounter = 0;
            foreach($arrData as $row){
                $fPrecio += ($row->Ss_Precio / ($row->ID_Tipo_Documento != 2 ? $row->Ss_Impuesto : 1));
                $fCantidadVenta += ($row->ID_Tipo_Documento != 5 ? $row->Qt_Producto : -$row->Qt_Producto);
                ++$iCounter;
            }
            
            return array(
                "status" => "success",
                "precio_promedio_venta" => ($fPrecio / $iCounter),
                "cantidad_venta" => $fCantidadVenta
            );
        }
            
        return array(
            "status" => "warning"
        );
	}

     public function CrearReporte($valores){
        $data = array(
            "ID_Empresa"=>$this->user->ID_Empresa,
            'Txt_Parametro' => serialize($valores),
            'Nu_Tipo_Reporte' => 3,
            'ID_Estatus' => 0,
            "Nu_Tipo_Formato"=>$valores["Nu_Tipo_Formato"]
        );

        $this->db->insert('reporte', $data);
        return json_encode(array("sStatus"=>"success"));
    } 

    public function getReporteBG(){
        $query = "SELECT * FROM reporte  WHERE ID_Estatus IN (0) AND Nu_Tipo_Reporte=3 ORDER BY Fe_Creacion ASC LIMIT 1";
        $row = $this->db->query($query)->row();

       // if($row)
            return $row;
        // else
        //     exit();
    }

    public function getReporte_(){
        $query = "SELECT
                    ID_Reporte,
                    DATE_FORMAT(Fe_Creacion, \"%d/%m/%Y %T\") Fe_Creacion,
                    IF(Txt_Nombre_Archivo IS NULL or Txt_Nombre_Archivo = '', 'Esperando...', Txt_Nombre_Archivo) Txt_Nombre_Archivo,
                    ID_Estatus,Nu_Tipo_Formato
                    FROM
                    `reporte`
                    WHERE
                    ID_Empresa = ".$this->user->ID_Empresa."
                    AND Nu_Tipo_Reporte=3
                    AND ID_Estatus IN(0,1,2)
                    ORDER BY ID_Reporte DESC";
        $row = $this->db->query($query)->result();
        return $row;
    }

    public function getReporteRow($ID_Reporte){
        $query = "SELECT * FROM `reporte` WHERE ID_Reporte=".$ID_Reporte." AND ID_Empresa = ".$this->user->ID_Empresa." AND Nu_Tipo_Reporte=3 AND ID_Estatus=2";
        $row = $this->db->query($query)->row();
        return $row;
    }

    public function CancelarReporte($ID_Reporte){
        $this->UpdateReporteBG(array("ID_Estatus"=>3),$ID_Reporte);
        return json_encode(array("sStatus"=>"success"));
    }

    public function UpdateReporteBG($data,$ID_Reporte){

        $this->db->where('ID_Reporte', $ID_Reporte);
        $this->db->update('reporte', $data);
        //print_r($this->db->last_query());
    }
}
