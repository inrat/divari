<!-- order_confirmation.php -->
<?php
session_start();

// Haetaan viimeisin tilaus sessiosta
$tilaus = $_SESSION['viimeisin_tilaus'] ?? null;

// Näytetään tarvittaessa virheviesti
if (!$tilaus || !is_array($tilaus['tuotteet'] ?? null)) {
    echo "<p>Tilauksen tietoja ei löytynyt.</p>";
    exit;
}
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
        <?php foreach ($tilaus['tuotteet'] as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['tekija']) ?></td>
                <td><?= htmlspecialchars($item['nimi']) ?></td>
                <td><?= htmlspecialchars($item['hinta']) ?> €</td>
                <td><?= htmlspecialchars($item['divari']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Kokonaispaino:</strong> <?= $tilaus['kokonaispaino'] ?> g</p>
    <p><strong>Postikulut:</strong> <?= number_format($tilaus['postikulut'], 2) ?> €</p>
    <p><strong>Lopullinen summa:</strong> <?= number_format($tilaus['kokonaissumma'], 2) ?> €</p>

    <p><a href="home.php">Palaa etusivulle</a></p>
</body>
</html>

