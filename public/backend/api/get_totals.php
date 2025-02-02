<?php

if (session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__.'/../../../backend/vendor/autoload.php';
require_once __DIR__.'/../../../backend/src/Database.php';

use App\Database;

//Проверка авторизации
if(!isset($_SESSION['user_id'])){
    echo json_encode(['error' =>'Unauthorized']);
    exit;
}
//Подключение к базе данных
$database = new Database();
$pdo = $database->getPDO();
// Подсчет расходов
$expensesQuery = $pdo->query("SELECT SUM(amount) as total_expenses FROM operations WHERE type = 'expense'");
$totalExpenses = $expensesQuery->fetch()['total_expenses'] ?? 0;

// Подсчет доходов
$incomeQuery = $pdo->query("SELECT SUM(amount) as total_income FROM operations WHERE type = 'income'");
$totalIncome = $incomeQuery->fetch()['total_income'] ?? 0;

// Возврат данных в формате JSON
echo json_encode([
    'totalExpenses' => $totalExpenses,
    'totalIncome' => $totalIncome,
//    'totall'=> $totalBalance,
]);
exit;