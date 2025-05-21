class FileUploader {
  static instances = {};
  constructor(config) {
    // Configuración predeterminada
    this.config = {
      containerId: '', // ID del contenedor principal (también será el prefijo)
      acceptedTypes: '*/*', // Tipos de archivos aceptados (todos por defecto)
      maxSize: 5 * 1024 * 1024, // Tamaño máximo en bytes (5MB por defecto)
      placeholderText: 'Arrastra o sube tu archivo aquí',
      placeholderIcon: 'i-lucide-file',
      showPreview: true,
      showAsModal: false, // Mostrar como modal
      // Clase de icono para placeholder
      ...config
    };
    this.currentUrl = null; // URL actual del archivo
    // Validar que se proporcionó un ID de contenedor
    if (!this.config.containerId) {
      throw new Error('Se requiere un ID de contenedor');
    }

    // El prefijo será el mismo ID del contenedor
    this.prefix = this.config.containerId;

    // Inicializar variables
    this.container = document.getElementById(this.config.containerId);
    this.file = null;

    if (!this.container) {
      throw new Error(`No se encontró el contenedor con ID: ${this.config.containerId}`);
    }

    this.cleanupPreviousInstance();

    // Registrar esta instancia
    FileUploader.instances[this.config.containerId] = this;

    // Guardar referencias a los event listeners para poder eliminarlos después
    this.eventListeners = [];

    // Generar estructura HTML
    this.generateHTML();

    // Inicializar eventos

    // Inicializar eventos
    this.initEvents();
  }

  generateHTML() {
    // Crear IDs para los elementos internos usando el ID del contenedor como prefijo
    const previewId = `${this.prefix}-preview`;
    const placeholderId = `${this.prefix}-placeholder`;
    const inputId = `${this.prefix}-input`;
    const removeId = `${this.prefix}-remove`;

    // Obtener formatos aceptados en texto legible
    const acceptedTypesText = this.getAcceptedTypesText();

    // Guardar referencias para usar más tarde
    this.ids = { previewId, placeholderId, inputId, removeId };

    // Establecer clases para el contenedor principal si no las tiene
    if (!this.container.classList.contains('relative')) {
      this.container.classList.add('relative', 'bg-gray-50', 'border-2', 'border-dashed',
        'border-gray-300', 'rounded-lg', 'mb-4', 'transition-all',
        'hover:bg-gray-100', 'hover:border-blue-300', 'p-6');
    }
    // Generar HTML interno
    this.container.innerHTML = `
      <div class="flex flex-col items-center justify-center h-48 w-full">
        <div id="${previewId}" class="hidden w-full h-full flex flex-col items-center justify-center"></div>
        <div id="${placeholderId}" class="text-center gap-5 w-100 flex flex-col items-center justify-center w-full">
        <svg width="40" height="36  " viewBox="0 0 25 23" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M22.7564 15.3809H2.24359C1.01282 15.3809 0 16.3937 0 17.6244V20.1886C0 21.4193 1.01282 22.4321 2.24359 22.4321H22.7564C23.9872 22.4321 25 21.4193 25 20.1886V17.6244C25 16.3937 23.9872 15.3809 22.7564 15.3809ZM23.0769 20.1886C23.0769 20.368 22.9359 20.5091 22.7564 20.5091H2.24359C2.0641 20.5091 1.92308 20.368 1.92308 20.1886V17.6244C1.92308 17.445 2.0641 17.3039 2.24359 17.3039H22.7564C22.9359 17.3039 23.0769 17.445 23.0769 17.6244V20.1886Z" fill="#585858"/>
        <path d="M8.78089 5.48397L10.986 3.27885V12.4968C10.986 13.0224 11.4219 13.4583 11.9476 13.4583C12.4732 13.4583 12.9091 13.0224 12.9091 12.4968V3.27885L15.1142 5.48397C15.3065 5.67628 15.5501 5.76603 15.7937 5.76603C16.0373 5.76603 16.2809 5.67628 16.4732 5.48397C16.845 5.11218 16.845 4.49679 16.4732 4.125L12.627 0.278846C12.2552 -0.0929487 11.6399 -0.0929487 11.2681 0.278846L7.42191 4.125C7.05012 4.49679 7.05012 5.11218 7.42191 5.48397C7.79371 5.85577 8.40909 5.85577 8.78089 5.48397Z" fill="#585858"/>
        </svg>
        <div>
          <p class="text-sm text-gray-500">${this.config.placeholderText}</p>
          <p class="text-xs text-gray-400">Formatos: ${acceptedTypesText}</p>
         
        </div>
        <div
          class="bg-gray-200  w-75 hover:bg-gray-300 text-black text-sm py-2 px-8 rounded-md transition-colors flex items-center justify-center ">Subir archivo</div> 
        </div>
        <input id="${inputId}" type="file" accept="${this.config.acceptedTypes}" class="hidden" />
      </div>
      ${this.config.showPreview ? `<button id="${removeId}" class="absolute top-2 right-2 hidden ">
        <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.8335 14H14.6671" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M11.2504 1.47176C11.5524 1.1697 11.9621 1 12.3893 1C12.6008 1 12.8103 1.04166 13.0057 1.12261C13.2011 1.20355 13.3787 1.32219 13.5282 1.47176C13.6778 1.62133 13.7964 1.79889 13.8774 1.99431C13.9583 2.18972 14 2.39917 14 2.61069C14 2.82221 13.9583 3.03166 13.8774 3.22708C13.7964 3.42249 13.6778 3.60006 13.5282 3.74962L4.03715 13.2407L1 14L1.75929 10.9629L11.2504 1.47176Z" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

      </button>`: `<button id="${removeId}" class="absolute top-2 right-2 hidden "></button>`}
    `;

    // Guardar referencias a los elementos DOM
    this.preview = document.getElementById(previewId);
    this.placeholder = document.getElementById(placeholderId);
    this.input = document.getElementById(inputId);
    this.removeButton = document.getElementById(removeId);
  }


  initEvents() {
    //if showAsModal is true, open modal with image else clik on the input
    let handleContainerClick;
    if (this.config.showAsModal) {
      handleContainerClick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        //get if is video with extension
        const isVideo = this.currentUrl && (this.currentUrl.endsWith('.mp4') || this.currentUrl.endsWith('.webm') || this.currentUrl.endsWith('.ogg'))|| this.currentUrl.endsWith('.mov');
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
          <div class="bg-white rounded-lg shadow-lg p-4 relative max-w-2xl w-1/2 max-h-screen">
            ${ !isVideo?`<img src="${this.currentUrl}" alt="${this.file.name}" class="max-w-full  w-full max-h-[80vh] object-contain"
               />`:
            `<video src="${this.currentUrl}" controls class="max-w-full w-full max-h-[80vh]"></video>`}
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" id="close-modal">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"> 
                <path d="M1 1L15 15" stroke="#585858" stroke-width="2" stroke-linecap="round"/>
                <path d="M1 15L15 1" stroke="#585858" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
        `;
        document.body.appendChild(modal);
        // Evento para cerrar el modal
        document.getElementById('close-modal').addEventListener('click', () => {
          document.body.removeChild(modal);
        });
        // También cerrar al hacer clic fuera del contenido del modal
        modal.addEventListener('click', (e) => {
          if (e.target === modal) {
            document.body.removeChild(modal);
          }
        });
      };
    } else {
      handleContainerClick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.input.click();
      };
    }
    // const handleContainerClick = () => this.input.click();
    this.addEventListenerWithTracking(this.container, 'click', handleContainerClick);

    const handleDragOver = (e) => {
      e.preventDefault();
      this.container.classList.add('border-blue-500', 'bg-blue-50');
    };
    this.addEventListenerWithTracking(this.container, 'dragover', handleDragOver);

    const handleDragLeave = () => {
      this.container.classList.remove('border-blue-500', 'bg-blue-50');
    };
    this.addEventListenerWithTracking(this.container, 'dragleave', handleDragLeave);

    const handleDrop = (e) => {
      e.preventDefault();
      this.container.classList.remove('border-blue-500', 'bg-blue-50');

      if (e.dataTransfer.files.length) {
        this.handleFile(e.dataTransfer.files[0]);
      }
    };
    this.addEventListenerWithTracking(this.container, 'drop', handleDrop);

    // Evento del input de archivo
    const handleInputChange = () => {
      if (this.input.files.length) {
        this.handleFile(this.input.files[0]);
      }
    };
    this.addEventListenerWithTracking(this.input, 'change', handleInputChange);

    // Evento del botón de eliminar
    const handleRemoveClick = (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.clearFile();
    };
    this.addEventListenerWithTracking(this.removeButton, 'click', handleRemoveClick);
  }

  handleFile(file) {
    // Validar tipo de archivo
    const fileType = file.type;
    const isValidType = this.validateFileType(fileType);

    if (!isValidType) {
      alert(`Tipo de archivo no permitido. Por favor, use: ${this.config.acceptedTypes}`);
      return;
    }

    // Validar tamaño de archivo
    if (file.size > this.config.maxSize) {
      const maxSizeMB = this.config.maxSize / (1024 * 1024);
      alert(`El archivo es demasiado grande. Tamaño máximo: ${maxSizeMB}MB`);
      return;
    }

    // Guardar referencia al archivo
    this.file = file;

    // Mostrar vista previa
    this.showPreview();

    // Disparar evento personalizado
    this.dispatchChangeEvent();
  }

  validateFileType(fileType) {
    // Si se acepta cualquier tipo
    if (this.config.acceptedTypes === '*/*') return true;

    const acceptedTypes = this.config.acceptedTypes.split(',');

    for (const type of acceptedTypes) {
      // Manejar comodines (image/*)
      if (type.includes('*')) {
        const mainType = type.split('/')[0];
        if (fileType.startsWith(`${mainType}/`)) {
          return true;
        }
      } else if (fileType === type.trim()) {
        return true;
      }
    }

    return false;
  }

  showPreview() {
    // Limpiar cualquier vista previa anterior
    this.preview.innerHTML = '';

    // Mostrar vista previa según el tipo de archivo
    if (this.file.type.startsWith('image/')) {
      // Vista previa para imágenes
      this.showImagePreview();
    } else if (this.file.type.startsWith('video/')) {
      // Vista previa para videos
      this.showVideoPreview();
    } else if (this.file.type.startsWith('audio/')) {
      // Vista previa para audio
      this.showAudioPreview();
    } else if (this.file.type === 'application/pdf') {
      // Vista previa para PDF (icono)
      this.showDocumentPreview('i-lucide-file-text', 'PDF');
    } else if (this.file.type.includes('spreadsheet') || this.file.type.includes('excel')) {
      // Vista previa para hojas de cálculo
      this.showDocumentPreview('i-lucide-table', 'Excel');
    } else if (this.file.type.includes('document') || this.file.type.includes('word')) {
      // Vista previa para documentos
      this.showDocumentPreview('i-lucide-file-text', 'Word');
    } else if (this.file.type.includes('presentation') || this.file.type.includes('powerpoint')) {
      // Vista previa para presentaciones
      this.showDocumentPreview('i-lucide-monitor', 'PowerPoint');
    } else if (this.file.type.includes('zip') || this.file.type.includes('compressed') || this.file.type.includes('archive')) {
      // Vista previa para archivos comprimidos
      this.showDocumentPreview('i-lucide-archive', 'Archivo comprimido');
    } else {
      // Vista previa genérica para otros tipos de archivo
      this.showDocumentPreview('i-lucide-file', '');
    }

    // Mostrar el área de vista previa y ocultar el placeholder
    this.preview.classList.remove('hidden');
    this.placeholder.classList.add('hidden');
    this.removeButton.classList.remove('hidden');
  }

  showImagePreview() {
    const reader = new FileReader();
    reader.onload = (e) => {
      const imageDataUrl = e.target.result;

      if (this.config.showPreview) {
        // Si showPreview es true, mostrar la imagen directamente
        this.preview.innerHTML = `
          <div class="relative w-full h-full flex items-center justify-center">
            <img src="${imageDataUrl}" alt="${this.file.name}" class="max-w-full max-h-full object-contain" />
            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate">
              ${this.file.name} 
            </div>
          </div>
        `;
      } else {
        // Si showPreview es false, mostrar solo los metadatos y el icono
        this.preview.innerHTML = `
          <div class="relative w-full h-full flex items-center justify-center">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-1/6 h-1/6 cursor-pointer" id="${this.prefix}-show-preview-modal">
              <path d="M17 1H3C1.89543 1 1 1.89543 1 3V17C1 18.1046 1.89543 19 3 19H17C18.1046 19 19 18.1046 19 17V3C19 1.89543 18.1046 1 17 1Z" stroke="#585858" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M6.5 8C7.32843 8 8 7.32843 8 6.5C8 5.67157 7.32843 5 6.5 5C5.67157 5 5 5.67157 5 6.5C5 7.32843 5.67157 8 6.5 8Z" stroke="#585858" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M19 13L14 8L3 19" stroke="#585858" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="w-4/6">
              <p class="text-sm text-gray-500">${this.file.name.length > 25 ? this.file.name.substring(0, 25) + '...' : this.file.name}</p>
              <p class="text-xs text-gray-400">Tamaño: ${(this.file.size / 1024).toFixed(2)} KB</p>
            </div>
            <div class="w-1/6 h-1/6">
              <svg width="28" height="24" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="${this.prefix}-remove-button">
                <path d="M1.39941 3.51953H2.55941H11.8394" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10.6796 3.51922V11.6392C10.6796 11.9469 10.5574 12.2419 10.3398 12.4595C10.1223 12.677 9.82722 12.7992 9.51957 12.7992H3.71957C3.41192 12.7992 3.11687 12.677 2.89933 12.4595C2.68178 12.2419 2.55957 11.9469 2.55957 11.6392V3.51922M4.29957 3.51922V2.35922C4.29957 2.05157 4.42178 1.75652 4.63933 1.53897C4.85687 1.32143 5.15192 1.19922 5.45957 1.19922H7.77957C8.08722 1.19922 8.38227 1.32143 8.59981 1.53897C8.81736 1.75652 8.93957 2.05157 8.93957 2.35922V3.51922" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5.45947 6.41992V9.89992" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.7793 6.41992V9.89992" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        `;

        // Añadir evento de clic al icono para mostrar el modal
        setTimeout(() => {
          const showPreviewButton = document.getElementById(`${this.prefix}-show-preview-modal`);
          const removeButton = document.querySelector(`.${this.prefix}-remove-button`);
          if (showPreviewButton) {
            showPreviewButton.addEventListener('click', (evt) => {
              evt.preventDefault();
              evt.stopPropagation();

              // Crear y mostrar el modal
              const modal = document.createElement('div');
              modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
              modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg p-4 relative max-w-2xl max-h-screen">
                  <img src="${imageDataUrl}" alt="${this.file.name}" class="max-w-full max-h-[80vh] object-contain" />
                  <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" id="close-modal">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 1L15 15" stroke="#585858" stroke-width="2" stroke-linecap="round"/>
                      <path d="M1 15L15 1" stroke="#585858" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                  </button>
                </div>
              `;
              document.body.appendChild(modal);

              // Evento para cerrar el modal
              document.getElementById('close-modal').addEventListener('click', () => {
                document.body.removeChild(modal);
              });

              // También cerrar al hacer clic fuera del contenido del modal
              modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                  document.body.removeChild(modal);
                }
              });
            });
          }
          if (removeButton) {
            removeButton.addEventListener('click', (evt) => {
              evt.preventDefault();
              evt.stopPropagation();
              this.clearFile();
            });
          }

        }, 0);
      }
    };

    reader.readAsDataURL(this.file);
  }

  showVideoPreview() {
    const reader = new FileReader();
    reader.onload = (e) => {
      this.preview.innerHTML = `
        <div class="relative w-full h-full flex items-center justify-center">
          <video src="${e.target.result}" controls class="max-w-full max-h-full"></video>
          <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate">
            ${this.file.name}
          </div>
        </div>
      `;
    };
    reader.readAsDataURL(this.file);
  }

  showAudioPreview() {
    const reader = new FileReader();
    reader.onload = (e) => {
      this.preview.innerHTML = `
        <div class="flex flex-col items-center justify-center w-full h-full">
          <span class="i-lucide-music text-gray-500 w-16 h-16 mb-2"></span>
          <audio src="${e.target.result}" controls class="w-full max-w-xs"></audio>
          <p class="text-sm text-gray-700 mt-2 truncate max-w-xs">${this.file.name}</p>
        </div>
      `;
    };
    reader.readAsDataURL(this.file);
  }

  showDocumentPreview(iconClass, fileType) {
    // Formatear el tamaño del archivo
    const sizeInKB = this.file.size / 1024;
    let formattedSize;

    if (sizeInKB < 1024) {
      formattedSize = `${sizeInKB.toFixed(1)} KB`;
    } else {
      formattedSize = `${(sizeInKB / 1024).toFixed(1)} MB`;
    }

    // Mostrar vista previa con icono
    this.preview.innerHTML = `
      <div class="flex flex-col items-center justify-center h-full">
        <span class="${iconClass} text-gray-500 w-16 h-16 mb-2"></span>
        <p class="text-sm font-medium text-gray-800 truncate max-w-xs">${this.file.name}</p>
        <div class="flex items-center justify-center mt-1">
          ${fileType ? `<span class="text-xs text-gray-500 mr-2">${fileType}</span>` : ''}
          <span class="text-xs text-gray-500">${formattedSize}</span>
        </div>
      </div>
    `;
  }

  clearFile() {
    // Reiniciar el input
    this.input.value = '';

    // Limpiar vista previa
    this.preview.innerHTML = '';
    this.preview.classList.add('hidden');
    this.placeholder.classList.remove('hidden');
    this.removeButton.classList.add('hidden');

    // Eliminar referencia al archivo
    this.file = null;

    // Disparar evento personalizado
    this.dispatchChangeEvent();
  }
  async loadFromURL(url) {
    try {
      // Mostrar un estado de carga
      this.preview.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full">
          <span class="i-lucide-loader text-blue-500 w-12 h-12 animate-spin mb-2"></span>
          <p class="text-sm text-gray-500">Cargando imagen...</p>
        </div>
      `;
      this.preview.classList.remove('hidden');
      this.placeholder.classList.add('hidden');
      this.removeButton.classList.remove('hidden');

      // Obtener la imagen de la URL
      const response = await fetch(url);
      this.currentUrl = url;
      if (!response.ok) {
        throw new Error(`Error al cargar la imagen: ${response.status} ${response.statusText}`);
      }

      // Obtener el blob de la respuesta
      const blob = await response.blob();

      // Extraer el nombre de archivo de la URL
      const urlParts = url.split('/');
      const fileName = urlParts[urlParts.length - 1].split('?')[0] || 'imagen';

      // Crear un objeto File a partir del blob
      const file = new File([blob], fileName, { type: blob.type });

      // Procesar el archivo como si se hubiera subido normalmente
      this.handleFile(file);

    } catch (error) {
      console.error('Error al cargar la imagen desde URL:', error);

      // Mostrar mensaje de error
      this.preview.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full">
          <span class="i-lucide-alert-circle text-red-500 w-12 h-12 mb-2"></span>
          <p class="text-sm text-red-500">Error al cargar la imagen</p>
          <p class="text-xs text-gray-500 mt-1">${error.message}</p>
        </div>
      `;
      this.preview.classList.remove('hidden');
      this.placeholder.classList.add('hidden');
      this.removeButton.classList.remove('hidden');
    }
  }
  cleanupPreviousInstance() {
    const previousInstance = FileUploader.instances[this.config.containerId];
    if (previousInstance) {
      console.log(`Limpiando instancia anterior de FileUploader con ID: ${this.config.containerId}`);
      previousInstance.destroy();
    }
  }

  destroy() {
    // Eliminar todos los event listeners registrados
    this.eventListeners.forEach(({ element, type, handler }) => {
      element.removeEventListener(type, handler);
    });

    // Limpiar la referencia en el almacenamiento estático
    delete FileUploader.instances[this.config.containerId];

    console.log(`FileUploader con ID: ${this.config.containerId} destruido correctamente`);
  }

  // Método auxiliar para agregar event listeners y rastrearlos
  addEventListenerWithTracking(element, type, handler) {
    element.addEventListener(type, handler);
    this.eventListeners.push({ element, type, handler });
  }
  dispatchChangeEvent() {
    // Crear evento personalizado
    const event = new CustomEvent(`${this.prefix}-change`, {
      detail: { file: this.file }
    });

    // Disparar evento en el contenedor principal
    this.container.dispatchEvent(event);
  }

  // Método público para obtener el archivo actual
  getFile() {
    return this.file;
  }

  // Método público para establecer un archivo programáticamente
  setFile(file) {
    if (file) {
      this.handleFile(file);
    } else {
      this.clearFile();
    }
  }
  getAcceptedTypesText() {
    if (this.config.acceptedTypes === '*/*') return 'Cualquier archivo';
    const types = this.config.acceptedTypes.split(',').map(type => type.trim().toLowerCase());

    let hasImage = false, hasVideo = false, hasAudio = false;
    let hasPdf = false, hasExcel = false, hasWord = false, hasPowerPoint = false, others = [];

    types.forEach(type => {
      if (
        type === 'image/png' ||
        type === 'image/jpeg' ||
        type === 'image/jpg' ||
        type === 'image/gif' ||
        type === 'image/webp' ||
        type === 'image/*' ||
        type === '.png' ||
        type === '.jpg' ||
        type === '.jpeg' ||
        type === '.gif' ||
        type === '.webp'
      ) hasImage = true;
      else if (
        type === 'video/*' ||
        type === 'video/mp4' ||
        type === 'video/webm' ||
        type === 'video/ogg' ||
        type === 'video/quicktime' ||
        type === 'video/mov' ||
        type === '.mp4' ||
        type === '.webm' ||
        type === '.ogg' ||
        type === '.mov'
      ) hasVideo = true;
      else if (type === 'audio/*' || type === '.mp3' || type === '.wav' || type === '.ogg') hasAudio = true;
      else if (type === 'application/pdf' || type === '.pdf') hasPdf = true;
      else if (
        type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
        type === 'application/vnd.ms-excel' ||
        type === '.xls' ||
        type === '.xlsx'
      ) hasExcel = true;
      else if (
        type === 'application/msword' ||
        type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
        type === '.doc' ||
        type === '.docx'
      ) hasWord = true;
      else if (
        type === 'application/vnd.ms-powerpoint' ||
        type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ||
        type === '.ppt' ||
        type === '.pptx'
      ) hasPowerPoint = true;
      else others.push(type.replace(/^\s*\.\s*/, '').toUpperCase());
    });

    const result = [];
    if (hasImage) result.push('Imágenes');
    if (hasVideo) result.push('Videos');
    if (hasAudio) result.push('Audios');
    if (hasPdf) result.push('PDF');
    if (hasExcel) result.push('Excel');
    if (hasWord) result.push('Word');
    if (hasPowerPoint) result.push('PowerPoint');
    if (others.length) result.push(...others);

    return result.join(', ');
  }
}