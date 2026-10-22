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

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    // TLS para conexiones externas, como TiDB Cloud.
    // La conexion local de Kubernetes permanece sin cambios.
    if (getenv('DB_SSL') === 'true') {
        $rutaCA = getenv('DB_SSL_CA')
            ?: '/etc/ssl/certs/ca-certificates.crt';

        if (!is_readable($rutaCA)) {
            throw new RuntimeException(
                'No se encuentra el certificado CA para la conexion TLS.'
            );
        }

        $opciones[PDO::MYSQL_ATTR_SSL_CA] = $rutaCA;
        $opciones[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }

    return new PDO(
        $dsn,
        $usuario,
        $contrasena,
        $opciones
    );
}
