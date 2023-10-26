<!DOCTYPE html>
<html>
	<head>
        <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
        <title>Laesystems</title>
        <style type=text/css>
            .table_pdf {
            width: 100%;
            }
            
            .tr-theadFormatTitle th{
                font-weight: bold;
                font-size: 11px;
            }
            
            .tr-theadFormat th{
                font-weight: bold;
            }

            .tr-theadFormat_header th{
                background-color: #F2F5F5;
            }
            
            .tr-theadFormat_footer th{
                background-color: #E7E7E7;
            }
            
            .tr-thead th{
                font-size: 5px;
                border: solid 0.5px #000000;
            }
            
            .tr-lista-total th, .tr-lista-total td{
                background-color: #F2F5F5;
            }
            
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .text-right{text-align: right;}
        </style>
    </head>
    <body>
        <br/>
        <table class="table_pdf">
            <thead>
                <tr class="tr-theadFormat">
                    <th>FORMATO 12.1: REGISTRO DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS- DETALLE DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS</th>
                </tr>
            </thead>
        </table>
        <br/>
        <br/>
	  	<table class="table_pdf">
        <?php
        if ( $arrDetalle['sStatus'] == 'success' ) {
            $ID_Almacen = 0; $counter_almacen = 0; $sum_Almacen_Producto_Qt_Entrada = 0.00; $sum_Almacen_Producto_Qt_Salida = 0.00;
            $ID_Producto = 0;
            $counter = 0;
            $sum_Producto_Qt_Entrada = 0.00;
            $sum_Producto_Qt_Salida = 0.00;
            $sum_General_Qt_Entrada = 0.00;
            $sum_General_Qt_Salida = 0.00;
            $Qt_Producto_Saldo_Movimiento = 0.00; ?>
            <tbody>
            <?php
            $arrFechaInicio = explode('-', $arrCabecera['dInicio']);
            $fYear = $arrFechaInicio[0];
            $fMonth = $arrFechaInicio[1];
            //foreach($arrDetalle['arrData'] as $row) {
            for ($i=0; $i < count($arrDetalle['arrData']); $i++) {
                $row = $arrDetalle['arrData'][$i];
                if ($ID_Producto != $row->ID_Producto || $ID_Almacen != $row->ID_Almacen) {
                    $Qt_Producto_Saldo_Movimiento = $row->Qt_Producto_Inicial;
                    if ($counter != 0) { ?>
                        <tr class="tr-theadFormat tr-theadFormat_header">
                            <th class="text-right" colspan="8">TOTAL PRODUCTO</th>
                            <th class="text-right"><?php echo $sum_Producto_Qt_Entrada; ?></th>
                            <th class="text-right"><?php echo $sum_Producto_Qt_Salida; ?></th>
                        </tr>
                    <?php
                    }

                    if ($ID_Almacen != $row->ID_Almacen) {
                        if ($counter_almacen != 0) {?>
                            <tr class="tr-theadFormat tr-theadFormat_footer">
                                <th class="text-right" colspan="8">TOTAL ALMACÉN: <?php echo $arrDetalle['arrData'][$i-1]->No_Almacen; ?></th>
                                <th class="text-right"><?php echo $sum_Almacen_Producto_Qt_Entrada; ?></th>
                                <th class="text-right"><?php echo $sum_Almacen_Producto_Qt_Salida; ?></th>
                            </tr>
                            <?php
                        }
                        $sum_Almacen_Producto_Qt_Entrada = 0.00;
                        $sum_Almacen_Producto_Qt_Salida = 0.00;

                        $ID_Almacen = $row->ID_Almacen;
                    }
                    ?>
                    <br><br>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">PERIODO: </td>
                        <th class="text-left" colspan="8"><?php echo $fMonth . ' ' . $fYear; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">RUC: </td>
                        <th class="text-left" colspan="8"><?php echo $this->empresa->Nu_Documento_Identidad; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL: </td>
                        <th class="text-left" colspan="8"><?php echo $this->empresa->No_Empresa; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">ALMACÉN: </td>
                        <th class="text-left" colspan="8"><?php echo $row->No_Almacen; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">ESTABLECIMIENTO (1): </td>
                        <th class="text-left" colspan="8"><?php echo $row->Txt_Direccion_Almacen; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">CÓDIGO DE LA EXISTENCIA: </td>
                        <th class="text-left" colspan="8"><?php echo $row->TP_Sunat_Codigo; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">TIPO (TABLA 5): </td>
                        <th class="text-left" colspan="8"><?php echo $row->TP_Sunat_Nombre; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">UPC: </td>
                        <th class="text-left" colspan="8"><?php echo $row->Nu_Codigo_Barra; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">SKU: </td>
                        <th class="text-left" colspan="8"><?php echo $row->No_Codigo_Interno; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">DESCRIPCIÓN: </td>
                        <th class="text-left" colspan="8"><?php echo $row->No_Producto; ?></th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <td class="text-left" colspan="3">CÓDIGO DE LA UNIDAD DE MEDIDA (TABLA 6): </td>
                        <th class="text-left" colspan="8"><?php echo $row->UM_Sunat_Codigo; ?></th>
                    </tr>
                    <br/>
                    <tr class="tr-thead tr-theadFormat">
                        <th class="text-center" colspan="5">DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO, DOCUMENTO INTERNO O SIMILAR</th>
                        <th class="text-center" rowspan="2">TIPO DE OPERACIÓN (TABLA 12)</th>
                        <th class="text-center" rowspan="2">MOVIMIENTO</th>
                        <th class="text-center" rowspan="2">ENTIDAD</th>
                        <th class="text-center" rowspan="2">ENTRADAS</th>
                        <th class="text-center" rowspan="2">SALIDAS</th>
                        <th class="text-center" rowspan="2">SALDO FINAL</th>
                    </tr>
                    <tr class="tr-thead tr-theadFormat">
                        <th class="text-center">FECHA</th>
                        <th class="text-center">TIPO (TABLA 10)</th>
                        <th class="text-center">TIPO</th>
                        <th class="text-center">SERIE</th>
                        <th class="text-center">NÚMERO</th>
                    </tr>
                    <tr class="tr-theadFormat">
                        <th class="text-right" colspan="8"></th>
                        <th class="text-right" colspan="2">SALDO INICIAL</th>
                        <th class="text-right"><?php echo numberFormat($Qt_Producto_Saldo_Movimiento, 3, '.', ''); ?></th>
                    </tr>
                <?php
                    $ID_Producto = $row->ID_Producto;
                    $sum_Producto_Qt_Entrada = 0.00;
                    $sum_Producto_Qt_Salida = 0.00;
                } // /. if producto
                ?>
                <tr class="tr-theadFormat">
                    <td class="text-center"><?php echo $row->Fe_Emision; ?></td>
                    <td class="text-center"><?php echo $row->Tipo_Documento_Sunat_Codigo; ?></td>
                    <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                    <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                    <td class="text-left"><?php echo $row->ID_Numero_Documento; ?></td>
                    <td class="text-center"><?php echo $row->Tipo_Operacion_Sunat_Codigo; ?></td>
                    <td class="text-center"><?php echo $row->No_Tipo_Movimiento; ?></td>
                    <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                    <?php
                    if ($row->Nu_Tipo_Movimiento == 0){ ?>
                        <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 3, '.', ''); ?></td>
                        <td class="text-right">0</td>
                    <?php
                        $Qt_Producto_Saldo_Movimiento += $row->Qt_Producto;
                        $sum_Producto_Qt_Entrada += $row->Qt_Producto;
                        $sum_Almacen_Producto_Qt_Entrada += $row->Qt_Producto;
                        $sum_General_Qt_Entrada += $row->Qt_Producto;
                    } else { ?>
                        <td class="text-right">0</td>
                        <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 3, '.', ''); ?></td>
                    <?php
                        $Qt_Producto_Saldo_Movimiento -= $row->Qt_Producto;
                        $sum_Producto_Qt_Salida += $row->Qt_Producto;
                        $sum_Almacen_Producto_Qt_Salida += $row->Qt_Producto;
                        $sum_General_Qt_Salida += $row->Qt_Producto;
                    }
                    ?>
                    <td class="text-right"><?php echo numberFormat($Qt_Producto_Saldo_Movimiento, 3, '.', ''); ?></td>
                </tr>
                <?php $counter++; $counter_almacen++; ?>
  			<?php
            } // ./ foreach arrdata
            ?>
            </tbody>
            <tfoot>
                <tr class="tr-theadFormat tr-theadFormat_header">
                    <th class="text-right" colspan="8">TOTAL PRODUCTO</th>
                    <th class="text-right"><?php echo $sum_Producto_Qt_Entrada; ?></th>
                    <th class="text-right"><?php echo $sum_Producto_Qt_Salida; ?></th>
                </tr>
                <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="8">TOTAL ALMACÉN: <?php echo $row->No_Almacen; ?></th>
                    <th class="text-right"><?php echo $sum_Almacen_Producto_Qt_Entrada; ?></th>
                    <th class="text-right"><?php echo $sum_Almacen_Producto_Qt_Salida; ?></th>
                </tr>
                <tr class="tr-theadFormat">
                    <th class="text-right" colspan="8">TOTAL GENERAL</th>
                    <th class="text-right"><?php echo $sum_General_Qt_Entrada; ?></th>
                    <th class="text-right"><?php echo $sum_General_Qt_Salida; ?></th>
                </tr>
                <tr class="tr-theadFormat">
                    <th class="text-right" colspan="8">TOTAL CANTIDAD (ENTRADA - SALIDA)</th>
                    <th class="text-right" colspan="2"><?php echo ($sum_General_Qt_Entrada - $sum_General_Qt_Salida); ?></th>
                </tr>
            </tfoot>

            <!--PRODUCTOS SIN MOVIMIENTO -->
            <tbody>
            <?php
            $ID_Almacen = 0;
            for ($i = 0; $i < count($arrDetalle['arrDataAlmacenSinMovimiento']); $i++) {
                $row_almacen = $arrDetalle['arrDataAlmacenSinMovimiento'];
                if ($ID_Almacen != $arrDetalle['arrDataAlmacenSinMovimiento'][$i][0]->ID_Almacen) {
                    $ID_Almacen = $arrDetalle['arrDataAlmacenSinMovimiento'][$i][0]->ID_Almacen;

                    for ($p = 0; $p < count($arrDetalle['arrDataAlmacenSinMovimiento'][$i]); $p++) {
                        $row_producto = $row_almacen[$i]; ?>
                        <br><br>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">PERIODO: </td>
                            <th class="text-left" colspan="8"><?php echo $fMonth . ' ' . $fYear; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">RUC: </td>
                            <th class="text-left" colspan="8"><?php echo $this->empresa->Nu_Documento_Identidad; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL: </td>
                            <th class="text-left" colspan="8"><?php echo $this->empresa->No_Empresa; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">ALMACÉN: </td>
                            <th class="text-left" colspan="8"><?php echo $row_almacen[$i][0]->No_Almacen; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">ESTABLECIMIENTO (1): </td>
                            <th class="text-left" colspan="8"><?php echo $arrCabecera['Txt_Direccion_Almacen']; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">UPC: </td>
                            <th class="text-left" colspan="8"><?php echo $row_producto[$p]->Nu_Codigo_Barra; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">SKU: </td>
                            <th class="text-left" colspan="8"><?php echo $row_producto[$p]->No_Codigo_Interno; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">DESCRIPCIÓN: </td>
                            <th class="text-left" colspan="8"><?php echo $row_producto[$p]->No_Producto; ?></th>
                        </tr>
                        <tr class="tr-theadFormat">
                            <td class="text-left" colspan="3">SALDO: </td>
                            <th class="text-left" colspan="8"><?php echo $row_producto[$p]->Qt_Producto; ?></th>
                        </tr>
                    <?php 
                    }
                } ?>
  			<?php
            } // ./ foreach arrdata
            ?>
            </tbody>

            <?php
        } else { ?>
            <tbody>
                <tr>
                    <th class="text-right" colspan="11"><?php echo $arrDetalle['sMessage']; ?></th>
                </tr>
            </tbody>
            <?php
        } // /. if - else respuesta model ?>
        </table>
    </body>
</html>