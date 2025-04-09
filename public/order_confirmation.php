<!-- order_confirmation.php -->
<?php
session_start();

// Varmistetaan, että tilausdata on olemassa
if (!isset($_SESSION['viimeisin_tilaus'])) {
    header("Location: home.php");
    exit;
}

$tilaus = $_SESSION['viimeisin_tilaus'];
unset($_SESSION['viimeisin_tilaus']); // Poistetaan kun on näytetty
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tilausvahvistus</title>
</head>
<body>
    <h1>Kiitos tilauksestasi!</h1>
    <p>Tässä on tilausvahvistus, jonka divari lähettäisi sinulle sähköpostitse:</p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Tekijä</th>
            <th>Teos</th>
            <th>Hinta</th>
            <th>Divari</th>
        </tr>
        <?php
        $yhteensa = 0;
        foreach ($tilaus as $item) {
            $yhteensa += (float)$item['hinta'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['tekija']) . "</td>";
            echo "<td>" . htmlspecialchars($item['nimi']) . "</td>";
            echo "<td>" . htmlspecialchars($item['hinta']) . " €</td>";
            echo "<td>" . htmlspecialchars($item['divari']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <p><strong>Yhteensä:</strong> <?php echo number_format($yhteensa, 2); ?> €</p>

    <p>Toimitamme tilauksen ilmoittamaasi osoitteeseen mahdollisimman pian.</p>

    <p><a href="home.php">Palaa etusivulle</a></p>
</body>
</html>
