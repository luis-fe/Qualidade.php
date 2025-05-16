let cacheDescricao = ''
$(document).ready(async () => {
    Consulta_Planos();
    Consulta_Simulacoes();
    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    console.log('teste')

    $('#select-simulacao').select2({
        placeholder: "Selecione uma simulação",
        allowClear: false,
        width: '100%',
        dropdownParent: $('#modal-simulacao')
    });

    $('#select-simulacao').on('change', async function () {
        $('#inputs-container-marcas').removeClass('d-none')
        await Consulta_Abc_Plano();
        await Consulta_Categorias()
        await Consulta_Simulacao_Especifica();
    });

    $('#select-pedidos-bloqueados').select2({
        placeholder: "Pedidos Bloqueados?",
        allowClear: false,
        width: '100%'
    });

    $('#btn-vendas').addClass('btn-menu-clicado');


// Aqui está o onsubmit do form
    $('#form-simulacao').on('submit', async function (e) {
        e.preventDefault();

        const inputDescricao = document.getElementById('select-simulacao');


        cacheDescricao = inputDescricao.value;


        console.log('cacheDescricao:', cacheDescricao);

        await Cadastro_Simulacao();
        await Consulta_Simulacoes();
        await Simular_Programacao(inputDescricao.value);

        $('#modal-simulacao').modal('hide');
    });

    $('#form-cadastrar-nova-simulacao').on('submit', async function (e) {
    e.preventDefault();

    await Cadastro_Simulacao();
    await Consulta_Simulacoes();

    $('#descricao-simulacao').removeAttr('disabled');

    $('#modal-cadastrar-nova-simulacao"').modal('hide');
    });

});

async function Consulta_Simulacoes() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Simulacoes',
        },
        success: function (data) {
            $('#select-simulacao').empty();
            $('#select-simulacao').append('<option value="" disabled selected>Selecione uma simulação...</option>');
            data.forEach(function (item) {
                $('#select-simulacao').append(`
                        <option value="${item['nomeSimulacao']}">
                            ${item['nomeSimulacao']}
                        </option>
                    `);
            });
            $('#loadingModal').modal('hide');
            const descricao = $('#descricao-simulacao').val();
            console.log(descricao)
            $('#select-simulacao').val(descricao);
        },

        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
}


function Consulta_Planos() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Planos',
        },
        success: function (data) {
            $('#select-plano').empty();
            $('#select-plano').append('<option value="" disabled selected>Selecione um plano...</option>');
            data.forEach(function (plano) {
                $('#select-plano').append(`
                        <option value="${plano['01- Codigo Plano']}">
                            ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                        </option>
                    `);
            });
            $('#loadingModal').modal('hide');
        },
        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

async function Consulta_Tendencias() {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Consulta_Tendencias",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val()
            }

        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaTendencia(response);
        $('.div-tendencia').removeClass('d-none');
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
};

async function Simular_Programacao(campoDescricao) {
     console.log(`minha descricao: ${campoDescricao}`);
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Simular_Programacao",
            dados: {
                codPlano: $('#select-plano').val(),
                consideraPedidosBloqueado: $('#select-pedidos-bloqueados').val(),
                nomeSimulacao:  campoDescricao
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });


        TabelaTendencia(response);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


