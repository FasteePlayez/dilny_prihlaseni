<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
    <header class="site-header">
        <div class="logo-container">
             <h1><a href="<?php echo $path_prefix; ?>index.php" class="logo-link">Dílničky</a></h1>
             <img src="images/deblin_logo.png">
        </div>
        <nav class="main-nav">
            <ul>
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>

                    <li class="nav-text-link"><a href="<?php echo $path_prefix; ?>admin/admin_panel.php" <?php if (strpos($current_script_path, "/admin/") !== false && $current_page_filename !== 'logout.php') echo 'class="active"'; ?>>Admin Panel</a></li>
                    <li class="nav-text-link"><a href="<?php echo $path_prefix; ?>admin/logout.php">Odhlásit se</a></li>
                <?php else: ?>
                    <li class="nav-text-link"><a href="<?php echo $path_prefix; ?>admin/admin_login.php" <?php if ($current_page_filename == "admin_login.php" && strpos($current_script_path, "/admin/") !== false) echo 'class="active"'; ?>>admin přihlášení</a></li>
                <?php endif; ?>
                <li><a href="https://www.zs-deblin.cz/" target="_blank" class="nav-button">Přejít na stránky školy</a></li>
                <li><a href="<?php echo $path_prefix; ?>index.php" class="nav-button <?php if ($current_page_filename == "index.php" && $path_prefix == '') echo 'active-button'; ?>">Domů</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <!-- začátek stránky, ukončeno ve footeru -->
