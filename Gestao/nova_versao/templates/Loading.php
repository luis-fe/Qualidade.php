<!DOCTYPE html>
<html lang="pt-BR">

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

        .loader {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .loader .Icone {
            width: 30px;
            height: 30px;
            margin: 10px;
            background-color: var(--CorMenu);
            /* Azul escuro */
            border-radius: 50%;
            animation: bounce 0.6s infinite alternate;
        }

        .loading-bar {
            width: 10px;
            height: 40px;
            margin: 0 2px;
            background-color: rgb(0, 8, 97);
            border-radius: 4px;
            display: inline-block;
            animation: pulse 1.2s infinite ease-in-out both;
        }

        .loading-bar:nth-child(2) {
            animation-delay: 0.2s;
        }

        .loading-bar:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scaleY(1);
                opacity: 1;
            }

            50% {
                transform: scaleY(1.5);
                opacity: 0.5;
            }
        }

        /* Ajuste para modal fullscreen */
        #loadingModal .modal-dialog {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            max-height: 100vh;
            margin: 0;
            width: 100%;
            min-width: 100%;
            z-index: 9999;
            overflow: hidden;
        }

        #modal-content {
            border-radius: 12px;
        }

        #modal-body {
            padding: 40px;
        }

        .loader-text {
            color: black;
            font-size: 1.3rem;
            margin-top: 15px;
            letter-spacing: 2px;
            font-weight: 400;
        }
    </style>
</head>

<body>

    <!-- Modal de Loading -->
    <div class="modal" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" style="z-index: 9999; background-color: white; max-height: 100vh">
        <div class="modal-dialog" role="document" style="max-height: 100vh;">
            <div class="modal-content" style="border: none; max-height: 100vh">
                <div class="modal-body">
                    <div class="loader">
                        <div class="d-flex justify-content-center">
                            <div class="loading-bar"></div>
                            <div class="loading-bar"></div>
                            <div class="loading-bar"></div>
                        </div>
                        <div class="loader-text">Carregando...</div>
                    </div>
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