<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>Laesystems</title>
      <style type=text/css>
        .table_pdf {
          width: 100%;
        }

        .tr-sub_thead th.title {
          color: #000000;
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
        }

        .tr-sub_thead td.title {
          color: #000000;
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: normal;
        }

        .tr-sub_thead td.item {
          color: #000000;
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: normal;
        }

        .tr-head th.standar, .tr-head td.standar {
          color: #000000;
          font-family: "Arial", Helvetica, sans-serif;
          font-size: 7px;
        }

        .tr-head th.title_bold, .tr-head td.title_bold {
          font-weight: bold;
        }
        
        .text-left{text-align: left;}
        .text-center{text-align: center;}
        .text-right{text-align: right;}
      </style>
    </head>
    <body>
	  	<table class="table_pdf" border="0">
        <thead>
          <?php
          if (!empty($arrData[0]->No_Empresa_Comercial)) { ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold"><?php echo $arrData[0]->No_Empresa_Comercial; ?></th>
          </tr>
          <?php } else { ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold" style="font-size: 6.5px;"><?php echo $arrData[0]->No_Empresa; ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-sub_thead">
            <th class="text-center title"><?php echo strtoupper($arrData[0]->No_Tipo_Operacion_Caja) . ' - ' . $arrData[0]->ID_Caja_Pos; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-center title"><?php echo $arrData[0]->No_Empleado; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <td class="text-center title">F. EMISIÓN: <b><?php echo allTypeDate($arrData[0]->Fe_Movimiento, '-', 0); ?></b></td>
          </tr>
        </thead>
      </table>
      <br><br>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 20%">MONEDA</th>
            <th class="text-left title" style="width: 20%">IMPORTE</th>
            <th class="text-left title" style="width: 60%">NOTA</th>
          </tr>
        </thead>
        <tbody>
          <tr class="tr-sub_thead">
            <td class="text-left item" style="width: 20%"><?php echo $arrData[0]->No_Signo; ?></td>
            <td class="text-left item" style="width: 20%"><?php echo number_format($arrData[0]->Ss_Total, 2, '.', ','); ?></td>
            <td class="text-left item" style="width: 60%"><?php echo $arrData[0]->Txt_Nota; ?></td>
          </tr>
        </tbody>
      </table>
    </body>
</html>