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
                <div class="col-12 col-md-12 d-flex gap-2 justify-content-end ">
                    <!-- Buscador -->
                    <div class="dataTables_filter col-12 col-sm-3 d-flex justify-content-end ">
                        <input type="search"
                            class="form-control bg-white h-100 hover:bg-white-200 text-black-200  border border-transparent hover:border-orange-600 rounded search-table"
                            placeholder="Buscar por" aria-controls="table-contenedor"
                            style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
                    </div>
                    <!-- Filtro -->
                    <div class=" col-5 col-sm-2 dropdown">
                        <button
                            class="bg-white py-3 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 1.19922H1L6.6 7.82122V12.3992L9.4 13.7992V7.82122L15 1.19922Z"
                                    stroke="#272A30" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Filtros
                        </button>
                        <!-- Menú Desplegable MAIN -->
                        <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-filtrar-carga">
                            <div class="form-group">
                                <div class="d-flex align-items-center p-2">
                                    <div class="d-flex" style="width:60%">Fecha Inicio</div>
                                    <div style="width: 200px;">
                                        <input type="text" id="txt-Fe_Inicio"
                                            class="form-control text-center input-date input-report required">
                                        <span class="help-block text-danger" id="error"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center p-2">
                                    <div class="d-flex" style="width:60%">Fecha Fin</div>
                                    <div style="width: 200px;">
                                        <input type="text" id="txt-Fe_Fin"
                                            class="form-control input-date input-report required">
                                        <span class="help-block text-danger" id="error"></span>
                                    </div>
                                </div>
                                <?php if ($this->user->No_Grupo != "Coordinación") {  ?>
                                    <div class="d-flex align-items-center p-2" style="width:300px;">
                                        <div class="d-flex" style="width:60%">Estado</div>
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
                                    <div class="d-flex align-items-center p-2" style="width:300px;">
                                        <div class="d-flex" style="width:60%">Campaña</div>
                                        <div style="width: 200px;">
                                            <select id="txt-ID_Campana_Curso" name="ID_Campana_Curso"
                                                class="form-control input-estado">

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
                                    style="margin-top: .5rem;" id="cancelar-btn">Limpiar</button>
                                <button
                                    class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                    id="aplicar-btn-cotizacion">Aplicar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="button" id="btn-nueva-campana"
                            class="text-white bg-[#FF500B] py-3 px-3 border border-transparent rounded btn-block btn-reporte"
                            data-type="html"> Ver Campañas</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content" id="section-listar-pedidos">
        <div class="container-fluid">
            <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control"
                value="<?php echo $this->router->method; ?>">
            <div class="flex">
                <div class="row tabs" style="width: -webkit-fill-available;">
                    <div data-table="alumnos"
                        class="col-12 col-md-4 col-xl-1 d-flex align-items-center justify-content-center btn tab tab-curso">
                        Alumnos
                    </div>
                    <div data-table="pagos"
                        class="col-12 col-md-4 col-xl-1 d-flex align-items-center justify-content-center btn tab tab-curso">
                        Pagos
                    </div>

                </div>
                <div class="col-12 col-md-4 col-xl-2 d-flex align-items-center">
                    Importe total:
                    <span id="span-total-importe" class=" pl-2 font-weight-regular header-indicator"></span>
                </div>
            </div>
            <div class="table-responsive div-Listar">
                <table id="table-curso-pedidos" class="table table-hover dataTable no-footer hidden">
                    <thead class="thead-default">
                        <tr>
                            <th>N°</th>
                            <th>Fecha</th>
                            <th style="">Cliente</th>
                            <th>Curso</th>
                            <th>Campaña</th>
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
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <section id="section-campanas-cursos" class="container my-4 none hidden">
        <div class="card-body">
            <div class="table-responsive">
                <table id="table-campanas" class="table table-hover dataTable no-footer ">
                    <thead class="thead-default">
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
        <div class="">

            <div class="">
                <form id="form-datos-cliente" autocomplete="off">
                    <input type="hidden" name="ID_Entidad" id="cliente-id" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-4 p-4 grid-rows-3">

                        <div class="card px-4 py-4 row-span-3">
                            <div class="bg-white text-black  flex flex-row justify-between align-items-center border-bottom">
                                <h5 class="mb-0 flex flex-row gap-1"><svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.1111 15.8737V14.1098C15.1111 13.1742 14.7394 12.2769 14.0778 11.6153C13.4163 10.9537 12.519 10.582 11.5833 10.582H4.52778C3.59215 10.582 2.69485 10.9537 2.03326 11.6153C1.37168 12.2769 1 13.1742 1 14.1098V15.8737" stroke="#272A30" stroke-width="1.12889" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M8.0561 7.99306C10.0044 7.99306 11.5839 6.41362 11.5839 4.46528C11.5839 2.51694 10.0044 0.9375 8.0561 0.9375C6.10776 0.9375 4.52832 2.51694 4.52832 4.46528C4.52832 6.41362 6.10776 7.99306 8.0561 7.99306Z" stroke="#272A30" stroke-width="1.12889" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    Información del alumno</h5>
                                <div class="flex flex-row gap-2">
                                    <button id="btn-editar-cliente" class="btn p-1 ml-4" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button id="btn-cancel" class="hidden btn p-1 ml-4" title="Cancelar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="col-3 col-xl-10 py-sm-3 py-xl-0 py-md-0">
                                        <button id="btn-guardar-cliente" type="button" class="text-white bg-[#fd7e14] py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <!-- Columna izquierda -->
                                <div class="col-md-12">
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Nombre y apellidos:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-nombres"
                                            name="No_Entidad" readonly>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Dni / ID:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-dni"
                                            name="Nu_Documento_Identidad" readonly>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Correo:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-correo"
                                            name="Txt_Email_Entidad" readonly>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">WhatsApp:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-whatsapp"
                                            name="Nu_Celular_Entidad" readonly>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Fecha de nacimiento:</label>
                                        <input type="date" class="form-control cliente-input" id="cliente-edad"
                                            name="Fe_Nacimiento" readonly>
                                    </div>
                                </div>
                                <!-- Columna derecha -->
                                <div class="col-md-12">
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Sexo:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-sexo"
                                            name="Nu_Tipo_Sexo" readonly>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Red social:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-redsocial"
                                            name="Nu_Como_Entero_Empresa" readonly>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">País:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-pais" readonly>
                                        <select id="select-pais" class="hidden form-control cliente-input"
                                            name="ID_Pais"></select>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Departamento:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-departamento"
                                            readonly>
                                        <select id="select-departamento" class="hidden form-control cliente-input"
                                            name="ID_Departamento"></select>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Provincia:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-provincia" readonly>
                                        <select id="select-provincia" class="hidden form-control cliente-input"
                                            name="ID_Provincia"></select>
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Distrito:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-distrito" readonly>
                                        <select id="select-distrito" class="hidden form-control cliente-input"
                                            name="ID_Distrito"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card px-4 py-4 row-span-1
                        " id="acceso-aula-virtual">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3"><i class="fa fa-graduation-cap"></i> ACCESO AULA VIRTUAL</h6>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Usuario:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-moodle-usuario">
                                    </div>
                                    <div class="form-group flex align-items-center">
                                        <label class="w-[40%] mb-0 form-info-label">Contraseña:</label>
                                        <input type="text" class="form-control cliente-input" id="cliente-moodle-password"
                                            readonly>
                                    </div>
                                </div>
                                <div id="contenedor-boton-usuario"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<!-- Modal Nueva Campaña -->
<div class="modal fade" id="modalClientePagosCoordination" tabindex="-1" role="dialog"
    aria-labelledby="modalClientePagosCoordinationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg adelantos-modal" role="document">
        <div class="modal-content justify-content-center">
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
                            <th>Acciones</th>
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
<div class="modal fade" id="modal-nueva-campana" tabindex="-1" aria-labelledby="modalNuevaCampanaLabel"

    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="form-nueva-campana">
            <input type="hidden" id="id-campana-editar" name="ID_Campana">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaCampanaLabel">Crear Campaña</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  gap-1" id="modal-body-nueva-campana">
                    <div class="month-selector grid md:grid-cols-4 gap-3 px-10"></div>
                    <div class="month-container grid md:grid-cols-2"></div>
                    <!-- <div class="form-group">
                        <label for="fecha-inicio-campana">Fecha de inicio</label>
                        <input type="date" class="form-control" id="fecha-inicio-campana" name="Fe_Inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha-fin-campana">Fecha fin</label>
                        <input type="date" class="form-control" id="fecha-fin-campana" name="Fe_Fin" required>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <div class="w-100 flex flex-row justify-content-between">
                        <div class="flex flex-row gap-2">
                            <div class="flex flex-col">
                                <label>Fecha Inicio:</label>
                                <span id="fecha-inicio-campana-span" class="campana-indicator"></span>
                            </div>
                            <div class="flex flex-col">
                                <label>Fecha Fin:</label>
                                <span id="fecha-fin-campana-span" class="campana-indicator"></span>
                            </div>
                        </div>
                        <div class="flex flex-row gap-2">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn bg-[#FF500B] text-white">Guardar</button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal para registrar pago de curso -->
<div class="modal fade" id="modal-pago-curso" tabindex="-1" aria-labelledby="modalPagoCursoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="form-pago-curso" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h2 class="modal-title" id="modalPagoCursoLabel">Registrar Pago de Curso</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body row">
                    <input type="hidden" id="id-pedido-curso" name="idPedido">
                    <div class="col-md-12 mb-3">
                        <label for="monto" class="form-label">Monto</label>
                        <div class="input-soles-wrapper">
                            <span class="soles-symbol">S/</span>
                            <input type="number" id="monto" name="monto" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="banco" class="form-label">Banco</label>
                        <div class="d-flex gap-3 align-items-center" id="banco-group">
                            <div class="form-check form-check-inline text-center">
                                <input class="form-check-input" type="radio" name="banco" id="banco-bcp" value="BCP"
                                    required>
                                <label class="form-check-label flex" for="banco-bcp">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/Logo_credito.gif"
                                        alt="BCP" style="height:32px;"><br>
                                </label>
                            </div>
                            <div class="form-check form-check-inline text-center">
                                <input class="form-check-input" type="radio" name="banco" id="banco-interbank"
                                    value="INTERBANK" required>
                                <label class="form-check-label flex" for="banco-interbank">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/Interbank_logo.svg"
                                        alt="INTERBANK" style="height:32px;"><br>
                                </label>
                            </div>
                            <div class="form-check form-check-inline text-center">
                                <input class="form-check-input" type="radio" name="banco" id="banco-yape" value="YAPE"
                                    required>
                                <label class="form-check-label flex" for="banco-yape">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/08/Icono_de_la_aplicaci%C3%B3n_Yape.png"
                                        alt="YAPE" style="height:32px;"><br>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" id="fecha_pago" name="fecha" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Voucher</label>
                        <div id="file-upload-pagos-container">
                            <div class="file-upload-box" id="single-file-upload-pagos">
                                <input type="file" id="file-input-pagos" class="file-input" name="voucher"
                                    accept=".pdf, .docx, .xlsx, .xls, .xlsm, .csv, .xlsb, .xltx, .xlt, .png, .jpg, .jpeg">
                                <label for="file-input-pagos" class="file-label d-flex">
                                    <i class="fas fa-upload"></i>
                                    <div class="file-group-text">
                                        <span class="file-text">Selecciona o arrastra tu archivo aquí</span><br>
                                        <span class="file-format">Formatos: .pdf, .docx, .xlsx, .xls, .xlsm, .csv,
                                            .xlsb, .xltx, .xlt, .png, .jpg, .jpeg</span>
                                    </div>
                                    <button class="upload-button upload-button-pagos" type="button">Subir
                                        archivo</button>
                                </label>
                                <div class="file-info-box hidden">
                                    <div class="file-info">
                                        <div class="file-iconic"></div>
                                        <span class="file-name"></span>
                                        <span class="file-size"></span>
                                        <button class="remove-file-button"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn bg-orange text-white">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para imágenes -->
<div class="modal fade" id="image-modal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <img id="image-preview" src="" style="width: 100%;">
            </div>
        </div>
    </div>
</div>
<div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto" id="modal-pago" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg relative w-auto pointer-events-none">
    <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
      <div class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-gray-200 rounded-t-md">
        <h5 class="text-xl font-medium leading-normal text-gray-800" id="pagoModalLabel">Detalles del Pago</h5>
        <button type="button" class="btn-close box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline" data-bs-dismiss="modal" aria-label="Close">×</button>
      </div>
      <div class="modal-body relative p-4">
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <p class="text-sm font-medium text-gray-500">Monto:</p>
            <p id="monto-pago" class="text-lg font-semibold text-gray-800"></p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Banco:</p>
            <p id="banco-pago" class="text-lg font-semibold text-gray-800"></p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Fecha:</p>
            <p id="fecha-pago" class="text-lg font-semibold text-gray-800"></p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Comprobante:</p>
            <div id="voucher-container" class="mt-2">
            </div>
          </div>
          
        </div>
      </div>
      <div class="modal-footer flex flex-shrink-0 flex-wrap items-center justify-end p-4 border-t border-gray-200 rounded-b-md">
        <!--borrar pago-->
        <button type="button" class="btn btn-danger mr-2" id="delete-pago-btn">Borrar Pago</button>
        <button type="button" class="close-modal-pago px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for image preview -->
<div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto" id="modal-image-preview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg relative w-auto pointer-events-none">
    <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
      <div class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-gray-200 rounded-t-md">
        <h5 class="text-xl font-medium leading-normal text-gray-800">Vista previa del comprobante</h5>
        <button type="button" class="btn-close box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline" data-bs-dismiss="modal" aria-label="Close">×</button>
      </div>
      <div class="modal-body relative p-4 flex justify-center">
        <img id="image-preview-comprobante" src="" alt="Comprobante" class="max-w-full h-auto rounded-lg">
      </div>
      <div class="modal-footer flex flex-shrink-0 flex-wrap items-center justify-end p-4 border-t border-gray-200 rounded-b-md">
        <a id="download-btn" href="#" 
        target="_blank" rel="noopener noreferrer"
        class="px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out mr-2" download>Descargar</a>
        <button type="button" class="close-modal-comprobante px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
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

    .bg-orange {
        color: #fff !important;
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

    .dataTables_filter {
        display: none;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .file-group-text {
        font-weight: 400;
    }

    .file-upload-box {
        border: 2px dashed #cccccc;
        padding: 1.5rem;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .file-input {
        display: none;
    }

    .file-label {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.5rem;
    }

    .file-text {
        font-size: 1rem;
        color: #333333;
    }

    .file-format {
        font-size: 0.9rem;
        color: #666666;
        margin-bottom: 1rem;
    }

    .upload-button {
        width: 60%;
        padding: 0.75rem .5rem;
        background-color: #F0F4F9;
        color: #272A30;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: .75rem;
        transition: background-color 0.3s ease;
    }

    .file-info-box {
        border: 1px solid #cccccc;
        padding: 1rem;
        border-radius: 10px;
        background-color: #f9f9f9;
        margin-top: 1rem;
        text-align: left;
    }

    .file-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .file-name {
        font-size: 1rem;
        color: #333333;
    }

    .file-size {
        font-size: 0.9rem;
        color: #666666;
    }

    .input-soles-wrapper {
        position: relative;
    }

    .input-soles-wrapper .soles-symbol {
        position: absolute;
        left: 20px;
        top: 51%;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 1rem;
    }

    .input-soles-wrapper input {
        padding-left: 2.2em;
    }

    .tabs {
        gap: 10px;
        margin-right: 0;
        margin-left: 0;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .tab {
        padding: 0.5em;
        border-radius: 0.5em;
        width: 100%;
        border-width: 2px;
        border-color: #CDCDCD;
        color: #7E7E7E;
        text-align: center;
        cursor: pointer;
        background-color: transparent;
    }

    .tab.active {
        background-color: #FFFFFF;
        color: black;
    }

    .adelantos-modal {
        background-color: #F0F4F9;
    }

    .form-control {
        border: 1px solid #CDCDCD !important;
    }

    .header-indicator {
        background: white;
        padding: 0.3em 2em;
        text-align: center;
        color: black;
        margin-left: 1em;
        border: 1px solid #CDCDCD;
        border-radius: 0.3em;
    }

    .campana-indicator {
        background: white;
        padding: 0.2em 1em;
        height: 2em;
        text-align: center;
        color: black;
        border: 1px solid #CDCDCD;
        border-radius: 0.3em;
    }

    .month-span {
        padding: 0.3em 1em;
        color: black;
        text-align: center;
        border: 1px solid #CDCDCD;
        border-radius: 0.3em;
        display: block;
        width: 100%;
        cursor: pointer;
    }

    .month-span-disabled {
        padding: 0.3em 1em;
        color: #CDCDCD;
        text-align: center;
        border: 1px solid #CDCDCD;
        border-radius: 0.3em;
        display: block;
        width: 100%;
    }

    .month-span:hover {
        background: #FF500B;
        color: white;
    }

    .month-span.selected {
        background: #FF500B;
        color: white;
    }

    /* Estilos para el calendario */


    .month-span {
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        background-color: #f8f9fa;
        transition: all 0.2s;
    }




    .month-span-disabled {
        color: #adb5bd;
        cursor: not-allowed;
        text-decoration: line-through;
    }

    .month-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
    }

    .month-calendar {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        background-color: white;
        width: 100%;
        max-width: 300px;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }

    .day-header {
        text-align: center;
        font-weight: 500;
        font-size: 12px;
        color: #6c757d;
        padding: 5px 0;
    }

    .day-empty {
        aspect-ratio: 1;
    }

    .day-cell {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .day-button {
        width: 100%;
        height: 100%;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 500;
        color: #212529;
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .form-info-label {
        font-weight: 500 !important;
        color: rgb(109, 108, 108) !important;
    }

    .day-button:hover {
        background-color: #f8f9fa;
    }

    .day-today {
        background-color: #FF500B !important;
    }

    .day-selected {
        background-color: #FF500B !important;
        color: white !important;
    }

    .cliente-input {
        border: none !important;
        color: black !important;
    }
</style>