<?php
class AjusteInventarioModel extends CI_Model{
	var $table = 'movimiento_inventario';	
    var $column_order = array('ALMA.No_Almacen', 'Fe_Emision_Hora');
    var $column_search = array('ALMA.No_Almacen', 'Fe_Emision_Hora');
	var $order = array('Fe_Emision_Hora' => 'asc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
    	$this->db->where("Fe_Emision_Hora BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");
        
        $this->db->select('CABSTOCK.ID_Documento_Cabecera, ALMA.No_Almacen, Fe_Emision_Hora, COUNT(*) AS Nu_Cantidad')
		->from('movimiento_inventario AS STOCK')
		->join('almacen AS ALMA', 'ALMA.ID_Almacen = STOCK.ID_Almacen', 'left')
		->join('documento_cabecera AS CABSTOCK', 'CABSTOCK.ID_Documento_Cabecera = STOCK.ID_Documento_Cabecera', 'left')
		->where('CABSTOCK.ID_Empresa', $this->empresa->ID_Empresa)
		->where('CABSTOCK.ID_Organizacion', $this->empresa->ID_Organizacion)
		->where('CABSTOCK.ID_Almacen', $this->session->userdata['almacen']->ID_Almacen)
		->where_in('STOCK.ID_Tipo_Movimiento', array(19,21))
		->group_by('CABSTOCK.ID_Documento_Cabecera, ALMA.No_Almacen, Fe_Emision_Hora');

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	function get_datatables(){
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all(){
    	$this->db->where("Fe_Emision_Hora BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");
        
        $this->db->select('CABSTOCK.ID_Documento_Cabecera, ALMA.No_Almacen, Fe_Emision_Hora, COUNT(*) AS Nu_Cantidad')
		->from('movimiento_inventario AS STOCK')
		->join('almacen AS ALMA', 'ALMA.ID_Almacen = STOCK.ID_Almacen', 'left')
		->join('documento_cabecera AS CABSTOCK', 'CABSTOCK.ID_Documento_Cabecera = STOCK.ID_Documento_Cabecera', 'left')
		->where('CABSTOCK.ID_Empresa', $this->empresa->ID_Empresa)
		->where('CABSTOCK.ID_Organizacion', $this->empresa->ID_Organizacion)
		->where('CABSTOCK.ID_Almacen', $this->session->userdata['almacen']->ID_Almacen)
		->where('STOCK.ID_Tipo_Movimiento', 19)
		->group_by('CABSTOCK.ID_Documento_Cabecera, ALMA.No_Almacen, Fe_Emision_Hora');
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Entidad',$ID);
        $query = $this->db->get();
        return $query->row();
    }
        
	public function getItemsAjusteInvetario($arrPost){
		$query = "SELECT
ITEM.ID_Producto,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
STOCK.Qt_Producto
FROM
producto AS ITEM
LEFT JOIN stock_producto AS STOCK ON(ITEM.ID_Producto = STOCK.ID_Producto)
WHERE
STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND ITEM.Nu_Tipo_Producto = 1
AND ITEM.Nu_Compuesto = 0
ORDER BY
ITEM.No_Producto";
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
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
		);
	}

	public function procesarAjusteInventario($arrPost){
		$arrEntidadEmpresa = $this->db->query("SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $this->empresa->ID_Tipo_Documento_Identidad . " AND Nu_Documento_Identidad = '" . $this->empresa->Nu_Documento_Identidad . "' LIMIT 1")->row();
		if ( is_object($arrEntidadEmpresa) ) {
			$this->db->trans_begin();
			
			$iIdMoneda = $this->db->query("SELECT ID_Moneda FROM moneda WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Moneda;
			$iIdMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Medio_Pago;

			$arrCabeceraAjusteInventario = array(
				'ID_Empresa' => $this->empresa->ID_Empresa,
				'ID_Organizacion' => $this->empresa->ID_Organizacion,
				'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
				'ID_Entidad' => $arrEntidadEmpresa->ID_Entidad,
				'ID_Tipo_Asiento' => 2,//Compra
				'ID_Tipo_Documento'	=> 2,
				'ID_Serie_Documento' => dateNow('serie_ymd'),
				'ID_Numero_Documento' => dateNow('numero_ymdhms'),
				'Fe_Emision' => dateNow('fecha'),
				'Fe_Emision_Hora' => dateNow('fecha_hora'),
				'ID_Moneda'	=> $iIdMoneda,
				'ID_Medio_Pago' => $iIdMedioPago,
				'Fe_Vencimiento' => dateNow('fecha'),
				'Fe_Periodo' => dateNow('fecha'),
				'Nu_Descargar_Inventario' => 1,
				'Ss_Total' => 0,
				'Nu_Correlativo' => 0,
				'Nu_Estado' => 6,//Completado
			);
			$this->db->insert('documento_cabecera', $arrCabeceraAjusteInventario);
			$iIdAjusteInventario = $this->db->insert_id();

			$iCounter = 0;
			foreach($arrPost['arrAjusteInventario'] as $row){
				if ( $row['fStockFisico']!='' ) {
					$ID_Producto = $this->security->xss_clean($row['iIdItem']);
					$fStockFisico = round($this->security->xss_clean($row['fStockFisico']), 6);

					$Ss_Costo_Promedio = $this->db->query("SELECT Ss_Costo_Promedio FROM stock_producto WHERE ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Producto = " . $ID_Producto . " LIMIT 1")->row()->Ss_Costo_Promedio;

					$arrMovimientoAjusteInventario[] = array(
						'ID_Documento_Cabecera' => $iIdAjusteInventario,
						'ID_Empresa' => $this->empresa->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
						'ID_Tipo_Movimiento' => $this->security->xss_clean($arrPost['iTipoMovimientoInventario']),//AJUSTE POR DIFERENCIA DE INVENTARIO
						'ID_Producto' => $ID_Producto,
						'Qt_Producto' => $fStockFisico,
						'Ss_Precio' => $Ss_Costo_Promedio,
						'Ss_SubTotal' => ($fStockFisico * $Ss_Costo_Promedio),
						'Ss_Costo_Promedio' => $Ss_Costo_Promedio
					);
					++$iCounter;
				}// if - validaciones de campos
			}

			if (isset($arrMovimientoAjusteInventario)) {
				$this->db->insert_batch('movimiento_inventario', $arrMovimientoAjusteInventario);

				foreach($arrPost['arrAjusteInventario'] as $row){
					if ( !empty($row['fStockFisico']) ) {
						$ID_Producto = $this->security->xss_clean($row['iIdItem']);
						if ($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Producto = " . $ID_Producto . " LIMIT 1")->row()->existe > 0){
							$where_stock_producto = array('ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen, 'ID_Producto' => $ID_Producto);
						
							$Qt_Producto_Salida = $this->db->query("SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND K.ID_Producto = " . $ID_Producto . "
AND TMOVI.Nu_Tipo_Movimiento = 1")->row()->Qt_Producto;
							settype($Qt_Producto_Salida, "double");

							$Qt_Producto_Entrada = $this->db->query("SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND K.ID_Producto = " . $ID_Producto . "
AND TMOVI.Nu_Tipo_Movimiento = 0")->row()->Qt_Producto;
							settype($Qt_Producto_Entrada, "double");

							$stock_producto = array(
								'ID_Producto' => $ID_Producto,
								'Qt_Producto' => round(($Qt_Producto_Entrada - $Qt_Producto_Salida), 6)
							);
							$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
						}// if - si existe item
					} // if - fStockFisico
				}// foreach - arreglo items a ajustar
			}// if - solo existe el arreglo cumple los filtros
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al procesar ajuste',
				);
			} else {
				//$this->db->trans_rollback();
				$this->db->trans_commit();
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Cantidad de ajustes procesado: ' . $iCounter,
				);
			} 
		} else {// if -> verificar que existe entidad
			return array('sStatus' => 'danger', 'sMessage' => 'Debes de crear primero un proveedor con tu mismo numero de RUC ' . $this->empresa->Nu_Documento_Identidad);
		}
	}

	public function verAjusteProcesado($iIdDocumentoCabecera){
		$query = "SELECT
ITEM.ID_Producto,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
MOVIK.Qt_Producto,
CC.Fe_Emision_Hora
FROM
movimiento_inventario AS MOVIK
JOIN producto AS ITEM ON(ITEM.ID_Producto = MOVIK.ID_Producto)
JOIN documento_cabecera AS CC ON(CC.ID_Documento_Cabecera = MOVIK.ID_Documento_Cabecera)
WHERE
MOVIK.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND MOVIK.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND MOVIK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . "
AND MOVIK.ID_Documento_Cabecera = " . $iIdDocumentoCabecera . "
ORDER BY
ITEM.No_Producto";
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
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
		);
	}

	public function getItem($Nu_Codigo_Barra){
		$query = "SELECT ID_Producto, Nu_Tipo_Producto, Nu_Compuesto, No_Producto FROM producto WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Codigo_Barra='" . $this->db->escape_like_str($Nu_Codigo_Barra) . "' LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function getStockItemxAlmacen($ID_Producto){
		$query = "SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " AND ID_Producto=" . $ID_Producto . " LIMIT 1";
		return $this->db->query($query)->row();
	}
}
