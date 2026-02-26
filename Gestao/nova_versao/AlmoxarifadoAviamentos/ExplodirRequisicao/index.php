<?php
include_once('requests.php');
include_once("../../../../templates/LoadingGestao.php");
include_once('../../../../templates/headerGestao.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    /* Estilos de tela mantidos */
    .menus { display: flex; justify-content: start; padding: 0px 10px; margin-top: 15px; }
    .dataTables_wrapper { display: block; }
    
    /* CONFIGURAÇÃO PARA OS CARDS FICAREM UM AO LADO DO OUTRO NA TELA */
    #container-cards {
        display: flex;
        flex-wrap: wrap; /* Permite quebrar linha quando não couber mais na largura */
        gap: 15px;      /* Espaço entre as etiquetas na tela */
        padding: 20px;
        justify-content: center; /* Centraliza a grade de etiquetas na tela */
        background-color: #f4f4f4;
    }

    /* ESTILO DO CARD NA TELA (Simulando a etiqueta real) */
    .card-etiqueta {
        width: 10.9cm !important;
        height: 2.1cm !important;
        min-width: 10.9cm;
        min-height: 2.1cm;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: 1px solid #ddd; /* Borda leve apenas para visualização na tela */
        display: block;
        overflow: hidden;
    }

    /* Ajuste do corpo interno (usado no JS) */
    .card-body-custom {
        display: flex;
        flex-direction: row;
        width: 100%;
        height: 100%;
        padding: 0.1cm 0.3cm 0.1cm 1cm; /* Mantendo seu padrão de 1cm na esquerda */
        box-sizing: border-box;
        align-items: center;
    }

    /* Estilos de tabelas e botões (mantidos do seu original) */
    .button-filtros { border: 1px solid black !important; background-color: white !important; margin-bottom: 10px !important; width: 70px; padding: 2px 0px; }
    .titulo-tela { font-size: 1.2rem; }

    @media print {
        /* Esconde elementos de interface */
        .no-print, header, footer, .btn, .titulo-tela, #loadingModal, .span-icone { display: none !important; }
        
        body { margin: 0 !important; padding: 0 !important; background-color: white !important; }
        body * { visibility: hidden; }
        #container-cards, #container-cards * { visibility: visible; }

        @page {
            size: 9.9cm 2.1cm;
            margin: 0 !important;
        }

        #container-cards {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 10.9cm !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important; /* Na impressão volta a ser bloco para sair uma por página */
        }

        .card-etiqueta {
            width: 10.9cm !important;
            height: 2.1cm !important;
            border: none !important; 
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            page-break-after: always !important;
            display: block !important;
            box-shadow: none !important;
        }

        .card-body-custom {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            height: 100% !important;
            padding: 0.1cm 0.03cm 0.1cm 1cm !important; 
            box-sizing: border-box !important;
        }
    }
</style>

<?php
$numeroRequisicao = isset($_GET['requisicao']) ? $_GET['requisicao'] : 'Não Informada';
?>

<div class="no-print d-flex justify-content-between align-items-center mt-3 mb-3" style="padding: 0 20px;">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.history.back();">
        Voltar
    </button>
    <div class="titulo-tela mb-0">
        <span><i class="bi bi-bullseye"></i></span> REQUISICAO - <strong><?php echo htmlspecialchars($numeroRequisicao); ?></strong>
        <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick="window.print();">
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
    </div>
</div>

<input type="hidden" id="numRequisicao" value="<?php echo htmlspecialchars($numeroRequisicao); ?>">

<div id="container-cards"></div>

<?php include_once('../../../../templates/footerGestao.php'); ?>
<script src="script2.js"></script>