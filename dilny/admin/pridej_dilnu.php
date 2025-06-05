<?php
require_once '../includes/db_config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nazev_dilna = trim(filter_input(INPUT_POST, 'nazev_dilna', FILTER_SANITIZE_STRING));
    $vedouci = trim(filter_input(INPUT_POST, 'vedouci', FILTER_SANITIZE_STRING));
    $kapacita = filter_input(INPUT_POST, 'kapacita', FILTER_VALIDATE_INT);
    $cena = filter_input(INPUT_POST, 'cena', FILTER_VALIDATE_FLOAT);
    $datum_konani_raw = $_POST['datum_konani'];

    if (empty($nazev_dilna) || empty($vedouci) || $kapacita === false || $kapacita < 1 || $cena === false || $cena < 0 || empty($datum_konani_raw)) {
        $_SESSION['admin_message'] = "Všechna pole jsou povinná a musí být ve správném formátu.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: admin_panel.php");
        exit;
    }

    $datum_konani_dt = DateTime::createFromFormat('Y-m-d\TH:i', $datum_konani_raw);

    if (!$datum_konani_dt) {
        $datum_konani_dt = DateTime::createFromFormat('Y-m-d H:i', $datum_konani_raw);
    }

    if (!$datum_konani_dt) {
        $_SESSION['admin_message'] = "Neplatný formát data a času konání. Očekávaný formát je YYYY-MM-DDTHH:MM.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: admin_panel.php");
        exit;
    }

    $datum_konani_db = $datum_konani_dt->format('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("INSERT INTO dilny (nazev_dilna, vedouci, kapacita, cena, datum_konani) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nazev_dilna, $vedouci, $kapacita, $cena, $datum_konani_db]);

        $_SESSION['admin_message'] = "Dílna '" . htmlspecialchars($nazev_dilna) . "' byla úspěšně přidána.";
        $_SESSION['admin_message_type'] = "success";
    } catch (PDOException $e) {
        error_log("PDO Error adding workshop: " . $e->getMessage());
        $_SESSION['admin_message'] = "Chyba při přidávání dílny: " . $e->getMessage();
        $_SESSION['admin_message_type'] = "error";
    }
    header("Location: admin_panel.php");
    exit;

} else {
    header("Location: admin_panel.php");
    exit;
}
?>
