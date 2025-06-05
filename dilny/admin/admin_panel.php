<?php
require_once '../includes/db_config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$page_title = "Admin Panel";
require_once '../includes/header.php';

$stmt_vsechny_dilny = $pdo->query("SELECT id_dilna, nazev_dilna FROM dilny ORDER BY nazev_dilna");
$vsechny_dilny = $stmt_vsechny_dilny->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="admin-welcome-text">Vítejte v administraci, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>

<?php
if (isset($_SESSION['admin_message'])): ?>
    <div class="<?php echo isset($_SESSION['admin_message_type']) ? htmlspecialchars($_SESSION['admin_message_type']) : 'info'; ?>">
        <?php echo htmlspecialchars($_SESSION['admin_message']); unset($_SESSION['admin_message']); unset($_SESSION['admin_message_type']); ?>
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
        <table>
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
                            |
                            <a href="smaz_dilnu.php?id_dilna=<?php echo $ex_dilna['id_dilna']; ?>"
                               class="smazat-dilnu-link"
                               data-nazev-dilny="<?php echo htmlspecialchars($ex_dilna['nazev_dilna'], ENT_QUOTES, 'UTF-8'); ?>"
                               style="color: red;">Smazat</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php

require_once '../includes/footer.php';
?>
