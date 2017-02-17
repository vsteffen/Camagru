<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['title'])
          && isset($_POST['id_snap']) && !empty($_POST['id_snap'])
          && isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      if ($_POST['title'] == "") {
        echo json_encode(array("status" => 0, "message" => "Title is empty, please put at least one character (30 max)."));
      }
      else if (strlen($_POST['title']) > 30)
        echo json_encode(array("status" => 0, "message" => "Title is too long, please put at least 30 character max (1 min)."));
      else {
        $snap = $bdd->prepare('SELECT `id_user` FROM `snapshots` WHERE `id_snap` = :id_snap;');
        $snap->execute(array('id_snap' => $_POST['id_snap']));
        if ($snapData = $snap->fetch()) {
          if ($snapData['id_user'] != $_SESSION['id_user'] && $_SESSION['rank'] != 2) {
            echo json_encode(array("status" => 0, "message" => "Don't have rights to change!."));
          }
          else {
            $updateTitle = $bdd->prepare('UPDATE `snapshots` SET `title` = :title WHERE `snapshots`.`id_snap` = :id_snap;');
            $updateTitle->execute(array('title' => $_POST['title'], 'id_snap' => $_POST['id_snap']));
            echo json_encode(array("status" => 1));
          }
          $snap->closeCursor();
        }
        else {
          echo json_encode(array("status" => 0, "message" => "Snapshot specified isn't valid."));
        }
      }

    }
    else {
      header('Location: index.php');
    }
?>
