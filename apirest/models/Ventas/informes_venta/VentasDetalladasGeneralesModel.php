<?php
class VentasDetalladasGeneralesModel extends CI_Model{
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
        $ID_Familia=$arrParams['ID_Familia'];
        $ID_Sub_Familia=$arrParams['ID_Sub_Familia'];
        $ID_Marca=$arrParams['ID_Marca'];
        $Nu_Tipo_Recepcion=$arrParams['Nu_Tipo_Recepcion'];
        $Nu_Estado_Despacho_Pos=$arrParams['Nu_Estado_Despacho_Pos'];
        $ID_Transporte_Delivery=$arrParams['ID_Transporte_Delivery'];
        $ID_Lista_Precio_Cabecera=$arrParams['ID_Lista_Precio_Cabecera'];
        $ID_Canal_Venta_Tabla_Dato=$arrParams['ID_Canal_Venta_Tabla_Dato'];
        $Nu_Tipo_Impuesto=$arrParams['Nu_Tipo_Impuesto'];
        $ID_Almacen=$arrParams['ID_Almacen'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND VC.ID_Entidad = ' . $iIdCliente : "";
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND VD.ID_Producto = ' . $iIdItem : "";

        $cond_familia = $ID_Familia != "0" ? 'AND ITEM.ID_Familia = ' . $ID_Familia : "";
        $cond_sub_familia = $ID_Sub_Familia != "0" ? 'AND ITEM.ID_Sub_Familia = ' . $ID_Sub_Familia : "";
        $cond_marca = $ID_Marca != "0" ? 'AND ITEM.ID_Marca = ' . $ID_Marca : "";

        $cond_tipo_recepcion = $Nu_Tipo_Recepcion != "0" ? 'AND VC.Nu_Tipo_Recepcion = ' . $Nu_Tipo_Recepcion : "";
        $cond_estado_despacho_pos = $Nu_Estado_Despacho_Pos != "-" ? 'AND VC.Nu_Estado_Despacho_Pos = ' . $Nu_Estado_Despacho_Pos : "";
        $cond_delivery = $ID_Transporte_Delivery != "0" ? 'AND VC.ID_Transporte_Delivery = ' . $ID_Transporte_Delivery : "";

        $cond_lista_precio_cabecera = $ID_Lista_Precio_Cabecera != "0" ? 'AND VC.ID_Lista_Precio_Cabecera = ' . $ID_Lista_Precio_Cabecera : "";
        
        $cond_canal_venta = $ID_Canal_Venta_Tabla_Dato != "0" ? 'AND VC.ID_Canal_Venta_Tabla_Dato = ' . $ID_Canal_Venta_Tabla_Dato : "";

        $cond_tipo_venta = '';
        if ( $iTipoVenta == 1 )
            $cond_tipo_venta = 'AND SD.ID_POS IS NULL';
        else if ( $iTipoVenta == 2 )
            $cond_tipo_venta = 'AND SD.ID_POS > 0';
            
        $where_id_almacen = ($ID_Almacen > 0 ? 'AND ALMA.ID_Almacen = ' . $ID_Almacen : '');

        $where_gratuita = '';
        if ( $Nu_Tipo_Impuesto == 1 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto=4';
        else if ( $Nu_Tipo_Impuesto == 2 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto!=4';

//AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
         $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Documento_Cabecera,
VC.Fe_Emision,
VC.Fe_Emision_Hora,
EMPLE.No_Entidad AS No_Empleado,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
TDI.No_Tipo_Documento_Identidad_Breve,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
MONE.ID_Moneda,
MONE.No_Signo,
'0.00' AS Ss_Tipo_Cambio,
'0.00' AS Ss_Tipo_Cambio_Modificar,
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
VC.Txt_Glosa AS Txt_Nota,
VC.Nu_Tipo_Recepcion,
DELI.No_Entidad AS No_Delivery,
VC.Fe_Entrega,
VC.Nu_Estado_Despacho_Pos,
LPC.No_Lista_Precio,
VC.ID_Canal_Venta_Tabla_Dato,
VC.Txt_Garantia,
VC.No_Orden_Compra_FE,
VC.No_Placa_FE,
D.No_Departamento,
P.No_Provincia,
DTR.No_Distrito,
CLI.Nu_Celular_Entidad,
CLI.Txt_Email_Entidad,
CLI.Txt_Direccion_Entidad,
IMP.Nu_Tipo_Impuesto,
(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto) AS Ss_Descuento_Producto,
(VC.Ss_Descuento + VC.Ss_Descuento_Impuesto) AS Ss_Descuento_Global,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN documento_detalle AS VD ON(VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
LEFT JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
LEFT JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
LEFT JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto)
LEFT JOIN marca AS MC ON(MC.ID_Marca = ITEM.ID_Marca)
LEFT JOIN familia AS F ON(F.ID_Familia = ITEM.ID_Familia)
LEFT JOIN subfamilia AS SF ON(SF.ID_Sub_Familia = ITEM.ID_Sub_Familia)
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = ITEM.ID_Unidad_Medida)
LEFT JOIN distrito AS DTR ON(DTR.ID_Distrito = CLI.ID_Distrito)
LEFT JOIN provincia AS P ON(P.ID_Provincia = CLI.ID_Provincia)
LEFT JOIN departamento AS D ON(D.ID_Departamento = CLI.ID_Departamento)
LEFT JOIN matricula_empleado AS MEMPLE ON(VC.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
LEFT JOIN entidad AS EMPLE ON(MEMPLE.ID_Entidad = EMPLE.ID_Entidad)
LEFT JOIN entidad AS DELI ON(DELI.ID_Entidad = VC.ID_Transporte_Delivery)
LEFT JOIN lista_precio_cabecera AS LPC ON(LPC.ID_Lista_Precio_Cabecera = VC.ID_Lista_Precio_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_tipo_venta . "
" . $cond_cliente . "
" . $cond_item . "
" . $cond_familia . "
" . $cond_sub_familia . "
" . $cond_marca . "
" . $cond_tipo_recepcion . "
" . $cond_estado_despacho_pos . "
" . $cond_delivery . "
" . $cond_lista_precio_cabecera . "
" . $cond_canal_venta . "
" . $where_gratuita . "
ORDER BY
ALMA.ID_Almacen DESC,
VC.ID_Documento_Cabecera DESC;";
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

     public function CrearReporte($valores){
        $data = array(
            "ID_Empresa"=>$this->user->ID_Empresa,
            'Txt_Parametro' => serialize($valores),
            'Nu_Tipo_Reporte' => 4,
            'ID_Estatus' => 0,
            "Nu_Tipo_Formato"=>$valores["Nu_Tipo_Formato"]
        );

        $this->db->insert('reporte', $data);
        return json_encode(array("sStatus"=>"success"));
    } 

    public function getReporteBG(){
        $query = "SELECT * FROM reporte  WHERE ID_Estatus IN (0) AND Nu_Tipo_Reporte=4 ORDER BY Fe_Creacion ASC LIMIT 1";
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
                    AND Nu_Tipo_Reporte=4
                    AND ID_Estatus IN(0,1,2)
                    ORDER BY ID_Reporte DESC";
        $row = $this->db->query($query)->result();
        return $row;
    }

    public function getReporteRow($ID_Reporte){
        $query = "SELECT * FROM `reporte` WHERE ID_Reporte=".$ID_Reporte." AND ID_Empresa = ".$this->user->ID_Empresa." AND Nu_Tipo_Reporte=4 AND ID_Estatus=2";
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
