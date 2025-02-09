<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/TelaInicial.css">
    <link rel="stylesheet" href="./css/ListaOps.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

    <style>
        .Tabela {
            width: 80%;
            margin: 30px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: auto;
        }

        #TabelaGrades {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        th {
            background-color: rgb(17, 45, 126);
            color: white;
        }

        .Informacoes {
            margin: 20px auto;
            padding: 15px;
            width: 80%;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #Atualizar {
            cursor: pointer;
            font-size: 25PX;

        }

        .Finalizar {
            width: 100%;
            align-items: center;
            text-align: center;
            justify-content: center;
        }

        #ButtonFinalizar,
        #ButtonAtualizarOps,
        #Button2Qualidade {
            font-size: 15px;
            font-weight: bold;
            width: 15%;
            box-sizing: border-box;
            cursor: pointer;
            background-color: var(--cor2);
            color: white;
        }

        #ButtonFinalizar:hover,
        #ButtonAtualizarOps:hover,
        #Button2Qualidade:hover {
            background-color: rgb(16, 131, 255);
        }


        .Informacoes label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
            color: #333;
        }

        .ModalDefinirLinha {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .ModalDefinirLinha-content {
            background-color: #fff;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            margin: 10% auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .ModalDefinirLinha-content h3 {
            margin: 0;
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px
        }


        button {
            padding: 10px 20px;
            background-color: var(--cor2);
            color: var(--cor4);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        input[type="text"],
        select {
            width: calc(100%);
            margin: 10px 0;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #666;
    }


        .ModalDefinirLinha-content #SelecaoLinha {
            display: block;
            margin-top: 60px;
        }

        .fechar {
            position: absolute;
            cursor: pointer;
            font-size: 30px;
            color: var(--cor2);
            z-index: 1000;
        }

        .fechar:hover {
            color: #555;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            text-align: center;
        }

        .modal-content h2 {
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .modal-buttons button {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-buttons button.primary {
            background-color: green;
            color: white;
        }

        .modal-buttons button.secondary {
            background-color: red;
            color: white;
        }
    </style>
</head>

<body>
<div class="container">
        <div class="title">
            <h3>CONFERÊNCIA REPOSIÇÃO</h3>
            <i class="bi bi-x-square-fill" title="Fechar" id="FecharRotina" style="color: black"></i>
        </div>

        <div class="Informacoes">
            <label for="text" id="LabelOp"></label>
            <label for="text" id="PecasLidas"></label>
            <label for="text" id="PecasOps"></label>
            <i title="Atualizar Grades" id="Atualizar" class="bi bi-arrow-clockwise"></i>
        </div>

        <div class="Tabela">
            <table id="TabelaGrades"></table>
        </div>

        <div class="Finalizar">
            <button id="ButtonFinalizar">FINALIZAR OP</button>
            <button id="ButtonAtualizarOps">ATUALIZAR TAG'S</button>
            <button id="Button2Qualidade">APONTAR 2ª QUALIDADE</button>
        </div>

        <div id="ModalDefinirLinha" class="ModalDefinirLinha">
            <div class="ModalDefinirLinha-content">
                <span id="FecharModalNovoUsuario" class="fechar">&times;</span>
                <h3>Finalização de Op</h3>
                <label for="text" id="SelecaoLinha" class="SelecaoLinha">Selecione A Linha de Atribuição:</label>
                <select id="SelectLinha">
                </select>
                <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                    <button type="button" id="ButtonConfirmar">Confirmar</button>
                    <button type="button" id="buttonCancelar">Cancelar</button>
                </div>
                <div class="NomesDaLinha" id="NomesDaLinha" style="display: none;">
                    <form id="FormConfereOperadores" action="" method="post"
                        onsubmit="event.preventDefault();">
                        <select id="Operador1" name="Operador1" required>
                            <option value="">Selecione o Operador 1</option>
                        </select>
                        <select id="Operador2" name="Operador2" required>
                            <option value="">Selecione o Operador 2</option>
                        </select>
                        <select id="Operador3" name="Operador3" required>
                            <option value="">Selecione o Operador 3</option>
                        </select>
                        <input type="text" placeholder="Quantidade" style="display: none" id="InputQuantidade">

                        <button type="submit" id="salvarEdicaoUsuario">Salvar</button>
                    </form>
                </div>
            </div>
</div>

<div class="modal-overlay" id="modal">
    <div class="modal-content">
        <h2>Op Será Dividida?</h2>
        <div class="modal-buttons">
            <button class="primary" onclick="confirmar()">Sim</button>
            <button class="secondary" onclick="fecharModal()">Não</button>
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
            const ApiAtualizar = "http://10.162.0.190:5000/api/AtualizacaoFilaOFF";
            const ApiConsultarLinhas = 'http://10.162.0.190:5000/api/linhasPadrao';
            const GetUsuarios = "http://10.162.0.190:5000/api/UsuariosPortal";
            const ApiSalvarOperadores = 'http://10.162.0.190:5000/api/SalvarProdutividadeLinha'
            const Token = "a40016aabcx9";
            const SalvarOperadores = document.getElementById('salvarEdicaoUsuario');

            let dadosApi = [];
            let Linhas = '';

            async function Api(Op) {
                try {
                    const numeroOP = localStorage.getItem('numeroOP');
                    const response = await fetch(`http://10.162.0.190:5000/api/DetalhaOPQuantidade?empresa=1&numeroop=${Op}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'a40016aabcx9'
                        },
                    });

                    if (response.ok) {
                        const data = await response.json();
                        dadosApi = data[0]["3- Detalhamento da Grade"];
                        const QtdLida = data[0]["2.2- Total Bipado"];
                        const QtdOp = data[0]["2.1 - Total OP"];
                        criarTabela(dadosApi);
                        document.getElementById('LabelOp').textContent = `Numero da Op: ${numeroOP}`;
                        document.getElementById('PecasLidas').textContent = `Qtd Peças Lidas: ${QtdLida}`;
                        document.getElementById('PecasOps').textContent = `Qtd Peças Op: ${QtdOp}`
                        console.log(data);
                    } else {
                        throw new Error('Erro No Retorno');
                    }
                } catch (error) {
                    console.error(error);
                }
            }

           // Função para selecionar uma opção existente em uma select
function selecionarOpcaoExistente(select, valor) {
    const opcoes = Array.from(select.options);
    const opcaoExistente = opcoes.find(opcao => opcao.value === valor);

    if (opcaoExistente) {
        opcaoExistente.selected = true;
    }
}

// Função para bloquear as opções selecionadas em todas as selects
function bloquearOpcoesSelecionadas(selects) {
    const selectedValues = selects.map(select => select.value);

    selects.forEach(select => {
        Array.from(select.options).forEach(option => {
            option.disabled = false;

            if (selectedValues.includes(option.value) && option.value !== select.value) {
                option.disabled = true;
            }
        });
    });
}

// Função para preencher opções bloqueadas com base nos usuários
function preencherOpcoesBloqueadas(selects, usuarios) {
    if (selects && Array.isArray(selects)) {
        selects.forEach(select => {
            if (select && select.options) {
                Array.from(select.options).forEach(option => {
                    if (option && usuarios.some(usuario => usuario.nome === option.value)) {
                        option.remove();
                    }
                });
            }
        });
    }
}

async function ApiLinhas(Api) {
    const DefineLinhas = document.getElementById('SelectLinha');
    const SelectOperador1 = document.getElementById('Operador1');
    const SelectOperador2 = document.getElementById('Operador2');
    const SelectOperador3 = document.getElementById('Operador3');
    const optionDefault = document.createElement('option');

    optionDefault.value = 'NADA';
    optionDefault.textContent = 'Selecione uma Linha';
    DefineLinhas.appendChild(optionDefault);

    
    // Adicionar um ouvinte de eventos à seleção de linha
    DefineLinhas.addEventListener('change', function () {
        const linhaSelecionada = DefineLinhas.value;

        // Encontrar a linha correspondente nos dados da API
        const linha = Linhas.find(item => item.Linha === linhaSelecionada);

        // Se a linha for encontrada, selecionar as opções de operadores existentes
        if (linha) {
            selecionarOpcaoExistente(SelectOperador1, linha.operador1);
            selecionarOpcaoExistente(SelectOperador2, linha.operador2);
            selecionarOpcaoExistente(SelectOperador3, linha.operador3);

            // Bloquear opções selecionadas imediatamente após selecionar a linha
            bloquearOpcoesSelecionadas([SelectOperador1, SelectOperador2, SelectOperador3]);
        }
    });

    try {
        const response = await fetch(Api, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'a40016aabcx9'
            },
        });

        if (response.ok) {
            const data = await response.json();
            Linhas = data;
            console.log(Linhas);
            Linhas.forEach(Linha => {
                
                const option = document.createElement('option');

                option.value = Linha.Linha;
                option.textContent = Linha.Linha;

                DefineLinhas.appendChild(option);
            });
        } else {
            throw new Error('Erro No Retorno');
        }
    } catch (error) {
        console.error(error);
    }
}

async function ApiGetUsuarios() {
    const SelectOperador1 = document.getElementById('Operador1');
    const SelectOperador2 = document.getElementById('Operador2');
    const SelectOperador3 = document.getElementById('Operador3');

    try {
        const response = await fetch(GetUsuarios, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "a40016aabcx9"
            },
        });

        if (response.ok) {
            const data = await response.json();
            const usuarios = data;

            // Atualizar as opções bloqueadas após adicionar os novos usuários
            preencherOpcoesBloqueadas([SelectOperador1, SelectOperador2, SelectOperador3], usuarios);

            [SelectOperador1, SelectOperador2, SelectOperador3].forEach(select => {
                select.addEventListener('change', (event) => {
                    const selectedText = event.target.options[event.target.selectedIndex].text;
                    const previouslySelectedText = select.getAttribute('data-selected-text');

                    [SelectOperador1, SelectOperador2, SelectOperador3].forEach(otherSelect => {
                        if (previouslySelectedText && otherSelect !== select) {
                            Array.from(otherSelect.options).forEach(option => {
                                if (option.text === previouslySelectedText) {
                                    option.disabled = false;
                                }
                            });
                        }
                    });

                    [SelectOperador1, SelectOperador2, SelectOperador3].forEach(otherSelect => {
                        if (otherSelect !== select) {
                            Array.from(otherSelect.options).forEach(option => {
                                if (option.text === selectedText) {
                                    option.disabled = true;
                                }
                            });
                        }
                    });

                    // Update the data-selected-text attribute with the currently selected text
                    select.setAttribute('data-selected-text', selectedText);

                    // Bloquear opções selecionadas em todas as selects
                    bloquearOpcoesSelecionadas([SelectOperador1, SelectOperador2, SelectOperador3]);
                });
            });

            // Adiciona as opções com base nos usuários da API
            usuarios.forEach(usuario => {
                const option1 = document.createElement('option');
                const option2 = document.createElement('option');
                const option3 = document.createElement('option');

                option1.value = usuario.nome;
                option2.value = usuario.nome;
                option3.value = usuario.nome;

                option1.textContent = usuario.nome;
                option2.textContent = usuario.nome;
                option3.textContent = usuario.nome;

                SelectOperador1.appendChild(option1);
                SelectOperador2.appendChild(option2);
                SelectOperador3.appendChild(option3);
            });

        } else {
            throw new Error('Erro no retorno da API');
        }
    } catch (error) {
        console.error(error);
    }
}


            function criarTabela(dados) {
                const tabela = document.getElementById('TabelaGrades');
                tabela.innerHTML = '';

                const cabecalho = tabela.createTHead();
                const linhaCabecalho = cabecalho.insertRow();
                linhaCabecalho.insertCell().textContent = 'Cores';

                // Defina a ordem desejada para os tamanhos
                const ordemTamanhos = ['2', '4', '6', '8', '10', '12', '14', 'PP', 'P', 'M', 'G', 'GG', 'XG', 'XGG', 'G1', 'G2', 'G3', 'UNI'];

                ordemTamanhos.forEach(tamanho => {
                    const th = document.createElement('th');
                    th.textContent = tamanho;
                    linhaCabecalho.appendChild(th);
                });

                dados.forEach(item => {
                    const linha = tabela.insertRow();
                    const celulaCor = linha.insertCell();
                    celulaCor.textContent = item['2-sortimentosCores'];

                    ordemTamanhos.forEach(tamanho => {
                        const celulaQuantidade = linha.insertCell();
                        const indice = item.tamanho.indexOf(tamanho);

                        if (indice !== -1) {
                            const quantidade = item.quantidade[indice];
                            const [atual, total] = quantidade.split('/').map(Number);

                            if (atual === 0) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = 'white';
                            } else if (atual < total) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = '#FA8072';
                            }
                            else if (atual === total) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = '#2E8B57';
                            }
                            else if (atual > total) {
                                celulaQuantidade.textContent = quantidade;
                                celulaQuantidade.style.backgroundColor = '#FFFF66';
                            } else {
                                celulaQuantidade.textContent = quantidade;
                            }
                        } else {
                            celulaQuantidade.textContent = '-';
                        }
                    });
                });
            }




            window.addEventListener('load', () => {
                const numeroOP = localStorage.getItem('numeroOP');
                if (numeroOP) {
                    Api(numeroOP);
                } else {
                    console.error('Número da OP não encontrado.');
                    alert('Selecione uma Op para Prosseguir!');
                    window.location.href = 'TelaLiberacao.html';
                }
            });

            async function ChamadaAtualizar(Op) {
                try {
                    const response = await fetch(`${ApiAtualizar}?op=${Op}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': Token
                        },
                    });

                    if (response.ok) {
                        const data = await response.json();
                        console.log(data);
                    } else {
                        throw new Error('Erro No Retorno');
                    }
                } catch (error) {
                    console.error(error);
                }
            }


            const AtualizarPagina = document.getElementById('Atualizar');
            const ButtonFinalizarOp = document.getElementById('ButtonFinalizar');
            const ModalDefinirLinha = document.getElementById('ModalDefinirLinha');
            const FecharModal = document.getElementById('FecharModalNovoUsuario');
            const InputOperadores = document.getElementById('InputOperadores');
            const EditarOperadores = document.getElementById('EditarOperadores');
            const ButtonConfirmar = document.getElementById('ButtonConfirmar');
            const ButtonCancelar = document.getElementById('buttonCancelar');
            const ButtonCancelar1 = document.getElementById('buttonCancelar2');
            const NomesDaLinha = document.getElementById('NomesDaLinha');
            const AtualizarOps = document.getElementById('ButtonAtualizarOps');
            const InputQuantidade = document.getElementById('InputQuantidade');
            
            
           


            ButtonConfirmar.addEventListener('click', () => {
                const SelectLinha = document.getElementById('SelectLinha');
                if(SelectLinha.value === 'NADA'){
                    alert('Seleciona uma Linha antes de Prosseguir')
                } else{
                    NomesDaLinha.style.display = 'block';
                    ButtonConfirmar.style.display = 'none';
                    ButtonCancelar.style.display = 'none';
                }
            });


            ButtonCancelar.addEventListener('click', () => {
                NomesDaLinha.style.display = 'none';
                ModalDefinirLinha.style.display = 'none';
                const SelectLinha = document.getElementById('SelectLinha');
                SelectLinha.innerHTML = '';
                InputQuantidade.value = '';
            });



            FecharModal.addEventListener('click', () => {
                NomesDaLinha.style.display = 'none';
                ModalDefinirLinha.style.display = 'none';
                const SelectLinha = document.getElementById('SelectLinha');
                SelectLinha.innerHTML = '';
                InputQuantidade.value = '';
            });

            function abrirModal() {
                document.getElementById("modal").style.display = "flex";
            }

            async function fecharModal() {
                ModalDefinirLinha.style.display = 'block';
                ButtonConfirmar.style.display = 'flex';
                ButtonCancelar.style.display = 'flex';
                InputQuantidade.style.display = 'none';
                await ApiGetUsuarios()
                await ApiLinhas(ApiConsultarLinhas);
                document.getElementById("modal").style.display = "none";
            }

            async function confirmar() {
                ModalDefinirLinha.style.display = 'block';
                ButtonConfirmar.style.display = 'flex';
                ButtonCancelar.style.display = 'flex';
                InputQuantidade.style.display = 'block';
                await ApiGetUsuarios()
                await ApiLinhas(ApiConsultarLinhas);
                document.getElementById("modal").style.display = "none";
            }


            ButtonFinalizarOp.addEventListener('click', async () => {
                abrirModal();
            });


            AtualizarPagina.addEventListener('click', () => {
                const numeroOP = localStorage.getItem('numeroOP');
                if (numeroOP) {
                    Api(numeroOP);
                } else {
                    console.error('Número da OP não encontrado.');
                    alert('Selecione uma Op para Prosseguir!');
                    window.location.href = 'TelaLiberacao.html';
                }

            });



            AtualizarOps.addEventListener('click', () => {
                const numeroOP = localStorage.getItem('numeroOP');
                ChamadaAtualizar(numeroOP)

            })

            SalvarOperadores.addEventListener('click', async () => {
    const numeroOP = localStorage.getItem('numeroOP');
    const SelectLinha = document.getElementById('SelectLinha');
    const SelectOperador1 = document.getElementById('Operador1');
    const SelectOperador2 = document.getElementById('Operador2');
    const SelectOperador3 = document.getElementById('Operador3');
    const dados = {
        "linha": SelectLinha.value,
        "numeroop": numeroOP,
        "operador1": SelectOperador1.value,
        "operador2": SelectOperador2.value,
        "operador3": SelectOperador3.value,
        "qtd": InputQuantidade.value
    };
    console.log(dados)

    try {
        const response = await fetch(ApiSalvarOperadores, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
            body: JSON.stringify(dados),
        });

        if (response.ok) {
            const data = await response.json();
            console.log(data);

            // Limpa as opções existentes e adiciona a opção padrão
            SelectLinha.innerHTML = '';
            
        

            // Atualiza a tabela e demais elementos conforme necessário
            NomesDaLinha.style.display = 'none';
            await Api(numeroOP);
            ModalDefinirLinha.style.display = 'none';
            InputQuantidade.value = '';
        } else {
            throw new Error('Erro No Retorno');
        }
    } catch (error) {
        console.error(error);
        NomesDaLinha.style.display = 'none';
        await Api(numeroOP);
        ModalDefinirLinha.style.display = 'none';
    }
});


        </script>
</html>
