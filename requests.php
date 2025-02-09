<?php

function fazerChamadaApi($username, $password, $empresa) {
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    
    $apiUrl = "{$baseUrl}/api/UsuarioSenha?codigo={$username}&senha={$password}";

    $ch = curl_init($apiUrl);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: a40016aabcx9',
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        die('Erro na requisição: ' . curl_error($ch));
    }

    curl_close($ch);

    $Resposta = json_decode($apiResponse, true);
    
    return $Resposta;
}
?>