async function Cadastro_Simulacao() {
    $('#loadingModal').modal('show');
    try {
        const categorias = [];
        const percentuais_categorias = [];

        $('.input-categoria').each(function () {
            const categoria = $(this).attr('id');
            const percentual = parseFloat($(this).val().replace(',', '.'));

            if (categoria && !isNaN(percentual)) {
                categorias.push(categoria);
                percentuais_categorias.push(percentual);
            }
        });

        const abcs = [];
        const percentuais_abc = [];

        $('.input-abc').each(function () {
            const abc = $(this).attr('id');
            const percentual = parseFloat($(this).val().replace(',', '.'));

            if (abc && !isNaN(percentual)) {
                abcs.push(abc);
                percentuais_abc.push(percentual);
            }
        });

        const marcas = [];
        const percentuais_marca = [];

        $('.input-marca').each(function () {
            const marca = $(this).attr('id');
            const percentual = parseFloat($(this).val().replace(',', '.'));

            if (marca && !isNaN(percentual)) {
                marcas.push(marca);
                percentuais_marca.push(percentual);
            }
        });

        const requestData = {
            acao: "Cadastro_Simulacao",
            dados: {
                "nomeSimulacao": $('#descricao-simulacao').val(),
                arrayAbc: [
                    abcs,
                    percentuais_abc

                ],
                arrayCategoria: [
                    categorias,
                    percentuais_categorias
                ],
                arrayMarca: [
                    marcas,
                    percentuais_marca
                ]
            }
        };
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Abc_Plano = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc_Plano',
                plano: $('#select-plano').val()
            }
        });

        const inputsContainer = $('#inputs-container');
        inputsContainer.empty();

        data[0]['3- Detalhamento:'].forEach((item) => {
            const inputHtml = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}">
                </div>
            `;
            inputsContainer.append(inputHtml);
        });

        $('.input-abc').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Abc = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc',
            }
        });

        const inputsContainer = $('#inputs-container');
        inputsContainer.empty();

        data.forEach((item) => {
            const inputHtml = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}" placeholder="%">
                </div>
            `;
            inputsContainer.append(inputHtml);
        });

        $('.input-abc').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Categorias = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Categorias',
            }
        });

        const inputsContainer = $('#inputs-container-categorias');
        inputsContainer.empty();

        data.forEach((item) => {
            const inputHtml = `
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${item.nomeCategoria}</label>
                        <input type="text" class="inputs-percentuais input-categoria col-12" id="${item.nomeCategoria}" placeholder="%">
                    </div>
                `;
            inputsContainer.append(inputHtml);
        });

        $('.input-categoria').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Simulacao_Especifica = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Simulacao_Especifica',
                simulacao: $('#select-simulacao').val()
            }
        });

        if (!data) {
            Mensagem_Canto('Não possui simulação para editar', 'warning');
            $('#modal-simulacao').modal('hide');
            return;
        }

        const campos = ["2- ABC", "3- Categoria", "4- Marcas"];
        campos.forEach(campo => {
            if (data[0][campo]) {
                data[0][campo].forEach(item => {
                    const key = item.class || item.categoria || item.marca;
                    const input = $(`#${key.replace(/\s+/g, '-').replace(/[^\w-]/g, '')}`);
                    if (input.length) {
                        input.val(`${parseFloat(item.percentual).toFixed(1).replace('.', ',')}%`);
                    }
                });
            }
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};



function TabelaTendencia(listaTendencia) {
    if ($.fn.DataTable.isDataTable('#table-tendencia')) {
        $('#table-tendencia').DataTable().destroy();
    }

    const tabela = $('#table-tendencia').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaTendencia,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Tendências de Vendas',
            className: 'btn-tabelas',
            exportOptions: {
                columns: ':visible',
                format: {
                    body: function (data, row, column, node) {
                        if (typeof data === 'string') {
                            return data.replace(/\./g, '').replace(',', '.');
                        }
                        return data;
                    }
                }
            }
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Simulação',
            title: 'Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('#modal-simulacao').modal('show');
                $('#campo-simulacao').removeClass('d-none');

                const simulacaoValue = $('#select-simulacao').val()?.trim() || "";

                if (simulacaoValue === "") {
                    $('#inputs-container-categorias').empty();
                    $('#inputs-container').empty();
                    $('#inputs-container-marcas').addClass('d-none')
                } else {
                    await Consulta_Abc_Plano();
                    await Consulta_Categorias();
                    await Consulta_Simulacao_Especifica();
                    $('#inputs-container-marcas').removeClass('d-none')
                }
            }
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Nova Simulação',
            title: 'Nova Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('#modal-cadastrar-nova-simulacao').modal('show');

                await Consulta_Abc();
                await Consulta_Categorias();


            },
        },
            {
                text: `<span class="text-primary fw-bold" style="background-color: transparent !important; margin-right: 10px !important;">${cacheDescricao}</span>`,
                className: 'bg-transparent border-0 p-0 m-0'  // aparência mínima
            }
        ],
        columns: [{
            data: 'marca'
        },
        {
            data: 'codItemPai'
        },
        {
            data: 'tam'
        },
        {
            data: 'codCor'
        },
        {
            data: 'nome'
        },
        {
            data: 'codReduzido'
        },
        {
            data: 'categoria'
        },
        {
            data: 'class'
        },
        {
            data: 'classCategoria'
        },
        {
            data: 'Ocorrencia em Pedidos',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'valorVendido',
            render: function (data, type) {
                let ValorInt = parseFloat(data.replace(/[^\d,]/g, '').replace(',', '.'));
                return type === 'display' ?
                    `R$ ${ValorInt.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` :
                    ValorInt;
            }
        },
        {
            data: 'previcaoVendas',
            render: function (data, type, row) {
                return `<span class="detalha-SimulacaoSku" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;

            }
        },
        {
            data: 'qtdePedida',
            render: function (data, type, row) {
                return `<span class="detalha-pedidos" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;

            }
        },
        {
            data: 'qtdeFaturada',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'estoqueAtual',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'emProcesso',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'disponivel',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'Prev Sobra',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'statusAFV'
        }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-tendencia').html($('.dataTables_paginate').html());
            $('#pagination-tendencia span').remove();
            $('#pagination-tendencia a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        },
        footerCallback: function (row, data, start, end, display) {
            const api = this.api();

            // Helper para converter strings para número
            const intVal = (i) => {
                if (typeof i === 'string') {
                    // Remover "R$", pontos e substituir vírgula por ponto
                    return parseFloat(i.replace(/[R$ ]/g, '').replace(/[\.]/g, '').replace(',', '.')) || 0;
                } else if (typeof i === 'number') {
                    return i;
                }
                return 0;
            };

            // Colunas que precisam de total
            const columnsToSum = ['valorVendido', 'previcaoVendas', 'qtdePedida', 'qtdeFaturada', 'estoqueAtual', 'emProcesso', 'faltaProg (Tendencia)', 'disponivel', 'Prev Sobra'];

            columnsToSum.forEach((columnName, idx) => {
                const colIndex = idx + 10; // Índice da coluna no DataTables

                // Total considerando todos os dados após filtro
                const total = api.column(colIndex, {
                    filter: 'applied'
                }).data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Atualizar o rodapé da coluna
                $(api.column(colIndex).footer()).html(
                    columnName === 'valorVendido' ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : total.toLocaleString('pt-BR')
                );
            });
        },

    });

    $('.search-input-tendencia').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#btn-selecionar-colecoes').off('click').on('click', () => {
        ColecoesSelecionadas = tabela.rows().nodes().toArray()
            .filter(row => $(row).find('.row-checkbox').is(':checked'))
            .map(row => String(tabela.row(row).data().codColecao));

        if (ColecoesSelecionadas.length === 0) {
            Mensagem('Nenhuma Coleção selecionada!', 'warning');
        } else {
            Vincular_Colecoes();
        }
    });

        // Clique no hiperlink "codReduzido"
    $('#table-tendencia').on('click', '.detalha-pedidos', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        const codPlan = $('#select-plano').val();
        const consideraPedidosBloqueado =  $('#select-pedidos-bloqueados').val();
        console.log(`Teste2 Plano selecionado: ${codPlan}`)
        
        Detalha_Pedidos(codReduzido,consideraPedidosBloqueado, codPlan);
    });

        $('#table-tendencia').on('click', '.detalha-SimulacaoSku', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        
        Detalha_SimulacaoSku(codReduzido);
    });
}

