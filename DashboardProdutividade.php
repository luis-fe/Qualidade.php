<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/TelaInicial.css">
    <link rel="stylesheet" href="./css/Dashboards.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    
</head>
<body>
<div class="container">
    <div class="title">
        <h3>DASHBOARD PRODUTIVIDADE</h3>
        <i class="bi bi-x-square-fill" title="Fechar" id="FecharRotina" style="color: black"></i>
    </div>
    <div class="Corpo">
        <div class="ranking-container" id="ranking-container">
            <h1>Ranking de Produtividade</h1>
            <div class="date-range">
                <label for="data-inicio">Data de Início:</label>
                <input type="date" id="data-inicio" name="data-inicio">
                <label for="data-fim">Data Fim:</label>
                <input type="date" id="data-fim" name="data-fim">
                <button id="filtrarLinhas">Filtrar</button>
                <i id="IconeProdução" class="bi bi-cash-coin" style="margin-left: 20px; font-size: 30px; margin-top: 15px; cursor: pointer" title="Produção R$"></i>
                <i id="IconeConfig" class="bi bi-gear" style="margin-left: 20px; font-size: 30px; margin-top: 15px; cursor: pointer" title="Lista Op's"></i>
            </div>
            <div id="ranking-table">
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Operador</th>
                            <th>Quantidade Peças</th>
                            <th>Quantidade Op's Produzidas</th>
                            <th>Média Peças</th>
                        </tr>
                    </thead>
                    <tbody id="ranking-body">
                        <!-- As linhas da tabela serão inseridas aqui via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Movendo a tabela de produção para dentro da div Corpo -->
        <div class="Producao" id="Producao">
            <h1>Produção R$</h1>
            <div class="date-range">
                <label for="data-inicio">Data de Início:</label>
                <input type="date" id="data-inicioProducao" name="data-inicio">
                <label for="data-fim">Data Fim:</label>
                <input type="date" id="data-fimProducao" name="data-fim">
                <button id="filtrar">Filtrar</button>
                <i id="IconeLinhas" class="bi bi-boxes" style="margin-left: 20px; font-size: 30px; margin-top: 15px; cursor: pointer" title="Produtividade Linhas"></i>
                <i id="IconeConfig1" class="bi bi-gear" style="margin-left: 20px; font-size: 30px; margin-top: 15px; cursor: pointer" title="Lista Op's"></i>
            </div>
            <div id="Producao-table">
                <table class="Producao-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Operador</th>
                            <th>Quantidade Peças</th>
                            <th>Valor Produtividade R$</th>
                        </tr>
                    </thead>
                    <tbody id="Producao-body">
                        <!-- As linhas da tabela serão inseridas aqui via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Código da Tabela de Op's-->
        <div class="TelaOps" id="TelaOps">
            <h1>Lista de Op's Finalizadas</h1>
            <div class="date-range">
                <label for="data-inicio">Data de Início:</label>
                <input type="date" id="data-inicioOps" name="data-inicio">
                <label for="data-fim">Data Fim:</label>
                <input type="date" id="data-fimOps" name="data-fim">
                <button id="filtrarOps">Filtrar</button>
                <i id="IconeLinhas1" class="bi bi-boxes" style="margin-left: 20px; font-size: 30px; margin-top: 15px; cursor: pointer" title="Produtividade Linhas"></i>
                <i id="IconeProdução1" class="bi bi-cash-coin" style="margin-left: 20px; font-size: 30px; margin-top: 15px; cursor: pointer" title="Produção R$"></i>
            </div>
            <div id="Ops-table">
                <table class="Ops-table" id="Ops-table1">
                    <thead>
                        <tr>
                            <th>Numero Op</th>
                            <th>Linha</th>
                            <th>Operador 1</th>
                            <th>Operador 2</th>
                            <th>Operador 3</th>
                            <th>Qtd Produzida</th>
                            <th>Editar</th>
                            <th>Excluir</th>
                        </tr>
                    </thead>
                    <tbody id="Ops-body">
                        <!-- As linhas da tabela serão inseridas aqui via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modalEditar" class="modals">
    <div class="modals-content">
        <span class="close" onclick="fecharModalEditar()">&times;</span>
        <h2>Editar Op</h2>
        <form id="formEditar">
            <label for="text">Numero Op:</label>
            <input type="text" id="InputNumeroOp" name="InputNumeroOp" required readonly>
            <label for="text">Linha Atual:</label>
            <input type="text" id="InputLinhaAtual" name="InputLinhaAtual" required readonly>
            <label for="text">Nova Linha:</label>
            <input type="text" id="InputNovaLinha" name="InputNovaLinha">
            <label for="text">Quantidade:</label>
            <input type="text" id="InputQuantidade" name="InputQuantidade" required>
            <button type="submit">Salvar</button>
        </form>
    </div>
