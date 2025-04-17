<!-- logout.php -->
<?php
session_start();      // Aloitetaan istunto
session_unset();      // Poistetaan kaikki session muuttujat
session_destroy();    // Tuhoaa session kokonaan

// Nollaa sessiotiedot
setcookie(session_name(), '', time() - 3600);

// Ohjataan käyttäjä takaisin kirjautumissivulle tai kotisivulle
header("Location: index.php");
exit;
?>
