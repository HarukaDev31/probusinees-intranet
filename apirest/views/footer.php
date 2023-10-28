  <footer class="main-footer" style="background-color: #1a2226;">
    <div class="pull-right hidden-xs">
      <b style="color: white">Version</b> <span style="color: white"><?php echo NUEVA_VERSION_SISTEMA; ?></span>
    </div>
    <strong><a href="https://www.ecxpresslae.com" target="_blank" alt="ecxpresslae" title="ecxpresslae"><span style="color: #EC4747 !important">ProBusiness</span></a> <span style="color: white">&copy; 2023</strong>
  </footer>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<?php $iControlVersionDashboard = '2.61.0'; ?>
<!-- jQuery 3 -->
<script src="<?php echo base_url() . 'bower_components/jquery/dist/jquery.min.js'; ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url() . 'bower_components/jquery-ui/jquery-ui.min.js'; ?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<!-- jQuery Validate -->
<script src="<?php echo base_url() . 'assets/js/jquery.validate.min.js'; ?>"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url() . 'bower_components/bootstrap/dist/js/bootstrap.min.js'; ?>"></script>
<!-- InputMask -->
<script src="<?php echo base_url() . 'plugins/input-mask/jquery.inputmask.js'; ?>"></script>
<script src="<?php echo base_url() . 'plugins/input-mask/jquery.inputmask.date.extensions.js'; ?>"></script>
<script src="<?php echo base_url() . 'plugins/input-mask/jquery.inputmask.extensions.js'; ?>"></script>
<!-- DataTables -->
<script src="<?php echo base_url() . 'bower_components/datatables.net/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'; ?>"></script>
<!-- Select2 -->
<script src="<?php echo base_url() . 'bower_components/select2/dist/js/select2.full.min.js'; ?>"></script>
<!-- daterangepicker -->
<script src="<?php echo base_url() . 'bower_components/moment/min/moment.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'bower_components/bootstrap-daterangepicker/daterangepicker.js'; ?>"></script>
<!-- datepicker -->
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url() . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'; ?>"></script>
<!-- iCheck 1.0.1 -->
<script src="<?php echo base_url() . 'plugins/iCheck/icheck.min.js'; ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url() . 'dist/js/adminlte.min.js'; ?>"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo base_url() . 'dist/js/pages/dashboard.js?ver=1.0'; ?>"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url() . 'dist/js/demo.js?ver=1.0.0'; ?>"></script>
<!-- Datatable Export Files -->
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/dataTables.buttons.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/buttons.bootstrap.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/jszip.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/pdfmake.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/vfs_fonts.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/buttons.html5.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/buttons.print.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/datatables/buttons.colVis.min.js'; ?>"></script>
<!-- Combinaciones de teclado con JQUERY -->
<script src="<?php echo base_url() . 'assets/js/jquery.hotkeys.js'; ?>"></script>
<script type="text/javascript">
var base_url = '<?php echo base_url(); ?>';
</script>
<!-- QRCODE -->
<script src="<?php echo base_url() . 'assets/js/qrcode.min.js?ver=1.0'; ?>"></script>
<!-- Init -->
<script src="<?php echo base_url() . 'dist/js/init.js?ver=' . $iControlVersionDashboard; ?>"></script>
<!-- laesystems -->
<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<script src="<?php echo base_url() . 'assets/dropzone/js/dropzone.min.js'; ?>"></script>
<!-- Inicio -->
<?php if (isset($js_inicio) && $js_inicio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Configuracion -->
<?php if (isset($js_sistema_formato_ordenes) && $js_sistema_formato_ordenes==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/sistema.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_empresa) && $js_empresa==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/empresa.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_monitoreo_empresas) && $js_monitoreo_empresas==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/monitoreo_empresas.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_organizacion) && $js_organizacion==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/organizacion.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_moneda) && $js_moneda==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/moneda.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_medio_pago) && $js_medio_pago==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/medio_pago.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_tipo_medio_pago) && $js_tipo_medio_pago==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/tipo_medio_pago.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_tipo_operacion_caja) && $js_tipo_operacion_caja==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/tipo_operacion_caja.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pais) && $js_pais==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/pais.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_departamento) && $js_departamento==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/departamento.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_provincia) && $js_provincia==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/provincia.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_distrito) && $js_distrito==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/distrito.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_impuesto) && $js_impuesto==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/impuesto.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_valor_impuesto) && $js_valor_impuesto==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/valor_impuesto.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_tipo_documento) && $js_tipo_documento==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/tipo_documento.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_backup) && $js_backup==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/backup.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_medio_pago_marketplace) && $js_medio_pago_marketplace==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/medio_pago_marketplace.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_tipo_medio_pago_marketplace) && $js_tipo_medio_pago_marketplace==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/tipo_medio_pago_marketplace.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Personal -->
<?php if (isset($js_empleado) && $js_empleado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Personal/empleado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_delivery) && $js_delivery==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Personal/delivery.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_matricular_empleado) && $js_matricular_empleado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Personal/matricular_empleado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_transporte_delivery) && $js_transporte_delivery==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Personal/transporte_delivery.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Logistica -->
<?php if (isset($js_importacion_stock_inicial) && $js_importacion_stock_inicial==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/importacion_stock_inicial.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_orden_compra) && $js_orden_compra==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/orden_compra.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_compra) && $js_compra==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/compra.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ingreso_inventario) && $js_ingreso_inventario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ingreso_inventario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_salida_inventario) && $js_salida_inventario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/salida_inventario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_guia_entrada) && $js_guia_entrada==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/guia_entrada.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_guia_salida) && $js_guia_salida==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/guia_salida.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ajuste_inventario) && $js_ajuste_inventario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ajuste_inventario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Logistica -> Informes de Logistica -->
<?php if (isset($js_detalle_guia) && $js_detalle_guia==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/detalle_guia.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_stock_valorizado) && $js_stock_valorizado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/stock_valorizado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_stock_x_empresa) && $js_stock_x_empresa==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/stock_x_empresa.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_consistencia_stock) && $js_consistencia_stock==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/consistencia_stock.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_compras_x_proveedor) && $js_compras_x_proveedor==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/compras_x_proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_saldo_proveedor) && $js_saldo_proveedor==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/saldo_proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_reporte_forma_pago_proveedor) && $js_reporte_forma_pago_proveedor==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/reporte_forma_pago_proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_compras_detalladas_generales) && $js_compras_detalladas_generales==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/informes_logistica/compras_detalladas_generales.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Logistica -> Reglas de Logistica -->
<?php if (isset($js_tipo_movimiento) && $js_tipo_movimiento==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/tipo_movimiento.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_almacen) && $js_almacen==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/almacen.js?ver=' . $iControlVersionDashboard; ?>"></script>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDVDLWnTGZSTRV6idKVdWiFg1I5wX1iVo4&libraries=places"></script>-->
<?php endif; ?>
<?php if (isset($js_ubicacion_inventario) && $js_ubicacion_inventario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/ubicacion_inventario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_unidad_medida) && $js_unidad_medida==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/unidad_medida.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_categoria) && $js_categoria==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/categoria.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_linea) && $js_linea==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/linea.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_marca) && $js_marca==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/marca.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_laboratorio) && $js_laboratorio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/laboratorio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_composicion) && $js_composicion==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/composicion.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_variante) && $js_variante==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/variante.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_producto) && $js_producto==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/producto.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_proveedor) && $js_proveedor==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_series_x_producto) && $js_series_x_producto==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Logistica/ReglasLogistica/series_x_producto.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Ventas -> Reglas de Ventas -->
<?php if (isset($js_cliente) && $js_cliente==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/ReglasVentas/cliente.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_lista_precio) && $js_lista_precio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/ReglasVentas/lista_precio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Ventas -->
<?php if (isset($js_orden_venta) && $js_orden_venta==true) : ?>
<!-- CKEditor -->
<script src="<?php echo base_url() . 'bower_components/ckeditor/ckeditor.js'; ?>"></script>
<script src="<?php echo base_url() . 'dist/js/Ventas/orden_venta.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_orden_seguimiento) && $js_orden_seguimiento==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/orden_seguimiento.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_factura_venta_lae) && $js_factura_venta_lae==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/factura_venta_lae.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pos) && $js_pos==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/pos.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_serie) && $js_serie==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/serie.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_monitoreo_documentos_electronicos) && $js_monitoreo_documentos_electronicos==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/monitoreo_documentos_electronicos.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_venta) && $js_venta==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/venta.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pedidos) && $js_pedidos==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/pedidos.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pedidos_marketplace) && $js_pedidos_marketplace==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/pedidos_marketplace.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_estado_lavado) && $js_estado_lavado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/estado_lavado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_lavado_seco) && $js_lavado_seco==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/lavado_seco.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_prelavado) && $js_prelavado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/prelavado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_secado) && $js_secado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/secado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_planchado) && $js_planchado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/planchado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_doblado) && $js_doblado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/doblado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_embolsado) && $js_embolsado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/embolsado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_servicio_tercerizado) && $js_servicio_tercerizado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/servicio_tercerizado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_lavanderia_externas) && $js_lavanderia_externas==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/lavanderia_externas.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_aviso_pedido_transporte) && $js_aviso_pedido_transporte==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/aviso_pedido_transporte.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Ventas -> Informes de Ventas -->
<?php if (isset($js_general_varios) && $js_general_varios==true) : ?>
<script src="<?php echo base_url() . 'assets/js/Chart.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/general_varios.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_venta_x_tipo_documento_sunat) && $js_venta_x_tipo_documento_sunat==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/venta_x_tipo_documento_sunat.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ventas_x_cliente) && $js_ventas_x_cliente==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/ventas_x_cliente.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ventas_x_trabajador) && $js_ventas_x_trabajador==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/ventas_x_trabajador.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ventas_detalladas_generales) && $js_ventas_detalladas_generales==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/ventas_detalladas_generales.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ventas_x_familia) && $js_ventas_x_familia==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/ventas_x_familia.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_saldo_cliente) && $js_saldo_cliente==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/saldo_cliente.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_reporte_forma_pago) && $js_reporte_forma_pago==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/reporte_forma_pago.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_reporte_utilidad_bruta) && $js_reporte_utilidad_bruta==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/reporte_utilidad_bruta.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ventas_x_delivery) && $js_ventas_x_delivery==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/ventas_x_delivery.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ventas_x_marca) && $js_ventas_x_marca==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/ventas_x_marca.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_ventas_x_usuario) && $js_ventas_x_usuario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Ventas/informes_venta/ventas_x_usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Punto de Venta -->
<?php if (isset($js_apertura_caja) && $js_apertura_caja==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/apertura_caja.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pos_farmacia) && $js_pos_farmacia==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/pos_farmacia.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pos) && $js_pos==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/pos.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_escenarios_restaurante) && $js_escenarios_restaurante==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/escenarios_restaurante.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pos_restaurante) && $js_pos_restaurante==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/pos_restaurante.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pos_lavanderia) && $js_pos_lavanderia==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/pos_lavanderia.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_movimiento_caja) && $js_movimiento_caja==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/movimiento_caja.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_venta_punto_venta) && $js_venta_punto_venta==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/venta_punto_venta.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_despacho_pos) && $js_despacho_pos==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/despacho_pos.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_estado_cuenta_corriente_cliente) && $js_estado_cuenta_corriente_cliente==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/estado_cuenta_corriente_cliente.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_cierre_caja) && $js_cierre_caja==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/cierre_caja.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Informes de Caja -->
<?php if (isset($js_liquidacion_caja) && $js_liquidacion_caja==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/informes_caja/liquidacion_caja.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_informe_movimiento_caja) && $js_informe_movimiento_caja==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PuntoVenta/informes_caja/movimiento_caja.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Contabilidad -->
<?php if (isset($js_tasa_cambio) && $js_tasa_cambio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/LibrosPLE/tasa_cambio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_registro_venta_ingreso) && $js_registro_venta_ingreso==true) : ?>
<script src="<?php echo base_url() . 'dist/js/LibrosPLE/registro_venta_ingreso.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_registro_compras) && $js_registro_compras==true) : ?>
<script src="<?php echo base_url() . 'dist/js/LibrosPLE/registro_compra.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_kardex) && $js_kardex==true) : ?>
<script src="<?php echo base_url() . 'dist/js/LibrosPLE/kardex.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_kardex_valorizado) && $js_kardex_valorizado==true) : ?>
<script src="<?php echo base_url() . 'dist/js/LibrosPLE/kardex_valorizado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_kardex_textil) && $js_kardex_textil==true) : ?>
<script src="<?php echo base_url() . 'dist/js/LibrosPLE/kardex_textil.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Páginas ecommerce -->
<?php if (isset($js_pagina_inicio) && $js_pagina_inicio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Paginas/inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Blog -->
<!-- Blog Slide - Inicio -->
<?php if (isset($js_blog_inicio) && $js_blog_inicio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Blog/blog_inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Blog Publicar Post -->
<?php if (isset($js_blog_post) && $js_blog_post==true) : ?>
<!-- CKEditor -->
<script src="<?php echo base_url() . 'bower_components/ckeditor/ckeditor.js'; ?>"></script>
<script src="<?php echo base_url() . 'dist/js/Blog/blog_post.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Blog Historial de Usuario -->
<?php if (isset($js_blog_historial_usuario) && $js_blog_historial_usuario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Blog/blog_historial_usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<!-- Control de Accesos -->
<?php if (isset($js_perfil_usuario) && $js_perfil_usuario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PanelAcceso/perfil_usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_usuario) && $js_usuario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PanelAcceso/usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_permiso_usuario) && $js_permiso_usuario==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PanelAcceso/permiso_usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_link_referido) && $js_link_referido==true) : ?>
<script src="<?php echo base_url() . 'dist/js/PanelAcceso/link_referido.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- Escuela -->
<?php if (isset($js_escuela_sede) && $js_escuela_sede==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Escuela/escuela_sede.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_escuela_salon) && $js_escuela_salon==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Escuela/escuela_salon.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_escuela_horario_clase) && $js_escuela_horario_clase==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Escuela/escuela_horario_clase.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_escuela_profesor) && $js_escuela_profesor==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Escuela/escuela_profesor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_escuela_matricula) && $js_escuela_matricula==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Escuela/escuela_matricula.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_escuela_asistencia_alumno) && $js_escuela_asistencia_alumno==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Escuela/escuela_asistencia_alumno.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_escuela_reporte_matricula_alumno) && $js_escuela_reporte_matricula_alumno==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Escuela/reporte_matricula_alumno.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- TIENDA VIRTUAL LAE SHOP -->
<?php if (isset($js_tienda_virtual_inicio) && $js_tienda_virtual_inicio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_producto_tienda_virtual) && $js_producto_tienda_virtual==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>
<!--
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
-->
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/producto.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_marca_tienda_virtual) && $js_marca_tienda_virtual==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/marca.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_categoria_tienda_virtual) && $js_categoria_tienda_virtual==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/categoria.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_subcategoria_tienda_virtual) && $js_subcategoria_tienda_virtual==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/subcategoria.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_configuracion_tienda_virtual) && $js_configuracion_tienda_virtual==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>
<!--
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
-->
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/Configuracion/sistema.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_slider_tienda_virtual) && $js_slider_tienda_virtual==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/Configuracion/slider.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_redes_sociales_tienda_virtual) && $js_redes_sociales_tienda_virtual==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/Configuracion/redes_sociales.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_metodo_pago_tienda_virtual) && $js_metodo_pago_tienda_virtual==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/Configuracion/metodo_pago.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_metodo_entrega_tienda_virtual) && $js_metodo_entrega_tienda_virtual==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/Configuracion/metodo_entrega.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pedidos_tienda_virtual) && $js_pedidos_tienda_virtual==true) : ?>
<!--
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
-->
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>

