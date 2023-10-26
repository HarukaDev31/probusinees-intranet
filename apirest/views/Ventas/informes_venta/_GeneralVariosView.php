<?php
$TInterno = 0;
$TBoleta = 0;
$TFactura = 0;
$TNCredito = 0;
$TNDedito = 0;

$TVendido = 0;
$TComprado = 0;
$TGanado = 0;

$sCssDivColumn = "col-md-2";
$sCssDisplayOrder='style="display:none"';
if ($tipo == 4 || $tipo == 5 || $tipo == 6) {
	$sCssDisplayOrder='';
	$sCssDivColumn = "";
}

if ( $Nu_Tipo_Producto == "") {
	$selected_producto = "";
	$selected_servicio = "";
	$selected_todos = "selected";
}

if($Nu_Tipo_Producto == "1"){
	$selected_producto = "selected";
	$selected_servicio = "";
	$selected_todos = "";
}

if($Nu_Tipo_Producto == "0"){
	$selected_producto = "";
	$selected_servicio = "selected";
	$selected_todos = "";
}

if($iOrder == "1"){
	$selected_order_importe = "selected";
	$selected_order_cantidad = "";
}

if($iOrder == "2"){
	$selected_order_importe = "";
	$selected_order_cantidad = "selected";
}

if($iImpuesto == "0"){
	$selected_impuesto_no = "selected";
	$selected_impuesto_si = "";
}

if($iImpuesto == "1"){
	$selected_impuesto_no = "";
	$selected_impuesto_si = "selected";
}

?>
<script>
	$(document).ready(function(){
		$( "#dvFiltro select" ).change(function(){
			Reporte($("#dvAnio select").val(), $("#dvMes select").val(), $("#cbo-Monedas select").val(), $("#div-Nu_Tipo_Producto select").val(), $("#div-iOrder select").val(), $("#div-impuesto select").val());
		})
		
		$( ".aReporteDiarioDetalle" ).click(function(){
			var fecha = $(this).data('fecha');
			var tipo_producto = $( "#div-Nu_Tipo_Producto select" ).val();
			var iImpuesto = $( "#div-impuesto select" ).val();
			AjaxPopupModal('aReporteDiarioDetalle', 'Detalle de Productos Vendidos', 'Ventas/informes_venta/GeneralVariosController/Ajax/SubReporte', { tipo: 'reportediariodetalle', fecha: fecha, tipo_producto : tipo_producto, iImpuesto : iImpuesto});
		})

		<?php if($tipo == 1 || $tipo == 2 || $tipo == 3): ?>
  		$("#liGrafica").click(function(){
  			var typeGraf = $(this).attr("data-tipoGrafico");
  			if(!$(this).hasClass('loared'))
  			{
  				$(this).addClass('loaded');
  				setTimeout(function(){
  					CargarGrafica(typeGraf);					
  				})
  			}
  		})
  		
  		$("#liGraficaBar").click(function(){
  			var typeGraf = $(this).attr("data-tipoGrafico");
  			if(!$(this).hasClass('loared')){
  				$(this).addClass('loaded');
  				setTimeout(function(){
  					CargarGrafica(typeGraf);					
  				})
  			}
  		})
		<?php endif; ?>
	})
	
	<?php if($tipo == 1 || $tipo == 2 || $tipo == 3): ?>
	
	function CargarGrafica(typeGraf){
	    $(document).ready(function() {
	    	var ctx = (typeGraf == 'line' ? document.getElementById('grafica').getContext('2d') : document.getElementById('graficaBar').getContext('2d'));
			var grafica = new Chart(ctx, {
				type: typeGraf,
				data: {
			    	labels: [<?php echo $reporte['Grafica']['Categoria'];?>, ],
					datasets: [{
						label: 'Vendido',
						data: [<?php echo $reporte['Grafica']['Vendido']; ?>, ],
						borderColor: "rgba(0,48,97,0.6)",
						backgroundColor: (typeGraf == 'line' ? "rgba(0,128,255,0.1)" : "rgba(0,60,255,0.5)"),
					}]
				},
				options: {
					responsive: true,
		            legend: {
		            	display: true,
	                	position: 'right',
		            },
	                title: {
	                    display: true,
	                    text: $("#sltReporte option:selected").text()
	                },
	                tooltips: {
	                	callbacks: {
		                	label: function(tooltipItem, data) {
		                		var No_Tipo_Ganancia = data.datasets[tooltipItem.datasetIndex].label;
		                		var moneda	= '<?php echo $reporte['Grafica']['Moneda']; ?>';
		                		moneda		= moneda.split(',');
		                		var signo_moneda = '';
		                		for (var i = 1; i < moneda.length; i++){
		                			if(i == data.labels[tooltipItem.index])//Comparo con el dia que selecciono vs lo que tengo en BD de moneda
		                				signo_moneda = moneda[i - 1];//Le resto uno porque el array empieza en 0
		                		}
		                		var amount = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
			                    return No_Tipo_Ganancia + " - " + '<?php
			                    	if($tipo == 1) echo 'Día';
			                    	if($tipo == 2) echo 'Mes';
			                    	if($tipo == 3) echo 'Año';
			                    ?> ' + data.labels[tooltipItem.index] + ': ' + signo_moneda + ' ' + parseFloat(amount).toFixed(4);
		                	},
						},
			        },
				}
			});
	    });
	}
	<?php endif; ?>
