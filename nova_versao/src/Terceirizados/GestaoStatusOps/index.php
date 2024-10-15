<?php
include_once("requests.php");
include_once("../../../templates/Loading.php");
include_once("../../../templates/header.php");
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="style2.css">
<style>
   .input-group {
        margin-bottom: 15px;
    }

    .dropdown {
        margin-top: 15px;
    }

    .inner-accordion {
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .accordion {
            width: 95%;
        }

        .form-group,
        .dropdown {
            width: 100%;
            margin-left: 0;
        }
    }

    .input-group {
        position: relative;
    }

    .input-group .form-control {
        border: 1px solid #007bff;
        border-radius: 5px 0 0 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .input-group .btn {
        border-radius: 0 5px 5px 0;
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .input-group .btn:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    table.dataTable {
        border-collapse: collapse;
        width: 100%;
        max-width: 100%;
        overflow: auto;
        font-size: 14px;
        padding: 5px;
        font-weight: 600;
    }

    table.dataTable thead {
        background-color: #002955;
        color: white;
        font-size: 15px;
    }

    table.dataTable thead th {
        padding: 5px;
        text-align: center;
        border-bottom: 1px solid lightgray;
        border-right: 1px solid gray;
    }

    .table.dataTable th,
    .table.dataTable td {
        white-space: nowrap;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }


    table.dataTable td {
        padding: 7px;
    }

    table.dataTable tbody tr:hover {
        background-color: lightblue;
        /* Cor de fundo ao passar o mouse */
    }

    /* Estilo para as células */
    table.dataTable tbody td {
        border-bottom: 1px solid lightgray;
        border-right: 1px solid lightgray;
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 20px;
        text-align: right;
        /* Alinha os botões à direita */
    }

    #itemCountContainer .paginate_button,
    #itemCountContainer-ops .paginate_button,
    #itemCountContainer-sem-ops .paginate_button {
        padding: 5px 8px;
        margin: 0 0px;
        background: #007bff;
        color: #002955;
        background-color: white;
        border-top: 1px solid gray;
        border-bottom: 1px solid gray;
        border-right: 0.5px solid gray;
        border-left: 0.5px solid gray;
        transition: background-color 0.3s;
        cursor: pointer;
        text-decoration: none;
    }

    #itemCountContainer .paginate_button.previous,
    #itemCountContainer-ops .paginate_button.previous,
    #itemCountContainer-sem-ops .paginate_button.previous {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        border-left: 1px solid gray;
    }

    #itemCountContainer .paginate_button.next,
    #itemCountContainer-ops .paginate_button.next,
    #itemCountContainer-sem-ops .paginate_button.next {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
        border-right: 1px solid gray;
    }

    #itemCountContainer .paginate_button:hover,
    #itemCountContainer-ops .paginate_button:hover,
    #itemCountContainer-sem-ops .paginate_button:hover {
        background: #002955;
        color: white;
        /* Cor do botão ao passar o mouse */
    }

    #itemCountContainer .paginate_button.current,
    #itemCountContainer-ops .paginate_button.current,
    #itemCountContainer-sem-ops .paginate_button.current {
        background: #002955;
        color: white;
        /* Cor do botão ativo */
    }


    /* Estilo para a linha de pesquisa */
    .search-row input {
        width: 100%;
        /* Largura do campo de pesquisa */
        box-sizing: border-box;
        /* Inclui padding e border no cálculo da largura */
        padding: 5px;
        /* Padding do campo de pesquisa */
        margin-top: 5px;
        /* Espaço acima do campo de pesquisa */
    }

    .pagination-container-pedidos {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        font-size: 13px;
    }

    #itemCount {
        width: 60px;
        display: inline-block;
        border-radius: 15px;
        border: 1px solid gray;
        font-size: 13px;
    }

    .search-input,
    .search-input-ops,
    .search-input-sem-ops {
        margin-top: -10px;
        border-radius: 15px;
        padding: 1px 10px;
    }

    .dataTables_filter {
        display: none;
    }

    .div-tabela input:focus {
        outline: none;
    }

    .ButtonExcel {
        background: linear-gradient(135deg, #4caf50, #81c784) !important;
        border: none;
        border-radius: 8px;
        color: white;
        padding: 5px 15px;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s, transform 0.3s;
        cursor: pointer;
        float: left;
        margin-bottom: 10px;
    }
</style>

<div class="titulo" style="padding: 10px; text-align: left; border-bottom: 1px solid black; color: black; font-size: 15px; font-weight: 600;">
    <i class="icon ph-bold ph-target"></i> Status das Op's
</div>
<div class="corpo">
    <div class="corpo-ops d-flex justify-content-md-start justify-content-center" style="padding: 5px 10px; flex-wrap: wrap;">
        <div class="col-12">
            <div class="div-tabela" style="max-width: 100%; overflow: auto">
                <table class="table table-bordered" id="TableOps">
                    <thead>
                        <tr>
                            <th scope="col">OP<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Facção<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Nome Facção<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Engenharia<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Descrição Engenharia<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Prioridade<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Qtd.<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Data Envio<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Dias para Retorno<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                            <th scope="col">Status<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-ops">
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    $(document).ready(async () => {
        $('#loadingModal').modal('show');
        await Consultar_Faccionistas();
        $('#loadingModal').modal('hide');
        $('#btn-ops').addClass('btn-menu-clicado')
    });


    async function Consultar_Faccionistas() {
        $('#loadingModal').modal('show');

        try {
            const requestData = {
                acao: "Consultar_Faccionistas",
                dados: {}
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            allOps = response[0]["2- Detalhamento:"]; // Armazena todas as OPs
            currentOffset = 0;
            criarTabelaOps(allOps); // Carrega as OPs

        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
            alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    function criarTabelaOps(listaOps) {

        // Verifica se a DataTable já foi inicializada e a destrói para evitar erros
        if ($.fn.DataTable.isDataTable('#TableOps')) {
            $('#TableOps').DataTable().destroy();
        }

        // Cria a DataTable para "TableOps"
        const tabelaOps = $('#TableOps').DataTable({
            responsive: false,
            paging: true,
            info: false,
            searching: true,
            colReorder: true,
            data: listaOps,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                title: 'Status das Ops',
                className: 'ButtonExcel'
            }, ],
            columns: [{
                    data: 'numeroOP'
                },
                {
                    data: 'codfaccionista'
                },
                {
                    data: 'apelidofaccionista'
                },
                {
                    data: 'codProduto'
                },
                {
                    data: 'nome'
                },
                {
                    data: 'prioridade'
                },
                {
                    data: 'carga'
                },
                {
                    data: 'dataEnvio'
                },
                {
                    data: 'leadtime'
                },
                {
                    data: 'status'
                },
            ],
            pagingType: 'simple',
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<i class="icon ph-bold ph-skip-back"></i>',
                    next: '<i class="icon ph-bold ph-skip-forward"></i>',
                    last: 'Última'
                },
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
            drawCallback: function() {
                const info = this.api().page.info();
                const currentPageInput = `
    <input type="text" id="pageInput" class="form-control" value="${info.page + 1}" min="1" max="${info.pages}" 
    style="width: 50px; text-align: center; margin-left: 3px; margin-right: 3px">
`;
                const message = `Página ${currentPageInput} de ${info.pages}`;

                const paginateContainer = $('.pagination-container-ops');

                if (!$('#itemCountContainer').length) {
                    paginateContainer.before(`
        <div id="itemCountContainer" class="d-flex flex-column flex-md-row justify-content-between align-items-md-end align-items-start w-100 mb-2" style="min-height: 50px;">
            <div class="d-flex flex-row align-items-center mb-2 mb-md-0">
                <input type="number" id="itemCount" class="form-control" min="1" max="99" value="${info.length}" style="width: 60px;">
                <span class="ms-1" style="color: black; width: 200px">Registro(s) por página</span>
            </div>
            <div class="d-flex flex-row align-items-center justify-content-end w-100 w-md-auto mt-2 mt-md-0">
                <span class="pagination-info me-3 d-flex align-items-center justify-content-center" style="color: black;">${message}</span>
            </div>
        </div>
    `);

                    // Move os botões de paginação para o novo layout sem duplicar
                    $('.dataTables_wrapper .dataTables_paginate').appendTo('#itemCountContainer .d-flex.align-items-center.justify-content-end');
                } else {
                    // Apenas atualiza a mensagem
                    $('.pagination-info').html(message);
                }

                // Listener para mudar o número de registros por página
                $('#itemCount').off('change').on('change', function() {
                    const count = parseInt($(this).val(), 10);
                    if (count > 0) {
                        tabelaOps.page.len(count).draw();
                    }
                });

                // Listener para digitar e mudar de página ao pressionar Enter
                $('#pageInput').off('keydown').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const newPage = parseInt($(this).val(), 10) - 1; // Páginas no DataTable são baseadas em zero
                        if (newPage >= 0 && newPage < info.pages) {
                            tabelaOps.page(newPage).draw('page');
                        } else {
                            alert(`Por favor, insira um número de página entre 1 e ${info.pages}.`);
                        }
                    }
                });
            }
        });

        // Filtros de busca
        $('.search-input').on('keyup change', function() {
            const columnIndex = $(this).closest('th').index();
            const searchTerm = $(this).val();
            tabelaOps.column(columnIndex).search(searchTerm).draw();
        });

        // Evitar propagação do clique no input de busca
        $('.search-input').on('click', function(e) {
            e.stopPropagation();
        });

        // Prevenir o envio do formulário ao pressionar Enter dentro do input de busca
        $('.search-input').on('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
</script>