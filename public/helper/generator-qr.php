<?php
/**
 * Generador de códigos QR para carnets
 * Genera QR que apunta a una URL con ID encriptado del usuario
 */

// Función para encriptar el ID del usuario
function encryptUserId($userId) {
    $key = 'carnet_secret_key_2024'; // Clave secreta (cambiar en producción)
    $method = 'AES-256-CBC';
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($userId, $method, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Función para desencriptar el ID del usuario
function decryptUserId($encryptedId) {
    $key = 'carnet_secret_key_2024'; // Misma clave secreta
    $method = 'AES-256-CBC';
    $data = base64_decode($encryptedId);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, $method, $key, 0, $iv);
}

// Función para generar QR usando una librería externa (QR Server API)
function generateQRCode($userId, $baseUrl = null) {
    try {
        // Usar la URL base actual si no se proporciona
        if (!$baseUrl) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $baseUrl = $protocol . '://' . $host;
        }
        
        // Encriptar el ID del usuario
        $encryptedId = encryptUserId($userId);
        
        // Crear la URL del carnet (usando ruta Laravel)
        $carnetUrl = $baseUrl . '/carnet-digital/' . urlencode($encryptedId);
        
        // Generar QR usando QR Server API (servicio gratuito)
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        $qrParams = http_build_query([
            'size' => '200x200',
            'data' => $carnetUrl,
            'format' => 'png',
            'ecc' => 'M', // Error correction level
            'margin' => 10
        ]);
        
        $qrImageUrl = $qrApiUrl . '?' . $qrParams;
        
        return [
            'success' => true,
            'qr_url' => $qrImageUrl,
            'carnet_url' => $carnetUrl,
            'encrypted_id' => $encryptedId
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error generando QR: ' . $e->getMessage()
        ];
    }
}

// Función para generar QR como imagen base64
function generateQRBase64($userId, $baseUrl = null) {
    try {
        $qrData = generateQRCode($userId, $baseUrl);
        
        if (!$qrData['success']) {
            return $qrData;
        }
        
        // Obtener la imagen del QR
        $qrImageData = file_get_contents($qrData['qr_url']);
        
        if ($qrImageData === false) {
            throw new Exception('No se pudo obtener la imagen del QR');
        }
        
        // Convertir a base64
        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImageData);
        
        return [
            'success' => true,
            'qr_base64' => $qrBase64,
            'carnet_url' => $qrData['carnet_url'],
            'encrypted_id' => $qrData['encrypted_id']
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error generando QR base64: ' . $e->getMessage()
        ];
    }
}

// Si se llama directamente via AJAX (no cuando se incluye desde otro archivo)
if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') && 
    basename($_SERVER['SCRIPT_NAME']) === 'generator-qr.php') {
    header('Content-Type: application/json');
    
    $userId = $_POST['user_id'] ?? $_GET['user_id'] ?? null;
    $baseUrl = $_POST['base_url'] ?? $_GET['base_url'] ?? null;
    
    if (!$userId) {
        echo json_encode([
            'success' => false,
            'error' => 'ID de usuario requerido'
        ]);
        exit;
    }
    
    $result = generateQRBase64($userId, $baseUrl);
    echo json_encode($result);
    exit;
}
?>