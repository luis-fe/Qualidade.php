<?php


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
                case 'ConsultarRecebimento':
                    jsonResponse(ConsultarRecebimento('1'));
                    break;
                case 'Consulta_Lotes':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultarLotes('1', $plano));
                    break;
                case 'Consultar_Realizados':
                    $Fase = $_GET['Fase'];
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    jsonResponse(ConsultarRealizados('1', $Fase, $dataInicial, $dataFinal));
                    break;
                case 'Consultar_RealizadosDia':
                        $Fase = $_GET['Fase'];
                        $dataInicial = $_GET['dataInicial'];
                        jsonResponse(ConsultarRealizadosDia('1', $Fase, $dataInicial));
                        break;
                case 'Consultar_Cronograma':
                    $plano = $_GET['plano'];
                    $fase = $_GET['fase'];
                    jsonResponse(ConsultarCronograma('1', $plano, $fase));
                    break;
                case 'Consultar_Tipo_Op':
                    jsonResponse(ConsultarTipoOp('1'));
                    break;
               
                case 'ConsultaFaltaProduzirCategoria_Fase':
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaFaltaProduzirCategoria_Fase($dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Acao GET não reconhecida.']);
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
                case 'Consulta_Falta_Produzir_Categoria':
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaFaltaProduzirCategoria_Fase($dados));
                    break;
                case 'inserir_endereco':
                        header('Content-Type: application/json');
                        echo json_encode(inserir_endereco($dados));
                        break;
                case 'inserir_endereco_massa':
                        jsonResponse(inserir_endereco_massa($dados));
                        break;
                case 'Consulta_fila_fase':
                        jsonResponse(ConsultaFilaResumo($dados));
                        break;
                case 'ConsultaFilaResumoCategoria':
                        jsonResponse(ConsultaFilaResumoCategoria($dados));
                        break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;
    case "PUT":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            switch ($acao) {
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;
    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}


function inserir_endereco($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_endereco_aviamento";
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

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function inserir_endereco_massa($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_endereco_aviamento_em_massa";
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

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}



function ConsultarRecebimento($empresa)
{
    $baseUrl ='http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/Fila_recebimento_Aviamentos";
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

