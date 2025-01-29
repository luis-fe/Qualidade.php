<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    .form-label {
        font-weight: bold;
        color: #555;
    }

    .inputs-gerais {
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 15px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .inputs-gerais:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        background-color: #fff;
    }
</style>
<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-arrow-down-up"></i></span> Itens Substitutos
</div>


<div class="col-12 mt-2" id="opcoes" style="flex-wrap: wrap">
    <button class="btn btn-geral" style="width: 100px" onclick="$('#modal-substitutos').modal('show');">
        <span><i class="fa-solid fa-pencil"></i></span>
        Novo
    </button>
</div>

<div class="col-12 div-substitutos mt-3" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-substitutos" style="width: 100%;">
            <thead>
                <tr>
                    <th>Código Mp Original<br><input type="search" class="search-input search-input-substitutos"></th>
                    <th>Descrição Mp Original<br><input type="search" class="search-input search-input-substitutos"></th>
                    <th>Código Mp Substituto<br><input type="search" class="search-input search-input-substitutos"></th>
                    <th>Descrição Mp Substituto<br><input type="search" class="search-input search-input-substitutos"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-venda d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-substitutos" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-substitutos" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-substitutos" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Substitutos</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-substituto" onsubmit="Salvar_Substitutos(); return false;">
                <div class="modal-body col-12" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <div class="mb-5 col-12" id="inputs-container-original">
                        <div class="mb-3 col-12"><label class="fw-bold">Item Original</label></div>
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <label class="fw-bold">Código Item</label>
                                <input type="text" class="inputs-gerais col-12" required id="input-codigo-original" onkeypress="if (event.key === 'Enter') { event.preventDefault(); Consulta_Item($('#input-codigo-original').val(), 'input-descricao-original')}" />
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="fw-bold">Descrição Item</label>
                                <input type="text" id="input-descricao-original" readonly class="inputs-gerais col-12" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12" id="inputs-container-substituto">
                        <div class="mb-3 col-12"><label class="fw-bold">Item Substituto</label></div>
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <label class="fw-bold">Código Item</label>
                                <input type="text" id="input-codigo-substituto" required class="inputs-gerais col-12" onkeypress="if (event.key === 'Enter') { event.preventDefault(); Consulta_Item($('#input-codigo-substituto').val(), 'input-descricao-substituto')}" />
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="fw-bold">Descrição Item</label>
                                <input type="text" id="input-descricao-substituto" readonly class="inputs-gerais col-12" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-salvar">
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
<script src="script1.js"></script>
