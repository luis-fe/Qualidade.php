$(document).ready(async () => {
    $('#itensPorPagina').change(function() {
        const itensPorPagina = $(this).val();
        $('#TableTags').DataTable().page.len(itensPorPagina).draw();
    });

    ConsultarTags();

});



const ConsultarTags = () => {
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Tags'
        },
        success: (data) => {
            console.log(data);
            criarTabelaTags(data[0]['4- Detalhamento ']);
        },
    });
}

function criarTabelaTags(listaReservas) {
    $('#Paginacao .dataTables_paginate').remove();

    if ($.fn.DataTable.isDataTable('#TableTags')) {
        $('#TableTags').DataTable().destroy();
    }

    const tabela = $('#TableTags').DataTable({
        excel: true,
        responsive: false,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: listaReservas,
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
            { data: 'reduzido' },
                    { data: 'em_conferencia' },
                    { data: 'situacao3' },
                    { data: 'posicao_estoque' },
                    { data: 'diferenca' }
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

    $('#searchFila').on('keyup', function() {
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