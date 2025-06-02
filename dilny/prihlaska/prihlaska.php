<?php
require_once '../includes/db_config.php';

if (!isset($_GET['id_dilna']) || !filter_var($_GET['id_dilna'], FILTER_VALIDATE_INT)) {
    $_SESSION['message'] = "Chybný požadavek: ID dílny chybí nebo je neplatné.";
    $_SESSION['message_type'] = "error";
    header("Location: ../index.php");
    exit;
}

$id_dilna = (int)$_GET['id_dilna'];

$stmt = $pdo->prepare("SELECT nazev_dilna, kapacita, datum_konani FROM dilny WHERE id_dilna = ?");
$stmt->execute([$id_dilna]);
$dilna = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dilna) {
    $_SESSION['message'] = "Dílna nebyla nalezena.";
    $_SESSION['message_type'] = "error";
    header("Location: ../index.php");
    exit;
}

// Nastavení titulku stránky PŘED vložením header.php
$page_title = "Přihláška na dílnu: " . htmlspecialchars($dilna['nazev_dilna']);
require_once '../includes/header.php';

// --- ZDE ZAČÍNÁ SPECIFICKÝ OBSAH PRO prihlaska.php ---
$stmt_pocet = $pdo->prepare("SELECT COUNT(*) as pocet FROM prihlasky WHERE id_dilna = ?");
$stmt_pocet->execute([$id_dilna]);
$aktualni_pocet = $stmt_pocet->fetchColumn();

if ($aktualni_pocet >= $dilna['kapacita']) {

    if (!isset($_SESSION['message'])) { // Zobrazíme jen pokud nebyla nastavena jiná zpráva
        $_SESSION['message'] = "Kapacita dílny '" . htmlspecialchars($dilna['nazev_dilna']) . "' je již bohužel naplněna.";
        $_SESSION['message_type'] = "error";

    }
}

$datum_konani_format = (new DateTime($dilna['datum_konani']))->format('j. n. Y \v H:i');
$nazev_dilny_pro_js = htmlspecialchars($dilna['nazev_dilna'], ENT_QUOTES, 'UTF-8');

?>

<h2>Přihláška na: <?php echo htmlspecialchars($dilna['nazev_dilna']); ?></h2>
<p><strong>Termín konání:</strong> <?php echo $datum_konani_format; ?></p>

<?php
if (isset($_SESSION['message'])): ?>
    <div class="<?php echo isset($_SESSION['message_type']) ? htmlspecialchars($_SESSION['message_type']) : 'info'; ?>">
        <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    </div>
<?php endif; ?>

<form action="zpracuj_prihlasku.php" method="post" id="prihlaskaForm" data-nazev-dilny="<?php echo $nazev_dilny_pro_js; ?>">
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

<?php
// --- ZDE KONČÍ SPECIFICKÝ OBSAH PRO prihlaska.php ---

require_once '../includes/footer.php';
?>
