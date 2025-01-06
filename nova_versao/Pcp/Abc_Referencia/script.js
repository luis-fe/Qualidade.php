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

async function Abc_Referencia() {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Abc_Referencia",
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
        TabelaAbc(response);
        $('.div-abc').removeClass('d-none')
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function TabelaAbc(listaAbc) {
    if ($.fn.DataTable.isDataTable('#table-abc')) {
        $('#table-abc').DataTable().destroy();
    }

    const tabela = $('#table-abc').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaAbc,
        columns: [{
                data: 'marca'
            },
            {
                data: 'codItemPai'
            },
            {
                data: 'nome'
            },
            {
                data: 'class'
            },
            {
                data: 'categoria'
            },
            {
                data: 'classCategoria'
            },
            {
                data: 'qtdePedida',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data.toLocaleString('pt-BR');
                    }
                    return data; // Retorna o valor original para ordenação
                }
            },
            {
                data: 'qtdeFaturada',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data.toLocaleString('pt-BR');
                    }
                    return data; // Retorna o valor original para ordenação
                }
            },
            {
                data: 'valorVendido',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return `R$ ${parseFloat(data).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    }
                    return data; // Retorna o valor original para ordenação
                }
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
            $('#pagination-abc').html($('.dataTables_paginate').html());
            $('#pagination-abc span').remove();
            $('#pagination-abc a').off('click').on('click', function(e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-abc').on('input', function() {
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