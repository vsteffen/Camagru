<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['upOrDown']) && !empty($_POST['upOrDown'])
          && isset($_POST['id_snap']) && !empty($_POST['id_snap'])
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
      $verifThumb = $bdd->query("SELECT `upOrDown` FROM `thumbs` WHERE `id_snap` = " . $_POST['id_snap'] . " AND `id_user` = " . $_SESSION['id_user'] .";");
      if ($thumbData = $verifThumb->fetch()) {
        if ($_POST['upOrDown'] == 1) {
          if ($thumbData['upOrDown'] == 1) {
            echo json_encode(array("status" => 0, "up" => 0, "down" => 0));
          }
          else {
            $bdd->exec("UPDATE `snapshots` SET `thumbs_up` = `thumbs_up` + 1, `thumbs_down` = `thumbs_down` - 1 WHERE `snapshots`.`id_snap` = " . $_POST['id_snap'] . ";");
            $bdd->exec("UPDATE `thumbs` SET `upOrDown` = 1 WHERE `thumbs`.`id_snap` = " . $_POST['id_snap'] . " AND `thumbs`.`id_user` = " . $_SESSION['id_user'] .";");
            echo json_encode(array("status" => 1, "up" => 1, "down" => -1));
          }
        }
        else {
          if ($thumbData['upOrDown'] == 1) {
            $bdd->exec("UPDATE `snapshots` SET `thumbs_up` = `thumbs_up` - 1, `thumbs_down` = `thumbs_down` + 1 WHERE `snapshots`.`id_snap` = " . $_POST['id_snap'] . ";");
            $bdd->exec("UPDATE `thumbs` SET `upOrDown` = 0 WHERE `thumbs`.`id_snap` = " . $_POST['id_snap'] . " AND `thumbs`.`id_user` = " . $_SESSION['id_user'] .";");
            echo json_encode(array("status" => 1, "up" => -1, "down" => 1));
          }
          else {
            echo json_encode(array("status" => 0, "up" => 0, "down" => 0));
          }
        }
      }
      else {
        if ($_POST['upOrDown'] == 1) {
          $bdd->exec("UPDATE `snapshots` SET `thumbs_up` = `thumbs_up` + 1 WHERE `snapshots`.`id_snap` = " . $_POST['id_snap'] . ";");
          $bdd->exec("INSERT INTO `thumbs` (`upOrDown`, `id_snap`, `id_user`) VALUES ('1', '" . $_POST['id_snap'] . "', '" . $_SESSION['id_user'] . "');");
          echo json_encode(array("status" => 1, "up" => 1, "down" => 0));
        }
        else {
          $bdd->exec("UPDATE `snapshots` SET `thumbs_down` = `thumbs_down` + 1 WHERE `snapshots`.`id_snap` = " . $_POST['id_snap'] . ";");
          $bdd->exec("INSERT INTO `thumbs` (`upOrDown`, `id_snap`, `id_user`) VALUES ('0', '" . $_POST['id_snap'] . "', '" . $_SESSION['id_user'] . "');");
          echo json_encode(array("status" => 1, "up" => 0, "down" => 1));
        }
      }
      $verifThumb->closeCursor();
    }
    else {
      header('Location: index.php');
    }
?>
