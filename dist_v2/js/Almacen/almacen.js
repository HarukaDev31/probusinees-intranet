let url, sort_col = "0", sort_type = "desc";
let containerAlmacen = $(".table-almacen");
let containerInspection = $(".table-inspection");
let tableAlmacen = $("#table-almacen");
let tableInspection = $("#table-inspection");
let driveContainer = $("#drive-container");
let driveFiles = [];
let idExcel = 0;
let idDetalle = 0;
let idOrder = 0;
$(function () {
  containerInspection.hide();
  driveContainer.hide();
  url = base_url + "Almacen/Trading/getAlmacen";
  tableAlmacen = tableAlmacen.DataTable({
    dom:
      "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons: [
      {
        extend: "excel",
        text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
        titleAttr: "Excel",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "pdf",
        text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
        titleAttr: "PDF",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "colvis",
        text: '<i class="fa fa-ellipsis-v"></i> Columnas',
        titleAttr: "Columnas",
        exportOptions: {
          columns: ":visible",
        },
      },
    ],
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: false,
    serverSide: false,
    pagingType: "full_numbers",
    oLanguage: {
      sInfo: "Mostrando (_START_ - _END_) total de registros _TOTAL_",
      sLengthMenu: "_MENU_",
      sSearch: "Buscar por: ",
      sSearchPlaceholder: "",
      sZeroRecords: "No se encontraron registros",
      sInfoEmpty: "No hay registros",
      sLoadingRecords: "Cargando...",
      sProcessing: "Procesando...",
      oPaginate: {
        sFirst: "<<",
        sLast: ">>",
        sPrevious: "<",
        sNext: ">",
      },
    },
    order: [[sort_col, "desc"]],
    ajax: {
      url: url,
      type: "POST",
      dataType: "JSON",
      data: function (data) {
        (data.Filtro_Fe_Inicio = ParseDateString(
          $("#txt-Fe_Inicio").val(),
          "fecha",
          "/"
        )),
        (data.Filtro_Fe_Fin = ParseDateString(
          $("#txt-Fe_Fin").val(),
          "fecha",
          "/"
        ));
      data.Filtro_Estado = $("#txt-ID_Estado").val();
    
      },
      complete: function () {
        $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
      },
    },
    columnDefs: [
      {
        targets: "no-hidden",
        visible: false,
      },
      {
        className: "text-center",
        targets: "no-sort",
        orderable: false,
      },
    ],
    lengthMenu: [
      [10, 100, 1000, -1],
      [10, 100, 1000, "Todos"],
    ],
  })


});

