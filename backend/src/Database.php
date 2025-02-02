<?php
namespace App;

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Database
{
    private $pdo;



    public function __construct()
    {

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // Получение параметров из переменных окружения
        $host = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost';
        $dbname = isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'test';
        $username = isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root';
        $password = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '';
        $charset = 'utf8mb4';


        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        // Опции для PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Подключение к базе данных
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            // Вывод ошибки при неудачном подключении
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }


    public function getPDO()
    {
        return $this->pdo;
    }
}