<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de productos importados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .action-btn {
            transition: all 0.2s ease;
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-history text-blue-600 mr-3"></i>
                        Historial de productos importados
                    </h1>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Buscador -->
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Buscar por..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <!-- Filtros -->
                        <button id="filterBtn" class="flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-filter mr-2 text-gray-500"></i>
                            Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Filtros desplegables -->
            <div id="filterPanel" class="hidden px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todas</option>
                            <option value="tecnologia">Tecnología</option>
                            <option value="herramientas">Herramientas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todas</option>
                            <option value="acme">Acme</option>
                            <option value="samsung">Samsung</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="0-100">$0 - $100</option>
                            <option value="100-500">$100 - $500</option>
                            <option value="500+">$500+</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Aplicar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header de tabla -->
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                    <div class="col-span-1">N.</div>
                    <div class="col-span-2">Nombre comercial</div>
                    <div class="col-span-1">Foto</div>
                    <div class="col-span-2">Características</div>
                    <div class="col-span-1">Rubro</div>
                    <div class="col-span-1">T. Producto</div>
                    <div class="col-span-1">Unidad Com.</div>
                    <div class="col-span-1">Precio Exw</div>
                    <div class="col-span-1">Subsidiaria</div>
                    <div class="col-span-1">Acciones</div>
                </div>
            </div>

            <!-- Productos -->
            <div id="productsList" class="divide-y divide-gray-200">
                <!-- Producto 1 -->
                <div class="product-card p-6 hover:bg-gray-50 transition-colors">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <!-- N. -->
                        <div class="col-span-1">
                            <span class="text-sm font-medium text-gray-900">1</span>
                        </div>
                        
                        <!-- Nombre comercial -->
                        <div class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-900">CAJA DE MANTENIMIENTO PARA IMPRESO</h3>
                        </div>
                        
                        <!-- Foto -->
                        <div class="col-span-1">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                <img src="https://via.placeholder.com/64x64/374151/ffffff?text=IMG" 
                                     alt="Producto" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        
                        <!-- Características -->
                        <div class="col-span-2">
                            <div class="text-xs text-gray-600 space-y-1">
                                <div><span class="font-medium">Material:</span> Acero</div>
                                <div><span class="font-medium">Marca:</span> SAM</div>
                                <div><span class="font-medium">Tamaño:</span> 50*30*30</div>
                                <div><span class="font-medium">Sistema:</span> Barra de nylon</div>
                            </div>
                        </div>
                        
                        <!-- Rubro -->
                        <div class="col-span-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Tecnología
                            </span>
                        </div>
                        
                        <!-- T. Producto -->
                        <div class="col-span-1">
                            <span class="text-sm text-gray-600">Jaula</span>
                        </div>
                        
                        <!-- Unidad Com. -->
                        <div class="col-span-1">
                            <span class="text-sm text-gray-600">Piezas</span>
                        </div>
                        
                        <!-- Precio Exw -->
                        <div class="col-span-1">
                            <span class="text-sm font-medium text-green-600">$10.5</span>
                        </div>
                        
                        <!-- Subsidiaria -->
                        <div class="col-span-1">
                            <span class="text-xs text-gray-500">500190600</span>
                            <div class="text-xs text-gray-400">$1 - 25</div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="col-span-1">
                            <div class="flex space-x-2">
                                <button class="action-btn w-8 h-8 bg-orange-500 hover:bg-orange-600 text-white rounded-full flex items-center justify-center" 
                                        title="Editar"
                                        onclick="editProduct(1)">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="action-btn w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center" 
                                        title="Ver detalles"
                                        onclick="viewProduct(1)">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                <button class="action-btn w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center" 
                                        title="Eliminar"
                                        onclick="deleteProduct(1)">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producto 2 -->
                <div class="product-card p-6 hover:bg-gray-50 transition-colors">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-1">
                            <span class="text-sm font-medium text-gray-900">2</span>
                        </div>
                        <div class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-900">CAJA DE MANTENIMIENTO PARA IMPRESO</h3>
                        </div>
                        <div class="col-span-1">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                <img src="https://via.placeholder.com/64x64/374151/ffffff?text=IMG" 
                                     alt="Producto" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-xs text-gray-600 space-y-1">
                                <div><span class="font-medium">Material:</span> Acero</div>
                                <div><span class="font-medium">Marca:</span> SAM</div>
                                <div><span class="font-medium">Tamaño:</span> 50*30*30</div>
                                <div><span class="font-medium">Sistema:</span> Barra de nylon</div>
                            </div>
                        </div>
                        <div class="col-span-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Tecnología
                            </span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-sm text-gray-600">Rectángulo</span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-sm text-gray-600">Piezas</span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-sm font-medium text-green-600">$10.5</span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-xs text-gray-500">500190600</span>
                            <div class="text-xs text-gray-400">$2 - 25</div>
                        </div>
                        <div class="col-span-1">
                            <div class="flex space-x-2">
                                <button class="action-btn w-8 h-8 bg-orange-500 hover:bg-orange-600 text-white rounded-full flex items-center justify-center" 
                                        title="Editar"
                                        onclick="editProduct(2)">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="action-btn w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center" 
                                        title="Ver detalles"
                                        onclick="viewProduct(2)">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producto 3 -->
                <div class="product-card p-6 hover:bg-gray-50 transition-colors">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-1">
                            <span class="text-sm font-medium text-gray-900">3</span>
                        </div>
                        <div class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-900">CAJA DE MANTENIMIENTO PARA IMPRESO</h3>
                        </div>
                        <div class="col-span-1">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                <img src="https://via.placeholder.com/64x64/374151/ffffff?text=IMG" 
                                     alt="Producto" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-xs text-gray-600 space-y-1">
                                <div><span class="font-medium">Material:</span> Acero</div>
                                <div><span class="font-medium">Marca:</span> SAM</div>
                                <div><span class="font-medium">Tamaño:</span> 50*30*30</div>
                                <div><span class="font-medium">Sistema:</span> Barra de nylon</div>
                            </div>
                        </div>
                        <div class="col-span-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Herramientas
                            </span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-sm text-gray-600">Libra</span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-sm text-gray-600">Kg</span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-sm font-medium text-green-600">$10.5</span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-xs text-gray-500">500190600</span>
                            <div class="text-xs text-gray-400">$2 - 25</div>
                        </div>
                        <div class="col-span-1">
                            <div class="flex space-x-2">
                                <button class="action-btn w-8 h-8 bg-orange-500 hover:bg-orange-600 text-white rounded-full flex items-center justify-center" 
                                        title="Editar"
                                        onclick="editProduct(3)">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="action-btn w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center" 
                                        title="Ver detalles"
                                        onclick="viewProduct(3)">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-700">
                Mostrando <span class="font-medium">1</span> a <span class="font-medium">3</span> de <span class="font-medium">3</span> productos
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Anterior
                </button>
                <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg">
                    1
                </button>
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle filtros
            $('#filterBtn').on('click', function() {
                $('#filterPanel').toggleClass('hidden');
            });

            // Búsqueda en tiempo real
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.product-card').each(function() {
                    const productText = $(this).text().toLowerCase();
                    if (productText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });

        // Funciones de acciones
        function editProduct(id) {
            console.log('Editando producto:', id);
            alert('Función de editar producto ' + id);
        }

        function viewProduct(id) {
            console.log('Viendo producto:', id);
            alert('Función de ver detalles del producto ' + id);
        }

        function deleteProduct(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                console.log('Eliminando producto:', id);
                alert('Producto ' + id + ' eliminado');
            }
        }
    </script>
</body>
</html> 