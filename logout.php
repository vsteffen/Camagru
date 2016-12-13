<?php
    session_start();

    if (isset($_SESSION['login'])) {
        $_SESSION['login'] = "";
        $_SESSION['rank'] = 0;
    }
    header('Location: index.php');
?>
