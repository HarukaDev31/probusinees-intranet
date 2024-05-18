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
FROM carga_consolidada_tipo_cliente AS cctc2) AS Client_Types');
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
            $this->db->close();
            $this->db->initialize();
            $this->db->update($this->table_cotizacion_detalles, array("CBM_Total" => $sum_CBM, "Peso_Total" => $sum_Peso), array("ID_Cotizacion" => $cotizacion[0]['ID_Cotizacion']));
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

        $InitialColumn = 'C';
        $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);

        foreach ($query as $row) {
            $rowExcel = 5;

            foreach ($row as $key => $value) {
                if ($rowExcel == 5) {
                    $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . $rowExcel)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . $rowExcel)->getFill()->getStartColor()->setARGB($blueColor);
                }
                if ($rowExcel == 12 || $rowExcel == 16 || $rowExcel == 19||$rowExcel==9) {
                    $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . $rowExcel)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . $rowExcel)->getFill()->getStartColor()->setARGB($yellowColor);
                }
                if ($rowExcel > 19) {
                    break;
                }

                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . $rowExcel, $value);
                $rowExcel++;
            }
            //set auto size for columns
            $InitialColumn++;
            $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);


        }
        $objPHPExcel->getActiveSheet()->getStyle('B5:'.$InitialColumn.'19')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B28:'.$InitialColumn.'32')->applyFromArray($borders);

        $objPHPExcel->getActiveSheet()->getStyle('B40:'.$InitialColumn.'40')->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->getStyle('B43:'.$InitialColumn.'47')->applyFromArray($borders);

        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->getStartColor()->setARGB($blueColor);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '5', "Total");

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', $query[0]["Peso_Total"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', $query[0]["Total_CBM"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', $query[0]["Total_Cantidad"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', $query[0]["sum_fob"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '12', $query[0]["sum_fob_valorado"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', $query[0]["Flete"] / $query[0]["Distribucion"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '15', $query[0]["cfr_total"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '16', $query[0]["cfr_valorado_total"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '17', $query[0]["seguro_total"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '18', $query[0]["cif_total"]);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '19', $query[0]["cif_valorado_total"]);
        //
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B23:E23');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B23', 'Tributos Aplicables');
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

        foreach ($query as $row) {
            $sum = 0;
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '26', "$".$row["antidumping"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', $row["ad_valorem"]."%");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '28', "$".$row["ad_valorem_value"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "$".$row["igv_value"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "$".$row["ipm_value"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "$".$row["percepcion_value"]);
            $sum = $row["ad_valorem_value"] + $row["igv_value"] + $row["ipm_value"] + $row["percepcion_value"];
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', "$".$sum);
            $sumAdValorem += $row["ad_valorem_value"];
            $sumIGV += $row["igv_value"];
            $sumIPM += $row["ipm_value"];
            $sumPercepcion += $row["percepcion_value"];
            $sumTotal += $sum;
            $InitialColumn++;

        }

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', "$".$sumAdValorem);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '28', "$".$sumAdValorem);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "$".$sumIGV);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "$".$sumIPM);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "$".$sumPercepcion);
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', "$".$sumTotal);

        //Costos Destinos
        $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B37:E37');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B37', 'Costos Destinos');
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B40', 'ITEM');
        $InitialColumn = 'C';
        $sumCostoDestino = 0;
        foreach ($query as $row) {
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', $row["costo_de_envio"]);
            $sumCostoDestino += $row["costo_de_envio"];
            $InitialColumn++;
        }
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', $sumCostoDestino);
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
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', $row["costo_total"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '45', $row["Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '46', $row["costo_total"] / $row["Cantidad"]);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '47', $row["Valor_Unitario"] * 3.7);
            $sumCostoTotal += $row["costo_total"];
            $InitialColumn++;
        }
        $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', $sumCostoTotal);

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
        /*foreach query as row starts in row 36 set b as index +1, c as query["Nombre_Comercial"]
        f as query["Cantidad"] g as query["Valor_Unitario"] i as query["costo_total"]/ $query["Cantidad"]
        j as  query["costo_total"] k as query["Valor_Unitario"]*3.7
         */

        for ($index = 0; $index < count($query); $index++) {
            $row = 36 + $index;
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $index + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $query[$index]["Nombre_Comercial"]);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $query[$index]["Cantidad"]);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $query[$index]["Valor_Unitario"]);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $query[$index]["costo_total"] / $query[$index]["Cantidad"]);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $query[$index]["costo_total"]);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $query[$index]["Valor_Unitario"] * 3.7);
        };

        $cotizationDetails = $this->db->select('*')
            ->from($this->table_cotizacion_detalles)
            ->where('ID_Cotizacion', $ID_Cotizacion)
            ->get()
            ->result_array();

        $objPHPExcel->getActiveSheet()->setCellValue('C8', $cotizationDetails[0]['Nombres']);
        $objPHPExcel->getActiveSheet()->setCellValue('C9', $cotizationDetails[0]['Apellidos']);
        $objPHPExcel->getActiveSheet()->setCellValue('C10', $cotizationDetails[0]['DNI']);
        $objPHPExcel->getActiveSheet()->setCellValue('C11', $cotizationDetails[0]['Telefono']);
        $objPHPExcel->getActiveSheet()->setCellValue('J9', $query[0]["Peso_Total"]);
        $objPHPExcel->getActiveSheet()->setCellValue('J11', $query[0]["CBM_Total"]);
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
        $this->db->update($this->table, array("ID_Tipo_Cliente" => $ID_TipoCliente));

        return array("success" => true);
    }
}
