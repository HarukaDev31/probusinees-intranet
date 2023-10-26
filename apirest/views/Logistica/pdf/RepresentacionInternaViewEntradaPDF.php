<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
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
        
        .text-left{text-align: left;}
        .text-center{text-align: center;}
        .text-right{text-align: right;}
      </style>
    </head>
    <body>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left title" style="width: 100%;"><b>DESTINATARIO</b></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve ?>:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo $arrData[0]->Nu_Documento_Identidad; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>DENOMINACIÓN:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo $arrData[0]->No_Entidad; ?></th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left title" style="width: 100%;"><b>DATOS DEL TRASLADO</b></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>FECHA EMISIÓN:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo ToDateBD($arrData[0]->Fe_Emision); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>FECHA INICIO DE TRASLADO:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo (!empty($arrData[0]->Fe_Traslado) ? ToDateBD($arrData[0]->Fe_Traslado) : '-'); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>MOTIVO DE TRASLADO:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo strtoupper($arrData[0]->No_Motivo_Traslado_Sunat); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>MODALIDAD DE TRANSPORTE:</b></th>
            <th class="text-left content" style="width: 67%;">TRANSPORTE <?php echo ($arrData[0]->No_Tipo_Transporte == '01' ? 'PÚBLICO' : 'PRIVADO'); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>PESO BRUTO TOTAL (KGM):</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo $arrData[0]->Ss_Peso_Bruto; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>NÚMERO DE BULTOS:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo $arrData[0]->Nu_Bulto; ?></th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left title" style="width: 100%;"><b>DATOS DEL TRASLADO</b></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>PUNTO DE PARTIDA:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo ($arrData[0]->Txt_Direccion_Empresa != $arrData[0]->Txt_Direccion_Origen ? $arrData[0]->Txt_Direccion_Origen : $arrData[0]->Txt_Direccion_Empresa); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>PUNTO DE LLEGADA:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo $arrData[0]->Txt_Direccion_Destino; ?></th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left title" style="width: 100%;"><b>DATOS DEL TRANSPORTE</b></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>TRANSPORTISTA:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve_Transporte . ': ' . $arrData[0]->Nu_Documento_Identidad_Transportista . ' ' . $arrData[0]->No_Entidad_Transportista; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 33%;"><b>VEHÍCULO:</b></th>
            <th class="text-left content" style="width: 67%;"><?php echo $arrData[0]->No_Placa; ?></th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
	  	<table class="table_pdf" cellpadding="5">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" style="width: 10%">NRO.</th>
            <th class="text-left" style="width: 15%">COD.</th>
            <th class="text-left" style="width: 55%">DESCRIPCIÓN</th>
            <th class="text-center" style="width: 10%">UM</th>
            <th class="text-right" style="width: 10%">CANT.</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $iCounter = 0;
          $sNameClassTrDetalle = '';
          $iNroItem = 1;
          $fTotalCantidad=0;
          foreach($arrData as $row) {
            $sNameClassTrDetalle = ($iCounter%2) == 0 ? 'tr-fila_par' : 'tr-fila_impar';
            $sVarianteMultiple = '';
            if($arrData[0]->Nu_Tipo_Rubro_Empresa==6) {
              $sVarianteMultiple .= (!empty($row->No_Variante_1) ? ' ' . $row->No_Variante_1 . ':' . $row->No_Valor_Variante_1 : '');
              $sVarianteMultiple .= (!empty($row->No_Variante_2) ? '<br>' . $row->No_Variante_2 . ':' . $row->No_Valor_Variante_2 : '');
              $sVarianteMultiple .= (!empty($row->No_Variante_3) ? '<br>' . $row->No_Variante_3 . ':' . $row->No_Valor_Variante_3 : '');              
            }
            $fTotalCantidad+=$row->Qt_Producto;
          ?>
            <tr class="<?php echo $sNameClassTrDetalle; ?>">
              <td class="text-center" style="width: 10%">&nbsp;<?php echo $iNroItem; ?></td>
              <td class="text-left" style="width: 15%">&nbsp;<?php echo $row->Nu_Codigo_Barra; ?></td>
              <td class="text-left" style="width: 55%">&nbsp;<?php echo nl2br($row->No_Producto . $row->Txt_Nota_Item . $sVarianteMultiple); ?></td>
              <td class="text-center" style="width: 10%">&nbsp;<?php echo $row->Nu_Sunat_Codigo_UM; ?></td>
              <td class="text-right" style="width: 10%">&nbsp;<?php echo number_format($row->Qt_Producto, 3, '.', ','); ?></td>
            </tr>
            <?php
            $iCounter++;
					  ++$iNroItem;
          } ?>
        </tbody>
      </table>
	  	<table class="table_pdf" cellpadding="5">
        <thead>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right standar title_bold" style="width: 90%">TOTAL</th>
            <th class="text-right standar title_bold" style="width: 10%"><?php echo numberFormat($fTotalCantidad, 3, '.', ','); ?></th>
          </tr>
        </thead>
      </table>
      <br/><br/>
	  	<table class="table_pdf" cellpadding="1">
        <thead>
          <?php if (!empty($arrData[0]->Txt_Glosa)) { ?>
          <tr class="tr-importe_letras">
            <th class="text-left" colspan="3"><b>OBSERVACIONES:</b> <?php echo nl2br($arrData[0]->Txt_Glosa); ?></th>
          </tr>
          <?php } ?>
        </thead>
      </table>
      <?php if(substr($arrData[0]->ID_Serie_Documento, 0, 1) == 'T') { ?>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-sub_thead"><br>
            <th class="text-left content" style="width: 80%;">Representación impresa de la <?php echo strtoupper($arrData[0]->No_Tipo_Documento); ?>, <b>visita https://laesystems.com</b></th>
          </tr>
        </thead>
      </table>
      <?php } ?>
      <?php if ( $this->empresa->Nu_Estado_Sistema == 0 ) { ?>
        <table class="table_pdf" cellpadding="4">
          <tr class="tr-sub_thead">
            <th class="text-center content">Representación Impresa de Documento Electrónico Generado En Una Versión de Pruebas. No tiene Validez!</th>
          </tr>
        </table>
      <?php } ?>
      <br/>
      <br/>
    </body>
</html>