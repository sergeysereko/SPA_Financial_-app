<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ .'/../backend/vendor/autoload.php';
require_once __DIR__.'/../backend/src/Database.php';

use App\Database;

// Проверяем, залогинился ли пользователь
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : null;

// Получение данных с сервера
$database = new Database();
$pdo = $database->getPDO();


$query = $pdo->query("SELECT * FROM operations ORDER BY created_at DESC LIMIT 10");
$operations = $query->fetchAll();


// Суммируем расходы
$expensesQuery = $pdo->query("SELECT SUM(amount) as total_expenses FROM operations WHERE type = 'expense'");
$totalExpenses = $expensesQuery->fetch()['total_expenses'] ?? 0;

// Суммируем приходы
$incomeQuery = $pdo->query("SELECT SUM(amount) as total_income FROM operations WHERE type = 'income'");
$totalIncome = $incomeQuery->fetch()['total_income'] ?? 0;

// Рассчитываем итоговую сумму
$totalBalance = $totalIncome - $totalExpenses;

// Включение HTML-шаблона
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Финансовое приложение</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Финансовое приложение</h1>
    <nav>
        <?php if ($isLoggedIn): ?>
            <span>Добро пожаловать, <?php echo $username; ?>!</span>
            <a href="/backend/api/logout.php">Выход</a>
        <?php else: ?>
            <a href="/backend/api/login.php">Вход</a>
            <a href="/backend/api/register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <!-- Блок добавления операции -->
    <section class="add-operation">
        <h2>Добавить операцию</h2>
        <form id="addOperationForm">
            <div class="form-group">
                <label for="amount">Сумма:</label>
                <input type="number" name="amount" id="amount" placeholder="Введите сумму" required <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
            </div>
            <div class="form-group">
                <label for="type">Тип:</label>
                <select name="type" id="type" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                    <option value="expense">Расход</option>
                    <option value="income">Приход</option>
                </select>
            </div>
            <div class="form-group">
                <label for="comment">Комментарий:</label>
                <input type="text" name="comment" id="comment" placeholder="Введите комментарий" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
            </div>
            <button type="submit" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>Добавить</button>
        </form>
        <?php if (!$isLoggedIn): ?>
            <p class="info-message">Авторизуйтесь, чтобы добавить операцию.</p>
        <?php endif; ?>
    </section>

    <!-- Блок последних операций -->
    <section class="recent-operations">
        <h2>Последние операции</h2>
        <table id="operationsTable">
            <thead>
            <tr>
                <th>Сумма</th>
                <th>Тип</th>
                <th>Комментарий</th>
                <th>Дата</th>
                <th>Удалить</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($operations as $operation): ?>
                <tr>
                    <td><?php echo htmlspecialchars($operation['amount']); ?></td>
                    <td><?php echo $operation['type'] === 'expense' ? 'Расход' : 'Приход'; ?></td>
                    <td><?php echo htmlspecialchars($operation['comment']); ?></td>
                    <td><?php echo $operation['created_at']; ?></td>
                    <td>
                        <button class="delete-btn" data-id="<?php echo $operation['id']; ?>" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>Удалить</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (!$isLoggedIn): ?>
            <p class="info-message">Авторизуйтесь, чтобы удалять операции.</p>
        <?php endif; ?>
    </section>

    <!-- Блок итогов -->
    <section class="summary">
        <h2>Итоги за день</h2>
        <p>Сумма всех расходов: <span id="totalExpenses"><?php echo htmlspecialchars($totalExpenses); ?></span></p>
        <p>Сумма всех приходов: <span id="totalIncome"><?php echo htmlspecialchars($totalIncome); ?></span></p>
        <p><strong>ИТОГО:</strong> <span id="totalBalance"><?php echo htmlspecialchars($totalBalance); ?></span></p>
    </section>
</main>

<script src="js/jquery.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>