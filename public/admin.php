<!-- admin.php -->
<?php session_start(); ?>
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Sivu - <?php echo isset($_SESSION['nimi']) ? $_SESSION['nimi'] : 'Not Logged In'; ?></title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <?php if (isset($_SESSION['nimi'])): ?>
        <h1>Hei, <?php echo $_SESSION['nimi']; ?>!</h1>
    <?php else: ?>
        <h1>Hei!</h1>
    <?php endif; ?>
</body>
</html>