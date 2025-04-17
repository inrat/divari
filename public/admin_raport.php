<!-- admin_raport.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Hae luokkakooste (kokonaismyynti ja keskihinta)
$sql1 = "
    SELECT t.luokka, 
           SUM(n.hinta) AS kokonaismyynti, 
           ROUND(AVG(n.hinta), 2) AS keskihinta
    FROM teokset t
    JOIN nide n ON t.teos_id = n.teos_id
    WHERE n.tila = 'myynnissä' AND t.luokka IS NOT NULL
    GROUP BY t.luokka
    ORDER BY kokonaismyynti DESC;
";
$result1 = pg_query($db, $sql1);
if (!$result1) {
    die("Tietokantavirhe (kooste): " . pg_last_error($db));
}

// Hae kaikki teokset luokkineen
$sql2 = "
    SELECT t.luokka, t.nimi, t.tekija, n.hinta
    FROM teokset t
    JOIN nide n ON t.teos_id = n.teos_id
    WHERE n.tila = 'myynnissä' AND t.luokka IS NOT NULL
    ORDER BY t.luokka, t.nimi;
";
$result2 = pg_query($db, $sql2);
if (!$result2) {
    die("Tietokantavirhe (teokset): " . pg_last_error($db));
}

// Ryhmittele teokset luokan mukaan
$teokset_luokittain = [];
while ($rivi = pg_fetch_assoc($result2)) {
    $luokka = $rivi['luokka'];
    $teokset_luokittain[$luokka][] = $rivi;
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Raportti: Teokset luokittain</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f39c12;
            color: white;
        }
        h1, h2 {
            text-align: center;
        }
        ul {
            margin-top: 5px;
            margin-bottom: 20px;
            padding-left: 40px;
        }
    </style>
</head>
<body>
    <h1>Myynnissä olevat teokset luokittain</h1>

    <table>
        <tr>
            <th>Luokka</th>
            <th>Kokonaismyynti (€)</th>
            <th>Keskihinta (€)</th>
        </tr>
        <?php while ($row = pg_fetch_assoc($result1)): ?>
            <?php $luokka = $row['luokka']; ?>
            <tr>
                <td><strong><?php echo htmlspecialchars(ucfirst($luokka)); ?></strong></td>
                <td><?php echo number_format($row['kokonaismyynti'], 2, ',', ' '); ?></td>
                <td><?php echo number_format($row['keskihinta'], 2, ',', ' '); ?></td>
            </tr>
            <tr>
                <td colspan="3">
                    <ul>
                        <?php if (isset($teokset_luokittain[$luokka])): ?>
                            <?php foreach ($teokset_luokittain[$luokka] as $teos): ?>
                                <li>
                                    <?php echo htmlspecialchars($teos['tekija']) . ": <em>" . htmlspecialchars($teos['nimi']) . "</em> (" . number_format($teos['hinta'], 2, ',', ' ') . " €)"; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Ei teoksia tässä luokassa.</li>
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
