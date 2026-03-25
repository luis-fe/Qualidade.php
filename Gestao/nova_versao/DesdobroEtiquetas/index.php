<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<style>
    /* ==========================================================================
       ESTILOS DA TELA (SCREEN) - Foco em não ter rolagem
       ========================================================================== */
    .wms-container { max-width: 800px; margin: 10px auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .wms-container h2 { color: #333; margin-top: 0; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #f0f0f0; font-size: 1.5rem; }
    .botoes-opcao { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
    .btn-opcao { padding: 8px 15px; background-color: #f8f9fa; color: #495057; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-opcao:hover { background-color: #e2e6ea; }
    .btn-opcao.ativo { background-color: #0d6efd; color: white; border-color: #0d6efd; box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3); }

    .card-material { display: none; background-color: #e3f2fd; border: 1px solid #90caf9; border-radius: 6px; padding: 8px 12px; margin-bottom: 15px; align-items: center; gap: 10px; }
    .badge-codigo { background-color: #0d6efd; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.9rem; }
    .nome-material { font-size: 0.95rem; color: #052c65; font-weight: 600; margin: 0; }

    .secao-desdobro { display: none; background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 15px 20px; border-radius: 8px; }
    .secao-desdobro h3 { margin-top: 0; color: #0d6efd; margin-bottom: 15px; font-size: 1.1rem; }
    .linha { display: flex; align-items: center; margin-bottom: 12px; gap: 10px; flex-wrap: wrap; }
    .linha label { font-weight: 600; color: #495057; min-width: 170px; font-size: 0.95rem; }
    .linha input[type="text"], .linha input[type="number"], .linha select { padding: 8px 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px; width: 100%; max-width: 250px; }
    .linha input:focus { outline: none; border-color: #86b7fe; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25); }
    
    .readonly { background-color: #e9ecef; border: 1px solid #ced4da; padding: 6px 12px; border-radius: 5px; font-weight: bold; color: #212529; display: inline-block; min-width: 80px; text-align: center; font-size: 0.95rem; }
    .divisor-tela { border: none; border-top: 2px dashed #ced4da; margin: 15px 0; width: 100%; }
    
    .btn-imprimir { background-color: #198754; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; margin-top: 10px; display: flex; align-items: center; gap: 8px; }
    .btn-imprimir:hover { background-color: #157347; }

    .select2-container .select2-selection--single { height: 36px; padding: 3px; border: 1px solid #ced4da; border-radius: 5px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 34px; }

    /* ==========================================================================
       ESTILOS DE IMPRESSÃO (Padrão Zebra)
       ========================================================================== */
    @media print {
        .no-print, .wms-container, header, footer, #loadingModal, .span-icone { display: none !important; }

        body * { visibility: hidden; }
        #container-cards, #container-cards * { visibility: visible; }
        
        #container-cards {
            /* Definindo a largura do container para bater com a página */
            width: 15.0cm !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        .card-etiqueta {
            /* Aumentei a largura para 15cm e a altura para 4cm para dar mais respiro */
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
    <h2>Processo de Desdobro</h2>

    <div class="botoes-opcao">
        <button id="btn-kit-kit" class="btn-opcao" onclick="mostrarSecao('kit-kit')">Kit-Kit</button>
        <button id="btn-kit-unidade" class="btn-opcao" onclick="mostrarSecao('kit-unidade')">Kit-Unidade</button>
        <button id="btn-unidade-kit" class="btn-opcao" onclick="mostrarSecao('unidade-kit')">Unidade-Kit</button>
    </div>

    <div id="sec-kit-kit" class="secao-desdobro">
        <h3>Opção: Kit - Kit</h3>
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
            <button id="btn_imprimir_kk" class="btn-imprimir no-print" onclick="imprimirEtiqueta('Kit-Kit')">🖨️ Imprimir Novas Etiquetas</button>
        </div>
    </div>

    <div id="sec-kit-unidade" class="secao-desdobro">
        <h3>Opção: Kit - Unidade</h3>
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
            <button id="btn_imprimir_ku" class="btn-imprimir no-print" onclick="imprimirEtiqueta('Kit-Unidade')">🖨️ Imprimir Novas Etiquetas</button>
        </div>
    </div>

    <div id="sec-unidade-kit" class="secao-desdobro">
        <h3>Opção: Unidade - Kit</h3>
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
                <span id="total_utilizado_uk" class="readonly" style="background-color: #fff3cd; color: #856404;">0</span>
            </div>
            <div class="linha">
                <label>Saldo Restante:</label>
                <span id="saldo_uk" class="readonly">0</span>
            </div>
            <button id="btn_imprimir_uk" class="btn-imprimir no-print" onclick="imprimirEtiqueta('Unidade-Kit')">🖨️ Imprimir Novas Etiquetas</button>
        </div>
    </div>

</div>

<div id="container-cards" class="d-none d-flex flex-wrap gap-2 mt-3 p-2"></div>

<div class="modal fade" id="modalConfirmarPendencia" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Estorno da Etiqueta
                </h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-0 fs-5">O material lido está correto.<br>Deseja confirmar o estorno desta etiqueta do estoque para iniciar o desdobro?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" onclick="cancelarEstorno()">
                    <i class="bi bi-x-lg me-1"></i> Cancela
                </button>
                <button type="button" class="btn btn-warning px-4 fw-bold text-dark" onclick="efetivarFinalizacaoPendencia()">
                    <i class="bi bi-check-lg me-1"></i> Sim
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    // ==========================================
    // 1. CONFIGURAÇÃO DOS SONS
    // ==========================================
    const somAlerta = new Audio('MensagemAlerta.mp3'); 
    const somSucesso = new Audio('MensagemCorrect.mp3');
    const somErro = new Audio('MenasagemErro.mp3');

    // ==========================================
    // 2. VARIÁVEL GLOBAL (MEMÓRIA DO DESDOBRO)
    // ==========================================
    let estadoAtualDesdobro = { 
        tipo: null, 
        codMaterial: null, 
        sequencia: null, 
        qtd: null, 
        nomeMaterial: null,
        fornecedor: null,
        unidadeMedida: null
    };

    $(document).ready(function() {
        $('#num_kits').select2({ minimumResultsForSearch: Infinity });
        $('#num_kits').on('change', function() { gerarCamposDinamicosKits(); });
    });

    // ==========================================
    // 3. NAVEGAÇÃO DAS ABAS
    // ==========================================
    function mostrarSecao(secao) {
        document.getElementById('sec-kit-kit').style.display = 'none';
        document.getElementById('sec-kit-unidade').style.display = 'none';
        document.getElementById('sec-unidade-kit').style.display = 'none';
        
        document.getElementById('btn-kit-kit').classList.remove('ativo');
        document.getElementById('btn-kit-unidade').classList.remove('ativo');
        document.getElementById('btn-unidade-kit').classList.remove('ativo');

        document.getElementById('sec-' + secao).style.display = 'block';
        document.getElementById('btn-' + secao).classList.add('ativo');
        
        limparCampos();
    }

    mostrarSecao('kit-kit');

    // ==========================================
    // 4. LEITURA DO QR CODE E BUSCA (GET)
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

        // Extrai os dados do QR Code
        let codItemExtraido = partes[0].trim();
        let sequenciaExtraida = partes[1].trim(); 
        let qtdExtraida = parseFloat(partes[2].trim());

        // PROATIVIDADE: Se bipar uma etiqueta de Controle Unitário, o padrão é diferente (cod - qtd - CONTROLE)
        if (partes[2].toUpperCase().includes("CONTROLE UNITARIO")) {
            qtdExtraida = parseFloat(partes[1].trim()); // Puxa a quantidade do meio
            sequenciaExtraida = "N/A"; // Não usa sequência
        }

        if (tipo === 'kk') document.getElementById('qtd_origem_kk').innerText = qtdExtraida;
        if (tipo === 'ku') document.getElementById('qtd_origem_ku').innerText = qtdExtraida;
        if (tipo === 'uk') document.getElementById('qtd_origem_uk').innerText = qtdExtraida;

        document.getElementById(cardId).style.display = 'flex';
        document.getElementById(codDisplayId).innerText = codItemExtraido;
        document.getElementById(nomeDisplayId).innerText = "Buscando informações...";
        document.getElementById(nomeDisplayId).style.color = '#052c65';

        fetch(`requests.php?acao=obterNomeItem&codMaterial=${codItemExtraido}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0 && data[0].nomeMaterial) {
                    let itemAPI = data[0];
                    let nomeProduto = itemAPI.nomeMaterial;
                    let fornecedorProduto = itemAPI.fornecedor || 'N/D';
                    let unidadeProduto = itemAPI.unidadeMedida || 'UN';

                    document.getElementById(nomeDisplayId).innerText = nomeProduto;
                    
                    somAlerta.play();
                    
                    estadoAtualDesdobro = { 
                        tipo: tipo, codMaterial: codItemExtraido, sequencia: sequenciaExtraida, 
                        qtd: qtdExtraida, nomeMaterial: nomeProduto, fornecedor: fornecedorProduto,
                        unidadeMedida: unidadeProduto
                    };
                    
                    let modalConf = new bootstrap.Modal(document.getElementById('modalConfirmarPendencia'));
                    modalConf.show();

                } else {
                    dispararErroProdutoNaoEncontrado(nomeDisplayId, false);
                }
            })
            .catch(error => {
                console.error("Erro na API GET:", error);
                dispararErroProdutoNaoEncontrado(nomeDisplayId, true);
            });
    }

    // ==========================================
    // 5. ESTORNO DA ETIQUETA (POST)
    // ==========================================
    function efetivarFinalizacaoPendencia() {
        let modalEl = document.getElementById('modalConfirmarPendencia');
        let modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        let tipo = estadoAtualDesdobro.tipo;
        let nomeDisplayId = `display_nome_item_${tipo}`;

        document.getElementById(nomeDisplayId).innerText = "Estornando etiqueta no banco...";
        document.getElementById(nomeDisplayId).style.color = "#d39e00"; 

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
                document.getElementById(nomeDisplayId).style.color = "green";

                document.getElementById(`campos_extras_${tipo}`).style.display = 'block';

                if (tipo === 'kk') {
                    gerarCamposDinamicosKits();
                    setTimeout(() => document.getElementById('qtd_dinamica_1').focus(), 100);
                } else if (tipo === 'ku') {
                    document.getElementById('qtd_unitario_ku').innerText = estadoAtualDesdobro.qtd;
                } else if (tipo === 'uk') {
                    document.getElementById('saldo_uk').innerText = estadoAtualDesdobro.qtd;
                    document.getElementById('qtd_kits_uk').value = '';
                    document.getElementById('und_por_kit_uk').value = '';
                    document.getElementById('total_utilizado_uk').innerText = '0';
                    setTimeout(() => document.getElementById('qtd_kits_uk').focus(), 100);
                }
            } else {
                somErro.play();
                let msgErro = retorno && retorno.Mensagem ? retorno.Mensagem : "Erro desconhecido na API";
                alert("Falha no Estorno: " + msgErro);
                limparCampos();
            }
        })
        .catch(error => {
            console.error("Erro no POST:", error);
            somErro.play();
            alert("Erro de conexão ao tentar estornar a etiqueta.");
            limparCampos();
        });
    }

    function cancelarEstorno() {
        limparCampos();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove()); 
    }

    function dispararErroProdutoNaoEncontrado(nomeDisplayId, erroConexao) {
        somErro.play();
        document.getElementById(nomeDisplayId).innerText = erroConexao ? "❌ Erro de conexão." : "⚠️ Material não encontrado!";
        document.getElementById(nomeDisplayId).style.color = "red";
    }

    // ==========================================
    // 6. CÁLCULOS MATEMÁTICOS E VALIDAÇÕES
    // ==========================================
    function gerarCamposDinamicosKits() {
        let qtdKits = parseInt(document.getElementById('num_kits').value);
        let container = document.getElementById('container-quantidades');
        container.innerHTML = ''; 

        for (let i = 1; i < qtdKits; i++) {
            container.innerHTML += `
                <div class="linha">
                    <label for="qtd_dinamica_${i}">Nova Quantidade ${i}:</label>
                    <input type="number" id="qtd_dinamica_${i}" class="input-dinamico" placeholder="Digite a Qtd" oninput="calcularDistribuicao(this)">
                </div>
            `;
        }
        container.innerHTML += `
            <div class="linha">
                <label>Quantidade ${qtdKits} (Restante):</label>
                <span id="qtd_restante_kk" class="readonly">0</span>
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

    function limparCampos() {
        let inputsText = document.querySelectorAll('input[type="text"]');
        inputsText.forEach(input => input.value = '');
        let inputsNum = document.querySelectorAll('input[type="number"]');
        inputsNum.forEach(input => input.value = '');
        let readonlySpans = document.querySelectorAll('.readonly');
        readonlySpans.forEach(span => span.innerText = '0');

        ['kk', 'ku', 'uk'].forEach(tipo => {
            let card = document.getElementById(`card_info_material_${tipo}`);
            if(card) card.style.display = 'none';
            let campos = document.getElementById(`campos_extras_${tipo}`);
            if(campos) campos.style.display = 'none';
            let displayNome = document.getElementById(`display_nome_item_${tipo}`);
            if(displayNome) displayNome.style.color = '#052c65';
        });
    }

    // ==========================================
    // 7. IMPRESSÃO E GERAÇÃO DA ETIQUETA ZPL/HTML
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
            // CONSULTA A ÚLTIMA SEQUÊNCIA
            const respGetSeq = await fetch(`requests.php?acao=devolver_ultima_sequencia_item&codMaterial=${cod}`);
            const dataGetSeq = await respGetSeq.json();
            
            let seqAtual = 0;
            let objSeq = Array.isArray(dataGetSeq) ? dataGetSeq[0] : dataGetSeq;
            if (objSeq) {
                seqAtual = parseInt(objSeq.sequencia || objSeq.ultima_sequencia || objSeq.ultimaSequencia || objSeq) || 0;
            }

            // APLICA NOVA SEQUÊNCIA (X + 1)
            itensParaImprimir.forEach(item => {
                seqAtual++; 
                item.sequencia = String(seqAtual).padStart(3, '0'); 
            });

            // ATUALIZA A SEQUÊNCIA NO BANCO
            let formSeq = new FormData();
            formSeq.append('acao', 'inserir_atualizar_sequencia_codMaterial');
            formSeq.append('codMaterial', cod);
            formSeq.append('sequencia', seqAtual);

            await fetch('requests.php', { method: 'POST', body: formSeq });

            // PREPARA A TELA VISUALMENTE
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

            // DESENHA O LAYOUT DE CADA ETIQUETA
            itensParaImprimir.forEach(item => {
                
                let qrData = "";
                let htmlQuantidade = "";

                // Verifica se é Controle Unitário para mudar o layout (Fundo Preto / Letra Branca)
                if (item.isControleUnitario) {
                    qrData = encodeURIComponent(`${item.codigo}-${item.qtd}-CONTROLE UNITARIO`);
                    // Usando -webkit-print-color-adjust para forçar a impressão da cor de fundo no Chrome/Edge
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

            // CARREGA IMAGENS ASSÍNCRONAMENTE E IMPRIME
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