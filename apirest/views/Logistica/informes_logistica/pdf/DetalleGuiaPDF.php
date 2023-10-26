<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>Laesystems - Detalle de Guías de Entradas / Salidas</title>
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
            <td align="right"><?php echo $arrCabecera['No_Almacen']; ?></td>
          </tr>
          <tr class="tr-theadFormatTitle">
            <th align="center" colspan="2">Detalle de Guías de Entrada y Salida</th>
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
            <th class="text-center" colspan="2">Guia</th>
            <th class="text-center">Fecha</th>
            <th class="text-center" colspan="2">Proveedor</th>
            <th class="text-center" colspan="2">Factura</th>
            <th class="text-center" rowspan="2">Moneda</th>
            <th class="text-center">Tipo</th>
            <th class="text-center" colspan="8">Producto</th>
            <th class="text-center" rowspan="2">Glosa</th>
            <th class="text-center" rowspan="2">Estado</th>
            <th class="text-center" rowspan="2">Tipo</th>
            <th class="text-center" rowspan="2">Movimiento</th>
          </tr>
          <tr class="tr-thead tr-theadFormat">
            <th class="text-center">Serie</th>
            <th class="text-center">Número</th>
            <th class="text-center">Emisión</th>
            <th class="text-center">RUC</th>
            <th class="text-center">Razón Social</th>
            <th class="text-center">Serie</th>
            <th class="text-center">Número</th>
            <th class="text-center">Cambio</th>
            <th class="text-center">Código Barra</th>
            <th class="text-center">Descripción</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Precio</th>
            <th class="text-center">SubTotal S/</th>
            <th class="text-center">Impuesto S/</th>
            <th class="text-center">Total S/</th>
            <th class="text-center">Total M. Ext.</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( count($arrDetalle) > 0) {
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_guia_cantidad = 0.00;
            $sum_almacen_guia_subtotal_s = 0.00; $sum_almacen_guia_impuesto_s = 0.00; $sum_almacen_guia_total_s = 0.00;
            $sum_almacen_guia_total_d = 0.00;
            $ID_Guia_Cabecera = '';
            $counter = 0;
            $sum_guia_cantidad = 0.000000;
            $sum_guia_subtotal_s = 0.00;
            $sum_guia_impuesto_s = 0.00;
            $sum_guia_total_s = 0.00;
            $sum_guia_total_d = 0.00;
            foreach($arrDetalle as $row) {
              if ($ID_Guia_Cabecera != $row->ID_Guia_Cabecera) {
                if ($counter != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="11">Total Guía</th>
                    <th class="text-right"><?php echo numberFormat($sum_guia_cantidad, 3, '.', ','); ?></th>
                    <th class="text-right">&nbsp;</th>
                    <th class="text-right"><?php echo numberFormat($sum_guia_subtotal_s, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_guia_impuesto_s, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_guia_total_s, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_guia_total_d, 2, '.', ','); ?></th>
                  </tr>
                  <?php
                  $sum_guia_cantidad = 0.000000;
                  $sum_guia_subtotal_s = 0.00;
                  $sum_guia_impuesto_s = 0.00;
                  $sum_guia_total_s = 0.00;
                  $sum_guia_total_d = 0.00;
                }
                
                if ($ID_Almacen != $row->ID_Almacen) {
                  if ($counter_almacen != 0) { ?>
                    <tr class="tr-theadFormat tr-theadFormat_footer">
                      <th class="text-right" colspan="11">Total Almacén</th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_guia_cantidad, 3, '.', ','); ?></th>
                      <th class="text-right">&nbsp;</th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_guia_subtotal_s, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_guia_impuesto_s, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_guia_total_s, 2, '.', ','); ?></th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_guia_total_d, 2, '.', ','); ?></th>
                    </tr>
                    <?php
                    $sum_almacen_guia_cantidad = 0.000000;
                    $sum_almacen_guia_subtotal_s = 0.00;
                    $sum_almacen_guia_impuesto_s = 0.00;
                    $sum_almacen_guia_total_s = 0.00;
                    $sum_almacen_guia_total_d = 0.00;
                  } ?>
                    
                  <tr class="tr-theadFormat tr-theadFormat_header">
                    <th class="text-left">Almacén</th>
                    <th class="text-left" colspan="18"><?php echo $row->No_Almacen; ?></th>
                  </tr>
                  <?php
                  $ID_Almacen = $row->ID_Almacen;
                } ?>

                <tr class="tr-theadFormat tr-theadFormat_header">
                  <th class="text-left"><?php echo $row->ID_Serie_Documento; ?></th>
                  <th class="text-left"><?php echo $row->ID_Numero_Documento; ?></th>
                  <th class="text-center"><?php echo $row->Fe_Emision; ?></th>
                  <th class="text-center"><?php echo $row->Nu_Documento_Identidad; ?></th>
                  <th class="text-left"><?php echo $row->No_Entidad; ?></th>
                  <th class="text-left"><?php echo ($row->ID_Serie_Documento_Factura !== null ? $row->ID_Serie_Documento_Factura : ''); ?></th>
                  <th class="text-left"><?php echo ($row->ID_Numero_Documento_Factura !== null ? $row->ID_Numero_Documento_Factura : ''); ?></th>
                  <th class="text-right" colspan="10">&nbsp;</th>
                  <th class="text-left"><?php echo $row->Txt_Glosa; ?></th>
                  <th class="text-center"><?php echo $row->No_Estado; ?></th>
                  <th class="text-center"><?php echo $row->No_Tipo_Movimiento; ?></th>
                  <th class="text-center"><?php echo $row->No_Tipo_Movimiento_Detallado; ?></th>
                </tr>
                <?php
                $ID_Guia_Cabecera = $row->ID_Guia_Cabecera;
              }
              if ($row->Qt_Producto !== '' && $row->Ss_Precio !== '') { ?>
                <tr class="tr-theadFormat">
                  <td class="text-right" colspan="7">&nbsp;</td>
                  <td class="text-right"><?php echo $row->No_Signo; ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','); ?></td>
                  <td class="text-left"><?php echo $row->Nu_Codigo_Barra; ?></td>
                  <td class="text-left"><?php echo $row->No_Producto; ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 3, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_SubTotal, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Impuesto, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo ($row->MONE_Nu_Sunat_Codigo == 'PEN' ? '0.00' : numberFormat($row->Ss_Total * $row->Ss_Tipo_Cambio, 2, '.', ',')); ?></td>
                </tr>
                <?php
                $sum_guia_cantidad += $row->Qt_Producto;
                $sum_guia_subtotal_s += $row->Ss_SubTotal;
                $sum_guia_impuesto_s += $row->Ss_Impuesto;
                $sum_guia_total_s += $row->Ss_Total;
                $sum_guia_total_d += ($row->MONE_Nu_Sunat_Codigo != 'PEN' ? $row->Ss_Total : 0);

                $sum_almacen_guia_cantidad += $row->Qt_Producto;
                $sum_almacen_guia_subtotal_s += $row->Ss_SubTotal;
                $sum_almacen_guia_impuesto_s += $row->Ss_Impuesto;
                $sum_almacen_guia_total_s += $row->Ss_Total;
                $sum_almacen_guia_total_d += ($row->MONE_Nu_Sunat_Codigo != 'PEN' ? $row->Ss_Total : 0);
              }
              $counter++;
              $counter_almacen++;
            } ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="11">Total Guía</th>
              <th class="text-right"><?php echo numberFormat($sum_guia_cantidad, 3, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_guia_subtotal_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_guia_impuesto_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_guia_total_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_guia_total_d, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="11">Total Almacén</th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_guia_cantidad, 3, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_guia_subtotal_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_guia_impuesto_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_guia_total_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_guia_total_d, 2, '.', ','); ?></th>
            </tr>
          <?php
          } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="16">No hay registros</td>
          </tr>
          <?php
          } ?>
        </tbody>
      </table>
    </body>
</html>