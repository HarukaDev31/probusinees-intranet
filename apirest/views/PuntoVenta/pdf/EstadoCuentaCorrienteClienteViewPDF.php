<!DOCTYPE html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>laesystems</title>
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
            <th align="center" colspan="2">Informe de Estado de Cuenta Corriente del Cliente</th>
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
            <th class="text-center">T.C.</th>
            <th class="text-center">Medio Pago</th>
            <th class="text-center">Tipo Tarjeta</th>
            <th class="text-center">Nro. Tarjeta</th>
            <th class="text-center">Nro. Voucher</th>
            <th class="text-center">Total Pago S/</th>
            <th class="text-center">Total M. Ex.</th>
            <th class="text-center">Estado</th>
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
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                <td class="text-center"><?php echo $row->ID_Numero_Documento; ?></td>
                <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','); ?></td>
                <td class="text-left"><?php echo $row->No_Medio_Pago; ?></td>
                <td class="text-left"><?php echo $row->No_Tipo_Medio_Pago; ?></td>
                <td class="text-left"><?php echo $row->Nu_Tarjeta; ?></td>
                <td class="text-left"><?php echo $row->Nu_Transaccion; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_Extranjero, 2, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->No_Estado; ?></td>
              </tr>
              <?php
              $sum_general_total_s += $row->Ss_Total;
              $sum_general_total_d += $row->Ss_Total_Extranjero;
            }// /. for each arrData ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="10">Total</th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_general_total_d, 2, '.', ','); ?></th>
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