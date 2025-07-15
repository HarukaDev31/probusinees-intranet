<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'ProBusiness'; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url("assets/ico/favicon.ico?ver=10.0.0"); ?>">
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url("plugins_v2/fontawesome-free/css/all.min.css"); ?>">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo base_url("bower_components/bootstrap/dist/css/bootstrap.min.css"); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url("plugins_v2/fontawesome-free/css/all.min.css"); ?>">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?php echo base_url("dist_v2/css/adminlte.min.css"); ?>">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/style_v2.css"); ?>">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?php echo base_url("plugins_v2/sweetalert2/sweetalert2.min.css"); ?>">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .content-wrapper {
            background-color: #f4f6f9;
            min-height: 100vh;
            margin-left: 0 !important;
            padding: 20px !important;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
        }
        .card-body {
            padding: 1.25rem;
        }
        .btn {
            margin-right: 0.25rem;
        }
        .table img {
            border-radius: 4px;
        }
        .badge {
            font-size: 0.75em;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        .modal-content {
            border: 1px solid #dee2e6;
            border-radius: 0.3rem;
        }
    </style>
</head>
<body class="hold-transition">
    <div class="content-wrapper" style="margin-left: 0; padding: 20px;"> 