<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>Laesystems</title>
      <style type=text/css>
        .table_pdf {
          width: 100%;
        }
    
        .tr-thead th.title {
          color: #000000;
          font-size: 10px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
        }

        .tr-thead th.sub_title_fecha {
          color: #000000;
          font-size: 10px;
          font-family: "Arial", Helvetica, sans-serif;
        }
        
        .tr-thead th.sub_title_nro {
          color: #000000;
          font-size: 10px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
        }

        .tr-sub_thead th.title {
          color: #000000;
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
        }

        .tr-sub_thead td.title {
          color: #000000;
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: normal;
        }

        .tr-sub_thead th.content {
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
        }
        
        .tr-theadFormat th{
          font-weight: bold;
          font-family: Arial;
        }
        
        .tr-header-detalle th{
          font-size: 7px;
          font-family: Arial;
          background-color: #F2F5F5;
        }

        .tr-header th{
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #F2F5F5;
        }
        
        .tr-footer th{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .tr-totales th{
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .tr-fila_impar td{
          font-size: 7px;
          font-family: Arial;
          background-color: #F2F5F5;
        }
        
        .tr-fila_par td{
          font-size: 7px;
          font-family: Arial;
          background-color: #FFFFFF;
        }
        
        .tr-importe_letras{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .text-left{text-align: left;}
        .text-center{text-align: center;}
        .text-right{text-align: right;}
      </style>
    </head>
    <body>
      <?php //array_debug($arrData); ?>
	  	<table class="table_pdf" style="">
        <thead>
          <tr class="tr-thead">
            <th class="text-center title">LIQUIDACIÓN DE CAJA</th>
          </tr>
        </thead>
      </table>
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr>
            <th class="text-left" colspan="2"><b>F. Apertura</b> &nbsp;<?php echo allTypeDate($arrData['TotalesLiquidacionCaja'][0]->Fe_Apertura, '-', 0); ?></th>
          </tr>
          <tr>
            <th class="text-left" colspan="2"><b>F. Cierre</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo allTypeDate($arrData['TotalesLiquidacionCaja'][0]->Fe_Cierre, '-', 0); ?></th>
          </tr>
          <tr>
            <th class="text-left" colspan="2"><b>Caja</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $arrData['TotalesLiquidacionCaja'][0]->ID_POS; ?></th>
          </tr>
          <tr>
            <th class="text-left" colspan="2"><b>Cajero</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $arrData['TotalesLiquidacionCaja'][0]->No_Entidad; ?></th>
          </tr>
        </thead>
      </table>
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" colspan="4">Ventas por <?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?></th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center"><?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?></th>
            <th class="text-right">Cantidad</th>
            <th class="text-right">M</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( !empty($arrData['VentasxFamilia']) ) {
            $fCantidadTotal = 0.00;
            $fTotal = 0.00;                                
            foreach( $arrData['VentasxFamilia'] as $row ){ ?>
          <tr>
            <td class="text-left"><?php echo $row->No_Familia_Item; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></td>
            <td class="text-right"><?php echo $row->No_Signo; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
          </tr>
            <?php
              $fCantidadTotal += $row->Qt_Producto;
              $fTotal += $row->Ss_Total;
            } // ./ foreach
            ?>
          </tbody>
          <tfoot>
            <tr class="tr-theadFormat tr-totales">
              <th class="text-right">Total</th>
              <th class="text-right"><?php echo numberFormat($fCantidadTotal, 2, '.', ','); ?></th>
              <th class="text-right" colspan="2"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
            </tr>
          </tfoot>
          <?php
          } else {
          ?>
            <tr>
              <td class="text-center" colspan="4">No hay movimientos</td>
            </tr>
          <?php
          }// ./ if - else
          ?>
        </tbody>
      </table><!-- Ventas por categoría -->
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" colspan="4">Ventas por <?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?> - Nota Crédito</th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center"><?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?></th>
            <th class="text-right">Cantidad</th>
            <th class="text-right">M</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( !empty($arrData['VentasxFamiliaNotaCredito']) ) {
            $fCantidadTotal = 0.00;
            $fTotal = 0.00;                                
            foreach( $arrData['VentasxFamiliaNotaCredito'] as $row ){ ?>
          <tr>
            <td class="text-left"><?php echo $row->No_Familia_Item; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></td>
            <td class="text-right"><?php echo $row->No_Signo; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
          </tr>
            <?php
              $fCantidadTotal += $row->Qt_Producto;
              $fTotal += $row->Ss_Total;
            } // ./ foreach
            ?>
          </tbody>
          <tfoot>
            <tr class="tr-theadFormat tr-totales">
              <th class="text-right">Total</th>
              <th class="text-right"><?php echo numberFormat($fCantidadTotal, 2, '.', ','); ?></th>
              <th class="text-right" colspan="2"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
            </tr>
          </tfoot>
          <?php
          } else {
          ?>
            <tr>
              <td class="text-center" colspan="4">No hay movimientos</td>
            </tr>
          <?php
          }// ./ if - else
          ?>
        </tbody>
      </table><!-- Ventas por categoría Nota de crédito-->
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" colspan="3">Movimientos de Caja</th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center">Movimiento</th>
            <th class="text-center">M</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( !empty($arrData['MovimientosCaja']) ) {
            $fCantidadTotal = 0.00;
            $fTotal = 0.00;
            foreach( $arrData['MovimientosCaja'] as $row ){ ?>
          <tr>
            <td class="text-left"><?php echo $row->No_Tipo_Operacion_Caja; ?></td>
            <td class="text-center"><?php echo $row->No_Signo; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?> (<?php echo $row->Nu_Tipo != '6' ? '+' : '-' ; ?>)</td>
          </tr>
            <?php
              $fTotal += ($row->Nu_Tipo != '6' ? $row->Ss_Total : -$row->Ss_Total);
            } // ./ foreach
            ?>
          </tbody>
          <tfoot>
            <tr class="tr-theadFormat tr-totales">
              <th class="text-right" colspan="2">Total</th>
              <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
            </tr>
          </tfoot>
          <?php
          } else {
          ?>
            <tr>
              <td class="text-center" colspan="3">No hay movimientos</td>
            </tr>
          <?php
          }// ./ if - else
          ?>
        </tbody>
      </table><!-- MovimientosCaja -->
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" colspan="3">Forma de Pago</th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center">Movimiento</th>
            <th class="text-center">M</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( !empty($arrData['VentasGenerales']) ) {
            $fTotal = 0.00;
            foreach( $arrData['VentasGenerales'] as $row ){ ?>
          <tr>
            <td class="text-left"><?php echo $row->No_Medio_Pago; ?></td>
            <td class="text-center"><?php echo $row->No_Signo; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?> <?php echo $row->Nu_Tipo_Caja == '0' ? '(+)' : '' ; ?></td>
          </tr>
            <?php
              $fTotal += $row->Ss_Total;
            } // ./ foreach
            ?>
          </tbody>
          <tfoot>
            <tr class="tr-theadFormat tr-totales">
              <th class="text-right" colspan="2">Total</th>
              <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
            </tr>
          </tfoot>
          <?php
          } else {
          ?>
            <tr>
              <td class="text-center" colspan="3">No hay movimientos</td>
            </tr>
          <?php
          }// ./ if - else
          ?>
        </tbody>
      </table><!-- Ventas Generales -->
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" colspan="3">Ventas al Crédito Generales</th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center">Tipo</th>
            <th class="text-center">M</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( !empty($arrData['VentasGeneralesCreditoCreditoNoSuma']) ) {
            $fTotal = 0.00;
            foreach( $arrData['VentasGeneralesCreditoCreditoNoSuma'] as $row ){ ?>
          <tr>
            <td class="text-left"><?php echo $row->No_Medio_Pago; ?></td>
            <td class="text-center"><?php echo $row->No_Signo; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
          </tr>
            <?php
              $fTotal += $row->Ss_Total;
            } // ./ foreach
            ?>
          </tbody>
          <tfoot>
            <tr class="tr-theadFormat tr-totales">
              <th class="text-right" colspan="2">Total</th>
              <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
            </tr>
          </tfoot>
          <?php
          } else {
          ?>
            <tr>
              <td class="text-center" colspan="3">No hay movimientos</td>
            </tr>
          <?php
          }// ./ if - else
          ?>
        </tbody>
      </table><!-- Ventas al Crédito Generales -->
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" colspan="3">Ventas por Descuento</th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left">Nombre</th>
            <th class="text-right">M</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( !empty($arrData['VentasxDescuento']) ) {
            $fTotal = 0.00;
            foreach( $arrData['VentasxDescuento'] as $row ){ ?>
          <tr>
            <td class="text-left">Descuento</td>
            <td class="text-right"><?php echo $row->No_Signo; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
          </tr>
            <?php
              $fTotal += $row->Ss_Total;
            } // ./ foreach
            ?>
          </tbody>
          <tfoot>
            <tr class="tr-theadFormat tr-totales">
              <th class="text-right" colspan="2">Total</th>
              <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
            </tr>
          </tfoot>
          <?php
          } else {
          ?>
            <tr>
              <td class="text-center" colspan="3">No hay movimientos</td>
            </tr>
          <?php
          }// ./ if - else
          ?>
        </tbody>
      </table><!-- Ventas por GRATUITAS o REGALOS -->
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" colspan="3">Ventas por GRATUITAS o REGALOS</th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left">Nombre</th>
            <th class="text-right">M</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( !empty($arrData['VentasxRegaloGratuita']) ) {
            $fTotal = 0.00;
            foreach( $arrData['VentasxRegaloGratuita'] as $row ){ ?>
          <tr>
            <td class="text-left">Gratuitas</td>
            <td class="text-right"><?php echo $row->No_Signo; ?></td>
            <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
          </tr>
            <?php
              $fTotal += $row->Ss_Total;
            } // ./ foreach
            ?>
          </tbody>
          <tfoot>
            <tr class="tr-theadFormat tr-totales">
              <th class="text-right" colspan="2">Total</th>
              <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
            </tr>
          </tfoot>
          <?php
          } else {
          ?>
            <tr>
              <td class="text-center" colspan="3">No hay movimientos</td>
            </tr>
          <?php
          }// ./ if - else
          ?>
        </tbody>
      </table><!-- Ventas por GRATUITAS o REGALOS -->
      <br><br>
      <table class="table_pdf" border="0">
        <thead>
          <tr>
            <th colspan="2" class="text-left"><b>Total a Depositado</b></th>
            <th class="text-right"><?php echo numberFormat($arrData['TotalesLiquidacionCaja'][0]->Ss_Total, 2, '.', ','); ?></th>
          </tr>
          <tr>
            <th colspan="2" class="text-left"><b>Total a Liquidar</b></th>
            <th class="text-right"><?php echo numberFormat($arrData['TotalesLiquidacionCaja'][0]->Ss_Expectativa, 2, '.', ','); ?></th>
          </tr>
          <tr>
            <th colspan="2" class="text-left"><b>Diferencia (T.D - T.L)</b></th>
            <th class="text-right"><?php echo numberFormat($arrData['TotalesLiquidacionCaja'][0]->Ss_Total - $arrData['TotalesLiquidacionCaja'][0]->Ss_Expectativa, 2, '.', ','); ?></th>
          </tr>
        </thead>
      </table><!-- Ventas por Descuento -->
    </body>
</html>