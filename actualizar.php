<?php

require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$codigo = trim($_POST['codigo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$precio = trim((string) ($_POST['precio'] ?? ''));
$stock = trim((string) ($_POST['stock'] ?? ''));

if (
    !$id ||
    $codigo === '' ||
    $nombre === '' ||
    $categoria === '' ||
    strlen($codigo) > 30 ||
    strlen($nombre) > 150 ||
    strlen($categoria) > 100 ||
    !is_numeric($precio) ||
    (float) $precio < 0 ||
    (float) $precio > 99999999.99 ||
    !preg_match('/^\d+$/', $stock) ||
    (float) $stock > 2147483647
) {
    http_response_code(400);
    exit('Datos inválidos. Revisa la información del producto.');
}

try {
    $conexion = conectarBD();

    $consulta = $conexion->prepare(
        'UPDATE productos
         SET codigo = :codigo,
             nombre = :nombre,
             categoria = :categoria,
             precio = :precio,
             stock = :stock
         WHERE id = :id'
    );

    $consulta->execute([
        ':id' => $id,
        ':codigo' => $codigo,
        ':nombre' => $nombre,
        ':categoria' => $categoria,
        ':precio' => $precio,
        ':stock' => (int) $stock
    ]);

    header('Location: index.php?mensaje=actualizado');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    exit('No fue posible actualizar el producto.');
}
