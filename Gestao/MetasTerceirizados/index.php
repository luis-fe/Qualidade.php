<?php
include_once('requests.php');

if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    include_once("../../templates/header.php");
    include_once("../../templates/loading.php");
} else {
    include_once("../../templates/header1.php");
    include_once("../../templates/loading1.php");
}
?>
<link rel="stylesheet" href="style1.css">


<label for="" class="d-flex flex-start col-12 titulo">Metas dos Terceirizados</label>
<div class="responsive-container" id="teste" style="background-color: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); border-radius: 5px; padding-top: 15px; padding-left: 30px;">
    <div class="row text-align-start justify-content-start mb-1">
        <div class="row mb-1 align-items-end">
            <div class="col-12 col-md-2 d-flex align-items-center">
                <div class="input-group">
                    <input type="search" id="codigoPlano" class="form-control" placeholder="Plano" onkeydown="if (event.key === 'Enter') {event.preventDefault(); ConsultaLote()}">
                    <span class="input-group-text search-icon" id="search-icon" onclick="Consulta_Planos_Disponiveis()">
                        <i class="lni lni-search-alt"></i>
                    </span>
                </div>
            </div>
            <div class="col-12 col-md-5 d-flex align-items-center mt-3 mt-md-0">
                <div class="input-group">
                    <input type="search" id="DescPlano" readonly class="form-control" placeholder="Descrição">
                </div>
            </div>
        </div>
        <div class="row col-12 mb-3 align-items-end d-none" id="selects">
            <div class="col-12 col-md-3">
                <label for="Lote" class="form-label">Lote</label>
                <select class="form-select" id="SelectLote" onchange="SelecaoLote(false)">
                </select>
            </div>
            <div class="col-12 col-md-3"></div>
        </div>
        <div class="row" style="overflow: auto;">
            <!-- Primeira tabela -->
            <div class="container-tabela col-12 col-md-7 mb-3 mb-md-0">
                <table id="table-metas" class="table table-custom table-striped d-none">
                    <thead id="fixed-header">
                        <tr>
                           <th scope="col"><p>Categoria</p></th>
                            <th scope="col">Falta<p>Prog.</p></th>
                            <th scope="col">Falta<p>Produzir</p></th>
                            <th scope="col"><p>Fila</p></th>
                            <th scope="col"><p>codFaccionista</p></th>
                            <th scope="col"><p>Faccionista</p></th>
                            <th scope="col">Faccionista Csw</th>
                            <th scope="col">Capac.<p>Dia</p></th>
                            <th scope="col"><p>Carga</p></th>
                            <th scope="col">Dias<p>úteis</p></th>
                            <th scope="col">Meta<p>Dia</p></th>
                            <th scope="col">Realizado</p></th>
                            <th scope="col">%</p></th>
                            <th scope="col"><p>Ações</p></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Segunda e terceira tabela -->
            <div class="container-tabela col-12 col-md-5 d-flex flex-column">
                <!-- Segunda tabela -->
                <div class="table-wrapper mb-3">
                    <table id="table-categorias" class="table table-custom table-striped d-none">
                        <thead id="fixed-header">
                            <tr>
                                <th scope="col"><p>Categoria</p></th>
                                <th scope="col">Falta<p>Prog.</p></th>
                                <th scope="col"><p>À enviar</p></th>
                                <th scope="col"><p>Carga</p></th>
                                <th scope="col">Falta<p>Produzir</p></th>
                                <th scope="col">Dias<p>úteis</p></th>
                                <th scope="col">Meta<p>Dia</p></th>
                                <th scope="col"><p>Realizado</p></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- Terceira tabela -->
                <div class="flex-grow-1 table-wrapper">
                    <table id="table-realizado" class="table table-custom table-striped d-none">
                        <thead id="fixed-header">
                            <tr>
                                <th scope="col">Realizado</th>
                                <th scope="col">Data</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ModalPlanosDisponiveis" tabindex="-1" role="dialog" aria-labelledby="ModalPlanosDisponiveis" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="max-height: 80vh">
            <div class="modal-header">
                <h5 class="modal-title">Planos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow: auto">
                <div class="table-responsive" style="max-height: 55vh; overflow-y: auto;">
                    <table class="table table-bordered table-striped" id="table-planos-disponiveis" style="width: 100%; min-width: 100%">
                        <thead id="fixed-header" style="position: sticky; top: 0; background: white; z-index: 1;">
                            <tr>
                                <th>Código do Plano</th>
                                <th>Descrição do Plano</th>
                                <th>selecionar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Conteúdo da tabela -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filtrosModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="data-inicial">Data Início</label>
                        <input type="date" class="form-control" id="data-inicial" name="data-inicial">
                    </div>
                    <div class="form-group">
                        <label for="data-final">Data Fim</label>
                        <input type="date" class="form-control" id="data-final" name="data-final">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="$('#filtrosModal').modal('hide'); SelecaoLote(true)">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cadastrar-faccionista" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastro de Faccionista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group mb-3">
                        <select class="form-control" id="select-faccionista" required>
                            <option value="">Selecione o Faccionista (csw)</option>
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="apelido-faccionista" name="apelido-faccionista">
                        <label for="data-final">Apelido do Faccionista</label>
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" id="select-categoria" required>
                            <option value="">Selecione a Categoria</option>
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="capacidade-dia" name="capacidade-dia">
                        <label for="data-final">Capacidade Dia</label>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="CadastrarFaccionista()">Cadastrar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once("../../templates/footer1.php"); ?>
<script src='script1.js'></script>
