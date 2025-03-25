// Rekisteröinti 
<?php
session_start();
require_once __DIR__ . '/../config/config.php';

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
            header("Location: index.php");
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
