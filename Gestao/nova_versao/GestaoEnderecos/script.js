$(document).ready(async () => {
    await ConsultarEnderecos();

    // Fica escutando a troca dos botões radio para esconder/mostrar as seções
    $('input[name="tipoInsercao"]').on('change', function() {
        const valorSelecionado = $(this).val();
        
        if (valorSelecionado === 'individual') {
            $('#section-massa').addClass('d-none');
            $('#section-individual').removeClass('d-none');
        } else {
            $('#section-individual').addClass('d-none');
            $('#section-massa').removeClass('d-none');
        }
    });
});

function abrirModalInserirEndereco() {
    $('#radioIndividual').prop('checked', true).trigger('change');
    $('#modalInserirEndereco input[type="text"]').val('');
    $('#modalInserirEndereco').modal('show');
}

async function salvarEnderecos() {
    const tipo = $('input[name="tipoInsercao"]:checked').val();
    let payloadEnvio = { acao: '', dados: {} };

    if (tipo === 'individual') {
        const rua = $('#indRua').val();
        const quadra = $('#indQuadra').val();
        const posicao = $('#indPosicao').val();
        
        if (!rua || !quadra || !posicao) {
            alert("Preencha todos os campos do endereço individual.");
            return;
        }

        payloadEnvio.acao = 'inserir_endereco';
        payloadEnvio.dados = { rua, quadra, posicao };

    } else {
        const ruaInicial = $('#masRuaIni').val();
        const ruaFinal = $('#masRuaFim').val();
        const quadraInicial = $('#masQuadraIni').val();
        const quadraFinal = $('#masQuadraFim').val();
        const posicaoInicial = $('#masPosicaoIni').val();
        const posicaoFinal = $('#masPosicaoFim').val();
        
        if (!ruaInicial || !ruaFinal || !quadraInicial || !quadraFinal || !posicaoInicial || !posicaoFinal) {
            alert("Preencha todos os campos (Rua, Quadra e Posição - Inicial e Final) para a inserção em massa.");
            return;
        }

        payloadEnvio.acao = 'inserir_endereco_massa';
        payloadEnvio.dados = { ruaInicial, quadraInicial, posicaoInicial, ruaFinal, quadraFinal, posicaoFinal };
    }

    try {
        const response = await $.ajax({
            url: 'requests.php',
            type: 'POST',
            contentType: 'application/json', 
            data: JSON.stringify(payloadEnvio), 
            dataType: 'json' 
        });

        let dadosResposta = Array.isArray(response) ? response[0] : response;

        if (dadosResposta && dadosResposta.status === true) {
            alert(dadosResposta.Mensagem || dadosResposta.mensagem || "Salvo com sucesso!");
            $('#modalInserirEndereco').modal('hide');
            await ConsultarEnderecos();
        } else {
            alert("Atenção: " + (dadosResposta?.Mensagem || dadosResposta?.mensagem || "A API recusou o salvamento."));
        }

    } catch (error) {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao comunicar com o servidor.');
    }
}

const ConsultarEnderecos = async () => {
    try {
        $('#loadingModal').modal('show');
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: 'ConsultarEnderecos' },
        });

        Tabela(response);
    } catch (error) {
        console.error('Erro ao consultar serviço:', error);
    } finally {
        setTimeout(() => { $('#loadingModal').modal('hide'); }, 500);
    }
};

