<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
		<standar>Laesystems</standar>
      <style type=text/css>
        .table_pdf {
          width: 100%;
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
      <?php
      $sTipoDocumento = strtoupper($arrData[0]->No_Tipo_Documento) . ($arrData[0]->ID_Tipo_Documento != 2 ? ' ELECTRÓNICA' : '');
      ?>
	  	<table class="table_pdf" border="0">
        <thead>
          <?php if (!empty($sUrlImagen)) { ?>
            <tr class="tr-head">
              <th class="text-center standar"><img style="height: 30px; width: 50px;" src="<?php echo $sUrlImagen; ?>"></th> <!-- style="height: 80 px; width: 80px;"-->
              <!--<th class="text-center standar"><img style="height: <?php echo $arrDataEmpresa[0]->Nu_Height_Logo_Ticket; ?> px; width: <?php echo $arrDataEmpresa[0]->Nu_Width_Logo_Ticket; ?>px;" src="<?php echo $sUrlImagen; ?>"></th>-->
            </tr>
          <?php
          } ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold" style="font-size: 6.5px;"><?php echo $arrData[0]->No_Tienda_Lae_Shop; ?></th>
          </tr>
          <?php if ( !empty($arrData[0]->No_Dominio_Empresa) ) { ?>
          <tr class="tr-head">
            <td class="text-center standar"><?php echo $arrData[0]->No_Dominio_Empresa; ?></td>
          </tr>
          <?php } 
          if ( !empty($arrData[0]->Txt_Email_Empresa) ) { ?>
          <tr class="tr-head">
            <td class="text-center standar"><?php echo $arrData[0]->Txt_Email_Empresa; ?></td>
          </tr>
          <?php }
          if ( !empty($arrData[0]->Nu_Celular_Empresa)) { ?>
          <tr class="tr-head">
            <td class="text-center standar"><?php echo $arrData[0]->Nu_Celular_Empresa; ?></td>
          </tr>
          <?php } ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold"><?php echo $sTipoDocumento . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento;?></th>
          </tr>
        </thead>
      </table>
      <br><br>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-head">
            <td class="text-left standar" style="width: 30%;"><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve; ?></td>
            <td class="text-left standar" style="width: 70%;"><?php echo $arrData[0]->Nu_Documento_Identidad; ?></td>
          </tr>
          <tr class="tr-head">
            <td class="text-left standar" colspan="2" style="width: 100%;"><?php echo $arrData[0]->No_Entidad; ?></td>
          </tr>
          <tr class="tr-head">
            <td class="text-left standar" colspan="2" style="width: 100%;"><?php echo $arrData[0]->Txt_Direccion_Entidad; ?></td>
          </tr>
          <tr class="tr-head">
            <td class="text-left standar" style="width: 23%;"><b>F. EMISIÓN</b></td>
            <td class="text-left standar" style="width: 36%;"><?php echo allTypeDate($arrData[0]->Fe_Emision_Hora, '-', 0); ?></td>
          </tr>
        </thead>
      </table>
      <br><br>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-head">
            <th class="text-left standar title_bold" style="width: 23%">CANT.</th>
            <th class="text-left standar title_bold" style="width: 45%">DESCRIPCION</th>
            <th class="text-right standar title_bold" style="width: 14%">P.U.</th>
            <th class="text-right standar title_bold" style="width: 18%">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $sNombreBreveLaboratorio='';
            $Ss_SubTotal_Producto = 0.00;
            $fOperacionesGravadas = 0.00;
            $fOperacionesGravadasConImpuesto = 0.00;
            $fIGV = 0.00;
            $fOperacionesInafectas = 0.00;
            $fOperacionesExoneradas = 0.00;
            $fTotalIcbper = 0.00;
            $fCantidadItem = 0.000;
            $sPorecentajeDescuento = "0.00 %";
            $fGratuita=0.00;

            $iNumImpuestoDescuento = 0;
            $iNumImpuestoDescuentoIGV = 0;
            $iNumImpuestoDescuentoEXO = 0;
            $fImpuestoConfiguracionIGV = 1;
            $fDescuentoItem = 0;
            foreach($arrData as $row) {
              $sCodigoInterno = (!empty($row->No_Codigo_Interno) ? $row->No_Codigo_Interno . '-' : '');

              $sNombreBreveLaboratorio='';
              if ($arrDataEmpresa[0]->Nu_Tipo_Rubro_Empresa == 1) {
                $sCodigoInterno = '';
                if (!empty($row->No_Laboratorio_Breve))
                  $sNombreBreveLaboratorio= ' ' . $row->No_Laboratorio_Breve;
              }

              $Ss_SubTotal_Producto = $row->Ss_SubTotal_Producto;
              if ($row->Nu_Tipo_Impuesto == "1" && $row->ID_Impuesto_Icbper != '1') {//IGV
                $fOperacionesGravadas += $Ss_SubTotal_Producto;
                $fOperacionesGravadasConImpuesto += $row->Ss_Total_Producto;
                $fIGV += $row->Ss_Impuesto_Producto;
                $sPorecentajeDescuento = $row->Po_Impuesto . ' %';
                $iNumImpuestoDescuentoIGV = 1;

                $fImpuestoConfiguracionIGV = $row->Ss_Impuesto;
                $fDescuentoItem += $row->Ss_Descuento_Producto;
              }
                
              if ($row->Nu_Tipo_Impuesto == 2)//INA
                $fOperacionesInafectas += $Ss_SubTotal_Producto;
              else if ($row->Nu_Tipo_Impuesto == 3) {//EXO
                $fOperacionesExoneradas += $Ss_SubTotal_Producto;
                $iNumImpuestoDescuentoEXO = 1;
                $fDescuentoItem += $row->Ss_Descuento_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
                $fGratuita += $row->Ss_SubTotal_Producto;
              }
                
              if ($row->ID_Impuesto_Icbper == 1)
                $fTotalIcbper += $row->Ss_Icbper;
                
              $fCantidadItem = $row->Qt_Producto;

              $sImpresionProducto = $row->No_Producto;
              if ($arrDataEmpresa[0]->Nu_Imprimir_Columna_Ticket_Detalle == 1) {//codigo + nombre
                $sImpresionProducto = $row->Nu_Codigo_Barra . ' ' . $row->No_Producto;
              } else if ($arrDataEmpresa[0]->Nu_Imprimir_Columna_Ticket_Detalle == 2) {//codigo + nombre + marca
                $sImpresionProducto = $row->Nu_Codigo_Barra . ' ' . $row->No_Producto;
                if (!empty($row->No_Marca))
                  $sImpresionProducto = $row->Nu_Codigo_Barra . ' ' . $row->No_Producto . ' [' . $row->No_Marca . ']';
              }
              ?>
              <tr class="tr-head">
                <td class="text-left standar" style="width: 23%"><?php echo number_format($fCantidadItem, 2, '.', '') . ' ' . $row->nu_codigo_unidad_medida_sunat; ?></td>
                <td class="text-left standar" style="width: 45%"><?php echo nl2br($sCodigoInterno . $sImpresionProducto . $sNombreBreveLaboratorio . (!empty($row->Txt_Nota_Item) ? ' ' . $row->Txt_Nota_Item : '')); ?></td>
                <td class="text-right standar" style="width: 14%"><?php echo numberFormat($row->ss_precio_unitario, 2, '.', ''); ?></td>
                <td class="text-right standar" style="width: 18%"><?php echo numberFormat($row->Ss_Total_Producto, 2, '.', ''); ?></td>
              </tr>
              <?php
            } // foreach
            $iNumImpuestoDescuento = ($iNumImpuestoDescuentoIGV + $iNumImpuestoDescuentoEXO);
            ?>
        </tbody>
      </table>
      <br><br>
	  	<table class="table_pdf" border="0">
        <thead>
          <?php
          if($arrData[0]->Ss_Descuento_Total>0.00){
            $fDescuentoTotalOperacion = ($arrData[0]->Ss_Descuento_Total / $iNumImpuestoDescuento);

            if ( $iNumImpuestoDescuentoEXO == 1 ) {
              $fOperacionesExoneradas = $fOperacionesExoneradas - $fDescuentoTotalOperacion;
            }

            if ( $iNumImpuestoDescuentoIGV == 1 ) {
              $fOperacionesGravadas = round(($fOperacionesGravadasConImpuesto - $fDescuentoTotalOperacion) / $fImpuestoConfiguracionIGV, 2);
              $fIGV = ($fOperacionesGravadas * $fImpuestoConfiguracionIGV) - $fOperacionesGravadas;
            }
          }
          if($arrData[0]->Ss_Descuento_Total>0.00 || $fDescuentoItem > 0.00){ ?>
          <tr class="tr-head">
            <th class="text-right standar title_bold" style="width: 75%">DSCTO. TOTAL (-)</th>
            <th class="text-right standar title_bold" style="width: 25%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($arrData[0]->Ss_Descuento_Total + $arrData[0]->Ss_Descuento_Impuesto + $fDescuentoItem, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-head">
            <th class="text-right standar title_bold" style="width: 75%;">TOTAL</th>
            <th class="text-right standar title_bold" style="width: 25%;"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($arrData[0]->Ss_Total - $fGratuita, 2, '.', ','); ?></th>
          </tr>
        </thead>
      </table>
      
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-head">
            <th class="text-left standar" colspan="2">&nbsp;</th>
          </tr>

          <?php if ($arrData[0]->ID_Tipo_Documento != '2') { ?>
          <tr class="tr-head">
            <td class="text-left standar" colspan="2" style="width: 100%"><b>SON:</b> <?php echo $totalEnLetras; ?></td>
          </tr>
          <?php
          } ?>
          
          <?php
            if ($arrData[0]->No_Codigo_Medio_Pago_Sunat_PLE != '0') { ?>
              <tr class="tr-head">
                <td class="text-left standar" colspan="2" style="width: 100%"><b>FORMA DE PAGO:</b> <?php echo $sConcatenarMultiplesMedioPago; ?></td>
              </tr>
            <?php } else { ?>
              <tr class="tr-head">
                <td class="text-left standar" colspan="2" style="width: 100%"><b>FORMA DE PAGO:</b> <?php echo $sConcatenarMultiplesMedioPago . '<br><b>CUOTA 1 </b>' . $arrData[0]->Fe_Vencimiento . ' ' . $arrData[0]->No_Signo . ' ' . numberFormat($arrData[0]->Ss_Total_Saldo, 2, '.', ','); ?></td>
              </tr>
            <?php
            } ?>

          <?php if (!empty($arrData[0]->Txt_Glosa_Global)) { ?>
          
          <tr class="tr-head">
            <th class="text-left standar" colspan="2">&nbsp;</th>
          </tr>

          <tr class="tr-head">
            <td class="text-left standar" colspan="2" style="width: 100%"><b>OBSERVACIONES:</b> <?php echo $arrData[0]->Txt_Glosa_Global; ?></td>
          </tr>
          <?php } ?>

          <?php
          if ($arrData[0]->Nu_Tipo_Recepcion == '6'){// Negocio Delivery ?>
            <tr class="tr-head">
              <th class="text-center standar" colspan="2">&nbsp;</th>
            </tr>
            <tr class="tr-head">
              <td class="text-left standar" colspan="2" style="width: 100%"><b>DELIVERY</b></td>
            </tr>
          <?php } else if ($arrData[0]->Nu_Tipo_Recepcion == '7') { ?>
            <tr class="tr-head">
              <th class="text-center standar" colspan="2">&nbsp;</th>
            </tr>
            <tr class="tr-head">
              <td class="text-left standar" colspan="2" style="width: 100%"><b>RECOJO EN TIENDA</b></td>
            </tr>
          <?php }
          if ($arrData[0]->Nu_Tipo_Recepcion != '5' && !empty($arrData[0]->Nu_Celular_Entidad)) { ?>
          <tr class="tr-head">
            <td class="text-left standar" colspan="2" style="width: 100%"><b>CELULAR:</b> <?php echo $arrData[0]->Nu_Celular_Entidad; ?></td>
          </tr>
          <?php }
          if ($arrData[0]->Nu_Tipo_Recepcion != '5' && !empty($arrData[0]->Txt_Direccion_Entidad)) { ?>
          <tr class="tr-head">
            <td class="text-left standar" colspan="2" style="width: 100%"><b>DIRECCIÓN:</b> <?php echo $arrData[0]->Txt_Direccion_Entidad; ?></td>
          </tr>
          <?php } ?>
          
          <tr class="tr-head">
            <th class="text-center standar" colspan="2">&nbsp;</th>
          </tr>

          <tr class="tr-head">
            <td class="text-center standar" colspan="2" style="width: 100%">Generado por <b>ecxpresslae.com</b></td>
          </tr>
        </thead>
      </table>
    </body>
</html>