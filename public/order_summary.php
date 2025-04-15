<!-- order_summary.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php';

// Jos ostoskori on tyhjä, ohjataan takaisin kassalle
if (empty($_SESSION['cart'])) {
    header('Location: checkout.php');
    exit;
}

// Haetaan kirjautuneen asiakkaan tiedot
$asiakas = null;
if (isset($_SESSION['asiakas_id'])) {
    $sql = "SELECT nimi, osoite, email, puhelinnumero FROM asiakas WHERE asiakas_id = $1";
    $result = pg_query_params($db, $sql, [$_SESSION['asiakas_id']]);
    if ($result && pg_num_rows($result) === 1) {
        $asiakas = pg_fetch_assoc($result);
    }
}

// Lasketaan postikulut ja kokonaispaino
$posti = laske_postikulut($_SESSION['cart'], $db);
$postikulu = $posti['hinta'];
$kokonaispaino = $posti['paino'];

// Lasketaan ostoskorin kokonaissumma
$yhteensa = 0;
foreach ($_SESSION['cart'] as $item) {
    $yhteensa += (float)$item['hinta'];
}
$kokonaissumma = $yhteensa + $postikulu;

// Tallenna tilauksen tiedot sessioon seuraavaa vaihetta varten
$_SESSION['tilaustiedot'] = [
    'yhteensa' => $yhteensa,
    'kokonaispaino' => $kokonaispaino,
    'postikulut' => $postikulu,
    'kokonaissumma' => $kokonaissumma,
    'postikulu_id' => $posti['postikulu_id'] ?? null, // jos haettu talteen funktiossa
];
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
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['tekija']) ?></td>
                <td><?= htmlspecialchars($item['nimi']) ?></td>
                <td><?= htmlspecialchars($item['hinta']) ?> €</td>
                <td><?= htmlspecialchars($item['divari']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Yhteensä:</strong> <?= number_format($yhteensa, 2) ?> €</p>
    <p><strong>Kokonaispaino:</strong> <?= $kokonaispaino ?> g</p>
    <p><strong>Postikulut:</strong> <?= number_format($postikulu, 2) ?> €</p>
    <p><strong>Lopullinen summa:</strong> <?= number_format($kokonaissumma, 2) ?> €</p>

    <?php if ($asiakas): ?>
        <h2>Asiakastiedot</h2>
        <p><strong>Nimi:</strong> <?= htmlspecialchars($asiakas['nimi']) ?></p>
        <p><strong>Osoite:</strong> <?= htmlspecialchars($asiakas['osoite']) ?></p>
        <p><strong>Sähköposti:</strong> <?= htmlspecialchars($asiakas['email']) ?></p>
        <p><strong>Puhelinnumero:</strong> <?= htmlspecialchars($asiakas['puhelinnumero']) ?></p>
    <?php else: ?>
        <p><em>Asiakastietoja ei löytynyt.</em></p>
    <?php endif; ?>

    <form action="check_order.php" method="POST" style="margin-top: 20px;">
        <button type="submit">Vahvista ja maksa tilaus</button>
    </form>

    <p><a href="checkout.php">Palaa takaisin ostoskoriin</a></p>
</body>
</html>


