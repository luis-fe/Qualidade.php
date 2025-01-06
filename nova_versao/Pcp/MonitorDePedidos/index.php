<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/header.php');
?>
<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-box-seam"></i></span> Monitor de Pedidos
</div>

<div class="col-12 mt-2 d-flex" style="border-bottom: 1px solid lightgray; max-width: 100%; overflow-x: auto">
    <button class="btn btn-menu" id="btn-pedidos"
        onclick="
            alterna_button_selecionado(this);
            $('.div-pedidos').removeClass('d-none');
            $('.div-ops').addClass('d-none');
            $('.div-sem-ops').addClass('d-none');
            ">
        <i class="bi bi-box-seam-fill"></i>
        <span>Pedidos</span>
    </button>
    <button class="btn btn-menu disabled" id="btn-ops"
        onclick="
            alterna_button_selecionado(this); 
            $('.div-pedidos').addClass('d-none');
            $('.div-ops').removeClass('d-none');
            $('.div-sem-ops').addClass('d-none');
            ">
        <i class="fa-solid fa-clone"></i>
        <span>Ops</span>
    </button>
    <button class="btn btn-menu disabled" id="btn-sem-ops"
        onclick="
            alterna_button_selecionado(this); 
            $('.div-pedidos').addClass('d-none');
            $('.div-ops').addClass('d-none');
            $('.div-sem-ops').removeClass('d-none');
        ">
        <i class="fa-solid fa-clone"></i>
        <span>Sem Ops</span>
    </button>
</div>

<div class="accordion col-12 p-3" style="color: black;">
    <h3>Filtros</h3>
    <div class="col-12 row position-relative justify-content-center">
        <div class="mb-3" style="width: 200px;">
            <label for="inicio-venda" class="form-label fw-bold">Início Faturamento</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date" class="form-control border-secondary rounded-end-3" id="inicio-venda">
            </div>
        </div>
        <div class="mb-3" style="width: 200px;">
            <label for="final-venda" class="form-label fw-bold">Final Faturamento</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date" class="form-control border-secondary rounded-end-3" id="final-venda">
            </div>
        </div>
        <div class="mb-3 d-flex justify-content-end align-items-end" style="width: 250px;">
            <select id="select-tipo-data" class="form-select">
                <option></option>
                <option value="DataEmissao">Data Emissão</option>
                <option value="DataPrevOri">Data Previsão Original</option>
            </select>
        </div>
        <div class="mb-3 d-flex justify-content-end align-items-end" style="width: 200px;">
            <select id="select-priorizacao" class="form-select">
                <option></option>
                <option value="DataEmissao">Data Previsão</option>
                <option value="DataPrevOri">Faturamento</option>
            </select>
        </div>
        <div class="custom-dropdown mb-3 d-flex justify-content-end align-items-end" style="width: 300px;">
            <button class="select-notas" id="dropdownToggle">
                Tipos de Notas
            </button>
            <div class="menu-notas" id="menu-notas">
            </div>
        </div>
        <div class="mb-3" style="width: 200px;">
            <label for="inicio-emissao" class="form-label fw-bold">Emissão Inicial</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date" class="form-control border-secondary rounded-end-3" id="inicio-emissao">
            </div>
        </div>
        <div class="mb-3" style="width: 200px;">
            <label for="final-emissao" class="form-label fw-bold">Emissão Final</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date" class="form-control border-secondary rounded-end-3" id="final-emissao">
            </div>
        </div>
        <div style="width: 200px">
            <button class="btn btn-geral" style="margin-top: 30px"
                onclick="
                    async function atualizarTabelas (){
                        $('#loadingModal').modal('show');
                        await ConsultaPedidos();
                        await Consultar_Ops();
                        await Consultar_Sem_Ops();
                        $('#loadingModal').modal('hide');
                    }
                                
                    atualizarTabelas()
            ">Consultar</button>
        </div>
    </div>
</div>

