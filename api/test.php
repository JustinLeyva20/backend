<?php
header("Content-Type: application/json");

$response = [
    "php_version" => phpversion(),
    "extensions" => get_loaded_extensions(),
    "has_pgsql" => extension_loaded("pgsql"),
    "has_pdo_pgsql" => extension_loaded("pdo_pgsql"),
    "has_pdo" => extension_loaded("pdo"),
];

try {
    $dsn = "pgsql:host=ep-blue-firefly-ajwhbjh6-pooler.c-3.us-east-2.aws.neon.tech;port=5432;dbname=neondb;sslmode=require";
    $pdo = new PDO($dsn, "neondb_owner", "npg_xs9OYRo8eltd", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $response["db_connection"] = "OK";
} catch (Exception $e) {
    $response["db_connection"] = "ERROR: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
