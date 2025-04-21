<?php
// Ladataan tietokantayhteyden konfiguraatiot (mm. $db-muuttuja)
require_once __DIR__ . '/../config/config.php';

/**
 * Hakee kirjoja annetulla hakusanalla.
 * Haku suoritetaan kahdesta eri skeemasta: lassen_lehti ja public (Gallein Galle).
 * Palauttaa kirjojen perustiedot, jos ne vastaavat hakukriteeriä.
 *
 * @param string $query Hakusana (esim. kirjailijan nimi, teoksen nimi, luokka tai tyyppi)
 * @param resource $db PostgreSQL-tietokantayhteys
 * @return array Palauttaa löydetyt teokset assosiatiivisena taulukkona
 */
function hae_kirjat($query, $db) {
    $query = trim($query);

    if ($query === '') {
        // Ei hakusanaa → haetaan kaikki teokset molemmista lähteistä
        $sql = "
            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM public.teokset t
            LEFT JOIN public.nide n ON t.teos_id = n.teos_id

            UNION

            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM lassen_lehti.teokset t
            LEFT JOIN lassen_lehti.nide n ON t.teos_id = n.teos_id
        ";
    } else {
        // Hakusana → etsitään molemmista teostietokannoista, niteitä ei rajoiteta
        $sql = "
            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM public.teokset t
            LEFT JOIN public.nide n ON t.teos_id = n.teos_id
            WHERE LOWER(t.tekija) LIKE LOWER('%$query%')
               OR LOWER(t.nimi) LIKE LOWER('%$query%')
               OR LOWER(t.tyyppi) LIKE LOWER('%$query%')
               OR LOWER(t.luokka) LIKE LOWER('%$query%')

            UNION

            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM lassen_lehti.teokset t
            LEFT JOIN lassen_lehti.nide n ON t.teos_id = n.teos_id
            WHERE LOWER(t.tekija) LIKE LOWER('%$query%')
               OR LOWER(t.nimi) LIKE LOWER('%$query%')
               OR LOWER(t.tyyppi) LIKE LOWER('%$query%')
               OR LOWER(t.luokka) LIKE LOWER('%$query%')
        ";
    }

    $result = pg_query($db, $sql);
    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: [];
}

/**
 * Hakee kaikki käytetyt teostyypit tietokannasta.
 * Haku suoritetaan kahdesta eri skeemasta: public (Gallein Galle) ja lassen_lehti.
 * Palauttaa uniikit tyyppi-arvot merkkijonoina, joita voidaan käyttää esimerkiksi suodatusvalikossa.
 *
 * @param resource $db PostgreSQL-tietokantayhteys
 * @return array Palauttaa tyyppi-arvot yksinkertaisena merkkijonotaulukkona
 */
function hae_tyypit($db) {
    $sql = "
        SELECT DISTINCT tyyppi FROM public.teokset
        UNION
        SELECT DISTINCT tyyppi FROM lassen_lehti.teokset
    ";
    $result = pg_query($db, $sql);
    if (!$result) {
        return [];
    }

    $tyypit = [];
    while ($row = pg_fetch_assoc($result)) {
        if (!empty($row['tyyppi'])) {
            $tyypit[] = $row['tyyppi'];
        }
    }

    return $tyypit;
}

/**
 * Hakee kaikki eri luokat tietokannasta yhdistettynä molemmista skeemoista
 *
 * @param resource $db Tietokantayhteys
 * @return array Palauttaa taulukon luokista (esim. ['romaani', 'historia'])
 */
function hae_luokat($db) {
    $luokat = [];
    $sql = "
        SELECT DISTINCT luokka FROM (
            SELECT luokka FROM public.teokset
            UNION
            SELECT luokka FROM lassen_lehti.teokset
        ) AS yhdistetty
        WHERE luokka IS NOT NULL
        ORDER BY luokka;
    ";

    $result = pg_query($db, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $luokat[] = $row['luokka'];
        }
    }

    return $luokat;
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

/**
 * Laskee ostoskorin kokonaispainon ja hakee oikean postikulun
 *
 * @param array $cart Ostoskorin sisältö
 * @param resource $db Tietokantayhteys
 * @return array ['paino' => int, 'hinta' => float, 'postikulu_id' => int|null]
 */
function laske_postikulut($cart, $db) {
    $kokonais_paino = 0;

    // Lasketaan kaikkien tuotteiden yhteispaino
    foreach ($cart as $item) {
        $nide_id = $item['nide_id'];

        $sql = "
            SELECT paino FROM public.nide WHERE nide_id = $1
            UNION
            SELECT paino FROM lassen_lehti.nide WHERE nide_id = $1
            LIMIT 1
        ";
        $result = pg_query_params($db, $sql, [$nide_id]);
        if ($row = pg_fetch_assoc($result)) {
            $kokonais_paino += (int)$row['paino'];
        }
    }

    // Haetaan postikulurivi, jonka max_paino kattaa yhteispainon
    $sql_posti = "
        SELECT postikulu_id, hinta 
        FROM postikulut 
        WHERE max_paino >= $1 
        ORDER BY max_paino ASC 
        LIMIT 1
    ";
    $result_posti = pg_query_params($db, $sql_posti, [$kokonais_paino]);

    if ($result_posti && pg_num_rows($result_posti) > 0) {
        $postikulu = pg_fetch_assoc($result_posti);
        return [
            'paino' => $kokonais_paino,
            'hinta' => (float)$postikulu['hinta'],
            'postikulu_id' => (int)$postikulu['postikulu_id']
        ];
    }

    // Jos ei löytynyt mitään postikuluriviä, palautetaan nollat
    return [
        'paino' => $kokonais_paino,
        'hinta' => 0.00,
        'postikulu_id' => null
    ];
}

?>
