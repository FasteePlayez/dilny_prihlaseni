<?php
require_once 'includes/db_config.php';

$page_title = "Vítejte na Dílničkách";
require_once 'includes/header.php';

$stmt_dilny = $pdo->query("SELECT id_dilna, nazev_dilna, vedouci, kapacita, cena, datum_konani FROM dilny ORDER BY datum_konani ASC");
$dilny = $stmt_dilny->fetchAll(PDO::FETCH_ASSOC);

$datum_dilnicek_info = "již brzy";
if (!empty($dilny)) {
    $nejblizsi_dilna_obj = new DateTime($dilny[0]['datum_konani']);
    $datum_dilnicek_info = "od " . $nejblizsi_dilna_obj->format('j. n. Y \v H:i');
}
?>

<section id="uvod">
    <div class="uvod-text-box">
        <h2>Vítejte na dílničkách</h2>
        <p><?php echo htmlspecialchars($datum_dilnicek_info); ?></p>
        <p>Srolujte dolů pro zobrazení nabídky dílen.</p>
    </div>
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
        <div class="dilny-grid">
            <?php foreach ($dilny as $dilna): ?>
                <?php
                    $stmt_pocet_prihlasenych = $pdo->prepare("SELECT COUNT(*) as pocet FROM prihlasky WHERE id_dilna = ?");
                    $stmt_pocet_prihlasenych->execute([$dilna['id_dilna']]);
                    $aktualni_pocet_prihlasenych = $stmt_pocet_prihlasenych->fetchColumn();
                    $volna_mista = $dilna['kapacita'] - $aktualni_pocet_prihlasenych;
                    $procent_obsazenosti = ($dilna['kapacita'] > 0) ? ($aktualni_pocet_prihlasenych / $dilna['kapacita']) * 100 : 0;
                    $datum_obj = new DateTime($dilna['datum_konani']);
                ?>
                <div class="dilna-karta">
                    <div class="dilna-obsah">
                        <div class="dilna-header">
                            <h3><?php echo htmlspecialchars($dilna['nazev_dilna']); ?></h3>

                        </div>
                        <p class="dilna-vedouci">Vede: <?php echo htmlspecialchars($dilna['vedouci']); ?></p>

                        <div class="dilna-info-grid">
                            <div class="info-item">
                                <span class="ikona">🗓️</span> <?php echo $datum_obj->format('j. n. Y'); ?>
                            </div>
                            <div class="info-item">
                                <span class="ikona">🕒</span> <?php echo $datum_obj->format('H:i'); ?>
                            </div>
                            <div class="info-item">
                                <span class="ikona">💰</span> <?php echo htmlspecialchars(number_format($dilna['cena'], 0, ',', ' ')); ?> Kč
                            </div>
                            <div class="info-item">
                                <span class="ikona">👥</span> <?php echo $aktualni_pocet_prihlasenych; ?>/<?php echo $dilna['kapacita']; ?> přihlášeno
                            </div>
                        </div>

                        <div class="dilna-dostupnost">
                            <span>Dostupnost</span>
                            <strong><?php echo $volna_mista > 0 ? $volna_mista . " volných míst" : "Naplněno"; ?></strong>
                        </div>
                        <div class="progress-bar-kontejner">
                            <div class="progress-bar" style="width: <?php echo round($procent_obsazenosti, 0); ?>%;"></div>
                        </div>

                        <?php if ($volna_mista > 0): ?>
                            <?php
                                $prihlaska_path_prefix = '';
                                if (file_exists('prihlaska/prihlaska.php')) {
                                    $prihlaska_path_prefix = 'prihlaska/';
                                } elseif (file_exists('../prihlaska/prihlaska.php')) {
                                    $prihlaska_path_prefix = '../prihlaska/';
                                }
                            ?>
                            <a href="<?php echo $prihlaska_path_prefix; ?>prihlaska.php?id_dilna=<?php echo $dilna['id_dilna']; ?>" class="tlacitko-prihlasit">Registrovat</a>
                        <?php else: ?>
                            <button class="tlacitko-prihlasit obsazeno" disabled>Naplněno</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
require_once 'includes/footer.php';
?>
