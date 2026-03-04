<?php include_once("../../../templates/Loading.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- ICONS -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../../templates/style.css" />
  <title>Sidebar</title>
</head>

<body>
  <div id="container">
    <div id="sidebar">
      <div class="menu-btn">
        <i class="ph-bold ph-caret-left"></i>
      </div>
      <div class="head">
        <div class="logo">
          <img src="../../../templates/ImagemMplSemFundo.png" alt="" />
        </div>
      </div>
      <div id="nav">
        <div class="menu">
          <ul>
            <!-- <li>
              <a href="#">
                <i class="icon ph-bold ph-house"></i>
                <span class="text">Home</span>
              </a>
            </li> -->
            <!-- <li>
              <a href="#">
                <i class="icon ph-bold ph-folder-open"></i>
                <span class="text">Cadastros</span>
                <i class="arrow ph-bold ph-caret-down"></i>
              </a>
              <ul class="sub-menu">
                <li><a href="#"><span class="text">Caixa</span></a></li>
                <li><a href="#"><span class="text">Endereço</span></a></li>
                <li><a href="#"><span class="text">Perfil</span></a></li>
                <li><a href="#"><span class="text">Usuário</span></a></li>
              </ul>
            </li> -->
            <li>
              <a href="../../Terceirizados/Dashboards">
                <i class="icon ph-bold ph-speedometer"></i>
                <span class="text">Dashboards</span>
              </a>
            </li>
            <li>
              <a href="../../Terceirizados/StatusOps">
                <i class="icon ph-bold ph-device-mobile-camera"></i>
                <span class="text">Apontar Status</span>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="icon ph-bold ph-speedometer"></i>
                <span class="text">Relatórios</span>
                <i class="arrow ph-bold ph-caret-down"></i>
              </a>
              <ul class="sub-menu">
                <li><a href="#"><span class="text">Metas dos Terceirizados</span></a></li>
                <li><a href="../../Terceirizados/GestaoStatusOps/"><span class="text">Status das Op's</span></a></li>
              </ul>
            </li>
            <li>
              <a href="#">
                <i class="icon ph-bold ph-headset"></i>
                <span class="text">Suporte</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="menu">
        <ul>
          <li>
            <a href="#">
              <i class="icon ph-bold ph-sign-out"></i>
              <span class="text">Logout</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="main">
      <nav class="navbar navbar-dark navbar-expand px-3 border-bottom" style="position: sticky; top: 0; z-index: 7">
        <button class="btn custom-toggler" id="sidebar-toggle" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>
        <label for="" id="Rotina"></label>
        <div class="navbar-collapse">
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <span class="pe-4"><?php echo $usuario; ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end">
                <a href="#" class="dropdown-item" onclick="$('#modal-alterar-senha').modal('show');">
                  <i class="fas fa-unlock-keyhole pe-4"></i> Alterar Senha
                </a>
                <a href="../../../templates/Logout/" class="dropdown-item">
                  <i class="fas fa-arrow-right-from-bracket pe-4"></i> Sair
                </a>
              </div>
            </li>
          </ul>
        </div>
      </nav>
