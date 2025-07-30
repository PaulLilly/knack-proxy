<?php
// Allow any domain (or restrict to Knack domain if preferred)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Knack-Application-Id, X-Knack-REST-API-Key");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Preflight request, just return the headers
    http_response_code(200);
    exit();
}

$appId = '5973a02979b32254d3497b82';
$apiKey = '545804f0-745b-11e7-bac6-cd87cac67aaf';
$sceneId = 'scene_816';
$viewId = $_GET['view'] ?? '';
$body = file_get_contents('php://input');

$ch = curl_init("https://api.knack.com/v1/pages/{$sceneId}/views/{$viewId}/records");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Knack-Application-Id: $appId",
    "X-Knack-REST-API-Key: $apiKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
http_response_code(curl_getinfo($ch, CURLINFO_HTTP_CODE));
echo $response;
