<!-- empty_cart.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php';

// Jos ostoskori on jo tyhjä, ohjataan suoraan kassalle tai kotiin
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: checkout.php');
    exit;
}

// Palautetaan kunkin ostoskorin niteen tila takaisin "myynnissä"
foreach ($_SESSION['cart'] as $item) {
    $nide_id = $item['nide_id'];
    // Päivitetään tila "myynnissä"
    $sql = "UPDATE public.nide SET tila = 'myynnissä' WHERE nide_id = $1";
    $result = pg_query_params($db, $sql, [$nide_id]);
    // Voit lisätä virhetarkistuksen, jos haluat varmistaa päivityksen onnistumisen
    if (!$result) {
        error_log("Virhe tilan palautuksessa nide_id: $nide_id - " . pg_last_error($db));
    }
}

// Tyhjennetään ostoskori
$_SESSION['cart'] = [];

// Ohjataan käyttäjä takaisin kassalle tai hakusivulle
header('Location: checkout.php');
exit;
?>
