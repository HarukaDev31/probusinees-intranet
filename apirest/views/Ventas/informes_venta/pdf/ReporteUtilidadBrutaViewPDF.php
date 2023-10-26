<!DOCTYPE html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>Laesystems</title>
      <style type=text/css>
        .table_pdf {
          width: 100%;
        }
        
        .tr-theadFormatTitle th{
          font-weight: bold;
          font-size: 14px;
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
          font-size: 7px;
          border: solid 0.5px #000000;
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
            <td align="left"><?php echo $this->empresa->No_Empresa; ?></td>
          </tr>
          <tr class="tr-theadFormatTitle">
            <th align="center" colspan="2">Informe de Reporte Utilidad Bruta</th>
          </tr>
          <tr class="tr-theadFormat">
            <th align="center" colspan="2">&nbsp;</th>
          </tr>
          <tr class="tr-theadFormat">
            <td align="center" colspan="2">Desde: <?php echo $arrCabecera['Fe_Inicio'] . ' Hasta: ' . $arrCabecera['Fe_Fin']; ?></td>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
      <br/>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-thead tr-theadFormat">
            <th class="text-center">Código</th>
            <th class="text-center">Item</th>
            <th class="text-center">Moneda</th>
            <th class="text-center">Último Costo Compra</th>
            <th class="text-center">Costo Promedio Venta</th>
            <th class="text-center">Ganancia</th>
            <th class="text-center">Margen</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Utilidad</th>
            <th class="text-center">Descuento</th>
            <th class="text-center">Neto</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $counter = 0; $ID_Familia = '';
            $sum_cantidad = 0.00; $sum_total = 0.00; $sum_descuento = 0.00; $sum_utilidad_neta = 0.00;
            $sum_general_cantidad = 0.00; $sum_general_total = 0.00; $sum_general_total_descuento = 0.00; $sum_general_total_utilidad_neta = 0.00;
            $ID_Almacen = 0; $sum_cantidad_almacen = 0.00; $sum_total_almacen = 0.00; $sum_total_descuento_almacen = 0.00; $sum_total_utilidad_neta_almacen = 0.00; $counter_almacen = 0;
            foreach($arrDetalle['arrData'] as $row) {
              if ($ID_Familia != $row->ID_Familia || $ID_Almacen != $row->ID_Almacen) {
                if ($counter != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="7">Total</th>
                    <th class="text-right"><?php echo numberFormat($sum_cantidad, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_descuento, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_utilidad_neta, 2, '.', ','); ?></th>
                  </tr>
                  <?php
                  $sum_cantidad = 0.000;
                  $sum_total = 0.00;
                  $sum_descuento = 0.00;
                  $sum_utilidad_neta = 0.00;
                }

                if ($ID_Almacen != $row->ID_Almacen) {
                  if ($counter_almacen != 0) { ?>
                    <tr class="tr-theadFormat tr-theadFormat_footer">
                      <th class="text-right" colspan="7">Total Almacén</th>
                      <th class="text-right"><?php echo numberFormat($sum_cantidad_almacen, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_total_almacen, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_total_descuento_almacen, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_total_utilidad_neta_almacen, 2, '.', ','); ?></th>
                    </tr>
                    <?php
                    $sum_cantidad_almacen = 0.000;
                    $sum_total_almacen = 0.00;
                    $sum_total_descuento_almacen = 0.00;
                    $sum_total_utilidad_neta_almacen = 0.00;
                  } ?>                  
                  <tr class="tr-theadFormat tr-theadFormat_header">
                    <th class="text-left">Almacén </th>
                    <th class="text-left" colspan="10"><?php echo $row->No_Almacen; ?></th>
                  </tr><?php
                  $ID_Almacen = $row->ID_Almacen;
                } ?>
                <tr class="tr-theadFormat tr-theadFormat_header">
                  <th class="text-left">Familia </th>
                  <th class="text-left" colspan="10"><?php echo $row->No_Familia; ?></th>
                </tr>
                <?php
                $ID_Familia = $row->ID_Familia;
              }// /. if id familia ?>
              <tr class="tr-theadFormat">
                <td class="text-left"><?php echo $row->Nu_Codigo_Barra; ?></td>
                <td class="text-left"><?php echo $row->No_Producto; ?></td>
                <td class="text-center"><?php echo $row->No_Signo; ?></td>                
                <td class="text-right"><?php echo numberFormat($row->Ss_Costo, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Ganancia, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Po_Margen_Ganancia, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Utilidad, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Descuento, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Utilidad_Neta, 2, '.', ','); ?></td>
              </tr>
              <?php
              
              $sum_cantidad += $row->Qt_Producto;
              $sum_total += $row->Ss_Utilidad;
              $sum_descuento += $row->Ss_Descuento;
              $sum_utilidad_neta += $row->Ss_Utilidad_Neta;
              
              $sum_cantidad_almacen += $row->Qt_Producto;
              $sum_total_almacen += $row->Ss_Utilidad;
              $sum_total_descuento_almacen += $row->Ss_Descuento;
              $sum_total_utilidad_neta_almacen += $row->Ss_Utilidad_Neta;
              
              $sum_general_cantidad += $row->Qt_Producto;
              $sum_general_total += $row->Ss_Utilidad;
              $sum_general_total_utilidad_neta += $row->Ss_Descuento;
              $sum_general_total_descuento += $row->Ss_Utilidad_Neta;
              
              $counter++;
              $counter_almacen++;
            }// /. foreach ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="7">Total</th>
              <th class="text-right"><?php echo numberFormat($sum_cantidad, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_descuento, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_utilidad_neta, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="7">Total Almacén</th>
              <th class="text-right"><?php echo numberFormat($sum_cantidad_almacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_almacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_descuento_almacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_utilidad_neta_almacen, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="7">Total General</th>
              <th class="text-right"><?php echo numberFormat($sum_general_cantidad, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_total, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_utilidad_neta, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_descuento, 2, '.', ','); ?></th>
            </tr>
          <?php
        } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="11">No hay registros</td>
          </tr>
          <?php
        } ?>
        </tbody>
      </table>
    </body>
</html>