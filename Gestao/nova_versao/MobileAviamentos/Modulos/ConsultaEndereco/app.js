document.addEventListener("DOMContentLoaded", () => {
    // --- ELEMENTOS DA TELA ---
    const inputEndereco = document.getElementById("endereco-consulta");
    const btnConsultar = document.getElementById("btn-consultar");
    const btnCamera = document.querySelector(".btn-camera");
    const btnNovaConsulta = document.getElementById("btn-nova-consulta");
    
    const divReader = document.getElementById("reader");
    const stepConsulta = document.getElementById("step-consulta");
    const stepResultado = document.getElementById("resultado-consulta");
    
    const displayEndereco = document.getElementById("display-endereco-resultado");
    const indicadorTotal = document.getElementById("indicador-qtd-total");
    const indicadorKits = document.getElementById("indicador-qtd-kits");
    const conteudoResultado = document.getElementById("conteudo-resultado");

    let html5QrCode = null;

    // --- 1. CONTROLE DA CÂMERA (HTML5-QRCode) ---
    btnCamera.addEventListener("click", () => {
        if (divReader.style.display === "block") {
            pararCamera();
            return;
        }

        divReader.style.display = "block";
        html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                inputEndereco.value = decodedText;
                pararCamera();
                consultarEndereco(); // Dispara a consulta ao ler o QR Code
            },
            (errorMessage) => {
                // Erros de leitura contínuos são ignorados aqui
            }
        ).catch(err => {
            console.error("Erro ao iniciar a câmera", err);
            alert("Não foi possível acessar a câmera.");
            divReader.style.display = "none";
        });
    });

    function pararCamera() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                divReader.style.display = "none";
            }).catch(err => console.error("Erro ao parar câmera", err));
        }
    }

    // --- 2. GATILHOS DE CONSULTA ---
    btnConsultar.addEventListener("click", consultarEndereco);

    inputEndereco.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            consultarEndereco();
        }
    });

    btnNovaConsulta.addEventListener("click", () => {
        stepResultado.classList.add("hidden");
        stepConsulta.classList.remove("hidden");
        inputEndereco.value = "";
        inputEndereco.focus();
    });

    // --- 3. LÓGICA DE BUSCA E RENDERIZAÇÃO ---
    async function consultarEndereco() {
        const endereco = inputEndereco.value.trim().toUpperCase();

        if (!endereco) {
            alert("Por favor, informe ou bipe um endereço.");
            inputEndereco.focus();
            return;
        }

        btnConsultar.innerText = "Consultando...";
        btnConsultar.disabled = true;

        try {
            const url = `requests.php?acao=get_consultar_endereco&endereco=${encodeURIComponent(endereco)}`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error("Erro na comunicação com o servidor.");
            }

            const data = await response.json();

            // ---> NOVA VALIDAÇÃO DO ENDEREÇO AQUI <---
            if (Array.isArray(data) && data.length > 0 && data[0].status === false) {
                alert(data[0].Mensagem); // Exibe "Endereco nao existe"
                inputEndereco.value = "";
                inputEndereco.focus();
                return; // Interrompe a função e não muda de tela
            }
            // -----------------------------------------

            if (!Array.isArray(data)) {
                alert("Erro ao interpretar os dados do endereço.");
                return;
            }

            renderizarResultados(endereco, data);

        } catch (error) {
            console.error("Erro na requisição:", error);
            alert("Ocorreu um erro ao comunicar com o servidor.");
        } finally {
            btnConsultar.innerText = "Consultar";
            btnConsultar.disabled = false;
        }
    }

    function renderizarResultados(endereco, itens) {
        displayEndereco.innerText = endereco;
        conteudoResultado.innerHTML = ""; // Limpa os resultados anteriores

        let qtdTotal = 0;
        let qtdKits = 0;

        // Se a API retornar um array vazio (o que significa que o endereço existe, mas não tem nada dentro)
        if (itens.length === 0) {
            conteudoResultado.innerHTML = `<p class="text-center text-gray-500 py-4">O endereço está vazio no momento.</p>`;
            indicadorTotal.innerText = 0;
            indicadorKits.innerText = 0;
            
            stepConsulta.classList.add("hidden");
            stepResultado.classList.remove("hidden");
            return;
        }

        // REGRA WMS: > 1 linha = Kit. Apenas 1 linha = Unidade.
        const isKit = itens.length > 1;

        // A quantidade de kits é exatamente o número de linhas (se atender a regra de ser kit)
        if (isKit) {
            qtdKits = itens.length;
        }

        // Percorre o array retornado da API normalmente, sem agrupar
        itens.forEach(item => {
            // Garante que a quantidade do banco é tratada como número
            const quantidade = Number(item.qtd) || 0;
            
            // A quantidade total é a soma da coluna 'qtd' de todas as linhas
            qtdTotal += quantidade;
            
            // Configura as cores e textos baseados na regra geral do endereço
            const corBorda = isKit ? 'border-l-green-500' : 'border-l-blue-500';
            const corTagBg = isKit ? 'bg-green-100' : 'bg-blue-100';
            const corTagText = isKit ? 'text-green-800' : 'text-blue-800';
            const txtTag = isKit ? 'Kit' : 'Unidade';

            // Cria a div do card dinamicamente com as chaves corretas
            const div = document.createElement('div');
            div.className = `p-3 bg-white border border-gray-200 rounded-lg flex justify-between items-center shadow-sm border-l-4 ${corBorda}`;
            div.innerHTML = `
                <div>
                    <span class="text-[10px] font-bold ${corTagBg} ${corTagText} px-2 py-0.5 rounded uppercase mb-1 inline-block">${txtTag}</span>
                    <p class="text-sm font-bold text-gray-800">${item.nome}</p>
                    <p class="text-xs text-gray-500">Código: ${item.codItem}</p>
                </div>
                <div class="text-right">
                    <span class="text-lg font-black text-gray-800">${quantidade}</span>
                    <span class="text-xs text-gray-500 block">un</span>
                </div>
            `;
            
            conteudoResultado.appendChild(div);
        });

        // Atualiza os indicadores do topo
        indicadorTotal.innerText = qtdTotal;
        indicadorKits.innerText = qtdKits;

        // Esconde a div de consulta e mostra a de resultados
        stepConsulta.classList.add("hidden");
        stepResultado.classList.remove("hidden");
    }
});