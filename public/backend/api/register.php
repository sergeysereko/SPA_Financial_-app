<?php
require_once __DIR__ . '/../../../backend/vendor/autoload.php';
require_once __DIR__ .'/../../../backend/src/Database.php';

use App\Database;

session_start();

//Если пользователь ужевошел, переброс на главную страницу
if(isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

//Проверка отправки формы
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');
    $confirm_password = trim(isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '');

    if(empty($username) || empty($email) || empty($password) || empty($confirm_password)){
        $error = 'Пожалуйста, заполните все поля.';
    }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Некорректный email';
    }elseif ($password !== $confirm_password){
        $error = 'Пароли не совападают';
    }elseif (strlen($password) < 6){
        $error = 'Пароль должен содержать не менее 6 символов';
    }else{
        try{
            //Подключение к базе данных
            $database = new Database();
            $pdo = $database->getPDO();

            //Проверка существует пользователь с таким email
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email'=> $email]);
            if($stmt->fetch()){
                $error = 'Пользовтаельс таким email уже зарегистрирован';
            }else{
                //Хеширование пароля
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                //Вставка нового пользовтаеля
                $stmt = $pdo->prepare('INSERT INTO users(username, email, password_hash) VALUES (:username, :email, :password_hash)');
                $stmt->execute([
                   'username' => $username,
                   'email' => $email,
                   'password_hash' => $hashed_password,
                ]);

                $success = 'Регистрация прошла успешно. Теперь вы можете войти';
                // Перенаправление на страницу входа
                header('Location: login.php');
                exit;
            }
        }catch (Exception $e){
            $error = 'Ошибка сервера:'. $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header>
    <h1>Регистрация</h1>
    <nav>
        <a href="/index.php">Главная</a>
        <a href="login.php">Вход</a>
    </nav>
</header>

<main>
    <div class="form-container">
    <h2>Создать учетную запись</h2>

        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Имя пользователя:</label>
            <input type="text" name="username" id="username" placeholder="Введите ваше имя" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Введите ваш email" required>

            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" placeholder="Введите ваш пароль" required>

            <label for="confirm_password">Подтвердите пароль:</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Введите пароль еще раз" required>

            <button type="submit">Зарегистрироваться</button>
        </form>
    </div>
</main>
<footer>
    &copy; 2024 Финансовое приложение
</footer>
</body>
</html>
