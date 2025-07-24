
// Product data structure to store information for each product
const productData = {
  calzados: {
    name: "Calzados",
    description: "CALZADO CON SUELA Y PARTE SUPERIOR DE CAUCHO O PLÁSTICO",
    antidumping: { completed: 85, hasData: true },
    permiso: { completed: 60, hasData: true },
    etiquetado: { completed: 25, hasData: true },
    documentos: { completed: 0, hasData: false },
  },
  "motos-electricas": {
    name: "Motos Eléctricas",
    description: "MOTOCICLETA ELÉCTRICA CON BATERÍA DE LITIO",
    antidumping: { completed: 0, hasData: false },
    permiso: { completed: 40, hasData: true },
    etiquetado: { completed: 0, hasData: false },
    documentos: { completed: 75, hasData: true },
  },
  textiles: {
    name: "Textiles",
    description: "PRODUCTOS TEXTILES DE ALGODÓN Y FIBRAS SINTÉTICAS",
    antidumping: { completed: 90, hasData: true },
    permiso: { completed: 0, hasData: false },
    etiquetado: { completed: 80, hasData: true },
    documentos: { completed: 30, hasData: true },
  },
  electronicos: {
    name: "Electrónicos",
    description: "DISPOSITIVOS ELECTRÓNICOS Y COMPONENTES",
    antidumping: { completed: 70, hasData: true },
    permiso: { completed: 85, hasData: true },
    etiquetado: { completed: 60, hasData: true },
    documentos: { completed: 90, hasData: true },
  },
  juguetes: {
    name: "Juguetes",
    description: "JUGUETES INFANTILES DE PLÁSTICO Y MATERIALES SEGUROS",
    antidumping: { completed: 0, hasData: false },
    permiso: { completed: 20, hasData: true },
    etiquetado: { completed: 95, hasData: true },
    documentos: { completed: 50, hasData: true },
  },
}

