<!-- admin.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Sivu - <?php echo isset($_SESSION['nimi']) ? $_SESSION['nimi'] : 'Not Logged In'; ?></title>
</head>
<body>
    <?php if (isset($_SESSION['nimi'])): ?>
        <h1>Hei, <?php echo $_SESSION['nimi']; $_SESSION['divari_id'] ?>!</h1>
        <a href="teos_ja_nide_lisays.php" class="button">Lis&auml;&auml; ja tarkastele omia niteit&auml;</a>
        
        <!-- Tarjoa mahdollisuutta tarkastella teoksia ja asiakkaita vain, jos kirjautuneen divarin divari_id = 1,
         eli keskusdivari. -->
        <?php if (isset($_SESSION['divari_id']) && $_SESSION['divari_id'] == 1): ?>
            <a href="admin_raport.php" class="button">Tarkastele myynniss&auml; olevia teoksia</a>
        <?php endif; ?>
        
    <?php else: ?>
        <h1>Hei!</h1>
        <p>Et ole kirjautuneena Adminiin. Siirry kirjautumiseen 
            <a href="admin_login_popup.php"> t&auml;st&auml;.</a>
        </p>

    <?php endif; ?>
</body>
</html>