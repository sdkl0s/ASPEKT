<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'Все поля обязательны';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email';
    } elseif ($password !== $password_confirm) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль минимум 6 символов';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute(array($email));
        if ($check->fetch()) {
            $error = 'Email уже зарегистрирован';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute(array($full_name, $email, $hashed))) {
                $success = 'Регистрация успешна! Перенаправляем...';
                header("refresh:2;url=login.php");
            } else {
                $error = 'Ошибка регистрации';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 class="form-title">Регистрация</h2>
        <?php if($error): ?><div class="alert alert--error"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert--success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>ФИО</label><input type="text" name="full_name" value="<?php echo isset($_POST['full_name'])?h($_POST['full_name']):''; ?>" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo isset($_POST['email'])?h($_POST['email']):''; ?>" required></div>
            <div class="form-group"><label>Пароль</label><input type="password" name="password" required></div>
            <div class="form-group"><label>Подтвердите пароль</label><input type="password" name="password_confirm" required></div>
            <button type="submit" class="btn btn--primary" style="width:100%;">Зарегистрироваться</button>
        </form>
        <p style="text-align:center;margin-top:20px;color:#666;">Уже есть аккаунт? <a href="login.php" style="color:var(--primary);">Войти</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>