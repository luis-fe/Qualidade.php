<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<!DOCTYPE html>
<html lang="pt-Br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mapa de Endereços - Grupo Mpl</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    body {
      background-color: #f8f9fa;
      overflow-x: hidden;
    }

    /* --- NAVBAR E SIDEBAR --- */
    .navbar {
      background-color: #10045a;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 10px 20px;
      z-index: 1000;
      position: sticky;
      top: 0;
    }

    #btn-menu, #btn-user { cursor: pointer; font-size: 1.5rem; color: white; }
    .titulo-plataforma { color: white; font-weight: bold; letter-spacing: 1px; text-align: center; flex-grow: 1; }

    #sidebar {
      height: 100vh; width: 280px; position: fixed; top: 0; left: 0;
      background-color: #10045a; color: #fff;
      transition: transform 0.3s ease-in-out; transform: translateX(-100%);
      overflow-y: auto; padding: 15px; z-index: 2050;
      box-shadow: 10px 0 20px rgba(0, 0, 0, 0.5);
    }
    #sidebar.ativo { transform: translateX(0); }
    #sidebar .nav-link { color: white; font-weight: 500; margin-bottom: 5px; border-radius: 7px; padding: 12px 15px; display: flex; align-items: center; transition: background 0.2s; }
    #sidebar .nav-link:hover { background-color: rgba(255, 255, 255, 0.15); }
    .icon-main { margin-right: 12px; font-size: 0.85rem; color: #00d4ff; }
    
    .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.6); z-index: 2000; backdrop-filter: blur(3px); }
    .sidebar-overlay.ativo { display: block; }

    /* --- ESTILOS DO MAPA DE ENDEREÇOS --- */
    .content-area {
      padding: 30px 20px;
      position: relative;
      z-index: 1;
    }

    .titulo-pagina {
      color: #10045a;
      font-weight: 800;
      text-transform: uppercase;
      margin-bottom: 15px; 
      border-left: 5px solid #00d4ff;
      padding-left: 15px;
    }

    /* Contêiner de cada Rua */
    .rua-container {
      background-color: #ffffff;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      height: 100%;
    }

    .rua-titulo {
      text-align: center;
      font-size: 1.2rem;
      font-weight: bold;
      color: #10045a;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 2px dashed #ccc;
    }

    /* Grid dos Quadrados */
    .grid-enderecos {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
      gap: 5px;
    }

    /* O Quadrado (Card do Endereço) */
    .endereco-card {
      aspect-ratio: 1 / 1; 
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 0.65rem; 
      border-radius: 4px;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1);
      text-align: center;
      padding: 2px;
      word-break: break-all;
    }

    .endereco-card:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      z-index: 10;
    }

    /* Cores de Status */
    .status-vazio { background-color: #198754; }   
    .status-ocupado { background-color: #101ff1e3; } 
    .status-multi { background-color: #fd7e14; }   

  </style>
</head>

<body>

  <div class="sidebar-overlay" id="overlay"></div>

  <div id="sidebar">
    <div class="text-center mb-5">
      <img src="../../../templates/logo-grupompl.png" alt="Logo" style="width: 70%; margin-top: 20px;">
    </div>
    <ul class="nav flex-column">
      <li><a href="../Voltar" class="nav-link"><i class="bi bi-arrow-left-circle icon-main"></i> Voltar ao Menu</a></li>
    </ul>
  </div>

  <div class="content-area">
    <div class="container-fluid">
      
      <h2 class="titulo-pagina">Mapa de Endereços</h2>

      <div class="row align-items-center mb-4">
        
        <div class="col-12 col-xl-8 mb-3 mb-xl-0">
          <div class="row g-2">
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm" style="background-color: #6a6970; color: white; border-radius: 8px;">
                <div class="card-body text-center py-2 px-1">
                  <div style="font-size: 0.65rem; opacity: 0.8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Total</div>
                  <div class="fw-bold fs-5 mt-1" id="ind-total">0</div>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm" style="background-color: #6a6970; color: white; border-radius: 8px;">
                <div class="card-body text-center py-2 px-1">
                  <div style="font-size: 0.65rem; opacity: 0.8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Cheio</div>
                  <div class="fw-bold fs-5 mt-1" id="ind-cheio">0</div>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm" style="background-color: #6a6970; color: white; border-radius: 8px;">
                <div class="card-body text-center py-2 px-1">
                  <div style="font-size: 0.65rem; opacity: 0.8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Vazio</div>
                  <div class="fw-bold fs-5 mt-1" id="ind-vazio">0</div>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm" style="background-color: #6a6970; color: white; border-radius: 8px;">
                <div class="card-body text-center py-2 px-1">
                  <div style="font-size: 0.65rem; opacity: 0.8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Utilização</div>
                  <div class="fw-bold fs-5 mt-1" id="ind-taxa">0%</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-xl-4">
          <div class="d-flex flex-column justify-content-center p-2" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05); background: white; border-radius: 8px; border: 1px solid #dee2e6; height: 100%; min-height: 56px;">
            <div class="d-flex justify-content-around align-items-center">
              <div class="d-flex align-items-center gap-1" style="font-weight: 600; font-size: 0.75rem; color: #333;">
                <div class="status-vazio" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                <span>Vazio</span>
              </div>
              <div class="d-flex align-items-center gap-1" style="font-weight: 600; font-size: 0.75rem; color: #333;">
                <div class="status-ocupado" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                <span>Ocupado (1)</span>
              </div>
              <div class="d-flex align-items-center gap-1" style="font-weight: 600; font-size: 0.75rem; color: #333;">
                <div class="status-multi" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                <span>Cheio (2+)</span>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="row g-4" id="mapa-container">
         <div class="col-12 text-center text-muted py-5">
             <div class="spinner-border text-primary" role="status"></div>
             <p class="mt-2 fw-bold">Carregando mapa de endereços...</p>
         </div>
      </div> 
      
    </div>
  </div>

  <div class="modal fade" id="modalOpcoesEndereco" tabindex="-1" aria-labelledby="modalOpcoesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #10045a; color: white;">
          <h5 class="modal-title fs-6" id="modalOpcoesLabel">
            <i class="bi bi-geo-alt me-2 text-info"></i>Opções do Endereço
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p class="mb-3">Endereço selecionado:<br><strong id="modalEnderecoSelecionado" class="fs-4 text-primary"></strong></p>
          
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-info text-white fw-bold" id="btnConsultarEndereco">
              <i class="bi bi-search me-2"></i> Consultar
            </button>
            <button type="button" class="btn btn-danger fw-bold" id="btnExcluirEndereco">
              <i class="bi bi-trash me-2"></i> Excluir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modalTabelaItens" tabindex="-1" aria-labelledby="modalTabelaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #10045a; color: white;">
          <h5 class="modal-title fs-6" id="modalTabelaLabel">
            <i class="bi bi-box-seam me-2 text-info"></i>Itens no Endereço: <span id="tituloEnderecoTabela" class="fw-bold text-info"></span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div class="table-responsive" style="max-height: 60vh;">
            <table class="table table-striped table-hover mb-0" style="font-size: 0.85rem;">
              <thead class="table-dark sticky-top" style="background-color: #10045a;">
                <tr>
                  <th>Código</th>
                  <th>Seq</th>
                  <th>Descrição</th>
                  <th class="text-center">Qtd</th>
                  <th>Data/Hora</th>
                  <th>Usuário</th>
                </tr>
              </thead>
              <tbody id="tbodyItensEndereco">
                </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" style="background-color: #f8f9fa;">
           <button type="button" class="btn btn-secondary btn-sm fw-bold" data-bs-target="#modalOpcoesEndereco" data-bs-toggle="modal">
              <i class="bi bi-arrow-left me-1"></i> Voltar
           </button>
           <button type="button" class="btn btn-danger btn-sm fw-bold" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#btn-menu').on('click', function () {
        $('#sidebar').addClass('ativo');
        $('#overlay').addClass('ativo');
      });

      $('#overlay').on('click', function () {
        $('#sidebar').removeClass('ativo');
        $(this).removeClass('ativo');
      });
    });
  </script>

  <script src="app.js"></script>

</body>
</html>