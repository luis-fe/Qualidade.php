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
            acao: 'produtividade_aviamentos', // Certifique-se que está minúsculo como no novo requests.php
            dataInicio: dInicio,
            dataFim: dFim
        },
        dataType: 'json',
        beforeSend: function() {
            // Opcional: mostrar um pequeno indicador de que está a atualizar
            // Mas sem apagar a tabela toda para não "piscar" de forma agressiva
            console.log("A consultar dados...");
        },
        success: function (data) {
            let linhas = '';

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    linhas += `
                        <tr>
                            <td>${item.usuario}</td>
                            <td>${item["qtd.Kit Reposto"]}</td>
                            <td>${item["qtd Enderecos"]}</td>
                        </tr>`;
                });
            } else {
                linhas = '<tr><td colspan="3" class="text-center">Nenhum registro encontrado.</td></tr>';
            }

            $('#table-metas tbody').html(linhas);
        },
        error: function (xhr) {
            console.error("Erro na atualização:", xhr.responseText);
        }
    });
}