$(".input-report").datepicker({
  autoclose: true,
  startDate: new Date("2023", "10", "01"),
  todayHighlight: true,
  dateFormat: "dd/mm/yyyy",
  format: "dd/mm/yyyy",
});
$('#btn-html_reporte').on('click', function () {
  reload_table_almacen();
});
  /**GDrive functions  */
  const $dragDropContainer = $('#drag-drop-container');
  const $fileInput = $('#file-input');
  const $uploadBtn = $('#upload-btn');
  const $fileGrid = $('#file-grid');
  const $searchInput = $('#search-input');
  const $fileList = $('#file-list');
  const $backBtn = $('#back-btn');
  const iconMap = {
    'application/pdf': '<i class="fas fa-file-pdf w-12 h-12 text-red-400"></i>',
    'image/jpeg': '<i class="fas fa-file-image w-12 h-12 text-blue-400"></i>',
    'image/png': '<i class="fas fa-file-image w-12 h-12 text-blue-400"></i>',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
      '<i class="fas fa-file-word w-12 h-12 text-blue-600"></i>'
  };
  const pendingFiles = [];
  // Initial files
  // const files = [
  //   { id: 1, name: 'Proyecto.docx', type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', size: '2.5 MB', lastModified: '2023-11-26' },
  //   { id: 2, name: 'Presentacion.pdf', type: 'application/pdf', size: '1.2 MB', lastModified: '2023-11-25' },
  //   { id: 3, name: 'Imagen.jpg', type: 'image/jpeg', size: '4.7 MB', lastModified: '2023-11-24' }
  // ];

  // Render initial files
  renderFileGrid(driveFiles);

  // Toggle drag-drop area
  $uploadBtn.on('click', function () {
    $fileGrid.toggleClass('hidden');
    $fileInput.click();
  });

  // Prevent default drag behaviors
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    $fileGrid.on(eventName, function (e) {
      e.preventDefault();
      e.stopPropagation();
    });
  });

  // Drag enter - add visual cue
  $fileGrid.on('dragenter', function () {
    $(this).addClass('drag-over');
  });

  // Drag leave - remove visual cue
  $fileGrid.on('dragleave', function (e) {
    // Only remove if mouse has left the entire container
    if (e.originalEvent.clientX <= 0 ||
      e.originalEvent.clientY <= 0 ||
      e.originalEvent.clientX >= $(this).width() ||
      e.originalEvent.clientY >= $(this).height()) {
      $(this).removeClass('drag-over');
    }
  });

  // Drop event
  $fileGrid.on('drop', function (e) {
    $(this).removeClass('drag-over');
    handleFiles(e.originalEvent.dataTransfer.files);
  });

  // Click to select files
  $fileInput.on('change', function () {
    handleFiles(this.files);
  });

  // Search functionality
  $searchInput.on('input', function () {
    const searchTerm = $(this).val().toLowerCase();
    const filteredFiles = driveFiles.filter(file =>
      file.name.toLowerCase().includes(searchTerm)
    );
    renderFileGrid(filteredFiles);
  });
  $backBtn.on('click', function () {
    closePagos();

  });
  // Handle file processing
  function handleFiles(newFiles) {
    // Convert FileList to Array and filter
    const validFiles = Array.from(newFiles).filter(validateFile);

    validFiles.forEach(file => {
      const pendingFile = {
        id: pendingFiles.length + 1,
        name: file.name,
        type: file.type,
        size: `${(file.size / 1024 / 1024).toFixed(1)} MB`,
        lastModified: new Date().toISOString().split('T')[0]
      };

      pendingFiles.push(pendingFile);
      renderPendingFiles(pendingFile);

      // Simular carga y mover el archivo a la lista de subidos
      simulateFileUpload(pendingFile, file);
    });
  }

  // File validation
  function validateFile(file) {
    const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    const maxSize = 10 * 1024 * 1024; // 10MB

    if (!validTypes.includes(file.type)) {
      alert(`Tipo de archivo no soportado: ${file.name}`);
      return false;
    }

    if (file.size > maxSize) {
      alert(`Archivo demasiado grande: ${file.name}`);
      return false;
    }

    return true;
  }

  // Process and display file
  function processFile(file) {
    const reader = new FileReader();
    const newFile = {
      id: driveFiles.length + 1,
      name: file.name,
      type: file.type,
      size: `${(file.size / 1024 / 1024).toFixed(1)} MB`,
      lastModified: new Date().toISOString().split('T')[0]
    };

    reader.onload = function (e) {
      newFile.thumbnail = file.type.startsWith('image/') ? e.target.result : null;
      driveFiles.push(newFile);
      renderFileGrid(driveFiles);
    };

    // Read image files to show thumbnail
    if (file.type.startsWith('image/')) {
      reader.readAsDataURL(file);
    } else {
      //add to file list and wait 3 seconds to simulate upload
      driveFiles.push(newFile);
      renderFileGrid(driveFiles);
    }
  }

  // Render file grid
  function renderFileGrid(filesToRender) {
    $fileGrid.empty();

    filesToRender.forEach(function (file) {
      const $fileItem = createFileItem(file);

      // Añadir la animación al aparecer
      $fileItem.addClass('fade-in');
      setTimeout(() => {
        $fileItem.removeClass('fade-in');
      }, 300);

      $fileGrid.append($fileItem);
    })
  }

  // Create file item for grid
  function createFileItem(file) {
    const $fileItem = $('<div>', {
      class: 'group relative aspect-square border py-5 rounded-lg overflow-hidden hover:shadow-md transition-shadow'
    });

    // More options button
    const $moreButton = $('<div>', {
      class: 'absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10'
    }).append(
      $('<button>', {
        class: 'p-1 rounded-full bg-white/80 hover:bg-white'
      }).append(
        $('<i>', {
          class: 'fas fa-ellipsis-v w-4 h-4 text-gray-700'
        })
      )
    );
    const $contextMenu = $('<div>', {
      class: 'context-menu  right-0  mt-2 bg-white border rounded shadow-lg text-sm hidden z-20'
    }).append(
      
      $('<button>', {
        class: 'block w-full text-left px-4 py-2 hover:bg-gray-100',
        text: 'Descargar',
        click: function (e) {
          e.stopPropagation();
          //downloadFile(file);
          const a = document.createElement('a');
          a.target = '_blank';
          a.href = file.path || 'https://via.placeholder.com/150';
          a.download = file.name;
          a.click();
        }
      }),
      $('<button>', {
        class: 'block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100',
        text: 'Eliminar',
        click: function (e) {
          e.stopPropagation();
          deleteInspeccionFiles(file.id);
          driveFiles = driveFiles.filter(f => f.id !== file.id);
          renderFileGrid(driveFiles);
        }
      })
    );
    $moreButton.on('click', function (e) {
      e.stopPropagation();
      $contextMenu.addClass('fade-in').show();

      // Quitar la clase de animación después de la animación
      setTimeout(() => {
        $contextMenu.removeClass('fade-in');
      }, 300); // Duración de la animación en milisegundos
    });
    // Close menu on outside click
    $(document).on('click', function () {
      $contextMenu.hide();
    });
    let $fileContent;
    if (file.thumbnail
&& file.type.startsWith('image/')
    ) {
      $fileContent = $('<img>', {
        src: file.path,
        alt: file.name,
        class: 'w-full h-full object-cover'
      });
    } else {
      $fileContent = $('<div>', {
        class: 'w-full h-full flex items-center justify-center bg-gray-100'
      });
      $fileContent.append(iconMap[file.type] || '<i class="fas fa-file w-12 h-12 text-gray-400"></i>');
    }

    // File name label
    const $fileLabel = $('<div>', {
      class: 'absolute bottom-0 left-0 right-0 bg-white/80 p-2'
    }).append(
      $('<p>', {
        class: 'text-sm font-medium text-gray-800 truncate',
        text: file.name
      })
    );

    // Tooltip with file details
    const $tooltip = $('<div>', {
      class: 'absolute hidden group-hover:block top-full left-0 mt-2 bg-gray-800 text-white text-xs rounded py-1 px-2 z-20',
      text: `${file.size} • ${file.lastModified}`
    });

    // Assemble the file item
    $fileItem
      .append($moreButton)
      .append($contextMenu) // Append menu to file item
      .append($fileContent)
      .append($fileLabel)
      .append($tooltip);

    return $fileItem;
  }
  function renderPendingFiles(pendingFile) {
    const $pendingFileList = $('#pending-file-list');
    $pendingFileList.empty();

    if (pendingFiles.length === 0) {
      $pendingFileList.append('<p class="text-gray-500">No hay archivos pendientes.</p>');
      return;
    }

    pendingFiles.forEach(file => {
      const $fileItem = $('<div>', {
        class: 'file-item flex items-center space-x-4',
        id: `pending-file-${file.id}`
      });

      const $progressWrapper = $('<div>', { class: 'relative w-12 h-12' });

      const $progressCircle = $('<svg>', {
        class: 'progress-circle',
        viewBox: '0 0 36 36',

      }).append(
        $('<circle>', {
          class: 'circle-background',
          cx: 18,
          cy: 18,
          r: 15,
          stroke: 'red',
          'stroke-width': 2,
          fill: 'none'
        }),

      );

      $progressWrapper.append($progressCircle);

      $fileItem.append(
        $progressWrapper,
        $('<div>', { class: 'file-name', text: file.name }),
        $('<div>', { class: 'text-sm text-gray-500', text: file.size })
      );

      $pendingFileList.append($fileItem);
    });
  }
  async function getInternetSpeed() {
    const fileUrl = "https://cargaconsolidadaback.probusiness.pe/storage/RC7VD9KRltwn4hEzPrrSz6Tbv6IIr2C1laOIqPa6.png";
    const startTime = Date.now();
    try {
      const response = await fetch(fileUrl, { method: 'GET', cache: 'no-store' });
      const blob = await response.blob();
      const endTime = Date.now();

      const fileSizeInBits = blob.size * 8; // Tamaño del archivo en bits
      const durationInSeconds = (endTime - startTime) / 1000; // Duración en segundos

      const speedInBps = fileSizeInBits / durationInSeconds; // Velocidad en bits por segundo
      const speedInMbps = speedInBps / (1024 * 1024); // Velocidad en Mbps

      return speedInMbps; // Retornar velocidad estimada
    } catch (error) {
      console.error("Error al medir la velocidad:", error);
      return null; // Manejo de errores
    }

  }

  function simulateFileUpload(pendingFile, file) {
    getInternetSpeed().then(speedInMbps => {
      const duration = (file.size / (speedInMbps * 1024 * 1024)) * 1000; // Duración simulada en ms
      const $fileItem = $(`#pending-file-${pendingFile.id}`); // Localizar el archivo pendiente
      const $progressCircle = $fileItem.find('.circle-progress');
      const progressCircle = $progressCircle[0];  // Referencia directa al SVG

      let progress = 0;
    

      const progressInterval = setInterval(() => {
        progress += 1;
        const offset = 94.25 - (94.25 * progress / 100); // Calculamos el progreso en base a porcentaje
        $progressCircle.attr('stroke-dashoffset', offset);
        // Si el progreso alcanza el 100%, limpiamos el intervalo
        if (progress >= 100) {
          clearInterval(progressInterval);

          // Mover archivo de pendientes a subidos
          pendingFiles.splice(pendingFiles.indexOf(pendingFile), 1);
          

          renderPendingFiles();
          renderFileGrid(driveFiles);  // Suponiendo que renderFileGrid maneja la lista de subidos
        }
      }, duration / 100); // Actualiza el progreso cada 1% del tiempo estimado
      uploadFile(file);
    });
  }

