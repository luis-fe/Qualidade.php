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

    <!-- Rótulo, ícone e placeholder mudam conforme o modo escolhido em
         "Iniciar Apontamento": o mesmo campo recebe a Tag ou a OP/Referência -->
    <div class="campo">
      <label for="campoIdentificacao" id="rotuloIdentificacao">Informe a OP / Referência</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-upc-scan" id="iconeIdentificacao"></i></span>
        <input type="text"
               id="campoIdentificacao"
               class="form-control"
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
        <span class="sessao-rotulo" id="sessaoTipo">OP</span>
        <span class="sessao-valor" id="sessaoIdentificacao"></span>
      </div>
      <div class="sessao-info">
        <span class="sessao-rotulo">Data</span>
        <span class="sessao-valor" id="sessaoData"></span>
      </div>
      <div class="sessao-info">
        <span class="sessao-rotulo">Gravados</span>
        <span class="sessao-valor" id="sessaoGravados">0</span>
      </div>
      <button type="button" class="sessao-reler oculto" id="btnRelerTag">
        <i class="bi bi-qr-code-scan"></i> Outra tag
      </button>
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

        <!-- Mira da leitura de QR Code. Só no modo Tag: em OP/Referência
             a câmera serve apenas para fotografar. -->
        <div class="mira oculto" id="mira">
          <div class="mira-alvo"></div>
          <p class="mira-texto">Aponte para o QR Code da tag</p>
          <button type="button" class="mira-digitar" id="btnDigitarTag">
            <i class="bi bi-keyboard"></i> Digitar a tag
          </button>
        </div>
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
     Modal do modo — perguntado a cada "Iniciar Apontamento", porque a
     forma de identificar a peça muda de uma série para outra.
     ============================================================ -->
<div class="modal fade" id="modalModo" tabindex="-1" aria-labelledby="modalModoTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-claro">

      <div class="modal-header">
        <h5 class="modal-title" id="modalModoTitulo">
          <i class="bi bi-signpost-split"></i> Como deseja apontar?
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body">
        <div class="opcoes-modo">

          <button type="button" class="opcao-modo" data-modo="TAG">
            <i class="bi bi-qr-code-scan"></i>
            <span class="opcao-modo-titulo">Por Tag</span>
            <span class="opcao-modo-ajuda">Leia ou digite a tag da peça</span>
          </button>

          <button type="button" class="opcao-modo" data-modo="OP">
            <i class="bi bi-upc-scan"></i>
            <span class="opcao-modo-titulo">Por OP / Referência</span>
            <span class="opcao-modo-ajuda">Informe a OP ou a referência do produto</span>
          </button>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- ============================================================
     Modal do defeito — abre a cada captura. A foto só entra no
     apontamento depois de classificada.
     ============================================================ -->
<div class="modal fade" id="modalMotivo" tabindex="-1" aria-labelledby="modalMotivoTitulo" aria-hidden="true">
  <!-- sem modal-dialog-scrollable: o <form> envolve corpo e rodapé e
       quebraria o cálculo de altura do Bootstrap, cortando os campos em
       telas baixas. A lista de motivos rola por conta própria. -->
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-claro">

      <div class="modal-header">
        <h5 class="modal-title" id="modalMotivoTitulo">
          <i class="bi bi-clipboard2-x"></i> Tipo de defeito
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <form id="formMotivo" novalidate>

        <div class="modal-body">

          <img id="previaFoto" class="previa-foto" alt="Prévia da foto capturada">

          <label for="buscaMotivo">Motivo do defeito</label>
          <input type="search"
                 id="buscaMotivo"
                 class="form-control"
                 autocomplete="off"
                 placeholder="Filtrar motivo...">

          <div class="lista-motivos" id="listaMotivos"></div>

          <label for="campoObservacao" class="rotulo-observacao">Observação do defeito</label>
          <input type="text"
                 id="campoObservacao"
                 class="form-control"
                 autocomplete="off"
                 maxlength="200"
                 placeholder="Detalhe o defeito (opcional)">

          <p class="modal-aviso" id="motivoAviso" role="alert"></p>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">
            <i class="bi bi-trash3"></i> Descartar foto
          </button>
          <button type="submit" class="btn btn-geral">
            <i class="bi bi-check2"></i> Confirmar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- ============================================================
     Modal do responsável — só aparece quando não há usuário
     identificado na página nem nome guardado de um apontamento anterior.
     ============================================================ -->
<div class="modal fade" id="modalResponsavel" tabindex="-1" aria-labelledby="modalResponsavelTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-claro">

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

<!-- ============================================================
     Modal de liberação da câmera — abre sozinho quando a página é
     servida por http (sem TLS). O navegador bloqueia a câmera ao vivo
     em contexto inseguro e NÃO exibe o popup nativo de permissão; este
     popup guia a liberação única da origem no Chrome do Android.
     ============================================================ -->
<div class="modal fade" id="modalLiberarCamera" tabindex="-1" aria-labelledby="modalLiberarCameraTitulo" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-claro">

      <div class="modal-header">
        <h5 class="modal-title" id="modalLiberarCameraTitulo">
          <i class="bi bi-camera-fill"></i> Tirar foto da peça
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body">
        <p class="modal-ajuda modal-ajuda--destaque">
          Toque no botão abaixo para abrir a câmera do celular e registrar a peça.
        </p>

        <button type="button" class="btn btn-geral btn-bloco" id="btnUsarCameraCelular">
          <i class="bi bi-camera"></i> Usar a câmera do celular
        </button>

        <!-- Passos técnicos escondidos: só quem precisa da leitura de QR
             (câmera ao vivo) abre este bloco -->
        <details class="liberar-avancado">
          <summary>Ativar câmera ao vivo e leitura de QR (avançado)</summary>

          <p class="modal-ajuda">
            Uma vez por celular, no Chrome do Android:
          </p>

          <ol class="passos-liberar">
            <li>Abra este endereço na barra do Chrome:
              <div class="campo-copiar">
                <input type="text" id="flagUrl" readonly
                       value="chrome://flags/#unsafely-treat-insecure-origin-as-secure">
                <button type="button" class="btn btn-copiar" data-copiar="#flagUrl" title="Copiar">
                  <i class="bi bi-clipboard"></i>
                </button>
              </div>
            </li>
            <li>Cole a <strong>origem</strong> desta página no campo da flag:
              <div class="campo-copiar">
                <input type="text" id="origemPagina" readonly>
                <button type="button" class="btn btn-copiar" data-copiar="#origemPagina" title="Copiar">
                  <i class="bi bi-clipboard"></i>
                </button>
              </div>
            </li>
            <li>Mude para <strong>Enabled</strong> e toque em <strong>Relaunch</strong>.</li>
            <li>Volte aqui e toque em <strong>Já liberei, recarregar</strong>.</li>
          </ol>

          <button type="button" class="btn btn-cancelar btn-bloco" id="btnJaLiberei">
            <i class="bi bi-arrow-clockwise"></i> Já liberei, recarregar
          </button>

          <p class="modal-aviso">
            <i class="bi bi-apple"></i> No iPhone (Safari) essa opção não existe —
            use a câmera do celular acima.
          </p>
        </details>
      </div>

    </div>
  </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>

<!-- Leitura de QR Code do quadro da câmera, usada só no modo Tag -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script src="script.js"></script>
