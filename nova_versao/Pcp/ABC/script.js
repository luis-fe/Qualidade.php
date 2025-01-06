$(document).ready(async () => {
    $('#loadingModal').modal('show');
    await Consulta_Abc();
    $('#loadingModal').modal('hide');
});

const Consulta_Abc = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc'
            },
        });
        TabelaAbc(response);
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

async function Cadastrar_Parametro() {
    $('#loadingModal').modal('show');
    const dados = {
        parametroABC: $('#input-parametro').val()
    };

    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify({
                acao: 'Cadastrar_Parametro',
                dados
            }),
        });
        console.log('Sucesso:', response);
        if (response['status'] === true) {
            Mensagem_Canto('Parametro Cadastrado', 'success');
            await Consulta_Abc();
        } else {
            Mensagem_Canto('Erro', 'error')
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
        $('#modal-parametros').modal('hide');
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
            data: 'nomeABC'
        }, ],
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
            $('#pagination-abc').html(paginateHtml);
            $('#pagination-abc span').remove();
            $('#pagination-abc a').off('click').on('click', function(e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-abc').on('input', function() {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        },
    });

    $('.search-input').off('keyup change').on('keyup change', function() {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });
}