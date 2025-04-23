<?php 
require_once __DIR__ . '/../config/config.php';

$edellinen_vuosi = date("Y")-1;

$query = "
    SELECT 
        a.asiakas_id, 
        a.nimi, 
        a.osoite, 
        a.email, 
        a.puhelinnumero,
        COUNT(CASE 
                  WHEN EXTRACT(YEAR FROM ti.tilauspvm) = $1 
                  THEN t.nide_id 
             END) AS ostetut_niteet
    FROM asiakas a
    LEFT JOIN tilaus ti ON a.asiakas_id = ti.asiakas_id
    LEFT JOIN tilatut_tuotteet t ON ti.tilaus_id = t.tilaus_id
    GROUP BY a.asiakas_id, a.nimi, a.osoite, a.email, a.puhelinnumero
    ORDER BY a.asiakas_id ASC
";

$result = pg_query_params($db, $query, array($edellinen_vuosi));

if ($result && pg_num_rows($result) > 0) { 
    $delimiter = ";"; 
    $filename = "asiakas-data_" . date('Y-m-d') . ".csv"; 
     
    $f = fopen('php://memory', 'w'); 
    $fields = array('asiakas_id', 'nimi', 'osoite', 'email', 'puhelinnumero', $edellinen_vuosi . '_ostetut_niteet'); 
    fputcsv($f, $fields, $delimiter); 
     
    while ($row = pg_fetch_assoc($result)) { 
        $lineData = array(
            $row['asiakas_id'], 
            $row['nimi'], 
            $row['osoite'], 
            $row['email'], 
            $row['puhelinnumero'], 
            $row['ostetut_niteet']
        ); 
        fputcsv($f, $lineData, $delimiter); 
    } 
     
    fseek($f, 0); 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
    fpassthru($f); 
} else {
    $f = fopen('php://memory', 'w');
    fputcsv($f, array('Ei asiakastietoja saatavilla.'), ";");
    fseek($f, 0);
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="tyhja.csv";'); 
    fpassthru($f);
}

exit; 
?>
