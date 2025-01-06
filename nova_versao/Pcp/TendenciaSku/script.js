$(document).ready(async () => {
    await Consulta_Planos();
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

function Consulta_Planos() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Planos',
        },
        success: function(data) {
            $('#select-plano').empty();
            $('#select-plano').append('<option value="" disabled selected>Selecione um plano...</option>');
            data.forEach(function(plano) {
                $('#select-plano').append(`
                        <option value="${plano['01- Codigo Plano']}">
                            ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                        </option>
                    `);
            });
            $('#loadingModal').modal('hide');
        },
        error: function(xhr, status, error) {
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
        $('.div-tendencia').removeClass('d-none')
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}


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
            className: 'btn-tabelas'
        }],
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
                data: 'Ocorrencia em Pedidos',
                render: function(data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'valorVendido',
                render: function(data, type) {
                    let ValorInt = parseFloat(data.replace(/[^\d,]/g, '').replace(',', '.'));
                    return type === 'display' ?
                        `R$ ${ValorInt.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` :
                        ValorInt;
                }
            },
            {
                data: 'previcaoVendas',
                render: function(data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'qtdePedida',
                render: function(data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'qtdeFaturada',
                render: function(data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'estoqueAtual',
                render: function(data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'emProcesso',
                render: function(data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'faltaProg (Tendencia)',
                render: function(data, type) {
                    return type === 'display' ? data.toLocaleString('pt-BR') : data;
                }
            },
            {
                data: 'Prev Sobra',
                render: function(data, type) {
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
        drawCallback: function() {
            $('#pagination-tendencia').html($('.dataTables_paginate').html());
            $('#pagination-tendencia span').remove();
            $('#pagination-tendencia a').off('click').on('click', function(e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        },
        footerCallback: function(row, data, start, end, display) {
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
            const columnsToSum = ['valorVendido', 'previcaoVendas', 'qtdePedida', 'qtdeFaturada', 'estoqueAtual', 'emProcesso', 'faltaProg (Tendencia)', 'Prev Sobra'];

            columnsToSum.forEach((columnName, idx) => {
                const colIndex = idx + 8; // Índice da coluna no DataTables

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

    $('.search-input-tendencia').on('input', function() {
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
}