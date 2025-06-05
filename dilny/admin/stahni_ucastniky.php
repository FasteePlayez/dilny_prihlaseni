<?php
require_once '../includes/db_config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    echo "Přístup odepřen.";
    exit;
}

if (!isset($_GET['id_dilna']) || !filter_var($_GET['id_dilna'], FILTER_VALIDATE_INT)) {
    $_SESSION['admin_message'] = "Chybné ID dílny pro stažení.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: admin_panel.php");
    exit;
}

$id_dilna = (int)$_GET['id_dilna'];

try {
    $stmt_nazev = $pdo->prepare("SELECT nazev_dilna FROM dilny WHERE id_dilna = ?");
    $stmt_nazev->execute([$id_dilna]);
    $nazev_dilny_obj = $stmt_nazev->fetch(PDO::FETCH_OBJ);

    if(!$nazev_dilny_obj){
        $_SESSION['admin_message'] = "Dílna s ID $id_dilna nebyla nalezena.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: admin_panel.php");
        exit;
    }
    $nazev_dilny_pro_soubor = preg_replace('/[^a-z0-9_]/i', '_', $nazev_dilny_obj->nazev_dilna);


    $stmt = $pdo->prepare("
        SELECT r.jmeno_rodic, d.jmeno_dite, p.datum_prihlaseni
        FROM prihlasky p
        JOIN deti d ON p.id_dite = d.id_dite
        JOIN rodice r ON d.id_rodic = r.id_rodic
        WHERE p.id_dilna = ?
        ORDER BY r.jmeno_rodic, d.jmeno_dite
    ");
    $stmt->execute([$id_dilna]);
    $ucastnici = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($ucastnici)) {
        $_SESSION['admin_message'] = "Pro dílnu '" . htmlspecialchars($nazev_dilny_obj->nazev_dilna) . "' nejsou žádní přihlášení účastníci.";
        $_SESSION['admin_message_type'] = "info";
        header("Location: admin_panel.php");
        exit;
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="ucastnici_'. $nazev_dilny_pro_soubor .'_'.date('Y-m-d').'.csv"');

    $output = fopen('php://output', 'w');

    fwrite($output, "\xEF\xBB\xBF");
    //fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, array('Jméno rodiče', 'Jméno dítěte', 'Datum přihlášení'), ';');

    foreach ($ucastnici as $ucastnik) {
        $datum_prihlaseni_format = (new DateTime($ucastnik['datum_prihlaseni']))->format('j.n.Y H:i:s');
        fputcsv($output, [$ucastnik['jmeno_rodic'], $ucastnik['jmeno_dite'], $datum_prihlaseni_format], ';');
    }
    fclose($output);
    exit;

} catch (PDOException $e) {
    error_log("PDO Error downloading participants: " . $e->getMessage());
    $_SESSION['admin_message'] = "Chyba při stahování účastníků: " . $e->getMessage();
    $_SESSION['admin_message_type'] = "error";
    header("Location: admin_panel.php");
    exit;
}
?>
