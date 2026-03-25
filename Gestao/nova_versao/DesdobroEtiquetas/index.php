<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<style>
    /* ==========================================================================
       ESTILOS DA TELA (SCREEN) - Design Moderno e Corporativo
       ========================================================================== */
    body { background-color: #f4f7f6; } /* Fundo levemente cinza para destacar o container */

    .wms-container { 
        max-width: 850px; 
        margin: 20px auto; 
        background-color: #ffffff; 
        padding: 30px; 
        border-radius: 12px; 
        box-shadow: 0 8px 24px rgba(0,0,0,0.08); 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    }
    
    .wms-container h2 { 
        color: #003366; /* Azul escuro corporativo */
        margin-top: 0; 
        margin-bottom: 20px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #e2e8f0; 
        font-size: 1.6rem; 
        font-weight: 700;
    }

    /* Botões Superiores (Abas) */
    .botoes-opcao { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    .btn-opcao { 
        padding: 10px 20px; 
        background-color: #f8fafc; 
        color: #475569; 
        border: 1px solid #cbd5e1; 
        border-radius: 8px; 
        font-size: 15px; 
        font-weight: 600; 
        cursor: pointer; 
        transition: all 0.2s ease-in-out; 
    }
    .btn-opcao:hover { background-color: #e2e8f0; color: #1e293b; }
    .btn-opcao.ativo { 
        background-color: #003366; 
        color: white; 
        border-color: #003366; 
        box-shadow: 0 4px 6px rgba(0, 51, 102, 0.2); 
    }

    /* Card do Material */
    .card-material { 
        display: none; 
        background-color: #f0fdf4; /* Fundo verde super claro (sucesso) */
        border-left: 5px solid #16a34a; /* Borda esquerda de destaque */
        border-radius: 8px; 
        padding: 12px 16px; 
        margin-bottom: 20px; 
        align-items: center; 
        gap: 15px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    .badge-codigo { background-color: #16a34a; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold; font-size: 1rem; letter-spacing: 0.5px; }
    .nome-material { font-size: 1.05rem; color: #1e293b; font-weight: 600; margin: 0; }

    /* Seções de Formulário */
    .secao-desdobro { 
        display: none; 
        background-color: #ffffff; 
        border: 1px solid #e2e8f0; 
        padding: 25px; 
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .secao-desdobro h3 { margin-top: 0; color: #003366; margin-bottom: 20px; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    
    /* Linhas e Inputs */
    .linha { display: flex; align-items: center; margin-bottom: 15px; gap: 15px; flex-wrap: wrap; }
    .linha label { font-weight: 600; color: #334155; min-width: 190px; font-size: 0.95rem; }
    .linha input[type="text"], .linha input[type="number"], .linha select { 
        padding: 10px 14px; 
        border: 1px solid #cbd5e1; 
        border-radius: 6px; 
        font-size: 14px; 
        width: 100%; 
        max-width: 280px; 
        background-color: #f8fafc;
        transition: all 0.2s;
    }
    .linha input:focus, .linha select:focus { outline: none; border-color: #3b82f6; background-color: #fff; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
    
    .readonly { 
        background-color: #f1f5f9; 
        border: 1px solid #cbd5e1; 
        padding: 8px 16px; 
        border-radius: 6px; 
        font-weight: 700; 
        color: #0f172a; 
        display: inline-block; 
        min-width: 90px; 
        text-align: center; 
        font-size: 1rem; 
    }
    .divisor-tela { border: none; border-top: 2px dashed #cbd5e1; margin: 25px 0; width: 100%; }
    
    /* Botões de Ação Final */
    .container-botoes-acao { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
    
    .btn-imprimir { 
        background-color: #10b981; 
        color: white; 
        border: none; 
        padding: 12px 24px; 
        border-radius: 8px; 
        font-size: 15px; 
        font-weight: bold; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        transition: background-color 0.2s;
    }
    .btn-imprimir:hover { background-color: #059669; }

    .btn-secundario { 
        background-color: #f59e0b; /* Laranja para alertar que é uma ação diferente */
        color: white; 
        border: none; 
        padding: 12px 24px; 
        border-radius: 8px; 
        font-size: 15px; 
        font-weight: bold; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        transition: background-color 0.2s;
    }
    .btn-secundario:hover { background-color: #d97706; }

    .select2-container .select2-selection--single { height: 40px; padding: 5px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #f8fafc; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; }

    /* ==========================================================================
       ESTILOS DE IMPRESSÃO (Padrão Zebra) - MANTIDOS INTACTOS
       ========================================================================== */
    @media print {
        .no-print, .wms-container, header, footer, #loadingModal, .span-icone, .modal { display: none !important; }

        body * { visibility: hidden; }
        #container-cards, #container-cards * { visibility: visible; }
        
        #container-cards {
            width: 15.0cm !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        .card-etiqueta {
            width: 15.0cm !important; 
            height: 4.0cm !important;
            page-break-after: always !important;
            page-break-inside: avoid !important;
            break-after: page !important;
            display: block !important;
            border: none !important;
            box-shadow: none !important;
        }

        @page {
            size: 10.0cm 3.8cm !important; 
            margin: 0 !important;
        }
        
        body { margin: 0 !important; padding: 0 !important; background-color: white !important; }

        img.img-qrcode {
            visibility: visible !important;
            display: block !important;
            opacity: 1 !important;
            width: 80px !important;
            height: 80px !important;
        }
    }
</style>

<div class="wms-container">
    <h2><i class="bi bi-diagram-3-fill me-2 text-primary"></i> Processo de Desdobro</h2>

    <div class="botoes-opcao">
        <button id="btn-kit-kit" class="btn-opcao" onclick="mostrarSecao('kit-kit')">Kit <i class="bi bi-arrow-right mx-1"></i> Kit</button>
        <button id="btn-kit-unidade" class="btn-opcao" onclick="mostrarSecao('kit-unidade')">Kit <i class="bi bi-arrow-right mx-1"></i> Unidade</button>
        <button id="btn-unidade-kit" class="btn-opcao" onclick="mostrarSecao('unidade-kit')">Unidade <i class="bi bi-arrow-right mx-1"></i> Kit</button>
    </div>

    <div id="sec-kit-kit" class="secao-desdobro">
        <h3><i class="bi bi-box-seam"></i> Opção: Kit para Kit</h3>
        <div class="linha">
            <label for="qr_kk">QR Code do Desdobro:</label>
            <input type="text" id="qr_kk" placeholder="Biper o QR Code" onchange="simularBuscaEtiqueta('kk')">
        </div>
        
        <div id="card_info_material_kk" class="card-material">
            <span id="display_cod_item_kk" class="badge-codigo">---</span>
            <p id="display_nome_item_kk" class="nome-material">Buscando informações...</p>
        </div>

        <div id="campos_extras_kk" style="display: none;">
            <div class="linha">
                <label>Qtd Original:</label>
                <span id="qtd_origem_kk" class="readonly">0</span>
            </div>
            <hr class="divisor-tela">
            <div class="linha">
                <label for="num_kits">Dividir em quantos Kits?</label>
                <select id="num_kits" style="width: 200px;" onchange="gerarCamposDinamicosKits()">
                    <option value="2">2 Kits</option>
                    <option value="3">3 Kits</option>
                    <option value="4">4 Kits</option>
                    <option value="5">5 Kits</option>
                    <option value="6">6 Kits</option>
                    <option value="7">7 Kits</option>
                    <option value="8">8 Kits</option>
                    <option value="9">9 Kits</option>
                    <option value="10">10 Kits</option>
                </select>
            </div>
            <div id="container-quantidades"></div>
            
            <div class="container-botoes-acao">
                <button id="btn_imprimir_kk" class="btn-imprimir no-print" onclick="imprimirEtiqueta('Kit-Kit')">
                    <i class="bi bi-printer-fill text-white"></i> Imprimir Novas Etiquetas
                </button>
            </div>
        </div>
    </div>

    <div id="sec-kit-unidade" class="secao-desdobro">
        <h3><i class="bi bi-boxes"></i> Opção: Kit para Unidade</h3>
        <div class="linha">
            <label for="qr_ku">QR Code do Desdobro:</label>
            <input type="text" id="qr_ku" placeholder="Biper o QR Code" onchange="simularBuscaEtiqueta('ku')">
        </div>

        <div id="card_info_material_ku" class="card-material">
            <span id="display_cod_item_ku" class="badge-codigo">---</span>
            <p id="display_nome_item_ku" class="nome-material">Buscando informações...</p>
        </div>

        <div id="campos_extras_ku" style="display: none;">
            <div class="linha">
                <label>Qtd Original:</label>
                <span id="qtd_origem_ku" class="readonly">0</span>
            </div>
            <hr class="divisor-tela">
            <div class="linha">
                <label>Qtd Controle Unitário:</label>
                <span id="qtd_unitario_ku" class="readonly">0</span>
            </div>
            
            <div class="container-botoes-acao">
                <button id="btn_imprimir_ku" class="btn-imprimir no-print" onclick="imprimirEtiqueta('Kit-Unidade')">
                    <i class="bi bi-printer-fill text-white"></i> Imprimir Novas Etiquetas
                </button>
                <button id="btn_nao_imprimir_ku" class="btn-secundario no-print" onclick="abrirModalNaoImprimir()">
                    <i class="bi bi-ban"></i> Não Imprimir (Repor)
                </button>
            </div>
        </div>
    </div>

    <div id="sec-unidade-kit" class="secao-desdobro">
        <h3><i class="bi bi-box"></i> Opção: Unidade para Kit</h3>
        <div class="linha">
            <label for="qr_uk">QR Code da Unidade:</label>
            <input type="text" id="qr_uk" placeholder="Biper o QR Code" onchange="simularBuscaEtiqueta('uk')">
        </div>

        <div id="card_info_material_uk" class="card-material">
            <span id="display_cod_item_uk" class="badge-codigo">---</span>
            <p id="display_nome_item_uk" class="nome-material">Buscando informações...</p>
        </div>

        <div id="campos_extras_uk" style="display: none;">
            <div class="linha">
                <label>Qtd Original Disponível:</label>
                <span id="qtd_origem_uk" class="readonly">0</span>
            </div>
            <hr class="divisor-tela">
            <div class="linha">
                <label for="qtd_kits_uk">Qtd de Kits a Montar:</label>
                <input type="number" id="qtd_kits_uk" placeholder="Ex: 5 kits" oninput="calcularUnidadeKit()">
            </div>
            <div class="linha">
                <label for="und_por_kit_uk">Unidades por Kit:</label>
                <input type="number" id="und_por_kit_uk" placeholder="Ex: 10 unidades" oninput="calcularUnidadeKit()">
            </div>
            <div class="linha">
                <label>Total Utilizado:</label>
                <span id="total_utilizado_uk" class="readonly" style="background-color: #fff3cd; color: #856404; border-color: #ffe69c;">0</span>
            </div>
            <div class="linha">
                <label>Saldo Restante:</label>
                <span id="saldo_uk" class="readonly">0</span>
            </div>
            
            <div class="container-botoes-acao">
                <button id="btn_imprimir_uk" class="btn-imprimir no-print" onclick="imprimirEtiqueta('Unidade-Kit')">
                    <i class="bi bi-printer-fill text-white"></i> Imprimir Novas Etiquetas
                </button>
            </div>
        </div>
    </div>
</div>

<div id="container-cards" class="d-none d-flex flex-wrap gap-2 mt-3 p-2"></div>

<div class="modal fade" id="modalConfirmarPendencia" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Estorno
                </h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-0 fs-5">O material lido está correto.<br>Deseja confirmar o estorno desta etiqueta do estoque para iniciar o desdobro?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" onclick="cancelarEstorno()">Cancela</button>
                <button type="button" class="btn btn-warning px-4 fw-bold text-dark" onclick="efetivarFinalizacaoPendencia()">Sim, Estornar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNaoImprimir" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-geo-alt-fill me-2"></i> Selecione o Endereço para Repor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4 text-center">
                    <span class="text-muted d-block mb-1">Quantidade a repor no endereço</span>
                    <span id="label_qtd_repor" class="badge bg-success fs-3 px-4 py-2">0</span>
                </div>
                
                <div class="form-group mb-3">
                    <label for="input_endereco_repor" class="form-label fw-bold text-secondary">Endereço Físico:</label>
                    <input type="text" class="form-control form-control-lg border-primary" id="input_endereco_repor" placeholder="Ex: A-01-10" style="text-transform: uppercase;">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Voltar</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" onclick="confirmarNaoImprimir()">
                    <i class="bi bi-check-circle me-1"></i> Confirmar Endereçamento
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    // ==========================================
    // 1. CONFIGURAÇÃO DOS SONS E VARIÁVEIS
    // ==========================================
    const somAlerta = new Audio('MensagemAlerta.mp3'); 
    const somSucesso = new Audio('MensagemCorrect.mp3');
    const somErro = new Audio('MenasagemErro.mp3');

    let estadoAtualDesdobro = { 
        tipo: null, codMaterial: null, sequencia: null, qtd: null, 
        nomeMaterial: null, fornecedor: null, unidadeMedida: null
    };

    $(document).ready(function() {
        $('#num_kits').select2({ minimumResultsForSearch: Infinity });
        $('#num_kits').on('change', function() { gerarCamposDinamicosKits(); });
    });

    // ==========================================
    // 2. NAVEGAÇÃO DAS ABAS E LIMPEZA
    // ==========================================
    function mostrarSecao(secao) {
        $('.secao-desdobro').hide();
        $('.btn-opcao').removeClass('ativo');

        $('#sec-' + secao).fadeIn(200);
        $('#btn-' + secao).addClass('ativo');
        
        limparCampos();
    }

    mostrarSecao('kit-kit');

    function limparCampos() {
        $('input[type="text"]').val('');
        $('input[type="number"]').val('');
        $('.readonly').text('0');
        $('.card-material, [id^="campos_extras_"]').hide();
        $('[id^="display_nome_item_"]').css('color', '#1e293b');
    }

    // ==========================================
    // 3. LEITURA DO QR CODE E ESTORNO
    // ==========================================
    function simularBuscaEtiqueta(tipo) {
        let inputId = (tipo === 'kk') ? 'qr_kk' : (tipo === 'ku' ? 'qr_ku' : 'qr_uk');
        let cardId = `card_info_material_${tipo}`;
        let codDisplayId = `display_cod_item_${tipo}`;
        let nomeDisplayId = `display_nome_item_${tipo}`;
        let camposExtrasId = `campos_extras_${tipo}`;
        
        let textoQrCode = document.getElementById(inputId).value;

        document.getElementById(camposExtrasId).style.display = 'none';
        document.getElementById(cardId).style.display = 'none';

        if (!textoQrCode) return;

        let partes = textoQrCode.split('-');

        if (partes.length < 3) {
            somErro.play();
            alert("Formato de QR Code inválido!\nEsperado: codItem - seq - qtd");
            document.getElementById(inputId).value = ''; 
            document.getElementById(inputId).focus(); 
            return;
        }

        let codItemExtraido = partes[0].trim();
        let sequenciaExtraida = partes[1].trim(); 
        let qtdExtraida = parseFloat(partes[2].trim());

        if (partes[2].toUpperCase().includes("CONTROLE UNITARIO")) {
            qtdExtraida = parseFloat(partes[1].trim()); 
            sequenciaExtraida = "N/A"; 
        }

        if (tipo === 'kk') document.getElementById('qtd_origem_kk').innerText = qtdExtraida;
        if (tipo === 'ku') document.getElementById('qtd_origem_ku').innerText = qtdExtraida;
        if (tipo === 'uk') document.getElementById('qtd_origem_uk').innerText = qtdExtraida;

        document.getElementById(cardId).style.display = 'flex';
        document.getElementById(codDisplayId).innerText = codItemExtraido;
        document.getElementById(nomeDisplayId).innerText = "Buscando informações...";

        fetch(`requests.php?acao=obterNomeItem&codMaterial=${codItemExtraido}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0 && data[0].nomeMaterial) {
                    let itemAPI = data[0];
                    document.getElementById(nomeDisplayId).innerText = itemAPI.nomeMaterial;
                    
                    somAlerta.play();
                    
                    estadoAtualDesdobro = { 
                        tipo: tipo, codMaterial: codItemExtraido, sequencia: sequenciaExtraida, 
                        qtd: qtdExtraida, nomeMaterial: itemAPI.nomeMaterial, fornecedor: itemAPI.fornecedor || 'N/D',
                        unidadeMedida: itemAPI.unidadeMedida || 'UN'
                    };
                    
                    new bootstrap.Modal(document.getElementById('modalConfirmarPendencia')).show();
                } else {
                    dispararErroProdutoNaoEncontrado(nomeDisplayId, false);
                }
            })
            .catch(error => {
                console.error("Erro na API GET:", error);
                dispararErroProdutoNaoEncontrado(nomeDisplayId, true);
            });
    }

    function efetivarFinalizacaoPendencia() {
        let modalEl = document.getElementById('modalConfirmarPendencia');
        bootstrap.Modal.getInstance(modalEl)?.hide();

        let tipo = estadoAtualDesdobro.tipo;
        let nomeDisplayId = `display_nome_item_${tipo}`;

        document.getElementById(nomeDisplayId).innerText = "Estornando etiqueta no banco...";
        document.getElementById(nomeDisplayId).style.color = "#d97706"; 

        let formData = new FormData();
        formData.append('acao', 'estornarEtiqueta'); 
        formData.append('codMaterial', estadoAtualDesdobro.codMaterial);
        formData.append('sequencia', estadoAtualDesdobro.sequencia);
        formData.append('qtd', estadoAtualDesdobro.qtd);

        fetch('requests.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            let retorno = data[0];

            if (retorno && retorno.status === true) {
                somSucesso.play();
                
                document.getElementById(nomeDisplayId).innerText = "✅ " + estadoAtualDesdobro.nomeMaterial;
                document.getElementById(nomeDisplayId).style.color = "#16a34a";

                $(`#campos_extras_${tipo}`).fadeIn(300);

                if (tipo === 'kk') {
                    gerarCamposDinamicosKits();
                    setTimeout(() => document.getElementById('qtd_dinamica_1').focus(), 100);
                } else if (tipo === 'ku') {
                    document.getElementById('qtd_unitario_ku').innerText = estadoAtualDesdobro.qtd;
                } else if (tipo === 'uk') {
                    document.getElementById('saldo_uk').innerText = estadoAtualDesdobro.qtd;
                    setTimeout(() => document.getElementById('qtd_kits_uk').focus(), 100);
                }
            } else {
                somErro.play();
                alert("Falha no Estorno: " + (retorno?.Mensagem || "Erro desconhecido"));
                limparCampos();
            }
        })
        .catch(error => {
            somErro.play();
            alert("Erro de conexão ao tentar estornar a etiqueta.");
            limparCampos();
        });
    }

    function cancelarEstorno() {
        limparCampos();
        $('.modal-backdrop').remove(); 
    }

    function dispararErroProdutoNaoEncontrado(nomeDisplayId, erroConexao) {
        somErro.play();
        document.getElementById(nomeDisplayId).innerText = erroConexao ? "❌ Erro de conexão." : "⚠️ Material não encontrado!";
        document.getElementById(nomeDisplayId).style.color = "#dc2626";
    }

    // ==========================================
    // 4. NOVA FUNÇÃO: NÃO IMPRIMIR (ENDEREÇAR)
    // ==========================================
    function abrirModalNaoImprimir() {
        let qtd = document.getElementById('qtd_unitario_ku').innerText;
        document.getElementById('label_qtd_repor').innerText = qtd;
        document.getElementById('input_endereco_repor').value = '';
        
        let modal = new bootstrap.Modal(document.getElementById('modalNaoImprimir'));
        modal.show();
        
        // Foca no input após abrir o modal para facilitar o uso de leitor de código de barras
        setTimeout(() => document.getElementById('input_endereco_repor').focus(), 500);
    }

    function confirmarNaoImprimir() {
        let endereco = document.getElementById('input_endereco_repor').value.trim().toUpperCase();
        let qtd = document.getElementById('label_qtd_repor').innerText;
        
        if(!endereco) {
            alert("Por favor, informe o endereço de reposição.");
            document.getElementById('input_endereco_repor').focus();
            return;
        }
        
        // -------------------------------------------------------------
        // AQUI VOCÊ VAI INSERIR SUA CHAMADA DE API (AJAX/FETCH) FUTURA
        // -------------------------------------------------------------
        console.log("=== ENVIANDO PARA API ===");
        console.log("Material:", estadoAtualDesdobro.codMaterial);
        console.log("Quantidade:", qtd);
        console.log("Endereço:", endereco);
        
        // Simulação de fechamento após sucesso
        let modalEl = document.getElementById('modalNaoImprimir');
        bootstrap.Modal.getInstance(modalEl).hide();
        
        somSucesso.play();
        alert(`Sucesso! Material ${estadoAtualDesdobro.codMaterial} encaminhado para o endereço ${endereco}. (API pendente)`);
        
        // Reseta a tela
        document.getElementById('qr_ku').value = '';
        limparCampos();
    }

    // ==========================================
    // 5. CÁLCULOS MATEMÁTICOS E VALIDAÇÕES
    // ==========================================
    function gerarCamposDinamicosKits() {
        let qtdKits = parseInt(document.getElementById('num_kits').value);
        let container = document.getElementById('container-quantidades');
        container.innerHTML = ''; 

        for (let i = 1; i < qtdKits; i++) {
            container.innerHTML += `
                <div class="linha">
                    <label for="qtd_dinamica_${i}">Nova Quantidade ${i}:</label>
                    <input type="number" id="qtd_dinamica_${i}" class="input-dinamico form-control" placeholder="Digite a Qtd" oninput="calcularDistribuicao(this)">
                </div>
            `;
        }
        container.innerHTML += `
            <div class="linha mt-3">
                <label class="text-primary">Quantidade ${qtdKits} (Restante):</label>
                <span id="qtd_restante_kk" class="readonly border-primary text-primary">0</span>
            </div>
        `;
        calcularDistribuicao(); 
    }

    function calcularDistribuicao(elementoAlterado = null) {
        let origem = parseFloat(document.getElementById('qtd_origem_kk').innerText) || 0;
        let inputs = document.querySelectorAll('.input-dinamico');
        let soma = 0;
        
        inputs.forEach(input => { soma += parseFloat(input.value) || 0; });
        let restante = origem - soma;

        if (restante < 0) {
            somErro.play(); 
            alert("Erro: A soma ultrapassa a Quantidade Original!");
            if (elementoAlterado) elementoAlterado.value = ''; 
            calcularDistribuicao(); 
            return;
        }
        let spanRestante = document.getElementById('qtd_restante_kk');
        if(spanRestante) spanRestante.innerText = restante;
    }

    function calcularUnidadeKit() {
        let origem = parseFloat(document.getElementById('qtd_origem_uk').innerText) || 0;
        let qtdKits = parseFloat(document.getElementById('qtd_kits_uk').value) || 0;
        let undPorKit = parseFloat(document.getElementById('und_por_kit_uk').value) || 0;

        let totalUtilizado = qtdKits * undPorKit;
        let restante = origem - totalUtilizado;

        if (restante < 0) {
            somErro.play();
            alert("Operação Inválida!\nFaltam unidades na etiqueta para montar essa quantidade de kits.");
            document.activeElement.value = ''; 
            document.getElementById('total_utilizado_uk').innerText = '0';
            document.getElementById('saldo_uk').innerText = origem;
        } else {
            document.getElementById('total_utilizado_uk').innerText = totalUtilizado;
            document.getElementById('saldo_uk').innerText = restante;
        }
    }

    // ==========================================
    // 6. IMPRESSÃO E GERAÇÃO DA ETIQUETA ZPL/HTML
    // ==========================================
    async function imprimirEtiqueta(abaClicada) {
        let itensParaImprimir = [];
        
        let cod = estadoAtualDesdobro.codMaterial;
        let desc = estadoAtualDesdobro.nomeMaterial || '';
        let forn = estadoAtualDesdobro.fornecedor || 'N/D';
        let un = estadoAtualDesdobro.unidadeMedida || 'UN';

        if (desc.length > 30) desc = desc.substring(0, 30) + '...';

        if (abaClicada === 'Kit-Kit') {
            let inputs = document.querySelectorAll('.input-dinamico');
            inputs.forEach(input => {
                let qtd = parseFloat(input.value) || 0;
                if (qtd > 0) itensParaImprimir.push({ codigo: cod, qtd: qtd, descricao: desc, fornecedor: forn, unidade: un, isControleUnitario: false });
            });
            let restante = parseFloat(document.getElementById('qtd_restante_kk').innerText) || 0;
            if (restante > 0) itensParaImprimir.push({ codigo: cod, qtd: restante, descricao: desc, fornecedor: forn, unidade: un, isControleUnitario: false });

        } else if (abaClicada === 'Kit-Unidade') {
            let qtd = parseFloat(document.getElementById('qtd_unitario_ku').innerText) || 0;
            if (qtd > 0) itensParaImprimir.push({ codigo: cod, qtd: qtd, descricao: desc, fornecedor: forn, unidade: un, isControleUnitario: true });

        } else if (abaClicada === 'Unidade-Kit') {
            let qtdKits = parseFloat(document.getElementById('qtd_kits_uk').value) || 0;
            let undPorKit = parseFloat(document.getElementById('und_por_kit_uk').value) || 0;
            let saldo = parseFloat(document.getElementById('saldo_uk').innerText) || 0;

            for (let i = 0; i < qtdKits; i++) {
                if(undPorKit > 0) itensParaImprimir.push({ codigo: cod, qtd: undPorKit, descricao: desc, fornecedor: forn, unidade: un, isControleUnitario: false });
            }
            if (saldo > 0) itensParaImprimir.push({ codigo: cod, qtd: saldo, descricao: desc + " (SALDO)", fornecedor: forn, unidade: un, isControleUnitario: false });
        }

        if (itensParaImprimir.length === 0) {
            somErro.play();
            alert("Atenção: Não há quantidades preenchidas para imprimir.");
            return;
        }

        if ($('#loadingModal').length) $('#loadingModal').modal('show');

        try {
            const respGetSeq = await fetch(`requests.php?acao=devolver_ultima_sequencia_item&codMaterial=${cod}`);
            const dataGetSeq = await respGetSeq.json();
            
            let seqAtual = 0;
            let objSeq = Array.isArray(dataGetSeq) ? dataGetSeq[0] : dataGetSeq;
            if (objSeq) {
                seqAtual = parseInt(objSeq.sequencia || objSeq.ultima_sequencia || objSeq.ultimaSequencia || objSeq) || 0;
            }

            itensParaImprimir.forEach(item => {
                seqAtual++; 
                item.sequencia = String(seqAtual).padStart(3, '0'); 
            });

            let formSeq = new FormData();
            formSeq.append('acao', 'inserir_atualizar_sequencia_codMaterial');
            formSeq.append('codMaterial', cod);
            formSeq.append('sequencia', seqAtual);

            await fetch('requests.php', { method: 'POST', body: formSeq });

            $('.wms-container').addClass('d-none');
            $('#container-cards').removeClass('d-none').empty();

            $('#container-cards').append(`
                <div class="w-100 mb-3 no-print text-start" style="padding: 15px;">
                    <button type="button" class="btn btn-secondary shadow-sm" onclick="voltarParaDesdobro()">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para Desdobro
                    </button>
                </div>
            `);

            let dataAtual = new Date().toLocaleDateString('pt-BR'); 

            itensParaImprimir.forEach(item => {
                let qrData = "";
                let htmlQuantidade = "";

                if (item.isControleUnitario) {
                    qrData = encodeURIComponent(`${item.codigo}-${item.qtd}-CONTROLE UNITARIO`);
                    htmlQuantidade = `<strong style="font-size: 15px; background-color: #000 !important; color: #fff !important; padding: 3px 6px; border-radius: 4px; line-height: 1.1; white-space: nowrap; -webkit-print-color-adjust: exact; print-color-adjust: exact;">CONTROLE UNITÁRIO</strong>`;
                } else {
                    qrData = encodeURIComponent(`${item.codigo}-${item.sequencia}-${item.qtd}`);
                    htmlQuantidade = `<strong style="font-size: 22px; color: #000; line-height: 1.1; white-space: nowrap;">Qtd: ${item.qtd} <span style="font-size: 14px;">${item.unidade}</span></strong>`;
                }

                const qrUrl = `https://quickchart.io/qr?text=${qrData}&size=100&margin=0`;

                const cardHTML = `
                    <div class="card card-etiqueta" style="border: none; background-color: #fff; margin: 0; padding: 0; border-radius: 0; width: 10.9cm; height: 2.8cm; page-break-after: always; box-sizing: border-box;">
                        <div class="card-body d-flex flex-row align-items-center justify-content-between p-1" style="height: 100%; gap: 0.2cm; padding-left: 1cm !important; padding-right: 0.1cm !important;">
                            
                    <div class="d-flex flex-column justify-content-center" style="width: 9.5cm; overflow: hidden; font-family: Arial, sans-serif;">
                        <strong style="font-size: 28px; color: #000; line-height: 1.1; white-space: nowrap; overflow: hidden;">${item.codigo}</strong>
                        <strong style="font-size: 16px; color: #000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">${item.descricao}</strong>
                        <span style="font-size: 14px; color: #000; line-height: 1.2;">Forn: ${item.fornecedor}</span>
                                
                            <div class="d-flex justify-content-between align-items-center" style="margin-top: 5px;">
                                <strong style="font-size: 26px; color: #000; line-height: 1.1; white-space: nowrap;">${htmlQuantidade}</strong>
                            <strong style="font-size: 12px; color: #000; line-height: 1.1;">Imp: ${dataAtual}</strong>
                                </div>
                            </div>

                            <div class="d-flex flex-column justify-content-center align-items-center" style="width: 80px; min-width: 80px; flex-shrink: 0;">
                                <img class="img-qrcode" src="${qrUrl}" alt="QR Code" style="width: 76px; height: 76px; display: block;">
                                <strong style="font-size: 11px; color: #000; margin-top: 2px; text-align: center; line-height: 1;">Seq: ${item.sequencia}</strong>
                            </div>
                            
                        </div>
                    </div>
                `;
                $('#container-cards').append(cardHTML);
            });

            const imagens = $('#container-cards img.img-qrcode');
            const promessasDeCarregamento = [];

            imagens.each(function() {
                if (!this.complete) {
                    promessasDeCarregamento.push(new Promise((resolve) => {
                        this.onload = resolve;
                        this.onerror = resolve; 
                    }));
                }
            });

            await Promise.all(promessasDeCarregamento);

            if ($('#loadingModal').length) $('#loadingModal').modal('hide');

            setTimeout(() => {
                window.print();
                limparCampos(); 
                document.getElementById('qr_kk').value = '';
                document.getElementById('qr_ku').value = '';
                document.getElementById('qr_uk').value = '';
            }, 150);

        } catch (erro) {
            console.error("Erro na impressão:", erro);
            somErro.play();
            alert("Falha ao comunicar com o banco de dados. Impressão cancelada.");
            if ($('#loadingModal').length) $('#loadingModal').modal('hide');
        }
    }

    function voltarParaDesdobro() {
        $('#container-cards').addClass('d-none').empty();
        $('.wms-container').removeClass('d-none');
    }
</script>