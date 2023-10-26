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
          font-size: 10px;
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
          font-size: 5.5px;
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
            <td align="right"><?php echo $arrCabecera['No_Almacen']; ?></td>
          </tr>
          <tr class="tr-theadFormatTitle">
            <th align="center" colspan="2">Stock Valorizado</th>
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
            <th class="text-left">Código</th>
            <th class="text-left">Nombre</th>
            <th class="text-center">Unidad Medida</th>
            <th class="text-right">Stock</th>
            <th class="text-right">Precio Venta</th>
            <?php if($arrCabecera['iEditarValor']==1){ ?>
            <th class="text-right">Precio Compra</th>
            <?php } ?>
            <th class="text-right">Costo Promedio</th>
            <th class="text-right">Total</th>
            <th class="text-left">Categoría</th>
            <th class="text-left">SubCategoría</th>
            <th class="text-left">Marca</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( count($arrDetalle) > 0) {
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_linea_cantidad = 0.00; $sum_almacen_linea_importe_promedio = 0.00;
            $ID_Familia = '';
            $counter = 0;
            $sum_linea_cantidad = 0.000000;
            $sum_cantidad = 0.000000;
            $sum_linea_importe_promedio = 0.000000;
            $sum_importe_promedio = 0.000000;
            foreach($arrDetalle as $row) {
              if (($ID_Familia != $row->ID_Familia || $ID_Almacen != $row->ID_Almacen) && $arrCabecera['iAgruparxCategoria']==1) {
                if ($counter != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="3">Total Categoría</th>
                    <th class="text-right"><?php echo numberFormat($sum_linea_cantidad, 3, '.', ','); ?></th>
                    <th class="text-left" colspan="3">&nbsp;</th>
                    <th class="text-right"><?php echo numberFormat($sum_linea_importe_promedio, 3, '.', ','); ?></th>
                  </tr>
                  <?php
                  $sum_linea_cantidad = 0.000000;
                  $sum_linea_importe_promedio = 0.000000;
                }
                
                if ($ID_Almacen != $row->ID_Almacen) {
                  if ($counter_almacen != 0) { ?>
                    <tr class="tr-theadFormat tr-theadFormat_footer">
                      <th class="text-right" colspan="3">Total Almacén</th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_linea_cantidad, 3, '.', ','); ?></th>
                      <th class="text-left" colspan="3">&nbsp;</th>
                      <th class="text-right"><?php echo numberFormat($sum_almacen_linea_importe_promedio, 3, '.', ','); ?></th>
                    </tr>
                    <?php
                    $sum_almacen_linea_cantidad = 0.000000;
                    $sum_almacen_linea_importe_promedio = 0.000000;
                  }?>
                  
                  <tr class="tr-theadFormat tr-theadFormat_header">
                    <th class="text-right" colspan="2">Almacén</th>
                    <th class="text-left" colspan="16"><?php echo $row->No_Almacen; ?></th>
                  </tr>
                  <?php
                  $ID_Almacen = $row->ID_Almacen;
                } ?>

                <tr class="tr-theadFormat tr-theadFormat_header">
                  <th class="text-right" colspan="2">Categoría</th>
                  <th class="text-left" colspan="16"><?php echo $row->No_Familia; ?></th>
                </tr>
                <?php
                $ID_Familia = $row->ID_Familia;
              } ?>

              <tr class="tr-theadFormat">
                <td class="text-left"><?php echo $row->Nu_Codigo_Barra; ?></td>
                <td class="text-left"><?php echo $row->No_Producto; ?></td>
                <td class="text-center"><?php echo $row->No_Unidad_Medida; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 3, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                <?php if($arrCabecera['iEditarValor']==1){ ?>
                  <td class="text-right"><?php echo numberFormat($row->Ss_Costo, 2, '.', ','); ?></td>
                <?php } ?>
                <td class="text-right"><?php echo numberFormat($row->Ss_Costo_Promedio, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total_Promedio, 2, '.', ','); ?></td>
                <td class="text-left"><?php echo $row->No_Familia; ?></td>
                <td class="text-left"><?php echo $row->No_Sub_Familia; ?></td>
                <td class="text-left"><?php echo $row->No_Marca; ?></td>
              </tr>
              <?php
              $sum_linea_cantidad += $row->Qt_Producto;
              $sum_almacen_linea_cantidad += $row->Qt_Producto;
              $sum_cantidad += $row->Qt_Producto;
              
              $sum_linea_importe_promedio += $row->Ss_Total_Promedio;
              $sum_almacen_linea_importe_promedio += $row->Ss_Total_Promedio;
              $sum_importe_promedio += $row->Ss_Total_Promedio;

              $counter++; $counter_almacen++;
            }
            if ($arrCabecera['iAgruparxCategoria']==1){?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="3">Total Categoría</th>
              <th class="text-right"><?php echo numberFormat($sum_linea_cantidad, 3, '.', ','); ?></th>
              <th class="text-left" colspan="3">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_linea_importe_promedio, 3, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="3">Total Almacén</th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_linea_cantidad, 3, '.', ','); ?></th>
              <th class="text-left" colspan="3">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_almacen_linea_importe_promedio, 3, '.', ','); ?></th>
            </tr>
            <?php } ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="3">Total General</th>
              <th class="text-right"><?php echo numberFormat($sum_cantidad, 3, '.', ','); ?></th>
              <th class="text-left" colspan="3">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($sum_importe_promedio, 3, '.', ','); ?></th>
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