// ==========================================
// CONFIGURAÇÕES GLOBAIS E SONS (LATÊNCIA ZERO)
// ==========================================

let linhaParaExcluir = null;

// Variáveis para o áudio de alta performance
let audioCtx = null;
let bufferErro = null;
let bufferSucesso = null; 

// 1. Pré-carrega os áudios diretamente na memória RAM (Zero Delay)
async function carregarAudioNaMemoria() {
    try {
        window.AudioContext = window.AudioContext || window.webkitAudioContext;
        audioCtx = new AudioContext();
        
        // Faz o download e decodifica o som de ERRO
        const responseErro = await fetch('MenasagemErro.mp3');
        const arrayBufferErro = await responseErro.arrayBuffer();
        bufferErro = await audioCtx.decodeAudioData(arrayBufferErro);

        // Faz o download e decodifica o som de SUCESSO
        const responseSucesso = await fetch('MensagemCorrect.mp3');
        const arrayBufferSucesso = await responseSucesso.arrayBuffer();
        bufferSucesso = await audioCtx.decodeAudioData(arrayBufferSucesso);
        
    } catch (erro) {
        console.error("Erro ao pré-carregar áudios de alta performance:", erro);
    }
}

// Inicia o carregamento assim que o script é lido
carregarAudioNaMemoria();

// 2. Função de disparo imediato para ERRO
function tocarBipeErro() {
    if (!audioCtx || !bufferErro) return;
    if (audioCtx.state === 'suspended') audioCtx.resume();

    const fonte = audioCtx.createBufferSource();
    fonte.buffer = bufferErro;
    fonte.connect(audioCtx.destination);
    fonte.start(0); 
}

// 3. Função de disparo imediato para SUCESSO
function tocarBipeSucesso() {
    if (!audioCtx || !bufferSucesso) return;
    if (audioCtx.state === 'suspended') audioCtx.resume();

    const fonte = audioCtx.createBufferSource();
    fonte.buffer = bufferSucesso;
    fonte.connect(audioCtx.destination);
    fonte.start(0); 
}

// ==========================================
// MODO SCANNER: TRAVA DE FOCO
// ==========================================

// Garante que o input nunca perca o foco enquanto o modal principal estiver aberto
$('#inputQrCode').on('blur', function() {
    setTimeout(() => {
        // Só puxa o foco de volta se o modal da OP estiver visível E os modais de alerta estiverem fechados
        if ($('#modalItensOP').is(':visible') && 
            !$('#modalErroBipagem').hasClass('show') && 
            !$('#modalConfirmarExclusao').hasClass('show') &&
            !$('#modalSucesso').hasClass('show')) {
            
            $('#inputQrCode').focus();
        }
    }, 150); 
});

// Mantém o foco na tela inteira (se o cara clicar no fundo cinza da tela, volta pro input)
$('#modalItensOP').on('click', function(e) {
    if(e.target.id !== 'inputQrCode') {
        $('#inputQrCode').focus();
    }
});

// ==========================================
// INICIALIZAÇÃO DA PÁGINA E MODAL MATRÍCULA
// ==========================================
$(document).ready(async () => {
    
    // 1. Trava a tela e mostra o modal de matrícula logo que a página carrega
    $('#modalLoginMatricula').modal('show');
    
    // 2. Coloca o foco no input automaticamente para o usuário não precisar clicar
    setTimeout(() => {
        $('#inputMatriculaLogin').focus();
    }, 500);

    // 3. Captura a descrição e junta no título do modal (Tabela Principal)
    $('#table-metas tbody').on('click', 'button.btn-abrir-modal', function() {
        let numeroOP = $(this).attr('data-op');
        let descricao = $(this).attr('data-descricao'); 
        
        $('#spanNumeroOP').text(`${numeroOP} - ${descricao}`); 
        ConsultarFilaConferencia_itens(numeroOP); 
    });

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

    let table = $('#table-metas').DataTable();

    $('#table-metas tbody').on('click', 'button.btn-detalhes', function() {
        let tr = $(this).closest('tr');
        let row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(formatarDetalhes(row.data())).show();
            tr.addClass('shown');
        }
    });
}); // <--- O $(document).ready FECHA AQUI


// ==========================================
// LÓGICA DO MODAL DE IDENTIFICAÇÃO (MATRÍCULA)
// ==========================================
$('#inputMatriculaLogin').on('blur keypress', async function(e) {
    // Se for evento de teclado, só faz algo se for a tecla Enter (13)
    if (e.type === 'keypress' && e.which !== 13) return; 
    if (e.type === 'keypress') e.preventDefault();

    let matricula = $(this).val().trim();
    let labelNome = $('#labelNomeOperador');
    let btnAcessar = $('#btnAcessarSistema');

    if (matricula === '') {
        labelNome.text('Aguardando digitação...').removeClass('text-dark text-danger fw-bold').addClass('text-muted fst-italic');
        btnAcessar.prop('disabled', true);
        return;
    }

    // Feedback visual de carregamento
    labelNome.html('<i class="bi bi-hourglass-split"></i> Buscando...').removeClass('text-danger text-muted').addClass('text-primary');
    btnAcessar.prop('disabled', true);

    try {
        // 1. Faz a requisição real para a sua API via requests.php
        const response = await $.ajax({
            url: 'requests.php',
            type: 'GET',
            dataType: 'json',
            data: { 
                acao: 'Consultar_Usuarios',
                codEmpresa: '1' // <--- FALTAVA ISSO AQUI!
            }
        });
        
        let nomeEncontrado = null;

        // 2. Verifica se a API retornou um Array de usuários válido
        if (response && Array.isArray(response)) {
            
            // 3. Procura na lista o usuário com a matrícula exata
            // Convertendo ambos para String para garantir que "1" bata com 1
            const usuario = response.find(u => String(u.codMatricula) === String(matricula));
            
            if (usuario) {
                nomeEncontrado = usuario.nomeUsuario;
            }
        }

        // 4. Valida se achou o nome e atualiza a interface
        if (nomeEncontrado) {
            labelNome.text(nomeEncontrado).removeClass('text-primary text-muted fst-italic').addClass('text-dark fw-bold');
            btnAcessar.prop('disabled', false).focus(); // Foca no botão Acessar
        } else {
            labelNome.text('Matrícula não encontrada!').removeClass('text-primary text-muted').addClass('text-danger fw-bold');
            btnAcessar.prop('disabled', true);
        }

    } catch (error) {
        console.error("Erro ao validar matrícula:", error);
        labelNome.text('Erro na conexão com a API.').removeClass('text-primary text-muted').addClass('text-danger fw-bold');
    }
});

