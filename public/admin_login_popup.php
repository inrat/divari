<!-- admin-login-popup.php -->
<?php session_start(); ?>
require_once __DIR__ . '/../config/config.php'; 
<!DOCTYPE html>
<html>
<head>
    <title>Keskusdivari - Admin-kirjautuminen</title>
    <h2>Kirjaudu sisään</h2>
    <?php 

    if (isset($_SESSION['message'])) { 
        echo "<p>" . $_SESSION['message'] . "</p>"; 
        unset($_SESSION['message']); } ?>

    <form action="admin_login.php" method="POST">
        <label for="divari_id">Divarin ID:</label>
        <input type="divari_id" name="divari_id" required>
        <br>
        <label for="password">Salasana:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Kirjaudu</button>
    </form>
    </head>
    <body>