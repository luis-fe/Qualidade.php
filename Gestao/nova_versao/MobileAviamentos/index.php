<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
    /* Esconde o container da câmera inicialmente */
    #reader { width: 100%; border-radius: 0.5rem; overflow: hidden; display: none; }
    /* Garante um fundo cinza claro para destacar o card branco */
    main { background-color: #f3f4f6; }
</style>

<main class="w-full p-4 flex flex-col items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        
        <div class="mb-4">
            <label for="endereco" class="block text-sm font-medium text-gray-700 mb-2">
                Digite ou escaneie o endereço
            </label>
            
            <div class="flex space-x-2">
                <input type="text" id="endereco" name="endereco" placeholder="Ex: A-10-05" 
                    class="flex-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg px-4 py-3 border">
                
                <button type="button" id="btn-camera" 
                    class="inline-flex items-center p-3 border border-transparent rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="reader" class="mb-4 border-2 border-dashed border-gray-300"></div>

        <button type="button" id="btn-confirmar" class="w-full bg-green-600 text-white font-bold text-lg py-3 px-4 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition mt-2">
            Confirmar Endereço
        </button>
        
    </div>
</main>

<script src="app.js"></script>

<?php
// Caso você tenha um rodapé padrão no sistema, inclua-o aqui embaixo.
// Exemplo: include_once('../../../templates/footerGestao.php');
?>