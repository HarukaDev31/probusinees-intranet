<?php
$totCantidad = 0;
$totVendido = 0.00;
?>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
    	<thead>
    		<tr>
    			<th class="text-center">Nombre</th>
    			<th style="width:70px;" class="text-right">Cantidad</th>
    			<th style="width:90px;" class="text-right">Vendido</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php if(count($reporte)==0): ?>
    		<tr>
    			<td colspan="3" class="text-center">No hay resultados</td>
    		</tr>
    		<?php endif; ?>
    		<?php
    		    foreach($reporte as $r):
    		?>
    		<tr>
    			<td style="font-size:12px;" class="text-left" title="<?php echo $r->No_Producto; ?>" ><?php echo (strlen($r->No_Producto) > 80 ? $r->No_Producto . '..' : $r->No_Producto); ?></td>
    			<td style="font-size:12px;" class="text-right"><?php echo numberFormat($r->Qt_Producto, 2, '.', ','); $totCantidad += $r->Qt_Producto; ?></td>
    			<td style="font-size:12px;" class="text-right"><?php echo $r->No_Signo ?> <?php echo numberFormat($r->Ss_Vendido, 2, '.', ','); $totVendido += $r->Ss_Vendido; ?></td>
    		</tr>
    		<?php endforeach; ?>
    	</tbody>
    	<?php if(count($reporte) > 0): ?>
    	<tfoot>
    		<tr>
    			<th class="total text-right">Total</th>
    			<th style="width:140px;" class="total text-right"><?php echo numberFormat($totCantidad, 2, '.', ','); ?></th>
    			<th style="width:140px;" class="total text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($totVendido, 2, '.', ','); ?></th>
    		</tr>
    	</tfoot>
    	<?php endif; ?>
    </table>
</div>