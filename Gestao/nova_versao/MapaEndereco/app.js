$(document).ready(function () {
    // Carrega o mapa assim que a tela abre
    carregarMapaEnderecos();

    // ================= EVENTOS DE CLIQUE =================

    // 1. Ao clicar em um quadrado de endereço no mapa
    $(document).on('click', '.endereco-card', function() {
        const enderecoClicado = $(this).data('endereco');
        
        // Preenche o modal pequeno com o nome do endereço
        $('#modalEnderecoSelecionado').text(enderecoClicado);
        $('#btnConsultarEndereco').data('endereco', enderecoClicado);
        $('#btnExcluirEndereco').data('endereco', enderecoClicado);

        // Abre o modal de opções
        const modal = new bootstrap.Modal(document.getElementById('modalOpcoesEndereco'));
        modal.show();
    });

    // 2. Ao clicar no botão "Consultar" dentro do modal de opções
    $(document).on('click', '#btnConsultarEndereco', function() {
        const endereco = $(this).data('endereco');
        const btn = $(this);
        const textoOriginal = btn.html();

        // Feedback visual: botão carrega e fica bloqueado
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...');
        btn.prop('disabled', true);

        // Requisição para buscar os itens daquele endereço
        $.ajax({
            url: 'requests.php',
            type: 'GET',
            data: { 
                acao: 'get_consultar_endereco',
                endereco: endereco
            },
            dataType: 'json',
            success: function(response) {
                // Restaura o botão original
                btn.html(textoOriginal).prop('disabled', false);

                // Coloca o nome do endereço no título do novo modal da tabela
                $('#tituloEnderecoTabela').text(endereco);

                let linhasTabela = '';
                let totalSoma = 0; // Variável para somar as quantidades

                // Monta as linhas se houver itens
                if (response && response.length > 0) {
                    response.forEach(item => {
                        
                        // Soma a quantidade deste item ao total geral
                        totalSoma += parseInt(item.qtd) || 0; 

                        // Formatação rápida de data (de "2026-03-10 16:17:44" para "10/03/2026 16:17")
                        let dataFormatada = item.dataHora;
                        if (item.dataHora && item.dataHora.includes('-')) {
                            const partes = item.dataHora.split(' ');
                            const d = partes[0].split('-');
                            const h = partes[1].split(':');
                            dataFormatada = `${d[2]}/${d[1]}/${d[0]} ${h[0]}:${h[1]}`;
                        }

                        linhasTabela += `
                            <tr>
                                <td class="fw-bold align-middle">${item.codItem}</td>
                                <td class="align-middle text-center">${item.codItem_seq}</td>
                                <td class="align-middle" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${item.nome}">${item.nome}</td>
                                <td class="text-center fw-bold text-primary align-middle" style="font-size: 1rem;">${item.qtd}</td>
                                <td class="align-middle" style="font-size: 0.8rem;">${dataFormatada}</td>
                                <td class="align-middle" style="font-size: 0.75rem;">${item.usuario}</td>
                            </tr>
                        `;
                    });

                    // ADICIONA A LINHA DE TOTAL NO FINAL DA TABELA
                    linhasTabela += `
                        <tr class="table-secondary">
                            <td colspan="3" class="text-end fw-bold align-middle text-uppercase" style="color: #10045a;">Total de Peças no Endereço:</td>
                            <td class="text-center fw-bold align-middle" style="font-size: 1.1rem; color: #10045a;">${totalSoma}</td>
                            <td colspan="2"></td>
                        </tr>
                    `;

                } else {
                    linhasTabela = `<tr><td colspan="6" class="text-center text-muted py-4 fw-bold">Nenhum item encontrado no endereço ${endereco}.</td></tr>`;
                }

                // Injeta as linhas no corpo da tabela
                $('#tbodyItensEndereco').html(linhasTabela);

                // Esconde o modal de opções (o pequeno)
                $('#modalOpcoesEndereco').modal('hide');
                
                // Abre o modal grande com a tabela
                const modalTabela = new bootstrap.Modal(document.getElementById('modalTabelaItens'));
                modalTabela.show();
            },
            error: function(err) {
                console.error("Erro ao consultar endereço:", err);
                btn.html(textoOriginal).prop('disabled', false);
                alert("Erro ao buscar os itens. Verifique sua conexão ou a resposta do servidor.");
            }
        });
    });

    // 3. Ao clicar no botão "Excluir" dentro do modal de opções
    $(document).on('click', '#btnExcluirEndereco', function() {
        const endereco = $(this).data('endereco');
        
        if(confirm('Atenção! Tem certeza que deseja excluir o endereço ' + endereco + '?')) {
            // AQUI ENTRARÁ O POST DE EXCLUSÃO
            alert('Lógica para excluir o endereço: ' + endereco + ' acionada.');
        }
    });
});

// ================= FUNÇÕES DO MAPA =================

