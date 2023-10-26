<!DOCTYPE html>
	<head>
  <meta http-equiv=Content-Type content=text/html; charset=UTF-8/>
  <title>Laesystems</title>
    <style type=text/css>
      body {
        margin:0;
        padding:0;
        font-family: Arial, Helvetica, sans-serif;
      }

      .paco{
        background:black;
        color:white;
        display:inline-block;
      }

      .table_pdf {
        width: 100%;
      }
      
      .tr-theadFormat th{
        font-weight: bold;
      }
      
      .tr-theadFormat td{
        font-weight: bold;
        font-size: 7px;
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
            <td align="center" style="font-size: 11px;">PRECIO S/ <?php echo numberFormat($arrCabecera['arrData']->Ss_Precio, 2, '.', ','); ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center" style="font-size: 11px;">PRECIO S/ <?php echo numberFormat($arrCabecera['arrData']->Ss_Precio, 2, '.', ','); ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center" style="font-size: 11px;">PRECIO S/ <?php echo numberFormat($arrCabecera['arrData']->Ss_Precio, 2, '.', ','); ?></td>
            <?php } ?>
          </tr>
          <tr class="tr-theadFormat">
            <?php
            $sNombreProducto=substr($arrCabecera['arrData']->No_Producto, 0, 40);
            if($arrCabecera['iNumeroColuma']==1 || $arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $sNombreProducto; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $sNombreProducto; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $sNombreProducto; ?></td>
            <?php } ?>
          </tr>
          <?php if (!empty($arrCabecera['arrData']->No_Codigo_Interno) && $arrCabecera['iPrintSku']==1) { ?>
          <tr class="tr-theadFormat">
            <?php if($arrCabecera['iNumeroColuma']==1 || $arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $arrCabecera['arrData']->No_Codigo_Interno; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $arrCabecera['arrData']->No_Codigo_Interno; ?></td>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <td align="center"><?php echo $arrCabecera['arrData']->No_Codigo_Interno; ?></td>
            <?php } ?>
          </tr>
          <?php } ?>
          <tr>
            <?php if($arrCabecera['iNumeroColuma']==1 || $arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <th align="left"><img src="<?php echo $arrCabecera['filepath_barcode_url']; ?>" ></th>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==2 || $arrCabecera['iNumeroColuma']==3){ ?>
            <th align="center"><img src="<?php echo $arrCabecera['filepath_barcode_url']; ?>" ></th>
            <?php } ?>
            <?php if($arrCabecera['iNumeroColuma']==3){ ?>
            <th align="center"><img src="<?php echo $arrCabecera['filepath_barcode_url']; ?>" ></th>
            <?php } ?>
          </tr>
        </thead>
      </table>
    </body>
</html>