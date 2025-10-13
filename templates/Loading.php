<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap CSS -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    .modal-content {
        text-align: center;
    }

    .loader-icon {
        font-size: 48px;
        color: #3498db; /* Cor azul */
        margin-bottom: 20px;
        animation: spin 2s linear infinite; /* Adicionando animação de rotação */
    }

    .loader-text {
        font-size: 24px;
        color: #555; /* Cor cinza */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Ajuste para modal fullscreen */
    #modal-dialog {
        margin: 0;
        min-width: 100%;
        height: 100vh;
        padding: 0;
    }

    #modal-content {
        height: 100%;
        border-radius: 0;
    }

    #modal-body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        flex-direction: column;
    }
</style>
</head>
<body>

<!-- Modal de Loading -->
<div class="modal" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="modal-dialog"role="document">
        <div class="modal-content" id="modal-content">
            <div class="modal-body" id="modal-body">
                <div class="loader-icon">
                    <i class="fas fa-box-open"></i> <!-- Ícone de uma caixa de armazenamento -->
                </div>
                <div class="loader-text">Carregando...</div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (opcional, necessário apenas se você quiser usar funcionalidades do Bootstrap, como modals animados) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Script para exibir a modal -->
<script>
    $(document).ready(function() {
        $('#loadingModal').modal('hide');
    });
</script>

</body>
</html>
