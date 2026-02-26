<?php
include_once('requests.php');
include_once("../../../../templates/LoadingGestao.php");
include_once('../../../../templates/headerGestao.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    /* --- SEUS ESTILOS DE TELA (MANTIDOS IGUAIS) --- */
    .menus { display: flex; justify-content: start; padding: 0px 10px; margin-top: 15px; }
    .dataTables_wrapper { display: block; }
    .custom-pagination-container { justify-content: space-between; align-items: center; background-color: lightgray; padding: 5px; border-radius: 8px; }
    /* ... (Mantenha o restante dos seus estilos de botões e tabelas aqui) ... */

    /* --- AJUSTE DO CONTAINER NA TELA (PARA NÃO FICAR DESCONFIGURADO) --- */
    #container-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 20px;
        justify-content: center;
    }

    /* --- BLOCO DE IMPRESSÃO (O SEGREDO ESTÁ AQUI) --- */
    @media print {
        /* Esconde tudo do sistema */
        body * { visibility: hidden !important; }
        #container-cards, #container-cards * { visibility: visible !important; }

        @page {
            size: 10.1cm 2.6cm;
            margin: 0 !important;
        }

        #container-cards {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 10.1cm !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card-etiqueta {
            /* 144% de escala equivale a aumentar as medidas reais. 
               Ajustamos aqui para que o navegador preencha o papel */
            width: 10.1cm !important;
            height: 2.6cm !important;
            
            display: flex !important;
            flex-direction: row !important;
            border: 1px solid #000 !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            page-break-after: always !important;
            
            /* Remove sombras e efeitos que atrasam a impressão */
            box-shadow: none !important;
            transform: scale(1); 
            transform-origin: top left;
        }

        /* Força o preenchimento interno */
        .card-etiqueta .card-body {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            height: 100% !important;
            padding: 0 0.2cm 0 1cm !important; /* Margem de 1cm na esquerda */
            margin: 0 !important;
            box-sizing: border-box !important;
            align-items: center !important;
        }

        /* Esconde o cabeçalho que voltou a aparecer */
        .no-print, header, .titulo-tela, .navbar {
            display: none !important;
            height: 0 !important;
            margin: 0 !important;
        }
    }
</style>

<?php
$numeroRequisicao = isset($_GET['requisicao']) ? $_GET['requisicao'] : 'Não Informada';
$separador = isset($_GET['separador']) ? $_GET['separador'] : 'Não Informada';
?>

<div class="no-print d-flex justify-content-between align-items-center mt-3 mb-3">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.history.back();">
        Voltar
    </button>
    <div class="titulo-tela mb-0">
        <span class="span-icone"><i class="bi bi-bullseye"></i></span> REQUISICAO - <strong><?php echo htmlspecialchars($numeroRequisicao); ?></strong>
        <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick="window.print();">
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
    </div>
</div>

<input type="hidden" id="numRequisicao" value="<?php echo htmlspecialchars($numeroRequisicao); ?>">

<div id="container-cards">
    </div>

<?php include_once('../../../../templates/footerGestao.php'); ?>
<script src="script2.js"></script>