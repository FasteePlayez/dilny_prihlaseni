<?php
require_once '../includes/db_config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$stmt_vsechny_dilny = $pdo->query("SELECT id_dilna, nazev_dilna FROM dilny ORDER BY nazev_dilna");
$vsechny_dilny = $stmt_vsechny_dilny->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Zobrazit web</a></li>
                <li><a href="logout.php">Odhlásit se (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Vítejte v administraci, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>

        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="<?php echo $_SESSION['admin_message_type']; ?>">
                <?php echo $_SESSION['admin_message']; unset($_SESSION['admin_message']); unset($_SESSION['admin_message_type']); ?>
            </div>
        <?php endif; ?>

        <section class="admin-section">
            <h3>Přidat novou dílnu</h3>
            <form action="pridej_dilnu.php" method="post">
                <div>
                    <label for="nazev_dilna">Název dílny:</label>
                    <input type="text" id="nazev_dilna" name="nazev_dilna" required>
                </div>
                <div>
                    <label for="vedouci">Kdo vede:</label>
                    <input type="text" id="vedouci" name="vedouci" required>
                </div>
                <div>
                    <label for="kapacita">Kapacita:</label>
                    <input type="number" id="kapacita" name="kapacita" min="1" required>
                </div>
                <div>
                    <label for="cena">Cena (Kč):</label>
                    <input type="number" id="cena" name="cena" step="0.01" min="0" required>
                </div>
                <div>
                    <!-- Změněno input type na datetime-local -->
                    <label for="datum_konani">Datum a čas konání:</label>
                    <input type="datetime-local" id="datum_konani" name="datum_konani" required>
                </div>
                <div>
                    <input type="submit" value="Přidat dílnu">
                </div>
            </form>
        </section>

        <section class="admin-section">
            <h3>Stáhnout seznam přihlášených na dílnu</h3>
            <?php if (empty($vsechny_dilny)): ?>
                <p>Zatím nebyly vytvořeny žádné dílny.</p>
            <?php else: ?>
                <form action="stahni_ucastniky.php" method="get" target="_blank">
                    <div>
                        <label for="id_dilna_download">Vyberte dílnu:</label>
                        <select name="id_dilna" id="id_dilna_download" required>
                            <option value="">-- Vyberte dílnu --</option>
                            <?php foreach ($vsechny_dilny as $dilna_option): ?>
                                <option value="<?php echo $dilna_option['id_dilna']; ?>">
                                    <?php echo htmlspecialchars($dilna_option['nazev_dilna']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <input type="submit" value="Stáhnout seznam (CSV)">
                    </div>
                </form>
            <?php endif; ?>
        </section>

        <section class="admin-section">
            <h3>Seznam existujících dílen</h3>
            <?php
            $stmt_existujici_dilny = $pdo->query("SELECT id_dilna, nazev_dilna, vedouci, kapacita, cena, datum_konani FROM dilny ORDER BY datum_konani ASC");
            $existujici_dilny = $stmt_existujici_dilny->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <?php if (empty($existujici_dilny)): ?>
                <p>Nebyly nalezeny žádné dílny.</p>
            <?php else: ?>
                <table border="1" style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Název</th>
                            <th>Vede</th>
                            <th>Kapacita</th>
                            <th>Cena</th>
                            <th>Datum a čas</th>
                            <th>Přihlášeno</th>
                            <th>Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($existujici_dilny as $ex_dilna): ?>
                            <?php
                                $stmt_pocet_pr = $pdo->prepare("SELECT COUNT(*) FROM prihlasky WHERE id_dilna = ?");
                                $stmt_pocet_pr->execute([$ex_dilna['id_dilna']]);
                                $pocet_prihlasenych = $stmt_pocet_pr->fetchColumn();
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ex_dilna['nazev_dilna']); ?></td>
                                <td><?php echo htmlspecialchars($ex_dilna['vedouci']); ?></td>
                                <td><?php echo htmlspecialchars($ex_dilna['kapacita']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($ex_dilna['cena'], 2, ',', ' ')); ?> Kč</td>
                                <td><?php echo (new DateTime($ex_dilna['datum_konani']))->format('j. n. Y H:i'); ?></td>
                                <td><?php echo $pocet_prihlasenych; ?></td>
                                <td>
                                    <a href="stahni_ucastniky.php?id_dilna=<?php echo $ex_dilna['id_dilna']; ?>" target="_blank">Stáhnout účastníky</a>
                                    <!-- Přidáno tlačítko/odkaz pro smazání -->
                                    |
                                    <a href="smaz_dilnu.php?id_dilna=<?php echo $ex_dilna['id_dilna']; ?>"
                                       onclick="return confirm('Opravdu chcete smazat dílnu \'<?php echo htmlspecialchars(addslashes($ex_dilna['nazev_dilna'])); ?>\'? Tímto krokem smažete i všechny přihlášky na tuto dílnu!');"
                                       style="color: red;">Smazat</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

    </div>

    <footer>
        <p>© <?php echo date("Y"); ?> ZŠ a MŠ Deblín</p>
    </footer>
</body>
</html>
