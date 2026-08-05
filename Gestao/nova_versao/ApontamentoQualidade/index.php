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

    <!-- O responsável não ocupa espaço aqui: aparece no cabeçalho, ao lado
         do ícone de usuário, e é trocado clicando nele -->

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

    <!-- Abre a sessão da OP: trava os campos, mostra a câmera e permite
         gravar vários apontamentos seguidos sem redigitar a OP -->
    <button type="button" class="btn btn-geral btn-iniciar" id="btnIniciarApontamento">
      <i class="bi bi-play-circle"></i> Iniciar Apontamento
    </button>

  </section>

  <!-- ============================================================
       Sessão da OP — só aparece depois de "Iniciar Apontamento".
       Enquanto estiver aberta, a mesma OP recebe quantos apontamentos
       forem necessários, um a cada "Gravar apontamento".
       ============================================================ -->
  <section class="sessao oculto" id="sessao">

    <div class="sessao-barra">
      <div class="sessao-info">
        <span class="sessao-rotulo">OP</span>
        <span class="sessao-valor" id="sessaoOp"></span>
      </div>
      <div class="sessao-info">
        <span class="sessao-rotulo">Data</span>
        <span class="sessao-valor" id="sessaoData"></span>
      </div>
      <div class="sessao-info">
        <span class="sessao-rotulo">Gravados</span>
        <span class="sessao-valor" id="sessaoGravados">0</span>
      </div>
      <button type="button" class="sessao-encerrar" id="btnEncerrarSessao">
        <i class="bi bi-box-arrow-right"></i> Encerrar
      </button>
    </div>

    <!-- Retângulo da câmera, no formato da câmera do WhatsApp -->
    <div class="camera">

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

      <!-- Capturar logo abaixo do retângulo da câmera, entre a galeria
           (alternativa quando a câmera ao vivo não abre) e o descarte -->
      <div class="camera-controles">

        <label class="camera-acao" for="campoArquivo" title="Escolher da galeria">
          <i class="bi bi-images"></i>
          <input type="file" id="campoArquivo" accept="image/*" capture="environment" multiple hidden>
        </label>

        <button type="button" class="btn-capturar" id="btnCapturar">
          <i class="bi bi-camera-fill"></i> Capturar
        </button>

        <button type="button" class="camera-acao" id="btnLimparFotos" title="Descartar todas as fotos" aria-label="Descartar todas as fotos">
          <i class="bi bi-trash3"></i>
        </button>

      </div>

      <!-- Miniaturas do que já foi capturado -->
      <div class="fotos" id="fotos"></div>

    </div>

    <button type="button" class="btn btn-geral btn-gravar" id="btnGravarApontamento">
      <i class="bi bi-check2-circle"></i> Gravar apontamento
    </button>

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
