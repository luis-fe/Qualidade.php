<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    .menus {
        display: flex;
        justify-content: start;
        padding: 0px 10px;
        margin-top: 15px;
    }

    .dataTables_wrapper {
        display: block;
    }

    .custom-pagination-container {
        justify-content: space-between;
        align-items: center;
        background-color: lightgray;
        padding: 5px;
        border-radius: 8px;
    }

    .button-filtros {
        border: 1px solid black !important;
        background-color: white !important;
        margin-bottom: 10px !important;
        margin-left: 0px !important;
        margin-right: -7px !important;
        width: 70px;
        padding: 2px 0px;
    }

    .button-filtros:hover {
        background-color: lightgray !important;
    }

    tfoot tr th {
    background-color: #003366 !important;
    color: white !important;
    font-weight: bold !important;
    text-align: center !important;
}

#CabecalhoModal th {
    background-color: #003366 !important;
    color: white !important;
    font-weight: bold !important;
    text-align: center !important;
}

#formato_descricao {
    max-width: 150px !important;
    width: 150px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}



#table-falta-produzir-categorias tbody tr:hover {
    background-color: rgb(199, 225, 252) !important;
    cursor: pointer;
}

#modal-cargaOP_fase .modal-dialog {
    width: 90vw !important;
    max-width: 90vw !important;
    margin: auto !important;
}

#modal-cargaOP_fase .modal-content {
    width:
    
    100% !important;
}


#modal-realizadoDia .modal-dialog {
    width: 90vw !important;
    max-width: 90vw !important;
    margin: auto !important;
}

#modal-realizadoDia .modal-content {
    width:
    
    100% !important;
}



#modal-realizadoDia thead th,
#modal-realizadoDia tfoot th {
    position: sticky !important;
    z-index: 10 !important;
    padding: 0.5rem !important;
    height: 60px !important;
    vertical-align: middle !important;
}


#modal-realizadoDia thead th {
    top: 0 !important;
}

#modal-realizadoDia tfoot th {
    bottom: 0 !important;
}

#modal-cargaOP_fase thead th,
#modal-cargaOP_fase tfoot th {
    position: sticky !important;
    z-index: 10 !important;
    padding: 0.5rem !important;
    height: 60px !important;
    vertical-align: middle !important;
}





#modal-cargaOP_fase thead th {
    top: 0 !important;
}

#modal-cargaOP_fase tfoot th {
    bottom: 0 !important;
}

#modal-cargaOP_fase thead th input {
    margin: 0 !important;
    padding: 0.25rem 0.5rem !important;
    box-sizing: border-box !important;
    width: 100% !important;
    border: 1px solid #ced4da !important;
}

#table-cargaOP_fase {
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

#modal-cargaOP_fase tbody tr:first-child td {
    margin-top: 0 !important;
    padding-top: 0.5rem !important; /* ou o valor que harmoniza com o <thead> */
}

#modal-cargaOP_fase thead th {
    border-bottom: 2px solid #dee2e6 !important;
}


</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bullseye"></i></span> plano
</div>

<div class="mt-3 mb-4 row justify-content-left" id="selecao-plano">
    <div class="col-12 col-md-6">
        <div class="select text-start">
            <label for="select-plano" class="form-label">Selecionar plano</label>
            <select id="select-plano" class="form-select" required>
            </select>
        </div>
    </div>
    <div class="col-12 col-md-6 d-none" id="div-selecionar-lote">
        <div class="select text-start">
            <label for="select-lote" class="form-label">Selecionar Lote</label>
            <select id="select-lote" class="form-select" required>
            </select>
        </div>
    </div>
</div>

