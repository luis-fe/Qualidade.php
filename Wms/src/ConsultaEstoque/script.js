$(document).ready(function() {
    $('#itensPorPagina').change(function() {
        const itensPorPagina = $(this).val();
        $('#TableEstoques').DataTable().page.len(itensPorPagina).draw();
    });

    $('#NomeRotina').text('Consulta de Estoques');
    ConsultaEstoques()
});

const ConsultaEstoques = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Estoque',
                natureza: $('#SelectNatureza').val(),
            }
        });

        console.log(response);
        criarTabelaEstoque(response[0]['3- Detalhamento '])

    } catch (error) {
        console.log('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function criarTabelaEstoque(listaEstoque) {
    $('#Paginacao .dataTables_paginate').remove();

    listaEstoque.forEach(item => {
        let endereco = item['Endereco'];
        item['Rua'] = endereco.substring(0, 2);
        item['Modulo'] = endereco.substring(3, 5);
        item['Posicao'] = endereco.substring(6, 8);
    });

    if ($.fn.DataTable.isDataTable('#TableEstoques')) {
        $('#TableEstoques').DataTable().destroy();
    }

    const tabela = $('#TableEstoques').DataTable({
        excel: true,
        responsive: false,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: listaEstoque,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                title: 'Fila de Reposição',
                className: 'ButtonExcel'
            },
            {
                extend: 'colvis',
                text: 'Visibilidade das Colunas',
                className: 'ButtonVisibilidade'
            }
        ],
        columns: [
            {
                    data: 'Rua',
                    visible: false
                },
                {
                    data: 'Modulo',
                    visible: false
                },
                {
                    data: 'Posicao',
                    visible: false
                },
                {
                    data: 'Endereco'
                },
                {
                    data: 'engenharia'
                },
                {
                    data: 'desc_tam'
                },
                {
                    data: 'desc_cor'
                },
                {
                    data: 'codreduzido'
                },
                {
                    data: 'nome'
                },
                {
                    data: 'saldo'
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

    $('.dataTables_paginate').appendTo('#Paginacao');

    $('#Paginacao .paginate_button.previous').on('click', function() {
        tabela.page('previous').draw('page');
    });

    $('#Paginacao .paginate_button.next').on('click', function() {
        tabela.page('next').draw('page');
    });

    const paginaInicial = 1;
    tabela.page(paginaInicial - 1).draw('page');

    $('#Paginacao .paginate_button').on('click', function() {
        $('#Paginacao .paginate_button').removeClass('current');
        $(this).addClass('current');
    });

    // Adiciona evento de busca para cada campo
    $('#searchEngenharia').on('keyup change', function() {
        tabela.column(4).search(this.value).draw();
    });
    $('#searchRua').on('keyup change', function() {
        tabela.column(0).search(this.value).draw();
    });
    $('#searchModulo').on('keyup change', function() {
        tabela.column(1).search(this.value).draw();
    });
    $('#searchPosicao').on('keyup change', function() {
        tabela.column(2).search(this.value).draw();
    });
    $('#searchCodReduzido').on('keyup change', function() {
        tabela.column(7).search(this.value).draw();
    });
}
