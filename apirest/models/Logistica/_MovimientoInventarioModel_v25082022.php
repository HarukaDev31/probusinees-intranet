<?php
class MovimientoInventarioModel extends CI_Model{
    public function crudMovimientoInventario($ID_Almacen, $ID_Documento_Cabecera, $ID_Guia_Cabecera, $arrDetalle, $ID_Tipo_Movimiento, $iAgregar_Modificar, $arrWhere, $Nu_Tipo_Movimiento, $iGeneraStock){
		$this->db->trans_begin();

		//Validar producto cambiado por otro y lo actualizamos de la tabla stock_producto
		if ($iAgregar_Modificar == 1) {//1 = Modificar - Solo sin son documentos de compra, venta o guía
			$ID_Documento_Guia = (isset($arrWhere['ID_Documento_Cabecera']) ? $arrWhere['ID_Documento_Cabecera'] : $arrWhere['ID_Guia_Cabecera'] );
			if (!empty($ID_Documento_Guia)) {// Si guia no esta vacia
				$query = "SELECT
MI.ID_Empresa,
MI.ID_Organizacion,
MI.ID_Almacen,
MI.ID_Producto,
MI.Qt_Producto,
PROD.Nu_Compuesto
FROM
movimiento_inventario AS MI
JOIN producto AS PROD ON(PROD.ID_Producto = MI.ID_Producto)
WHERE
MI.ID_Documento_Cabecera = " . $ID_Documento_Guia . " OR MI.ID_Guia_Cabecera = " . $ID_Documento_Guia;
				$arrDetalleAnterior = $this->db->query($query)->result();
				
				foreach ($arrDetalleAnterior as $row) {
					if ($row->Nu_Compuesto == '0'){
						$Qt_Producto_Actual = $this->db->query("SELECT
Qt_Producto
FROM
stock_producto
WHERE
ID_Almacen = " . $row->ID_Almacen . "
AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->Qt_Producto;
						settype($Qt_Producto_Actual, "double");
						
						$where_stock_producto = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
						$stock_producto_anterior = array(
							'ID_Producto'		=> $row->ID_Producto,
							'Qt_Producto'		=> $Qt_Producto_Actual - $row->Qt_Producto,
							'Ss_Costo_Promedio'	=> 0,
						);
						$this->db->update('stock_producto', $stock_producto_anterior, $where_stock_producto);
					} else {
				        $query = "SELECT
ENLAPRO.ID_Producto,
ENLAPRO.Qt_Producto_Descargar
FROM
enlace_producto AS ENLAPRO
JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
WHERE
ENLAPRO.ID_Producto_Enlace = " . $row->ID_Producto;
				        $arrItems = $this->db->query($query)->result();
				        
						foreach ($arrItems as $row_enlace) {
							$Qt_Producto_Actual = $this->db->query("SELECT
Qt_Producto
FROM
stock_producto
WHERE
ID_Almacen = " . $row->ID_Almacen . "
AND ID_Producto = " . $row_enlace->ID_Producto . " LIMIT 1")->row()->Qt_Producto;
							settype($Qt_Producto_Actual, "double");
							
							$where_stock_producto = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row_enlace->ID_Producto);
							$stock_producto_anterior = array(
								'ID_Producto'		=> $row_enlace->ID_Producto,
								'Qt_Producto'		=> $Qt_Producto_Actual - ($row->Qt_Producto * $row_enlace->Qt_Producto_Descargar),
								'Ss_Costo_Promedio'	=> 0,
							);
							$this->db->update('stock_producto', $stock_producto_anterior, $where_stock_producto);
						}
					}
				}
	        	$this->db->delete('movimiento_inventario', $arrWhere);
			} // ./ if Si guia no esta vacia
		} // ./ if Si se modifico registro
		
		// foreach - Registrar movimiento de inventario por ITEM NORMAL e ITEM PROMOCION
		foreach ($arrDetalle as $row) {
			$ID_Producto = $this->security->xss_clean($row['ID_Producto']);
			
			$arrDataItem = $this->db->query("SELECT Nu_Tipo_Producto, Nu_Compuesto FROM producto WHERE ID_Producto = " . $ID_Producto . " LIMIT 1")->result();
			$Nu_Tipo_Producto = $arrDataItem[0]->Nu_Tipo_Producto;
			$Nu_Compuesto = $arrDataItem[0]->Nu_Compuesto;
			
			if ( $Nu_Tipo_Producto != '0' ) {
				if ($Nu_Compuesto == 0){
					$_movimiento_inventario = array(
						'ID_Empresa'			=> $this->empresa->ID_Empresa,
						'ID_Organizacion'		=> $this->empresa->ID_Organizacion,
						'ID_Almacen'			=> $this->session->userdata['almacen']->ID_Almacen,
						'ID_Tipo_Movimiento'	=> $ID_Tipo_Movimiento,
						'ID_Producto'			=> $ID_Producto,
						'Qt_Producto'			=> round($this->security->xss_clean($row['Qt_Producto']), 6),
						'Ss_Precio'				=> round($this->security->xss_clean(($row['Ss_SubTotal'] / $row['Qt_Producto'])), 6),//Ss_Precio - 12/02/2021
						'Ss_SubTotal' 			=> round($this->security->xss_clean($row['Ss_SubTotal']), 2),
						'Ss_Costo_Promedio'		=> 0.00,
					);
					if ( !empty($ID_Documento_Cabecera) )
						$_movimiento_inventario = array_merge($_movimiento_inventario, array('ID_Documento_Cabecera' => $ID_Documento_Cabecera));
					if ( !empty($ID_Guia_Cabecera) )
						$_movimiento_inventario = array_merge($_movimiento_inventario, array('ID_Guia_Cabecera' => $ID_Guia_Cabecera));
					$movimiento_inventario[] = $_movimiento_inventario;
				} else {
		        	$query = "SELECT
ENLAPRO.ID_Producto,
ENLAPRO.Qt_Producto_Descargar
FROM
enlace_producto AS ENLAPRO
JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
WHERE
ENLAPRO.ID_Producto_Enlace = " . $ID_Producto;
					$arrItems = $this->db->query($query)->result();
					
					foreach ($arrItems as $row_enlace) {
						$_movimiento_inventario = array(
							'ID_Empresa'			=> $this->empresa->ID_Empresa,
							'ID_Organizacion'		=> $this->empresa->ID_Organizacion,
							'ID_Almacen'			=> $this->session->userdata['almacen']->ID_Almacen,
							'ID_Tipo_Movimiento'	=> $ID_Tipo_Movimiento,
							'ID_Producto'			=> $row_enlace->ID_Producto,
							'Qt_Producto'			=> round($this->security->xss_clean($row['Qt_Producto'] * $row_enlace->Qt_Producto_Descargar), 6),
							'Ss_Precio'				=> round($this->security->xss_clean($row['Ss_Precio']), 6),
							'Ss_SubTotal' 			=> round($this->security->xss_clean($row['Ss_SubTotal']), 2),
							'Ss_Costo_Promedio'		=> 0,
						);
						if ( !empty($ID_Documento_Cabecera) )
							$_movimiento_inventario = array_merge($_movimiento_inventario, array('ID_Documento_Cabecera' => $ID_Documento_Cabecera));
						if ( !empty($ID_Guia_Cabecera) )
							$_movimiento_inventario = array_merge($_movimiento_inventario, array('ID_Guia_Cabecera' => $ID_Guia_Cabecera));
						$movimiento_inventario[] = $_movimiento_inventario;
					}// ./ foreach generar movimiento de inventario
				}// ./ if - else ITEM NORMAL e ITEM PROMOCION 
			}// /. validando tipo de item 0 = Servicio
		}// ./ foreach - Registrar movimiento de inventario por ITEM NORMAL e ITEM PROMOCION

		$iIdDocumentoDetalleFirst = 0;
		if (isset($movimiento_inventario)) {
			$this->db->insert_batch('movimiento_inventario', $movimiento_inventario);
			$iIdDocumentoDetalleFirst = $this->db->insert_id();
		}

		// Obtener costo promedio
		// Recordar que solo los movimiento de TIPO ENTRADA puede modificar costo $ID_Tipo_Movimiento
		// Recordar que cuenta es SALIDA debe de tomar el costo promedio anterior
		// la tabla tipo_movimiento su campo de $ID_Tipo_Movimiento
			// campo Nu_Costear = 1 quiere decir que debemos de sacar costo promedio
			// campo Nu_Tipo_Movimiento = 1 SALIDA y 0 = ENTRADA
		if ( $iIdDocumentoDetalleFirst > 0 ) {
			foreach ($arrDetalle as $row) {
				$ID_Producto = $this->security->xss_clean($row['ID_Producto']);
				$ID_Documento_Guia = (!empty($ID_Documento_Cabecera) ? $ID_Documento_Cabecera : $ID_Guia_Cabecera);
				
				if ($iAgregar_Modificar == 1)//1 = Modificar - Solo sin son documentos de compra, venta o guía
					$ID_Documento_Guia = (isset($arrWhere['ID_Documento_Cabecera']) ? $arrWhere['ID_Documento_Cabecera'] : $arrWhere['ID_Guia_Cabecera'] );

				$Fe_Emision = '';
				if (!empty($ID_Documento_Guia)) {// Si guia no esta vacia
					$row_fecha = $this->db->query("SELECT Fe_Emision FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $ID_Documento_Guia . " LIMIT 1")->row();
					if ( !empty($row_fecha) ) {
						$Fe_Emision = $row_fecha->Fe_Emision;
					}
					
					if ( empty($Fe_Emision) ) {
						$Fe_Emision = $this->db->query("SELECT Fe_Emision FROM guia_cabecera WHERE ID_Guia_Cabecera = " . $ID_Documento_Guia . " LIMIT 1")->row()->Fe_Emision;
						if ( !empty($row_fecha) ) {
							$Fe_Emision = $row_fecha->Fe_Emision;
						}
					}
				}

				$Ss_Costo_Promedio = 0.00;
				$arrParams = array(
					'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
					'Fe_Emision' => $Fe_Emision,
					'ID_Producto' => $ID_Producto,
				);
				$arrDataStockTotalActual = $this->HelperModel->getStockProductoTotal($arrParams);
				$Ss_Importe_Total = ($arrDataStockTotalActual['Ss_Importe_Total_Entrada'] - $arrDataStockTotalActual['Ss_Importe_Total_Salida']);
				$Qt_Producto_Total = ($arrDataStockTotalActual['Qt_Producto_Total_Entrada'] - $arrDataStockTotalActual['Qt_Producto_Total_Salida']);
				$Ss_Costo_Promedio = ($Qt_Producto_Total > 0 ? ($Ss_Importe_Total / $Qt_Producto_Total) : 0);
				$movimiento_inventarioUPD[] = array(
					'ID_Inventario' => $iIdDocumentoDetalleFirst,
					'Ss_Costo_Promedio' => $Ss_Costo_Promedio
				);
			}
    		$this->db->update_batch('movimiento_inventario', $movimiento_inventarioUPD, 'ID_Inventario');
		}
		// ./ costo promedio
		
		// foreach - Actualizar cantidad tabla stock_producto
		foreach ($arrDetalle as $row) {
			$ID_Producto = $this->security->xss_clean($row['ID_Producto']);
			
			$arrDataItem = $this->db->query("SELECT Nu_Tipo_Producto, Nu_Compuesto FROM producto WHERE ID_Producto = " . $ID_Producto . " LIMIT 1")->result();
			$Nu_Tipo_Producto = $arrDataItem[0]->Nu_Tipo_Producto;
			$Nu_Compuesto = $arrDataItem[0]->Nu_Compuesto;
			
			if ($iGeneraStock == 1) {//tabla stock_producto
				if ( $Nu_Tipo_Producto != '0' ) {
					if ($Nu_Compuesto == 0){
						if ($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Producto = " . $ID_Producto . " LIMIT 1")->row()->existe > 0){
							$where_stock_producto = array('ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen, 'ID_Producto' => $ID_Producto);
						
							$arrRowSalida = $this->db->query("SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND K.ID_Producto = " . $ID_Producto . "
AND TMOVI.Nu_Tipo_Movimiento = 1")->row();
							$Qt_Producto_Salida = $arrRowSalida->Qt_Producto;
							$Ss_SubTotal_Salida = $arrRowSalida->Ss_SubTotal;
							settype($Qt_Producto_Salida, "double");
							settype($Ss_SubTotal_Salida, "double");
						
							$arrRowEntrada = $this->db->query("SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND K.ID_Producto = " . $ID_Producto . "
AND TMOVI.Nu_Tipo_Movimiento = 0")->row();
							$Qt_Producto_Entrada = $arrRowEntrada->Qt_Producto;
							$Ss_SubTotal_Entrada = $arrRowEntrada->Ss_SubTotal;
							settype($Qt_Producto_Entrada, "double");
							settype($Ss_SubTotal_Entrada, "double");
						
							$Qt_Producto_Actual = round(($Qt_Producto_Entrada - $Qt_Producto_Salida), 6);
							$Ss_SubTotal_Actual = round(($Ss_SubTotal_Entrada - $Ss_SubTotal_Salida), 6);
							$stock_producto = array(
								'ID_Producto' => $ID_Producto,
								'Qt_Producto' => $Qt_Producto_Actual,
								'Ss_Costo_Promedio'	=> ($Qt_Producto_Actual > 0 ? ($Ss_SubTotal_Actual / $Qt_Producto_Actual) : 0),
							);
							$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
						} else { // Insert a stock_producto por primera vez
							$stock_producto = array(
								'ID_Empresa'		=> $this->empresa->ID_Empresa,
								'ID_Organizacion'	=> $this->empresa->ID_Organizacion,
								'ID_Almacen'		=> $this->session->userdata['almacen']->ID_Almacen,
								'ID_Producto'		=> $ID_Producto,
								'Qt_Producto'		=> ($Nu_Tipo_Movimiento == 1 ? -round($this->security->xss_clean($row['Qt_Producto']), 6) : round($this->security->xss_clean($row['Qt_Producto']), 6)),
								'Ss_Costo_Promedio'	=> round($this->security->xss_clean($row['Ss_Precio']), 6),
							);
							$this->db->insert('stock_producto', $stock_producto);
						}
					} else {
		    			$query = "SELECT
ENLAPRO.ID_Producto,
ENLAPRO.Qt_Producto_Descargar
FROM
enlace_producto AS ENLAPRO
JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
WHERE
ENLAPRO.ID_Producto_Enlace = " . $ID_Producto;
						$arrItems = $this->db->query($query)->result();
						
						foreach ($arrItems as $row_enlace) {
							$ID_Producto = $row_enlace->ID_Producto;
							$Qt_Producto_Descargar = $row_enlace->Qt_Producto_Descargar;
							if ($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Producto = " . $ID_Producto . " LIMIT 1")->row()->existe > 0){
								$where_stock_producto = array('ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen, 'ID_Producto' => $ID_Producto);
								
								$arrRowSalida = $this->db->query("SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND K.ID_Producto = " . $ID_Producto . "
AND TMOVI.Nu_Tipo_Movimiento = 1")->row();
								$Qt_Producto_Salida = $arrRowSalida->Qt_Producto;
								$Ss_SubTotal_Salida = $arrRowSalida->Ss_SubTotal;
								settype($Qt_Producto_Salida, "double");
								settype($Ss_SubTotal_Salida, "double");
								
								$arrRowEntrada = $this->db->query("SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND K.ID_Producto = " . $ID_Producto . "
AND TMOVI.Nu_Tipo_Movimiento = 0")->row();
								$Qt_Producto_Entrada = $arrRowEntrada->Qt_Producto;
								$Ss_SubTotal_Entrada = $arrRowEntrada->Ss_SubTotal;
								settype($Qt_Producto_Entrada, "double");
								settype($Ss_SubTotal_Entrada, "double");
								
								$Qt_Producto_Actual = round(($Qt_Producto_Entrada - $Qt_Producto_Salida), 6);
								$Ss_SubTotal_Actual = round(($Ss_SubTotal_Entrada - $Ss_SubTotal_Salida), 6);
								$stock_producto = array(
									'ID_Producto' => $ID_Producto,
									'Qt_Producto' => $Qt_Producto_Actual,
									'Ss_Costo_Promedio'	=> ($Qt_Producto_Actual > 0 ? ($Ss_SubTotal_Actual / $Qt_Producto_Actual) : 0),
								);
								$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
							} else {
								$stock_producto = array(
									'ID_Empresa'		=> $this->empresa->ID_Empresa,
									'ID_Organizacion'	=> $this->empresa->ID_Organizacion,
									'ID_Almacen'		=> $this->session->userdata['almacen']->ID_Almacen,
									'ID_Producto'		=> $ID_Producto,
									'Qt_Producto'		=> $Nu_Tipo_Movimiento == 1 ? - round($this->security->xss_clean($row['Qt_Producto'] * $Qt_Producto_Descargar), 6) : round($this->security->xss_clean($row['Qt_Producto'] * $Qt_Producto_Descargar), 6),
									'Ss_Costo_Promedio'	=> round($this->security->xss_clean($row['Ss_Precio']), 6),
								);
								$this->db->insert('stock_producto', $stock_producto);
							}
						} // /. foreach calcular stock por enlaces de item
					} // /. validacion item si es compuesto
				} // /. validando tipo de item 0 = Servicio
			} // /. genera stock value de function controller
		}// foreach - Actualizar cantidad tabla stock_producto
		
		$mensaje = $iAgregar_Modificar == 1 ? 'Modificado' : 'Agregado';
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'error',
				'style_modal' => 'modal-danger',
				'message' => 'Error al ' . $mensaje,
				'sStatus' => 'danger',
				'sMessage' => 'Error al ' . $mensaje,
			);
        } else {
            $this->db->trans_commit();
            return array(
				'status' => 'success',
				'style_modal' => 'modal-success',
				'message' => 'Registro ' . $mensaje,
				'sEnviarSunatAutomatic' => 'No',
				'sStatus' => 'success',
				'sMessage' => 'Venta completada',
				'iIdDocumentoCabecera' => $ID_Documento_Cabecera
			);
        }
    }
}