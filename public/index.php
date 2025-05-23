<!-- index.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Keskusdivari - Kirjautuminen</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <div class="container">
        <h2>Kirjaudu sisään</h2>
        <?php 
        // Näytetään mahdollinen rekisteröinti-ilmoitus
        if (isset($_SESSION['message'])) { 
            echo "<p>" . $_SESSION['message'] . "</p>"; 
            unset($_SESSION['message']); } ?>

        <form action="login.php" method="POST">
            <label for="email">Sähköposti:</label>
            <input type="email" name="email" required>
            <br>
            <label for="password">Salasana:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Kirjaudu</button>
        </form>
        <p>Eikö sinulla ole tiliä? <a href="register.php">Rekisteröidy tästä</a></p>
        <p>Oletko Divarin ylläpitäjä (Admin)? <a href="admin_login_popup.php">Kirjaudu tästä</a></p>
    </div>
</body>
</html>