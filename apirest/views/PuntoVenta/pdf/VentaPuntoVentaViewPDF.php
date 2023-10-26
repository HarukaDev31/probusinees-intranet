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
          font-size: 6.5px;
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
            <th align="center" colspan="2">Informe de Ventas por Punto de Venta</th>
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
            <th class="text-center">Cajero</th>
            <th class="text-center">Recepción</th>
            <th class="text-center">Tipo</th>
            <th class="text-center">Serie</th>
            <th class="text-center">Número</th>
            <th class="text-center">Cliente</th>
            <th class="text-center">M.</th>
            <th class="text-center">Total</th>
            <th class="text-center">Saldo</th>
            <th class="text-center">Pago</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Glosa</th>
            <th class="text-center">Guia(s)</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $arrData = $arrDetalle['arrData'];
            $subtotal_s = 0.00; $descuento_s = 0.00; $igv_s = 0.00; $total_s = 0.00; $total_d = 0.00;
            $sum_general_subtotal_s=0.00; $sum_general_igv_s=0.00; $sum_general_descuento_s=0.00; $sum_general_total_s=0.00; $sum_general_total_d=0.00;
            foreach($arrData as $row) { ?>
              <tr class="tr-theadFormat">
                <td class="text-center"><?php echo $row->Fe_Emision_Hora; ?></td>
                <td class="text-center"><?php echo $row->No_Empleado; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Recepcion; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                <td class="text-center"><?php echo $row->ID_Numero_Documento; ?></td>
                <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                <td class="text-center"><?php echo $row->No_Signo; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_Saldo, 2, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->No_Estado_Pago; ?></td>
                <td class="text-center"><?php echo $row->No_Estado; ?></td>
                <td class="text-left"><?php echo $row->Txt_Glosa; ?></td>
                <td class="text-left"><?php echo $row->Txt_Garantia; ?></td>
              </tr>
              <?php
              $sum_general_total_s += $row->Ss_Total;
            }// /. for each arrData ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="8">Total</th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_s, 2, '.', ','); ?></th>
            </tr>
          <?php
        } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="12">No hay registros</td>
          </tr>
          <?php
        } ?>
        </tbody>
      </table>
    </body>
</html>