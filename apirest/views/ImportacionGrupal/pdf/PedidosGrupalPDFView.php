<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>ProBusiness</title>
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
                <div style="border-top: 2.7px solid #FF6700">
                  &nbsp;<br>
                  Día <?php echo dateNow('dia'); ?> de <?php echo getNameMonth(dateNow('mes')); ?> del <?php echo dateNow('año'); ?>
                </div>
              </div>
            </th>
            <th class="text-right sub_title_nro">
              <div>
                &nbsp;<br>
                <div style="border-top: 2.7px solid #FF6700">
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
            <th class="text-left content" style="width: 25%"></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 15%;"><b><?php echo $arrData[0]->No_Tipo_Documento_Identidad_Breve; ?></b></th>
            <th class="text-left content" style="width: 35%;"><?php echo ': ' . $arrData[0]->Nu_Documento_Identidad; ?></th>
            <th class="text-left content" style="width: 25%;"></th>
            <th class="text-left content" style="width: 25%;"></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 15%;"><b>DENOMINACIÓN</b></th>
            <th class="text-left content" style="width: 35%;"><?php echo ': ' . $arrData[0]->No_Entidad; ?></th>
            <th class="text-left content" style="width: 25%;"></th>
            <th class="text-left content" style="width: 25%"><b>FORMA DE PAGO:</b> <?php echo $arrData[0]->No_Medio_Pago; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content" style="width: 15%;"><b>DIRECCIÓN</b></th>
            <th class="text-left content" style="width: 35%;"><?php echo ': ' . $arrData[0]->Txt_Direccion_Entidad; ?></th>
            <th class="text-left content" style="width: 25%;"></th>
            <th class="text-left content" style="width: 25%"><b>MONEDA:</b> <?php echo $arrData[0]->No_Moneda; ?></th>
          </tr>
        </thead>
      </table>
      <br/>
      <br/>
	  	<table class="table_pdf" border="0" cellpadding="2">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left border-left border-right">Observaciones</th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"><?php echo $arrData[0]->No_Importacion_Grupal; ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content"></th>
          </tr>
        </thead>
      </table>
	  	<table class="table_pdf" cellpadding="5">
        <thead>
          <tr class="tr-theadFormat tr-header tr-header-detalle">
            <th class="text-left border-left" style="width: 12%">CÓDIGO</th>
            <th class="text-left" style="width: 48%">DESCRIPCIÓN</th>
            <th class="text-left" style="width: 15%">CANTIDAD</th>
            <th class="text-right" style="width: 10%">PRECIO</th>
            <th class="text-right border-right" style="width: 15%;">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $iCounter = 0; 
            $sNameClassTrDetalle = '';
            foreach($arrData as $row) {
              $sNameClassTrDetalle = ($iCounter%2) == 0 ? 'tr-fila_par' : 'tr-fila_impar';
            ?>
              <tr class="<?php echo $sNameClassTrDetalle; ?>">
                <td class="text-left border-left" style="width: 12%"><?php echo nl2br($row->Nu_Codigo_Barra); ?></td>
                <td class="text-left" style="width: 48%"><?php echo nl2br($row->No_Producto); ?></td>
                <td class="text-left" style="width: 15%"><?php echo round($row->Qt_Producto, 2) . '<br>' . ( !empty($row->No_Unidad_Medida) ? $row->No_Unidad_Medida : $row->No_Unidad_Medida_2); ?></td>
                <td class="text-right" style="width: 10%"><?php echo numberFormat($row->Ss_Precio, 2, '.', ','); ?></td>
                <td class="text-right border-right" style="width: 15%"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
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
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">TOTAL</th>
            <th class="text-right" style="width: 15%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat($arrData[0]->importe_total, 2, '.', ','); ?></th>
          </tr>
          <?php if($arrData[0]->Nu_Estado_Pedido==2){//2= ?>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">A CUENTA</th>
            <th class="text-right" style="width: 15%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat(($arrData[0]->importe_total/2), 2, '.', ','); ?></th>
          </tr>
          <tr class="tr-theadFormat tr-totales">
            <th class="text-right" style="width: 65%"></th>
            <th class="text-right" style="width: 20%">SALDO</th>
            <th class="text-right" style="width: 15%"><?php echo $arrData[0]->No_Signo . ' ' . numberFormat(($arrData[0]->importe_total/2), 2, '.', ','); ?></th>
          </tr>
          <?php } ?>
        </thead>
      </table>
	  	<table class="table_pdf" cellpadding="2">
        <tr class="tr-otros_campos_footer">
          <th class="text-center" colspan="3">&nbsp;</th>
        </tr>
        <tr class="tr-importe_letras">
          <th style="border-color: #7d7d7d; border-bottom-color:#7d7d7d; border-right-color:#7d7d7d; border-left-color:#7d7d7d;" class="text-center" colspan="3"><b>SON:</b> <?php echo strtoupper($totalEnLetras); ?></th>
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
      <br><br><br>
	  	<table class="table_pdf">
        <thead>
          <tr class="tr-sub_thead">
            <th class="text-left content">&nbsp;</th>
            <th class="text-right content"><b><?php echo (!empty($this->user->No_Grupo_Descripcion) ? $this->user->No_Grupo_Descripcion : ''); ?></b></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content">&nbsp;</th>
            <th class="text-right content" style="font-size: 10.5px"><?php echo (!empty($this->user->No_Nombres_Apellidos) ? $this->user->No_Nombres_Apellidos : ''); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content">&nbsp;</th>
            <th class="text-right content"><?php echo (!empty($this->user->No_Usuario) ? '<b>Email:</b> ' . $this->user->No_Usuario : ''); ?></th>
          </tr>
          <tr class="tr-sub_thead">
            <th class="text-left content">&nbsp;</th>
            <th class="text-right content"><?php echo (!empty($this->user->Nu_Celular) ? '<b>Celular:</b> ' . $this->user->Nu_Celular : ''); ?></th>
          </tr>
        </thead>
      </table>
    </body>
</html>