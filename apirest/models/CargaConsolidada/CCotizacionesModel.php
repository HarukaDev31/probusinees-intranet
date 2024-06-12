<?php
require_once APPPATH . 'third_party/PHPExcel.php';
class CCotizacionesModel extends CI_Model
{
    public $table_carga_consolidada = 'carga_consolidada';
    public $table = 'carga_consolidada_cotizaciones_cabecera';
    public $table_proveedor = "carga_consolidada_cotizaciones_detalles_proovedor";
    public $table_producto = "carga_consolidada_cotizaciones_detalles_producto";
    public $table_tributo = "carga_consolidada_cotizaciones_detalles_tributo";
    public $table_tipo_tributo = "tipo_carga_consolidada_cotizaciones_tributo";
    public $table_cotizacion_detalles = "carga_consolidada_cotizaciones_detalle";
    public $table_tipo_cliente = "carga_consolidada_tipo_cliente";
    public $get_excel_data = "get_cotization_tributos_v2";
    public $table_tarifas = "carga_consolidada_cbm_tarifas";
    public function __construct()
    {
        parent::__construct();
    }
    public function _get_datatables_query()
    {
        $this->db->select('*,  (SELECT CONCAT("[", GROUP_CONCAT(
                JSON_OBJECT(
                    "value", cctc2.ID_Tipo_Cliente,
                    "label", cctc2.Nombre
                )
            ), "]")
        FROM carga_consolidada_tipo_cliente AS cctc2) AS Client_Types,
        (select Telefono from carga_consolidada_cotizaciones_detalle cccd where cccd.ID_Cotizacion = carga_consolidada_cotizaciones_cabecera.ID_Cotizacion) as Telefono,
        (SELECT COUNT(*)
        FROM carga_consolidada_cotizaciones_detalles_tributo AS ccdt
        WHERE ccdt.ID_Cotizacion = carga_consolidada_cotizaciones_cabecera.ID_Cotizacion) as Tributos_Pendientes');
        $this->db->from($this->table);
        $this->db->join($this->table_tipo_cliente, 'carga_consolidada_cotizaciones_cabecera.ID_Tipo_Cliente = carga_consolidada_tipo_cliente.ID_Tipo_Cliente', 'join');
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }
    public function get_cotization_header($ID_Cotizacion)
    {
        $this->db->select('N_Cliente as Nombre,Empresa,SUM(cccdp.CBM_Total) AS CBM_Total,
        SUM(cccdp.Peso_Total) AS Peso_Total');
        $this->db->from($this->table);
        $this->db->join($this->table_proveedor . ' as cccdp',
            'cccdp.ID_Cotizacion = ' . $this->table . '.ID_Cotizacion ', 'join');
        $this->db->where($this->table . '.ID_Cotizacion', $ID_Cotizacion);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_cotization_body($ID_Cotizacion)
    {
        $this->db->select("cccdprov.ID_Proveedor,
        cccdprov.CBM_Total,
        cccdprov.Peso_Total,

        (select ID_Tipo_Cliente from carga_consolidada_cotizaciones_cabecera where ID_Cotizacion = cccdprov.ID_Cotizacion) as ID_Tipo_Cliente,
        (
            SELECT CONCAT('[', GROUP_CONCAT(
                JSON_OBJECT(
                    'ID_Producto', cccdpro.ID_Producto,

                    'Nombre_Comercial', cccdpro.Nombre_Comercial,
                    'Uso', cccdpro.Uso,
                    'URL_Link', cccdpro.URL_Link,
                    'Url_Image', cccdpro.URL_Image,
                    'Cantidad', cccdpro.Cantidad,
                    'Valor_unitario', IFNULL(cccdpro.Valor_unitario, 0),
                    'Tributos_Pendientes', (
                        SELECT
                            COUNT(*)
                        FROM
                            carga_consolidada_cotizaciones_detalles_tributo cccdt
                        WHERE
                            cccdt.ID_Producto = cccdpro.ID_Producto
                            AND cccdt.ID_Proveedor = cccdpro.ID_Proveedor
                            AND cccdt.ID_Cotizacion = cccdpro.ID_Cotizacion
                            AND cccdt.Status = 'Pending'
                    )
                )
            SEPARATOR ','), ']')
            FROM
                carga_consolidada_cotizaciones_detalles_producto cccdpro
            WHERE
                cccdpro.ID_Cotizacion = cccdprov.ID_Cotizacion
                AND cccdpro.ID_Proveedor = cccdprov.ID_Proveedor
        ) AS productos");
        $this->db->from($this->table_proveedor . ' as cccdprov');
        $this->db->where('cccdprov.ID_Cotizacion', $ID_Cotizacion);
        // Correctamente aplicar el offset
        $query = $this->db->get();
        $data = $query->result();

        return $data;

    }
    public function guardarTributos($tributos)
    {
        $ID_Producto = $tributos['ID_Producto'];

        // Define un array asociativo que mapea los nombres de tributos a los valores proporcionados
        $tributosArray = array(
            'ad-valorem' => $tributos['ad-valorem'],
            'igv' => $tributos['igv'],
            'ipm' => $tributos['ipm'],
            'percepcion' => $tributos['percepcion'],
            'valoracion' => $tributos['valoracion'],
            'antidumping' => $tributos['antidumping'],
        );
        // Itera sobre cada tipo de tributo y actualiza su valor en la base de datos
        foreach ($tributosArray as $tipoTributo => $valor) {
            $this->db->where(array(
                "ID_Producto" => $ID_Producto,
                "ID_Tipo_Tributo" => $this->getTributoId($tipoTributo),

            ));

            $data = array("value" => doubleval($valor), "Status" => "Completed");
            $this->db->update($this->table_tributo, $data);
        }

        // Retorna algún mensaje de éxito o indicador de éxito
        return array("success" => true);
    }
    public function getTributoId($table_key)
    {
        $this->db->select('ID_Tipo_Tributo');
        $this->db->from($this->table_tipo_tributo);

        $this->db->where('table_key', $table_key);
        $query = $this->db->get();
        $result = $query->row();
        return $result->ID_Tipo_Tributo;

    }
    public function guardarCotizacion($cotizacion)
    {
        //[{"ID_Proveedor":"9","CBM_Total":"150.00","Peso_Total":"1500.00","productos":[{"ID_Producto":"6","URL_Link":"https:\/\/music.youtube.com\/watch?v=zul8B399nzA&list=RDAMVMxQEV9lYHlNY","Nombre_Comercial":"Zapatos","Uso":"para los pies","Cantidad":"10000","Valor_Unitario":"0"},{"ID_Producto":"7","URL_Link":"31313","Nombre_Comercial":"1131","Uso":"313","Cantidad":"131","Valor_Unitario":"0"}]}]
        try {
            $sum_CBM = 0;
            $sum_Peso = 0;
            foreach ($cotizacion as $cot) {
                $ID_Proveedor = $cot['ID_Proveedor'];

                $CBM_Total = $cot['CBM_Total'];
                $Peso_Total = $cot['Peso_Total'];
                $sum_CBM += $CBM_Total;
                $sum_Peso += $Peso_Total;

                if (intval($ID_Proveedor) === -1) {
                    $this->db->insert($this->table_proveedor, array("CBM_Total" => $CBM_Total, "Peso_Total" => $Peso_Total, "ID_Cotizacion" => $cot['ID_Cotizacion']));
                    $ID_Proveedor = $this->db->insert_id();
                    foreach ($cot['productos'] as $producto) {
                        //if product dont have key tributos continue
                        if (!array_key_exists('tributos', $producto)) {
                            continue;
                        }
                        $URL_Link = $producto['URL_Link'];
                        $Nombre_Comercial = $producto['Nombre_Comercial'];
                        $Uso = $producto['Uso'];
                        $Cantidad = $producto['Cantidad'];
                        $Valor_Unitario = $producto['Valor_Unitario'];
                        $this->db->insert($this->table_producto, array("URL_Link" => $URL_Link, "Nombre_Comercial" => $Nombre_Comercial, "Uso" => $Uso, "Cantidad" => $Cantidad, "Valor_Unitario" => $Valor_Unitario, "ID_Proveedor" => $ID_Proveedor, "ID_Cotizacion" => $cot['ID_Cotizacion']));
                        $ID_Producto = $this->db->insert_id();
                        //foreach key in producto['tributos'] call function to get type product id with key and insert into table tributo
                        foreach ($producto['tributos'] as $key => $value) {
                            //if producto not have tributos continue

                            $ID_Tipo_Tributo = $this->getTypeTributoId($key);
                            $this->db->insert($this->table_tributo, array("ID_Producto" => $ID_Producto, "ID_Proveedor" => $ID_Proveedor, "ID_Cotizacion" => $cot['ID_Cotizacion'], "ID_Tipo_Tributo" => intval($ID_Tipo_Tributo), "value" => $value, "Status" => "Pending"));
                        }

                    }
                    //foreach newproducts where key created_for_new is false  insert
                    foreach ($cot['newProductos'] as $producto) {
                        if ($producto['created_for_new'] === false) {
                            $URL_Link = $producto['URL_Link'];
                            $Nombre_Comercial = $producto['Nombre_Comercial'];
                            $Uso = $producto['Uso'];
                            $Cantidad = $producto['Cantidad'];
                            $Valor_Unitario = $producto['Valor_Unitario'];
                            $this->db->insert($this->table_producto, array("URL_Link" => $URL_Link, "Nombre_Comercial" => $Nombre_Comercial, "Uso" => $Uso, "Cantidad" => $Cantidad, "Valor_Unitario" => $Valor_Unitario, "ID_Proveedor" => $ID_Proveedor, "ID_Cotizacion" => $cot['ID_Cotizacion']));
                            $ID_Producto = $this->db->insert_id();
                            foreach ($producto['tributos'] as $key => $value) {
                                $ID_Tipo_Tributo = $this->getTypeTributoId($key);
                                $this->db->insert($this->table_tributo, array("ID_Producto" => $ID_Producto, "ID_Proveedor" => $ID_Proveedor, "ID_Cotizacion" => $cot['ID_Cotizacion'], "ID_Tipo_Tributo" => intval($ID_Tipo_Tributo), "value" => $value, "Status" => "Pending"));
                            }
                        }
                    }
                } else {
                    $this->db->where('ID_Proveedor', $ID_Proveedor);
                    $this->db->update($this->table_proveedor, array("CBM_Total" => $CBM_Total, "Peso_Total" => $Peso_Total));
                    foreach ($cot['productos'] as $producto) {
                        $ID_Producto = $producto['ID_Producto'];
                        $URL_Link = $producto['URL_Link'];
                        $Nombre_Comercial = $producto['Nombre_Comercial'];
                        $Uso = $producto['Uso'];
                        $Cantidad = $producto['Cantidad'];
                        $Valor_Unitario = $producto['Valor_Unitario'];
                        $this->db->where('ID_Producto', $ID_Producto);
                        $this->db->update($this->table_producto, array("URL_Link" => $URL_Link, "Nombre_Comercial" => $Nombre_Comercial, "Uso" => $Uso, "Cantidad" => $Cantidad, "Valor_Unitario" => $Valor_Unitario));
                    }
                    if (count($cot['newProductos']) > 0) {
                        for ($i = 0; $i <= count($cot['newProductos']); $i++) {

                            $URL_Link = $cot['newProductos'][$i]['URL_Link'];
                            $Nombre_Comercial = $cot['newProductos'][$i]['Nombre_Comercial'];
                            $Uso = $cot['newProductos'][$i]['Uso'];
                            $Cantidad = $cot['newProductos'][$i]['Cantidad'];
                            $Valor_Unitario = $cot['newProductos'][$i]['Valor_Unitario'];
                            $this->db->insert($this->table_producto, array("URL_Link" => $URL_Link, "Nombre_Comercial" => $Nombre_Comercial, "Uso" => $Uso, "Cantidad" => $Cantidad, "Valor_Unitario" => $Valor_Unitario, "ID_Proveedor" => $ID_Proveedor, "ID_Cotizacion" => $cot['ID_Cotizacion']));
                            $ID_Producto = $this->db->insert_id();
                            foreach ($cot['newProductos'][$i]['tributos'] as $key => $value) {
                                $ID_Tipo_Tributo = $this->getTypeTributoId($key);
                                $this->db->insert($this->table_tributo, array("ID_Producto" => $ID_Producto, "ID_Proveedor" => $ID_Proveedor, "ID_Cotizacion" => $cot['ID_Cotizacion'], "ID_Tipo_Tributo" => intval($ID_Tipo_Tributo), "value" => $value, "Status" => "Pending"));
                            }
                        }

                    }
                }
            }
            if (count($cotizacion[0]['deletedProveedores']) > 0) {
                for ($i = 0; $i < count($cotizacion[0]['deletedProveedores']); $i++) {
                    $this->db->where('ID_Proveedor', $cotizacion[0]['deletedProveedores'][$i]);
                    $this->db->delete($this->table_tributo);
                    $this->db->where('ID_Proveedor', $cotizacion[0]['deletedProveedores'][$i]);
                    $this->db->delete($this->table_producto);

                    $this->db->where('ID_Proveedor', $cotizacion[0]['deletedProveedores'][$i]);
                    $this->db->delete($this->table_proveedor);
                }

            }
            if (count($cotizacion[0]['deletedProductos']) > 0) {
                for ($i = 0; $i < count($cotizacion[0]['deletedProductos']); $i++) {
                    $this->db->where('ID_Producto', $cotizacion[0]['deletedProductos'][$i]);
                    $this->db->delete($this->table_tributo);
                    $this->db->where('ID_Producto', $cotizacion[0]['deletedProductos'][$i]);
                    $this->db->delete($this->table_producto);
                }
            }
            $this->db->close();
            $this->db->initialize();
            $this->db->update($this->table_cotizacion_detalles, array("CBM_Total" => $sum_CBM, "Peso_Total" => $sum_Peso), array("ID_Cotizacion" => $cotizacion[0]['ID_Cotizacion']));
            //COUNT ALL TRIBUTES WITH STATUS PENDING FROM THIS COTIZATION AND IF COUNT IS 0 UPDATE Cotizacion_Status_ID TO 2
            $this->db->select('COUNT(*) as count');
            $this->db->from($this->table_tributo);
            $this->db->where('ID_Cotizacion', $cotizacion[0]['ID_Cotizacion']);
            $this->db->where('Status', 'Pending');
            $query = $this->db->get();
            $result = $query->row();
            if ($result->count == 0) {
                $this->db->update($this->table, array("Cotizacion_Status_ID" => 2), array("ID_Cotizacion" => $cotizacion[0]['ID_Cotizacion']));
            }
            return array("success" => true);

        } catch (Exception $e) {
            return array("success" => false, "message" => $e->getMessage());
        }

    }
    /**
     * Función que obtiene el ID del tipo de tributo a partir de la clave de la tabla
     * @param string $table_key Clave de la tabla
     * @return int ID del tipo de tributo
     */
    public function getTypeTributoId($table_key)
    {
        $this->db->select('ID_Tipo_Tributo');
        $this->db->from($this->table_tipo_tributo);
        $this->db->where('table_key', $table_key);
        $query = $this->db->get();
        $result = $query->row();
        return $result->ID_Tipo_Tributo;
    }
    /**
     * Función que obtiene los datos de la cotización para exportar a Excel
     * @param array $ID_Cotizacion Arreglo con el ID de la cotización
     * @param PHPExcel $objPHPExcel Objeto de PHPExcel
     * @return PHPExcel Objeto de PHPExcel con los datos de la cotización
     * @throws PHPExcel_Exception
     */
    public function fillExcelData($ID_Cotizacion, $objPHPExcel)
    {
        $ID_Cotizacion = intval($ID_Cotizacion["ID_Cotizacion"]);

        $query = $this->db->query("CALL " . $this->get_excel_data . "(" . $ID_Cotizacion . ")");

        $query = json_decode(json_encode($query->result()), true);

        $this->db->close();
        $this->db->initialize();
        $newSheet = $objPHPExcel->createSheet();
        //set title
        $newSheet->setTitle('3');

        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B3:G3');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B3', 'Calculo de Tributos');
        $style = $objPHPExcel->getActiveSheet()->getStyle('B3');
        $grayColor = 'F8F9F9';
        $blueColor = '1F618D';
        $yellowColor = 'FFFF33';
        $whiteColor = 'FFFFFF';
        $greenColor = "009999";
        $borders = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set auto size for columns

        // Establecer el color de fondo gris
        $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB($grayColor);

        //ALL BORDERS
        $objPHPExcel->getActiveSheet()->getStyle('B3:G3')->applyFromArray($borders);
        //center text
        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B5', 'Nombres');
        //set fill blue color
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFill()->getStartColor()->setARGB($blueColor);
        //letter white
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
        //all borders
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //apply border to all cells
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B6', 'Peso');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B7', "Valor CBM");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B8', 'Valor Unitario');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B9', 'Valoracion');
        //apply yellow color
        $objPHPExcel->getActiveSheet()->getStyle('B9')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B9')->getFill()->getStartColor()->setARGB($yellowColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B10', 'Cantidad');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B11', 'Valor FOB');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B12', 'Valor FOB Valoracion');
        $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->getStartColor()->setARGB($yellowColor);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B13', 'Distribucion %');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B14', 'Flete');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B15', 'Valor CFR');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B16', 'CFR Valorizado');
        $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->getStartColor()->setARGB($yellowColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B17', 'Seguro');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B18', 'Valor CIF');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B19', 'CIF Valorizado');
        $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->getStartColor()->setARGB($yellowColor);

        //foreach row in sp result set the values in the excel
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);

        $InitialColumn = 'C';
        $totalRows = 0;
        foreach ($query as $row) {

            $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);

            //set auto size for columns
            $InitialColumn++;
            $totalRows++;

        }

        $ID_Tipo_Cliente = $query[0]["ID_Tipo_Cliente"];

        $tarifas = $this->db->select('*')
            ->from($this->table_tarifas)
            ->where('id_tipo_cliente', $ID_Tipo_Cliente)
            ->where('updated_at is null')
            ->order_by('limite_inf', 'ASC')
            ->get()
            ->result_array();

        $tarifas = json_decode(json_encode($tarifas), true);
        $TarifasStartColumn = chr(ord($InitialColumn) + 5);
        $TarifasStartColumn2 = chr(ord($InitialColumn) + 6);
        $TarifasStartColumn3 = chr(ord($InitialColumn) + 7);
        $TarifasStartColumn4 = chr(ord($InitialColumn) + 8);
        //set tarifas cell auto size
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn2)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn3)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn4)->setAutoSize(true);
        //merge cells from $TarifasStartColumn2 to $TarifasStartColumn
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . '8:' . $TarifasStartColumn4 . '8');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . '8', $query[0]["tipo_cliente"]);
        //set center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . '8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . '8')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . '9', "CBM Limite Inferior");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn2 . '9', "CBM Limite Superior");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . '9', "Tarifa");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn4 . '9', "Tipo de Tarifa");

        $CBM_Total_C = $query[0]["CBM_Total"];
        $initialRow = 10;
        $tarifaCell = "";
        $tipoTarifa = "";
        foreach ($tarifas as $tarifa) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, $tarifa["limite_inf"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn2 . $initialRow, floatval($tarifa["limite_sup"]));
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, $tarifa["tarifa"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn4 . $initialRow, $tarifa["id_tipo_tarifa"] == 1 ? "Estandar" : "No Estandar");
            //set currency format with dollar symbol
            if ($CBM_Total_C >= $tarifa["limite_inf"] && $CBM_Total_C <= $tarifa["limite_sup"]) {
                $tarifaCell = $TarifasStartColumn3 . $initialRow;
                $tipoTarifa = $TarifasStartColumn4 . $initialRow;
            }
            $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $initialRow++;
        }

        //from $TarifasStartColumn8 to $TarifasStartColumn4 15 apply all borders
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . '8:' . $TarifasStartColumn4 . ($initialRow - 1))->applyFromArray($borders);

        $initialRow++;
        //merge cells from $TarifasStartColumn2 to $TarifasStartColumn
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, "CBM");
        ////set center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getFont()->setBold(true);
        //merge cells from $TarifasStartColumn3 to $TarifasStartColumn4
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, "Cobro");
        //set center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getFont()->setBold(true);
        $initialRow++;
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow,
            "=ROUND(MAX(" . $query[0]["peso_total"] / 1000 . "," . $CBM_Total_C . "),2)");
        //center horizontal
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //IF TIPO TARIFA IS Estandar set the value to $tarifaCell else set the TarifaCell*CBM_Total_C
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
            $TarifasStartColumn3 . $initialRow,
            "=IF(" . $tipoTarifa . "=\"Estandar\"," . $tarifaCell . ",ROUNDUP(" . $tarifaCell . "*" . ($TarifasStartColumn . $initialRow) . ", 0))"
        );
        //center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getFont()->setBold(true);
        //set currency format with dollar symbol
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $initialRow++;

        $CobroCell = $TarifasStartColumn3 . ($initialRow - 1);
        $initialRow++;
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, "Flete(60%)");
        //center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, "Destino(40%)");
        //center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $initialRow++;
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, "=ROUNDUP(" . $CobroCell . "*0.6,2)");
        //center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getFont()->setBold(true);
        $FleteCell = $TarifasStartColumn . $initialRow;
        //set currency format with dollar symbol
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, "=ROUNDUP(" . $CobroCell . "*0.4,2)");
        //center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getFont()->setBold(true);
        $DestinoCell = $TarifasStartColumn3 . $initialRow;
        //set currency format with dollar symbol
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . ($initialRow - 1) . ":" . $TarifasStartColumn4 . $initialRow)->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . ($initialRow - 4) . ":" . $TarifasStartColumn4 . ($initialRow - 3))->applyFromArray($borders);
        $initialRow++;

        $InitialColumn = 'C';
        foreach ($query as $row) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '5', $row["Nombre_Comercial"]);
            //APLY BACKGROUND COLOR BLUE AND LETTERS WHITE
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->getStartColor()->setARGB($blueColor);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', 0);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', 0);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '8', $row["Valor_Unitario"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '9', $row["Valoracion"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', $row["Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', "=" . $InitialColumn . "8*" . $InitialColumn . "10");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '12', "=" . $InitialColumn . "10*" . $InitialColumn . "9");
            //set format currency with dollar symbol $InitialColumn.8,9,11,12
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '8')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);
            //set auto size for columns
            $InitialColumn++;
            $totalRows++;
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);
        $InitialColumnLetter = chr(ord($InitialColumn) - 1);

        $objPHPExcel->getActiveSheet()->getStyle('B5:' . $InitialColumn . '19')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B28:' . $InitialColumn . '32')->applyFromArray($borders);

        $objPHPExcel->getActiveSheet()->getStyle('B40:' . $InitialColumn . '40')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B43:' . $InitialColumn . '47')->applyFromArray($borders);

        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->getStartColor()->setARGB($blueColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '5', "Total");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', $query[0]["peso_total"] > 1000 ? round($query[0]["peso_total"] / 1000, 2) : $query[0]["peso_total"]);
        // IF initial column 6>= 1000 set tn format else set kg format
        if ($query[0]["peso_total"] > 1000) {
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getNumberFormat()->setFormatCode('0.00" tn"');
        } else {
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getNumberFormat()->setFormatCode('0.00" Kg"');
        }

        // $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getNumberFormat()->setFormatCode('0.00" Kg"');

        //set text alignment to right
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', $query[0]["Total_CBM"]);
        //set currency format with $ symbol and text bold
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '7')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', "=SUM(C10:" . $InitialColumnLetter . "10)");

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', "=SUM(C11:" . $InitialColumnLetter . "11)");
        $VFOBCell = $InitialColumn . '11';
        $InitialColumn = 'C';

        foreach ($query as $row) {
            //$INITIALCOLUMN13 =ROUND($VFOBCell/$InitialColumn.'11') TO PERCENTAGE;
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '13', "=" . $InitialColumn . '11/' . $VFOBCell);
            $distroCell = $InitialColumn . '13';
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '13')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            //$initialcolumn14=round($FleteCell*$InitialColumn.'13',2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', "=ROUNDUP(" . $FleteCell . '*' . $InitialColumn . '13,2)');
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //$initialcolumn15=roundup( $initialcolumn11+$initialcolumn14,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '15', "=ROUNDUP(" . $InitialColumn . '11+' . $InitialColumn . '14,2)');
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $cfrCell = $InitialColumn . '15';
            //$initialcolumn15=roundup( $initialcolumn12+$initialcolumn14,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '16', "=ROUNDUP(" . $InitialColumn . '12+' . $InitialColumn . '14,2)');
            $cfrvCell = $InitialColumn . '16';

            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $seguroCell = $InitialColumn . '17';
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //IF COBROCELL IS GREATER THAN 5000 SET THE VALUE TO $initialcolumn17  TO roundup100/ distroCell ELSE SET roundup50/distroCell
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '17', "=IF(" . $CobroCell . ">5000,ROUND(100*" . $distroCell . ",2),ROUND(50*" . $distroCell . ",2))");
            //initial18 is roundup($cfrCell+$seguroCell,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '18', "=ROUNDUP(" . $cfrCell . '+' . $seguroCell . ",2)");
            //initial19 is roundup($cfrvCell+$seguroCell,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '19', "=ROUNDUP(" . $cfrvCell . '+' . $seguroCell . ",2)");

            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $InitialColumn++;
            $totalRows++;

        }

        //g7 =cobrocELL
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', "=" . $CobroCell);

        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '12', "=SUM(C12:" . $InitialColumnLetter . "12)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', "=SUM(C14:" . $InitialColumnLetter . "14)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '15', "=SUM(C15:" . $InitialColumnLetter . "15)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '16', "=SUM(C16:" . $InitialColumnLetter . "16)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '17', "=SUM(C17:" . $InitialColumnLetter . "17)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '18', "=SUM(C18:" . $InitialColumnLetter . "18)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '19', "=SUM(C19:" . $InitialColumnLetter . "19)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getFont()->setBold(true);
        //
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B23:E23');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B23', 'Tributos Aplicables');
        $style = $objPHPExcel->getActiveSheet()->getStyle('B23');
        $borders = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set auto size for columns

        // Establecer el color de fondo gris
        $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB($grayColor);

        //ALL BORDERScc
        $objPHPExcel->getActiveSheet()->getStyle('B23:E23')->applyFromArray($borders);
        //center text
        $objPHPExcel->getActiveSheet()->getStyle('B23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B26', 'ANTIDUMPING');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B28', 'AD VALOREM');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B29', 'IGV');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B30', 'IPM');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B31', 'PERCEPCION');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B32', 'TOTAL');
        $InitialColumn = 'C';
        $sumAdValorem = 0;
        $sumIGV = 0;
        $sumIPM = 0;
        $sumPercepcion = 0;
        $sumTotal = 0;
        $sumAntidumping = 0;

        foreach ($query as $row) {
            $sum = 0;
            $sheet = $objPHPExcel->getActiveSheet();

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '26', $row["antidumping"]);
            //set currency format with $ symbol
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '26')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', $row["ad_valorem"] / 100);
            //set porcentage format
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            //set text color red
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
            $AdValoremCell = $InitialColumn . '28';
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '28',
                "=MAX(" . $InitialColumn . "19," . $InitialColumn . "18)*" . $InitialColumn . "27"
            );
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '28')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "$" . $row["igv_value"]);
            //set initialcolumn29 = $row["igv"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "=" . ($row['igv'] / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+" . $AdValoremCell . ")");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '29')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "$" . $row["ipm_value"]);
            //set initialcolumn30 = $row["ipm"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "=" . ($row['ipm'] / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+" . $AdValoremCell . ")");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '30')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "$" . $row["percepcion_value"]);
            //set initialcolumn31 = $row["percepcion"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '31',
                "=" . ($row['percepcion'] / 100) . "*(MAX(" . $InitialColumn . '18,' . $InitialColumn . '19) +' . $InitialColumn . '28+' . $InitialColumn . '29+' . $InitialColumn . '30)'
            );
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '31')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $sum = "=SUM(" . $InitialColumn . "28:" . $InitialColumn . "31)";
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', $sum);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '32')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $sumIGV += $row["igv_value"];
            $sumIPM += $row["ipm_value"];
            $sumPercepcion += $row["percepcion_value"];
            $sumAntidumping += $row["antidumping"];
            $sumTotal += $sum;
            $cell = $sheet->getCell($InitialColumn . '32');

            $InitialColumn++;

        }

        //apply borders to b26 to initialColumn26
        $objPHPExcel->getActiveSheet()->getStyle('B26:' . $InitialColumn . '26')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('C27:' . $InitialColumn . '27')->applyFromArray($borders);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '26', "=SUM(C26:" . $InitialColumnLetter . "26)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '26')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $cell = $sheet->getCell($InitialColumn . '26');
        // Verificar si el valor es numérico

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', "=SUM(C27:" . $InitialColumnLetter . "27)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '28', "=SUM(C28:" . $InitialColumnLetter . "28)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '28')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "=SUM(C29:" . $InitialColumnLetter . "29)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '29')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $cell = $sheet->getCell($InitialColumn . '29');

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "=SUM(C30:" . $InitialColumnLetter . "30)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '30')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "=SUM(C31:" . $InitialColumnLetter . "31)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '31')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', "=SUM($InitialColumn" . "28:" . $InitialColumn . "31)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '32')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        //Costos Destinos
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B37:E37');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B37', 'Costos Destinos');
        $style = $objPHPExcel->getActiveSheet()->getStyle('B37');
        $borders = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set auto size for columns

        // Establecer el color de fondo gris
        $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB($grayColor);

        //ALL BORDERS
        $objPHPExcel->getActiveSheet()->getStyle('B37:E37')->applyFromArray($borders);
        //center text
        $objPHPExcel->getActiveSheet()->getStyle('B37')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B40', 'ITEM');
        //CBM Total IS INItialColumn7
        $CBMTotal = $InitialColumn . "7";
        $InitialColumn = 'C';
        $sumCostoDestino = 0;

        foreach ($query as $row) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', "=" . $CBMTotal . "*0.4" . "*" . $InitialColumn . "13");
            //Set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '40')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $InitialColumn++;
        }
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', "=SUM(C40:" . $InitialColumnLetter . "40)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '40')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $InitialColumn = 'C';
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B41:E41');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B43', 'ITEM');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B44', 'COSTO TOTAL');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B45', 'CANTIDAD');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B46', 'COSTO UNITARIO');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B47', 'COSTO SOLES');
        $sumCostoTotal = 0;
        foreach ($query as $row) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', $row["Nombre_Comercial"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "$" . $row["Total_Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '45', $row["Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '46', "$" . round($row["Total_Cantidad"] / $row["Cantidad"], 2));
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '47', 'S/.' . round(($row["Total_Cantidad"] / $row["Cantidad"]) * 3.7, 2));
            $sumCostoTotal += $row["Total_Cantidad"];
            $InitialColumn++;
        }
        $ColumndIndex = PHPExcel_Cell::stringFromColumnIndex(count($query) + 1);
        $InitialColumn = "C";
        foreach ($query as $row) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', $row["Nombre_Comercial"]);
            //initial column 44=sum(initialcolumn16+initialcolumn40+initialcolumn32)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '44',
                "=SUM(" . $InitialColumn . "15," . $InitialColumn . "40," . $InitialColumn . "32," . $InitialColumn . "26)"
            );
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '47')->getNumberFormat()->setFormatCode('"S/." #,##0.00_-');

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '45', $row["Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '46', "=ROUND(SUM(" . $InitialColumn . "44/" . $InitialColumn . "45),2)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '46')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //initial column 47=S/.initialcolumn46*3.7
            $CellVal = $objPHPExcel->getActiveSheet()->getCell($InitialColumn . '46')->getValue();
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '47', "=ROUND(" . $InitialColumn . "46*3.7,2)");
            //concat n° to the value text

            //SET PEN CURRENCY FORMAT
            $InitialColumn++;
        }
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "=SUM(C44" . ":" . $InitialColumnLetter . "44)");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J20', "=MAX('3'!C27:" . $ColumndIndex . "27)");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', "Total");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "=SUM(C44:" . $InitialColumnLetter . "44)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        $columnaIndex = PHPExcel_Cell::stringFromColumnIndex(count($query) + 2);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('K14', "='3'!" . $columnaIndex . "11");

        // Construir la fórmula para sumar los valores de las celdas en las columnas 14 y 17
        $formula = "='3'!" . $columnaIndex . "14 + '3'!" . $columnaIndex . "17";

        // Establecer la fórmula en la celda K14
        $objPHPExcel->getActiveSheet()->setCellValue('K15', $formula);
        //k20 =columnaIndex 28
        $objPHPExcel->getActiveSheet()->setCellValue('K20', "='3'!" . $columnaIndex . "28");
        //k21 =columnaIndex 29
        $objPHPExcel->getActiveSheet()->setCellValue('K21', "='3'!" . $columnaIndex . "29");
        //k22 =columnaIndex 30
        $objPHPExcel->getActiveSheet()->setCellValue('K22', "='3'!" . $columnaIndex . "30");
        //k25 =columnaIndex 31
        $objPHPExcel->getActiveSheet()->setCellValue('K25', "='3'!" . $columnaIndex . "31");
        $objPHPExcel->getActiveSheet()->setCellValue('K30', "='3'!" . $CobroCell);

        for ($row = 36; $row <= 39; $row++) {
            for ($col = 1; $col <= 12; $col++) {
                $cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $objPHPExcel->getActiveSheet()->setCellValue($cell, ''); // Establecer el valor de la celda como vacío
                $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray(array()); // Eliminar cualquier estilo aplicado a la celda

            }
        }

        //set column j y k autosize
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        /*foreach query as row starts in row 36 set b as index +1, c as query["Nombre_Comercial"]
        f as query["Cantidad"] g as query["Valor_Unitario"] i as query["costo_total"]/ $query["Cantidad"]
        j as  query["costo_total"] k as query["Valor_Unitario"]*3.7
         */
        $lastRow = 0;
        $InitialColumn = 'C';
        //center i35
        $objPHPExcel->getActiveSheet()->getStyle('I35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //if count query is lower than 3 get sustract 3- count query and set the value to $substract and for each $substract remove border from row 36 to 39
        if (count($query) < 3) {
            $substract = 3 - count($query);
            for ($i = 0; $i < $substract; $i++) {
                $row = 36 + $i + count($query);
                //set not borders from b$row to l$row
                $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_NONE,
                                'color' => array('rgb' => '000000'),
                            ),
                        ),
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle('B39' . ':L39')->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_NONE,
                                'color' => array('rgb' => '000000'),
                            ),
                        ),
                    )
                );
                //set background color to white
                $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                if (count($query) < 3) {
                    $style = $objPHPExcel->getActiveSheet()->getStyle('K' . $row);
                    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $style->getFill()->getStartColor()->setARGB($whiteColor);
                }
            }
            //remove borders from b36 to l39
        }

        for ($index = 0; $index < count($query); $index++) {
            $row = 36 + $index;
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $index + 1);
            //SET FONT BOLD FALSE
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(false);
            //center horizontal
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $query[$index]["Nombre_Comercial"]);
            $objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, "='3'!" . $InitialColumn . 10);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(false);

            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, "='3'!" . $InitialColumn . 8);
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, "='3'!" . $InitialColumn . 46);
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('I' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, "='3'!" . $InitialColumn . 44);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(false);

            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $JCellVal = $objPHPExcel->getActiveSheet()->getCell('J' . $row)->getValue();
            //CENTER TEXT
            $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, "='3'!" . $InitialColumn . 47);
            //set currency format with pen symbol
            //combine cells from C$ROW to e$row
            $objPHPExcel->getActiveSheet()->mergeCells('C' . $row . ':E' . $row);
            $objPHPExcel->getActiveSheet()->mergeCells('G' . $row . ':H' . $row);
            //center text
            $objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //SET CURRRENCY FORMAT WITH DOLLAR SYMBOL

            $objPHPExcel->getActiveSheet()->mergeCells('K' . $row . ':L' . $row);
            $style = $objPHPExcel->getActiveSheet()->getStyle('K' . $row);

            //set letter color to white
            //set background color to green
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($greenColor);
            //set bold false
            $style->getFont()->setBold(false);
            $style->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
            //center text
            $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $columnsToApply = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
            foreach ($columnsToApply as $column) {
                //set font to calibri
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setName('Calibri');
                //set font size to 11
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setSize(11);
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if ($column == 'K') {
                    $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getNumberFormat()->setFormatCode('"S/." #,##0.00_-');
                }
            }
            //set normal weiight
            $InitialColumn++;
            $lastRow = $row;
        };
        //SET K31='3'!$InitialColumn7

        $lastRow++;
        //set b$latsrow values "total"
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $lastRow, "TOTAL");

        //UNMERGE C D E AND MERGE B TO E

        if (count($query) < 3) {
            $objPHPExcel->getActiveSheet()->unmergeCells('C' . $lastRow . ':E' . $lastRow);
            $objPHPExcel->getActiveSheet()->mergeCells('B' . $lastRow . ':E' . $lastRow);

        } else {
            $objPHPExcel->getActiveSheet()->mergeCells('B' . $lastRow . ':E' . $lastRow);

        }

        //SET BORDER TO B$lastrow to e$lastrow
        $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow . ':E' . $lastRow)->applyFromArray($borders);

        //center text
        $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //VERTICAL CENTER
        $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //set row height
        $objPHPExcel->getActiveSheet()->getRowDimension($lastRow)->setRowHeight(40);
        //set bold true
        $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow)->getFont()->setBold(true);
        //set f$lastrow =sum(f37:f$lastrow-1)
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $lastRow, "=SUM(F36:F" . ($lastRow - 1) . ")");
        //apply borders
        $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->applyFromArray($borders);
        //set bold true and set dollar format
        $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getFont()->setBold(true);
        //center text
        $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //set f$lastrow =sum(j37:j$lastrow-1)
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $lastRow, "=SUM(J36:J" . ($lastRow - 1) . ")");
        //apply borders
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->applyFromArray($borders);
        //set bold true and set dollar format
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        //center text
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle('B36:L' . $row)->applyFromArray($borders);

        // $objPHPExcel->getActiveSheet()->getStyle('B38:L38')->applyFromArray(array(
        //     'borders' => array(
        //         'allborders' => array(
        //             'style' => PHPExcel_Style_Border::BORDER_NONE,
        //             'color' => array('rgb' => '000000')
        //         ),
        //     ),
        // ));

        //set bold false from b36 to k$row

        $cotizationDetails = $this->db->select('*')
            ->from($this->table_cotizacion_detalles)
            ->where('ID_Cotizacion', $ID_Cotizacion)
            ->get()
            ->result_array();
        //set antidumping
        $antiDumping = 0;
        foreach ($query as $row) {
            $antiDumping += $row["antidumping"];
        }
        $sheet = $objPHPExcel->getActiveSheet();

        // Definir la celda y la fila que se va a evaluar
        $cellToCheck = 'I22';
        $rowToCheck = 23;

        // Obtener el valor de la celda

        // Verificar si se cumple la condición
        if ($antiDumping != 0) {
            // Insertar una nueva fila en la posición 22
            // $objPHPExcel->getActiveSheet()->getStyle('K23')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->getActiveSheet()->getStyle('K22')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->getActiveSheet()->getStyle('K24')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->getActiveSheet()->getStyle('K25')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->insertNewRowBefore($rowToCheck, 1);

            // Opcional: Puedes rellenar la nueva fila con datos si es necesario
            $newRowIndex = $rowToCheck;
            $sheet->setCellValue('B' . $newRowIndex, "ANTIDUMPING");
            $sheet->setCellValue('K' . $newRowIndex, $antiDumping);
            //set currency format with dollar symbol

            //set b$NewRowIndex to l$NewRowIndex    background yellow
            $style = $sheet->getStyle('B' . $newRowIndex . ':L' . $newRowIndex);
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($yellowColor);
            // Ajusta según tus necesidades
            $objPHPExcel->getActiveSheet()->setCellValue('K24', "=SUM(K20:K23)");

        } else {
        }

        // Set the values of the cells in the Excel sheet payroll
        $objPHPExcel->getActiveSheet()->setCellValue('C8', $cotizationDetails[0]['Nombres']);
        $objPHPExcel->getActiveSheet()->setCellValue('C9', $cotizationDetails[0]['Apellidos']);
        $objPHPExcel->getActiveSheet()->setCellValue('C10', $cotizationDetails[0]['DNI']);
        $objPHPExcel->getActiveSheet()->setCellValue('C11', $cotizationDetails[0]['Telefono']);
        $objPHPExcel->getActiveSheet()->setCellValue('J9', $query[0]["peso_total"] >= 1000 ? $query[0]["peso_total"] / 1000 . " Tn" : $query[0]["peso_total"] . " Kg");
        $objPHPExcel->getActiveSheet()->setCellValue('J11', $query[0]["CBM_Total"] . " m3");
        $objPHPExcel->getActiveSheet()->setCellValue('I10', "QTY PROVEEDORES");
        $objPHPExcel->getActiveSheet()->setCellValue('I11', "CBM");

        //set number format
        $objPHPExcel->getActiveSheet()->getStyle('J9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        //SET COLUMN I AUTO SIZE
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->setCellValue('K10', $query[0]["count_proveedores"]);
        $objPHPExcel->getActiveSheet()->setCellValue('J10', "");
        //APPPLY NUMBER FORMAT TO K10
        $objPHPExcel->getActiveSheet()->getStyle('K10')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        $objPHPExcel->getActiveSheet()->setCellValue('L10', "");

        $objPHPExcel->getActiveSheet()->setCellValue('F11', $query[0]["tipo_cliente"]);

        //select * from table_tarifas where id_tipo_cliente=$ID_Tipo_Cliente and updated_at is null

        //select
        //remove page 2
        $objPHPExcel->removeSheetByIndex(1);
        //set sheet 3 title to 2
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setTitle('2');
        return $objPHPExcel;
    }
    public function generateMassiveExcelPayrolls($objPHPExcel,$tarifas)
    {
        //init more memory
        ini_set('memory_limit', '1024M');
        $this->load->library('PHPExcel');
        $this->load->library('zip');
        // Create a new PHPExcel object
        $templatePath = 'assets/downloads/Boleta_Template.xlsx';
        $data = $this->getMassiveExcelData($objPHPExcel); 
        // Assuming this gets the data for all rows
        // Iterate through the data, generate an Excel file for each row, add it to a ZIP file
      
        foreach ($data as $key => $value) {
            $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
            $objPHPExcel = $this->getFinalCotizacionExcel($objPHPExcel, $value,$tarifas);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $excelFileName = 'Boleta_' . $value['cliente']['nombre'] . '.xlsx';
            $excelFilePath = 'assets/downloads/' . $excelFileName;
            $objWriter->save($excelFilePath);
            $this->zip->read_file($excelFilePath, $excelFileName); // Add the Excel file to the ZIP
            unlink($excelFilePath); // Remove the Excel file after adding it to the ZIP
        }

        // Save the ZIP file
        $zipFileName = 'Boletas.zip';
        $zipFilePath = 'assets/downloads/' . $zipFileName;
        $this->zip->archive($zipFilePath);
        return $zipFilePath;
    }

    public function getFinalCotizacionExcel($objPHPExcel, $data,$tarifas)
    {
        $newSheet = $objPHPExcel->createSheet();
        $newSheet->setTitle('3');
        /**Base Styles */
        $grayColor = 'F8F9F9';
        $blueColor = '1F618D';
        $yellowColor = 'FFFF33';
        $greenColor = "009999";
        $whiteColor = "FFFFFF";
        $borders = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        /**Apply Tributes Calc Zones Rows Title */
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B3:G3');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B3', 'Calculo de Tributos');
        $style = $objPHPExcel->getActiveSheet()->getStyle('B3');
        $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB($grayColor);
        $objPHPExcel->getActiveSheet()->getStyle('B3:G3')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B5', 'Nombres');
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFill()->getStartColor()->setARGB($blueColor);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B6', 'Peso');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B7', "Valor CBM");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B8', 'Valor Unitario');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B9', 'Valoracion');
        $objPHPExcel->getActiveSheet()->getStyle('B9')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B9')->getFill()->getStartColor()->setARGB($yellowColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B10', 'Cantidad');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B11', 'Valor FOB');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B12', 'Valor FOB Valoracion');
        $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->getStartColor()->setARGB($yellowColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B13', 'Distribucion %');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B14', 'Flete');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B15', 'Valor CFR');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B16', 'CFR Valorizado');
        $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->getStartColor()->setARGB($yellowColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B17', 'Seguro');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B18', 'Valor CIF');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B19', 'CIF Valorizado');
        $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->getStartColor()->setARGB($yellowColor);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $InitialColumn = 'C';
        $totalRows = 0;
        $cbmTotal = 0;
        $pesoTotal = 0;
        //first iterate for tributes zone, set values and apply styles to cells
        foreach ($data['cliente']['productos'] as $producto) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '5', $producto["nombre"]);
            //APLY BACKGROUND COLOR BLUE AND LETTERS WHITE
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->getStartColor()->setARGB($blueColor);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', 0);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', 0);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '8', $producto["precio_unitario"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '9', $producto["valoracion"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', $producto["cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', "=" . $InitialColumn . "8*" . $InitialColumn . "10");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '12', "=" . $InitialColumn . "10*" . $InitialColumn . "9");
            //set format currency with dollar symbol $InitialColumn.8,9,11,12
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '8')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //set auto size for columns
            $InitialColumn++;
            $totalRows++;
            $cbmTotal += $producto['cbm'];
            $pesoTotal += $producto['peso'];

        }
        $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);
        //get tarifas from db
        $tipoCliente =trim($data['cliente']["tipo"]);
        //SET CURRENT SHEET TO TITLE TO $tipoCliente
        
        $tarifas = json_decode($tarifas, true);
        //filter tarifas if tipoCliente is NUEVO then filter by id_tipo_cliente=1 els if tipoCliente is ANTIGUO filter by id_tipo_cliente=2 
        $tarifas = array_filter($tarifas, function ($tarifa) use ($tipoCliente) {
            if ($tipoCliente == "NUEVO") {
                return $tarifa["id_tipo_cliente"] == 1;
            } else if($tipoCliente == "ANTIGUO") {
                return $tarifa["id_tipo_cliente"] == 2;
            }else if($tipoCliente == "SOCIO"){
                return $tarifa["id_tipo_cliente"] == 3;
            }
        });
        $TarifasStartColumn = chr(ord($InitialColumn) + 5);
        $TarifasStartColumn2 = chr(ord($InitialColumn) + 6);
        $TarifasStartColumn3 = chr(ord($InitialColumn) + 7);
        $TarifasStartColumn4 = chr(ord($InitialColumn) + 8);
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn2)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn3)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($TarifasStartColumn4)->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . '8:' . $TarifasStartColumn4 . '8');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . '8', $tipoCliente);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . '8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . '8')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . '9', "CBM Limite Inferior");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn2 . '9', "CBM Limite Superior");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . '9', "Tarifa");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn4 . '9', "Tipo de Tarifa");

        $initialRow = 10;
        $tarifaCell = "";
        $tipoTarifa = "";
        $tarifaValue=0;
        //fill tarifas zone
        foreach ($tarifas as $tarifa) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, $tarifa["limite_inf"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn2 . $initialRow, floatval($tarifa["limite_sup"]));
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, $tarifa["tarifa"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn4 . $initialRow, $tarifa["id_tipo_tarifa"] == 1 ? "Estandar" : "No Estandar");
            //set currency format with dollar symbol
            $cbmTotal = round($cbmTotal,2);
            $limiteInf=round($tarifa["limite_inf"],2);
            $limiteSup=round($tarifa["limite_sup"],2);
            if ( $cbmTotal >= $limiteInf && $cbmTotal <= $limiteSup) {
                $tarifaCell = $TarifasStartColumn3 . $initialRow;
                $tipoTarifa = $TarifasStartColumn4 . $initialRow;
            }
            $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $initialRow++;
        }
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . '8:' . $TarifasStartColumn4 . ($initialRow - 1))->applyFromArray($borders);

        $initialRow++;
        //merge cells from $TarifasStartColumn2 to $TarifasStartColumn
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, "CBM");
        ////set center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getFont()->setBold(true);
        //merge cells from $TarifasStartColumn3 to $TarifasStartColumn4
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, "Cobro");
        //set center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getFont()->setBold(true);
        $initialRow++;
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow,
            "=ROUND(MAX(" . $pesoTotal / 1000 . "," . $cbmTotal . "),2)");
        //center horizontal
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //IF TIPO TARIFA IS Estandar set the value to $tarifaCell else set the TarifaCell*cbmTotal
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
            $TarifasStartColumn3 . $initialRow,
            "=IF(" . $tipoTarifa . "=\"Estandar\"," . $tarifaCell . ",ROUNDUP(" . $tarifaCell . "*" . ($TarifasStartColumn . $initialRow) . ", 0))"
        );
        //center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getFont()->setBold(true);
        //set currency format with dollar symbol
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $initialRow++;

        //fill cobro zone
        $CobroCell = $TarifasStartColumn3 . ($initialRow - 1);
        $initialRow++;
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, "Flete(60%)");
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, "Destino(40%)");
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $initialRow++;
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn . $initialRow . ':' . $TarifasStartColumn2 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells($TarifasStartColumn3 . $initialRow . ':' . $TarifasStartColumn4 . $initialRow);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, "=ROUNDUP(" . $CobroCell . "*0.6,2)");
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getFont()->setBold(true);
        $FleteCell = $TarifasStartColumn . $initialRow;
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, "=ROUNDUP(" . $CobroCell . "*0.4,2)");
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getFont()->setBold(true);
        $DestinoCell = $TarifasStartColumn3 . $initialRow;
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . ($initialRow - 1) . ":" . $TarifasStartColumn4 . $initialRow)->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . ($initialRow - 4) . ":" . $TarifasStartColumn4 . ($initialRow - 3))->applyFromArray($borders);
        $initialRow++;
        //create remaining zones and apply styles
        $InitialColumnLetter = chr(ord($InitialColumn) - 1);

        $objPHPExcel->getActiveSheet()->getStyle('B5:' . $InitialColumn . '19')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B28:' . $InitialColumn . '32')->applyFromArray($borders);

        $objPHPExcel->getActiveSheet()->getStyle('B40:' . $InitialColumn . '40')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B43:' . $InitialColumn . '47')->applyFromArray($borders);

        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->getStartColor()->setARGB($blueColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '5', "Total");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', $pesoTotal > 1000 ? round($pesoTotal / 1000, 2) : $pesoTotal);
        // IF initial column 6>= 1000 set tn format else set kg format
        if ($pesoTotal > 1000) {
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getNumberFormat()->setFormatCode('0.00" tn"');
        } else {
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getNumberFormat()->setFormatCode('0.00" Kg"');
        }
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', $cbmTotal);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '7')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', "=SUM(C10:" . $InitialColumnLetter . "10)");

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', "=SUM(C11:" . $InitialColumnLetter . "11)");
        $VFOBCell = $InitialColumn . '11';
        $CBMTotal = $InitialColumn . "7";
        $antidumpingSum = 0;
        $InitialColumn = 'C';

        //second iteration  for each product and set values and apply styles
        foreach ($data['cliente']['productos'] as $producto) {
            //$INITIALCOLUMN13 =ROUND($VFOBCell/$InitialColumn.'11') TO PERCENTAGE;
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '13', "=" . $InitialColumn . '11/' . $VFOBCell);
            $distroCell = $InitialColumn . '13';
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '25', $tarifaValue);

            // return $objPHPExcel;

            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '13')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            //$initialcolumn14=round($FleteCell*$InitialColumn.'13',2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', "=ROUNDUP(" . $FleteCell . '*' . $InitialColumn . '13,2)');
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //$initialcolumn15=roundup( $initialcolumn11+$initialcolumn14,2)

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '15', "=ROUNDUP(" . $InitialColumn . '11+' . $InitialColumn . '14,2)');
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $cfrCell = $InitialColumn . '15';
            //$initialcolumn15=roundup( $initialcolumn12+$initialcolumn14,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '16', "=ROUNDUP(" . $InitialColumn . '12+' . $InitialColumn . '14,2)');
            $cfrvCell = $InitialColumn . '16';

            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $seguroCell = $InitialColumn . '17';
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //IF COBROCELL IS GREATER THAN 5000 SET THE VALUE TO $initialcolumn17  TO roundup100/ distroCell ELSE SET roundup50/distroCell
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '17', "=IF(" . $CobroCell . ">5000,ROUND(100*" . $distroCell . ",2),ROUND(50*" . $distroCell . ",2))");
            //initial18 is roundup($cfrCell+$seguroCell,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '18', "=ROUNDUP(" . $cfrCell . '+' . $seguroCell . ",2)");
            //initial19 is roundup($cfrvCell+$seguroCell,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '19', "=ROUNDUP(" . $cfrvCell . '+' . $seguroCell . ",2)");

            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '26', $producto["antidumping"]);
            $antidumpingSum += $producto["antidumping"];
            //set currency format with $ symbol
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '26')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', $producto["ad_valorem"] / 100);
            //set porcentage format
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            //set text color red
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
            $AdValoremCell = $InitialColumn . '28';
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '28',
                "=MAX(" . $InitialColumn . "19," . $InitialColumn . "18)*" . $InitialColumn . "27"
            );
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '28')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "$" . $row["igv_value"]);
            //set initialcolumn29 = $row["igv"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "=" . (16 / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+" . $AdValoremCell . ")");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '29')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "$" . $row["ipm_value"]);
            //set initialcolumn30 = $row["ipm"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "=" . (2 / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+" . $AdValoremCell . ")");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '30')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "$" . $row["percepcion_value"]);
            //set initialcolumn31 = $row["percepcion"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '31',
                "=" . ($producto['percepcion']) . "*(MAX(" . $InitialColumn . '18,' . $InitialColumn . '19) +' . $InitialColumn . '28+' . $InitialColumn . '29+' . $InitialColumn . '30)'
            );
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '31')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $sum = "=SUM(" . $InitialColumn . "28:" . $InitialColumn . "31)";
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', $sum);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '32')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', "=" . $CBMTotal . "*0.4" . "*" . $InitialColumn . "13");
            //Set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '40')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', $producto["nombre"]);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "$" . $producto["Total_Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '45', $producto["cantidad"]);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '46', "$" . round($producto["Total_Cantidad"] / $producto["Cantidad"], 2));
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '47', 'S/.' . round(($producto["Total_Cantidad"] / $producto["Cantidad"]) * 3.7, 2));
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', $producto["Nombre_Comercial"]);
            //initial column 44=sum(initialcolumn16+initialcolumn40+initialcolumn32)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '44',
                "=SUM(" . $InitialColumn . "15," . $InitialColumn . "40," . $InitialColumn . "32," . $InitialColumn . "26)"
            );
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '45', $producto["cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '46', "=ROUND(SUM(" . $InitialColumn . "44/" . $InitialColumn . "45),2)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '46')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //initial column 47=S/.initialcolumn46*3.7
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '47', "=ROUND(" . $InitialColumn . "46*3.7,2)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '47')->getNumberFormat()->setFormatCode('"S/." #,##0.00_-');
            $InitialColumn++;

        }

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', "=" . $CobroCell);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '12', "=SUM(C12:" . $InitialColumnLetter . "12)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', "=SUM(C14:" . $InitialColumnLetter . "14)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '15', "=SUM(C15:" . $InitialColumnLetter . "15)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '16', "=SUM(C16:" . $InitialColumnLetter . "16)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '17', "=SUM(C17:" . $InitialColumnLetter . "17)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '18', "=SUM(C18:" . $InitialColumnLetter . "18)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '19', "=SUM(C19:" . $InitialColumnLetter . "19)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getFont()->setBold(true);

        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B23:E23');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B23', 'Tributos Aplicables');
        $style = $objPHPExcel->getActiveSheet()->getStyle('B23');
        $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB($grayColor);
        $objPHPExcel->getActiveSheet()->getStyle('B23:E23')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B26', 'ANTIDUMPING');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B28', 'AD VALOREM');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B29', 'IGV');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B30', 'IPM');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B31', 'PERCEPCION');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B32', 'TOTAL');
        //waos
        $objPHPExcel->getActiveSheet()->getStyle('B26:' . $InitialColumn . '26')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('C27:' . $InitialColumn . '27')->applyFromArray($borders);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '26', "=SUM(C26:" . $InitialColumnLetter . "26)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '26')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        // Verificar si el valor es numérico

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', "=SUM(C27:" . $InitialColumnLetter . "27)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '28', "=SUM(C28:" . $InitialColumnLetter . "28)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '28')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "=SUM(C29:" . $InitialColumnLetter . "29)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '29')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "=SUM(C30:" . $InitialColumnLetter . "30)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '30')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "=SUM(C31:" . $InitialColumnLetter . "31)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '31')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', "=SUM($InitialColumn" . "28:" . $InitialColumn . "31)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '32')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        //Costos Destinos
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B37:E37');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B37', 'Costos Destinos');
        $style = $objPHPExcel->getActiveSheet()->getStyle('B37');
        $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB($grayColor);
        $objPHPExcel->getActiveSheet()->getStyle('B37:E37')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B37')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B40', 'ITEM');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', "=SUM(C40:" . $InitialColumnLetter . "40)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '40')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B41:E41');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B43', 'ITEM');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B44', 'COSTO TOTAL');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B45', 'CANTIDAD');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B46', 'COSTO UNITARIO');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B47', 'COSTO SOLES');

        //a
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "=SUM(C44" . ":" . $InitialColumnLetter . "44)");
        $productsCount = count($data['cliente']['productos']);
        $ColumndIndex = PHPExcel_Cell::stringFromColumnIndex($productsCount + 1);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J20', "=MAX('3'!C27:" . $ColumndIndex . "27)");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', "Total");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "=SUM(C44:" . $InitialColumnLetter . "44)");
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        $columnaIndex = PHPExcel_Cell::stringFromColumnIndex($productsCount + 2);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('K14', "='3'!" . $columnaIndex . "11");

        // Construir la fórmula para sumar los valores de las celdas en las columnas 14 y 17
        $formula = "='3'!" . $columnaIndex . "14 + '3'!" . $columnaIndex . "17";

        // Establecer la fórmula en la celda K14
        $objPHPExcel->getActiveSheet()->setCellValue('K15', $formula);
        //k20 =columnaIndex 28
        $objPHPExcel->getActiveSheet()->setCellValue('K20', "='3'!" . $columnaIndex . "28");
        //k21 =columnaIndex 29
        $objPHPExcel->getActiveSheet()->setCellValue('K21', "='3'!" . $columnaIndex . "29");
        //k22 =columnaIndex 30
        $objPHPExcel->getActiveSheet()->setCellValue('K22', "='3'!" . $columnaIndex . "30");
        //k25 =columnaIndex 31
        $objPHPExcel->getActiveSheet()->setCellValue('K25', "='3'!" . $columnaIndex . "31");

        //k30 =$query[0]["Flete"]/$query[0]["Distribucion"]
        $objPHPExcel->getActiveSheet()->setCellValue('K30', "='3'!" . $CobroCell);

        for ($row = 36; $row <= 39; $row++) {
            for ($col = 1; $col <= 12; $col++) {
                $cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $objPHPExcel->getActiveSheet()->setCellValue($cell, ''); // Establecer el valor de la celda como vacío
                $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray(array()); // Eliminar cualquier estilo aplicado a la celda

            }
        }
        //set column j y k autosize
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        /*foreach query as row starts in row 36 set b as index +1, c as query["Nombre_Comercial"]
        f as query["Cantidad"] g as query["Valor_Unitario"] i as query["costo_total"]/ $query["Cantidad"]
        j as  query["costo_total"] k as query["Valor_Unitario"]*3.7
         */

        $lastRow = 0;
        $InitialColumn = 'C';
        //if count query is lower than 3 get sustract 3- count query and set the value to $substract and for each $substract remove border from row 36 to 39
        if ($productsCount < 3) {
            $substract = 3 - $productsCount;
            for ($i = 0; $i < $substract; $i++) {
                $row = 36 + $i + $productsCount;
                //set not borders from b$row to l$row
                $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->applyFromArray(array());
            }
            //remove borders from b36 to l39
        }
        for ($index = 0; $index < $productsCount; $index++) {
            $row = 36 + $index;
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $index + 1);
            //SET FONT BOLD FALSE
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(false);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $data['cliente']['productos'][$index]["nombre"]);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, "='3'!" . $InitialColumn . 10);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(false);
            //center text
            $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, "='3'!" . $InitialColumn . 8);
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, "='3'!" . $InitialColumn . 46);
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('I' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, "='3'!" . $InitialColumn . 44);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(false);

            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $JCellVal = $objPHPExcel->getActiveSheet()->getCell('J' . $row)->getValue();
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, "='3'!" . $InitialColumn . 47);
            //set currency format with pen symbol
            //combine cells from C$ROW to e$row
            $objPHPExcel->getActiveSheet()->mergeCells('C' . $row . ':E' . $row);
            $objPHPExcel->getActiveSheet()->mergeCells('G' . $row . ':H' . $row);
            //SET CURRRENCY FORMAT WITH DOLLAR SYMBOL

            $objPHPExcel->getActiveSheet()->mergeCells('K' . $row . ':L' . $row);
            //copy currency format from k$row-1 to k$row
            $style = $objPHPExcel->getActiveSheet()->getStyle('K' . $row);
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($greenColor);
            //set letter color to white
            $style->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
            //center text
            //set normal weight
            $columnsToApply = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
            //apply borders from b$row to l$row
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->applyFromArray($borders);
            //for each column in columnsToApply apply style center and auto size
            foreach ($columnsToApply as $column) {
                //set font to calibri
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setName('Calibri');
                //set font size to 11
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setSize(11);
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if ($column == 'K') {
                    $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getNumberFormat()->setFormatCode('"S/." #,##0.00_-');
                }
            }
            $InitialColumn++;
            $lastRow = $row;
        };

        $lastRow++;
        //set b$latsrow values "total"
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $lastRow, "TOTAL");
        //set bold true
        $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow)->getFont()->setBold(true);
        //set f$lastrow =sum(f37:f$lastrow-1)
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $lastRow, "=SUM(F36:F" . ($lastRow - 1) . ")");
        //set bold true and center text
        $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set f$lastrow =sum(j37:j$lastrow-1)
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $lastRow, "=SUM(J36:J" . ($lastRow - 1) . ")");
        //set bold true and set dollar format
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        //center text
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set borders  from b$lastrow to l$lastrow
        $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow . ':L' . $lastRow)->applyFromArray(array());
        //set without borders  from b36 to k$row

        // Definir la celda y la fila que se va a evaluar
        $cellToCheck = 'I22';
        $rowToCheck = 23;
        $sheet = $objPHPExcel->getActiveSheet();

        // Obtener el valor de la celda

        // Verificar si se cumple la condición
        if ($antidumpingSum != 0) {
            // Insertar una nueva fila en la posición 22
            // $objPHPExcel->getActiveSheet()->getStyle('K23')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->getActiveSheet()->getStyle('K22')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->getActiveSheet()->getStyle('K24')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->getActiveSheet()->getStyle('K25')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->insertNewRowBefore($rowToCheck, 1);

            // Opcional: Puedes rellenar la nueva fila con datos si es necesario
            $newRowIndex = $rowToCheck;
            $sheet->setCellValue('B' . $newRowIndex, "ANTIDUMPING");
            $sheet->setCellValue('K' . $newRowIndex, $antidumpingSum);
            //set currency format with dollar symbol

            //set b$NewRowIndex to l$NewRowIndex    background yellow
            $style = $sheet->getStyle('B' . $newRowIndex . ':L' . $newRowIndex);
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($yellowColor);
            // Ajusta según tus necesidades
            $objPHPExcel->getActiveSheet()->setCellValue('K24', "=SUM(K20:K23)");

        } else {
        }

        //merge c8:c9
        $objPHPExcel->getActiveSheet()->mergeCells('C8:C9');
        //center vertically and horizontally
        $objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue('C8', $data['cliente']['nombre']);
        $objPHPExcel->getActiveSheet()->setCellValue('C10', "74645561");
        $objPHPExcel->getActiveSheet()->setCellValue('C11', "912705923");
        $objPHPExcel->getActiveSheet()->setCellValue('J9', $pesoTotal >= 1000 ? $pesoTotal / 1000 . " Tn" : $pesoTotal . " Kg");
        $objPHPExcel->getActiveSheet()->setCellValue('J11', $cbmTotal . " m3");
        //   $objPHPExcel->getActiveSheet()->setCellValue('I10', "QTY PROVEEDORES");
        $objPHPExcel->getActiveSheet()->setCellValue('I11', "CBM");

        //set number format
        $objPHPExcel->getActiveSheet()->getStyle('J9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        //SET COLUMN I AUTO SIZE
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

        //   $objPHPExcel->getActiveSheet()->setCellValue('K10', $query[0]["count_proveedores"]);
        $objPHPExcel->getActiveSheet()->setCellValue('J10', "");
        //APPPLY NUMBER FORMAT TO K10
        $objPHPExcel->getActiveSheet()->getStyle('K10')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        $objPHPExcel->getActiveSheet()->setCellValue('L10', "");

        $objPHPExcel->getActiveSheet()->setCellValue('F11', $tipoCliente);
        if ($productsCount < 3) {
            //remove borders from b36 to l39
            $objPHPExcel->getActiveSheet()->getStyle('B39:L39')->applyFromArray(array());
        }
        //select * from table_tarifas where id_tipo_cliente=$ID_Tipo_Cliente and updated_at is null

        //select
        //remove page 2
        $objPHPExcel->removeSheetByIndex(1);
        //set sheet 3 title to 2
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setTitle('2');

        return $objPHPExcel;
    }

    public function getMassiveExcelData($objPHPExcel)
    {
        //PHPExcel_Cell::extractAllCellReferencesInRange($mergedRange);
        $this->load->library('PHPExcel');

        // Create a new PHPExcel object
        $templatePath = 'assets/downloads/Massive_Payroll.xlsx';
        $excel = $objPHPExcel;
        $worksheet = $excel->getActiveSheet();

        // Obtener los rangos de celdas mergeadas
        $mergedCells = $worksheet->getMergeCells();
        $columnClientes = [];
        $columnTipoClientes = [];
        $columnDNIClientes = [];
        $columnPhoneClientes = [];
        $columnProductos = [];
        $columnCantidad = [];
        $columnPrecioUnitario = [];
        $columnAntidumping = [];
        $columnValoracion = [];
        $columnAdValorem = [];
        $columnPercepcion = [];
        $columnPeso = [];
        $columnCBM = [];

        // Filtrar y agregar los rangos mergeados en las columnas correspondientes a sus respectivos arrays
        foreach ($mergedCells as $mergedRange) {
            if (preg_match('/^A\d+:A\d+$/', $mergedRange)) {
                $columnClientes[] = $mergedRange;
            }
            if (preg_match('/^B\d+:B\d+$/', $mergedRange)) {
                $columnTipoClientes[] = $mergedRange;
            }
            if (preg_match('/^C\d+:C\d+$/', $mergedRange)) {
                $columnDNIClientes[] = $mergedRange;
            }
            if (preg_match('/^D\d+:D\d+$/', $mergedRange)) {
                $columnPhoneClientes[] = $mergedRange;
            }
            if (preg_match('/^F\d+:F\d+$/', $mergedRange)) {
                $columnProductos[] = $mergedRange;
            }
            if (preg_match('/^G\d+:G\d+$/', $mergedRange)) {
                $columnCantidad[] = $mergedRange;
            }
            if (preg_match('/^H\d+:H\d+$/', $mergedRange)) {
                $columnPrecioUnitario[] = $mergedRange;
            }
            if (preg_match('/^I\d+:I\d+$/', $mergedRange)) {
                $columnAntidumping[] = $mergedRange;
            }
            if (preg_match('/^J\d+:J\d+$/', $mergedRange)) {
                $columnValoracion[] = $mergedRange;
            }
            if (preg_match('/^K\d+:K\d+$/', $mergedRange)) {
                $columnAdValorem[] = $mergedRange;
            }
            if (preg_match('/^L\d+:L\d+$/', $mergedRange)) {
                $columnPercepcion[] = $mergedRange;
            }
            if (preg_match('/^M\d+:M\d+$/', $mergedRange)) {
                $columnPeso[] = $mergedRange;
            }
            if (preg_match('/^N\d+:N\d+$/', $mergedRange)) {
                $columnCBM[] = $mergedRange;
            }
        }
        // Ordenar los rangos en función del número de fila inicial para cada columna
        usort($columnClientes, function ($a, $b) {
            preg_match('/^A(\d+):A\d+$/', $a, $matchesA);
            preg_match('/^A(\d+):A\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnTipoClientes, function ($a, $b) {
            preg_match('/^B(\d+):B\d+$/', $a, $matchesA);
            preg_match('/^B(\d+):B\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnDNIClientes, function ($a, $b) {
            preg_match('/^C(\d+):C\d+$/', $a, $matchesA);
            preg_match('/^C(\d+):C\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnPhoneClientes, function ($a, $b) {
            preg_match('/^D(\d+):D\d+$/', $a, $matchesA);
            preg_match('/^D(\d+):D\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnProductos, function ($a, $b) {
            preg_match('/^F(\d+):F\d+$/', $a, $matchesA);
            preg_match('/^F(\d+):F\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });

        usort($columnCantidad, function ($a, $b) {
            preg_match('/^G(\d+):G\d+$/', $a, $matchesA);
            preg_match('/^G(\d+):G\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });

        usort($columnPrecioUnitario, function ($a, $b) {
            preg_match('/^H(\d+):H\d+$/', $a, $matchesA);
            preg_match('/^H(\d+):H\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnAntidumping, function ($a, $b) {
            preg_match('/^I(\d+):I\d+$/', $a, $matchesA);
            preg_match('/^I(\d+):I\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnValoracion, function ($a, $b) {
            preg_match('/^J(\d+):J\d+$/', $a, $matchesA);
            preg_match('/^J(\d+):J\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnAdValorem, function ($a, $b) {
            preg_match('/^K(\d+):K\d+$/', $a, $matchesA);
            preg_match('/^K(\d+):K\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnPercepcion, function ($a, $b) {
            preg_match('/^L(\d+):L\d+$/', $a, $matchesA);
            preg_match('/^L(\d+):L\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnPeso, function ($a, $b) {
            preg_match('/^M(\d+):M\d+$/', $a, $matchesA);
            preg_match('/^M(\d+):M\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });
        usort($columnCBM, function ($a, $b) {
            preg_match('/^N(\d+):N\d+$/', $a, $matchesA);
            preg_match('/^N(\d+):N\d+$/', $b, $matchesB);
            return $matchesA[1] - $matchesB[1];
        });

        $clients = [];
        foreach ($columnClientes as $key => $mergedRangeA) {
            // Obtener la primera celda del rango de la columna A
            $rangeA = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeA);
            $firstCellA = $rangeA[0];
            $valueA = $worksheet->getCell($firstCellA)->getValue();
            if ($valueA == null) {
                continue;
            }
            // Obtener el tipo de cliente
            $mergedRangeB = $columnTipoClientes[$key];
            $rangeB = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeB);
            $firstCellB = $rangeB[0];
            $valueB = $worksheet->getCell($firstCellB)->getValue();
            if ($valueB == null) {
                continue;
            }
            $mergedRangeC = $columnDNIClientes[$key];
            $rangeC = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeC);
            $firstCellC = $rangeC[0];
            $valueC = $worksheet->getCell($firstCellC)->getValue();
            
            $mergedRangeD = $columnPhoneClientes[$key];
            $rangeD = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeD);
            $firstCellD = $rangeD[0];
            $valueD = $worksheet->getCell($firstCellD)->getValue();
            if ($valueD == null) {
                continue;
            }
            // Inicializar el array de productos para este cliente
            $productos = [];

            // Obtener los límites del rango de la columna A
            preg_match('/^A(\d+):A(\d+)$/', $mergedRangeA, $matchesA);
            $startRowA = $matchesA[1];
            $endRowA = $matchesA[2];

            foreach ($columnProductos as $key => $mergedRangeF) {
                preg_match('/^F(\d+):F(\d+)$/', $mergedRangeF, $matchesF);
                $startRowF = $matchesF[1];
                $endRowF = $matchesF[2];

                if ($startRowF >= $startRowA && $endRowF <= $endRowA) {
                    $rangeF = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeF);
                    $firstCellF = $rangeF[0];
                    $producto = $worksheet->getCell($firstCellF)->getValue();
                    if ($producto == null) {
                        continue;
                    }
                    // Obtener cantidad, precio unitario, valoración, ad valorem, percepción, peso y CBM
                    $valueG = $valueH = $valueI = $valueJ = $valueK = $valueL = $valueM = $valueN = null;
                    foreach ($columnCantidad as $mergedRangeG) {
                        preg_match('/^G(\d+):G(\d+)$/', $mergedRangeG, $matchesG);
                        $startRowG = $matchesG[1];
                        $endRowG = $matchesG[2];
                        if ($startRowG == $startRowF && $endRowG == $endRowF) {
                            $rangeG = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeG);
                            $firstCellG = $rangeG[0];
                            $valueG = $worksheet->getCell($firstCellG)->getValue();
                            if ($valueG == null) {
                                continue;
                            }
                            break;
                        }
                    }
                    foreach ($columnPrecioUnitario as $mergedRangeH) {
                        preg_match('/^H(\d+):H(\d+)$/', $mergedRangeH, $matchesH);
                        $startRowH = $matchesH[1];
                        $endRowH = $matchesH[2];
                        if ($startRowH == $startRowF && $endRowH == $endRowF) {
                            $rangeH = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeH);
                            $firstCellH = $rangeH[0];
                            $valueH = $worksheet->getCell($firstCellH)->getValue();
                            if ($valueH == null) {
                                continue;
                            }
                            break;
                        }
                    }
                    foreach ($columnAntidumping as $mergedRangeI) {
                        preg_match('/^I(\d+):I(\d+)$/', $mergedRangeI, $matchesI);
                        $startRowI = $matchesI[1];
                        $endRowI = $matchesI[2];
                        if ($startRowI == $startRowF && $endRowI == $endRowF) {
                            $rangeI = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeI);
                            $firstCellI = $rangeI[0];
                            $valueI = $worksheet->getCell($firstCellI)->getValue();
                            if ($valueI == null || $valueI == "-") {
                                $valueI = 0;
                            }
                            break;
                        }
                    }
                    foreach ($columnValoracion as $mergedRangeJ) {
                        preg_match('/^J(\d+):J(\d+)$/', $mergedRangeJ, $matchesJ);
                        $startRowJ = $matchesJ[1];
                        $endRowJ = $matchesJ[2];
                        if ($startRowJ == $startRowF && $endRowJ == $endRowF) {
                            $rangeJ = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeJ);
                            $firstCellJ = $rangeJ[0];
                            $valueJ = $worksheet->getCell($firstCellJ)->getValue();
                            if ($valueJ == null || $valueI == "-") {
                                $valueJ = 0;

                            }
                            break;
                        }
                    }
                    foreach ($columnAdValorem as $mergedRangeK) {
                        preg_match('/^K(\d+):K(\d+)$/', $mergedRangeK, $matchesK);
                        $startRowK = $matchesK[1];
                        $endRowK = $matchesK[2];
                        if ($startRowK == $startRowF && $endRowK == $endRowF) {
                            $rangeK = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeK);
                            $firstCellK = $rangeK[0];
                            $valueK = $worksheet->getCell($firstCellK)->getValue();
                            if ($valueK == null || $valueI == "-") {
                                $valueK = 0;
                            }
                            break;
                        }
                    }
                    foreach ($columnPercepcion as $mergedRangeL) {
                        preg_match('/^L(\d+):L(\d+)$/', $mergedRangeL, $matchesL);
                        $startRowL = $matchesL[1];
                        $endRowL = $matchesL[2];
                        if ($startRowL == $startRowF && $endRowL == $endRowF) {
                            $rangeL = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeL);
                            $firstCellL = $rangeL[0];
                            $valueL = $worksheet->getCell($firstCellL)->getValue();
                            if ($valueL == null || $valueI == "-") {
                                $valueL = 0.035;
                            }
                            break;
                        }
                    }
                    foreach ($columnPeso as $mergedRangeM) {
                        preg_match('/^M(\d+):M(\d+)$/', $mergedRangeM, $matchesM);
                        $startRowM = $matchesM[1];
                        $endRowM = $matchesM[2];
                        if ($startRowM == $startRowF && $endRowM == $endRowF) {
                            $rangeM = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeM);
                            $firstCellM = $rangeM[0];
                            $valueM = $worksheet->getCell($firstCellM)->getValue();
                            if ($valueM == null || $valueI == "-") {
                                $valueM = 0;
                            }
                            break;
                        }
                    }
                    foreach ($columnCBM as $mergedRangeN) {
                        preg_match('/^N(\d+):N(\d+)$/', $mergedRangeN, $matchesN);
                        $startRowN = $matchesN[1];
                        $endRowN = $matchesN[2];
                        if ($startRowN == $startRowF && $endRowN == $endRowF) {
                            $rangeN = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRangeN);
                            $firstCellN = $rangeN[0];
                            $valueN = $worksheet->getCell($firstCellN)->getValue();
                            if ($valueN == null || $valueI == "-") {
                                continue;
                            }
                            break;
                        }
                    }
                    if ($valueG != null && $valueH != null && $valueN != null) {
                        $productos[] = [
                            'nombre' => $producto,
                            'cantidad' => $valueG,
                            'precio_unitario' => $valueH,
                            'antidumping' => $valueI,
                            'valoracion' => $valueJ,
                            'ad_valorem' => $valueK,
                            'percepcion' => $valueL,
                            'peso' => $valueM,
                            'cbm' => $valueN,
                        ];
                    }

                }
            }

            // Añadir cliente con sus datos y productos al array de clientes
            $clients[] = [
                'cliente' => [
                    'nombre' => $valueA,
                    'tipo' => $valueB,
                    'dni' => $valueC,
                    'telefono' => $valueD,
                    'productos' => $productos,
                ],
            ];
        }
        return $clients;
    }

    public function get_cotization_tributos($ID_Producto)
    {

        //select value from table tributo and table_key from table_tipo_tributo
        $this->db->select('ccct.value, cctt.table_key');
        $this->db->from($this->table_tributo . ' as ccct');
        $this->db->join($this->table_tipo_tributo . ' as cctt', 'ccct.ID_Tipo_Tributo = cctt.ID_Tipo_Tributo', 'join');
        $this->db->where('ccct.ID_Producto', $ID_Producto);
        $query = $this->db->get();
        //return as asociative array table_key => value
        $asoArray = array();
        foreach ($query->result() as $row) {
            $asoArray[$row->table_key] = $row->value;
        }
        return $asoArray;
        //return as asociative array
    }
    public function updateTipoCliente($data)
    {
        $ID_Cotizacion = $data['ID_Cotizacion'];
        $ID_TipoCliente = $data['Tipo_Cliente'];
        $this->db->where('ID_Cotizacion', $ID_Cotizacion);
        $this->db->update($this->table, array("ID_Tipo_Cliente" => intval($ID_TipoCliente)));

        return array("success" => true);
    }
    public function updateEstadoCotizacion($data)
    {
        $ID_Cotizacion = $data['ID_Cotizacion'];
        $Estado = $data['Estado'];
        $this->db->where('ID_Cotizacion', $ID_Cotizacion);
        $this->db->update($this->table, array("Cotizacion_Status_ID" => $Estado));

        return array("success" => true);
    }
    public function getTarifas(){
        $this->db->select('ID_Tarifa, ID_Tipo_Cliente,tarifa,ID_Tipo_Tarifa');
        $this->db->from($this->table_tarifas);
        //join with table tipo cliente        
        $this->db->where('updated_at is null');
        //order by ID_Tipo_Cliente id_tipo_cliente, limite_inf
        $this->db->order_by('ID_Tipo_Cliente, limite_inf');
        $query = $this->db->get();
        return $query->result();
    }
}