<div class="col-12 div-metas d-none" style="background-color: lightgray; border-radius: 8px; padding: 10px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 800px; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
        <thead style="position: sticky; top: 0; background-color: #003366; z-index: 10;">
                <tr>
                    <th>Sequencia</th>
                    <th>Cód. Fase</th>
                    <th>Nome Fase<br><input type="search" class="search-input search-input-metas" style="min-width: 150px;"></th>
                    <th>Previsão de Peças</th>
                    <th>Falta Programar</th>
                    <th>Carga</th>
                    <th>Fila</th>
                    <th>Falta Produzir</th>
                    <th>Qtd. Dias</th>
                    <th>Meta Anterior</th>
                    <th>Meta Dia</th>
                    <th>Realizado</th>
                    <th>Efic. %</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-filtros" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Filtro</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-filtros" onsubmit="Consulta_Metas(true); return false;">
                    <div class="mb-3">
                        <label for="data-inicial" class="form-label text-dark fw-bold">Data de Início:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-inicial" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="data-final" class="form-label text-dark fw-bold">Data de Fim:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-final" required>
                        </div>
                    </div>
                    <div class="form-group mb-3" style="font-size: 20px; font-weight: 500">Tipos de Op's</div>
                    <div class="form-group" id="TiposOps"></div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-salvar">
                            <span><i class="bi bi-floppy"></i></span>
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-custom" id="modal-realizado" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-realizado" style="color: black;"></h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 800px; overflow: auto">
                <table class="table table-bordered table-striped" id="table-realizado" style="width: 100%;">
                    <thead id = 'CabecalhoModal'>
                        <tr>
                            <th>Data<br></th>
                            <th>Dia<br></th>
                            <th>Realizado<br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Previsão -->
                            <th></th> <!-- Realizado -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal-custom" id="modal-realizadoDia" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered w-100 m-0">
        <div class="modal-content w-100">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-realizadoDia" style="color: black;"></h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 900px; overflow: auto">
                <table class="table table-bordered table-striped" id="table-realizadoDia">
                    <thead id = 'CabecalhoModal'>
                        <tr>
                            <th>Colecao<br></th>
                            <th>numeroOP<br></th>
                            <th>Engenharia<br></th>
                            <th>Data<br></th>
                            <th>horario<br></th>
                            <th>Realizado<br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Previsão -->
                            <th></th> <!-- Previsão -->
                            <th></th> <!-- Realizado -->
                            <th></th> <!-- Realizado -->
                            <th></th> <!-- Realizado -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal-custom" id="modal-previsao-categorias" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog w-100 m-0">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Previsão</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 900px; overflow: auto">
                <table class="table table-bordered table-striped" id="table-previsao-categorias" style="width: 100%;">
                    <thead id = 'CabecalhoModal'>
                        <tr>
                            <th>Categoria<br></th>
                            <th>Previsão<br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Previsão -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal-custom" id="modal-resumo-fila" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;" id = 'titulo-fila'>Resumo da Fila</h5>
                <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-fase">Fila por Fase</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-categoria">Fila por Categoria</button>
        <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

            <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 800px; overflow: auto">
                <table class="table table-bordered table-striped" id="table-resumo-fila" style="width: 100%;">
                    <thead id = 'CabecalhoModal'>
                        <tr>
                            <th>Fase Atual<br></th>
                            <th>Fila <br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Previsão -->
                        </tr>
                    </tfoot>
                </table>


            <!-- Tabela por Categoria (inicialmente oculta) -->
            <table class="table table-bordered table-striped" id="table-resumo-categoria" style="width: 100%; display: none;">
                    <thead>
                        <tr>
                            <th>Fase Atual<br></th>
                            <th>Categoria</th><input type="search" class="search-input search-input-metas" style="min-width: 150px;"></th>
                            <th>Fila</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal-custom" id="modal-falta-produzir-categorias" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;" id = 'titulo-falta-produzir'>Falta Produzir</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 800px; overflow: auto">
                <table class="table table-bordered table-striped" id="table-falta-produzir-categorias" style="width: 100%;">
                    <thead id = 'CabecalhoModal'>
                        <tr >
                            <th>Categoria<br></th>
                            <th>Carga<br></th>
                            <th>Fila<br></th>
                            <th>Falta Programar<br></th>
                            <th>Falta Produzir<br></th>
                            <th>Dias<br></th>
                            <th>Meta Diária<br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Carga -->
                            <th></th> <!-- Fila -->
                            <th></th> <!-- Falta Programar -->
                            <th></th> <!-- Falta Produzir -->
                            <th></th> <!-- Dias -->
                            <th></th> <!-- Meta Diária -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal-custom" id="modal-cargaOP_fase" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered w-100 m-0">
        <div class="modal-content w-100">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;" id = 'titulo-cargaOP_fase'>Carga</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 900px; overflow: auto">
                <table class="table table-bordered table-striped" id="table-cargaOP_fase">
                    <thead id = 'CabecalhoModal'>
                        <tr >
                            <th>Colecao<br><input type="search" class="search-input-table-cargaOP_fase"></th>
                            <th>numeroOP<br></th>
                            <th>categoria<br><input type="search" class="search-input-table-cargaOP_fase"></th>
                            <th>codProduto</th>
                            <th id="formato_descricao">descricao<br></th>
                            <th>prioridade</th>
                            <th>Carga</th>
                            <th>Data<br>Entrada</br></th>
                            <th>DiasFase</th>
                            <th>Data Ger.<br>OP</br></th>
                            <th>Lead Time<br>Acum.</br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th></th>
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>







<div class="modal fade modal-custom" id="modal-cronograma" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Filtro</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-cronograma">
                    <div class="mb-3">
                        <label for="data-inicial" class="form-label text-dark fw-bold">Data de Início:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-inicial-cronograma" disabled>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="data-final" class="form-label text-dark fw-bold">Data de Fim:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-final-cronograma" disabled>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script2.js"></script>