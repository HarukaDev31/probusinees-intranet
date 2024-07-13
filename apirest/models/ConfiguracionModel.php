<?php
class ConfiguracionModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerEmpresa()
    {
        $this->db->select('empresa.*, CONFI.*, ORG.*, ORG.Nu_Estado AS Nu_Estado_Organizacion, ALMA.*, ALMA.Nu_Estado AS Nu_Estado_Almacen, 2043 AS Nu_Multiple_Servicio');
        $this->db->from('empresa');
        $this->db->join('configuracion AS CONFI', 'CONFI.ID_Empresa = empresa.ID_Empresa', 'join');
        $this->db->join('organizacion AS ORG', 'ORG.ID_Empresa = empresa.ID_Empresa', 'join');
        $this->db->join('almacen AS ALMA', 'ORG.ID_Organizacion = ALMA.ID_Organizacion', 'join');
        $this->db->where('empresa.ID_Empresa', $this->user->ID_Empresa);
        $this->db->where('ORG.ID_Organizacion', $this->user->ID_Organizacion);
        $query = $this->db->get();
        return $query->row();
    }

    public function obtenerEmpresa_()
    {
        $this->db->select('empresa.*, CONFI.*, ORG.*, ORG.Nu_Estado AS Nu_Estado_Organizacion, ALMA.*, ALMA.Nu_Estado AS Nu_Estado_Almacen, 2043 AS Nu_Multiple_Servicio');
        $this->db->from('empresa');
        $this->db->join('configuracion AS CONFI', 'CONFI.ID_Empresa = empresa.ID_Empresa', 'join');
        $this->db->join('organizacion AS ORG', 'ORG.ID_Empresa = empresa.ID_Empresa', 'join');
        $this->db->join('almacen AS ALMA', 'ORG.ID_Organizacion = ALMA.ID_Organizacion', 'join');
        $this->db->where('empresa.ID_Empresa', $this->user->ID_Empresa);

        if ($this->user->ID_Organizacion != 0) {
            $this->db->where('ORG.ID_Organizacion', $this->user->ID_Organizacion);
        }

        $query = $this->db->get();
        return $query->row();
    }

    public function obtenerDocumentosPendientePagoLae()
    {
        $query = "SELECT
VC.Fe_Emision,
VC.Fe_Vencimiento,
TDOCU.No_Tipo_Documento_Breve,
VC.ID_Serie_Documento,
ID_Numero_Documento,
VC.Ss_Total_Saldo,
VC.Txt_Url_PDF,
MONE.No_Signo
FROM
documento_cabecera AS VC
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
WHERE
VC.ID_Empresa=1
AND VC.ID_Organizacion=1
AND VC.ID_Almacen=1
AND CLI.Nu_Documento_Identidad='" . $this->empresa->Nu_Documento_Identidad . "'
AND VC.Ss_Total_Saldo>0.00
ORDER BY
VC.Fe_Emision DESC
LIMIT 1;";
        return $this->db->query($query)->row();
    }

    public function obtenerAlmacenes($arrParams)
    {
        $query = "SELECT
EMP.ID_Empresa,
ORG.ID_Organizacion,
ALMA.*
FROM
almacen AS ALMA
JOIN organizacion AS ORG ON(ORG.ID_Organizacion = ALMA.ID_Organizacion)
JOIN empresa AS EMP ON(EMP.ID_Empresa = ORG.ID_Empresa)
WHERE
ALMA.Nu_Estado=1
AND EMP.ID_Empresa=" . $arrParams['iIdEmpresa'] . "
AND ORG.ID_Organizacion=" . $arrParams['iIdOrganizacion'];
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function inicio()
    {
        $query = "SELECT
(SELECT COUNT(*) FROM stock_producto WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Qt_Producto <= 0.000000) AS productos_stock_cero_negativo,
(SELECT COUNT(*) FROM stock_producto AS SP JOIN producto AS P ON(SP.ID_Empresa = P.ID_Empresa AND SP.ID_Producto = P.ID_Producto) WHERE SP.ID_Empresa = " . $this->user->ID_Empresa . " AND SP.Qt_Producto < P.Nu_Stock_Minimo) AS productos_sin_stock_minimo,
(SELECT COUNT(*) FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Organizacion = " . $this->user->ID_Organizacion . " AND ID_Tipo_Asiento=1 AND Ss_Total_Saldo > 0.00 AND Fe_Vencimiento <= '" . dateNow('fecha') . "') AS cuentas_x_cobrar_vencidas,
(SELECT COUNT(*) FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Organizacion = " . $this->user->ID_Organizacion . " AND ID_Tipo_Asiento=2 AND Ss_Total_Saldo > 0.00 AND Fe_Vencimiento <= '" . dateNow('fecha') . "') AS cuentas_x_pagar_vencidas
FROM
empresa
WHERE
ID_Empresa=" . $this->user->ID_Empresa;
        return $this->db->query($query)->row();
    }

    public function reporteGrafico($arrGrafico)
    {
        $column_total = "VD.Ss_SubTotal";
        $column_total_descuento_impuesto = "";
        if ($arrGrafico["iImpuesto"] == '1') { //Si
            $column_total = "VD.Ss_Total";
            $column_total_descuento_impuesto = " + COALESCE(VC.Ss_Descuento_Impuesto, 0) ";
        }

        $sql = "SELECT
VC.Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo,
SUM(COALESCE(VD.total_bfnd, 0) + COALESCE(VD.total_nv, 0)) AS venta_bfnd,
SUM(COALESCE(VD.total_nc, 0)) AS venta_nc,
SUM(COALESCE(VDCOMPRA.total_bfnd, 0)) AS compra_bfnd,
SUM(COALESCE(VDCOMPRA.total_nc, 0)) AS compra_nc
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
LEFT JOIN (
SELECT
VC.ID_Documento_Cabecera,
(CASE WHEN VC.ID_Tipo_Documento=2 THEN SUM(VD.Ss_Total) - SUM(CASE WHEN IMP.Nu_Tipo_Impuesto = 4 THEN VD.Ss_Total ELSE 0 END) - (COALESCE(VC.Ss_Descuento, 0) + COALESCE(VC.Ss_Descuento_Impuesto, 0)) END) AS total_nv,
(CASE WHEN VC.ID_Tipo_Documento IN(3,4,6) THEN SUM(" . $column_total . ") - SUM(CASE WHEN IMP.Nu_Tipo_Impuesto = 4 THEN " . $column_total . " ELSE 0 END) - (COALESCE(VC.Ss_Descuento, 0) " . $column_total_descuento_impuesto . ") END) AS total_bfnd,
SUM((CASE WHEN VC.ID_Tipo_Documento = 5 THEN " . $column_total . " END)) AS total_nc
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
1
) AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN (
SELECT
VC.ID_Documento_Cabecera,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN " . $column_total . " END)) AS total_bfnd,
SUM((CASE WHEN VC.ID_Tipo_Documento = 5 THEN " . $column_total . " END)) AS total_nc
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 2
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
1
) AS VDCOMPRA ON(VC.ID_Documento_Cabecera = VDCOMPRA.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
VC.Fe_Emision,
MONE.ID_Moneda
ORDER BY
VC.Fe_Emision DESC;";

        $r['Tabla'] = $this->db->query($sql)->result();

        $arrDate = explode('-', $arrGrafico["dFinal"]);
        $iYear = $arrDate[0];
        $iMonth = $arrDate[1];

        // Reporte Grafico
        $r['Grafica'] = array('Categoria' => '', 'Moneda' => '', 'Vendido' => '', 'Compra' => '');
        $i = 0;
        $x = 0;

        for ($i = 0; $i <= date('t', strtotime($iYear . "/" . $iMonth . "/01")); $i++) {
            $encontrado = true;
            foreach ($r['Tabla'] as $t) {
                $d = date('d', strtotime($t->Fe_Emision));

                if ($i == $d) {
                    $r['Grafica']['Categoria'] .= $i . ($i != 0 ? ',' : '');
                    $r['Grafica']['Moneda'] .= $t->No_Signo . ($i != 0 ? ',' : '');
                    $r['Grafica']['Vendido'] .= ($t->venta_bfnd - $t->venta_nc) . ($i != 0 ? ',' : '');
                    $r['Grafica']['Compra'] .= ($t->compra_bfnd - $t->compra_nc) . ($i != 0 ? ',' : '');

                    $encontrado = false;
                    break;
                }
            }

            if ($encontrado == true && $i > 0) {
                $r['Grafica']['Categoria'] .= $i . ',';
                $r['Grafica']['Moneda'] .= '0' . ',';
                $r['Grafica']['Vendido'] .= '0' . ',';
                $r['Grafica']['Compra'] .= '0' . ',';
            }
        }

        // SQL - Productos mas vendidos
        $sql = "SELECT
PROD.ID_Producto,
M.No_Marca,
PROD.No_Producto,
PROD.No_Imagen_Item,
MONE.No_Signo,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN VD.Ss_Total ELSE 0 END) - (CASE WHEN VC.ID_Tipo_Documento=5 THEN VD.Ss_Total ELSE 0 END)) AS Ss_Producto,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN VD.Qt_Producto ELSE 0 END) - (CASE WHEN VC.ID_Tipo_Documento=5 THEN VD.Qt_Producto ELSE 0 END)) AS Qt_Producto
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS PROD ON(VD.ID_Producto = PROD.ID_Producto)
LEFT JOIN marca AS M ON(PROD.ID_Marca = M.ID_Marca)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
1
ORDER BY
Ss_Producto DESC
LIMIT 5;";
        $r['arrProductosVendidos'] = $this->db->query($sql)->result();

        // SQL - Categoría mas vendidos
        $sql = "SELECT
