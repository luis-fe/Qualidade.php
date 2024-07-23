<?php

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo Mpl</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.lineicons.com/2.0/LineIcons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.2.1/css/fixedHeader.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css" rel="stylesheet">


    <link rel="stylesheet" href="../../templates/style.css">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <!-- Content For Sidebar -->
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">Grupo Mpl</a>
                </div>
                <ul class="sidebar-nav">

                    <li class="sidebar-item">
                        <a href="../../Gestao/FilaDeFases" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Fila de Fases
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../../Gestao/GestaoDeOps" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Gestão de Op's
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../../Gestao/Metas" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Meta dos Departamentos
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <div class="main">
            <nav class="navbar navbar-dark navbar-expand px-3 border-bottom" style="background-color: var(--CorMenu);">
                <button class="btn custom-toggler" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- <div class="navbar-collapse navbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                <span class="pe-4" style="color: white;">joao.ferreira</span><i class="fa-solid fa-angle-down" style="font-size: 10px; color: white;"></i>

                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item"><i class="fa-solid fa-gear pe-4"></i>Configurações</a>
                                <a href="#" class="dropdown-item"><i class="fa-solid fa-unlock-keyhole pe-4"></i>Alterar
                                    Senha</a>
                                <a href="#" class="dropdown-item"><i class="fa-solid fa-arrow-right-from-bracket pe-4"></i>Sair</a>
                            </div>
                        </li>
                    </ul>
                </div> -->
            </nav>
            <main class="content px-3 py-2">