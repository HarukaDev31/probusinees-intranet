<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <main id="section-listar-pedidos">
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
                                    <?php if ($this->user->No_Grupo != "Coordinación") {  ?>
                                        <div class="d-flex align-items-center p-2" style="width:300px;">
                                            <div class="d-flex" style="width:60%">Estado</div>
                                            <div style="width: 200px;">
                                                <select id="txt-ID_Estado_Cotizacion" name="ID_Estado"
                                                    class="form-control input-estado">
                                                    <option value="0" selected>Todos</option>
                                                    <option value="PENDIENTE">PENDIENTE</option>
                                                    <option value="ADELANTO">ADELANTO</option>
                                                    <option value="PAGADO">PAGADO</option>
                                                    <option value="CONFIRMADO">CONFIRMADO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center p-2" style="width:300px;">
                                            <div class="d-flex" style="width:60%">Campañas</div>
                                            <div style="width: 200px;">
                                                <select id="txt-ID_Campana" name="ID_Campana"
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
        <section class="content">
            <div class="container-fluid">
                <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control"
                    value="<?php echo $this->router->method; ?>">
                <div class="row tabs justify-between">
                    <div class="row tabs">
                        <div data-table="consolidado"
                            class="col-12 col-md-4 col-xl-2 max-w-fit tab tab-administracion">
                            Consolidado
                        </div>
                        <div data-table="cursos"
                            class="col-12 col-md-4 col-xl-2 max-w-fit tab tab-administracion">
                            Cursos
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-xl-2 d-flex align-items-center">
                        Importe total:
                        <input id="span-total-importe" type="text" class="ml-2 px-2 py-1 font-weight-bold w-[110px] border border-gray-200 rounded-lg" readonly>
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
                                <th>Estado</th>
                                <th>Importe</th>
                                <th>Pagado</th>
                                <th>Adelantos</th>
                                <th>Acciones</th>
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
                                <th>Estados</th>
                                <th>Importe</th>
                                <th>Pagado</th>
                                <th>Adelanto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
    </main>
    <section class="container mx-auto p-6 hidden" id="payment-tracking-section">
        <!-- Header with Glass Effect -->
         <div class="row mb-4 d-flex justify-content-between">
                <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                    <button type="button" class="bg-white text-black-200 py-2 px-2 rounded-lg btn-block" data-type="html" onclick="backToList()"><i class="fa fa-arrow-left"></i> Regresar</button>
                </div>
                <div class="col-12 col-md-8 d-flex gap-2 justify-content-end">
                    <div class="col-12 col-md-3">
                        <button type="button" id="btn-save-adelantos" class="bg-[#FF500B] text-white py-2 px-3 border border-transparent rounded btn-block btn-reporte" data-type="html" onclick="saveNote()"> Guardar <i class="fa fa-save"></i></button>
                    </div>
                </div>
            </div>

        <div class="py-5  border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex gap-6 text-lg ">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Importe:</span>
                        <span class="text-gray-600 font-semibold" id="total-amount"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Pagado:</span>
                        <span class="text-gray-600 font-semibold" id="paid-amount"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Cards Grid -->
        <div class="p-4 rounded-xl grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="payment-tracking-section-cards">
            <!-- Payment Card 1 -->


        </div>

        <!-- Bottom Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Quotations Section -->
            <div class="bg-white rounded-xl shadow-md p-6 " id="cotizaciones-container">
                <h2 class="text-lg text-gray-800 mb-6 border-b border-gray-200">Cotizaciones</h2>
                <div class="space-y-4 file-lista" id="cotizacion-card">

                </div>
            </div>

            <!-- Notes Section -->
            <div class=" bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-200">
                    <h2 class="text-lg text-gray-800">Nota <i class="fas fa-edit"></i></h2>
                    <!-- button save note -->
                    <button class=" rounded-lg px-4 py-2 transition-all duration-300"
                        onclick="document.getElementById('nota').value = ''">
                        <i class="fas fa-eraser"></i>
                    </button>
                </div>
                <div class="bg-white p-4 rounded-lg" style="height: 250px;">
                    <textarea id="nota" class="form-control" style="height: 200px;"></textarea>
                </div>
            </div>
        </div>
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
<style>
    * {
        font-family: Epilogue;
    }

    p {
        font-size: 14px;
    }
    .dataTables_filter {
        display: none;
        justify-content: flex-end;
    }
    .table.table-hover.dataTable.no-footer tbody {
        background-color: white;
    }
    .table thead th {
        border-bottom: 0px solid #dee2e6 !important;
        border-top: 0px solid #dee2e6;
    }
    tr.odd>td, tr.even>td {
        border-top: 4px solid #f4f6f9;
        border-bottom: 4px solid #f4f6f9;
        vertical-align: middle !important;
        height: 3vh;
    }
    th.sorting_disabled {
    font-weight: normal !important;
    }


    .tabs{
        gap:10px;
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

    .tab:hover {
        background-color: #FFFFFF;
        color: black;
        transition: background-color 0.5s ease;
    }
    .file-upload-box {
    border: 2px dashed #cccccc;
    padding: 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.file-upload-box:hover {
    border-color: #cccccc;
}

.file-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.file-input {
    display: none;
    /* Oculta el input de archivo por defecto */
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

.upload-button:hover {
    background-color: #F0F4F9;
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

/* Lista de archivos seleccionados */
.file-lista {
    margin-top: 20px;
    text-align: left;
}

.file-lista.hidden {
    display: none;
}

.file-lista-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    background-color: #f9f9f9;
}

.file-lista-item svg {
    width: 30px;
    height: 30px;
    margin-right: 10px;
}

.file-lista-item span {
    font-size: 14px;
    color: #333;
    flex-grow: 1;
}


.remove-button {
    background: none;
    border: none;
    cursor: pointer;
    color: #ff4d4d;
    font-size: 1rem;
    transition: color 0.3s ease;
}

.remove-button:hover {
    color: #cc0000;
}

.file-list {
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
}

.file-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.file-list-item:last-child {
    border-bottom: none;
}
.file-icon-container {
    display: flex;
    justify-content: space-between;
    width: -webkit-fill-available;
}
.file-item {
    display: flex
;
    align-items: center;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    background: #f9f9f9;
}
.file-lista {
    margin-top: 20px;
    text-align: left;
}
/* Mejora el menú desplegable de Select2 */
.select2-container--default .select2-results__option {
  display: block;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  color: #374151; /* gray-700 */
  border-radius: 0.375rem;
  background-color: #fff; /* white */
  transition: background 0.2s;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #e0e7ff; /* indigo-100 */
  color: #1e293b; /* slate-800 */
}

.select2-container--default .select2-results__option[aria-selected="true"] {
  background-color: #d1fae5; /* emerald-100 */
  color: #065f46; /* emerald-800 */
}

.select2-container--default .select2-results__option--highlighted[aria-selected="true"] {
  background-color: #a7f3d0; /* emerald-200 */
  color: #065f46;
}

/* Mejora el input cerrado */
.select2-container--default .select2-selection--single {
  cursor: pointer;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb; /* gray-200 */
  padding: 0.5rem 0.75rem;
  height: 42px;
  min-height: 42px;
  background: #fff;
  font-size: 1rem;
  display: flex;
  align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: #374151; /* gray-700 */
  line-height: 42px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 42px;
  right: 10px;
}
.select2-container.select2-container--default.select2-container--open{
    display: block;
    width: auto !important;
    --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.select2-search.select2-search--dropdown.select2-search--hide{
    display: none;
}
.select2-container--default .select2-dropdown{
    border: 0px solid #e5e7eb; /* gray-200 */
}
  
.filename-truncate {
  display: inline-block;
  max-width: 140px;      /* Ajusta el ancho según tu diseño */
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  vertical-align: middle;
}  display: block;

</style>