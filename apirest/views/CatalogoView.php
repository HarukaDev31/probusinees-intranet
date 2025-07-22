<script>
    var currentPrivilege = "<?php echo $this->user->No_Grupo; ?>";
    localStorage.setItem("currentPrivilege", currentPrivilege);
</script>
<div class="content-wrapper">
    <section class="content" id="productListSection">
        <div class="container-fluid">
            <div class="flex items-center
            flex-col flex-md-row flex-lg-row flex-xl-row
            justify-between mb-8 pt-3  border-b-2 border-gray-200 pb-2">
                <h1 class="text-2xl font-bold text-gray-800" id="section-title">Listado de Cotizados</h1>

                <div class="flex flex-col gap-2 flex-md-row flex-xs-row flex-xl-row">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <span class="i-lucide-search w-5 h-5"></span>
                        </span>
                        <input type="text" id="searchInput"
                            placeholder="Buscar por"
                            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <!-- button enviar productos -->

                    </div>
                    <div class="relative dropdown px-1">
                        <button
                            class="bg-white py-2 px-2 border border-transparent rounded btn-block btn-reporte"
                            type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-filter"></i>
                            Filtros
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
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Categoría</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_Categoria" name="ID_Categoria"
                                            class="form-control input-estado">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <!-- Botones -->
                            <div class="d-flex justify-content-around">
                                <button
                                    class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                    style="margin-top: .5rem;" id="cancelar-btn">Cancelar</button>
                                <button
                                    class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                    id="filterBtn">Aplicar</button>
                            </div>
                        </div>
                    </div>

                    <?php if ($this->user->No_Grupo == "CatalogoChina") { ?>
                        <button id="btnAddProduct" class="gap-2 bg-orange-600 hover:bg-orange-700 text-white py-2 px-10 rounded-md transition-colors flex items-center justify-center shadow-sm">
                            <span>Agregar Producto</span>
                            <span class="i-lucide-plus text-lg"></span>
                        </button>
                    <?php } ?>
                    <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                        <button id="btnEnviarProductos" class="btn text-white text-sm bg-orange-600 hover:bg-orange-700 inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            Seleccionar
                        </button>
                        <button id="selectAll" class="hidden btn text-white text-sm bg-orange-600 hover:bg-orange-700 inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                        </button>
                        <button id="btnCancelarEnvio" class="hidden btn text-white text-sm bg-white hover:bg-white-200 border border-orange-600 hover:border-orange-600 inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            Cancelar
                        </button>
                        <button id="btnDeleteProducts" class="hidden btn border-2 border-red-500 gap-2 text-red py-2 px-8 rounded transition-colors flex items-center justify-center shadow-sm">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 3.5H2.55556H15" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M13.4446 3.5V14C13.4446 14.3978 13.2807 14.7794 12.9889 15.0607C12.6972 15.342 12.3016 15.5 11.889 15.5H4.11122C3.69866 15.5 3.303 15.342 3.01128 15.0607C2.71955 14.7794 2.55566 14.3978 2.55566 14V3.5M4.889 3.5V2C4.889 1.60218 5.05289 1.22064 5.34461 0.93934C5.63633 0.658035 6.03199 0.5 6.44455 0.5H9.55566C9.96822 0.5 10.3639 0.658035 10.6556 0.93934C10.9473 1.22064 11.1112 1.60218 11.1112 2V3.5" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M6.44434 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M9.55566 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Eliminar Productos
                        </button>
                        <div id="dynamicCategorizeBtnContainer" class="hidden"></div>
                    <?php } ?>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class=" mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col flex-sm-row align-items-center justify-end gap-2">
                        <span value="" class="text-sm font-medium text-gray-400 mb-1">Ordenar por:</span>
                        <div class="flex gap-2 items-center justify-center mr-4">
                            <select id="sortSelect" class="border border-gray-300 rounded-md px-4 py-2 bg-white focus:ring-2  text-gray-300  focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="recent">Agregados recientemente</option>
                                <option value="nameAZ">Nombre, A a Z</option>
                                <option value="nameZA">Nombre, Z a A</option>
                                <option value="pricemin">Precio bajo al más alto</option>
                                <option value="pricemax">Precio alto al más bajo</option>
                            </select>
                        </div>
                        <!-- ver vista cuadricula ,lista -->
                        <div class="flex gap-1 items-center justify-center">
                            Ver
                            <button id="gridViewBtn" class="rounded-full w-8 h-8 flex items-center justify-center shadow-sm focus:outline-none">
                                <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                    <rect y="4.9082" width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                    <rect x="4.90918" width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                    <rect x="4.90918" y="4.9082" width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                </svg>

                            </button>
                            <button id="listViewBtn" class="rounded-full w-8 h-8 flex items-center justify-center shadow-sm focus:outline-none">
                                <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" width="9" height="2" rx="1" fill="#7E7E7E" />
                                    <rect width="2" height="2" rx="1" fill="#7E7E7E" />
                                    <rect y="3" width="2" height="2" rx="1" fill="#7E7E7E" />
                                    <rect y="6" width="2" height="2" rx="1" fill="#7E7E7E" />
                                    <rect x="3" y="3" width="9" height="2" rx="1" fill="#7E7E7E" />
                                    <rect x="3" y="6" width="9" height="2" rx="1" fill="#7E7E7E" />
                                </svg>

                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4  gap-5 p-5 h-full">

            </div>
            
            <!-- Pagination Controls -->
            <div id="paginationContainer" class="flex items-center justify-between px-5 py-4 bg-white border-t border-gray-200">
                <div class="flex items-center text-sm text-gray-700">
                    <span id="paginationInfo">Mostrando 0 de 0 productos</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="prevPageBtn" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa fa-chevron-left mr-1"></i>
                        Anterior
                    </button>
                    <div id="pageNumbers" class="flex items-center space-x-1">
                        <!-- Los números de página se generarán dinámicamente -->
                    </div>
                    <button id="nextPageBtn" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Siguiente
                        <i class="fa fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- View Product Section -->
    <section id="viewProductSection" class="hidden content">
        <div class="container-fluid">
            <div class="flex items-center justify-between flex-col md:flex-row pt-4">
                <button
                    id="btnBackView"
                    type="button" id="btn-back-documentacion-documentacion" class="py-2 px-4 bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded " data-type="html"><i class="fa fa-arrow-left mr-2"></i> Regresar</button>
                <div class="gap-2 d-flex flex-row  mt-2 md:mt-0" id="actionButtons">
                    <button id="btnDelete" class="border-2 border-red-500 gap-2 text-red py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 3.5H2.55556H15" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.4446 3.5V14C13.4446 14.3978 13.2807 14.7794 12.9889 15.0607C12.6972 15.342 12.3016 15.5 11.889 15.5H4.11122C3.69866 15.5 3.303 15.342 3.01128 15.0607C2.71955 14.7794 2.55566 14.3978 2.55566 14V3.5M4.889 3.5V2C4.889 1.60218 5.05289 1.22064 5.34461 0.93934C5.63633 0.658035 6.03199 0.5 6.44455 0.5H9.55566C9.96822 0.5 10.3639 0.658035 10.6556 0.93934C10.9473 1.22064 11.1112 1.60218 11.1112 2V3.5" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6.44434 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9.55566 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Eliminar Producto</span>
                    </button>
                    <div id="dynamicCategorizeBtnContainerView"></div>
                </div>
            </div>
            <div id="ViewProduct">
                <div class="w-full flex justify-center items-center">
                    <!-- Detalle principal del producto -->
                    <div class="flex flex-col md:flex-row justify-around gap-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-4 w-full md:w-3/4">
                        <!-- Columna de imágenes -->
                        <div class="flex flex-row items-center justify-center w-full">
                            <!-- Miniaturas en columna -->
                            <div class="flex flex-col gap-2 mr-4">
                                <img id="detalleMiniatura1" src="URL_IMAGEN_1" class="w-20 h-20 object-cover rounded border-2 border-orange-400 cursor-pointer" />
                                <img id="detalleMiniatura2" src="URL_IMAGEN_2" class="w-20 h-20 object-cover rounded cursor-pointer" />
                                <!-- Puedes agregar más miniaturas si lo necesitas -->
                                <!-- Miniatura de video -->
                                <video id="detalleMiniaturaVideo1" src="URL_VIDEO_1" class="w-20 h-20 object-cover rounded cursor-pointer" muted></video>
                                <img id="detalleMiniatura3" src="URL_IMAGEN_PRINCIPAL" class="w-20 h-20 object-cover rounded cursor-pointer" />

                            </div>
                            <!-- Imagen principal -->
                            <div class="ml-6 flex items-center justify-center w-fit">
                                <img id="detalleImagenPrincipal" src="URL_IMAGEN_PRINCIPAL" class="mw-100 mh-100 w-[30rem] h-[30rem] object-contain rounded shadow" />
                            </div>
                        </div>
                        <!-- Columna de datos -->
                        <div class="flex flex-col gap-4">
                            <div>
                                <h2 class="font-bold text-lg mb-1"></h2>
                            </div>
                            <!-- Cantidades y precios -->
                            <div id="contenedorPriceRange" class="my-4">

                            </div>
                            <!-- Links -->
                            <div class="flex gap-2 mt-2">
                                <a href="LINK_PRODUCTO" target="_blank" class="border px-4 py-2 rounded text-sm hover:bg-gray-50">Link del producto</a>
                                <a href="LINK_ALIBABA" target="_blank" class="border px-4 py-2 rounded text-sm hover:bg-gray-50">Link alibaba</a>
                            </div>
                            <!-- Datos importantes -->
                            <div class="mt-4 border-t pt-4">
                                <div class="font-semibold mb-2">Datos importantes:</div>
                                <div class="flex flex-col gap-1 text-sm">
                                    <div class="flex align-items-center gap-2">
                                        <svg width="25" height="28" viewBox="0 0 18 11" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-79fc5d5c="">
                                            <path d="M14.7425 0H2.42041C1.08258 0 0 1.08258 0 2.42041V7.70131C0 9.03914 1.08258 10.1217 2.42041 10.1217H14.7425C16.0803 10.1217 17.1629 9.03914 17.1629 7.70131V2.42041C17.1629 1.08258 16.0803 0 14.7425 0ZM2.42041 1.32022H3.35337C3.45019 1.52266 3.5206 1.7427 3.5206 1.98034C3.5206 2.82528 2.82528 3.5206 1.98034 3.5206C1.7427 3.5206 1.52266 3.45019 1.32022 3.35337V2.42041C1.32022 1.81311 1.81311 1.32022 2.42041 1.32022ZM1.32022 7.70131V4.75281C1.53146 4.80562 1.7515 4.84082 1.98034 4.84082C3.55581 4.84082 4.84082 3.55581 4.84082 1.98034C4.84082 1.7515 4.80562 1.53146 4.75281 1.32022H14.7425C15.3498 1.32022 15.8427 1.81311 15.8427 2.42041V5.36891C15.6315 5.31611 15.4114 5.2809 15.1826 5.2809C13.6071 5.2809 12.3221 6.56592 12.3221 8.14139C12.3221 8.37022 12.3573 8.59026 12.4101 8.8015H2.42041C1.81311 8.8015 1.32022 8.30861 1.32022 7.70131ZM14.7425 8.8015H13.8096C13.7127 8.59906 13.6423 8.37903 13.6423 8.14139C13.6423 7.29644 14.3376 6.60112 15.1826 6.60112C15.4202 6.60112 15.6403 6.67154 15.8427 6.76835V7.70131C15.8427 8.30861 15.3498 8.8015 14.7425 8.8015Z" fill="#272A30" data-v-79fc5d5c=""></path><path d="M8.58252 2.64062C7.24469 2.64062 6.16211 3.72321 6.16211 5.06104C6.16211 6.39886 7.24469 7.48145 8.58252 7.48145C9.92035 7.48145 11.0029 6.39886 11.0029 5.06104C11.0029 3.72321 9.92035 2.64062 8.58252 2.64062ZM8.58252 6.16122C7.97522 6.16122 7.48233 5.66834 7.48233 5.06104C7.48233 4.45373 7.97522 3.96085 8.58252 3.96085C9.18982 3.96085 9.68271 4.45373 9.68271 5.06104C9.68271 5.66834 9.18982 6.16122 8.58252 6.16122Z" fill="#272A30" data-v-79fc5d5c=""></path>
                                        </svg>
                                        <b>Sobre el precio</b></div><div class="pl-4 ml-2">Es puesto en Perú, incluye todo.</div>
                                    <div class="flex align-items-center gap-2">
                                        <svg width="25" height="28" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-79fc5d5c=""><path d="M8.58146 0C3.84625 0 0 3.84625 0 8.58146C0 13.3167 3.84625 17.1629 8.58146 17.1629C13.3167 17.1629 17.1629 13.3167 17.1629 8.58146C17.1629 3.84625 13.3167 0 8.58146 0ZM9.24157 15.8075V14.7425C9.24157 14.3816 8.94232 14.0824 8.58146 14.0824C8.2206 14.0824 7.92135 14.3816 7.92135 14.7425V15.8075C4.44476 15.4906 1.67228 12.7182 1.35543 9.24157H2.42041C2.78127 9.24157 3.08052 8.94232 3.08052 8.58146C3.08052 8.2206 2.78127 7.92135 2.42041 7.92135H1.35543C1.67228 4.44476 4.44476 1.67228 7.92135 1.35543V2.42041C7.92135 2.78127 8.2206 3.08052 8.58146 3.08052C8.94232 3.08052 9.24157 2.78127 9.24157 2.42041V1.35543C12.7182 1.67228 15.4906 4.44476 15.8075 7.92135H14.7425C14.3816 7.92135 14.0824 8.2206 14.0824 8.58146C14.0824 8.94232 14.3816 9.24157 14.7425 9.24157H15.8075C15.4906 12.7182 12.7182 15.4906 9.24157 15.8075Z" fill="#272A30" data-v-79fc5d5c=""></path><path d="M11.1161 6.05201L8.74852 7.66268L6.64496 4.57335C6.44252 4.2741 6.02885 4.19489 5.7296 4.39732C5.43035 4.59976 5.35114 5.01343 5.55357 5.31268L8.02679 8.9477C8.15882 9.13253 8.36125 9.23815 8.57249 9.23815C8.69571 9.23815 8.82773 9.20294 8.94215 9.12373L11.8554 7.14339C12.1547 6.94096 12.2339 6.52729 12.0315 6.22804C11.829 5.92878 11.4154 5.84957 11.1161 6.05201Z" fill="#272A30" data-v-79fc5d5c="">
                                            </path>
                                        </svg>
                                        <b>Tiempo de entrega:</b></div><div class="pl-4 ml-2">Se tiene que realizar la importación, la entrega es en 2 meses</div>
                                    <div class="flex align-items-center gap-2">
                                        <svg width="25" height="28" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.7425 7.0412H12.4893C12.6566 6.70674 12.7622 6.33708 12.7622 5.94101V2.42041C12.7622 1.08258 11.6796 0 10.3418 0H6.82116C5.48333 0 4.40075 1.08258 4.40075 2.42041V5.94101C4.40075 6.33708 4.50637 6.70674 4.6736 7.0412H2.42041C1.08258 7.0412 0 8.12378 0 9.46161V13.8624C0 15.2002 1.08258 16.2828 2.42041 16.2828H6.82116C7.51648 16.2828 8.14139 15.9835 8.58146 15.517C9.02154 15.9923 9.64644 16.2828 10.3418 16.2828H14.7425C16.0803 16.2828 17.1629 15.2002 17.1629 13.8624V9.46161C17.1629 8.12378 16.0803 7.0412 14.7425 7.0412ZM5.72097 5.94101V2.42041C5.72097 1.81311 6.21386 1.32022 6.82116 1.32022H10.3418C10.9491 1.32022 11.4419 1.81311 11.4419 2.42041V5.94101C11.4419 6.54832 10.9491 7.0412 10.3418 7.0412H6.82116C6.21386 7.0412 5.72097 6.54832 5.72097 5.94101ZM6.82116 14.9625H2.42041C1.81311 14.9625 1.32022 14.4697 1.32022 13.8624V9.46161C1.32022 8.85431 1.81311 8.36142 2.42041 8.36142H6.82116C7.42846 8.36142 7.92135 8.85431 7.92135 9.46161V13.8624C7.92135 14.4697 7.42846 14.9625 6.82116 14.9625ZM15.8427 13.8624C15.8427 14.4697 15.3498 14.9625 14.7425 14.9625H10.3418C9.73446 14.9625 9.24157 14.4697 9.24157 13.8624V9.46161C9.24157 8.85431 9.73446 8.36142 10.3418 8.36142H14.7425C15.3498 8.36142 15.8427 8.85431 15.8427 9.46161V13.8624Z" fill="#272A30"/>
                                            <path d="M7.70237 5.28125H6.82222C6.46136 5.28125 6.16211 5.5805 6.16211 5.94136C6.16211 6.30222 6.46136 6.60147 6.82222 6.60147H7.70237C8.06323 6.60147 8.36248 6.30222 8.36248 5.94136C8.36248 5.5805 8.06323 5.28125 7.70237 5.28125Z" fill="#272A30"/>
                                            <path d="M3.30198 13.1992H2.42183C2.06097 13.1992 1.76172 13.4985 1.76172 13.8593C1.76172 14.2202 2.06097 14.5194 2.42183 14.5194H3.30198C3.66284 14.5194 3.96209 14.2202 3.96209 13.8593C3.96209 13.4985 3.66284 13.1992 3.30198 13.1992Z" fill="#272A30"/>
                                            <path d="M11.2219 13.1992H10.3418C9.98089 13.1992 9.68164 13.4985 9.68164 13.8593C9.68164 14.2202 9.98089 14.5194 10.3418 14.5194H11.2219C11.5828 14.5194 11.882 14.2202 11.882 13.8593C11.882 13.4985 11.5828 13.1992 11.2219 13.1992Z" fill="#272A30"/>
                                        </svg>
                                        <b>La cantidad:</b> </div><div class="pl-4 ml-2">No se acepta pedidos por unidades, compra mínima de <b>s/3,000</b></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full flex justify-center items-center mt-6">
                    <!-- Atributos clave -->
                    <div id="contenedorAttributes" class="my-4">
                        
                    </div>

                </div>
                <div class="w-full flex justify-center items-center mt-6">
                    <!-- Detalle extendido del producto -->
                    <div id="contenedorProductDetails" class="my-4">

                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Skeleton Loader para viewProductSection -->
    <div id="viewProductSkeleton" class="hidden content w-full flex justify-center items-center animate-pulse">
        <div class="flex flex-col md:flex-row justify-around gap-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-4 w-full md:w-3/4">
            <!-- Columna de imágenes -->
            <div class="flex flex-row gap-4 items-center justify-end w-1/2">
                <div class="flex flex-col gap-2">
                    <div class="bg-gray-200 rounded w-20 h-20"></div>
                    <div class="bg-gray-200 rounded w-20 h-20"></div>
                    <div class="bg-gray-200 rounded w-20 h-20"></div>
                    <div class="bg-gray-200 rounded w-20 h-20"></div>
                </div>
                <div class="bg-gray-200 rounded w-[30rem] h-[30rem] mt-4"></div>
            </div>
            <!-- Columna de datos -->
            <div class="flex flex-col justify-center gap-4 w-1/2">
                <div class="h-8 bg-gray-200 rounded w-2/3 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
                <div class="flex gap-2">
                    <div class="h-8 bg-gray-200 rounded w-32"></div>
                    <div class="h-8 bg-gray-200 rounded w-32"></div>
                </div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
    </div>
    <!-- Add Product Form -->
    <section class="content hidden" id="productoFormSection">
        <div class="container-fluid">

            <div class="flex items-center justify-between flex-col md:flex-row pt-4">
                <button
                    id="btnBackForm"
                    type="button" id="btn-back-documentacion-documentacion" class="py-2 px-4 bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded " data-type="html"><i class="fa fa-arrow-left mr-2"></i> Regresar</button>
                <div class="gap-2 d-flex flex-row  mt-2 md:mt-0" id="actionButtons">
                    <button id="btnDelete" class="border-2 border-red-500 gap-2 text-red py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 3.5H2.55556H15" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.4446 3.5V14C13.4446 14.3978 13.2807 14.7794 12.9889 15.0607C12.6972 15.342 12.3016 15.5 11.889 15.5H4.11122C3.69866 15.5 3.303 15.342 3.01128 15.0607C2.71955 14.7794 2.55566 14.3978 2.55566 14V3.5M4.889 3.5V2C4.889 1.60218 5.05289 1.22064 5.34461 0.93934C5.63633 0.658035 6.03199 0.5 6.44455 0.5H9.55566C9.96822 0.5 10.3639 0.658035 10.6556 0.93934C10.9473 1.22064 11.1112 1.60218 11.1112 2V3.5" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6.44434 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9.55566 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Eliminar Producto</span>


                    </button>
                    <button id="btnSave" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm">
                        <span>Guardar</span>
                        <span class="i-lucide-save ml-2"></span>
                    </button>
                </div>
            </div>

        </div>
        <div class="grid grid-cols-1 lg:grid-cols-8 gap-6 mt-6">
            <!-- Left Column - Images -->
            <div class="lg:col-span-3">
                <div class="">
                    <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Main Image Upload -->
                        <div id="mainImageContainer" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg mb-4 transition-all hover:bg-gray-100 hover:border-blue-300 w-75 mx-auto">
                        </div>
                    </div>
                    <div class=" mt-2 shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Main Image Upload -->
                        <div class="flex md:grid md:grid-cols-3 gap-3 overflow-x-auto md:overflow-x-visible snap-x snap-mandatory">
                            <div id="additionalImage1Container"
                                class="min-w-[70vw] md:min-w-0 snap-center bg-white p-2 rounded-lg additional-image-container relative bg-gray-50 border-2 border-dashed border-gray-300 transition-all hover:bg-gray-100 hover:border-blue-300 h-full flex items-center justify-center"></div>
                            <div id="additionalImage2Container"
                                class="min-w-[70vw] md:min-w-0 snap-center bg-white p-2 rounded-lg additional-image-container relative bg-gray-50 border-2 border-dashed border-gray-300 transition-all hover:bg-gray-100 hover:border-blue-300 h-full flex items-center justify-center"></div>
                            <div id="additionalVideo1Container"
                                class="min-w-[70vw] md:min-w-0 snap-center bg-white p-2 rounded-lg video-container relative bg-gray-50 border-2 border-dashed border-gray-300 transition-all hover:bg-gray-100 hover:border-blue-300 h-full flex items-center justify-center"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Column - Product Details Form -->
            <div class="lg:col-span-3">

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden " <?php if ($this->user->No_Grupo != "CatalogoChina") echo 'style="pointer-events:none"'; ?>>
                    <div class="p-4 border-b border-gray-100 flex items-center">
                        <h2 class="text-lg font-medium text-gray-800">Detalles del Producto</h2>
                        <div class="ml-2 tooltip" data-tooltip="Información obligatoria para guardar el producto">
                            <span class="i-lucide-info text-blue-500 w-5 h-5"></span>
                        </div>
                    </div>

                    <div class="px-4 py-3">
                        <form id="productForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <!-- Nombre -->
                                <div class="form-group col-span-1 md:col-span-2">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="nombre" class="mb-1 sm:mb-0 sm:w-1/6 text-sm font-medium text-gray-400 flex items-center">
                                            Nombre <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-5/6">
                                            <input type="text" id="nombre" name="nombre"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Este campo es obligatorio.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Precio -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="precio" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Precio <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                                <input type="text" id="precio" name="precio"
                                                    class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8"
                                                    placeholder="0.00" required>
                                            </div>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese solo valores numéricos.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profit (conditionally shown) -->
                                <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                                    <div class="form-group col-span-1">
                                        <div class="flex flex-col sm:flex-row">
                                            <label for="profit" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                                Profit <span class="text-red-500">*</span>
                                            </label>
                                            <div class="w-full sm:w-2/3">
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                                    <input type="text" id="profit"
                                                        class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8"
                                                        placeholder="0.00" disabled value="3.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- MOQ -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="moq" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            MOQ <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="moq" name="moq" min="1"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Qty x box -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="qtyXbox" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Qty x box <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="qtyXbox" name="qtyXbox" min="1"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cbm x box -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="cbmXbox" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Cbm x box <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="cbmXbox" name="cbmXbox" min="0" step="0.001"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Días Entrega -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="diasEntrega" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Días Entrega <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="diasEntrega" name="diasEntrega" min="1"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="delivery" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Delivery <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                                <input type="text" id="delivery" name="delivery"
                                                    class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8"
                                                    placeholder="0.00" required>
                                            </div>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese solo valores numéricos.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Colores -->
                                <div class="form-group col-span-1 md:col-span-2">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="colores" class="mb-1 sm:mb-0 sm:w-1/6 text-sm font-medium text-gray-400 flex items-center">
                                            Colores
                                        </label>
                                        <div class="w-full sm:w-5/6">
                                            <input type="text" id="colores" name="colores"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- Notas -->
                                <div class="form-group col-span-1 md:col-span-2">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="notas" class="mb-1 sm:mb-0 sm:w-1/6 text-sm font-medium text-gray-400 flex items-center">
                                            Notas
                                        </label>
                                        <div class="w-full sm:w-5/6">
                                            <textarea id="notas" name="notas" rows="4"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Provider Data -->

            </div>
            <div class="lg:col-span-2">



                <!-- Provider Data -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden ">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-medium text-gray-800">Datos del Proveedor</h2>
                    </div>

                    <div class="p-4">
                        <form id="providerForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- WeChat / Phone -->
                                <div class="form-group col-span-2 flex flex-row gap-1">
                                    <label for="wechatPhone" class="block text-sm font-medium text-gray-400 mb-1 w-1/2">
                                        WeChat / Phone
                                    </label>
                                    <input type="text" id="wechatPhone" name="wechatPhone" required
                                        class=" w-1/2 form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Este campo es obligatorio.</p>

                                </div>

                                <!-- Contact Card -->
                                <div class="form-group col-span-2">
                                    <label for="contactCard" class="block text-sm font-medium text-gray-400 mb-1">
                                        Tarjeta de contacto
                                    </label>
                                    <div id="contactCardContainer" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg transition-all hover:bg-gray-100 hover:border-blue-300">

                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- both columns -->
            <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                <div class="lg:col-span-3"></div>
                <div class="lg:col-span-5">
                    <div class=" bg-white rounded-lg shadow-lg ">
                        <h1 class="text-2xl font-bold mb-6 p-6  border-b-2 border-gray-300">Costos de importación</h1>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 p-6">
                            <div class="md:grid-cols-1 grid grid-cols-1 ">
                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Precio USD:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="precioUSD" class="block w-full text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" disabled>
                                    </div>
                                </div>
                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Total USD:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="totalUSD" class="block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" disabled>
                                    </div>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Total CBM:</label>
                                    <input type="number" id="totalCBM" class="block w-full md:w-1/3 text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" step="0.01" disabled>
                                </div>
                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Servicio Impo:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="servicioImpo" class="border-gray-300 block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="350.00" readonly>
                                    </div>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-1/3">Arancel:</label>
                                    <select id="arancel" class="block w-full md:w-2/3 text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        <option value="0.00">0%</option>
                                        <option value="6.00">6%</option>
                                        <option value="11.00">11%</option>
                                    </select>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">IGV:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <input type="number" id="igv" class="border-gray-300 block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="18" readonly>
                                        <span class="absolute bottom-2 left-0 flex items-center pl-3 text-gray-500">%</span>
                                    </div>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Antidumping:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="antidumping" class="border-gray-300 block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="0">
                                    </div>
                                </div>


                                <div class="form-group flex md:flex-row flex-col gap-1">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Percepción:</label>
                                    <input type="number" id="percepcion" class="block w-full md:w-1/3 text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="3.5">
                                </div>
                            </div>
                            <div class="md:grid-cols-1 grid grid-cols-1 text-start ">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="border border-1 bg-gray-300 border-gray-200 text-center">Calculo de base imponible</th>
                                            <th class="border border-1 bg-gray-300 border-gray-200 text-center">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Valor de carga</td>
                                            <td class="border border-1 border-gray-300 text-center" id="valorCarga">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Flete</td>
                                            <td class="border border-1 border-gray-300 text-center" id="flete">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Seguro</td>
                                            <td class="border border-1 border-gray-300 text-center" id="seguro">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Valor cif</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="valorCIF">$ 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="border border-1 bg-gray-300 border-gray-200 text-center">Calculo de tributos</th>
                                            <td class="border border-1 bg-gray-300 border-gray-200 text-center">Monto</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Ad valorem </td>
                                            <td class="border border-1 border-gray-30 text-center" id="adValorem">$ 0.00</td>

                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">IGV</td>
                                            <td class="border border-1 border-gray-300 text-center" id="igvTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">IPM</td>
                                            <td class="border border-1 border-gray-300 text-center" id="ipmTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Antidumping</td>
                                            <td class="border border-1 border-gray-300 text-center" id="antidumpingTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Sub Total</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="subtotal">$ 0.00</td>
                                        </tr>

                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Percepción</td>
                                            <td class="border border-1 border-gray-300 text-center" id="percepcionTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Total</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="total">$ 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!--table with only head for costo en destino-->
                                <!-- <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="border bg-gray-300 text-center">Costo en destino</th>
                                            <td class="border  bg-gray-300" id="costoDestino">$ 0.00</td>
                                        </tr>
                                    </thead>
                                </table> -->
                            </div>
                            <div class="md:grid-cols-1 grid grid-cols-1 text-start ">
                                <!--table base imponible-->
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="border border-1 bg-gray-500 text-white border-gray-200 text-center">Calculo de base imponible</th>
                                            <td class="border border-1 bg-gray-500 text-white border-gray-200 text-center">Monto</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Valor carga(pago China)</td>
                                            <td class="border border-1 border-gray-300 text-center" id="valorCargaResumen">$ 0.00</td>

                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Servicio trading(pago China)</td>
                                            <td class="border border-1 border-gray-300 text-center" id="servicioTrading">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Servicio importación</td>
                                            <td class="border border-1 border-gray-300 text-center" id="servicioImportacion">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Impuestos</td>
                                            <td class="border border-1 border-gray-300 text-center" id="impuestos">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Sub Total</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="montoTotal">$ 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="border bg-gray-500 text-white text-center">Costo unitario de importación </th>
                                            <td class="border  bg-gray-500 text-white" id="costoUnitarioUSD">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <th class="border bg-gray-500 text-white text-center">Costo unitario de importación </th>
                                            <td class="border  bg-gray-500 text-white" id="costoUnitarioPEN">S/ 0.00</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 p-6">

                    </div>
                </div>
        </div>
