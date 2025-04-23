<!-- check_order.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Tarkistetaan että ostoskori ja asiakas on olemassa
if (empty($_SESSION['cart']) || !isset($_SESSION['asiakas_id']) || !isset($_SESSION['tilaustiedot'])) {
    header('Location: checkout.php');
    exit;
}

$asiakas_id = $_SESSION['asiakas_id'];
$cart = $_SESSION['cart'];
$tilaustiedot = $_SESSION['tilaustiedot'];
$postikulu_id = $tilaustiedot['postikulu_id'] ?? null;

// Lisätään tilaus tauluun
$sql = "INSERT INTO tilaus (asiakas_id) VALUES ($1) RETURNING tilaus_id";
$result = pg_query_params($db, $sql, [$asiakas_id]);
if (!$result) {
    die("Tilausta ei voitu tallentaa: " . pg_last_error($db));
}
$tilaus_id = pg_fetch_result($result, 0, 'tilaus_id');

// Lisätään jokainen tuote tilatut_tuotteet -tauluun ja merkitään myydyksi
foreach ($cart as $item) {
    $nide_id = $item['nide_id'];

    // Lisätään yhdistystauluun
    $sql_tt = "INSERT INTO tilatut_tuotteet (tilaus_id, nide_id) VALUES ($1, $2)";
    pg_query_params($db, $sql_tt, [$tilaus_id, $nide_id]);

    // Päivitetään tila
    pg_query_params($db, "UPDATE public.nide SET tila = 'myyty' WHERE nide_id = $1", [$nide_id]);
    pg_query_params($db, "UPDATE {$schema_name}.nide SET tila = 'myyty' WHERE nide_id = $1", [$nide_id]);
}

// Liitetään tilaukseen käytetty postikulu
if ($postikulu_id !== null) {
    $sql_posti = "INSERT INTO tilauksen_postikulut (postikulu_id, tilaus_id) VALUES ($1, $2)";
    $result_posti = pg_query_params($db, $sql_posti, [$postikulu_id, $tilaus_id]);

    if (!$result_posti) {
        die("Virhe postikulun liittämisessä: " . pg_last_error($db));
    }
}

// Tallennetaan tilausnäyttöön tiedot
$_SESSION['viimeisin_tilaus'] = [
    'tilaus_id' => $tilaus_id,
    'tuotteet' => $cart,
    'yhteensa' => $tilaustiedot['yhteensa'],
    'kokonaispaino' => $tilaustiedot['kokonaispaino'],
    'postikulut' => $tilaustiedot['postikulut'],
    'kokonaissumma' => $tilaustiedot['kokonaissumma'],
];

// Tyhjennetään ostoskori ja väliaikaiset tiedot
unset($_SESSION['cart'], $_SESSION['tilaustiedot']);

// Ohjataan kiitossivulle
header("Location: order_confirmation.php");
exit;
?>
