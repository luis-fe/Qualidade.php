<?php
include_once("../../../templates/header.php");
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="style2.css">
<style>
    .search-container {
        max-width: 300px;
        margin: 5px;
    }

    .modal-header {
        background-color: #002955;
        color: white;
    }

    #dropdownMenu {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ccc;
        background-color: #fff;
        padding: 10px;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }


    .ui-accordion-header .ui-icon {
        float: left;
        /* Garante que o ícone fique à esquerda */
    }

    .op-button {
        padding: 10px;
        border-radius: 8px;
        border: 1px solid black;
        color: black;
        font-weight: 500;
    }

    #sidebar {
        display: none;
    }

    #sidebar.active {
        width: 0px;
    }

    .menu-btn {
        display: none;
    }

    .main {
        width: 100%;
    }

    .op-header {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .op-product,
    .op-name {
        text-align: left;
    }

    /* Estilos para telas menores */
    @media (max-width: 768px) {
        .op-header {
            flex-direction: column;
            /* Muda para coluna em telas menores */
            align-items: flex-start;
            /* Alinha à esquerda */
        }

        .op-header span {
            width: 100%;
            /* Cada elemento ocupa a linha inteira */
            text-align: left;
            /* Alinha texto à esquerda */
        }

        .op-product {
            text-align: left;
            /* Alinha produto à esquerda */
        }

        .op-name {
            text-align: left;
            /* Alinha nome à esquerda */
        }
    }

    .op-search {
        padding: 10px;
        /* Espaçamento interno */
        border: 2px solid #ccc;
        /* Borda padrão */
        border-radius: 5px;
        /* Cantos arredondados */
        transition: border-color 0.3s, box-shadow 0.3s;
        /* Transições suaves */
        font-size: 16px;
        /* Tamanho da fonte */
        outline: none;
        /* Remove o contorno padrão */
    }

    .op-search::placeholder {
        color: #aaa;
        /* Cor do texto do placeholder */
    }

    .op-search:focus {
        border-color: #007BFF;
        /* Cor da borda ao focar */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        /* Sombra ao focar */
    }
</style>
<div class="titulo" style="padding: 10px; text-align: left; border-bottom: 1px solid black; color: black; font-size: 15px; font-weight: 600;">
    <i class="icon ph-bold ph-target"></i> Status das Op's
</div>
<div class="corpo" style="text-align: left; justify-content: left">
    <div class="dropdown" style="margin-left: 10px; margin-top: 10px; margin-bottom: 15px">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Categorias
        </button>
        <div class="dropdown-menu" id="dropdownMenu" aria-labelledby="dropdownMenuButton">
            <!-- As opções serão adicionadas aqui via JavaScript -->
        </div>
    </div>
    <div class="legenda" style="display: flex; flex-wrap: wrap; align-items: center; margin-left: 10px; color: black; margin-bottom: 15px">
        <div style="display: flex; align-items: center; margin-right: 15px;">
            <div style="width: 20px; height: 20px; background-color: rgb(250,128,114); margin-right: 5px;"></div>
            <span>FAT ATRASADO e P. FAT.</span>
        </div>
        <div style="display: flex; align-items: center; margin-right: 15px;">
            <div style="width: 20px; height: 20px; background-color: rgb(255,255,0); margin-right: 5px;"></div>
            <span>MOSTRUÁRIO</span>
        </div>
        <div style="display: flex; align-items: center; margin-right: 15px;">
            <div style="width: 20px; height: 20px; background-color: lightBlue; margin-right: 5px;"></div>
            <span>Outros</span>
        </div>
    </div>

    <div class="accordion"></div>
</div>

<?php
include_once("../../../templates/footer.php");
?>

<script>
    $(document).ready(() => {
        Consulta_Categorias();

        // Adiciona o evento de mudança para os checkboxes das categorias
        $('#dropdownMenu').on('change', 'input[type="checkbox"]', function() {
            // Desmarque todos os checkboxes, exceto o que foi clicado
            $('#dropdownMenu input[type="checkbox"]').not(this).prop('checked', false);
            // Chama a função com a categoria selecionada
            Consultar_Faccionistas();
        });
    });

    const Consulta_Categorias = async () => {
        $('#loadingModal').modal('show');
        try {
            const data = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Categorias',
                }
            });
            CriarCategorias(data);
        } catch (error) {
            console.error('Erro ao consultar categorias:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    };

    async function Consultar_Faccionistas() {
        $('#loadingModal').modal('show');

        // Limpa a accordion antes de adicionar novos resultados
        $('.accordion').empty();

        try {
            const selectedCategory = $('#dropdownMenu input[type="checkbox"]:checked').val();
            const dados = {
                categoria: selectedCategory
            };
            const requestData = {
                acao: "Consultar_Faccionistas",
                dados: dados
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            const dadosApi = response[0]['1- Resumo:'];

            dadosApi.forEach(faccionista => {
                const title = `
            <h3>
                Código Faccionista: ${faccionista.codfaccionista}
                <div style="font-weight: 500">${faccionista.apelidofaccionista}</div>
                Categoria: ${faccionista.categoria}
                Carga: ${faccionista.carga}
            </h3>
            `;

                const content = $('<div></div>');

                // Campo de pesquisa
                const searchInput = `
            <input type="text" class="op-search" placeholder="Pesquisar OPs..." style="margin-bottom: 10px; width: 100%;">
            `;
                content.append(searchInput);

                // Mapeamento de cores por prioridade
                const prioridadeCores = {
                    "FAT ATRASADO": "rgb(250,128,114)",
                    "P\\ FATURAMENTO": "rgb(250,128,114)",
                    "MOSTRUÁRIO": "rgb(255,255,0)",
                    // Adicione outras prioridades e cores conforme necessário
                };

                // Adiciona botões de OPs
                const buttonContainer = $('<div class="op-button-container"></div>');

                const opDetails = response[0]['2- Detalhamento:'].filter(op => op.codfaccionista === faccionista.codfaccionista);

                opDetails.forEach(op => {
                    const corBotao = prioridadeCores[op.prioridade] || "lightBlue"; // Cor padrão se não houver prioridade definida

                    const button = `
    <button class="op-button" data-codop="${op.codOP}" style="margin: 5px; display: flex; flex-direction: column; width: 100%; background-color: ${corBotao}; color: black;">
        <div class="op-header mb-1">
            <span class="op-number">OP: ${op.numeroOP}</span>
            <span class="op-quantity">Qtd: ${op.carga}</span>
        </div>
        <div class="op-product mb-1">
            <span>Produto: ${op.codProduto}</span>
        </div>
        <span class="op-name mb-1">${op.nome}</span>
    </button>`;
                    buttonContainer.append(button);
                });

                content.append(buttonContainer);
                $('.accordion').append(title).append(content);
            });

            // Reinicializa o accordion
            if ($('.accordion').data("ui-accordion")) {
                $('.accordion').accordion("destroy"); // Remove a instância anterior
            }

            $('.accordion').accordion({
                heightStyle: "content",
                active: false,
                collapsible: true
            });

            // Adiciona eventos
            $('.op-button').on('click', function() {
                const codop = $(this).data('codop');
                alert(`Você clicou na OP: ${codop}`);
            });

            $('.op-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                const buttons = $(this).siblings('.op-button-container').find('.op-button');

                buttons.each(function() {
                    const buttonText = $(this).text().toLowerCase();
                    $(this).toggle(buttonText.includes(searchTerm));
                });
            });

        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
            alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }


    function CriarCategorias(options) {
        $('#dropdownMenu').empty();

        options.forEach(option => {
            const checkbox = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${option.categoria}" id="${option.categoria}">
                    <label class="form-check-label" for="${option.categoria}">${option.categoria}</label>
                </div>
            `;
            $('#dropdownMenu').append(checkbox);
        });
    }
</script>