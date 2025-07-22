<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Regulaciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#">Base de Datos</a></li>
                        <li class="breadcrumb-item active">Regulaciones</li>
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
                            <h3 id="totalRegulaciones">0</h3>
                            <p>Total Regulaciones</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="regulacionesVigentes">0</h3>
                            <p>Vigentes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="enRevision">0</h3>
                            <p>En Revisión</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="proximasVencer">0</h3>
                            <p>Próximas a Vencer</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y acciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtros y Acciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="nuevaRegulacion()">
                            <i class="fas fa-plus"></i> Nueva Regulación
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarRegulaciones()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="mostrarProximasVencer()">
                            <i class="fas fa-clock"></i> Próximas a Vencer
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtroTipo">Tipo:</label>
                                <select id="filtroTipo" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="ley">Ley</option>
                                    <option value="decreto">Decreto</option>
                                    <option value="resolucion">Resolución</option>
                                    <option value="norma_tecnica">Norma Técnica</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtroEstado">Estado:</label>
                                <select id="filtroEstado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="vigente">Vigente</option>
                                    <option value="derogada">Derogada</option>
                                    <option value="en_revision">En Revisión</option>
                                    <option value="proyecto">Proyecto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtroPais">País:</label>
                                <select id="filtroPais" class="form-control">
                                    <option value="">Todos los países</option>
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

            <!-- Tabla de regulaciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Regulaciones</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="regulacionesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>País</th>
                                    <th>Entidad Emisora</th>
                                    <th>Fecha Vigencia</th>
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

<!-- Modal para Ver Regulación -->
<div class="modal fade" id="modalVerRegulacion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalles de la Regulación</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Código:</strong> <span id="verCodigo"></span><br>
                        <strong>Título:</strong> <span id="verTitulo"></span><br>
                        <strong>Tipo:</strong> <span id="verTipo"></span><br>
                        <strong>Estado:</strong> <span id="verEstado"></span><br>
                    </div>
                    <div class="col-md-6">
                        <strong>País:</strong> <span id="verPais"></span><br>
                        <strong>Entidad Emisora:</strong> <span id="verEntidadEmisora"></span><br>
                        <strong>Fecha Vigencia:</strong> <span id="verFechaVigencia"></span><br>
                        <strong>Fecha Vencimiento:</strong> <span id="verFechaVencimiento"></span><br>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Descripción:</strong><br>
                        <span id="verDescripcion"></span>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Observaciones:</strong><br>
                        <span id="verObservaciones"></span>
                    </div>
                </div>
                <div class="row mt-3" id="documentoSection" style="display: none;">
                    <div class="col-12">
                        <strong>Documento:</strong><br>
                        <a id="linkDocumento" href="#" target="_blank" class="btn btn-success btn-sm">
                            <i class="fas fa-file-pdf"></i> Ver Documento
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarDesdeModal()">Editar</button>
                <button type="button" class="btn btn-info" onclick="marcarRevisada()">Marcar como Revisada</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Próximas a Vencer -->
<div class="modal fade" id="modalProximasVencer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Regulaciones Próximas a Vencer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="diasVencimiento">Mostrar regulaciones que vencen en:</label>
                    <select id="diasVencimiento" class="form-control" onchange="cargarProximasVencer()">
                        <option value="7">7 días</option>
                        <option value="15">15 días</option>
                        <option value="30" selected>30 días</option>
                        <option value="60">60 días</option>
                        <option value="90">90 días</option>
                    </select>
                </div>
                <div id="listaProximasVencer">
                    <!-- Se carga dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div> 