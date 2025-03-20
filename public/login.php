<!-- login.php -->
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Oletetaan, että tunnukset kelpaavat
    $_SESSION['message'] = "Kirjautuminen onnistui!";
    header("Location: index.php");
    exit();
}
?>