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
            <th align="center" colspan="2">Informe de Ventas por Delivery</th>
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
            <th class="text-center" rowspan="2">Fecha Emisión</th>
            <th class="text-center" colspan="3">Documento</th>
            <th class="text-center" rowspan="2">Moneda</th>
            <th class="text-center">Tipo</th>
            <th class="text-center" colspan="8">Producto</th>
            <th class="text-center" rowspan="2">Estado</th>
            <th class="text-center" rowspan="2">Estado Delivery</th>
          </tr>
          <tr class="tr-thead tr-theadFormat">
            <th class="text-center">Tipo</th>
            <th class="text-center">Serie</th>
            <th class="text-center">Número</th>
            <th class="text-center">Cambio</th>
            <th class="text-center">Descripción</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Precio</th>
            <th class="text-center">Subtotal S/</th>
            <th class="text-center">I.G.V. S/</th>
            <th class="text-center">Dscto. S/</th>
            <th class="text-center">Total S/</th>
            <th class="text-center">Total M. Ex.</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $ID_Entidad = '';
            $counter = 0;
            
            $subtotal_s = 0.00;
            $igv_s = 0.00;
            $descuento_s = 0.00;
            $total_s = 0.00;
            
            $sum_cantidad = 0.000000;
            $sum_subtotal_s = 0.00;
            $sum_descuento_s = 0.00;
            $sum_igv_s = 0.00;
            $sum_total_s = 0.00;
            $sum_total_d = 0.00;
            
            $sum_general_cantidad = 0.000000;
            $sum_general_subtotal_s = 0.00;
            $sum_general_descuento_s = 0.00;
            $sum_general_igv_s = 0.00;
            $sum_general_total_s = 0.00;
            $sum_general_total_d = 0.00;
            foreach($arrDetalle['arrData'] as $row) {
              if ($ID_Entidad != $row->ID_Entidad) {
                if ($counter != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="7">Total</th>
                    <th class="text-right"><?php echo numberFormat($sum_cantidad, 6, '.', ','); ?></th>
                    <th class="text-right">&nbsp;</th>
                    <th class="text-right"><?php echo numberFormat($sum_subtotal_s, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_igv_s, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_descuento_s, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_s, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_d, 2, '.', ','); ?></th>
                    <th class="text-right">&nbsp;</th>
                  </tr>
                  <?php
                  $sum_cantidad = 0.000000;
                  $sum_subtotal_s = 0.00;
                  $sum_igv_s = 0.00;
                  $sum_descuento_s = 0.00;
                  $sum_total_s = 0.00;
                  $sum_total_d = 0.00;
                } ?>
                <tr class="tr-theadFormat tr-theadFormat_header">
                  <th class="text-left">Delivery </th>
                  <th class="text-left" colspan="2"><?php echo $row->Nu_Documento_Identidad; ?></th>
                  <th class="text-left" colspan="12"><?php echo $row->No_Entidad; ?></th>
                </tr>
                <?php
                $ID_Entidad = $row->ID_Entidad;
              }
              if ($arrCabecera['iTipoReporte'] == 0) {
              ?>
                <tr class="tr-theadFormat">
                  <td class="text-center"><?php echo $row->Fe_Emision; ?></td>
                  <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                  <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                  <td class="text-right"><?php echo $row->ID_Numero_Documento; ?></td>
                  <td class="text-center"><?php echo $row->No_Signo; ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','); ?></td>
                  <td class="text-left"><?php echo $row->No_Producto; ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 6, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Precio, 6, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_SubTotal, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_IGV, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Descuento, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Total_Extranjero, 2, '.', ','); ?></td>
                  <td class="text-center"><?php echo $row->No_Estado; ?></td>
                  <td class="text-center"><?php echo $row->No_Estado_Delivery; ?></td>
                </tr>
                <?php
              }

              $sum_cantidad += $row->Qt_Producto;
              $sum_subtotal_s += $row->Ss_SubTotal;
              $sum_igv_s += $row->Ss_IGV;
              $sum_descuento_s += $row->Ss_Descuento;
              $sum_total_s += $row->Ss_Total;
              $sum_total_d += $row->Ss_Total_Extranjero;
              
              $sum_general_cantidad += $row->Qt_Producto;
              $sum_general_subtotal_s += $row->Ss_SubTotal;
              $sum_general_igv_s += $row->Ss_IGV;
              $sum_general_descuento_s += $row->Ss_Descuento;
              $sum_general_total_s += $row->Ss_Total;
              $sum_general_total_d += $row->Ss_Total_Extranjero;
                
              $counter++;
            } ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="7">Total</th>
              <th class="text-right"><?php echo numberFormat($sum_cantidad, 6, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_subtotal_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_igv_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_descuento_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_d, 2, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="7">Total General</th>
              <th class="text-right"><?php echo numberFormat($sum_general_cantidad, 6, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_general_subtotal_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_igv_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_descuento_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_d, 2, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
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