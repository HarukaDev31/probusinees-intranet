<?php $iControlVersionDashboard = '1.2.55'; ?>
<!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> <?php echo $iControlVersionDashboard; ?>
    </div>
    <strong>&copy; 2023 <a style="color: #FF500B !important" href="https://probusiness.pe" target="_blank" alt="ProBusiness" rel="noopener noreferrer">ProBusiness</a></strong>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script>
const socket = new WebSocket('wss://websockets.probusiness.pe');

// Suscribirse a múltiples canales
function subscribeToChannels(project, role, user) {
    const message = JSON.stringify({
        action: 'subscribe',
        project: project,
        role: role,
        user: user
    });
    socket.send(message);
}

// Publicar un mensaje en múltiples canales
function publishToChannels(project, role, user, message) {
    const msg = JSON.stringify({
        action: 'publish',
        project: project,
        role: role,
        user: user,
        message: message
    });
    socket.send(msg);
}

// Manejar mensajes recibidos del servidor
socket.onmessage = function(event) {
    const message = JSON.parse(event.data);
    console.log('Received message:', message);
    // Aquí puedes manejar los mensajes recibidos del servidor
    try{
      alert(message.message);
    }
    catch(e){
      console.log(e);
    }
    
};

//on connect to socket
socket.onopen = function(event) {
    subscribeToChannels('project1', 'admin', 'user123');
    publishToChannels('project1', 'admin', 'user123', 'Hello everyone!');
};
</script>
<script src="<?php echo base_url("plugins_v2/jquery/jquery.min.js"); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<!-- Bootstrap 4 -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="<?php echo base_url("plugins_v2/bootstrap/js/bootstrap.bundle.min.js"); ?>"></script>
<!-- DataTables  & Plugins -->
<script src="<?php echo base_url("plugins_v2/datatables/jquery.dataTables.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-bs4/js/dataTables.bootstrap4.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-responsive/js/dataTables.responsive.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-responsive/js/responsive.bootstrap4.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-buttons/js/dataTables.buttons.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-buttons/js/buttons.bootstrap4.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/jszip/jszip.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/pdfmake/pdfmake.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/pdfmake/vfs_fonts.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-buttons/js/buttons.html5.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-buttons/js/buttons.print.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/datatables-buttons/js/buttons.colVis.min.js"); ?>"></script>

<!-- AdminLTE App -->
<script src="<?php echo base_url("dist_v2/js/adminlte.min.js"); ?>"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url("dist_v2/js/demo.js?ver=1.0.0"); ?>"></script>

<!-- jQuery Validate -->
<script src="<?php echo base_url() . 'assets/js/jquery.validate.min.js'; ?>"></script>
<script src="<?php echo base_url("plugins_v2/jquery-validation/jquery.validate.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/jquery-validation/additional-methods.min.js"); ?>"></script>

<script type="text/javascript">var base_url = '<?php echo base_url(); ?>';</script>

<script src="<?php echo base_url() . 'dist_v2/js/init.js?ver=' . $iControlVersionDashboard; ?>"></script>

<!-- JS INTERNO DE EMPRESA -->
<!-- Inicio -->
<?php if (isset($js_inicio) && $js_inicio==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>
<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_cliente) && $js_cliente==true) : ?>
<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/Ventas/ReglasVentas/cliente.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_proveedor) && $js_proveedor==true) : ?>
<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/Logistica/ReglasLogistica/proveedor.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- Folder: ImportacionGrupal -->
<?php if (isset($js_campana_grupal) && $js_campana_grupal==true) : ?>
<!-- InputMask -->
<script src="<?php echo base_url("plugins_v2/moment/moment.min.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/inputmask/jquery.inputmask.min.js"); ?>"></script>

<!-- date-range-picker -->
<script src="<?php echo base_url("plugins_v2/daterangepicker/daterangepicker.js"); ?>"></script>
<script src="<?php echo base_url("plugins_v2/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"); ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/campana_grupal.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_pedidos_grupal) && $js_pedidos_grupal==true) : ?>
<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/pedidos_grupal.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_metodo_pago_grupal) && $js_metodo_pago_grupal==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/metodo_pago.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_metodo_entrega_grupal) && $js_metodo_entrega_grupal==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/metodo_entrega.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_slider_importacion) && $js_slider_importacion==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/dropzone/css/dropzone.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/dropzone/js/dropzone.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/slider.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_categoria_importacion_grupal) && $js_categoria_importacion_grupal==true) : ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/dropzone/css/dropzone.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/dropzone/js/dropzone.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/categoria.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- agente de compra -->
<?php if (isset($js_pedidos_agente) && $js_pedidos_agente==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/AgenteCompra/pedidos_agente.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_pedidos_garantizados) && $js_pedidos_garantizados==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/AgenteCompra/pedidos_garantizados.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_pedidos_pagados) && $js_pedidos_pagados==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>

<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>

