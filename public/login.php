<!-- login.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hae käyttäjä tietokannasta
    $query = "SELECT asiakas_id, salasana FROM asiakas WHERE email = $1";
    $result = pg_query_params($db, $query, [$email]);

    if ($row = pg_fetch_assoc($result)) {
        $salasana_tietokannassa = $row['salasana'];

        // Tarkistetaan ensin hashattu salasana, sitten selkokielinen (koska osa asiakkaista lisätty käsin tietokantaan)
        if (
            (str_starts_with($salasana_tietokannassa, '$2y$') && password_verify($password, $salasana_tietokannassa)) ||
            $password === $salasana_tietokannassa
        ) {
            $_SESSION['asiakas_id'] = $row['asiakas_id'];
            $_SESSION['message'] = "Kirjautuminen onnistui!";
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['message'] = "Virheellinen salasana.";
        }
    } else {
        $_SESSION['message'] = "Sähköpostia ei löydy.";
    }

    header("Location: home.php");
    exit();
}
?>
