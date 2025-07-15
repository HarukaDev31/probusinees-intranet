<?php $this->load->view('header_v2'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Órdenes</h1>
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
                                    <h3 class="card-title">Lista de Órdenes</h3>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-primary" onclick="showFilters()">
                                            <i class="fas fa-filter"></i> Filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ordersTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>N.</th>
                                            <th>N. Orden</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>DNI</th>
                                            <th>WhatsApp</th>
                                            <th>Correo</th>
                                            <th>Estado</th>
                                            <th>Cotización</th>
                                            <th>Acciones</th>
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

<!-- Modal de Filtros -->
<div class="modal fade" id="filtersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="filtersForm">
                    <div class="form-group">
                        <label>Fecha de inicio:</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                    </div>
                    <div class="form-group">
                        <label>Fecha de fin:</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                    </div>
                  
                    <div class="form-group">
                        <label>Estado de cotización:</label>
                        <select class="form-control" id="estado" name="estado">
                            <option value="0">Todos</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Cotizado">Cotizado</option>
                            <option value="Observado">Observado</option>
                            <option value="Confirmado">Confirmado</option>
                            <option value="Rechazado">Rechazado</option>
                        </select>
                    </div>
                    <div class="form-group" id="estado_confirmado_group" style="display:none;">
                        <label>Estado Confirmado:</label>
                        <select class="form-control" id="estado_confirmado" name="estado_confirmado">
                            <option value="0">Todos</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Negociacion">Negociación</option>
                            <option value="Produccion">Producción</option>
                            <option value="Embarcado">Embarcado</option>
                            <option value="Entregado">Entregado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
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
                <button type="button" class="btn btn-primary" onclick="uploadCotizacion()">Subir</button>
            </div>
        </div>
    </div>
</div>

