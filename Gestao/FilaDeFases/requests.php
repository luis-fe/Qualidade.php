<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



function ConsultaColecoes()
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/DistinctColecao";
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

function ConsultarFilaDasFases($dados)
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/FilaFases";

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
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultarDetalhaFila($dados)
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/DetalhaOpFilas";

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
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}



function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($_GET["acao"])) {
            $acao = $_GET["acao"];
            switch ($acao) {
                case 'Consultar_Colecao':
                    jsonResponse(ConsultaColecoes());
                    break;

                default:
                    jsonResponse(['status' => false, 'message' => 'Ação GET não reconhecida.']);
                    break;
            }
        }
        break;

    case "POST":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            switch ($acao) {

                case 'Consultar_Fila_Fases':
                    $requestData = json_decode(file_get_contents('php://input'), true);
                    $dados = $requestData['dados'] ?? null;
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo json_encode(ConsultarFilaDasFases($dadosObjeto));
                    break;
                case 'Consultar_Detalha_Fila':
                    $requestData = json_decode(file_get_contents('php://input'), true);
                    $dados = $requestData['dados'] ?? null;
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo json_encode(ConsultarDetalhaFila($dadosObjeto));
                    break;

                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;

        // case "PUT":
        //     $requestData = json_decode(file_get_contents('php://input'), true);
        //     $acao = $requestData['acao'] ?? null;
        //     $dados = $requestData['dados'] ?? null;
        //     if ($acao === 'Cadastrar_Justificativa') {
        //         $requestData = json_decode(file_get_contents('php://input'), true);
        //         $dados = $requestData['dados'] ?? null;
        //         $dadosObjeto = (object)$dados;
        //         header('Content-Type: application/json');
        //         echo json_encode(CadastrarJustificativa($dadosObjeto));
        //     } else {
        //         jsonResponse(['status' => false, 'message' => 'Ação PUT não reconhecida.']);
        //     }
        //     break;

    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}