$(document).ready(() => {
  // Initialize with default product
  updateProductData("calzados")

  // Product selector change
  $("#productSelector").change(function () {
    const selectedProduct = $(this).val()
    updateProductData(selectedProduct)
    showNotification(`Producto cambiado a: ${productData[selectedProduct].name}`, "info")
  })

  // Tab switching functionality
  $(".tab-btn").click(function () {
    const tabId = $(this).data("tab")

    // Update button states
    $(".tab-btn").removeClass("active bg-blue-600 text-white").addClass("bg-gray-100 text-gray-700")
    $(this).removeClass("bg-gray-100 text-gray-700").addClass("active bg-blue-600 text-white")

    // Show/hide content
    $(".tab-content").addClass("hidden")
    $(`#${tabId}`).removeClass("hidden")
  })

  // Function to update product data and status indicators
  function updateProductData(productKey) {
    const product = productData[productKey]

    // Update product description in forms
    $(".product-description").val(product.description)
    $(".permit-name").attr("placeholder", `Permiso para ${product.name}`)

    // Update status indicators
    updateStatusIndicators(product)
  }

  function updateStatusIndicators(product) {
    const tabs = ["antidumping", "permiso", "etiquetado", "documentos"]

    tabs.forEach((tab) => {
      const indicator = $(`.status-indicator[data-tab="${tab}"]`)
      const data = product[tab]
      const percentage = data.completed
      const hasData = data.hasData

      // Update percentage text
      indicator.find(".text-xs.text-gray-600").text(`${percentage}%`)

      // Update status dot color based on completion
      const statusDot = indicator.find(".w-2.h-2.rounded-full")
      statusDot.removeClass("bg-green-500 bg-yellow-500 bg-red-500 bg-gray-400")

      if (percentage >= 80) {
        statusDot.addClass("bg-green-500")
      } else if (percentage >= 40) {
        statusDot.addClass("bg-yellow-500")
      } else if (percentage > 0) {
        statusDot.addClass("bg-red-500")
      } else {
        statusDot.addClass("bg-gray-400")
      }

      // Add click functionality to status indicators
      indicator.css("cursor", "pointer").click(() => {
        // Switch to the corresponding tab
        $(`.tab-btn[data-tab="${tab}"]`).click()
      })
    })
  }

  // Image upload functionality
  $(".image-upload-container").click(function () {
    if (!$(this).find("img").length) {
      $("#imageUpload").click()
    }
  })

  $("#imageUpload").change((e) => {
    const files = e.target.files
    const emptyContainers = $(".image-upload-container").filter(function () {
      return !$(this).find("img").length
    })

    for (let i = 0; i < Math.min(files.length, emptyContainers.length); i++) {
      const file = files[i]
      const reader = new FileReader()

      reader.onload = (e) => {
        const container = $(emptyContainers[i])
        const uploadDiv = container.find("div").first()

        uploadDiv.html(`
                    <img src="${e.target.result}" alt="Producto" class="max-h-full max-w-full object-contain">
                `)

        // Add remove button
        if (!container.find(".group").length) {
          container.addClass("group")
          container.append(`
                        <button class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity remove-image">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    `)
        }
      }

      reader.readAsDataURL(file)
    }
  })

  // Remove image functionality
  $(document).on("click", ".remove-image", function (e) {
    e.stopPropagation()
    const container = $(this).parent()
    const uploadDiv = container.find("div").first()

    uploadDiv.html(`
            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
            <span class="text-sm text-gray-500">Subir imagen</span>
        `)

    container.removeClass("group")
    $(this).remove()
  })

  // Form validation and interactions
  $("input, select, textarea")
    .on("focus", function () {
      $(this).parent().addClass("focused")
    })
    .on("blur", function () {
      $(this).parent().removeClass("focused")
    })

  // Auto-calculate total when prices change
  function updateTotal() {
    const declaredPrice = Number.parseFloat($('input[value="7.5"]').val()) || 0
    const antidumping = Number.parseFloat($('input[value="0.63"]').val()) || 0
    const total = declaredPrice + antidumping

    $('.text-green-600:contains("$")')
      .last()
      .text(`$${total.toFixed(2)}`)
  }

  $('input[type="number"]').on("input", updateTotal)

  // Smooth animations for cards
  $(".bg-white").hover(
    function () {
      $(this).addClass("shadow-md").removeClass("shadow-sm")
    },
    function () {
      $(this).addClass("shadow-sm").removeClass("shadow-md")
    },
  )

  // Success notifications
  $(".bg-green-600").click(() => {
    showNotification("Información guardada exitosamente", "success")
  })

  $(".bg-red-600").click(() => {
    if (confirm("¿Está seguro de que desea limpiar esta sección?")) {
      showNotification("Sección limpiada", "error")
    }
  })

  // Enhanced notification system
  function showNotification(message, type) {
    let bgColor, icon

    switch (type) {
      case "success":
        bgColor = "bg-green-500"
        icon = "fa-check-circle"
        break
      case "error":
        bgColor = "bg-red-500"
        icon = "fa-exclamation-circle"
        break
      case "info":
        bgColor = "bg-blue-500"
        icon = "fa-info-circle"
        break
      default:
        bgColor = "bg-gray-500"
        icon = "fa-bell"
    }

    const notification = $(`
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform">
                <div class="flex items-center">
                    <i class="fas ${icon} mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `)

    $("body").append(notification)

    setTimeout(() => {
      notification.removeClass("translate-x-full")
    }, 100)

    setTimeout(() => {
      notification.addClass("translate-x-full")
      setTimeout(() => {
        notification.remove()
      }, 300)
    }, 3000)
  }

  // Document upload functionality for permits
  $(".document-upload-container, .add-document-btn").click(() => {
    $("#documentUpload").click()
  })

  $("#documentUpload").change((e) => {
    const files = e.target.files

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      addDocumentToList(file)
    }
  })

  function addDocumentToList(file) {
    const fileSize = (file.size / 1024 / 1024).toFixed(2) // Convert to MB
    const fileIcon = getFileIcon(file.type)

    const documentItem = $(`
        <div class="document-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
            <div class="flex items-center">
                <i class="${fileIcon} text-2xl text-blue-600 mr-3"></i>
                <div>
                    <p class="font-medium text-gray-800">${file.name}</p>
                    <p class="text-sm text-gray-500">${fileSize} MB</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="text-blue-600 hover:text-blue-800 transition-colors">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-red-600 hover:text-red-800 transition-colors remove-document">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `)

    $("#documentList").append(documentItem)
  }

  function getFileIcon(fileType) {
    if (fileType.includes("pdf")) return "fas fa-file-pdf"
    if (fileType.includes("word") || fileType.includes("document")) return "fas fa-file-word"
    if (fileType.includes("image")) return "fas fa-file-image"
    return "fas fa-file"
  }

  // Remove document functionality
  $(document).on("click", ".remove-document", function () {
    $(this)
      .closest(".document-item")
      .fadeOut(300, function () {
        $(this).remove()
      })
  })

  // Auto-calculate permit costs
  function updatePermitTotal() {
    const baseCost = Number.parseFloat($('#permiso input[value="90"]').val()) || 0
    const processorCost = Number.parseFloat($('#permiso input[value="50"]').val()) || 0
    const subtotal = baseCost + processorCost
    const igv = subtotal * 0.18
    const total = subtotal + igv

    // Update the summary card
    $('.text-green-600:contains("S/.")').text(`S/. ${total.toFixed(2)}`)
  }

  // Update costs when permit values change
  $('#permiso input[type="number"]').on("input", updatePermitTotal)

  // Label image upload functionality
  $(".label-image-upload-container, .add-label-image-btn").click(() => {
    $("#labelImageUpload").click()
  })

  $("#labelImageUpload").change((e) => {
    const files = e.target.files

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      addLabelImageToList(file)
    }
  })

  function addLabelImageToList(file) {
    const reader = new FileReader()

    reader.onload = (e) => {
      const imageItem = $(`
        <div class="label-image-item relative group">
          <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200">
            <img src="${e.target.result}" alt="Etiqueta" class="w-full h-full object-cover">
          </div>
          <button class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity remove-label-image">
            <i class="fas fa-times text-xs"></i>
          </button>
          <div class="mt-2 text-xs text-gray-600 text-center truncate">${file.name}</div>
        </div>
      `)

      $("#labelImageList").append(imageItem)
    }

    reader.readAsDataURL(file)
  }

  // Remove label image functionality
  $(document).on("click", ".remove-label-image", function (e) {
    e.stopPropagation()
    $(this)
      .closest(".label-image-item")
      .fadeOut(300, function () {
        $(this).remove()
      })
  })

  // Special document upload functionality
  $(".special-document-upload-container, .add-special-document-btn").click(() => {
    $("#specialDocumentUpload").click()
  })

  $("#specialDocumentUpload").change((e) => {
    const files = e.target.files

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      addSpecialDocumentToList(file)
    }
  })

  function addSpecialDocumentToList(file) {
    const fileSize = (file.size / 1024 / 1024).toFixed(2) // Convert to MB
    const fileIcon = getSpecialFileIcon(file.type, file.name)

    const documentItem = $(`
      <div class="special-document-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
        <div class="flex items-center">
          <i class="${fileIcon} text-2xl text-orange-600 mr-3"></i>
          <div>
            <p class="font-medium text-gray-800">${file.name}</p>
            <p class="text-sm text-gray-500">${fileSize} MB</p>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <button class="text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-eye"></i>
          </button>
          <button class="text-green-600 hover:text-green-800 transition-colors">
            <i class="fas fa-check-circle"></i>
          </button>
          <button class="text-red-600 hover:text-red-800 transition-colors remove-special-document">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    `)

    $("#specialDocumentList").append(documentItem)
    updateDocumentProgress()
  }

  function getSpecialFileIcon(fileType, fileName) {
    const extension = fileName.split(".").pop().toLowerCase()

    if (fileType.includes("pdf") || extension === "pdf") return "fas fa-file-pdf"
    if (fileType.includes("word") || extension === "doc" || extension === "docx") return "fas fa-file-word"
    if (fileType.includes("excel") || extension === "xls" || extension === "xlsx") return "fas fa-file-excel"
    if (fileType.includes("image") || ["jpg", "jpeg", "png", "gif"].includes(extension)) return "fas fa-file-image"
    return "fas fa-file"
  }

  // Remove special document functionality
  $(document).on("click", ".remove-special-document", function () {
    $(this)
      .closest(".special-document-item")
      .fadeOut(300, function () {
        $(this).remove()
        updateDocumentProgress()
      })
  })

  // Update document progress
  function updateDocumentProgress() {
    const totalDocuments = $("#specialDocumentList .special-document-item").length
    const progress = Math.min((totalDocuments / 5) * 100, 100) // Assuming 5 documents needed

    $(".bg-orange-600.h-2.rounded-full").css("width", `${progress}%`)
    $(".text-center.text-sm.text-gray-600").text(`${Math.round(progress)}% Completado`)
  }

  // Checklist functionality
  $('input[type="checkbox"]').change(() => {
    const totalCheckboxes = $('input[type="checkbox"]').length
    const checkedBoxes = $('input[type="checkbox"]:checked').length
    const progress = (checkedBoxes / totalCheckboxes) * 100

    // Update any progress indicators if needed
    console.log(`Checklist progress: ${progress}%`)
  })

  // Initialize tooltips and help text
  $("[title]").each(function () {
    $(this).hover(
      function () {
        const tooltip = $(
          `<div class="absolute bg-gray-800 text-white text-xs rounded py-1 px-2 z-10">${$(this).attr("title")}</div>`,
        )
        $(this).append(tooltip)
        $(this).removeAttr("title")
      },
      function () {
        $(this).find(".absolute").remove()
      },
    )
  })
})