</div>

<div id="modalSuccess" class="modal">
    <div class="modal-contentSucess">
        <p id="MessagemDeSucesso"></p>
        <i class="bi bi-check-circle-fill"></i>
    </div>
</div>
<div id="modalError" class="modal">
    <div class="modal-contentError">
        <p>Erro</p>
        <i class="bi bi-x-circle-fill"></i>
    </div>
</div>
</body>
<script>
    //----------------- Script para fechar a rotina---------------//
    const btnFecharRotina = document.getElementById("FecharRotina");
    const InputNumeroOp = document.getElementById('InputNumeroOp');
    const InputLinhaAtual = document.getElementById('InputLinhaAtual');
    const InuptQuantidade = document.getElementById('InputQuantidade');
    const InputNovaLinha = document.getElementById('InputNovaLinha');

    function fecharModal(element) {
    element.style.display = 'none';
}

    function openModal(Modal) {
    Modal.style.display = 'flex';
    setTimeout(() => {
        fecharModal(Modal);
    }, 3000);
}

    function fecharRotina() {
        window.location.replace("TelaInicial.php");
    }

    btnFecharRotina.addEventListener('click', () => {
        fecharRotina();
    });

    function abrirModalEditar(ValorOp, ValorLinha, ValorQtd) {
    document.getElementById('modalEditar').style.display = "block";
    InputNumeroOp.value = ValorOp;
    InputLinhaAtual.value = ValorLinha;
    InputQuantidade.value = ValorQtd;
    
};

function fecharModalEditar() {
    document.getElementById('modalEditar').style.display = "none";
    document.getElementById('InputNovaLinha').value = "";
}


    const ApiLinhas = 'http://10.162.0.190:5000/api/ProdutividadeGarantiaEquipe?';
    const ApiProducao = 'http://10.162.0.190:5000/api/ProdutividadeGarantiaIndividual?';
    const ApiListaOps = 'http://10.162.0.190:5000/api/OpsProduzidasGarantia?';
    const ApiAtualizarDado = 'http://10.162.0.190:5000/api/AlterarOPsProduzidasGarantia';
    const ApiDeletarUsuario = 'http://10.162.0.190:5000/api/DeletarOPsProduzidasGarantia';
    const Token = "a40016aabcx9";

    document.addEventListener("DOMContentLoaded", function () {
        
        const dataAtual = new Date();
        const formatarData = (data) => {
            const ano = data.getFullYear();
            const mes = String(data.getMonth() + 1).padStart(2, '0');
            const dia = String(data.getDate()).padStart(2, '0');
            return `${ano}-${mes}-${dia}`;
        };

        document.getElementById("data-inicio").value = formatarData(dataAtual);
        document.getElementById("data-fim").value = formatarData(dataAtual);
        document.getElementById("data-inicioProducao").value = formatarData(dataAtual);
        document.getElementById("data-fimProducao").value = formatarData(dataAtual);
        document.getElementById("data-inicioOps").value = formatarData(dataAtual);
        document.getElementById("data-fimOps").value = formatarData(dataAtual);

        preencherTabela(ApiLinhas);
    });

