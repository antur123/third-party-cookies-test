<?php

// Enable CORS
header('Access-Control-Allow-Origin: ' . getOrigin());
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Credentials: true');

// Get user id
if (!isset($_COOKIE["__tracker_id__"])) {
    // If not sent in cookie, create new id
    $userId = generateUserId();

    // Generate new cookie with value for next requests
    setcookie("__tracker_id__", $userId);
} else {
    // Retrieve from cookie otherwise
    $userId = $_COOKIE["__tracker_id__"];
}

// Get source url
$sourceUrl = $_SERVER["HTTP_REFERER"];

// Read db (JSON file)
$json = file_get_contents(__DIR__ . "/data.json");
$data = json_decode($json, true);

// if (!isset($data[$userId])) {
//     $data[$userId] = [];
// }

// Track user
$data[$userId][] = [
    "date" => (new DateTime())->format("Y-m-d H:i:s e"),
    "url" => $sourceUrl
];

// Write db (JSON file)
$json = json_encode($data, JSON_PRETTY_PRINT);
file_put_contents(__DIR__ . "/data.json", $json);

// === Aux functions ===
function generateUserId($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getOrigin() {
    $url = $_SERVER["HTTP_REFERER"];
    $parsed = parse_url($url);
    $origin = $parsed["scheme"] . "://" . $parsed["host"];
    if (isset($parsed["port"])) {
        $origin .= ":" . $parsed["port"];
    }
    return $origin;
}