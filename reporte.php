<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

$pdo = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $correo = trim($_GET['correo'] ?? '');
    if (empty($correo)) {
        jsonResponse(["error" => "Parámetro correo requerido"], 400);
    }
    $stmt = $pdo->prepare("SELECT id, asunto, descripcion, created_at FROM reportes WHERE correo = ? ORDER BY created_at DESC");
    $stmt->execute([$correo]);
    $reportes = $stmt->fetchAll();
    jsonResponse($reportes);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $correo = trim($input['correo'] ?? '');
    $asunto = trim($input['asunto'] ?? '');
    $descripcion = trim($input['descripcion'] ?? '');

    if (empty($correo) || empty($asunto) || empty($descripcion)) {
        jsonResponse(["error" => "Todos los campos son requeridos"], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO reportes (correo, asunto, descripcion) VALUES (?, ?, ?)");
    $stmt->execute([$correo, $asunto, $descripcion]);

    jsonResponse(["mensaje" => "Reporte enviado correctamente"]);
}

jsonResponse(["error" => "Método no permitido"], 405);
