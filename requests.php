<?php

function fazerChamadaApi($username, $password, $empresa) {
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:5000' : 'http://192.168.0.184:5000';
    
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

