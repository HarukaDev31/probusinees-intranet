<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Clientes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#">Base de Datos</a></li>
                        <li class="breadcrumb-item active">Clientes</li>
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
                            <h3 id="totalClientes">0</h3>
                            <p>Total Clientes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="clientesActivos">0</h3>
                            <p>Clientes Activos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="prospectos">0</h3>
                            <p>Prospectos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="favoritos">0</h3>
                            <p>Favoritos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y acciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtros y Acciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="nuevoCliente()">
                            <i class="fas fa-plus"></i> Nuevo Cliente
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarClientes()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="importarClientes()">
                            <i class="fas fa-file-upload"></i> Importar Excel
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="mostrarFavoritos()">
                            <i class="fas fa-star"></i> Ver Favoritos
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtroTipoCliente">Tipo Cliente:</label>
                                <select id="filtroTipoCliente" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="persona_natural">Persona Natural</option>
                                    <option value="empresa">Empresa</option>
                                    <option value="gobierno">Gobierno</option>
                                    <option value="ong">ONG</option>
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
                                    <option value="prospecto">Prospecto</option>
                                    <option value="suspendido">Suspendido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtroPais">País:</label>
                                <select id="filtroPais" class="form-control">
                                    <option value="">Todos los países</option>
                                    <option value="Peru">Perú</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Ecuador">Ecuador</option>
                                    <option value="Bolivia">Bolivia</option>
                                    <option value="Chile">Chile</option>
                                    <option value="Argentina">Argentina</option>
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

            <!-- Tabla de clientes -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Clientes</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="clientesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Razón Social</th>
                                    <th>Nombre Comercial</th>
                                    <th>Tipo</th>
                                    <th>Documento</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>País</th>
                                    <th>Estado</th>
                                    <th>Fecha Registro</th>
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

<!-- Modal para Ver Cliente -->
<div class="modal fade" id="modalVerCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalles del Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Información General</h5>
                        <strong>Código:</strong> <span id="verCodigo"></span><br>
                        <strong>Razón Social:</strong> <span id="verRazonSocial"></span><br>
                        <strong>Nombre Comercial:</strong> <span id="verNombreComercial"></span><br>
                        <strong>Tipo Cliente:</strong> <span id="verTipoCliente"></span><br>
                        <strong>Documento:</strong> <span id="verDocumento"></span><br>
                        <strong>Estado:</strong> <span id="verEstado"></span><br>
                    </div>
                    <div class="col-md-6">
                        <h5>Información de Contacto</h5>
                        <strong>Email:</strong> <span id="verEmail"></span><br>
                        <strong>Teléfono:</strong> <span id="verTelefono"></span><br>
                        <strong>Dirección:</strong> <span id="verDireccion"></span><br>
                        <strong>Ciudad:</strong> <span id="verCiudad"></span><br>
                        <strong>País:</strong> <span id="verPais"></span><br>
                        <strong>Sitio Web:</strong> <span id="verSitioWeb"></span><br>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5>Persona de Contacto</h5>
                        <strong>Nombre:</strong> <span id="verPersonaContacto"></span><br>
                        <strong>Teléfono:</strong> <span id="verTelefonoContacto"></span><br>
                        <strong>Email:</strong> <span id="verEmailContacto"></span><br>
                    </div>
                    <div class="col-md-6">
                        <h5>Información Adicional</h5>
                        <strong>Fecha Registro:</strong> <span id="verFechaRegistro"></span><br>
                        <strong>Favorito:</strong> <span id="verFavorito"></span><br>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Notas</h5>
                        <p id="verNotas"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarDesdeModal()">Editar</button>
                <button type="button" class="btn btn-info" onclick="verHistorialModal()">Ver Historial</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Historial de Cliente -->
<div class="modal fade" id="modalHistorialCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Historial de Actividades</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="historialContent">
                    <!-- Se carga dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Importar Clientes -->
<div class="modal fade" id="modalImportarClientes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Importar Clientes desde Excel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formImportarClientes" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="archivoExcel">Archivo Excel:</label>
                        <input type="file" id="archivoExcel" name="archivo" class="form-control" accept=".xlsx,.xls" required>
                        <small class="form-text text-muted">
                            Formatos permitidos: .xlsx, .xls. Máximo 5MB.
                        </small>
                    </div>
                    <div class="alert alert-info">
                        <h6>Formato requerido del archivo:</h6>
                        <ul class="mb-0">
                            <li>Columna A: Código Cliente (opcional)</li>
                            <li>Columna B: Razón Social</li>
                            <li>Columna C: Nombre Comercial</li>
                            <li>Columna D: Tipo Documento</li>
                            <li>Columna E: Número Documento</li>
                            <li>Columna F: Email</li>
                            <li>Columna G: Teléfono</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div> 