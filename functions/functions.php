<?php
require_once __DIR__ . '/../config/config.php';

function hae_kirjat($query, $db, $schema_name) {
    $query = trim($query);

    if ($query === '') {
        $sql = "
            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM public.teokset t
            LEFT JOIN public.nide n ON t.teos_id = n.teos_id

            UNION

            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM {$schema_name}.teokset t
            LEFT JOIN {$schema_name}.nide n ON t.teos_id = n.teos_id
        ";
    } else {
        $safe_query = pg_escape_string($db, $query);
        $sql = "
            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM public.teokset t
            LEFT JOIN public.nide n ON t.teos_id = n.teos_id
            WHERE LOWER(t.tekija) LIKE LOWER('%{$safe_query}%')
               OR LOWER(t.nimi) LIKE LOWER('%{$safe_query}%')
               OR LOWER(t.tyyppi) LIKE LOWER('%{$safe_query}%')
               OR LOWER(t.luokka) LIKE LOWER('%{$safe_query}%')

            UNION

            SELECT DISTINCT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn
            FROM {$schema_name}.teokset t
            LEFT JOIN {$schema_name}.nide n ON t.teos_id = n.teos_id
            WHERE LOWER(t.tekija) LIKE LOWER('%{$safe_query}%')
               OR LOWER(t.nimi) LIKE LOWER('%{$safe_query}%')
               OR LOWER(t.tyyppi) LIKE LOWER('%{$safe_query}%')
               OR LOWER(t.luokka) LIKE LOWER('%{$safe_query}%')
        ";
    }

    $result = pg_query($db, $sql);
    if (!$result) {
        die("Virhe SQL-haussa: " . pg_last_error($db));
    }

    return pg_fetch_all($result) ?: [];
}

function hae_tyypit($db, $schema_name) {
    $sql = "
        SELECT DISTINCT tyyppi FROM public.teokset
        UNION
        SELECT DISTINCT tyyppi FROM {$schema_name}.teokset
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

function hae_luokat($db, $schema_name) {
    $luokat = [];
    $sql = "
        SELECT DISTINCT luokka FROM (
            SELECT luokka FROM public.teokset
            UNION
            SELECT luokka FROM {$schema_name}.teokset
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

function hae_kirja_niteet($tekija, $nimi, $db, $schema_name) {
    $sql = "
        SELECT t.tekija, t.nimi, t.tyyppi, t.luokka, t.isbn,
               n.hinta, n.tila, d.nimi AS divari_nimi, n.nide_id AS nide_id
        FROM 
            {$schema_name}.teokset t
        JOIN 
            {$schema_name}.nide n ON t.teos_id = n.teos_id
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

function varaa_nide($nide_id, $db) {
    $sql = "UPDATE public.nide SET tila = 'varattu' WHERE nide_id = $1";
    $result = pg_query_params($db, $sql, [$nide_id]);
    return ($result !== false);
}

function laske_postikulut($cart, $db, $schema_name) {
    $kokonais_paino = 0;

    foreach ($cart as $item) {
        $nide_id = $item['nide_id'];

        $sql = "
            SELECT paino FROM public.nide WHERE nide_id = $1
            UNION
            SELECT paino FROM {$schema_name}.nide WHERE nide_id = $1
            LIMIT 1
        ";
        $result = pg_query_params($db, $sql, [$nide_id]);
        if ($row = pg_fetch_assoc($result)) {
            $kokonais_paino += (int)$row['paino'];
        }
    }

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

    return [
        'paino' => $kokonais_paino,
        'hinta' => 0.00,
        'postikulu_id' => null
    ];
}
?>
