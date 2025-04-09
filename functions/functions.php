<?php
// Ladataan tietokantayhteyden konfiguraatiot (mm. $db-muuttuja)
require_once __DIR__ . '/../config/config.php';

/**
 * Hakee kirjoja annetulla hakusanalla.
 * Haku suoritetaan kahdesta eri skeemasta: lassen_lehti ja public.
 * Palauttaa kirjojen perustiedot (ei niteitä), jos ne vastaavat hakukriteeriä.
 *
 * @param string $query Hakusana (esim. kirjailijan nimi, teoksen nimi tai tyyppi)
 * @param resource $db PostgreSQL-tietokantayhteys
 * @return array Palauttaa löydetyt teokset assosiatiivisena taulukkona
 */
function hae_kirjat($query, $db) {
    if (empty($query)) {
        return [];
    }

    // SQL-kysely: haku sekä lassen_lehti- että public-skeemoista
    $sql = "
        SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
        FROM lassen_lehti.teokset t
        JOIN lassen_lehti.nide n ON t.teos_id = n.teos_id
        JOIN public.divarit d ON n.divari_id = d.divari_id
        WHERE LOWER(t.tekija) LIKE LOWER('%$query%')
           OR LOWER(t.nimi) LIKE LOWER('%$query%')
           OR LOWER(t.tyyppi) LIKE LOWER('%$query%')
           OR LOWER(t.luokka) LIKE LOWER('%$query%')
           OR LOWER(d.nimi) LIKE LOWER('%$query%')

        UNION

        SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
        FROM public.teokset t
        JOIN public.nide n ON t.teos_id = n.teos_id
        JOIN public.divarit d ON n.divari_id = d.divari_id
        WHERE LOWER(t.tekija) LIKE LOWER('%$query%')
           OR LOWER(t.nimi) LIKE LOWER('%$query%')
           OR LOWER(t.tyyppi) LIKE LOWER('%$query%')
           OR LOWER(t.luokka) LIKE LOWER('%$query%')
           OR LOWER(d.nimi) LIKE LOWER('%$query%')
    ";

    $result = pg_query($db, $sql);

    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: []; // Palautetaan taulukko tai tyhjä taulukko
}

/**
 * Hakee tietyn teoksen kaikki niteet (fyysiset kappaleet) kahdesta skeemasta.
 *
 * @param string $tekija Teoksen tekijän nimi (täsmällinen)
 * @param string $nimi Teoksen nimi (täsmällinen)
 * @param resource $db PostgreSQL-tietokantayhteys
 * @return array Palauttaa niteet assosiatiivisena taulukkona
 */
function hae_kirja_niteet($tekija, $nimi, $db) {
    $sql = "
        SELECT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn,
               n.hinta, n.tila, d.nimi AS divari_nimi, n.nide_id AS nide_id
        FROM 
            lassen_lehti.teokset t
        JOIN 
            lassen_lehti.nide n ON t.teos_id = n.teos_id
        JOIN 
            public.divarit d ON n.divari_id = d.divari_id
        WHERE LOWER(t.tekija) = LOWER($1)
          AND LOWER(t.nimi) = LOWER($2)

        UNION

        SELECT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn,
               n.hinta, n.tila, d.nimi AS divari_nimi, n.nide_id AS nide_id
        FROM 
            public.teokset t
        JOIN 
            public.nide n ON t.teos_id = n.teos_id
        JOIN 
            public.divarit d ON n.divari_id = d.divari_id
        WHERE LOWER(t.tekija) = LOWER($1)
          AND LOWER(t.nimi) = LOWER($2)
    ";

    // Parametrisoitu kysely suojaa SQL-injektiolta
    $result = pg_query_params($db, $sql, [$tekija, $nimi]);

    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: []; // Palautetaan niteet tai tyhjä taulukko
}

/**
 * Päivittää yksittäisen niteen tilan "varatuksi".
 * Tätä käytetään kun käyttäjä lisää niteen ostoskoriin.
 *
 * @param int $nide_id Niteen yksilöllinen tunniste (primary key)
 * @param resource $db PostgreSQL-tietokantayhteys
 * @return bool Palauttaa true jos päivitys onnistui, false jos epäonnistui
 */
function varaa_nide($nide_id, $db) {
    $sql = "UPDATE public.nide SET tila = 'varattu' WHERE nide_id = $1";
    $result = pg_query_params($db, $sql, [$nide_id]);
    return ($result !== false);
}
?>
