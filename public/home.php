<!-- home.php -->
<?php
session_start();
// Varmista, että käyttäjä on kirjautunut. (Tätä voisi laajentaa autentikoinnilla.)
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Keskusdivari - Etusivu</title>
    <style>
        /* Ostoskoriin pääsyn nappi oikeassa reunassa */
        .shopping-cart {
            position: fixed;
            right: 20px;
            top: 20px;
            background-color: #f39c12;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        /* Perustyyli hakutuloksille */
        .result-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Ostoskori-painike -->
    <a href="checkout.php" class="shopping-cart">Ostoskori</a>

    <h1>Tervetuloa Keskusdivariin</h1>
    <h2>Haku kirjoille</h2>
    <form action="home.php" method="GET">
        <input type="text" name="q" placeholder="Kirjoita hakusanoja..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit">Hae</button>
    </form>
    <div id="results">
        <?php
        // Simuloitu kirjatietojen lista. Oikeassa sovelluksessa nämä tiedot haetaan tietokannasta.
        $books = [
            ['title' => 'Corto Maltese Etelämerellä', 'author' => 'Hugo Pratt', 'type' => 'Sarjakuva', 'category' => 'Seikkailu'],
            ['title' => 'Maltese Falcon', 'author' => 'Dashiell Hammett', 'type' => 'Romaani', 'category' => 'Sikailu'],
            ['title' => 'Maltese Mystery', 'author' => 'Some Author', 'type' => 'Kuvakirja', 'category' => 'Romantiikka']
        ];

        if (isset($_GET['q'])) {
            $query = trim($_GET['q']);
            if (!empty($query)) {
                echo "<h3>Hakutulokset:</h3>";
                $found = false;
                foreach ($books as $book) {
                    // Haetaan hakusana kaikista kentistä (osittainen täsmäys, case-insensitive)
                    if (stripos($book['title'], $query) !== false ||
                        stripos($book['author'], $query) !== false ||
                        stripos($book['type'], $query) !== false ||
                        stripos($book['category'], $query) !== false) {
                        echo "<div class='result-item'>";
                        echo "<strong>" . htmlspecialchars($book['title']) . "</strong><br>";
                        echo "Tekijä: " . htmlspecialchars($book['author']) . "<br>";
                        echo "Tyyppi: " . htmlspecialchars($book['type']) . "<br>";
                        echo "Luokka: " . htmlspecialchars($book['category']) . "<br>";
                        echo "</div>";
                        $found = true;
                    }
                }
                if (!$found) {
                    echo "<p>Ei tuloksia haulla <strong>" . htmlspecialchars($query) . "</strong>.</p>";
                }
            } else {
                echo "<p>Anna hakusana.</p>";
            }
        }
        ?>
    </div>
</body>
</html>
