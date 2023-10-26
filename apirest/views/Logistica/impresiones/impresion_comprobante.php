<?php
$ventaDetalle = $venta;
$venta = $venta[0];
$format = $this->empresa->Txt_Formato_Guia;
$No_Foto_Comprobante='';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Vista Preliminar</title>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    
		<?php echo link_tag('assets/css/print.css?ver=1.0'); ?>		
		<?php echo link_tag('assets/css/jquery-ui-1.10.4.custom.min.css'); ?>
		
		<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-1.10.2.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui-1.10.3.custom.min.js'); ?>"></script>
		<script>
			var ID_Documento_Cabecera = <?php echo $venta->ID_Guia_Cabecera; ?>;
			var ID_Tipo_Documento     = <?php echo $venta->ID_Tipo_Documento; ?>;
			var base_url			  = '<?php echo base_url(''); ?>';
			
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
					f += '#Txt_Direccion_Origen?' + $("#Txt_Direccion_Origen").attr('style') + '|';
					f += '#Txt_Direccion_Destino?' + $("#Txt_Direccion_Destino").attr('style') + '|';
					f += '#Nu_Documento_Identidad?' + $("#Nu_Documento_Identidad").attr('style') + '|';
					f += '#Fe_Emision?' + $("#Fe_Emision").attr('style') + '|';
					f += '#Fe_Traslado?' + $("#Fe_Traslado").attr('style') + '|';
					f += '#No_Entidad_Transportista?' + $("#No_Entidad_Transportista").attr('style') + '|';
					f += '#No_Placa?' + $("#No_Placa").attr('style') + '|';
					f += '#No_Licencia?' + $("#No_Licencia").attr('style') + '|';
					f += '#No_Certificado_Inscripcion?' + $("#No_Certificado_Inscripcion").attr('style') + '|';
					f += '#No_Motivo_Traslado_Sunat?' + $("#No_Motivo_Traslado_Sunat").attr('style') + '|';
					
					f += '#detalle_productos?' + $("#detalle_productos").attr('style') + '|';
					f += '#detalle_productos .row?';
					
					$('#detalle_productos .row').each(function(){
						f += $(this).attr('style') + '!';
					})
					
					if($('#detalle_productos .row').size() > 0){
						f = f.substring(0,f.length - 1);
					}
					
					$.post(base_url + 'ImprimirComprobanteController/formatoImpresionComprobante',{
						ID_Documento_Cabecera : ID_Documento_Cabecera,
						ID_Tipo_Documento : ID_Tipo_Documento,
						f : f
					}, function(r){
						Volver();
					}, 'json');
					SetearImpresion();
				})

				$("#btnImprimir").click(function(){
					var f = '';

					f += '#No_Entidad?' + $("#No_Entidad").attr('style') + '|';
					f += '#Txt_Direccion_Origen?' + $("#Txt_Direccion_Origen").attr('style') + '|';
					f += '#Txt_Direccion_Destino?' + $("#Txt_Direccion_Destino").attr('style') + '|';
					f += '#Nu_Documento_Identidad?' + $("#Nu_Documento_Identidad").attr('style') + '|';
					f += '#Fe_Emision?' + $("#Fe_Emision").attr('style') + '|';
					f += '#Fe_Traslado?' + $("#Fe_Traslado").attr('style') + '|';
					f += '#No_Entidad_Transportista?' + $("#No_Entidad_Transportista").attr('style') + '|';
					f += '#No_Placa?' + $("#No_Placa").attr('style') + '|';
					f += '#No_Licencia?' + $("#No_Licencia").attr('style') + '|';
					f += '#No_Certificado_Inscripcion?' + $("#No_Certificado_Inscripcion").attr('style') + '|';
					f += '#No_Motivo_Traslado_Sunat?' + $("#No_Motivo_Traslado_Sunat").attr('style') + '|';
					
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
						ID_Documento_Cabecera : ID_Documento_Cabecera,
						ID_Tipo_Documento : ID_Tipo_Documento,
						f : f
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
			  window.location.href = base_url + 'Logistica/SalidaInventarioController/listarSalidas';
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
			<div title="Direccion" class="absolute" id="Txt_Direccion_Origen" style="left:120px;top:340px;"><?php echo $venta->Txt_Direccion_Origen; ?></div>
			<div title="Referencia de Direccion" class="absolute" id="Txt_Direccion_Destino" style="left:120px;top:340px;"><?php echo $venta->Txt_Direccion_Destino; ?></div>
			<div title="Numero de documento Identidad" class="absolute" id="Nu_Documento_Identidad" style="left:120px;top:360px;"><?php echo $venta->Nu_Documento_Identidad; ?></div>
			<div title="Fecha de Emisión" class="absolute" id="Fe_Emision" style="left:160px;top:500px;"><?php echo ToDateBD($venta->Fe_Emision); ?></div>
			<div title="Fecha de Traslado" class="absolute" id="Fe_Traslado" style="left:160px;top:500px;"><?php echo ToDateBD($venta->Fe_Traslado); ?></div>
			<div title="Chofer" class="absolute" id="No_Entidad_Transportista" style="left:160px;top:500px;"><?php echo $venta->No_Entidad_Transportista; ?></div>
			<div title="Nro. Placa" class="absolute" id="No_Placa" style="left:160px;top:500px;"><?php echo $venta->No_Placa; ?></div>
			<div title="Nro. Licencia" class="absolute" id="No_Licencia" style="left:160px;top:500px;"><?php echo $venta->No_Licencia; ?></div>
			<div title="Certificado de Inscripción" class="absolute" id="No_Certificado_Inscripcion" style="left:160px;top:500px;"><?php echo $venta->No_Certificado_Inscripcion; ?></div>
			<div title="Motivo de Traslado" class="absolute" id="No_Motivo_Traslado_Sunat" style="left:160px;top:500px;"><?php echo $venta->No_Motivo_Traslado_Sunat; ?></div>
			<div title="Detalle de Producto" id="detalle_productos">
				<div class="absolute row">
					<?php foreach($ventaDetalle as $row):?>
						<div class="text-left" title="Nombre Producto" alt="Nombre Producto"><?php echo $row->No_Producto; ?></div>
					<?php endforeach;?>
				</div>
				<div class="absolute row">
					<?php foreach($ventaDetalle as $row):?>
						<div class="text-right" title="Cantidad" alt="Cantidad"><?php echo number_format($row->Qt_Producto, 2); ?></div>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</body>
</html>