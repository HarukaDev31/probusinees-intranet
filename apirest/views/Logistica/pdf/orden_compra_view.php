<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>laesystems</title>
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
          font-size: 9px;
          font-family: Arial;
          background-color: #F2F5F5;
        }
        
        .tr-fila_par td{
          font-size: 9px;
          font-family: Arial;
          background-color: #FFFFFF;
        }
        
        .tr-importe_letras{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .tr-sub_thead th.contacto_nombres {
          color: #000000;
          font-size: 11px;
          font-family: Arial;
        }
        
        .tr-sub_thead th.contacto_cargo {
          color: #838585;
          font-size: 11px;
          font-family: Arial;
        }

        .tr-sub_thead th.contacto_empresa_celular {
          color: #000000;
          font-size: 11px;
          font-family: Arial;
        }

        .tr-sub_thead th.contacto_empresa_correo {
          color: #34bdad;
          font-size: 11px;
          font-family: Arial;
        }

        .text-left{text-align: left;}
        .text-center{text-align: center;}
        .text-right{text-align: right;}
      </style>
    </head>
    <body>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-thead">
            <th class="text-left title"><br/><br/><br/>Orden de Compra</th>
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
                  Nro. <?php echo $arrData[0]->ID_Documento_Cabecera; ?>
                </div>
              </div>
            </th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
      <br/>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 40%">PROVEEDOR</th>
            <th class="text-left title" style="width: 30%">CONTACTO</th>
            <th class="text-left content" style="width: 30%"><b>FECHA ENTREGA:</b> <?php echo ToDateBD($arrData[0]->Fe_Periodo); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrData[0]->No_Entidad; ?></th>
            <th class="text-left content"><?php echo $arrData[0]->No_Contacto; ?></th>
            <th class="text-left content"><b>FECHA DE VENC:</b> <?php echo ToDateBD($arrData[0]->Fe_Vencimiento); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve . ': ' . $arrData[0]->Nu_Documento_Identidad; ?></th>
            <th class="text-left content"><?php echo $arrData[0]->Nu_Celular_Contacto; ?></th>
            <th class="text-left content"><b>FORMA DE PAGO:</b> <?php echo $arrData[0]->No_Medio_Pago; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrData[0]->Txt_Direccion_Entidad; ?></th>
            <th class="text-left content"><?php echo $arrData[0]->Txt_Email_Contacto; ?></th>
            <th class="text-left content"><b>MONEDA:</b> <?php echo $arrData[0]->No_Moneda; ?></th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
      <br/>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-center" style="width: 15%">&nbsp;<br/>CANT.<br/></th>
            <th class="text-center" style="width: 60%">&nbsp;<br/>DESCRIPCIÓN<br/></th>
            <th class="text-right" style="width: 10%">&nbsp;<br/>PRECIO<br/></th>
            <th class="text-right" style="width: 15%">&nbsp;<br/>IMPORTE<br/></th>
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
            foreach($arrData as $row) {
              $sNameClassTrDetalle = ($iCounter%2) == 0 ? 'tr-fila_par' : 'tr-fila_impar';

              if ($row->Nu_Tipo_Impuesto == 1){//IGV
                $Ss_IGV += $row->Ss_Impuesto_Producto;              
                $Ss_Gravada += $row->Ss_SubTotal_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 2){//Inafecto - Operación Onerosa
                $Ss_Inafecto += $row->Ss_SubTotal_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 3){//Exonerado - Operación Onerosa
                $Ss_Exonerada += $row->Ss_SubTotal_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
                $Ss_Gratuita += $row->Ss_SubTotal_Producto;
              }
      
              $Ss_Total += $row->Ss_Total_Producto;
            ?>
              <tr class="<?php echo $sNameClassTrDetalle; ?>">
                <td class="text-center" style="width: 15%">&nbsp;<br/><?php echo round($row->Qt_Producto, 0); ?><br/></td>
                <td class="text-left" style="width: 60%">&nbsp;<br/><?php echo $row->No_Producto; ?><br/></td>
                <td class="text-right" style="width: 10%">&nbsp;<br/><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?><br/></td>
                <td class="text-right" style="width: 15%">&nbsp;<br/><?php echo numberFormat($row->Ss_Total_Producto, 2, '.', ','); ?><br/></td>
              </tr>
              <?php
              $iCounter++;
            } ?>
        </tbody>
      </table>
      <br/>
      <br/>
	  	<table class="table_pdf" cellpadding="2">
        <thead>
          <?php if ($Ss_Gravada > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">GRAVADA</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_Gravada, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($Ss_Inafecto>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">INAFECTO</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_Inafecto, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($Ss_Exonerada>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">EXONERADA</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_Exonerada, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($arrData[0]->Ss_Descuento>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">DSCTO. TOTAL (-)</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($arrData[0]->Ss_Descuento, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($Ss_IGV>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">IGV</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_IGV, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($Ss_Total>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">TOTAL</th>
            <th class="text-right" style="width: 15%"><?php echo numberFormat($Ss_Total, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
        </thead>
      </table>
      <br/>
      <br/>
      <?php if ( !empty($arrData[0]->Txt_Glosa) || !empty($arrData[0]->Txt_Garantia)) { ?>
	  	<table class="table_pdf" cellpadding="4">
        <thead>
          <?php if (!empty($arrData[0]->Txt_Garantia)) { ?>
          <tr class="tr-theadFormat tr-header">
            <th class="text-left">Garantía</th>
          </tr>
          <tr class="tr-sub_thead tr-header">
            <th class="text-left content"><?php echo $arrData[0]->Txt_Garantia; ?></th>
          </tr>
          <tr class="tr-sub_thead tr-header">
            <th class="text-left content"><br/></th>
          </tr>
          <?php } ?>
          <?php if (!empty($arrData[0]->Txt_Glosa)) { ?>
          <tr class="tr-theadFormat tr-header">
            <th class="text-left content">Glosa</th>
          </tr>
          <tr class="tr-sub_thead tr-header">
            <th class="text-left content"><?php echo nl2br($arrData[0]->Txt_Glosa); ?></th>
          </tr>
          <tr class="tr-sub_thead tr-header">
            <th class="text-left content"><br/></th>
          </tr>
          <?php } ?>
        </thead>
      </table>
      <?php } ?>
      <?php if ( !empty($this->empresa->Txt_Nota) ) { ?>
	  	<table class="table_pdf" cellpadding="2">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left content"></th>
          </tr>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left">Nota</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $this->empresa->Txt_Nota; ?></th>
          </tr>
      </table>
      <?php } ?>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-sub_thead">
            <br/>
            <th class="text-right contacto_nombres"><?php echo $this->user->No_Nombres_Apellidos; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-right contacto_cargo"><?php echo $this->user->No_Grupo; ?></th>
          </tr>
          <?php if (!empty($this->user->Nu_Celular)) { ?>
          <tr class="tr-sub_thead">
            <th class="text-right contacto_empresa_celular">Cel. <?php echo $this->user->Nu_Celular; ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-sub_thead">
            <th class="text-right contacto_empresa_correo"><?php echo $this->user->Txt_Email; ?></th>
          </tr>
        </thead>
      </table>
    </body>
</html>