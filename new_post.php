<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['postText']) && !empty($_POST['postText'])
          && isset($_POST['idPrimSnap']) && !empty($_POST['idPrimSnap'])
          && isset($_SESSION['login']) && !empty($_SESSION['login'])
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
      $newPost = $bdd->exec("INSERT INTO `posts` (`id_post`, `text`, `timestamps`, `id_snap`, `id_user`) VALUES (NULL, '" . $_POST['postText'] . "', NOW(), '" . $_POST['idPrimSnap'] . "', '" . $_SESSION['id_user'] . "');");
      if ($newPost) {
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
