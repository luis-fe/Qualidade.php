<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");

// Assumindo que você pegue a empresa e o token da sessão ou de alguma outra rotina de autenticação
$empresa = isset($_SESSION['empresa']) ? $_SESSION['empresa'] : '1';
$token = isset($_SESSION['token']) ? $_SESSION['token'] : '';

// Executa a requisição para a API
$clientesDesagrupados = consultarClientesDesagrupados($empresa, $token);
?>

<link rel="stylesheet" href="style.css">
<style>
    .fixed-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: #008FFB; /* Cor azul puxada do seu layout padrão */
        color: white;
    }
    
    /* Ajuste para garantir que a tabela não quebre em telas menores */
    .table-container {
        max-height: 60vh; 
        overflow-y: auto;
    }

    /* Ajuste para deixar as linhas bem finas e visuais */
    .tabela-compacta td, .tabela-compacta th {
        padding: 0.25rem 0.5rem !important; /* Reduz drasticamente o espaço em branco (padding) */
        font-size: 13px; /* Deixa a fonte um pouco menor */
        vertical-align: middle; /* Garante que o texto fique bem centralizado na linha fina */
        line-height: 1.2; /* Aproxima as linhas de texto, se houver quebra */
    }
</style>

<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height p-4">
        
        <div class="row mb-4">
            <div class="col-12 col-md-8">
                <h4 class="mb-0 fw-bold">Clientes Desagrupados</h4>
                <p class="text-muted">Lista de clientes configurados para o desagrupamento de pedidos.</p>
            </div>
            
            <div class="col-12 col-md-4 justify-content-end align-items-end mt-2 mt-md-0">
                <label for="searchCliente" class="form-label">Pesquisar Cliente</label>
                <div class="input-group">
                    <span class="input-group-text" id="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" id="searchCliente" class="form-control" placeholder="Digite a descrição..." aria-label="Pesquisar" aria-describedby="search-icon">
                </div>
            </div>
        </div>

        <div class="table-responsive table-container mt-2">
            <table class="table table-bordered table-hover table-sm tabela-compacta" id="TableClientes">
                <thead class="fixed-header">
                    <tr>
                        <th scope="col" class="text-center">Descrição do Cliente</th>
                    </tr>
                </thead>
                <tbody id="tbodyClientes">
                    <?php
                    // Verifica se a API retornou dados válidos
                    if ($clientesDesagrupados && is_array($clientesDesagrupados) && count($clientesDesagrupados) > 0) {
                        foreach ($clientesDesagrupados as $cliente) {
                            // htmlspecialchars previne problemas se houver aspas ou tags na string do banco
                            $descricao = htmlspecialchars($cliente['descricao_cliente'] ?? '');
                            
                            echo "<tr>";
                            echo "  <td class='text-center'>{$descricao}</td>";
                            echo "</tr>";
                        }
                    } else {
                        // Caso a API retorne vazio ou falso
                        echo "<tr>";
                        echo "  <td class='text-center text-muted p-4'>Nenhum cliente configurado para desagrupamento no momento.</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include_once("../../../templates/footer.php"); ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchCliente");
        const tableBody = document.getElementById("tbodyClientes");
        const rows = tableBody.getElementsByTagName("tr");

        searchInput.addEventListener("keyup", function() {
            const filter = searchInput.value.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                // Pega o texto da primeira coluna (Descrição do Cliente)
                let td = rows[i].getElementsByTagName("td")[0]; 
                if (td) {
                    let textValue = td.textContent || td.innerText;
                    // Se o texto digitado existir na linha, mostra, senão, esconde (display: none)
                    if (textValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });
    });
</script>

<script src="script.js"></script>