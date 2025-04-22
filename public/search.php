<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php';

// Lisää tämä:
$divari_id = $_SESSION['divari_id'] ?? null;
$schema_name = $divari_id ? 'divari_' . $divari_id : 'public';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style3.css">
    <meta charset="UTF-8">
    <title>Teoksen niteet</title>
    <style>
        .copy-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
    <?php
    if (isset($_GET['id'])) {
        // Tarkistetaan haetaanko teosta ID:n perusteella.
        $id = $_GET['id'];
        
        // Päivitä SQL, jos käytät skeemoja tässä
        $sql = "SELECT t.*, n.*, d.nimi as divari_nimi
        FROM public.teokset t
        LEFT JOIN public.nide n ON t.teos_id = n.teos_id
        LEFT JOIN public.divarit d ON n.divari_id = d.divari_id
        WHERE t.teos_id = $1";
 
        $result = pg_query_params($db, $sql, [$id]);
        $results = pg_fetch_all($result);
    }
    elseif (isset($_GET['tekija']) && isset($_GET['nimi'])) {
        $tekija = $_GET['tekija'];
        $nimi   = $_GET['nimi'];

        // Päivitetty: Lisätään $schema_name funktiokutsuun
        $results = hae_kirja_niteet($tekija, $nimi, $db, $schema_name);
    } 
    ?>
    <?php if ($results): ?>
        <?php
            $first = $results[0];
            echo "<h2> Teos: " . htmlspecialchars($first['nimi']) . "</h2>";
            echo "<p>Tekijä: " . htmlspecialchars($first['tekija']) . "</p>";
            echo "<p>Tyyppi: " . htmlspecialchars($first['tyyppi']) . "</p>";
            echo "<p>Luokka: " . htmlspecialchars($first['luokka']) . "</p>";

            if (!empty($first['isbn'])) {
                echo "<p>ISBN: " . htmlspecialchars($first['isbn']) . "</p>";
            }

            echo "<h3>Niteet:</h3>";

            $has_copies = false;
            foreach ($results as $copy) {
                if (isset($copy['nide_id']) && !is_null($copy['nide_id'])) {
                    $has_copies = true;
                    echo "<div class='copy-item'>";
                    echo "Hinta: " . htmlspecialchars($copy['hinta']) . " €<br>";
                    echo "Tila: " . htmlspecialchars($copy['tila']) . "<br>";
                    echo "Divari: " . htmlspecialchars($copy['divari_nimi']) . "<br>";

                    echo "<form action='cart.php' method='POST' style='margin-top: 10px;'>";
                    echo "<input type='hidden' name='nide_id' value='" . htmlspecialchars($copy['nide_id']) . "'>";
                    echo "<input type='hidden' name='hinta' value='" . htmlspecialchars($copy['hinta']) . "'>";
                    echo "<input type='hidden' name='tila' value='" . htmlspecialchars($copy['tila']) . "'>";
                    echo "<input type='hidden' name='divari' value='" . htmlspecialchars($copy['divari_nimi']) . "'>";
                    echo "<input type='hidden' name='nimi' value='" . htmlspecialchars($copy['nimi']) . "'>";
                    echo "<input type='hidden' name='tekija' value='" . htmlspecialchars($copy['tekija']) . "'>";
                    echo "<button type='submit' name='add_to_cart'>Lisää ostoskoriin</button>";
                    echo "</form>";

                    echo "</div>";
                }
            }

            if (!$has_copies) {
                echo "<p> Tälle teokselle ei tällä hetkellä ole niteitä saatavilla. </p>"; 
            }
        ?>
    <?php elseif (isset($_GET['id']) || (isset($_GET['tekija']) && isset($_GET['nimi']))): ?>
        <p>Teoksesta ei löytynyt niteitä ei löytynyt.</p>
    <?php else: ?>
        <p>Teosta ei ole valittuna.</p>
    <?php endif; ?>

    <p><a href="home.php">Takaisin hakutuloksiin</a></p>
    <a href="checkout.php" class="shopping-cart">Ostoskori</a>
    <a href="logout.php" class="logout-link">Kirjaudu ulos</a>
    </div>
</body>
</html>
