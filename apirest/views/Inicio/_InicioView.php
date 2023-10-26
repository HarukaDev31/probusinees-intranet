<?php
$sSignoMoneda = $this->user->No_Signo;
$fCantTrans = 0;
$fTotal = 0.00;
foreach ($reporte['arrPedidosEstados'] as $row){
  $fCantTrans += $row->Qt_Cantidad_Trans;
  $fTotal += $row->Ss_Total;
}
?>

<br>
<div class="row">
  <div class="col-md-4 offset-md-4 col-sm-3 offset-sm-3">
  </div>
  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-green"><i class="ion ion-bag"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Ventas totales</span>
        <span class="info-box-number" style="font-size: 2.5rem;"><?php echo $sSignoMoneda . ' ' . numberFormat($fTotal, 2, '.', ','); ?></span>
        <span class="info-box-number" style="font-weight: normal;"><?php echo $fCantTrans; ?> pedidos</span>
      </div>
    </div>
  </div>
</div>
<!-- /.box footer TOTAL TRANS -->

<div id="div-inicio-filtro-reporte-grafico" class="col-xs-12 col-sm-12 col-md-12 tab-pane" style="padding: 0px;margin: 0px;">
<canvas id="canvas-graficaBar" class="wrapper-home"></canvas>
</div>