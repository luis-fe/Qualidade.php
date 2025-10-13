<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>
<link rel="stylesheet" href="style.css">
<style>
    #form-container {
        min-width: 100%;
        width: 100%;
        height: calc(100vh - 50px);
        padding: 20px;
        overflow-y: auto;
        background-color: gray;
    }

    .Corpo {
        width: 100%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        overflow: auto;
        background-color: var(--branco);
        padding: 20px;
        min-height: calc(100% - 50px);
        max-height: calc(100% - 50px);
    }

    #Infos {
        display: flex;
        height: 5vh;
        margin-top: 40px;
        justify-content: left;
        align-items: center;
        text-align: right;
    }

    #itensPorPagina {
        max-width: 100px;
        margin-left: 5px;
        margin-right: 5px;
        margin-top: -10px;
    }

    .table-container {
        margin-top: 20px;
        position: relative;
    }

    .fixed-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: white;
        /* Altere a cor de fundo conforme necessário */
    }

    .table-responsive {
        min-height: 75vh;
        max-height: 75vh;
        overflow: auto;
    }

    .table {
        padding: auto;
        margin: auto;
        width: 100%;
        min-width: 100%;
        max-width: 100%;
        min-height: 100%;
        max-height: 100%;
        overflow: auto;
    }

    .table th,
    .table td {
        white-space: nowrap;
    }

    .table tbody tr:hover {
        background-color: var(--corFundoTabela);
    }

    .table th {
        background-color: var(--corBase);
        color: var(--branco);
        text-align: center;
    }

    #Paginacao {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
        min-width: 100%;
        height: auto;
        overflow-x: auto;
        padding: 10px 0;
        flex-wrap: wrap;
        /* Adiciona wrap para melhor responsividade */
    }

    #Paginacao .paginate_button {
        margin: 3px;
        padding: 3px 6px;
        color: var(--corBase);
        border: 1px solid var(--corBase);
        border-radius: 4px;
        cursor: pointer;
        background-color: var(--branco);
    }

    #Paginacao .paginate_button:hover {
        background-color: var(--corBase);
        color: var(--branco);
    }

    #Paginacao .paginate_button.current {
        background-color: var(--corBase);
        color: var(--branco);
    }

    .acoes {
        display: flex;
        justify-content: space-around;
        align-items: center;
        height: 100%;
    }

    .acoes i {
        cursor: pointer;
        font-size: 20px;
        margin: 0 0;
    }

    .dataTables_wrapper .dataTables_filter {
        display: none;
    }

    .ButtonExcel i {
        color: green;
        font-size: 25px;
    }

    .ButtonVisibilidade {
        border: none !important;
    }

    @media (max-width: 768px) {
        td.descricao {
            white-space: normal;
            word-break: break-word;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .table {
            padding: auto;
            margin: auto;
            width: 100%;
            min-width: 100%;
            max-width: 100%;
            min-height: 100%;
            max-height: 100%;
            overflow: auto;
        }

        #form-container,
        .Corpo {
            padding: 10px;
        }

        #Paginacao {
            flex-direction: column;
            /* Direção da coluna para melhor ajuste em telas pequenas */
        }

        #Paginacao .paginate_button {
            margin: 5px 0;
        }
    }
</style>

