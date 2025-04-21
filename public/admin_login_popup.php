<!-- admin_login_popup.php -->
<?php 
session_start();
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Keskusdivari - Admin-kirjautuminen</title>
    <link rel="stylesheet" href="style3.css">
</head>
</head>
<body>
    <div class="container">    
        <h2>Kirjaudu sis&auml;&auml;n</h2>

        <?php
        if (isset($_SESSION['message'])) { 
            echo "<p>" . $_SESSION['message'] . "</p>"; 
            unset($_SESSION['message']); 
        } 
        ?>

        <form action="admin_login.php" method="POST">
            <label for="divari_id">Divarin ID:</label>
            <input type="text" name="divari_id" required>
            <br>
            <label for="password">Salasana:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Kirjaudu</button>
        </form>
    </div>
</body>
</html>