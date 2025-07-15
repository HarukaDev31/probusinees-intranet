<?php $this->load->view('header_v2', ["js_order_confirmado_detail" => true]); ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detalle de Orden Confirmada</h1>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex justify-content-end">
                        <a href="<?php echo base_url('OrdersController/listarConfirmados'); ?>" class="btn btn-secondary">
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
                            <div id="cotizacionActions" class="mb-3">
                                <!-- Botón de descargar cotización, solo si existe (se controla por JS) -->
                                <button id="btnDescargarCotizacion" class="btn btn-info" style="display:none;">
                                    <i class="fas fa-download"></i> Descargar Cotización
                                </button>
                                <!-- Acciones para la orden -->
                                <button id="btnSubirOrden" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Subir Orden
                                </button>
                                <button id="btnDescargarOrden" class="btn btn-success" style="display:none;">
                                    <i class="fas fa-download"></i> Descargar Orden
                                </button>
                                <button id="btnBorrarOrden" class="btn btn-danger" style="display:none;">
                                    <i class="fas fa-trash"></i> Borrar Orden
                                </button>
                                <!-- Select para estado confirmado -->
                                
                            </div>
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

<!-- Modal para subir orden -->
<div class="modal fade" id="uploadOrdenModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Orden</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadOrdenForm" enctype="multipart/form-data">
                    <input type="hidden" name="orderId" id="uploadOrderId">
                    <div class="form-group">
                        <label for="orden_file">Seleccionar archivo:</label>
                        <input type="file" class="form-control-file" id="orden_file" name="orden_file" accept=".xlsx,.xls,.pdf,.doc,.docx" required>
                        <small class="form-text text-muted">Formatos permitidos: Excel, PDF, Word</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnUploadOrden">Subir</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('footer_v2', ["js_order_confirmado_detail" => true]); ?> 