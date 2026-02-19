// ============================================================================
// VARIÁVEIS GLOBAIS
// ============================================================================
let dadosOriginais = []; // Guarda a cópia fiel do banco de dados das OPs
let ordemAtual = { coluna: null, direcao: 'asc' }; // Guarda o estado da ordenação

// ============================================================================
// INICIALIZAÇÃO (Quando a página carrega)
// ============================================================================
$(document).ready(async () => {
    // 1. Configura os eventos de clique (ordenação) e digitação (filtro)
    configurarOrdenacao();
    configurarFiltros();
    
    // 2. Busca os dados iniciais da tela
    await Consultar_OP_requisicao();
    await CarregarSelectUsuarios();

    // ========================================================================
    // EVENTO: SELECIONAR USUÁRIO PARA ATRIBUIR OPs
    // ========================================================================
    $('#selectUsuarioAviamentador').on('change', function() {
        const matriculaSelecionada = $(this).val();
        const textoOpcao = $(this).find("option:selected").text();

        // Se voltou para a opção vazia, não faz nada
        if (!matriculaSelecionada) return;

        // Extrai apenas o nome do usuário (Ex: "1414 - LUIS" vira "LUIS")
        const partesTexto = textoOpcao.split(' - ');
        const nomeUsuario = partesTexto.length > 1 ? partesTexto[1].trim() : textoOpcao.trim();

        // Coleta todas as OPs marcadas
        const opsSelecionadas = [];
        $('.checkbox-selecao:checked').each(function() {
            opsSelecionadas.push($(this).val());
        });

        // Validação
        if (opsSelecionadas.length === 0) {
            alert("Por favor, selecione pelo menos uma OP na tabela antes de atribuir a um usuário.");
            $(this).val(''); // Volta a caixa para o padrão
            return;
        }

        // Exibe a mensagem de confirmação
        const confirmacao = confirm(`Deseja atribuir as ${opsSelecionadas.length} OP's selecionadas para esse Usuario:\n${nomeUsuario} ?`);

        // Executa a ação
        if (confirmacao) {
            atribuirOPsAoUsuario(matriculaSelecionada, nomeUsuario, opsSelecionadas);
        } else {
            $(this).val('');
        }
    });

    // ========================================================================
    // EVENTO: SELECIONAR TODOS OS CHECKBOXES
    // ========================================================================
    // 1. Clicou no checkbox do cabeçalho (Selecionar Todos)
    $('#table-metas').on('change', '#checkAllMetas', function() {
        const isChecked = $(this).prop('checked');
        $('.checkbox-selecao').prop('checked', isChecked);
    });

    // 2. Clicou em um checkbox individual na linha
    $('#table-metas').on('change', '.checkbox-selecao', function() {
        if (!$(this).prop('checked')) {
            // Se desmarcou um, tira o "Selecionar Todos"
            $('#checkAllMetas').prop('checked', false);
        } else {
            // Se marcou, verifica se todos estão marcados agora
            const totalCheckboxes = $('.checkbox-selecao').length;
            const marcados = $('.checkbox-selecao:checked').length;
            if (totalCheckboxes === marcados && totalCheckboxes > 0) {
                $('#checkAllMetas').prop('checked', true);
            }
        }
    });
});


// ============================================================================
// REQUISIÇÕES AJAX - OPs E ATRIBUIÇÕES
// ============================================================================

// Busca as OPs e preenche a tabela principal
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
        console.log("Dados recebidos (OPs):", dadosOriginais);
        
        // Renderiza a primeira vez
        aplicarFiltrosEOrdenacao();

    } catch (error) {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao comunicar com o servidor.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

// Dispara a ação de atribuir as OPs selecionadas a um usuário
async function atribuirOPsAoUsuario(matricula, nome, listaOPs) {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                acao: 'atribuir_op_aviamentador',
                dados: {
                    codMatricula: matricula,
                    nomeUsuario: nome,
                    arrayOPs: listaOPs,
                    empresa: '1' // <--- Adicione esta linha enviando a empresa
                }
            })
        });

        const resultado = Array.isArray(response) ? response[0] : response;

        if (resultado && resultado.status !== false) {
            alert(resultado.Mensagem || "OPs atribuídas com sucesso!");
            
            $('#selectUsuarioAviamentador').val('');
            $('.checkbox-selecao').prop('checked', false); 
            $('#checkAllMetas').prop('checked', false);

            // Atualiza a tabela de OPs
            await Consultar_OP_requisicao();
            
        } else {
            alert("Atenção: " + (resultado.Mensagem || "Não foi possível atribuir as OPs."));
            $('#selectUsuarioAviamentador').val('');
        }

    } catch (error) {
        console.error('Erro ao atribuir OPs:', error);
        
        // ADICIONE ESTA LINHA: Vai mostrar exatamente o que o PHP cuspiu de volta
        console.error('Resposta crua do PHP:', error.responseText); 
        
        alert('Erro ao tentar atribuir OPs ao usuário. Verifique o console.');
        $('#selectUsuarioAviamentador').val('');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


// ============================================================================
// REQUISIÇÕES AJAX - GERENCIAMENTO DE USUÁRIOS (MODAL E SELECT)
// ============================================================================

// Carrega o <select> no cabeçalho da página
async function CarregarSelectUsuarios() {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Usuarios',
                codEmpresa: '1'
            }
        });

        const select = $('#selectUsuarioAviamentador');
        select.empty();
        select.append('<option value="">Selecione um usuário...</option>');

        if (response && response.length > 0) {
            response.forEach(usuario => {
                const textoOpcao = `${usuario.codMatricula} - ${usuario.nomeUsuario}`;
                select.append(`<option value="${usuario.codMatricula}">${textoOpcao}</option>`);
            });
        }

        if ($.fn.select2) {
            select.select2({
                placeholder: "Selecione um usuário...",
                allowClear: true,
                width: 'resolve'
            });
        }
    } catch (error) {
        console.error('Erro ao carregar o select de usuários:', error);
    }
}

