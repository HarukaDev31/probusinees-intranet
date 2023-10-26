<?php
class HelperDropshippingModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function listarBanco($arrPost){
		$query = "SELECT * FROM dropshipping_banco WHERE Nu_Estado=1 AND ID_Pais = " . $this->user->ID_Pais;
		
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}

	public function getProveedoresDropshipping(){
		$query = "SELECT * FROM empresa WHERE Nu_Proveedor_Dropshipping = 1 AND ID_Pais = " . $this->user->ID_Pais . " AND Nu_Estado=1 ORDER BY No_Empresa;";
		return $this->db->query($query)->result();
	}

	public function getProveedoresDropshippingSinPais(){
		$query = "SELECT * FROM empresa WHERE Nu_Proveedor_Dropshipping = 1 AND Nu_Estado=1 ORDER BY No_Empresa;";
		return $this->db->query($query)->result();
	}

	public function getProveedoresAlmacenesDropshipping($arrPost){
		$query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen
FROM
empresa AS EMP
JOIN organizacion AS ORG ON(EMP.ID_Empresa = ORG.ID_Empresa)
JOIN almacen AS ALMA ON(ALMA.ID_Organizacion = ORG.ID_Organizacion)
WHERE
EMP.ID_Empresa=".$arrPost['iIdEmpresa']."
AND EMP.Nu_Proveedor_Dropshipping = 1
AND EMP.Nu_Estado=1
AND ALMA.Nu_Estado=1
ORDER BY
ALMA.No_Almacen";
		return $this->db->query($query)->result();
	}
	
	public function getDataAutocompleteProduct($global_search){
		$where_id_pais = "AND EMP.ID_Pais = " . $this->user->ID_Pais;
		if ($this->user->ID_Empresa == 1){
			$where_id_pais = "";
		}

	    $sql = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Producto AS Nombre,
PRO.Nu_Estado,
EMP.ID_Empresa,
EMP.No_Empresa,
ALMA.ID_Almacen,
ALMA.No_Almacen,
ROUND(STOCK.Qt_Producto,0) AS Qt_Producto,
ROUND(PRO.Ss_Precio_Proveedor_Dropshipping,2) AS Ss_Precio_Proveedor_Dropshipping,
ROUND(PRO.Ss_Precio_Vendedor_Dropshipping,2) AS Ss_Precio_Vendedor_Dropshipping,
ICD.ID_Impuesto_Cruce_Documento
FROM
producto AS PRO
JOIN empresa AS EMP ON(EMP.ID_Empresa = PRO.ID_Empresa)
JOIN organizacion AS ORG ON(EMP.ID_Empresa = ORG.ID_Empresa)
JOIN almacen AS ALMA ON(ALMA.ID_Organizacion = ORG.ID_Organizacion)
JOIN impuesto_cruce_documento AS ICD ON(ICD.ID_Impuesto = PRO.ID_Impuesto AND ICD.Nu_Estado=1)
JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = PRO.ID_Empresa AND STOCK.ID_Organizacion = ORG.ID_Organizacion AND STOCK.ID_Almacen = ALMA.ID_Almacen AND STOCK.ID_Producto = PRO.ID_Producto)
WHERE
EMP.Nu_Estado=1
" . $where_id_pais . "
AND PRO.No_Producto LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
AND (EMP.Nu_Proveedor_Dropshipping=1 OR EMP.Nu_Tienda_Virtual_Propia=1)
AND PRO.Nu_Activar_Item_Lae_Shop =1
ORDER BY
Nombre DESC,
ALMA.No_Almacen
LIMIT 30";
//array_debug($sql);
		return $this->db->query($sql)->result();
    }

	public function listarUsuarioDelivery($arrPost){
		//localhost para asignar grupo delivery
		//$query = "SELECT ID_Usuario, No_Usuario, No_Nombres_Apellidos FROM usuario WHERE ID_Empresa=1 AND ID_Grupo = 1200 AND Nu_Estado=1 ORDER BY No_Nombres_Apellidos";
		//cloud para asignar grupo delivery
		$query = "SELECT ID_Usuario, No_Usuario, No_Nombres_Apellidos FROM usuario WHERE ID_Empresa=1 AND ID_Grupo = 1643 AND Nu_Estado=1 ORDER BY No_Nombres_Apellidos";
		
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}

	function obtenerEstadoRegistroArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Activo','No_Class_Estado' => 'success');
		return array('No_Estado' => 'Inactivo','No_Class_Estado' => 'danger');
	}

	public function getDataAutocompleteMisProductos($global_search){
	    $sql = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Producto AS Nombre,
ROUND(PRO.Ss_Precio_Ecommerce_Online_Regular,2) AS Ss_Precio_Ecommerce_Online_Regular
FROM
producto AS PRO
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.No_Producto LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
AND PRO.Nu_Activar_Item_Lae_Shop =1
AND PRO.Nu_Tipo_Producto=1
ORDER BY
PRO.No_Producto DESC
LIMIT 15";
//array_debug($sql);
		return $this->db->query($sql)->result();
    }

	public function listarTodosPaises(){
		$query = "SELECT * FROM pais WHERE Nu_Estado=1 ORDER BY No_Pais;";
		return $this->db->query($query)->result();
	}

	public function acumularSaldoBilleteraTienda($arrParams){
		//echo "generar saldo billetera";
	    $sql = "SELECT ID_Empresa, Nu_Estado, Ss_Total, Ss_Total_Proveeedor, Ss_Precio_Delivery, Nu_Tipo_Guia_Api, Nu_Servicio_Transportadora_Dropshipping, Nu_Forma_Pago_Dropshipping FROM pedido_cabecera WHERE ID_Pedido_Cabecera=" . $arrParams['ID_Pedido_Cabecera'] . " LIMIT 1";
		$objPedido = $this->db->query($sql)->row();

		//MEXICO
		if($arrParams['ID_Pais']==2) {
        	$precio_callcenter = 30;//pesos mexicanos

			//RESTA
			//Estado anteriores para recuperar su saldo anterior
			$total_comision_mexico=0;
			$ganancia=0;
			if($objPedido->Nu_Estado==5){
				$total_comision_mexico = (($objPedido->Ss_Total * 4) / 100);
				if($objPedido->Ss_Precio_Delivery>0 && $objPedido->Nu_Tipo_Guia_Api==1)//1=99 minutos
					$total_comision_mexico += (($objPedido->Ss_Total * 4) / 100);
				
				$ganancia = ($objPedido->Ss_Total - $objPedido->Ss_Total_Proveeedor - $objPedido->Ss_Precio_Delivery - $total_comision_mexico);
				if($objPedido->Nu_Servicio_Transportadora_Dropshipping==1)//1=callcenter mexico $30 pesos
					$ganancia -= $precio_callcenter;

				//actualizar billetera de proveedor
				//actualizar tabla empresa
				$query_proveedor = "SELECT ID_Empresa_Marketplace_Seller, Ss_Precio_Empresa_Proveedor, Qt_Producto FROM pedido_detalle WHERE ID_Pedido_Cabecera=".$arrParams['ID_Pedido_Cabecera'];
				$arrResultProveedor = $this->db->query($query_proveedor)->result();
				$fTotalProveedor = 0.00;
				
				$precio_comision_proveedor_ecxlae=20;//20=pesos mexicanos

				foreach ($arrResultProveedor as $row) {
					$Ss_Precio_Empresa_Proveedor = $row->Ss_Precio_Empresa_Proveedor;
					$Qt_Producto = $row->Qt_Producto;
					$fTotalProveedor = ($Ss_Precio_Empresa_Proveedor * $Qt_Producto);
					
					$fTotalProveedor -= ($precio_comision_proveedor_ecxlae * $Qt_Producto);
					$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera-" . $fTotalProveedor . " WHERE ID_Empresa=".$row->ID_Empresa_Marketplace_Seller);
				}
			} else if($objPedido->Nu_Estado == 13){//13=Devolución Pendiente
				$ganancia = -($objPedido->Ss_Total_Proveeedor + $objPedido->Ss_Precio_Delivery);
			} else if($objPedido->Nu_Estado == 14){//14=devuelto
				$ganancia = -$objPedido->Ss_Precio_Delivery;
			}

			//actualizar tabla empresa
			$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera-" . $ganancia . " WHERE ID_Empresa=".$objPedido->ID_Empresa);

			//refrescar a nuevo saldo
			//SUMA
			$total_comision_mexico=0;
			$ganancia=0;
			if($arrParams['Nu_Estado']==5){//NUEVO ESTADO
				$total_comision_mexico = (($objPedido->Ss_Total * 4) / 100);
				if($objPedido->Ss_Precio_Delivery>0 && $objPedido->Nu_Tipo_Guia_Api==1)//1=99 minutos
					$total_comision_mexico += (($objPedido->Ss_Total * 4) / 100);
				
				$ganancia = ($objPedido->Ss_Total - $objPedido->Ss_Total_Proveeedor - $objPedido->Ss_Precio_Delivery - $total_comision_mexico);
				if($objPedido->Nu_Servicio_Transportadora_Dropshipping==1)//1=callcenter mexico $30 pesos
					$ganancia -= $precio_callcenter;

				//actualizar billetera de proveedor
				//actualizar tabla empresa
				$query_proveedor = "SELECT ID_Empresa_Marketplace_Seller, Ss_Precio_Empresa_Proveedor, Qt_Producto FROM pedido_detalle WHERE ID_Pedido_Cabecera=".$arrParams['ID_Pedido_Cabecera'];
				$arrResultProveedor = $this->db->query($query_proveedor)->result();
				$fTotalProveedor = 0.00;
				
				$precio_comision_proveedor_ecxlae=20;//20=pesos mexicanos

				foreach ($arrResultProveedor as $row) {
					$Ss_Precio_Empresa_Proveedor = $row->Ss_Precio_Empresa_Proveedor;
					$Qt_Producto = $row->Qt_Producto;
					$fTotalProveedor = ($Ss_Precio_Empresa_Proveedor * $Qt_Producto);
					
					$fTotalProveedor -= ($precio_comision_proveedor_ecxlae * $Qt_Producto);
					$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera+" . $fTotalProveedor . " WHERE ID_Empresa=".$row->ID_Empresa_Marketplace_Seller);
				}
			} else if($arrParams['Nu_Estado'] == 13){//13=Devolución Pendiente
				$ganancia = -($objPedido->Ss_Total_Proveeedor + $objPedido->Ss_Precio_Delivery);
			} else if($arrParams['Nu_Estado'] == 14){//14=devuelto
				$ganancia = -$objPedido->Ss_Precio_Delivery;
			}
			//actualizar tabla empresa
			$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera+" . $ganancia . " WHERE ID_Empresa=".$objPedido->ID_Empresa);
		} else if ($arrParams['ID_Pais']==1) {//1=PERU
			//RESTA
			//Estado anteriores para recuperar su saldo anterior
			$ganancia=0;
			if($objPedido->Nu_Estado==5){//5=ENTREGADO
				$fPrecioCallcenter = 0;
				if($objPedido->Nu_Servicio_Transportadora_Dropshipping == 1){//callcenter
				  $fPrecioCallcenter = 5;
				}
	
				$fPrecioDropshipping = 0;
				if($objPedido->Nu_Forma_Pago_Dropshipping==2){//2=dropshipping
				  $fPrecioDropshipping = 8;
				}
				
				//total cliente - total proveedor - delivery - call center - dropshipping (OLVA, etc)
				$ganancia = ($objPedido->Ss_Total - $objPedido->Ss_Total_Proveeedor - $objPedido->Ss_Precio_Delivery - $fPrecioCallcenter - $fPrecioDropshipping);

				//actualizar billetera de proveedor
				//actualizar tabla empresa
				$query_proveedor = "SELECT ID_Empresa_Marketplace_Seller, Ss_Precio_Empresa_Proveedor, Qt_Producto FROM pedido_detalle WHERE ID_Pedido_Cabecera=".$arrParams['ID_Pedido_Cabecera'];
				$arrResultProveedor = $this->db->query($query_proveedor)->result();
				$fTotalProveedor = 0.00;
				
				$precio_comision_proveedor_ecxlae=3;//3=SOLES

				foreach ($arrResultProveedor as $row) {
					$Ss_Precio_Empresa_Proveedor = $row->Ss_Precio_Empresa_Proveedor;
					$Qt_Producto = $row->Qt_Producto;
					$fTotalProveedor = ($Ss_Precio_Empresa_Proveedor * $Qt_Producto);
					
					$fTotalProveedor -= ($precio_comision_proveedor_ecxlae * $Qt_Producto);
					$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera-" . $fTotalProveedor . " WHERE ID_Empresa=".$row->ID_Empresa_Marketplace_Seller);
				}
			} else if($objPedido->Nu_Estado == 6){//6=rechazado
				$ganancia = -$objPedido->Ss_Precio_Delivery;
			}

			//actualizar tabla empresa
			$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera-" . $ganancia . " WHERE ID_Empresa=".$objPedido->ID_Empresa);

			//refrescar a nuevo saldo
			//SUMA
			$ganancia=0;
			if($arrParams['Nu_Estado']==5){//5=ENTREGADO NUEVO ESTADO AL QUE ACTUALIZAN
				$fPrecioCallcenter = 0;
				if($objPedido->Nu_Servicio_Transportadora_Dropshipping == 1){//callcenter
				  $fPrecioCallcenter = 5;
				}
	
				$fPrecioDropshipping = 0;
				if($objPedido->Nu_Forma_Pago_Dropshipping==2){//2=dropshipping
				  $fPrecioDropshipping = 8;
				}

				//total cliente - total proveedor - delivery - call center - dropshipping (OLVA, etc)
				$ganancia = ($objPedido->Ss_Total - $objPedido->Ss_Total_Proveeedor - $objPedido->Ss_Precio_Delivery - $fPrecioCallcenter - $fPrecioDropshipping);

				//actualizar billetera de proveedor
				//actualizar tabla empresa
				$query_proveedor = "SELECT ID_Empresa_Marketplace_Seller, Ss_Precio_Empresa_Proveedor, Qt_Producto FROM pedido_detalle WHERE ID_Pedido_Cabecera=".$arrParams['ID_Pedido_Cabecera'];
				$arrResultProveedor = $this->db->query($query_proveedor)->result();
				$fTotalProveedor = 0.00;
				
				$precio_comision_proveedor_ecxlae=3;//3=soles

				foreach ($arrResultProveedor as $row) {
					$Ss_Precio_Empresa_Proveedor = $row->Ss_Precio_Empresa_Proveedor;
					$Qt_Producto = $row->Qt_Producto;
					$fTotalProveedor = ($Ss_Precio_Empresa_Proveedor * $Qt_Producto);
					
					$fTotalProveedor -= ($precio_comision_proveedor_ecxlae * $Qt_Producto);
					$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera+" . $fTotalProveedor . " WHERE ID_Empresa=".$row->ID_Empresa_Marketplace_Seller);
				}
			} else if($arrParams['Nu_Estado'] == 6){//6=rechazado
				$ganancia = -$objPedido->Ss_Precio_Delivery;
			}
			//actualizar tabla empresa
			$this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=Ss_Saldo_Acumulado_Billetera+" . $ganancia . " WHERE ID_Empresa=".$objPedido->ID_Empresa);
		}
	}
	
	public function getPedidoDetalle($arrParams){
		$query = "SELECT
PD.ID_Producto,
PD.Qt_Producto,
ITEM.No_Producto,
PD.Txt_Nota AS Txt_Nota_Item
FROM
pedido_detalle AS PD
JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
WHERE ID_Pedido_Cabecera = " . $arrParams['ID_Pedido_Cabecera'];
		return array(
			'result' => $this->db->query($query)->result()
		);
	}

	public function actualizarStockGeneral($arrParams){
		//solo faltaría evaluar cuando es tienda propia
		$query = "SELECT
PC.Nu_Estado,
ITEM.ID_Producto,
ITEM.ID_Producto_Relacion_Producto_Dropshipping AS ID_Producto_Proveedor,
PD.Qt_Producto,
PD.ID_Almacen_Marketplace_Seller AS ID_Almacen
FROM
pedido_detalle AS PD
JOIN pedido_cabecera AS PC ON(PC.ID_Pedido_Cabecera = PD.ID_Pedido_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto=PD.ID_Producto)
WHERE
PC.ID_Pedido_Cabecera=".$arrParams['ID_Pedido_Cabecera'];
        $arrDetalle = $this->db->query($query)->result();

		//cuando esta pendiente y pasa a rechazado no genero stock pero si esta en pendiente y luego pasa a confirmado y luego pasa a rechazado si hacer suma y resta

		foreach ($arrDetalle as $row) {
			if(
				($row->Nu_Estado==1 && $arrParams['Nu_Estado']==2) ||
				($row->Nu_Estado==2 && $arrParams['Nu_Estado']==14) ||
				($row->Nu_Estado==2 && $arrParams['Nu_Estado']==6)
			) {//1=Pendiente y pasa a confirmado si actualizamos
				$iActualizoStock = 0;
				$objStock = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto_Proveedor . " LIMIT 1")->row();
				if(is_object($objStock)){
					if($arrParams['Nu_Estado']==2){
						$data_stock_producto = array('Qt_Producto' => ($objStock->Qt_Producto - $row->Qt_Producto));
					} else {
						$data_stock_producto = array('Qt_Producto' => ($objStock->Qt_Producto + $row->Qt_Producto));
					}
					$where_stock_producto = array('ID_Producto' => $row->ID_Producto_Proveedor);
					$this->db->update('stock_producto', $data_stock_producto, $where_stock_producto);

					$iActualizoStock = 1;//1=si
				}

				if($iActualizoStock==0){
					//verificar con producto propio
					$objStock = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto . " LIMIT 1")->row();
					if(is_object($objStock)){
						if($arrParams['Nu_Estado']==2){
							$data_stock_producto = array('Qt_Producto' => ($objStock->Qt_Producto - $row->Qt_Producto));
						} else {
							$data_stock_producto = array('Qt_Producto' => ($objStock->Qt_Producto + $row->Qt_Producto));
						}
						$where_stock_producto = array('ID_Producto' => $row->ID_Producto);
						$this->db->update('stock_producto', $data_stock_producto, $where_stock_producto);
					}
				}
			}
		}
	}
}
