$(document).ready(async () => {
    await Consulta_Planos();
    await Consulta_Marcas();
    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    $('#select-pedidos-bloqueados').select2({
        placeholder: "Pedidos Bloqueados?",
        allowClear: false,
        width: '100%'
    });

    $('#btn-vendas').addClass('btn-menu-clicado');

    console.log('teste')
});

function alterna_button_selecionado(button) {
    $(button).closest('.d-flex').find('button').removeClass('btn-menu-clicado');
    $(button).addClass('btn-menu-clicado');
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

function Consulta_Marcas() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Marcas',
        },
        success: function (data) {
            console.log(data);

            $('#select-marca').empty();
            $('#select-marca-categoria').empty();
            $('#select-marca').append('<option value="" disabled selected>Selecione uma marca...</option>');
            $('#select-marca-categoria').append('<option value="" disabled selected>Selecione uma marca...</option>');
            data.forEach(function (marca) {
                $('#select-marca').append(`
                        <option value="${marca['marca']}">
                            ${marca['marca']}
                        </option>
                    `);
                $('#select-marca-categoria').append(`
                        <option value="${marca['marca']}">
                            ${marca['marca']}
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

async function Vendas_por_Plano() {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Vendas_por_Plano",
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
        if (response === null) {
            TabelaVendido(response)
        } else {
            const dadosFiltrados = response[0]["7- Detalhamento:"].filter(item => item.marca !== 'TOTAL');
            TabelaVendido(dadosFiltrados);
            TabelaVendidoCategoria(response[0]["8- DetalhamentoCategoria"])
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function TabelaVendido(listaVendido) {
    if ($.fn.DataTable.isDataTable('#table-vendas')) {
        $('#table-vendas').DataTable().destroy();
    }

    const tabela = $('#table-vendas').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: [],
        columns: [
            {
                data: 'marca'
            },
            {
                data: 'metaFinanceira',
                render: function (data, type) {
                    let ValorInt = parseFloat(data.replace(/[^\d,]/g, '').replace(',', '.'));
                    return type === 'display' ?
                        `R$ ${ValorInt.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` :
                        ValorInt;
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
                data: 'metaPecas',
                render: function (data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'qtdePedida',
                render: function (data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'qtdeFaturada',
                render: function (data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'faltaProgVendido',
                render: function (data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
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

            $('#pagination-vendas').html(paginateHtml);

            $('#pagination-vendas span').remove();

            $('#pagination-vendas a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-vendas').on('input', function () {
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
            const columnsToSum = ['metaFinanceira', 'valorVendido', 'metaPecas', 'qtdePedida', 'qtdeFaturada', 'faltaProgVendido'];

            columnsToSum.forEach((columnName, idx) => {
                const colIndex = idx + 1; // Índice da coluna no DataTables

                // Total considerando todos os dados após filtro
                const total = api.column(colIndex, {
                    filter: 'applied'
                }).data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Atualizar o rodapé da coluna
                $(api.column(colIndex).footer()).html(
                    columnName === 'metaFinanceira' ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : total.toLocaleString('pt-BR'),
                    columnName === 'valorVendido' ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : total.toLocaleString('pt-BR')
                );
            });
        },
    });

    if (!listaVendido || listaVendido.length === 0) {
        tabela.clear().draw();
        $('#btn-vendas-categoria').addClass('disabled');
        return;
    } else {
        $('#btn-vendas-categoria').removeClass('disabled');
    };

    tabela.clear().rows.add(listaVendido).draw();

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

function TabelaVendidoCategoria(listaVendido) {
    // Destruir a tabela existente, se houver
    if ($.fn.DataTable.isDataTable('#table-vendas-categoria')) {
        $('#table-vendas-categoria').DataTable().destroy();
    }

    // Configuração inicial da tabela
    const tabela = $('#table-vendas-categoria').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: [],
        columns: [{
            data: 'categoria'
        },
        {
            data: 'marca'
        },
        {
            data: 'metaFinanceira',
            render: function (data, type) {
                let ValorInt = parseFloat(data.replace(/[^\d,]/g, '').replace(',', '.'));
                return type === 'display' ?
                    `R$ ${ValorInt.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` :
                    ValorInt;
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
            data: 'metaPcs',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'quantidadeVendida',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'quantidadeFaturada',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
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

            $('#pagination-vendas-categoria').html(paginateHtml);

            $('#pagination-vendas-categoria span').remove();

            $('#pagination-vendas-categoria a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-vendas-categoria').on('input', function () {
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
            const columnsToSum = ['metaFinanceira', 'valorVendido', 'metaPecas', 'quantidadeVendida', 'quantidadeFaturada'];

            columnsToSum.forEach((columnName, idx) => {
                const colIndex = idx + 2; // Índice da coluna no DataTables

                // Total considerando todos os dados após filtro
                const total = api.column(colIndex, {
                    filter: 'applied'
                }).data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Atualizar o rodapé da coluna
                $(api.column(colIndex).footer()).html(
                    columnName === 'metaFinanceira' ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : total.toLocaleString('pt-BR'),
                    columnName === 'valorVendido' ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : total.toLocaleString('pt-BR')
                );
            });
        },
    });

    // Verificar se os dados estão disponíveis
    if (!listaVendido || listaVendido.length === 0) {
        tabela.clear().draw();
        return;
    }

    // Processar os dados da API
    const dadosProcessados = listaVendido.flatMap(categoria => {
        const nomeCategoria = categoria["8.1-categoria"];
        const marcas = Object.keys(categoria["8.5-qtdVendido"]);

        return marcas.map(marca => ({
            categoria: nomeCategoria,
            marca: marca,
            metaFinanceira: categoria["8.9-metaFinanceira"][marca] || "-",
            valorVendido: categoria["8.6-valorVendido"][marca] || "-",
            metaPcs: categoria["8.7-metaPcs"][marca] || "-",
            quantidadeVendida: categoria["8.5-qtdVendido"][marca] || "-",
            quantidadeFaturada: categoria["8.8-qtdeFaturada"][marca] || "-"
        }));
    });

    // Adicionar os dados processados à tabela
    tabela.clear().rows.add(dadosProcessados).draw();

    // Configurar eventos de pesquisa
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
