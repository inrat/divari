<!-- asiakkaat_csv.php -->
<?php 
require_once __DIR__ . '/../config/config.php';

// luodaan muuttuja edelliselle vuodelle
$edellinen_vuosi = date("Y")-1;
 
// haetaan tietokannasta asiakkaan ja asiakkaan tilaamien tuotteiden määrän
$query = "SELECT a.asiakas_id, a.nimi, a.osoite, a.email, a.puhelinnumero, 
                COUNT(t.nide_id) AS ostetut_niteet
          FROM asiakas a
          LEFT JOIN tilaus ti ON a.asiakas_id = ti.asiakas_id
          LEFT JOIN tilatut_tuotteet t ON ti.tilaus_id = t.tilaus_id
          WHERE (ti.tilaus_id IS NULL OR EXTRACT(YEAR FROM ti.tilauspvm) = $1)
          GROUP BY a.asiakas_id, a.nimi, a.osoite, a.email, a.puhelinnumero
          ORDER BY a.asiakas_id ASC";
$result = pg_query_params($db, $query, array($edellinen_vuosi));
 
// jos tuloksesta löytyy asiakkaita, ruvetaan luomaan .csv -tiedostoa.
if($result && pg_num_rows($result) > 0) { 
    $delimiter = ";"; 
    $filename = "asiakas-data_" . date('Y-m-d') . ".csv"; 
     
    // avataan tiedostoonkirjoitus ja syötetään ensimmäiselle riville datan tyypit
    $f = fopen('php://memory', 'w'); 
    $fields = array('asiakas_id', 'nimi', 'osoite', 'email', 'puhelinnumero', $edellinen_vuosi . '_ostetut_niteet'); 
    fputcsv($f, $fields, $delimiter); 
     
    // Niin kauan kuin tuloksessa on rivejä, tulostetaan ne tiedostoon.
    while($row = pg_fetch_assoc($result)) { 
        $lineData = array($row['asiakas_id'], $row['nimi'], $row['osoite'], $row['email'], $row['puhelinnumero'], $row['ostetut_niteet']); 
        fputcsv($f, $lineData, $delimiter); 
    } 
     
    // Liikutaan takaisin tiedoston alkuun ja lisätään headerit.
    fseek($f, 0); 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
    fpassthru($f); 

} else {
    fputcsv($output, array('Ei asiakastietoja saatavilla.'));
}

exit; 
 
?>