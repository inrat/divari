<!-- logout.php -->
<?php
session_start();      // Aloitetaan istunto
session_unset();      // Poistetaan kaikki session muuttujat
session_destroy();    // Tuhoaa session kokonaan

// Voit halutessasi myös nollata session-tiedot selainevästeestä:
setcookie(session_name(), '', time() - 3600);

// Ohjataan käyttäjä takaisin kirjautumissivulle tai kotisivulle
header("Location: index.php");
exit;
?>
