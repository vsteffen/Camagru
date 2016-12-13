<?php
    session_start();

    if (!$_SESSION['login'])
        $_SESSION['login'] = "";
        $_SESSION['rank'] = 0;
    }
    header('Location: index.php');
?>
