<?php
if (!isset($db)) {
    $y_tiedot = "dbname=dcinra user=dcinra password=4zVb9ZVXDIxXudP";
    $db = pg_connect($y_tiedot);
    if (!$db) {
        die("Tietokantayhteyden luominen epäonnistui: " . pg_last_error());
    }
}
?>