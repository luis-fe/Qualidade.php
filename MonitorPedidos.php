<?php
include_once("./templates/cabecalho.php");
?>

<!DOCTYPE html>
<html lang="pt-BR">

    
<head>
    <link rel="stylesheet" href="./css/ContainerWms.css">
    <link rel="stylesheet" href="./css/Modais.css">
    <link rel="stylesheet" href="./css/Distribuicao.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

</head>
<style>
    .modalLoading {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: white;
    }

    .tabela {
        width: 99%;
        min-height: 78%;
        max-height: 78%;
        padding: auto;
        margin: auto;
        overflow: auto;
    }


    .modalLoading-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        text-align: center;
        border-radius: 8px;
    }

    .loader {
        border: 4px solid #f3f3f3;
        border-radius: 50%;
        border-top: 4px solid var(--cor2);
        width: 40px;
        height: 40px;
        animation: spin 2s linear infinite;
        margin: 0 auto 20px auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .Dados {
        margin-top: 30px;
        align-items: center
    }


    input[type="date"] {
        padding: 3.5px;
        border-radius: 10px;
        height: 35px;
        margin-left: 5px;
        margin-right: 5px
    }

    button {
        padding-right: 3.5px;
        padding-left: 3.5px;
        background-color: var(--cor2);
        color: white;
        border-radius: 10px;
        width: 130px;
        cursor: pointer;
        height: 35px;
        max-height: 35px
    }

    .Totais {
        display: flex;
        flex-wrap: wrap;
        /* Permite que os elementos quebrem para a próxima linha */
        margin-top: 10px;
        /* Margem superior para separar os totais dos elementos acima */
        . height: 20%;
        max-height: 20%;
        overflow: auto
    }

    .TotaisMetas,
    .TotaisRealiza {
        width: 50%;
        /* Cada div de total ocupa metade da largura da div Totais */
        text-align: center;
        /* Alinhamento centralizado do texto */
        margin-bottom: 20px;
        /* Margem inferior para separar os totais */
        height: 20%;
        max-height: 20%;
        border-radius: 10px;
        overflow: auto
    }

    .TipoDeData,
    .Priorizacao {
        display: block;
        flex-direction: column;
        padding: 5px;
        justify-Content: center;
        text-align: center;
        align-items: center;
        margin-left: 10px;
    }

    .Opcoes {
        display: flex;

    }

    .Embarques {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 20px;
    }

    .Embarque {
        display: flex;
        justify-Content: center;
        align-items: center;
        text-align: center
    }

    .Embarque label {
        font-weight: bold;
        padding: 8px;
    }

    .Embarque input[type="text"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100px;
        /* Ajuste a largura conforme necessário */
        max-width: 100px;
        /* Largura máxima */
        box-sizing: border-box;
        /* Garante que o padding não altere a largura total */
    }

    /* Estilo para o input em foco */
    .Embarque input[type="text"]:focus {
        outline: none;
        /* Remove a borda ao receber foco */
        border-color: #007bff;
        /* Cor da borda em foco */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        /* Sombra suave */
    }

    .Dados {
        border-bottom: 1px solid black
    }

    .campos {
        display: flex
    }

    .campos select {
        width: 200px
    }
</style>

<body class="<?php echo $classe_empresa; ?>">
    <div class="Container">
        <div class="title">
            <h3>MONITOR DE PEDIDOS</h3>
            <i class="bi bi-download" title="Exportar Excel" id="Exportar" style="color: black"></i>
            <i class="bi bi-funnel-fill" title="Filtros" id="Filtros" style="color: black; display: none"></i>
            <i class="bi bi-gear-fill" title="Fechar" id="Configuracoes" style="color: black"></i>
            <i class="bi bi-x-square-fill" title="Fechar" id="FecharRotina" style="color: black"></i>
        </div>

        <div class="Corpo">
            <div class="Dados" id='dados'>
                <label for="text">Data Inicio</label>
                <input type="date" id="DataInicial">
                <label for="text">Data Fim</label>
                <input type="date" id="DataFinal">
                <div class="TipoDeData">
                    <div class="Opcao">
                        <input type="radio" id="TipoDeData1" name="TipoData" value="DataEmissao">
                        <label for="TipoDeData1">Data Emissão</label>
                    </div>
                    <div class="Opcao">
                        <input type="radio" id="TipoDeData2" name="TipoData" value="DataPrevOri">
                        <label for="TipoDeData2">Data Previsão Original</label>
                    </div>
                </div>
                <div class="Priorizacao">
                    <div class="Opcao">
                        <input type="radio" id="Priorizacao1" name="TipoPriorizacao" value="DataPrevisao">
                        <label for="Priorizacao1">Data Previsão</label>
                    </div>
                    <div class="Opcao">
                        <input type="radio" id="Priorizacao2" name="TipoPriorizacao" value="Faturamento">
                        <label for="Priorizacao2">Faturamento</label>
                    </div>
                </div>
                <button id="btnFiltrar">Filtrar</button>
            </div>
            <div class="tabela" id='tabela'>
                <table id="TabelaPedidos" class="Tabela">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Marca</th>
                            <th>Tipo de Nota</th>
                            <th>Cód. Cliente</th>
                            <th>Data de Emissão</th>
                            <th>Previsão Inicial</th>
                            <th>Último Faturamento</th>
                            <th>Previsão Próximo Embarque</th>
                            <th>Entregas Solicitadas</th>
                            <th>Entregas Faturadas</th>
                            <th>Entregas Restantes</th>
                            <th>Qtd. Peças Faturadas</th>
                            <th>Saldo R$</th>
                            <th>R$ Atendido/COR</th>
                            <th>R$ Atendido Distríbuido</th>
                            <th>Qtd. Peças Saldo</th>
                            <th>Qtd. Peças Atendidas/COR</th>
                            <th>Qtd. Peças Distribuídas</th>
                            <th>Sugestão Pedido</th>
                            <th>% Distribuído</th>
                            <th>Pedidos Agrupados</th>
                        </tr>
                    </thead>
                    <tbody id="tBodyTabela">
                    </tbody>
                </table>
            </div>
            <div class="Totais">
                <div class="TotaisMetas"></div>
                <div class="TotaisRealiza"></div>
            </div>
        </div>
    </div>

    <div class="modalLoading" id="modalLoading">
        <div class="modalLoading-content">
            <div class="loader"></div>
            <p>Aguarde, carregando...</p>
        </div>
    </div>

    <div class="Modal" id="ModalPercentuais">
        <div class="Modal-Content">
            <span class="Close-Modal" onclick="FecharModal('ModalPercentuais')">&times;</span>
            <h2 id="TituloModal">% De Embarques Restantes</h2>
            <form action="" id='FormEdit'>
                <div class='Embarques'>
                    <div class="Embarque">
                        <label for="text">1 Embarque</label>
                        <input type="text" id="Min1Embarque" placeholder='min' required>
                        <input type="text" id="Max1Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">2 Embarque</label>
                        <input type="text" id="Min2Embarque" placeholder='min' required>
                        <input type="text" id="Max2Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">3 Embarque</label>
                        <input type="text" id="Min3Embarque" placeholder='min' required>
                        <input type="text" id="Max3Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">4 Embarque</label>
                        <input type="text" id="Min4Embarque" placeholder='min' required>
                        <input type="text" id="Max4Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">5 Embarque</label>
                        <input type="text" id="Min5Embarque" placeholder='min' required>
                        <input type="text" id="Max5Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">6 Embarque</label>
                        <input type="text" id="Min6Embarque" placeholder='min' required>
                        <input type="text" id="Max6Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">7 Embarque</label>
                        <input type="text" id="Min7Embarque" placeholder='min' required>
                        <input type="text" id="Max7Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">8 Embarque</label>
                        <input type="text" id="Min8Embarque" placeholder='min' required>
                        <input type="text" id="Max8Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">9 Embarque</label>
                        <input type="text" id="Min9Embarque" placeholder='min' required>
                        <input type="text" id="Max9Embarque" placeholder='max' required>
                    </div>
                    <div class="Embarque">
                        <label for="text">10 Embarque</label>
                        <input type="text" id="Min10Embarque" placeholder='min' required>
                        <input type="text" id="Max10Embarque" placeholder='max' required>
                    </div>
                </div>
                <button type='submit'>Salvar</button>
            </form>
        </div>
    </div>

    <!--<div class="Modal" id="ModalFiltroEmbarquesRestantes">
        <div class="Modal-Content">
            <span class="Close-Modal" onclick="FecharModal('ModalPercentuais')">&times;</span>
            <h2 id="TituloModal">Filtro Embarques Restantes</h2>
            <div class="campos">
                <select name="pesquisaEmbarques" id="PesquisaEmbarques">
                    <option value=">">maior que</option>
                    <option value=">=">maior ou igual que</option>
                    <option value="<">menor que</option>
                    <option value="<=">menor ou igual que</option>
                    <option value="=">igual a</option>
                    <option value="!=">diferente de</option>
                </select>
                <input type="text" id="Pesquisa">
            </div>
            <div class="campos">
                <button id='AplicarFiltro'>Aplicar</button>
                <button id="LimparFiltros">Limpar</button>
            </div>
        </div>
    </div>
-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

    <script>
        const ApiDistribuicaoMatriz = 'http://192.168.0.183:8000/pcp/api/monitorPreFaturamento?empresa=1&';
        const ApiConsultaPercentuaisEmbarques = 'http://192.168.0.183:8000/pcp/api/ConsultaConfiguracaoDistribuicao';
        const ApiUpdatePercentuaisEmbarques = 'http://192.168.0.183:8000/pcp/api/UpdateConfiguracaoDistribuicao';
        var empresa = '<?php echo $empresa; ?>';
        var usuario = '<?php echo $usuario; ?>';
        const Token = 'a44pcp22';
        var Embarques = [];
        var VlrMin = [];
        var VlrMax = [];
        var PriorizacaoSelecionado = "";
        var TipoDataSelecionado = "";


        $(document).ready(function () {
            $('input[name="TipoPriorizacao"]').change(function () {
                PriorizacaoSelecionado = $('input[name="TipoPriorizacao"]:checked').val();
            });
            $('input[name="TipoData"]').change(function () {
                TipoDataSelecionado = $('input[name="TipoData"]:checked').val();
            });
        });


        /*$('#filterEntregaRestante').click(function () {
            $('#ModalFiltroEmbarquesRestantes').show();
        });

        $('#AplicarFiltro').click(function () {
            filtrarTabela('9', '>', $('#Pesquisa').val());
        });

        $('#LimparFiltros').click(function () {
            $('#PesquisaEmbarques').val('>');
            $('#Pesquisa').val('');
            limparFiltros();
        });

        function filtrarTabela(Coluna, Operador, Valor) {
            tabela.column(Coluna).search(`^(${Operador === '>' ? '' : '\\'}${Valor})`, true, false).draw();
        }

        function limparFiltros() {
            tabela.columns().search('').draw();
        }
        */
        function getFormattedDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function AbrirModal(Modal) {
            document.getElementById(Modal).style.display = 'block';
        };


        function FecharModal(Modal) {
            document.getElementById(Modal).style.display = 'none';
        }

        function mostrarModalLoading() {
            document.getElementById("modalLoading").style.display = "block";
        }


        function ocultarModalLoading() {
            document.getElementById("modalLoading").style.display = "none";
        }

        function formatarMoeda(valor) {
            return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
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


        async function obterPedidos(API, dataInicial, dataFinal, TiposNota, parametro, tipoData) {
            mostrarModalLoading();
            try {
                const response = await fetch(`${API}iniVenda=${dataInicial}&finalVenda=${dataFinal}&tiponota=${TiposNota}&parametroClassificacao=${parametro}&tipoData=${tipoData}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': Token
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log(data);
                    if (data[0]['0-Status'] === true) {
                        const DadosFormatados = formatarDados(data[0]['6 -Detalhamento']);
                        console.log(DadosFormatados);
                        CriarListaPedidos(DadosFormatados);
                        ocultarModalLoading();
                        document.getElementById('tabela').style.display = 'block';
                    } else {
                       ocultarModalLoading();
                            Swal.fire({
                                title: "Aguarde, Consulta sendo realizada por outro Usuário!",
                                icon: "error",
                             });
                    }
                } else {
                    throw new Error('Erro no retorno');
                    ocultarModalLoading();
                    Swal.fire({
                        title: "Erro",
                        icon: "error",
                    });
                }
            } catch (error) {
                console.error(error.message);
                ocultarModalLoading();
                Swal.fire({
                    title: "Erro",
                    icon: "error",
                });
            }
        }


        async function obterPercentuais(API) {
            mostrarModalLoading();
            try {
                const response = await fetch(`${API}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': Token
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log(data);

                    // Extract only the necessary fields from the data
                    const filteredData = data.map(item => ({
                        ValorMin: item.ValorMin,
                        ValorMax: item.ValorMax
                    }));

                    filteredData.forEach((item, index) => {
                        const minInput = document.getElementById(`Min${index + 1}Embarque`);
                        const maxInput = document.getElementById(`Max${index + 1}Embarque`);

                        if (minInput && maxInput) {
                            minInput.value = item.ValorMin;
                            maxInput.value = item.ValorMax;
                        }
                    });
                    ocultarModalLoading();
                } else {
                    throw new Error('Erro no retorno');
                    ocultarModalLoading();
                }
            } catch (error) {
                console.error(error.message);
                ocultarModalLoading();
            };
        };


        function CriarListaPedidos(ListaPedidos) {
            // Calcular a diferença entre '09-Entregas Solic' e '10-Entregas Fat' para cada item nos dados
            ListaPedidos.forEach(item => {
                item['Diferenca_Entregas'] = item['09-Entregas Solic'] - item['10-Entregas Fat'];
            });

            // Verificar se a tabela já foi inicializada
            if (!$.fn.DataTable.isDataTable('#TabelaPedidos')) {
                // Se não foi inicializada, então inicialize o DataTables
                tabela = $('#TabelaPedidos').DataTable({
                    paging: false,
                    info: false,
                    searching: true,
                    colReorder: true,
                    colResize: true,
                    autoWidth: false,
                    data: ListaPedidos,
                    columns: [
                        { data: '02-Pedido' },
                        { data: '01-MARCA' },
                        { data: '03-tipoNota' },
                        { data: '06-codCliente' },
                        {data: 'dataEmissao' },
                        { data: '04-PrevOriginal' },
                        { data: '11-ultimo fat' },
                        { data: '05-PrevAtualiz' },
                        { data: '09-Entregas Solic' },
                        { data: '10-Entregas Fat' },
                        { data: 'Diferenca_Entregas' }, // Nova coluna para exibir a diferença calculada
                        { data: '12-qtdPecas Fat' },
                        { data: '08-vlrSaldo' },
                        { data: '16-Valor Atende por Cor' },
                        { data: '22-Valor Atende por Cor(Distrib)' },
                        { data: 'Saldo +Sugerido' },
                        { data: '15-Qtd Atende p/Cor' },
                        { data: '21-Qnt Cor(Distrib)' },
                        { data: '18-Sugestao(Pedido)' },
                        {
                            data: '23-% qtd cor',
                            render: function (data, type, row) {
                                // Adiciona o símbolo de porcentagem apenas durante a exibição
                                if (type === 'display') {
                                    return data + '%'; // Adiciona o símbolo de porcentagem ao valor
                                }
                                return data; // Retorna o valor original para outras operações (por exemplo, ordenação)
                            }
                        },
                        { data: 'Agrupamento' },
                    ],
                    language: {
                        paginate: {
                            first: 'Primeira',
                            previous: 'Anterior',
                            next: 'Próxima',
                            last: 'Última',
                        },
                    },
                });
            }


            function calcularTotal() {
                var totalSaldoR$ = tabela.column(12, { search: 'applied' }).data().reduce(function (acc, curr) {
                    return acc + parseFloat(curr.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
                }, 0);

                var totalSaldoPcs = tabela.column(15, { search: 'applied' }).data().reduce(function (acc, curr) {
                    return acc + parseFloat(curr);
                }, 0);

                var totalR$AtendidoCor = tabela.column(14, { search: 'applied' }).data().reduce(function (acc, curr) {
                    return acc + parseFloat(curr.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
                }, 0);

                var totalSaldoPcsAtendidoCor = tabela.column(17, { search: 'applied' }).data().reduce(function (acc, curr) {
                    return acc + parseFloat(curr)
                }, 0);

                // Exibir os totais na div de Totais
                $('.TotaisMetas').html('<p>Saldo em R$: ' + totalSaldoR$.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</p>' +
                    '<p>Saldo em Peças: ' + totalSaldoPcs.toLocaleString('pt-BR') + '</p>');

                // Exibir os totais na div de Totais
                $('.TotaisRealiza').html('<p>R$ Atendido/Cor: ' + totalR$AtendidoCor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</p>' +
                    '<p>Peças Atendidas: ' + totalSaldoPcsAtendidoCor.toLocaleString('pt-BR') + '</p>');
            }


            // Chamar a função de cálculo ao inicializar e ao filtrar a tabela
            tabela.on('draw', function () {
                calcularTotal();
            });

            // Inicializar o cálculo do total
            calcularTotal();
        }



        window.addEventListener('load', async () => {
            const currentDate = new Date();
            const formattedDate = getFormattedDate(currentDate);
            const DataInicial = document.getElementById("DataInicial");
            const DataFinal = document.getElementById("DataFinal")
            DataInicial.value = formattedDate;
            DataFinal.value = formattedDate;
            document.getElementById('tabela').style.display = 'none'
        });

        document.getElementById('btnFiltrar').addEventListener('click', async () => {
            const dataInicial = document.getElementById("DataInicial");
            const dataFinal = document.getElementById("DataFinal");

            if (TipoDataSelecionado === '') {
                Swal.fire({
                    title: "Selecione qual o tipo de data desejado!",
                    icon: "error",
                });
            } else if (PriorizacaoSelecionado === '') {
                Swal.fire({
                    title: "Selecione qual a Priorização desejada!",
                    icon: "error",
                });
            } else {
                await obterPedidos(ApiDistribuicaoMatriz, dataInicial.value, dataFinal.value, "1,2,3,4", PriorizacaoSelecionado, TipoDataSelecionado);
                await obterPercentuais(ApiConsultaPercentuaisEmbarques);
            }
        });


        function exportarParaExcel() {
            try {
                // Selecionar a tabela e obter os dados
                const tabela = document.getElementById('TabelaPedidos');

                // Verificar se a tabela existe
                if (!tabela) {
                    throw new Error('A tabela não foi encontrada.');
                }

                // Criar um objeto Excel
                const wb = XLSX.utils.table_to_book(tabela, { sheet: "Sheet JS" });

                // Salvar o arquivo Excel
                XLSX.writeFile(wb, 'dados_pedidos.xlsx');
            } catch (error) {
                console.error('Erro ao exportar para Excel:', error.message);
                // Exibir mensagem de erro ao usuário
                alert('Ocorreu um erro ao exportar para Excel. Por favor, tente novamente.');
            }
        }


        $('#Configuracoes').click(async function () {
            AbrirModal('ModalPercentuais');
        });


        $('#Exportar').click(async function () {
            exportarParaExcel();
        });

        async function EditPercentuais(Api) {
            Embarques = ['1', '2', '3', '4', '5', '6'];
            VlrMin = [$('#Min1Embarque').val(), $('#Min2Embarque').val(), $('#Min3Embarque').val(), $('#Min4Embarque').val(), $('#Min5Embarque').val(), $('#Min6Embarque').val()];
            VlrMax = [$('#Max1Embarque').val(), $('#Max2Embarque').val(), $('#Max3Embarque').val(), $('#Max4Embarque').val(), $('#Max5Embarque').val(), $('#Max6Embarque').val()];

            dados = {
                "arrayEmbarque": Embarques,
                "arrayMIN": VlrMin,
                "arrayMAX": VlrMax,
            };

            console.log(dados)

            try {
                const response = await fetch(`${Api}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': Token
                    },
                    body: JSON.stringify(dados),
                });

                if (response.ok) {
                    const data = await response.json();
                    FecharModal('ModalPercentuais');
                    Swal.fire({
                        title: "% Alterados",
                        icon: "success",
                        showConfirmButton: false,
                        timer: "3000",
                    });
                } else {
                    throw new Error('Erro ao obter os dados da API');
                }
            } catch (error) {
                console.error(error);
                FecharModal('ModalPercentuais');
                Swal.fire({
                    title: "Erro",
                    icon: "error",
                    showConfirmButton: false,
                    timer: "3000",
                });
            }
        };

        $('#FormEdit').submit(async function (event) {
            event.preventDefault();

            await EditPercentuais(ApiUpdatePercentuaisEmbarques);
        });


    </script>
</body>

</html>