<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/pedido.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_cupon_descuento) && $js_cupon_descuento==true) : ?>
<script src="<?php echo base_url() . 'dist/js/TiendaVirtual/Configuracion/cupon_descuento.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- TIENDA VIRTUAL LAE SHOP -->
<?php if (isset($js_dropshipping_inicio) && $js_dropshipping_inicio==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Dropshipping/inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_pedidos_dropshipping) && $js_pedidos_dropshipping==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Dropshipping/pedido.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_productos_dropshipping) && $js_productos_dropshipping==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Dropshipping/producto.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_productos_proveedores_dropshipping) && $js_productos_proveedores_dropshipping==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Dropshipping/producto_proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_sistema_dropshipping) && $js_sistema_dropshipping==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Dropshipping/sistema.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_billetera_dropshipping) && $js_billetera_dropshipping==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Dropshipping/billetera.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_productos_proveedores_tienda_virtual) && $js_productos_proveedores_tienda_virtual==true) : ?>
<!--
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
-->

<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>

<script src="<?php echo base_url() . 'dist/js/Proveedores/producto_proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_billetera) && $js_billetera==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Billetera/billetera.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_academiax) && $js_academiax==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Academia/academiax.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_callcenter) && $js_callcenter==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>
<!--
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
-->
<script src="<?php echo base_url() . 'dist/js/callcenter.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_coordinado) && $js_coordinado==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>
<!--
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
-->
<script src="<?php echo base_url() . 'dist/js/coordinado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_delivery_dropshipping) && $js_delivery_dropshipping==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>
<!--
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
-->
<script src="<?php echo base_url() . 'dist/js/delivery_dropshipping.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_pedido_proveedor) && $js_pedido_proveedor==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>

