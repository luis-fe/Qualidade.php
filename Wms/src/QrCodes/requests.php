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

function ImprimirQrCode($empresa, $token, $dados) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/GerarCaixa";

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
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } elseif ($httpCode >= 400) {
        $response = [
            'status' => false,
            'message' => "Erro na API: Código HTTP {$httpCode}",
            'apiResponse' => json_decode($apiResponse, true)
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

        if ($acao == 'Consultar_Usuarios') {
            header('Content-Type: application/json');
            echo json_encode(ConsultarUsuarios($empresa, $token));
        }
    }
} 
 elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Capturando o corpo da requisição PUT
    $input = file_get_contents("php://input");
    $putData = json_decode($input, true);

    if (isset($putData["acao"]) && $putData["acao"] == 'Imprimir_QrCodes') {
        if (isset($putData["dados"])) {
            $dados = $putData["dados"];
            header('Content-Type: application/json');
            echo ImprimirQrCode($empresa, $token, $dados);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Erro: Dados não fornecidos.']);
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
?>
