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
            <th align="center" colspan="2">Informe de Ventas Detalladas Generales</th>
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
            <th class="text-center" rowspan="2">Hora Emisión</th>
            <th class="text-center" rowspan="2">Personal / Cajero</th>
            <th class="text-center" colspan="3">Documento</th>
            <th class="text-center" colspan="3">Cliente</th>
            <th class="text-center" colspan="2">Moneda</th>
            <th class="text-center" colspan="13">Producto</th>
            <th class="text-center" colspan="4">Recepción</th>
            <th class="text-center" colspan="3">Guía</th>
            <th class="text-center" rowspan="2">Nota Global</th>
            <th class="text-center" rowspan="2">L. Precio</th>
            <th class="text-center" rowspan="2">Canal Venta</th>
            <th class="text-center" rowspan="2">O/C</th>
            <th class="text-center" rowspan="2">Placa</th>
            <th class="text-center" rowspan="2">Descuento ITEM</th>
            <th class="text-center" rowspan="2">Descuento TOTAL</th>
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
            <th class="text-center">Marca</th>
            <th class="text-center">Categoría</th>
            <th class="text-center">Sub Categoría</th>
            <th class="text-center">Unidad Medida</th>
            <th class="text-center">Código Barra</th>
            <th class="text-center">Nombre</th>
            <th class="text-center">Nota</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">CO2</th>
            <th class="text-center">Precio</th>
            <th class="text-center">Subtotal</th>
            <th class="text-center">Impuesto</th>
            <th class="text-center">Total</th>
            <th class="text-center">Tipo</th>
            <th class="text-center">Transporte</th>
            <th class="text-center">F. Entrega</th>
            <th class="text-center">Estado Pedido</th>
            <th class="text-center">Tipo</th>
            <th class="text-center">Serie</th>
            <th class="text-center">Número</th>
          </tr>
        </thead>
        <tbody>
        <?php
          if ( $arrDetalle['sStatus'] == 'success' ) {
            $arrData = $arrDetalle['arrData'];  
            $fCantidadItem = 0.00; $fPrecioItem = 0.00; $fSubtotalItem = 0.00; $fImpuestoItem = 0.00; $fTotalItem = 0.00; $fTotalDescuentoItem = 0.00; $fTotalDescuento = 0.00;
            $fCantidadTotalGeneral = 0.00; $fSubtotalGeneral = 0.00; $fImpuestoGeneral = 0.00; $fTotalGeneral = 0.00; $fTotalDescuentoItemGeneral = 0.00; $fTotalDescuentoGeneral = 0.00;
            $ID_Almacen = 0; $counter_almacen=0; $fCantidadTotalGeneralAlmacen = 0.00; $fSubtotalGeneralAlmacen = 0.00; $fImpuestoGeneralAlmacen = 0.00; $fTotalGeneralAlmacen = 0.00; $fTotalDescuentoItemAlmacen = 0.00; $fTotalDescuentoAlmacen = 0.00;
            foreach($arrData as $row) {
              if ($ID_Almacen != $row->ID_Almacen) {
                if ($counter_almacen != 0) { ?>
                  <tr class="tr-theadFormat tr-theadFormat_footer">
                    <th class="text-right" colspan="18">Total Almacén</th>
                    <th class="text-right"><?php echo numberFormat($fCantidadTotalGeneralAlmacen, 2, '.', ','); ?></th>
                    <th class="text-right">&nbsp;</th>
                    <th class="text-right">&nbsp;</th>
                    <th class="text-right"><?php echo numberFormat($fSubtotalGeneralAlmacen, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($fImpuestoGeneralAlmacen, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($fTotalGeneralAlmacen, 2, '.', ','); ?></th>
                    <th class="text-right" colspan="12">&nbsp;</th>
                    <th class="text-right"><?php echo numberFormat($fTotalDescuentoItemAlmacen, 2, '.', ','); ?></th>
                    <th class="text-right"><?php echo numberFormat($fTotalDescuentoAlmacen, 2, '.', ','); ?></th>
                  </tr>
                  <?php
                  $fCantidadTotalGeneralAlmacen = 0.00;
                  $fSubtotalGeneralAlmacen = 0.00;
                  $fImpuestoGeneralAlmacen = 0.00;
                  $fTotalGeneralAlmacen = 0.00;
                  $fTotalDescuentoItemAlmacen = 0.00;
                  $fTotalDescuentoAlmacen = 0.00;
                } ?>
                <tr class="tr-theadFormat tr-theadFormat_footer">
                  <th class="text-right">Almacen</th>
                  <th class="text-left" colspan="38"><?php echo $row->No_Almacen; ?></th>
                </tr>
                <?php
                $ID_Almacen = $row->ID_Almacen;
              } ?>
              <tr class="tr-theadFormat">
                <td class="text-center"><?php echo $row->Fe_Emision_Hora; ?></td>
                <td class="text-center"><?php echo $row->Fe_Hora; ?></td>
                <td class="text-left"><?php echo $row->No_Empleado; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Breve; ?></td>
                <td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
                <td class="text-center"><?php echo $row->ID_Numero_Documento; ?></td>
                <td class="text-center"><?php echo $row->No_Tipo_Documento_Identidad_Breve; ?></td>
                <td class="text-center"><?php echo $row->Nu_Documento_Identidad; ?></td>
                <td class="text-left"><?php echo $row->No_Entidad; ?></td>
                <td class="text-center"><?php echo $row->No_Signo; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','); ?></td>
                <td class="text-left"><?php echo $row->No_Marca; ?></td>
                <td class="text-left"><?php echo $row->No_Familia; ?></td>
                <td class="text-left"><?php echo $row->No_Sub_Familia; ?></td>
                <td class="text-left"><?php echo $row->No_Unidad_Medida; ?></td>
                <td class="text-left"><?php echo $row->Nu_Codigo_Barra; ?></td>
                <td class="text-left"><?php echo $row->No_Producto; ?></td>
                <td class="text-left"><?php echo $row->Txt_Nota_Item; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo $row->Qt_CO2_Producto; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Subtotal, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Impuesto, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                <td class="text-left"><?php echo $row->No_Tipo_Recepcion; ?></td>
                <td class="text-left"><?php echo $row->No_Delivery; ?></td>
                <td class="text-left"><?php echo $row->Fe_Entrega; ?></td>
                <td class="text-left"><?php echo $row->No_Estado_Delivery; ?></td>
                <td class="text-left"><?php echo $row->No_Tipo_Documento_Breve_Guia; ?></td>
                <td class="text-left"><?php echo $row->ID_Serie_Documento_Guia; ?></td>
                <td class="text-left"><?php echo $row->ID_Numero_Documento_Guia; ?></td>
                <td class="text-left"><?php echo $row->Txt_Nota; ?></td>
                <td class="text-left"><?php echo $row->No_Lista_Precio; ?></td>
                <td class="text-left"><?php echo $row->No_Canal_Venta; ?></td>
                <td class="text-left"><?php echo $row->No_Orden_Compra_FE; ?></td>
                <td class="text-left"><?php echo $row->No_Placa_FE; ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Descuento_Producto, 2, '.', ','); ?></td>
                <td class="text-right"><?php echo numberFormat($row->Ss_Descuento_Global, 2, '.', ','); ?></td>
                <td class="text-center"><?php echo $row->No_Estado; ?></td>
              </tr>
              <?php
              $fCantidadTotalGeneral += $row->Qt_Producto;
              $fSubtotalGeneral += $row->Ss_Subtotal;
              $fImpuestoGeneral += $row->Ss_Impuesto;
              $fTotalGeneral += $row->Ss_Total;
              $fTotalDescuentoItemGeneral += $row->Ss_Descuento_Producto;
              $fTotalDescuentoGeneral += $row->Ss_Descuento_Global;
              
              $fCantidadTotalGeneralAlmacen += $row->Qt_Producto;
              $fSubtotalGeneralAlmacen += $row->Ss_Subtotal;
              $fImpuestoGeneralAlmacen += $row->Ss_Impuesto;
              $fTotalGeneralAlmacen += $row->Ss_Total;
              $fTotalDescuentoItemAlmacen += $row->Ss_Descuento_Producto;
              $fTotalDescuentoAlmacen += $row->Ss_Descuento_Global;

              $counter_almacen++;
            }// /. for each arrData ?>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="18">Total</th>
              <th class="text-right"><?php echo numberFormat($fCantidadTotalGeneralAlmacen, 2, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($fSubtotalGeneralAlmacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($fImpuestoGeneralAlmacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($fTotalGeneralAlmacen, 2, '.', ','); ?></th>
              <th class="text-right" colspan="12">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($fTotalDescuentoItemAlmacen, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($fTotalDescuentoAlmacen, 2, '.', ','); ?></th>
            </tr>
            <tr class="tr-theadFormat tr-theadFormat_footer">
              <th class="text-right" colspan="18">Total</th>
              <th class="text-right"><?php echo numberFormat($fCantidadTotalGeneral, 2, '.', ','); ?></th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($fSubtotalGeneral, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($fImpuestoGeneral, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($fTotalGeneral, 2, '.', ','); ?></th>
              <th class="text-right" colspan="12">&nbsp;</th>
              <th class="text-right"><?php echo numberFormat($fTotalDescuentoItemGeneral, 2, '.', ','); ?></th>
              <th class="text-right"><?php echo numberFormat($fTotalDescuentoGeneral, 2, '.', ','); ?></th>
            </tr>
          <?php
        } else { ?>
          <tr class="tr-theadFormat">
            <td class="text-center" colspan="38">No hay registros</td>
          </tr>
          <?php
        } ?>
        </tbody>
      </table>
    </body>
</html>