<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="style.css">
<style>
    label {
        color: black !important;
    }

</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="fa-solid fa-map"></i></span> Plano de Produção
</div>
<div class="col-12 mt-2 d-flex" style="border-bottom: 1px solid lightgray; max-width: 100%; overflow-x: auto">
    <button class="btn btn-menu"
        onclick="
            alterna_button_selecionado(this);
            $('#div-tabela').removeClass('d-none');
            $('#div-tabela-colecoes').addClass('d-none');
            $('#div-tabela-lotes').addClass('d-none');
            $('#div-tabela-notas').addClass('d-none');
            $('#div-tabela-abc').addClass('d-none');
            $('#btn-adicionar-colecao').addClass('d-none');
            $('#btn-adicionar-lotes').addClass('d-none');
            if ($('.btn-menu').hasClass('disabled')) {
            $('#btn-salvar-edicao').addClass('d-none');
            } else {
             $('#btn-salvar-edicao').removeClass('d-none');
            }
            " id="detalhes">
        <i class="fa-solid fa-map"></i>
        <span>Detalhes</span>
    </button>
    <button class="btn btn-menu disabled"
        onclick="
            alterna_button_selecionado(this);
            $('#div-tabela').addClass('d-none');
            $('#div-tabela-colecoes').removeClass('d-none');
            $('#div-tabela-lotes').addClass('d-none');
            $('#div-tabela-notas').addClass('d-none');
            $('#div-tabela-abc').addClass('d-none');
            $('#btn-salvar-edicao').addClass('d-none');
            $('#btn-salvar').addClass('d-none');
            $('#btn-adicionar-colecao').removeClass('d-none');
            $('#btn-adicionar-lotes').addClass('d-none');
            $('#btn-adicionar-notas').addClass('d-none');
            $('#btn-adicionar-abc').addClass('d-none');
            ">
        <i class="fa-solid fa-clone"></i>
        <span>Coleções</span>
    </button>
    <button class="btn btn-menu disabled"
        onclick="
            alterna_button_selecionado(this);
            $('#div-tabela').addClass('d-none');
            $('#div-tabela-colecoes').addClass('d-none');
            $('#div-tabela-lotes').removeClass('d-none');
            $('#div-tabela-notas').addClass('d-none');
            $('#div-tabela-abc').addClass('d-none');
            $('#btn-salvar-edicao').addClass('d-none');
            $('#btn-salvar').addClass('d-none');
            $('#btn-adicionar-colecao').addClass('d-none');
            $('#btn-adicionar-lotes').removeClass('d-none');
            $('#btn-adicionar-notas').addClass('d-none');
            $('#btn-adicionar-abc').addClass('d-none');
            ">
        <i class="fa-solid fa-clipboard"></i>
        <span>Lotes</span>
    </button>
    <button class="btn btn-menu disabled"
        onclick="
            alterna_button_selecionado(this);
            $('#div-tabela').addClass('d-none');
            $('#div-tabela-colecoes').addClass('d-none');
            $('#div-tabela-lotes').addClass('d-none');
            $('#div-tabela-notas').removeClass('d-none');
            $('#div-tabela-abc').addClass('d-none');
            $('#btn-salvar-edicao').addClass('d-none');
            $('#btn-salvar').addClass('d-none');
            $('#btn-adicionar-colecao').addClass('d-none');
            $('#btn-adicionar-lotes').addClass('d-none');
            $('#btn-adicionar-notas').removeClass('d-none');
            $('#btn-adicionar-abc').addClass('d-none');
            ">
        <i class="fa-solid fa-file-invoice"></i>
        <span>Tipos de Notas</span>
    </button>
    <button class="btn btn-menu disabled"
        onclick="
            alterna_button_selecionado(this);
            $('#div-tabela').addClass('d-none');
            $('#div-tabela-colecoes').addClass('d-none');
            $('#div-tabela-lotes').addClass('d-none');
            $('#div-tabela-notas').addClass('d-none');
            $('#div-tabela-abc').removeClass('d-none');
            $('#btn-salvar-edicao').addClass('d-none');
            $('#btn-salvar').addClass('d-none');
            $('#btn-adicionar-colecao').addClass('d-none');
            $('#btn-adicionar-lotes').addClass('d-none');
            $('#btn-adicionar-notas').addClass('d-none');
            $('#btn-adicionar-abc').removeClass('d-none');
            ">
        <i class="bi bi-alphabet-uppercase"></i>
        <span>ABC</span>
    </button>