<div class="col-12 mt-5 div-pedidos d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-pedidos" style="width: 100%;">
            <thead>
                <tr>
                    <th>Pedido<br><input type="search" class="search-input search-input-pedidos" style="min-width: 150px;"></th>
                    <th>Marca<br><input type="search" class="search-input search-input-pedidos" style="min-width: 150px;"></th>
                    <th>Tipo de Nota<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Cód. Cliente<br><input type="search" class="search-input search-input-pedidos" style="min-width: 150px;"></th>
                    <th>Data Emissão<br><input type="search" class="search-input search-input-pedidos" style="min-width: 150px;"></th>
                    <th>Previsão Inicial<br><input type="search" class="search-input search-input-pedidos" style="min-width: 150px;"></th>
                    <th>Último Faturamento<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Previsão Próximo Embarque<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Entregas Solicitadas<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Entregas Faturadas<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Entregas Restantes<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Qtd. Peças Faturadas<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Saldo R$<br><input type="search" class="search-input search-input-pedidos" style="min-width: 150px;"></th>
                    <th>R$ Atendido/COR<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>R$ Atendido Distríbuido<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Qtd. Peças Saldo<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Qtd. Peças Atendidas/COR<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Qtd. Peças Distribuídas/COR<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Sugestão Pedido<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>% Distribuído<br><input type="search" class="search-input search-input-pedidos"></th>
                    <th>Pedidos Agrupados<br><input type="search" class="search-input search-input-pedidos"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>

    </div>
    <div class="custom-pagination-container pagination-pedidos d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-pedidos" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-pedidos" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="col-12 mt-5 div-ops d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela-1">
        <div class="div-tabela" style="max-width: 100%; overflow: auto;">
            <table class="table table-bordered table-striped" id="table-ops" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Numero Op<br><input type="search" class="search-input search-input-ops" style="min-width: 150px;"></th>
                        <th>Engeharia<br><input type="search" class="search-input search-input-ops" style="min-width: 150px;"></th>
                        <th>Descrição<br><input type="search" class="search-input search-input-ops"></th>
                        <th>Cód. Fase Atual<br><input type="search" class="search-input search-input-ops" style="min-width: 150px;"></th>
                        <th>Nome da Fase<br><input type="search" class="search-input search-input-ops" style="min-width: 150px;"></th>
                        <th>Quantidade em Pedidos<br><input type="search" class="search-input search-input-ops" style="min-width: 150px;"></th>
                        <th>Necessidade em Peças<br><input type="search" class="search-input search-input-ops"></th>
                        <th>Prioridade<br><input type="search" class="search-input search-input-ops"></th>
                        <th>Previsão de Término<br><input type="search" class="search-input search-input-ops"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aqui vão os dados da tabela -->
                </tbody>
            </table>

        </div>
        <div class="custom-pagination-container pagination-ops d-md-flex col-12 text-center text-md-start">
            <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                <label for="text">Itens por página</label>
                <input id="itens-ops" class="input-itens" type="text" value="10" min="1">
            </div>
            <div id="pagination-ops" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

            </div>
        </div>
    </div>
    <div class="div-tabela-2 d-none">
        <div class="div-tabela-sku" style="max-width: 100%; overflow: auto;">
            <table class="table table-bordered table-striped" id="table-skus" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Numero Op<br><input type="search" class="search-input search-input-skus" style="min-width: 150px;"></th>
                        <th>Engeharia<br><input type="search" class="search-input search-input-skus" style="min-width: 150px;"></th>
                        <th>Reduzido<br><input type="search" class="search-input search-input-skus"></th>
                        <th>Descrição<br><input type="search" class="search-input search-input-skus"></th>
                        <th>Quantidade em Pedidos<br><input type="search" class="search-input search-input-skus" style="min-width: 150px;"></th>
                        <th>Necessidade em Peças<br><input type="search" class="search-input search-input-skus"></th>
                        <th>Quantidade Op<br><input type="search" class="search-input search-input-skus"></th>
                        <th>Cód. Fase Atual<br><input type="search" class="search-input search-input-skus" style="min-width: 150px;"></th>
                        <th>Nome da Fase<br><input type="search" class="search-input search-input-skus" style="min-width: 150px;"></th>
                        <th>Prioridade<br><input type="search" class="search-input search-input-skus"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aqui vão os dados da tabela -->
                </tbody>
            </table>

        </div>
        <div class="custom-pagination-container pagination-skus d-md-flex col-12 text-center text-md-start">
            <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                <label for="text">Itens por página</label>
                <input id="itens-skus" class="input-itens" type="text" value="10" min="1">
            </div>
            <div id="pagination-skus" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

            </div>
        </div>
    </div>

</div>


<div class="col-12 mt-5 div-sem-ops d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-sem-ops" style="width: 100%;">
            <thead>
                <tr>
                    <th>Engenharia<br><input type="search" class="search-input search-input-sem-ops" style="min-width: 150px;"></th>
                    <th>Tamanho<br><input type="search" class="search-input search-input-sem-ops" style="min-width: 150px;"></th>
                    <th>Cód. Cor<br><input type="search" class="search-input search-input-sem-ops" style="min-width: 150px;"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-sem-ops" style="min-width: 150px;"></th>
                    <th>Quantidade em Pedidos<br><input type="search" class="search-input search-input-sem-ops"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>

    </div>
    <div class="custom-pagination-container pagination-sem-ops d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-sem-ops" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-sem-ops" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-filtros" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Filtro de Pedidos</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style=" min-width: 100%">
                <div class="div-tabela" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered table-striped" id="table-lista-pedidos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Ações</th>
                                <th>Código Pedido<br><input type="search" class="search-input search-input-lista-pedidos" style="min-width: 150px;"></th>
                                <th>Data Emissão<br><input type="search" class="search-input search-input-lista-pedidos" style="min-width: 150px;"></th>
                                <th>Código Cliente<br><input type="search" class="search-input search-input-lista-pedidos" style="min-width: 150px;"></th>
                                <th>Nome Cliente<br><input type="search" class="search-input search-input-lista-pedidos"></th>
                                <th>Código Tipo de Nota<br><input type="search" class="search-input search-input-lista-pedidos"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aqui vão os dados da tabela -->
                        </tbody>
                    </table>

                </div>
                <div class="custom-pagination-container pagination-lista-pedidos d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-lista-pedidos" class="input-itens" type="text" value="10" min="1">
                    </div>
                    <div id="pagination-lista-pedidos" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-geral" id="btn-filtrar">
                    Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../templates/footer.php');
?>
<script src="script.js"></script>