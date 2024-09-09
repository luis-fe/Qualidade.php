<?php


if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];

    $empresaAtual = $_SESSION['empresa'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.2.1/css/fixedHeader.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css">
    <link rel="website icon" type="jpg" href="../../../templates/imagens/ImagemMpl.jpg">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../templates/style.css">
    <link rel="stylesheet" href="../../../css/Cores.css">
    <style>


    </style>

    <title>Grupo Mpl</title>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar" class="collapsed">
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">Gestão à Vista</a>
                </div>
                <!-- Sidebar Navigation -->
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a href="../../../Pcp/Gestao_a_Vista/GestaoDeOps" class="sidebar-link">
                            <i class="fa-solid fa-house pe-2"></i>
                            <span>Gestão de Op's</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../../../Pcp/Gestao_a_Vista/FilaDeFases" class="sidebar-link">
                            <i class="fa-solid fa-house pe-2"></i>
                            <span>Fila de Fases</span>
                        </a>
                    </li>
                     <li class="sidebar-item">
                        <a href="../../../Pcp/Gestao_a_Vista/LeadTime" class="sidebar-link">
                            <i class="fa-solid fa-house pe-2"></i>
                            <span>Lead Time</span>
                        </a>
                    </li>

                </ul>
            </div>
        </aside>

        <div class="main">
            <nav class="navbar navbar-dark" style="display: flex; align-items: center;">
                <div class="menu-rotina-container">
                    <button class="btn menu-btn" type="button" title="Menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <label for="text" id="NomeRotina" style="color: white; font-size: 30px"></label>
                </div>
            </nav>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
            <script src="../../../templates/sidebar.js"></script>
</body>

</html>
