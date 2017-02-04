<?php
    session_start();

    if (isset($_SESSION['login'])) {
        $_SESSION['login'] = "";
        $_SESSION['rank'] = 0;
        $_SESSION['id_user'] = 0;
    }
    header('Location: index.php');
?>
