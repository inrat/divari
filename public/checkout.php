<!-- checkout.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php'; // Yhdistetään tietokantaan

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ostoskori</title>
</head>
<body>
<h1>Ostoskori</h1>

<?php
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Ostoskori on tyhjä</p>";
} else {
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr><th>Tekijä</th><th>Nimi</th><th>Hinta</th><th>Tila</th><th>Divari</th></tr>";

    foreach ($_SESSION['cart'] as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['tekija']) . "</td>";
        echo "<td>" . htmlspecialchars($item['nimi'])   . "</td>";
        echo "<td>" . htmlspecialchars($item['hinta'])  . " €</td>";
        echo "<td>" . htmlspecialchars($item['tila'] ?? 'varattu')   . "</td>";
        echo "<td>" . htmlspecialchars($item['divari']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

        // "Tilaa kirjat" -lomake
        echo '
        <form action="order_summary.php" method="POST" style="margin-top: 20px;">
            <button type="submit">Tee tilaus</button>
        </form>
    ';
}
?>

<!-- Tyhjennä ostoskori -nappi, joka ohjaa empty_cart.php-tiedostoon -->
<form action="empty_cart.php" method="POST" style="margin-top: 20px;">
    <button type="submit">Tyhjennä ostoskori</button>
</form>

<p>
    <a href="home.php">Takaisin hakuun</a>
</p>

</body>
</html>
