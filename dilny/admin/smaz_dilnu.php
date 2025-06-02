<?php
require_once '../includes/db_config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['admin_message'] = "Pro tuto akci musíte být přihlášeni.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id_dilna']) || !filter_var($_GET['id_dilna'], FILTER_VALIDATE_INT)) {
    $_SESSION['admin_message'] = "Chybné nebo chybějící ID dílny pro smazání.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: admin_panel.php");
    exit;
}

$id_dilna_smazat = (int)$_GET['id_dilna'];

try {
    $stmt_nazev = $pdo->prepare("SELECT nazev_dilna FROM dilny WHERE id_dilna = ?");
    $stmt_nazev->execute([$id_dilna_smazat]);
    $dilna = $stmt_nazev->fetch(PDO::FETCH_ASSOC);

    if (!$dilna) {
        $_SESSION['admin_message'] = "Dílna s ID $id_dilna_smazat nebyla nalezena.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: admin_panel.php");
        exit;
    }

    $nazev_smazane_dilny = $dilna['nazev_dilna'];

    // Smazání dílny z databáze
    // i všechny přihlášky spojené s touto dílnou.
    $stmt_smaz = $pdo->prepare("DELETE FROM dilny WHERE id_dilna = ?");
    $stmt_smaz->execute([$id_dilna_smazat]);

    if ($stmt_smaz->rowCount() > 0) {
        $_SESSION['admin_message'] = "Dílna '" . htmlspecialchars($nazev_smazane_dilny) . "' (ID: $id_dilna_smazat) a všechny její přihlášky byly úspěšně smazány.";
        $_SESSION['admin_message_type'] = "success";
    } else {
        // Toto by se nemělo stát, pokud kontrola názvu výše prošla, ale pro jistotu
        $_SESSION['admin_message'] = "Dílnu '" . htmlspecialchars($nazev_smazane_dilny) . "' se nepodařilo smazat nebo již neexistovala.";
        $_SESSION['admin_message_type'] = "warning";
    }

} catch (PDOException $e) {
    error_log("PDO Error deleting workshop: " . $e->getMessage());
    $_SESSION['admin_message'] = "Chyba databáze při mazání dílny: " . $e->getMessage();
    $_SESSION['admin_message_type'] = "error";
}

header("Location: admin_panel.php");
exit;
?>
