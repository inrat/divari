<?php
session_start();
require_once __DIR__ . '/../divari/config/config.php';
if (!isset($_SESSION['divari_id'])) {
    $_SESSION['message'] = "Kirjaudu sis&auml;&auml;n nähdäksesi kirjat.";
    header("Location: admin_login_popup.php");
    exit();
}
$divari_id = $_SESSION['divari_id'];

$query = "SELECT n.*, t.nimi AS teos_nimi 
          FROM nide n
          LEFT JOIN teokset t ON n.teos_id = t.teos_id
          WHERE n.divari_id = $1";
          
$result = pg_query_params($db, $query, [$divari_id]);
if (pg_num_rows($result) > 0) {
    echo "<h2>Divarin " . htmlspecialchars($_SESSION['nimi']) . " niteet:</h2>";
    echo "<table border='1'>
            <tr>
                <th>Nide ID</th>
                <th>Teos</th>
                <th>Tila</th>
                <th>Hinta</th>
                <th>Sis&auml;&auml;nostohinta</th>
                <th>Paino</th>
            </tr>";
   
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['nide_id']}</td>
                <td>
                    <a href=\"./teos.php?id={$row['teos_id']}\">
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
} else {
    echo "Ei lis&auml;ttyj&auml; kirjoja.";
}
?>

<h2>Lis&auml;&auml; uusi nide:</h2>
<form action='process_nidelisays.php' method='POST'>
    <div>
        <label for='teos_id'>Teos ID:</label>
        <input type='text' id='teos_id' name='teos_id' required>
    </div>
    <div>
        <label for='hinta'>Hinta:</label>
        <input type='number' id='hinta' name='hinta' required>
    </div>
    <div>
        <label for='sisaanostohinta'>Sisaanostohinta:</label>
        <input type='number' id='sisaanostohinta' name='sisaanostohinta' required>
    </div>
    <div>
        <label for='paino'>Paino:</label>
        <input type='number' id='paino' name='paino'>
    </div>
    <div>
        <button type='submit'>Lis&auml;&auml; nide</button>
    </div>
</form>
</body>
</html>