<?php
require_once 'includes/db_config.php';

$page_title = "Vítejte na Dílničkách";
require_once 'includes/header.php';

// --- ZDE ZAČÍNÁ SPECIFICKÝ OBSAH PRO index.php ---
$stmt_dilny = $pdo->query("SELECT id_dilna, nazev_dilna, vedouci, kapacita, cena, datum_konani FROM dilny ORDER BY datum_konani ASC");
$dilny = $stmt_dilny->fetchAll(PDO::FETCH_ASSOC);

$datum_dilnicek_info = "již brzy";
if (!empty($dilny)) {
    $nejblizsi_dilna_obj = new DateTime($dilny[0]['datum_konani']);
    $datum_dilnicek_info = "od " . $nejblizsi_dilna_obj->format('j. n. Y \v H:i');
}
?>

<section id="uvod">
    <h2>Vítejte na Dílničkách!</h2>
    <p>Připravované dílničky proběhnou <?php echo htmlspecialchars($datum_dilnicek_info); ?>.</p>
    <p>Srolujte dolů pro zobrazení nabídky dílen.</p>
</section>

<section id="seznam-dilen">
    <h2>Nabídka dílen</h2>
    <?php
    if (isset($_SESSION['message'])): ?>
        <div class="<?php echo isset($_SESSION['message_type']) ? htmlspecialchars($_SESSION['message_type']) : 'info'; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
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
                <p><strong>Datum a čas konání:</strong> <?php echo (new DateTime($dilna['datum_konani']))->format('j. n. Y H:i'); ?></p>
                <?php if ($volna_mista > 0): ?>
                    <?php
                        // Zjištění prefixu cesty pro odkaz na přihlášku
                        // Pokud je prihlaska.php v podadresáři /prihlaska/
                        $prihlaska_path_prefix = (file_exists('prihlaska/prihlaska.php')) ? 'prihlaska/' : '';
                    ?>
                    <a href="<?php echo $prihlaska_path_prefix; ?>prihlaska.php?id_dilna=<?php echo $dilna['id_dilna']; ?>" class="prihlasit-se">Přihlásit se</a>
                <?php else: ?>
                    <p style="color: red; font-weight: bold;">Kapacita dílny je naplněna.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php

require_once 'includes/footer.php';
?>
