<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$input = getJsonInput();
$nombre = trim($input['nombre'] ?? '');
$correo = trim($input['correo'] ?? '');
$password = $input['password'] ?? '';
$telefono = trim($input['telefono'] ?? '');
$direccion = trim($input['direccion'] ?? '');

if (empty($nombre) || empty($correo) || empty($password)) {
    jsonResponse(["error" => "Nombre, correo y contraseña requeridos"], 400);
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(["error" => "Correo inválido"], 400);
}

$pdo = getDbConnection();

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);
if ($stmt->fetch()) {
    jsonResponse(["error" => "El correo ya está registrado"], 409);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare(
    "INSERT INTO usuarios (nombre, correo, password, telefono, direccion) VALUES (?, ?, ?, ?, ?) RETURNING id, nombre, correo, telefono, direccion"
);
$stmt->execute([$nombre, $correo, $hash, $telefono, $direccion]);
$user = $stmt->fetch();

jsonResponse([
    "id" => (int)$user['id'],
    "nombre" => $user['nombre'],
    "correo" => $user['correo'],
    "telefono" => $user['telefono'],
    "direccion" => $user['direccion'],
], 201);
