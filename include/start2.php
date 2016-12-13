<?php
  if (!isset($_SESSION))
      session_start();
  if (!isset($_SESSION['rank']) || $_SESSION['rank'] != 2)
      header('Location: index.php');
?>
