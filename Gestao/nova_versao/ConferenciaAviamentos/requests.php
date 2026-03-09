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
                case 'ConsultarFilaConferencia':
                    jsonResponse(ConsultarFilaConferencia('1'));
                    break;
                case 'Consultar_Usuarios':
                    $codEmpresa = $_GET['codEmpresa'] ?? '';
                    jsonResponse(Consultar_Usuarios($codEmpresa));
                    break;
                case 'ConsultarFilaConferencia_itens':
                    $numeroOP = $_GET['numeroOP'] ?? '';
                    jsonResponse(ConsultarFilaConferencia_itens('1', $numeroOP));
                    break;
                case 'get_obter_itens_configurados':
                    $codEmpresa = $_GET['codEmpresa'] ?? '';
                    jsonResponse(get_obter_itens_configurados($codEmpresa));
                    break;
                case 'get_obter_nome_material':
                    $codMaterial = $_GET['codMaterial'] ?? '';
                    jsonResponse(get_obter_nome_material($codMaterial));
                    break;
                case 'Consultar_RealizadosDia':
                    $Fase = $_GET['Fase'] ?? '';
                    $dataInicial = $_GET['dataInicial'] ?? '';
                    jsonResponse(ConsultarRealizadosDia('1', $Fase, $dataInicial));
                    break;
                case 'Consultar_Cronograma':
                    $plano = $_GET['plano'] ?? '';
                    $fase = $_GET['fase'] ?? '';
                    jsonResponse(ConsultarCronograma('1', $plano, $fase));
                    break;
                case 'Consultar_Tipo_Op':
                    jsonResponse(ConsultarTipoOp('1'));
                    break;
                case 'ConsultaFaltaProduzirCategoria_Fase':
                    // Correção: No GET, os dados vêm da URL, não do body
                    $dados = $_GET; 
                    jsonResponse(ConsultaFaltaProduzirCategoria_Fase($dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação GET não reconhecida.']);
                    break;
            }
        } else {
            jsonResponse(['status' => false, 'message' => 'Parâmetro acao ausente no GET.']);
        }
        break;

    case "POST":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        
        if ($acao) {
            switch ($acao) {
                case 'Consulta_Falta_Produzir_Categoria':
                    jsonResponse(ConsultaFaltaProduzirCategoria_Fase($dados));
                    break;
                case 'finalizar_conferencia':
                    jsonResponse(finalizar_conferencia($dados));
                    break;
                case 'inserir_conferencia_itens_op':
                    jsonResponse(inserir_conferencia_itens_op($dados));
                    break;
                case 'inserir_material_desconsiderar_conf':
                    jsonResponse(inserir_material_desconsiderar_conf($dados));
                    break;
                case 'remover_item_considerado':
                    jsonResponse(remover_item_considerado($dados));
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
        } else {
            jsonResponse(['status' => false, 'message' => 'Parâmetro acao ausente na requisição POST.']);
        }
        break;

    case "PUT":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        
        if ($acao) {
            switch ($acao) {
                default:
                    // Correção: Alterado de POST para PUT na mensagem de erro
                    jsonResponse(['status' => false, 'message' => 'Ação PUT não reconhecida.']);
                    break;
            }
        } else {
            jsonResponse(['status' => false, 'message' => 'Parâmetro acao ausente na requisição PUT.']);
        }
        break;

    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}

// ==========================================
// FUNÇÕES DE COMUNICAÇÃO COM A API EXTERNA
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

function inserir_conferencia_itens_op($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/conferenciaAviamentos_";
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

function inserir_material_desconsiderar_conf($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_material_desconsiderar_conf";
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


function finalizar_conferencia($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/finalizar_conferencia";
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


function remover_item_considerado($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/remover_item_considerado";
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
function Consultar_Usuarios($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/UsuarioHabilitadoAviamento?codEmpresa={$empresa}";
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

function ConsultarFilaConferencia($empresa)
{
    $baseUrl ='http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/FilaConferencia";
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

function ConsultarFilaConferencia_itens($empresa, $numeroOP)
{
    $baseUrl ='http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/ItensConferencia?numeroOP={$numeroOP}";
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

function get_obter_itens_configurados($empresa)
{
    $baseUrl ='http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/get_obter_itens_configurados?codEmpresa={$empresa}";
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

function get_obter_nome_material($codMaterial)
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