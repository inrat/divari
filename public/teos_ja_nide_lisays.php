<!-- toes_ja_nide_lisays.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Niteiden ja teoksien tarkastelu ja lisäys</title>
    <link rel="stylesheet" href="style3.css">
</head>
</html>

<?php
// Virheviesti tilanteeseen, jossa admin ei ole kirjautuneena. 
if (!isset($_SESSION['divari_id'])) {
    $_SESSION['message'] = "Kirjaudu sis&auml;&auml;n n&auml;hd&auml;ksesi kirjat.";
    header("Location: admin_login_popup.php");
    exit();
}

if (isset($_SESSION['message'])) {
    echo "<p style='font-weight: bold;'>" . htmlspecialchars($_SESSION['message']) . "</p>";
    unset($_SESSION['message']); // estää viestiä näkymästä uudelleen
}

// Määritellään kirjautunut divari
$divari_id = $_SESSION['divari_id'];

// jos kirjautuneena keskusdivari
if (isset($_SESSION['divari_id']) && $_SESSION['divari_id'] == 1) {
	$query = "SELECT n.*, t.nimi AS teos_nimi 
          	  FROM nide n
          	  LEFT JOIN teokset t ON n.teos_id = t.teos_id";
        $result = pg_query($db, $query);
	
} else  {
    // Haetaan kirjautuneen divarin niteet
    $query = "SELECT n.*, t.nimi AS teos_nimi 
              FROM nide n
              LEFT JOIN teokset t ON n.teos_id = t.teos_id
              WHERE n.divari_id = $1";
    $result = pg_query_params($db, $query, [$divari_id]);
}

// Jos niteitä on, tulostetaan ne taulukkona
if (pg_num_rows($result) > 0) {
    echo "<div class='container'>";
        echo "<h2>Divarin " . htmlspecialchars($_SESSION['nimi']) . " niteet:</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Nide ID</th>
                    <th>Teos</th>
                    <th>Tila</th>
                    <th>Hinta (eur)</th>
                    <th>Sis&auml;&auml;nostohinta (eur)</th>
                    <th>Paino (g)</th>
                </tr>";
    
        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['nide_id']}</td>
                    <td>
                        <a href=\"./search.php?id={$row['teos_id']}\">
                            <div style=\"height:100%;width:100%\">
                            " . htmlspecialchars($row['teos_nimi'] ?: 'Tuntematon teos') . "
                            </div>
                        </a>
                    </td>
                    <td>{$row['tila']}</td>
                    <td>{$row['hinta']}</td>
                    <td>{$row['sisaanostohinta']}</td>
                    <td>{$row['paino']}</td>
                </tr>";
        }
        echo "</table>";
    echo "</div>";
} else {
    echo "Ei lis&auml;ttyj&auml; kirjoja.";
}
?>

