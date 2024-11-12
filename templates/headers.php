<?php
    include_once("helpers/url.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $BASE_URL ?>/css/header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Portal da Qualidade</title>
</head>
<body>
<div class="header">
        <i class="bi bi-list" id="icon-menu"></i>
        <h3>Portal da Qualidade</h3>
    </div>
<div class="Menu" id="Menu">
    <div class="sidebar">
        <ul>
            <li>
                <a href="#">
                    <i class="bi bi-folder-fill"></i> 
                    CONFIGURAÇÕES
                    <div class="teste">
                        <i class="bi bi-chevron-right"></i>
                        <i class="bi bi-chevron-down" style="display: none"></i>
                    </div>
                </a>
                <ul>
                    <li><a  href="<?=$BASE_URL ?>GerenciamentoUsuarios.php">GERENCIAMENTO DE USUÁRIOS</a></li>
                    <li><a  href="<?=$BASE_URL ?>GerenciamentoLinhas.php">GERENCIAMENTO DE LINHAS</a></li>
                </ul>
            </li>
        </ul>
        <ul>
            <li>
                <a href="#">
                    <i class="bi bi-folder-fill"></i> 
                    RELATÓRIOS
                    <div class="teste">
                        <i class="bi bi-chevron-right"></i>
                        <i class="bi bi-chevron-down" style="display: none"></i>
                    </div>
                </a>
                <ul>
                    <li><a  href="ListaOps.php">LISTA DE OP'S</a></li>
                    <li><a  href="#">2ª QUALIDADE</a></li>
                    <li><a  href="TelaCaixas.php">GERENCIAMENTO DE CAIXAS</a></li>
                    <li><a  href="Carrinhos.php">GERENCIAMENTO DE CARRINHOS</a></li>
                    <li><a  href="DashboardProdutividade.php">DASHBOARD PRODUTIVIDADE</a></li>
                </ul>
            </li>
        </ul>
        <ul>
            <li>
                <a href="Logout.php">
                <i class="bi bi-power" style="color: red; font-size: 20px; font-weight: bold;"></i>
                    SAIR
                </a>
            </li>
        </ul>
    </div>

</div>
</body>
<script>
    const Menu = document.getElementById('Menu');
    const IconMenu = document.getElementById('icon-menu');
    let MenuVisible = false;

    IconMenu.addEventListener('click', () => {
        MenuVisible = !MenuVisible;

        if (MenuVisible) {
            Menu.style.display = 'block';
            setTimeout(() => {
                Menu.classList.add('active');
            }, 10);
        } else {
            Menu.classList.remove('active');
            setTimeout(() => {
                Menu.style.display = 'none';
            }, 500); // Ajuste o tempo de acordo com a duração da transição no CSS
        }
    });

    const menuItems = document.querySelectorAll('.sidebar ul li');

menuItems.forEach(item => {
    item.addEventListener('click', () => {
        const submenu = item.querySelector('ul');

        if (submenu) {
            submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';

            const chevronDown = item.querySelector('.bi-chevron-down');
            const chevronRight = item.querySelector('.bi-chevron-right');

            if (submenu.style.display === 'block') {
                chevronDown.style.display = 'inline';
                chevronRight.style.display = 'none';
            } else {
                chevronDown.style.display = 'none';
                chevronRight.style.display = 'inline';
            }
        }
    });
});


</script>
</html>
