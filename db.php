<?php

function getDbConnection(): PDO {
    $dsn = "pgsql:host=ep-blue-firefly-ajwhbjh6-pooler.c-3.us-east-2.aws.neon.tech;port=5432;dbname=neondb;sslmode=require";
    $username = "neondb_owner";
    $password = "npg_xs9OYRo8eltd";

    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
        exit;
    }
}

function jsonResponse(mixed $data, int $status = 200): void {
    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}

function getJsonInput(): array {
    $input = json_decode(file_get_contents("php://input"), true);
    return is_array($input) ? $input : [];
}