</div>
<?php } ?>
</div>

</section>


<template id="skeletonTemplate">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden animate-pulse">
        <div class="aspect-square bg-gray-200"></div>
        <div class="p-4">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="flex items-center justify-between mb-4">
                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
            </div>
            <div class="border-t border-gray-100 pt-4 mt-4">
                <div class="h-8 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>
</template>
<template id="productTemplate">

<?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
    <div class="card view-btn w-100 rounded-lg cursor-pointer flex flex-col w-full my-0 shadow-sm border border-gray-200 overflow-hidden hover:shadow-md hover:bg-gray-100 transition-shadow group relative"> <!--status badge-->
        <!--checkbox-->
        <input type="checkbox" class="checkbox hidden h-6 w-6 absolute top-2 right-2 
        accent-orange-500
        cursor-pointer checkbox" style="z-index: 100;" />
<?php } else { ?>
    <div class="card edit-btn  w-100 rounded-lg cursor-pointer flex flex-col w-full my-0 shadow-sm border border-gray-200 overflow-hidden hover:shadow-md hover:bg-gray-100 transition-shadow group relative"> <!--status badge-->
        <!--checkbox-->
        <input type="checkbox" class="checkbox hidden h-6 w-6 absolute top-2 left-2 
        accent-orange-500
        cursor-pointer checkbox" style="z-index: 100;" />
        <div class="absolute bottom-2 right-2 text-white text-xs badge font-semibold px-2 py-1 rounded-full" style="z-index: 200;"></div>

        <!--dropdown menu trigger-->
        <div class="absolute top-0 right-2 z-30">
            <button class="dropdown-trigger bg-white hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center shadow-sm focus:outline-none">
                <svg width="20" height="5" viewBox="0 0 11 3" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="9.76901" cy="1.15385" r="1.15385" transform="rotate(90 9.76901 1.15385)" fill="#D9D9D9" />
                    <circle cx="5.46139" cy="1.15385" r="1.15385" transform="rotate(90 5.46139 1.15385)" fill="#D9D9D9" />
                    <circle cx="1.15377" cy="1.15385" r="1.15385" transform="rotate(90 1.15377 1.15385)" fill="#D9D9D9" />
                </svg>

            </button>

            <!--dropdown menu content-->
            <div class="dropdown-menu2 absolute right-0 mt-1 w-80 bg-white rounded-md shadow-lg overflow-hidden z-40 hidden">
                <div class="py-1">
                    <button class="edit-btn w-full text-left px-4 py-2 text-sm text-gray-700 w-full hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar
                    </button>
                    <button class="delete-btn w-full text-left px-4 py-2 text-sm text-gray-700 w-full hover:bg-red-50 text-red-600 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                        Eliminar producto
                    </button>
                </div>
            </div>
        </div>

<?php } ?>
        <div class="card-content flex flex-col gap-2 w-full p-4 justify-between h-full">
            <div class="card-img relative w-full aspect-square rounded-t-lg overflow-hidden">
                <img src="" alt="" class="w-full h-full object-cover">
                <div class="absolute inset-0 "></div>
            </div>

            
            <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                <div class="px-2 pb-4 pt-2 w-full flex flex-col gap-2 justify-center">
                    <h3 class="mb-0 font-bold nameProducto text-md"></h3>
                    <!--badge category_name-->
                    <div class="flex flex-col">
                        <span class="badge-category text-gray-800 text-xs font-semibold"></span>
                        <span class="text-gray-500 text-sm MOQ"></span>
                        <span class="text-black-600 text-lg precioPeru"></span>
                    </div>
            <?php } else { ?>
                <div class="px-2 pb-4 pt-2 w-full flex flex-col gap-2">
                    <span class="text-gray-400 text-sm codProducto"></span>
                    <!--badge category_name-->
                    <span class=" badge-category text-gray-800 text-xs  font-semibold "></span>
                    <h3 class="mb-0 font-bold text-md nameProducto"></h3>
                    <div class="flex-col flex items-start justify-between text-sm py-1">
                        <span class="text-gray-600 text-md precioChina"></span>
                        <span class="text-black-600 text-md precioPeru"></span>
                        <span class="text-black-500 text-md precioUSD"></span>
                        <span class="text-gray-500 text-md MOQ"></span>
                    </div>
                    <button class="btn btn-primary btnTienda mx-auto mt-4 bg-blue-50 text-blue-600 py-2 rounded-md transition-colors flex items-center justify-center gap-1 w-full">
                        <span>PASAR A TIENDA</span>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
</template>
<div id="noResults" class="hidden col-span-2 lg:col-span-4 h-full">
    <div class=" rounded-lg  overflow-hidden flex flex-col items-center gap-10 justify-center h-full">
        <svg width="140" height="217" viewBox="0 0 74 107" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="40" width="66" height="66" rx="9" stroke="#D9D9D9" stroke-width="2" />
            <mask id="path-2-inside-1_3747_3546" fill="white">
                <path d="M71 97C71 102.523 66.5228 107 61 107H13C7.47715 107 3 102.523 3 97V75H27V87.1377H48V75H71V97Z" />
            </mask>
            <path d="M61 107V109V107ZM13 107V109V107ZM3 75V73H1V75H3ZM27 75H29V73H27V75ZM27 87.1377H25V89.1377H27V87.1377ZM48 87.1377V89.1377H50V87.1377H48ZM48 75V73H46V75H48ZM71 75H73V73H71V75ZM71 97H69C69 101.418 65.4183 105 61 105V107V109C67.6274 109 73 103.627 73 97H71ZM61 107V105H13V107V109H61V107ZM13 107V105C8.58172 105 5 101.418 5 97H3H1C1 103.627 6.37258 109 13 109V107ZM3 97H5V75H3H1V97H3ZM3 75V77H27V75V73H3V75ZM27 75H25V87.1377H27H29V75H27ZM27 87.1377V89.1377H48V87.1377V85.1377H27V87.1377ZM48 87.1377H50V75H48H46V87.1377H48ZM48 75V77H71V75V73H48V75ZM71 75H69V97H71H73V75H71Z" fill="#D9D9D9" mask="url(#path-2-inside-1_3747_3546)" />
            <path d="M37.0195 2V21.8129" stroke="#DFDFDE" stroke-width="3.30215" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M2 16.5117L16.0176 30.5294" stroke="#DFDFDE" stroke-width="3.30215" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M58.0212 30.5294L72.0389 16.5117" stroke="#DFDFDE" stroke-width="3.30215" stroke-linecap="round" stroke-linejoin="round" />
        </svg>

        <p class="text-gray-500">Aún no hay productos ingresados</p>
    </div>
</div>
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacion" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacionLabel">Confirmación de envio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-5 text-center">Por favor seleccionar la categoría para actualizar la web Pro Business.</p>
                <!-- Modal body content tailwind input dropdowns seleccionar la categoria label-->
                <div class="row px-5 mb-5 align-items-center">
                    <div class="col-10">
                        <label for="categoriaProductos" class="block text-sm font-medium text-gray-700">Selecciona la categoría</label>
                        <select id="categoriaProductos" name="categoria" class="form-select block w-full h-50 mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">

                        </select>
                    </div>
                    <!--button to add new category-->
                    <div class="col-2">
                        <button type="button" class="btn bg-green-400 text-white px-3 py-1 rounded-md hover:bg-green-300" data-toggle="modal" data-target="#modalNuevaCategoria">
                            +
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer row">
                <button type="button" class="btn bg-white col-5
                border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md mr-2" data-dismiss="modal">
                    <span class="">Cancelar</span>
                </button>
                <button type="button" class="btn bg-[#13d680] text-white col-6" id="btnGuardarCategoriaProductos">
                    <span class="ml-2">Si, Enviar</span>
                </button>

            </div>
        </div>
    </div>
</div>
<!--modal create new category-->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-labelledby="modalNuevaCategoria" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevaCategoriaLabel">Crear nueva categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-5">Ingresa el nombre de la nueva categoría</p>
                <!-- Modal body content tailwind input dropdowns seleccionar la categoria label-->
                <div class="row px-5">
                    <div class="col-12">
                        <label for="nuevaCategoria" class="block text-sm font-medium text-gray-700">Nombre de la nueva categoría</label>
                        <input type="text" id="nuevaCategoria" name="nuevaCategoria" class="form-input block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">

                    </div>
                </div>
            </div>
            <div class="modal-footer row">
                <button type="button" class="btn bg-white col-5
                border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md mr-2" data-dismiss="modal">
                    <span class="">Cancelar
                    </span>
                </button>
                <button type="button" class="btn bg-green-400 text-white col-6" id="btnCrearCategoria" data-dismiss="modal">
                    <span class="ml-2">Crear</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shake {
        0% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        50% {
            transform: translateX(5px);
        }

        75% {
            transform: translateX(-5px);
        }

        100% {
            transform: translateX(0);
        }
    }

    /* Custom utilities */
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    .shake {
        animation: shake 0.5s ease-in-out;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* CSS for Lucide icons */
    [class^="i-lucide-"] {
        display: inline-block;
        width: 1em;
        height: 1em;
        background-color: currentColor;
        mask-repeat: no-repeat;
        mask-size: contain;
        mask-position: center;
    }

    .i-lucide-plus {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14'%3E%3C/path%3E%3Cpath d='M12 5v14'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-search {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cpath d='m21 21-4.3-4.3'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-filter {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 3H2l8 9.46V19l4 2v-8.54L22 3z'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-edit {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'%3E%3C/path%3E%3Cpath d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-trash-2 {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 6h18'%3E%3C/path%3E%3Cpath d='M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6'%3E%3C/path%3E%3Cpath d='M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2'%3E%3C/path%3E%3Cline x1='10' x2='10' y1='11' y2='17'%3E%3C/line%3E%3Cline x1='14' x2='14' y1='11' y2='17'%3E%3C/line%3E%3C/svg%3E");
    }

    .i-lucide-check-circle {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 11.08V12a10 10 0 1 1-5.93-9.14'%3E%3C/path%3E%3Cpolyline points='22 4 12 14.01 9 11.01'%3E%3C/polyline%3E%3C/svg%3E");
    }

    .i-lucide-alert-circle {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'%3E%3C/circle%3E%3Cline x1='12' x2='12' y1='8' y2='12'%3E%3C/line%3E%3Cline x1='12' x2='12.01' y1='16' y2='16'%3E%3C/line%3E%3C/svg%3E");
    }

    .i-lucide-arrow-left {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 18l-6-6 6-6'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-save {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M19 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12l4 4v10a2 2 0 0 1-2 2z'%3E%3C/path%3E%3Cpolyline points='17 21 17 13 7 13 7 21'%3E%3C/polyline%3E%3Cpolyline points='7 3 7 8 15.5 8'%3E%3C/polyline%3E%3C/svg%3E");
    }

    .i-lucide-x {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' x2='6' y1='6' y2='18'%3E%3C/line%3E%3Cline x1='6' x2='18' y1='6' y2='18'%3E%3C/line%3E%3C/svg%3E");
    }

    .i-lucide-file {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-send {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 2 11 13'%3E%3C/path%3E%3Cpath d='M22 2l-4 20-2-8-8-2L2 2l20 20'%3E%3C/path%3E%3C/svg%3E");
    }

    #productGrid {
        height: auto;
        min-height: 40vh;
        gap: 10px;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    body {
        font-family: 'Epilogue', sans-serif;
    }

    .dropdown-trigger {
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        /* Elimina el resaltado azul en iOS */
    }

    .dropdown-menu {
        -webkit-overflow-scrolling: touch;
        /* Mejor scrolling en iOS */
    }
    .product-image{
        object-fit: cover;
        border-radius: 0.5rem;
    }
</style>