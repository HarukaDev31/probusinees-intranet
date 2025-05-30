<script>
var currentPrivilege = "<?php echo $this->user->No_Grupo; ?>";
localStorage.setItem("currentPrivilege", currentPrivilege);
</script>
<div class="content-wrapper">

    <section class="content card" id="cotizacion-crons-container">
        <div class="card-header">
            <h1 class="card-title
                text-center">Contenedores Cron Consolidado</h1>
        </div>
        <div class="table-responsive" class="table table-bordered table-hover table-striped">
            <table id="table-crons-consolidado" class="table table-bordered table-hover table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>N°</th>
                        <th>Contenedor</th>
                        <th>Cotizacion</th>
                        <th>Fecha de Creacion</th>
                        <th>Fecha de Ejecucion</th>
                        <th>Data</th>
                        <th>Ejecutado </th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    <!-- Modal para mostrar los detalles del cron message, numero de telefono y fecha de ejecucion -->
    <div class="modal fade" id="cronDetailsModal" tabindex="-1" role="dialog" aria-labelledby="cronDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cronDetailsModalLabel">Detalles del Cron</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cronMessage">Mensaje del Cron:</label>
                        <textarea class="form-control" id="cronMessage" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="cronPhoneNumber">Númeroe Teléfono:</label>
                        <input type="text" class="form-control" id="cronPhoneNumber">
                    </div>
                    <div class="form-group">
                        <label for="cronExecutionTime">Tiempo de Ejecución:</label>
                        <input type="text" class="form-control" id="cronExecutionTime">
                    </div>
                    <div class="form-group">
                        <label for="cronExecutionDate">Fecha de Ejecución:</label>
                        <input type="text" class="form-control" id="cronExecutionDate" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveCronDetails">Guardar Detalles</button>
                </div>
            </div>
        </div>
    </div>

    <style scoped>
    * {
        font-family: Epilogue;
    }

    p {
        font-size: 14px;
    }

    .country-icons {
        width: 24px;
        height: 16px;
        margin-right: 5px;
        margin-top: -4px;
    }

    #steps {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1em;
        margin-top: 1em;
        flex-direction: column;
    }

    #steps-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1em;
        margin-top: 1em;
    }

    .step-container {
        display: flex;
        flex-direction: column;
        border: 1px solid #ccc;
        border-radius: 1em;
        align-items: center;
        margin-bottom: 10px;
        width: 200px;
        padding: 1em 2em;
        animation: fadeIn 1s forwards;
        min-width: 200px;
        /* Inicialmente ocultar los elementos */
        opacity: 0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Aplicar un retraso basado en el índice del hijo */
    .step-container:nth-child(1) {
        animation-delay: 0s;
    }

    .step-container:nth-child(2) {
        animation-delay: 0.2s;
    }

    .step-container:nth-child(3) {
        animation-delay: 0.4s;
    }

    .step-container:nth-child(4) {
        animation-delay: 0.6s;
    }

    .step-container:nth-child(4) {
        animation-delay: 0.8s;
    }

    .step-container:nth-child(5) {
        animation-delay: 1s;
    }

    .step-container:nth-child(6) {
        animation-delay: 1.2s;
    }

    .step-container-completed {
        display: flex;
        flex-direction: column;
        border: 1px solid #ccc;
        border-radius: 1em;
        align-items: center;
        margin-bottom: 10px;
        width: 200px;
        padding: 1em 2em;
        animation: fadeInBounce 1s forwards;
        min-width: 200px;
        /* Inicialmente ocultar los elementos */
        opacity: 0;
        background-color: #85C1E9;
        color: white;
    }

    .step-container-progress {
        display: flex;
        flex-direction: column;
        border: 1px solid #ccc;
        border-radius: 1em;
        align-items: center;
        margin-bottom: 10px;
        width: 200px;
        padding: 1em 2em;
        animation: fadeInBounce 1s forwards;
        min-width: 200px;
        /* Inicialmente ocultar los elementos */
        opacity: 0;
        background-color: #f4d03f;
        text-align: center;
        color: white;
    }

    @keyframes fadeInBounce {
        from {
            opacity: 0;
            transform: scale(0.5);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .step-container-completed span {
        text-align: center;
    }

    .step-container span {
        text-align: center;
    }

    .step-container:hover {
        background-color: #f9f9f9;
    }

    .collapse.show {

        visibility: visible;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .documentation-title {
        cursor: pointer;
        padding: 0 1em;
        height: 3em;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .documentation-title:hover {
        background-color: #d5dbdb;
    }

    .delete-folder-button:hover {
        cursor: pointer;
    }

    i:hover {
        cursor: pointer;
        color: #007bff;
    }

    .drag-drop-area {
        transition: all 0.3s ease;
    }

    .drag-over {
        background-color: rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }

    .context-menu {
        position: absolute;
        z-index: 50;
        min-width: 150px;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.25rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        top: 2rem;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .main-container {
        height: 100%;
        position: relative;
    }

    .file-list {
        position: absolute;
        bottom: 0;
        max-height: 100px;
        height: 100px;
        width: 500px;
        right: 1em;
        border-top-left-radius: 1em;
        border-top-right-radius: 1em;
        overflow-y: auto;
    }

    .file-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5em 1em;
        background-color: #f9f9f9;
        border-top-left-radius: 1em;
        border-top-right-radius: 1em;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
        background: #f9f9f9;
    }

    .progress-bar {
        height: 10px;
        background: #76c7c0;
        width: 0%;
        border-radius: 5px;
        transition: width 0.3s ease;
    }

    i {
        align-self: center;
        align-items: center;

    }

    i:hover {
        cursor: pointer;
        color: #5dade2;
    }

    #pending-files,
    #pending-files-inspection,
    #pending-files-inspection-coordinacion {
        background-color: #fff8e1;
        border: 1px solid #ffe082;
        padding: 1em;
        border-radius: 0.5em;
        position: absolute;
        bottom: 0;
        right: 1em;
        max-height: 80%;
        overflow-y: auto;
    }

    .progress-circle circle {
        transition: stroke-dashoffset 0.3s ease;
    }

    #pending-file-list .file-item,
    #pending-file-list-inspection .file-item,
    #pending-file-list-inspection-coordinacion .file-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    #pending-file-list .file-name,
    #pending-file-list-inspection .file-name,
    #pending-file-list-inspection-coordinacion .file-name {
        flex-grow: 1;
        padding-left: 10px;
        font-weight: 600;
    }

    #pending-file-list .text-sm,
    #pending-file-list-inspection .text-sm,
    #pending-file-list-coordinacion .text-xs {
        color: #666;
    }

    .file-section-container {
        width: 100%;
        height: 50vh;
        min-height: 600px;
        position: relative;
    }

    #file-grid,
    #file-grid-inspection,
    #file-grid-inspection-coordinacion {
        height: auto;
        width: 100%;
        gap: 1rem;
        max-height: 500px;
        min-height: 400px;
        overflow-y: auto;
    }

    h1 {
        font-weight: 600;
        font-size: 1.5em;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    .btn-block {
        font-size: 14px;
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .bg-orange {
        background-color: #FF500B !important;
        color: #fff !important;
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


    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        margin: 2px 0;
        white-space: nowrap;
        justify-content: flex-start;
    }

    div#table-contenedor_length {
        display: none;
    }

    .page-item.active .page-link {
        background-color: #FF500B;
        border-color: #FF500B;
    }

    .page-link {
        color: #585858;
    }

    .step-icon {
        padding-top: 10px;
        display: flex;
        width: 70px;
        height: 50px;
        align-items: center;
        justify-content: center;

    }

    div:where(.swal2-container) h2:where(.swal2-title) {
        font-weight: 400;
        font-size: 28px !important;
        line-height: 30px;
        color: #272A30;
        height: auto;
        min-height: 150px;
    }

    button.swal2-confirm.swal2-styled.swal2-default-outline {
        color: #585858;
    }


    button.swal2-confirm.swal2-styled.swal2-default-outline,
    button.swal2-cancel.swal2-styled.swal2-default-outline {
        margin-top: -4em;
        padding: .8em 2.8em;
    }

    .btn-primary {
        background-color: #FF500B;
        border-color: #FF500B;
    }

    .btn-primary:hover {
        background-color: #FF500B;
        border-color: #FF500B;
    }

    h5.modal-title {
        text-align: center;
        width: 100%;
        font-weight: 500;
        font-size: 25px !important;
    }

    label:not(.form-check-label):not(.custom-file-label) {
        font-weight: normal;
    }

    @media (min-width: 576px) {
        .modal-content {
            padding: 20px 70px;
        }
    }

    input#txt-Fe_Puerto,
    input#txt-Fe_Cierre,
    input#txt-Fe_Entrega {
        width: 115px;
    }

    .col-6.col-sm-6.fech {
        padding-left: 5%;
        padding-top: 5%;
    }

    .cont-fech {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .form-control.input-report.required.input-date::placeholder {
        text-align: center;
        color: #495057;
    }

    #txt-Empresa::placeholder {
        color: #495057;
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

    .table.embarque th {
        padding: .55rem;
    }

    /* Estilos para el cuadro de información del archivo subido */
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

    div#table-cotizacion-embarque_filter,
    div#table-contenedor_filter,
    div#table-clientes-general_filter,
    div#table-cotizacion-inspection_filter,
    div#table-cotizacion-inspection-coordinacion_filter,
    div#table-clientes-variacion_filter,
    div#table-cotizacion-prospectos_filter {
        display: none;
    }



    #table-contenedor_wrapper.dt-buttons.btn-group.flex-wrap {
        display: none;
    }

    .tab-cliente-documentacion {
        padding: 0.5em;
        border-radius: 0.5em;
        margin-bottom: 1em;
        width: 100%;
        border-width: 2px;
        border-color: #CDCDCD;
        color: #7E7E7E;
        text-align: center;
        cursor: pointer;

    }

    .tab-cliente-documentacion.active {
        background-color: #FFFFFF;
        color: black;
        border-width: 0px;

    }

    .documentos-clientes-tabs {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 1em;
        margin: 1em 2em;
    }

    .file-icon-container {
        display: flex;
        justify-content: space-between;
        width: -webkit-fill-available;
    }

    label {
        font-family: Epilogue;
        font-size: 14px;
        font-weight: 400;
        color: #272A30;
    }

    label>i {
        font-size: 20px;
    }

    /* Ocultar el texto del botón en pantallas pequeñas */
    @media (max-width: 768px) {

        #table-contenedor.table.table-hover.dataTable.no-footer tbody {
            background-color: transparent;
        }

        .fa-bars {
            font-size: 20px;
        }

        h1,
        .content-header h1 {
            font-size: 1.2rem;
        }

        body {
            font-size: 12px;
        }

        button>.fa {
            font-size: 15px !important;
            width: 20px;
            height: 20px;
            margin-top: 1px;
        }

        #table-cotizacion-embarque tbody {
            padding: 10px 20px;
        }

        .consolid-name {
            display: none !important;
        }

        .documentation-title {
            border-bottom: 1px #DFDFDF solid;
        }

        .documentation-title>h2>label,
        .title-inspection>label {
            font-size: 1rem !important;
        }

        .file-upload-box {
            padding: 0;
            height: 200px;
        }

        .form-horizontal {
            max-width: 100%;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        .file-preview>p {
            width: 70%;
            overflow-x: hidden;
            font-weight: 400;
            font-size: 12px;
        }

        .file-preview {
            width: 80%;
        }

        .file-format {
            font-size: 10px !important;
            margin-top: -5%;
            padding-bottom: 5%;
        }

        .file-item {
            padding: 8px;
            background: transparent;
        }

        #table-contenedor thead {
            display: none;
            /* Ocultar encabezados de la tabla */
        }

        #table-contenedor tbody tr:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
            background: #f7f7f7;
        }

        .view-eye {
            display: none;
        }

        .buyer {
            min-width: 20em !important;
        }

        .number {
            min-width: 10em !important;
        }

        #table-contenedor tbody tr {
            font-size: 11px;
            cursor: pointer;
            transition: box-shadow 0.2s;
            display: grid;
            grid-template-columns: 1fr 1fr;
            /* Dos columnas iguales */
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        #table-contenedor tbody td {
            align-content: center;
            gap: 0px;
            /* Espaciado entre   el select y el botón */
        }

        .form-horizontal>.row>div>.form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group>.file-upload-box {
            width: 80%;
        }

        #table-contenedor tr.odd>td,
        #table-contenedor tr.even>td {
            border: 0px solid transparent;
            padding: 0rem;
        }

        td>.form-control {
            height: auto;
            width: auto;
            border-radius: .40rem;
            border: 0px solid #ddd;
            font-size: 13px;
            padding: 9px 20px;
            margin: 1px;
        }

        .form-control:disabled {
            background-color: transparent;
            border: 0px solid #ddd;
            opacity: 1;
            font-size: 12px;
        }

        #btn-exportar-carga,
        #btn-filtrar-carga,
        #btn-cargar-carga {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0;
            /* Oculta el texto del botón */
        }

        #btn-exportar-carga i {
            margin-right: 0;
            /* Asegúrate de que el ícono esté centrado */
        }

        #btn-exportar-carga>.fa-upload,
        #btn-exportar-carga>.fa-download,
        #btn-filtrar-carga>.fa-filter,
        #btn-cargar-carga>.fa-upload {
            font-size: 20px;
            margin-right: -12px;
        }

        .note-container-container {
            min-height: 10%;
            padding-bottom: 25%;
            margin-top: 7%;
        }

        #btn-grd-doc-not {
            position: absolute;
            bottom: 0%;
            left: 0%;
            order: 99;
        }

        #txt-Id_Carga_Consolidada {
            height: 20vh;
        }

        .file-label>i {
            font-size: 2.5rem;
        }

        .file-label {
            flex-direction: column;
            justify-content: center;
            gap: 0px;
            height: 100%;
            font-size: 11px;
        }

        .file-group-text {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .file-group-text>span {
            font-size: 12px;
        }

        .file-format {
            margin-bottom: 0;
        }

        .title-inspection {
            justify-content: center !important;
        }

    }

    .scroll-arrow {
        position: absolute;
        top: 400px;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 10px;
        cursor: none;
        z-index: 10;
        border-radius: 50%;
        font-size: 16px;
        display: none;
        /* Ocultar inicialmente */
        justify-content: center;
        align-items: center;
    }

    .scroll-arrow.left {
        left: 10px;
    }

    .scroll-arrow.right {
        right: 10px;
    }

    .scroll-arrow:hover {
        background-color: rgba(0, 0, 0, 0.8);
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
    </style>