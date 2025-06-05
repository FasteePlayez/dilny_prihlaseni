<?php
require_once '../includes/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_dilna = filter_input(INPUT_POST, 'id_dilna', FILTER_VALIDATE_INT);
    $jmeno_rodic = trim(filter_input(INPUT_POST, 'jmeno_rodic', FILTER_SANITIZE_STRING));
    $jmeno_dite = trim(filter_input(INPUT_POST, 'jmeno_dite', FILTER_SANITIZE_STRING));

    if (!$id_dilna || empty($jmeno_rodic) || empty($jmeno_dite)) {
        $_SESSION['message'] = "Všechna pole jsou povinná.";
        $_SESSION['message_type'] = "error";
        header("Location: prihlaska.php?id_dilna=" . $id_dilna);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt_dilna_info = $pdo->prepare("SELECT kapacita FROM dilny WHERE id_dilna = ?");
        $stmt_dilna_info->execute([$id_dilna]);
        $dilna_info = $stmt_dilna_info->fetch(PDO::FETCH_ASSOC);

        if (!$dilna_info) {
            throw new Exception("Dílna nebyla nalezena.");
        }

        $stmt_pocet_prihlasenych = $pdo->prepare("SELECT COUNT(*) FROM prihlasky WHERE id_dilna = ?");
        $stmt_pocet_prihlasenych->execute([$id_dilna]);
        $aktualni_pocet = $stmt_pocet_prihlasenych->fetchColumn();

        if ($aktualni_pocet >= $dilna_info['kapacita']) {
            throw new Exception("Kapacita dílny je již bohužel naplněna.");
        }

        $stmt_rodic = $pdo->prepare("SELECT id_rodic FROM rodice WHERE jmeno_rodic = ?");
        $stmt_rodic->execute([$jmeno_rodic]);
        $rodic = $stmt_rodic->fetch(PDO::FETCH_ASSOC);

        if ($rodic) {
            $id_rodic = $rodic['id_rodic'];
        } else {
            $stmt_insert_rodic = $pdo->prepare("INSERT INTO rodice (jmeno_rodic) VALUES (?)");
            $stmt_insert_rodic->execute([$jmeno_rodic]);
            $id_rodic = $pdo->lastInsertId();
        }

        $stmt_dite = $pdo->prepare("SELECT id_dite FROM deti WHERE jmeno_dite = ? AND id_rodic = ?");
        $stmt_dite->execute([$jmeno_dite, $id_rodic]);
        $dite = $stmt_dite->fetch(PDO::FETCH_ASSOC);

        if ($dite) {
            $id_dite = $dite['id_dite'];
        } else {
            $stmt_insert_dite = $pdo->prepare("INSERT INTO deti (jmeno_dite, id_rodic) VALUES (?, ?)");
            $stmt_insert_dite->execute([$jmeno_dite, $id_rodic]);
            $id_dite = $pdo->lastInsertId();
        }

        // kontrola jestli neni dite ve vice jak 3 dilnach
        $stmt_pocet_dilen_dite = $pdo->prepare("SELECT COUNT(*) FROM prihlasky WHERE id_dite = ?");
        $stmt_pocet_dilen_dite->execute([$id_dite]);
        $pocet_dilen_pro_dite = $stmt_pocet_dilen_dite->fetchColumn();

        if ($pocet_dilen_pro_dite >= 3) {
            throw new Exception("Dítě '" . htmlspecialchars($jmeno_dite) . "' je již přihlášeno na maximální počet (3) dílen.");
        }

        $stmt_existujici_prihlaska = $pdo->prepare("SELECT COUNT(*) FROM prihlasky WHERE id_dite = ? AND id_dilna = ?");
        $stmt_existujici_prihlaska->execute([$id_dite, $id_dilna]);
        if ($stmt_existujici_prihlaska->fetchColumn() > 0) {
            throw new Exception("Dítě '" . htmlspecialchars($jmeno_dite) . "' je již na tuto dílnu přihlášeno.");
        }

        $stmt_insert_prihlaska = $pdo->prepare("INSERT INTO prihlasky (id_dite, id_dilna) VALUES (?, ?)");
        $stmt_insert_prihlaska->execute([$id_dite, $id_dilna]);

        $pdo->commit();
        $_SESSION['message'] = "Dítě '" . htmlspecialchars($jmeno_dite) . "' bylo úspěšně přihlášeno na dílnu!";
        $_SESSION['message_type'] = "success";
        header("Location: ../index.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Chyba databáze při zpracování přihlášky: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        error_log("PDO Error: " . $e->getMessage());
        header("Location: prihlaska.php?id_dilna=" . $id_dilna);
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: prihlaska.php?id_dilna=" . $id_dilna);
        exit;
    }

} else {
    header("Location: ../index.php");
    exit;
}
?>