function uploadFile(file) {
  const formData = new FormData();
  formData.append('file', file);
  formData.append('idExcel', idExcel);
  formData.append('idDetalle', idDetalle);
  formData.append('idOrder', idOrder);
  let url=base_url + "Almacen/Trading/uploadInspeccionFiles";
  fetch(url, {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      console.log('Archivo subido:', data);
      driveFiles.push({
        id: data.id,
        name: data.name,
        type: data.type,
        path: data.path,
        thumbnail: data.thumbnail,
        size: `${(data.size / 1024 / 1024).toFixed(1)} MB`,
        lastModified: data.lastModified
      });
    })
    .catch(error => {
      console.error('Error al subir archivo:', error);
    });

}



function getAlmacenData(idO) {
  idOrder = idO;
  url = base_url + "Almacen/Trading/getInspeccion";
  containerInspection.show();
  containerAlmacen.hide();
  tableInspection = tableInspection.DataTable({
    dom:
      "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons: [
      {
        extend: "excel",
        text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
        titleAttr: "Excel",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "pdf",
        text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
        titleAttr: "PDF",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "colvis",
        text: '<i class="fa fa-ellipsis-v"></i> Columnas',
        titleAttr: "Columnas",
        exportOptions: {
          columns: ":visible",
        },
      },
    ],
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: false,
    serverSide: false,
    pagingType: "full_numbers",
    oLanguage: {
      sInfo: "Mostrando (_START_ - _END_) total de registros _TOTAL_",
      sLengthMenu: "_MENU_",
      sSearch: "Buscar por: ",
      sSearchPlaceholder: "",
      sZeroRecords: "No se encontraron registros",
      sInfoEmpty: "No hay registros",
      sLoadingRecords: "Cargando...",
      sProcessing: "Procesando...",
      oPaginate: {
        sFirst: "<<",
        sLast: ">>",
        sPrevious: "<",
        sNext: ">",
      },
    },
    order: [[sort_col, "desc"]],
    ajax: {
      url: url,
      type: "POST",
      dataType: "JSON",

      data: function (data) {
        data.idOrder = idOrder;
      },
      complete: function () {
        $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
        calcTotales();
      },
    },
    columnDefs: [
      {
        targets: "no-hidden",
        visible: false,
      },
      {
        className: "text-center",
        targets: "no-sort",
        orderable: false,
      },
    ],
    lengthMenu: [
      [10, 100, 1000, -1],
      [10, 100, 1000, "Todos"],
    ],
  })
  addEventsToInspection();
  
}
function getFotos(idEx,idD) {
  removeEventInspection();
  idExcel = idEx;
  idDetalle = idD;
  containerInspection.hide();
  driveContainer.show();
  $.ajax({
    url: base_url + "Almacen/Trading/getInspeccionFiles",
    type: "POST",
    dataType: "JSON",
    data: { idExcel: idExcel,
            idDetalle: idDetalle
     },
    success: function (data) {
      driveFiles = data.data;
      renderFileGrid(driveFiles);
    },
  });
}
function deleteInspeccionFiles(id){
  $.ajax({
    url: base_url + "Almacen/Trading/deleteInspeccionFiles",
    type: "POST",
    dataType: "JSON",
    data: { idFile: id,idDetalle: idDetalle },
    success: function (data) {
      
    },
  });
}
function closePagos(){
  containerInspection.show();
  driveContainer.hide();
  reload_table_inspection();
  
}
function removeEventInspection(){
  $(document).off("change", ".box_value");
  $(document).off("change", ".cbm_value");
  $(document).off("change", ".kg_value");
}
function calcTotales(){
  let total = 0;
  $(".box_value").each(function () {
    total += +$(this).val();
  });
  $("#total-box").val(total);
  total = 0;
  $(".cbm_value").each(function () {
    total += +$(this).val();
  }
  );
  $("#total-cbm").val(total);
  total = 0;
  $(".kg_value").each(function () {
    total += +$(this).val();
  }
  );
  $("#total-kg").val(total);

}
function addEventsToInspection(){
  // Add event listener to file input with class box_value on change sum all 
  // values of all inputs with class box_value and set the result to the input with id total-box
  $(document).on("input", ".box_value", function () {
    console.log("input");
    let total = 0;
    $(".box_value").each(function () {
      total += +$(this).val();
    });
    $("#total-box").val(total);
  });
  $(document).on("input", ".cbm_value", function () {
    let total = 0;
    $(".cbm_value").each(function () {
      total += +$(this).val();
    });
    $("#total-cbm").val(total);
  });
  $(document).on("input", ".kg_value", function () {
    let total = 0;
    $(".kg_value").each(function () {
      total += +$(this).val();
    });
    $("#total-kg").val(total);
  });
  $("#btn-save-inspection").on("click", function () {
  saveInspection()
  });
  $("#btn-back-inspection").on("click", function () {
    closeInspection();
  });
}
function saveInspection(){
  //get all values of inputs with class box_value, cbm_value and kg_value
  let data = [];
  $(".box_value").each(function () {
    data.push({ id: $(this).data("id"), value: !isNaN($(this).val()) ? $(this).val() : 0, key:'total_box_almacen' 
     });
  });
  $(".cbm_value").each(function () {
    data.push({ id: $(this).data("id"), value: !isNaN($(this).val()) ? $(this).val() : 0 , key:'total_cbm_almacen'});
  });
  $(".kg_value").each(function () {
    data.push({ id: $(this).data("id"), value: !isNaN($(this).val()) ? $(this).val() : 0 , key:'total_kg_almacen'});
  });
  $(".almacen_notas").each(function () {
    data.push({ id: $(this).data("id"), value: $(this).val() , key:'nota_almacen'});
  });
  console.log(data);
  $.ajax({
    url: base_url + "Almacen/Trading/saveInspection",
    type: "POST",
    dataType: "JSON",
    data: { 
      data: data,
      idOrder: idOrder,
     },
    success: function (data) {
      reload_table_inspection();
      //show toast
    },
  });
}
function closeInspection(){
  containerInspection.hide();
  containerAlmacen.show();
  reload_table_almacen();
}
function reload_table_inspection(){
  tableInspection.ajax.reload(null, false);
}
function reload_table_almacen(){
  tableAlmacen.ajax.reload(null, false);
}