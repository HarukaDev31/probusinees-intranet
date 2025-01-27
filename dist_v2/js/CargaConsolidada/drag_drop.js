class FileManager {
    // Constructor que acepta configuraciones personalizadas
    constructor(config) {
      // Configuración predeterminada
      this.defaultConfig = {
        validTypes: [
          "image/jpeg", "image/png", "image/gif", "image/webp", "image/bmp", "image/avif",
          "video/mp4", "video/mkv", "video/webm", "video/avi", "video/mov",
          "application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation"
        ],
        maxSize: 20 * 1024 * 1024, // 20MB
        fileGrid: "#file-grid",
        fileInput: "#file-input",
        uploadBtn: "#btn-upload",
        dragDropContainer: "#drag-drop-container",
        searchInput: "#search-input",
        pendingFileList: "#pending-file-list",
        iconMap : {
    'application/pdf': '<i class="fas fa-file-pdf w-12 h-12 text-red-400"></i>',
    'image/jpeg': '<i class="fas fa-file-image w-12 h-12 text-blue-400"></i>',
    'image/png': '<i class="fas fa-file-image w-12 h-12 text-blue-400"></i>',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
      '<i class="fas fa-file-word w-12 h-12 text-blue-600"></i>'
  },
        onFileUpload: () => {},
      };
  
      // Mezclar configuraciones personalizadas con las predeterminadas
      this.config = { ...this.defaultConfig, ...config };
      this.data = {
        driveFiles: [],
        pendingFiles: [],
      };
      this.callbacks = {
        onFileUpload: this.config.onFileUpload,
        onLoadFiles: this.config.onLoadFiles,
      };
      // Cachear selectores iniciales
      this.cacheSelectors();
      // Vincular eventos
      this.bindEvents();
      // Renderizar la cuadrícula inicial
      this.renderFileGrid(this.data.driveFiles);
      if (this.callbacks.onLoadFiles) {
        this.loadExistingFiles();
      }
    }
  
    // Cachear los selectores
    cacheSelectors() {
      this.$fileGrid = $(this.config.fileGrid);
      this.$fileInput = $(this.config.fileInput);
      this.$uploadBtn = $(this.config.uploadBtn);
      this.$dragDropContainer = $(this.config.dragDropContainer);
      this.$searchInput = $(this.config.searchInput);
      this.$pendingFileList = $(this.config.pendingFileList);
    }
  
    // Vincular eventos
    bindEvents() {
      const self = this;
  
      //off para evitar que se dupliquen los eventos
      self.$fileInput.off("change");
      self.$uploadBtn.off("click");
      self.$dragDropContainer.off("dragenter");
      self.$dragDropContainer.off("dragleave");
      self.$dragDropContainer.off("drop");
      self.$searchInput.off("input");
      
      self.$uploadBtn.on("click", function (e) {
        e.preventDefault();
        self.handleFiles(self.$fileInput[0].files);
      });
  
      // Eventos de arrastrar y soltar
      ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
        self.$fileGrid.on(eventName, function (e) {
          e.preventDefault();
          e.stopPropagation();
          console.log(eventName);
        });
      });
  
      self.$fileGrid.on("dragenter", function () {
        $(this).addClass("drag-over");
      });
  
      self.$fileGrid.on("dragleave", function (e) {
        if (e.originalEvent.clientX <= 0 || e.originalEvent.clientY <= 0 ||
            e.originalEvent.clientX >= $(this).width() || e.originalEvent.clientY >= $(this).height()) {
          $(this).removeClass("drag-over");
        }
      });
  
      self.$fileGrid.on("drop", function (e) {
        $(this).removeClass("drag-over");
        self.handleFiles(e.originalEvent.dataTransfer.files);
      });
  
      // Búsqueda de archivos
      self.$searchInput.on("input", function () {
        const searchTerm = $(this).val().toLowerCase();
        const filteredFiles = self.data.driveFiles.filter(file =>
          file.name.toLowerCase().includes(searchTerm)
        );
        self.renderFileGrid(filteredFiles);
      });
    }
    loadExistingFiles() {
      const that = this;
      this.callbacks.onLoadFiles().then((files) => {
        files.forEach((file) => {
          this.data.driveFiles.push(file);

        });
      }).catch((err) => {
        console.error("Error loading files:", err);
      }).finally(() => {
        that.renderFileGrid(that.data.driveFiles);
      });
    }
    // Manejo de archivos cargados
    handleFiles(files) {
      const validFiles = Array.from(files).filter(file => this.validateFile(file));
      validFiles.forEach(file => {
        const pendingFile = {
          id: this.data.pendingFiles.length + 1,
          name: file.name,
          type: file.type,
          size: `${(file.size / 1024 / 1024).toFixed(1)} MB`,
          lastModified: new Date().toISOString().split("T")[0],
        };
        this.data.pendingFiles.push(pendingFile);
        this.renderPendingFiles(pendingFile);
        this.simulateFileUpload(pendingFile, file);
      });
    }
    renderPendingFiles(pendingFile) {
        this.$pendingFileList.empty();
      
        if (this.data.pendingFiles.length === 0) {
          this.$pendingFileList.append('<p class="text-gray-500">No hay archivos pendientes.</p>');
          return;
        }
      
        this.data.pendingFiles.forEach(file => {
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
      
          this.$pendingFileList.append($fileItem);
        });
      }
    // Validar archivo
    validateFile(file) {
      if (!this.config.validTypes.includes(file.type)) {
        alert(`Tipo de archivo no soportado: ${file.name}`);
        return false;
      }
  
      if (file.size > this.config.maxSize) {
        alert(`Archivo demasiado grande: ${file.name}`);
        return false;
      }
  
      return true;
    }
    createFileItem(file) {
       try{
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
                // e.stopPropagation();
                // deleteInspeccionFiles(file.id);
                // driveFiles = driveFiles.filter(f => f.id !== file.id);
                // renderFileGrid(driveFiles);
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
          } else if (file.thumbnail && file.type.startsWith('video/')) {
            $fileContent = $('<video>', {
              src: file.path,
              alt: file.name,
              class: 'w-full h-full object-cover',
              controls: true,
              autoplay: false,
              loop: true,
              muted: true
            })
          }
          else {
            $fileContent = $('<div>', {
              class: 'w-full h-full flex items-center justify-center bg-gray-100'
            });
            $fileContent.append(this.config.iconMap[file.type] || '<i class="fas fa-file w-12 h-12 text-gray-400"></i>');
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
       }catch(e){
        console.log(e);
       }
    }
    // Renderizar la cuadrícula de archivos
    renderFileGrid(files) {
      this.$fileGrid.empty();
      console.log(files,"files");
      files.forEach(file => {
        console.log(file,"file");
        const $fileItem = this.createFileItem(file);
        console.log($fileItem,"$fileItem");
        $fileItem.addClass("fade-in");
        setTimeout(() => {
          $fileItem.removeClass("fade-in");
        }, 300);
        this.$fileGrid.append($fileItem);
      });
    }
  
    // Crear un elemento para un archivo
    
    async getInternetSpeed() {
        const fileUrl = "https://cargaconsolidadaback.probusiness.pe/storage/RC7VD9KRltwn4hEzPrrSz6Tbv6IIr2C1laOIqPa6.png";
        const startTime = Date.now();
        try {
          let response =
            await fetch(fileUrl, { method: 'GET', cache: 'no-store' });
          const blob =
            await response.blob();
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
    // Simulación de carga de archivos
    simulateFileUpload(pendingFile, file) {
        this.getInternetSpeed().then(speedInMbps => {
            const duration = (file.size / (speedInMbps * 1024 * 1024)) * 1000; // Duración simulada en ms
            const $fileItem = $(`#pending-file-${this.config.fileGrid}-${pendingFile.id}`); // Localizar el archivo pendiente
            const $progressCircle = $fileItem.find('.circle-progress');
            const progressCircle = $progressCircle[0];  // Referencia directa al SVG
        
            let progress = 0;
        
        
            const progressInterval = setInterval(async() => {
              progress += 1;
              console.log(progress);
              const offset = 94.25 - (94.25 * progress / 100); // Calculamos el progreso en base a porcentaje
              $progressCircle.attr('stroke-dashoffset', offset);
              if (progress >= 100) {
                clearInterval(progressInterval);
                this.data.pendingFiles.splice(this.data.pendingFiles.indexOf(this.pendingFile), 1);
                this.renderPendingFiles();
                this.renderFileGrid(this.data.driveFiles);
                // await getDocumentationList(idOrder,booking_tipo_personal);
                // initDocumentationDriveEvents();
              }
            }, duration / 100); 
            if (typeof this.callbacks.onFileUpload === "function") {
                this.callbacks.onFileUpload(file).then((file) => {
                    clearInterval(progressInterval);
                    $progressCircle.attr('stroke-dashoffset', 94.25);
                    this.data.driveFiles.push(file);
                    console.log(this.data.driveFiles,file);
                    this.renderFileGrid(this.data.driveFiles);
                    }).catch(() => {
                    clearInterval(progressInterval);
                    $progressCircle.attr('stroke-dashoffset', 94.25);
                    }).finally(() => {
                    this.data.pendingFiles.splice(this.data.pendingFiles.indexOf(this.pendingFile), 1);
                    this.renderPendingFiles();
                  })
                    
              }
          });
    }
     
  }
  
  // Uso de múltiples instancias
//   $(document).ready(function () {
//     // Primera instancia
    
  
//     // Segunda instancia
//     const fileManager2 = new FileManager({
//       fileGrid: "#file-grid-2",
//       fileInput: "#file-input-2",
//       uploadBtn: "#btn-upload-2",
//       dragDropContainer: "#drag-drop-container-2",
//       searchInput: "#search-input-2",
//     });
//   });
  