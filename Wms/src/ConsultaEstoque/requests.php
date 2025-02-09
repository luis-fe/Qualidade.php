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
    header("Location: ../../../index.php");
}


function ConsultarEstoque($empresa, $token, $natureza)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/ConsultaEstoqueWMS?natureza={$natureza}";
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



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["acao"])) {
        $acao = $_GET["acao"];

        if ($acao == 'Consultar_Estoque') {
            $natureza = $_GET['natureza'];
            header('Content-Type: application/json');
            echo json_encode(ConsultarEstoque($empresa, $token, $natureza));
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
