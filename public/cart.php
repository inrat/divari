<!-- cart.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php';

// Varmistetaan, että ostoskori on alustettu
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Luetaan lomakkeelta tulleet tiedot
$nide_id = $_POST['nide_id'] ?? null;
$hinta   = $_POST['hinta']   ?? null;
$tila    = $_POST['tila']    ?? null;
$divari  = $_POST['divari']  ?? null;
$nimi    = $_POST['nimi']    ?? null;
$tekija  = $_POST['tekija']  ?? null;

// Voi olla hyvä tarkistaa, että $nide_id on oikeasti annettu
if (!$nide_id) {
    die("Virhe: nide_id puuttuu!");
}

// Tarkista ensin, että niteen nykyinen tila on "myynnissä"
// Tämä voi olla erillinen SELECT-kysely, jos haluat varmistaa tilan ennen päivitystä
$sql_check = "SELECT tila FROM public.nide WHERE nide_id = $1";
$result_check = pg_query_params($db, $sql_check, [$nide_id]);
if ($result_check) {
    $row = pg_fetch_assoc($result_check);
    if ($row && $row['tila'] !== 'myynnissä') {
        die("Tuotteen tila ei ole myynnissä, ei voida varata.");
    }
} else {
    die("Virhe tilan tarkistuksessa: " . pg_last_error($db));
}

// Vaihdetaan niteen tila "varattu" -tilaan
if (!varaa_nide($nide_id, $db)) {
    die("Niteen varaaminen epäonnistui.");
}

// Lisätään tuote session ostoskoriin
$item = [
    'nide_id' => $nide_id,
    'hinta'   => $hinta,
    'divari'  => $divari,
    'nimi'    => $nimi,
    'tekija'  => $tekija
];

$_SESSION['cart'][] = $item;

// Ohjataan käyttäjä kassalle tai takaisin hakutuloksiin
header('Location: checkout.php');
exit;
?>