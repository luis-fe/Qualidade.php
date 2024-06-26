<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include_once("../../../templates/Loading.php")
?>
<link rel="stylesheet" href="style.css">
<style>
 .clickable-number {
    cursor: pointer;
}

.clickable-number .numero {
    color: blue;
    text-decoration: underline;
}

</style>

<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row">
            <div class="col-12 col-md-2">
                <label for="Natureza" class="form-label">Natureza</label>
                <select class="form-select" id="SelectNatureza" required>
                    <option value="5">5</option>
                    <option value="7">7</option>
                    <option value="">Ambas</option>
                </select>
            </div>
            <div class="col-12 col-md-2 text-center mt-3 mt-md-4">
                <button type="button" id="ButtonFiltrar" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 col-md-6 d-flex align-items-center mb-3">
                <label for="itensPorPagina" class="me-2">Mostrar</label>
                <select class="form-select" id="itensPorPagina" style="width: auto;">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="ms-2">elementos</span>
            </div>
            <div class="col-12 col-md-6">
                <div id="search-container">
                    <input type="text" id="searchEmbalagens" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="TableFila">
                <thead>
                    <tr>
                        <th scope="col">Caixas Abertas</th>
                        <th scope="col">Código Reduzido</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Numero Op</th>
                        <th scope="col">Pçs</th>
                        <th scope="col">Estoque Csw</th>
                        <th scope="col">Estoque Endereçado</th>
                        <th scope="col">Descrição Lote</th>
                        <th scope="col">Data Entrada Op</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tabela preenchida via JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3" id="Paginacao">
            <!-- Paginação será preenchida pelo DataTables -->
        </div>
        <div class="row text-center" style="margin-top: 25px; width: 100%; align-items: center; justify-content: center">
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Peças em Fila:</label>
                <label for="text" class="form-control btn-primary" id="TotalPcsFila"></label>
            </div>
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Total Caixas em Fila:</label>
                <label for="text" class="form-control btn-primary" id="TotalCaixasFila"></label>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable">
                        <thead id="fixed-header">
                            <tr>
                                <th>Data Reposição</th>
                                <th>Cod Barras Tag</th>
                                <th>Cod Reduzido</th>
                                <th>EPC</th>
                                <th>Nome</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be appended here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dataModal2" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel2"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable2">
                        <thead id="fixed-header">
                            <tr>
                                <th>Data Entrada</th>
                                <th>Cod Barras Tag</th>
                                <th>Cod Reduzido</th>
                                <th>EPC</th>
                                <th>Numero Op</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be appended here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>
<script>
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
        columns: [
           {
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
                    data: 'SaldoEnderecos'
                },
                {
                    data: 'estoqueCsw'
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

</script>
