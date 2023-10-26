<?php
class RegistroVentaIngresoModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
    
	public function getTiposLibroSunat($ID_Tipo_Asiento){
		$query = "SELECT * FROM asiento_libro_sunat_detalle WHERE ID_Tipo_Asiento = " . $ID_Tipo_Asiento;
		return $this->db->query($query)->result();
	}

    public function getReporteBG(){
        $query = "SELECT * FROM reporte  WHERE ID_Estatus IN (0) AND Nu_Tipo_Reporte=1 ORDER BY Fe_Creacion ASC LIMIT 1";
        $row = $this->db->query($query)->row();

       // if($row)
            return $row;
        // else
        //     exit();
    }

    public function getReporte(){
        $query = "SELECT
ID_Reporte,
DATE_FORMAT(Fe_Creacion, \"%d/%m/%Y %T\") Fe_Creacion,
IF(Txt_Nombre_Archivo IS NULL or Txt_Nombre_Archivo = '', 'Esperando...', Txt_Nombre_Archivo) Txt_Nombre_Archivo,
ID_Estatus,Nu_Tipo_Formato
FROM
`reporte`
WHERE
ID_Empresa = ".$this->user->ID_Empresa."
AND Nu_Tipo_Reporte=1
AND ID_Estatus IN(0,1,2)
ORDER BY ID_Reporte DESC";
        $row = $this->db->query($query)->result();
        return $row;
    }

    public function getReporteRow($ID_Reporte){
        $query = "SELECT * FROM `reporte` WHERE ID_Reporte=".$ID_Reporte." AND ID_Empresa = ".$this->user->ID_Empresa." AND Nu_Tipo_Reporte=1 AND ID_Estatus=2";
        $row = $this->db->query($query)->row();
        return $row;
    }

    public function CancelarReporte($ID_Reporte){
        $this->UpdateReporteBG(array("ID_Estatus"=>3),$ID_Reporte);
        return json_encode(array("sStatus"=>"success"));
    }

    public function CrearReporteVentas($valores){
        $data = array(
            "ID_Empresa"=>$this->user->ID_Empresa,
            'Txt_Parametro' => serialize($valores),
            'Nu_Tipo_Reporte' => 1,
            'ID_Estatus' => 0,
            "Nu_Tipo_Formato"=>$valores["Nu_Tipo_Formato"]
        );

        $this->db->insert('reporte', $data);
        return json_encode(array("sStatus"=>"success"));
    } 

    public function UpdateReporteBG($data,$ID_Reporte){

        $this->db->where('ID_Reporte', $ID_Reporte);
        $this->db->update('reporte', $data);
        //print_r($this->db->last_query());
    }
	
    public function registroVentasIngresos($arrParams){
        $ID_Organizacion = $arrParams['ID_Organizacion'];
        $ID_Tipo_Asiento = $arrParams['ID_Tipo_Asiento'];
        $fYear = $arrParams['fYear'];
        $fMonth = $arrParams['fMonth'];
        $where_id_organizacion = $ID_Organizacion == 0 ? '' : "AND VC.ID_Organizacion=" . $ID_Organizacion;

        $query = "SELECT
VC.Nu_Correlativo AS CUO,
VC.Fe_Emision,
TDOCU.Nu_Sunat_Codigo AS DOCU_Nu_Sunat_Codigo,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDOCUIDE.Nu_Sunat_Codigo AS IDE_Nu_Sunat_Codigo,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
ICDOCU.Ss_Impuesto,
SUM(
CASE WHEN IMP.Nu_Tipo_Impuesto = 1 AND VC.Ss_Total > 0.00 THEN
    (CASE WHEN MONE.Nu_Valor_FE = 1 THEN
        CASE WHEN VD.Ss_Descuento > 0 THEN
            VD.Ss_SubTotal
        ELSE
            VD.Ss_SubTotal - ((VD.Ss_SubTotal * COALESCE(VC.Po_Descuento, 0)) / 100)
        END
    ELSE
        CASE WHEN VD.Ss_Descuento > 0 THEN
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        ELSE
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2) - ROUND(((VD.Ss_SubTotal * VC.Po_Descuento) / 100) * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        END
    END)
ELSE
    0.00
END) AS Ss_SubTotal_Gravadas,
SUM(
CASE WHEN IMP.Nu_Tipo_Impuesto = 1 AND VC.Ss_Total > 0.00 THEN
    (CASE WHEN MONE.Nu_Valor_FE = 1 THEN
        CASE WHEN VD.Ss_Descuento > 0 THEN
            VD.Ss_Impuesto
        ELSE
            VD.Ss_Impuesto - ((VD.Ss_Impuesto * COALESCE(VC.Po_Descuento, 0)) / 100)
        END
    ELSE
        CASE WHEN VD.Ss_Descuento > 0 THEN
            ROUND(VD.Ss_Impuesto * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        ELSE
            ROUND(VD.Ss_Impuesto * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2) - ROUND(((VD.Ss_Impuesto * VC.Po_Descuento) / 100) * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        END
    END)
ELSE
    0.00
END) AS Ss_IGV_Gravadas,
SUM(
CASE WHEN IMP.Nu_Tipo_Impuesto = 2 AND VC.Ss_Total > 0.00 THEN
    (CASE WHEN MONE.Nu_Valor_FE = 1 THEN
        CASE WHEN VD.Ss_Descuento > 0 THEN
            VD.Ss_SubTotal
        ELSE
            VD.Ss_SubTotal - ((VD.Ss_SubTotal * VC.Po_Descuento) / 100)
        END
    ELSE
        CASE WHEN VD.Ss_Descuento > 0 THEN
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        ELSE
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2) - ROUND(((VD.Ss_SubTotal * VC.Po_Descuento) / 100) * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        END
    END)
ELSE
    0.00
END) AS Ss_Inafecta,
SUM(
CASE WHEN IMP.Nu_Tipo_Impuesto = 3 AND VC.Ss_Total > 0.00 THEN
    (CASE WHEN MONE.Nu_Valor_FE = 1 THEN
        CASE WHEN VD.Ss_Descuento > 0 THEN
            VD.Ss_SubTotal
        ELSE
            VD.Ss_SubTotal - ((VD.Ss_SubTotal * VC.Po_Descuento) / 100)
        END
    ELSE
        CASE WHEN VD.Ss_Descuento > 0 THEN
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        ELSE
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2) - ROUND(((VD.Ss_SubTotal * VC.Po_Descuento) / 100) * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        END
    END)
ELSE
    0.00
END) AS Ss_Exonerada,
SUM(
CASE WHEN IMP.Nu_Tipo_Impuesto = 4 AND VC.Ss_Total > 0.00 THEN
    (CASE WHEN MONE.Nu_Valor_FE = 1 THEN
        CASE WHEN VD.Ss_Descuento > 0 THEN
            VD.Ss_SubTotal
        ELSE
            VD.Ss_SubTotal - ((VD.Ss_SubTotal * VC.Po_Descuento) / 100)
        END
    ELSE
        CASE WHEN VD.Ss_Descuento > 0 THEN
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        ELSE
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2) - ROUND(((VD.Ss_SubTotal * VC.Po_Descuento) / 100) * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        END
    END)
ELSE
    0.00
END) AS Ss_Gratuita,
SUM(
CASE WHEN IMP.ID_Impuesto = 16 AND VC.Ss_Total > 0.00 THEN
    (CASE WHEN MONE.Nu_Valor_FE = 1 THEN
        CASE WHEN VD.Ss_Descuento > 0 THEN
            VD.Ss_SubTotal
        ELSE
            VD.Ss_SubTotal - ((VD.Ss_SubTotal * VC.Po_Descuento) / 100)
        END
    ELSE
        CASE WHEN VD.Ss_Descuento > 0 THEN
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        ELSE
            ROUND(VD.Ss_SubTotal * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2) - ROUND(((VD.Ss_SubTotal * VC.Po_Descuento) / 100) * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
        END
    END)
ELSE
    0.00
END) AS Ss_Exportacion,
SUM(
CASE WHEN MONE.Nu_Valor_FE = 1 THEN
    VD.Ss_Icbper
ELSE
    ROUND(VD.Ss_Icbper * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END), 2)
END) AS Ss_Icbper,
(CASE WHEN MONE.Nu_Valor_FE = 1 THEN VC.Ss_Total ELSE ROUND((VC.Ss_Total * (CASE WHEN VC.ID_Tipo_Documento != 5 && VC.ID_Tipo_Documento != 6 THEN TC.Ss_Venta_Oficial ELSE VE.Ss_Tipo_Cambio_Modificar END)), 2) END) AS Ss_Total,
MONE.Nu_Sunat_Codigo AS MONE_Nu_Sunat_Codigo,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio,
VE.Fe_Emision_Modificar AS Fe_Emision_Modificar,
VE.ID_Tipo_Documento_Modificar,
VE.ID_Serie_Documento_Modificar,
VE.ID_Numero_Documento_Modificar,
VE.Ss_Tipo_Cambio_Modificar,
MP.No_Codigo_Sunat_PLE,
SD.Nu_Cantidad_Caracteres,
TDOCU.No_Tipo_Documento,
VC.Nu_Estado,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto,
VC.Po_Descuento
FROM
documento_cabecera AS VC
LEFT JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN tipo_documento_identidad AS TDOCUIDE ON(TDOCUIDE.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
LEFT JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
LEFT JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
LEFT JOIN (
SELECT
VE.ID_Documento_Cabecera,
TDOCU.Nu_Sunat_Codigo AS ID_Tipo_Documento_Modificar,
VC.ID_Serie_Documento AS ID_Serie_Documento_Modificar,
VC.ID_Numero_Documento AS ID_Numero_Documento_Modificar,
VC.Fe_Emision AS Fe_Emision_Modificar,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio_Modificar
FROM
documento_cabecera AS VC
JOIN tipo_documento AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN documento_enlace AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera_Enlace)
JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
WHERE
VC.ID_Empresa=" . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento=1
AND VC.Nu_Estado=8
AND VC.ID_Tipo_Documento IN(3,4)
) AS VE ON(VC.ID_Documento_Cabecera=VE.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa=" . $this->user->ID_Empresa . "
" . $where_id_organizacion . "
AND VC.ID_Tipo_Asiento=1
AND YEAR(VC.Fe_Emision)=" . $fYear . "
AND MONTH(VC.Fe_Emision)=" . $fMonth . "
AND VC.Nu_Estado IN(8,10)
AND VC.ID_Tipo_Documento IN(3,4,5,6)
GROUP BY
VC.ID_Empresa,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Nu_Correlativo,
VC.Fe_Emision,
TDOCU.Nu_Sunat_Codigo,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDOCUIDE.Nu_Sunat_Codigo,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,MONE.Nu_Sunat_Codigo,
TC.Ss_Venta_Oficial,
VE.Fe_Emision_Modificar,
VE.ID_Tipo_Documento_Modificar,
VE.ID_Serie_Documento_Modificar,
VE.ID_Numero_Documento_Modificar,
VE.Ss_Tipo_Cambio_Modificar,
MP.No_Codigo_Sunat_PLE,
SD.Nu_Cantidad_Caracteres,
TDOCU.No_Tipo_Documento,
VC.Nu_Estado,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto,
VC.Po_Descuento
ORDER BY
VC.Fe_Emision ASC,
VC.ID_Tipo_Documento ASC,
VC.ID_Serie_Documento ASC,
CONVERT(VC.ID_Numero_Documento, SIGNED INTEGER) ASC;";
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
            'sMessage' => 'No se encontro registro',
        );
    }

    public function modificarCorrelativo($arrPost){
        $fYear = $arrPost['fYear'];
        $fMonth = $arrPost['fMonth'];
        $iOrdenar = $arrPost['iOrdenar'];

        $order_by_fecha = "
Fe_Emision ASC,
ID_Tipo_Documento ASC,
ID_Serie_Documento ASC,
CONVERT(ID_Numero_Documento, SIGNED INTEGER) ASC;";
        if ( $iOrdenar==1 )//1=Fecha de sistema
            $order_by_fecha = "Fe_Emision_Hora ASC";
        else if ( $iOrdenar==2 ) {//2=Fecha de periodo
            $order_by_fecha = "
Fe_Emision ASC,
ID_Serie_Documento ASC,
CONVERT(ID_Numero_Documento, SIGNED INTEGER) ASC";
        } else if ( $iOrdenar==3 ) {//Series
            $order_by_fecha = "
ID_Serie_Documento ASC,
CONVERT(ID_Numero_Documento, SIGNED INTEGER) ASC,
Fe_Emision ASC";
        }

        $query="SELECT
ID_Documento_Cabecera
FROM
documento_cabecera
WHERE
ID_Tipo_Asiento = 1
AND ID_Empresa = " . $this->user->ID_Empresa . "
AND ID_Tipo_Asiento=1
AND YEAR(Fe_Emision)=" . $fYear . "
AND MONTH(Fe_Emision)=" . $fMonth . "
AND Nu_Estado IN(6,7,8,10)
AND ID_Tipo_Documento IN(3,4,5,6)
ORDER BY " . $order_by_fecha;

        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al modificar correlativo',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }

        $arrResponseSQL = $this->db->query($query);
        if ( $arrResponseSQL->num_rows() > 0 ){
            $iCounter=1;
            foreach( $arrResponseSQL->result() as $row ){
                $arrUpdateCorrelativo[] = array(
                    'ID_Documento_Cabecera' => $row->ID_Documento_Cabecera,
                    'Nu_Correlativo' => $iCounter,
                );
                ++$iCounter;
            }

            $this->db->update_batch('documento_cabecera', $arrUpdateCorrelativo, 'ID_Documento_Cabecera');

            return array(
                'sStatus' => 'success',
                'sMessage' => 'Correlativo actualizado',
            );
        }
    }
}