// Ação final: Quando clicar no botão "Acessar Sistema"
$('#btnAcessarSistema').on('click', async function() {
    let matriculaFinal = $('#inputMatriculaLogin').val();
    let nomeFinal = $('#labelNomeOperador').text();
    
    // Guarda em variável global
    window.usuarioAtivo = {
        matricula: matriculaFinal,
        nome: nomeFinal
    };

    // ====================================================
    // PREENCHE O CABEÇALHO PRINCIPAL
    // ====================================================
    let primeiroNome = nomeFinal.split(' ')[0]; 
    
    $('#header-nome-usuario').text(primeiroNome);
    $('#header-matricula-usuario').text('Mat: ' + matriculaFinal);
    $('#info-usuario-logado').removeClass('d-none'); 

    // ====================================================
    // PREENCHE O CABEÇALHO DO MODAL DA OP (NOVO)
    // ====================================================
    $('#modalOpNomeUsuario').text(nomeFinal); // Aqui deixei o nome completo
    $('#modalOpMatriculaUsuario').text('(' + matriculaFinal + ')');
    // ====================================================

    // Fecha a trava e carrega os dados da tabela
    $('#modalLoginMatricula').modal('hide');
    await ConsultarFilaConferencia();
});

// ==========================================
// FUNÇÕES DE UTILIDADE E FORMATAÇÃO
// ==========================================

function converterParaFloat(valor) {
    if (valor === null || valor === undefined || valor === '') return 0;
    if (typeof valor === 'number') return valor; 

    let texto = valor.toString().trim();
    texto = texto.replace(/\./g, ''); 
    texto = texto.replace(',', '.');  

    let numero = parseFloat(texto);
    return isNaN(numero) ? 0 : numero;
}

function formatarParaPtBr(valor) {
    if (isNaN(valor) || valor === null) return "0";
    return valor.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
}

// ==========================================
// FUNÇÕES DE TELA E CÁLCULO DE KITS
// ==========================================

