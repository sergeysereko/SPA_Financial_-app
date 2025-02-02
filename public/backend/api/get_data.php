<?php
require_once __DIR__ . '/../../../backend/vendor/autoload.php';
require_once __DIR__ . '/../../../backend/src/Database.php'; // Путь к классу Database
use App\Database;

header('Content-Type: application/json');
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Подключение к базе данных
    $db = new Database();
    $pdo = $db->getPDO();

    // Получаем последние операции пользователя
    $stmt = $pdo->prepare("SELECT * FROM operations ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($operations);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}