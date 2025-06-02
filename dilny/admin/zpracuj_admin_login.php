<?php
require_once '../includes/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Vyplňte prosím uživatelské jméno i heslo.";
        header("Location: admin_login.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id_admin, username, password_hash FROM admini WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Heslo je správné, vytvořit session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];

            unset($_SESSION['login_error']); // Smazat případnou předchozí chybovou hlášku
            header("Location: admin_panel.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Neplatné uživatelské jméno nebo heslo.";
            header("Location: admin_login.php");
            exit;
        }
    } catch (PDOException $e) {
        error_log("PDO Error during admin login: " . $e->getMessage());
        $_SESSION['login_error'] = "Došlo k chybě při přihlašování. Zkuste to prosím později.";
        header("Location: admin_login.php");
        exit;
    }
} else {
    header("Location: admin_login.php");
    exit;
}
?>
