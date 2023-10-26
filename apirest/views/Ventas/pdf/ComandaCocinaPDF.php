<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<title>Laesystems</title>
      <style type=text/css>
        .table_pdf {
          width: 100%;
        }
    
        .tr-thead th.title {
          color: #000000;
          font-size: 19px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
        }

        .tr-thead th.sub_title_fecha {
          color: #000000;
          font-size: 10px;
          font-family: "Arial", Helvetica, sans-serif;
        }
        
        .tr-thead th.sub_title_nro {
          color: #000000;
          font-size: 10px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
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
          font-size: 8px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: normal;
        }

        .tr-sub_thead th.content {
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
        }
        
        .tr-theadFormat th{
          font-weight: bold;
          font-family: Arial;
        }
        
        .tr-header-detalle th{
          font-size: 9px;
          font-family: Arial;
          background-color: #F2F5F5;
        }

        .tr-header th{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #F2F5F5;
        }
        
        .tr-footer th{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .tr-totales th{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .tr-fila_impar td{
          font-size: 8px;
          font-family: Arial;
          background-color: #F2F5F5;
        }
        
        .tr-fila_par td{
          font-size: 8px;
          font-family: Arial;
          background-color: #FFFFFF;
        }
        
        .tr-importe_letras{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
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
            <th class="text-center title"><?php echo 'COMANDA COCINA - ' . $arrData[0]->ID_Pedido_Cabecera; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-center title"><?php echo $arrData[0]->No_Escenario_Restaurante . ' - ' . $arrData[0]->No_Mesa_Restaurante; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-center title"><?php echo $arrData[0]->No_Mesero; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <td class="text-center title">F. EMISIÓN: <b><?php echo allTypeDate($arrData[0]->Fe_Emision_Hora, '-', 0); ?></b></td>
          </tr>
        </thead>
      </table>
      <br><br>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 15%">CANT.</th>
            <th class="text-left title" style="width: 85%">DESCRIPCION</th>
          </tr>
        </thead>
        <tbody>
          <?php
            foreach($arrData as $row) {
              if ($row->Nu_Imprimir_Comanda_Restaurante == 1) {
                $fCantidadItem = $row->Qt_Producto; ?>
                <tr class="tr-sub_thead">
                  <td class="text-left item" style="width: 15%"><?php echo number_format($fCantidadItem, 2, '.', ','); ?></td>
                  <td class="text-left item" style="width: 85%"><?php echo nl2br((!empty($row->No_Codigo_Interno) ? '[' .  $row->No_Codigo_Interno . '] ' : '') . $row->No_Producto . (!empty($row->Txt_Nota_Item) ? $row->Txt_Nota_Item : '')); ?></td>
                </tr>
                <?php
              }
            } // foreach
            ?>
        </tbody>
      </table>
    </body>
</html>