</div>
<div class="col-12 mt-2" style="flex-wrap: wrap">
    <button class="btn btn-geral" style="width: 100px" id="btn-novo-plano"
        onclick="
        $('.div-tabela').addClass('d-none');
        $('.div-cadastro-plano').removeClass('d-none');
        $('#btn-novo-plano').addClass('d-none');
        $('#btn-voltar').removeClass('d-none');
        $('#btn-salvar').removeClass('d-none');
        ">
        <span><i class="bi bi-pencil-fill"></i></span>
        Novo
    </button>
    <button class="btn btn-geral d-none" style="width: 100px" id="btn-voltar"
        onclick="
            $('.div-tabela').removeClass('d-none');
            $('#div-tabela').removeClass('d-none');
            $('.div-cadastro-plano').addClass('d-none');
            $('#div-tabela-colecoes').addClass('d-none');
            $('#div-tabela-lotes').addClass('d-none');
            $('#div-tabela-notas').addClass('d-none');
            $('#div-tabela-abc').addClass('d-none');
            $('#btn-novo-plano').removeClass('d-none');
            $('#btn-voltar').addClass('d-none');
            $('#btn-salvar').addClass('d-none');
            $('#btn-salvar-edicao').addClass('d-none');
            $('#btn-adicionar-colecao').addClass('d-none');
            $('#btn-adicionar-lotes').addClass('d-none');
            $('#btn-adicionar-notas').addClass('d-none');
            $('#btn-adicionar-abc').addClass('d-none');
            $('#codigo-plano').val('');
            $('#descricao-plano').val('');
            $('#inicio-venda').val('');
            $('#final-venda').val('');
            $('#inicio-faturamento').val('');
            $('#final-faturamento').val('');
            $('#codigo-plano').removeAttr('disabled', true);
            $('#codigo-plano').css('cursor', 'auto');
            $('.btn-menu').addClass('disabled');
            $('.btn-menu').removeClass('btn-menu-clicado');
            $('#detalhes').removeClass('disabled');
            $('#detalhes').addClass('btn-menu-clicado');
            ">
        <span><i class="bi bi-arrow-left-circle-fill"></i></span>
        Voltar
    </button>
    <button class="btn btn-salvar d-none" style="width: 100px;" id="btn-salvar-edicao" onclick="Alterar_Plano()">
        <span><i class="bi bi-floppy"></i></span>
        Salvar
    </button>
    <button class="btn btn-salvar d-none" style="width: 100px" id="btn-salvar" onclick="Cadastrar_Plano()">
        <span><i class="bi bi-floppy"></i></span>
        Salvar
    </button>
    <button class="btn btn-salvar d-none" style="width: 150px" id="btn-adicionar-colecao" onclick="Consulta_Colecoes_Csw()">
        <span><i class="bi bi-plus"></i></span>
        Adicionar
    </button>
    <button class="btn btn-salvar d-none" style="width: 150px" id="btn-adicionar-lotes" onclick="Consulta_Lotes_Csw()">
        <span><i class="bi bi-plus"></i></span>
        Adicionar
    </button>
    <button class="btn btn-salvar d-none" style="width: 150px" id="btn-adicionar-notas" onclick="Consulta_Notas_Csw()">
        <span><i class="bi bi-plus"></i></span>
        Adicionar
    </button>
    <button class="btn btn-salvar d-none" style="width: 150px" id="btn-adicionar-abc" onclick="$('#modal-abc').modal('show')">
        <span><i class="bi bi-plus"></i></span>
        Adicionar
    </button>

