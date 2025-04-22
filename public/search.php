<!-- search.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php';
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
        
        // SQL-kysely tietojen hakuun.
        $sql = "SELECT t.*, n.*, d.nimi as divari_nimi
        FROM teokset t
        LEFT JOIN nide n ON t.teos_id = n.teos_id
        LEFT JOIN divarit d ON n.divari_id = d.divari_id
        WHERE t.teos_id = $1";
 
        $result = pg_query_params($db, $sql, [$id]);
        $results = pg_fetch_all($result);
    }
    // Tarkistetaan, että GET-parametrit ovat olemassa
    elseif (isset($_GET['tekija']) && isset($_GET['nimi'])) {
        $tekija = $_GET['tekija'];
        $nimi   = $_GET['nimi'];

        // Haetaan kirjan ja sen niteiden tiedot
        $results = hae_kirja_niteet($tekija, $nimi, $db);
    } 
    if ($results) {
            // Näytetään ensimmäisestä rivistä teoksen perustiedot
            $first = $results[0];
            echo "<h2> Teos: " . htmlspecialchars($first['nimi']) . "</h2>";
            echo "<p>Tekijä: " . htmlspecialchars($first['tekija']) . "</p>";
            echo "<p>Tyyppi: " . htmlspecialchars($first['tyyppi']) . "</p>";
            echo "<p>Luokka: " . htmlspecialchars($first['luokka']) . "</p>";

            // Näytetään ISBN vain, jos se on ei-tyhjä
            if (!empty($first['isbn'])) {
                echo "<p>ISBN: " . htmlspecialchars($first['isbn']) . "</p>";
            }

            // Listataan kaikki niteet (jokainen rivi results-taulukosta)
            echo "<h3>Niteet:</h3>";

            $has_copies = false;
            foreach ($results as $copy) {
                if (isset($copy['nide_id']) && !is_null($copy['nide_id'])) {
                    $has_copies = true;
                    echo "<div class='copy-item'>";
                    echo "Hinta: " . htmlspecialchars($copy['hinta']) . " €<br>";
                    echo "Tila: " . htmlspecialchars($copy['tila']) . "<br>";
                    echo "Divari: " . htmlspecialchars($copy['divari_nimi']) . "<br>";

                    // Ostoskoriin lisäämislomake
                    echo "<form action='cart.php' method='POST' style='margin-top: 10px;'>";
                    // Varmistetaan, että nide_id on olemassa
                    if(isset($copy['nide_id'])) {
                        echo "<input type='hidden' name='nide_id' value='" . htmlspecialchars($copy['nide_id']) . "'>";
                    }
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
        // Näytetään jos annetut parametrit ovat vääriä.
        } elseif (isset($_GET['id']) || (isset($_GET['tekija']) && isset($_GET['nimi']))) {
            echo "<p>Teoksesta ei löytynyt niteitä ei löytynyt.</p>";
        }
        else {
            // Jos parametrit puuttuvat, ilmoitetaan asiasta
            echo "<p>Teosta ei ole valittuna.</p>";
    }
    ?>
    <p><a href="home.php">Takaisin hakutuloksiin</a></p>
    <a href="checkout.php" class="shopping-cart">Ostoskori</a>
    <a href="logout.php" class="logout-link">Kirjaudu ulos</a>
    </div>
</body>
</html>