<?php
include_once("Wms/src/VerificaTag/requests.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta Pedido por Tags</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        /* Seu CSS aqui */
        #form-container { padding: 20px; }
        .Corpo { display: flex; flex-direction: column; gap: 20px; }
        .input-container { display: flex; flex-direction: column; gap: 5px; max-width: 400px; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-wrapper i { position: absolute; left: 10px; color: #aaa; font-size: 18px; transition: color 0.3s ease; }
        .input-wrapper input {
            width: 100%; padding: 10px 15px 10px 35px;
            border: 2px solid #ddd; border-radius: 25px;
            outline: none; font-size: 16px; transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .input-wrapper input:focus { border-color: #007bff; box-shadow: 0 4px 10px rgba(0,123,255,0.25); }
    </style>
</head>

<body>
    <div class="h-100">
        <div class="sidebar-logo bg-primary text-white text-center p-2">
            <a>WMS MPL - Consulta Pedido por Tags</a>
        </div>

        <div class="container-fluid" id="form-container">
            <div class="Corpo auto-height" style="min-height: 75vh; align-items: center">
                <!-- Campo Tag -->
                <div class="input-container">
                    <label for="tag">Tag</label>
                    <div class="input-wrapper">
                        <i class="fa fa-search"></i>
                        <input type="text" id="tag" placeholder="Digite a tag..." />
                    </div>
                </div>

                <!-- Campo Pedido -->
                <div class="input-container">
                    <label for="pedido">Pedido</label>
                    <div class="input-wrapper">
                        <i class="fa fa-file"></i>
                        <input type="text" id="pedido" readonly />
                    </div>
                </div>

                <!-- Campo Cliente -->
                <div class="input-container">
                    <label for="cliente">Cliente</label>
                    <div class="input-wrapper">
                        <i class="fa fa-user"></i>
                        <input type="text" id="cliente" readonly />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de loading -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-3 mb-0">Consultando tag...</p>
            </div>
        </div>
    </div>

    <!-- 🧩 Bibliotecas JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 💻 Seu Script -->
    <script>
    $(document).ready(() => {

        const Consultar_Tags = async () => {
            $('#loadingModal').modal('show');

            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Tags',
                        codigoBarras: $('#tag').val().trim()
                    },
                });

                if (response && response.length > 0) {
                    $('#pedido').val(response[0]['codpedido']);
                    $('#cliente').val(response[0]['codcliente']);
                } else {
                    alert('Nenhum registro encontrado para este código de barras.');
                }

            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao consultar tags. Verifique o console para mais detalhes.');
                $('#loadingModal').modal('hide');

            } finally {
                $('#loadingModal').modal('hide');
            }
        };

        $('#tag').on('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                Consultar_Tags();
            }
        });

    });
    </script>
</body>
</html>
