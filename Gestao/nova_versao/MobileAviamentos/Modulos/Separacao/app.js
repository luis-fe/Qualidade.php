document.addEventListener('DOMContentLoaded', () => {
    const listaOpsContainer = document.getElementById('lista-ops');
    const inputPesquisa = document.getElementById('input-pesquisa-op');
    let opsData = []; // Armazena os dados originais para o filtro funcionar

    // Função para buscar os dados da API
    async function carregarOPs() {
        try {
            // === OPÇÃO 1: CHAMADA REAL PARA A SUA API ===
            // Ajuste a URL abaixo para o caminho correto do seu endpoint
            const response = await fetch('requests.php?acao=Consultar_OP_requisicao');
            if (!response.ok) throw new Error('Erro na requisição da rede');
            const data = await response.json();

            // === OPÇÃO 2: MOCK (DADOS DE TESTE) ===
            // Se for testar o visual antes de ligar a API, comente as 4 linhas acima 
            // e descomente o array abaixo:
            /*
            const data = [
                {
                    "FaseAtual": "ALMOX. DE AVIAMENTOS",
                    "QtdPecas_x": 141.0,
                    "codProduto": "010474212-0",
                    "descricao": "CAMISA MC SLIM LISTRADA LINN",
                    "nomeUsuario": "-",
                    "numeroOP": "174651-001"
                },
                {
                    "FaseAtual": "ALMOX. DE TECIDOS",
                    "QtdPecas_x": 85.0,
                    "codProduto": "020585323-1",
                    "descricao": "CALÇA JEANS MASCULINA RETA",
                    "nomeUsuario": "Carlos Mendes",
                    "numeroOP": "174652-002"
                }
            ];
            */

            // O seu JSON pode retornar um objeto único ou um array. 
            // Garantimos que seja um array para o forEach funcionar.
            opsData = Array.isArray(data) ? data : [data]; 
            
            renderizarCards(opsData);

        } catch (error) {
            console.error("Erro ao carregar OPs:", error);
            listaOpsContainer.innerHTML = `<p class="text-center text-red-500 text-sm mt-4 font-bold">Erro ao carregar as OPs.</p>`;
        }
    }

    // Função para montar o HTML dos cards na tela
    function renderizarCards(dados) {
        listaOpsContainer.innerHTML = ''; // Limpa o "Carregando..." ou os cards antigos

        if (dados.length === 0) {
            listaOpsContainer.innerHTML = `<p class="text-center text-gray-500 text-sm mt-4">Nenhuma OP encontrada.</p>`;
            return;
        }

        dados.forEach(op => {
            // Formata a quantidade (remove casas decimais)
            const qtdPecas = parseInt(op.QtdPecas_x) || 0;
            
            // Lógica do separador (se vier '-' ou vazio, exibe a tag de Aguardando)
            const separadorHtml = (op.nomeUsuario === '-' || !op.nomeUsuario)
                ? `<span class="text-gray-400 italic font-normal bg-gray-100 px-1 py-px rounded">Aguardando...</span>`
                : `<span class="text-gray-700 font-medium">${op.nomeUsuario}</span>`;

            // HTML do Card (Design super compacto)
            const cardHTML = `
                <div class="bg-white p-2.5 rounded-lg shadow-sm border border-gray-100 transition hover:border-blue-100 hover:shadow-md cursor-pointer relative overflow-hidden group op-card">
                    <div class="absolute inset-y-0 left-0 w-1 bg-blue-500 transform -translate-x-full group-hover:translate-x-0 transition-transform duration-200"></div>

                    <div class="flex justify-between items-start mb-1.5 pb-1.5 border-b border-gray-50">
                        <div>
                            <span class="text-[10px] text-gray-500 font-medium leading-none block mb-0.5">OP</span>
                            <p class="text-sm font-extrabold text-gray-950 tracking-tight leading-none">${op.numeroOP}</p>
                        </div>
                        <div class="text-right bg-gray-50 px-2 py-0.5 rounded border border-gray-100 flex flex-col justify-center">
                            <span class="text-[9px] text-gray-500 block leading-none font-medium mb-0.5">PEÇAS</span>
                            <span class="text-sm font-bold text-gray-950 leading-none">${qtdPecas} pçs</span>
                        </div>
                    </div>
                    
                    <div class="space-y-1 text-[11px]">
                        <div class="flex items-start gap-1 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400 mt-[1px] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M17 7h.01M17 11h.01M17 15h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" /></svg>
                            <p class="leading-tight"><span class="text-gray-400 font-medium">Ref.:</span> <span class="font-medium text-gray-800">${op.codProduto}</span> <span class="text-gray-500">- ${op.descricao}</span></p>
                        </div>
                        <div class="flex items-center gap-1 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <p class="leading-tight"><span class="text-gray-400 font-medium">Cat.:</span> <span class="text-gray-700 font-medium">${op.FaseAtual}</span></p>
                        </div>
                        <div class="flex items-center gap-1 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <p class="leading-tight"><span class="text-gray-400 font-medium">Sep.:</span> ${separadorHtml}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Insere o card no final da lista
            listaOpsContainer.insertAdjacentHTML('beforeend', cardHTML);
        });
    }

    // Filtro da barra de pesquisa
    inputPesquisa.addEventListener('input', (e) => {
        const termo = e.target.value.toLowerCase().trim();
        
        // Retorna todas as OPs se o campo estiver vazio, senão filtra
        const opsFiltradas = opsData.filter(op => {
            // Previne erros caso algum campo venha nulo/undefined da API
            const opNum = op.numeroOP ? op.numeroOP.toLowerCase() : '';
            const codProd = op.codProduto ? op.codProduto.toLowerCase() : '';
            const desc = op.descricao ? op.descricao.toLowerCase() : '';

            return opNum.includes(termo) || codProd.includes(termo) || desc.includes(termo);
        });

        renderizarCards(opsFiltradas);
    });

    // Inicia a aplicação chamando a API
    carregarOPs();
});