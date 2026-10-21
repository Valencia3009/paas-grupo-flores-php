<?php

function conectarBD(): PDO
{
    $host = getenv('DB_HOST');
    $puerto = getenv('DB_PORT') ?: '3306';
    $baseDatos = getenv('DB_NAME');
    $usuario = getenv('DB_USER');
    $contrasena = getenv('DB_PASSWORD');

    if (!$host || !$baseDatos || !$usuario || $contrasena === false) {
        throw new RuntimeException(
            'Faltan variables de entorno para la base de datos.'
        );
    }

    $dsn = "mysql:host={$host};port={$puerto};dbname={$baseDatos};charset=utf8mb4";

    return new PDO(
        $dsn,
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
}
