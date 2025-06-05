<?php
require_once '../includes/db_config.php';

// Zničení všech session proměnných
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// zničení session
session_destroy();

header("Location: admin_login.php");
exit;
?>
