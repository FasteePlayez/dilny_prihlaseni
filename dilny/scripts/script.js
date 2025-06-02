// scripts/script.js

document.addEventListener('DOMContentLoaded', function() {
    const modalOverlay = document.getElementById('customConfirmModal');
    const modalMessage = document.getElementById('customConfirmMessage');
    const okButton = document.getElementById('customConfirmOk');
    const cancelButton = document.getElementById('customConfirmCancel');
    let confirmCallback = null;

    function showCustomConfirm(message, callback) {
        if (!modalOverlay || !modalMessage || !okButton || !cancelButton) {
            console.error("Chybí elementy pro vlastní modální okno. Používám standardní confirm.");
            // Fallback na standardní confirm, pokud elementy nejsou nalezeny
            if (window.confirm(message)) {
                callback(true);
            } else {
                callback(false);
            }
            return;
        }

        modalMessage.textContent = message;
        confirmCallback = callback;
        modalOverlay.style.display = 'flex'; // Zobrazit overlay
        setTimeout(() => modalOverlay.classList.add('active'), 10); // Aktivovat s malým zpožděním pro animaci
    }

    if (okButton) {
        okButton.addEventListener('click', function() {
            modalOverlay.classList.remove('active');
             setTimeout(() => {
                modalOverlay.style.display = 'none';
                if (confirmCallback) confirmCallback(true);
            }, 300); // Čas odpovídající transition-duration
        });
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            modalOverlay.classList.remove('active');
            setTimeout(() => {
                modalOverlay.style.display = 'none';
                if (confirmCallback) confirmCallback(false);
            }, 300); // Čas odpovídající transition-duration
        });
    }


    // --- Potvrzení pro přihlášku na dílnu ---
    const prihlaskaForm = document.getElementById('prihlaskaForm');
    if (prihlaskaForm) {
        prihlaskaForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Vždy zabráníme výchozímu odeslání, budeme ho řídit callbackem

            const nazevDilny = this.dataset.nazevDilny || "tuto dílnu";
            const jmenoDiteInput = document.getElementById('jmeno_dite');
            let jmenoDite = "toto dítě";
            if (jmenoDiteInput && jmenoDiteInput.value.trim() !== "") {
                jmenoDite = jmenoDiteInput.value.trim();
            }
            const potvrzeniZprava = `Vážně chcete dítě "${jmenoDite}" přihlásit na dílnu "${nazevDilny}"?`;

            showCustomConfirm(potvrzeniZprava, (confirmed) => {
                if (confirmed) {
                    this.submit(); // Odešleme formulář, pokud bylo potvrzeno
                }
            });
        });
    }

    // --- Potvrzení pro smazání dílny v admin panelu ---
    const odkazySmazatDilnu = document.querySelectorAll('a.smazat-dilnu-link');
    odkazySmazatDilnu.forEach(odkaz => {
        odkaz.addEventListener('click', function(event) {
            event.preventDefault(); // Vždy zabráníme výchozí akci odkazu

            const nazevDilnyProSmazani = this.dataset.nazevDilny || "vybranou dílnu";
            const href = this.href; // Uložíme si URL odkazu

            const potvrzeniZpravaSmazani = `Opravdu chcete smazat dílnu "${nazevDilnyProSmazani}"? Tímto krokem smažete i všechny přihlášky na tuto dílnu!`;

            showCustomConfirm(potvrzeniZpravaSmazani, (confirmed) => {
                if (confirmed) {
                    window.location.href = href; // Přejdeme na URL, pokud bylo potvrzeno
                }
            });
        });
    });
});
