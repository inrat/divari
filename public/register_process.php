<!-- register_process.php -->
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['message'] = "Rekisteröinti onnistui! Voit nyt kirjautua sisään.";
    header("Location: index.php");
    exit();
}
?>