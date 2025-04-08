<!-- functions.php -->
<?php

// Sisällytä konfiguraatio
require_once __DIR__ . '/../config/config.php';

 function hae_kirjat($query, $db) {
    if (empty($query)) {
        return [];
    }

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

    return pg_fetch_all($result) ?: [];
}

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

    $result = pg_query_params($db, $sql, [$tekija, $nimi]);
    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: [];
}

?>
