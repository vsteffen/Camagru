<?php
  if (!isset($_SESSION))
      session_start();
  if ($_SESSION['rank'] != 2)
      header('Location: index.php');
?>
