<!-- teoslisays_process.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php'; 

// Varmistetaan että käyttäjä on kirjautunut.
if (!isset($_SESSION['divari_id'])) {
    $_SESSION['message'] = "Kirjaudu sis&auml;&auml;n lis&auml;t&auml;ksesi teoksia.";
    header("Location: admin_login_popup.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan käyttäjän syöttämä lisättävän teoksen data
    $tekija = $_POST['tekija'];
    $nimi = $_POST['nimi'];
    $isbn = $_POST['isbn'];
    $julkaisuvuosi = $_POST['julkaisuvuosi'];
    $tyyppi = $_POST['tyyppi'];
    $luokka = $_POST['luokka'];
    $divari_id = $_SESSION['divari_id'];
        
    try {
        // Lisätään 
        $query1 = "INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka) 
                  VALUES ($1, $2, $3, $4, $5, $6)";
        $result1 = pg_query_params($db, $query1, [$tekija, $nimi, $isbn, $julkaisuvuosi, $tyyppi, $luokka]);
        
    } catch (Exception $e) {
        $_SESSION['message'] = "Virhe: " . $e->getMessage();
    }
    
    // Palataan teoslisays.php -sivulle.
    header("Location: teos_ja_nide_lisays.php");
    exit();
}
?>