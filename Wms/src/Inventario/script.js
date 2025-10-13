let PaginasSelecionadas = 10;

$(document).ready(async () => {
    $('#itensPorPagina').change(function() {
        PaginasSelecionadas = $(this).val();
        $('#TableEmbalagens').DataTable().page.len(PaginasSelecionadas).draw();
    });
    $('#NomeRotina').text('Inventários');

    ConsultaInventarios();

});

const hoje = new Date().toISOString().split('T')[0];
document.getElementById('dataInicio').value = hoje;
document.getElementById('dataFim').value = hoje;

$('#ButtonFiltrar').click(() => {
    ConsultaInventarios()
});


function formatarDados(data) {
    return data.map(item => {
        return {
            'Rua': item['Rua'],
            'Qtd Prateleiras': item['Qtd Prat.'],
            'status': item['status'],
            '% Realizado': item['% Realizado'],
        };
    });
}

const ConsultaInventarios = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Inventarios',
            dataInicio: $('#dataInicio').val(),
            dataFim: $('#dataFim').val(),
            natureza: $('#SelectNatureza').val()
        },
        success: (data) => {
            console.log('Resposta do servidor:', data);
            if(data == null){
                $('#loadingModal').modal('hide');
                Swal.fire({
                title: `Erro`,
                icon: `error`,
                showConfirmButton: false,
                timer: "3000",
            });
                
            } else {
            const dadosFormatados = formatarDados(data[0]['5- Detalhamento Ruas:']);
            console.log(dadosFormatados);
            $('#EnderecosTotais').text(data[0]['3 - Total Enderecos']);
            $('#EnderecosInventariados').text(data[0]['4- Enderecos Inventariados']);
            $('#PecasTotais').text(data[0]['1: Total de Peças']);
            $('#PecasInventariadas').text(data[0]['2- Pçs Inventariadas']);
            criarTabelaInventário(dadosFormatados, PaginasSelecionadas);
        }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            console.error('Erro na consulta de inventários:', textStatus, errorThrown);
        },
        complete: () => {
            $('#loadingModal').modal('hide');
        }
    });
}


function criarTabelaInventário(listaReservas, itensPorPagina) {
    $('#Paginacao .dataTables_paginate').remove();

    if ($.fn.DataTable.isDataTable('#TableEmbalagens')) {
        $('#TableEmbalagens').DataTable().destroy();
    }

    const tabela = $('#TableEmbalagens').DataTable({
        excel: true,
        responsive: true,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: listaReservas,
        lengthChange: false,
        pageLength: itensPorPagina,
        fixedHeader: true,
        dom: 'Bfrtip',
        buttons: [{
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
        columns: [{
                data: 'Rua'
            },
            {
                data: 'Qtd Prateleiras'
            },
            {
                data: 'status'
            },
            {
                data: '% Realizado'
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

    $('#searchEmbalagens').on('keyup', function() {
        tabela.search(this.value).draw();
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
}