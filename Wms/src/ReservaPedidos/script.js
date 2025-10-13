$(document).ready(async () => {
    $('#itensPorPagina').change(function() {
        const itensPorPagina = $(this).val();
        $('#TableReserva').DataTable().page.len(itensPorPagina).draw();
    });
    $('#NomeRotina').text('Reserva de Pedidos');
    ConsultaReservas();

});



const ConsultaReservas = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Reservas'
        },
        success: (data) => {
            console.log(data);
            criarTabelaReservas(data);
            $('#loadingModal').modal('hide');
        },
    });
}

function criarTabelaReservas(listaReservas) {
    $('#Paginacao .dataTables_paginate').remove();

    if ($.fn.DataTable.isDataTable('#TableReserva')) {
        $('#TableReserva').DataTable().destroy();
    }

    const tabela = $('#TableReserva').DataTable({
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
                title: 'Reserva de Pedidos',
                className: 'ButtonExcel'
            },
            {
                extend: 'colvis',
                text: 'Visibilidade das Colunas',
                className: 'ButtonVisibilidade'
            }
        ],
        columns: [
            {data: 'codPedido'},
            {data: 'codTipoNota'},
            {data: 'QtdePedida'},
            {data: 'dataFaturamento'},
            {data: 'codCliente'},
            {data: 'nomeCliente'},
            {data: 'Pçaberto'},
            {data: 'descricao'},
            {data: 'entregas_Solicitadas'},
            {data: 'entregas_realiadas'},
            {data: 'situacao Pedido'},
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

    $('#searchReserva').on('keyup', function() {
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