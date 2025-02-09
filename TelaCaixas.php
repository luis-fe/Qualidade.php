<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/TelaInicial.css">
    <link rel="stylesheet" href="./css/TelaCaixas.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.4/js/dataTables.colReorder.min.js"></script>

    <!-- DataTables Buttons CSS e JS -->
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

</head>
<body>
<div class="container">
        <div class="title">
            <h3>Gerenciamento de Caixas</h3>
            <i class="bi bi-x-square-fill" title="Fechar" id="FecharRotina" style="color: black"></i>
        </div>
        <div class="Selecoes" style="display: flex; justify-content: space-between;">
            <div class="SelecaoEnderecos">
                <label for="text" id="LabelQuantidade">Nº da Caixa</label>
                <input type="text" id="InputNumero">
                <button id="BotaoConsultarCaixa">Consultar Caixa</button>
            </div>
            <div class="SelecaoTag">
                <label for="text" id="LabelTag">Cód. Tag</label>
                <input type="text" id="InputTag">
                <button id="BotaoConsultarTag">Consultar Tag</button>
            </div>
        </div>

        <div class="Informacoes" style=" display: flex; text-align: center; margin: 20px auto; width: 60%; justify-content: space-around;">
            <label for="text" id="LabelOp" style="margin-left: 10px; text-align: center; font-weight: bold; font-size: 20px;"></label>
            <label for="text" id="LabelProduto" style="margin-left: 10px; text-align: center; font-weight: bold; font-size: 20px;"></label>
            <label for="text" id="LabelDescricao" style="margin-left: 10px; text-align: center; font-weight: bold; font-size: 20px;"></label>
        </div>


        <div class="Tabela" id="Tabela" style="display: none;">
            <table id="TabelaTags" class="display" style="width: 100%; min-width: 100%;">
                <thead>
                    <tr>
                        <th>Tag's Na Caixa</th>
                    </tr>
                </thead>
            </table>

        </div>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p id="Conteudo"></p>
            </div>
        </div>
