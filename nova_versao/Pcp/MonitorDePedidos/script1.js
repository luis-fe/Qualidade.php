$(function () {

    $('#btn-pedidos').addClass('btn-menu-clicado')
    const dataAtual = new Date();
    const data = dataFormatada(dataAtual);
    const $menu = $("#menu-notas");

    $('#inicio-venda').val(data);
    $('#final-venda').val(data);
    $('#inicio-emissao').val(data);
    $('#final-emissao').val(data);

    $("#dropdownToggle").on("click", function (event) {
        event.preventDefault();
        event.stopPropagation();

        const $button = $(this);
        const offset = $button.offset();

        if ($menu.is(":visible")) {
            $menu.hide();
        } else {
            $menu
                .appendTo("body")
                .css({
                    position: "absolute",
                    top: offset.top + $button.outerHeight(),
                    left: offset.left,
                    display: "block",
                });
        }
    });

    $menu.on("click", function (event) {
        event.stopPropagation();
    });

    // Fecha o menu ao clicar fora
    $(document).on("click", function () {
        $menu.hide();
    });

    $(".accordion").accordion({
        collapsible: true,
        active: false,
        heightStyle: "content"
    });

    $(".inner-accordion").accordion({
        collapsible: true,
        active: false,
        heightStyle: "content"
    });

    $('#select-tipo-data').select2({
        placeholder: "Tipo de Data",
        allowClear: false,
        width: '100%'
    });

    $('#select-priorizacao').select2({
        placeholder: "Priorizar por:",
        allowClear: false,
        width: '100%'
    });

    $('#select-notas').select2({
        placeholder: "Priorizar por:",
        allowClear: false,
        width: '100%'
    });

    Consulta_Notas();
});

function alterna_button_selecionado(button) {
    $(button).closest('.d-flex').find('button').removeClass('btn-menu-clicado');
    $(button).addClass('btn-menu-clicado');
}

function dataFormatada(data) {
    const year = data.getFullYear();
    const month = String(data.getMonth() + 1).padStart(2, '0');
    const day = String(data.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatarMoeda(valor) {
    return parseFloat(valor).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

function Consulta_Notas() {
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Notas',
        },
        success: function (data) {
            const $menu = $("#menu-notas");
            $menu.empty();

            data.forEach(item => {
                const $option = $("<li>")
                    .addClass("dropdown-item")
                    .html(`
                        <label>
                            <input type="checkbox" value="${item.codigo}" />
                            ${item.codigo} - ${item.descricao}
                        </label>
                    `);

                $menu.append($option); // Adiciona a opção ao dropdown
            });
        },
        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
        }
    });
}

function formatarDados(data) {
    return data.map(item => {
        return {
            '01-MARCA': item['01-MARCA'],
            '02-Pedido': item['02-Pedido'],
            '03-tipoNota': item['03-tipoNota'],
            '04-PrevOriginal': item['04-Prev.Original'],
            '05-PrevAtualiz': item['05-Prev.Atualiz'],
            '06-codCliente': item['06-codCliente'],
            '08-vlrSaldo': formatarMoeda(item['08-vlrSaldo']),
            '09-Entregas Solic': item['09-Entregas Solic'],
            '10-Entregas Fat': item['10-Entregas Fat'],
            '11-ultimo fat': item['11-ultimo fat'],
            '12-qtdPecas Fat': item['12-qtdPecas Fat'],
            '13-Qtd Atende': item['13-Qtd Atende'],
            '14- Qtd Saldo': item['14- Qtd Saldo'],
            '15-Qtd Atende p/Cor': item['15-Qtd Atende p/Cor'],
            '18-Sugestao(Pedido)': item['18-Sugestao(Pedido)'],
            '21-Qnt Cor(Distrib)': item['21-Qnt Cor(Distrib.)'],
            '22-Valor Atende por Cor(Distrib)': formatarMoeda(item['22-Valor Atende por Cor(Distrib.)']),
            '23-% qtd cor': item['23-% qtd cor'],
            '16-Valor Atende por Cor': formatarMoeda(item['16-Valor Atende por Cor']),
            'Saldo +Sugerido': item['Saldo +Sugerido'],
            'dataEmissao': item['dataEmissao'],
            'Agrupamento': item['Agrupamento'],
        };
    });
}

