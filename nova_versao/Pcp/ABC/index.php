<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-graph-up-arrow"></i></span> ABC
</div>

<div class="col-12 mt-2" id="opcoes" style="flex-wrap: wrap">
    <button class="btn btn-geral" style="width: 100px" onclick="$('#modal-parametros').modal('show');">
        <span><i class="fa-solid fa-pencil"></i></span>
        Novo
    </button>
</div>

<div class="col-12 div-abc" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-abc" style="width: 100%;">
            <thead>
                <tr>
                    <th>Descrição ABC<br><input type="search" class="search-input form-control"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-abc d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-abc" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-abc" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-parametros" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Novo Parâmetro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-parametros" onsubmit="Cadastrar_Parametro(); return false;">
                <div class="modal-body">
                    <div class="col-12">
                        <label for="input-parametro" class="form-label">Parâmetro</label>
                        <input type="text" class="form-control" id="input-parametro" placeholder="Parâmetro" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-salvar" style="width: 100px" id="btn-salvar-parametro">
                        <span><i class="bi bi-floppy"></i></span>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script.js"></script>
