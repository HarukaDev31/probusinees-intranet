<!DOCTYPE html>
<html>
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>Laesystems | Registro de Compras</title>
        <style type="text/css">
            .table_pdf {
              width: 100%;
            }
            
            .tr-theadFormat th{
                font-weight: bold;
            }
                
            .tr-thead th{
                font-size: 3.5px;
                border: solid 1px #000000;
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
                    <th colspan="30">FORMATO <?php echo $arrCabecera['sNombreLibroSunat']; ?></th>
                </tr>
                <tr>
                    <td colspan="30">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="30">PERIODO: <?php echo $arrCabecera['fMonthText'] . ' ' . $arrCabecera['fYear']; ?></td>
                </tr>
                <tr>
                    <td colspan="30">RUC: <?php echo $this->empresa->Nu_Documento_Identidad; ?></td>
                </tr>
                <tr>
                    <td colspan="30">APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL: <?php echo $this->empresa->No_Empresa; ?></td>
                </tr>
                <tr>
                    <td colspan="30">&nbsp;</td>
                </tr>
                <tr class="tr-thead tr-theadFormat">
                    <th class="text-center" rowspan="3">NÚMERO CORRELATIVO DEL REGISTRO O CÓDIGO UNICO DE LA OPERACIÓN</th>
                    <th class="text-center" rowspan="3">FECHA DE EMISIÓN DEL COMPROBANTE DE PAGO O DOCUMENTO</th>
                    <th class="text-center" rowspan="3">FECHA DE VENCIMIENTO Y/O PAGO</th>
                    <th class="text-center" colspan="3">COMPROBANTE DE PAGO O DOCUMENTO</th>
                    <th class="text-center" rowspan="3">N° DEL COMPROBANTE DE PAGO, DOCUMENTO, N° DE ORDEN DEL FORMULARIO FÍSICO O VIRTUAL, N° DE DUA, DSI O LIQUIDACIÓN DE COBRANZA U OTROS DOCUMENTOS EMITIDOS POR SUNAT PARA ACREDITAR EL CRÉDITO FISCAL EN LA IMPORTACIÓN</th>
                    <th class="text-center" colspan="3">INFORMACIÓN DEL PROVEEDOR</th>
                    <th class="text-center" colspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O DE EXPORTACIÓN</th>
                    <th class="text-center" colspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O DE EXPORTACIÓN Y A OPERACIONES NO GRAVADAS</th>
                    <th class="text-center" colspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES NO GRAVADAS</th>
                    <th class="text-center" rowspan="3">VALOR DE LAS ADQUISICIONES NO GRAVADAS</th>
                    <th class="text-center" rowspan="3">ISC</th>
                    <th class="text-center" rowspan="3">ICBPER</th>
                    <th class="text-center" rowspan="3">OTROS TRIBUTOS Y CARGOS</th>
                    <th class="text-center" rowspan="3">IMPORTE TOTAL</th>
                    <th class="text-center" rowspan="3">N° DE COMPROBANTE DE PAGO EMITIDO POR SUJETO NO DOMICILIADO (2)</th>
                    <th class="text-center" colspan="2">CONSTANCIA DE DEPÓSITO DE DETRACCIÓN (3)</th>
                    <th class="text-center" rowspan="3">TIPO DE CAMBIO</th>
                    <th class="text-center" colspan="4">REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA</th>
                </tr>
                <tr class="tr-thead tr-theadFormat">
                    <th class="text-center" rowspan="2">TIPO (TABLA 10)</th>
                    <th class="text-center" rowspan="2">SERIE O CÓDIGO DE LA DEPENDENCIA ADUANERA (TABLA 11)</th>
                    <th class="text-center" rowspan="2">AÑO DE EMISIÓN DE LA DUA O DSI</th>
                    <th class="text-center" colspan="2">DOCUMENTO DE IDENTIDAD</th>
                    <th class="text-center" rowspan="2">APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL</th>
                    <th class="text-center" rowspan="2">BASE IMPONIBLE</th>
                    <th class="text-center" rowspan="2">IGV</th>
                    <th class="text-center" rowspan="2">BASE IMPONIBLE</th>
                    <th class="text-center" rowspan="2">IGV</th>
                    <th class="text-center" rowspan="2">BASE IMPONIBLE</th>
                    <th class="text-center" rowspan="2">IGV</th>
                    <th class="text-center" rowspan="2">NÚMERO</th>
                    <th class="text-center" rowspan="2">FECHA DE EMISIÓN</th>
                    <th class="text-center" rowspan="2">FECHA</th>
                    <th class="text-center" rowspan="2">TIPO (TABLA 10)</th>
                    <th class="text-center" rowspan="2">SERIE</th>
                    <th class="text-center" rowspan="2">COMPROBANTE DE PAGO O DOCUMENTO</th>
                </tr>
                <tr class="tr-thead tr-theadFormat">
                    <th class="text-center">TIPO (TABLA 2)</th>
                    <th class="text-center">NÚMERO</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ( $arrDetalle['sStatus'] == 'success' ) {
                    $sum_Ss_SubTotal_Gravadas = 0.00;
                    $sum_Ss_IGV = 0.00;
                    $sum_Ss_Gratuita = 0.00;
                    $sum_Ss_Inafecta = 0.00;
                    $sum_Ss_Exonerada = 0.00;
                    $sum_Ss_Percepcion = 0.00;
                    $sum_Ss_Icbper = 0.00;
                    $sum_Ss_Total = 0.00;
                    
                    $sumGeneral_Ss_SubTotal_Gravadas = 0.00;
                    $sumGeneral_Ss_IGV = 0.00;
                    $sumGeneral_Ss_Gratuita = 0.00;
                    $sumGeneral_Ss_Inafecta = 0.00;
                    $sumGeneral_Ss_Exonerada = 0.00;
                    $sumGeneral_Ss_Percepcion = 0.00;
                    $sumGeneral_Ss_Icbper = 0.00;
                    $sumGeneral_Ss_Total = 0.00;
    
                    $DOCU_Nu_Sunat_Codigo = '';
                    $ID_Tipo_Documento = 0;
                    $No_Tipo_Documento = '';
                    $counter = 0;
                    foreach($arrDetalle['arrData'] as $row) {
                        if ($DOCU_Nu_Sunat_Codigo != $row->DOCU_Nu_Sunat_Codigo) {
                            if ($counter != 0) { ?>
        	                    <tr class="tr-theadFormat">
        	                        <th class="text-right" colspan="10">Total <?php echo $No_Tipo_Documento ?>: </th>
                                    <th class="text-right"><?php echo numberFormat($sum_Ss_SubTotal_Gravadas, 2, '.', ''); ?></th>
                                    <th class="text-right"><?php echo numberFormat($sum_Ss_IGV, 2, '.', ''); ?></th>
                                    <th class="text-right"><?php echo numberFormat($sum_Ss_Gratuita, 2, '.', ''); ?></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"><?php echo numberFormat($sum_Ss_Inafecta + $sum_Ss_Exonerada, 2, '.', ''); ?></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"><?php echo numberFormat($sum_Ss_Icbper, 2, '.', ''); ?></th>
                                    <td class="text-right"><?php echo numberFormat($sum_Ss_Percepcion, 2, '.', ''); ?></td>
                                    <th class="text-right"><?php echo numberFormat($sum_Ss_Percepcion + $sum_Ss_Total, 2, '.', ''); ?></th>
          				        </tr>
                            <?php }
                            $sum_Ss_SubTotal_Gravadas = 0.00;
                            $sum_Ss_IGV = 0.00;
                            $sum_Ss_Gratuita = 0.00;
                            $sum_Ss_Inafecta = 0.00;
                            $sum_Ss_Exonerada = 0.00;
                            $sum_Ss_Percepcion = 0.00;
                            $sum_Ss_Icbper = 0.00;
                            $sum_Ss_Total = 0.00;
                            $DOCU_Nu_Sunat_Codigo = $row->DOCU_Nu_Sunat_Codigo;
                        } ?>
        	        <tr>
          				<td class="text-center"><?php echo $row->CUO; ?></td>
          				<td class="text-center"><?php echo $row->Fe_Emision; ?></td>
          				<td class="text-left"><?php echo ($row->ID_Tipo_Documento == 10 ? $row->Fe_Vencimiento : ''); ?></td>
          				<td class="text-center"><?php echo $row->DOCU_Nu_Sunat_Codigo; ?></td>
          				<td class="text-center"><?php echo $row->ID_Serie_Documento; ?></td>
          				<td class="text-right"></td>
          				<td class="text-left"><?php echo $row->ID_Numero_Documento_Inicial . $row->ID_Numero_Documento_Final; ?></td>
          				<td class="text-center"><?php echo $row->IDE_Nu_Sunat_Codigo; ?></td>
          				<td class="text-left" style="width:31px;"><?php echo $row->Nu_Documento_Identidad; ?></td>
          				<td class="text-left"><?php echo $row->No_Entidad; ?></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_SubTotal_Gravadas + $row->Ss_Exportacion, 2, '.', ''); ?></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_IGV, 2, '.', ''); ?></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_Gratuita, 2, '.', ''); ?></td>
          				<td class="text-right"></td>
          				<td class="text-right"></td>
          				<td class="text-right"></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_Inafecta + $row->Ss_Exonerada, 2, '.', ''); ?></td>
          				<td class="text-right"></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_Icbper, 2, '.', ''); ?></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_Percepcion, 2, '.', ''); ?></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_Percepcion + $row->Ss_Total, 2, '.', ''); ?></td>
          				<td class="text-right"></td>
          				<td class="text-right"><?php echo $row->Nu_Detraccion; ?></td>
          				<td class="text-right"><?php echo ($row->Fe_Detraccion == '01/01/0001' ? '' : $row->Fe_Detraccion); ?></td>
          				<td class="text-right"><?php echo numberFormat($row->Ss_Tipo_Cambio, 3, '.', ''); ?></td>
          				<td class="text-center"><?php echo ($row->Fe_Emision_Modificar == '01/01/0001' ? '' : $row->Fe_Emision_Modificar); ?></td>
          				<td class="text-center"><?php echo $row->ID_Tipo_Documento_Modificar; ?></td>
          				<td class="text-center"><?php echo $row->ID_Serie_Documento_Modificar; ?></td>
          				<td class="text-left"><?php echo $row->ID_Numero_Documento_Modificar; ?></td>
          			</tr>
                    <?php
                        $counter++;
                        $sum_Ss_SubTotal_Gravadas += $row->Ss_SubTotal_Gravadas + $row->Ss_Exportacion;
                        $sum_Ss_IGV += $row->Ss_IGV;
                        $sum_Ss_Gratuita += $row->Ss_Gratuita;
                        $sum_Ss_Inafecta += $row->Ss_Inafecta;
                        $sum_Ss_Exonerada += $row->Ss_Exonerada;
                        $sum_Ss_Percepcion += $row->Ss_Percepcion;
                        $sum_Ss_Icbper += $row->Ss_Icbper;
                        $sum_Ss_Total += $row->Ss_Total;

                        $sumGeneral_Ss_SubTotal_Gravadas += $row->Ss_SubTotal_Gravadas + $row->Ss_Exportacion;
                        $sumGeneral_Ss_IGV += $row->Ss_IGV;
                        $sumGeneral_Ss_Gratuita += $row->Ss_Gratuita;
                        $sumGeneral_Ss_Inafecta += $row->Ss_Inafecta;
                        $sumGeneral_Ss_Exonerada += $row->Ss_Exonerada;
                        $sumGeneral_Ss_Percepcion += $row->Ss_Percepcion;
                        $sumGeneral_Ss_Icbper += $row->Ss_Icbper;
                        $sumGeneral_Ss_Total += $row->Ss_Total;
                        
                        if ($ID_Tipo_Documento != $row->ID_Tipo_Documento) {
                            $ID_Tipo_Documento = $row->ID_Tipo_Documento;
                            $No_Tipo_Documento = $row->No_Tipo_Documento;
                        }
                    } ?>
                    <tr class="tr-theadFormat">
                        <th class="text-right" colspan="10">Total <?php echo $No_Tipo_Documento ?>: </th>
                        <th class="text-right"><?php echo numberFormat($sum_Ss_SubTotal_Gravadas, 2, '.', ''); ?></th>
                        <th class="text-right"><?php echo numberFormat($sum_Ss_IGV, 2, '.', ''); ?></th>
                        <th class="text-right"><?php echo numberFormat($sum_Ss_Gratuita, 2, '.', ''); ?></th>
                        <th class="text-right"></th>
                        <th class="text-right"></th>
                        <th class="text-right"></th>
                        <th class="text-right"><?php echo numberFormat($sum_Ss_Inafecta + $sum_Ss_Exonerada, 2, '.', ''); ?></th>
                        <th class="text-right"></th>
                        <th class="text-right"><?php echo numberFormat($sum_Ss_Icbper, 2, '.', ''); ?></th>
                        <td class="text-right"><?php echo numberFormat($sum_Ss_Percepcion, 2, '.', ''); ?></td>
                        <th class="text-right"><?php echo numberFormat($sum_Ss_Percepcion + $sum_Ss_Total, 2, '.', ''); ?></th>
      		        </tr>
      		        
                    <tr class="tr-theadFormat">
                        <th class="text-right" colspan="10">Total General: </th>
                        <th class="text-right"><?php echo numberFormat($sumGeneral_Ss_SubTotal_Gravadas, 2, '.', ''); ?></th>
                        <th class="text-right"><?php echo numberFormat($sumGeneral_Ss_IGV, 2, '.', ''); ?></th>
                        <th class="text-right"><?php echo numberFormat($sumGeneral_Ss_Gratuita, 2, '.', ''); ?></th>
                        <th class="text-right"></th>
                        <th class="text-right"></th>
                        <th class="text-right"></th>
                        <th class="text-right"><?php echo numberFormat($sumGeneral_Ss_Inafecta + $sumGeneral_Ss_Exonerada, 2, '.', ''); ?></th>
                        <th class="text-right"></th>
                        <th class="text-right"><?php echo numberFormat($sumGeneral_Ss_Icbper, 2, '.', ''); ?></th>
                        <td class="text-right"><?php echo numberFormat($sumGeneral_Ss_Percepcion, 2, '.', ''); ?></td>
                        <th class="text-right"><?php echo numberFormat($sumGeneral_Ss_Percepcion + $sumGeneral_Ss_Total, 2, '.', ''); ?></th>
      		        </tr>
  		        <?php } else { ?>
        	        <tr>
                        <td class="text-center" colspan="7">&nbsp;</td>
          				<td class="text-center" colspan="3">Sin Operaciones</td>
          				<td class="text-right">0.00</td>
          				<td class="text-right">0.00</td>
          				<td class="text-right" colspan="4">&nbsp;</td>
          				<td class="text-right">0.00</td>
          				<td class="text-right"></td>
          				<td class="text-right">0.00</td>
          				<td class="text-right">0.00</td>
          				<td class="text-right">0.00</td>
          				<td class="text-right" colspan="8">&nbsp;</td>
          			</tr>
  		        <?php } ?>
		    </tbody>
        </table>
    </body>
</html>