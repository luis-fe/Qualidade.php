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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css" rel="stylesheet">


    <style>
        :root {
    --CorMenu: #002955;
    --Branco: white;
    --Cinza: lightGray;
    --Preto: black;
}

@import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

*,
::after,
::before {
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem;
    opacity: 1;
    overflow-y: hidden !important;
    margin: 0;
}

a {
    cursor: pointer;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
}

li {
    list-style: none;
}

h4 {
    font-family: 'Poppins', sans-serif;
    font-size: 1.275rem;
    color: var(--Branco);
}

/* Layout for admin dashboard skeleton */

.wrapper {
    align-items: stretch;
    display: flex;
    width: 100%;
}

#sidebar {
    max-width: 380px;
    min-width: 380px;
    background: var(--CorMenu);
    transition: all 0.35s ease-in-out;
    height: 100vh;
    min-height: 100vh;
    max-height: 100vh;
    overflow: auto;
    border-right: 1px solid gray;
}

.main {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    min-width: 0;
    overflow: hidden;
    transition: all 0.35s ease-in-out;
    width: 100%;
    background: var(--Branco);
    color: var(--Preto);
}

/* Sidebar Elements Style */

.sidebar-logo {
    padding: 1.30rem;
    margin-bottom: 30px;
}

.sidebar-logo a {
    color: var(--Branco);
    font-size: 1.30rem;
    font-weight: 600;
}

.sidebar-nav {
    list-style: none;
    margin-bottom: 0;
    padding-left: 0;
    margin-left: 0;
}


a.sidebar-link {
    margin: auto;
    padding: auto;
    width: 90%;
    padding: 1rem 1rem;
    color: var(--Branco);
    position: relative;
    display: block;
    font-size: 1.3rem;
    font-weight: 600;
    border-radius: 8px;
    margin-bottom: 10px;
}

a.sidebar-link .sidebar-link[data-bs-toggle="collapse"]::after {
    border: solid;
    border-width: 0 .075rem .075rem 0;
    content: "";
    display: inline-block;
    padding: 2px;
    position: absolute;
    right: 1.5rem;
    top: 1.4rem;
    transform: rotate(-135deg);
    transition: all .2s ease-out;
}

.sidebar-link[data-bs-toggle="collapse"].collapsed::after {
    transform: rotate(45deg);
    transition: all .2s ease-out;
}

#sidebar-link {
    font-size: 20px;
    padding: 10px;
    font-weight: 500;
    cursor: pointer;
}

#sidebar-link a {
    cursor: pointer;
}


.navbar-expand .navbar-nav {
    margin-left: auto;
}

.content {
    flex: 1;
    max-width: 100vw;
    width: 100vw;
}

@media (min-width:768px) {
    .content {
        max-width: auto;
        width: auto;
    }
}


/* Sidebar Toggle */

#sidebar.collapsed {
    margin-left: -380px;
}

/* Footer and Nav */

@media (max-width:767.98px) {

    .js-sidebar {
        margin-left: 0;
    }


    #sidebar {
        max-width: 380px;
        min-width: 380px;
    }

    a.sidebar-link {
        font-size: 1.2rem;
    }

    #sidebar-link {
        font-size: 1rem;
    }

    #sidebar.collapsed {
        margin-left: -380px;
    }

    .navbar,
    footer {
        width: 100vw;
    }
}

a.sidebar-link:hover {
    background-color: var(--Cinza);
    color: var(--Preto);
}

.sidebar-item.active>.sidebar-link {
    background-color: var(--Cinza);
    color: var(--Preto);
}

.sidebar-item.active>.sidebar-link::after {
    transform: rotate(-135deg);
}

.sidebar-link.selected {
    background-color: #f8f9fa;
    color: #000;
}

.btn-custom {
    background-color: var(--CorMenu);
    color: white;
    border: none;
    transition: background-color 0.3s, color 0.3s;
}

.btn-custom:hover {
    background-color: rgb(37, 112, 251);
    color: var(--Preto);
}


    </style>
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
                        <a href="../../Gestao/FilaDeFases/" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Fila de Fases
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../../Gestao/GestaoDeOps/" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Gestão de Op's
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../../Gestao/Metas/" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Meta dos Departamentos
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../../Gestao/MetasTerceirizados/" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Meta dos Terceirizados
                            </span>
                        </a>
                    </li>
                     <li class="sidebar-item">
                        <a href="../../Gestao/LeadTime/" class="sidebar-link">
                            <span>
                                <i class="fa-regular fa-circle pe-4" style="font-size: 10px;"></i>
                                Lead Time
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
