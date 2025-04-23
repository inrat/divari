<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $divari_id = $_SESSION['divari_id'];
    synkronoi_niteet_divariin($db, $divari_id);
    $_SESSION['message'] = "Niteet synkronoitu onnistuneesti.";
    header("Location: admin_synkronointi.php");
    exit();
}

if (!isset($_SESSION['divari_id'])) {
    echo "Et ole kirjautunut sisään.";
    echo "<a href=\"admin_login_popup.php\"> t&auml;st&auml;.</a>";
    exit;
}
$divari_id = $_SESSION['divari_id'];
$schema_name = 'divari_' . $divari_id;

try {
    // Tarkistetaan, että osatietokanta on olemassa.
    $query = "SELECT schema_name
            FROM information_schema.schemata
            WHERE schema_name = $1";
    $result = pg_query_params($db, $query, array($schema_name));
    if (pg_num_rows($result) == 0 || !$result) {
        echo "Skeemaa '$schema_name' ei l&ouml;ydy.";
        exit;
    }
    
    // Haetaan nide-taulun tiedot molemmista tietokannoista.
    $private_nide_query = pg_query($db, "SELECT * FROM {$schema_name}.nide ORDER BY nide_id ASC");
    if (!$private_nide_query) {
        $private_nide_data = [];
        $private_nide_query = null;
    } else {
        $private_nide_data = pg_fetch_all($private_nide_query) ?: [];
        pg_result_seek($private_nide_query, 0);
    }

    $public_nide_query = pg_query_params($db, 
        "SELECT * FROM public.nide WHERE divari_id = $1 ORDER BY nide_id ASC", 
        array($divari_id));
    if (!$public_nide_query) {
        $public_nide_data = [];
        $public_nide_query = null;
    } else {
        $public_nide_data = pg_fetch_all($public_nide_query) ?: [];
        pg_result_seek($public_nide_query, 0);
    }
    
    // Haetaan teokset-taulun tiedot molemmista tietokannoista.
    $private_teokset_query = pg_query($db, "SELECT t.* FROM {$schema_name}.teokset t ORDER BY t.teos_id ASC");
    if (!$private_teokset_query) {
        $private_teokset_data = [];
        $private_teokset_query = null;
    } else {
        $private_teokset_data = pg_fetch_all($private_teokset_query) ?: [];
        pg_result_seek($private_teokset_query, 0);
    }

    $public_teokset_query = pg_query_params($db, 
        "SELECT DISTINCT t.* FROM public.teokset t 
         JOIN public.nide n ON t.teos_id = n.teos_id 
         WHERE n.divari_id = $1 
         ORDER BY t.teos_id ASC", 
        array($divari_id));
    if (!$public_teokset_query) {
        $public_teokset_data = [];
        $public_teokset_query = null;
    } else {
        $public_teokset_data = pg_fetch_all($public_teokset_query) ?: [];
        pg_result_seek($public_teokset_query, 0);
    }
    
} catch (Exception $e) {
    echo "Virhe tietokannan käsittelyssä: " . $e->getMessage();
    exit;
}

