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
        padding: 0.25rem 0.5rem !important;
        font-size: 13px; 
        vertical-align: middle; 
        line-height: 1.2; 
    }
</style>

<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height p-4">
        
        <div class="row mb-4">
            <div class="col-12 col-md-8">
                <h4 class="mb-0 fw-bold">Clientes Desagrupados</h4>
                <p class="text-muted mb-2">Lista de clientes configurados para o desagrupamento de pedidos.</p>
                <button type="button" class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalInserirCliente">
                    <i class="fa-solid fa-plus"></i> Inserir Cliente
                </button>
            </div>
            
            <div class="col-12 col-md-4 justify-content-end align-items-end mt-2 mt-md-0">
                <label for="searchCliente" class="form-label">Pesquisar Cliente na Tela</label>
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
                        <th scope="col" class="text-center" style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody id="tbodyClientes">
                    <?php
                    // Verifica se a API retornou dados válidos
                    if ($clientesDesagrupados && is_array($clientesDesagrupados) && count($clientesDesagrupados) > 0) {
                        foreach ($clientesDesagrupados as $cliente) {
                            // Adicionado ENT_QUOTES para evitar quebra no Javascript se o nome tiver aspas
                            $descricao = htmlspecialchars($cliente['descricao_cliente'] ?? '', ENT_QUOTES, 'UTF-8');
                            
                            echo "<tr>";
                            echo "  <td class='text-center'>{$descricao}</td>";
                            echo "  <td class='text-center'>";
                            echo "      <button type='button' class='btn btn-danger btn-sm' onclick='excluirCliente(\"{$descricao}\")' title='Excluir'>";
                            echo "          <i class='fa-solid fa-trash'></i>";
                            echo "      </button>";
                            echo "  </td>";
                            echo "</tr>";
                        }
                    } else {
                        // Caso a API retorne vazio ou falso
                        echo "<tr>";
                        echo "  <td class='text-center text-muted p-4' colspan='2'>Nenhum cliente configurado para desagrupamento no momento.</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="modalInserirCliente" tabindex="-1" aria-labelledby="modalInserirClienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalInserirClienteLabel">Inserir Cliente para Desagrupamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="selectClientesAtuais" class="form-label">Selecione o Cliente</label>
            <select class="form-select" id="selectClientesAtuais">
                <option value="">Aguarde, carregando clientes...</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSalvarCliente">Inserir</button>
      </div>
    </div>
  </div>
</div>

<?php include_once("../../../templates/footer.php"); ?>

<script>
    // --- Nova Função: Excluir Cliente ---
    function excluirCliente(descricaoCliente) {
        // Confirmação para evitar exclusão acidental
        if (confirm(`Tem certeza que deseja remover o cliente "${descricaoCliente}" do desagrupamento?`)) {
            const payload = {
                acao: 'Excluir_Cliente',
                dados: {
                    descricao_cliente: descricaoCliente
                }
            };

            fetch('requests.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert("Erro ao excluir: " + data.error);
                } else {
                    alert("Cliente removido do desagrupamento com sucesso!");
                    location.reload(); // Recarrega a página para atualizar a tabela
                }
            })
            .catch(error => {
                console.error("Erro na exclusão:", error);
                alert("Ocorreu um erro ao comunicar com o servidor.");
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // --- Lógica do Filtro da Tabela Principal ---
        const searchInput = document.getElementById("searchCliente");
        const tableBody = document.getElementById("tbodyClientes");
        const rows = tableBody.getElementsByTagName("tr");

        searchInput.addEventListener("keyup", function() {
            const filter = searchInput.value.toLowerCase();
            for (let i = 0; i < rows.length; i++) {
                let td = rows[i].getElementsByTagName("td")[0]; 
                if (td) {
                    let textValue = td.textContent || td.innerText;
                    if (textValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });

        // --- Lógica do Modal e APIs ---
        const modalInserir = document.getElementById('modalInserirCliente');
        const selectClientes = document.getElementById('selectClientesAtuais');
        const btnSalvar = document.getElementById('btnSalvarCliente');

        // Quando o modal for aberto, busca os clientes da API GET
        modalInserir.addEventListener('show.bs.modal', function () {
            selectClientes.innerHTML = '<option value="">Aguarde, carregando clientes...</option>';
            
            fetch('requests.php?acao=Listar_Clientes_Atuais')
                .then(response => response.json())
                .then(data => {
                    selectClientes.innerHTML = '<option value="">Selecione um cliente da lista...</option>';
                    
                    if(data && !data.error && Array.isArray(data)) {
                        data.forEach(cliente => {
                            let nomeCliente = cliente.descricao_cliente || cliente.cliente || cliente.desc_cliente || Object.values(cliente)[0];
                            
                            let option = document.createElement('option');
                            option.value = nomeCliente;
                            option.textContent = nomeCliente;
                            selectClientes.appendChild(option);
                        });
                    } else {
                        selectClientes.innerHTML = '<option value="">Nenhum cliente disponível</option>';
                    }
                })
                .catch(error => {
                    console.error("Erro ao buscar clientes:", error);
                    selectClientes.innerHTML = '<option value="">Erro ao carregar clientes</option>';
                });
        });

        // Quando clicar em "Inserir", dispara a API POST
        btnSalvar.addEventListener('click', function() {
            const clienteSelecionado = selectClientes.value;
            
            if(!clienteSelecionado) {
                alert("Por favor, selecione um cliente na lista.");
                return;
            }

            // Desabilita o botão para evitar duplos cliques
            btnSalvar.disabled = true;
            btnSalvar.textContent = "Inserindo...";

            const payload = {
                acao: 'Inserir_Cliente',
                dados: {
                    descricao_cliente: clienteSelecionado
                }
            };

            fetch('requests.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert("Erro ao inserir: " + data.error);
                } else {
                    alert("Cliente adicionado ao desagrupamento com sucesso!");
                    location.reload(); 
                }
            })
            .catch(error => {
                console.error("Erro na inserção:", error);
                alert("Ocorreu um erro ao comunicar com o servidor.");
            })
            .finally(() => {
                btnSalvar.disabled = false;
                btnSalvar.textContent = "Inserir";
            });
        });
    });
</script>

<script src="script.js"></script>