<!-- admin.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Sivu - <?php echo isset($_SESSION['nimi']) ? $_SESSION['nimi'] : 'Not Logged In'; ?></title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <?php if (isset($_SESSION['nimi'])): ?>
        <h1>Hei, <?php echo $_SESSION['nimi']; ?>!</h1>
        <a href="teos_ja_nide_lisays.php" class="button">Lis&auml;&auml; ja tarkastele teoksia</a>
    <?php else: ?>
        <h1>Hei!</h1>
        <p>Et ole kirjautuneena Adminiin. Siirry kirjautumiseen 
            <a href="admin_login_popup.php"> t&auml;st&auml;.</a>
        </p>
    <?php endif; ?>
</body>
</html>