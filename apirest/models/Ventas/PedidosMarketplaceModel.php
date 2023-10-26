<?php
class PedidosMarketplaceModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Tipo_Documento=$arrParams['ID_Tipo_Documento'];
        $ID_Pedido_Cabecera=$arrParams['ID_Pedido_Cabecera'];
        $Nu_Estado_Pedido=$arrParams['Nu_Estado_Pedido'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND PC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : '';
        $cond_numero = $ID_Pedido_Cabecera != "-" ? "AND PC.ID_Pedido_Cabecera = '" . $ID_Pedido_Cabecera . "'" : "";
        $cond_estado_pedido = $Nu_Estado_Pedido != "0" ? 'AND PC.Nu_Estado = ' . $Nu_Estado_Pedido : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";

        $query = "SELECT
 PC.Fe_Emision_Hora,
 TD.No_Tipo_Documento_Breve,
 PC.ID_Tipo_Documento,
 PC.ID_Pedido_Cabecera,
 TDI.No_Tipo_Documento_Identidad_Breve,
 CLI.Nu_Documento_Identidad,
 CLI.No_Entidad,
 PC.Ss_Total,
 TDRECEP.No_Descripcion AS No_Estado_Recepcion,
 TDRECEP.No_Class AS No_Class_Estado_Recepcion,
 TDESTADOPEDIDO.No_Descripcion AS No_Estado,
 TDESTADOPEDIDO.No_Class AS No_Class_Estado
FROM
 pedido_cabecera AS PC
 JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = PC.ID_Tipo_Documento)
 JOIN entidad AS CLI ON(CLI.ID_Entidad = PC.ID_Entidad)
 JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
 JOIN tabla_dato AS TDRECEP ON(TDRECEP.Nu_Valor = PC.Nu_Tipo_Recepcion AND TDRECEP.No_Relacion = 'Tipos_Recepcion')
 JOIN tabla_dato AS TDESTADOPEDIDO ON(TDESTADOPEDIDO.Nu_Valor = PC.Nu_Estado AND TDESTADOPEDIDO.No_Relacion = 'Tipos_Estado_Orden_Pedido')
WHERE
 PC.ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND PC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
 " . $cond_tipo . "
 " . $cond_numero . "
 " . $cond_estado_pedido . "
 " . $cond_cliente . "
ORDER BY
 PC.ID_Pedido_Cabecera DESC;";
        
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

    public function verPedido($iIdPedido){
        $query = "SELECT
 PC.Fe_Emision_Hora,
 TD.No_Tipo_Documento_Breve,
 PC.ID_Pedido_Cabecera,
 TDI.No_Tipo_Documento_Identidad_Breve,
 CLI.Nu_Documento_Identidad,
 PC.No_Entidad_Order_Address_Entry AS No_Entidad,
 CLI.Nu_Celular_Entidad AS Nu_Celular_Entidad,
 CLI.Txt_Email_Entidad,
 PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular_Referencia,
 TDRECEP.No_Descripcion AS No_Estado_Recepcion,
 TDRECEP.No_Class AS No_Class_Estado_Recepcion,
 PC.Nu_Tipo_Recepcion,
 DISTRI.No_Distrito,
 PC.Txt_Direccion_Entidad_Order_Address_Entry AS Txt_Direccion,
 PC.Txt_Direccion_Referencia_Entidad_Order_Address_Entry AS Txt_Direccion_Referencia,
 ITEM.No_Producto,
 PD.Qt_Producto,
 PD.Ss_Precio,
 PD.Ss_SubTotal,
 PC.Ss_Total
FROM
 pedido_cabecera AS PC
 JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = PC.ID_Tipo_Documento)
 JOIN pedido_detalle AS PD ON(PD.ID_Pedido_Cabecera = PC.ID_Pedido_Cabecera)
 JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
 LEFT JOIN distrito AS DISTRI ON(DISTRI.ID_Distrito = PC.ID_Distrito_Delivery)
 JOIN tabla_dato AS TDRECEP ON(TDRECEP.Nu_Valor = PC.Nu_Tipo_Recepcion AND TDRECEP.No_Relacion = 'Tipos_Recepcion')
 JOIN entidad AS CLI ON(CLI.ID_Entidad = PC.ID_Entidad)
 JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
WHERE
 PC.ID_Pedido_Cabecera = " . $iIdPedido;

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
}
