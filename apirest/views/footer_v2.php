  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1.0.0
    </div>
    <strong>&copy; 2023 <a href="#">ProBusiness</a></strong>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php $iControlVersionDashboard = '1.0.22'; ?>

<!-- jQuery -->
<script src="<?php echo base_url("plugins_v2/jquery/jquery.min.js"); ?>"></script>
<!-- Bootstrap 4 -->
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

<script type="text/javascript">var base_url = '<?php echo base_url(); ?>';</script>

<script src="<?php echo base_url() . 'dist_v2/js/init.js?ver=' . $iControlVersionDashboard; ?>"></script>

<!-- JS INTERNO DE EMPRESA -->
<!-- Inicio -->
<?php if (isset($js_inicio) && $js_inicio==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
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
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/pedidos_grupal.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_cliente) && $js_cliente==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/Ventas/ReglasVentas/cliente.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_metodo_pago_grupal) && $js_metodo_pago_grupal==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/metodo_pago.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>

<?php if (isset($js_metodo_entrega_grupal) && $js_metodo_entrega_grupal==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/ImportacionGrupal/metodo_entrega.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>


<!-- Message Delete Modal -->
<div class="modal fade modal-danger modal-message-delete" id="modal-message-delete" aria-modal="true" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btn-save-delete" class="btn btn-primary">Aceptar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
<div></div></div>


</body>
</html>