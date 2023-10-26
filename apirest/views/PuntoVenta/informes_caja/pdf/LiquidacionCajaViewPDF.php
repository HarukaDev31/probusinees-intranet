<!DOCTYPE html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>Laeystems</title>
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
            <th align="center" colspan="2">Informe de Liquidación de Caja</th>
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
            <th class="text-left">Organización</th>
            <th class="text-left">Almacén</th>
            <th class="text-left">Personal</th>
            <th class="text-center">F. Apertura</th>
            <th class="text-center">F. Cierre</th>
            <th class="text-right">M</th>
            <th class="text-right">Total a Liquidar</th>
            <th class="text-right">Total Depositado</th>
            <th class="text-right">Diferencia</th>
            <th class="text-left">Nota</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            foreach($arrDetalle['arrData'] as $row) { ?>
              <tr class="tr-theadFormat">
                <td class="text-left"><?php echo $row->No_Organizacion; ?></td>
                <td class="text-left"><?php echo $row->No_Almacen; ?></td>
                <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                <td class="text-center"><?php echo $row->Fe_Apertura; ?></td>
                <td class="text-center"><?php echo $row->Fe_Cierre; ?></td>
                <td class="text-right"><?php echo $row->No_Signo; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Expectativa, 3, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Diferencia, 2, '.', ','); ?></td>
                <td class="text-left"><?php echo $row->Txt_Nota; ?></td>
              </tr>
              <?php
            }// /. for each arrData
            ?>
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