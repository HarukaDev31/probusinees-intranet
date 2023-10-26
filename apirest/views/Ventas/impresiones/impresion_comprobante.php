<?php
  $ventaDetalle = $venta;
	$venta = $venta[0];
	if($venta->ID_Tipo_Documento === '3'){//Factura
		$format = $this->empresa->Txt_Formato_Factura;
		$No_Foto_Comprobante = $this->empresa->No_Foto_Factura;
	} else if($venta->ID_Tipo_Documento == '4'){//Boleta
		$format = $this->empresa->Txt_Formato_Boleta;
		$No_Foto_Comprobante = $this->empresa->No_Foto_Boleta;
	} else {//NCredito
		$format = $this->empresa->Txt_Formato_NCredito;
		$No_Foto_Comprobante = $this->empresa->No_Foto_NCredito;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Vista Preliminar</title>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    
		<?php echo link_tag('assets/css/print.css'); ?>		
		<?php echo link_tag('assets/css/jquery-ui-1.10.4.custom.min.css'); ?>
		
		<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-1.10.2.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui-1.10.3.custom.min.js'); ?>"></script>
		<script>
			var ID_Documento_Cabecera = <?php echo $venta->ID_Documento_Cabecera; ?>;
			var ID_Tipo_Documento     = <?php echo $venta->ID_Tipo_Documento; ?>;
			var base_url					    = '<?php echo base_url(''); ?>';
			
			$(document).ready(function(){
				$(".absolute").draggable();
				
				$(".row").resizable({
					 resize: function(event, ui) {
					 	ui.size.width = ui.originalSize.width;
					 }
				})
				
				$(".ui-icon-gripsmall-diagonal-s,.ui-icon-gripsmall-diagonal-se").remove();
				
				$("#btnImprimirCancelar").click(function(){
					var f = '';

					f += '#No_Entidad?' + $("#No_Entidad").attr('style') + '|';
					f += '#Txt_Direccion_Entidad?' + $("#Txt_Direccion_Entidad").attr('style') + '|';
					f += '#Nu_Documento_Identidad?' + $("#Nu_Documento_Identidad").attr('style') + '|';
					f += '#Fe_Emision?' + $("#Fe_Emision").attr('style') + '|';
					
					f += '#detalle_productos?' + $("#detalle_productos").attr('style') + '|';
					f += '#detalle_productos .row?';
					
					$('#detalle_productos .row').each(function(){
						f += $(this).attr('style') + '!';
					})
					
					if($('#detalle_productos .row').size() > 0){
						f = f.substring(0,f.length - 1);
					}
					
					$.post(base_url + 'ImprimirComprobanteController/formatoImpresionComprobante',{
						ID_Documento_Cabecera	: ID_Documento_Cabecera,
						ID_Tipo_Documento			: ID_Tipo_Documento,
						f					            : f
					}, function(r){
						Volver();
					}, 'json');
				})

				$("#btnImprimir").click(function(){
					var f = '';

					f += '#No_Entidad?' + $("#No_Entidad").attr('style') + '|';
					f += '#Txt_Direccion_Entidad?' + $("#Txt_Direccion_Entidad").attr('style') + '|';
					f += '#Nu_Documento_Identidad?' + $("#Nu_Documento_Identidad").attr('style') + '|';
					f += '#Fe_Emision?' + $("#Fe_Emision").attr('style') + '|';
					
					f += '#detalle_productos?' + $("#detalle_productos").attr('style') + '|';
					f += '#detalle_productos .row?';
					
					$('#detalle_productos .row').each(function(){
						f += $(this).attr('style') + '!';
					})
					
					if($('#detalle_productos .row').size() > 0){
						f = f.substring(0,f.length - 1);
					}
					
					var button = $(this);
					
					$.post(base_url + 'ImprimirComprobanteController/formatoImpresionComprobante',{
						ID_Documento_Cabecera	: ID_Documento_Cabecera,
						ID_Tipo_Documento			: ID_Tipo_Documento,
						f					            : f
					}, function(r){
						if(r.status === 'success'){
							PrepararHoja();
							window.print();
							alert('La impresión ha sido enviada, lo redireccionaremos a la página anterior.');
							Volver();
						}else
							alert(r.message);
					}, 'json');
				})
				SetearImpresion();
			})
			
			function Volver(){
			  window.location.href = base_url + 'Ventas/VentaController/listarVentas';
			}
			
			function PrepararHoja(){
				$(".hidden").hide();
				$("body, .absolute, .row").css('background', 'none');
				$(".row,#container").css('border', 'none');
			}
			
			function SetearImpresion(){
				var f = '<?php echo $format; ?>'.split('|');
			
				for(var i = 0; i < f.length; i++){
					var data = f[i].split('?');

					if(data[0] != '#detalle_servicios .row'){
						$(data[0]).attr('style', data[1]);
					}else{
						var w = data[1].split('!');
						$('#detalle_servicios .row').each(function(i){
							$(this).attr('style',w[i]);
						})
					}
					
					if(data[0] == '#detalle_productos .row'){
						var y = data[1].split('!');
						$('#detalle_productos .row').each(function(i){
							$(this).attr('style',y[i]);
						})
					}
				}
			}
			
		</script>
		<style type="text/css" media="print">
			.no-print{ display: none; }
			@page{margin: 0;padding:0;}
		</style>
	</head>
	<body>
		<img class="no-print" id="boceto" src="<?php echo base_url('assets/img/formatos_documento/' . $No_Foto_Comprobante); ?>" />
		<div id="botones" class="no-print">
			<button id="btnImprimirCancelar">Cancelar</button>
			<button id="btnImprimir">Imprimir</button>
		</div>
		<div id="container">
			<div class="margin-left margin no-print"></div>
			<div class="margin-right margin no-print"></div>
			<div title="Nombre del Cliente" class="absolute" id="No_Entidad" style="left:120px;top:320px;"><?php echo $venta->No_Entidad; ?></div>
			<div title="Direccion" class="absolute" id="Txt_Direccion_Entidad" style="left:120px;top:340px;"><?php echo $venta->Txt_Direccion_Entidad; ?></div>
			<div title="Numero de documento Identidad" class="absolute" id="Nu_Documento_Identidad" style="left:120px;top:360px;"><?php echo $venta->Nu_Documento_Identidad; ?></div>
			<div title="Fecha de Emisión" class="absolute" id="Fe_Emision" style="left:160px;top:500px;"><?php echo $venta->Fe_Emision; ?></div>
			<div title="Detalle de Producto" id="detalle_productos">
				<div class="absolute row">
					<?php foreach($ventaDetalle as $row):?>
						<div class="text-right"><?php echo number_format($row->Qt_Producto, 2); ?></div>
					<?php endforeach;?>
				</div>
				<div class="absolute row">
					<?php foreach($ventaDetalle as $row):?>
						<div><?php echo $row->No_Producto; ?></div>
					<?php endforeach;?>
				</div>
				<div class="absolute row">
					<?php foreach($ventaDetalle as $row):?>
						<div class="text-right"><?php echo number_format($row->Ss_Precio, 2); ?></div>
					<?php endforeach;?>
				</div>
				<div class="absolute row">
					<?php foreach($ventaDetalle as $row):?>
						<div class="text-right"><?php echo number_format($row->Ss_SubTotal, 2); ?></div>
					<?php endforeach;?>
				</div>
  			<div class="absolute row">
  				<div class="text-left"><?php echo $totalEnLetras; ?></div>
  			</div>
				<div class="absolute row">
					<div class="text-right"><?php echo number_format($venta->Ss_Total, 2); ?></div>
				</div>
			</div>
		</div>
	</body>
</html>