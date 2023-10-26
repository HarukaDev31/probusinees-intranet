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

<?php $iControlVersionDashboard = '1.0.0'; ?>

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

<!-- JS INTERNO DE EMPRESA -->
<!-- Inicio -->
<?php if (isset($js_inicio) && $js_inicio==true) : ?>
<script src="<?php echo base_url() . 'dist_v2/js/inicio.js?ver=' . $iControlVersionDashboard; ?>"></script>
<?php endif; ?>
</body>
</html>