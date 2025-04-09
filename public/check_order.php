<!-- check_order.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Varmistetaan, että ostoskori ei ole tyhjä
if (empty($_SESSION['cart'])) {
    header('Location: checkout.php');
    exit;
}

// Merkitään kaikki niteet "myydyiksi"
foreach ($_SESSION['cart'] as $item) {
    $nide_id = $item['nide_id'];

    $sql = "UPDATE public.nide SET tila = 'myyty' WHERE nide_id = $1";
    $result = pg_query_params($db, $sql, [$nide_id]);

    if (!$result) {
        die("Virhe niteen päivittämisessä: " . pg_last_error($db));
    }
}

// Tallennetaan tilauksen yhteenveto sessioon, jotta voidaan näyttää se kiitossivulla
$_SESSION['viimeisin_tilaus'] = $_SESSION['cart'];

// Tyhjennetään ostoskori
$_SESSION['cart'] = [];

// Ohjataan kiitossivulle
header("Location: order_confirmation.php");
exit;
?>
