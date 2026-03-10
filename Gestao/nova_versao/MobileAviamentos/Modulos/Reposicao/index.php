<?php
session_start();

// Proteção da rota: volta duas pastas para achar o login na raiz
if (!isset($_SESSION['matricula']) || empty($_SESSION['matricula'])) {
    header("Location: ../../login_mobile.php");
    exit;
}

include_once('requests.php');
include_once("../../../../../templates/LoadingGestao.php");
include_once('../../../../../templates/headerGestao.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
    #reader { width: 100%; border-radius: 0.5rem; overflow: hidden; display: none; }
    main { background-color: #f3f4f6; }
</style>

<main class="w-full p-4 flex flex-col items-center justify-start min-h-[80vh]">

    <div id="main-card" class="w-full max-w-md bg-white p-6 rounded-xl shadow-sm border border-gray-200 transition-colors duration-300">
        
        <div id="reader" class="mb-4 border-2 border-dashed border-gray-300 hidden"></div>

        <div id="step-1">
            <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Reposição</h2>
                        <p class="text-xs text-gray-500 mt-1" id="info-operador" 
                        data-matricula="<?= htmlspecialchars($_SESSION['matricula']) ?>" 
                        data-usuario="<?= htmlspecialchars($_SESSION['nomeUsuario']) ?>">
                            Operador: <span class="font-bold text-blue-600"><?= htmlspecialchars($_SESSION['nomeUsuario']) ?></span>
                        </p>
                </div>
                <a href="../../logout_mobile.php" class="text-sm text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded bg-red-50">Sair</a>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Reposição</label>
                <div class="flex bg-gray-100 p-1 rounded-lg">
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="tipo_reposicao" value="kit" class="peer sr-only" checked>
                        <div class="py-2 px-4 rounded-md text-sm font-semibold text-gray-600 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm transition-all duration-200">Por Kit</div>
                    </label>
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="tipo_reposicao" value="unidade" class="peer sr-only">
                        <div class="py-2 px-4 rounded-md text-sm font-semibold text-gray-600 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm transition-all duration-200">Por Unidade</div>
                    </label>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="endereco" class="block text-sm font-medium text-gray-700 mb-2">Endereço de Destino</label>
                <div class="flex space-x-2">
                    <input type="text" id="endereco" placeholder="Ex: A-10-05" autofocus class="flex-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg px-4 py-3 border">
                    <button type="button" class="btn-camera inline-flex items-center p-3 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition" data-target="endereco">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </button>
                </div>
            </div>

            <button type="button" id="btn-avancar" class="w-full bg-green-600 text-white font-bold text-lg py-3 px-4 rounded-lg shadow hover:bg-green-700 transition mt-4">Avançar</button>
        </div>

        <div id="step-2-kit" class="hidden">
            <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                <button type="button" id="btn-voltar-kit" class="text-gray-500 hover:text-blue-600 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Bipar Kits</h2>
                    <p class="text-sm text-gray-500">Endereço: <span id="display-endereco" class="font-bold text-blue-600"></span></p>
                </div>
            </div>

            <div class="flex space-x-3 mb-6">
                <div class="flex-1 bg-blue-50 border border-blue-200 rounded-lg p-3 text-center shadow-sm">
                    <span class="block text-xs font-bold text-blue-700 uppercase tracking-wide">Total de Kits</span>
                    <span id="total-kits-sessao" class="block text-2xl font-black text-gray-800">0</span>
                </div>
                <div class="flex-1 bg-orange-50 border border-orange-200 rounded-lg p-3 text-center shadow-sm">
                    <span class="block text-xs font-bold text-orange-700 uppercase tracking-wide">Total Unidades</span>
                    <span id="total-unidades-sessao" class="block text-2xl font-black text-gray-800">0</span>
                </div>
            </div>

            <div class="mb-4">
                <label for="codigo-kit" class="block text-sm font-medium text-gray-700 mb-2">QR Code do Kit</label>
                <div class="flex space-x-2">
                    <input type="text" id="codigo-kit" placeholder="Bipe o Kit aqui" class="flex-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg px-4 py-3 border">
                    <button type="button" class="btn-camera inline-flex items-center p-3 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition" data-target="codigo-kit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /></svg>
                    </button>
                    <button type="button" id="btn-add-kit" class="inline-flex items-center p-3 rounded-lg text-white bg-green-500 hover:bg-green-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Kits Lidos (<span id="contador-kits">0</span>)</h3>
                <ul id="lista-kits" class="max-h-48 overflow-y-auto space-y-2">
                </ul>
            </div>

            <button type="button" id="btn-finalizar-kit" class="w-full bg-blue-600 text-white font-bold text-lg py-3 px-4 rounded-lg shadow hover:bg-blue-700 transition mt-2">Concluir Reposição</button>
        </div>

        <div id="step-2-unidade" class="hidden">
            <div class="flex items-center mb-6 border-b border-orange-200 pb-4">
                <button type="button" id="btn-voltar-unidade" class="text-gray-500 hover:text-orange-700 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Repor Unidade</h2>
                    <p class="text-sm text-gray-600">Endereço: <span id="display-endereco-unidade" class="font-bold text-orange-700"></span></p>
                </div>
            </div>

            <div class="mb-4">
                <label for="codigo-unidade" class="block text-sm font-medium text-gray-700 mb-2">QR Code do Produto</label>
                <div class="flex space-x-2">
                    <input type="text" id="codigo-unidade" placeholder="Bipe o produto" class="flex-1 block w-full rounded-lg border-orange-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-lg px-4 py-3 border bg-white">
                    <button type="button" class="btn-camera inline-flex items-center p-3 rounded-lg text-white bg-orange-600 hover:bg-orange-700 transition" data-target="codigo-unidade">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /></svg>
                    </button>
                </div>
            </div>

            <div class="mb-6">
                <label for="qtde-unidade" class="block text-sm font-medium text-gray-700 mb-2">Informe a Quantidade</label>
                <input type="number" id="qtde-unidade" placeholder="Ex: 10" min="1" class="block w-full rounded-lg border-orange-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-2xl px-4 py-3 border font-bold text-center bg-white">
            </div>

            <button type="button" id="btn-finalizar-unidade" class="w-full bg-orange-600 text-white font-bold text-lg py-3 px-4 rounded-lg shadow hover:bg-orange-700 transition mt-2">Salvar Reposição</button>
        </div>

    </div> 
</main>

<script src="app.js"></script>