document.addEventListener('DOMContentLoaded', () => {
    // Elementos da interface
    const step1 = document.getElementById('step-1');
    const step2Kit = document.getElementById('step-2-kit');
    const inputEndereco = document.getElementById('endereco');
    const displayEndereco = document.getElementById('display-endereco');
    const inputCodigoKit = document.getElementById('codigo-kit');
    const listaKitsEl = document.getElementById('lista-kits');
    const contadorKitsEl = document.getElementById('contador-kits');
    const readerDiv = document.getElementById('reader');
    
    let html5QrCode;
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
        });
    };

    // Aplica o evento de abrir a câmera para todos os botões com a classe .btn-camera
    document.querySelectorAll('.btn-camera').forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Pega o botão clicado (mesmo se clicar no SVG dentro dele)
            const button = e.target.closest('button');
            targetInputId = button.getAttribute('data-target');

            if (!readerDiv.classList.contains('hidden')) {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        readerDiv.classList.add('hidden');
                        html5QrCode.clear();
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
                alert("Erro ao acessar a câmera. Verifique as permissões.");
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

        if (tipoReposicao === 'kit') {
            displayEndereco.textContent = endereco;
            step1.classList.add('hidden');
            step2Kit.classList.remove('hidden');
            // Foca automaticamente no input do kit para quem usa leitor laser
            inputCodigoKit.focus(); 
        } else {
            alert('A tela de Unidade está em desenvolvimento.');
        }
    });

    document.getElementById('btn-voltar-kit').addEventListener('click', () => {
        step2Kit.classList.add('hidden');
        step1.classList.remove('hidden');
        // Opcional: Limpar a lista de kits ao voltar
        // kitsLidos = []; renderizarLista();
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

    // Ouve o Enter ou o botão de "+"
    document.getElementById('btn-add-kit').addEventListener('click', adicionarKit);
    inputCodigoKit.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') adicionarKit();
    });

    // Renderiza a lista no HTML
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

        // Eventos para remover item
        document.querySelectorAll('.btn-remover').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.target.closest('button').getAttribute('data-index');
                kitsLidos.splice(index, 1);
                renderizarLista();
            });
        });
    };

    // ================= FINALIZAR =================
    document.getElementById('btn-finalizar-kit').addEventListener('click', () => {
        if (kitsLidos.length === 0) {
            alert('Bipe ao menos um Kit antes de salvar.');
            return;
        }

        const enderecoFinal = inputEndereco.value;
        const payload = {
            endereco: enderecoFinal,
            tipo: 'kit',
            kits: kitsLidos
        };

        console.log("Pronto para enviar ao banco:", payload);
        alert('Lote finalizado! (Abra o console para ver o Payload)');
        // AQUI VOCÊ COLOCA O FETCH PARA O SEU requests.php
    });
});