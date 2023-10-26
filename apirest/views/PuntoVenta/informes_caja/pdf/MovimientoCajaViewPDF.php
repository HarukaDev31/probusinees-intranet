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
          font-size: 7px;
          border: solid 0.5px #000000;
        }
        
        .success {background-color: #dff0d8;}
        .danger {background-color: #f2dede;}
        
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
            <th align="center" colspan="2">Informe de Caja Ingresos y Egresos PV</th>
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
            <th class="text-center">Almacén</th>
            <th class="text-center">Tipo Operación</th>
            <th class="text-center">Fe. Movimiento</th>
            <th class="text-center">Moneda</th>
            <th class="text-center">Total</th>
            <th class="text-center">Nota</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $iCounter = 0; $ID_Empleado = ''; $fTotal = 0.00; $fTotalIngresos = 0.00; $fTotalEgresos = 0.00; $fSumGeneralTotalIngresos = 0.00; $fSumGeneralTotalEgresos = 0.00;
            foreach($arrDetalle['arrData'] as $row) {
              if ($ID_Empleado != $row->ID_Empleado) {
                if ($iCounter != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="4">Total Ingresos</th>
                    <th class="text-right"><?php echo numberFormat($fTotalIngresos, 2, '.', ','); ?></th>
                  </tr>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="4">Total Egresos</th>
                    <th class="text-right"><?php echo numberFormat($fTotalEgresos, 2, '.', ','); ?></th>
                  </tr>
                  <?php
                  $fTotalIngresos = 0.00;
                  $fTotalEgresos = 0.00;
                } ?>
                <tr class="tr-theadFormat tr-theadFormat_header">
                  <th class="text-center">Personal </th>
                  <th class="text-left" colspan="6"><?php echo $row->No_Empleado; ?></th>
                </tr>
                <?php
                $ID_Empleado = $row->ID_Empleado;
              }
              
              if($row->Nu_Tipo == '5') {//Ingresos
                $fTotalIngresos += (!empty($row->Ss_Total) ? $row->Ss_Total : 0.00);
                $fSumGeneralTotalIngresos += $row->Ss_Total;
              } else {
                $fTotalEgresos += (!empty($row->Ss_Total) ? $row->Ss_Total : 0.00);
                $fSumGeneralTotalEgresos += $row->Ss_Total;
              }
            ?>
              <tr class="tr-theadFormat">
                <td class="text-center"><?php echo $row->No_Almacen; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Operacion_Caja; ?></td>
                <td class="text-center"><?php echo $row->Fe_Movimiento; ?></td>
                <td class="text-center"><?php echo $row->No_Signo; ?></td>
                <td class="text-right"><?php echo numberFormat((!empty($row->Ss_Total) ? $row->Ss_Total : 0.00), 2, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->Txt_Nota; ?></td>
              </tr>
            <?php
              
              $iCounter++;
            } ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="4">Total Ingresos</th>
              <th class="text-right"><?php echo numberFormat($fTotalIngresos, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="4">Total Egresos</th>
              <th class="text-right"><?php echo numberFormat($fTotalEgresos, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="4">Total General Ingresos</th>
              <th class="text-right"><?php echo numberFormat($fSumGeneralTotalIngresos, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="4">Total General Egresos</th>
              <th class="text-right"><?php echo numberFormat($fSumGeneralTotalEgresos, 2, '.', ','); ?></th>
            </tr>
          <?php
          } else { ?>
            <tr class="tr-theadFormat">
              <td class="text-center" colspan="6">No hay registros</td>
            </tr>
          <?php
          } ?>
        </tbody>
      </table>
    </body>
</html>