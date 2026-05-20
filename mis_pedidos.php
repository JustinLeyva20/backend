<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$usuario = trim($_GET['usuario'] ?? '');

if (empty($usuario)) {
    jsonResponse(["error" => "Parámetro usuario requerido"], 400);
}

$pdo = getDbConnection();

$stmt = $pdo->prepare(
    "SELECT id, nombre_cliente, telefono, direccion, fecha, hora, metodo_pago, total, estado
     FROM pedidos WHERE usuario = ? ORDER BY id DESC"
);
$stmt->execute([$usuario]);
$pedidos = $stmt->fetchAll();

$result = [];
foreach ($pedidos as $pedido) {
    $stmtDetalle = $pdo->prepare(
        "SELECT nombre, precio, cantidad FROM detalle_pedidos WHERE pedido_id = ?"
    );
    $stmtDetalle->execute([$pedido['id']]);
    $detalles = $stmtDetalle->fetchAll();

    $productos = array_map(function ($d) {
        return [
            "nombre" => $d['nombre'],
            "precio" => (float)$d['precio'],
            "cantidad" => (int)$d['cantidad'],
        ];
    }, $detalles);

    $result[] = [
        "id" => (int)$pedido['id'],
        "nombre_cliente" => $pedido['nombre_cliente'],
        "telefono" => $pedido['telefono'],
        "direccion" => $pedido['direccion'],
        "fecha" => $pedido['fecha'],
        "hora" => $pedido['hora'],
        "metodo_pago" => $pedido['metodo_pago'],
        "total" => (float)$pedido['total'],
        "estado" => $pedido['estado'],
        "productos" => $productos,
    ];
}

jsonResponse($result);
