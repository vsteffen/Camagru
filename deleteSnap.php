<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['id_snap']) && !empty($_POST['id_snap'])
          && isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {
      require_once('config/database.php');
      try
      {
        $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
      }
      catch(Exception $e)
      {
        die('Error : '.$e->getMessage());
      }
      $snap = $bdd->query("SELECT `id_user` FROM `snapshots` WHERE `id_snap` = " . $_POST['id_snap'] . ";");
      if ($snapData = $snap->fetch()) {
        if ($snapData['id_user'] != $_SESSION['id_user'] && $_SESSION['rank'] != 2) {
          echo json_encode(array("status" => 0, "message" => "Don't have rights to change!."));
        }
        else {
          $bdd->exec("DELETE FROM `snapshots` WHERE `snapshots`.`id_snap` = " . $_POST['id_snap'] . ";");
          echo json_encode(array("status" => 1));
        }
        $snap->closeCursor();
      }
      else {
        echo json_encode(array("status" => 0, "message" => "Snapshot specified isn't valid."));
      }
    }
    else {
      header('Location: index.php');
    }
?>