function Tabela(dados) {
    if ($.fn.DataTable.isDataTable('#table-metas')) {
        $('#table-metas').DataTable().destroy();
    }

    // Desmarca o checkbox do cabeçalho ao recarregar a tabela
    $('#checkAllFiltro').prop('checked', false);

    var table = $('#table-metas').DataTable({
        data: dados, 
        searching: true, 
        paging: true, 
        lengthChange: false,
        info: false,
        pageLength: 10,
        dom: '<"top">rt<"bottom"p><"clear">', 
        columns: [
            { data: 'endereco' }, // Col 0
            { data: 'rua' },      // Col 1
            { data: 'quadra' },   // Col 2
            { data: 'posicao' },  // Col 3
            {                     // Col 4 (Checkbox Imprimir)
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {
                    let itemEncoded = encodeURIComponent(JSON.stringify(row));
                    return `<input class="form-check-input check-imprimir border-secondary" type="checkbox" data-item="${itemEncoded}">`;
                }
            }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        }
    });

    // --- 1. LÓGICA DOS FILTROS INDIVIDUAIS POR COLUNA ---
    $('#filtroEndereco, #filtroRua, #filtroQuadra, #filtroPosicao').off('keyup');

    $('#filtroEndereco').on('keyup', function () { table.column(0).search(this.value).draw(); });
    $('#filtroRua').on('keyup', function () { table.column(1).search(this.value).draw(); });
    $('#filtroQuadra').on('keyup', function () { table.column(2).search(this.value).draw(); });
    $('#filtroPosicao').on('keyup', function () { table.column(3).search(this.value).draw(); });

    // --- 2. LÓGICA DO CHECKBOX "SELECIONAR TODOS" ---
    
    // Quando clica no checkbox do cabeçalho
    $('#checkAllFiltro').off('change').on('change', function() {
        var isChecked = $(this).is(':checked');
        
        // Pega apenas as linhas que estão visíveis após os filtros aplicados
        var linhasFiltradas = table.rows({ search: 'applied' }).nodes();
        
        // Marca ou desmarca os checkboxes dentro dessas linhas
        $('input.check-imprimir', linhasFiltradas).prop('checked', isChecked);
    });

    // Se o usuário desmarcar apenas UM item da lista, desmarca o cabeçalho automaticamente
    $('#table-metas tbody').off('change', '.check-imprimir').on('change', '.check-imprimir', function() {
        if (!$(this).is(':checked')) {
            $('#checkAllFiltro').prop('checked', false);
        }
    });
}
// Transformamos a função em async para podermos usar o await
async function imprimirSelecionados() {
    let itensParaImprimir = [];

    // Pega os itens selecionados
    $('#table-metas').DataTable().$('input.check-imprimir:checked').each(function() {
        let dadosItem = JSON.parse(decodeURIComponent($(this).attr('data-item')));
        itensParaImprimir.push(dadosItem);
    });

    if (itensParaImprimir.length === 0) {
        alert("Atenção: Nenhum endereço foi selecionado para impressão.");
        return;
    }

    // --- NOVIDADE: Mostra o modal de loading para o usuário não achar que travou ---
    $('#loadingModal').modal('show');

    // 1. Esconde a área da tabela e filtros
    $('.div-metas').addClass('d-none');
    
    // 2. Mostra o container de cards e limpa o que tinha antes
    $('#container-cards').removeClass('d-none').empty();

    // --- ADICIONA O BOTÃO VOLTAR NO TOPO ---
    // A classe 'no-print' garante que ele não apareça na Zebra
    $('#container-cards').append(`
        <div class="w-100 mb-3 no-print text-start">
            <button type="button" class="btn btn-secondary shadow-sm" onclick="voltarParaTabela()">
                <i class="bi bi-arrow-left me-1"></i> Voltar para a Tabela
            </button>
        </div>
    `);

    // 3. Monta um card para cada item selecionado
    itensParaImprimir.forEach(item => {
        const qrData = encodeURIComponent(item.endereco);
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=${qrData}`;

        const cardHTML = `
            <div class="card card-etiqueta" style="border: 1px solid #000; background-color: #fff; margin-bottom: 10px;">
                <div class="card-body d-flex flex-row align-items-center justify-content-between p-1" style="height: 100%;">
                    
                    <div class="d-flex flex-column justify-content-center h-100" style="padding-left: 10px; width: calc(100% - 75px);">
                        <strong style="font-size: 4.4rem; color: #000; line-height: 1.5;">${item.endereco}</strong>
                    </div>

                    <div class="d-flex  align-items-center" style="width: 100px; height: 100px;">
                        <img class="img-qrcode" src="${qrUrl}" alt="QR Code" style="max-width: 100%; max-height: 100%;">
                    </div>
                    
                </div>
            </div>
        `;
        
        $('#container-cards').append(cardHTML);
    });

    // --- NOVIDADE: Lógica de aguardar as imagens ---
    
    // Pega todas as imagens que acabamos de colocar na tela
    const imagens = $('#container-cards img.img-qrcode');
    const promessasDeCarregamento = [];

    // Para cada imagem, criamos um "vigia"
    imagens.each(function() {
        const img = this;
        
        // Se a imagem já carregou instantaneamente (as vezes acontece do cache do navegador), ignoramos
        if (!img.complete) {
            const promessa = new Promise((resolve) => {
                // Quando a imagem terminar de carregar, avisa que está "resolvida"
                img.onload = resolve;
                // Se der erro de internet (API do QR Code cair), também resolvemos para não travar a tela inteira
                img.onerror = resolve; 
            });
            promessasDeCarregamento.push(promessa);
        }
    });

    // O código vai pausar AQUI embaixo até que a última imagem termine de carregar
    await Promise.all(promessasDeCarregamento);

    // Oculta o modal de loading, pois tudo já carregou
    $('#loadingModal').modal('hide');

    // Um micro delay (100ms) só pro navegador terminar de pintar o modal de loading sumindo da tela
    setTimeout(() => {
        window.print();
    }, 100);
}

// Função ativada pelo botão "Voltar"
function voltarParaTabela() {
    // Esconde e limpa o container de cards
    $('#container-cards').addClass('d-none').empty();
    
    // Mostra a tabela e os filtros novamente
    $('.div-metas').removeClass('d-none');
}