<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$input = getJsonInput();
$correo = trim($input['correo'] ?? '');
$password = $input['password'] ?? '';

if (empty($correo) || empty($password)) {
    jsonResponse(["error" => "Correo y contraseña requeridos"], 400);
}

$pdo = getDbConnection();
$stmt = $pdo->prepare("SELECT id, nombre, correo, telefono, direccion, pass FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);
$user = $stmt->fetch();

if (!$user) {
    jsonResponse(["error" => "Credenciales inválidas"], 401);
}

if (!password_verify($password, $user['pass']) && $password !== $user['pass']) {
    jsonResponse(["error" => "Credenciales inválidas"], 401);
}

if (password_needs_rehash($user['pass'], PASSWORD_BCRYPT)) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE usuarios SET pass = ? WHERE id = ?");
    $stmt->execute([$hash, $user['id']]);
}

jsonResponse([
    "id" => (int)$user['id'],
    "nombre" => $user['nombre'],
    "correo" => $user['correo'],
    "telefono" => $user['telefono'] ?? '',
    "direccion" => $user['direccion'] ?? '',
]);