<script src="<?php echo base_url() . 'dist/js/Proveedores/pedido_proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- Folder: Logistica -->
<?php if (isset($js_producto_importacion) && $js_producto_importacion==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/Logistica/ReglasLogistica/producto_importacion.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- Ticket Modal -->
<div class="modal fade modal_ticket" id="modal-default">
  <div class="modal-dialog" id="div-formato_ticket">
    <div class="modal-content">
      <div class="modal-body" id="div-ticket">
        <?php if($this->empresa->Nu_MultiAlmacen==0) { ?>
          <img id="img-logo_punto_venta" src="<?php echo $this->empresa->No_Imagen_Logo_Empresa; ?>" style="width:<?php echo $this->empresa->Nu_Width_Logo_Ticket; ?>px; height:<?php echo $this->empresa->Nu_Height_Logo_Ticket; ?>px; margin-left: auto; margin-right: auto; display: block;" />
          <img id="img-logo_punto_venta_click" style="width: <?php echo $this->empresa->Nu_Width_Logo_Ticket; ?>px; height:<?php echo $this->empresa->Nu_Height_Logo_Ticket; ?>px; margin-left: auto; margin-right: auto; display: block;" />
        <?php } else if($this->empresa->Nu_MultiAlmacen==1) { ?>
          <img id="img-logo_punto_venta" src="<?php echo $this->empresa->No_Logo_Url_Almacen; ?>" style="width:<?php echo $this->empresa->Nu_Width_Logo_Ticket; ?>px; height:<?php echo $this->empresa->Nu_Height_Logo_Ticket; ?>px; margin-left: auto; margin-right: auto; display: block;" />
          <img id="img-logo_punto_venta_click" style="width: <?php echo $this->empresa->Nu_Width_Logo_Ticket; ?>px; height:<?php echo $this->empresa->Nu_Height_Logo_Ticket; ?>px; margin-left: auto; margin-right: auto; display: block;" />
        <?php } ?>
        <p id="modal-body-p-title" style="text-align: center; align-content: center; font-size: 12px; font-family: Arial, Helvetica, sans-serif;"></p>
        <table id="table-modal_ticket" class="table table-hover table-demo"></table>
        <div id="div-codigo_qr" style="width: 64px; height: 64px; margin-left: auto; margin-right: auto; display: block;"></div>
        <p id="modal-body-p-terminos_condiciones_ticket" style="text-align: center; align-content: center; font-size: 12px; font-family: Arial, Helvetica, sans-serif;"></p>
        <br>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.Ticket modal -->
<!-- Ticket Comanda Lavado Modal -->
<div class="modal fade modal-ticket_comanda_lavado" id="modal-default">
  <div class="modal-dialog" id="div-ticket_comanda_lavado">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-div-ticket_comanda_lavado">
        <img id="img-logo_punto_venta_lavado" src="../../../assets/images/logos/<?php echo $this->empresa->No_Logo_Empresa; ?>" style="width: <?php echo $this->empresa->Nu_Width_Logo_Ticket; ?>px; height:<?php echo $this->empresa->Nu_Height_Logo_Ticket; ?>px; margin-left: auto; margin-right: auto; display: block;" />
        <img id="img-logo_punto_venta_click_lavado" style="width: <?php echo $this->empresa->Nu_Width_Logo_Ticket; ?>px; height:<?php echo $this->empresa->Nu_Height_Logo_Ticket; ?>px; margin-left: auto; margin-right: auto; display: block;" />
        <p id="modal-body-p-title_numero" style="text-align: center; align-content: center; font-size: 60px;"></p>
        <p id="modal-body-p-title_tipo_envio_lavado" style="text-align: center; align-content: center; font-size: 20px; margin-top: -25px !important;"></p>
        <table id="modal-table-ticket_comanda_lavado" class="table table-hover"></table>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.Ticket Comanda Lavado modal -->
<!-- modal Liquidacion de caja -->
<div class="modal fade modal-liquidacion_caja" id="modal-default">
  <div class="modal-dialog" id="div-formato_liquidacion_caja">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-div-liquidacion_caja">
        <h4 class="text-center" id="modal-h4-liquidacion_caja">LIQUIDACIÓN DE CAJA</h4>
        <table id="modal-table-ventas_x_familia" class="table table-hover"></table>
        <table id="modal-table-movimientos_caja" class="table table-hover"></table>
        <table id="modal-table-ventas_generales" class="table table-hover"></table>
        <table id="modal-table-ventas_x_descuento" class="table table-hover"></table>
        <table id="modal-table-ventas_x_gratuita_regalo" class="table table-hover"></table>
        <table id="modal-table-ventas_totales" class="table table-hover"></table>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal Liquidacion de caja -->

<!-- Ver Detalle Modal -->
<div class="modal fade modal_detalle" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="table-responsive">
          <table id="table-modal_detalle" class="table table-striped table-bordered"></table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-salir" class="btn btn-danger btn-block pull-center" data-dismiss="modal"><span class="fa fa-sign-out"></span> Salir</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Message Modal -->
<div class="modal modal-message fade" id="modal-message">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title-message text-center"></h4>
    </div>
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Message Delete Modal -->
<div class="modal modal-danger modal-message-delete fade" id="modal-message-delete">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title-message-delete text-center">¿Deseas eliminar?</h4>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" id="btn-cancel-delete" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
      <button type="button" id="btn-save-delete" class="btn btn-outline">Aceptar</button>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.message delete modal -->

<!-- Message Repetir Mensualmente Modal -->
<div class="modal modal-default modal-message-repetir_mensualmente fade" id="modal-message-repetir_mensualmente">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title-message-repetir_mensualmente text-center">Configurar Repetición</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <label>Tiempo <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <select id="cbo-modal-tipo_tiempo_repetir" name="ID_Tipo_Tiempo_Repetir" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <div class="col-xs-12 div-modal-repetir_mensualmente-mes">
            <label>Mes <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <select id="cbo-modal-repetir_mensualmente-mes" class="form-control select2" style="width: 100%;"></select>
            </div>
          </div>
          <div class="col-xs-12">
            <label>Día <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <select id="cbo-modal-repetir_mensualmente-dia" class="form-control select2" style="width: 100%;"></select>
            </div>
          </div>
        </div>
      </div>
            
      <div class="modal-footer">
        <div class="col-xs-6">
          <button type="button" id="btn-cancel-repetir_mensualmente" class="btn btn-danger btn-block pull-center" data-dismiss="modal">Cancelar</button>
        </div>
        <div class="col-xs-6">
          <button type="button" id="btn-save-repetir_mensualmente" class="btn btn-success btn-block pull-center btn-generar_pedido" data-type="generar_ticket">Guardar</button>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.message Repetir Mensualmente modal -->

<!-- Loader Modal -->
<div class="modal modal-default fade" id="modal-loader">
  <div class="modal-dialog" id="modal-loader-change">
    <div class="modal-header modal-header-change ">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title-loader"></h4>
    </div>
    <div class="modal-content modal-content-loader-change">
      <p></p>
    </div>
    <div class="modal-footer">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="loader"><div class="ball-triangle-path"><div></div><div></div><div></div></div></div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer"></div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.message Loader modal -->
<!-- Importar Productos -->
<div class="modal fade modal_importar_producto" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>Logistica/ReglasLogistica/ProductoController/importarExcelProductos" enctype="multipart/form-data">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Productos</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>&nbsp;
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>- La plantilla que se debe utilizar es la siguiente, dar clic en el siguiente botón
                <br>&nbsp;
                <a id="a-download-product" href="<?php echo base_url(); ?>DownloadController/download/Laesystems_Plantilla_Productos.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>
              
    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector">
                    <input type="file" id="my-file-selector" name="excel-archivo_producto" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info').html(this.files[0].name)">Buscar...
                  </label>
                  <span class='label label-info' id="upload-file-info"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="button" id="btn-cancel-product" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Cancelar</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_producto" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Subir excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.modal Importar productos -->
<!-- Importar Proveedores -->
<div class="modal fade modal_importar_proveedor" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>Logistica/ReglasLogistica/ProveedorController/importarExcelProveedor" enctype="multipart/form-data">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Proveedores</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>&nbsp;
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>- La plantilla que se debe utilizar es la siguiente, dar clic en el siguiente botón
                <br>&nbsp;
                <a id="a-download-provider" href="<?php echo base_url(); ?>DownloadController/download/Laesystems_Plantilla_Proveedores.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>
              
    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector_proveedor">
                    <input type="file" id="my-file-selector_proveedor" name="excel-archivo_proveedor" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info_proveedor').html(this.files[0].name)">Buscar...
                  </label>
                  <span class='label label-info' id="upload-file-info_proveedor"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="button" id="btn-cancel-provider" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Cancelar</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_proveedor" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Subir excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.modal Importar Proveedores -->
<!-- Importar Clientes -->
<div class="modal fade modal_importar_cliente" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>Ventas/ReglasVenta/ClienteController/importarExcelCliente" enctype="multipart/form-data">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Clientes</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>&nbsp;
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>- La plantilla que se debe utilizar es la siguiente, dar clic en el siguiente botón
                <br>&nbsp;
                <a id="a-download-client" href="<?php echo base_url(); ?>DownloadController/download/Laesystems_Plantilla_Clientes.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>

    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector_cliente">
                    <input type="file" id="my-file-selector_cliente" name="excel-archivo_cliente" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info_cliente').html(this.files[0].name)">Buscar...
                  </label>
                  <span class='label label-info' id="upload-file-info_cliente"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="button" id="btn-cancel-client" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Cancelar</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_cliente" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Subir excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.modal Importar Clientes -->
<!-- Importar Lista de Precios -->
<div class="modal fade modal_importar_lista_precio" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>Ventas/ReglasVenta/Lista_precio_controller/importarExcelListaPrecios" enctype="multipart/form-data">
          <input type="hidden" name="modal-ID_Lista_Precio_Cabecera" class="form-control">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Lista de Precios</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>&nbsp;
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>- Clic en el botón "Descargar Plantilla" y luego subir archivo
                <br>&nbsp;
                <a id="a-download-list_price" href="<?php echo base_url(); ?>DownloadController/download/Laesystems_Plantilla_Lista_Precios.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>
              
    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector_lista_precio">
                    <input type="file" id="my-file-selector_lista_precio" name="excel-archivo_lista_precio" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info_lista_precio').html(this.files[0].name)">Buscar...
                  </label>
                  <span class='label label-info' id="upload-file-info_lista_precio"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="button" id="btn-cancel-list_price" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Cancelar</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_lista_precio" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Subir excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.modal Importar Lista de Precios -->
<!-- Orden Venta Modal -->
<div class="modal fade modal-orden" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <input type="hidden" class="hidden-modal_orden" value="" autocomplete="off">
      <div class="modal-body" id="div-modal-body-orden">
      </div>
      <div class="modal-footer">
        <div class="col-xs-6">
          <button type="button" id="btn-modal-salir-orden" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Cancelar</button>
        </div>
        <div class="col-xs-6">
          <button type="button" id="btn-modal-facturar-orden" class="btn btn-primary btn-lg btn-block pull-center">Generar Venta</button>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.Orden Venta modal -->
<!-- Message Enviar Correo COTIZACION Modal -->
<div class="modal modal-default fade" id="modal-orden_correo_sunat">
<div class="modal-dialog modal-sm">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="text-center" id="modal-header-orden-title"></h4>
    </div>
    <div class="modal-body">
	    <div class="col-md-12">
        <label>De</label>
        <div class="form-group">
          <input type="email" id="txt-orden-email_correo_sunat_de" placeholder="Ingresar correo" class="form-control" autocomplete="on">
          <span class="hide form-group help-block" id="span-email" style="color: #dd4b39;">Ingresa un email válido</span>
        </div>
      </div>
	    <div class="col-md-12">
        <label>Para</label>
        <div class="form-group">
          <input type="email" id="txt-orden-email_correo_sunat_para" placeholder="Ingresar correo" class="form-control" autocomplete="on">
          <span class="hide form-group help-block" id="span-email" style="color: #dd4b39;">Ingresa un email válido</span>
        </div>
      </div>
	    <div class="col-md-12">
        <label>Asunto</label>
        <div class="form-group">
          <input type="email" id="txt-orden-email_correo_sunat_asunto" placeholder="Ingresar asunto" class="form-control" autocomplete="on">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <div class="col-xs-6">
        <button type="button" id="btn-modal-footer-orden_correo_sunat-cancel" class="btn btn-danger btn-md btn-block pull-left" data-dismiss="modal">Cancelar</button>
      </div>
      <div class="col-xs-6">
        <button type="button" id="btn-modal-footer-orden_correo_sunat-send" class="btn btn-success btn-md btn-block">Enviar</button>
      </div>
    </div>
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.message Enviar Correo COTIZACION modal -->
<!-- Message Enviar Correo SUNAT Modal -->
<div class="modal modal-default fade" id="modal-correo_sunat">
<div class="modal-dialog modal-sm">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-header_message_correo_sunat text-center"></h4>
      <h6 class="text-left">Puedes registrar el número en Ventas > Reglas de Ventas > Clientes <a class="btn btn-link" target="_blank" style="color: #FFFFFF" href="<?php echo base_url() . 'Ventas/ReglasVenta/ClienteController/listarClientes'; ?>">(Ver listado)</a></h6>
    </div>
    <div class="modal-body">
	    <div class="col-md-12">
        <div class="form-group">
          <input type="email" id="txt-email_correo_sunat" placeholder="Ingresar correo" class="form-control" autocomplete="on">
          <span class="hide form-group help-block" id="span-email" style="color: #dd4b39;">Ingresa un email válido</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" id="btn-modal_message_correo_sunat-cancel" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
      <button type="button" id="btn-modal_message_correo_sunat-send" class="btn btn-outline">Enviar</button>
    </div>
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.message Enviar Correo SUNAT modal -->

<!-- Message Enviar WhatsApp Modal -->
<div class="modal modal-default fade" id="modal-whatsApp">
<div class="modal-dialog modal-sm">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-header_message_whatsApp text-center"></h4>
      <h6 class="text-left">Puedes registrar el número en Ventas > Reglas de Ventas > Clientes <a class="btn btn-link" target="_blank" style="color: #FFFFFF" href="<?php echo base_url() . 'Ventas/ReglasVenta/ClienteController/listarClientes'; ?>">(Ver listado)</a></h6>
    </div>
    <div class="modal-body">
	    <div class="col-md-12">
        <div class="form-group">
          <input type="tel" id="txt-Nu_Celular_Entidad_Cliente_WhatsApp" name="Nu_Celular_Entidad" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="on" placeholder="opcional">
          <span class="hide" id="span-celular" style="color: #dd4b39;">Ingresa un celular válido</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" id="btn-modal_message_whatsApp-cancel" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
      <button type="button" id="btn-modal_message_whatsApp-send" class="btn btn-outline">Enviar</button>
    </div>
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.message Enviar WhatsApp SUNAT modal -->

<!-- Message configurar datos AUTOMATICAMENTE Modal -->
<div class="modal modal-default fade" id="modal-configuracion_automatica">
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-header-title-configuracion_automatica text-center"></h4>
    </div>
    <div class="modal-body">
      <div class="col-md-4">
        <label title="Rubro empresa">Rubro <span class="label-advertencia">*</span></label>
        <div class="form-group">
          <select id="cbo-tipo_rubro_empresa_automatico" name="Nu_Tipo_Rubro_Empresa" title="Rubro Empresa" class="form-control select2 required" style="width: 100%;"></select>
          <span class="help-block" id="error"></span>
        </div>
      </div>

      <div class="col-md-6">
        <label>Nombre(s) y Apellidos</label>
        <div class="form-group">
          <input type="text" id="txt-nombres_apellidos_automatico" name="No_Nombres_Apellidos" placeholder="Ingresar nombre(s) y apellidos" class="form-control" autocomplete="off" maxlength="100">
          <span class="help-block" id="error"></span>
        </div>
      </div>

      <div class="col-md-2">
        <label>Celular</label>
        <div class="form-group">
          <input type="tel" id="txt-celular_automatico" name="Nu_Celular" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
          <span class="help-block" id="error"></span>
        </div>
      </div>

	    <div class="col-md-5">
        <label>Correo</label>
        <div class="form-group">
          <input type="email" id="txt-email_automatico" placeholder="Ingresar correo" class="form-control" autocomplete="on">
          <span class="hide form-group help-block" id="div-email" style="color: #dd4b39;">Ingresa un email válido</span>
        </div>
      </div>

	    <div class="col-md-4">
        <label>Contraseña</label>
        <div class="form-group">
          <input type="text" id="txt-password_automatico" placeholder="Ingresar contraseña" class="form-control" autocomplete="off">
          <span class="help-block" id="error"></span>
        </div>
      </div>

	    <div class="col-md-3">
        <label data-toggle="tooltip" data-placement="bottom" title="Importe de pago de nuestro servicio para el cliente">Pago Cliente</label>
        <div class="form-group">
          <input type="text" id="txt-pago_cliente" placeholder="Ingresar importe" class="form-control input-decimal required" autocomplete="off" data-toggle="tooltip" data-placement="bottom" title="Importe de pago de nuestro servicio para el cliente">
          <span class="help-block" id="error"></span>
        </div>
      </div>
      
      <div class="col-md-12 div-fe">
        <label>URL FE</label>
        <div class="form-group">
          <input type="text" id="txt-url_fe_automatico" name="Txt_Fe_Ruta" placeholder="Ingresar url fe" class="form-control" autocomplete="off" maxlength="100">
          <span class="help-block" id="error"></span>
        </div>
      </div>
      
      <div class="col-md-12 div-fe token_fe_automatico">
        <label>Token FE</label>
        <div class="form-group">
          <input type="text" id="txt-token_fe_automatico" name="Txt_Fe_Token" placeholder="Ingresar token fe" class="form-control" autocomplete="off" maxlength="100">
          <span class="help-block" id="error"></span>
        </div>
      </div>
      
      <div class="col-md-12 hidden"><!-- div-generar-token_lae_fe -->
        <label>Acceso lae FE</label>
        <div class="form-group">
          <a href="https://laesystems.com/librerias/RegisterController" target="_blank" class="btn btn-primary btn-block" role="button">Generar token</a>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <div class="col-xs-6">
        <button type="button" id="btn-modal-configuracion_automatica-cancel" class="btn btn-danger btn-md btn-block pull-left" data-dismiss="modal">Cancelar</button>
      </div>
      <div class="col-xs-6">
        <button type="button" id="btn-modal-configuracion_automatica-send" class="btn btn-success btn-md btn-block">Enviar</button>
      </div>
    </div>
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.message configurar datos AUTOMATICAMENTE modal -->
<!-- Crear item Modal -->
<div class="modal fade modal-default" id="modal-crearItem">
  <div class="modal-dialog">
    <div class="modal-header" style="background-color: #fff;">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 id="modal-header-crearItem" class="text-center"></h4>
    </div>
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-6 col-sm-3">
            <label>Grupo <span class="label-advertencia">*</span></label>
            <div class="form-group div-modal-grupoItem">
    				  <select id="cbo-modal-grupoItem" class="form-control">
    				    <option value="1">Producto</option>
    				    <option value="0">Servicio</option>
    				  </select>
    				</div>
          </div>
          
          <div class="col-xs-6 col-sm-4 hidden"><!-- div-Producto -->
            <div class="form-group">
              <label>Tipo Producto</label>
              <select id="cbo-modal-tipoItem" class="form-control"></select>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-5">
            <label>Código barra <span class="label-advertencia">*</span></label>
            <div class="form-group">
    				  <input type="text" id="txt-modal-upcItem" class="form-control input-codigo_barra input-Mayuscula" placeholder="Ingresar código" maxlength="20" autocomplete="off">
    				</div>
          </div>
          
          <div class="col-xs-6 col-sm-2">
            <label>Precio <span class="label-advertencia">*</span></label>
            <div class="form-group">
    				  <input type="text" inputmode="decimal" id="txt-modal-precioItem" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off">
              <span class="help-block" id="error"></span>
    				</div>
          </div>

          <div class="col-xs-6 col-sm-2">
            <label>Costo</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" id="txt-modal-costoItem" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off">
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-8" style="display:none">
            <label>Producto SUNAT <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="hidden" id="hidden-ID_Tabla_Dato" name="ID_Tabla_Dato" class="form-control">
              <input type="text" id="txt-No_Descripcion" name="No_Descripcion" class="form-control autocompletar_producto_sunat" placeholder="Ingresar nombre" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-12">
            <label>Nombre <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <textarea name="textarea-modal-nombreItem" class="form-control required" rows="1" placeholder="Ingresar nombre" maxlength="250" autocomplete="off" aria-required="true"></textarea>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-3">
            <label>Impuesto <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <select id="cbo-modal-impuestoItem" class="form-control"></select>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-3">
            <label>U.M. </label>
            <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Unidad de Medida">
              <i class="fa fa-info-circle"></i>
            </span><a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/CategoriaController/listarUnidadesMedida'; ?>">[Crear]</a>
            <div class="form-group">
              <select id="cbo-modal-unidad_medidaItem" class="form-control"></select>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-4">
            <label>Categorías </label><a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/CategoriaController/listarCategorias'; ?>">[Crear]</a>
            <div class="form-group">
              <select id="cbo-modal-categoria" class="form-control select2" style="width: 100%;"></select>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-2">
            <label>Favorito</label>
            <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para la sección Favoritos en el Punto de Venta">
              <i class="fa fa-info-circle"></i>
            </span>
            <div class="form-group">
              <select id="cbo-modal-favorito" class="form-control" style="width: 100%;">
                <option value="0">No</option>
                <option value="1">Si</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-6">
          <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal"><span class="fa fa-close"></span> Cancelar</button>
        </div>
        <div class="col-xs-6">
          <button type="button" id="btn-modal-crearItem" class="btn btn-success btn-lg btn-block pull-center btn-generar_pedido" data-type="generar_ticket"><i class="fa fa-save"></i> Guardar</button>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /. Forma Pago POS modal -->
<!-- Notificaciones de actualizacion de sistema Modal -->
<div class="modal fade modal-actualizacion_sistema" id="modal-version_sistema">
  <div class="modal-dialog">
    <div class="modal-content">
      <input type="hidden" class="hidden-modal_orden" value="" autocomplete="off">
      <div class="modal-body" id="div-modal-body-orden">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-header-actualizacion_sistema" class="text-center" style="font-weight: bold;"></h4>
        <div class="row">
            <div class="col-xs-12">
            <?php echo DESCRIPCION_NUEVA_VERSION_SISTEMA; ?>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-xs-12">
            <button type="button" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /. Notificaciones de actualizacion de sistema modal -->

<!-- Message lae_pagos Modal -->
<?php
$fImporteComprobantesAdicionalesConsumidosResellerPse=0;
$iCantidadComprobantesAdicionalesConsumidosResellerPseBD=0;
/*
$iCantidadComprobantesAdicionalesConsumidosResellerPse = 0;
$fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse1000 = 0.0708;//0-1,000
$fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse3999 = 0.0472;//1,001-5,000
$fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse4999 = 0.0354;//5,001-10,000
$fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse39999 = 0.0236;//10,001-50,000
$fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse49999 = 0.02124;//50,001-100,000
$fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse899999 = 0.01416;//100,001-1,000,000
$fImporteComprobantesAdicionalesConsumidosResellerPse = $this->empresa->Ss_Total_Pago_Cliente_Servicio;//Aqui se tomara dato de BD la tabla configuracion new campo 
if ($this->empresa->Nu_Tipo_Proveedor_FE == 1) {
  if ( $this->notificaciones->cantidad_comprobantes_adicional_consumido_reseller_pse > 500 ) {
    $iAvanceRango = 0;
    $iCantidadComprobantesAdicionalesConsumidosResellerPseBD = $this->notificaciones->cantidad_comprobantes_adicional_consumido_reseller_pse - 500;
    $iCantidadComprobantesAdicionalesConsumidosResellerPse = $this->notificaciones->cantidad_comprobantes_adicional_consumido_reseller_pse - 500;
    if ( $iCantidadComprobantesAdicionalesConsumidosResellerPseBD > 0 && $iCantidadComprobantesAdicionalesConsumidosResellerPseBD <= 1000) {
      $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse1000 * $iCantidadComprobantesAdicionalesConsumidosResellerPse, 2);
    } else {
      $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse1000 * 1000, 2);
      ++$iAvanceRango;
    }
    
    if ($iAvanceRango == 1) {
      $iCantidadComprobantesAdicionalesConsumidosResellerPse = $iCantidadComprobantesAdicionalesConsumidosResellerPse - 1000;
      if ( $iCantidadComprobantesAdicionalesConsumidosResellerPseBD > 1001 && $iCantidadComprobantesAdicionalesConsumidosResellerPseBD <= 5000) {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse3999 * $iCantidadComprobantesAdicionalesConsumidosResellerPse, 2);
      } else {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse3999 * 3999, 2);
        ++$iAvanceRango;
      }
    }
    
    if ($iAvanceRango == 2) {
      $iCantidadComprobantesAdicionalesConsumidosResellerPse = $iCantidadComprobantesAdicionalesConsumidosResellerPse - 3999;
      if ( $iCantidadComprobantesAdicionalesConsumidosResellerPseBD > 5001 && $iCantidadComprobantesAdicionalesConsumidosResellerPseBD <= 10000) {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse4999 * $iCantidadComprobantesAdicionalesConsumidosResellerPse, 2);
      } else {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse4999 * 4999, 2);
        ++$iAvanceRango;
      }
    }
    
    if ($iAvanceRango == 3) {
      $iCantidadComprobantesAdicionalesConsumidosResellerPse = $iCantidadComprobantesAdicionalesConsumidosResellerPse - 4999;
      if ( $iCantidadComprobantesAdicionalesConsumidosResellerPseBD > 10001 && $iCantidadComprobantesAdicionalesConsumidosResellerPseBD <= 50000) {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse39999 * $iCantidadComprobantesAdicionalesConsumidosResellerPse, 2);
      } else {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse39999 * 39999, 2);
        ++$iAvanceRango;
      }
    }
    
    if ($iAvanceRango == 4) {
      $iCantidadComprobantesAdicionalesConsumidosResellerPse = $iCantidadComprobantesAdicionalesConsumidosResellerPse - 39999;
      if ( $iCantidadComprobantesAdicionalesConsumidosResellerPseBD > 50001 && $iCantidadComprobantesAdicionalesConsumidosResellerPseBD <= 100000) {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse49999 * $iCantidadComprobantesAdicionalesConsumidosResellerPse, 2);
      } else {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse49999 * 49999, 2);
        ++$iAvanceRango;
      }
    }
    
    if ($iAvanceRango == 5) {
      $iCantidadComprobantesAdicionalesConsumidosResellerPse = $iCantidadComprobantesAdicionalesConsumidosResellerPse - 49999;
      if ( $iCantidadComprobantesAdicionalesConsumidosResellerPseBD > 100001 && $iCantidadComprobantesAdicionalesConsumidosResellerPseBD <= 1000000) {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse899999 * $iCantidadComprobantesAdicionalesConsumidosResellerPse, 2);
      } else {
        $fImporteComprobantesAdicionalesConsumidosResellerPse += round($fFactorMultplicacionComprobantesAdicionalesConsumidosResellerPse899999 * 899999, 2);
      }
    }
  }// end if solo entra si la cantidad es > a 500
}// end if reseller pse nubefact
*/
?>
<div class="modal modal-lae_pagos" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="text-center"><b>Procedimiento para pagar</b> - S/ <?php echo numberFormat($fImporteComprobantesAdicionalesConsumidosResellerPse, 2, '.', ','); ?></h4>
        <div class="row">
          <div class="col-xs-12">
            <b>IMPORTANTE: </b>Escribir su RUC en <b>Descripción / Nota</b> al momento de generar el pago y enviar correo a <b>pagos@laesystems.com</b>.</b>
            <?php
            if ( $iCantidadComprobantesAdicionalesConsumidosResellerPseBD > 0 && $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {
              echo '<br>Cantidad de comprobantes adicionales consumidos: ' . $iCantidadComprobantesAdicionalesConsumidosResellerPseBD . ', puede verificar la cantidad en la opción Ventas -> Informes de Venta -> Reporte por Tipos de Documentos';
            }
            ?>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12"><b>CUENTA CORRIENTE SOLES</b></div>
          <div class="col-xs-3"><b>Banco</b></div>
          <div class="col-xs-9">INTERBANK</div>
          
          <div class="col-xs-3"><b>Titular</b></div>
          <div class="col-xs-9">LAE SYSTEM EIRL</div>
          
          <div class="col-xs-3"><b>Nro. Cuenta</b></div>
          <div class="col-xs-9">200-3001580696</div>
          
          <div class="col-xs-3"><b>CCI</b></div>
          <div class="col-xs-9">003-200-003001580696-38</div>
        </div>

        <!--
        <br>
          
        <div class="row">
          <div class="col-xs-12"><b>CUENTA CORRIENTE DÓLARES</b></div>
          <div class="col-xs-3"><b>Banco</b></div>
          <div class="col-xs-9">INTERBANK</div>
          
          <div class="col-xs-3"><b>Titular</b></div>
          <div class="col-xs-9">LAE SYSTEM EIRL</div>
          
          <div class="col-xs-3"><b>Nro. Cuenta</b></div>
          <div class="col-xs-9">289-3003040025</div>
        </div>
        -->

        <br>
        
        <div class="row">
          <div class="col-xs-12"><b>CUENTA AHORROS SOLES</b></div>
          <div class="col-xs-3"><b>Banco</b></div>
          <div class="col-xs-9">BCP</div>
          
          <div class="col-xs-3"><b>Titular</b></div>
          <div class="col-xs-9">Antony Geancarlos Collazos Chumbile</div>
          
          <div class="col-xs-3"><b>Nro. Cuenta</b></div>
          <div class="col-xs-9">191-92655231-0-40</div>
        </div>
        
        <br>
          
        <div class="row">
          <div class="col-xs-12"><b>CUENTA AHORROS SOLES</b></div>
          <div class="col-xs-3"><b>Banco</b></div>
          <div class="col-xs-9">BBVA</div>
          
          <div class="col-xs-3"><b>Titular</b></div>
          <div class="col-xs-9">Antony Geancarlos Collazos Chumbile</div>
          
          <div class="col-xs-3"><b>Nro. Cuenta</b></div>
          <div class="col-xs-9">0011-0002-0200107307</div>
        </div>
        
        <br>
          
        <div class="row">
          <div class="col-xs-3"><b>Celular</b></div>
          <div class="col-xs-9">Yape o Plin</div>
          
          <div class="col-xs-3"><b>Titular</b></div>
          <div class="col-xs-9">Antony Geancarlos Collazos Chumbile</div>
          
          <div class="col-xs-3"><b>Número</b></div>
          <div class="col-xs-9">941 400 239</div>
        </div>
          
        <!--
        <br>
        
        <div class="row">
          <div class="col-xs-12"><b>CUENTA AHORROS DÓLARES</b></div>
          <div class="col-xs-3"><b>Banco</b></div>
          <div class="col-xs-9">BCP</div>
          
          <div class="col-xs-3"><b>Titular</b></div>
          <div class="col-xs-9">Antony Geancarlos Collazos Chumbile</div>
          
          <div class="col-xs-3"><b>Nro. Cuenta</b></div>
          <div class="col-xs-9">191-95821664-1-57</div>
        </div>
          
        <br>
        
        <div class="row">
          <div class="col-xs-12"><b>CUENTA AHORROS DÓLARES</b></div>
          <div class="col-xs-3"><b>Banco</b></div>
          <div class="col-xs-9">BBVA</div>
          
          <div class="col-xs-3"><b>Titular</b></div>
          <div class="col-xs-9">Antony Geancarlos Collazos Chumbile</div>
          
          <div class="col-xs-3"><b>Nro. Cuenta</b></div>
          <div class="col-xs-9">0011-0814-0202940299</div>
        </div>
        -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-block pull-left" data-dismiss="modal">Salir</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.message lae_pagos modal -->
</body>
</html>