<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row">
            <div class="col-12 col-md-3 mb-3">
                <div id="search-container">
                    <input type="text" id="searchFila" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div id="select-container">
                    <select class="form-control" id="Usuarios">
                        <option disabled selected>Selecione um usuário para Atribuição!</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-2 mb-3">
                <button class="btn btn-primary w-100" id="PecasFaltantes">Peças Faltantes</button>
            </div>
            <div class="col-12 col-md-2 mb-3">
                <button class="btn btn-primary w-100" id="PriorizarPedidos">Priorizar Pedido</button>
            </div>
            <div class="col-12 col-md-2 mb-3">
                <button class="btn btn-primary w-100" id="AtualiarPedidos" onclick="RecarregarPedidos()">Atualizar Pedidos</button>
            </div>
            <div class="col-12 col-md-2 mb-3">
                <button class="btn btn-primary w-100" id="LimparSeparacao" onclick="">Limpar Separação</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="TablePedidos">
                <thead class="fixed-header">
                    <tr>
                        <th><input type="checkbox" id="selectAllCheckbox"></th>
                        <th scope="col">Pedido</th>
                        <th scope="col">Usuário<br>Atribuído</br></th>
                        <th scope="col">Tipo<br>Nota</br></th>
                        <th scope="col">Data<br>Sugestão</br></th>
                        <th scope="col">Quant.<br>Peças</br></th>
                        <th scope="col">%<br>Reposto</br></th>
                        <th scope="col">%<br>Separado</br></th>
                        <th scope="col">Priorid.</th>
                        <th scope="col">Valor<br>R$</br></th>
                        <th scope="col">Pedidos Agrupados</th>
                        <th scope="col">UF</th>
                        <th scope="col">Situação<br>Pedido</br></th>
                        <th scope="col">Marca</th>
                        <th scope="col">obs</th>
                        <th scope="col">Transportadora</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="ModalFaltantes" tabindex="-1" role="dialog" aria-labelledby="ModalFaltantesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalFaltantesLabel">Peças Faltantes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="ListaPeçasFaltantes" class="list-group"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>

