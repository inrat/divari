<?php
// cart.php

session_start();

// Varmistetaan, että ostoskori on alustettu
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Luetaan lomakkeelta tulleet tiedot
$nide_id = $_POST['nide_id'] ?? null;
$hinta   = $_POST['hinta']   ?? null;
$tila    = $_POST['tila']    ?? null;
$divari  = $_POST['divari']  ?? null;
$nimi    = $_POST['nimi']    ?? null;
$tekija  = $_POST['tekija']  ?? null;

// Voi olla hyvä tarkistaa, että $nide_id on oikeasti annettu
if (!$nide_id) {
    die("Virhe: nide_id puuttuu!");
}

// Kootaan taulukko, joka edustaa yhtä ostoskorissa olevaa nide-alkiota
$item = [
    'nide_id' => $nide_id,
    'hinta'   => $hinta,
    'tila'    => $tila,
    'divari'  => $divari,
    'nimi'    => $nimi,
    'tekija'  => $tekija
];

// Lisätään (push) tuote session cartiin
$_SESSION['cart'][] = $item;

// Voit valita, haluatko ohjata käyttäjän esim. checkout-sivulle vai takaisin hakuun
header('Location: checkout.php'); // tai home.php?q=rom
exit;
