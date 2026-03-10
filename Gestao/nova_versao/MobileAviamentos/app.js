document.addEventListener('DOMContentLoaded', () => {
    // ================= ELEMENTOS DA INTERFACE =================
    const step1 = document.getElementById('step-1');
    const step2Kit = document.getElementById('step-2-kit');
    const step2Unidade = document.getElementById('step-2-unidade');
    const mainCard = document.getElementById('main-card');
    
    // Elementos dos Totalizadores
    const totalKitsSessaoEl = document.getElementById('total-kits-sessao');
    const totalUnidadesSessaoEl = document.getElementById('total-unidades-sessao');
    
    const inputEndereco = document.getElementById('endereco');
    const displayEndereco = document.getElementById('display-endereco');
    const displayEnderecoUnidade = document.getElementById('display-endereco-unidade');
    
    const inputCodigoKit = document.getElementById('codigo-kit');
    const listaKitsEl = document.getElementById('lista-kits');
    const contadorKitsEl = document.getElementById('contador-kits');
    
    const inputCodigoUnidade = document.getElementById('codigo-unidade');
    const inputQtdeUnidade = document.getElementById('qtde-unidade');
    
    const readerDiv = document.getElementById('reader');
    
    // Variáveis de Estado
    let html5QrCode = null;
    let targetInputId = ''; 
    let kitsLidos = [];     
    let totalKitsGlobal = 0;
    let totalUnidadesGlobal = 0;

    // ================= FUNÇÃO DE AVANÇAR TELA =================
    const avancarParaProximaTela = () => {
        const endereco = inputEndereco.value.trim();
        const tipoReposicao = document.querySelector('input[name="tipo_reposicao"]:checked').value;

        if (!endereco) {
            alert('Por favor, informe o endereço de destino.');
            return;
        }

        step1.classList.add('hidden');

        if (tipoReposicao === 'kit') {
            displayEndereco.textContent = endereco;
            step2Kit.classList.remove('hidden');
            inputCodigoKit.focus(); 
        } else if (tipoReposicao === 'unidade') {
            displayEnderecoUnidade.textContent = endereco;
            step2Unidade.classList.remove('hidden');
            
            mainCard.classList.remove('bg-white');
            mainCard.classList.add('bg-[#fef3c7]', 'border-orange-200');
            
            inputCodigoUnidade.focus();
        }
    };

    // ================= CONTROLE DA CÂMERA =================
    const onScanSuccess = (decodedText) => {
        const targetInput = document.getElementById(targetInputId);
        targetInput.value = decodedText;
        
        html5QrCode.stop().then(() => {
            readerDiv.classList.add('hidden');
            html5QrCode.clear();
            html5QrCode = null;

            // Avança ou adiciona automaticamente após bipar com a câmera
            if (targetInputId === 'endereco') {
                avancarParaProximaTela();
            } else if (targetInputId === 'codigo-kit') {
                adicionarKit();
            }
        }).catch(err => console.error("Erro ao parar câmera:", err));
    };

    document.querySelectorAll('.btn-camera').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const button = e.target.closest('button');
            targetInputId = button.getAttribute('data-target');

            if (!readerDiv.classList.contains('hidden')) {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        readerDiv.classList.add('hidden');
                        html5QrCode.clear();
                        html5QrCode = null;
                    });
                }
                return;
            }

            readerDiv.classList.remove('hidden');
            html5QrCode = new Html5Qrcode("reader");
            
            html5QrCode.start(
                { facingMode: "environment" }, 
                { fps: 10, qrbox: { width: 250, height: 250 }, formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ] },
                onScanSuccess,
                () => {} 
            ).catch(err => {
                alert("Erro ao acessar a câmera. Verifique as permissões do navegador.");
                readerDiv.classList.add('hidden');
            });
        });
    });

    // ================= TRANSIÇÕES DE TELA =================
    document.getElementById('btn-avancar').addEventListener('click', avancarParaProximaTela);

    inputEndereco.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            avancarParaProximaTela();
        }
    });

    document.getElementById('btn-voltar-kit').addEventListener('click', () => {
        step2Kit.classList.add('hidden');
        step1.classList.remove('hidden');
        inputEndereco.focus();
    });

    document.getElementById('btn-voltar-unidade').addEventListener('click', () => {
        step2Unidade.classList.add('hidden');
        step1.classList.remove('hidden');
        
        mainCard.classList.remove('bg-[#fef3c7]', 'border-orange-200');
        mainCard.classList.add('bg-white');
        inputEndereco.focus();
    });

    // ================= LÓGICA DO KIT =================
    const adicionarKit = () => {
        const kitRaw = inputCodigoKit.value.trim();
        if (!kitRaw) return;
        
        // Separa o formato ITEM-QTD-SEQUENCIA
        const partes = kitRaw.split('-');
        
        if (partes.length !== 3) {
            alert('Formato inválido! O QR Code deve ser no formato: ITEM-QTD-SEQUENCIA.');
            inputCodigoKit.value = '';
            inputCodigoKit.focus();
            return;
        }

        const codMaterial = partes[0];
        const qtdReposto = partes[1];
        const sequencia = partes[2];
        const enderecoFinal = inputEndereco.value.trim();

        // Evita bipar o mesmo código exato (mesma sequência)
        if (kitsLidos.some(k => k.raw === kitRaw)) {
            alert('Esta etiqueta (sequência) já foi bipada nesta sessão!');
            inputCodigoKit.value = '';
            inputCodigoKit.focus();
            return;
        }

        const payload = {
            codMaterial: codMaterial,
            qtdReposto: qtdReposto,
            sequencia: sequencia,
            Endereco: enderecoFinal,
            codEmpresa: "1" 
        };

        inputCodigoKit.disabled = true;

        fetch('requests.php?acao=inserir_endereco_item_reposto_kit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro na resposta do servidor');
            return response.text(); 
        })
        .then(textData => {
            console.log("Salvo no banco:", textData);
            
            kitsLidos.push({ 
                raw: kitRaw, 
                item: codMaterial, 
                qtd: qtdReposto,
                seq: sequencia 
            });
            
            renderizarLista();

            // ==========================================
            // CORREÇÃO: SOMA NO TOTALIZADOR DE KITS E UNIDADES
            // ==========================================
            totalKitsGlobal++;
            totalUnidadesGlobal += parseInt(qtdReposto, 10); // Adiciona a QTD extraída do QR Code
            
            totalKitsSessaoEl.textContent = totalKitsGlobal;
            totalUnidadesSessaoEl.textContent = totalUnidadesGlobal; // Atualiza a tela
            // ==========================================
        })
        .catch(error => {
            console.error("Erro no Fetch:", error);
            alert("Erro ao salvar o Kit no banco de dados. Verifique sua conexão.");
        })
        .finally(() => {
            inputCodigoKit.disabled = false;
            inputCodigoKit.value = ''; 
            inputCodigoKit.focus();
        });
    };

    document.getElementById('btn-add-kit').addEventListener('click', adicionarKit);
    inputCodigoKit.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            adicionarKit();
        }
    });

    const renderizarLista = () => {
        listaKitsEl.innerHTML = '';
        contadorKitsEl.textContent = kitsLidos.length;

        kitsLidos.forEach((kitObj) => {
            const li = document.createElement('li');
            li.className = 'flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200';
            
            li.innerHTML = `
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Item</span>
                    <span class="font-bold text-gray-800 text-lg">${kitObj.item}</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Seq</span>
                    <span class="text-gray-600 font-medium">${kitObj.seq}</span>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Qtd</span>
                    <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-md text-lg">${kitObj.qtd}</span>
                </div>
            `;
            listaKitsEl.appendChild(li);
        });
    };

    document.getElementById('btn-finalizar-kit').addEventListener('click', () => {
        // Limpa a lista atual e volta para a tela inicial
        kitsLidos = [];
        renderizarLista();
        step2Kit.classList.add('hidden');
        step1.classList.remove('hidden');
        inputEndereco.value = '';
        inputEndereco.focus();
    });

    // ================= LÓGICA DA UNIDADE =================
    inputCodigoUnidade.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && inputCodigoUnidade.value.trim() !== '') {
            e.preventDefault();
            inputQtdeUnidade.focus();
        }
    });

    document.getElementById('btn-finalizar-unidade').addEventListener('click', () => {
        const produto = inputCodigoUnidade.value.trim();
        const quantidade = inputQtdeUnidade.value.trim();
        const enderecoFinal = inputEndereco.value.trim();

        if (!produto || !quantidade || quantidade <= 0) {
            alert('Por favor, informe o produto e uma quantidade válida.');
            return;
        }

        const payload = {
            endereco: enderecoFinal,
            tipo: 'unidade',
            produto: produto,
            quantidade: quantidade
        };

        console.log("Pronto para enviar ao banco (Unidade):", payload);
        alert('Reposição de unidade finalizada! (Abra o console para ver os dados)');
        
        // SOMA NO TOTALIZADOR DE UNIDADES (Caso ele use a tela de unidades também)
        totalUnidadesGlobal += parseInt(quantidade, 10);
        totalUnidadesSessaoEl.textContent = totalUnidadesGlobal;
        
        // AQUI VOCÊ PODE ADICIONAR O SEU FETCH PARA requests.php PARA SALVAR A UNIDADE NO BANCO
        
        inputCodigoUnidade.value = '';
        inputQtdeUnidade.value = '';
        inputCodigoUnidade.focus();
    });
});