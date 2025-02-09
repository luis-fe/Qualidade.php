<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/TelaInicial.css">
    <link rel="stylesheet" href="./css/ListaOps.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

</head>

<body>
<div class="container">
        <div class="title">
            <h3>Lista de Op's</h3>
            <i class="bi bi-x-square-fill" title="Fechar" id="FecharRotina" style="color: black"></i>
        </div>

    <div class="dados">
        <label id="Ops" for="text"></label>
        <label id="Pcs" for="text"></label>
    </div>

    <div class="Pesquisa">
        <input id="pesquisar" type="search" placeholder="Pesquisar por OP" oninput="filtrarPorOP(this.value)">
    </div>
    <div class="container2">
        <div class="left-container" id="opButtonsContainerLeft"></div>
        <div class="right-container" id="opButtonsContainerRight"></div>
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
      document.addEventListener('DOMContentLoaded', function () {
    let detalhesOPsData; // Armazena os detalhes da OP para referência posterior

    async function ChamadaApi() {
        try {
            const response = await fetch('http://10.162.0.190:5000/api/RelacaoDeOPs?empresa=1', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'a40016aabcx9'
                },
            });

            if (response.ok) {
                const data = await response.json();
                detalhesOPsData = data[0]["Detalhamento das OPs "];
                construirInterface(detalhesOPsData);
                document.getElementById('Ops').innerHTML = `Total Op's na Fase: <strong>${data[0]['0 - Total de OPs ']}</strong>`;
                document.getElementById('Pcs').innerHTML = `Total Peças na Fase: <strong>${data[0]['03 - Total Pçs ']}</strong>`;
                console.log(data);
            } else {
                throw new Error('Erro No Retorno');
            }
        } catch (error) {
            console.error(error);
        }
    }

    function construirInterface(detalhesOPs) {
        const opButtonsContainerLeft = document.getElementById('opButtonsContainerLeft');
        const opButtonsContainerRight = document.getElementById('opButtonsContainerRight');

        detalhesOPs.forEach(item => {
            const btn = document.createElement('button');
            btn.classList.add('button');

            const infoDiv = document.createElement('div');
            infoDiv.style.flex = '1';
            infoDiv.style.textAlign = 'left';
            infoDiv.innerHTML = `<strong style="font-size: 26px;">OP: ${item.numeroop}</strong><br><br><strong>Produto: ${item.codProduto}</strong><br><br><strong>Quantidade Op: ${item.quantidade}</strong>`;
            btn.appendChild(infoDiv);

            const statusDiv = document.createElement('div');
            statusDiv.classList.add('statusDiv');
            statusDiv.style.textAlign = 'right';
            statusDiv.innerHTML = `<br><strong>Stauts Reposição:</strong> ${item.status_reposicao}<br><br><strong>Fase Atual:</strong> ${item.faseAtual}`;
            btn.appendChild(statusDiv);

            const detailsDiv = document.createElement('div');
            detailsDiv.classList.add('details');
            detailsDiv.innerHTML = `Detalhes:<br>Categoria: ${item.categoria}<br>Descrição: ${item.nome}<br>Data Finalização Costura: ${item.dataCostura}<br>Faccionista: ${item.nomeFaccionista}<br>Lote: ${item.lote}`;
            btn.appendChild(detailsDiv);

            btn.addEventListener('click', () => Grades(item.numeroop));

            if (item.status_reposicao === 'Nao Iniciado') {
                opButtonsContainerLeft.appendChild(btn);
            } else {
                opButtonsContainerRight.appendChild(btn);
                btn.style.backgroundColor = 'rgb(255, 241, 113)';
                btn.style.color = 'black'
            }
        });
    }

    function Grades(Op) {
        // Armazene o valor da OP no localStorage
        localStorage.setItem('numeroOP', Op);

        // Redirecione para a nova página
        window.location.href = 'TelaGrades.php';
    }

    function filtrarPorOP(op) {
        const opLowerCase = op.toLowerCase(); // Convertendo para minúsculas

        const opFiltered = detalhesOPsData.filter(item =>
            item.numeroop.toLowerCase().includes(opLowerCase) ||
            item.nomeFaccionista.toLowerCase().includes(opLowerCase) ||
            item.categoria.toLowerCase().includes(opLowerCase) ||
            item.lote.toLowerCase().includes(opLowerCase)
        );

        const opButtonsContainerLeft = document.getElementById('opButtonsContainerLeft');
        const opButtonsContainerRight = document.getElementById('opButtonsContainerRight');

        opButtonsContainerLeft.innerHTML = ''; // Limpa o conteúdo atual
        opButtonsContainerRight.innerHTML = ''; // Limpa o conteúdo atual

        construirInterface(opFiltered);
    }

    ChamadaApi();

    document.getElementById('pesquisar').addEventListener('input', function () {
        filtrarPorOP(this.value);
    });
});

    </script>

</html>
