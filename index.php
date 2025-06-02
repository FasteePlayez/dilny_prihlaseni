<?php
require_once 'includes/db_config.php';

// Načtení dílen z databáze
$stmt_dilny = $pdo->query("SELECT id_dilna, nazev_dilna, vedouci, kapacita, cena, datum_konani FROM dilny ORDER BY datum_konani ASC");
$dilny = $stmt_dilny->fetchAll(PDO::FETCH_ASSOC);

// Najdeme nejbližší datum a čas konání dílny
$datum_dilnicek_info = "již brzy";
if (!empty($dilny)) {
    // Předpokládáme, že $dilny jsou seřazeny podle datum_konani ASC
    $nejblizsi_dilna_obj = new DateTime($dilny[0]['datum_konani']);
    $datum_dilnicek_info = "od " . $nejblizsi_dilna_obj->format('j. n. Y \v H:i');
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dílničky</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Dílničky</h1>
        <nav>
            <ul>
                <li><a href="index.php">Domů</a></li>
                <li><a href="admin/admin_login.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <section id="uvod">
            <h2>Vítejte na Dílničkách!</h2>
            <!-- Aktualizováno zobrazení data a času -->
            <p>Připravované dílničky proběhnou <?php echo htmlspecialchars($datum_dilnicek_info); ?>.</p>
            <p>Srolujte dolů pro zobrazení nabídky dílen.</p>
        </section>

        <section id="seznam-dilen">
            <h2>Nabídka dílen</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="<?php echo $_SESSION['message_type']; ?>">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($dilny)): ?>
                <p>Aktuálně nejsou vypsané žádné dílny.</p>
            <?php else: ?>
                <?php foreach ($dilny as $dilna): ?>
                    <?php
                        $stmt_pocet = $pdo->prepare("SELECT COUNT(*) as pocet FROM prihlasky WHERE id_dilna = ?");
                        $stmt_pocet->execute([$dilna['id_dilna']]);
                        $aktualni_pocet = $stmt_pocet->fetchColumn();
                        $volna_mista = $dilna['kapacita'] - $aktualni_pocet;
                    ?>
                    <div class="dilna">
                        <h3><?php echo htmlspecialchars($dilna['nazev_dilna']); ?></h3>
                        <p><strong>Vede:</strong> <?php echo htmlspecialchars($dilna['vedouci']); ?></p>
                        <p><strong>Kapacita:</strong> <?php echo htmlspecialchars($dilna['kapacita']); ?> (Volných míst: <?php echo $volna_mista > 0 ? $volna_mista : 0; ?>)</p>
                        <p><strong>Cena:</strong> <?php echo htmlspecialchars(number_format($dilna['cena'], 2, ',', ' ')); ?> Kč</p>
                        <!-- Aktualizováno zobrazení data a času -->
                        <p><strong>Datum a čas konání:</strong> <?php echo (new DateTime($dilna['datum_konani']))->format('j. n. Y H:i'); ?></p>
                        <?php if ($volna_mista > 0): ?>
                            <a href="prihlaska/prihlaska.php?id_dilna=<?php echo $dilna['id_dilna']; ?>" class="prihlasit-se">Přihlásit se</a>
                        <?php else: ?>
                            <p style="color: red; font-weight: bold;">Kapacita dílny je naplněna.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>

    <footer>
        <p>© <?php echo date("Y"); ?> ZŠ a MŠ Deblín</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
