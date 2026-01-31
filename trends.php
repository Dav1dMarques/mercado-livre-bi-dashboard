<?php
require_once 'auth.php';
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

$cat = $_GET['cat'];
$token = obterAccessToken($clientId, $clientSecret, $tokenFile);

// LÓGICA CORRIGIDA:
// Se houver categoria, usa a URL com /$cat. Se não houver, usa a URL geral.
if (!empty($cat)) {
    $url = "https://api.mercadolibre.com/trends/MLB/$cat";
} else {
    $url = "https://api.mercadolibre.com/trends/MLB";
}

$apiResult = chamarAPI($url, $token);

// Se o token expirou (401), renova e tenta de novo
if ($apiResult['code'] == 401) {
    $token = renovarToken($clientId, $clientSecret, $tokenFile);
    if ($token) {
        $apiResult = chamarAPI($url, $token);
    }
}

// Retorna o resultado da API ou um array vazio em caso de erro crítico
if ($apiResult['code'] != 200) {
    echo json_encode([]);
} else {
    echo $apiResult['body'];
}