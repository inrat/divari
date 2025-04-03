<!-- register.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Rekisteröidy</title>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION['message'])) {
        echo "<p style='color: red; font-weight: bold;'>" . htmlspecialchars($_SESSION['message']) . "</p>";
        unset($_SESSION['message']); // estää viestiä näkymästä uudelleen
    }
    ?>

    <h2>Rekisteröidy</h2>
    <form action="register_process.php" method="POST">
        <label for="name">Nimi:</label>
        <input type="text" name="name" required>
        <br>
        <label for="email">Sähköposti:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Salasana:</label>
        <input type="password" name="password" required>
        <br>
        <label for="address">Osoite:</label>
        <input type="text" name="address" required>
        <br>
        <label for="phone">Puhelinnumero:</label>
        <input type="text" name="phone" required>
        <br>
        <button type="submit">Rekisteröidy</button>
    </form>
</body>
</html>

