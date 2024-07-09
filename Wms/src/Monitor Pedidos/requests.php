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


function ConsultarPedidos($empresa, $token, $iniVenda, $finalVenda, $tipoNota, $parametroClassificacao, $tipoData)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/monitorPreFaturamento?empresa={$empresa}&iniVenda={$iniVenda}&finalVenda={$finalVenda}&tiponota={$tipoNota}&parametroClassificacao={$parametroClassificacao}&tipoData={$tipoData}";
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
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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

function DetalharOp($empresa, $numeroOp)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["acao"])) {
        $acao = $_GET["acao"];

        if ($acao == 'Consultar_Pedidos') {
            $iniVenda = $_GET['iniVenda'];
            $finalVenda = $_GET['finalVenda'];
            $tipoNota = $_GET['tipoNota'];
            $parametroClassificacao = $_GET['parametroClassificacao'];
            $tipoData = $_GET['tipoData'];
        
            header('Content-Type: application/json');
            echo json_encode(ConsultarPedidos($empresa, $token, $iniVenda, $finalVenda, $tipoNota, $parametroClassificacao, $tipoData));
        } elseif ($acao == 'Consultar_Ops'){
            $dataInicio = $_GET['dataInicio'];
            $dataFim = $_GET['dataFim'];
            header('Content-Type: application/json');
            echo json_encode(ConsultarOps($empresa, $dataInicio, $dataFim));
        } elseif ($acao == 'Detalhar_Op'){
            $numeroOp = $_GET['numeroOp'];
            header('Content-Type: application/json');
            echo json_encode(DetalharOp($empresa, $numeroOp));
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
