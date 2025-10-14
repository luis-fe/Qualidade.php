<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

function ConsultarTags($empresa, $token, $codigoBarras) {
    $empresa = "1";
    $baseUrl = 'http://10.162.0.190:5000';
    $apiUrl = "{$baseUrl}/api/ConsultaPedidoViaTag?codBarras={$codigoBarras}";

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

        if ($acao == 'Consultar_Tags') {
            if (isset($_GET['codigoBarras'])) {
                $codigoBarras = $_GET['codigoBarras'];

                // Definir empresa e token antes de chamar a função
                $empresa = 1; // ou obter dinamicamente de $_GET, $_SESSION, etc.
                $token = "a40016aabcx9";

                header('Content-Type: application/json');
                echo json_encode(ConsultarTags($empresa, $token, $codigoBarras));
            } else {
                echo json_encode(['status' => false, 'message' => 'Código de barras não informado.']);
            }
        }
    }
} else {
    error_log("Método de ação não especificado no método POST", 0);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
}
?>
