<?php

  function connectBDD() {
    require_once('config/database.php');
    try
    {
      $bdd =  new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
      $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $bdd;
    }
    catch(Exception $e)
    {
      die('Error : '.$e->getMessage());
    }
  }

?>
