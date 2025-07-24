let mainContainer;
let createProductContainer;
$(document).ready(() => {
    mainContainer = $("#main-container");
    createProductContainer = $("#create-product-container");
    $("#btn-crear-producto").click(() => {
        mainContainer.hide();
        createProductContainer.show();
    })
    $("#back-btn").click(() => {
        createProductContainer.hide();
        mainContainer.show();
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
        showNotification("Producto guardado exitosamente", "success")
    })

    $(".bg-red-600").click(() => {
        if (confirm("¿Está seguro de que desea eliminar este producto?")) {
            showNotification("Producto eliminado", "error")
        }
    })

    // Notification system
    function showNotification(message, type) {
        const bgColor = type === "success" ? "bg-green-500" : "bg-red-500"
        const icon = type === "success" ? "fa-check-circle" : "fa-exclamation-circle"

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
