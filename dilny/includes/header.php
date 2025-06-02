<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Zjistíme aktuální stránku pro případné aktivní označení v navigaci
$current_page_filename = basename($_SERVER['PHP_SELF']);
$current_script_path = $_SERVER['PHP_SELF'];

$path_prefix = '';
$script_directory = dirname($current_script_path);

if ($script_directory !== '/' && $script_directory !== '\\' && $script_directory !== '.') {
    $trimmed_directory = trim($script_directory, '/\\');
    $directory_segments = explode('/', $trimmed_directory);
    $depth = count($directory_segments);
    $path_prefix = str_repeat('../', $depth);
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Dílničky"; ?></title>
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>css/styles.css">
</head>
<body>
    <header>
        <h1>Dílničky</h1>
        <nav>
            <ul>
                <li><a href="<?php echo $path_prefix; ?>index.php" <?php if ($current_page_filename == "index.php" && $path_prefix == '') echo 'class="active"'; ?>>Domů</a></li>
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                    <li><a href="<?php echo $path_prefix; ?>admin/admin_panel.php" <?php if (strpos($current_script_path, "/admin/") !== false && $current_page_filename !== 'logout.php') echo 'class="active"'; ?>>Admin Panel</a></li>
                    <li><a href="<?php echo $path_prefix; ?>admin/logout.php">Odhlásit se (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $path_prefix; ?>admin/admin_login.php" <?php if ($current_page_filename == "admin_login.php" && strpos($current_script_path, "/admin/") !== false) echo 'class="active"'; ?>>Admin Přihlášení</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <!-- Zde začíná hlavní obsah stránky -->
