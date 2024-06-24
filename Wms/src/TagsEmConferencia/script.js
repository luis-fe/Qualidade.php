let PaginasSelecionadas = 10;

$(document).ready(async () => {
    $('#itensPorPagina').change(function() {
        PaginasSelecionadas = $(this).val();
        $('#TableTagsConf').DataTable().page.len(PaginasSelecionadas).draw();
    });
    $('#NomeRotina').text("Tag's em Conferência");

    ConsultaTags();

});


const ConsultaTags = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Tags',
        },
        success: (data) => {
            console.log(data);
            criarTabelaTags(data[0]['2.0- Detalhamento'], PaginasSelecionadas);
            $('#TotalPcs').text(Number(parseInt(data[0]['1.0- Total Peças'])).toLocaleString('pt-BR'));
            $('#TotalPedidos').text(Number(parseInt(data[0]['1.1- Total Pedidos na Fila'])).toLocaleString('pt-BR'));
            $('#loadingModal').modal('hide');
        },
    });
}

function criarTabelaTags(ListaTags, itensPorPagina) {
    $('#Paginacao .dataTables_paginate').remove();

    if ($.fn.DataTable.isDataTable('#TableTagsConf')) {
        $('#TableTagsConf').DataTable().destroy();
    }

    const tabela = $('#TableTagsConf').DataTable({
        excel: true,
        responsive: true,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: ListaTags,
        lengthChange: false,
        pageLength: itensPorPagina,
        fixedHeader: true,
        dom: 'Bfrtip',
        buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                title: "Tag's em Conferencia",
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
                data: 'codpedido'
            },
            {
                data: 'engenharia'
            },
            {
                data: 'codreduzido'
            },
            {
                data: 'codbarrastag'
            },
            {data: 'epc'},
            {data: 'dataseparacao'},
            {data: 'usuario_separou'},
            {data: 'EnderecoOrigem'}
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

