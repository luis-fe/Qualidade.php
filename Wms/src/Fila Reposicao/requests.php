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

function ConsultarFila($empresa, $token, $natureza)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/DetalhamentoFila?empresa={$empresa}&natureza={$natureza}";
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

function AtualizarFila($empresa, $token)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/AtualizarTagsEstoque?empresa={$empresa}&IntervaloAutomacao=20";
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

function ConsultarCaixa($empresa, $token, $numCaixa)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/DetalharCaixa?numeroCaixa={$numCaixa}";
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

function ConsultarReduzidos($empresa, $natureza, $token, $numeroOp, $codReduzido)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/DetalhaTagsNumeroOPReduzido?numeroop={$numeroOp}&codreduzido={$codReduzido}&codEmpresa={$empresa}&natureza={$natureza}";
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



        if ($acao == 'Consultar_Fila') {
            $natureza = isset($_GET["natureza"]) ? $_GET["natureza"] : "";
            header('Content-Type: application/json');
            echo json_encode(ConsultarFila($empresa, $token, $natureza));
        } elseif ($acao == 'Consultar_Caixa') {
            $numCaixa = isset($_GET["numCaixa"]) ? $_GET["numCaixa"] : "";
            header('Content-Type: application/json');
            echo json_encode(ConsultarCaixa($empresa, $token, $numCaixa));
        } elseif ($acao == 'Consultar_Reduzido') {
            $natureza = isset($_GET["natureza"]) ? $_GET["natureza"] : "";
            $codReduzido = isset($_GET["codReduzido"]) ? $_GET["codReduzido"] : "";
            $numeroOp = isset($_GET["numeroOp"]) ? $_GET["numeroOp"] : "";
            header('Content-Type: application/json');
            echo json_encode(ConsultarReduzidos($empresa, $natureza, $token, $numeroOp, $codReduzido));
        } elseif ($acao == 'Atualizar_Fila') {
            header('Content-Type: application/json');
            echo json_encode(AtualizarFila($empresa, $token));
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
