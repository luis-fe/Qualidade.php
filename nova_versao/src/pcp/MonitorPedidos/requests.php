<?php

set_time_limit(300);
session_start();
if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $usuario = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];
} else {
    header("Location: ../../../indexPcp.php");
}

$usuario = $_SESSION['usuario'];
$empresa = $_SESSION['empresa'];
$token = $_SESSION['token'];

function ConsultarPedidos($empresa, $iniVenda, $finalVenda, $tipoNota, $parametroClassificacao, $tipoData, $emissaoinicial, $emissaofinal)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/monitorPreFaturamento?empresa={$empresa}&iniVenda={$iniVenda}&finalVenda={$finalVenda}&tiponota={$tipoNota}&parametroClassificacao={$parametroClassificacao}&tipoData={$tipoData}&FiltrodataEmissaoInicial={$emissaoinicial}&FiltrodataEmissaoFinal={$emissaofinal}";
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
    $baseUrl = ($empresa == "1") ? '192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/monitorOPs?dataInico={$dataInicio}&dataFim={$dataFim}";
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


function ConsultarListaPedidos($empresa, $dataInicio, $dataFim)
{
    $baseUrl = ($empresa == "1") ? '192.168.0.183:8000' : 'http://10.162.0.191:8000';
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


function DetalharOp($empresa, $numeroOp)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/DelhalamentoMonitorOP?numeroOP={$numeroOp}";
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

function DetalharPedido($empresa, $numeroPedido)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/DetalhaPedidoMonitor?codPedido={$numeroPedido}";
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

function ConsultaTiposNotaCsw($empresa, $token)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/TipoNotasCsw";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: {$token}",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultaProdutoSemOp($empresa, $token, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/ProdutosSemOP";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: {$token}",
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
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["acao"])) {
        $acao = $_GET["acao"];

        if ($acao == 'Consultar_Pedidos') {
            $iniVenda = $_GET['iniVenda'];
            $finalVenda = $_GET['finalVenda'];
            $tipoNota = $_GET['tipoNota'];
            $parametroClassificacao = $_GET['parametroClassificacao'];
            $tipoData = $_GET['tipoData'];
            $emissaoinicial = $_GET['emissaoinicial'];
            $emissaofinal = $_GET['emissaofinal'];

            header('Content-Type: application/json');
            echo json_encode(ConsultarPedidos('1', $iniVenda, $finalVenda, $tipoNota, $parametroClassificacao, $tipoData, $emissaoinicial, $emissaofinal));
        } elseif ($acao == 'Consultar_Lista_Pedidos') {
            $iniVenda = $_GET['iniVenda'];
            $finalVenda = $_GET['finalVenda'];

            header('Content-Type: application/json');
            echo json_encode(ConsultarListaPedidos('1', $iniVenda, $finalVenda));
        } elseif ($acao == 'Consultar_Ops') {
            $dataInicio = $_GET['dataInicio'];
            $dataFim = $_GET['dataFim'];
            header('Content-Type: application/json');
            echo json_encode(ConsultarOps('1', $dataInicio, $dataFim));
        } elseif ($acao == 'Detalhar_Op') {
            $numeroOp = $_GET['numeroOp'];
            header('Content-Type: application/json');
            echo json_encode(DetalharOp('1', $numeroOp));
        } elseif ($acao == 'Detalhar_Pedido') {
            $numeroPedido = $_GET['numeroPedido'];
            header('Content-Type: application/json');
            echo json_encode(DetalharPedido('1', $numeroPedido));
        } elseif ($acao == 'Consulta_Notas') {
            echo json_encode(ConsultaTiposNotaCsw('1', 'a44pcp22'));
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {  // Corrigido para tratar requisições POST
    $requestData = json_decode(file_get_contents('php://input'), true);
    $acao = $requestData['acao'] ?? null;
    $dados = $requestData['dados'] ?? null;

    if ($acao) {
        if ($acao == 'Consulta_Sem_Op') {
            header('Content-Type: application/json');
            echo ConsultaProdutoSemOp('1', 'a44pcp22', $dados);
        } else if ($acao == 'Filtro_Monitor_Ops') {
            header('Content-Type: application/json');
            echo FiltrosOps('1', $dados);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Ação não reconhecida.']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Ação não especificada.']);
    }
} else {  // Se o método não for GET nem POST
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
