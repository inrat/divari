<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Jos ostoskori on tyhjä, ohjataan takaisin kassalle
if (empty($_SESSION['cart'])) {
    header('Location: checkout.php');
    exit;
}

// Haetaan kirjautuneen asiakkaan tiedot tietokannasta
$asiakas = null;
if (isset($_SESSION['asiakas_id'])) {
    $sql = "SELECT nimi, osoite, email, puhelinnumero FROM asiakas WHERE asiakas_id = $1";
    $result = pg_query_params($db, $sql, [$_SESSION['asiakas_id']]);
    if ($result && pg_num_rows($result) === 1) {
        $asiakas = pg_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tilaus – Yhteenveto</title>
</head>
<body>
    <h1>Tilaus – Yhteenveto</h1>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Tekijä</th>
            <th>Teos</th>
            <th>Hinta</th>
            <th>Divari</th>
        </tr>
        <?php
        $yhteensa = 0;
        foreach ($_SESSION['cart'] as $item) {
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

    <!-- Näytetään asiakastiedot -->
    <?php if ($asiakas): ?>
        <h2>Asiakastiedot</h2>
        <p><strong>Nimi:</strong> <?php echo htmlspecialchars($asiakas['nimi']); ?></p>
        <p><strong>Osoite:</strong> <?php echo htmlspecialchars($asiakas['osoite']); ?></p>
        <p><strong>Sähköposti:</strong> <?php echo htmlspecialchars($asiakas['email']); ?></p>
        <p><strong>Puhelinnumero:</strong> <?php echo htmlspecialchars($asiakas['puhelinnumero']); ?></p>
    <?php else: ?>
        <p><em>Asiakastietoja ei löytynyt.</em></p>
    <?php endif; ?>

    <!-- Tilaus lähetetään seuraavaan vaiheeseen -->
    <form action="check_order.php" method="POST" style="margin-top: 20px;">
        <button type="submit">Vahvista ja maksa tilaus</button>
    </form>

    <p><a href="checkout.php">Palaa takaisin ostoskoriin</a></p>
</body>
</html>