</div>
<div class="col-12" style="background-color: rgb(231,231,231); border-radius: 8px;" id="div-tabela">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-planos" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ação</th>
                    <th>Código Plano<br><input type="search" class="search-input" id="search-table-planos-codigo"></th>
                    <th>Descrição<br><input type="search" class="search-input" id="search-table-planos-descricao"></th>
                    <th>Início das Vendas<br><input type="search" class="search-input" id="search-table-planos-inicio-vendas"></th>
                    <th>Final das Vendas<br><input type="search" class="search-input" id="search-table-planos-fim-vendas"></th>
                    <th>Início do Faturamento<br><input type="search" class="search-input" id="search-table-planos-inicio-faturamento"></th>
                    <th>Final do Faturamento<br><input type="search" class="search-input" id="search-table-planos-fim-faturamento"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
        <div class="custom-pagination-container pagination-planos d-md-flex d-block col-12 text-center text-md-start">
            <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                <label for="text">Itens por página</label>
                <input id="itens-planos" class="input-itens" type="text" value="10" min="1">
            </div>
            <div id="pagination-planos" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

            </div>
        </div>
    </div>
    <div class="col-12 div-cadastro-plano row d-none" style="border: 1px solid rgb(231,231,231); border-radius: 8px; background-color: rgb(231,231,231); padding: auto; margin:auto; padding: 10px 0px">
        <div class="row justify-content-center gx-3">
            <div class="col-md-3 col-12 mb-3">
                <label for="codigo-plano" class="form-label fw-bold">Código do Plano</label>
                <input type="text" class="form-control border-secondary" id="codigo-plano" placeholder="Insira o Código">
            </div>

            <div class="col-md-6 col-12 mb-3">
                <label for="descricao-plano" class="form-label fw-bold">Descrição do Plano</label>
                <input type="text" class="form-control border-secondary" id="descricao-plano" placeholder="Insira a Descrição">
            </div>
        </div>

        <div class="row justify-content-center gx-3">
            <div class="col-md-3 col-12 mb-3">
                <label for="inicio-venda" class="form-label fw-bold">Início Vendas</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control border-secondary rounded-end-3" id="inicio-venda">
                </div>
            </div>

            <div class="col-md-3 col-12 mb-3">
                <label for="final-venda" class="form-label fw-bold">Final Vendas</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control border-secondary rounded-end-3" id="final-venda">
                </div>
            </div>

            <div class="col-md-3 col-12 mb-3">
                <label for="inicio-faturamento" class="form-label fw-bold">Início Faturamento</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control border-secondary rounded-end-3" id="inicio-faturamento">
                </div>
            </div>

            <div class="col-md-3 col-12 mb-3">
                <label for="final-faturamento" class="form-label fw-bold">Final Faturamento</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control border-secondary rounded-end-3" id="final-faturamento">
                </div>
            </div>
        </div>
    </div>
</div>



