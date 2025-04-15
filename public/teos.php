<?php
session_start();
require_once __DIR__ . '/../divari/config/config.php';

$teos_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($teos_id <= 0) {
    header("Location: home.php");
    exit();
}

$query = "SELECT * FROM teokset WHERE teos_id = $1";
$result = pg_query_params($db, $query, [$teos_id]);

if ($teos = pg_fetch_assoc($result)) {
    // teos löytyi, näytetään yksityiskohdat
    $nimi = $teos['nimi'];
    $tekija = $teos['tekija'];
    $isbn = $teos['isbn'];
    $julkaisuvuosi = $teos['julkaisuvuosi'];
    $tyyppi = $teos['tyyppi'];
    $luokka = $teos['luokka'];

} else {
    // Ei löytynyt
    header("home.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($nimi); ?> - Keskusdivari</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($nimi); ?></h1>
    <img src="/uploads/lataukset/ei_kuvaa.png" alt="Kuvaa ei saatavilla">
    <p>Tekija: <?php echo htmlspecialchars($tekija); ?></p>
    <p>Julkaisuvuosi: <?php echo htmlspecialchars($julkaisuvuosi); ?></p>
    <p>Tyyppi: <?php echo htmlspecialchars($tyyppi); ?></p>
    <p>luokka: <?php echo htmlspecialchars($luokka); ?></p>
</body>
</html>