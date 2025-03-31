<?php

// Sisällytä konfiguraatio
require_once __DIR__ . '/../config/config.php';

/**
 * Hakee kirjat tietokannasta hakusanoilla
 * 
 * @param string $query Hakusana käyttäjältä
 * @param resource $db PostgreSQL-tietokantayhteys
 * @return array Tulokset assosiatiivisena taulukkona
 */

function hae_kirjat($query, $db) {
    if (empty($query)) {
        return [];
    }

    $sql = "SELECT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn, n.hinta, n.tila 
            FROM teokset t
            JOIN nide n ON t.teos_id = n.teos_id
            WHERE LOWER(t.tekija) LIKE LOWER('%$query%')
            OR LOWER(t.nimi) LIKE LOWER('%$query%')
            OR LOWER(t.tyyppi) LIKE LOWER('%$query%')
            OR LOWER(t.luokka) LIKE LOWER('%$query%')";

    $result = pg_query($db, $sql);

    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: []; // Palautetaan tyhjä taulukko jos ei tuloksia
}
?>
