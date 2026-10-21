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

if (!$id) {
    http_response_code(400);
    exit('Identificador de producto inválido.');
}

try {
    $conexion = conectarBD();

    $consulta = $conexion->prepare(
        'DELETE FROM productos WHERE id = :id'
    );

    $consulta->execute([
        ':id' => $id
    ]);

    header('Location: index.php?mensaje=eliminado');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    exit('No fue posible eliminar el producto.');
}
