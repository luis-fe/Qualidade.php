$(document).ready(() => {

    $('#NomeRotina').text('Metas');
    $('#loadingModal').modal('hide')
    // $('#table thead th').on('click', '.no-sort', function(event) {
    //     event.stopPropagation();
    //     console.log('Ícone clicado');
    // });
});

async function ConsultarMetas() {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            codigoPlano: $('#codigoPlano').val(),
            arrayCodLoteCsw: [$('#SelectLote').val()]
        };

        const requestData = {
            acao: "Consultar_Metas",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response);
        CriarTabelaMetas(response[0]['1-Detalhamento']);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function CriarTabelaMetas(listaMetas) {
    if ($.fn.DataTable.isDataTable('#table')) {
        $('#table').DataTable().destroy();
    }

    const tabela = $('#table').DataTable({
        paging: false,
        info: false,
        searching: true,
        colReorder: true,
        data: listaMetas,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        ordering: false,
        columns: [{
                data: 'apresentacao',
                visible: false
            },
            {
                data: 'codFase',
                visible: false
            },
            {
                data: 'nomeFase'
            },
            {
                data: 'previsao',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'FaltaProgramar',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Carga Atual',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Fila',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Falta Produzir',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'dias'
            },
            {
                data: 'Meta Dia',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },

        ],
        language: {
            paginate: {
                first: 'Primeira',
                previous: '<',
                next: '>',
                last: 'Última',
            },
        },

    });
    $('#search').on('keyup', function() {
        tabela.search(this.value).draw();
    });
}

async function ConsultaLote(event) {
    if (event.key === 'Enter') {
        event.preventDefault();

        if ($('#codigoPlano').val() === '') {
            Mensagem('Campo Vazio', 'warning')
        } else {
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Lotes',
                        plano: $('#codigoPlano').val()
                    }
                });

                const $select = $('#SelectLote');
                $select.empty();
                $select.append('<option value="">Selecione o Lote:</option>');

                response.forEach(item => {
                    $select.append(`<option value="${item.lote}">${item.lote} - ${item.nomelote}</option>`);
                });
                // Ativar o Select2 para o elemento
                $('#SelectLote').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    height: '30px',
                });
                $('.select2-selection__rendered').addClass('form-control')

                $('#DescPlano').val(response[0]['nomelote']);
                $('#selects').removeClass('d-none');
            } catch (error) {
                console.error('Erro ao consultar chamados:', error);
            } finally {
                $('#loadingModal').modal('hide');
            }
        }
    }
}



function SelecaoLote() {

    ConsultarMetas();
    $('#table').removeClass('d-none');
    $('#campo-search').removeClass('d-none');

}