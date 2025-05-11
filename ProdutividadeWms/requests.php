<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];
}

function ConsultarFaturamento($dataInicio, $dataFim) {
    $baseUrl = 'http://10.162.0.190:5000';
    $apiUrl = "{$baseUrl}/api/Faturamento?empresa=1&dataInicio={$dataInicio}&dataFim={$dataFim}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a40016aabcx9",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultarProdutividade($dataInicio, $dataFim, $Consulta, $HoraInicio, $HoraFim) {
    $baseUrl = 'http://10.162.0.190:5000';
    $apiUrl = "{$baseUrl}/api/{$Consulta}/Resumo?DataInicial={$dataInicio}&DataFinal={$dataFim}&horarioInicial={$HoraInicio}&horarioFinal={$HoraFim}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a40016aabcx9",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultarProdutividadeCaixa($dataInicio, $dataFim) {
    $baseUrl = 'http://10.162.0.190:5000';
    $apiUrl = "{$baseUrl}/api/ProdCarregarEndereco?empresa=1&dataInicio={$dataInicio}&dataFinal={$dataFim}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a40016aabcx9",
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

        $dataInicio = isset($_GET["dataInicio"]) ? $_GET["dataInicio"] : "";
        $dataFim = isset($_GET["dataFim"]) ? $_GET["dataFim"] : "";
        $Consulta = isset($_GET["Consulta"]) ? $_GET["Consulta"] : "";
        $HoraInicio = isset($_GET["HoraInicio"]) ? $_GET["HoraInicio"] : "";
        $HoraFim = isset($_GET["HoraFim"]) ? $_GET["HoraFim"] : "";

        if ($acao == 'Consultar_Faturamentos') {
            header('Content-Type: application/json');
            echo json_encode(ConsultarFaturamento($dataInicio, $dataFim));
        } elseif ($acao == 'Consultar_Produtividade') {
            header('Content-Type: application/json');
            echo json_encode(ConsultarProdutividade($dataInicio, $dataFim, $Consulta, $HoraInicio, $HoraFim));
        } elseif ($acao == 'Consultar_Produtividade_Caixa') {
            header('Content-Type: application/json');
            echo json_encode(ConsultarProdutividadeCaixa($dataInicio, $dataFim));
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
?>
