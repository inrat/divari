<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php';

$divari_id = $_SESSION['divari_id'] ?? null;
$schema_name = $divari_id ? 'divari_' . $divari_id : 'public'; // Oletetaan public jos ei määritelty
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
    
    <!-- Kirjaudu ulos -painike -->
    <a href="logout.php" class="logout-link">Kirjaudu ulos</a>

    <h1>Tervetuloa Keskusdivariin</h1>
    <h2>Haku kirjoille</h2>
    <form action="home.php" method="GET">
        <input type="text" name="q" placeholder="Kirjoita hakusanoja..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
    
        <!-- Luokkasuodatin -->
        <?php
        $valittu_luokka = $_GET['luokka'] ?? '';

        // Tämä hakee luokat, nyt tallennetaan se muuttujiin
        $luokat = hae_luokat($db, $schema_name);

        $valittu_tyyppi = $_GET['tyyppi'] ?? '';
        $tyypit = hae_tyypit($db, $schema_name);
        ?>

        <select name="luokka">
            <option value="" <?php if ($valittu_luokka === '') echo 'selected'; ?>>Kaikki luokat</option>
            <?php foreach ($luokat as $luokka): ?>
                <option value="<?php echo htmlspecialchars($luokka); ?>"
                    <?php if (strtolower($valittu_luokka) === strtolower($luokka)) echo 'selected'; ?>>
                    <?php echo ucfirst(htmlspecialchars($luokka)); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tyyppi">
            <option value="" <?php if ($valittu_tyyppi === '') echo 'selected'; ?>>Kaikki tyypit</option>
            <?php foreach ($tyypit as $tyyppi): ?>
                <option value="<?php echo htmlspecialchars($tyyppi); ?>"
                    <?php if (strtolower($valittu_tyyppi) === strtolower($tyyppi)) echo 'selected'; ?>>
                    <?php echo ucfirst(htmlspecialchars($tyyppi)); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Hae</button>
    </form>

    <div id="results">
        <?php
            $query = trim($_GET['q'] ?? '');
            $luokka = $_GET['luokka'] ?? '';
            $tyyppi = $_GET['tyyppi'] ?? '';

            $results = hae_kirjat($query, $db, $schema_name);

            if ($luokka !== '') {
                $results = array_filter($results, function($book) use ($luokka) {
                    return strtolower($book['luokka']) === strtolower($luokka);
                });
            }
            if ($tyyppi !== '') {
                $results = array_filter($results, function($book) use ($tyyppi) {
                    return strtolower($book['tyyppi']) === strtolower($tyyppi);
                });
            }

            // Järjestä nimen mukaan aakkosjärjestykseen
            usort($results, function($a, $b) {
                return strcasecmp($a['nimi'], $b['nimi']);
            });

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
                echo "<p>Ei tuloksia annetulla haulla.</p>";
            }    
        ?>
    </div>
</body>
</html>
