<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$pdo = getDbConnection();
$stmt = $pdo->prepare("SELECT id, nombre, precio, imagen, badge, fecha FROM menu_items WHERE categoria = 'plato' ORDER BY id");
$stmt->execute();
$items = $stmt->fetchAll();

$items = array_map(function ($item) {
    return [
        "id" => (int)$item['id'],
        "nombre" => $item['nombre'],
        "precio" => (float)$item['precio'],
        "imagen" => $item['imagen'],
        "badge" => $item['badge'],
        "fecha" => $item['fecha'],
    ];
}, $items);

jsonResponse($items);
