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

function ConsultarInventarios($empresa, $token, $dataInicio, $dataFim, $natureza) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/RelatorioInventario?natureza={$natureza}&datainicio={$dataInicio}&datafinal={$dataFim}";
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

        // Verifica se as variáveis de data foram passadas e as define
        $dataInicio = isset($_GET["dataInicio"]) ? $_GET["dataInicio"] : "";
        $dataFim = isset($_GET["dataFim"]) ? $_GET["dataFim"] : "";
        $natureza = isset($_GET["natureza"]) ? $_GET["natureza"] : "";

        if ($acao == 'Consultar_Inventarios') {
            header('Content-Type: application/json');
            echo json_encode(ConsultarInventarios($empresa, $token, $dataInicio, $dataFim, $natureza));
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
?>
