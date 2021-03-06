<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['action']) && !empty($_POST['action'])
          && isset($_POST['id_snap']) && !empty($_POST['id_snap'])
          && isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      $action = $_POST['action'];
      $snap = $bdd->prepare('SELECT `id_user`, `scope` FROM `snapshots` WHERE `id_snap` = :id_snap;');
      $snap->execute(array('id_snap' => $_POST['id_snap']));
      if ($snapData = $snap->fetch()) {
        if (($snapData['id_user'] != $_SESSION['id_user']) && $_SESSION['rank'] != 2) {
          echo json_encode(array("status" => 0, "message" => "Don't have rights to change!."));
        }
        else {
          if ($action == 11) {
            if ($snapData['scope'] != 0) {
              $updateSnapshot = $bdd->prepare('UPDATE `snapshots` SET `scope` = "0" WHERE `snapshots`.`id_snap` = :id_snap;');
              $updateSnapshot->execute(array('id_snap' => $_POST['id_snap']));
              echo json_encode(array("status" => 1));
            }
            else {
              echo json_encode(array("status" => 0, "message" => "Already set as private!"));
            }
          }
          else if ($action == 12){
            if ($snapData['scope'] != 1) {
              $updateSnapshot = $bdd->prepare('UPDATE `snapshots` SET `scope` = "1" WHERE `snapshots`.`id_snap` = :id_snap;');
              $updateSnapshot->execute(array('id_snap' => $_POST['id_snap']));
              echo json_encode(array("status" => 1));
            }
            else {
              echo json_encode(array("status" => 0, "message" => "Already set as everyone!"));
            }
          }
          else if ($action == 13){
            if ($snapData['scope'] != 2) {
              $updateSnapshot = $bdd->prepare('UPDATE `snapshots` SET `scope` = "2" WHERE `snapshots`.`id_snap` = :id_snap;');
              $updateSnapshot->execute(array('id_snap' => $_POST['id_snap']));
              echo json_encode(array("status" => 1));
            }
            else {
              echo json_encode(array("status" => 0, "message" => "Already set as members!"));
            }
          }
          else {
            echo json_encode(array("status" => 0, "message" => "Action specified isn't valid."));
          }
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
