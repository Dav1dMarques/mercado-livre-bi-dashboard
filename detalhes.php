<?php
require_once 'auth.php';
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode(["error" => "ID do produto não fornecido"]);
    exit;
}

$token = obterAccessToken($clientId, $clientSecret, $tokenFile);
$url = "https://api.mercadolibre.com/products/$id";

$apiResult = chamarAPI($url, $token);

// Se o token expirou (401), renova automaticamente
if ($apiResult['code'] == 401) {
    $token = renovarToken($clientId, $clientSecret, $tokenFile);
    $apiResult = chamarAPI($url, $token);
}

if ($apiResult['code'] !== 200) {
    echo json_encode(["error" => "Produto não encontrado", "status" => $apiResult['code']]);
    exit;
}

$p = json_decode($apiResult['body'], true);

// No BI, o que importa são os Atributos (Especificações)
$specs = [];
foreach ($p['attributes'] ?? [] as $attr) {
    $specs[] = [
        "name" => $attr['name'],
        "value" => $attr['value_name']
    ];
}

$out = [
    "id" => $p['id'],
    "title" => $p['name'] ?? '',
    "price" => isset($p['buy_box_winner']['price']) ? "R$ " . number_format($p['buy_box_winner']['price'], 2, ',', '.') : "Sob consulta",
    "img" => $p['pictures'][0]['url'] ?? '',
    "specs" => $specs, // Array limpo com as especificações técnicas
    "brand" => $p['main_features']['brand'] ?? 'Não informada'
];

echo json_encode($out);