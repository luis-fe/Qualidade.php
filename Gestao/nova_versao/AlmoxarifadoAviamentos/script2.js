// Variáveis Globais
let dadosOriginais = []; // Guarda a cópia fiel do banco de dados
let ordemAtual = { coluna: null, direcao: 'asc' }; // Guarda o estado da ordenação

$(document).ready(async () => {
    // 1. Configura os eventos de clique (ordenação) e digitação (filtro)
    configurarOrdenacao();
    configurarFiltros();
    
    // 2. Busca os dados
    await Consultar_OP_requisicao();
});

async function Consultar_OP_requisicao() {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_OP_requisicao',
                codEmpresa: '1'
            },
        });

        // SALVA OS DADOS NA VARIÁVEL "IMUTÁVEL"
        dadosOriginais = response || [];

        console.log("Dados recebidos:", dadosOriginais);
        
        // Renderiza a primeira vez (sem filtros, ordem padrão)
        aplicarFiltrosEOrdenacao();

    } catch (error) {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao comunicar com o servidor.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

// --- CONFIGURAÇÃO DOS FILTROS (DIGITAÇÃO) ---
function configurarFiltros() {
    // Evento 'input' dispara ao digitar, colar ou apagar texto
    $('.search-input').on('input', function() {
        aplicarFiltrosEOrdenacao();
    });
    
    // Evita que o clique no input dispare a ordenação da coluna
    $('.search-input').on('click', function(e) {
        e.stopPropagation();
    });
}

// --- CONFIGURAÇÃO DA ORDENAÇÃO (CLIQUE NO TH) ---
function configurarOrdenacao() {
    $('.sortable').on('click', function(e) {
        // Se clicar no input, não faz nada (já tratado no stopPropagation, mas garantindo)
        if (e.target.tagName === 'INPUT') return;

        const propriedade = $(this).data('prop');
        
        // Define direção
        if (ordemAtual.coluna === propriedade) {
            ordemAtual.direcao = ordemAtual.direcao === 'asc' ? 'desc' : 'asc';
        } else {
            ordemAtual.coluna = propriedade;
            ordemAtual.direcao = 'asc';
        }

        // Atualiza visual das setas
        $('.sortable').removeClass('sort-asc sort-desc');
        $(this).addClass(ordemAtual.direcao === 'asc' ? 'sort-asc' : 'sort-desc');

        // Reaplica tudo (Filtros + Nova Ordem)
        aplicarFiltrosEOrdenacao();
    });
}

// --- FUNÇÃO CENTRAL: FILTRA E ORDENA ---
function aplicarFiltrosEOrdenacao() {
    // 1. Começa com uma cópia dos dados originais
    let listaProcessada = [...dadosOriginais];

    // 2. APLICA OS FILTROS (Loop por cada input de pesquisa)
    $('.search-input').each(function() {
        const termo = $(this).val().toLowerCase().trim();
        const prop = $(this).closest('th').data('prop'); // Pega o data-prop do TH pai

        // Se tiver texto digitado e a coluna tiver uma propriedade mapeada
        if (termo && prop) {
            listaProcessada = listaProcessada.filter(item => {
                // Pega o valor, trata nulo, converte pra string e compara
                const valorCampo = (item[prop] ?? '').toString().toLowerCase();
                return valorCampo.includes(termo);
            });
        }
    });

    // 3. APLICA A ORDENAÇÃO (Se houver coluna selecionada)
    if (ordemAtual.coluna) {
        const prop = ordemAtual.coluna;
        const dir = ordemAtual.direcao;

        listaProcessada.sort((a, b) => {
            let valA = a[prop];
            let valB = b[prop];

            if (valA == null) valA = "";
            if (valB == null) valB = "";

            // Tenta detectar número
            const isNumber = !isNaN(parseFloat(valA)) && isFinite(valA) && !isNaN(parseFloat(valB)) && isFinite(valB);
            
            if (isNumber) {
                valA = parseFloat(valA);
                valB = parseFloat(valB);
            } else {
                valA = valA.toString().toLowerCase();
                valB = valB.toString().toLowerCase();
            }

            if (valA < valB) return dir === 'asc' ? -1 : 1;
            if (valA > valB) return dir === 'asc' ? 1 : -1;
            return 0;
        });
    }

    // 4. MANDA RENDERIZAR O RESULTADO FINAL
    renderizarTabela(listaProcessada);
}

// --- FUNÇÃO DE RENDERIZAÇÃO (Mantida igual) ---
// --- FUNÇÃO DE RENDERIZAÇÃO ATUALIZADA ---
function renderizarTabela(dados) {
    const tbody = $('#table-metas tbody');
    const lblTotal = $('#lblTotalPecas'); 
    tbody.empty();
    lblTotal.text('0'); 

    const totalColunas = $('#table-metas thead th').length; 

    if (dados && dados.status === false) {
        tbody.append(`<tr><td colspan="${totalColunas}" class="text-center text-danger">Erro: ${dados.message}</td></tr>`);
        return;
    }

    if (!dados || !Array.isArray(dados) || dados.length === 0) {
        tbody.append(`<tr><td colspan="${totalColunas}" class="text-center">Nenhum registro encontrado</td></tr>`);
        return;
    }

    let acumuladorPecas = 0; 

    dados.forEach((row, index) => {
        let qtd = parseFloat(row.QtdPecas_x) || 0;
        acumuladorPecas += qtd;

        const idCollapse = `collapseReq_${index}`;
        const temRequisicoes = row.requisicoes && Array.isArray(row.requisicoes) && row.requisicoes.length > 0;

        // --- 1. Botão de Ação + Resumo de IDs ---
        let btnAcao = "";
        
        if (temRequisicoes) {
            // Cria a string apenas com os números e barras
            const listaIds = row.requisicoes
                .map(r => r.requisicoes || r.ID_REQUISICAO) 
                .join(' / '); 

            // MUDANÇA AQUI: justify-content-start (alinha à esquerda) e removido o "..."
            btnAcao = `
                <div class="d-flex align-items-center justify-content-start">
                    <button class="btn btn-sm btn-info" type="button" data-toggle="collapse" data-target="#${idCollapse}" aria-expanded="false" aria-controls="${idCollapse}" title="Ver Detalhes">
                        <i class="bi bi-list-ul"></i>
                    </button>
                    <span class="ml-2 text-muted text-nowrap" style="font-size: 0.75rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis;">
                        ${listaIds}
                    </span>
                </div>`;
        } else {
            btnAcao = '<span class="text-muted small" style="font-size: 0.8em;">-</span>';
        }

        // --- 2. Conteúdo Interno (70% width) ---
        let conteudoInterno = "";
        if (temRequisicoes) {
            const linhasInternas = row.requisicoes.map(req => `
                <tr>
                    <td class="text-center">${req.requisicoes || req.ID_REQUISICAO || '-'}</td>
                    <td class="text-center">
                        <span class="badge ${req.SITUACAO_REQUISICAO === 'BAIXADA' ? 'badge-success' : 'badge-warning'}">
                            ${req.SITUACAO_REQUISICAO || '-'}
                        </span>
                    </td>
                    <td class="text-center">${req.seqRoteiro || '-'}</td>
                    <td class="text-center">${req.nome || '-'}</td>                    
                    <td class="text-center">${req.nomeNatEstoque || '-'}</td>

                </tr>
            `).join('');

            conteudoInterno = `
                <div style="width: 75%; margin: 0 auto;" class="tabela-centralizada-shadow mt-2 mb-2">
                    <table class="table table-sm table-bordered mb-0" style="background-color: white; font-size: 0.85rem;">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" style="width: 15%">Requisicao.</th>
                                <th class="text-center" style="width: 20%">Situação</th>
                                <th class="text-center" style="width: 10%">FaseReq..</th>
                                <th class="text-center" style="width: 10%">Nome..</th>
                                <th class="text-center" style="width: 10%">Nat.Estoque</th>
                            </tr>
                        </thead>
                        <tbody>${linhasInternas}</tbody>
                    </table>
                </div>
            `;
        }

        // --- 3. Linha Principal ---
        // MUDANÇA AQUI: text-align: left na última TD
        const trPrincipal = `
            <tr>
                <td style="vertical-align: middle;"><strong>${row.numeroOP ?? '-'}</strong></td>
                <td style="vertical-align: middle;"><strong>${row.codProduto ?? '-'}</strong></td>
                <td style="vertical-align: middle;">${row.descricao ?? '-'}</td>
                <td style="vertical-align: middle;">${row.QtdPecas_x ?? '-'}</td>
                <td style="vertical-align: middle;">${row.prioridade ?? '-'}</td>
                <td style="vertical-align: middle;">${row.dataBaixa_x ?? row.dataBaixa ?? '-'}</td>
                <td style="vertical-align: middle; text-align: left;">${btnAcao}</td>
            </tr>
        `;

        // --- 4. Linha Filho ---
        if (temRequisicoes) {
            const trDetalhe = `
                <tr class="p-0">
                    <td colspan="${totalColunas}" class="p-0 border-0">
                        <div class="collapse undo-row-bg fundo-expandido" id="${idCollapse}">
                            ${conteudoInterno}
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(trPrincipal);
            tbody.append(trDetalhe);
        } else {
            tbody.append(trPrincipal);
        }
    });

    lblTotal.text(acumuladorPecas.toLocaleString('pt-BR'));
}