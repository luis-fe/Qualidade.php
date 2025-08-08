<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['username'];
    $empresa = $_SESSION['empresa'];
} else {
    header("Location: ../../indexPcp.php");
}

function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($_GET["acao"])) {
            $acao = $_GET["acao"];
            switch ($acao) {
                case 'Consulta_Notas':
                    jsonResponse(ConsultarNotas('1'));
                    break;
                case 'Consultar_Ops':
                    $dataInicio = $_GET['dataInicio'];
                    $dataFim = $_GET['dataFim'];
                    jsonResponse(ConsultarOps('1', $dataInicio, $dataFim));
                    break;
                case 'Consultar_Lista_Pedidos':
                    $iniVenda = $_GET['iniVenda'];
                    $finalVenda = $_GET['finalVenda'];
                    jsonResponse(ConsultarListaPedidos('1', $iniVenda, $finalVenda));
                    break;


                default:
                    jsonResponse(['status' => false, 'message' => 'Ação GET não reconhecida.']);
                    break;
            }
        }
        break;
    case "POST":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            switch ($acao) {
                case 'Consultar_Pedidos':
                    header('Content-Type: application/json');
                    echo (ConsultarPedidos($dados));
                    break;
                case 'Consultar_Sem_Ops':
                    header('Content-Type: application/json');
                    echo (ConsultaProdutoSemOp('1', $dados));
                    break;
                case 'Filtros_Op':
                    header('Content-Type: application/json');
                    echo (FiltrosOps('1', $dados));
                    break;
                case 'Consultar_Skus':
                    header('Content-Type: application/json');
                    echo (ConsultaSkus('1', $dados));
                    break;
                case 'Consultar_Skus_Pedidos':
                    header('Content-Type: application/json');
                    echo (ConsultaSkusPedido('1', $dados));
                    break;

                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;
    case "PUT":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            switch ($acao) {
                case 'Alterar_Plano':
                    header('Content-Type: application/json');
                    echo json_encode(AlterarPlano('1',  $dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;
    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}

function ConsultarNotas($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http:/10.162.0.53:9000' : 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/TipoNotasCsw";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultarPedidos($dados)
{
    $minhaEmpresa = $_SESSION['empresa'];
        // Adiciona a chave "empresa" no array $dados
    $dados['empresa'] = $minhaEmpresa;

    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/monitorPreFaturamento";
    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
}

function ConsultarListaPedidos($empresa, $dataInicio, $dataFim)
{
    $baseUrl = ($empresa == "1") ? '10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/ListaPedidos?iniVenda={$dataInicio}&finalVenda={$dataFim}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultarOps($empresa, $dataInicio, $dataFim)
{

    $minhaEmpresa = $_SESSION['empresa'];
        // Adiciona a chave "empresa" no array $dados


    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/monitorOPs?empresa={$minhaEmpresa}&dataInico={$dataInicio}&dataFim={$dataFim}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}


function ConsultaProdutoSemOp($empresa, $dados)
{
    $minhaEmpresa = $_SESSION['empresa'];
        // Adiciona a chave "empresa" no array $dados
    $dados['empresa'] = $minhaEmpresa;
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/ProdutosSemOP";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
}

function FiltrosOps($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/monitorOPsFiltroPedidos";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
}


function ConsultaSkusPedido($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/Op_tam_corPedidos";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
}

function ConsultaSkus($empresa, $dados)
{
        $minhaEmpresa = $_SESSION['empresa'];
        // Adiciona a chave "empresa" no array $dados
    $dados['empresa'] = $minhaEmpresa;
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/Op_tam_cor";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
}
