<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>


<link rel="stylesheet" href="style.css">

<div class="d-flex align-items-center gap-2 mt-3 mb-3">
    <i class="bi bi-calendar fs-4 text-primary"></i>
    <h3 class="mb-0">Cronograma de Atividades</h3>
</div>

<div class="col-12 mt-4 mb-4 div-analise" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <div class="d-flex justify-content-start mb-2 dt-buttons-container"></div>
        <table class="table table-bordered" id="table-abc" style="width: 100%;">
            <thead>
                <tr>
                    <th>Atividade<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Data Inicio<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Data Prev.<br>Final</br><input type="search" class="search-input search-input-analise"></th>
                    <th>Responsavel<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Status<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Projeto<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Descricao<br>Atividade</br></th>
                    <th>Ação<br></th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-analise d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-analise" class="input-itens" type="text" value="15" min="1">
        </div>
        <div id="pagination-analise" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>


<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script.js"></script>
