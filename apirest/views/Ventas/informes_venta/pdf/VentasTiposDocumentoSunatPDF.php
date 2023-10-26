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
            <th align="center" colspan="2">Informe de Tipos de Documento</th>
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
            <th class="text-center" colspan="2">Boleta</th>
            <th class="text-center" colspan="2">Factura</th>
            <th class="text-center" colspan="2">N/Crédito</th>
            <th class="text-center" colspan="2">N/Débito</th>
            <th class="text-center" colspan="2">Total</th>
          </tr>
          <tr class="tr-thead tr-theadFormat">
            <th class="text-center">Trans.</th>
            <th class="text-center">Importe</th>
            <th class="text-center">Trans.</th>
            <th class="text-center">Importe</th>
            <th class="text-center">Trans.</th>
            <th class="text-center">Importe</th>
            <th class="text-center">Trans.</th>
            <th class="text-center">Importe</th>
            <th class="text-center">Trans.</th>
            <th class="text-center">Importe</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( count($arrDetalle) > 0) {
            $sum_cantidad_trans_b = 0;
            $sum_total_b = 0;
            $sum_cantidad_trans_f = 0;
            $sum_total_f = 0;
            $sum_cantidad_trans_nc = 0;
            $sum_total_nc = 0;
            $sum_cantidad_trans_nd = 0;
            $sum_total_nd = 0;
            $ID_Almacen = 0; $counter_almacen = 0; $sum_cantidad_trans_b_almacen = 0; $sum_total_b_almacen = 0.00; $sum_cantidad_trans_f_almacen = 0; $sum_total_f_almacen = 0.00; $sum_cantidad_trans_nc_almacen = 0; $sum_total_nc_almacen = 0.00; $sum_cantidad_trans_nd_almacen = 0; $sum_total_nd_almacen = 0.00;
            foreach($arrDetalle as $row) {
              if ($ID_Almacen != $row->ID_Almacen) {
                if ($counter_almacen != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right">Total</th>
                    <th class="text-right"><?php echo $sum_cantidad_trans_b_almacen; ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_b_almacen, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo $sum_cantidad_trans_f_almacen; ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_f_almacen, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo $sum_cantidad_trans_nc_almacen; ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_nc_almacen, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo $sum_cantidad_trans_nd_almacen; ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_nd_almacen, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo ($sum_cantidad_trans_b_almacen + $sum_cantidad_trans_f_almacen + $sum_cantidad_trans_nc_almacen + $sum_cantidad_trans_nd_almacen); ?></th>
                    <th class="text-right"><?php echo numberFormat($sum_total_b_almacen + $sum_total_f_almacen - $sum_total_nc_almacen + $sum_total_nd_almacen, 2, '.', ','); ?></th>
                  </tr>
                  <?php
                  $sum_cantidad_trans_b_almacen = 0;
                  $sum_total_b_almacen = 0;
                  $sum_cantidad_trans_f_almacen = 0;
                  $sum_total_f_almacen = 0;
                  $sum_cantidad_trans_nc_almacen = 0;
                  $sum_total_nc_almacen = 0;
                  $sum_cantidad_trans_nd_almacen = 0;
                  $sum_total_nd_almacen = 0;
                } ?>
                <tr class="tr-theadFormat tr-theadFormat_footer">
                  <th class="text-right">Almacen</th>
                  <th class="text-left" colspan="34"><?php echo $row->No_Almacen; ?></th>
                </tr>
                <?php
                $ID_Almacen = $row->ID_Almacen;
              } ?>
              <tr class="tr-theadFormat">
                <td class="text-center"><?php echo $row->Fe_Emision; ?></td>
                <td class="text-right"><?php echo $row->Nu_Cantidad_Trans_BOL; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_BOL, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo $row->Nu_Cantidad_Trans_FACT; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_FACT, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo $row->Nu_Cantidad_Trans_NC; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_NC, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo $row->Nu_Cantidad_Trans_ND; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_ND, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo ($row->Nu_Cantidad_Trans_BOL + $row->Nu_Cantidad_Trans_FACT + $row->Nu_Cantidad_Trans_NC + $row->Nu_Cantidad_Trans_ND); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_BOL + $row->Ss_Total_FACT - $row->Ss_Total_NC + $row->Ss_Total_ND, 2, '.', ','); ?></td>
              </tr>
              <?php
              if (!empty($row->Nu_Cantidad_Trans_BOL))
                $sum_cantidad_trans_b += $row->Nu_Cantidad_Trans_BOL;
              if (!empty($row->Ss_Total_BOL))
                $sum_total_b += $row->Ss_Total_BOL;
              if (!empty($row->Nu_Cantidad_Trans_FACT))
                $sum_cantidad_trans_f += $row->Nu_Cantidad_Trans_FACT;
              if (!empty($row->Ss_Total_FACT))
                $sum_total_f += $row->Ss_Total_FACT;
              if (!empty($row->Nu_Cantidad_Trans_NC))
                $sum_cantidad_trans_nc += $row->Nu_Cantidad_Trans_NC;
              if (!empty($row->Ss_Total_NC))
                $sum_total_nc += $row->Ss_Total_NC;
              if (!empty($row->Nu_Cantidad_Trans_ND))
                $sum_cantidad_trans_nd += $row->Nu_Cantidad_Trans_ND;
              if (!empty($row->Ss_Total_ND))
                $sum_total_nd += $row->Ss_Total_ND;
                    
              if (!empty($row->Nu_Cantidad_Trans_BOL))
                  $sum_cantidad_trans_b_almacen += $row->Nu_Cantidad_Trans_BOL;
              if (!empty($row->Ss_Total_BOL))
                  $sum_total_b_almacen += $row->Ss_Total_BOL;
              if (!empty($row->Nu_Cantidad_Trans_FACT))
                  $sum_cantidad_trans_f_almacen += $row->Nu_Cantidad_Trans_FACT;
              if (!empty($row->Ss_Total_FACT))
                  $sum_total_f_almacen += $row->Ss_Total_FACT;
              if (!empty($row->Nu_Cantidad_Trans_NC))
                  $sum_cantidad_trans_nc_almacen += $row->Nu_Cantidad_Trans_NC;
              if (!empty($row->Ss_Total_NC))
                  $sum_total_nc_almacen += $row->Ss_Total_NC;
              if (!empty($row->Nu_Cantidad_Trans_ND))
                  $sum_cantidad_trans_nd_almacen += $row->Nu_Cantidad_Trans_ND;
              if (!empty($row->Ss_Total_ND))
                  $sum_total_nd_almacen += $row->Ss_Total_ND;

              $counter_almacen++;
            } ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right">Total</th>
              <th class="text-right"><?php echo $sum_cantidad_trans_b_almacen; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_b_almacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo $sum_cantidad_trans_f_almacen; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_f_almacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo $sum_cantidad_trans_nc_almacen; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_nc_almacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo $sum_cantidad_trans_nd_almacen; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_nd_almacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo ($sum_cantidad_trans_b_almacen + $sum_cantidad_trans_f_almacen + $sum_cantidad_trans_nc_almacen + $sum_cantidad_trans_nd_almacen); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_b_almacen + $sum_total_f_almacen - $sum_total_nc_almacen + $sum_total_nd_almacen, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right">Total</th>
              <th class="text-right"><?php echo $sum_cantidad_trans_b; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_b, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo $sum_cantidad_trans_f; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_f, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo $sum_cantidad_trans_nc; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_nc, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo $sum_cantidad_trans_nd; ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_nd, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo ($sum_cantidad_trans_b + $sum_cantidad_trans_f + $sum_cantidad_trans_nc + $sum_cantidad_trans_nd); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_b + $sum_total_f - $sum_total_nc + $sum_total_nd, 2, '.', ','); ?></th>
            </tr>
          <?php
          } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="10">No hay registros</td>
          </tr>
          <?php
          } ?>
        </tbody>
      </table>
    </body>
</html>