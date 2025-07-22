<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Productos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#">Base de Datos</a></li>
                        <li class="breadcrumb-item active">Productos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Tarjetas de estadísticas -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalProductos">0</h3>
                            <p>Total Productos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="productosActivos">0</h3>
                            <p>Productos Activos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="stockBajo">0</h3>
                            <p>Stock Bajo</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="productosAgotados">0</h3>
                            <p>Productos Agotados</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y acciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtros y Acciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="nuevoProducto()">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarProductos()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtroCategoria">Categoría:</label>
                                <select id="filtroCategoria" class="form-control">
                                    <option value="">Todas las categorías</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtroEstado">Estado:</label>
                                <select id="filtroEstado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="agotado">Agotado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                        <i class="fas fa-broom"></i> Limpiar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Productos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="productosTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargan via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para Ver Producto -->
<div class="modal fade" id="modalVerProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalles del Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Código:</strong> <span id="verCodigo"></span><br>
                        <strong>Nombre:</strong> <span id="verNombre"></span><br>
                        <strong>Categoría:</strong> <span id="verCategoria"></span><br>
                        <strong>Estado:</strong> <span id="verEstado"></span><br>
                    </div>
                    <div class="col-md-6">
                        <strong>Precio:</strong> <span id="verPrecio"></span><br>
                        <strong>Stock:</strong> <span id="verStock"></span><br>
                        <strong>Stock Mínimo:</strong> <span id="verStockMinimo"></span><br>
                        <strong>Fecha Creación:</strong> <span id="verFechaCreacion"></span><br>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Descripción:</strong><br>
                        <span id="verDescripcion"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarDesdeModal()">Editar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Actualizar Stock -->
<div class="modal fade" id="modalActualizarStock" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Actualizar Stock</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formActualizarStock">
                <div class="modal-body">
                    <input type="hidden" id="stockProductoId">
                    <div class="form-group">
                        <label>Producto:</label>
                        <span id="stockProductoNombre" class="form-control-plaintext"></span>
                    </div>
                    <div class="form-group">
                        <label>Stock Actual:</label>
                        <span id="stockActual" class="form-control-plaintext"></span>
                    </div>
                    <div class="form-group">
                        <label for="tipoMovimiento">Tipo de Movimiento:</label>
                        <select id="tipoMovimiento" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="entrada">Entrada (+)</option>
                            <option value="salida">Salida (-)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cantidadMovimiento">Cantidad:</label>
                        <input type="number" id="cantidadMovimiento" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Stock</button>
                </div>
            </form>
        </div>
    </div>
</div> 