function formatarDetalhes(row) {
    let saldoLimpo = converterParaFloat(row.estoqueAtual); 
    let granelInicial = saldoLimpo >= 1 ? 1 : 0;
    let preparadoInicial = granelInicial; 
    let aPrepararInicial = saldoLimpo - preparadoInicial;

    let nomeSeguro = (row.nome || '').toString().replace(/"/g, '&quot;');
    let fornecedorSeguro = (row.fornencedorPreferencial || '').toString().replace(/"/g, '&quot;');
    let codEditadoSeguro = (row.codEditado_y || row.CodComponente).toString().replace(/"/g, '&quot;');
    let unidadeSegura = (row.unidadeMedida || '').toString().replace(/"/g, '&quot;'); 

    return `
        <div class="p-3 border-start border-primary border-4 bg-light">
            <h6 class="fw-bold text-primary mb-3">Configurações do Material: ${row.codEditado_y || ''}</h6>
            
            <div class="row g-2 align-items-stretch">
                
                <div class="col-md-2 d-flex flex-column justify-content-center pe-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">Saldo</span>
                        <span id="saldo_original_${row.CodComponente}" data-saldo="${saldoLimpo}" class="fw-bold text-danger fs-5">
                            ${formatarParaPtBr(saldoLimpo)}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">Preparado</span>
                        <span id="preparado_display_${row.CodComponente}" class="fw-bold text-primary fs-5">
                            ${formatarParaPtBr(preparadoInicial)}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top border-secondary-subtle pt-1 mt-1">
                        <span class="small fw-bold text-muted">A Preparar</span>
                        <span id="a_preparar_display_${row.CodComponente}" class="fw-bold text-success fs-5">
                            ${formatarParaPtBr(aPrepararInicial)}
                        </span>
                    </div>
                </div>

                <div class="col-md-2">
                    <fieldset class="border border-secondary-subtle rounded p-2 pt-0 h-100 bg-white shadow-sm">
                        <legend class="float-none w-auto px-2 fw-bold text-primary mb-0" style="font-size: 0.8rem;">Kit 1</legend>
                        <div class="row g-1">
                            <div class="col-6">
                                <label class="form-label mb-1 text-muted" style="font-size: 0.75rem; font-weight: 600;">Tam Kit</label>
                                <input type="number" step="any" id="kit1_tam_${row.CodComponente}" class="form-control form-control-sm" placeholder="Tam" oninput="calcularSaldoEnderecado('${row.CodComponente}')">
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1 text-muted" style="font-size: 0.75rem; font-weight: 600;">Qtd Kit</label>
                                <input type="number" step="any" id="kit1_qtd_${row.CodComponente}" class="form-control form-control-sm" placeholder="Qtd" oninput="calcularSaldoEnderecado('${row.CodComponente}')">
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="border border-secondary-subtle rounded p-2 pt-0 h-100 bg-white shadow-sm">
                        <legend class="float-none w-auto px-2 fw-bold text-primary mb-0" style="font-size: 0.8rem;">Kit 2</legend>
                        <div class="row g-1">
                            <div class="col-6">
                                <label class="form-label mb-1 text-muted" style="font-size: 0.75rem; font-weight: 600;">Tam Kit</label>
                                <input type="number" step="any" id="kit2_tam_${row.CodComponente}" class="form-control form-control-sm" placeholder="Tam" oninput="calcularSaldoEnderecado('${row.CodComponente}')">
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1 text-muted" style="font-size: 0.75rem; font-weight: 600;">Qtd Kit</label>
                                <input type="number" step="any" id="kit2_qtd_${row.CodComponente}" class="form-control form-control-sm" placeholder="Qtd" oninput="calcularSaldoEnderecado('${row.CodComponente}')">
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="border border-secondary-subtle rounded p-2 pt-0 h-100 bg-white shadow-sm">
                        <legend class="float-none w-auto px-2 fw-bold text-primary mb-0" style="font-size: 0.8rem;">Kit 3</legend>
                        <div class="row g-1">
                            <div class="col-6">
                                <label class="form-label mb-1 text-muted" style="font-size: 0.75rem; font-weight: 600;">Tam Kit</label>
                                <input type="number" step="any" id="kit3_tam_${row.CodComponente}" class="form-control form-control-sm" placeholder="Tam" oninput="calcularSaldoEnderecado('${row.CodComponente}')">
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1 text-muted" style="font-size: 0.75rem; font-weight: 600;">Qtd Kit</label>
                                <input type="number" step="any" id="kit3_qtd_${row.CodComponente}" class="form-control form-control-sm" placeholder="Qtd" oninput="calcularSaldoEnderecado('${row.CodComponente}')">
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-2 d-flex flex-column justify-content-end pb-2">
                    <label class="form-label small fw-bold mb-1">Granel (Padrão 1)</label>
                    <input type="number" step="any" id="granel_${row.CodComponente}" class="form-control form-control-sm shadow-sm" value="${granelInicial}" oninput="calcularSaldoEnderecado('${row.CodComponente}')">
                </div>

                <div class="col-md-2 d-flex flex-column justify-content-end pb-2">
                    <button class="btn btn-sm btn-success w-100 shadow-sm" 
                            data-cod="${codEditadoSeguro}" 
                            data-nome="${nomeSeguro}" 
                            data-forn="${fornecedorSeguro}"
                            data-unidade="${unidadeSegura}"
                            onclick="salvarConfigKit('${row.CodComponente}', this)" 
                            style="height: 31px;">
                        <i class="bi bi-printer-fill me-1"></i> Imprimir Kits
                    </button>
                </div>

            </div>
        </div>
    `;
}

function calcularSaldoEnderecado(cod) {
    let spanSaldo = $(`#saldo_original_${cod}`);
    let spanPreparado = $(`#preparado_display_${cod}`);
    let spanAPreparar = $(`#a_preparar_display_${cod}`);

    let saldoOriginal = converterParaFloat(spanSaldo.attr('data-saldo'));

    let k1_t = converterParaFloat($(`#kit1_tam_${cod}`).val());
    let k1_q = converterParaFloat($(`#kit1_qtd_${cod}`).val());
    let k2_t = converterParaFloat($(`#kit2_tam_${cod}`).val());
    let k2_q = converterParaFloat($(`#kit2_qtd_${cod}`).val());
    let k3_t = converterParaFloat($(`#kit3_tam_${cod}`).val());
    let k3_q = converterParaFloat($(`#kit3_qtd_${cod}`).val());
    let granel = converterParaFloat($(`#granel_${cod}`).val());

    let totalPreparado = (k1_t * k1_q) + (k2_t * k2_q) + (k3_t * k3_q) + granel;
    let aPreparar = saldoOriginal - totalPreparado;

    spanPreparado.text(formatarParaPtBr(totalPreparado));
    spanAPreparar.text(formatarParaPtBr(aPreparar));

    spanAPreparar.removeClass('text-success text-secondary text-danger');

    if (aPreparar < 0) {
        spanAPreparar.addClass('text-danger'); 
    } else if (aPreparar === 0) {
        spanAPreparar.addClass('text-secondary'); 
    } else {
        spanAPreparar.addClass('text-success'); 
    }
}

// ==========================================
// FUNÇÕES DE IMPRESSÃO E MODAIS
// ==========================================

async function salvarConfigKit(codID, btnElement) {
    let spanSaldo = $(`#saldo_original_${codID}`);
    let saldoOriginal = converterParaFloat(spanSaldo.attr('data-saldo'));

    let k1_t = converterParaFloat($(`#kit1_tam_${codID}`).val());
    let k1_q = converterParaFloat($(`#kit1_qtd_${codID}`).val());
    let k2_t = converterParaFloat($(`#kit2_tam_${codID}`).val());
    let k2_q = converterParaFloat($(`#kit2_qtd_${codID}`).val());
    let k3_t = converterParaFloat($(`#kit3_tam_${codID}`).val());
    let k3_q = converterParaFloat($(`#kit3_qtd_${codID}`).val());
    let granel = converterParaFloat($(`#granel_${codID}`).val());

    let totalPreparado = (k1_t * k1_q) + (k2_t * k2_q) + (k3_t * k3_q) + granel;

    if (totalPreparado > saldoOriginal) {
        alert(`Atenção: A quantidade preparada (${formatarParaPtBr(totalPreparado)}) não pode ser maior que o Saldo disponível (${formatarParaPtBr(saldoOriginal)}).`);
        return;
    }

    let totalKitsParaImprimir = k1_q + k2_q + k3_q;
    if (totalKitsParaImprimir <= 0) {
        alert("Atenção: Você precisa preencher a quantidade de pelo menos 1 Kit (1, 2 ou 3) para imprimir as etiquetas.");
        return;
    }

    let codEditado = $(btnElement).attr('data-cod') || '';
    let descricao = $(btnElement).attr('data-nome') || '';
    let fornecedor = $(btnElement).attr('data-forn') || '';
    let unidade = $(btnElement).attr('data-unidade') || ''; 

    if (fornecedor.length > 12) {
        fornecedor = fornecedor.substring(0, 12);
    }

    let dataAtual = new Date().toLocaleDateString('pt-BR'); 

    let itensParaImprimir = [];

    function adicionarAoPrint(qtdKits, tamanhoDoKit) {
        if (qtdKits > 0 && tamanhoDoKit > 0) {
            for (let i = 0; i < qtdKits; i++) {
                itensParaImprimir.push({
                    codigo: codEditado,
                    descricao: descricao,
                    fornecedor: fornecedor,
                    tamanho: tamanhoDoKit,
                    unidade: unidade 
                });
            }
        }
    }

    adicionarAoPrint(k1_q, k1_t);
    adicionarAoPrint(k2_q, k2_t);
    adicionarAoPrint(k3_q, k3_t);

    if (itensParaImprimir.length === 0) {
        alert("Nenhum kit válido para impressão. Verifique se preencheu o Tamanho e a Quantidade.");
        return;
    }

    $('#loadingModal').modal('show');
    $('.div-metas').addClass('d-none');
    $('#container-cards').removeClass('d-none').empty();

    $('#container-cards').append(`
        <div class="w-100 mb-3 no-print text-start">
            <button type="button" class="btn btn-secondary shadow-sm" onclick="voltarParaTabela()">
                <i class="bi bi-arrow-left me-1"></i> Voltar para a Tabela
            </button>
        </div>
    `);

    itensParaImprimir.forEach(item => {
        const qrData = encodeURIComponent(`${item.codigo}-${item.tamanho}`);
        const qrUrl = `https://quickchart.io/qr?text=${qrData}&size=100&margin=0`;

        const cardHTML = `
            <div class="card card-etiqueta" style="border: none; background-color: #fff; margin: 0; padding: 0; border-radius: 0; width: 10.9cm; height: 2.8cm; page-break-after: always; box-sizing: border-box;">
                <div class="card-body d-flex flex-row align-items-center justify-content-between p-1" style="height: 100%; gap: 0.2cm; padding-left: 1cm !important; padding-right: 0.1cm !important;">
                    
                    <div class="d-flex flex-column justify-content-center" style="width: 6.5cm; overflow: hidden; font-family: Arial, sans-serif;">
                        
                        <strong style="font-size: 25px; color: #000; line-height: 1.1; white-space: nowrap; overflow: hidden;">${item.codigo}</strong>
                        
                        <strong style="font-size: 14px; color: #000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">${item.descricao}</strong>
                        <span style="font-size: 14px; color: #000; line-height: 1.2;">Forn: ${item.fornecedor}</span>
                        
                        <div class="d-flex justify-content-between align-items-end" style="margin-top: 2px;">
                            <strong style="font-size: 24px; color: #000; line-height: 1.1; white-space: nowrap;">Qtd.: ${item.tamanho} ${item.unidade}</strong>
                            <strong style="font-size: 12px; color: #000; line-height: 1.1;">Imp: ${dataAtual}</strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center align-items-center" style="width: 80px; height: 80px; min-width: 80px; flex-shrink: 0;">
                        <img class="img-qrcode" src="${qrUrl}" alt="QR Code" style="width: 80px; height: 80px; display: block;">
                    </div>
                    
                </div>
            </div>
        `;
        $('#container-cards').append(cardHTML);
    });

    const imagens = $('#container-cards img.img-qrcode');
    const promessasDeCarregamento = [];

    imagens.each(function() {
        const img = this;
        if (!img.complete) {
            const promessa = new Promise((resolve) => {
                img.onload = resolve;
                img.onerror = resolve; 
            });
            promessasDeCarregamento.push(promessa);
        }
    });

    await Promise.all(promessasDeCarregamento);

    $('#loadingModal').modal('hide');
    setTimeout(() => {
        window.print();
    }, 150);
}

async function imprimirSelecionados() {
    let itensParaImprimir = [];

    $('#table-metas').DataTable().$('input.check-imprimir:checked').each(function() {
        let dadosItem = JSON.parse(decodeURIComponent($(this).attr('data-item')));
        itensParaImprimir.push(dadosItem);
    });

    if (itensParaImprimir.length === 0) {
        alert("Atenção: Nenhum endereço foi selecionado para impressão.");
        return;
    }

    $('#loadingModal').modal('show');
    $('.div-metas').addClass('d-none');
    $('#container-cards').removeClass('d-none').empty();

    $('#container-cards').append(`
        <div class="w-100 mb-3 no-print text-start">
            <button type="button" class="btn btn-secondary shadow-sm" onclick="voltarParaTabela()">
                <i class="bi bi-arrow-left me-1"></i> Voltar para a Tabela
            </button>
        </div>
    `);

    itensParaImprimir.forEach(item => {
        const qrData = encodeURIComponent(item.endereco);
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${qrData}`;

        const cardHTML = `
            <div class="card card-etiqueta" style="border: none; background-color: #fff; margin: 0; padding: 0; border-radius: 0;">
                <div class="card-body d-flex flex-row align-items-center justify-content-start p-1" style="height: 100%; gap: 0.5cm; padding-left: 0.6cm !important;">
                    
                    <div class="d-flex flex-row align-items-center" style="gap: 0.5cm;">
                        
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span style="font-size: 2.5rem; color: #000; font-weight: bold;">Rua</span>
                            <strong style="font-size: 3.4rem; color: #000; line-height: 0.6;">${item.rua}</strong>
                        </div>

                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span style="font-size: 2.5rem; color: #000; font-weight: bold;">Quadra</span>
                            <strong style="font-size: 3.4rem; color: #000; line-height: 0.6;">${item.quadra}</strong>
                        </div>

                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span style="font-size: 2.5rem; color: #000; font-weight: bold;">Posicao</span>
                            <strong style="font-size: 3.4rem; color: #000; line-height: 0.6;">${item.posicao}</strong>
                        </div>

                    </div>

                    <div class="d-flex justify-content-center align-items-center" style="width: 100px; height: 100px; min-width: 100px; flex-shrink: 0;">
                        <img class="img-qrcode" src="${qrUrl}" alt="QR Code" style="width: 100px; height: 100px; display: block;">
                    </div>
                    
                </div>
            </div>
        `;
        $('#container-cards').append(cardHTML);
    });

    const imagens = $('#container-cards img.img-qrcode');
    const promessasDeCarregamento = [];

    imagens.each(function() {
        const img = this;
        if (!img.complete) {
            const promessa = new Promise((resolve) => {
                img.onload = resolve;
                img.onerror = resolve; 
            });
            promessasDeCarregamento.push(promessa);
        }
    });

    await Promise.all(promessasDeCarregamento);

    $('#loadingModal').modal('hide');
    setTimeout(() => {
        window.print();
    }, 100);
}

function voltarParaTabela() {
    $('#container-cards').addClass('d-none').empty();
    $('.div-metas').removeClass('d-none');

    if ($.fn.DataTable.isDataTable('#table-metas')) {
        let table = $('#table-metas').DataTable();
        
        table.rows().every(function () {
            if (this.child.isShown()) {
                this.child.hide(); 
                $(this.node()).removeClass('shown'); 
            }
        });
    }
}

function abrirModalInserirEndereco() {
    $('#radioIndividual').prop('checked', true).trigger('change');
    $('#modalInserirEndereco input[type="text"]').val('');
    $('#modalInserirEndereco').modal('show');
}

// ==========================================
// AJAX E RENDERIZAÇÃO DA TABELA DATATABLES
// ==========================================

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
            await ConsultarFilaConferencia();
        } else {
            alert("Atenção: " + (dadosResposta?.Mensagem || dadosResposta?.mensagem || "A API recusou o salvamento."));
        }

    } catch (error) {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao comunicar com o servidor.');
    }
}

const ConsultarFilaConferencia = async () => {
    try {
        $('#loadingModal').modal('show');
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: 'ConsultarFilaConferencia' },
        });

        Tabela(response);
    } catch (error) {
        console.error('Erro ao consultar serviço:', error);
    } finally {
        setTimeout(() => { $('#loadingModal').modal('hide'); }, 500);
    }
};

// ==========================================
// CARREGAR ITENS DESCONSIDERADOS E LÓGICA DO MODAL
// ==========================================
async function carregarItensDesconsiderados() {
    const tbody = $('#table-itens-desconsiderados tbody');

    // Coloca uma mensagem de "Carregando" na tabela para o usuário saber que está buscando
    tbody.html('<tr><td colspan="3" class="text-center text-muted p-3"><i class="bi bi-hourglass-split me-2"></i>Carregando itens...</td></tr>');

    try {
        const response = await $.ajax({
            url: 'requests.php',
            type: 'GET',
            dataType: 'json',
            data: { acao: 'get_obter_itens_configurados' }
        });

        tbody.empty(); // Limpa a mensagem de carregando

        // Se a API retornou dados e é um array com itens
        if (response && Array.isArray(response) && response.length > 0) {
            
            response.forEach(item => {
                let cod = item.codMaterialEdt || item.codMaterial || item.codigo || item.codProduto || '';
                let nome = item.nomeMaterial || item.descricao || item.nome || 'Sem descrição vinculada';

                const linha = `
                    <tr>
                        <td class="align-middle p-2 text-center">
                            <span class="fw-bold text-primary fs-6 span-codigo">${cod}</span>
                            <input type="text" class="form-control form-control-sm text-center fw-bold input-cod-desconsiderar d-none" value="${cod}" placeholder="Código (Ex: 100100...)">
                        </td>
                        <td class="align-middle p-2">
                            <span class="text-dark fw-bold label-nome-desconsiderar">${nome}</span>
                        </td>
                        <td class="text-center align-middle p-2">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editarLinhaDesconsiderar(this)" title="Editar Código">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerLinhaDesconsiderar(this)" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(linha);
            });
            
        } else {
            // Se o banco retornar vazio, apenas adiciona a linha padrão em branco
            adicionarLinhaDesconsiderar();
        }

    } catch (error) {
        console.error("Erro ao carregar itens desconsiderados do banco:", error);
        tbody.html('<tr><td colspan="3" class="text-center text-danger p-3"><i class="bi bi-exclamation-triangle me-2"></i>Erro ao carregar dados.</td></tr>');
        
        // Deixa uma linha em branco pro cara tentar usar mesmo com erro
        setTimeout(() => {
            tbody.empty();
            adicionarLinhaDesconsiderar();
        }, 2000);
    }
}

// Dispara o carregamento toda vez que o modal for aberto
$('#modalConfigurarItens').on('show.bs.modal', function () {
    carregarItensDesconsiderados();
});

function adicionarLinhaDesconsiderar() {
    const novaLinha = `
        <tr>
            <td class="align-middle p-2 text-center">
                <span class="fw-bold text-primary fs-6 span-codigo d-none"></span>
                <input type="text" class="form-control form-control-sm text-center fw-bold input-cod-desconsiderar border-primary shadow-sm" placeholder="Digite o Código...">
            </td>
                <td class="align-middle p-2">
                    <span class="text-muted label-nome-desconsiderar fst-italic">Aguardando código...</span>
                </td>
                <td class="text-center align-middle p-2">
                    <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editarLinhaDesconsiderar(this)" title="Editar Código">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerLinhaDesconsiderar(this)" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        // Insere a nova linha no topo da tabela e já coloca o foco no input
        $('#table-itens-desconsiderados tbody').prepend(novaLinha);
        $('#table-itens-desconsiderados tbody tr:first-child .input-cod-desconsiderar').focus();
}

function editarLinhaDesconsiderar(botao) {
    let linha = $(botao).closest('tr');
    let spanCodigo = linha.find('.span-codigo');
    let inputCodigo = linha.find('.input-cod-desconsiderar');
    
    // Esconde a label e mostra o input, focando o cursor nele
    spanCodigo.addClass('d-none');
    inputCodigo.removeClass('d-none').addClass('border-primary shadow-sm').focus();
}

async function removerLinhaDesconsiderar(botao) {
    let linha = $(botao).closest('tr');
    let inputCodigo = linha.find('.input-cod-desconsiderar');
    let codMaterial = inputCodigo.val().trim();

    // 1. Se o input estiver vazio, significa que a linha nem foi salva no banco ainda.
    if (codMaterial === '') {
        removerLinhaVisualmente(linha);
        return;
    }

    // 2. Confirmação de segurança 
    if (!confirm(`Deseja realmente remover o material ${codMaterial} da lista de desconsiderados?`)) {
        return;
    }

    // 3. Prepara o botão: Guarda o ícone original e coloca um "Carregando..."
    let iconeOriginal = $(botao).html();
    $(botao).html('<i class="bi bi-hourglass-split"></i>').prop('disabled', true);

    // 4. Monta o corpo da requisição 
    let payloadEnvio = {
        acao: 'remover_item_considerado',
        dados: {
            codMaterial: codMaterial
        }
    };

    try {
        // 5. Faz a chamada POST para o requests.php
        const response = await $.ajax({
            url: 'requests.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payloadEnvio),
            dataType: 'json'
        });

        // 6. Sucesso! A API excluiu, então agora removemos a linha da tela
        removerLinhaVisualmente(linha);

    } catch (error) {
        console.error('Erro ao remover item:', error);
        alert('Erro ao comunicar com o servidor. O item pode não ter sido removido.');
        // Restaura o botão em caso de erro
        $(botao).html(iconeOriginal).prop('disabled', false);
    }
}

function removerLinhaVisualmente(linha) {
    // Evita excluir a última linha restante na tabela para o visual não quebrar
    if ($('#table-itens-desconsiderados tbody tr').length > 1) {
        linha.remove();
    } else {
        // Se for a última linha, apenas limpa os dados
        linha.find('input').val('');
        linha.find('span.span-codigo').text('').addClass('d-none');
        linha.find('input').removeClass('d-none border-primary shadow-sm'); 
        linha.find('span.label-nome-desconsiderar').text('Aguardando código...').addClass('fst-italic text-muted').removeClass('text-dark fw-bold');
    }
}

// Eventos para buscar nome na API ao digitar o código (blur / Enter)
$('#table-itens-desconsiderados').on('blur', '.input-cod-desconsiderar', async function() {
    let input = $(this);
    let span = input.siblings('.span-codigo');
    let linha = input.closest('tr');
    let labelNome = linha.find('.label-nome-desconsiderar');
    let valor = input.val().trim();

    // Só avança se ele tiver digitado algo
    if (valor !== '') {
        // 1. Alterna visualmente para texto
        span.text(valor).removeClass('d-none');
        input.addClass('d-none').removeClass('border-primary shadow-sm');

        // 2. Coloca o status de "Buscando..."
        labelNome.removeClass('fst-italic text-muted text-danger')
                 .addClass('text-dark fw-bold')
                 .html('<i class="bi bi-hourglass-split me-1 text-primary"></i>Buscando...');

        try {
            // 3. Faz a requisição na API
            const response = await $.ajax({
                url: 'requests.php',
                type: 'GET',
                dataType: 'json',
                data: { 
                    acao: 'get_obter_nome_material',
                    codMaterial: valor 
                }
            });

            // 4. Lida com a resposta
            let nomeEncontrado = "Material não encontrado";
            
            if (response) {
                if (Array.isArray(response) && response.length > 0) {
                    let primeiroItem = response[0];
                    if (primeiroItem.nomeMaterial) {
                        nomeEncontrado = primeiroItem.nomeMaterial;
                    } else if (primeiroItem.descricao) {
                        nomeEncontrado = primeiroItem.descricao;
                    }
                } 
                else if (!Array.isArray(response) && response.nomeMaterial) {
                    nomeEncontrado = response.nomeMaterial;
                } 
                else if (typeof response === 'string' && response.trim() !== '') {
                    nomeEncontrado = response;
                }
            }
            
            // 5. Atualiza a tela com o resultado final
            if (nomeEncontrado === "Material não encontrado" || nomeEncontrado === "Não encontrado") {
                labelNome.removeClass('text-dark').addClass('text-danger').text("Material não encontrado");
            } else {
                labelNome.removeClass('text-danger').addClass('text-dark').text(nomeEncontrado);
            }

        } catch (error) {
            console.error('Erro ao buscar nome do material:', error);
            labelNome.removeClass('text-dark').addClass('text-danger').html('<i class="bi bi-exclamation-triangle me-1"></i>Erro na busca');
        }
    }
});

$('#table-itens-desconsiderados').on('keypress', '.input-cod-desconsiderar', function(e) {
    if (e.which === 13) { // 13 é o código da tecla Enter
        e.preventDefault();
        $(this).blur(); // Dispara o evento de blur acima
    }
});

// ==========================================
// SALVAR ITENS DESCONSIDERADOS (POST LINHA A LINHA)
// ==========================================
async function salvarItensDesconsiderados() {
    let codigos = [];
    
    // 1. Varre todos os inputs da tabela para pegar os códigos preenchidos
    $('.input-cod-desconsiderar').each(function() {
        let valor = $(this).val().trim();
        if(valor !== '') {
            codigos.push(valor); // Guarda só os inputs que não estão vazios
        }
    });

    if (codigos.length === 0) {
        alert("Atenção: A lista está vazia. Adicione pelo menos um código para salvar.");
        return;
    }

    let btnSalvar = $('#modalConfigurarItens .btn-success');
    let textoOriginal = btnSalvar.html();

    try {
        // 2. Muda o texto do botão para dar um feedback visual e trava ele
        btnSalvar.html('<i class="bi bi-hourglass-split me-1"></i> Salvando...').prop('disabled', true);

        // 3. Loop: Dispara uma requisição POST separada para CADA código encontrado
        for (let codigo of codigos) {
            
            let payloadEnvio = {
                acao: 'inserir_material_desconsiderar_conf', 
                dados: {
                    codMaterial: codigo
                }
            };

            // Espera salvar a linha atual antes de passar para a próxima
            await $.ajax({
                url: 'requests.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payloadEnvio),
                dataType: 'json'
            });
        }

        // 4. Se o loop terminou sem cair no catch, tudo deu certo
        btnSalvar.html(textoOriginal).prop('disabled', false);
        alert("Itens desconsiderados salvos com sucesso!");
        $('#modalConfigurarItens').modal('hide');

    } catch (error) {
        console.error('Erro ao salvar itens desconsiderados:', error);
        alert('Erro ao comunicar com o servidor. Alguns itens podem não ter sido salvos.');
        btnSalvar.html(textoOriginal).prop('disabled', false);
    }
}

const ConsultarFilaConferencia_itens = async (numeroOP) => {
    try {
        $('#loadingModal').modal('show');
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: 'ConsultarFilaConferencia_itens', numeroOP: numeroOP },
        });

        TabelaItens(response); 
        $('#modalItensOP').modal('show'); 
        
    } catch (error) {
        console.error('Erro ao consultar serviço:', error);
        alert("Erro ao buscar os itens da OP.");
    } finally {
        setTimeout(() => { $('#loadingModal').modal('hide'); }, 500);
    }
};

function TabelaItens(dados) {
    if ($.fn.DataTable.isDataTable('#table-itens-conferencia')) {
        $('#table-itens-conferencia').DataTable().destroy();
    }

    let table = $('#table-itens-conferencia').DataTable({
        data: dados, 
        searching: true, 
        paging: false, 
        info: false,
        autoWidth: false, 
        
        createdRow: function(row, data, dataIndex) {
            let opAtual = $('#spanNumeroOP').text().split(' - ')[0].trim();
            let material = data.codMaterialEdt || data.codProduto; 
            let chave = opAtual + '||' + material;
            $(row).attr('data-chave', chave);
        },
        
        columnDefs: [
            { targets: 0, width: "10%", className: "align-middle" }, 
            { targets: 1, width: "15%", className: "align-middle" }, 
            { targets: 2, width: "35%", className: "align-middle" }, 
            { targets: 3, width: "15%", className: "align-middle" }, 
            { targets: 4, width: "15%", className: "align-middle" }, 
            { targets: 5, width: "10%", className: "text-center align-middle" }  
        ],
        columns: [
            { data: 'numeroOP', defaultContent: '-' }, 
            { data: 'codMaterialEdt', defaultContent: '-' }, 
            { data: 'nomeMaterial', defaultContent: 'Sem descrição' }, 
            { data: 'localizacao', defaultContent: '-' }, 
            { data: 'separador', defaultContent: '-' }, 
            { 
                data: 'qtdeRequisitada', 
                defaultContent: '0',
                render: function(data, type, row) {
                    return `<span class="fw-bold fs-4 text-primary">${data}</span>`;
                }
            } 
        ],
        language: {
            emptyTable: "Nenhum item encontrado para esta OP",
            zeroRecords: "Nenhum registro encontrado",
            search: "Pesquisar Item:"
        }
    });

    // MÁGICA DO CARREGAMENTO (PÓS-RENDERIZAÇÃO)
    let qtdJaConferidos = 0;
    let totalLinhas = table.rows().count();

    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
        let data = this.data();
        let tr = $(this.node()); 

        if (tr.length > 0 && data.statusConferido && data.statusConferido.toString().trim().toLowerCase() === 'conferido') {
            
            tr.addClass('ja-conferido');
            tr.find('td').removeClass('bg-light').addClass('bg-success text-white fw-bold');
            tr.find('.text-primary').removeClass('text-primary').addClass('text-white');
            
            tr.detach().appendTo('#table-itens-conferencia tbody');
            
            qtdJaConferidos++;
        }
    });

    // ATUALIZA OS CONTADORES NA TELA
    $('#contadorTotal').text(totalLinhas);
    $('#contadorBipados').text(qtdJaConferidos);

    if (totalLinhas > 0 && qtdJaConferidos === totalLinhas) {
        setTimeout(() => {
            $('#modalSucesso').modal('show');
        }, 500);
    }
}

// ==========================================
// SALVAMENTO ASSÍNCRONO NO BANCO (FIRE AND FORGET)
// ==========================================

function salvarItemConferidoAsync(numeroOP, codMaterial) {
    let payloadEnvio = {
        acao: 'inserir_conferencia_itens_op',
        dados: {
            numeroOP: numeroOP,
            codMaterial: codMaterial
        }
    };

    $.ajax({
        url: 'requests.php',
        type: 'POST',
        contentType: 'application/json', 
        data: JSON.stringify(payloadEnvio), 
        dataType: 'json',
        success: function(response) {
            console.log(`[SUCESSO] Item ${codMaterial} da OP ${numeroOP} salvo!`, response);
        },
        error: function(xhr, status, error) {
            console.error(`[ERRO BANCO] Falha ao salvar o item ${codMaterial}:`, error);
        }
    });
}

function Tabela(dados) {
    if ($.fn.DataTable.isDataTable('#table-metas')) {
        $('#table-metas').DataTable().destroy();
    }

    $('#checkAllFiltro').prop('checked', false);

    var table = $('#table-metas').DataTable({
        data: dados, 
        searching: true, 
        paging: true, 
        lengthChange: false,
        info: false,
        pageLength: 20,
        dom: '<"top">rt<"bottom"p><"clear">', 
        columns: [
            { data: 'numeroOP' }, 
            { data: 'codProduto' }, 
            { data: 'descricao' },      
            { data: 'prioridade' },      
            { data: 'FaseAtual' },   
            { data: 'separador' },  
            {                    
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {
                    return `
                        <button type="button" class="btn btn-sm btn-outline-success btn-abrir-modal" 
                                data-op="${row.numeroOP}" data-descricao="${row.descricao}">
                            <i class="bi bi-check2-square"></i> Conferir
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-detalhes ms-1">
                            <i class="bi bi-arrow-bar-down"></i>
                        </button>
                    `;
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

    $('#filtroNumeroOP').on('keyup', function () { table.column(0).search(this.value).draw(); });
    $('#filtroCodigoProduto').on('keyup', function () { table.column(1).search(this.value).draw(); });
    $('#filtroDescricao').on('keyup', function () { table.column(2).search(this.value).draw(); });
    $('#filtroSeparador').on('keyup', function () { table.column(5).search(this.value).draw(); });

    $('#checkAllFiltro').off('change').on('change', function() {
        var isChecked = $(this).is(':checked');
        var linhasFiltradas = table.rows({ search: 'applied' }).nodes();
        $('input.check-imprimir', linhasFiltradas).prop('checked', isChecked);
    });

    $('#table-metas tbody').off('change', '.check-imprimir').on('change', '.check-imprimir', function() {
        if (!$(this).is(':checked')) {
            $('#checkAllFiltro').prop('checked', false);
        }
    });
}

// ==========================================
// LÓGICA DE LEITURA DO QR CODE (OP||MATERIAL)
// ==========================================

$('#modalItensOP').on('shown.bs.modal', function () {
    $('#inputQrCode').val('').focus();
});

$('#inputQrCode').on('keypress', function(e) {
    if(e.which === 13) { 
        e.preventDefault();
        processarQrCode($(this).val());
    }
});

$('#btnBuscarQrCode').on('click', function() {
    processarQrCode($('#inputQrCode').val());
});

function processarQrCode(qrCodeVal) {
    if (!qrCodeVal) return;

    qrCodeVal = qrCodeVal.replace(/}}/g, '||');
    let partes = qrCodeVal.split('||');
    
    if (partes.length !== 2) {
        tocarBipeErro(); 
        $('#textoErroBipagem').html("Formato inválido! O padrão esperado é: <strong>OP||Material</strong>");
        $('#modalErroBipagem').modal('show');
        $('#inputQrCode').val('');
        return;
    }

    let opLida = partes[0].trim();
    let materialLido = partes[1].trim();
    let opAtual = $('#spanNumeroOP').text().split(' - ')[0].trim();
    
    if (opLida !== opAtual) {
        tocarBipeErro(); 
        $('#textoErroBipagem').html(`Atenção: Você bipou um material da OP <strong class="text-danger">${opLida}</strong>, mas está conferindo a OP <strong class="text-primary">${opAtual}</strong>!`);
        $('#modalErroBipagem').modal('show');
        $('#inputQrCode').val('');
        return;
    }

    let chaveBuscada = opLida + '||' + materialLido;
    let linhaEncontrada = $(`#table-itens-conferencia tbody tr[data-chave="${chaveBuscada}"]`);

    if (linhaEncontrada.length > 0) {
        
        if (linhaEncontrada.hasClass('ja-conferido')) {
            tocarBipeErro(); 
            linhaParaExcluir = linhaEncontrada; 
            $('#spanMaterialExcluir').text(materialLido); 
            $('#modalConfirmarExclusao').modal('show'); 
            $('#inputQrCode').val('');
            return; 
        }

        linhaEncontrada.addClass('ja-conferido');
        linhaEncontrada.find('td').removeClass('bg-light').addClass('bg-success text-white fw-bold');
        linhaEncontrada.find('.text-primary').removeClass('text-primary').addClass('text-white');
        
        salvarItemConferidoAsync(opAtual, materialLido);

        linhaEncontrada.detach().appendTo('#table-itens-conferencia tbody');

        let proximoItem = $('#table-itens-conferencia tbody tr:not(.ja-conferido)').first();
        if (proximoItem.length > 0) {
            proximoItem[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        let totalBipados = parseInt($('#contadorBipados').text()) + 1;
        $('#contadorBipados').text(totalBipados);

        let totalGeral = parseInt($('#contadorTotal').text());
        if (totalBipados === totalGeral) {
            tocarBipeSucesso(); 
            setTimeout(() => {
                $('#modalSucesso').modal('show');
            }, 300);
        }
        
    } else {
        tocarBipeErro(); 
        $('#textoErroBipagem').html(`O material <strong class="text-danger">${materialLido}</strong> não foi encontrado na lista desta OP!`);
        $('#modalErroBipagem').modal('show');
    }

    $('#inputQrCode').val('').focus();
}

$('#modalErroBipagem').on('shown.bs.modal', function () {
    $('#btnFecharErroBipagem').focus();
});
$('#modalErroBipagem').on('hidden.bs.modal', function () {
    $('#inputQrCode').val('').focus();
});

$('#modalConfirmarExclusao').on('shown.bs.modal', function () {
    $('#btnNaoExcluir').focus();
});
$('#modalConfirmarExclusao').on('hidden.bs.modal', function () {
    $('#inputQrCode').val('').focus();
});

function excluirBipagem() {
    if (linhaParaExcluir) {
        linhaParaExcluir.removeClass('ja-conferido');
        linhaParaExcluir.find('td').removeClass('bg-success text-white fw-bold').addClass('bg-light');
        linhaParaExcluir.find('span.text-white').removeClass('text-white').addClass('text-primary');

        linhaParaExcluir.detach().prependTo('#table-itens-conferencia tbody');
        
        linhaParaExcluir[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

        let totalBipados = parseInt($('#contadorBipados').text()) - 1;
        $('#contadorBipados').text(totalBipados);
        linhaParaExcluir = null;
    }
    
    $('#modalConfirmarExclusao').modal('hide');
    $('#inputQrCode').val('').focus();
}

function limparConferencia() {
    if (confirm("Tem certeza que deseja recomeçar a conferência? Todo o progresso será perdido.")) {
        let opAtual = $('#spanNumeroOP').text().split(' - ')[0].trim();
        ConsultarFilaConferencia_itens(opAtual);
        
        setTimeout(() => {
            $('#inputQrCode').val('').focus();
        }, 600);
    } else {
        $('#inputQrCode').val('').focus();
    }
}

async function efetivarConferencia() {
    // 1. Pega o número da OP e a matrícula do usuário logado
    let opAtual = $('#spanNumeroOP').text().split(' - ')[0].trim();
    let matriculaUsuario = window.usuarioAtivo ? window.usuarioAtivo.matricula : '';

    // Trava de segurança caso a matrícula tenha se perdido
    if (!matriculaUsuario) {
        alert("Erro: Identificação do usuário não encontrada. Por favor, atualize a página e informe a matrícula novamente.");
        return;
    }

    console.log(`Efetivando conferência da OP: ${opAtual} pelo usuário: ${matriculaUsuario}`);

    // 2. Monta o corpo da requisição exatamente como você pediu
    let payloadEnvio = {
        acao: 'finalizar_conferencia',
        dados: {
            codMatricula: matriculaUsuario,
            numeroOP: opAtual
        }
    };

    // 3. Pega o botão do modal de sucesso para dar feedback visual
    let btnEfetivar = $('#modalSucesso .btn-success');
    let textoOriginalBtn = btnEfetivar.html();
    btnEfetivar.html('<i class="bi bi-hourglass-split me-1"></i> Finalizando...').prop('disabled', true);

    try {
        // 4. Dispara a requisição POST para o requests.php
        const response = await $.ajax({
            url: 'requests.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payloadEnvio),
            dataType: 'json'
        });

        // 5. Sucesso!
        alert("Conferência da OP " + opAtual + " finalizada com sucesso!");
        
        // Fecha os modais e volta os botões ao normal
        btnEfetivar.html(textoOriginalBtn).prop('disabled', false);
        $('#modalSucesso').modal('hide');
        $('#modalItensOP').modal('hide');

        // 6. Atualiza a tabela principal de OPs (para a OP recém finalizada sumir da fila ou mudar de fase)
        await ConsultarFilaConferencia();

    } catch (error) {
        console.error('Erro ao efetivar conferência:', error);
        alert('Erro ao comunicar com o servidor. A OP pode não ter sido finalizada no banco de dados.');
        btnEfetivar.html(textoOriginalBtn).prop('disabled', false);
    }
}