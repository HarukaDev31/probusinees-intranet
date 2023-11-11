<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>probusiness</title>
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
          font-size: 10px;
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
        }
        
        .tr-otros_campos_footer{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
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

        .text-left{text-align: left;}
        .text-center{text-align: center;}
        .text-right{text-align: right;}
      </style>
    </head>
    <body>
	  	<table class="table_pdf" style="padding-top: -40px;">
        <thead>
          <tr class="tr-thead">
            <th class="text-left title"><br/><br/><br/><?php echo 'Cotización'; ?></th>
            <th class="text-center sub_title_fecha">
              <div>
                &nbsp;<br>
                <div style="border-top: 2.7px solid #34bdad">
                  &nbsp;<br>
                  Día <?php echo dateNow('dia'); ?> de <?php echo getNameMonth(dateNow('mes')); ?> del <?php echo dateNow('año'); ?>
                </div>
              </div>
            </th>
            <th class="text-right sub_title_nro">
              <div>
                &nbsp;<br>
                <div style="border-top: 2.7px solid #34bdad">
                  &nbsp;<br>
                  Nro. <?php echo $arrData[0]->ID_Pedido_Cabecera; ?>
                </div>
              </div>
            </th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
      <br/>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 50%">CLIENTE</th>
            <th class="text-left title" style="width: 25%"><?php echo (!empty($arrData[0]->No_Contacto) ? 'CONTACTO' : ''); ?></th>
            <th class="text-left content" style="width: 25%"><b>FECHA ENTREGA:</b> <?php echo ToDateBD($arrData[0]->Fe_Periodo); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 15%;"><b><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve; ?></b></th>
            <th class="text-left content" style="width: 35%;"><?php echo ': ' . $arrData[0]->Nu_Documento_Identidad; ?></th>
            <th class="text-left content" style="width: 25%;"><?php echo $arrData[0]->No_Contacto; ?></th>
            <th class="text-left content" style="width: 25%;"><b>FECHA DE VENC:</b> <?php echo ToDateBD($arrData[0]->Fe_Vencimiento); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 15%;"><b>DENOMINACIÓN</b></th>
            <th class="text-left content" style="width: 35%;"><?php echo ': ' . $arrData[0]->No_Entidad; ?></th>
            <th class="text-left content" style="width: 25%;"><?php echo $arrData[0]->Nu_Celular_Contacto; ?></th>
            <th class="text-left content" style="width: 25%"><b>FORMA DE PAGO:</b> <?php echo $arrData[0]->No_Medio_Pago; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 15%;"><b>DIRECCIÓN</b></th>
            <th class="text-left content" style="width: 35%;"><?php echo ': ' . $arrData[0]->Txt_Direccion_Entidad; ?></th>
            <th class="text-left content" style="width: 25%;"><?php echo $arrData[0]->Txt_Email_Contacto; ?></th>
            <th class="text-left content" style="width: 25%"><b>MONEDA:</b> <?php echo $arrData[0]->No_Moneda; ?>  <?php echo (!empty($arrData[0]->Ss_Tipo_Cambio) ? ' &nbsp;&nbsp;<b>T.C.</b> '. $arrData[0]->Ss_Tipo_Cambio : ''); ?></th>
          </tr>
        </thead>
      </table>
      <br/><br/>
	  	<table class="table_pdf" cellpadding="5">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center border-left" style="width: 10%">CANT.</th>
            <th class="text-center" style="width: 10%">UM</th>
            <th class="text-left" style="width: 55%">DESCRIPCIÓN</th>
            <th class="text-right" style="width: 10%">PRECIO</th>
            <th class="text-right border-right" style="width: 15%;">IMPORTE</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $iCounter = 0; 
            $sNameClassTrDetalle = '';
            $fOperacionesGravadas = 0.00;
            $fOperacionesExoneradas = 0.00;
            $fOperacionesInafectas = 0.00;
            $fGratuita = 0.00;
            $fIGV = 0.00;
            $fDescuentoTotalItem = 0;
            $fTotalIcbper = 0;
            $sPorecentajeDescuento='';
            foreach($arrData as $row) {
              $sNameClassTrDetalle = ($iCounter%2) == 0 ? 'tr-fila_par' : 'tr-fila_impar';

              $Ss_SubTotal_Producto = $row->Ss_SubTotal_Producto;
              if ($row->Nu_Tipo_Impuesto == 1) {//IGV
                $fOperacionesGravadas += $Ss_SubTotal_Producto;
                $fIGV += $row->Ss_Impuesto_Producto;
                $sPorecentajeDescuento = $row->Po_Impuesto . ' %';
              } else if ($row->Nu_Tipo_Impuesto == 2)//INA
                $fOperacionesInafectas += $Ss_SubTotal_Producto;
              else if ($row->Nu_Tipo_Impuesto == 3) {//EXO
                $fOperacionesExoneradas += $Ss_SubTotal_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
                $fGratuita += $row->Ss_Total_Producto;
              }

              if ( $row->ID_Impuesto_Icbper == 1 )
                $fTotalIcbper += $row->Ss_Icbper;

              $fDescuentoTotalItem += ($row->Ss_Descuento_Producto + $row->Ss_Descuento_Impuesto_Producto);
            ?>
              <tr class="<?php echo $sNameClassTrDetalle; ?>">
                <td class="text-center border-left" style="width: 10%"><?php echo number_format($row->Qt_Producto, 3, '.', ','); ?></td>
                <td class="text-center" style="width: 10%"><?php echo $row->Nu_Sunat_Codigo_UM; ?></td>
                <td class="text-left" style="width: 55%"><?php echo nl2br($row->No_Producto . (!empty($row->Txt_Nota_Item) ? ' ' . $row->Txt_Nota_Item : '')) . ($row->Nu_Tipo_Impuesto == 4 ? '<strong> [Gratuita]</strong>' : ''); ?></td>
                <td class="text-right" style="width: 10%"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                <!--<td class="text-right" style="width: 10%"><?php echo numberFormat($row->Ss_Descuento_Producto + $row->Ss_Descuento_Impuesto_Producto, 2, '.', ','); ?></td>-->
                <td class="text-right border-right" style="width: 15%"><?php echo numberFormat($row->Ss_Total_Producto, 2, '.', ','); ?></td>
              </tr>
              <?php
              $iCounter++;
            }
            ?>
        </tbody>
      </table>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-head">
            <th class="text-left standar" colspan="3">&nbsp;</th>
          </tr>
          <?php if($arrData[0]->Ss_Descuento>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">DSCTO. TOTAL (-)</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($arrData[0]->Ss_Descuento + $arrData[0]->Ss_Descuento_Impuesto, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($fDescuentoTotalItem>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">DSCTO. TOTAL</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($fDescuentoTotalItem, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($fGratuita > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%;">GRATUITA</th>
            <th class="text-right" style="width: 15%;"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($fGratuita, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <!--
          <?php if ($fOperacionesGravadas > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%;">GRAVADA</th>
            <th class="text-right" style="width: 15%;"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($fOperacionesGravadas, 2, '.', ','); ?></th>
          </tr>
          <?php }
          if ($fOperacionesInafectas > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">INAFECTO</th>
            <th class="text-right" style="width: 15%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($fOperacionesInafectas, 2, '.', ','); ?></th>
          </tr>
          <?php }
          if ($fOperacionesExoneradas > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">EXONERADA</th>
            <th class="text-right" style="width: 15%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($fOperacionesExoneradas, 2, '.', ','); ?></th>
          </tr>
          <?php }
          if ($fIGV > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">IGV <?php echo $sPorecentajeDescuento; ?></th>
            <th class="text-right" style="width: 15%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($fIGV, 2, '.', ','); ?></th>
          </tr>
          <?php }
          if ($fTotalIcbper > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%;">ICBPER</th>
            <th class="text-right" style="width: 15%;"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($fTotalIcbper, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          -->
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">TOTAL</th>
            <th class="text-right" style="width: 15%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($arrData[0]->Ss_Total - $fGratuita, 2, '.', ','); ?></th>
          </tr>
        </thead>
      </table>
	  	<table class="table_pdf" cellpadding="2">
        <tr class="tr-otros_campos_footer">
          <th class="text-center" colspan="3">&nbsp;</th>
        </tr>
        <tr class="tr-importe_letras">
          <th style="border-color: #7d7d7d; border-bottom-color:#7d7d7d; border-right-color:#7d7d7d; border-left-color:#7d7d7d;" class="text-center" colspan="3"><b>IMPORTE EN LETRAS:</b> <?php echo strtoupper($totalEnLetras); ?></th>
        </tr>
        <tr class="tr-otros_campos_footer">
          <th class="text-center" colspan="3">&nbsp;</th>
        </tr>
      </table>
      <?php if ( !empty($arrData[0]->Txt_Garantia)) { ?>
	  	<table class="table_pdf" cellpadding="4">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left border-left border-right">Garantía</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrData[0]->Txt_Garantia; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"></th>
          </tr>
        </thead>
      </table>
      <?php } ?>
      <?php if (!empty($arrData[0]->Txt_Glosa)) { ?>
        <table class="table_pdf" cellpadding="4">
          <thead>
            <tr class="tr-theadFormat tr-header tr-header-detalle">
              <th class="text-left content border-left border-right">Glosa</th>
            </tr>
          </thead>
        </table>
        <?php
        //echo base64_decode($arrData[0]->Txt_Glosa);
        if ( base64_encode(base64_decode($arrData[0]->Txt_Glosa, true)) === $arrData[0]->Txt_Glosa){
          echo base64_decode($arrData[0]->Txt_Glosa);
        } else { ?>
          <table class="table_pdf" cellpadding="4">
            <thead>
              <tr class="tr-sub_thead">
                <th class="text-left content"><?php echo $arrData[0]->Txt_Glosa; ?></th>
              </tr>
              <tr class="tr-sub_thead">
                <th class="text-left content"></th>
              </tr>
            </thead>
          </table>
        <?php
        }
        ?>
      <?php } ?>
      <?php if (!empty($arrDataEmpresa[0]->Txt_Cuentas_Bancarias) || !empty($arrDataEmpresa[0]->Txt_Nota)) { ?>
	  	<table class="table_pdf" cellpadding="2">
        <thead>
          <?php if (!empty($arrDataEmpresa[0]->Txt_Cuentas_Bancarias)) { ?>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left border-left border-right">Cuentas Bancarias</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrDataEmpresa[0]->Txt_Cuentas_Bancarias; ?></th>
          </tr>
          <?php } ?>
          <?php if (!empty($arrDataEmpresa[0]->Txt_Nota)) { ?>
          <tr class="tr-sub_thead">
            <th class="text-left content"></th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left border-left border-right">Nota</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrDataEmpresa[0]->Txt_Nota; ?></th>
          </tr>
          <?php } ?>
      </table>
      <?php } ?>
      <?php if (!empty($arrDataEmpresa[0]->Txt_Terminos_Condiciones)) { ?>
	  	<table class="table_pdf" cellpadding="2">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left content"></th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left border-left border-right">Términos y Condiciones</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrDataEmpresa[0]->Txt_Terminos_Condiciones; ?></th>
          </tr>
      </table>
      <?php } ?>
      <br><br>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left content">&nbsp;</th>
            <th class="text-right content"><b><?php echo (!empty($this->user->No_Grupo_Descripcion) ? $this->user->No_Grupo_Descripcion : ''); ?></b></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="font-size: 10.5px"><?php echo (!empty($arrData[0]->No_Vendedor) ? $arrData[0]->No_Vendedor : ''); ?></th>
            <th class="text-right content" style="font-size: 10.5px"><?php echo (!empty($this->user->No_Nombres_Apellidos) ? $this->user->No_Nombres_Apellidos : ''); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo (!empty($arrData[0]->Nu_Celular_Vendedor) ? '<b>Celular:</b> ' . $arrData[0]->Nu_Celular_Vendedor : ''); ?></th>
            <th class="text-right content"><?php echo (!empty($this->user->Nu_Celular) ? '<b>Celular:</b> ' . $this->user->Nu_Celular : ''); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo (!empty($arrData[0]->Txt_Email_Vendedor) ? '<b>Email:</b> ' . $arrData[0]->Txt_Email_Vendedor : ''); ?></th>
            <th class="text-right content"><?php echo (!empty($this->user->No_Usuario) ? '<b>Email:</b> ' . $this->user->No_Usuario : ''); ?></th>
          </tr>
        </thead>
      </table>
    </body>
</html>