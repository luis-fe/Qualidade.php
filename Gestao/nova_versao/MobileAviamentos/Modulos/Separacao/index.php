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

<style>
    main { background-color: #f9fafb; }
    .select2-container .select2-selection--single { height: 42px !important; border-radius: 8px !important; border-color: #d1d5db !important; }
</style>

<main class="w-full p-3 flex flex-col items-center justify-start min-h-[85vh]">

    <div class="w-full max-w-md mx-auto">
        
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
            <div>
                <h2 class="text-xl font-extrabold text-gray-950 tracking-tight">OPs a Separar</h2>
                <p class="text-[11px] text-gray-500 mt-0.5" id="info-operador" 
                   data-matricula="<?= htmlspecialchars($_SESSION['matricula']) ?>" 
                   data-usuario="<?= htmlspecialchars($_SESSION['nomeUsuario']) ?>">
                    Op: <span class="font-semibold text-blue-700"><?= htmlspecialchars($_SESSION['nomeUsuario']) ?></span>
                </p>
            </div>
            <a href="../../logout_mobile.php" class="text-xs text-red-600 hover:text-red-700 font-bold px-3 py-1.5 rounded-full bg-red-50 hover:bg-red-100 transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor font-bold"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Sair
            </a>
        </div>

        <div class="mb-3 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input type="text" id="input-pesquisa-op" placeholder="Digite a OP para buscar..." class="block w-full pl-10 pr-4 py-2 rounded-full border border-gray-200 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm bg-white transition placeholder:text-gray-400">
        </div>

        <div id="lista-ops" class="space-y-2 pb-6">
            <p class="text-center text-gray-500 text-sm mt-4 animate-pulse">Carregando OPs...</p>
        </div>
        
    </div> 
</main>

<script src="app.js"></script>