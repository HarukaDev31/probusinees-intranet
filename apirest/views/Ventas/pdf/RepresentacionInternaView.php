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
          font-size: 10px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
        }

        .tr-sub_thead th.content {
          font-size: 8.5px;
          font-family: "Arial", Helvetica, sans-serif;
        }

        .datos-cliente{
          border-style: solid;
          border-width: 1px;
          border-top-color:#7d7d7d;
        }

        .datos-cliente-right{
          border-style: solid;
          border-width: 1px;
          border-right-color:#7d7d7d;
        }
        
        .tr-theadFormat th{
          font-weight: bold;
          font-family: Arial;
        }
        
        .tr-header-detalle th{
          font-size: 9px;
          font-family: Arial;
          font-weight: bold;
          background-color: #e4e4e4;
          border-color: #7d7d7d;
          border-width: 1px;
          border-bottom-color:#7d7d7d;
        }
        
        .border-left{
          border-left-color:#7d7d7d;
        }
        
        .border-right{
          border-right-color:#7d7d7d;
        }
        
        .border-bottom{
          border-bottom-color:#7d7d7d;
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
          border-width: 1px;
          border-bottom-color:#7d7d7d;
        }
        
        .tr-fila_par td{
          font-size: 8px;
          font-family: Arial;
          background-color: #FFFFFF;
          border-width: 1px;
          border-bottom-color:#7d7d7d;
        }
        
        .tr-importe_letras th{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
          /*
          border-color: #7d7d7d;
          border-width: 1px;
          border-style: solid;
          */
        }
        
        .tr-otros_campos_footer{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .text-left{text-align: left;}
        .text-center{text-align: center;}
        .text-right{text-align: right;}
      </style>
    </head>
    <body>
      <?php
      $Po_IGV = "";
      foreach($arrData as $row)
        $Po_IGV = "18 %";
      ?>
	  	<table class="table_pdf" border="0" cellpadding="0" cellspacing="0">
        <thead>
          <tr class="tr-sub_thead" style="line-height: 60%;">
            <th class="datos-cliente border-right border-left" style="width: 55%;">&nbsp;</th>
            <th class="border-right" style="width: 5%">&nbsp;</th>
            <th class="datos-cliente border-right" style="width: 40%;">&nbsp;</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content border-right border-left" style="width: 55%;">&nbsp;&nbsp;<b>CLIENTE</b></th>
            <th class="text-left title " style="width: 5%;"></th>
            <th class="text-left content border-right border-left" style="width: 40%;">&nbsp;&nbsp;<b>FECHA EMISIÓN: </b><?php echo ToDateBD($arrData[0]->Fe_Emision); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content border-left" style="width: 15%;">&nbsp;&nbsp;<b><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve; ?></b></th>
            <th class="text-left content border-right" style="width: 40%;">: <?php echo $arrData[0]->Nu_Documento_Identidad; ?></th>
            <th class="text-left title" style="width: 5%;"></th>
            <th class="text-left content border-right border-left" style="width: 40%;">&nbsp;&nbsp;<b>FECHA DE VENC: </b><?php echo ToDateBD($arrData[0]->Fe_Vencimiento); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content border-left" style="width: 15%;">&nbsp;&nbsp;<b>DENOMINACIÓN</b></th>
            <th class="text-left content border-right" style="width: 40%;">: <?php echo $arrData[0]->No_Entidad; ?></th>
            <th class="text-left title" style="width: 5%;"></th>
            <th class="text-left content border-right border-left" style="width: 40%;">&nbsp;&nbsp;<b>MONEDA: </b><?php echo $arrData[0]->No_Moneda; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content border-left border-bottom" style="width: 15%;">&nbsp;&nbsp;<b>DIRECCIÓN</b></th>
            <th class="text-left content border-right border-bottom" style="width: 40%;">: <?php echo $arrData[0]->Txt_Direccion_Entidad; ?></th>
            <th class="text-left title" style="width: 5%;"></th>
            <th class="text-left content border-right border-left border-bottom" style="width: 40%;">&nbsp;&nbsp;<?php echo (!empty($Po_IGV) ? '<b>IGV: </b>' . $Po_IGV : '-'); ?></th>
          </tr>
        </thead>
      </table>
      
	  	<table class="table_pdf" cellpadding="4">
        <thead>
          <tr style="line-height: 10%;">
            <th colspan="6" style="line-height: 10%;"></th>
          </tr>
          <tr class="tr-header-detalle">
            <th class="text-center border-left" style="width: 10%;">CANT.</th>
            <th class="text-left" style="width: 45%;">DESCRIPCIÓN</th>
            <th class="text-right" style="width: 10%;;">V.U.</th>
            <th class="text-right" style="width: 10%;">PRECIO</th>
            <th class="text-right" style="width: 10%;">DSCTO</th>
            <th class="text-right border-right" style="width: 15%;">IMPORTE</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $Ss_Gravada = 0.00;
            $Ss_Exonerada = 0.00;
            $Ss_Inafecto = 0.00;
            $Ss_Gratuita = 0.00;
            $Ss_IGV = 0.00;
            $Ss_Total = 0.00;           
            $iCounter = 0; 
            $sNameClassTrDetalle = '';
            $Ss_Impuesto = 0.00;
            $Ss_Gravada = 0.00;
            $fTotalIcbper = 0.00;
            $fDescuentoTotalItem = 0;
            foreach($arrData as $row) {
              $sNameClassTrDetalle = ($iCounter%2) == 0 ? 'tr-fila_par' : 'tr-fila_impar';

              if ( $row->ID_Impuesto_Icbper == 1 )
                $fTotalIcbper += $row->Ss_Icbper;

              $Ss_Precio_VU = $row->Ss_Precio;
              if ($arrData[0]->ID_Tipo_Documento!='2') {
                if ($row->Nu_Tipo_Impuesto == 1){//IGV
                  $Ss_Impuesto = $row->Ss_Impuesto;
                  $Ss_Precio_VU = round($row->Ss_Precio / $row->Ss_Impuesto, 6);
                  $Ss_IGV += $row->Ss_Impuesto_Producto;
                  $Ss_Gravada += $row->Ss_SubTotal_Producto;
                } else if ($row->Nu_Tipo_Impuesto == 2){//Inafecto - Operación Onerosa
                  $Ss_Inafecto += $row->Ss_SubTotal_Producto;
                } else if ($row->Nu_Tipo_Impuesto == 3){//Exonerado - Operación Onerosa
                  $Ss_Exonerada += $row->Ss_SubTotal_Producto;
                } else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
                  $Ss_Gratuita = $row->Ss_SubTotal_Producto;
                }
              }
              
              $fDescuentoTotalItem += ($row->Ss_Descuento_Producto + $row->Ss_Descuento_Impuesto_Producto);
            ?>
              <tr class="<?php echo $sNameClassTrDetalle; ?>">
                <td class="text-center" style="width: 10%"><?php echo number_format($row->Qt_Producto, 3, '.', ','); ?></td>
                <td class="text-left" style="width: 45%"><?php echo nl2br($row->No_Producto . (!empty($row->Txt_Nota_Item) ? ' ' . $row->Txt_Nota_Item : '')); ?></td>
                <td class="text-right" style="width: 10%"><?php echo numberFormat($Ss_Precio_VU, 2, '.', ','); ?></td>
                <td class="text-right" style="width: 10%"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                <td class="text-right" style="width: 10%"><?php echo numberFormat($row->Ss_Descuento_Producto + $row->Ss_Descuento_Impuesto_Producto, 2, '.', ','); ?></td>
                <td class="text-right" style="width: 15%"><?php echo numberFormat($row->Ss_Total_Producto, 2, '.', ','); ?></td>
              </tr>
              <?php
              $iCounter++;
            }

            if($arrData[0]->Ss_Descuento > 0.00 && $Ss_Impuesto > 0){
              $Ss_Gravada = $arrData[0]->Ss_Total / $Ss_Impuesto;
              $Ss_IGV = $arrData[0]->Ss_Total - $Ss_Gravada;
            }
            ?>
        </tbody>
      </table>
      <br/>
      <br/>
	  	<table class="table_pdf" cellpadding="2">
        <thead>
          <?php if($fDescuentoTotalItem>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">DSCTO. TOTAL</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($fDescuentoTotalItem, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($arrData[0]->Ss_Descuento>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">DSCTO. TOTAL (-)</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($arrData[0]->Ss_Descuento + ($arrData[0]->ID_Tipo_Documento==2 ? $arrData[0]->Ss_Descuento_Impuesto : 0), 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($arrData[0]->ID_Tipo_Documento!='2' && $Ss_Gratuita > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%;"></th>
            <th class="text-right" style="width: 20%;">GRATUITA</th>
            <th class="text-right" style="width: 15%;"><?php echo numberFormat($Ss_Gratuita, 2, '.', ','); ?></th>
          </tr>
          <?php }
          if ($arrData[0]->ID_Tipo_Documento!='2' && $Ss_Gravada > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%;"></th>
            <th class="text-right" style="width: 20%;">GRAVADA</th>
            <th class="text-right" style="width: 15%;"><?php echo numberFormat($Ss_Gravada, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($arrData[0]->ID_Tipo_Documento!='2' && $Ss_Inafecto > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">INAFECTO</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_Inafecto, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($arrData[0]->ID_Tipo_Documento!='2' && $Ss_Exonerada > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">EXONERADA</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_Exonerada, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($arrData[0]->ID_Tipo_Documento!='2' && $Ss_IGV > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">IGV</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_IGV, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($arrData[0]->ID_Tipo_Documento!='2' && $fTotalIcbper > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%;"></th>
            <th class="text-right" style="width: 20%;">ICBPER</th>
            <th class="text-right" style="width: 15%;"><?php echo numberFormat($fTotalIcbper, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%;"></th>
            <th class="text-right" style="width: 20%;">TOTAL</th>
            <th class="text-right" style="width: 15%;"><?php echo numberFormat($arrData[0]->Ss_Total - $Ss_Gratuita, 2, '.', ','); ?></th>
          </tr>
          <?php if ($arrData[0]->ID_Tipo_Documento!='2' && $arrData[0]->Ss_Retencion > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%;"></th>
            <th class="text-right" style="width: 20%;">RETENCIÓN 3.0%</th>
            <th class="text-right" style="width: 15%;"><?php echo numberFormat($arrData[0]->Ss_Retencion, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($arrData[0]->ID_Tipo_Documento!='2' && $arrData[0]->Ss_Detraccion > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%;"></th>
            <th class="text-right" style="width: 20%;">DETRACCIÓN <?php echo $arrData[0]->Po_Detraccion; ?>%</th>
            <th class="text-right" style="width: 15%;"><?php echo numberFormat($arrData[0]->Ss_Detraccion, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3">&nbsp;</th>
          </tr>
          <tr class="tr-importe_letras">
            <th style="border-color: #7d7d7d; border-bottom-color:#7d7d7d; border-right-color:#7d7d7d; border-left-color:#7d7d7d;" class="text-center" colspan="3"><b>IMPORTE EN LETRAS:</b> <?php echo strtoupper($totalEnLetras); ?></th>
          </tr>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3">&nbsp;</th>
          </tr>
          <?php
          if (!empty($arrData[0]->Txt_Glosa)) {
          ?>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3">&nbsp;</th>
          </tr>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3"><b>OBSERVACIONES:</b> <?php echo nl2br($arrData[0]->Txt_Glosa); ?></th>
          </tr>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3">&nbsp;</th>
          </tr>
          <?php
          }
          
            if ($arrData[0]->No_Codigo_Medio_Pago_Sunat_PLE != '0') { ?>
              <tr class="tr-head">
                <td class="text-left standar" colspan="2" style="width: 100%"><b>FORMA DE PAGO:</b> <?php echo $sConcatenarMultiplesMedioPago; ?></td>
              </tr>
            <?php } else { ?>
              <tr class="tr-head">
                <td class="text-left standar" colspan="2" style="width: 100%"><b>FORMA DE PAGO:</b> <?php echo $sConcatenarMultiplesMedioPago . '<br><b>CUOTA 1 </b>' . $arrData[0]->Fe_Vencimiento . ' ' . $arrData[0]->No_Signo . ' ' . numberFormat($arrData[0]->Ss_Total_Saldo, 2, '.', ','); ?></td>
              </tr>
            <?php
            }
          
          if ($Ss_Gratuita > 0.00){ ?>
            <tr class="tr-head">
              <td class="text-left standar" colspan="2" style="width: 100%"><br><br><b>TRANSFERENCIA GRATUITA:</b> DE UN BIEN O SERVICIO PRESTADO GRATUITAMENTE</td>
            </tr>
          <?php
          } 
          if (!empty($arrData[0]->No_Orden_Compra_FE)) {
          ?>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3"><b>ORDEN DE COMPRA/SERVICIO:</b> <?php echo $arrData[0]->No_Orden_Compra_FE; ?></th>
          </tr>
          <?php
          }?>

          <?php
          if (!empty($arrData[0]->ID_Tipo_Documento_Modificar) && !empty($arrData[0]->ID_Serie_Documento_Modificar) && !empty($arrData[0]->ID_Numero_Documento_Modificar)) {
          ?>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3">&nbsp;</th>
          </tr>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3"><b>MOTIVO DE EMISIÓN:</b> <?php echo $arrData[0]->No_Descripcion_Motivo_Referencia; ?></th>
          </tr>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3"><b>DOCUMENTO RELACIONADO:</b> <?php echo $arrData[0]->No_Tipo_Documento_Modificar . '-' . $arrData[0]->ID_Serie_Documento_Modificar . '-' . $arrData[0]->ID_Numero_Documento_Modificar; ?></th>
          </tr>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3">&nbsp;</th>
          </tr>
          <?php
          }?>
          <?php
    			$cadena_de_texto = $arrData[0]->Txt_Garantia;
    			$cadena_buscada = '-';
    			$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
    			if ( strlen($arrData[0]->Txt_Garantia) > 5 && $posicion_coincidencia !== false) { ?>
        	  <?php $arrCadena = explode(',',$arrData[0]->Txt_Garantia);
      		  foreach ($arrCadena as $row) {
              $arrSerieNumero = explode('-', $row);
              if ( isset($arrSerieNumero[1]) ) { ?>
                <tr class="tr-otros_campos_footer">
                  <th class="text-left" colspan="3"><b>GUÍA DE REMISIÓN REMITENTE: </b><?php echo $row; ?></th>
                </tr>
                <?php
              }
      			} ?>
          <?php
          } else {
            $arrParamsGuia = array('ID_Documento_Cabecera' => $arrData[0]->ID_Documento_Cabecera);
            $arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParamsGuia);
            $span_enlace_guias = '';
            if ($arrResponseDocument['sStatus'] == 'success') {
              foreach ($arrResponseDocument['arrData'] as $rowEnlace)
                $span_enlace_guias .= '<span class="label label-dark">' . $rowEnlace->_ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . "</span><br>"; ?>
              <tr class="tr-otros_campos_footer">
                <th class="text-left" colspan="3"><b>GUÍA DE REMISIÓN REMITENTE: </b><?php echo $span_enlace_guias; ?></th>
              </tr>
            <?php 
            }
          }// if - else guias

          if ($arrData[0]->Nu_Detraccion == 1) { ?>
            <?php if (!empty($arrDataEmpresa[0]->Txt_Cuenta_Banco_Detraccion)) { ?>
              <tr class="tr-sub_thead">
                <th class="text-left" colspan="3">&nbsp;</th>
              </tr>
              <tr class="tr-otros_campos_footer">
                <th class="text-left" colspan="3"><b>Operación sujeta al Sistema de Pago de Obligaciones Tributarias:</b></th>
              </tr>
              <tr class="tr-otros_campos_footer">
                <th class="text-left content" colspan="3"><?php echo $arrDataEmpresa[0]->Txt_Cuenta_Banco_Detraccion; ?></th>
              </tr>
            <?php } else { ?>
              <tr class="tr-sub_thead">
                <th class="text-left" colspan="3">&nbsp;</th>
              </tr>
              <tr class="tr-otros_campos_footer">
                <th class="text-left" colspan="3"><b>Operación sujeta al Sistema de Pago de Obligaciones Tributarias: BANCO DE LA NACIÓN</b></th>
              </tr>
            <?php
            }
          }
          
          if ($arrData[0]->Nu_Retencion == 1) {
          ?>
          <tr class="tr-otros_campos_footer">
            <th class="text-left" colspan="3"><b>OPERACIÓN SUJETA A RETENCIÓN DEL I.G.V. TASA 3.0%</b></th>
          </tr>
          <?php } ?>

          <?php
          if (!empty($arrData[0]->No_Placa_FE)) {
          ?>
          <tr class="tr-otros_campos_footer">
            <th class="text-center" colspan="3"><b>PLACA VEHICULO:</b> <?php echo $arrData[0]->No_Placa_FE; ?></th>
          </tr>
          <?php
          }?>
        </thead>
      </table>
      <br/>
      <br/>
      <?php if (!empty($arrDataEmpresa[0]->Txt_Cuentas_Bancarias) || !empty($arrDataEmpresa[0]->Txt_Nota)) { ?>
	  	<table class="table_pdf" cellpadding="4">
        <thead>
          <?php if (!empty($arrDataEmpresa[0]->Txt_Cuentas_Bancarias)) { ?>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left">Cuentas Bancarias</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrDataEmpresa[0]->Txt_Cuentas_Bancarias; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"></th>
          </tr>
          <?php } ?>
      </table>
      <br/>
      <br/>
      <?php } ?>
	  	<table class="table_pdf" cellpadding="4">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-center content"><b>Copia para control administrativo</b></th>
          </tr>
          <tr class="tr-sub_thead">
            <?php if($arrDataEmpresa[0]->Nu_Tipo_Proveedor_FE == 2){?>
              <th class="text-center content">Consulte la representación impresa <b><a style="text-decoration: none; color: #201F1F;" target="_blank" title="Representación impresa" rel="Representación impresa" href="https://laesystems.com">laesystems.com</a></b></th>
            <?php } ?>
            <?php if($arrDataEmpresa[0]->Nu_Tipo_Proveedor_FE == 1){?>
              <th class="text-center content">Consulte la representación impresa <b><a style="text-decoration: none; color: #201F1F;" target="_blank" title="Representación impresa" rel="Representación impresa" href="https://laesystems.pse.pe/<?php echo $arrDataEmpresa[0]->Nu_Documento_Identidad; ?>">https://laesystems.pse.pe/<?php echo $arrDataEmpresa[0]->Nu_Documento_Identidad; ?></a></b></th>
            <?php } ?>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-center content">Autorizado mediante R.I. NRO. 034-005-0005315</th>
          </tr>
        </thead>
      </table>
    </body>
</html>