<?php
include_once("../../templates/header1.php");
include_once("../../templates/loading1.php");
?>
<link rel="stylesheet" href="style.css">


<label for="" style="font-size: 30px; font-weight: 700; color: black;">Metas</label>
<div class="container" style="background-color: rgb(179, 179, 179); border-radius: 5px; height: calc(100vh - 160px); max-height: calc(100vh - 160px); overflow: auto; min-width: 100%; width: 100%; padding-top: 25px; padding-left: 30px;">
    <div class="row text-align-start justify-content-start mb-1">
        <div class="row mt-3 mb-3 align-items-end">
            <div class="col-12 col-md-2 d-flex align-items-center">
                <div class="input-group">
                    <input type="search" id="codigoPlano" class="form-control" placeholder="Plano" style="background-color: white; color: black" onkeydown="ConsultaLote(event)">
                    <span class="input-group-text" id="search-icon" onclick="document.getElementById('codigoPlano').focus()" style="background-color: white; color: black">
                        <i class="lni lni-search-alt"></i>
                    </span>
                </div>
            </div>
            <div class="col-12 col-md-5 d-flex align-items-center mt-3 mt-md-0">
                <div class="input-group">
                    <input type="search" id="DescPlano" readonly class="form-control" placeholder="Descrição" style="background-color: white; color: black">
                </div>
            </div>
        </div>


        <div class="row mt-3 col-12 mb-3 align-items-end d-none" id="selects">
            <div class="col-12 col-md-3">
                <label for="Lote" class="form-label">Lote</label>
                <select class="form-select" id="SelectLote" onchange="SelecaoLote()">
                </select>
            </div>
            <div class="col-12 col-md-3"></div>
            <div class="col-12 col-md-6 d-flex justify-content-end align-items-center">
                <div class="input-group col-12 d-none" id="campo-search">
                    <input type="search" id="search" class="form-control" placeholder="Buscar" aria-label="Pesquisar" aria-describedby="search-icon" style="background-color: white; color: black">
                    <span class="input-group-text" id="search-icon" onclick="document.getElementById('search').focus()" style="background-color: white; color: black">
                        <i class="lni lni-search-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="container-tabela">
            <table id="table" class="table table-custom table-striped d-none" style="width: 100%;">
                <thead id="fixed-header">
                    <tr>
                        <th scope="col">Sequencia</th>
                        <th scope="col">Cod. Fase <span><i class="fa-solid fa-chevron-down no-sort" style="font-size: 8px; cursor: pointer" onclick="console.log('teste')"></i></span></th>
                        <th scope="col">Nome Fase</th>
                        <th scope="col">Previsão de Peças</th>
                        <th scope="col">Falta Programar</th>
                        <th scope="col">Carga</th>
                        <th scope="col">Fila</th>
                        <th scope="col">Falta Produzir</th>
                        <th scope="col">Qtd. Dias</th>
                        <th scope="col">Meta Dia</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="container3" style="margin-top: 1rem; width: 100%">
            <div class="col-12 align-items-center text-align-center justify-content-center" id="Paginacao">
            </div>
        </div>
    </div>
</div>

<?php include_once("../../templates/footer1.php"); ?>
