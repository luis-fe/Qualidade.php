// ==========================================
// CONFIGURAÇÕES GLOBAIS E SONS (LATÊNCIA ZERO)
// ==========================================

let linhaParaExcluir = null;

// Variáveis para o áudio de alta performance
let audioCtx = null;
let bufferErro = null;
let bufferSucesso = null; // <-- Nova variável para o som de sucesso

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
    }, 150); // 150ms é o tempo exato para permitir que cliques em botões funcionem antes de roubar o foco
});

// Mantém o foco na tela inteira (se o cara clicar no fundo cinza da tela, volta pro input)
$('#modalItensOP').on('click', function(e) {
    if(e.target.id !== 'inputQrCode') {
        $('#inputQrCode').focus();
    }
});

// ==========================================
// INICIALIZAÇÃO DA PÁGINA
// ==========================================
$(document).ready(async () => {
    await ConsultarFilaConferencia();

    // 1. Captura a descrição e junta no título do modal
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
// FUNÇÕES DE UTILIDADE E FORMATAÇÃO
// ==========================================

// Converte "61.644" ou "1.000,50" para número de computador
function converterParaFloat(valor) {
    if (valor === null || valor === undefined || valor === '') return 0;
    if (typeof valor === 'number') return valor; 
    
    let texto = valor.toString().trim();
    
    // 1. Remove TODOS os pontos
    texto = texto.replace(/\./g, ''); 
    
    // 2. Transforma a vírgula no ponto decimal
    texto = texto.replace(',', '.');  
    
    let numero = parseFloat(texto);
    return isNaN(numero) ? 0 : numero;
}

// Converte 1000.50 para "1.000,50" 
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

    // 1. Cria a tabela normalmente sem tentar pintar nada ainda
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

    // ==========================================
    // 2. MÁGICA DO CARREGAMENTO (PÓS-RENDERIZAÇÃO)
    // ==========================================
    let qtdJaConferidos = 0;
    let totalLinhas = table.rows().count();

    // Faz um loop por todas as linhas de forma segura pela API do DataTables
    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
        let data = this.data();
        let tr = $(this.node()); // Pega a linha HTML (TR) real dessa iteração

        // Verifica se a linha existe no HTML e se o status veio como 'Conferido' no JSON
        if (tr.length > 0 && data.statusConferido && data.statusConferido.toString().trim().toLowerCase() === 'conferido') {
            
            // 1. Marca de verde (Mesma lógica do processarQrCode)
            tr.addClass('ja-conferido');
            tr.find('td').removeClass('bg-light').addClass('bg-success text-white fw-bold');
            tr.find('.text-primary').removeClass('text-primary').addClass('text-white');
            
            // 2. Joga pro final da fila
            tr.detach().appendTo('#table-itens-conferencia tbody');
            
            qtdJaConferidos++;
        }
    });

    // ==========================================
    // 3. ATUALIZA OS CONTADORES NA TELA
    // ==========================================
    $('#contadorTotal').text(totalLinhas);
    $('#contadorBipados').text(qtdJaConferidos);

    // Bônus: Se a OP abrir já 100% conferida, dispara o modal de sucesso na hora!
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
    
    // Empacotando no formato exato que o requests.php espera ler:
    // "acao" e "dados"
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
        // ESTAS DUAS LINHAS SÃO OBRIGATÓRIAS POR CAUSA DO SEU 'php://input'
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

        // 1. MARCA A LINHA DE VERDE
        linhaEncontrada.addClass('ja-conferido');
        linhaEncontrada.find('td').removeClass('bg-light').addClass('bg-success text-white fw-bold');
        linhaEncontrada.find('.text-primary').removeClass('text-primary').addClass('text-white');
        
        // ========================================================
        // DISPARA O SALVAMENTO NO BANCO EM SEGUNDO PLANO AQUI!
        // ========================================================
        salvarItemConferidoAsync(opAtual, materialLido);


        // 2. MOVE A LINHA PARA O FINAL DA TABELA
        linhaEncontrada.detach().appendTo('#table-itens-conferencia tbody');

        // 3. ROLA A TELA PARA O PRÓXIMO ITEM "A BIPAR"
        let proximoItem = $('#table-itens-conferencia tbody tr:not(.ja-conferido)').first();
        if (proximoItem.length > 0) {
            proximoItem[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // ATUALIZA O CONTADOR
        let totalBipados = parseInt($('#contadorBipados').text()) + 1;
        $('#contadorBipados').text(totalBipados);

        // Verifica se finalizou
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

// ==========================================
// EVENTOS PARA MANTER O FOCO NO BIPADOR (MODO SCANNER)
// ==========================================

// Quando o modal de erro genérico abre/fecha
$('#modalErroBipagem').on('shown.bs.modal', function () {
    $('#btnFecharErroBipagem').focus();
});
$('#modalErroBipagem').on('hidden.bs.modal', function () {
    $('#inputQrCode').val('').focus();
});

// Quando o modal de confirmar exclusão abre/fecha
$('#modalConfirmarExclusao').on('shown.bs.modal', function () {
    $('#btnNaoExcluir').focus();
});
$('#modalConfirmarExclusao').on('hidden.bs.modal', function () {
    $('#inputQrCode').val('').focus();
});

// ==========================================
// FUNÇÕES DE AÇÕES DOS BOTÕES FINAIS
// ==========================================

function excluirBipagem() {
    if (linhaParaExcluir) {
        // 1. Tira as cores verdes
        linhaParaExcluir.removeClass('ja-conferido');
        linhaParaExcluir.find('td').removeClass('bg-success text-white fw-bold').addClass('bg-light');
        linhaParaExcluir.find('span.text-white').removeClass('text-white').addClass('text-primary');

        // 2. MOVE A LINHA DE VOLTA PARA O TOPO DA TABELA
        linhaParaExcluir.detach().prependTo('#table-itens-conferencia tbody');
        
        // 3. Rola a tela de volta para essa linha
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
        
        // Espera a tabela recarregar para devolver o foco
        setTimeout(() => {
            $('#inputQrCode').val('').focus();
        }, 600);
    } else {
        // Se clicar em Cancelar, devolve o foco na mesma hora
        $('#inputQrCode').val('').focus();
    }
}

function efetivarConferencia() {
    let opAtual = $('#spanNumeroOP').text().split(' - ')[0].trim();
    console.log(`Efetivando conferência da OP: ${opAtual}`);
    
    alert("Função de efetivação disparada! Coloque sua requisição AJAX aqui.");
    $('#modalSucesso').modal('hide');
    $('#modalItensOP').modal('hide');
}