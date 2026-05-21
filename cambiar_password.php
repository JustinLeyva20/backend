<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$input = getJsonInput();
$correo = trim($input['correo'] ?? '');
$passwordActual = $input['password_actual'] ?? '';
$passwordNueva = $input['password_nueva'] ?? '';

if (empty($correo) || empty($passwordActual) || empty($passwordNueva)) {
    jsonResponse(["error" => "Todos los campos son requeridos"], 400);
}

if (strlen($passwordNueva) < 6) {
    jsonResponse(["error" => "La nueva contraseña debe tener al menos 6 caracteres"], 400);
}

$pdo = getDbConnection();
$stmt = $pdo->prepare("SELECT pass FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);
$user = $stmt->fetch();

if (!$user) {
    jsonResponse(["error" => "Usuario no encontrado"], 404);
}

if (!password_verify($passwordActual, $user['pass'])) {
    jsonResponse(["error" => "La contraseña actual es incorrecta"], 401);
}

$hash = password_hash($passwordNueva, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE usuarios SET pass = ? WHERE correo = ?");
$stmt->execute([$hash, $correo]);

jsonResponse(["mensaje" => "Contraseña actualizada correctamente"]);
