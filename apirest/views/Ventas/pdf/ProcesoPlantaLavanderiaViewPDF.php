<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>Vista Preliminar</title>
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
            <th align="center" colspan="2">Estado de Lavado</th>
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
            <th class="text-center">Total</th>
            <th class="text-center">Saldo</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Estado Lavado</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $total_s = 0.00; $total_s_saldo = 0.00; $sum_total_s = 0.00; $sum_total_s_saldo = 0.00;
            foreach($arrDetalle['arrData'] as $row) { ?>
              <tr class="tr-theadFormat">
                <td class="text-center"><?php echo $row->Fe_Emision_Hora; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                <td class="text-center"><?php echo $row->ID_Numero_Documento; ?></td>
                <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                <td class="text-center"><?php echo $row->No_Signo; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_Saldo, 2, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->No_Estado; ?></td>
                <td class="text-center"><?php echo $row->No_Estado_Lavado; ?></td>
              </tr>
              <?php
              $sum_total_s += $row->Ss_Total;
              $sum_total_s_saldo += $row->Ss_Total_Saldo;
            }// /. foreach ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="6">Total</th>
              <th class="text-right"><?php echo numberFormat($sum_total_s, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($sum_total_s_saldo, 2, '.', ','); ?></th>
            </tr>
          <?php
        } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="9"><?php echo $arrDetalle['sMessage']; ?></td>
          </tr>
          <?php
        } ?>
        </tbody>
      </table>
    </body>
</html>