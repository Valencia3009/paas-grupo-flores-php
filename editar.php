
<?php

require_once __DIR__ . '/conexion.php';

function escapar($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

$id = filter_input(
    INPUT_GET,
    'id',
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
        'SELECT id, codigo, nombre, categoria, precio, stock
         FROM productos
         WHERE id = :id'
    );

    $consulta->execute([':id' => $id]);
    $producto = $consulta->fetch();

    if (!$producto) {
        http_response_code(404);
        exit('Producto no encontrado.');
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No fue posible consultar el producto.');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto - Grupo Flores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            color: #263445;
        }

        header {
            background: #17365d;
            color: white;
            padding: 25px;
            text-align: center;
        }

        main {
            max-width: 650px;
            margin: 35px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 15px 0 6px;
        }

        input {
            width: 100%;
            padding: 11px;
            box-sizing: border-box;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
        }

        button {
            background: #17365d;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        a {
            display: inline-block;
            margin-left: 15px;
            color: #17365d;
        }
    </style>
</head>

<body>

<header>
    <h1>Grupo Flores</h1>
    <p>Editar producto del inventario</p>
</header>

<main>
    <h2>Modificar producto</h2>

    <form action="actualizar.php" method="POST">
        <input
            type="hidden"
            name="id"
            value="<?= escapar($producto['id']) ?>">

        <label>Código</label>
        <input
            type="text"
            name="codigo"
            maxlength="30"
            value="<?= escapar($producto['codigo']) ?>"
            required>

        <label>Nombre del producto</label>
        <input
            type="text"
            name="nombre"
            maxlength="150"
            value="<?= escapar($producto['nombre']) ?>"
            required>

        <label>Categoría</label>
        <input
            type="text"
            name="categoria"
            maxlength="100"
            value="<?= escapar($producto['categoria']) ?>"
            required>

        <label>Precio ($)</label>
        <input
            type="number"
            name="precio"
            min="0"
            step="0.01"
            value="<?= escapar($producto['precio']) ?>"
            required>

        <label>Existencias</label>
        <input
            type="number"
            name="stock"
            min="0"
            step="1"
            value="<?= escapar($producto['stock']) ?>"
            required>

        <button type="submit">Guardar cambios</button>
        <a href="index.php">Cancelar</a>
    </form>
</main>

</body>
</html>
