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
          font-size: 9px;
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
            <th align="center" colspan="2">Informe de Ventas por Marca</th>
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
            <th class="text-center">F. Emisión</th>
            <th class="text-center">Tipo</th>
            <th class="text-center">Serie</th>
            <th class="text-center">Número</th>
            <th class="text-center">Cliente</th>
            <th class="text-center">M</th>
            <th class="text-center">T.C.</th>
            <th class="text-center">U.M.</th>
            <th class="text-center">Item</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Precio</th>
            <th class="text-center">SubTotal</th>
            <th class="text-center">Impuesto</th>
            <th class="text-center">Total</th>
            <th class="text-center">Estado</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $counter = 0; $ID_Marca = ''; $cantidad = 0.00; $subtotal = 0.00; $impuesto = 0.00; $total_s = 0.00;
            $sum_cantidad = 0.00; $sum_subtotal = 0.00; $sum_impuesto = 0.00; $sum_total_s = 0.00;
            $sum_general_cantidad = 0.00; $sum_general_subtotal = 0.00; $sum_general_impuesto = 0.00; $sum_general_total_s = 0.00;
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_cantidad = 0.000000; $sum_almacen_impuesto=0.00; $sum_almacen_subtotal = 0.00; $sum_impuesto = 0.00; $sum_almacen_total_s = 0.00;
            foreach($arrDetalle['arrData'] as $row) {
              if ($ID_Marca != $row->ID_Marca || $ID_Almacen != $row->ID_Almacen) {
                if ($counter != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="9">Total</th>
                    <th class="text-right"><?php echo numberFormat($sum_cantidad, 3, '.', ','); ?></th>
                    <th class="text-right">&nbsp;</th>
                    <th class="text-right"><?php echo numberFormat($sum_subtotal, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_impuesto, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_s, 2, '.', ','); ?></th>
                  </tr>
                  <?php
                  $sum_cantidad = 0.000000;
                  $sum_subtotal = 0.00;
                  $sum_impuesto = 0.00;
                  $sum_total_s = 0.00;
                }

                if ($ID_Almacen != $row->ID_Almacen) {
                  if ($counter_almacen != 0) { ?>
                    <tr class="tr-theadFormat tr-theadFormat_footer">
                      <th class="text-right" colspan="9">Total Almacén</th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_cantidad, 3, '.', ','); ?></th>
                      <th class="text-right">&nbsp;</th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_subtotal, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_impuesto, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_total_s, 2, '.', ','); ?></th>
                    </tr>
                    <?php
                    $sum_almacen_cantidad = 0.000000;
                    $sum_almacen_subtotal = 0.00;
                    $sum_almacen_impuesto = 0.00;
                    $sum_almacen_total_s = 0.00;
                  } ?>                  
                  <tr class="tr-theadFormat tr-theadFormat_header">
                    <th class="text-left">Almacén </th>
                    <th class="text-left" colspan="16"><?php echo $row->No_Almacen; ?></th>
                  </tr><?php
                  $ID_Almacen = $row->ID_Almacen;
                } ?>
                <tr class="tr-theadFormat tr-theadFormat_header">
                  <th class="text-left">Marca </th>
                  <th class="text-left" colspan="14"><?php echo $row->No_Marca; ?></th>
                </tr>
                <?php
                $ID_Marca = $row->ID_Marca;
              }// /. if id familia ?>
              <tr class="tr-theadFormat">
                <td class="text-center"><?php echo $row->Fe_Emision_Hora; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                <td class="text-center"><?php echo $row->ID_Numero_Documento; ?></td>
                <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                <td class="text-center"><?php echo $row->No_Signo; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','); ?></td>
                <td class="text-left"><?php echo $row->No_Unidad_Medida; ?></td>
                <td class="text-left"><?php echo $row->No_Producto; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 3, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Subtotal, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Impuesto, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->No_Estado; ?></td>
              </tr>
              <?php
              
              $sum_cantidad += $row->Qt_Producto;
              $sum_subtotal += $row->Ss_Subtotal;
              $sum_impuesto += $row->Ss_Impuesto;
              $sum_total_s += $row->Ss_Total;
              
              $sum_almacen_cantidad += $row->Qt_Producto;
              $sum_almacen_subtotal += $row->Ss_Subtotal;
              $sum_almacen_impuesto += $row->Ss_Impuesto;
              $sum_almacen_total_s += $row->Ss_Total;
              
              $sum_general_cantidad += $row->Qt_Producto;
              $sum_general_subtotal += $row->Ss_Subtotal;
              $sum_general_impuesto += $row->Ss_Impuesto;
              $sum_general_total_s += $row->Ss_Total;
              
              $counter++;
              $counter_almacen++;
            }// /. foreach ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="9">Total</th>
              <th class="text-right"><?php echo numberFormat($sum_cantidad, 3, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_subtotal, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_impuesto, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_s, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="9">Total Almacén</th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_cantidad, 3, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_subtotal, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_impuesto, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_total_s, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="9">Total General</th>
              <th class="text-right"><?php echo numberFormat($sum_general_cantidad, 3, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_general_subtotal, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_impuesto, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_s, 2, '.', ','); ?></th>
            </tr>
          <?php
        } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="15">No hay registros</td>
          </tr>
          <?php
        } ?>
        </tbody>
      </table>
    </body>
</html>