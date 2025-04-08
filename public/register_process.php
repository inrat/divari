<!-- register_process.php -->
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
        // Tarkistetaan, onko sähköposti jo käytössä
        $checkSql = "SELECT * FROM asiakas WHERE email = $1";
        $checkResult = pg_query_params($db, $checkSql, array($email));

        if (pg_num_rows($checkResult) > 0) {
            $_SESSION['message'] = "Sähköpostiosoite on jo rekisteröity. Käytä toista sähköpostia.";
            header("Location: register.php");
            exit();
        }

        // Jos sähköposti ei ole käytössä, jatketaan rekisteröintiä
        $sql = "INSERT INTO asiakas (nimi, email, salasana, osoite, puhelinnumero) 
                VALUES ($1, $2, $3, $4, $5)";
        $params = array($name, $email, $passwordHash, $address, $phone);
        $result = pg_query_params($db, $sql, $params);

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
