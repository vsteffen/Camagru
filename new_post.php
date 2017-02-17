<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['postText']) && !empty($_POST['postText'])
          && isset($_POST['idPrimSnap']) && !empty($_POST['idPrimSnap'])
          && isset($_SESSION['login']) && !empty($_SESSION['login'])
          && isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      $newPost = $bdd->prepare('INSERT INTO `posts` (`id_post`, `text`, `timestamps`, `id_snap`, `id_user`) VALUES (NULL, :postText, NOW(), :idPrimSnap, ' . $_SESSION['id_user'] . ');');
      $newPost->execute(array('postText' => $_POST['postText'], 'idPrimSnap' => $_POST['idPrimSnap']));
      if ($newPost->rowCount() == 1) {
          echo json_encode(array("status" => 1, "newPostId" => $bdd->lastInsertId()));
      }
      else {
        echo json_encode(array("status" => 0, "errorMessage" => "Error with database while sending post, please contact the support or try again."));
      }
    }
    else {
      header('Location: index.php');
    }
?>
