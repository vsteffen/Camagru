<?php
  if (!isset($_SESSION))
      session_start();
  if (!$_SESSION['login'])
  	header('Location: connexion.php');
?>
