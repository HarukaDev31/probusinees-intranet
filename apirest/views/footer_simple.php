    </div> <!-- Cierre de content-wrapper -->

    <!-- jQuery -->
    <script src="<?php echo base_url("plugins_v2/jquery/jquery.min.js"); ?>"></script>
    
    <!-- Bootstrap -->
    <script src="<?php echo base_url("bower_components/bootstrap/dist/js/bootstrap.min.js"); ?>"></script>
    

    
    <!-- SweetAlert2 -->
    <script src="<?php echo base_url("plugins_v2/sweetalert2/sweetalert2.min.js"); ?>"></script>
    
    <!-- AdminLTE -->
    <script src="<?php echo base_url("dist_v2/js/adminlte.min.js"); ?>"></script>
    
    <!-- Custom JavaScript -->
    <script>
        var base_url = '<?php echo base_url(); ?>';
        
        // Configuración global de SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        
        // Función para mostrar notificaciones
        function showNotification(type, message) {
            Toast.fire({
                icon: type,
                title: message
            });
        }
    </script>
    
    <?php if (isset($js_order_detail) && $js_order_detail == true) : ?>
        <!-- Order Detail JavaScript -->
        <script src="<?php echo base_url("assets/js/js_order_detail.js"); ?>"></script>
    <?php endif; ?>
</body>
</html> 