<?php
/**
 * Redirección de URLs antiguas de visitantes
 * 
 * Este archivo actúa como middleware para redirigir las URLs antiguas
 * del formato: https://siscarnetizacion.mincyt.gob.ve/visitantes.php?id=89
 * Al nuevo formato: https://siscarnetizacion.mincyt.gob.ve/v/{secure_token}
 * 
 * El secure_token se obtiene de la tabla carnet_visitantes usando el id proporcionado.
 */

// Cargar el autoloader de Laravel
require __DIR__.'/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

// Obtener el ID del carnet desde la URL
$carnetId = isset($_GET['id']) ? $_GET['id'] : null;

// Validar que el ID sea numérico
if (!$carnetId || !is_numeric($carnetId)) {
    http_response_code(400);
    echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlace Inválido - Sistema de Carnetización</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); text-align: center; max-width: 400px; }
        h1 { color: #dc3545; margin-bottom: 20px; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enlace Inválido</h1>
        <p>El enlace utilizado no es válido. Por favor, escanee el código QR del carnet.</p>
    </div>
</body>
</html>';
    exit;
}

// Obtener configuración de base de datos desde .env
$dbConnection = $_ENV['DB_CONNECTION'] ?? 'pgsql';
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '5432';
$database = $_ENV['DB_DATABASE'] ?? 'carnetizacion';
$username = $_ENV['DB_USERNAME'] ?? 'postgres';
$password = $_ENV['DB_PASSWORD'] ?? '';

try {
    // Conectar a PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$database";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar el secure_token del carnet
    $stmt = $pdo->prepare("SELECT id, secure_token FROM carnet_visitantes WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$carnetId]);
    $carnet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$carnet) {
        // El carnet no existe
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet No Encontrado - Sistema de Carnetización</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); text-align: center; max-width: 400px; }
        h1 { color: #dc3545; margin-bottom: 20px; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Carnet No Encontrado</h1>
        <p>Este carnet no está registrado en el sistema o ha sido desactivado.</p>
    </div>
</body>
</html>';
        exit;
    }
    
    $secureToken = $carnet['secure_token'];
    
    // Si no tiene token, generar uno nuevo
    if (empty($secureToken)) {
        $appKey = $_ENV['APP_KEY'] ?? '';
        $secretKey = $appKey . 'carnet_visitante_salt';
        $fixedSalt = 'carnet_permanent_2025_16char';
        $hash = hash('sha256', $carnetId . $secretKey . $fixedSalt);
        $secureToken = strtoupper(substr($hash, 0, 16));
        
        // Actualizar el carnet con el nuevo token
        $stmtUpdate = $pdo->prepare("UPDATE carnet_visitantes SET secure_token = ? WHERE id = ?");
        $stmtUpdate->execute([$secureToken, (int)$carnetId]);
    }
    
    // Construir la URL de redirección
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $redirectUrl = $protocol . '://' . $host . '/v/' . $secureToken;
    
    // Realizar redirección 301 (permanente)
    header("Location: $redirectUrl", true, 301);
    exit;
    
} catch (PDOException $e) {
    // Error de conexión a base de datos
    error_log("Error en visitantes.php: " . $e->getMessage());
    http_response_code(500);
    echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Sistema - Sistema de Carnetización</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); text-align: center; max-width: 400px; }
        h1 { color: #dc3545; margin-bottom: 20px; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Error del Sistema</h1>
        <p>Ha ocurrido un error al procesar el carnet. Por favor, contacte al personal de seguridad.</p>
    </div>
</body>
</html>';
    exit;
}
