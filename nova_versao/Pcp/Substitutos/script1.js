$(document).ready(() => {
    Consulta_Substitutos();
})


const Consulta_Item = async (item, inputDescricao) => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Item',
                item: item
            }
        });
        if (response[0]["status"] === true) {
            $(`#${inputDescricao}`).val(response[0]['nome'])
        } else {
            Mensagem_Canto('Produto não Encontrado', 'warning')
        }

    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

async function Salvar_Substitutos() {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Salvar_Substitutos",

            dados: {
                "codMateriaPrima": $('#input-codigo-original').val(),
                "codMateriaPrimaSubstituto": $('#input-codigo-substituto').val()
            }
        };
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response)
        if (response[0]['status'] === true) {
            await Consulta_Substitutos();
            $('#modal-substitutos').modal('hide')
            Mensagem_Canto('Substituto Inserido', 'success');
        } else {
            $('#modal-substitutos').modal('hide')
            Mensagem_Canto('Erro', 'error');
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Substitutos = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Substitutos'
            }
        });
        TabelaSubstitutos(response)

    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

function TabelaSubstitutos(listaSubstitutos) {
    if ($.fn.DataTable.isDataTable('#table-substitutos')) {
        $('#table-substitutos').DataTable().destroy();
    }

    const tabela = $('#table-substitutos').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaSubstitutos,
        columns: [{
            data: 'codMateriaPrima'
        },
        {
            data: 'nomeCodMateriaPrima'
        },
        {
            data: 'codMateriaPrimaSubstituto'
        },
        {
            data: 'nomeCodSubstituto'
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
            $('#pagination-substitutos').html($('.dataTables_paginate').html());
            $('#pagination-substitutos span').remove();
            $('#pagination-substitutos a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        },

    });

    $('.search-input-substitutos').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#itens-substitutos').on('input', function () {
        const pageLength = parseInt($(this).val(), 10);
        if (!isNaN(pageLength) && pageLength > 0) {
            tabela.page.len(pageLength).draw();
        }
    });


}