const ConsultaPedidos = async () => {
    $('#loadingModal').modal('show');

    const tipoNota = $('#menu-notas input[type="checkbox"]:checked')
        .map(function () {
            return $(this).val();
        })
        .get()
        .join(',');

    const parametroClassificacao = $('#select-priorizacao').val();

    const dados = {
        "iniVenda": $('#inicio-venda').val(),
        "finalVenda": $('#final-venda').val(),
        "FiltrodataEmissaoInicial": $('#inicio-emissao').val(),
        "FiltrodataEmissaoFinal": $('#final-emissao').val(),
        "parametroClassificacao": parametroClassificacao,
        "tipoData": $('#select-tipo-data').val()
    };

    const requestData = {
        acao: "Consultar_Pedidos",
        dados: dados
    };

    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        // ✅ Aqui está a correção:
        if (response && response.resposta && response.resposta.length > 0 && response.resposta[0]["6 -Detalhamento"]) {
            const DadosFormatados = formatarDados(response.resposta[0]["6 -Detalhamento"]);
            console.log(DadosFormatados);

            DadosPedidos = DadosFormatados;
            TabelaPedidos(DadosPedidos);
            $('.div-pedidos').removeClass('d-none');
            $('.btn-menu').removeClass('disabled');
        } else {
            console.warn("⚠️ Resposta inválida ou sem '6 -Detalhamento':", response);
            alert("Nenhum detalhamento encontrado na resposta!");
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        alert("Erro ao consultar os pedidos.");
    } finally {
        $('#loadingModal').modal('hide');
    }
};



const Consultar_Ops = async (datainicio, datafim) => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Ops',
                dataInicio: $('#inicio-venda').val(),
                dataFim: $('#final-venda').val(),
            }
        });
        console.log(response)
        DadosOps = response[0]['6 -Detalhamento'];
        TabelaOps(DadosOps);
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
}

async function Consultar_Sem_Ops() {
    $('#loadingModal').modal('show');
    const dados = {
        "dataInico": $('#inicio-venda').val(),
        "dataFim": $('#final-venda').val()
    }

    var requestData = {
        acao: "Consultar_Sem_Ops",
        dados: dados
    };

    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaSemOps(response['resposta'])
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
    } finally {
    }
}

