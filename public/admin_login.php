<!-- admin_login.php -->
<?php session_start(); ?>

require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $divari_id = $_POST['divari_id'];
    $password = $_POST['password'];
    
    $query = "SELECT divari_id, nimi, salasana FROM divarit WHERE divari_id = $1";
    $result = pg_query_params($db, $query, [$divari_id]);

    if ($row = pg_fetch_assoc($result)) {

        if ($password === $row['salasana']) {
            $_SESSION['message'] = "Kirjautuminen onnistui!";
            $_SESSION['nimi'] = $row['nimi'];
            header("Location: admin.php");
            exit();
        } else {
            $_SESSION['message'] = "Virheellinen salasana.";
            header("Location: admin_login_popup.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Sähköpostia ei löydy.";
        header("Location: admin_login_popup.php");
        exit();
    }
}

header("Location: admin_login_popup.php");
exit();
?>