<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'dist_v2/js/AgenteCompra/pedidos_pagados.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_pedidos_aprobados_pagados) && $js_pedidos_aprobados_pagados==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>

<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>

<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'dist_v2/js/AgenteCompra/pedidos_aprobados_pagados.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_historial_pagos) && $js_historial_pagos==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/AgenteCompra/historial_pagos.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- Usuarios y opciones -->
<?php if (isset($js_perfil_usuario) && $js_perfil_usuario==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/PanelAcceso/perfil_usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_usuario) && $js_usuario==true) : ?>
<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/PanelAcceso/usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
<?php if (isset($js_permiso_usuario) && $js_permiso_usuario==true) : ?>
<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/PanelAcceso/permiso_usuario.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- Folder: Logistica -->
<?php if (isset($js_producto_importacion) && $js_producto_importacion==true) : ?>
<!--
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/summernote.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/summernote.min.js';?>"></script>
-->
<script src="<?php echo base_url() . 'plugins_v2/summernote/summernote-bs4.js?ver=1.0.0';?>"></script>
<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/dropzone/css/dropzone.min.css'; ?>">
<script src="<?php echo base_url() . 'assets/dropzone/js/dropzone.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/Logistica/ReglasLogistica/producto_importacion.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- Curso -->
<?php if (isset($js_pedidos_curso) && $js_pedidos_curso==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/Curso/pedidos_curso.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<!-- carga consolidada -->
<?php if (isset($js_pedidos_cargaconsolidada) && $js_pedidos_cargaconsolidada==true) : ?>
<!-- datepicker -->
<link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.auto-complete.js?ver=1.0'; ?>"></script>

<script src="<?php echo base_url("plugins_v2/select2/js/select2.full.min.js"); ?>"></script>


<script src="<?php echo base_url() . 'dist_v2/js/CargaConsolidada/pedidos_cargaconsolidada.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_consolidado) && $js_consolidado==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/CargaConsolidada/consolidado.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

  
<?php if (isset($js_ccotizaciones) && $js_ccotizaciones==true) : ?>
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
<script src="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'dist_v2/js/CargaConsolidada/ccotizaciones.js'?>"></script>
<?php endif; ?>
<?php if (isset($js_tarifas_cotizaciones) && $js_tarifas_cotizaciones==true) : ?>
<script src="<?php echo base_url() . 'dist/js/Configuracion/tarifas_cotizaciones.js'?>"></script>
<?php endif; ?>
<div id="modal-loader" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-loader">
    <div class="modal-content modal-content-loader-change">
      <div class="modal-body">
        <div class="text-center">
          <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">
            <span class="sr-only"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Message Delete Modal -->
<div class="modal fade modal-danger modal-message-delete" id="modal-message-delete" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-secondary col" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btn-save-delete" class="btn btn-primary col">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Message Modal -->
<div class="modal modal-message fade" id="modal-message">
  <div class="modal-dialog">
    <div class="modal-content" id="moda-message-content">
      <div class="modal-header">
        <h4 class="modal-title-message text-center"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
    </div>
  </div>
</div>

<!-- modal notification -->
<div class="modal fade" id="modal-notification" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Notificaciones</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <?php if($this->user->Nu_Notificacion==1) { ?>
          <?php if($this->notificaciones['status']=='success') {
            $iCantidadNotificaciones = 0;
            foreach($this->notificaciones['result'] as $row) {
              $segundos = diferenciaFechasMultipleFormato($row->Fe_Registro, dateNow('fecha_hora'), 'segundos');
              $minutos = diferenciaFechasMultipleFormato($row->Fe_Registro, dateNow('fecha_hora'), 'minutos');
              $horas = diferenciaFechasMultipleFormato($row->Fe_Registro, dateNow('fecha_hora'), 'horas');
              $dias = diferenciaFechasMultipleFormato($row->Fe_Registro, dateNow('fecha_hora'), 'dias');

              $time = ($dias > 0 ? $dias . ' día ' : '') . ($horas > 0 ? $horas . ' H ' : '') . ($minutos > 0 ? $minutos . ' m ' : '') . $segundos . ' s';
            ?>
            <a href="#" class="dropdown-item">
              <div class="media">
                <img src="<?php echo base_url() . 'dist_v2/img/user_all.png?ver=1.0.0'; ?>" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                <div class="media-body">
                  <div class="row">
                    <div class="col-12">
                      <h3 class="dropdown-item-title">
                        <?php echo $row->No_Usuario_Evento; ?>
                      </h3>
                      <span class="badge bg-success"><?php echo $row->No_Menu; ?></span>
                      <p class="text-sm"><?php echo $row->No_Evento; ?></p>
                      <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i><?php echo $time; ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <?php
                ++$iCantidadNotificaciones;
              }
            ?>
          <?php } ?>
        <?php } ?>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-secondary col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save-delete" class="btn btn-primary col">Aceptar</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>