async function Detalha_Pedidos(codReduzido, consideraPedidosBloqueado, codPlan) {
    $('#loadingModal').modal('show');
    try {
        const params = new URLSearchParams({
            acao: "Detalha_Pedidos",
            codPlano: codPlan,
            consideraPedidosBloqueado: consideraPedidosBloqueado,
            codReduzido: codReduzido
        });

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php?' + params.toString(),
        });

        TabelaDetalhamentoPedidos(response);
        $('#modal-detalhamentoPedidoSku').modal('show');
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


async function Detalha_SimulacaoSku(codReduzido) {
    $('#loadingModal').modal('show');
        console.log('Valor da descrição da simulacao detalhado:', cacheDescricao);
    try {

        const requestData = {
            acao: "simulacaoDetalhadaPorSku",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedBloq": $('#select-pedidos-bloqueados').val(),
                "codSku": codReduzido,
                "nomeSimulacao":  cacheDescricao
            }

        };

const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response)
        TabeldetalhamentoSkuSimulado(response);
        $('#modal-detalhamentoSkuSimulado').modal('show');
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function TabelaDetalhamentoPedidos(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamentoPedidoSku')) {
        $('#table-detalhamentoPedidoSku').DataTable().destroy();
    }

    const tabela = $('#table-detalhamentoPedidoSku').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 15,
        dom: 'Bfrtip', // <-- necessário para os botões aparecerem
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
                title: 'Tendências de Vendas',
                className: 'btn-tabelas',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            if (typeof data === 'string') {
                                return data.replace(/\./g, '').replace(',', '.');
                            }
                            return data;
                        }
                    }
                }
            }
        ],
        data: listaDetalhes,
        columns: [
            { data: 'codPedido' },
            { data: 'codTipoNota' },
            { data: 'dataEmissao' },
            { data: 'dataPrevFat' },
            { data: 'marca' },
            { data: 'qtdeFaturada' },
            { data: 'qtdePedida' },
            { data: 'valorVendido' }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-detalhamentoPedidoSku').html($('.dataTables_paginate').html());
            $('#pagination-detalhamentoPedidoSku span').remove();
            $('#pagination-detalhamentoPedidoSku a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    // Adiciona os botões à interface
    tabela.buttons().container().appendTo('#table-detalhamentoPedidoSku_wrapper .col-md-6:eq(0)');

    $('#itens-detalhamentoPedidoSku').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamentoPedidoSku').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabeldetalhamentoSkuSimulado(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamentoSkuSimulado')) {
        $('#table-detalhamentoSkuSimulado').DataTable().destroy();
    }

    const tabela = $('#table-detalhamentoSkuSimulado').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 15,
        dom: 'Bfrtip', // <-- necessário para os botões aparecerem
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
                title: 'Tendências de Vendas',
                className: 'btn-tabelas',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            if (typeof data === 'string') {
                                return data.replace(/\./g, '').replace(',', '.');
                            }
                            return data;
                        }
                    }
                }
            }
        ],
        data: listaDetalhes,
        columns: [
            { data: 'nomeSimulacao' },
            { data: 'codReduzido' },
            { data: 'previcaoVendasOriginal' },
            { data: 'percentualMarca' },
            { data: 'percentualABC' },
            { data: 'percentualCategoria' },
            { data: '_%Considerado' },
            { data: 'NovaPrevicao' },
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-detalhamentoSkuSimulado').html($('.dataTables_paginate').html());
            $('#pagination-detalhamentoSkuSimulado span').remove();
            $('#pagination-detalhamentoSkuSimulado a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    // Adiciona os botões à interface
    tabela.buttons().container().appendTo('#table-detalhamentoSkuSimulado_wrapper .col-md-6:eq(0)');

    $('#itens-detalhamentoSkuSimulado').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamentoSkuSimulado').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}
