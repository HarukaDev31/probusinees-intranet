<?php
class PedidosModel extends CI_Model{
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

        $query = "SELECT DISTINCT
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
 PC.Nu_Estado
FROM
 pedido_cabecera AS PC
 JOIN pedido_detalle AS PD ON(PD.ID_Pedido_Cabecera = PC.ID_Pedido_Cabecera)
 JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = PC.ID_Tipo_Documento)
 JOIN entidad AS CLI ON(CLI.ID_Entidad = PC.ID_Entidad)
 JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
 JOIN tabla_dato AS TDRECEP ON(TDRECEP.Nu_Valor = PC.Nu_Tipo_Recepcion AND TDRECEP.No_Relacion = 'Tipos_Recepcion')
 JOIN tabla_dato AS TDESTADOPEDIDO ON(TDESTADOPEDIDO.Nu_Valor = PC.Nu_Estado AND TDESTADOPEDIDO.No_Relacion = 'Tipos_Estado_Orden_Pedido')
WHERE
 PD.ID_Empresa_Marketplace_Seller = " . $this->empresa->ID_Empresa . "
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

    public function updEstadoPedido($iIdPedido, $iEstadoNuevo){
        if ( $iEstadoNuevo == 2 ) {
            $data = array( 'Nu_Estado' => $iEstadoNuevo );
            $where = array( 'ID_Pedido_Cabecera' => $iIdPedido );
            if ( $this->db->update('pedido_cabecera', $data, $where) > 0 )
                return array('sStatus' => 'success', 'sMessage' => 'Estado de pedido cambiado');
        } else if ( $iEstadoNuevo == 3 ) {// Entregado
            $arrCabecera = $this->db->query("SELECT ID_Entidad, ID_Moneda, ID_Medio_Pago, Ss_Total FROM pedido_cabecera WHERE ID_Pedido_Cabecera = " . $iIdPedido . " LIMIT 1")->row();
            
            $query = "SELECT
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . "
AND ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND ID_Tipo_Documento=2
AND Nu_Estado=1
LIMIT 1";
            $arrSerieDocumento = $this->db->query($query)->row();

			//Generar venta
			$Nu_Correlativo = 0;
			$Fe_Year = dateNow('año');
			$Fe_Month = dateNow('mes');
			
            // Obtener correlativo
			if($this->db->query("SELECT COUNT(*) existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
				$sql_correlativo_libro_sunat = "
UPDATE
 correlativo_tipo_asiento
SET
 Nu_Correlativo=Nu_Correlativo + 1
WHERE
 ID_Empresa=" . $this->empresa->ID_Empresa . "
 AND ID_Tipo_Asiento=1
 AND Fe_Year='" . $Fe_Year. "'
 AND Fe_Month='" . $Fe_Month . "'";
				$this->db->query($sql_correlativo_libro_sunat);
			} else {
				$sql_correlativo_libro_sunat = "
INSERT INTO correlativo_tipo_asiento (
 ID_Empresa,
 ID_Tipo_Asiento,
 Fe_Year,
 Fe_Month,
 Nu_Correlativo
) VALUES (
 " . $this->empresa->ID_Empresa . ",
 1,
 '" . $Fe_Year . "',
 '" . $Fe_Month . "',
 1
);";
				$this->db->query($sql_correlativo_libro_sunat);
			}
			$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
			// /. Obtener correlativo
            
            $arrVentaCabecera = array(
                'ID_Empresa' => $this->empresa->ID_Empresa,
                'ID_Organizacion' => $this->empresa->ID_Organizacion,
                'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
				'ID_Entidad' => $arrCabecera->ID_Entidad,
				'ID_Tipo_Asiento' => 1,//Venta
				'ID_Tipo_Documento' => 2,//Tipo de documento 2 = Interno
				'ID_Serie_Documento_PK' => $arrSerieDocumento->ID_Serie_Documento_PK,
				'ID_Serie_Documento' => $arrSerieDocumento->ID_Serie_Documento,
				'ID_Numero_Documento' => $arrSerieDocumento->Nu_Numero_Documento,
				'Fe_Emision' => dateNow('fecha'),
				'Fe_Emision_Hora' => dateNow('fecha_hora'),
				'ID_Moneda' => $arrCabecera->ID_Moneda,
				'ID_Medio_Pago' => $arrCabecera->ID_Medio_Pago,
				'Fe_Vencimiento' => dateNow('fecha'),
				'Fe_Periodo' => dateNow('fecha'),
				'Nu_Descargar_Inventario' => 1,
				'Ss_Total' => $arrCabecera->Ss_Total,
				'Nu_Correlativo' => $Nu_Correlativo,
				'Nu_Estado' => 6,//Completado
                'ID_Pedido_Cabecera' => $iIdPedido,
            );

			$this->db->insert('documento_cabecera', $arrVentaCabecera);
            $Last_ID_Documento_Cabecera = $this->db->insert_id();

            $arrPedidoDetalle = $this->db->query("SELECT
ID_Producto,
Qt_Producto,
Ss_Precio,
Ss_SubTotal,
ID_Impuesto_Cruce_Documento
FROM pedido_detalle WHERE ID_Pedido_Cabecera = " . $iIdPedido)->result();

			foreach($arrPedidoDetalle as $row) {
				$documento_detalle[] = array(
                    'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
                    'ID_Producto' => $row->ID_Producto,
                    'Qt_Producto' => $row->Qt_Producto,
                    'Ss_Precio' => $row->Ss_Precio,
                    'Ss_SubTotal' => $row->Ss_SubTotal,
					'Ss_Descuento' => 0.00,
					'Ss_Descuento_Impuesto' => 0.00,
					'Po_Descuento' => 0.00,
					'Txt_Nota' => '',
					'ID_Impuesto_Cruce_Documento' => $row->ID_Impuesto_Cruce_Documento,
					'Ss_Impuesto' => 0,
					'Ss_Total' => $row->Ss_SubTotal,
                );
            }
            
            $this->db->insert_batch('documento_detalle', $documento_detalle);
            
            $data = array( 'Nu_Estado' => $iEstadoNuevo );
            $where = array( 'ID_Pedido_Cabecera' => $iIdPedido );
            $this->db->update('pedido_cabecera', $data, $where);

			$this->MovimientoInventarioModel->crudMovimientoInventario($this->session->userdata['almacen']->ID_Almacen,$Last_ID_Documento_Cabecera,0,$documento_detalle,1,0,'',1,1);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar venta');
			} else {
				$this->db->trans_commit();
				return array('sStatus' => 'success', 'sMessage' => 'Pedido entregado');
            }
        }
    }
	
}