<div class='container'>
    <!-- Lomake uuden niteen lisäämiselle, aluksi pitää valita teos jonka "alle" nide lisätään.  -->
    <h2>Lis&auml;&auml; uusi nide:</h2>
    <form id="search-form" method="GET" action="">
        <div class='form'>
            <label for="teos_id">Teos ID:</label>
            <input type="text" id="teos_id" name="teos_id">
	    <br>
	    <br>
            <h4 style="margin: 0 10px;">tai</h4>
	    <br>
            <label for="teos_nimi">Teoksen nimi:</label>
            <input type="text" id="teos_nimi" name="teos_nimi">
	    <br>
	    <br>
            <button type="submit" id="search-button">Etsi teos</button>
        </div>
    </form>

    <div id="results">

        <?php
        // Käsitellään virhetilanteita
        if (isset($_GET['teos_id']) || isset($_GET['teos_nimi'])) {
            $teos_id = isset($_GET['teos_id']) ? trim($_GET['teos_id']) : '';
            $teos_nimi = isset($_GET['teos_nimi']) ? trim($_GET['teos_nimi']) : '';

            // Jos etsitään sekä ID:llä että nimellä tulostetaan virhe/ohje.
            if (!empty($teos_id) && !empty($teos_nimi)) {
                echo "<p class='error'>Syöt&auml; vain teos ID tai nimi, ei molempia.</p>";
            
            // Jos ei ole syötetty ID:tä eikä nimeä, tulostetaan  virhe/ohje.
            } elseif (empty($teos_id) && empty($teos_nimi)) {
                echo "<p class='error'>Sy&oumlt&auml; joko teos ID tai nimi.</p>";
            
            } else {
                // jos haetaan teosID:n perusteella, tulostetaan hakuun sopiva teosID
                if (!empty($teos_id)) {
                    if (is_numeric($teos_id)) {
                        $query = "SELECT t.*
                                FROM teokset t 
                                WHERE t.teos_id = $1";
                        $result = pg_query_params($db, $query, [$teos_id]);
                    } else {
                        echo "<p class='error'>Teos ID:n on oltava numeerinen.</p>";
                        $result = false;
                    }
                } else {
                    if (strlen($teos_nimi) >= 3) {
                        $query = "SELECT t.*
                                FROM teokset t 
                                WHERE LOWER(t.nimi) LIKE LOWER($1)";
                        $result = pg_query_params($db, $query, ['%' . $teos_nimi . '%']);
                    } else {
                        echo "<p class='error'>Teoksen nimen on oltava v&auml;hint&auml;&auml;n 3 merkki&auml; pitk&auml;.</p>";
                        $result = false;
                    }
                }
                // Esitetään hakutuloksia vastaavat teokset
                if ($result && pg_num_rows($result) > 0) {
                    echo "<h3>Hakutulokset:</h3>";
                    while ($teos = pg_fetch_assoc($result)) {
                        echo "<div class='result-item' class='container'>";
                            echo "<strong>" . htmlspecialchars($teos['nimi']) . "</strong><br>";
                            echo "Teos ID: " . htmlspecialchars($teos['teos_id']) . "<br>";
                            echo "Tekij&auml;: " . htmlspecialchars($teos['tekija']) . "<br>";
                            echo "Tyyppi: " . htmlspecialchars($teos['tyyppi']) . "<br>";
                            echo "Luokka: " . htmlspecialchars($teos['luokka']) . "<br>";
                            echo "<button type='button' class='select-teos' 
                                data-teos-id='" . $teos['teos_id'] . "' 
                                data-teos-name='" . htmlspecialchars($teos['nimi']) . "'>
                                Valitse t&auml;m&auml; teos</button>";
                        echo "</div>";
                    }
                } elseif ($result) { 
                    echo "<a'>Haettua teosta ei l&ouml;ydy.</a>
                    <button type='button' class='add-teos'>Lis&auml;&auml; uusi teos?</button>";
                }
            }
        }
        ?>
    </div>

    <!-- Lomake niteiden lisäämiselle. -->
    <div id="nide-form" style="<?php echo (isset($_GET['selected_teos']) ? 'display: block;' : 'display: none;'); ?>">
    <form action='nidelisays_process.php' method='POST'>
            <input type="hidden" id="selected_teos_id" name="teos_id" value="">
            <div>
                <label for='valittu_nide'>Valittu teos:</label>
                <strong id="selected_teos_name"'></strong>
            </div>
            <div>
                <label for='hinta'>Hinta (eur):</label>
                <input type='number' id='hinta' name='hinta' step="0.01" min="0" required>
            </div>
            <div>
                <label for='sisaanostohinta'>Sisaanostohinta (eur):</label>
                <input type='number' id='sisaanostohinta' name='sisaanostohinta' step="0.01" min="0" required>
            </div>
            <div>
                <label for='paino'>Paino (g):</label>
                <input type='number' id='paino' name='paino' min="0">
            </div>
            <div>
                <button type='submit'>Lis&auml;&auml; nide</button>
            </div>
        </form>
    </div>

    <!-- Lomake teosten lisäämiselle. -->
    <div id="teos-form" style="display: none;">
        <h3>Lis&auml;&auml; uusi teos</h3>
        <form action='teoslisays_process.php' method='POST'>
            <div>
                <label for='new_tekija'>Tekij&auml;:</label>
                <input type='text' id='new_tekija' name='tekija' required>
            </div>
            <div>
                <label for='new_nimi'>Nimi:</label>
                <input type='text' id='new_nimi' name='nimi' required>
            </div>
            <div>
                <label for='new_isbn'>ISBN:</label>
                <input type='text' id='new_isbn' name='isbn'>
            </div>
            <div>
                <label for='new_julkaisuvuosi'>Julkaisuvuosi:</label>
                <input type='number' id='new_julkaisuvuosi' name='julkaisuvuosi'>
            </div>
            <div>
                <label for='new_tyyppi'>Tyyppi:</label>
                <input type='text' id='new_tyyppi' name='tyyppi'>
            </div>
            <div>
                <label for='new_luokka'>Luokka:</label>
                <input type='text' id='new_luokka' name='luokka'>
            </div>
            <div>
                <button type='submit'>Tallenna teos</button>
            </div>
        </form>
    </div>
</div>

