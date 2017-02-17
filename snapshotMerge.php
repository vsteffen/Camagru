<?php
// http://stackoverflow.com/questions/1397377/combine-2-3-transparent-png-images-on-top-of-each-other-with-php

function addImageToFileSystem($bdd, $img, $user, $id_user) {
  $lastID = $bdd->prepare('SELECT `id_snap_of_user` FROM `snapshots` WHERE `id_user` = :id_user ORDER BY `id_snap_of_user` DESC LIMIT 1');
  $lastID->execute(array('id_user' => $id_user));
  if ($dataLastID = $lastID->fetch()) {
    $id = $dataLastID['id_snap_of_user'] + 1;
  }
  else {
    $id = 1;
  }
  $lastID->closeCursor();

  set_error_handler(function($errno, $errstr, $errfile, $errline ){
      throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
  });
  try {
    imagepng($img, "./image/login/" . $user . "/" . $id . ".png");
  }
  catch (ErrorException $e){
    echo json_encode(array("key1" => 0, "key2" => "Error while saving the picture to filesystem.", "key3" => 0, "key4" => 0));
    exit();
  }
  restore_error_handler();
  return $id;
}

function addImageToDatabase($bdd, $snapPath, $id_user, $id_snap_of_user) {
  $newSnap = $bdd->prepare('INSERT INTO `snapshots` (`id_snap`, `path`, `id_snap_of_user`, `title`, `timestamps`, `thumbs_up`, `thumbs_down`, `scope`, `id_user`) VALUES (NULL, :snapPath, :id_snap_of_user, "", NOW(), 0, 0, 0, :id_user);');
  $newSnap->execute(array('snapPath' => $snapPath, 'id_snap_of_user' => $id_snap_of_user, 'id_user' => $id_user));
    if ($newSnap->rowCount() == 1) {
    return $bdd->lastInsertId();
  }
  else {
    return 0;
  }
}


function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);

        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }


    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['imgBase64']) && !empty($_POST['imgBase64'])
          && isset($_POST['filterPath'])
          && isset($_SESSION['login']) && !empty($_SESSION['login'])
          && isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      $imgDest = $_POST['imgBase64'];
      $imgSrc = $_POST['filterPath'];
      $id_user = $_SESSION['id_user'];
      $login = $_SESSION['login'];

      if ($_POST['streaming'] === true)
        $dest = imagecreatefrompng($imgDest);
      else {
        $substr = substr($imgDest, 11, 4);
        if (substr($substr, 0, 4) === "jpeg") {
          $dest = imagecreatefromjpeg($imgDest);
          $dest = imagescale($dest, 640, 480);
        }
        else if (substr($substr, 0, 3) === "png") {
          $dest = imagecreatefrompng($imgDest);
          $dest = imagescale($dest, 640, 480);
        }
        else {
          echo json_encode(array("key1" => 2, "key2" => "Wrong extension for the file send (we accept .jpeg or .png).", "key3" => 0, "key4" => 0));
          return ;
        }
      }
      if ($imgSrc != "") {
        $src = imagecreatefrompng($imgSrc);
        imagealphablending($src, true);
        imagecopymerge_alpha($dest, $src, 0, 0, 0, 0, 640, 480, 100);
      }

      $idSnap = addImageToFileSystem($bdd, $dest, $login, $id_user);
      $snapPath = "./image/login/" . $login . "/" . $idSnap . ".png";
      if (($idPrimSnap = addImageToDatabase($bdd, $snapPath, $id_user, $idSnap)) == 0)  {
        echo json_encode(array("key1" => 0, "key2" => "Error while inserting the picture in database.", "key3" => 0, "key4" => 0));
        return ;
      }
      $imgEncoded = base64_encode(file_get_contents($snapPath));
      echo json_encode(array("key1" => 1, "key2" => "data:image/png;base64," . $imgEncoded, "key3" => $idSnap, "key4" => $idPrimSnap));
      imagedestroy($dest);
      if ($imgSrc != "")
        imagedestroy($src);
    }
    else {
      header('Location: index.php');
    }
?>
