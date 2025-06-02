<?php
require_once '../includes/db_config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_panel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Přihlášení</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Admin Přihlášení</h1>
         <nav>
            <ul>
                <li><a href="../index.php">Zpět na web</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <?php if (isset($_SESSION['login_error'])): ?>
            <p class="error"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></p>
        <?php endif; ?>

        <form action="zpracuj_admin_login.php" method="post">
            <div>
                <label for="username">Uživatelské jméno:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <input type="submit" value="Přihlásit se">
            </div>
        </form>
    </div>
    <footer>
        <p>© <?php echo date("Y"); ?> ZŠ a MŠ Deblín</p>
    </footer>
</body>
</html>
