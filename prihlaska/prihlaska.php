<?php
require_once '../includes/db_config.php';

if (!isset($_GET['id_dilna']) || !filter_var($_GET['id_dilna'], FILTER_VALIDATE_INT)) {
    $_SESSION['message'] = "Chybný požadavek: ID dílny chybí nebo je neplatné.";
    $_SESSION['message_type'] = "error";
    header("Location: ../index.php");
    exit;
}

$id_dilna = (int)$_GET['id_dilna'];

// Načteme i datum_konani pro zobrazení
$stmt = $pdo->prepare("SELECT nazev_dilna, kapacita, datum_konani FROM dilny WHERE id_dilna = ?");
$stmt->execute([$id_dilna]);
$dilna = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dilna) {
    $_SESSION['message'] = "Dílna nebyla nalezena.";
    $_SESSION['message_type'] = "error";
    header("Location: ../index.php");
    exit;
}

$stmt_pocet = $pdo->prepare("SELECT COUNT(*) as pocet FROM prihlasky WHERE id_dilna = ?");
$stmt_pocet->execute([$id_dilna]);
$aktualni_pocet = $stmt_pocet->fetchColumn();

if ($aktualni_pocet >= $dilna['kapacita']) {
    $_SESSION['message'] = "Kapacita dílny '" . htmlspecialchars($dilna['nazev_dilna']) . "' je již bohužel naplněna.";
    $_SESSION['message_type'] = "error";
    header("Location: ../index.php");
    exit;
}

$datum_konani_format = (new DateTime($dilna['datum_konani']))->format('j. n. Y \v H:i');

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihláška na dílnu - <?php echo htmlspecialchars($dilna['nazev_dilna']); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Přihláška na dílnu</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Zpět na seznam dílen</a></li>
                <li><a href="../admin/admin_login.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Přihláška na: <?php echo htmlspecialchars($dilna['nazev_dilna']); ?></h2>
        <!-- Zobrazení data a času dílny -->
        <p><strong>Termín konání:</strong> <?php echo $datum_konani_format; ?></p>


        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <form action="zpracuj_prihlasku.php" method="post">
            <input type="hidden" name="id_dilna" value="<?php echo $id_dilna; ?>">

            <div>
                <label for="jmeno_rodic">Jméno a příjmení rodiče:</label>
                <input type="text" id="jmeno_rodic" name="jmeno_rodic" required>
            </div>
            <div>
                <label for="jmeno_dite">Jméno a příjmení dítěte:</label>
                <input type="text" id="jmeno_dite" name="jmeno_dite" required>
            </div>
            <div>
                <input type="submit" value="Odeslat přihlášku">
            </div>
        </form>
    </div>

    <footer>
        <p>© <?php echo date("Y"); ?> Dílničky s.r.o.</p>
    </footer>
</body>
</html>