//-----------------------Função para preenher a tabela de Produtividade por Linha-----------------------//
        function preencherTabela(Api) {
            const rankingBody = document.getElementById("ranking-body");
            const dataInicio = document.getElementById("data-inicio").value;
            const dataFim = document.getElementById("data-fim").value;
            const apiUrl = `${Api}DataInicial=${dataInicio}&DataFinal=${dataFim}`;
            const requestOptions = {
                method: "GET",
                headers: {
                    "Authorization": Token,
                    "Content-Type": "application/json"
                }
            };
            fetch(apiUrl, requestOptions)
                .then(response => response.json())
                .then(data => {
                    rankingBody.innerHTML = "";
                    let totalPecas = 0; // Inicializa o total de peças
                    let totalOps = 0;
                    let MediaPecas = 0;
                    let posicao = 0; // Inicializa a posição no ranking
                    let numOps = 0; // Inicializa o número total de operações

                    data.forEach((item, index) => {
                        item["2.0- Detalhamento"].forEach(detalhe => {
                            totalPecas += detalhe.qtd; // Adiciona a quantidade de cada detalhe ao total
                            totalOps += detalhe['qtd OP'];
                            MediaPecas += detalhe['Media Pçs/OP'];
                            posicao++; // Incrementa a posição no ranking
                            const tr = document.createElement("tr");
                            tr.innerHTML = `
                                <td>${posicao}º</td>
                                <td>${detalhe.operador1} / ${detalhe.operador2} / ${detalhe.operador3}</td>
                                <td>${detalhe.qtd}</td>
                                <td>${detalhe['qtd OP']}</td>
                                <td>${detalhe['Media']}</td>
                            `;
                            console.log(detalhe['Media Pçs/OP']);
                            rankingBody.appendChild(tr);
                        });
                    });

                    // Calcula a média geral de peças por OP
                    const mediaGeralPecas = MediaPecas / totalOps;

                    // Adiciona a linha de total de peças e média geral
                    const trTotal = document.createElement("tr");
                    trTotal.className = "total-row"; // Adiciona a classe CSS
                    trTotal.innerHTML = `
                        <td colspan="2" class="total-column"><strong>Total Peças</strong></td>
                        <td>${totalPecas}</td>
                        <td>${totalOps}</td>
                        <td>${mediaGeralPecas.toFixed(2)}</td>
                    `;
                    rankingBody.appendChild(trTotal);
                })
                .catch(error => console.error("Erro ao obter dados da API:", error));
        }

      

