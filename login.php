<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(array($email));
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            redirect('index.php');
        } else {
            $error = 'Неверный email или пароль';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 class="form-title">Вход</h2>
        <?php if($error): ?><div class="alert alert--error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Пароль</label><input type="password" name="password" required></div>
            <button type="submit" class="btn btn--primary" style="width:100%;">Войти</button>
        </form>
        <p style="text-align:center;margin-top:20px;color:#666;">Нет аккаунта? <a href="register.php" style="color:var(--primary);">Зарегистрироваться</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>