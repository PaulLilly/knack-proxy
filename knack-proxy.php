<?php
// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Knack-Application-Id, X-Knack-REST-API-Key");
header("Content-Type: application/json");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Reject anything that's not GET/OPTIONS
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed (GET only)']);
    exit();
}

// ---- CONFIG ----
$appId   = '5973a02979b32254d3497b82';
$apiKey  = '545804f0-745b-11e7-bac6-cd87cac67aaf';
$sceneId = 'scene_816'; // <-- confirm this is correct for your grid's scene

// ---- INPUT ----
$viewId   = isset($_GET['view']) ? $_GET['view'] : '';
$filters  = isset($_GET['filters']) ? $_GET['filters'] : '';
$perPage  = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 1000;
$page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if (!$viewId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing view parameter']);
    exit();
}

// Build Knack URL
$query = [
    'rows_per_page' => $perPage,
    'page'          => $page
];
if ($filters !== '') {
    $query['filters'] = $filters; // already JSON-encoded by the client, we’ll pass it through
}

$knackUrl = 'https://api.knack.com/v1/pages/' . rawurlencode($sceneId) .
            '/views/' . rawurlencode($viewId) . '/records?' . http_build_query($query);

// Curl GET
$ch = curl_init($knackUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Knack-Application-Id: $appId",
    "X-Knack-REST-API-Key: $apiKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Pass through status & body
http_response_code($httpCode ?: 500);
echo $response ?: json_encode(['error' => 'No response from Knack']);
