# Webová Aplikace pro Správu Dílniček

Jednoduchá webová aplikace postavená na PHP, MySQL, HTML, CSS a JavaScriptu pro evidenci a správu přihlášek dětí na volnočasové dílničky. Aplikace zahrnuje uživatelské rozhraní pro rodiče a administrátorský panel pro správu dílen a účastníků.

## Funkce

### Pro Uživatele (Rodiče):

*   Zobrazení hlavní stránky s úvodními informacemi a datem konání dílniček.
*   Přehledný seznam dostupných dílen s následujícími informacemi:
    *   Název dílny
    *   Jméno vedoucího
    *   Kapacita (a počet volných míst)
    *   Cena
    *   Datum a čas konání
*   Možnost přihlásit dítě na vybranou dílnu prostřednictvím formuláře.
    *   Zadání jména rodiče a jména dítěte.
    *   JavaScriptové potvrzení před odesláním přihlášky.
*   Omezení: Jedno dítě může být přihlášeno na maximálně 3 dílny. Rodič může přihlásit více svých dětí, každé s limitem 3 dílen.
*   Vizuální indikace naplněnosti kapacity dílny.

### Pro Administrátory:

*   Samostatná přihlašovací stránka pro administrátory.
*   Administrátorský panel po úspěšném přihlášení.
*   **Správa dílen:**
    *   Možnost přidat novou dílnu (název, vedoucí, kapacita, cena, datum a čas konání).
    *   Možnost smazat existující dílnu (včetně JavaScriptového potvrzení).
    *   Přehled existujících dílen s informacemi o počtu přihlášených.
*   **Správa účastníků:**
    *   Možnost stáhnout seznam přihlášených dětí a rodičů pro konkrétní dílnu ve formátu CSV.
*   Možnost odhlášení.

## Použité Technologie

*   **Backend:** PHP (s PDO pro interakci s databází)
*   **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS)
*   **Databáze:** MySQL / MariaDB
*   **Fonty:** Google Fonts (Lobster, Noto Sans)
*   **Design:** Vlastní vánoční téma (červená, zelená, bílá).

## Struktura Projektu

/dilnicky/
|-- index.php (Hlavní stránka pro uživatele)
|-- prihlaska/
| |-- prihlaska.php (Formulář pro přihlášení na dílnu)
| |-- zpracuj_prihlasku.php (PHP skript pro zpracování přihlášky)
|-- admin/
| |-- admin_login.php (Přihlašovací stránka pro admina)
| |-- admin_panel.php (Panel pro administrátory)
| |-- zpracuj_admin_login.php (PHP skript pro zpracování admin přihlášení)
| |-- pridej_dilnu.php (PHP skript pro přidání dílny adminem)
| |-- smaz_dilnu.php (PHP skript pro smazání dílny adminem)
| |-- stahni_ucastniky.php (PHP skript pro stažení seznamu účastníků)
| |-- logout.php (Odhlášení admina)
|-- includes/
| |-- db_config.php (Konfigurace připojení k databázi)
| |-- header.php (Společná hlavička HTML)
| |-- footer.php (Společná patička HTML)
|-- css/
| |-- styles.css (Hlavní CSS styly)
|-- scripts/
| |-- script.js (JavaScript pro interaktivitu)
|-- images/ (Adresář pro obrázky, např. pozadí)


## Instalace a Spuštění

1.  **Webový server:** Ujistěte se, že máte nainstalovaný a spuštěný webový server s podporou PHP (např. Apache, Nginx) a databázový server MySQL/MariaDB.
2.  **Databáze:**
    *   Vytvořte databázi (např. `dilnicky_db`).
    *   Importujte strukturu tabulek a případná počáteční data. SQL skript pro vytvoření tabulek:
      ```sql
      -- Zde vložte SQL kód pro vytvoření tabulek (rodice, deti, dilny, prihlasky, admini)
      -- CREATE TABLE `rodice` ( ... );
      -- ... atd.
      -- Nezapomeňte na ukázkového admina:
      -- INSERT INTO `admini` (`username`, `password_hash`) VALUES ('admin', '$2y$10$...hash_pro_heslo_admin123...');
      ```
3.  **Konfigurace:**
    *   Zkopírujte soubory projektu do kořenového adresáře vašeho webového serveru (např. `htdocs/dilnicky` nebo `www/dilnicky`).
    *   Upravte soubor `includes/db_config.php` a nastavte správné přihlašovací údaje k vaší databázi (`DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`).
4.  **Obrázky:**
    *   Pokud používáte obrázek na pozadí v sekci `#uvod`, nahrajte ho do adresáře `images/` a ujistěte se, že cesta v `css/styles.css` (`background-image: url('../images/vas-obrazek.jpg');`) je správná.
5.  **Přístup:**
    *   Otevřete webovou aplikaci v prohlížeči (např. `http://localhost/dilnicky/`).
    *   Přihlašovací údaje pro výchozího administrátora:
        *   Vytvořte pomocí register_admin

## Licence

Ber co vidiš bro :)
