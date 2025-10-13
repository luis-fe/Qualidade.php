let PriorizacaoSelecionado = "";
    let TipoDataSelecionado = "";
    let DadosPedidos = "";
    let DadosOps = "";

    $(document).ready(function() {
        $('#itensPorPagina').change(function() {
            const itensPorPagina = $(this).val();
            $('#TablePedidos').DataTable().page.len(itensPorPagina).draw();
        });

        $('#itensPorPaginaOp').change(function() {
            const itensPorPaginaOp = $(this).val();
            $('#TableOps').DataTable().page.len(itensPorPaginaOp).draw();
        });
        $("#accordion").accordion({
            collapsible: true
        });

        $("#accordion2").accordion({
            collapsible: true
        });

        $("#accordion2").accordion({
            active: false
        });

        $('#NomeRotina').text('Monitor de Pedidos');

        $('input[name="TipoPriorizacao"]').change(function() {
            PriorizacaoSelecionado = $('input[name="TipoPriorizacao"]:checked').val();
            console.log(PriorizacaoSelecionado);
        });

        $('input[name="TipoData"]').change(function() {
            TipoDataSelecionado = $('input[name="TipoData"]:checked').val();
            console.log(TipoDataSelecionado);
        });

        const dataAtual = new Date();
        const dataFormatada = getdataFormatada(dataAtual);
        $('#data-inicio-pedido').val(dataFormatada);
        $('#data-fim-pedido').val(dataFormatada);
        $('#data-emissao-inicial').val(dataFormatada);
        $('#data-emissao-final').val(dataFormatada);
        $('#data-inicio-ops').val(dataFormatada);
        $('#data-fim-ops').val(dataFormatada);

        $('#searchInputPedidos').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            var $container = $('#checkboxContainerPedidos');
            $container.find('.filtro').closest('label').each(function() {
                var $label = $(this);
                var labelText = $label.text().toLowerCase();
                if (labelText.includes(searchText)) {
                    $label.show().prependTo($container);
                } else {
                    $label.hide();
                }
            });
        });

        $('#searchInputMarca').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            var $container = $('#checkboxContainerMarca');
            $container.find('.filtro').closest('label').each(function() {
                var $label = $(this);
                var labelText = $label.text().toLowerCase();
                if (labelText.includes(searchText)) {
                    $label.show().prependTo($container);
                } else {
                    $label.hide();
                }
            });
        });

        $('.dropdown-toggle').on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).next('.dropdown-menu').toggle();
        });

        let today = new Date().toISOString().split('T')[0];
        $('#dataInicio').val(today);
        $('#dataFim').val(today);

    });

    function getdataFormatada(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function formatarDados(data) {
        return data.map(item => {
            return {
                '01-MARCA': item['01-MARCA'],
                '02-Pedido': item['02-Pedido'],
                '03-tipoNota': item['03-tipoNota'],
                '04-PrevOriginal': item['04-Prev.Original'],
                '05-PrevAtualiz': item['05-Prev.Atualiz'],
                '06-codCliente': item['06-codCliente'],
                '08-vlrSaldo': formatarMoeda(item['08-vlrSaldo']),
                '09-Entregas Solic': item['09-Entregas Solic'],
                '10-Entregas Fat': item['10-Entregas Fat'],
                '11-ultimo fat': item['11-ultimo fat'],
                '12-qtdPecas Fat': item['12-qtdPecas Fat'],
                '13-Qtd Atende': item['13-Qtd Atende'],
                '14- Qtd Saldo': item['14- Qtd Saldo'],
                '15-Qtd Atende p/Cor': item['15-Qtd Atende p/Cor'],
                '18-Sugestao(Pedido)': item['18-Sugestao(Pedido)'],
                '21-Qnt Cor(Distrib)': item['21-Qnt Cor(Distrib.)'],
                '22-Valor Atende por Cor(Distrib)': formatarMoeda(item['22-Valor Atende por Cor(Distrib.)']),
                '23-% qtd cor': item['23-% qtd cor'],
                '16-Valor Atende por Cor': formatarMoeda(item['16-Valor Atende por Cor']),
                'Saldo +Sugerido': item['Saldo +Sugerido'],
                'dataEmissao': item['dataEmissao'],
                'Agrupamento': item['Agrupamento'],
            };
        });
    }

    const ConsultaPedidos = async () => {
        try {
            $('#loadingModal').modal('show');
            const iniVenda = $('#data-inicio-pedido').val();
            const finalVenda = $('#data-fim-pedido').val();
            const emissaoinicial = $('#data-emissao-inicial').val();
            const emissaofinal = $('#data-emissao-final').val();
            const tipoNota = '1,2,3,4';
            const parametroClassificacao = TipoDataSelecionado;
            const tipoData = PriorizacaoSelecionado;

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Pedidos',
                    iniVenda: iniVenda,
                    finalVenda: finalVenda,
                    tipoNota: tipoNota,
                    parametroClassificacao: parametroClassificacao,
                    tipoData: tipoData,
                    FiltrodataEmissaoInicial: emissaoinicial,
                    FiltrodataEmissaoFinal: emissaofinal
                }
            });

            console.log(response);
            $('#checkboxContainerPedidos').empty();
            $('#checkboxContainerMarca').empty();
            response[0]['6 -Detalhamento'].forEach(item => {
                $('#checkboxContainerPedidos').append(`<label><input type="checkbox" class="filtro" value="${item["02-Pedido"]}"> ${item["02-Pedido"]}</label>`);
            });
            $('#checkboxContainerMarca').append(`<label><input type="checkbox" class="filtro" value="PACO"> PACO</label>`);
            $('#checkboxContainerMarca').append(`<label><input type="checkbox" class="filtro" value="M.POLLO"> M.POLLO</label>`);

            $("#Filtros").removeClass('d-none');
            $("#ItensPagina").removeClass('d-none');
            $(".table-responsive").removeClass('d-none');
            $("#accordion").accordion({
                active: false
            });

            const DadosFormatados = formatarDados(response[0]['6 -Detalhamento']);
            DadosPedidos = DadosFormatados;
            criarTabelaPedidos(DadosPedidos);
        } catch (error) {
            console.log('Erro:', error);
        } finally {}
    }



    const ConsultaOpsInicio = async () => {
        try {
            const dataInicioPedido = $('#data-inicio-pedido').val(); // Valor da input #data-inicio-pedido

            // Setando o valor da input de tipo data com o mesmo valor da #data-inicio-pedido
            $('#data-inicio-ops').val(dataInicioPedido);

            const dataFimPedido = $('#data-fim-pedido').val(); // Valor da input #data-fim-pedido

            // Setando o valor da input de tipo data com o mesmo valor da #data-fim-pedido
            $('#data-fim-ops').val(dataFimPedido);

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Ops',
                    dataInicio: dataInicioPedido,
                    dataFim: dataFimPedido,
                }
            });

            DadosOps = response[0]['6 -Detalhamento'];
            criarTabelaOps(DadosOps);
        } catch (error) {
            console.error('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const ConsultaOps = async () => {
        try {
            $('#loadingModal').modal('show');
            const dataInicio = $('#data-inicio-ops').val();

            const dataFim = $('#data-fim-ops').val();

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Ops',
                    dataInicio: dataInicio,
                    dataFim: dataFim,
                }
            });

            DadosOps = response[0]['6 -Detalhamento'];
            criarTabelaOps(DadosOps);
            $('#loadingModal').modal('hide');
        } catch (error) {
            console.error('Erro:', error);
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const DetalharOp = async (op) => {
        console.log(op)
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Detalhar_Op',
                    numeroOp: `${op}`,
                }
            });
            console.log(response)
            await CriarTabelaModal(response);

            $('#dataModal').modal('show');
            $('#fixed-header').css({
                'position': 'sticky',
                'top': '0',
                'z-index': '1000'
            });
            $('#dataModalLabel').text(`Número Op: ${op}`)
        } catch (error) {
            console.error('Erro:', error);
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const DetalharPedido = async (Pedido) => {
        console.log(Pedido);
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Detalhar_Pedido',
                    numeroPedido: Pedido,
                }
            });
            console.log(response);
            await CriarTabelaModalPedido(response);

            $('#dataModalPedidos').modal('show');
            $('#fixed-header-pedidos').css({
                'position': 'sticky',
                'top': '0',
                'z-index': '1000'
            });
            $('#dataModalLabelPedidos').html(`Número Pedido: ${Pedido}<br>Qtd Embarques: ${response[0]['02-Embarque']}<br>Cliente: ${response[0]['03-nome_cli']}`);
        } catch (error) {
            console.error('Erro:', error);
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }


    async function consultarDados() {

        await ConsultaPedidos();
        await ConsultaOpsInicio();

    }
    $('#BtnOps').on('click', function() {
        $('#CampoOps').removeClass('d-none');
        $('#campoPedidos').addClass('d-none');
        $('#NomeRotina').text("Monitor de Op's");
    });

    $('#BtnPedidos').on('click', function() {
        $('#CampoOps').addClass('d-none');
        $('#campoPedidos').removeClass('d-none');
    });


    function criarTabelaPedidos(listaPedidos) {
        $('#Paginacao .dataTables_paginate').remove();
        $('#PaginacaoOps .dataTables_paginate').remove();

        listaPedidos.forEach(item => {
            item['Diferenca_Entregas'] = item['09-Entregas Solic'] - item['10-Entregas Fat'];
        });

        if ($.fn.DataTable.isDataTable('#TablePedidos')) {
            $('#TablePedidos').DataTable().destroy();
        }

        const tabela = $('#TablePedidos').DataTable({
            excel: true,
            responsive: false,
            paging: true,
            info: false,
            searching: true,
            colReorder: true,
            data: listaPedidos,
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
                    data: '02-Pedido',
                    render: function(data, type, row) {
                        return `<span class="codPedidoClicado" data-codigoPedido="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
                    }
                },
                {
                    data: '01-MARCA'
                },
                {
                    data: '03-tipoNota'
                },
                {
                    data: '06-codCliente'
                },
                {
                    data: 'dataEmissao'
                },
                {
                    data: '04-PrevOriginal'
                },
                {
                    data: '11-ultimo fat'
                },
                {
                    data: '05-PrevAtualiz'
                },
                {
                    data: '09-Entregas Solic'
                },
                {
                    data: '10-Entregas Fat'
                },
                {
                    data: 'Diferenca_Entregas'
                },
                {
                    data: '12-qtdPecas Fat'
                },
                {
                    data: '08-vlrSaldo'
                },
                {
                    data: '16-Valor Atende por Cor'
                },
                {
                    data: '22-Valor Atende por Cor(Distrib)'
                },
                {
                    data: 'Saldo +Sugerido'
                },
                {
                    data: '15-Qtd Atende p/Cor'
                },
                {
                    data: '21-Qnt Cor(Distrib)'
                },
                {
                    data: '18-Sugestao(Pedido)'
                },
                {
                    data: '23-% qtd cor',
                    render: function(data, type, row) {
                        // Adiciona o símbolo de porcentagem apenas durante a exibição
                        if (type === 'display') {
                            return data + '%'; // Adiciona o símbolo de porcentagem ao valor
                        }
                        return data; // Retorna o valor original para outras operações (por exemplo, ordenação)
                    }
                },
                {
                    data: 'Agrupamento'
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

        //--Criando a paginação da tabela
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

        $('#selectAllMarca').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('#checkboxContainerMarca .filtro:visible').prop('checked', isChecked);
            atualizarFiltroMarca();
        });

        // Evento de mudança no checkbox "Selecionar Todos" de Pedidos
        $('#selectAllPedidos').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('#checkboxContainerPedidos .filtro:visible').prop('checked', isChecked);
            atualizarFiltroPedido();
        });

        $('#checkboxContainerMarca').on('change', '.filtro', function() {
            let todosMarcados = $('#checkboxContainerMarca .filtro:visible').length === $('#checkboxContainerMarca .filtro:visible:checked').length;
            $('#selectAllMarca').prop('checked', todosMarcados);
            atualizarFiltroMarca();
        });

        $('#checkboxContainerPedidos').on('change', '.filtro', function() {
            let todosMarcados = $('#checkboxContainerPedidos .filtro:visible').length === $('#checkboxContainerPedidos .filtro:visible:checked').length;
            $('#selectAllPedidos').prop('checked', todosMarcados);
            atualizarFiltroPedido();
        });

        function atualizarFiltroMarca() {
            var filtros = [];
            $('#checkboxContainerMarca .filtro:checked').each(function() {
                filtros.push($(this).val());
            });
            tabela.column(1).search(filtros.join('|'), true, false).draw();
        }

        function atualizarFiltroPedido() {
            var filtros = [];
            $('#checkboxContainerPedidos .filtro:checked').each(function() {
                filtros.push($(this).val());
            });
            tabela.column(0).search(filtros.join('|'), true, false).draw();
        }
        //-- Pesquisa qualquer palavra na tabela
        $('#searchMonitorPedidos').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        $('#TablePedidos').on('click', '.codPedidoClicado', function() {
            const codPedido = $(this).attr('data-codigoPedido');
            console.log('codPedido:', codPedido); // Ajuste aqui para melhorar a depuração
            DetalharPedido(codPedido);
        });
    }

    function criarTabelaOps(listaOps) {
        console.log(listaOps);

        // Remover elementos de paginação se existirem
        if ($('#Paginacao .dataTables_paginate').length) {
            $('#Paginacao .dataTables_paginate').remove();
        }
        if ($('#PaginacaoOps .dataTables_paginate').length) {
            $('#PaginacaoOps .dataTables_paginate').remove();
        }

        // Destruir DataTable se já estiver inicializada
        if ($.fn.DataTable.isDataTable('#TableOps')) {
            $('#TableOps').DataTable().destroy();
        }

        // Criar DataTable
        const tabela = $('#TableOps').DataTable({
            excel: true,
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
                    data: 'numeroop',
                    render: function(data, type, row) {
                        return `<span class="codOpClicado" data-codigoOp="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
                    }
                },
                {
                    data: 'codItemPai'
                },
                {
                    data: 'descricao'
                },
                {
                    data: 'codFaseAtual'
                },
                {
                    data: 'nome'
                },
                {
                    data: 'Ocorrencia Pedidos'
                },
                {
                    data: 'AtendePçs'
                },
                {
                    data: 'prioridade'
                },
                {
                    data: 'dataPrevisaoTermino'
                },
            ],
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<',
                    next: '>',
                    last: 'Última'
                }
            }
        });

        // Mover elementos de paginação
        $('.dataTables_paginate').appendTo('#PaginacaoOps');

        // Eventos de paginação
        $('#PaginacaoOps .paginate_button.previous').on('click', function() {
            tabela.page('previous').draw('page');
        });

        $('#PaginacaoOps .paginate_button.next').on('click', function() {
            tabela.page('next').draw('page');
        });

        // Selecionar página inicial
        const paginaInicial = 1;
        tabela.page(paginaInicial - 1).draw('page');

        // Atualizar estado dos botões de paginação
        $('#PaginacaoOps .paginate_button').on('click', function() {
            $('#PaginacaoOps .paginate_button').removeClass('current');
            $(this).addClass('current');
        });

        // Pesquisa na tabela
        $('#searchMonitorOp').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        // Evento de clique na linha da tabela
        $('#TableOps').on('click', '.codOpClicado', function() {
            const codOp = $(this).attr('data-codigoOp');
            console.log('codOp:', codOp); // Ajuste aqui para melhorar a depuração
            DetalharOp(codOp);
        });
    }


    async function CriarTabelaModal(dados) {
        // Destruir DataTable se já estiver inicializada
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }

        // Criar DataTable
        const tabela = $('#dataTable').DataTable({
            excel: true,
            responsive: false,
            paging: false,
            info: false,
            searching: false,
            colReorder: true,
            data: dados,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                title: 'Fila de Reposição',
                className: 'ButtonExcel'
            }],
            columns: [{
                    data: 'itemPai'
                },
                {
                    data: '03- Cód Reduzido'
                },
                {
                    data: '01-tipoNota'
                },
                {
                    data: '05-tam'
                },
                {
                    data: '04-cor'
                },
                {
                    data: '06-pcsOP'
                },
                {
                    data: '07-Necessidade'
                },
            ],
        });

    }


    async function CriarTabelaModalPedido(dados) {
    if ($.fn.DataTable.isDataTable('#dataTablePedidos')) {
        $('#dataTablePedidos').DataTable().destroy();
    }

    $('#dataTablePedidos').DataTable({
        excel: true,
        responsive: false,
        paging: false,
        info: false,
        searching: false,
        colReorder: true,
        data: dados,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel"></i>',
            title: 'Fila de Reposição',
            className: 'ButtonExcel'
        }],
        columns: [
            { data: '04-codProduto' },
            { data: '06-codCor' },
            { data: '05-codReduzido' },
            { data: '07-nomeSKU' },
            { data: '08-QtdSaldoPedido' },
            { data: '09-QtdAtendeEstoque' },
            { data: '10-situacao', visible: false},
            { data: '11-numeroop' }
        ],
        createdRow: function(row, data, dataIndex) {
            if (data['10-situacao'] === 'Atendeu') {
                $(row).css('background-color', 'rgb(42, 214, 74)');
            } else if (data['10-situacao'] === 'Nao Atendeu') {
                $(row).css('background-color', 'rgb(228, 87, 87)');
            }
        }
    });
}



    //------------------------------------------CRIAÇAO DOS FILTROS ESPECIAIS---------------------------------------//
    let Clientes = [];

    $('#Cliente').on('keypress', function(event) {
        if (event.which === 13) {

            event.preventDefault();

            let cliente = $(this).val().trim();

            if (cliente !== '') {
                if (Clientes.includes(cliente)) {
                    alert('O código do cliente já foi adicionado.');
                } else {
                    Clientes.push(cliente);
                    $(this).val('');
                    console.log('Clientes:', Clientes);
                }
            }
        }
    });
