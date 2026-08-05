<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<!-- jQuery, Bootstrap, SweetAlert e ícones vêm de headerGestao.php /
     footerGestao.php, como nas demais telas do módulo -->
<link rel="stylesheet" href="style.css">

<!-- ============================================================
     Título congelado — fica abaixo da navbar do cabeçalho.
     O deslocamento (top) é a altura real da navbar, medida pelo script,
     senão o título some atrás dela em telas de fonte maior.
     ============================================================ -->
<header class="titulo-fixo">
  <i class="bi bi-clipboard2-check"></i>
  <h1>Apontamento Qualidade</h1>
</header>

<main class="tela">

  <!-- ============================================================
       Frame do apontamento — OP, data e ação
       ============================================================ -->
  <section class="frame frame-apontamento">

    <!-- Quem está apontando. Fica visível o tempo todo para ninguém apontar
         no nome de outra pessoa sem perceber. -->
    <div class="responsavel oculto" id="blocoResponsavel">
      <i class="bi bi-person-badge"></i>
      <span class="responsavel-rotulo">Responsável</span>
      <span class="responsavel-nome" id="responsavelNome"></span>
      <button type="button" class="responsavel-trocar" id="btnTrocarResponsavel"
              title="Trocar responsável" aria-label="Trocar responsável">
        <i class="bi bi-pencil"></i>
      </button>
    </div>

    <div class="campo">
      <label for="campoOp">Informe a OP</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
        <input type="text"
               id="campoOp"
               class="form-control"
               inputmode="numeric"
               autocomplete="off"
               enterkeyhint="done"
               placeholder="Ex.: 123456">
      </div>
    </div>

    <div class="campo">
      <label for="campoData">Data Apontamento</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
        <input type="date" id="campoData" class="form-control">
      </div>
    </div>

    <button type="button" class="btn btn-geral btn-apontar" id="btnApontar">
      <i class="bi bi-check2-circle"></i> Apontar
    </button>

  </section>

  <!-- ============================================================
       Retângulo da câmera — no formato da câmera do WhatsApp:
       palco escuro com o vídeo ao vivo e a barra de controles logo
       abaixo, com o obturador ao centro.
       ============================================================ -->
  <section class="camera">

    <div class="camera-palco">
      <!-- playsinline evita o player em tela cheia do iOS -->
      <video id="cameraVideo" playsinline muted autoplay></video>

      <!-- Fora da tela: só serve para extrair o quadro capturado -->
      <canvas id="cameraCanvas" class="camera-canvas"></canvas>

      <!-- Estado inicial / erro de permissão. A câmera só liga por toque,
           porque o navegador exige gesto do usuário para pedir permissão. -->
      <div class="camera-cortina" id="cameraCortina">
        <i class="bi bi-camera-fill" id="cameraCortinaIcone"></i>
        <p id="cameraCortinaTexto">Ative a câmera para registrar a peça.</p>
        <button type="button" class="btn-ligar" id="btnLigarCamera">
          <i class="bi bi-camera-video-fill"></i> Ativar câmera
        </button>
      </div>

      <!-- Alternar frontal/traseira -->
      <button type="button" class="camera-virar oculto" id="btnVirarCamera" title="Alternar câmera" aria-label="Alternar câmera">
        <i class="bi bi-arrow-repeat"></i>
      </button>

      <span class="camera-contador oculto" id="cameraContador">0</span>
    </div>

    <div class="camera-controles">

      <!-- Alternativa quando a câmera ao vivo não está disponível
           (http sem TLS, permissão negada, navegador antigo) -->
      <label class="camera-acao" for="campoArquivo" title="Escolher da galeria">
        <i class="bi bi-images"></i>
        <input type="file" id="campoArquivo" accept="image/*" capture="environment" multiple hidden>
      </label>

      <button type="button" class="btn-capturar" id="btnCapturar">
        <span class="btn-capturar-anel"></span>
        <span class="btn-capturar-texto">Capturar</span>
      </button>

      <button type="button" class="camera-acao" id="btnLimparFotos" title="Descartar todas as fotos" aria-label="Descartar todas as fotos">
        <i class="bi bi-trash3"></i>
      </button>

    </div>

    <!-- Miniaturas do que já foi capturado -->
    <div class="fotos" id="fotos"></div>

  </section>

</main>

<!-- ============================================================
     Modal do responsável — só aparece quando não há usuário
     identificado na página nem nome guardado de um apontamento anterior.
     ============================================================ -->
<div class="modal fade" id="modalResponsavel" tabindex="-1" aria-labelledby="modalResponsavelTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-responsavel">

      <div class="modal-header">
        <h5 class="modal-title" id="modalResponsavelTitulo">
          <i class="bi bi-person-badge"></i> Responsável pelo apontamento
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <form id="formResponsavel" novalidate>

        <div class="modal-body">
          <label for="campoResponsavel">Nome do responsável</label>
          <input type="text"
                 id="campoResponsavel"
                 class="form-control caixa-alta"
                 autocomplete="off"
                 autocapitalize="characters"
                 spellcheck="false"
                 enterkeyhint="done"
                 maxlength="60"
                 placeholder="NOME DO RESPONSÁVEL">

          <p class="modal-ajuda">
            O nome fica guardado nesta tela e será usado nos próximos apontamentos,
            sem perguntar de novo.
          </p>

          <p class="modal-aviso" id="responsavelAviso" role="alert"></p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">
            <i class="bi bi-x-lg"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-geral">
            <i class="bi bi-check2"></i> Confirmar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>

<script src="script.js"></script>
