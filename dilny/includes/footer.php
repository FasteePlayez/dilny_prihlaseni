<?php

$path_prefix = '';
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

if ($current_dir === 'admin' || $current_dir === 'prihlaska') {
    $path_prefix = '../';
}

?>
    </div> <!-- Uzavření <div class="container"> z header.php -->

    <footer>
        <p>© <?php echo date("Y"); ?> ZŠ a MŠ Deblín</p>
    </footer>

    <!-- vlastní okno -->
    <div id="customConfirmModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <p id="customConfirmMessage"></p>
            <div class="modal-buttons">
                <button id="customConfirmOk">OK <span class="christmas-icon">🎄</span></button>
                <button id="customConfirmCancel">Storno <span class="christmas-icon">❄️</span></button>
            </div>
        </div>
    </div>

    <script src="<?php echo $path_prefix; ?>scripts/script.js"></script>

</body>
</html>
