<!-- nidelisays_process.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php'; 

// Varmistetaan että käyttäjä on kirjautunut.
if (!isset($_SESSION['divari_id'])) {
    $_SESSION['message'] = "Kirjaudu sis&auml;&auml;n lis&auml;t&auml;ksesi niteit&auml;.";
    header("Location: admin_login_popup.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan käyttäjän syöttämä lisättävän teoksen data
    $teos_id = $_POST['teos_id'];
    $divari_id = $_SESSION['divari_id'];
    $hinta = $_POST['hinta'];
    $sisaanostohinta = $_POST['sisaanostohinta'];
    $paino = $_POST['paino'];
        
    try {
        // Lisätään 
        $query1 = "INSERT INTO nide (teos_id, divari_id, tila, hinta, sisaanostohinta, paino) 
                  VALUES ($1, $2, 'myynnissä', $3, $4, $5)";
        $result1 = pg_query_params($db, $query1, [$teos_id, $divari_id, $hinta, $sisaanostohinta, $paino]);
        
        if ($result1) {
            $_SESSION['message'] = "Nide tallennettu onnistuneesti!";
        } else {
            $_SESSION['message'] = "Virhe niteen tallennuksessa.";
        }

    } catch (Exception $e) {
        $_SESSION['message'] = "Virhe: " . $e->getMessage();
    }
    
    // Palataan teoslisays.php -sivulle.
    header("Location: teos_ja_nide_lisays.php");
    exit();
}
?>