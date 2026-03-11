$(document).ready(function () {
    // 1. Carrega os dados imediatamente ao abrir a página
    buscarDados();

    // 2. Configura a atualização automática a cada 3 minutos (180.000 ms)
    setInterval(function() {
        console.log("Atualização automática disparada...");
        buscarDados();
    }, 180000); 

    // 3. Evento de clique no botão filtrar (caso o utilizador queira atualizar manualmente)
    $('#btnFiltrar').on('click', function() {
        buscarDados();
    });
});

function buscarDados() {
    const dInicio = $('#dataInicio').val();
    const dFim = $('#dataFim').val();

    $.ajax({
        url: 'requests.php',
        type: 'GET',
        data: {
            acao: 'produtividade_aviamentos', 
            dataInicio: dInicio,
            dataFim: dFim
        },
        dataType: 'json',
        beforeSend: function() {
            console.log("A consultar dados...");
        },
        success: function (data) {
            let linhasReposicao = '';
            let linhasConferencia = '';

            // Verifica se retornou um array e se não está vazio
            if (Array.isArray(data) && data.length > 0) {
                
                // Pega o objeto principal que contém as duas listas
                let dadosApi = data[0]; 
                
                // ==========================================
                // 1. MONTAR TABELA DE REPOSIÇÃO
                // ==========================================
                let listaReposicao = dadosApi["01-ProdutividadeReposicao"];
                
                if (listaReposicao && listaReposicao.length > 0) {
                    listaReposicao.forEach((item, index) => {
                        linhasReposicao += `
                            <tr>
                                <td class="text-center"><strong>${index + 1}º</strong></td>
                                <td>${item.usuario}</td>
                                <td class="text-center">${item["qtd.Kit Reposto"]}</td>
                                <td class="text-center">${item["qtd Enderecos"]}</td>
                            </tr>`;
                    });
                } else {
                    linhasReposicao = '<tr><td colspan="4" class="text-center">Nenhum registro encontrado.</td></tr>';
                }

                // ==========================================
                // 2. MONTAR TABELA DE CONFERÊNCIA
                // ==========================================
                let listaConferencia = dadosApi["02-ProdutividadeConferente"];
                
                if (listaConferencia && listaConferencia.length > 0) {
                    listaConferencia.forEach((item, index) => {
                        // Nota: mantive a chave "Ops Coferidas" exatamente como está no seu JSON
                        linhasConferencia += `
                            <tr>
                                <td class="text-center"><strong>${index + 1}º</strong></td>
                                <td>${item.nomeUsuario}</td>
                                <td class="text-center">${item["Ops Coferidas"]}</td>
                            </tr>`;
                    });
                } else {
                    linhasConferencia = '<tr><td colspan="3" class="text-center">Nenhum registro encontrado.</td></tr>';
                }

            } else {
                // Caso o array global venha vazio
                linhasReposicao = '<tr><td colspan="4" class="text-center">Nenhum registro encontrado.</td></tr>';
                linhasConferencia = '<tr><td colspan="3" class="text-center">Nenhum registro encontrado.</td></tr>';
            }

            // Injeta as linhas no corpo (tbody) de cada respectiva tabela
            $('#table-reposicao tbody').html(linhasReposicao);
            $('#table-conferencia tbody').html(linhasConferencia);
        },
        error: function (xhr) {
            console.error("Erro na atualização:", xhr.responseText);
            $('#table-reposicao tbody').html('<tr><td colspan="4" class="text-center text-danger">Erro ao carregar dados.</td></tr>');
            $('#table-conferencia tbody').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar dados.</td></tr>');
        }
    });
}