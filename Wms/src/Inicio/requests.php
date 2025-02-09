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


function ConsultarDadosEstoque($empresa, $token)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/RelatorioTotalFila";
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

function ConsultarDadosPedidos($empresa, $token)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/statuspedidos";
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

function ConsultarDadosEnderecos($empresa, $token, $natureza)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/DisponibilidadeEnderecos?empresa={$empresa}&natureza={$natureza}";
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

        if ($acao == 'Consultar_Dados_Estoque') {
            header('Content-Type: application/json');
            echo json_encode(ConsultarDadosEstoque($empresa, $token));
        } elseif ($acao == 'Consultar_Dados_Pedidos'){
            header('Content-Type: application/json');
            echo json_encode(ConsultarDadosPedidos($empresa, $token));
        } elseif ($acao == 'Consultar_Dados_Enderecos'){
            if (isset($_GET['natureza'])) {
                $natureza = $_GET['natureza'];
                header('Content-Type: application/json');
                echo json_encode(ConsultarDadosEnderecos($empresa, $token, $natureza));
            }
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
