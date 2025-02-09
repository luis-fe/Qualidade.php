<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];
} else {
    header("Location: ../../../index_.php");
}

function consultarPedidos($empresa, $token) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/FilaPedidos";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: {$token}",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch));
        return false;
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function consultarUsuarios($empresa, $token) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/Usuarios";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: {$token}",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch));
        return false;
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function RecarregarPedidos($empresa, $token) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/RecarregarPedidos?empresa={$empresa}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: {$token}",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch));
        return false;
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function consultarPecasFaltantes($empresa, $token, $codPedido) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $codPedidoArray = explode(',', $codPedido);
    $results = [];

    foreach ($codPedidoArray as $pedido) {
        $apiUrl = "{$baseUrl}/api/DetalharPedido?codPedido={$pedido}";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: {$token}",
        ]);

        $apiResponse = curl_exec($ch);

        if (!$apiResponse) {
            error_log("Erro na requisição: " . curl_error($ch));
            return false;
        }

        curl_close($ch);

        $results[] = json_decode($apiResponse, true);
    }

    return $results;
}

function atribuirPedidos($empresa, $token, $dados) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/AtribuirPedidos";

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
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function alterarPrioridade($empresa, $token, $dados) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/Prioriza";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: {$token}",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        error_log("Erro na API: Código HTTP {$httpCode}");
        return false;
    }

    return json_decode($apiResponse, true);
}

function LimparSeparacao($empresa, $token, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/LimparPedido";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: {$token}",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        error_log("Erro na API: Código HTTP {$httpCode}");
        return false;
    }

    return json_decode($apiResponse, true);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["acao"])) {
        $acao = $_GET["acao"];

        if ($acao == 'Consultar_Pedidos') {
            $pedidos = consultarPedidos($empresa, $token);
            if ($pedidos !== false) {
                header('Content-Type: application/json');
                echo json_encode($pedidos);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erro ao consultar pedidos']);
            }
        } elseif ($acao == 'Consultar_Usuarios') {
            $usuarios = consultarUsuarios($empresa, $token);
            if ($usuarios !== false) {
                header('Content-Type: application/json');
                echo json_encode($usuarios);
            } 
        } elseif ($acao == 'Recarregar_Pedidos') {
                $Pedidos = RecarregarPedidos($empresa, $token);
                if ($Pedidos !== false) {
                    header('Content-Type: application/json');
                    echo json_encode($Pedidos);
                }
                else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erro ao consultar usuários']);
            }
        } elseif ($acao == 'Consultar_Pecas_Faltantes' && isset($_GET['CodPedido'])) {
            $codPedido = is_array($_GET['CodPedido']) ? implode(',', $_GET['CodPedido']) : $_GET['CodPedido'];
            $pecasFaltantes = consultarPecasFaltantes($empresa, $token, $codPedido);
            if ($pecasFaltantes !== false) {
                header('Content-Type: application/json');
                echo json_encode($pecasFaltantes);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erro ao consultar peças faltantes']);
            }
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestData = json_decode(file_get_contents('php://input'), true);
    $acao = $requestData['acao'] ?? null;
    $dados = $requestData['dados'] ?? null;
    if ($acao) {
        if ($acao == 'Atribuir_Pedidos') {
            $dadosObjeto = (object)$dados;
            $atribuicao = atribuirPedidos($empresa, $token, $dadosObjeto);
            if ($atribuicao !== false) {
                header('Content-Type: application/json');
                echo json_encode($atribuicao);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erro ao atribuir pedidos']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Ação não reconhecida.']);
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $requestData = json_decode(file_get_contents('php://input'), true);
    $acao = $requestData['acao'] ?? null;
    $dados = $requestData['dados'] ?? null;
    if ($acao == 'Alterar_Prioridade') {
        $dadosObjeto = (object)$dados;
        $prioridade = alterarPrioridade($empresa, $token, $dadosObjeto);
        if ($prioridade !== false) {
            header('Content-Type: application/json');
            echo json_encode($prioridade);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erro ao alterar prioridade']);
        }
    } elseif ($acao == 'Limpar_Separacao'){
        $dadosObjeto = (object)$dados;
        header('Content-Type: application/json');
        echo json_encode(LimparSeparacao($empresa, $token, $dadosObjeto));
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Ação não reconhecida.']);
    }
} else {
    error_log("Método de ação não especificado no método POST");
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro: Método de requisição não suportado.']);
}

