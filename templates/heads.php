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
                    <a href="#">Wms Mpl</a>
                </div>
                <!-- Sidebar Navigation -->
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a href="../../../Wms/src/Inicio" class="sidebar-link">
                            <i class="fa-solid fa-house pe-2"></i>
                            <span>Home</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#Cadastros" aria-expanded="false" aria-controls="Cadastros">
                            <i class="fa-regular fa-square-plus pe-3"></i>
                            Cadastros
                        </a>
                        <ul id="Cadastros" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/CadEnderecos" class="sidebar-link">Endereços</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/QrCodes" class="sidebar-link">QR CODE DAS CAIXAS</a>
                            </li>

                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#Configuracoes" aria-expanded="false" aria-controls="Configuracoes">
                            <i class="fa-solid fa-gears pe-3"></i>
                            Configurações
                        </a>
                        <ul id="Configuracoes" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/Chamados" class="sidebar-link">Chamados</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/Usuarios" class="sidebar-link">Usuários</a>
                            </li>

                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#PreFaturamento" aria-expanded="false" aria-controls="PreFaturamento">
                            <i class="fa-solid fa-filter-circle-dollar pe-3"></i>
                            Pré-Faturamento
                        </a>
                        <ul id="PreFaturamento" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/Monitor Pedidos" class="sidebar-link">Monitor de Pedidos</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/ReservaPedidos" class="sidebar-link">Reserva de Pedidos</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                            <i class="fa-solid fa-gauge pe-3"></i>
                            Relatórios
                        </a>
                        <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/AnaliseSubstitutos" class="sidebar-link">Análise de Substitutos</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/ConsultaEstoque" class="sidebar-link">Consulta de Estoque</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/ConsumoEmbalagens" class="sidebar-link">Consumo de Embalagens</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/DistribuicaoPedidos" class="sidebar-link">Distribuição de Pedidos</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/Fila Reposicao" class="sidebar-link">Fila de Reposição</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/Inventario" class="sidebar-link">Inventário</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/Prioridade Reposicao" class="sidebar-link">Prioridade de Reposição</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/TagsEmConferencia" class="sidebar-link">Tag's em Conferência</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/Tags_X_Fisico" class="sidebar-link">Tag's x Físico</a>
                            </li>
                            <li class="sidebar-item sidebar-item-2">
                                <a href="../../../Wms/src/VerificaTag" class="sidebar-link">Verificar Tag</a>
                            </li>
                        </ul>

        <ul>
            <li class="nav-item logout-item mt-auto">
                            <a href="/templates/Logout/logout.php" class="nav-link menu-item text-danger">
                                <i class="bi bi-box-arrow-right icon-menu"></i> Sair do WMS
                            </a>
            </li>
        </ul>

                    </li>
                    <!-- <li class="sidebar-header">
                        Multi Level Nav
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#multi"
                            aria-expanded="false" aria-controls="multi">
                            <i class="fa-solid fa-share-nodes pe-2"></i>
                            Multi Level
                        </a>
                        <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#multi-two" aria-expanded="false" aria-controls="multi-two">
                                    Two Links
                                </a>
                                <ul id="multi-two" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="#" class="sidebar-link">Link 1</a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="#" class="sidebar-link">Link 2</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li> -->
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

<script>
    var empresaAtual = <?php echo json_encode($empresaAtual); ?>;
    console.log(empresaAtual)
    if (empresaAtual === '4') {
        var menusParaEsconder = [
            "PreFaturamento",
        ];

        menusParaEsconder.forEach(function(menuId) {
            var menu = document.getElementById(menuId);
            if (menu) {
                menu.closest('.sidebar-item').style.display = 'none';
            }
        });
    }

    if (empresaAtual === '4') {
        var itensParaEsconder = [
            "Fila de Reposição",
            "QR CODE DAS CAIXAS",
            "Análise de Substitutos",
            "Tag's em Conferência"
        ];

        itensParaEsconder.forEach(function(itemText) {
            var items = document.querySelectorAll('.sidebar-link');
            items.forEach(function(item) {
                if (item.textContent.trim() === itemText) {
                    item.parentElement.style.display = 'none';
                }
            });
        });
    }
</script>

</html>
