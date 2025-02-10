<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= $BASE_URL ?>/css/Carrinhos1.css">
    <!-- ICONS -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <title>Grupo Mpl</title>
    <style>
        .btn-botoes {
            border: 1px solid gray;
            border-radius: 15px;
            padding: 6px 15px;
            margin-left: 10px;
        }

        .btn-botoes:hover {
            background-color: lightgray;
            border: 1px solid gray;
        }
    </style>
</head>

<body>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <div class="titulo" style="padding: 10px; text-align: left; border-bottom: 1px solid black; color: black; font-size: 15px; font-weight: 600;"><i class="icon ph-bold ph-shopping-cart-simple"></i> Gerenciamento de Carrinhos</div>
    <div class="corpo">
        <div class="botoes d-flex col-12 border-bottom mt-3 mb-3 pb-2 justify-content-start">
            <button class="btn btn-botoes" id="btn-novo-carrinho" onclick="$('#modal-novo-carrinho').modal('show')">
                <i class="icon ph ph-pencil" style="margin-right: 5px;"></i>Novo
            </button>
        </div>
        <div class="corpo-carrinhos d-flex justify-content-md-start justify-content-center" style="padding: 5px 10px; flex-wrap: wrap;">
            <div class="col-12">
                <div class="div-tabela" style="min-width: 100%; max-width: 100%; overflow: auto">
                    <table class="table table-bordered" id="Tablecarrinhos" style="min-width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Numero Carrinho<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                                </th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pagination-container-carrinhos">
            </div>
        </div>
    </div>
    <div class="modal fade modal-custom" id="modal-detalha-carrinho" tabindex="-1" aria-labelledby="customModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customModalLabel" style="color: black;">Conferência de Carrinho<br><label for="" id="numeroOp"></label><br><label for="" id="pcsCarrinho"></label></h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <!-- <div class="Informacoes">
                        <label for="text" id="PecasLidas"></label>
                        <label for="text" id="PecasOps"></label>
                    </div> -->
                    <div class="Tabela">
                        <table id="TabelaGrades"></table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn" id="" style="background-color: #002955; color: white">Selecionar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-custom" id="modal-novo-carrinho" tabindex="-1" aria-labelledby="customModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customModalLabel" style="color: black;">Novo Carrinho</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <div class="input-group">
                        <input type="search" id="numCarrinho" class="form-control" placeholder="Número Carrinho" style="font-size: 0.9rem">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn" id="" style="background-color: #002955; color: white" onclick="CadastrarCarrinho()">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.1/js/dataTables.fixedHeader.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        const ApiCarrinhos = 'http://10.162.0.190:5000/api/consultarCarrinhos?';
        const ApiImprimirCarrinhos = 'http://10.162.0.190:5000/api/imprimirCodCarrinho';
        const ApiCadastrarCarrinhos = 'http://10.162.0.190:5000/api/NovoCarrinho';
        const ApiGrades = 'http://192.168.0.183:8000/pcp/api/DesmembramentoCargaCarrinho?';
        const Token = "a40016aabcx9";

        $(document).ready(() => {
            obterCarrinhos()
        })


        async function obterCarrinhos() {
            try {
                const response = await fetch(`${ApiCarrinhos}empresa=1`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': Token
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log(data);
                    criartabelaCarrinhos(data);
                } else {
                    throw new Error('Erro no retorno');
                }
            } catch (error) {
                console.error(error.message);
            }
        }

        async function obterGrades(carrinho) {
            
            try {
                const response = await fetch(`${ApiGrades}empresa=1&Ncarrinho=${carrinho}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'a44pcp22'
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log(data);
                     $('#numeroOp').text(`OP: ${data[0]['numeroop']}`);
                     $('#pcsCarrinho').text(`Peças no Carrinho: ${data[0]['totalPcCarrinho']}`)
                    criarTabelaGrades(data);
                } else {
                    throw new Error('Erro no retorno');
                }
            } catch (error) {
                console.error(error.message);
            }
        }


        function criartabelaCarrinhos(listaCarrinhos) {
            // Verifica se a DataTable já foi inicializada e a destrói para evitar erros
            if ($.fn.DataTable.isDataTable('#Tablecarrinhos')) {
                $('#Tablecarrinhos').DataTable().destroy();
                $(`#itemCountContainer`).remove();
            }

            // Cria a DataTable para "Tablecarrinhos"
            const tabelaCarrinhos = $('#Tablecarrinhos').DataTable({
                responsive: false,
                paging: true,
                info: false,
                searching: true,
                colReorder: true,
                data: listaCarrinhos,
                lengthChange: false,
                pageLength: 10,
                fixedHeader: true,
                columns: [{
                        data: 'NCarrinho'
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `
                <div class="acoes" style="text-align: center">
                    <button style="border: 1px solid black; border-radius: 7px; padding-left: 7px; padding-right: 7px; font-size: 20px; background-color: white; color: black">
                        <i class="ph-bold ph-printer" title="Imprimir" id="btn-imprimir"
                            onclick="
                                ImprimirCarrinho('${row.NCarrinho}')
                            "
                        ></i>
                    </button> 
                     <button style="border: 1px solid black; border-radius: 7px; padding-left: 7px; padding-right: 7px; font-size: 20px; background-color: white; color: black">
                        <i class="ph-bold ph-eye" title="Ver Carrinho" id="btn-ver-carrinho"
                            onclick="
                                $('#modal-detalha-carrinho').modal('show');
                                obterGrades('${row.NCarrinho}');
                            "
                        ></i>
                    </button> 
                    <button style="border: 1px solid black; border-radius: 7px; padding-left: 7px; padding-right: 7px; font-size: 20px; background-color: white; color: black">
                        <i class="ph-bold ph-trash" title="Excluir Carrinho" id="excluir-carrinho"
                            onclick="

                            "
                        ></i>
                    </button> 
                </div>
            `;
                        }
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

                    const paginateContainer = $('.pagination-container-carrinhos');

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
                            tabelaCarrinhos.page.len(count).draw();
                        }
                    });

                    // Listener para digitar e mudar de página ao pressionar Enter
                    $('#pageInput').off('keydown').on('keydown', function(e) {
                        if (e.key === 'Enter') {
                            const newPage = parseInt($(this).val(), 10) - 1; // Páginas no DataTable são baseadas em zero
                            if (newPage >= 0 && newPage < info.pages) {
                                tabelaCarrinhos.page(newPage).draw('page');
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
                tabelaCarrinhos.column(columnIndex).search(searchTerm).draw();
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
        };

        function criarTabelaOps(listaOps) {

            // Verifica se a DataTable já foi inicializada e a destrói para evitar erros
            if ($.fn.DataTable.isDataTable('#table-consulta-carrinhos')) {
                $('#table-consulta-carrinhos').DataTable().destroy();
            }

            // Cria a DataTable para "table-consulta-carrinhos"
            const tabelaConsultaCarrinhos = $('#table-consulta-carrinhos').DataTable({
                responsive: false,
                paging: true,
                info: false,
                searching: true,
                colReorder: true,
                data: listaOps,
                lengthChange: false,
                pageLength: 10,
                fixedHeader: true,
                columns: [{
                        data: 'NCarrinho'
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `
    <div class="acoes" style="text-align: center">
        <button style="border: 1px solid black; border-radius: 7px; padding-left: 7px; padding-right: 7px; font-size: 20px; background-color: white; color: black">
            <i class="ph-bold ph-printer" title="Imprimir" id="btn-imprimir"
                onclick="
                    ImprimirCarrinho('${row.NCarrinho}')
                "
            ></i>
        </button> 
         <button style="border: 1px solid black; border-radius: 7px; padding-left: 7px; padding-right: 7px; font-size: 20px; background-color: white; color: black">
            <i class="ph-bold ph-eye" title="Ver Carrinho" id="btn-ver-carrinho"
                onclick="
                    
                "
            ></i>
        </button> 
        <button style="border: 1px solid black; border-radius: 7px; padding-left: 7px; padding-right: 7px; font-size: 20px; background-color: white; color: black">
            <i class="ph-bold ph-trash" title="Excluir Carrinho" id="excluir-carrinho"
                onclick="

                "
            ></i>
        </button> 
    </div>
`;
                        }
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

                    const paginateContainer = $('.pagination-container-carrinhos1');

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
                            tabelaConsultaCarrinhos.page.len(count).draw();
                        }
                    });

                    // Listener para digitar e mudar de página ao pressionar Enter
                    $('#pageInput').off('keydown').on('keydown', function(e) {
                        if (e.key === 'Enter') {
                            const newPage = parseInt($(this).val(), 10) - 1; // Páginas no DataTable são baseadas em zero
                            if (newPage >= 0 && newPage < info.pages) {
                                tabelaConsultaCarrinhos.page(newPage).draw('page');
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
                tabelaConsultaCarrinhos.column(columnIndex).search(searchTerm).draw();
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
        };

        async function ImprimirCarrinho(carrinho) {
            console.log(carrinho)
            const Imprimir = {
                "empresa": "1",
                "NCarrinho": carrinho
            };

            try {
                const response = await fetch(ApiImprimirCarrinhos, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': Token
                    },
                    body: JSON.stringify(Imprimir),
                });
                console.log(response)
            } catch (error) {
                console.error(error);
            }
        };

        async function CadastrarCarrinho() {
            console.log($('#numCarrinho').val());

            if ($('#numCarrinho').val() === '') {
                Swal.fire({
                    title: 'Input Não Preenchida',
                    icon: 'warning',
                    showConfirmButton: false,
                    timer: 3000,
                });
                return;
            }

            const Imprimir = {
                "empresa": "1",
                "NCarrinho": $('#numCarrinho').val()
            };

            try {
                const response = await fetch(ApiCadastrarCarrinhos, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': Token
                    },
                    body: JSON.stringify(Imprimir),
                });

                const data = await response.json(); // Converte a resposta para JSON
                console.log(data);

                if (data[0].mensagem === 'Carrinho criado com sucesso') {
                    $('#modal-novo-carrinho').modal('hide');
                    $('#numCarrinho').val('')
                    Swal.fire({
                        title: 'Carrinho Criado',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                    obterCarrinhos()

                } else {
                    $('#modal-novo-carrinho').modal('hide');
                    $('#numCarrinho').val('')
                    Swal.fire({
                        title: 'Carrinho já existe',
                        icon: 'warning', // Corrigido o erro de digitação aqui
                        showConfirmButton: false,
                        timer: 3000,
                    });
                }

            } catch (error) {
                console.error('Erro na requisição:', error);
            }
        }



       function criarTabelaGrades(dados) {
            const tabela = document.getElementById('TabelaGrades');
            tabela.innerHTML = '';

            const cabecalho = tabela.createTHead();
            const linhaCabecalho = cabecalho.insertRow();
            linhaCabecalho.insertCell().textContent = 'Cores';

            // Defina a ordem desejada para os tamanhos
            const ordemTamanhos = ['2', '4', '6', '8', '10', '12', 'PP', 'P', 'M', 'G', 'GG', 'XG', 'XGG', 'G1', 'G2', 'G3', 'UNI'];

            // Crie um Set com os tamanhos presentes nos dados da API
            const tamanhosPresentes = new Set();

            dados.forEach(item => {
                item['tamanhos-PcBipadas/Total'].forEach(tamanhoQuant => {
                    const tamanho = tamanhoQuant.split(':')[0].trim(); // Extrai o tamanho
                    tamanhosPresentes.add(tamanho); // Adiciona o tamanho ao Set
                });
            });

            // Exibe os tamanhos presentes na API, respeitando a ordem de `ordemTamanhos`
            ordemTamanhos.forEach(tamanho => {
                if (tamanhosPresentes.has(tamanho)) {
                    const th = document.createElement('th');
                    th.textContent = tamanho;
                    linhaCabecalho.appendChild(th);
                }
            });

            // Preenche as linhas da tabela com os dados
            dados.forEach(item => {
                const linha = tabela.insertRow();
                const celulaCor = linha.insertCell();
                celulaCor.textContent = item['cor'];

                ordemTamanhos.forEach(tamanho => {
                    if (tamanhosPresentes.has(tamanho)) {
                        const celulaQuantidade = linha.insertCell();
                        const tamanhoQuant = item['tamanhos-PcBipadas/Total'].find(t => t.startsWith(tamanho + ' :')); // Adiciona ": " após o tamanho para precisão

                        if (tamanhoQuant) {
                            const quantidade = tamanhoQuant.split(':')[1].trim();
                            const [atual, total] = quantidade.split('/').map(Number);

                            // Define as cores baseadas no valor da quantidade
                            if (atual === 0) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = 'white';
                            } else if (atual < total) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = '#FA8072';
                            } else if (atual === total) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = '#2E8B57';
                            } else if (atual > total) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = '#FFFF66';
                            }
                        } else {
                            celulaQuantidade.textContent = '-';
                        }
                    }
                });
            });
        }
    </script>
</body>


</html>
