<!-- admin.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Sivu - <?php echo isset($_SESSION['nimi']) ? $_SESSION['nimi'] : 'Not Logged In'; ?></title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <div class="container">
    <?php if (isset($_SESSION['nimi'])): ?>
        <a href="logout.php" class="logout-link">Kirjaudu ulos</a>
        <h1>Hei, <?php echo $_SESSION['nimi']; $_SESSION['divari_id'] ?>!</h1>
        <a href="teos_ja_nide_lisays.php" class="button">Lis&auml;&auml; ja tarkastele omia niteit&auml;</a>
        <br>
        <?php if (isset($_SESSION['divari_id']) && $_SESSION['divari_id'] == 1): ?>
            <a href="admin_raport.php" class="button">Tarkastele kaikkia myynniss&auml; olevia teoksia</a>
            <br>
            <a href="asiakkaat.php" class="button">Tarkastele asiakkaita</a>
            <br>
        <?php endif; ?>
        
    <?php else: ?>
        <h1>Hei!</h1>
        <p>Et ole kirjautuneena Adminiin. Siirry kirjautumiseen 
            <a href="admin_login_popup.php"> t&auml;st&auml;.</a>
        </p>

    <?php endif; ?>
    </div>
</body>
</html>