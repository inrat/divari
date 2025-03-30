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

    $sql = "SELECT tekija, nimi, tyyppi, luokka FROM teokset 
            WHERE LOWER(tekija) LIKE LOWER('%$query%')
            OR LOWER(nimi) LIKE LOWER('%$query%')
            OR LOWER(tyyppi) LIKE LOWER('%$query%')
            OR LOWER(luokka) LIKE LOWER('%$query%')";

    $result = pg_query($db, $sql);

    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: []; // Palautetaan tyhjä taulukko jos ei tuloksia
}
?>
