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
          <?php if ($arrData[0]->Nu_Logo_Empresa_Ticket==1 && !empty($sUrlImagen)) { ?>
            <tr class="tr-head">
              <th class="text-center standar"><img style="height: <?php echo $arrDataEmpresa[0]->Nu_Height_Logo_Ticket; ?> px; width: <?php echo $arrDataEmpresa[0]->Nu_Width_Logo_Ticket; ?>px;" src="<?php echo $sUrlImagen; ?>"></th>
            </tr>
          <?php
          } if (!empty($arrData[0]->No_Empresa_Comercial)) { ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold"><?php echo $arrData[0]->No_Empresa_Comercial; ?></th>
          </tr>
          <?php } else { ?>
          <tr class="tr-head">
            <th class="text-center standar title_bold" style="font-size: 6.5px;"><?php echo $arrData[0]->No_Empresa; ?></th>
          </tr>
          <?php } ?>
          <tr class="tr-sub_thead">
            <td class="text-center standar title_bold" style="font-size: 6.5px;"><?php echo $arrData[0]->Txt_Direccion_Empresa;?></td>
          </tr>
          <?php if ($arrData[0]->Txt_Direccion_Empresa != $arrData[0]->Txt_Direccion_Almacen) { ?>
          <tr class="tr-sub_thead">
            <td class="text-center standar title_bold" style="font-size: 6.5px;"><?php echo $arrData[0]->Txt_Direccion_Almacen;?></td>
          </tr>
          <?php }
          if ( !empty($arrData[0]->No_Dominio_Empresa) || !empty($arrData[0]->Txt_Email_Empresa)) { ?>
          <tr class="tr-sub_thead">
            <td class="text-center title"><?php echo $arrData[0]->No_Dominio_Empresa . ' ' . $arrData[0]->Txt_Email_Empresa; ?></td>
          </tr>
          <?php }
          if ( !empty($arrData[0]->Nu_Celular_Empresa) || !empty($arrData[0]->Nu_Telefono_Empresa)) { ?>
          <tr class="tr-sub_thead">
            <td class="text-center title"><?php echo $arrData[0]->Nu_Celular_Empresa . ' ' . $arrData[0]->Nu_Telefono_Empresa; ?></td>
          </tr>
          <?php } ?>
          <tr class="tr-sub_thead">
            <th class="text-center title"><?php echo 'PRE CUENTA - ' . $arrData[0]->ID_Pedido_Cabecera; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-center title"><?php echo $arrData[0]->No_Escenario_Restaurante . ' - ' . $arrData[0]->No_Mesa_Restaurante; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-center title">&nbsp;</th>
          </tr>
        </thead>
      </table>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <td class="text-left title" style="width: 30%;"><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve; ?></td>
            <td class="text-left title" style="width: 70%;"><?php echo $arrData[0]->Nu_Documento_Identidad; ?></td>
          </tr>
          <tr class="tr-sub_thead">
            <td class="text-left title" style="width: 30%;">CLIENTE</td>
            <td class="text-left title" style="width: 70%;"><?php echo $arrData[0]->No_Entidad; ?></td>
          </tr>
          <tr class="tr-sub_thead">
            <td class="text-left title" style="width: 30%;">F. EMISIÓN</td>
            <td class="text-left title" style="width: 70%;"><?php echo allTypeDate($arrData[0]->Fe_Emision_Hora, '-', 0); ?></td>
          </tr>
          <tr class="tr-sub_thead">
            <td class="text-left title" style="width: 30%;">CAJERO</td>
            <td class="text-left title" style="width: 70%;"><?php echo $arrData[0]->No_Empleado; ?></td>
          </tr>
          <tr class="tr-sub_thead">
            <td class="text-left title" style="width: 30%;">MOZO</td>
            <td class="text-left title" style="width: 70%;"><?php echo $arrData[0]->No_Mesero; ?></td>
          </tr>
          <tr class="tr-sub_thead">
            <td class="text-left title" style="width: 30%;">N. PERSONA</td>
            <td class="text-left title" style="width: 70%;"><?php echo $arrData[0]->Nu_Cantidad_Personas_Restaurante; ?></td>
          </tr>
        </thead>
      </table>
      <br><br>
	  	<table class="table_pdf" border="0">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left title" style="width: 16%">CANT.</th>
            <th class="text-left title" style="width: 50%">DESCRIPCION</th>
            <th class="text-right title" style="width: 16%">P.U.</th>
            <th class="text-right title" style="width: 18%">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $sNombreBreveLaboratorio='';
            $Ss_SubTotal_Producto = 0.00;
            $fOperacionesGravadas = 0.00;
            $fIGV = 0.00;
            $fOperacionesInafectas = 0.00;
            $fOperacionesExoneradas = 0.00;
            $fTotalIcbper = 0.00;
            $fCantidadItem = 0.000;
            $sPorecentajeDescuento = "0.00 %";
            $iNumImpuestoDescuento = 0;
            $iNumImpuestoDescuentoIGV = 0;
            $iNumImpuestoDescuentoEXO = 0;
            $fImpuestoConfiguracionIGV = 1;
            $fDescuentoItem = 0;

            $fGratuitaRegalo =0.00;

            foreach($arrData as $row) {
              $fCantidadItem = $row->Qt_Producto;
              
              if ($row->Nu_Tipo_Impuesto == "1" && $row->ID_Impuesto_Icbper != '1') {//IGV
                $iNumImpuestoDescuentoIGV = 1;
                $fImpuestoConfiguracionIGV = $row->Ss_Impuesto;
                $fDescuentoItem += $row->Ss_Descuento_Producto;
              }
                
              if ($row->Nu_Tipo_Impuesto == "3" && $row->ID_Impuesto_Icbper != '1') {//EXO
                $iNumImpuestoDescuentoEXO = 1;
                $fDescuentoItem += $row->Ss_Descuento_Producto;
              }

              
              if ($row->Nu_Tipo_Impuesto == "4") {//GRATIS
                $fGratuitaRegalo += $row->Ss_Total_Producto;
              }
              ?>
              <tr class="tr-sub_thead">
                <td class="text-left title" style="width: 16%"><?php echo number_format($fCantidadItem, 2, '.', ','); ?></td>
                <td class="text-left title" style="width: 50%"><?php echo nl2br((!empty($row->No_Codigo_Interno) ? '[' .  $row->No_Codigo_Interno . '] ' : '') . $row->No_Producto . (!empty($row->Txt_Nota_Item) ? $row->Txt_Nota_Item : '')); ?></td>
                <td class="text-right title" style="width: 16%"><?php echo numberFormat($row->ss_precio_unitario, 2, '.', ','); ?></td>
                <td class="text-right title" style="width: 18%"><?php echo numberFormat($row->Ss_Total_Producto, 2, '.', ','); ?></td>
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
          <tr class="tr-sub_thead">
            <th class="text-right title" style="width: 75%;">TOTAL</th>
            <th class="text-right title" style="width: 25%;"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($arrData[0]->Ss_Total - $fGratuitaRegalo, 2, '.', ','); ?></th>
          </tr>
        </thead>
      </table>
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