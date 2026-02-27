<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bullseye"></i></span> Controle Automação
</div>
<div class="alert alert-info shadow-sm" role="alert">
  <p class="mb-0">
    <i class="bi bi-info-circle-fill me-"></i>
    Nessa tela estão listados os serviços de automação, que é a sincronização entre o ERP e a Plataforma.
  </p>
</div>


<div class="col-12 div-metas" style="background-color: lightgray; border-radius: 8px; padding: 10px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 800px; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
        <thead style="position: sticky; top: 0; background-color: #003366; z-index: 10;">
                <tr>
                    <th>ID</th>
                    <th>Serviço</th>
                    <th>Data</br>Atualizacao</th>
                    <th>Hora</br>Atualizacao</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>