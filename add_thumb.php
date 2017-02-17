<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['upOrDown']) && !empty($_POST['upOrDown'])
          && isset($_POST['id_snap']) && !empty($_POST['id_snap'])
          && isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      $verifThumb = $bdd->prepare('SELECT `upOrDown` FROM `thumbs` WHERE `id_snap` = :id_snap AND `id_user` = ' . $_SESSION['id_user'] . ';');
      $verifThumb->execute(array('id_snap' => $_POST['id_snap']));
      if ($thumbData = $verifThumb->fetch()) {
        if ($_POST['upOrDown'] == 1) {
          if ($thumbData['upOrDown'] == 1) {
            echo json_encode(array("status" => 0, "up" => 0, "down" => 0));
          }
          else {
            $updateSnapshot = $bdd->prepare('UPDATE `snapshots` SET `thumbs_up` = `thumbs_up` + 1, `thumbs_down` = `thumbs_down` - 1 WHERE `snapshots`.`id_snap` = :id_snap;');
            $updateSnapshot->execute(array('id_snap' => $_POST['id_snap']));
            $updateThumbs = $bdd->prepare('UPDATE `thumbs` SET `upOrDown` = 1 WHERE `thumbs`.`id_snap` = :id_snap AND `thumbs`.`id_user` = ' . $_SESSION['id_user'] . ';');
            $updateThumbs->execute(array('id_snap' => $_POST['id_snap']));
            echo json_encode(array("status" => 1, "up" => 1, "down" => -1));
          }
        }
        else {
          if ($thumbData['upOrDown'] == 1) {
            $updateSnapshot = $bdd->prepare('UPDATE `snapshots` SET `thumbs_up` = `thumbs_up` - 1, `thumbs_down` = `thumbs_down` + 1 WHERE `snapshots`.`id_snap` = :id_snap;');
            $updateSnapshot->execute(array('id_snap' => $_POST['id_snap']));
            $updateThumbs = $bdd->prepare('UPDATE `thumbs` SET `upOrDown` = 0 WHERE `thumbs`.`id_snap` = :id_snap AND `thumbs`.`id_user` = ' . $_SESSION['id_user'] . ';');
            $updateThumbs->execute(array('id_snap' => $_POST['id_snap']));
            echo json_encode(array("status" => 1, "up" => -1, "down" => 1));
          }
          else {
            echo json_encode(array("status" => 0, "up" => 0, "down" => 0));
          }
        }
      }
      else {
        if ($_POST['upOrDown'] == 1) {
          $updateSnapshot = $bdd->prepare('UPDATE `snapshots` SET `thumbs_up` = `thumbs_up` + 1 WHERE `snapshots`.`id_snap` = :id_snap;');
          $updateSnapshot->execute(array('id_snap' => $_POST['id_snap']));
          $updateThumbs = $bdd->prepare('INSERT INTO `thumbs` (`upOrDown`, `id_snap`, `id_user`) VALUES ("1", :id_snap, ' . $_SESSION['id_user'] . ');');
          $updateThumbs->execute(array('id_snap' => $_POST['id_snap']));
          echo json_encode(array("status" => 1, "up" => 1, "down" => 0));
        }
        else {
          $updateSnapshot = $bdd->prepare('UPDATE `snapshots` SET `thumbs_down` = `thumbs_down` + 1 WHERE `snapshots`.`id_snap` = :id_snap;');
          $updateSnapshot->execute(array('id_snap' => $_POST['id_snap']));
          $updateThumbs = $bdd->prepare('INSERT INTO `thumbs` (`upOrDown`, `id_snap`, `id_user`) VALUES ("0", :id_snap, ' . $_SESSION['id_user'] . ');');
          $updateThumbs->execute(array('id_snap' => $_POST['id_snap']));
          echo json_encode(array("status" => 1, "up" => 0, "down" => 1));
        }
      }
      $verifThumb->closeCursor();
    }
    else {
      header('Location: index.php');
    }
?>
