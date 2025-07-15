<?php $this->load->view('header_v2'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detalle de Orden</h1>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex justify-content-end">
                        <a href="<?php echo base_url('orders/listar'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </a>
                    </div>
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
                            <h3 class="card-title">Nombre del Cliente: <span id="customerName"></span></h3>
                        </div>
                        <div class="card-body">
                            <div id="cotizacionActions" class="mb-3"></div>
                            <h5>Productos de la Orden</h5>
                            <div class="table-responsive">
                                <table id="productsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Subtotal</th>
                                            <th>Link Tienda</th>
                                            <th>Link Alibaba</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                    <input type="hidden" name="orderId" id="uploadOrderId">
                    <div class="form-group">
                        <label for="cotizacion_file">Seleccionar archivo:</label>
                        <input type="file" class="form-control-file" id="cotizacion_file" name="cotizacion_file" accept=".xlsx,.xls,.pdf,.doc,.docx" required>
                        <small class="form-text text-muted">Formatos permitidos: Excel, PDF, Word</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnUploadCotizacion">Subir</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('footer_v2'); ?>

