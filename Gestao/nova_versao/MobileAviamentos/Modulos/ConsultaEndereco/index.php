<?php
session_start();

// Proteção da rota: volta duas pastas para achar o login na raiz
if (!isset($_SESSION['matricula']) || empty($_SESSION['matricula'])) {
    header("Location: ../../login_mobile.php");
    exit;
}

include_once('requests.php');
include_once("../../../../../templates/LoadingGestao.php");
include_once('../../../../../templates/headerGestaoMobile.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
    /* Correção do conflito Bootstrap x Tailwind */
    .collapse.show { visibility: visible !important; }
    
    #reader { width: 100%; border-radius: 0.5rem; overflow: hidden; display: none; }
    main { background-color: #f3f4f6; }
    
    /* Estilização da barra de rolagem para a lista de itens */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<main class="w-full p-4 flex flex-col items-center justify-start min-h-[80vh]">

    <div id="main-card" class="w-full max-w-md bg-white p-6 rounded-xl shadow-sm border border-gray-200 transition-colors duration-300">
        
        <div id="reader" class="mb-4 border-2 border-dashed border-gray-300 hidden"></div>

        <div id="step-consulta">
            <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Consultar Endereço</h2>
                    <p class="text-xs text-gray-500 mt-1" id="info-operador" 
                    data-matricula="<?= htmlspecialchars($_SESSION['matricula']) ?>" 
                    data-usuario="<?= htmlspecialchars($_SESSION['nomeUsuario']) ?>">
                        Operador: <span class="font-bold text-blue-600"><?= htmlspecialchars($_SESSION['nomeUsuario']) ?></span>
                    </p>
                </div>
                <a href="../../logout_mobile.php" class="text-sm text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded bg-red-50 transition">Sair</a>
            </div>

            <div class="mb-6">
                <label for="endereco-consulta" class="block text-sm font-medium text-gray-700 mb-2">Informe ou Bipe o Endereço</label>
                <div class="flex space-x-2">
                    <input type="text" id="endereco-consulta" placeholder="Ex: A-10-05" autofocus class="flex-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg px-4 py-3 border uppercase">
                    
                    <button type="button" class="btn-camera inline-flex items-center p-3 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition" data-target="endereco-consulta">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="button" id="btn-consultar" class="w-full bg-blue-600 text-white font-bold text-lg py-3 px-4 rounded-lg shadow hover:bg-blue-700 transition">
                Consultar
            </button>
        </div>

        <div id="resultado-consulta" class="hidden mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-800">Resultado</h3>
                <span id="display-endereco-resultado" class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-bold rounded-full border border-blue-200 uppercase">--</span>
            </div>

            <div class="flex space-x-3 mb-5">
                <div class="flex-1 bg-blue-50 border border-blue-200 rounded-lg p-3 text-center shadow-sm">
                    <span class="block text-[10px] font-bold text-blue-700 uppercase tracking-wider mb-1">Qtd Total</span>
                    <span id="indicador-qtd-total" class="block text-2xl font-black text-gray-800 leading-none">0</span>
                </div>
                <div class="flex-1 bg-green-50 border border-green-200 rounded-lg p-3 text-center shadow-sm">
                    <span class="block text-[10px] font-bold text-green-700 uppercase tracking-wider mb-1">Qtd Kits</span>
                    <span id="indicador-qtd-kits" class="block text-2xl font-black text-gray-800 leading-none">0</span>
                </div>
            </div>

            <div class="mb-2">
                <h4 class="text-sm font-bold text-gray-700 mb-2">Itens no Endereço</h4>
                
                <div id="conteudo-resultado" class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                    
                    <div class="p-3 bg-white border border-gray-200 rounded-lg flex justify-between items-center shadow-sm border-l-4 border-l-green-500">
                        <div>
                            <span class="text-[10px] font-bold bg-green-100 text-green-800 px-2 py-0.5 rounded uppercase mb-1 inline-block">Kit</span>
                            <p class="text-sm font-bold text-gray-800">Zíper Invisível 15cm</p>
                            <p class="text-xs text-gray-500">Código: KIT-99812</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-gray-800">50</span>
                            <span class="text-xs text-gray-500 block">un</span>
                        </div>
                    </div>

                    <div class="p-3 bg-white border border-gray-200 rounded-lg flex justify-between items-center shadow-sm border-l-4 border-l-blue-500">
                        <div>
                            <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded uppercase mb-1 inline-block">Avulso</span>
                            <p class="text-sm font-bold text-gray-800">Botão de Pressão Prata</p>
                            <p class="text-xs text-gray-500">Código: BTN-0045</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-gray-800">120</span>
                            <span class="text-xs text-gray-500 block">un</span>
                        </div>
                    </div>

                </div>
            </div>

            <button type="button" id="btn-nova-consulta" class="w-full bg-gray-100 text-gray-700 font-bold text-md py-3 px-4 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-200 transition mt-6">
                Fazer Nova Consulta
            </button>
        </div>

    </div> 
</main>

<script src="app.js"></script>