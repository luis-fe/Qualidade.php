<?php
include_once("/Wms/src/VerificaTag/requests.php");
include_once("/templates/heads.php");
include("/templates/Loading.php");
?>
<style>
    /* Ajustes gerais */
    #form-container {
        padding: 20px;
    }

    .Corpo {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Estilo do container de cada input */
    .input-container {
        display: flex;
        flex-direction: column;
        gap: 5px;
        max-width: 400px;
    }

    .input-container label {
        font-size: 14px;
        font-weight: 600;
        color: #555;
    }

    /* Wrapper para o input e o ícone */
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper i {
        position: absolute;
        left: 10px;
        color: #aaa;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .input-wrapper input {
        width: 100%;
        padding: 10px 15px 10px 35px;
        /* Espaço para o ícone */
        border: 2px solid #ddd;
        border-radius: 25px;
        outline: none;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .input-wrapper input:focus {
        border-color: #007bff;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.25);
    }

    .input-wrapper input::placeholder {
        color: #aaa;
        font-style: italic;
    }

    .input-wrapper input:focus+i {
        color: #007bff;
    }
</style>
<link rel="stylesheet" href="style.css">


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
                <i class="fa fa-search"></i>
                <input type="text" id="pedido" readonly />
            </div>
        </div>

        <!-- Campo Cliente -->
        <div class="input-container">
            <label for="cliente">Cliente</label>
            <div class="input-wrapper">
                <i class="fa fa-search"></i>
                <input type="text" id="cliente" readonly />
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(async () => {
        $('#NomeRotina').text("Verifica Tag");

    });
    const Consultar_Tags = async () => {
        $('#loadingModal').modal('show');
        try {
            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Tags',
                    codigoBarras: $('#tag').val()
                },
            });
            $('#pedido').val(response[0]['codpedido']);
            $('#cliente').val(response[0]['codcliente'])
        } catch (error) {
            console.error('Erro:', error);
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
</script>