</script>
<div id="dvFiltro" class="row">
	<div class="hidden col-xs-12 col-sm-12 <?php echo $sCssDivColumn; ?>"></div>
	<div class="col-xs-5 col-sm-3 col-md-2">
		<?php if($tipo == 1): ?>
		<div id="dvMes" class="form-group">
		  <label>Mes</label>
		  <?php echo Select('cbo-mes', 'valor', 'mes', Months(), $m, true, ''); ?>
		</div>
		<?php endif; ?>
		<?php if($tipo==7): ?>
		<div id="dvMes" class="form-group">
		  <label>Mes</label>
		  <?php
		  echo Select('cbo-mes', 'valor', 'mes', Months(), $m, false, '');
		  //array_debug(Months());
		  ?>
		</div>
		<?php endif; ?>
		<?php if($tipo==4 || $tipo==5): //4 = top productos //5=top clientes?>
		<div id="dvMes" class="form-group">
		  <label>Mes</label>
		  <?php //var_dump($m); ?>
		  <select id="cbo-mes" class="form-control">
		  <?php
		  foreach(Months() as $row){
			$selected = ($row->valor == $m ? $selected = 'selected' : ''); ?>
			<option value="<?php echo  $row->valor; ?>" <?php echo  $selected; ?>><?php echo  $row->mes; ?></option>
			<?php
		  }
		  ?>
		  	<?php $selected = (0 == $m ? $selected = 'selected' : ''); ?>
		  	<option value="0" <?php echo  $selected; ?>>TODOS</option>
		  </select>
		</div>
		<?php endif; ?>
	</div>
	<div class="col-xs-4 col-sm-3 col-md-2">
		<?php if($tipo == 1 || $tipo == 2 || $tipo == 4 || $tipo == 5 || $tipo == 6 || $tipo == 7): ?>
		<div id="dvAnio" class="form-group">
		  <label>Año</label>
	      <?php echo Select('cbo-year', 'year', 'year', YearsYMD($this->empresa->Fe_Inicio_Sistema), $y, true, ''); ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="col-xs-3 col-sm-3 col-md-2">
		<div id="cbo-Monedas" class="form-group">
		    <label>Moneda</label>
			<?php
	    	if($ID_Moneda > 0)
	    		echo Select('cbo-Monedas', 'ID_Moneda', 'No_Moneda', $arrMonedas, $ID_Moneda, true, '');
	    	else
	    		echo Select('cbo-Monedas', 'ID_Moneda', 'No_Moneda', $arrMonedas, $ID_Moneda, false, '');
	    	?>
		</div>
	</div>
	<div class="col-xs-4 col-sm-3 col-md-2">
		<div id="div-impuesto" class="form-group">
		    <label>Impuestos</label>
			<select id="cbo-impuesto" class="form-control input-group">
				<option <?php echo $selected_impuesto_no; ?> value="0">No</option>
				<option <?php echo $selected_impuesto_si; ?> value="1">Si</option>
			</select>
		</div>
	</div>
	<div class="col-xs-4 col-sm-3 col-md-2">
		<div id="div-Nu_Tipo_Producto" class="form-group">
		    <label>Tipo Item</label>
			<select id="cbo-Nu_Tipo_Producto" class="form-control input-group">
				<option <?php echo $selected_todos; ?> value="">Todos</option>
				<option <?php echo $selected_producto; ?> value="1">Productos</option>
        <option <?php echo $selected_servicio; ?> value="0">Servicios</option>
      </select>
		</div>
	</div>
	<div class="col-xs-4 col-sm-3 col-md-2" <?php echo $sCssDisplayOrder; ?>>
		<div id="div-iOrder" class="form-group">
		    <label>Ordenar por</label>
			<select id="cbo-iOrder" class="form-control input-group">
				<option <?php echo $selected_order_importe; ?> value="1">Importe</option>
				<option <?php echo $selected_order_cantidad; ?> value="2">Cantidad</option>
			</select>
		</div>
	</div>