<script>
    const PedidosSelecionados = [];
    $(document).ready(async () => {
        $('#NomeRotina').text('Distribuição de Pedidos');
        await ConsultarPedidos();
        ConsultarUsuario();



        $('#ModalFaltantes .close').click(() => {
            $('#ModalFaltantes').modal('hide');
        });
    });

    function getCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    const RecarregarPedidos = () => {
        $('#loadingModal').modal('show');
        $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Recarregar_Pedidos'
            },
            success: (data) => {
                $('#loadingModal').modal('hide');
            },
        });
    }

    const ConsultarPedidos = () => {
        $('#loadingModal').modal('show');
        $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Pedidos'
            },
            success: (data) => {
                const TipoNotaFiltrado = data.filter(item => item["03-TipoNota"] !== "39 - BN MPLUS");

                TipoNotaFiltrado.forEach(item => {
                    item["18-%Reposto"] = parseFloat(item["18-%Reposto"]);
                    item["18-%Reposto"] = (item["18-%Reposto"] * 1).toFixed(2);
                    item["18-%Reposto"] += "%";
                    item["20-Separado%"] = parseFloat(item["20-Separado%"]);
                    item["20-Separado%"] = (item["20-Separado%"] * 1).toFixed(2);
                    item["20-Separado%"] += "%";
                    item["14-AgrupamentosPedido"] = item["14-AgrupamentosPedido"].replaceAll('/', ',');
                });
                criarTabelaPedidos(TipoNotaFiltrado);
                PintarPedidosCompletos();
                marcarLinhasDuplicadas();
                PintarPedidosUrgentes();
                $('#loadingModal').modal('hide');
            },
        });
    }

    const ConsultarUsuario = () => {
        $('#loadingModal').modal('show');
        $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Usuarios'
            },
            success: (data) => {
                const UsuariosAtivos = data.filter(item => item["situacao"] !== "INATIVO");
                const usuariosSelect = $('#Usuarios');
                usuariosSelect.empty();
                usuariosSelect.append('<option disabled selected>Selecione um usuário para Atribuição!</option>');
                UsuariosAtivos.forEach(usuario => {
                    usuariosSelect.append(`<option value="${usuario.codigo}">${usuario.nome}</option>`);
                });
                $('#loadingModal').modal('hide');
            },
            error: (xhr, status, error) => {
                console.error('Erro na solicitação:', status, error);
                console.error('Resposta completa:', xhr.responseText);
                $('#loadingModal').modal('hide');
            }
        });
    }

    function criarTabelaPedidos(ListaPedidos) {
        $('#Paginacao .dataTables_paginate').remove();

        if ($.fn.DataTable.isDataTable('#TablePedidos')) {
            $('#TablePedidos').DataTable().destroy();
        }

        const tabela = $('#TablePedidos').DataTable({
            excel: true,
            responsive: false,
            paging: false,
            info: false,
            searching: true,
            colReorder: true,
            data: ListaPedidos,
            lengthChange: false,
            pageLength: 10,
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
                    data: null,
                    render: function(data, type, row, meta) {
                        return '<input type="checkbox" class="rowCheckbox" id="checkbox_' + meta.row + '">';
                    }
                },
                {
                    data: '01-CodPedido'
                },
                {
                    data: '11-NomeUsuarioAtribuido'
                },
                {
                    data: '03-TipoNota'
                },
                {
                    data: '02- Data Sugestao'
                },
                {
                    data: '15-qtdesugerida'
                },
                {
                    data: '18-%Reposto'
                },
                {
                    data: '20-Separado%'
                },
                                {
                    data: 'prioridade'
                },
                {
                    data: '12-vlrsugestao'
                },
                {
                    data: '14-AgrupamentosPedido'
                },
                {
                    data: '07-estado'
                },
                {
                    data: '22- situacaopedido'
                },
                {
                    data: '21-MARCA'
                },

                {
                    data: 'obs'
                },
                {
                    data: '23-transportadora'
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
        //-- Pesquisa qualquer palavra na tabela
        $('#searchFila').on('keyup', function() {
            tabela.search(this.value).draw();
        });

         async function VerificarPedidosSelecionados() {
            // Limpar a array de PedidosSelecionados
            PedidosSelecionados.length = 0;

            tabela.rows().every(function(rowIdx) {
                const checkbox = $(this.node()).find('.rowCheckbox');
                if (checkbox.is(':checked')) {
                    const row = this.data();
                    const codigosPedidos = row['14-AgrupamentosPedido'].split(',');

                    codigosPedidos.forEach(codigoPedido => {
                        codigoPedido = codigoPedido.trim(); // Remover espaços em branco
                        // Verificar se o código do pedido já existe na array PedidosSelecionados
                        if (!PedidosSelecionados.includes(codigoPedido)) {
                            // Se não existir, adicione à array PedidosSelecionados
                            PedidosSelecionados.push(codigoPedido);
                        }
                    });
                }
            });

            if (PedidosSelecionados.length === 0) {
                Swal.fire({
                    title: "Nenhum Pedido Selecionado",
                    icon: "warning",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        }


        $('#Usuarios').off('change').change(async function() {
            $('#loadingModal').modal('show');
            const selectedValue = $(this).val();
            await VerificarPedidosSelecionados();
            const currentDateTime = getCurrentDateTime();
            const Dados = {
                codUsuario: selectedValue,
                pedidos: PedidosSelecionados,
                data: currentDateTime
            };
            $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify({
                    acao: 'Atribuir_Pedidos',
                    dados: Dados
                }),
                success: async function(response) {
                    Swal.fire({
                            title: "Pedidos Atribuídos",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 3000,
                        });
                    $('#loadingModal').modal('hide');
                    await ConsultarPedidos();
                    ConsultarUsuario();
                },
                error: function(xhr, status, error) {
                    console.error('Erro na solicitação:', status, error);
                    console.error('Resposta completa:', xhr.responseText);
                    $('#loadingModal').modal('show');
                }
            });
            
        });

        $('#PecasFaltantes').off('click').click(async () => {
            $('#loadingModal').modal('show');
            await VerificarPedidosSelecionados();
            $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Pecas_Faltantes',
                    CodPedido: PedidosSelecionados
                },
                success: (data) => {
                    $('#ModalFaltantesLabel').text(`Peças Faltantes: ${PedidosSelecionados}`);
                    $('#ListaPeçasFaltantes').empty();

                    // Iterar sobre cada pedido retornado na resposta
                    data.forEach(pedido => {
                        const detalhamentoPedido = pedido[0]['5- Detalhamento dos Sku:'];
                        const filtro = detalhamentoPedido.filter(item => item["endereco"] === "Não Reposto");

                        // Adicionar os itens filtrados ao modal
                        filtro.forEach(item => {
                            const listItem = $('<li>').text(`${item["referencia"]} / ${item["tamanho"]} / ${item["cor"]} - ${item["reduzido"]}`);
                            $('#ListaPeçasFaltantes').append(listItem);
                        });
                    });

                    // Mostrar o modal
                    $('#ModalFaltantes').modal('show');
                    $('#loadingModal').modal('hide');
                },
                error: (xhr, status, error) => {
                    console.error('Erro na solicitação:', status, error);
                    console.error('Resposta completa:', xhr.responseText);
                    $('#loadingModal').modal('hide');
                }
            });
        });

         $('#LimparSeparacao').off('click').click(async () => {
        $('#loadingModal').modal('show');
        await VerificarPedidosSelecionados();
        if (PedidosSelecionados.length === 0) {
            Swal.fire({
                title: "Nenhum Pedido Selecionado",
                icon: "warning",
                showConfirmButton: false,
                timer: 3000,
            });
        } else {
            const Dados = {
                pedidos: PedidosSelecionados,
            };
            $.ajax({
                type: 'PUT',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify({
                    acao: 'Limpar_Separacao',
                    dados: Dados
                }),
                success: async function (response) {
                    $('#loadingModal').modal('hide');
                    if(response.status === true){
                        Swal.fire({
                            title: "Pedidos Limpados",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 3000,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro na solicitação:', status, error);
                    console.error('Resposta completa:', xhr.responseText);
                    $('#loadingModal').modal('show');
                }
            });
        }
    });

        $('#PriorizarPedidos').off('click').click(async () => {
    await VerificarPedidosSelecionados();
    const Dados = {
        pedidos: PedidosSelecionados
    };
    $.ajax({
        type: 'PUT',
        url: 'requests.php',
        contentType: 'application/json',
        data: JSON.stringify({
            acao: 'Alterar_Prioridade',
            dados: Dados
        }),
        success: async function(response) {
            if(response['status'] == true){
                Swal.fire({
                    title: "Priorização Alterada",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
            await ConsultarPedidos();
            ConsultarUsuario();
        },
        error: function(xhr, status, error) {
            console.error('Erro na solicitação:', status, error);
            console.error('Resposta completa:', xhr.responseText);
        }
    });
});


    }

    function PintarPedidosCompletos() {
        const colunaDesejada = 6;

        $('#TablePedidos tr').each(function(index) {
            if (index > 0) {
                const colunaValor = $(this).find(`td:eq(${colunaDesejada})`);
                const valor = colunaValor.text().trim();

                colunaValor.css('background-color', valor === "100.00%" ? 'lightgreen' : '');
            }
        });
    }

    function PintarPedidosUrgentes() {
        const colunaDesejada = 8;

        $('#TablePedidos tr').each(function(index) {
            if (index > 0) {
                const colunaValor = $(this).find(`td:eq(${colunaDesejada})`);
                const valor = colunaValor.text().trim();

                colunaValor.css('background-color', valor === "URGENTE" ? 'red' : '');
            }
        });
    }

   function marcarLinhasDuplicadas() {
        const valoresContados = {};
        const colunaDesejada = 10;

        // Primeira passagem para contar os valores
        $('#TablePedidos tr').each(function(index) {
            if (index > 0) { // Ignorar o cabeçalho
                const colunaValor = $(this).find(`td:eq(${colunaDesejada})`);
                const valor = colunaValor.text().trim();

                valoresContados[valor] = (valoresContados[valor] || 0) + 1;
            }
        });

        // Segunda passagem para marcar as linhas
        $('#TablePedidos tr').each(function(index) {
            if (index > 0) { // Ignorar o cabeçalho
                const colunaValor = $(this).find(`td:eq(${colunaDesejada})`);
                const valor = colunaValor.text().trim();
                const texto = colunaValor.text().toLowerCase();
                const linhaContemMkt = texto.includes('mkt');

                if (linhaContemMkt) {
                    $(this).css('background-color', '#9370DB');
                } else if (valoresContados[valor] > 1 && linhaContemMkt) {
                    $(this).css('background-color', '#9370DB');
                } else if (valoresContados[valor] > 1) {
                    $(this).css('background-color', 'yellow');
                } else {
                    $(this).css('background-color', '');
                }
            }
        });
    }
</script>
