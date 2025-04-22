<!-- admin.php -->
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Sivu - <?php echo isset($_SESSION['nimi']) ? $_SESSION['nimi'] : 'Not Logged In'; ?></title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <div class="container">
    <?php if (isset($_SESSION['nimi'])) { ?>
        <?php $divari_id =  $_SESSION['divari_id']; ?>
        <a href="logout.php" class="logout-link">Kirjaudu ulos</a>
        <h1>Hei, <?php echo $_SESSION['nimi']; ?>!</h1>
        <a href="teos_ja_nide_lisays.php" class="button">Lis&auml;&auml; ja tarkastele omia niteit&auml;</a>
        <br>
        <?php if (isset($_SESSION['divari_id']) && $_SESSION['divari_id'] == 1): ?>
            <a href="admin_raport.php" class="button">Tarkastele kaikkia myynniss&auml; olevia teoksia</a>
            <br>
            <a href="asiakkaat.php" class="button">Tarkastele asiakkaita</a>
            <br>
        <?php endif; ?>

        <!-- Tarkistetaan onko kirjautuneella adminilla korreloiva osatietokanta. Näytetään vaihteohtona synkrointi
         vain, jos sellainen löytyy. -->
        <?php  $schema_name = 'divari_' . $divari_id; ?>

        <?php $query = "SELECT schema_name
        FROM information_schema.schemata
        WHERE schema_name = $1"; ?>

        <?php $result = pg_query_params($db, $query, array($schema_name)); ?>

        <?php if ($result && pg_num_rows($result) > 0) { ?>
             <a href="admin_synkronointi.php" class="button">Tarkista keskus- ja oman tietokannan synkronointi </a>
        <?php } ?>
      
<?php } else { ?>
        <h1>Hei!</h1>
        <p>Et ole kirjautuneena Adminiin. Siirry kirjautumiseen 
            <a href="admin_login_popup.php"> t&auml;st&auml;.</a>
        </p>
    <?php } ?>
    </div>
</body>
</html>
