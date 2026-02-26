<?php
include_once('requests.php');
include_once("../../../../templates/LoadingGestao.php");
include_once('../../../../templates/headerGestao.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    /* Estilos de tela mantidos conforme seu padrão */
    .menus { display: flex; justify-content: start; padding: 0px 10px; margin-top: 15px; }
    #container-cards { display: flex; flex-wrap: wrap; gap: 10px; padding: 20px; justify-content: start; }

    @media print {
        /* Esconde elementos de interface */
        .no-print, header, footer, .btn, .titulo-tela, #loadingModal { display: none !important; }
        
        body * { visibility: hidden; }
        #container-cards, #container-cards * { visibility: visible; }

        @page {
            size: 10.1cm 2.6cm; /* Retirado o landscape */
            margin: 0 !important;
        }

        #container-cards {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 10.1cm !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        .card-etiqueta {
            width: 10.1cm !important;
            height: 2.6cm !important;
            border: none !important; /* Retirada a borda */
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            page-break-after: always !important;
            display: block !important;
        }

        /* Ajuste do card-body para manter a margem de 1cm à esquerda */
        .card-body-custom {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            height: 100% !important;
            padding: 0.1cm 0.3cm 0.1cm 1cm !important; 
            box-sizing: border-box !important;
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