<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <!-- Headers -->
            <div class="row col-12 mb-2 d-flex justify-content-between">
                <div class="col-12 col-sm-12">
                    <h1>
                        <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>"
                            aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
                        &nbsp;<span id="span-id_pedido" class="badge badge-primary"></span>
                    </h1>
                </div>
                <div class="col-12 col-md-12 d-flex gap-2 justify-content-end">
                    <!-- Buscador -->
                    <div class="dataTables_filter col-12 col-sm-3 d-flex justify-content-end">
                        <input type="search"
                            class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table"
                            placeholder="Buscar por" aria-controls="table-contenedor"
                            style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
                    </div>
                    <!-- Filtro -->
                    <div class=" col-5 col-sm-2 dropdown">
                        <button
                            class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-filter"></i>Filtros
                        </button>
                        <!-- Menú Desplegable -->
                        <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-filtrar-carga">
                            <div class="form-group">
                                <div class="d-flex align-items-center p-2">
                                    <div class="d-flex" style="width:60%">Fecha Inicio</div>
                                    <div style="width: 200px;">
                                        <input type="text" id="txt-Fe_Inicio_Carga"
                                            class="form-control text-center input-date input-report required"
                                            value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                                        <span class="help-block text-danger" id="error"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center p-2">
                                    <div class="d-flex" style="width:60%">Fecha Fin</div>
                                    <div style="width: 200px;">
                                        <input type="text" id="txt-Fe_Fin_Carga"
                                        value="<?php echo date('Y-m-t'); ?>"
                                            class="form-control input-date input-report required">
                                        <span class="help-block text-danger" id="error"></span>
                                    </div>
                                </div>
                                <?php if ($this->user->No_Grupo != "Coordinación") {  ?>
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Pago</div>
                                    <div style="width: 200px;">
                                        <select id="cbo-filtro-estado_pago" name="ID_Estado"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="pendiente">PENDIENTE</option>
                                            <option value="adelanto">ADELANTO</option>
                                            <option value="pagado">PAGADO</option>
                                            <option value="sobrepagado">SOBREPAGADO</option>
                                            <option value="constancia">CONSTANCIA</option>
                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if ($this->user->No_Grupo == "Coordinación") {  ?>
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Estado</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_States_Cliente" name="ID_States_Cliente"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="ROTULADO">ROTULADO</option>
                                            <option value="COBRANDO">COBRANDO</option>
                                            <option value="DATOS PROVEEDOR">DATOS PROVEEDOR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Status</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_Estatus_Cotizacion" name="ID_Status"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="NC">NC</option>
                                            <option value="C">C</option>
                                            <option value="R">R</option>
                                            <option value="NS">NS</option>
                                            <option value="INSPECTION">INSPECTION</option>
                                            <option value="LOADED">LOADED</option>
                                            <option value="NO LOADED">NO LOADED</option>
                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="dropdown-divider"></div>
                            <!-- Botones -->
                            <div class="d-flex justify-content-around">
                                <button
                                    class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                    style="margin-top: .5rem;" id="cancelar-btn">Cancelar</button>
                                <button
                                    class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                    id="aplicar-btn-cotizacion">Aplicar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="button" id="btn-crear-campana"
                            class="text-white bg-[#fd7e14] py-2 px-3 border border-transparent rounded btn-block btn-reporte"
                            data-type="html"><i class="fa fa-plus"></i> Campaña</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content" id="section-listar-pedidos">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control"
                        value="<?php echo $this->router->method; ?>">

                    <div class="col-6 col-sm-3 hidden">
                        <label>F. Inicio</label>
                        <div class="form-group">
                            <input type="text" id="txt-Fe_Inicio" class="form-control input-report required"
                                value="<?php echo dateNow('month_date_ini_report'); ?>">
                            <span class="help-block text-danger" id="error"></span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3 hidden">
                        <label>F. Fin</label>
                        <div class="form-group">
                            <input type="text" id="txt-Fe_Fin" class="form-control input-report required"
                                value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                            <span class="help-block text-danger" id="error"></span>
                        </div>
                    </div>


                    <div class="col-6 col-sm-3 hidden">
                        <label>F. Inicio</label>
                        <div class="form-group">
                            <input type="text" id="txt-Fe_Inicio" class="form-control input-report required"
                                value="<?php echo dateNow('month_date_ini_report'); ?>">
                            <span class="help-block text-danger" id="error"></span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3 hidden">
                        <label>F. Fin</label>
                        <div class="form-group">
                            <input type="text" id="txt-Fe_Fin" class="form-control input-report required"
                                value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                            <span class="help-block text-danger" id="error"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="flex col-xl-6">
                            <div data-table="alumnos"
                                class="col-12 col-md-4 col-xl-2 d-flex align-items-center btn btn-secondary btn-light tab-curso">
                                Alumnos
                            </div>
                            <div data-table="pagos"
                                class="col-12 col-md-4 col-xl-2 d-flex align-items-center btn btn-secondary btn-light tab-curso">
                                Pagos
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-xl-2 d-flex align-items-center">
                            Total Importe:
                            <span id="span-total-importe" class=" pl-2 font-weight-bold"></span>
                        </div>    
                    </div>
                    <div class="table-responsive div-Listar">
                        <table id="table-curso-pedidos" class="table table-hover dataTable no-footer hidden">
                            <thead class="thead-default">
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Curso</th>
                                    <th>Mes</th>
                                    <th>Usuario</th>
                                    <th>Importe</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                        <table id="table-curso-pagos" class="table table-hover dataTable no-footer hidden">
                            <thead class="thead-default">
                                <tr>
                                    <th>N.</th>
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>DNI/RUC</th>
                                    <th>WhatsApp</th>
                                    <th>Precio</th>
                                    <th>Pagado</th>
                                    <th>Adelanto</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <section id="section-campanas-cursos" class="container my-4 none hidden">
        <div class="card-body">
            <div class="table-responsive">
                <table id="table-campanas" class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha de Creación</th>
                            <th>Nombre de Campaña</th>
                            <th>Fecha de Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Cantidad de Personas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
    <section id="section-datos-cliente" class="container my-4" style="display:none;">
        <div class="card shadow">
            <div class="card-header text-white bg-secondary d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-user"></i> Datos del Cliente</h5>
            </div>
            <div class="card-body">
                <form id="form-datos-cliente" autocomplete="off">
                    <input type="hidden" name="ID_Entidad" id="cliente-id" value="">
                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Nombre y apellidos:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-nombres" name="No_Entidad"
                                    readonly>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Dni / ID:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-dni"
                                    name="Nu_Documento_Identidad" readonly>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Correo:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-correo"
                                    name="Txt_Email_Entidad" readonly>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">WhatsApp:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-whatsapp"
                                    name="Nu_Celular_Entidad" readonly>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Fecha de nacimiento:</label>
                                <input type="date" class="form-control cliente-input" id="cliente-edad" name="Fe_Nacimiento"
                                    readonly>
                            </div>
                        </div>
                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Sexo:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-sexo" name="Nu_Tipo_Sexo"
                                    readonly>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Red social:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-redsocial"
                                    name="Nu_Como_Entero_Empresa" readonly>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">País:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-pais" readonly>
                                <select id="select-pais" class="hidden form-control cliente-input" name="ID_Pais"></select>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Departamento:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-departamento" readonly>
                                <select id="select-departamento" class="hidden form-control cliente-input"
                                    name="ID_Departamento"></select>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Provincia:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-provincia" readonly>
                                <select id="select-provincia" class="hidden form-control cliente-input"
                                    name="ID_Provincia"></select>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Distrito:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-distrito" readonly>
                                <select id="select-distrito" class="hidden form-control cliente-input"
                                    name="ID_Distrito"></select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="acceso-aula-virtual" class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="mb-3"><i class="fa fa-graduation-cap"></i> ACCESO AULA VIRTUAL</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Usuario:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-moodle-usuario" readonly>
                            </div>
                            <div class="form-group flex align-items-center">
                                <label class="w-[40%] mb-0">Contraseña:</label>
                                <input type="text" class="form-control cliente-input" id="cliente-moodle-password" readonly>
                            </div>
                        </div>
                        <div id="contenedor-boton-usuario"></div>
                    </div>
                </form>
            </div>
        </div>
    </section>    
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<!-- Modal Nueva Campaña -->
<div class="modal fade" id="modal-nueva-campana" tabindex="-1" aria-labelledby="modalNuevaCampanaLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-nueva-campana">
            <input type="hidden" id="id-campana-editar" name="ID_Campana">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaCampanaLabel">Registrar Nueva Campaña</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fecha-inicio-campana">Fecha de inicio</label>
                        <input type="date" class="form-control" id="fecha-inicio-campana" name="Fe_Inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha-fin-campana">Fecha fin</label>
                        <input type="date" class="form-control" id="fecha-fin-campana" name="Fe_Fin" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
* {
    font-family: Epilogue;
}

#table-Pedidos_filter {
    display: none;
}

.table.table-hover.dataTable.no-footer tbody {
    background-color: white;
}

tr.odd>td,
tr.even>td {
    border-top: 4px solid #f4f6f9;
    border-bottom: 4px solid #f4f6f9;
    vertical-align: middle !important;
    height: 3vh;
}

th.sorting_disabled {
    font-weight: normal !important;
}

.table thead th {
    /* vertical-align: bottom !important; */
    border-bottom: 0px solid #dee2e6 !important;
    border-top: 0px solid #dee2e6;
}

i:hover {
    cursor: pointer;
    color: #5dade2;
}

i {
    align-self: center;
    align-items: center;

}

.btn-block {
    font-size: 14px;
    display: flex;
    justify-content: center;
    gap: 12px;
}

.selected-dia {
    background: #3b82f6 !important;
    color: #fff !important;
    border-radius: 50% !important;
}

.swal2-input {
    width: 80%;
    height: 40px;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-shadow: none;
    transition: border-color 0.3s ease;
}
.dataTables_filter{
    display: none;
}
</style>