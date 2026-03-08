<?php
// save.php — Royal Noir JSON persistence layer
// Place this file in the same directory as admin.html and casino.html

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$allowed = ['players', 'casino'];

$file = isset($_GET['file']) ? $_GET['file'] : '';

if (!in_array($file, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Archivo no permitido']);
    exit;
}

$path = __DIR__ . '/data/' . $file . '.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!file_exists($path)) {
        // Return empty defaults
        if ($file === 'players') echo json_encode(['players' => []]);
        elseif ($file === 'casino') echo json_encode(['admin' => ['username' => 'admin', 'password' => 'admin123'], 'chipLog' => []]);
        exit;
    }
    echo file_get_contents($path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $decoded = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'JSON inválido']);
        exit;
    }
    // Ensure data directory exists
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }
    file_put_contents($path, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