</div>
</body>
<script>
     const btnFecharRotina = document.getElementById("FecharRotina");
    function fecharRotina() {
        window.location.replace("TelaInicial.php");
    };

    btnFecharRotina.addEventListener('click', () => {
        fecharRotina();
    });
    const NumeroCaixa = document.getElementById('InputNumero');
    const ApiApagar = 'http://10.162.0.190:5000/api/LimparCaixa';
    const ApiConsultar = 'http://10.162.0.190:5000/api/ConsultaCaixa';
    const ApiConsultarTag = 'http://10.162.0.190:5000/api/PesquisarCodbarrastag';
    const Token = 'a40016aabcx9';
    const BotaoConsultarCaixa = document.getElementById('BotaoConsultarCaixa');
    const DivTabela = document.getElementById('Tabela');
    const seuBotao = document.getElementById('seuBotao'); 
    const modal = document.getElementById('myModal');
    const closeModal = document.getElementsByClassName('close')[0];
    const BotaoConsultarTag = document.getElementById('BotaoConsultarTag');
    const Conteudo = document.getElementById('Conteudo');
    const LabelOp = document.getElementById('LabelOp');
    const LabelProduto = document.getElementById('LabelProduto');
    const LabelDescricao = document.getElementById('LabelDescricao');

    BotaoConsultarTag.addEventListener('click', async () => {
        ConsultarTag(ApiConsultarTag, document.getElementById('InputTag').value);
    });

    NumeroCaixa.addEventListener('keyup', function (event) {
        if (event.key === 'Enter') {
            ConsultarCaixa(ApiConsultar, NumeroCaixa.value);
        }
    });

    document.getElementById('InputTag').addEventListener('keyup', function (event) {
        if (event.key === 'Enter') {
            ConsultarTag(ApiConsultarTag, document.getElementById('InputTag').value);
        }
    });

    BotaoConsultarCaixa.addEventListener('click', async () => {
        ConsultarCaixa(ApiConsultar, NumeroCaixa.value);
        console.log(parseInt(NumeroCaixa.value))
    })

    async function DeletarCaixa(Api, Caixa) {

        try {
            const response = await fetch(`${Api}?caixa=${Caixa}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': Token
                },
            });

            if (response.ok) {
                const data = await response.json();
                console.log(data);
                alert(`Tag's da Caixa ${NumeroCaixa.value} apagados com Sucesso!`);
                NumeroCaixa.value = ""
            } else {
                throw new Error('Erro No Retorno');
                alert(`Não foi possível apagar os dados`);
            }
        } catch (error) {
            console.error(error);
            alert(`Não foi possível apagar os dados`);
        }
    }


    async function ConsultarCaixa(Api, Caixa) {

        try {
            const response = await fetch(`${Api}?Ncaixa=${Caixa}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': Token
                },
            });

            if (response.ok) {
                const data = await response.json();
                console.log(data);
                if (data[0].mensagem === "caixa vazia") {
                    alert("Caixa está Vazia");

                } else {
                    console.log(data[0]['03- numeroOP'])
                    LabelOp.innerHTML = `OP:<br>${data[0]['03- numeroOP']}`;
                    LabelProduto.innerHTML = `Engenharia:<br>${data[0]['06- engenharia']}`;
                    LabelDescricao.innerHTML = `Descrição:<br>${data[0]['08- descricao']}`;

                    await criarTabelaTags(data[0]["13- Tags da Caixa "]);
                    DivTabela.style.display = 'flex'
                }
            } else {
                throw new Error('Erro No Retorno');
            }
        } catch (error) {
            console.error(error);
        }
    }

    async function ConsultarTag(Api, Tag) {

        try {
            const response = await fetch(`${Api}?codbarras=${Tag}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': Token
                },
            });

            if (response.ok) {
                const data = await response.json();
                console.log(data);
                if (data[0].caixa === "Nao encontrado em nenhuma caixa") {
                    Conteudo.textContent = `Tag Não Encontrada em nenhuma Caixa`
                    modal.style.display = 'flex'
                } else {

                    Conteudo.textContent = `Tag Encontrada na Caixa: ${data[0].caixa}`
                    modal.style.display = 'flex'
                }
            } else {
                throw new Error('Erro No Retorno');
                Conteudo.textContent = `Tag Não Encontrada em nenhuma Caixa`;
                modal.style.display = 'flex';
            }
        } catch (error) {
            console.error(error);
            Conteudo.textContent = `Tag Não Encontrada em nenhuma Caixa`;
            modal.style.display = 'flex';
        }
    }

    function criarTabelaTags(listaTags) {

        const tabelaExiste = $.fn.DataTable.isDataTable('#TabelaTags');

        // Inicializa a DataTable ou destroi e recria se já existir
        const tabela = tabelaExiste
            ? $('#TabelaTags').DataTable()
            : $('#TabelaTags').DataTable({
                paging: false,
                info: false,
                searching: true,
                colReorder: true,
                colResize: true,
                columns: [
                    { data: "codbarrastag" },

                ],
                dom: 'RBlfrtip', // Adiciona os controles de redimensionamento e botões
                responsive: true, // Torna a tabela responsiva
                buttons: [

                    {
                        extend: 'excel', // Adiciona o botão de exportar para Excel
                        text: 'Exportar para Excel'
                    },
                    {
                        text: 'Apagar Dados', // Texto do botão
                        className: 'ApagarCaixa',
                        action: function () {
                            DeletarCaixa(ApiApagar, NumeroCaixa.value);
                            DivTabela.style.display = 'none'
                        }
                    }
                ]
            });

        // Limpa a tabela existente (preservando o cabeçalho)
        if (tabelaExiste) {
            tabela.clear();
        }

        // Adiciona os novos dados
        tabela.rows.add(listaTags).draw();

    }

    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }

    // Ocultar a modal quando o botão de fechar for clicado
    closeModal.onclick = function () {
        modal.style.display = 'none';
    }
</script>

</html>
