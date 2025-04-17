<!-- home.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php'; // Funktiot käyttöön
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Keskusdivari - Etusivu</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <!-- Ostoskori-painike -->
    <a href="checkout.php" class="shopping-cart">Ostoskori</a>
    
    <!-- Kirjaudu ulos -painike, sama CSS-luokka mutta eri top-arvo -->
    <a href="logout.php" class="logout-link">Kirjaudu ulos</a>

    <h1>Tervetuloa Keskusdivariin</h1>
    <h2>Haku kirjoille</h2>
    <form action="home.php" method="GET">
        <input type="text" name="q" placeholder="Kirjoita hakusanoja..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit">Hae</button>
    </form>
    <div id="results">
        <?php
        if (isset($_GET['q'])) {
            $query = trim($_GET['q']);

            if (strlen($query) < 3) {
                echo "<p>Hakusanan on oltava vähintään 3 merkkiä pitkä.</p>";
            } else {
                $results = hae_kirjat($query, $db); // Käytetään funktiota hakemiseen

                if ($results) {
                    echo "<h3>Hakutulokset:</h3>";
                    foreach ($results as $book) {
                        echo "<div class='result-item'>";
                        echo "<a href='search.php?tekija=" . urlencode($book['tekija']) . "&nimi=" . urlencode($book['nimi']) . "'>";
                        echo "<strong>" . htmlspecialchars($book['nimi']) . "</strong>";
                        echo "</a><br>";
                        echo "Tekijä: " . htmlspecialchars($book['tekija']) . "<br>";
                        echo "Tyyppi: " . htmlspecialchars($book['tyyppi']) . "<br>";
                        echo "Luokka: " . htmlspecialchars($book['luokka']) . "<br>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Ei tuloksia haulla <strong>" . htmlspecialchars($query) . "</strong>.</p>";
                }
                
            }
        }
        ?>
    </div>
</body>
</html>
