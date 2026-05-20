<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$input = getJsonInput();

$usuario = trim($input['usuario'] ?? '');
$nombreCliente = trim($input['nombre_cliente'] ?? '');
$telefono = trim($input['telefono'] ?? '');
$direccion = trim($input['direccion'] ?? '');
$metodoPago = trim($input['metodo_pago'] ?? '');
$productos = $input['productos'] ?? [];

if (empty($nombreCliente) || empty($telefono) || empty($direccion) || empty($productos)) {
    jsonResponse(["error" => "Datos incompletos"], 400);
}

$pdo = getDbConnection();

$total = 0;
foreach ($productos as $p) {
    $total += ($p['precio'] ?? 0) * ($p['cantidad'] ?? 0);
}

$fecha = date('Y-m-d');
$hora = date('H:i:s');

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "INSERT INTO pedidos (usuario, nombre_cliente, telefono, direccion, fecha, hora, metodo_pago, total, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente') RETURNING id"
    );
    $stmt->execute([$usuario, $nombreCliente, $telefono, $direccion, $fecha, $hora, $metodoPago, $total]);
    $pedido = $stmt->fetch();
    $pedidoId = $pedido['id'];

    $stmtDetalle = $pdo->prepare(
        "INSERT INTO detalle_pedidos (pedido_id, nombre, precio, cantidad) VALUES (?, ?, ?, ?)"
    );

    foreach ($productos as $p) {
        $stmtDetalle->execute([
            $pedidoId,
            $p['nombre'] ?? '',
            $p['precio'] ?? 0,
            $p['cantidad'] ?? 1,
        ]);
    }

    $pdo->commit();

    jsonResponse([
        "pedido_id" => (int)$pedidoId,
        "mensaje" => "Pedido creado exitosamente",
        "total" => (float)$total,
    ], 201);
} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(["error" => "Error al crear pedido: " . $e->getMessage()], 500);
}
