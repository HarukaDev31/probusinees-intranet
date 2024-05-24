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
    public $table_tarifas="carga_consolidada_cbm_tarifas";
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
        cccdprov.URL_Proforma,
        cccdprov.URL_Packing,
        (select ID_Tipo_Cliente from carga_consolidada_cotizaciones_cabecera where ID_Cotizacion = cccdprov.ID_Cotizacion) as ID_Tipo_Cliente,

        (
            SELECT CONCAT('[', GROUP_CONCAT(
                JSON_OBJECT(
                    'ID_Producto', cccdpro.ID_Producto,
                    'URL_Link', cccdpro.URL_Link,
                    'Url_Image', cccdpro.Url_Image,
                    'Nombre_Comercial', cccdpro.Nombre_Comercial,
                    'Uso', cccdpro.Uso,
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
        // $this->db->join($this->table_producto.' as cccdp2',
        // 'cccdp2.ID_Cotizacion = cccdp.ID_Cotizacion ','join');
        $this->db->where('cccdprov.ID_Cotizacion', $ID_Cotizacion);
        $query = $this->db->get();
        return $query->result();
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
    public function getTypeTributoId($table_key)
    {
        $this->db->select('ID_Tipo_Tributo');
        $this->db->from($this->table_tipo_tributo);
        $this->db->where('table_key', $table_key);
        $query = $this->db->get();
        $result = $query->row();
        return $result->ID_Tipo_Tributo;
    }

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
        $ID_Tipo_Cliente=$query[0]["ID_Tipo_Cliente"];

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
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3. '9', "Tarifa");
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn4. '9', "Tipo de Tarifa");    

        $CBM_Total_C=$query[0]["CBM_Total"];
        $initialRow = 10;
        $tarifaCell="";
        $tipoTarifa="";
        foreach ($tarifas as $tarifa) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, $tarifa["limite_inf"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn2 . $initialRow, floatval($tarifa["limite_sup"]) );
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, $tarifa["tarifa"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn4 . $initialRow, $tarifa["id_tipo_tarifa"]==1?"Estandar":"No Estandar");
            //set currency format with dollar symbol
            if($CBM_Total_C>=$tarifa["limite_inf"] && $CBM_Total_C<=$tarifa["limite_sup"]){
                $tarifaCell=$TarifasStartColumn3 . $initialRow;
                $tipoTarifa=$TarifasStartColumn4 . $initialRow;
            }
            $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $initialRow++;
        }
        //from $TarifasStartColumn8 to $TarifasStartColumn4 15 apply all borders
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . '8:' . $TarifasStartColumn4 . ($initialRow-1))->applyFromArray($borders);

        $initialRow ++;
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

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn . $initialRow, $CBM_Total_C);
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

        $CobroCell=$TarifasStartColumn3 . ($initialRow-1);
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
        $FleteCell=$TarifasStartColumn . $initialRow;
        //set currency format with dollar symbol
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($TarifasStartColumn3 . $initialRow, "=ROUNDUP(" . $CobroCell . "*0.4,2)"); 
        //center horizontal bold true
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getFont()->setBold(true);     
        $DestinoCell=$TarifasStartColumn3 . $initialRow;
        //set currency format with dollar symbol
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn3 . $initialRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . ($initialRow-1).":" . $TarifasStartColumn4 . $initialRow)->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle($TarifasStartColumn . ($initialRow-4).":" . $TarifasStartColumn4 . ($initialRow-3))->applyFromArray($borders);
        $InitialColumn = 'C';
        foreach ($query as $row) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'5', $row["Nombre_Comercial"]);
            //APLY BACKGROUND COLOR BLUE AND LETTERS WHITE
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'5')->getFill()->getStartColor()->setARGB($blueColor);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);


            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'6', 0);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'7', 0);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'8', $row["Valor_Unitario"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'9', $row["Valoracion"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'10', $row["Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'11', "=".$InitialColumn."8*".$InitialColumn."10");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'12', "=".$InitialColumn."10*".$InitialColumn."9");
            //set format currency with dollar symbol $InitialColumn.8,9,11,12
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'8')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'12')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
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

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', $query[0]["Peso_Total"] . " Kg");
        //set text alignment to right
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', $query[0]["Total_CBM"]);
        //set currency format with $ symbol and text bold
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '7')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', "=SUM(C10:" . $InitialColumnLetter . "10)");

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', "=SUM(C11:" . $InitialColumnLetter . "11)");
        $VFOBCell=$InitialColumn . '11';
        $InitialColumn = 'C';
        
        foreach ($query as $row) {
            //$INITIALCOLUMN13 =ROUND($VFOBCell/$InitialColumn.'11') TO PERCENTAGE;
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'13', "=". $InitialColumn.'11/'.$VFOBCell);
            $distroCell=$InitialColumn.'13';
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'13')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            //$initialcolumn14=round($FleteCell*$InitialColumn.'13',2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'14', "=ROUNDUP(".$FleteCell.'*'.$InitialColumn.'13,2)');
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'14')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);   
            //$initialcolumn15=roundup( $initialcolumn11+$initialcolumn14,2) 
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'15', "=ROUNDUP(".$InitialColumn.'11+'.$InitialColumn.'14,2)');
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'15')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $cfrCell=$InitialColumn.'15';
            //$initialcolumn15=roundup( $initialcolumn12+$initialcolumn14,2) 
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'16', "=ROUNDUP(".$InitialColumn.'12+'.$InitialColumn.'14,2)');
            $cfrvCell=$InitialColumn.'16';
            
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'16')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $seguroCell=$InitialColumn.'17';
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'17')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //IF COBROCELL IS GREATER THAN 5000 SET THE VALUE TO $initialcolumn17  TO roundup100/ distroCell ELSE SET roundup50/distroCell
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'17', "=IF(".$CobroCell.">5000,ROUND(100*".$distroCell.",2),ROUND(50*".$distroCell.",2))");
            //initial18 is roundup($cfrCell+$seguroCell,2) 
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'18', "=ROUNDUP(".$cfrCell.'+'.$seguroCell.",2)");
            //initial19 is roundup($cfrvCell+$seguroCell,2)
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn.'19', "=ROUNDUP(".$cfrvCell.'+'.$seguroCell.",2)");

            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn.'19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $InitialColumn++;
            $totalRows++;

        }
        //g7 =cobrocELL
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', "=".$CobroCell);

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
            );            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '28')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "$" . $row["igv_value"]);
            //set initialcolumn29 = $row["igv"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "=" . ($row['igv'] / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+".$AdValoremCell.")");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '29')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "$" . $row["ipm_value"]);
            //set initialcolumn30 = $row["ipm"]*initialcolumn19
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "=" . ($row['ipm'] / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+".$AdValoremCell.")");
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
                "=SUM(".    $InitialColumn."15,". $InitialColumn . "40," . $InitialColumn . "32," . $InitialColumn . "26)"
            );
                        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
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

        //k30 =$query[0]["Flete"]/$query[0]["Distribucion"]
        $objPHPExcel->getActiveSheet()->setCellValue('K30', $query[0]["Servicio"]);

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
        if (count($query) < 3) {
            $substract = 3 - count($query);
            for ($i = 0; $i < $substract; $i++) {
                $row=36+$i+count($query);
                //set not borders from b$row to l$row
                $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->applyFromArray(array());
            }
            //remove borders from b36 to l39
        }
        for ($index = 0; $index < count($query); $index++) {
            $row = 36 + $index;
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $index + 1);
            //SET FONT BOLD FALSE
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(false);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $query[$index]["Nombre_Comercial"]);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, "='3'!".$InitialColumn . 10); 
            $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(false);

            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, "='3'!".$InitialColumn . 8);
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, "='3'!".$InitialColumn . 46);
            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('I' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, "='3'!".$InitialColumn . 44);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(false);

            //set currency format with dollar symbol
            $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $JCellVal = $objPHPExcel->getActiveSheet()->getCell('J' . $row)->getValue();
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, "='3'!".$InitialColumn . 47);
            //set currency format with pen symbol
            //combine cells from C$ROW to e$row
            $objPHPExcel->getActiveSheet()->mergeCells('C' . $row . ':E' . $row);
            $objPHPExcel->getActiveSheet()->mergeCells('G' . $row . ':H' . $row);
            //SET CURRRENCY FORMAT WITH DOLLAR SYMBOL

            $objPHPExcel->getActiveSheet()->mergeCells('K' . $row . ':L' . $row);
            $style = $objPHPExcel->getActiveSheet()->getStyle('K' . $row);
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($greenColor);
            //set letter color to white
            $style->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
            //center text
            $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //set normal weight
            $InitialColumn++;
            $lastRow = $row;
        };
        //SET K31='3'!$InitialColumn7
        
        $lastRow++;
        //set b$latsrow values "total"
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $lastRow, "TOTAL");
        //set bold true
        $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow)->getFont()->setBold(true);
        //set f$lastrow =sum(f37:f$lastrow-1)
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $lastRow, "=SUM(F36:F" . ($lastRow - 1) . ")");
        //set bold true and set dollar format
        $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getFont()->setBold(true);
                //set f$lastrow =sum(j37:j$lastrow-1)
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $lastRow, "=SUM(J36:J" . ($lastRow - 1) . ")");
        //set bold true and set dollar format
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);


        //set borders  from b36 to k$row
        $objPHPExcel->getActiveSheet()->getStyle('B36:L' . $row)->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B38:L38')->applyFromArray(array());

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
            $objPHPExcel->getActiveSheet()->getStyle('K23')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle('K22')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle('K24')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle('K25')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
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
        $objPHPExcel->getActiveSheet()->setCellValue('J9', $query[0]["Peso_Total"]." Kg");
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
        $objPHPExcel->getActiveSheet()->getStyle('K23')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle('K22')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle('K24')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->getStyle('K25')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);



        $objPHPExcel->getActiveSheet()->setCellValue('F11', $query[0]["tipo_cliente"]);
        if(count($query)<3){
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
}
