document.addEventListener('DOMContentLoaded', () => {
    // ================= ELEMENTOS DA INTERFACE =================
    const step1 = document.getElementById('step-1');
    const step2Kit = document.getElementById('step-2-kit');
    const step2Unidade = document.getElementById('step-2-unidade');
    const mainCard = document.getElementById('main-card');
    
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
    let targetInputId = ''; // Descobre qual input a câmera deve preencher
    let kitsLidos = [];     // Array que guarda os kits bipados

    // ================= CONTROLE DA CÂMERA =================
    const onScanSuccess = (decodedText) => {
        const targetInput = document.getElementById(targetInputId);
        targetInput.value = decodedText;
        
        // Se a câmera estava lendo um Kit, adiciona na lista automaticamente
        if (targetInputId === 'codigo-kit') {
            adicionarKit();
        }
        
        // Desliga a câmera após o bipe
        html5QrCode.stop().then(() => {
            readerDiv.classList.add('hidden');
            html5QrCode.clear();
            html5QrCode = null;
        }).catch(err => console.error("Erro ao parar câmera:", err));
    };

    // Aplica o evento de abrir a câmera para todos os botões
    document.querySelectorAll('.btn-camera').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const button = e.target.closest('button');
            targetInputId = button.getAttribute('data-target');

            // Se a câmera já estiver aberta, fecha
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
                () => {} // Ignora erros de frame vazio
            ).catch(err => {
                alert("Erro ao acessar a câmera. Verifique as permissões do navegador.");
                readerDiv.classList.add('hidden');
            });
        });
    });

    // ================= TRANSIÇÕES DE TELA =================
    document.getElementById('btn-avancar').addEventListener('click', () => {
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
            
            // Muda o fundo do card para um marrom claro
            mainCard.classList.remove('bg-white');
            mainCard.classList.add('bg-[#fef3c7]', 'border-orange-200');
            
            inputCodigoUnidade.focus();
        }
    });

    // Voltar do Kit
    document.getElementById('btn-voltar-kit').addEventListener('click', () => {
        step2Kit.classList.add('hidden');
        step1.classList.remove('hidden');
    });

    // Voltar da Unidade
    document.getElementById('btn-voltar-unidade').addEventListener('click', () => {
        step2Unidade.classList.add('hidden');
        step1.classList.remove('hidden');
        
        // Remove a cor marrom claro e volta para o fundo branco
        mainCard.classList.remove('bg-[#fef3c7]', 'border-orange-200');
        mainCard.classList.add('bg-white');
    });

    // ================= LÓGICA DO KIT =================
    const adicionarKit = () => {
        const kit = inputCodigoKit.value.trim();
        if (!kit) return;
        
        if (kitsLidos.includes(kit)) {
            alert('Este Kit já foi bipado neste endereço!');
            inputCodigoKit.value = '';
            return;
        }

        kitsLidos.push(kit);
        inputCodigoKit.value = ''; // Limpa o campo para o próximo bipe
        renderizarLista();
        inputCodigoKit.focus();
    };

    // Adiciona o kit ao apertar o botão de "+" ou a tecla "Enter"
    document.getElementById('btn-add-kit').addEventListener('click', adicionarKit);
    inputCodigoKit.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') adicionarKit();
    });

    // Renderiza a lista de kits no HTML
    const renderizarLista = () => {
        listaKitsEl.innerHTML = '';
        contadorKitsEl.textContent = kitsLidos.length;

        kitsLidos.forEach((kit, index) => {
            const li = document.createElement('li');
            li.className = 'flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200';
            li.innerHTML = `
                <span class="font-medium text-gray-700">${kit}</span>
                <button class="text-red-500 hover:text-red-700 btn-remover" data-index="${index}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            `;
            listaKitsEl.appendChild(li);
        });

        // Eventos para remover item da lista
        document.querySelectorAll('.btn-remover').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.target.closest('button').getAttribute('data-index');
                kitsLidos.splice(index, 1);
                renderizarLista();
            });
        });
    };

    // Salvar Lote de Kits
    document.getElementById('btn-finalizar-kit').addEventListener('click', () => {
        if (kitsLidos.length === 0) {
            alert('Bipe ao menos um Kit antes de salvar.');
            return;
        }

        const payload = {
            endereco: inputEndereco.value.trim(),
            tipo: 'kit',
            kits: kitsLidos
        };

        console.log("Pronto para enviar ao banco (Kit):", payload);
        alert('Lote de kits finalizado! (Abra o console para ver os dados)');
        
        // AQUI VOCÊ COLOCA O FETCH PARA O SEU requests.php
    });

    // ================= LÓGICA DA UNIDADE =================
    // Pula para a quantidade ao dar 'Enter' no produto
    inputCodigoUnidade.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && inputCodigoUnidade.value.trim() !== '') {
            inputQtdeUnidade.focus();
        }
    });

    // Salvar Reposição de Unidade
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
        
        // AQUI VOCÊ COLOCA O FETCH PARA O SEU requests.php
        
        // Limpa os campos após salvar para permitir nova leitura
        inputCodigoUnidade.value = '';
        inputQtdeUnidade.value = '';
        inputCodigoUnidade.focus();
    });
});