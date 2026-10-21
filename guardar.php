<?php

require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

$codigo = trim($_POST['codigo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$precio = $_POST['precio'] ?? '';
$stock = $_POST['stock'] ?? '';

if (
    $codigo === '' ||
    $nombre === '' ||
    $categoria === '' ||
    !is_numeric($precio) ||
    !filter_var($stock, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) && $stock !== '0'
) {
    http_response_code(400);
    exit('Datos inválidos. Verifica los campos del producto.');
}

if ((float)$precio < 0) {
    http_response_code(400);
    exit('El precio no puede ser negativo.');
}

try {
    $conexion = conectarBD();

    $consulta = $conexion->prepare(
        'INSERT INTO productos
        (codigo, nombre, categoria, precio, stock)
        VALUES (:codigo, :nombre, :categoria, :precio, :stock)'
    );

    $consulta->execute([
        ':codigo' => $codigo,
        ':nombre' => $nombre,
        ':categoria' => $categoria,
        ':precio' => $precio,
        ':stock' => $stock
    ]);

    header('Location: index.php?mensaje=registrado');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    exit('No fue posible registrar el producto.');
}
