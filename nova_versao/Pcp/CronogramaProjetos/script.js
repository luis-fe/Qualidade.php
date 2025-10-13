$(document).ready(async () => {
    $('#loadingModal').modal('show');
    await ConsultaCronograma();
    $('#loadingModal').modal('hide');
});



const ConsultaCronograma = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'getCronograma'
            },
        });
        TabelaCronograma(response);
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};


function TabelaCronograma(lista) {
    if ($.fn.DataTable.isDataTable('#table-abc')) {
        $('#table-abc').DataTable().destroy();
    }

    const tabela = $('#table-abc').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 15,
        data: lista,
                buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
                title: 'Cronograma de Atividades',
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
        columns: [
            {
            data: 'atividade'
        }, 
                    {
            data: 'dataInicio'
        }, 
                    {
            data: 'dataFinal'
        }, 
        {
            data: 'Responsavel'
        }, 
        {
                data: 'status',

        }, 
                        {
            data: 'projeto'
        }, 
                                {
            data: 'descricaoAtividade'
        }, 

        {
                    data: null,
    orderable: false,
    searchable: false,
    render: function(data, type, row) {
        return `
            <button class="btn btn-sm btn-primary btn-movimentar" data-id="${row.id}" title="Movimentar">
                <i class="bi bi-arrow-left-right"></i>
            </button>
        `;
    }

        }, 
    
    ],
        createdRow: function(row, data, dataIndex) {
     const statusCell = $('td', row).eq(4); // coluna "status"

        if (data.status === 'Em Andamento') {
            statusCell.addClass('status-amarelo');
        } else if (data.status === 'Nao Iniciado') {
            statusCell.addClass('status-cinza');
        } else if (data.status === 'Concluido') {
            statusCell.addClass('status-verde');
        }
    },  
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado",
        },
        drawCallback: function() {
            const paginateHtml = $('.dataTables_paginate').html();
            $('#pagination-analise').html(paginateHtml);
            $('#pagination-analise span').remove();
            $('#pagination-analise a').off('click').on('click', function(e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-analise').on('input', function() {
                const pageLength = parseInt($(this).val(), 15);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();

                $('.btn-movimentar').off('click').on('click', function() {
    const id = $(this).data('id');
    console.log('Movimentar item ID:', id);
    // Aqui você pode abrir um modal, redirecionar, chamar função AJAX etc.
});

        },
    });
    tabela.buttons().container()
        .appendTo('.dt-buttons-container');
    $('.search-input').off('keyup change').on('keyup change', function() {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });
}