function carregarMapaEnderecos() {
    $.ajax({
        url: 'requests.php', 
        type: 'GET',
        data: { acao: 'get_mapa_enderecos' },
        dataType: 'json',
        success: function (response) {
            if (response && response.length > 0) {
                renderizarMapa(response);
            } else {
                $('#mapa-container').html('<div class="col-12 text-center text-muted fw-bold py-5">Nenhum endereço encontrado na base de dados.</div>');
            }
        },
        error: function (err) {
            console.error("Erro na requisição AJAX:", err);
            $('#mapa-container').html('<div class="col-12 text-center text-danger fw-bold py-5">Erro ao comunicar com o servidor. Tente atualizar a página.</div>');
        }
    });
}

function renderizarMapa(dados) {
    const ruasAgrupadas = {};

    // --- VARIÁVEIS PARA OS INDICADORES DO TOPO ---
    let globalTotal = 0;
    let globalVazios = 0;
    let globalCheios = 0;

    // 1. Processar e agrupar os dados pela Rua
    dados.forEach(item => {
        const enderecoCompleto = item.endereco.toUpperCase();
        const partes = enderecoCompleto.split('-');
        
        if (partes.length >= 1) {
            const nomeRua = partes[0]; 

            if (!ruasAgrupadas[nomeRua]) {
                ruasAgrupadas[nomeRua] = [];
            }

            ruasAgrupadas[nomeRua].push({
                enderecoStr: enderecoCompleto,
                qtd: parseInt(item.QtdItens) 
            });
        }
    });

    // 2. Ordenar as ruas em ordem alfabética
    const ruasOrdenadas = Object.keys(ruasAgrupadas).sort();
    let htmlFinal = '';

    // 3. Montar o HTML
    ruasOrdenadas.forEach(rua => {
        const enderecosDaRua = ruasAgrupadas[rua].sort((a, b) => a.enderecoStr.localeCompare(b.enderecoStr));

        // --- CÁLCULO DOS CONTADORES DA RUA E GLOBAIS ---
        let totalEnderecos = enderecosDaRua.length;
        let qtdVazios = 0;
        let qtdCheios = 0;

        enderecosDaRua.forEach(end => {
            globalTotal++; // Soma 1 no total do galpão

            if (end.qtd === 0) {
                qtdVazios++;
                globalVazios++; // Soma 1 nos vazios do galpão
            } else {
                qtdCheios++;
                globalCheios++; // Soma 1 nos cheios do galpão
            }
        });

        // --- CABEÇALHO DA RUA COM ESTATÍSTICAS ---
        let htmlRua = `
        <div class="col-12 col-md-6 col-xl-4">
            <div class="rua-container">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 2px dashed #ccc;">
                    <div class="fs-4 fw-bold" style="color: #10045a;">RUA ${rua}</div>
                    <div class="text-end" style="font-size: 0.8rem; line-height: 1.4;">
                        <span class="d-block text-secondary fw-bold">Total: ${totalEnderecos}</span>
                        <span class="d-block text-success fw-bold">Vazios: ${qtdVazios}</span>
                        <span class="d-block text-danger fw-bold">Cheios: ${qtdCheios}</span>
                    </div>
                </div>
                <div class="grid-enderecos">
        `;

        // --- MONTAGEM DOS QUADRADOS ---
        enderecosDaRua.forEach(end => {
            let classeCor = '';
            let tooltipMsg = '';

            if (end.qtd === 0) {
                classeCor = 'status-vazio';
                tooltipMsg = `Endereço: ${end.enderecoStr} | Vazio`;
            } else if (end.qtd === 1) {
                classeCor = 'status-ocupado';
                tooltipMsg = `Endereço: ${end.enderecoStr} | 1 item`;
            } else if (end.qtd >= 2) {
                classeCor = 'status-multi';
                tooltipMsg = `Endereço: ${end.enderecoStr} | ${end.qtd} itens`;
            }

            htmlRua += `
                <div class="endereco-card ${classeCor}" data-endereco="${end.enderecoStr}" title="${tooltipMsg}">
                    ${end.enderecoStr}
                </div>
            `;
        });

        htmlRua += `
                </div>
            </div>
        </div>
        `;

        htmlFinal += htmlRua; 
    });

    // Injeta os blocos das ruas na tela
    $('#mapa-container').html(htmlFinal);

    // =========================================================
    // 4. ATUALIZAR OS CARDS DE INDICADORES NO TOPO DA PÁGINA
    // =========================================================
    let taxaUtilizacao = 0;
    if (globalTotal > 0) {
        taxaUtilizacao = ((globalCheios / globalTotal) * 100).toFixed(1);
    }

    $('#ind-total').text(globalTotal);
    $('#ind-cheio').text(globalCheios);
    $('#ind-vazio').text(globalVazios);
    $('#ind-taxa').text(taxaUtilizacao + '%');
}