<?php

require_once '../includes/db_config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_panel.php");
    exit;
}

$page_title = "Admin Přihlášení";
require_once '../includes/header.php';

// --- ZDE ZAČÍNÁ SPECIFICKÝ OBSAH PRO admin_login.php ---
?>

<?php
if (isset($_SESSION['login_error'])): ?>
    <p class="error"><?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?></p>
<?php endif; ?>

<!-- Formulář pro přihlášení -->
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

<?php
// --- ZDE KONČÍ SPECIFICKÝ OBSAH PRO admin_login.php ---

require_once '../includes/footer.php';
?>
