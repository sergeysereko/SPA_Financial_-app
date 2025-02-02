<?php

require_once __DIR__ . '/../../../backend/src/Database.php'; // Путь к классу Database
use App\Database;

if($_SERVER['REQUEST_METHOD']==='POST'){
    //Проверка сессии
    session_start();
    if(!isset($_SESSION['user_id'])){
        http_response_code(403);
        echo json_encode(['error' => 'Вы не авторизованы!']);
        exit;
    }
    //Подключение к базе данных

    $database = new Database();
    $pdo = $database->getPDO();

    //Получение и проверка идентификатора операции
    $input = json_decode(file_get_contents('php://input'), true);

    if(!isset($input['id']) || !is_numeric($input['id'])){
        http_response_code(400);
        echo json_encode(['error' => 'Некорректный идентификатор операции.']);
        exit;
    }
    $operationId = (int)$input['id'];

    try{
        $stmt = $pdo->prepare('DELETE FROM operations WHERE id = :id');
        $stmt->bindParam(':id', $operationId, PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount()>0){
            //Успешное удаление
            echo json_encode(['success'=>'Строка успешно удалена.']);
        }else{
            //Операция не найдена
            http_response_code(404);
            echo json_encode(['error'=>'Операция не найдена']);
        }
    }catch (Exception $e){
        http_response_code(500);
        echo json_encode(['error'=>'Ошибка на сервере: '.$e->getMessage()]);
    }
}else{
    // Метод запроса не POST
    http_response_code(405);
    echo json_encode(['error'=>'Неподдерживаемый метод запроса']);
}