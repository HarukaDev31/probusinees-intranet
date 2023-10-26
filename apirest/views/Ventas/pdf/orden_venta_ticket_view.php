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
          font-size: 12px;
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
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          font-weight: bold;
        }

        .tr-sub_thead th.content {
          font-size: 7px;
          font-family: "Arial", Helvetica, sans-serif;
        }
        
        .tr-theadFormat th{
          font-weight: bold;
          font-family: Arial;
        }
        
        .tr-header-detalle th{
          font-size: 7.5px; 
          font-family: Arial;
          font-weight: bold;
          background-color: #e4e4e4;
          border-color: #7d7d7d;
          border-width: 1px;
          border-bottom-color:#7d7d7d;
        }

        .tr-header th{
          font-size: 7.5px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #F2F5F5;
        }
        
        .tr-footer th{
          font-size: 9px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .tr-totales th{
          font-size: 8px;
          font-family: "Arial", Helvetica, sans-serif;
          background-color: #FFFFFF;
        }
        
        .tr-fila_impar td{
          font-size: 7px;
          font-family: Arial;
          background-color: #F2F5F5;
          border-width: 1px;
          border-bottom-color:#7d7d7d;
        }
        
        .tr-fila_par td{
          font-size: 7px;
          font-family: Arial;
          background-color: #FFFFFF;
          border-width: 1px;
          border-bottom-color:#7d7d7d;
        }
        
        .tr-importe_letras th{
          font-size: 7x;
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

        .tr-head th.title_bold, .tr-head td.title_bold {
          font-weight: bold;
        }
      </style>
    </head>
    <body>
	  	<table class="table_pdf" border="0" style="padding-top: 0px;">
        <thead>
          <?php if ($arrData[0]->Nu_Logo_Empresa_Ticket==1 && !empty($sUrlImagen)) { ?>
            <tr class="tr-head">
              <th class="text-center standar"><img style="height: <?php echo $arrDataEmpresa[0]->Nu_Height_Logo_Ticket; ?> px; width: <?php echo $arrDataEmpresa[0]->Nu_Width_Logo_Ticket; ?>px;" src="<?php echo $sUrlImagen; ?>"></th>
            </tr>
          <?php
          } if (!empty($arrData[0]->No_Empresa_Comercial)) { ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold"><?php echo $arrData[0]->No_Empresa_Comercial; ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold" style="font-size: 6.5px;"><?php echo $arrData[0]->No_Empresa; ?></th>
          </tr>
          <tr class="tr-head">
            <th class="text-center standar title_bold" style="font-size: 6.5px;"><?php echo 'RUC: ' . $arrData[0]->Nu_Documento_Identidad_Empresa;?></th>
          </tr>
          <tr class="tr-head">
            <td class="text-center standar" style="font-size: 6.5px;"><?php echo $arrData[0]->Txt_Direccion_Empresa . ' - ' . $arrData[0]->No_Departamento . ' - ' . $arrData[0]->No_Provincia . ' - ' . $arrData[0]->No_Distrito; ?></td>
          </tr>
          <?php if ($arrData[0]->Txt_Direccion_Empresa != $arrData[0]->Txt_Direccion_Almacen) { ?>
          <tr class="tr-head">
            <td class="text-center standar" style="font-size: 6.5px;"><?php echo $arrData[0]->Txt_Direccion_Almacen;?></td>
          </tr>
          <?php } ?>
          <?php if ( !empty($arrDataEmpresa[0]->No_Dominio_Empresa) || !empty($arrData[0]->Txt_Email_Empresa)) { ?>
          <tr class="tr-head">
            <td class="text-center standar"><?php echo $arrDataEmpresa[0]->No_Dominio_Empresa . ' ' . $arrData[0]->Txt_Email_Empresa; ?></td>
          </tr>
          <?php }
          if ( !empty($arrData[0]->Nu_Celular_Empresa) || !empty($arrData[0]->Nu_Telefono_Empresa)) { ?>
          <tr class="tr-head">
            <td class="text-center standar"><?php echo $arrData[0]->Nu_Celular_Empresa . ' ' . $arrData[0]->Nu_Telefono_Empresa; ?></td>
          </tr>
          <?php } ?>
          <tr class="tr-thead">
            <th class="text-center title"><?php echo $arrData[0]->No_Tipo_Documento; ?></th>
          </tr>
          <tr class="tr-thead">
            <th class="text-center content"><b>F. EMISIÓN</b> <?php echo ToDateBD($arrData[0]->Fe_Emision); ?>&nbsp;&nbsp; <b>NRO.</b> <?php echo $arrData[0]->ID_Documento_Cabecera; ?></th>
          </tr>
          <tr class="tr-head">
            <th class="text-center content">&nbsp;</th>
          </tr>
        </thead>
      </table>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left content" colspan="4">CLIENTE <?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve . ': ' . $arrData[0]->Nu_Documento_Identidad; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" colspan="4"><?php echo $arrData[0]->No_Entidad; ?></th>
          </tr>
          <?php if (!empty($arrData[0]->Txt_Direccion_Entidad)) { ?>
          <tr class="tr-sub_thead">
            <th class="text-left content" colspan="4"><?php echo $arrData[0]->Txt_Direccion_Entidad; ?></th>
          </tr>
          <?php } ?>
          <?php if (!empty($arrData[0]->Txt_Email_Contacto)) { ?>
          <tr class="tr-sub_thead">
            <th class="text-left content" colspan="4"><?php echo $arrData[0]->Txt_Email_Contacto; ?></th>
          </tr>
          <?php } ?>
          <?php if (!empty($arrData[0]->No_Contacto)) { ?>
            <tr class="tr-sub_thead">
              <th class="text-left content" style="width: 35%"><b>CONTACTO</b></th>
              <th class="text-left content" colspan="3"><?php echo $arrData[0]->No_Contacto; ?></th>
            </tr>
          <?php } ?>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 35%"><b>FECHA ENTREGA</b></th>
            <th class="text-left content" colspan="3"><?php echo ToDateBD($arrData[0]->Fe_Periodo); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 35%"><b>FECHA VCTO.</b></th>
            <th class="text-left content" colspan="3"><?php echo ToDateBD($arrData[0]->Fe_Vencimiento); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 35%"><b>FORMA PAGO</b></th>
            <th class="text-left content" colspan="3"><?php echo $arrData[0]->No_Medio_Pago; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 35%"><b>MONEDA</b></th>
            <th class="text-left content" colspan="3"><?php echo $arrData[0]->No_Moneda; ?>  <?php echo (!empty($arrData[0]->Ss_Tipo_Cambio) ? ' &nbsp;&nbsp;<b>T.C.</b> '. $arrData[0]->Ss_Tipo_Cambio : ''); ?></th>
          </tr>
        </thead>
      </table>
      <br/><br/>
	  	<table class="table_pdf" cellpadding="5">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left standar title_bold" style="width: 23%">CANT.</th>
            <th class="text-left standar title_bold" style="width: 45%">DESCRIPCION</th>
            <th class="text-right standar title_bold" style="width: 14%">P.U.</th>
            <th class="text-right standar title_bold" style="width: 18%">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $sNombreBreveLaboratorio='';
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
            $fDescuentoTotalItem = 0;
            foreach($arrData as $row) {
              $sNameClassTrDetalle = ($iCounter%2) == 0 ? 'tr-fila_par' : 'tr-fila_impar';

              $sNombreBreveLaboratorio='';
              if ($arrDataEmpresa[0]->Nu_Tipo_Rubro_Empresa == 1) {
                $sCodigoInterno = '';
                if (!empty($row->No_Laboratorio_Breve))
                  $sNombreBreveLaboratorio= ' ' . $row->No_Laboratorio_Breve;
              }

              if ( $row->ID_Impuesto_Icbper == 1 )
                $fTotalIcbper += $row->Ss_Icbper;

              if ($row->Nu_Tipo_Impuesto == 1){//IGV
                $Ss_Impuesto = $row->Ss_Impuesto;
                $Ss_IGV += $row->Ss_Impuesto_Producto;
                $Ss_Gravada += $row->Ss_SubTotal_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 2){//Inafecto - Operación Onerosa
                $Ss_Inafecto += $row->Ss_SubTotal_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 3){//Exonerado - Operación Onerosa
                $Ss_Exonerada += $row->Ss_SubTotal_Producto;
              } else if ($row->Nu_Tipo_Impuesto == 4){//Gratuita
                $Ss_Gratuita = $row->Ss_SubTotal_Producto;
              }
              $fCantidadItem = $row->Qt_Producto;

              $fDescuentoTotalItem += ($row->Ss_Descuento_Producto + $row->Ss_Descuento_Impuesto_Producto);
            ?>
              <tr class="<?php echo $sNameClassTrDetalle; ?>">
                <td class="text-left standar" style="width: 23%"><?php echo number_format($fCantidadItem, 2, '.', '') . ' ' . $row->nu_codigo_unidad_medida_sunat; ?></td>
                <td class="text-left standar" style="width: 45%"><?php echo nl2br($row->No_Producto . (!empty($row->Txt_Nota_Item) ? ' ' . $row->Txt_Nota_Item : '')) . ($row->Nu_Tipo_Impuesto == 4 ? '<strong> [Gratuita]</strong>' : ''); ?></td>
                <td class="text-right standar" style="width: 14%"><?php echo numberFormat($row->Ss_Precio, 2, '.', ''); ?></td>
                <td class="text-right standar" style="width: 18%"><?php echo numberFormat($row->Ss_Total_Producto, 2, '.', ''); ?></td>
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
            <th class="text-right" colspan="4">DSCTO. TOTAL &nbsp;&nbsp;&nbsp;<?php echo numberFormat($arrData[0]->Ss_Descuento + $arrData[0]->Ss_Descuento_Impuesto, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if($fDescuentoTotalItem>0.00){ ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" colspan="4">DSCTO. TOTAL &nbsp;&nbsp;&nbsp;<?php echo numberFormat($fDescuentoTotalItem, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <?php if ($Ss_Gratuita > 0.00) { ?>
          <tr class="tr-theadFormat tr-totales">
          <th class="text-right" colspan="4">GRATUITA &nbsp;&nbsp;&nbsp;<?php echo numberFormat($Ss_Gratuita, 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" colspan="4">TOTAL &nbsp;&nbsp;&nbsp;<?php echo numberFormat($arrData[0]->Ss_Total - $Ss_Gratuita, 2, '.', ','); ?></th>
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
            <th class="text-left content">&nbsp;</th>
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
      <?php if (!empty($arrData[0]->No_Vendedor)) { ?>
      <br><br>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-center content" style="font-size: 7px"><?php echo $arrData[0]->No_Vendedor; ?></th>
          </tr>
          <?php if (!empty($arrData[0]->Nu_Celular_Vendedor)) { ?>
          <tr class="tr-sub_thead">
            <th class="text-center content"><?php echo '<b>Celular:</b> ' . $arrData[0]->Nu_Celular_Vendedor; ?></th>
          </tr>
          <?php } ?>
          <?php if (!empty($arrData[0]->Txt_Email_Vendedor)) { ?>
          <tr class="tr-sub_thead">
            <th class="text-center content"><?php echo '<b>Email:</b> ' . $arrData[0]->Txt_Email_Vendedor; ?></th>
          </tr>
          <?php } ?>
        </thead>
      </table>
      <?php } ?>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <br/>
            <td class="text-center title" colspan="2" style="width: 100%">Generado por <b>laesystems.com</b></td>
          </tr>
        </thead>
      </table>
    </body>
</html>