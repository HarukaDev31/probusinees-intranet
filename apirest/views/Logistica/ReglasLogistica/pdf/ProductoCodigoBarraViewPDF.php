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
          font-size: 7px;
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
      <table class="table_pdf" border="0">
        <thead>
          <tr class="tr-theadFormat">
            <?php if($arrCabecera['iNumeroColuma']==1 || $arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="left">PRECIO S/ <?php echo numberFormat($arrCabecera['arrData']->Ss_Precio, 2, '.', ','); ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center">PRECIO S/ <?php echo numberFormat($arrCabecera['arrData']->Ss_Precio, 2, '.', ','); ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <td align="left">PRECIO S/ <?php echo numberFormat($arrCabecera['arrData']->Ss_Precio, 2, '.', ','); ?></td>
            <?php } ?>
          </tr>
          <tr class="tr-theadFormat">
            <?php            
            $sNombreProducto=substr($arrCabecera['arrData']->No_Producto, 0, 40);
            if($arrCabecera['iNumeroColuma']==1 || $arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="left"><?php echo $sNombreProducto; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $sNombreProducto; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <td align="left"><?php echo $sNombreProducto; ?></td>
            <?php } ?>
          </tr>
          <?php if (!empty($arrCabecera['arrData']->No_Codigo_Interno) && $arrCabecera['iPrintSku']==1) { ?>
          <tr class="tr-theadFormat">
            <?php if($arrCabecera['iNumeroColuma']==1 || $arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="left"><?php echo $arrCabecera['arrData']->No_Codigo_Interno; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $arrCabecera['arrData']->No_Codigo_Interno; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <td align="left"><?php echo $arrCabecera['arrData']->No_Codigo_Interno; ?></td>
            <?php } ?>
          </tr>
          <?php } ?>
          <tr>
            <?php if($arrCabecera['iNumeroColuma']==1 || $arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <th align="left"><img src="<?php echo $arrCabecera['filepath_barcode']; ?>" ></th>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <th align="center"><img src="<?php echo $arrCabecera['filepath_barcode']; ?>" ></th>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <th align="center"><img src="<?php echo $arrCabecera['filepath_barcode']; ?>" ></th>
            <?php } ?>
            <!--<th align="left"><img src="<?php echo $arrCabecera['filepath_barcode']; ?>" height="35" width="140"></th>-->
          </tr>
        </thead>
      </table>
    </body>
</html>