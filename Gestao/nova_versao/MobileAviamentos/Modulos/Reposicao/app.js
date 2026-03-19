document.addEventListener('DOMContentLoaded', () => {
    // ================= ÁUDIOS =================
    const somErro = new Audio('MenasagemErro.mp3'); 
    const somSucesso = new Audio('MensagemCorrect.mp3');

    // ================= FUNÇÃO DE AVISO =================
    const mostrarAviso = (mensagem, tipo = 'erro') => {
        if (tipo === 'erro') {
            somErro.currentTime = 0; 
            somErro.play().catch(e => console.log("Áudio bloqueado:", e));
        } else {
            somSucesso.currentTime = 0;
            somSucesso.play().catch(e => console.log("Áudio bloqueado:", e));
        }

        const toast = document.createElement('div');
        const corFundo = tipo === 'erro' ? 'bg-red-600' : 'bg-green-600';
        const icone = tipo === 'erro' 
            ? `<svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
            : `<svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

        toast.className = `fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[85%] max-w-sm px-3 py-2 rounded-lg shadow-2xl flex items-center justify-center text-white text-sm font-bold z-[100] transition-all duration-300 opacity-0 scale-90 ${corFundo}`;
        toast.innerHTML = `${icone} <span class="text-center">${mensagem}</span>`;

        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('opacity-0', 'scale-90');
            toast.classList.add('opacity-100', 'scale-100');
        });

        setTimeout(() => {
            toast.classList.remove('opacity-100', 'scale-100');
            toast.classList.add('opacity-0', 'scale-90');
            setTimeout(() => toast.remove(), 300); 
        }, 2500);
    };

    // ================= ELEMENTOS DA INTERFACE =================
    const step1 = document.getElementById('step-1');
    const step2Kit = document.getElementById('step-2-kit');
    const step2Unidade = document.getElementById('step-2-unidade');
    const mainCard = document.getElementById('main-card');
    
    const infoOperadorEl = document.getElementById('info-operador');
    const matriculaOperador = infoOperadorEl ? infoOperadorEl.getAttribute('data-matricula') : '';
    const nomeOperador = infoOperadorEl ? infoOperadorEl.getAttribute('data-usuario') : ''; 
    
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
    
    let html5QrCode = null;
    let targetInputId = ''; 
    let kitsLidos = [];     
    let totalKitsGlobal = 0;
    let totalUnidadesGlobal = 0;
    
    // Variável para armazenar temporariamente a sequência lida da Unidade
    let sequenciaLidaUnidade = ''; 

    // ================= ALTERAÇÃO DINÂMICA DE COR (OPÇÕES) =================
    const radiosTipo = document.querySelectorAll('input[name="tipo_reposicao"]');

    radiosTipo.forEach(radio => {
        radio.addEventListener('change', (e) => {
            mainCard.classList.add('transition-colors', 'duration-300');
            
            if (e.target.value === 'unidade') {
                mainCard.classList.remove('bg-white');
                mainCard.classList.add('bg-[#fef3c7]', 'border-orange-200');
            } else {
                mainCard.classList.remove('bg-[#fef3c7]', 'border-orange-200');
                mainCard.classList.add('bg-white');
            }
        });
    });

    // ================= FUNÇÃO DE AVANÇAR TELA (AGORA COM A CONSULTA INTEGRADA) =================
    const avancarParaProximaTela = () => {
        const endereco = inputEndereco.value.trim();
        const tipoReposicao = document.querySelector('input[name="tipo_reposicao"]:checked').value;

        if (!endereco) {
            mostrarAviso('Por favor, informe o endereço de destino.', 'erro');
            return;
        }

        // Fazer a consulta na API antes de mudar de tela
        const url = `requests.php?acao=get_consultar_endereco&endereco=${encodeURIComponent(endereco)}`;

        fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro na rede ao consultar endereço.');
            return response.json();
        })
        .then(data => {
            // ---> NOVA VALIDAÇÃO DO ENDEREÇO AQUI <---
            if (Array.isArray(data) && data.length > 0 && data[0].status === false) {
                mostrarAviso(data[0].Mensagem, 'erro'); // Mostra "Endereco nao existe"
                inputEndereco.value = '';
                inputEndereco.focus();
                return; // Interrompe o avanço
            }

            // --- SE O ENDEREÇO FOR VÁLIDO, AVANÇA DE TELA ---
            step1.classList.add('hidden');

            if (tipoReposicao === 'kit') {
                displayEndereco.textContent = endereco;
                step2Kit.classList.remove('hidden');
                
                kitsLidos = [];
                totalKitsGlobal = 0;
                totalUnidadesGlobal = 0;
                totalKitsSessaoEl.textContent = "0";
                totalUnidadesSessaoEl.textContent = "0";

                // Se a API retornou itens válidos para esse endereço, popula a lista
                if (Array.isArray(data) && data.length > 0 && data[0].codItem) {
                    data.forEach(item => {
                        if(!item.codItem || !item.qtd) return; 

                        const codItem = item.codItem;
                        const qtd = item.qtd;
                        const seq = item.codItem_seq || '0'; 
                        
                        const rawString = `${codItem}-${qtd}-${seq}`;

                        kitsLidos.push({
                            raw: rawString,
                            item: codItem,
                            qtd: qtd,
                            seq: seq
                        });

                        totalKitsGlobal++;
                        totalUnidadesGlobal += parseInt(qtd, 10);
                    });
                }

                totalKitsSessaoEl.textContent = totalKitsGlobal;
                totalUnidadesSessaoEl.textContent = totalUnidadesGlobal;
                renderizarLista();

                inputCodigoKit.focus(); 
            } else if (tipoReposicao === 'unidade') {
                displayEnderecoUnidade.textContent = endereco;
                step2Unidade.classList.remove('hidden');
                inputCodigoUnidade.focus();
            }
        })
        .catch(error => {
            console.error("Erro detalhado na consulta do endereço:", error);
            mostrarAviso('Erro de comunicação ao validar o endereço.', 'erro');
        });
    };

    // ================= CONTROLE DA CÂMERA =================
    const onScanSuccess = (decodedText) => {
        const targetInput = document.getElementById(targetInputId);
        targetInput.value = decodedText;
        
        html5QrCode.stop().then(() => {
            readerDiv.classList.add('hidden');
            html5QrCode.clear();
            html5QrCode = null;

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
                mostrarAviso("Erro ao acessar a câmera. Verifique as permissões.", "erro");
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
        
        const tipoReposicao = document.querySelector('input[name="tipo_reposicao"]:checked');
        if (tipoReposicao && tipoReposicao.value !== 'unidade') {
            mainCard.classList.remove('bg-[#fef3c7]', 'border-orange-200');
            mainCard.classList.add('bg-white');
        }
        
        // Se desistir da unidade, descongela e limpa os campos para a próxima vez
        inputCodigoUnidade.disabled = false;
        inputCodigoUnidade.value = '';
        inputQtdeUnidade.value = '';
        sequenciaLidaUnidade = ''; // Reseta a sequência
        
        inputEndereco.focus();
    });

    // ================= LÓGICA DO KIT =================
    const adicionarKit = () => {
        const kitRaw = inputCodigoKit.value.trim();
        if (!kitRaw) return;
        
        const partes = kitRaw.split('-');
        if (partes.length !== 3) {
            mostrarAviso('Formato inválido! O QR Code deve ser: ITEM-QTD-SEQUENCIA.', 'erro');
            inputCodigoKit.value = '';
            inputCodigoKit.focus();
            return;
        }

        const codMaterial = partes[0];

        if (codMaterial.length < 5 || !/^[0-9]/.test(codMaterial)) {
            mostrarAviso('Item inválido! O código deve ter pelo menos 5 caracteres e começar com número.', 'erro');
            inputCodigoKit.value = '';
            inputCodigoKit.focus();
            return;
        }

        const qtdReposto = partes[1];
        const sequencia = partes[2];
        const enderecoFinal = inputEndereco.value.trim();

        if (kitsLidos.some(k => k.raw === kitRaw)) {
            mostrarAviso('Esta etiqueta já foi bipada nesta sessão!', 'erro');
            inputCodigoKit.value = '';
            inputCodigoKit.focus();
            return;
        }

        inputCodigoKit.disabled = true;

        const payload = {
            acao: 'inserir_endereco_item_reposto_kit',
            dados: {
                codMaterial: codMaterial,
                qtdReposto: qtdReposto,
                sequencia: sequencia,
                Endereco: enderecoFinal,
                matricula: matriculaOperador,
                usuario: nomeOperador,
                codEmpresa: "1" 
            }
        };

        fetch('requests.php?acao=inserir_endereco_item_reposto_kit', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro na rede ou servidor (Status: ' + response.status + ')');
            return response.json(); 
        })
        .then(data => {
            console.log("Retorno do Servidor:", data);

            if (Array.isArray(data) && data.length > 0 && data[0].status === false) {
                mostrarAviso(data[0].Mensagem, 'erro');
                return; 
            }

            if (data) {
                kitsLidos.push({ 
                    raw: kitRaw, 
                    item: codMaterial, 
                    qtd: qtdReposto,
                    seq: sequencia 
                });
                
                renderizarLista();
                
                totalKitsGlobal++;
                totalUnidadesGlobal += parseInt(qtdReposto, 10); 
                
                totalKitsSessaoEl.textContent = totalKitsGlobal;
                totalUnidadesSessaoEl.textContent = totalUnidadesGlobal;

                mostrarAviso(`Kit ${codMaterial} adicionado!`, 'sucesso');
            } else {
                throw new Error('A API recusou a gravação dos dados.');
            }
        })
        .catch(error => {
            console.error("Erro detalhado:", error);
            mostrarAviso("Erro ao salvar: " + error.message, "erro");
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
            li.className = 'flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200 animate-fade-in';
            li.innerHTML = `
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Item</span>
                    <span class="font-bold text-gray-500 text-lg">${kitObj.item}</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Seq</span>
                    <span class="text-gray-400 font-medium">${kitObj.seq}</span>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Qtd</span>
                    <span class="bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-md text-lg">${kitObj.qtd}</span>
                </div>
            `;
            listaKitsEl.appendChild(li);
        });
    };

    document.getElementById('btn-finalizar-kit').addEventListener('click', () => {
        if (kitsLidos.length > 0) mostrarAviso('Sessão finalizada!', 'sucesso');
        kitsLidos = [];
        renderizarLista();
        step2Kit.classList.add('hidden');
        step1.classList.remove('hidden');
        inputEndereco.value = '';
        inputEndereco.focus();
    });

    // ================= LÓGICA DA UNIDADE =================

    inputCodigoUnidade.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            
            const qrCodeLido = inputCodigoUnidade.value.trim();
            
            if (!qrCodeLido) return;

            if (!qrCodeLido.endsWith('CONTROLE UNITARIO')) {
                mostrarAviso('Etiqueta fora do padrão para Controle Unitário!', 'erro');
                inputCodigoUnidade.value = ''; 
                inputCodigoUnidade.focus();    
                return;
            }

            const partes = qrCodeLido.split('-');
            
            if (partes.length >= 2) {
                const codMaterial = partes[0];

                if (codMaterial.length < 5 || !/^[0-9]/.test(codMaterial)) {
                    mostrarAviso('Item inválido! O código deve ter pelo menos 5 caracteres e começar com número.', 'erro');
                    inputCodigoUnidade.value = '';
                    inputCodigoUnidade.focus();
                    return;
                }
                
                sequenciaLidaUnidade = partes[1].replace('CONTROLE UNITARIO', '').trim();
                
                inputCodigoUnidade.value = codMaterial;
                inputCodigoUnidade.disabled = true;
                mostrarAviso('Etiqueta Válida!', 'sucesso');
                inputQtdeUnidade.focus();
            } else {
                mostrarAviso('Formato de etiqueta não reconhecido.', 'erro');
                inputCodigoUnidade.value = '';
            }
        }
    });

    const btnLimparUnidade = document.getElementById('btn-limpar-unidade');
    if (btnLimparUnidade) {
        btnLimparUnidade.addEventListener('click', () => {
            inputCodigoUnidade.disabled = false; 
            inputCodigoUnidade.value = '';
            inputQtdeUnidade.value = '';
            sequenciaLidaUnidade = ''; 
            inputCodigoUnidade.focus();
        });
    }

    document.getElementById('btn-finalizar-unidade').addEventListener('click', () => {
        const produto = inputCodigoUnidade.value.trim();
        const quantidade = inputQtdeUnidade.value.trim();
        const enderecoFinal = inputEndereco.value.trim();

        if (!produto || !quantidade || quantidade <= 0) {
            mostrarAviso('Informe dados válidos.', 'erro');
            return;
        }

        const payloadUnidade = {
            acao: 'inserir_endereco_item_reposto_unitario',
            dados: {
                codMaterial: produto,
                qtdReposto: quantidade,
                Endereco: enderecoFinal,
                sequencia: sequenciaLidaUnidade,
                usuario: nomeOperador,
                matricula: matriculaOperador
            }
        };

        fetch('requests.php?acao=inserir_endereco_item_reposto_unitario', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json' 
            },
            body: JSON.stringify(payloadUnidade)
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro de comunicação com o servidor');
            return response.json();
        })
        .then(data => {
            if (Array.isArray(data) && data.length > 0 && data[0].status === false) {
                mostrarAviso(data[0].Mensagem || 'Erro ao processar a unidade.', 'erro');
                return;
            }

            mostrarAviso('Unidade salva!', 'sucesso');
            totalUnidadesGlobal += parseInt(quantidade, 10);
            totalUnidadesSessaoEl.textContent = totalUnidadesGlobal;
            
            inputCodigoUnidade.disabled = false;
            inputCodigoUnidade.value = '';
            inputQtdeUnidade.value = '';
            sequenciaLidaUnidade = ''; 
            inputCodigoUnidade.focus();
        })
        .catch((error) => mostrarAviso('Erro ao salvar unidade: ' + error.message, 'erro'));
    });

});