<div class="col-12 d-none" style="background-color: rgb(231,231,231); border-radius: 8px;" id="div-tabela-colecoes">
    <div class="div-tabela-colecoes" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-colecoes" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ação</th>
                    <th>Código Coleção<br><input type="search" class="search-input search-table-colecoes"></th>
                    <th>Descrição<br><input type="search" class="search-input search-table-colecoes"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-colecoes d-md-flex d-block col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-colecoes" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-colecoes" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="col-12 d-none" style="background-color: rgb(231,231,231); border-radius: 8px;" id="div-tabela-lotes">
    <div class="div-tabela-lotes" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-lotes" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ação</th>
                    <th>Código Lote<br><input type="search" class="search-input search-table-lotes"></th>
                    <th>Descrição<br><input type="search" class="search-input search-table-lotes"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-lotes d-md-flex d-block col-12 text-center text-md-start">
        <div id="custom-info-lotes" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-lotes" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-lotes" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="col-12 d-none" style="background-color: rgb(231,231,231); border-radius: 8px;" id="div-tabela-notas">
    <div class="div-tabela-notas" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-notas" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ação</th>
                    <th>Tipo de Nota<br><input type="search" class="search-input search-table-notas"></th>
                    <th>Descrição<br><input type="search" class="search-input search-table-notas"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-notas d-md-flex d-block col-12 text-center text-md-start">
        <div id="custom-info-notas" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-notas" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-notas" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="col-12 d-none" style="background-color: rgb(231,231,231); border-radius: 8px;" id="div-tabela-abc">
    <div class="div-tabela-abc" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-abc" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ação</th>
                    <th>Parâmetro<br><input type="search" class="search-input search-table-abc"></th>
                    <th>% Distribído<br><input type="search" class="search-input search-table-abc"></th>
                    <th>% Acumulado<br><input type="search" class="search-input search-table-abc"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-abc d-md-flex d-block col-12 text-center text-md-start">
        <div id="custom-info-abc" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-abc" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-abc" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-colecoes" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Seleção de Coleção</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style=" min-width: 100%; max-height: 600px; overflow: auto">
                <div class="div-tabela-colecoes-csw" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered table-striped" id="table-colecoes-csw" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Ações</th>
                                <th>Código Coleção<br><input type="search" class="search-input search-table-colecoes-csw" style="min-width: 150px;"></th>
                                <th>Descrição<br><input type="search" class="search-input search-table-colecoes-csw" style="min-width: 150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aqui vão os dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-colecoes-csw d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-colecoes-csw" class="input-itens" type="text" value="10" min="1">
                    </div>
                    <div id="pagination-colecoes-csw" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-salvar" style="width: 100px" id="btn-selecionar-colecoes">
                    <span><i class="bi bi-floppy"></i></span>
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-lotes" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Seleção de Lotes</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style=" min-width: 100%; max-height: 600px; overflow: auto">
                <div class="div-tabela-lotes-csw" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered table-striped" id="table-lotes-csw" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Ações</th>
                                <th>Código Lote<br><input type="search" class="search-input search-input-lotes-csw" style="min-width: 150px;"></th>
                                <th>Descrição<br><input type="search" class="search-input search-input-lotes-csw" style="min-width: 150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aqui vão os dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-lotes-csw d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-lotes-csw" class="input-itens" type="text" value="10" min="1">
                    </div>
                    <div id="pagination-lotes-csw" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-salvar" style="width: 100px" id="btn-selecionar-lotes">
                    <span><i class="bi bi-floppy"></i></span>
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-notas" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Seleção de Notas</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style=" min-width: 100%; max-height: 600px; overflow: auto">
                <div class="div-tabela-notas-csw" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered table-striped" id="table-notas-csw" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Ações</th>
                                <th>Código Nota<br><input type="search" class="search-input search-table-notas-csw" style="min-width: 150px;"></th>
                                <th>Descrição<br><input type="search" class="search-input search-table-notas-csw" style="min-width: 150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aqui vão os dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-notas-csw d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-notas-csw" class="input-itens" type="text" value="10" min="1">
                    </div>
                    <div id="pagination-notas-csw" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-salvar" style="width: 100px" id="btn-selecionar-notas">
                    <span><i class="bi bi-floppy"></i></span>
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-abc" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Parâmetros</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-abc" onsubmit="Cadastrar_Abc(); return false;">
                <div class="modal-body" style=" min-width: 100%; max-height: 600px; overflow: auto">
                    <div class="col-12">
                        <div class="select text-start">
                            <label for="select-abc" class="form-label">Selecionar parâmetro</label>
                            <select id="select-abc" class="form-select" required>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="input-distribuicao" class="form-label">Distribuir</label>
                        <input type="text" class="form-control" id="input-distribuicao" placeholder="Distribuir" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-salvar" style="width: 100px" id="btn-selecionar-abc">
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
