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
                                            value="<?php echo dateNow('month_date_ini_report'); ?>">
                                        <span class="help-block text-danger" id="error"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center p-2">
                                    <div class="d-flex" style="width:60%">Fecha Fin</div>
                                    <div style="width: 200px;">
                                        <input type="text" id="txt-Fe_Fin_Carga"
                                            class="form-control input-date input-report required">
                                        <span class="help-block text-danger" id="error"></span>
                                    </div>
                                </div>
                                <?php if ($this->user->No_Grupo != "Coordinación") {  ?>
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Pago</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_Estado_Cotizacion" name="ID_Estado"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="PENDIENTE">PENDIENTE</option>
                                            <option value="RECIBIENDO">RECIBIENDO</option>
                                            <option value="COMPLETADO">COMPLETADO</option>
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
                    <div data-table="consolidado"
                        class="col-12 col-md-4 col-xl-2 d-flex align-items-center btn btn-secondary btn-light tab-administracion">
                        Consolidado
                    </div>
                    <div data-table="cursos"
                        class="col-12 col-md-4 col-xl-2 d-flex align-items-center btn btn-secondary btn-light tab-administracion">
                        Cursos
                    </div>

                </div>
                <div class="table-responsive div-Listar">
                    <table id="table-pagos-curso" class="table table-hover dataTable no-footer hidden">
                        <thead class="thead-default">
                            <tr>
                                <th>Pedido</th>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Whatsapp</th>
                                <th>Servicio</th>
                                <th>Campaña</th>
                                <th>Acciones</th>
                                <th>Estado</th>
                                <th>Importe</th>
                                <th>Pagado</th>
                                <th>Adelantos</th>
                            </tr>
                        </thead>
                    </table>
                    <table id="table-pagos-consolidado" class="table table-hover dataTable no-footer hidden">
                        <thead class="thead-default">
                            <tr>
                                <th>N.</th>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>DNI/RUC</th>
                                <th>WhatsApp</th>
                                <th>Servicio</th>
                                <th>Campaña</th>
                                <th>Acciones</th>
                                <th>Estados</th>
                                <th>Importe</th>
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
 <div class="modal fade" id="modalClientePagosCoordination" tabindex="-1" role="dialog"
        aria-labelledby="modalClientePagosCoordinationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClientePagosCoordinationLabel">Pagos del Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="table-pagos-tracking-coordinacion" class="table table-hover dataTable no-footer">
                        <thead class="thead-default">
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Banco</th>
                                <th>Monto</th>
                                <th>Voucher</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>