F.ID_Familia,
F.No_Familia,
MONE.No_Signo,
F.No_Imagen_Url_Categoria,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN VD.Ss_Total ELSE 0 END) - (CASE WHEN VC.ID_Tipo_Documento=5 THEN VD.Ss_Total ELSE 0 END)) AS Ss_Producto,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN VD.Qt_Producto ELSE 0 END) - (CASE WHEN VC.ID_Tipo_Documento=5 THEN VD.Qt_Producto ELSE 0 END)) AS Qt_Producto
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS PROD ON(VD.ID_Producto = PROD.ID_Producto)
JOIN familia AS F ON(F.ID_Familia = PROD.ID_Familia)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
1
ORDER BY
Ss_Producto DESC
LIMIT 5;";
        $r['arrCategoriasVendidos'] = $this->db->query($sql)->result();

        // SQL - Mejores Clientes
        $sql = "SELECT
COUNT(*) Cantidad,
CLI.No_Entidad AS No_Razsocial,
MONE.No_Signo,
SUM(VD.Qt_Producto) AS Qt_Producto,
ROUND(SUM(COALESCE(VD.total_bfnd, 0) - COALESCE(VD.total_nc, 0)), 2) AS venta_neta
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN entidad AS CLI ON(VC.ID_Entidad = CLI.ID_Entidad)
JOIN (
SELECT
VC.ID_Documento_Cabecera,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN " . $column_total . " END)) AS total_bfnd,
SUM((CASE WHEN VC.ID_Tipo_Documento = 5 THEN " . $column_total . " END)) AS total_nc,
SUM((CASE WHEN VC.ID_Tipo_Documento IN(2,3,4,6) THEN VD.Qt_Producto ELSE 0 END) - (CASE WHEN VC.ID_Tipo_Documento=5 THEN VD.Qt_Producto ELSE 0 END)) AS Qt_Producto
FROM
documento_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
1
) AS VD ON (VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.Nu_Estado IN(6,8)
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
CLI.ID_Entidad,
MONE.ID_Moneda
ORDER BY
venta_neta DESC
LIMIT 5;";
        $r['arrMejoresClientes'] = $this->db->query($sql)->result();
/*
$sql = "SELECT
TD.No_Tipo_Documento_Breve,
VC.Fe_Emision,
VC.Fe_Vencimiento,
VC.Fe_Periodo,
VC.ID_Documento_Cabecera,
CLI.No_Entidad,
CONTACT.No_Entidad AS No_Contacto,
MONE.No_Signo,
VC.Ss_Total,
TDESTADO.No_Class AS No_Class_Estado,
TDESTADO.No_Descripcion AS No_Descripcion_Estado
FROM
documento_cabecera AS VC
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN entidad AS CONTACT ON(CONTACT.ID_Entidad = VC.ID_Contacto)
JOIN tabla_dato AS TDESTADO ON(TDESTADO.Nu_Valor = VC.Nu_Estado AND TDESTADO.No_Relacion = 'Tipos_EstadoDocumento')
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento = 1
AND VC.Nu_Estado IN(0,1,5,6)
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
ORDER BY
VC.Fe_Emision DESC
LIMIT 5;";
 */
/*
0 = Entredao
1 = Revisado
5 = Registrado
 */
        //$r['arrOrdenesVenta'] = $this->db->query($sql)->result();
        $r['arrOrdenesVenta'] = array();

        return $r;
    }

    public function actualizarEstadoActualizacionVersionSistema($where, $data)
    {
        if ($this->db->update('configuracion', $data, $where) > 0) {
            return array(
                'status' => 'success',
                'style_modal' => 'modal-success',
                'message' => 'Se esta actualizando la nueva versión del sistema, se le notificará en cuanto haya culminado.',
            );
        } else {
            return array(
                'status' => 'error',
                'style_modal' => 'modal-danger',
                'message' => 'No se pudo realizar la actualización, inténtelo más tarde. (Model)',
            );
        }
    }

    public function reporteGraficoTiendaVirtual($arrGrafico)
    {
        $sql = "SELECT
VC.Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo,
SUM(VD.total + VC.Ss_Precio_Delivery) AS venta_neta
FROM
pedido_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
LEFT JOIN (
SELECT
VC.ID_Pedido_Cabecera,
SUM(VD.Ss_Total) AS total
FROM
pedido_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN pedido_detalle AS VD ON(VC.ID_Pedido_Cabecera = VD.ID_Pedido_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.Nu_Estado != 6
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
1
) AS VD ON(VC.ID_Pedido_Cabecera = VD.ID_Pedido_Cabecera)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.Nu_Estado != 6
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
VC.Fe_Emision,
MONE.ID_Moneda
ORDER BY
VC.Fe_Emision DESC;";

        $r['Tabla'] = $this->db->query($sql)->result();

        $arrDate = explode('-', $arrGrafico["dFinal"]);
        $iYear = $arrDate[0];
        $iMonth = $arrDate[1];

        // Reporte Grafico
        $r['Grafica'] = array('Categoria' => '', 'Moneda' => '', 'Vendido' => '');
        $i = 0;
        $x = 0;

        for ($i = 0; $i <= date('t', strtotime($iYear . "/" . $iMonth . "/01")); $i++) {
            $encontrado = true;
            foreach ($r['Tabla'] as $t) {
                $d = date('d', strtotime($t->Fe_Emision));

                if ($i == $d) {
                    $r['Grafica']['Categoria'] .= $i . ($i != 0 ? ',' : '');
                    $r['Grafica']['Moneda'] .= $t->No_Signo . ($i != 0 ? ',' : '');
                    $r['Grafica']['Vendido'] .= $t->venta_neta . ($i != 0 ? ',' : '');

                    $encontrado = false;
                    break;
                }
            }

            if ($encontrado == true && $i > 0) {
                $r['Grafica']['Categoria'] .= $i . ',';
                $r['Grafica']['Moneda'] .= '0' . ',';
                $r['Grafica']['Vendido'] .= '0' . ',';
            }
        }

        $sql = "SELECT
VC.Nu_Estado,
COUNT(*) AS Qt_Cantidad_Trans,
SUM(VC.Ss_Total) AS Ss_Total
FROM
pedido_cabecera AS VC
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.Nu_Estado != 6
AND VC.ID_Moneda = " . $arrGrafico["iIDMoneda"] . "
AND VC.Fe_Emision BETWEEN '" . $arrGrafico["dInicial"] . "' AND '" . $arrGrafico["dFinal"] . "'
GROUP BY
1";
        $r['arrPedidosEstados'] = $this->db->query($sql)->result();
        return $r;
    }

    public function obtenerTourTiendaVirtual()
    {
        $query = "SELECT * FROM tour_tienda_virtual WHERE ID_Empresa = " . $this->user->ID_Empresa . " ORDER BY Nu_Orden";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function obtenerUrlTiendaVirtual()
    {
        // echo "<pre>";
        // print_r($this->user);
        // echo "</pre>";
        //$this->session->userdata['almacen']->ID_Almacen
        $query = "SELECT * FROM subdominio_tienda_virtual WHERE ID_Empresa=" . $this->user->ID_Empresa;
        return $this->db->query($query)->row();
    }

    public function obtenerTourGestion()
    {
        $query = "SELECT * FROM tour_gestion WHERE ID_Empresa = " . $this->user->ID_Empresa . " ORDER BY Nu_Orden";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function obtenerTourDropshippingTiendaVirtual()
    {
        $query = "SELECT * FROM tour_dropshipping WHERE ID_Empresa = " . $this->user->ID_Empresa . " ORDER BY Nu_Orden";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function obtenerUrlDropshippingTiendaVirtual()
    {
        // echo "<pre>";
        // print_r($this->user);
        // echo "</pre>";
        //$this->session->userdata['almacen']->ID_Almacen
        $query = "SELECT * FROM subdominio_dropshipping WHERE ID_Empresa=" . $this->user->ID_Empresa;
        return $this->db->query($query)->row();
    }

    public function obtenerPortadaNovedadesPlataforma()
    {
        $query = "SELECT
No_Slider,
No_Imagen_Url_Inicio_Slider,
No_Url_Accion,
Nu_Orden_Slider,
Nu_Version_Imagen,
Nu_Tipo_Inicio
FROM
ecommerce_inicio
WHERE
ID_Empresa = 1
AND Nu_Estado_Slider = 1
AND Nu_Tipo_Inicio IN(1,3)
ORDER BY
Nu_Tipo_Inicio,
Nu_Orden_Slider;";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function obtenerPaisesOperaciones()
    {
        //Obtener paises de los usuarios para no repetirlos
        $query = "SELECT * FROM pais WHERE ID_Pais NOT IN(SELECT EMP.ID_Pais FROM usuario AS USR JOIN empresa AS EMP ON(USR.ID_Empresa = EMP.ID_Empresa) WHERE USR.No_Usuario = '" . $this->user->No_Usuario . "');";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function obtenerDatosUsuarioCreacionNuevaCuenta()
    {
        //Obtener paises de los usuarios para no repetirlos
        $query = "SELECT No_Password FROM usuario WHERE ID_Usuario = " . $this->user->ID_Usuario . " LIMIT 1;";
        return $this->db->query($query)->row();
    }

    public function obtenerPaisesUsuario()
    {
        $query = "SELECT EMP.ID_Pais, P.* FROM usuario AS USR JOIN empresa AS EMP ON(USR.ID_Empresa = EMP.ID_Empresa) JOIN pais AS P ON(P.ID_Pais = EMP.ID_Pais) WHERE USR.No_Usuario = '" . $this->user->No_Usuario . "'";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function obtenerPedidosSinAsignar()
    {
        //->where_in($this->table . '.Nu_Estado', array(2,3,4,8));//garantizados
        //->where_in($this->table . '.Nu_Estado', array(5,6,7,9));//pagados / oc

        $query = "select ID_Cotizacion  from carga_consolidada_cotizaciones_cabecera cccc where Cotizacion_Status_ID =1;";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
            return array(
                'status' => 'success',
                'result' => $arrResponseSQL->result(),
            );
        }

        return array(
            'status' => 'warning',
            'message' => 'No se encontro registro',
            'query' => $query,
        );
    }
    public function obtenerCotizacionesPedidosSinAsignar(){
        $query="select count(*) as count from carga_consolidada_cotizaciones_cabecera cccc  where cccc.Cotizacion_Status_ID =1 and cccc.updated_at is  null;
        ";
        return $this->db->query($query)->row();
    }
    public function obtenerPedidosXUsuario()
    {
        //->where_in($this->table . '.Nu_Estado', array(2,3,4,8));//garantizados
        //->where_in($this->table . '.Nu_Estado', array(5,6,7,9));//pagados / oc

        //$where_id_usuario = ($this->user->Nu_Tipo_Privilegio_Acceso==1 ? " ACPC.ID_Usuario_Interno_Empresa = " . $this->user->ID_Usuario : " ACPC.ID_Usuario_Interno_China = " . $this->user->ID_Usuario . " OR ACPC.ID_Usuario_Interno_Jefe_China = " . $this->user->ID_Usuario);

        $where_id_usuario = " ACPC.ID_Usuario_Interno_Empresa = " . $this->user->ID_Usuario;
        if ($this->user->Nu_Tipo_Privilegio_Acceso == 2) //personal china
        {
            $where_id_usuario = " ACPC.ID_Usuario_Interno_China = " . $this->user->ID_Usuario;
        } else if ($this->user->Nu_Tipo_Privilegio_Acceso == 5) //jefe china
        {
            $where_id_usuario = " ACPC.ID_Usuario_Interno_Jefe_China = " . $this->user->ID_Usuario;
        }

        $query = "SELECT
ACPC.ID_Pedido_Cabecera,
ACPC.Fe_Emision_Cotizacion,
CORRE.Fe_Month,
ACPC.Nu_Correlativo,
ACPC.Nu_Estado AS Nu_Estado_Pedido,
CLI.ID_Entidad,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.No_Contacto,
CLI.Nu_Celular_Contacto,
CLI.Txt_Email_Contacto,
ACPC.ID_Usuario_Interno_Empresa,
ACPC.ID_Usuario_Interno_China,
ACPC.ID_Usuario_Interno_Jefe_China,
ACPC.Nu_Tipo_Servicio,
ACPC.Nu_Tipo_Incoterms,
ACPC.Nu_Tipo_Transporte_Maritimo
FROM
agente_compra_pedido_cabecera AS ACPC
JOIN agente_compra_correlativo AS CORRE ON(CORRE.ID_Agente_Compra_Correlativo = ACPC.ID_Agente_Compra_Correlativo)
JOIN entidad AS CLI ON(CLI.ID_Entidad = ACPC.ID_Entidad)
WHERE " . $where_id_usuario . " ORDER BY ACPC.Fe_Registro_Hora_Cotizacion ASC"; //ver cual interesa más primeros pedidos o los ultimos arriba?
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
            return array(
                'status' => 'success',
                'result' => $arrResponseSQL->result(),
            );
        }

        return array(
            'status' => 'warning',
            'message' => 'No se encontro registro',
            'query' => $query,
        );
    }

    public function obtenerPedidosXUsuarioDetalle($ID_Pedido_Cabecera)
    {
        $query = "SELECT
PACP.No_Proceso,
PACP.Nu_Estado_Proceso,
PACP.Txt_Url_Menu,
PACP.Nu_ID_Interno
FROM
proceso_agente_compra_pedido AS PACP
WHERE
PACP.ID_Pedido_Cabecera = " . $ID_Pedido_Cabecera . " AND PACP.ID_Usuario_Interno_Empresa=" . $this->user->ID_Usuario . " ORDER BY PACP.Nu_Orden";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
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

    public function verificarEstadoProcesoAgenteCompra($ID_Pedido_Cabecera)
    {
        $query = "SELECT Nu_ID_Interno, Nu_Estado_Proceso FROM proceso_agente_compra_pedido WHERE ID_Pedido_Cabecera=" . $ID_Pedido_Cabecera . " AND ID_Usuario_Interno_Empresa=" . $this->user->ID_Usuario;
        return $this->db->query($query)->result();
    }
}
