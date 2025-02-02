<?php
require_once __DIR__ . '/../../../backend/vendor/autoload.php';
require_once __DIR__ . '/../../../backend/src/Database.php'; // Путь к классу Database
use App\Database;
use App\Api\Operations;


session_start();

if(!isset($_SESSION['user_id'])){
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
// Получение данных из JSON
$data = json_decode(file_get_contents("php://input"),true);

if (!isset($data['amount'], $data['type'], $data['comment'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input data']);
    exit;
}


try {
    //  подключения к базе данных
    $db = new Database();
    $operations = new Operations($db);

    // Добавление операции
    $result = $operations->add($_SESSION['user_id'], $data['amount'], $data['type'], $data['comment']);
    echo json_encode(['success' => $result]);
} catch (Exception $e) {
    // Обработка ошибок с кодом 500
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}