<?php

require_once __DIR__ . '/../../../backend/vendor/autoload.php';
require_once __DIR__ . '/../../../backend/src/Database.php';

use App\Database;

session_start();
// проверяем вошел пользоатель уже в систему
if(isset($_SESSION['user_id'])){
    header('Location: /index.php');
    exit;
}
$error = '';

// Проверка отправки формы
if($_SERVER['REQUEST_METHOD']=== 'POST'){
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');

    if(empty($email) || empty($password)){
        $error = 'Пожалуйста, заполните все поля.';
    }else{
        try{
            //Подключение к базе данных
            $database = new Database();
            $pdo = $database->getPDO();
            //поиск пользователя по email
            $smtp = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $smtp->execute(['email'=>$email]);
            $user = $smtp->fetch();

            if($user && password_verify($password, $user['password_hash'])){
                // если успешный вход - установка сессии
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /index.php');
                exit;
            }else{
                $error = 'Неверный email или пароль. ';
            }
        }catch (Exception $e){
            $error = 'Ошибка сервера: '.$e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header>
    <h1>Вход в систему</h1>
    <nav>
        <a href="/index.php">Главная</a>
        <a href="register.php">Регистрация</a>
    </nav>
</header>

<main>
    <div>
        <h2>Авторизация</h2>
    <?php if (!empty($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Введите ваш email" required>
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" placeholder="Введите ваш пароль" required>

        <button type="submit">Войти</button>
    </form>
    </div>
</main>
<footer>
    <p>&copy; 2024 Финансовое приложение</p>
</footer>
</body>
</html>

