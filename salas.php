<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$pdo = getDbConnection();

$salas = $pdo->query("SELECT id, nombre, mesas FROM salas ORDER BY id ASC")->fetchAll();

$salas = array_map(function ($s) {
    return [
        "id" => (int)$s['id'],
        "nombre" => $s['nombre'],
        "mesas" => (int)$s['mesas'],
    ];
}, $salas);

jsonResponse($salas);
