<?php
// checkout.php
session_start();

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

    // Käydään läpi kaikki sessiossa olevat niteet
    foreach ($_SESSION['cart'] as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['tekija']) . "</td>";
        echo "<td>" . htmlspecialchars($item['nimi'])   . "</td>";
        echo "<td>" . htmlspecialchars($item['hinta'])  . " €</td>";
        echo "<td>" . htmlspecialchars($item['tila'])   . "</td>";
        echo "<td>" . htmlspecialchars($item['divari']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>

<p>
    <a href="home.php">Takaisin hakuun</a>
</p>
</body>
</html>
