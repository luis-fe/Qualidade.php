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
                case 'ConsultarEnderecos':
                    // Verifica se a função existe antes de chamar (para evitar erros se você apagou algo do original)
                    if (function_exists('ConsultarEnderecos')) jsonResponse(ConsultarEnderecos('1'));
                    break;
                case 'get_consultar_endereco':
                    $endereco = $_GET['endereco'];
                    jsonResponse(get_consultar_endereco($endereco));
                    break;
                case 'obterNomeItem':
                    $codMaterial = $_GET['codMaterial'];
                    jsonResponse(obterNomeItem($codMaterial));
                    break;
                case 'devolver_ultima_sequencia_item': // <--- NOSSA AÇÃO GET PARA A SEQUÊNCIA
                    $empresa = $_GET['empresa'] ?? '1';
                    $codMaterial = $_GET['codMaterial'] ?? '';
                    jsonResponse(devolver_ultima_sequencia_item($empresa, $codMaterial));
                    break;
                case 'Consulta_Lotes':
                    if (function_exists('ConsultarLotes')) {
                        $plano = $_GET['plano'];
                        jsonResponse(ConsultarLotes('1', $plano));
                    }
                    break;
                case 'Consultar_Realizados':
                    if (function_exists('ConsultarRealizados')) {
                        $Fase = $_GET['Fase'];
                        $dataInicial = $_GET['dataInicial'];
                        $dataFinal = $_GET['dataFinal'];
                        jsonResponse(ConsultarRealizados('1', $Fase, $dataInicial, $dataFinal));
                    }
                    break;
                case 'Consultar_RealizadosDia':
                    if (function_exists('ConsultarRealizadosDia')) {
                        $Fase = $_GET['Fase'];
                        $dataInicial = $_GET['dataInicial'];
                        jsonResponse(ConsultarRealizadosDia('1', $Fase, $dataInicial));
                    }
                    break;
                case 'Consultar_Cronograma':
                    if (function_exists('ConsultarCronograma')) {
                        $plano = $_GET['plano'];
                        $fase = $_GET['fase'];
                        jsonResponse(ConsultarCronograma('1', $plano, $fase));
                    }
                    break;
                case 'Consultar_Tipo_Op':
                    if (function_exists('ConsultarTipoOp')) jsonResponse(ConsultarTipoOp('1'));
                    break;
                case 'ConsultaFaltaProduzirCategoria_Fase':
                    if (function_exists('ConsultaFaltaProduzirCategoria_Fase')) {
                        $dados = $_GET['dados'] ?? null; 
                        header('Content-Type: application/json');
                        echo json_encode(ConsultaFaltaProduzirCategoria_Fase($dados));
                        exit;
                    }
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Acao GET não reconhecida.']);
                    break;
            }
        }
        break;

    case "POST":
        // 1. Tenta ler como JSON
        $requestData = json_decode(file_get_contents('php://input'), true);
        
        // 2. Tenta pegar a ação de onde vier (FormData, JSON ou GET)
        $acao = $_POST['acao'] ?? ($requestData['acao'] ?? ($_GET['acao'] ?? null));
        
        // 3. Isola os dados (removendo a própria ação do pacote)
        if (!empty($_POST)) {
            $dados = $_POST;
            unset($dados['acao']); 
        } else {
            $dados = $requestData['dados'] ?? $requestData;
        }

        if ($acao) {
            switch ($acao) {
                case 'inserir_endereco_item_reposto_kit':
                    jsonResponse(inserir_endereco_item_reposto_kit($dados));
                    break;
                case 'estornarEtiqueta': // <--- NOSSA AÇÃO POST DO DESDOBRO
                    jsonResponse(desdobro_etiqueta($dados));
                    break;
                case 'inserir_atualizar_sequencia_codMaterial': // <--- NOSSA AÇÃO POST DA SEQUÊNCIA
                    jsonResponse(inserir_atualizar_sequencia_codMaterial($dados));
                    break;
                case 'inserir_endereco_massa':
                    if (function_exists('inserir_endereco_massa')) jsonResponse(inserir_endereco_massa($dados));
                    break;
                case 'Consulta_fila_fase':
                    if (function_exists('ConsultaFilaResumo')) jsonResponse(ConsultaFilaResumo($dados));
                    break;
                case 'ConsultaFilaResumoCategoria':
                    if (function_exists('ConsultaFilaResumoCategoria')) jsonResponse(ConsultaFilaResumoCategoria($dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => "Ação POST '{$acao}' não reconhecida."]);
                    break;
            }
        } else {
            jsonResponse(['status' => false, 'message' => 'Ação não informada no POST.']);
        }
        break;

    case "PUT":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            switch ($acao) {
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação PUT não reconhecida.']);
                    break;
            }
        }
        break;

    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}


// ==========================================
// FUNÇÕES DE COMUNICAÇÃO COM A API DO PCP
// ==========================================

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

function inserir_endereco_item_reposto_kit($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_endereco_item_reposto_kit";
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
        return ['status' => false, 'message' => 'Falha de comunicação com a API interna.'];
    }

    curl_close($ch);
    return json_decode($apiResponse, true);
}


function get_consultar_endereco($endereco)
{
    $baseUrl ='http://10.162.0.53:9000';
    $enderecoCodificado = urlencode($endereco);
    $apiUrl = "{$baseUrl}/pcp/api/get_consultar_endereco?endereco={$enderecoCodificado}";
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

function obterNomeItem($codMaterial)
{
    $baseUrl ='http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/procurar_nome_item_considear?codMaterial={$codMaterial}";
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

function desdobro_etiqueta($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/desdobro_etiqueta";
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

function devolver_ultima_sequencia_item($empresa, $codMaterial)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/devolver_ultima_sequencia_item?codMaterial={$codMaterial}&codEmpresa={$empresa}";
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

function inserir_atualizar_sequencia_codMaterial($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_atualizar_sequencia_codMaterial";
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