const Consultar_Lista_Pedidos = async () => {
    $('#loadingModal').modal('show');
    try {
        $('#loadingModal').modal('show');
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Lista_Pedidos',
                iniVenda: $('#inicio-venda').val(),
                finalVenda: $('#final-venda').val(),
            }
        });

        console.log(response);
        ListaPedidos(response);
    } catch (error) {
        console.log('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Filtro_Monitor_Ops(Pedidos) {
    $('#loadingModal').modal('show');
    const dados = {
        "dataInico": $('#inicio-venda').val(),
        "dataFim": $('#final-venda').val(),
        "arrayPedidos": Pedidos
    }

    var requestData = {
        acao: "Filtros_Op",
        dados: dados
    };
    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response)
        if (!response['resposta'] || response['resposta'] === null || !response['resposta'][0]) {
            Mensagem_Canto('Não há dados para consulta', 'warning');
            $('#modal-filtros').modal('hide');
            PedidosSelecionados = [];
        } else if (!response['resposta'][0]['6 -Detalhamento'] || response['resposta'][0]['6 -Detalhamento'].length === 0) {
            Mensagem_Canto('Não há dados para consulta', 'warning');
            $('#modal-filtros').modal('hide');
            PedidosSelecionados = [];
        } else {
            TabelaOps(response['resposta'][0]['6 -Detalhamento']);
            Consultar_Skus_Pedidos(Pedidos);
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Consultar_Skus_Pedidos(Pedidos) {
    $('#loadingModal').modal('show');
    const dados = {
        "dataInico": $('#inicio-venda').val(),
        "dataFim": $('#final-venda').val(),
        "arrayPedidos": Pedidos
    }

    var requestData = {
        acao: "Consultar_Skus_Pedidos",
        dados: dados
    };
    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaSkus(response['resposta'][0]['6 -Detalhamento']);
        $('#modal-filtros').modal('hide');
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Consultar_Skus() {
    $('#loadingModal').modal('show');
    const dados = {
        "dataInico": $('#inicio-venda').val(),
        "dataFim": $('#final-venda').val(),
    }
    var requestData = {
        acao: "Consultar_Skus",
        dados: dados
    };
    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaSkus(response['resposta'][0]['6 -Detalhamento']);
        $('#modal-filtros').modal('hide');
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function TabelaPedidos(listaPedidos) {

    listaPedidos.forEach(item => {
        item['Diferenca_Entregas'] = item['09-Entregas Solic'] - item['10-Entregas Fat'];
    });

    if ($.fn.DataTable.isDataTable('#table-pedidos')) {
        $('#table-pedidos').DataTable().destroy();
    }

    const tabela = $('#table-pedidos').DataTable({
        searching: true,
        paging: true,
        lengthChange: true,
        info: false,
        pageLength: 12,
        data: listaPedidos,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Fila de Reposição',
            className: 'btn-tabelas'
        },],
        columns: [{
            data: '02-Pedido',
            render: function (data, type, row) {
                return `<span class="codPedidoClicado" data-codigoPedido="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
            }
        }, {
            data: '01-MARCA'
        }, {
            data: '03-tipoNota'
        }, {
            data: '06-codCliente'
        }, {
            data: 'dataEmissao'
        }, {
            data: '04-PrevOriginal'
        }, {
            data: '11-ultimo fat'
        }, {
            data: '05-PrevAtualiz'
        }, {
            data: '09-Entregas Solic'
        }, {
            data: '10-Entregas Fat'
        }, {
            data: 'Diferenca_Entregas'
        }, {
            data: '12-qtdPecas Fat'
        }, {
            data: '08-vlrSaldo'
        }, {
            data: '16-Valor Atende por Cor'
        }, {
            data: '22-Valor Atende por Cor(Distrib)'
        }, {
            data: 'Saldo +Sugerido'
        }, {
            data: '15-Qtd Atende p/Cor'
        }, {
            data: '21-Qnt Cor(Distrib)'
        }, {
            data: '18-Sugestao(Pedido)'
        }, {
            data: '23-% qtd cor',
            render: function (data) {
                return data + '%'; // Adiciona o símbolo de porcentagem
            }
        }, {
            data: 'Agrupamento'
        }],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-pedidos').html($('.dataTables_paginate').html());
            $('#pagination-pedidos span').remove();
            $('#pagination-pedidos a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('#itens-pedidos').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
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
            const columnsToSum = ['12-qtdPecas Fat', '08-vlrSaldo', '16-Valor Atende por Cor', '22-Valor Atende por Cor(Distrib)', 'Saldo +Sugerido', '15-Qtd Atende p/Cor', '21-Qnt Cor(Distrib)'];

            columnsToSum.forEach((columnName, idx) => {
                const colIndex = idx + 11; // Índice da coluna no DataTables

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



    $('.search-input').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-input').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-input').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function TabelaOps(listaOps) {

    if ($.fn.DataTable.isDataTable('#table-ops')) {
        $('#table-ops').DataTable().destroy();
    }

    const tabela = $('#table-ops').DataTable({
        searching: true,
        paging: true,
        lengthChange: true,
        info: false,
        pageLength: 10,
        data: listaOps,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Fila de Reposição',
            className: 'btn-tabelas'
        },

        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Filtrar Pedidos',
            title: 'Filtrar Pedidos',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                Consultar_Lista_Pedidos();
                $('#modal-filtros').modal('show')
            },
        },
        {
            text: "<i class='bi bi-arrow-return-left' style='margin-right: 5px;'></i> Sku's",
            title: "Sku's",
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('.div-tabela-2').removeClass('d-none');
                $('.div-tabela-1').addClass('d-none');
            },
        },
        ],
        columns: [{
            data: 'numeroop',
            render: function (data, type) {
                return `<span class="codOpClicado" data-codigoOp="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
            }
        },
                  {
            data: 'qtdOP'
        },
        {
            data: 'codItemPai'
        },
        {
            data: 'descricao'
        },
        {
            data: 'codFaseAtual'
        },
        {
            data: 'nome'
        },
        {
            data: 'Ocorrencia Pedidos'
        },
        {
            data: 'AtendePçs'
        },
        {
            data: 'prioridade'
        },
        {
            data: 'dataPrevisaoTermino'
        }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();

            $('#pagination-ops').html(paginateHtml);

            $('#pagination-ops span').remove();

            $('#pagination-ops a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-ops').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-ops').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-input-ops').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-input-ops').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function TabelaSemOps(listaSemOps) {

    if ($.fn.DataTable.isDataTable('#table-sem-ops')) {
        $('#table-sem-ops').DataTable().destroy();
    }

    const tabela = $('#table-sem-ops').DataTable({
        searching: true,
        paging: true,
        lengthChange: true,
        info: false,
        pageLength: 10,
        data: listaSemOps,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Fila de Reposição',
            className: 'btn-tabelas'
        },],
        columns: [{
            data: 'codEngenharia'
        },
        {
            data: 'tamanho'
        },
        {
            data: 'codCor'
        },
        {
            data: 'nomeSKU'
        },
        {
            data: 'QtdSaldo'
        },
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();

            $('#pagination-sem-ops').html(paginateHtml);

            $('#pagination-sem-ops span').remove();

            $('#pagination-sem-ops a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-sem-ops').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-sem-ops').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-input-sem-ops').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-input-sem-ops').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function ListaPedidos(listaPedidos) {

    if ($.fn.DataTable.isDataTable('#table-lista-pedidos')) {
        $('#table-lista-pedidos').DataTable().destroy();
    }

    const tabela = $('#table-lista-pedidos').DataTable({
        searching: true,
        paging: true,
        lengthChange: true,
        info: false,
        pageLength: 10,
        data: listaPedidos,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Fila de Reposição',
            className: 'btn-tabelas'
        },],
        columns: [{
            data: null,
            render: function (row) {
                return `<div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                    <input type="checkbox" class="row-checkbox" value="${row.codLote}">
                </div>`;
            }
        },
        {
            data: 'codPedido'
        },
        {
            data: 'dataEmissao'
        },
        {
            data: 'codCliente'
        },
        {
            data: 'nome_cli'
        },
        {
            data: 'codTipoNota'
        },
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();

            $('#pagination-lista-pedidos').html(paginateHtml);

            $('#pagination-lista-pedidos span').remove();

            $('#pagination-lista-pedidos a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-lista-pedidos').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-lista-pedidos').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-input-lista-pedidos').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-input-lista-pedidos').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    const table = $('#table-lista-pedidos').DataTable();
    let PedidosSelecionados = [];

    async function VerificaPedidosSelecionados() {
        PedidosSelecionados.length = 0;
        table.rows().every(function () {
            const checkbox = $(this.node()).find('.row-checkbox');
            if (checkbox.is(':checked')) {
                const row = this.data();
                const CodigoPedido = String(row['codPedido']);

                // Adiciona o código do lote ao array se não estiver presente
                if (!PedidosSelecionados.includes(CodigoPedido)) {
                    PedidosSelecionados.push(CodigoPedido);
                }
            }
        });

        // Verifica se nenhum lote foi selecionado
        if (PedidosSelecionados.length === 0) {
            Mensagem_Canto('Nenhum pedido selecionado!', 'warning');
        }
    }

    $('#btn-filtrar').off('click').on('click', async () => {
        await VerificaPedidosSelecionados(); // Aguarda a verificação
        if (PedidosSelecionados.length === 0) {
            // Nenhum pedido selecionado
        } else {
            try {
                Filtro_Monitor_Ops(PedidosSelecionados);
            } catch (error) {
                console.error('Erro na solicitação AJAX:', error); // Exibe erro se ocorrer
                $('#loadingModal').modal('hide');
                Mensagem('Erro', 'error');
            }
        }
    });

}

function TabelaSkus(listaSkus) {

    if ($.fn.DataTable.isDataTable('#table-skus')) {
        $('#table-skus').DataTable().destroy();
    }

    const tabela = $('#table-skus').DataTable({
        searching: true,
        paging: true,
        lengthChange: true,
        info: false,
        pageLength: 10,
        data: listaSkus,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Fila de Reposição',
            className: 'btn-tabelas'
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Filtrar Pedidos',
            title: 'Filtrar Pedidos',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                Consultar_Lista_Pedidos();
                $('#modal-filtros').modal('show')
            },
        },
        {
            text: "<i class='bi bi-arrow-return-left' style='margin-right: 5px;'></i> Op's",
            title: "Voltar",
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('.div-tabela-2').addClass('d-none');
                $('.div-tabela-1').removeClass('d-none');

            },
        },
        ],
        columns: [{
            data: 'numeroop'
        },
        {
            data: null,
            render: function (data, type, row) {
                return `${row['codItemPai']}.${row['seqTamanho']}.${row['codCor']}`;
            }
        },
        {
            data: 'codreduzido'
        },
        {
            data: 'descricao'
        },
        {
            data: 'Ocorrencia Pedidos'
        },
        {
            data: 'AtendePçs'
        },
        {
            data: 'qtdOP'
        },
        {
            data: 'codFaseAtual'
        },
        {
            data: 'nome'
        },
        {
            data: 'prioridade'
        },

        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();

            $('#pagination-skus').html(paginateHtml);

            $('#pagination-skus span').remove();

            $('#pagination-skus a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-skus').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-lista-skus').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-input-lista-skus').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-input-lista-skus').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });

}
