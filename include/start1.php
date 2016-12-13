<?php
  if (!isset($_SESSION))
      session_start();
  if (!isset($_SESSION['login']) || $_SESSION['login'] == "")
  	header('Location: connection.php');
?>
