<?php
  if (!isset($_SESSION))
      session_start();

  if (isset($_GET['token'])) {
    if (empty($_GET['token']))
      header('Location: index.php');
  }
  else
    header('Location: index.php');

  require_once('config/database.php');
  try
  {
    $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
  }
  catch(Exception $e)
  {
    die('Erreur : '.$e->getMessage());
  }
  $result = $bdd->query("SELECT * FROM `tokens` WHERE content = '" . $_GET['token'] . "' AND expires > NOW();");
  if ($data = $result->fetch()) {
    if ($data['usage'] == 0) {
      $bdd->exec("UPDATE `users` SET `status` = '1' WHERE `users`.`id_user` = " . $data['id_user'] . ";");
      $bdd->exec("DELETE FROM `tokens` WHERE `tokens`.`id_token` = " . $data['id_token']. ";");
      header('Location: account_active.php');
      $headerLocSend = 1;
    }
  }
  if (!isset($headerLocSend))
    header('Location: index.php');
?>
