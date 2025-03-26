<?php
if (!isset($db)) {
    $y_tiedot = "dbname=hcmape user=hcmape password=salasana_tulee_syottaa";
    $db = pg_connect($y_tiedot);
    if (!$db) {
        die("Tietokantayhteyden luominen epäonnistui: " . pg_last_error());
    }
}
?>