// FUnktio, jolla luodaan nide-taulut
function render_nide_table($result, $title, $comparison_data = null) {
    echo "<h2>$title</h2>";
    echo "<table border='1' cellpadding='5' class='result-table'>";
    echo "<tr>
            <th>Nide ID</th>
            <th>Teos ID</th>
            <th>Tila</th>
            <th>Hinta</th>
            <th>Sisäänostohinta</th>
            <th>Paino</th>
            <th>Status</th>
          </tr>";
    
    // Jos niteitä ei ole, näytetään virheviesti.
    if (!$result || pg_num_rows($result) == 0) {
        echo "<tr><td colspan='7'>Ei tietueita.</td></tr>";
    } else {
        while ($row = pg_fetch_assoc($result)) {
            // Luodaan muuttujat synkronoinnin tarkistukselle: löytyykö toisesta taulusta?
            $status = "Synkronoitu";
            $row_class = "";
            
            if ($comparison_data) {
                $found = false;
                foreach ($comparison_data as $comp_row) {
                    // Käytetään tarkistukseen nide_id:tä
                    if ($row['nide_id'] == $comp_row['nide_id']) {
                        $found = true;
                        // Tarkistetaan eroavatko ID:t.
                        $different = false;
                        foreach ($row as $key => $value) {
                            if ($key != 'divari_id' && isset($comp_row[$key]) && $value != $comp_row[$key]) {
                                $different = true;
                                break;
                            }
                        }
                        if ($different) {
                            $status = "Eroaa";
                            $row_class = "class='different'";
                        }
                        break;
                    }
                }
                
                if (!$found) {
                    $status = "Puuttuu toisesta";
                    $row_class = "class='missing'";
                }
            }
            
            echo "<tr $row_class>";
            echo "<td>" . htmlspecialchars($row['nide_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['teos_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['tila'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['hinta'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['sisaanostohinta'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['paino'] ?? 'N/A') . "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
        }
    }
    echo "</table><br>";
}

// Funktio teoksien esittämiselle
function render_teokset_table($result, $title, $comparison_data = null) {
    echo "<h2>$title</h2>";
    echo "<table border='1' cellpadding='5' class='result-table'>";
    echo "<tr>
            <th>Teos ID</th>
            <th>Tekijä</th>
            <th>Nimi</th>
            <th>ISBN</th>
            <th>Julkaisuvuosi</th>
            <th>Tyyppi</th>
            <th>Luokka</th>
            <th>Status</th>
          </tr>";
    
    // Jos teos-tietueita ei löydy, näytetään virheviesti
    if (!$result || pg_num_rows($result) == 0) {
        echo "<tr><td colspan='8'>Ei tietueita.</td></tr>";
    } else {
        while ($row = pg_fetch_assoc($result)) {
            // Tarkistetaan synkronointi: löytyykö toisesta taulusta?
            $status = "Synkronoitu";
            $row_class = "";
            
            if ($comparison_data) {
                $found = false;
                
                // Kokeillaan matchata teokset teos_id mukaan
                foreach ($comparison_data as $comp_row) {
                    if ($row['teos_id'] == $comp_row['teos_id']) {
                        $found = true;
                        // Tarkistetaan eroavatko arvot
                        $different = false;
                        foreach ($row as $key => $value) {
                            if (isset($comp_row[$key]) && $value != $comp_row[$key]) {
                                $different = true;
                                break;
                            }
                        }
                        if ($different) {
                            $status = "Eroaa (ID sama)";
                            $row_class = "class='different'";
                        }
                        break;
                    }
                }
                
                // Jos ID on eri, kokeillaan vielä ISBN vertailua.
                if (!$found && !empty($row['isbn'])) {
                    foreach ($comparison_data as $comp_row) {
                        if (!empty($comp_row['isbn']) && $row['isbn'] == $comp_row['isbn']) {
                            $found = true;
                            $status = "Eroaa (ID eri, ISBN sama)";
                            $row_class = "class='different'";
                            break;
                        }
                    }
                }
                
                if (!$found) {
                    $status = "Puuttuu toisesta kannasta";
                    $row_class = "class='missing'";
                }
            }
            
            echo "<tr $row_class>";
            echo "<td>" . htmlspecialchars($row['teos_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['tekija'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['nimi'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['isbn'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['julkaisuvuosi'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['tyyppi'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['luokka'] ?? 'N/A') . "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
        }
    }
    echo "</table><br>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tietojen vertailu</title>
    <link rel="stylesheet" href="style3.css">
    <style>        
        .legend {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fafafa;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: left;
        }
        
        .legend-item {
            display: inline-block;
            margin-right: 20px;
        }
        
        .legend-color {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .result-table th {
            background-color: #4a4e69;
            color: white;
            text-align: left;
            padding: 8px;
        }
        
        .result-table td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        tr.different {
            background-color: #ffffc7;
        }
        
        tr.missing {
            background-color: #ffdddd;
        }
    </style>
</head>
<body>
    <div class="container">
    <a href="admin.php" class="button">Takaisin Adminiin</a>
        <h1>Tietojen vertailu - <?php echo htmlspecialchars($schema_name); ?> vs. public</h1>
        
        <div class="legend">
            <h3>Selitykset:</h3>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #ffffc7;"></span>
                <span>Tietue eroaa toisesta</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #ffdddd;"></span>
                <span>Tietue puuttuu toisesta</span>
            </div>
        </div>
        
        <div class="section">
            <h2>Nide-tietueet</h2>
            <p>Vertaa divarin nide-tietueita keskustietokannan tietueisiin.</p>
            
            <?php render_nide_table($private_nide_query, "Oma skeema ({$schema_name}) - Nide", $public_nide_data); ?>
            <?php render_nide_table($public_nide_query, "Keskustietokanta (public) - Nide", $private_nide_data); ?>

            <!-- Synkronointinappi -->
            <form method="POST">
                <button type="submit">Synkronoi niteet skeemaan</button>
            </form>
        
        </div>
        
        <div class="section">
            <h2>Teos-tietueet</h2>
            <p>Vertaa divarin teos-tietueita keskustietokannan tietueisiin.</p>
            
            <?php render_teokset_table($private_teokset_query, "Oma skeema ({$schema_name}) - Teokset", $public_teokset_data); ?>
            <?php render_teokset_table($public_teokset_query, "Keskustietokanta (public) - Teokset", $private_teokset_data); ?>
        </div>
    </div>
</body>
</html>