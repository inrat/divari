<?php
session_start();

// Yhteysasetukset tietokantaan (käyttäjän omat tunnukset)
$y_tiedot = "dbname=hcmape user=hcmape password=salasana_täytyy_asettaa";
$db = pg_connect($y_tiedot);

if (!$db) {
    die("Tietokantayhteyden luominen epäonnistui: " . pg_last_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hae lomakkeen tiedot
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Salasanan suojaus
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Valmistellaan SQL-lause
        $sql = "INSERT INTO asiakas (nimi, email, salasana, osoite, puhelinnumero) 
                VALUES ('$name', '$email', '$passwordHash', '$address', '$phone')";

        // Suoritetaan SQL-lause
        $result = pg_query($db, $sql);
        if ($result) {
            $_SESSION['message'] = "Rekisteröinti onnistui! Voit nyt kirjautua sisään.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['message'] = "Tietokantavirhe: " . pg_last_error($db);
            header("Location: register.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Virhe: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>
