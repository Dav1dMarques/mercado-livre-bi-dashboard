<?php
// Tenta carregar as configurações do arquivo .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    $clientId     = $env['ML_CLIENT_ID'] ?? '';
    $clientSecret = $env['ML_CLIENT_SECRET'] ?? '';
} else {
    // Se o .env não existir (como no GitHub), as variáveis ficam vazias
    $clientId     = '';
    $clientSecret = '';
}

$tokenFile = 'tokens_config.json'; 

function obterAccessToken($clientId, $clientSecret, $tokenFile) {
    if (!file_exists($tokenFile)) return null;
    $config = json_decode(file_get_contents($tokenFile), true);
    return $config['access_token'] ?? null;
}

function renovarToken($clientId, $clientSecret, $tokenFile) {
    if (empty($clientId) || empty($clientSecret)) return null;

    $config = json_decode(file_get_contents($tokenFile), true);
    $refreshToken = $config['refresh_token'] ?? '';

    $url = "https://api.mercadolibre.com/oauth/token";
    $postData = [
        'grant_type'    => 'refresh_token',
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => $refreshToken
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($result['access_token'])) {
        $novosDados = [
            'access_token'  => $result['access_token'],
            'refresh_token' => $result['refresh_token']
        ];
        file_put_contents($tokenFile, json_encode($novosDados));
        return $result['access_token'];
    }
    return null;
}

function chamarAPI($url, $token) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false, 
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['body' => $res, 'code' => $code];
}