<script>
// Toimintojen seuraus.
document.addEventListener('DOMContentLoaded', function() {
    // Virheviestien tulostus.
    function showError(element, message) {
        // Poistetaan mahdolliset aikaisemmat virheet.
        const existingErrors = element.querySelectorAll('.error');
        existingErrors.forEach(error => error.remove());
        
        const errorElement = document.createElement('p');
        errorElement.className = 'error';
        errorElement.innerHTML = message;
        element.prepend(errorElement);
    }
    
    // Tarkistetaan teoksen hakuun syötettyä dataa
    document.getElementById('search-form').addEventListener('submit', function(e) {
        var teos_id = document.getElementById('teos_id').value.trim();
        var teos_nimi = document.getElementById('teos_nimi').value.trim();
       
        // Tulostetaan virhe: ei syötettyä dataa.
        if ((teos_id === '' && teos_nimi === '') || (teos_id !== '' && teos_nimi !== '')) {
            e.preventDefault();
            showError(this, 'Sy&ouml;t&auml; joko Teos ID tai nimi');
        // Tulostetaan virhe: ID-kentässä muuta kuin numeroita.
        } else if (teos_id !== '' && !(/^\d+$/.test(teos_id))) {
            e.preventDefault();
            showError(this, 'Teos ID:n tulee olla numeerinen');
        // Tulostetaan virhe: nimen tulee olla vähintään 3 merkkiä.
        } else if (teos_nimi !== '' && teos_nimi.length < 3) {
            e.preventDefault();
            showError(this, 'Teoksen nimen tulee olla v&auml;hint&auml;&auml;n 3 merkki&auml;');
        }
    });
    
    // Tarkistetaan niteen luontiin syötettyä dataa.
    document.querySelector('#nide-form form').addEventListener('submit', function(e) {
        const teosId = document.getElementById('selected_teos_id').value.trim();
        const hinta = document.getElementById('hinta').value.trim();
        const sisaanostohinta = document.getElementById('sisaanostohinta').value.trim();
        const paino = document.getElementById('paino').value.trim();
        
        // Tarkista että vaaditut kentät on syötetty.
        if (!teosId || !hinta || !sisaanostohinta) {
            e.preventDefault();
            showError(this, 'Kaikki pakolliset kent&auml;t (teos, hinta, sis&auml;&auml;nostohinta) on t&auml;ytett&auml;v&auml;');
            return;
        }
    });
    
    // Tarkistetaan teoksen luontiin syötettyä dataa.
    document.querySelector('#teos-form form').addEventListener('submit', function(e) {
        const tekija = document.getElementById('new_tekija').value.trim();
        const nimi = document.getElementById('new_nimi').value.trim();
        const isbn = document.getElementById('new_isbn').value.trim();
        const julkaisuvuosi = document.getElementById('new_julkaisuvuosi').value.trim();
        
        // Varmistetaan että pakollinen data on syötetty.
        if (!tekija || !nimi) {
            e.preventDefault();
            showError(this, 'Tekij&auml; ja nimi ovat pakollisia kentti&auml;');
            return;
        }
        
        // Varmistetaan, että ISBN on 13 numeroa pitkä.
        if (isbn && !(/^(?:\d{13})$/.test(isbn))) {
            e.preventDefault();
            showError(this, 'ISBN:n tulee olla 13 numeroa');
            return;
        }
        
        // varmistetaan että syötettävä vuosi on jaa.
        if (julkaisuvuosi) {
            const currentYear = new Date().getFullYear();
            if (parseInt(julkaisuvuosi) < 0 || parseInt(julkaisuvuosi) > currentYear) {
                e.preventDefault();
                showError(this, 'Julkaisuvuoden tulee olla 0 ja ' + currentYear + ' v&auml;lill&auml. Jos teos on ajalta ennen ajanlaskun alkua, ota yhteys sivun yll&auml;pitoon.');
                return;
            }
        }
    });
   
    // Huomioidaan ja tallennetaan käyttäjän tekemät valinnat
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('select-teos')) {
            var teosId = e.target.getAttribute('data-teos-id');
            var teosName = e.target.getAttribute('data-teos-name');
           
            document.getElementById('selected_teos_id').value = teosId;
            document.getElementById('selected_teos_name').textContent = teosName;
            document.getElementById('nide-form').style.display = 'block';
            document.getElementById('search-form').style.display = 'none';
            document.getElementById('results').style.display = 'none';
        }
       
        if (e.target.classList.contains('add-teos')) {
            var searchNimi = document.getElementById('teos_nimi').value.trim();
            if (searchNimi) {
                document.getElementById('new_nimi').value = searchNimi;
            }
            document.getElementById('teos-form').style.display = 'block';
            document.getElementById('search-form').style.display = 'none';
            document.getElementById('results').style.display = 'none';
        }
    });
});
</script>
