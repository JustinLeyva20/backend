<?php
header("Content-Type: application/json");

$response = [
    "php_version" => phpversion(),
    "status" => "OK",
];

try {
    require_once __DIR__ . '/db.php';
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM config");
    $config = $stmt->fetch();
    $response["db"] = "Conectado";
    $response["config_count"] = $config['total'];
} catch (Exception $e) {
    $response["db"] = "ERROR: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
