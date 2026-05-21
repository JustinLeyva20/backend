<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

$pdo = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $correo = trim($_GET['correo'] ?? '');

    if (empty($correo)) {
        jsonResponse(["error" => "Parámetro correo requerido"], 400);
    }

    $stmt = $pdo->prepare("SELECT id, nombre, correo, telefono, direccion, created_at FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(["error" => "Usuario no encontrado"], 404);
    }

    jsonResponse([
        "id" => (int)$user['id'],
        "nombre" => $user['nombre'],
        "correo" => $user['correo'],
        "telefono" => $user['telefono'] ?? '',
        "direccion" => $user['direccion'] ?? '',
        "fecha_registro" => $user['created_at'] ?? '',
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = getJsonInput();
    $correo = trim($input['correo'] ?? '');

    if (empty($correo)) {
        jsonResponse(["error" => "Correo requerido"], 400);
    }

    $fields = [];
    $params = [];

    if (!empty($input['nombre'])) {
        $fields[] = "nombre = ?";
        $params[] = trim($input['nombre']);
    }
    if (isset($input['telefono'])) {
        $fields[] = "telefono = ?";
        $params[] = trim($input['telefono']);
    }
    if (isset($input['direccion'])) {
        $fields[] = "direccion = ?";
        $params[] = trim($input['direccion']);
    }
    if (!empty($input['password'])) {
        $fields[] = "pass = ?";
        $params[] = password_hash($input['password'], PASSWORD_BCRYPT);
    }

    if (empty($fields)) {
        jsonResponse(["error" => "No hay campos para actualizar"], 400);
    }

    $params[] = $correo;
    $sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE correo = ? RETURNING id, nombre, correo, telefono, direccion, created_at";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(["error" => "Usuario no encontrado"], 404);
    }

    jsonResponse([
        "id" => (int)$user['id'],
        "nombre" => $user['nombre'],
        "correo" => $user['correo'],
        "telefono" => $user['telefono'] ?? '',
        "direccion" => $user['direccion'] ?? '',
        "fecha_registro" => $user['created_at'] ?? '',
    ]);
}

jsonResponse(["error" => "Método no permitido"], 405);
