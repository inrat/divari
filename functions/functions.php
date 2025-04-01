<!-- functions.php -->
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

    // SQL-kysely, joka hakee teokset ja nide Lassen Lehti -skeemasta sekä public-skeemasta
    $sql = "SELECT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn, n.hinta, n.tila, 
                   d.nimi AS divari_nimi
            FROM 
                lassen_lehti.teokset t  -- Haetaan Lassen Lehti -skeeman teokset
            JOIN 
                lassen_lehti.nide n ON t.teos_id = n.teos_id  -- Haetaan Lassen Lehti -skeeman nide
            JOIN 
                public.divarit d ON n.divari_id = d.divari_id  -- Yhdistetään public-divarit-tauluun
            WHERE LOWER(t.tekija) LIKE LOWER('%$query%')
            OR LOWER(t.nimi) LIKE LOWER('%$query%')
            OR LOWER(t.tyyppi) LIKE LOWER('%$query%')
            OR LOWER(t.luokka) LIKE LOWER('%$query%')
            OR LOWER(d.nimi) LIKE LOWER('%$query%')

            UNION

            SELECT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn, n.hinta, n.tila, 
                   d.nimi AS divari_nimi
            FROM 
                public.teokset t  -- Haetaan public-skeeman teokset
            JOIN 
                public.nide n ON t.teos_id = n.teos_id  -- Haetaan public-skeeman nide
            JOIN 
                public.divarit d ON n.divari_id = d.divari_id  -- Yhdistetään public-divarit-tauluun
            WHERE LOWER(t.tekija) LIKE LOWER('%$query%')
            OR LOWER(t.nimi) LIKE LOWER('%$query%')
            OR LOWER(t.tyyppi) LIKE LOWER('%$query%')
            OR LOWER(t.luokka) LIKE LOWER('%$query%')
            OR LOWER(d.nimi) LIKE LOWER('%$query%')";  // Lisätty divari-nimi hakuehtoon

    $result = pg_query($db, $sql);

    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: []; // Palautetaan tyhjä taulukko, jos ei tuloksia
}


?>
