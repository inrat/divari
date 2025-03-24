// Login
<?php
session_start();
require_once 'config.php'; // Yhdistetään tietokantaan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hae käyttäjä tietokannasta
    $query = "SELECT asiakas_id, salasana FROM asiakas WHERE email = $1";
    $result = pg_query_params($db, $query, [$email]);

    if ($row = pg_fetch_assoc($result)) {
        // Tarkistetaan salasana
        if (password_verify($password, $row['salasana'])) {
            $_SESSION['user_id'] = $row['asiakas_id'];
            $_SESSION['message'] = "Kirjautuminen onnistui!";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['message'] = "Virheellinen salasana.";
        }
    } else {
        $_SESSION['message'] = "Sähköpostia ei löydy.";
    }

    header("Location: index.php");
    exit();
}
?>