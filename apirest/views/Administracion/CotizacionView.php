
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <a href="<?php echo base_url('orders/listar'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="card-title">Cotización - Orden <?php echo $order->order_number; ?></h3>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-success" onclick="downloadOrderExcel(<?php echo $order->id; ?>)">
                                                <i class="fas fa-file-excel"></i> Descargar Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Información del Cliente -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6><strong>Cliente:</strong></h6>
                                        <input type="text" class="form-control" value="<?php echo $order->customer_full_name; ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><strong>Agregar Cotización:</strong></h6>
                                        <button class="btn btn-primary" onclick="showUploadModal()">
                                            <i class="fas fa-plus"></i> Subir Cotización
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabla de Productos -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Imagen</th>
                                                <th>Nombre</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Link tienda.</th>
                                                <th>Link alibaba</th>
                                                <th>Delivery Lead Times</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($item->product_image)): ?>
                                                        <img src="<?php echo base_url($item->product_image); ?>" 
                                                             alt="<?php echo $item->product_name; ?>" 
                                                             style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <span class="text-muted">Sin imagen</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $item->product_name; ?></td>
                                                <td><?php echo $item->quantity; ?></td>
                                                <td>S/. <?php echo number_format($item->unit_price, 2); ?></td>
                                                <td>
                                                    <?php if (!empty($item->store_link)): ?>
                                                        <a href="<?php echo $item->store_link; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-external-link-alt"></i> Ver
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">No disponible</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($item->alibaba_link)): ?>
                                                        <a href="<?php echo $item->alibaba_link; ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-external-link-alt"></i> Ver
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">No disponible</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($item->delivery_lead_times)): ?>
                                                        <?php echo $item->delivery_lead_times; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal para subir cotización -->
    <div class="modal fade" id="uploadCotizacionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subir Cotización</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadCotizacionForm" enctype="multipart/form-data">
                        <input type="hidden" name="orderId" value="<?php echo $order->id; ?>">
                        <div class="form-group">
                            <label for="cotizacion_file">Seleccionar archivo:</label>
                            <input type="file" class="form-control-file" id="cotizacion_file" name="cotizacion_file" accept=".xlsx,.xls,.pdf,.doc,.docx" required>
                            <small class="form-text text-muted">Formatos permitidos: Excel, PDF, Word</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="uploadCotizacion()">Subir</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showUploadModal() {
            $('#uploadCotizacionModal').modal('show');
        }

        function uploadCotizacion() {
            var formData = new FormData(document.getElementById('uploadCotizacionForm'));
            
            $.ajax({
                url: '<?php echo base_url("orders/uploadCotizacion"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        alert('Cotización subida correctamente');
                        $('#uploadCotizacionModal').modal('hide');
                        // Recargar la página para mostrar el botón de descarga
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                },
                error: function() {
                    alert('Error al subir el archivo');
                }
            });
        }

        function downloadOrderExcel(orderId) {
            window.open('<?php echo base_url("orders/downloadOrderExcel/"); ?>' + orderId, '_blank');
        }
    </script> 