//------------------------------- Funções dos Botões ---------------------------------------------//

        document.getElementById("filtrarLinhas").addEventListener("click", function () {
            preencherTabela(ApiLinhas);
        });

        document.getElementById("IconeLinhas").addEventListener("click", function () {
            preencherTabela(ApiLinhas);
            document.getElementById("ranking-container").style.display = "block";
            document.getElementById("Producao").style.display = "none";
            document.getElementById("TelaOps").style.display = "none";
        });
    

    document.getElementById("IconeProdução").addEventListener("click", function () {
        preencherTabelaR$(ApiProducao);
        document.getElementById("ranking-container").style.display = "none";
        document.getElementById("TelaOps").style.display = "none";
        document.getElementById("Producao").style.display = "block";
    });

    document.getElementById("IconeLinhas1").addEventListener("click", function () {
            preencherTabela(ApiLinhas);
            document.getElementById("ranking-container").style.display = "block";
            document.getElementById("Producao").style.display = "none";
            document.getElementById("TelaOps").style.display = "none";
        });
    

    document.getElementById("IconeProdução1").addEventListener("click", function () {
        preencherTabelaR$(ApiProducao);
        document.getElementById("ranking-container").style.display = "none";
        document.getElementById("TelaOps").style.display = "none";
        document.getElementById("Producao").style.display = "block";
    });

    
    document.getElementById("IconeConfig").addEventListener("click", function () {
        preencherTabelaOps(ApiListaOps);
        document.getElementById("ranking-container").style.display = "none";
        document.getElementById("TelaOps").style.display = "block";
        document.getElementById("Producao").style.display = "none";
    });

    document.getElementById("IconeConfig1").addEventListener("click", function () {
        preencherTabelaOps(ApiListaOps);
        document.getElementById("ranking-container").style.display = "none";
        document.getElementById("TelaOps").style.display = "block";
        document.getElementById("Producao").style.display = "none";
    });

    document.getElementById("filtrar").addEventListener("click", function () {
        preencherTabelaR$(ApiProducao);
    });

    document.getElementById("filtrarOps").addEventListener("click", function () {
        preencherTabelaOps(ApiListaOps);
    });



    //----------------------------------- Função para preencher a Tabela de Produção em R$-----------------------------//

    function preencherTabelaR$(Api) {
        const ProducaoBody = document.getElementById("Producao-body");
        const dataInicio = document.getElementById("data-inicioProducao").value;
        const dataFim = document.getElementById("data-fimProducao").value;
        const apiUrl = `${Api}DataInicial=${dataInicio}&DataFinal=${dataFim}`;
        const requestOptions = {
            method: "GET",
            headers: {
                "Authorization": Token,
                "Content-Type": "application/json"
            }
        };
        fetch(apiUrl, requestOptions)
            .then(response => response.json())
            .then(data => {
                ProducaoBody.innerHTML = "";
                let posicaoR$ = 0; // Inicializa a posição no ranking

                data.forEach((item, index) => {
                    item["2.0- Detalhamento"].forEach(detalhe => {
                        posicaoR$++;
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${posicaoR$}º</td>
                            <td>${detalhe.operador}</td>
                            <td>${detalhe.qtd}</td>
                            <td>R$ ${detalhe.qtd * 0.010}</td>
                        `;
                        ProducaoBody.appendChild(tr);
                    });
                });
            })
            .catch(error => console.error("Erro ao obter dados da API:", error));
    };


//-------------------------------------Função para preencher a tabela com a lista de Op's---------------------------//
function preencherTabelaOps(Api) {
    const OpsBody = $('#Ops-table1');
    const dataInicio = document.getElementById("data-inicioOps").value;
    const dataFim = document.getElementById("data-fimOps").value;
    const apiUrl = `${Api}dataInicio=${dataInicio}&dataFinal=${dataFim}`;

    // Destruir a tabela DataTable existente, se já foi inicializada
    if ($.fn.DataTable.isDataTable(OpsBody)) {
        OpsBody.DataTable().destroy();
    }

    $.ajax({
        url: apiUrl,
        method: 'GET',
        headers: {
            'Authorization': Token,
            'Content-Type': 'application/json'
        },
        success: function (data) {
            OpsBody.DataTable({
                paging: false,
                info: false,
                searching: true,
                colReorder: false,
                colResize: false,
                data: data,
                columns: [
                    { data: 'numeroop' },
                    { data: 'linha' },
                    { data: 'operador1' },
                    { data: 'operador2' },
                    { data: 'operador3' },
                    { data: 'qtd' },
                    {
                      data: null,
                    render: function (data, type, row) {
                        return '<button class="btn btn-danger btn-editar" onclick="abrirModalEditar(\'' + row.numeroop + '\', \'' + row.linha + '\', \'' + row.qtd + '\')"><i class="bi bi-pencil-square"></i></button>';
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return '<button class="btn btn-danger btn-excluir" onclick="deletarRegistro(\'' + row.numeroop + '\', \'' + row.linha + '\')"><i class="bi bi-person-x-fill"></i></button>';
                        }
                    }
                ],
            });
        },
        error: function (xhr, status, error) {
            console.error("Erro ao obter dados da API:", error);
        }
    });
};

$('#Ops-table1 tbody').on('click', 'td:first-child', async function () {
    var numeroOp = $(this).text();
await localStorage.setItem('numeroOP', numeroOp);
window.location.href = 'TelaGrades.php';
});


async function AtualizarDados(Api) {
    dados = {
        "numeroop": InputNumeroOp.value,
        "linha": InputLinhaAtual.value,
        "qtd": InputQuantidade.value,
        "linhaNova": InputNovaLinha.value,
    }

    console.log('dados')
    try {
        const response = await fetch(Api, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
            body: JSON.stringify(dados),
        });

        if (response.ok) {
            const data = await response.json();
            openModal(modalSuccess);
            document.getElementById('MessagemDeSucesso').innerHTML = 'Salvo com Sucesso';
            fecharModalEditar();
            InputNovaLinha.value = "";
            await preencherTabelaOps(ApiListaOps);  
            
        } else {
            throw new Error('Erro ao obter os dados da API');
            openModal(modalError);
            fecharModalEditar();
            await preencherTabelaOps(ApiListaOps);   
        
        }
    } catch (error) {
        console.error(error);
        openModal(modalError);
            fecharModalEditar();
            await preencherTabelaOps(ApiListaOps);  
    }
}

document.getElementById('formEditar').addEventListener('submit', async function(event) {
    event.preventDefault();
    await AtualizarDados(ApiAtualizarDado);
});


async function deletarRegistro(numeroop, linha) {
    const dados = {
        "numeroop": numeroop,
        "linha": linha
    };
    console.log(dados);

    const confirmacao = confirm("Tem certeza que deseja excluir a OP:" + numeroop + "da Linha: " + linha + "?");

    if (confirmacao) {
        try {
            const response = await $.ajax({
                url: `${ApiDeletarUsuario}?numeroop=${numeroop}&linha=${linha}`,
                method: 'DELETE',
                headers: {
                    'Authorization': Token,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(dados)
            });
            openModal(modalSuccess);
            document.getElementById('MessagemDeSucesso').innerHTML = 'Excluído com Sucesso';
            await preencherTabelaOps(ApiListaOps);
        } catch (error) {
            console.error(error);
            openModal(modalError);
            await preencherTabelaOps(ApiListaOps);
        }
    } else {
        console.log("Exclusão cancelada.");
    }
}



</script>
</html>
