<?php
require_once '../includes/db_config.php'; // Pro připojení k DB a session_start()

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password']; // Heslo nefiltrujeme před hashem
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($password) || empty($password_confirm)) {
        $message = "Všechna pole jsou povinná.";
        $message_type = "error";
    } elseif ($password !== $password_confirm) {
        $message = "Hesla se neshodují.";
        $message_type = "error";
    } elseif (strlen($password) < 6) { // Základní kontrola délky hesla
        $message = "Heslo musí mít alespoň 6 znaků.";
        $message_type = "error";
    } else {
        try {
            // Zkontrolujeme, zda uživatel již neexistuje
            $stmt_check = $pdo->prepare("SELECT id_admin FROM admini WHERE username = ?");
            $stmt_check->execute([$username]);
            if ($stmt_check->fetch()) {
                $message = "Administrátor s tímto jménem již existuje.";
                $message_type = "error";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt_insert = $pdo->prepare("INSERT INTO admini (username, password_hash) VALUES (?, ?)");
                if ($stmt_insert->execute([$username, $password_hash])) {
                    $message = "Nový administrátor '$username' byl úspěšně vytvořen.";
                    $message_type = "success";
                } else {
                    $message = "Nepodařilo se vytvořit administrátora.";
                    $message_type = "error";
                }
            }
        } catch (PDOException $e) {
            $message = "Chyba databáze: " . $e->getMessage();
            $message_type = "error";
            error_log("Admin registration PDO Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace nového administrátora</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Jen pro tuto stránku, aby byla trochu oddělená */
        body { background-color: #e0e0e0; }
        .container { max-width: 500px; background-color: #fff; padding: 30px; margin-top: 50px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registrace nového administrátora</h2>
        <p><a href="admin_login.php">« Zpět na přihlášení</a></p>

        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="register_admin.php" method="post">
            <div>
                <label for="username">Uživatelské jméno:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="password_confirm">Potvrzení hesla:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <div>
                <input type="submit" value="Registrovat administrátora">
            </div>
        </form>
        <p style="color:red; margin-top:20px;"><strong>Varování:</strong> Tento skript by měl být po použití smazán nebo zabezpečen, aby se zabránilo neoprávněnému vytváření administrátorů.</p>
    </div>
</body>
</html>
