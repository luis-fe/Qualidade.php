<!DOCTYPE html>
<html lang="pt-Br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grupo Mpl - WMS Industrial</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    body {
      background-color: #f8f9fa;
      overflow-x: hidden;
    }

    /* --- NAVBAR --- */
    .navbar {
      background-color: #10045a;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 10px 20px;
      z-index: 1000; /* Abaixo da lateral */
      position: sticky;
      top: 0;
    }

    #btn-menu, #btn-user {
      cursor: pointer;
      font-size: 1.5rem;
      color: white;
    }

    .titulo-plataforma {
      color: white;
      font-weight: bold;
      letter-spacing: 1px;
      text-align: center;
      flex-grow: 1;
    }

    /* --- SIDEBAR (CAMADA ACIMA) --- */
    #sidebar {
      height: 100vh;
      width: 280px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #10045a;
      color: #fff;
      transition: transform 0.3s ease-in-out;
      transform: translateX(-100%);
      overflow-y: auto;
      padding: 15px;
      z-index: 2050; /* CAMADA MÁXIMA */
      box-shadow: 10px 0 20px rgba(0, 0, 0, 0.5);
    }

    #sidebar.ativo {
      transform: translateX(0);
    }

    /* Links do Menu */
    #sidebar .nav-link {
      color: white;
      font-weight: 500;
      margin-bottom: 5px;
      border-radius: 7px;
      padding: 12px 15px;
      display: flex;
      align-items: center;
      transition: background 0.2s;
    }

    #sidebar .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }

    .icon-main {
      margin-right: 12px;
      font-size: 0.85rem;
      color: #00d4ff;
    }

    /* --- SUBMENU --- */
    .submenu-list {
      list-style: none;
      padding-left: 30px;
      margin-bottom: 10px;
    }

    .submenu-list .nav-link {
      font-size: 0.9rem !important;
      opacity: 0.85;
    }

    .rotate-icon {
      transition: transform 0.3s ease;
    }

    [aria-expanded="true"] .rotate-icon {
      transform: rotate(180deg);
    }

    /* --- OVERLAY --- */
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.6);
      z-index: 2000; /* Abaixo da sidebar, acima do conteúdo */
      backdrop-filter: blur(3px);
    }

    .sidebar-overlay.ativo {
      display: block;
    }

    /* Conteúdo Principal */
    .content-area {
      padding: 20px;
      position: relative;
      z-index: 1; /* Camada base */
    }
  </style>
</head>

<body>

  <div class="sidebar-overlay" id="overlay"></div>

  <nav class="navbar d-flex align-items-center">
    <i class="fa-solid fa-bars" id="btn-menu"></i>
    <div class="titulo-plataforma d-none d-sm-block">
      PLATAFORMA DE GESTÃO INDUSTRIAL
    </div>
    <i class="bi bi-person-circle" id="btn-user"></i>
  </nav>

  <div id="sidebar">
    <div class="text-center mb-5">
      <img src="../../../templates/logo-grupompl.png" alt="Logo" style="width: 70%; margin-top: 20px;">
    </div>

    <ul class="nav flex-column">
      <li><a href="../FilaDeFases" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Fila de Fases</a></li>
      <li><a href="../GestaoDeOps" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Gestão de Op's</a></li>
      <li><a href="../LeadTime" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Lead Time</a></li>
      <li><a href="../Metas" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Metas</a></li>
      <li><a href="../Orcamento" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Orçamentos</a></li>
      <li><a href="../ControlePilotos" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Controle Pilotos</a></li>
      <li><a href="../Procedimentos" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Procedimentos</a></li>
      <li><a href="../Automacao" class="nav-link"><i class="bi bi-caret-right-fill icon-main"></i> Controle Automação</a></li>

      <li class="nav-item">
        <a href="#submenuAlmoxarifado" data-bs-toggle="collapse" class="nav-link justify-content-between" aria-expanded="false">
          <span><i class="bi bi-caret-right-fill icon-main"></i> Almoxarifado Aviamentos</span>
          <i class="bi bi-chevron-down rotate-icon"></i>
        </a>
        
        <div class="collapse" id="submenuAlmoxarifado">
          <ul class="submenu-list">
            <li><a href="../GestaoEnderecos" class="nav-link"><i class="bi bi-caret-right me-2"></i> Gestão Endereço</a></li>
            <li><a href="../Recebimento" class="nav-link"><i class="bi bi-caret-right me-2"></i> Recebimento</a></li>
            <li><a href="../AlmoxarifadoAviamentos" class="nav-link"><i class="bi bi-caret-right me-2"></i> Gestão Separação</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </div>



  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    $(document).ready(function () {
      // Abrir menu
      $('#btn-menu').on('click', function () {
        $('#sidebar').addClass('ativo');
        $('#overlay').addClass('ativo');
      });

      // Fechar menu ao clicar fora ou no overlay
      $('#overlay').on('click', function () {
        $('#sidebar').removeClass('ativo');
        $(this).removeClass('ativo');
      });
    });
  </script>
</body>

</html>