<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>


<link rel="stylesheet" href="style.css">


<div class="col-12 mt-4 mb-4 div-analise" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-abc'" style="width: 100%;">
            <thead>
                <tr>
                    <th>Atividade<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Data Inicio<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Data prevista<br>final</br><input type="search" class="search-input search-input-analise"></th>
                    <th>Responsavel<br><input type="search" class="search-input search-input-analise"></th>
                    <th>status<br><input type="search" class="search-input search-input-analise"></th>
                    <th>projeto<br><input type="search" class="search-input search-input-analise"></th>
                    <th>descricao<br>Atividade</br></th>
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
            <input id="itens-analise" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-analise" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>
