<?php
require_once 'auth.php'; 
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

$token = obterAccessToken($clientId, $clientSecret, $tokenFile);

$q = $_GET['q'] ?? '';
$cat = $_GET['cat'] ?? '';

if (!$q) { echo json_encode([]); exit; }

$url = "https://api.mercadolibre.com/products/search?site_id=MLB&q=" . urlencode($q) . "&limit=50";
if (!empty($cat)) { $url .= "&category=$cat"; }

$apiResult = chamarAPI($url, $token);

if ($apiResult['code'] == 401) {
    $token = renovarToken($clientId, $clientSecret, $tokenFile);
    if ($token) { $apiResult = chamarAPI($url, $token); }
}

$data = json_decode($apiResult['body'], true);
$out = [];
$contador = 0;

foreach ($data['results'] ?? [] as $p) {
    if ($contador >= 15) break;

    $dominio = strtoupper($p['domain_id'] ?? '');
    
    // 1. Filtro de Categoria (SÓ EXECUTA SE $cat NÃO FOR VAZIO)
    if (!empty($cat)) {
        $valido = false;
        switch ($cat) {
                    case 'MLB1648': if (preg_match('/(COMPUTER|KEYBOARD|MOUSE|NOTEBOOK|PRINTER|MONITOR)/', $dominio)) $valido = true; break;
                    case 'MLB1051': if (preg_match('/(CELLPHONE|SMARTPHONE|TABLET|WEARABLE)/', $dominio)) $valido = true; break;
                    case 'MLB1403': if (preg_match('/(FOOD|BEVERAGE|CANDY|WINE|COFFEE)/', $dominio)) $valido = true; break;
                    case 'MLB5672': if (preg_match('/(VEHICLE|CAR_ACCESSORY|TIRE|HELMET)/', $dominio)) $valido = true; break;
                    case 'MLB1039': if (preg_match('/(APPLIANCE|REFRIGERATOR|WASHING_MACHINE|STOVE|MICROWAVE)/', $dominio)) $valido = true; break;
                    case 'MLB1144': if (preg_match('/(VIDEO_GAME|CONSOLE|GAME_CONTROLLER)/', $dominio)) $valido = true; break;
                    case 'MLB1276': if (preg_match('/(SPORT|FOOTBALL|FITNESS|BICYCLE|TENT)/', $dominio)) $valido = true; break;
                    case 'MLB1430': if (preg_match('/(TOOL|DRILL|SAW|WELDING)/', $dominio)) $valido = true; break;
                    case 'MLB1574': if (preg_match('/(HOME|FURNITURE|MATTRESS|LIGHTING|DECORATION)/', $dominio)) $valido = true; break;
                    case 'MLB1117': if (preg_match('/(TOY|DOLL|ACTION_FIGURE|BOARD_GAME)/', $dominio)) $valido = true; break;
                    case 'MLB1246': if (preg_match('/(BEAUTY|HAIR|SKIN|PERFUME|MAKEUP)/', $dominio)) $valido = true; break;
                    case 'MLB1367': if (preg_match('/(CAMERA|LENS|TELESCOPE|DRONE)/', $dominio)) $valido = true; break;
                    
                    // ADICIONE ESTA LINHA PARA VEÍCULOS (MLB1743):
                    case 'MLB1743': if (preg_match('/(CAR|VEHICLE|PICKUP|TRUCK|MOTORCYCLE)/', $dominio)) $valido = true; break;
                    
                    default: $valido = true;
                }
        if (!$valido) continue; 
    }

    // 2. BUSCA DE IMAGEM
    $imgFinal = $p['pictures'][0]['url'] ?? $p['thumbnail'] ?? $p['buy_box_winner']['thumbnail'] ?? '';

    if (empty($imgFinal)) {
        continue;
    }

    $idLimpo = $p['id'] ?? $p['catalog_product_id'] ?? '';
    $tituloLimpo = $p['name'] ?? $p['title'] ?? 'Produto';

    if (!empty($idLimpo)) {
        $out[] = [
            "id"    => (string)$idLimpo,
            "title" => (string)$tituloLimpo,
            "img"   => str_replace("-I.jpg", "-O.jpg", $imgFinal),
            "price" => "Análise de Sortimento"
        ];
        $contador++;
    }
}

echo json_encode($out);