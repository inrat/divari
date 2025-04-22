<!-- asiakkaat.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Asiakkaat</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <!-- Lisätään painike CSV-tiedoston lataamiselle. -->
    <a href="asiakkaat_csv.php" class="button">Vie data CSV-tiedostona</a>
    <!-- Määritellään muuttuja edelliselle vuodelle. -->
    <?php $edellinen_vuosi = date("Y")-1; ?>
    <!-- Lisätään taulukko asiakasdatalle. -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Asiakas ID</th>
                <th>Nimi</th>
                <th>Osoite</th>
                <th>S&auml;hk&ouml;posti</th>
                <th>Puhelinnumero</th>
                <th>Ostettuja niteit&auml; vuonna <?php echo $edellinen_vuosi; ?> </th>
            </tr>
        </thead>
        <tbody> 
            <?php
                $query = "SELECT a.asiakas_id, a.nimi, a.osoite, a.email, a.puhelinnumero, 
                            COUNT(t.nide_id) AS ostetut_niteet
                          FROM asiakas a
                          LEFT JOIN tilaus ti ON a.asiakas_id = ti.asiakas_id
                          LEFT JOIN tilatut_tuotteet t ON ti.tilaus_id = t.tilaus_id
                          WHERE (ti.tilaus_id IS NULL OR EXTRACT(YEAR FROM ti.tilauspvm) = $1)
                          GROUP BY a.asiakas_id, a.nimi, a.osoite, a.email, a.puhelinnumero
                          ORDER BY a.asiakas_id ASC";
                          
                $result = pg_query_params($db, $query, array($edellinen_vuosi));
                   
                if ($result && pg_num_rows($result) > 0) {
                    while ($row = pg_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['asiakas_id']}</td>
                                <td>{$row['nimi']}</td>
                                <td>{$row['osoite']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['puhelinnumero']}</td>
                                <td>{$row['ostetut_niteet']}</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Ei lis&auml;ttyj&auml; asiakkaita.</td></tr>";
                }
            ?>
        </tbody>
    </table>
</body>
</html>