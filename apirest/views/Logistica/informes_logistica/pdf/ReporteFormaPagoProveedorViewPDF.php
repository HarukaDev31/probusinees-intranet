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
            <th align="center" colspan="2">Informe de Reporte Forma de Pago Proveedor</th>
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
            <th class="text-center" rowspan="2">Fecha Pago</th>
            <th class="text-center" colspan="3">Documento</th>
            <th class="text-center" colspan="3">Proveedor</th>
            <th class="text-center" colspan="2">Moneda</th>
            <th class="text-center" colspan="5">Forma Pago</th>
            <th class="text-center" rowspan="2">Estado</th>
          </tr>
          <tr class="tr-thead tr-theadFormat">
            <th class="text-center">Tipo</th>
            <th class="text-center">Serie</th>
            <th class="text-center">Número</th>
            <th class="text-center">Tipo</th>
            <th class="text-center"># Documento</th>
            <th class="text-center">Nombre</th>
            <th class="text-center">Tipo</th>
            <th class="text-center">T.C.</th>
            <th class="text-center">Medio Pago</th>
            <th class="text-center">Tipo Tarjeta</th>
            <th class="text-center">Nro. Tarjeta</th>
            <th class="text-center">Nro. Voucher</th>
            <th class="text-center">Total</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $arrData = $arrDetalle['arrData'];
            $fTotalItem = 0.00; $fTotalGeneral = 0.00;
            $ID_Almacen = 0; $fTotalGeneralAlmacen = 0.00; $counter_almacen = 0;
            foreach($arrData as $row) {
              if ($ID_Almacen != $row->ID_Almacen) {
                if ($counter_almacen != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="14">Total Almacén</th>
                    <th class="text-right"><?php echo numberFormat($fTotalGeneralAlmacen, 2, '.', ','); ?></th>
                  </tr>
                  <?php
                  $fTotalGeneralAlmacen = 0.00;
                } ?>

                <tr class="tr-theadFormat tr-theadFormat_footer">
                  <th class="text-right">Almacen</th>
                  <th class="text-left" colspan="14"><?php echo $row->No_Almacen; ?></th>
                </tr>
                <?php
                $ID_Almacen = $row->ID_Almacen;
              } ?>
              <tr class="tr-theadFormat">
                <td class="text-center"><?php echo $row->Fe_Emision_Hora; ?></td>
                <td class="text-center"><?php echo $row->Fe_Emision_Hora_Pago; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                <td class="text-center"><?php echo $row->ID_Numero_Documento; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Identidad_Breve; ?></td>
                <td class="text-center"><?php echo $row->Nu_Documento_Identidad; ?></td>
                <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                <td class="text-center"><?php echo $row->No_Signo; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->No_Medio_Pago; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Medio_Pago; ?></td>
                <td class="text-center"><?php echo $row->Nu_Tarjeta; ?></td>
                <td class="text-center"><?php echo $row->Nu_Transaccion; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->No_Estado; ?></td>
              </tr>
              <?php
              $fTotalGeneral += $row->Ss_Total;
              $fTotalGeneralAlmacen += $row->Ss_Total;
              $counter_almacen++;
            }// /. for each arrData ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="14">Total Almacén</th>
              <th class="text-right"><?php echo numberFormat($fTotalGeneralAlmacen, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="14">Total</th>
              <th class="text-right"><?php echo numberFormat($fTotalGeneral, 2, '.', ','); ?></th>
            </tr>
          <?php
        } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="17">No hay registros</td>
          </tr>
          <?php
        } ?>
        </tbody>
      </table>
    </body>
</html>