// Abre o modal e já carrega a lista de usuários na tabela dele
async function abrirModalInserirUsuario() {
    $('#modalInserirUsuario').modal('show');
    await Consultar_Usuarios();
}

// Busca a lista de usuários para preencher a tabela dentro do modal
async function Consultar_Usuarios() {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Usuarios',
                codEmpresa: '1'
            },
        });

        const tbody = $('#table-usuarios-aviamentadores tbody');
        tbody.empty(); 

        if (response && response.length > 0) {
            response.forEach(usuario => {
                const tr = `
                    <tr>
                        <td><strong>${usuario.codMatricula}</strong></td>
                        <td class="text-start">${usuario.nomeUsuario}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="excluirUsuario('${usuario.codMatricula}')" title="Excluir Usuário">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(tr);
            });
        } else {
            tbody.html('<tr><td colspan="3" class="text-center text-muted py-3">Nenhum usuário encontrado.</td></tr>');
        }
    } catch (error) {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao comunicar com o servidor.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

// Adiciona um novo usuário (Ação do botão dentro do modal)
async function salvarNovoUsuario() {
    const matricula = $('#inputNovaMatricula').val();
    
    if (!matricula) {
        alert("Por favor, digite uma matrícula antes de adicionar.");
        $('#inputNovaMatricula').focus();
        return;
    }
    
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                acao: 'inserir_usuario',
                dados: { codMatricula: matricula }
            })
        });

        const resultado = response[0] || response;

        if (resultado && resultado.status === true) {
            alert(resultado.Mensagem || "Usuário inserido com sucesso!");
            $('#inputNovaMatricula').val('');
            
            // Atualiza a tabela do modal e o select do cabeçalho
            await Consultar_Usuarios();
            await CarregarSelectUsuarios();
        } else {
            alert("Atenção: " + (resultado.Mensagem || "Erro ao inserir."));
        }
    } catch (error) {
        console.error('Erro na requisição AJAX (salvarNovoUsuario):', error);
        alert('Erro ao comunicar com o servidor.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

// Exclui um usuário (Ação do botão de lixeira dentro do modal)
async function excluirUsuario(matricula) {
    if(!confirm(`Tem certeza que deseja excluir o usuário de matrícula ${matricula}?`)) return;
    
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                acao: 'excluir_usuario',
                dados: { codMatricula: matricula }
            })
        });

        const resultado = response[0] || response;

        if (resultado && resultado.status !== false) {
            alert(resultado.Mensagem || "Usuário excluído com sucesso!");
            // Atualiza a tabela do modal e o select do cabeçalho
            await Consultar_Usuarios();
            await CarregarSelectUsuarios();
        } else {
            alert("Atenção: " + (resultado.Mensagem || "Não foi possível excluir."));
        }
    } catch (error) {
        console.error('Erro ao excluir usuário:', error);
        alert('Erro ao comunicar com o servidor.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


// ============================================================================
// LÓGICA DE TABELA - ORDENAÇÃO E FILTROS
// ============================================================================

function configurarFiltros() {
    $('.search-input').on('input', function() {
        aplicarFiltrosEOrdenacao();
    });
    $('.search-input').on('click', function(e) {
        e.stopPropagation();
    });
}

function configurarOrdenacao() {
    $('.sortable').on('click', function(e) {
        if (e.target.tagName === 'INPUT') return;

        const propriedade = $(this).data('prop');
        
        if (ordemAtual.coluna === propriedade) {
            ordemAtual.direcao = ordemAtual.direcao === 'asc' ? 'desc' : 'asc';
        } else {
            ordemAtual.coluna = propriedade;
            ordemAtual.direcao = 'asc';
        }

        $('.sortable').removeClass('sort-asc sort-desc');
        $(this).addClass(ordemAtual.direcao === 'asc' ? 'sort-asc' : 'sort-desc');

        aplicarFiltrosEOrdenacao();
    });
}

function aplicarFiltrosEOrdenacao() {
    let listaProcessada = [...dadosOriginais];

    // Filtros
    $('.search-input').each(function() {
        const termo = $(this).val().toLowerCase().trim();
        const prop = $(this).closest('th').data('prop');

        if (termo && prop) {
            listaProcessada = listaProcessada.filter(item => {
                const valorCampo = (item[prop] ?? '').toString().toLowerCase();
                return valorCampo.includes(termo);
            });
        }
    });

    // Ordenação
    if (ordemAtual.coluna) {
        const prop = ordemAtual.coluna;
        const dir = ordemAtual.direcao;

        listaProcessada.sort((a, b) => {
            let valA = a[prop] ?? "";
            let valB = b[prop] ?? "";

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

    renderizarTabela(listaProcessada);
}

// ============================================================================
// LÓGICA DE TABELA - RENDERIZAÇÃO HTML
// ============================================================================

function renderizarTabela(dados) {
    const tbody = $('#table-metas tbody');
    const lblTotal = $('#lblTotalPecas'); 
    
    tbody.empty();
    lblTotal.text('0'); 
    $('#checkAllMetas').prop('checked', false); // Reseta o checkbox master ao renderizar

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
            const listaIds = row.requisicoes.map(r => r.requisicoes || r.ID_REQUISICAO).join(' / '); 
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

        // --- 2. Conteúdo Interno ---
        let conteudoInterno = "";
if (temRequisicoes) {
            const linhasInternas = row.requisicoes.map(req => {
                const numReq = req.requisicoes || req.ID_REQUISICAO;
                
                // 1. Pega o separador (se não vier no 'req', pega do 'row.nomeUsuario' da linha pai)
                const nomeSeparador = req.separador || row.nomeUsuario || '';
                
                // 2. Transforma o texto para um formato seguro de URL (ex: "Luis Silva" vira "Luis%20Silva")
                const parametroSeparador = nomeSeparador ? `&separador=${encodeURIComponent(nomeSeparador)}` : '';

                // 3. Monta o link anexando o parâmetro codificado
                const linkRequisicao = numReq 
                    ? `<a href="ExplodirRequisicao/index.php?requisicao=${numReq}${parametroSeparador}" class="text-primary font-weight-bold" style="text-decoration: underline;">${numReq}</a>` 
                    : '-';

                return `
                <tr>
                    <td>${linkRequisicao}</td>
                    <td class="text-center">
                        <span class="badge ${req.SITUACAO_REQUISICAO === 'BAIXADA' ? 'badge-success' : 'badge-warning'}">
                            ${req.SITUACAO_REQUISICAO || '-'}
                        </span>
                    </td>
                    <td class="text-center">${req.seqRoteiro || '-'}</td>
                    <td class="text-center">${req.nome || '-'}</td>                    
                    <td class="text-center">${req.nomeNatEstoque || '-'}</td>
                </tr>`;
            }).join('');

            conteudoInterno = `
                <div style="width: 75%; margin: 0 auto;" class="tabela-centralizada-shadow mt-2 mb-2">
                    <table class="table table-sm table-bordered mb-0" style="background-color: white; font-size: 0.85rem;">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" style="width: 15%">Requisicao.</th>
                                <th class="text-center" style="width: 20%">Situação</th>
                                <th class="text-center" style="width: 10%">FaseDestino..</th>
                                <th class="text-center" style="width: 10%">Desc. Fase Destino..</th>
                                <th class="text-center" style="width: 10%">Nat.Estoque</th>
                            </tr>
                        </thead>
                        <tbody>${linhasInternas}</tbody>
                    </table>
                </div>`;
        }

        // --- 3. Linha Principal ---
        const trPrincipal = `
            <tr>
                <td style="vertical-align: middle;"><strong>${row.numeroOP ?? '-'}</strong></td>
                <td style="vertical-align: middle;"><strong>${row.codProduto ?? '-'}</strong></td>
                <td style="vertical-align: middle;">${row.descricao ?? '-'}</td>
                <td style="vertical-align: middle;">${row.QtdPecas_x ?? '-'}</td>
                <td style="vertical-align: middle;">${row.prioridade ?? '-'}</td>
                <td style="vertical-align: middle;">${row.FaseAtual ?? '-'}</td>
                <td style="vertical-align: middle;">${row.dataBaixa_x ?? row.dataBaixa ?? '-'}</td>
                <td style="vertical-align: middle; text-align: left;">${btnAcao}</td>
                <td style="vertical-align: middle;">${row.nomeUsuario ?? '-'}</td>
                <td class="text-center align-middle">
                    <input class="form-check-input checkbox-selecao" type="checkbox" value="${row.numeroOP}">
                </td>
            </tr>`;

        // --- 4. Linha Filho ---
        if (temRequisicoes) {
            const trDetalhe = `
                <tr class="p-0">
                    <td colspan="${totalColunas}" class="p-0 border-0">
                        <div class="collapse undo-row-bg fundo-expandido" id="${idCollapse}">
                            ${conteudoInterno}
                        </div>
                    </td>
                </tr>`;
            tbody.append(trPrincipal);
            tbody.append(trDetalhe);
        } else {
            tbody.append(trPrincipal);
        }
    });

    lblTotal.text(acumuladorPecas.toLocaleString('pt-BR'));
}