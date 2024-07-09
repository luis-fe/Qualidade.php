let PaginasSelecionadas = 10;

$(document).ready(async () => {
    $('#itensPorPagina').change(function() {
        PaginasSelecionadas = $(this).val();
        $('#TableFila').DataTable().page.len(PaginasSelecionadas).draw();
    });
    $('#NomeRotina').text('Fila de Reposição');

    ConsultaFila();

});


$('#ButtonFiltrar').click(() => {
    ConsultaFila()
});


const ConsultaFila = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Fila',
            natureza: $('#SelectNatureza').val()
        },
        success: (data) => {
            console.log(data);
            CriarTabelaFila(data[0]['2.0- Detalhamento']);
            $('#TotalPcsFila').text(Number(parseInt(data[0]['1.0- Total Peças Fila'])).toLocaleString('pt-BR'));
            $('#TotalCaixasFila').text(Number(data[0]['1.1- Total Caixas na Fila']).toLocaleString('pt-BR'));
            $('#LabelAtualizacao').text(`Última Atualização: ${data[0]['1.2- Ultima Atualizacao']}`)
            $('#loadingModal').modal('hide');
        },
    });
}

const ConsultaCaixa = (numCaixa) => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Caixa',
            numCaixa: numCaixa,
        },
        success: async (data) => {
            console.log(data);
            $('#loadingModal').modal('hide');
            await CriarTabelaModal(data);

            $('#dataModal').modal('show');
            $('#fixed-header').css({
                'position': 'sticky',
                'top': '0',
                'z-index': '1000'
            });
            $('#dataModalLabel').text(`Detalhamento Caixa: ${numCaixa}`)

        },
    });
};

const AtualizaFila = () => {
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Atualizar_Fila',
        },
        success: async (data) => {
            console.log(data);
            $('#Atualizar').removeClass('d-none');
            $('#Atualizando').addClass('d-none');
            ConsultaFila();
            $('#loadingModal').modal('hide');
        },
    });
};

const ConsultaTagsReduzido = (codReduzido, numeroOp) => {
    $('#loadingModal').modal('show');
    console.log(codReduzido)
    console.log(numeroOp)
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Reduzido',
            natureza: $('#SelectNatureza').val(),
            codReduzido: codReduzido,
            numeroOp: numeroOp
        },
        success: async (data) => {
            console.log(data);
            $('#loadingModal').modal('hide');
            await CriarTabelaModal2(data);

            $('#dataModal2').modal('show');
            $('#fixed-header').css({
                'position': 'sticky',
                'top': '0',
                'z-index': '1000'
            });
            $('#dataModalLabel2').text(`Detalhamento Reduzido: ${codReduzido}`)

        },
    });
};



function CriarTabelaModal(data) {
    const tbody = $('#dataTable tbody');
    tbody.empty(); // Clear existing data

    data.forEach(item => {
        const row = `<tr>
                    <td>${item.DataReposicao}</td>
                    <td>${item.codbarrastag}</td>
                    <td>${item.codreduzido}</td>
                    <td>${item.epc}</td>
                    <td>${item.nome}</td>
                </tr>`;
        tbody.append(row);
    });
}

function CriarTabelaModal2(data) {
    const tbody = $('#dataTable2 tbody');
    tbody.empty(); // Clear existing data

    data.forEach(item => {
        const row = `<tr>
                    <td>${item.DataHora}</td>
                    <td>${item.codbarrastag}</td>
                    <td>${item.codreduzido}</td>
                    <td>${item.epc}</td>
                    <td>${item.numeroop}</td>
                </tr>`;
        tbody.append(row);
    });
}

function CriarTabelaFila(ListaFila, itensPorPagina) {
    $('#Paginacao .dataTables_paginate').remove();

    if ($.fn.DataTable.isDataTable('#TableFila')) {
        $('#TableFila').DataTable().destroy();
    }

    const tabela = $('#TableFila').DataTable({
        excel: true,
        responsive: true,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: ListaFila,
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
                data: 'caixas',
                render: function(data, type, row) {
                    if (data === '-') {
                        return '-';
                    } else {
                        return data.split(',').map(item => {
                            let [numero, valor] = item.split(':');
                            return `<span class="numeroCaixaClicado" data-numero="${numero}" data-valor="${valor}"><span class="numero" style="text-decoration: underline; color: blue; cursor: pointer;">${numero}</span> - ${valor} pçs</span>`;
                        }).join(', ');
                    }
                }
            },
            {
                data: 'codreduzido',
                render: function(data, type, row) {
                    return `<span class="codReduzidoClicado" data-codreduzido="${data}" data-numeroop="${row.numeroop}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
                }
            },
            {
                data: 'descricao'
            },
            {
                data: 'numeroop'
            },
            {
                data: 'pcs'
            },
            {
                data: 'estoqueCsw'
            },
            {
                data: 'SaldoEnderecos'
            },
            {
                data: 'descOP'
            },
            {
                data: 'dataFim'
            }
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

    // Adicionar evento de clique aos números clicáveis após o desenho da tabela
    $('#TableFila').on('click', '.numeroCaixaClicado', function() {
        let numero = $(this).data('numero');
        ConsultaCaixa(parseInt(numero));

    });

    $('#TableFila').on('click', '.codReduzidoClicado', function() {
        const codreduzido = $(this).data('codreduzido');
        const numeroop = $(this).data('numeroop');
        ConsultaTagsReduzido(codreduzido, numeroop);
    });
}