</div>
<br>
<div class="table-responsive">
<?php if($tipo==1 || $tipo==2 || $tipo==3){ ?>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
		  <li class="active"><a href="#r1" data-toggle="tab">Tabla</a></li>
		  <li id="liGrafica" data-tipoGrafico="line"><a class="Graf" href="#r2" data-toggle="tab">Gráfíca Lineal</a></li>
		  <li id="liGraficaBar" data-tipoGrafico="bar"><a class="Graf" href="#r3" data-toggle="tab">Gráfíca Barra</a></li>
		</ul>
		
		<!-- Tab panes -->
		<div class="tab-content">
		  <div class="tab-pane in active" id="r1">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th colspan="7"><?php echo $titulo; ?> (no incluye impuestos)</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th></th>
						<th style="width:140px;" class="text-right">Nota de Venta</th>
						<th style="width:140px;" class="text-right">Boleta</th>
						<th style="width:140px;" class="text-right">Factura</th>
						<th style="width:140px;" class="text-right">N/Crédito</th>
						<th style="width:140px;" class="text-right">N/Débito</th>
						<th style="width:140px;" class="text-right">Vendido</th>
					</tr>
					<?php if(count($reporte['Tabla'])==0): ?>
					<tr>
						<th colspan="7" class="text-center">No hay resultados</th>
					</tr>
					<?php endif; ?>
					<?php foreach($reporte['Tabla'] as $r): ?>
					<tr class="<?php
						if($tipo == 1){
							echo $r->Fe_Emision == date('d/m/Y') ? 'today' : '';	
						}
						if($tipo == 2){
							echo $r->Fe_Emision == date('m') ? 'today' : '';
						}
						if($tipo == 3){
							echo $r->Fe_Emision == date('Y') ? 'today' : '';
						}?>">
						<th class="text-right" style="width:60px;">
							<?php if($tipo == 1){ ?>
								<a class="aReporteDiarioDetalle" title="Haga click para ver un resumen de los productos vendidos" href="#" data-fecha="<?php echo $r->Fe_Emision; ?>">
									<?php echo DateFormat($r->Fe_Emision, $tipo); ?>
								</a>
							<?php } elseif( $tipo == 2) {?>
								<?php echo (MonthToSpanish($r->Fe_Emision, true)); ?>
							<?php } else {?>
								<?php echo $r->Fe_Emision; ?>
							<?php } ?>
						</th>
						<td class="text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($r->Interno, 2, '.', ','); $TInterno += $r->Interno; ?></td>
						<td class="text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($r->Boleta, 2, '.', ','); $TBoleta += $r->Boleta; ?></td>
						<td class="text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($r->Factura, 2, '.', ','); $TFactura += $r->Factura;  ?></td>
						<td class="text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($r->NCredito, 2, '.', ','); $TNCredito += $r->NCredito;  ?></td>
						<td class="text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($r->NDebito, 2, '.', ','); $TNDedito += $r->NDebito;  ?></td>
						<td class="text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($r->Vendido, 2, '.', ','); $TVendido += $r->Vendido; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				<?php if(count($reporte['Tabla']) > 0): ?>
				<tfoot>
					<tr>
						<th class="text-right">Total</th>
						<th class="text-right"><?php echo $r->No_Signo; ?> <b><?php echo numberFormat($TInterno, 2, '.', ','); ?></th>
						<th class="text-right"><?php echo $r->No_Signo; ?> <b><?php echo numberFormat($TBoleta, 2, '.', ','); ?></th>
						<th class="text-right"><?php echo $r->No_Signo; ?> <b><?php echo numberFormat($TFactura, 2, '.', ','); ?></th>
						<th class="text-right"><?php echo $r->No_Signo; ?> <b><?php echo numberFormat($TNCredito, 2, '.', ','); ?></th>
						<th class="text-right"><?php echo $r->No_Signo; ?> <b><?php echo numberFormat($TNDedito, 2, '.', ','); ?></th>
						<th style="width:140px;" class="total text-right"><?php echo $r->No_Signo; ?> <?php echo numberFormat($TVendido, 2, '.', ','); ?></th>
					</tr>
				</tfoot>
				<?php endif; ?>
			</table>		  
		  </div>
		  <div class="tab-pane" id="r2">
		  	<canvas id="grafica"></canvas>
		  </div>
		  <div class="tab-pane" id="r3">
		  	<canvas id="graficaBar"></canvas>
		  </div>
		</div>
	<?php }else if($tipo==4 || $tipo==5 || $tipo==7){
		$iColspanTopClienteProducto=($tipo==5 ? 4 : 3);
		?>

		<?php if($tipo == 4) { ?>
			<input type="text" id="id-marca" onkeyup="topProductosxMarca()" placeholder="Buscar por Marca" title="Ingresar nombre de marca" class="form-control"><br>
		<?php } ?>

		<table class="table report" id="top-venta">
			<thead>
				<tr>
					<th colspan="<?php echo $iColspanTopClienteProducto; ?>"><?php echo $titulo; ?> (no incluye impuestos)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Descripción</th>
					<th style="width:100px;" class="text-right">
						<?php if($tipo==7) echo 'N de Ventas'; ?>
						<?php if($tipo==5) echo 'N de Veces'; ?>
						<?php if($tipo==4) echo 'Cantidad'; ?>
					</th>
					<?php if($tipo==5) { ?>
						<th class="text-right">Cantidad</th>
					<?php } ?>
					<th style="width:200px;" class="text-right">Vendido</th>
				</tr>
				<?php if(count($reporte)==0): ?>
				<tr>
					<th colspan="3" class="text-center">No hay resultados</th>
				</tr>
				<?php endif; ?>
				<?php foreach($reporte as $r): ?>
				<tr>
					<?php if($tipo == 4) { ?>
						<td style="font-size:11px;" title="<?php echo $r->No_Marca . ' ' . $r->No_Producto; ?>"><b><?php echo ($r->No_Marca!=''?$r->No_Marca:'SIN MARCA'); ?></b> - <?php echo $r->No_Producto; ?></td>
					<?php } else if($tipo == 5) { ?>
						<th style="font-size:11px;" title="<?php echo $r->No_Razsocial; ?>" ><?php echo strlen($r->No_Razsocial) > 80 ? $r->No_Razsocial . '..' : $r->No_Razsocial; ?></th>
					<?php } ?>
					<td class="text-right bg-sold"><?php echo numberFormat($r->Qt_Producto, 0, '.', ','); ?><?php if($tipo == 4) echo ' ' . $r->No_Unidad_Medida_Breve; ?></td>
					<?php if($tipo==5) { ?>
						<td class="text-right bg-sold"><?php echo numberFormat($r->Qt_Producto_2, 0, '.', ','); ?></td>
					<?php } ?>
					<td class="text-right bg-sold"><?php echo $r->No_Signo; ?> <?php echo numberFormat($r->Vendido, 2, '.', ','); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php }else if($tipo == 6){ ?>
		<div class="well well-sm text-center">Son los productos más vendidos dentro de un trimestre.</div>
		<table class="table report">
			<thead>
				<tr>
					<th colspan="5"><?php echo $titulo; ?> (no incluye impuestos)</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($reporte as $k => $r1): ?>
					<tr class="sub-legend">
						<th colspan="4"><?php echo $k; ?></th>
						<th class="text-right">
							<?php if($k == '1er Trimestre') echo '[Enero-Marzo]'; ?>
							<?php if($k == '2do Trimestre') echo '[Abril-Junio]'; ?>
							<?php if($k == '3er Trimestre') echo '[Julio-Setiembre]'; ?>
							<?php if($k == '4to Trimestre') echo '[Octubre-Diciembre]'; ?>
						</th>
					</tr>
					<tr>
						<th colspan="3">Descripción</th>
						<th style="width:150px;" class="text-right">Cantidad</th>
						<th style="width:150px;" class="text-right">Vendido</th>
					</tr>
					<?php if(count($r1) == 0): ?>
					<tr>
						<td colspan="5" class="text-center">
							<?php echo date('Y') == $y ? 'Aún no hay suficientes datos para generar un reporte del trimestre actual.' : 'No se han encontrado datos guardados para este trimestre.' ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php foreach($r1 as $r2): ?>
					<tr>
						<td colspan="3" title="<?php echo $r2->No_Marca . ' ' . $r2->No_Producto; ?>"><b><?php echo $r2->No_Marca; ?></b> - <?php echo $r2->No_Producto; ?></td>
						<td class="text-right"><?php echo numberFormat($r2->Qt_Producto, 2, '.', ',') . ' ' . $r2->No_Unidad_Medida_Breve; ?></td>
						<td class="text-right bg-sold"><?php echo $r2->No_Signo . ' ' .  numberFormat($r2->Vendido, 2, '.', ','); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php } ?>
</div>