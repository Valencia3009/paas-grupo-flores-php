
<?php

require_once __DIR__ . '/conexion.php';

$productos = [];
$error = '';

try {
    $conexion = conectarBD();

    $consulta = $conexion->query(
        'SELECT id, codigo, nombre, categoria, precio, stock
         FROM productos
         ORDER BY id DESC'
    );

    $productos = $consulta->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'No fue posible consultar los productos.';
}

function escapar($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

$registrado = ($_GET['mensaje'] ?? '') === 'registrado';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventario - Grupo Flores</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #263445;
        }

        header {
            background: #17365d;
            color: white;
            padding: 25px;
            text-align: center;
        }

        header h1 {
            margin: 0 0 8px;
        }

        main {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 18px;
        }

        .panel {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000012;
        }

        h2 {
            margin-top: 0;
            color: #17365d;
        }

        .formulario {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
        }

        button {
            background: #17365d;
            border: none;
            padding: 12px 24px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #244f81;
        }

        .acciones {
            margin-top: 20px;
        }

        .mensaje {
            background: #d9f5df;
            color: #17632a;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #eaf0f7;
        }

        @media (max-width: 650px) {
            .formulario {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Grupo Flores</h1>
    <p>Sistema de Gestión de Inventario</p>
</header>

<main>

    <?php if ($registrado): ?>
        <div class="mensaje">
            Producto registrado correctamente.
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h2>Registrar nuevo producto</h2>

        <form action="guardar.php" method="POST">

            <div class="formulario">

                <div>
                    <label for="codigo">Código</label>
                    <input
                        type="text"
                        id="codigo"
                        name="codigo"
                        maxlength="30"
                        required>
                </div>

                <div>
                    <label for="nombre">Nombre del producto</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        maxlength="150"
                        required>
                </div>

                <div>
                    <label for="categoria">Categoría</label>
                    <input
                        type="text"
                        id="categoria"
                        name="categoria"
                        maxlength="100"
                        required>
                </div>

                <div>
                    <label for="precio">Precio ($)</label>
                    <input
                        type="number"
                        id="precio"
                        name="precio"
                        step="0.01"
                        min="0"
                        required>
                </div>

                <div>
                    <label for="stock">Existencias</label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        min="0"
                        step="1"
                        required>
                </div>

            </div>

            <div class="acciones">
                <button type="submit">
                    Guardar producto
                </button>
            </div>

        </form>
    </section>

    <section class="panel">
        <h2>Productos registrados</h2>

        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Existencias</th>
			<th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= escapar($producto['codigo']) ?></td>
                            <td><?= escapar($producto['nombre']) ?></td>
                            <td><?= escapar($producto['categoria']) ?></td>
                            <td>$<?= number_format((float) $producto['precio'], 2) ?></td>
                            <td><?= escapar($producto['stock']) ?></td>
                            <td>
				<a href="editar.php?id=<?= (int) $producto['id'] ?>">
				        Editar
				    </a>

				    <form
				        action="eliminar.php"
				        method="POST"
				        style="display:inline;"
				        onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');"
				    >
				        <input
				            type="hidden"
				            name="id"
				            value="<?= (int) $producto['id'] ?>"
				        >

				        <button
				            type="submit"
				            style="background:#b91c1c; margin-left:10px;"
				        >
				            Eliminar
				        </button>
				    </form>
				</td>

                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="6">
                                No hay productos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

</body>
</html>
