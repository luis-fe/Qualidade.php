
<!DOCTYPE html>
<html lang="pt-Br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo Mpl</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        /* Sidebar Styles */
        #sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #10045a;
            color: #fff;
            transition: transform 0.3s ease;
            transform: translateX(-100%);
            overflow: hidden;
            padding: 5px 15px;
            border-radius: 10px;
            box-shadow: 4px 0 8px rgba(0, 0, 0, 0.2);
        }

        #sidebar.ativo {
            transform: translateX(0);
        }

        #sidebar .nav-link {
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 4px;
            border-radius: 7px;
            padding: 10px 0px;
            padding-left: 10px;
        }

        #sidebar .nav-link:hover {
            background-color: rgba(93, 77, 199, 0.5);
        }

        .menu-item.active {
            background-color: rgba(93, 77, 199, 0.5);
        }


        .fa-chevron-down {
            margin-right: 15px;
            margin-top: 8px !important;
            font-size: 0.9rem !important;
            transition: transform 0.3s ease;
        }

        .fa-chevron-down.rotate {
            transform: rotate(180deg);
        }

        .submenu {
            display: none;
            margin-left: 20px;
        }

        .submenu.show {
            display: block;
        }

        .submenu ul.show {
            display: block;
        }


        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .content {
            margin-left: 0;
            display: block;
            flex-wrap: nowrap;
            min-width: 100%;
        }

        .content.open {
            margin-left: 280px;
        }

        .content>* {
            flex: 0 0 auto;
        }

        .navbar {
            z-index: 999;
            position: sticky;
            top: 0;
            background-color: #10045a;
            border-bottom: 1px solid rgb(238, 233, 233);
            border-radius: 0 0 15px 15px;
            padding: 8px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 1.5rem;
        }

        .navbar .navbar-brand i {
            font-size: 2rem;
        }

        body {
            overflow-x: hidden;
        }

        #btn-menu,
        #btn-user {
            cursor: pointer;
            font-size: 1.9rem;
            color: white;
            margin-right: 50px;
        }

        .icon-menu {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .fa-chevron-down {
            position: absolute;
            right: 10px;
            font-size: 1.2rem;
            color: white;
        }

        .submenu-item i {
            margin-right: 8px;
            color: white;
            font-size: 0.4rem;
            vertical-align: middle;
        }


        .titulo-tela {
            min-width: 100%;
            width: 100%;
            border-bottom: 1px solid lightgray;
            padding: 10px;
            background-color: white;
            margin-top: -40px;
        }

        .span-icone {
            background-color: #10045a;
            color: white;
            padding: 5px;
            border-radius: 40%
        }

        .menu-rotina {
            display: flex;
            justify-content: start;
            padding: 0px 10px;
            border-bottom: 1px solid lightgray;
            margin-top: 15px;
            min-width: 100%;
        }

        .corpo {
            padding: 0px 10px;
        }

        /* BOTÕES */

        .btn-menu {
            border: 1px solid white;
            background-color: lightgray;
            padding: 5px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
            text-wrap: nowrap;
        }

        .btn-menu i {
            margin-right: 8px;
            color: #10045a;
        }

        .btn-menu:hover i {
            margin-right: 8px;
            color: white;
        }

        .btn-menu:hover {
            border-color: #10045a;
            background-color: #10045a;
            color: white;
        }

        .btn-menu-clicado {
            background-color: #10045a;
            color: white;
        }

        .btn-menu-clicado i {
            margin-right: 8px;
            color: white;
        }

        .btn-geral {
            border: 1px solid lightgray;
            border-radius: 30px;
            width: 180px;
            margin-bottom: 10px;
            margin-right: 8px;
        }

        .btn-geral:hover {
            background-color: #10045a;
            border: 1px solid #10045a;
            color: white;
        }

        .btn-salvar {
            border: 1px solid #0056b3;
            border-radius: 30px;
            background-color: #0056b3;
            color: white;
            margin-bottom: 10px;
            margin-right: 8px;
        }

        .btn-salvar:hover {
            background-color: rgb(50, 120, 211);
            border: 1px solid rgb(50, 120, 211);
            color: white;
        }

        .btn-table {
            border-radius: 10px;
            font-size: 15px;
            color: white;
            padding: 5px 10px;
        }


        .btn-close-custom {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: darkred;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            border: 1px solid black;
            border-radius: 5px;
        }

        .btn-close-custom::before,
        .btn-close-custom::after {
            content: '';
            position: absolute;
            width: 2px;
            height: 70%;
            background-color: white;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .btn-close-custom::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        .btn-close-custom {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: darkred;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            border: 1px solid black;
            border-radius: 5px;
        }

        .btn-close-custom::before,
        .btn-close-custom::after {
            content: '';
            position: absolute;
            width: 2px;
            height: 70%;
            background-color: white;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .btn-close-custom::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        /* SELECT 2 */

        .select2-container .select2-selection--single {
            height: 40px !important;
            padding: 5px 20px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid lightgray;
            background-color: white;
            transition: all 0.3s ease-in-out;

        }

        .select2-container--default .select2-results__option {
            padding: 5px 10px;
            font-size: 1rem;
            color: #495057;
            border-bottom: 1px solid #e1e1e1;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .select2-container--default .select2-results__option:hover {
            background-color: #10045a;
            color: white;
            cursor: pointer;
        }

        .select2-container .select2-selection__arrow {
            height: 35px;
            top: 50%;
            margin-top: 7px;
            right: 10px;
        }

        .select2-container--default .select2-selection__placeholder {
            color: black !important;
        }

        /* DATATABLES */

        .search-input {
            border-radius: 10px;
            outline: none;
            font-size: 1rem;
            padding: 0px 5px;
            height: 30px;
            border: 2px solid lightgray
        }


        .table {
            min-width: 100%;
        }

        .table th {
            background-color: white;
            color: black;
            text-align: center;
            font-size: 1rem;
            position: relative;
            border-top: 1px solid lightgray;
            white-space: nowrap;
        }

        .table td {
            font-size: 1rem;
            white-space: nowrap;
        }

        .dataTables_wrapper {
            display: block;
        }

        .custom-pagination-container {
            justify-content: space-between;
            align-items: center;
            background-color: lightgray;
            padding: 0px 5px;
            padding-bottom: 5px;
            padding-top: 5px;
            border-radius: 8px;
        }

        .dataTables_paginate {
            display: flex;
            gap: 5px;
        }

        .dataTables_info {
            font-size: 0.8rem;
        }

        .paginate_button {
            border: 1px solid #10045a;
            background-color: #10045a;
            color: white;
            border-radius: 5px;
            padding: 0px 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .table th input {
            margin-top: 5px;
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }

        .input-itens {
            border: 1px solid black;
            border-radius: 8px;
            width: 30px;
            text-align: center;
            padding: 1px;
        }

        .btn-tabelas {
            padding: 4px 10px !important;
            border: 1px solid black !important;
            background-color: white !important;
            margin-left: 5px !important;
            margin-top: 10px;
            margin-bottom: 10px;
            border-radius: 30px !important;
        }

        .btn-tabelas:hover {
            transform: scale(1.05);
        }

        .dataTables_filter {
            display: none;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="navbar-brand">
                <i class="fa-solid fa-bars" id="btn-menu"></i>
            </div>
            <div class="ms-auto">
                <i class="bi bi-person-circle" id="btn-user"></i>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="w-100" style="padding: 0; justify-content: center; align-items: center; text-align: center">
            <img src="../../templates/logo-grupompl.png" alt="" style="width: 60%; margin-top: 80px; margin-bottom: 20px">
        </div>
    </div>

    <!-- Content -->
    <div class="container mt-2 content">


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Sidebar Toggle
                $('#btn-menu').on('click', function() {
                    $('#sidebar').toggleClass('ativo');
                    $('.content').toggleClass('open');
                });

                // Menu item click
                $('.menu-item').on('click', function() {
                    $(this).toggleClass('active');
                    $(this).next('.submenu').toggleClass('show');
                    $(this).find('.fa-chevron-down').toggleClass('rotate');
                    // Close other open items
                    $('.menu-item').not(this).each(function() {
                        $(this).removeClass('active');
                        $(this).next('.submenu').removeClass('show');
                        $(this).find('.fa-chevron-down').removeClass('rotate');
                    });
                });
            });
        </script>
