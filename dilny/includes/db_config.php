<?php
define('DB_SERVER', 'localhost'); // nebo IP adresa serveru
define('DB_USERNAME', 'root');    // vaše uživatelské jméno k DB
define('DB_PASSWORD', '');        // vaše heslo k DB
define('DB_NAME', 'dilnicky_db'); // název vaší databáze

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("CHYBA: Nelze se připojit. " . $e->getMessage());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
