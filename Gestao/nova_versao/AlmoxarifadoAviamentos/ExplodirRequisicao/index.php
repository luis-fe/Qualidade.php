<?php
include_once('requests.php');
include_once("../../../../templates/LoadingGestao.php");
include_once('../../../../templates/headerGestao.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    /* Seus estilos de tela (Mantidos) */
    .menus { display: flex; justify-content: start; padding: 0px 10px; margin-top: 15px; }
    .dataTables_wrapper { display: block; }
    /* ... (restante dos seus estilos de tela) ... */

    /* NOVO BLOCO DE IMPRESSÃO CORRIGIDO */
    @media print {
        /* 1. Esconde absolutamente tudo, incluindo o header do PHP */
        body * {
            visibility: hidden !important;
        }

        /* 2. Mostra apenas o que interessa */
        #container-cards, #container-cards * {
            visibility: visible !important;
        }

        /* 3. Configuração da Página */
        @page {
            size: 10.1cm 2.6cm ;
            margin: 0 !important;
        }

        /* 4. O Container: Removido o fixed para permitir várias páginas */
        #container-cards {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 10.1cm !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* 5. O Card: Forçando a largura máxima de 10.1cm */
        .card-etiqueta {
            display: flex !important;
            flex-direction: row !important;
            width: 10.1cm !important;
            min-width: 10.1cm !important;
            max-width: 10.1cm !important;
            height: 2.6cm !important;
            border: 1px solid #000 !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            page-break-after: always !important;
            background-color: white !important;
        }

        /* 6. Ajuste interno para a margem de 1cm na esquerda */
        /* Note o seletor específico para não quebrar outros componentes */
        .card-etiqueta .card-body {
            visibility: visible !important;
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            height: 100% !important;
            /* Margem esquerda de 1cm conforme solicitado */
            padding: 0.1cm 0.3cm 0.1cm 1cm !important; 
            box-sizing: border-box !important;
            margin: 0 !important;
        }

        /* Força imagens (QR Code) a aparecerem */
        img {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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