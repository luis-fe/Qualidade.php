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
     O deslocamento (top) é a altura real da navbar, medida pelo script.
     ============================================================ -->
<header class="titulo-fixo">
  <i class="bi bi-images"></i>
  <h1>Consulta Apontamento</h1>
</header>

<main class="tela">

  <!-- ============================================================
       Frame da consulta — OP e ação
       ============================================================ -->
  <section class="frame frame-consulta">

    <div class="campo">
      <label for="campoOP">OP / Referência apontada</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
        <input type="text"
               id="campoOP"
               class="form-control"
               autocomplete="off"
               enterkeyhint="search"
               placeholder="Ex.: 037594">
      </div>
    </div>

    <button type="button" class="btn btn-geral btn-consultar" id="btnConsultar">
      <i class="bi bi-search"></i> Consultar
    </button>

  </section>

  <!-- ============================================================
       Resultado — barra com o resumo e a galeria de fotos.
       Cada card traz a foto e o motivo do defeito logo abaixo.
       ============================================================ -->
  <section class="resultado oculto" id="resultado">

    <div class="resultado-barra">
      <div class="resultado-info">
        <span class="resultado-rotulo">OP</span>
        <span class="resultado-valor" id="resultadoOP">—</span>
      </div>
      <div class="resultado-info">
        <span class="resultado-rotulo">Fotos</span>
        <span class="resultado-valor" id="resultadoTotal">0</span>
      </div>
    </div>

    <div class="galeria" id="galeria"></div>

  </section>

  <!-- Consulta sem retorno: OP digitada ainda não tem apontamento com foto -->
  <p class="estado-vazio oculto" id="estadoVazio">
    <i class="bi bi-camera"></i>
    Nenhum apontamento com foto encontrado para a OP <strong id="opSemFoto"></strong>.
  </p>

</main>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>
