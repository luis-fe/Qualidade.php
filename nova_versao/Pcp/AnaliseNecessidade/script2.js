$(document).ready(async () => {
    Consulta_Planos();
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

    $('#btn-vendas').addClass('btn-menu-clicado')
});

const Consulta_Planos = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Planos'
            },
        });
        $('#select-plano').empty();
        $('#select-plano').append('<option value="" disabled selected>Selecione um plano...</option>');
        response.forEach(function (plano) {
            $('#select-plano').append(`
                        <option value="${plano['01- Codigo Plano']}">
                            ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                        </option>
                    `);
        });
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Naturezas = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Naturezas'
            },
        });
        TabelaNaturezas(response);
        $('.div-naturezas').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

const Consulta_Comprometidos = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Comprometidos'
            },
        });
        TabelaComprometido(response);
        $('.div-comprometido').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

const Consulta_Comprometidos_Compras = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Comprometidos_Compras'
            },
        });
        TabelaComprometidoCompras(response);
        $('.div-comprometido-compras').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};




async function Analise_Materiais() {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Analise_Materiais",
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

        await $('.div-analise').removeClass('d-none');
        await TabelaAnalise(response);
        await Consulta_Naturezas();
        await Consulta_Comprometidos();
        Consulta_Comprometidos_Compras();
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Detalha_Necessidade(codReduzido) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Detalha_Necessidade",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "codComponente": codReduzido
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaDetalhamento(response);
        $('#modal-detalhamento').modal('show')
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}


async function TabelaAnalise(listaAnalise) {
    if ($.fn.DataTable.isDataTable('#table-analise')) {
        $('#table-analise').DataTable().destroy();
    }

    const tabela = $('#table-analise').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaAnalise,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Necessidade de Materiais',
            className: 'btn-tabelas'
        },
        ],
        columns: [{
            data: '02-codCompleto'
        },
        {
            data: '03-descricaoComponente'
        },
        {
            data: '01-codReduzido',
            render: function (data, type, row) {
                return `<span class="codReduzido" data-codigoReduzido="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
            }
        },
        {
            data: '10-Necessidade Compra (Tendencia)',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '12-Necessidade Ajustada Compra (Tendencia)',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '08-estoqueAtual',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '09-SaldoPedCompras',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '07-EmRequisicao',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '04-fornencedorPreferencial'
        },
        {
            data: '05-unidade'
        },
        {
            data: '14-Lote Mínimo'
        },
        {
            data: '11-Lote Mutiplo'
        },
        {
            data: '13-LeadTime'
        },
        {
            data: 'fatorConversao'
        },
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
            $('#pagination-analise').html($('.dataTables_paginate').html());
            $('#pagination-analise span').remove();
            $('#pagination-analise a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-analise').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#itens-analise').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('#table-analise tbody').on('click', 'tr', function (event) {
        // Verifica se o clique foi em um hiperlink
        if ($(event.target).hasClass('codReduzido')) {
            return; // Não executa o código para seleção de linha
        }

        const isSelected = $(this).hasClass('selected');
        $('#table-analise tbody tr').removeClass('selected');

        if (!isSelected) {
            $(this).addClass('selected');
            const rowData = tabela.row(this).data();
            const filtro = rowData['01-codReduzido'];
            filtrarTabelas(filtro);
        } else {
            // Remove o filtro e reseta a tabela de naturezas
            filtrarTabelas('');
        }
    });

    // Clique no hiperlink "codReduzido"
    $('#table-analise').on('click', '.codReduzido', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codigoReduzido');
        Detalha_Necessidade(codReduzido);
    });
}


function TabelaNaturezas(listaNaturezas) {
    if ($.fn.DataTable.isDataTable('#table-naturezas')) {
        $('#table-naturezas').DataTable().destroy();
    }

    const tabela = $('#table-naturezas').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaNaturezas,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nome'
        },
        {
            data: 'natureza'
        },
        {
            data: 'estoqueAtual',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
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
            $('#pagination-naturezas').html($('.dataTables_paginate').html());
            $('#pagination-naturezas span').remove();
            $('#pagination-naturezas a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-naturezas').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-naturezas').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaComprometido(listaComprometido) {
    if ($.fn.DataTable.isDataTable('#table-comprometido')) {
        $('#table-comprometido').DataTable().destroy();
    }

    const tabela = $('#table-comprometido').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaComprometido,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nomeMaterial'
        },
        {
            data: 'OP'
        },
        {
            data: 'EmRequisicao',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
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
            $('#pagination-comprometido').html($('.dataTables_paginate').html());
            $('#pagination-comprometido span').remove();
            $('#pagination-comprometido a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-comprometido').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-comprometido').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaComprometidoCompras(listaComprometido) {
    if ($.fn.DataTable.isDataTable('#table-comprometido-compras')) {
        $('#table-comprometido-compras').DataTable().destroy();
    }

    const tabela = $('#table-comprometido-compras').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaComprometido,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nome'
        },
        {
            data: 'numero'
        },
        {
            data: 'tipo'
        },
        {
            data: 'SaldoPedCompras',
            render: function (data, type) {
                if (type === 'display') {
                    // Formata o número para o formato brasileiro com separadores de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outras operações, retorna o número diretamente
                return data;
            }
        },
        {
            data: 'dataPrevisao'
        },
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
            $('#pagination-comprometido-compras').html($('.dataTables_paginate').html());
            $('#pagination-comprometido-compras span').remove();
            $('#pagination-comprometido-compras a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-comprometido-compras').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-comprometido-compras').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaDetalhamento(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento')) {
        $('#table-detalhamento').DataTable().destroy();
    }

    const tabela = $('#table-detalhamento').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaDetalhes,
        columns: [{
            data: '01-codEngenharia'
        },
        {
            data: '04-tam'
        },
        {
            data: '05-codCor'
        },
        {
            data: '03-nome'
        },
        {
            data: '02-codReduzido'
        },
        {
            data: '07-Ocorrencia em Pedidos',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '09-previcaoVendas',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '06-qtdePedida',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '10-faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'class'
        },
        {
            data: 'classCategoria'
        },
        {
            data: '08-statusAFV'
        },
        {
            data: '11-CodComponente'
        },
        {
            data: '12-unid'
        },
        {
            data: '13-consumoUnit'
        },
        {
            data: '14-Necessidade faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
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
            $('#pagination-detalhamento').html($('.dataTables_paginate').html());
            $('#pagination-detalhamento span').remove();
            $('#pagination-detalhamento a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-detalhamento').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamento').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function filtrarTabelas(filtro) {
    const TabelaNaturezas = $('#table-naturezas').DataTable();
    const tabelaComprometido = $('#table-comprometido').DataTable();
    const tabelaCompras = $('#table-comprometido-compras').DataTable();
    
    if (filtro === '') {
        TabelaNaturezas.column(0).search('').draw();
        tabelaComprometido.column(0).search('').draw();
        tabelaCompras.column(0).search('').draw();
    } else {
        TabelaNaturezas.column(0).search(`^${filtro}$`, true, false).draw();
        tabelaComprometido.column(0).search(`^${filtro}$`, true, false).draw();
        tabelaCompras.column(0).search(`^${filtro}$`, true, false).draw();
    }


}

