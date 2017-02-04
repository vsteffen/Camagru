<?php
// http://stackoverflow.com/questions/1397377/combine-2-3-transparent-png-images-on-top-of-each-other-with-php

function addImageToFileSystem($bdd, $img, $user, $id_user) {
  $lastID = $bdd->query("SELECT `id_snap_of_user` FROM `snapshots` WHERE `id_user` = \"" . $id_user. "\" ORDER BY `id_snap_of_user` DESC LIMIT 1");
  if ($dataLastID = $lastID->fetch()) {
    $id = $dataLastID['id_snap_of_user'] + 1;
  }
  else {
    $id = 1;
  }
  $lastID->closeCursor();
  if (!imagepng($img, "./image/login/" . $user . "/" . $id . ".png"))
    $id = 0;
  return $id;
}

function addImageToDatabase($bdd, $snapPath, $id_user, $id_snap_of_user) {
  $newSnap = $bdd->exec("INSERT INTO `snapshots` (`id_snap`, `path`, `id_snap_of_user`, `timestamps`, `thumbs_up`, `thumbs_down`, `scope`, `id_user`) VALUES (NULL, '". $snapPath . "', " . $id_snap_of_user . " , NOW(), '0', '0', '0', '". $id_user . "');");
  if ($newSnap == 1)
    return $bdd->lastInsertId();
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

        // echo $_SESSION['login'] . " AND " . $_SESSION['id_user'];
        // echo "imgBase64 vaut : " . $_POST['imgBase64'] . PHP_EOL;
        // echo "filterPath vaut : " . $_POST['filterPath'] . PHP_EOL;
        // return ;

    if (empty($_SESSION['login']))
        header('Location: profile.php');

    if (isset($_POST['imgBase64']) && !empty($_POST['imgBase64'])
          && isset($_POST['filterPath'])
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
      $imgDest = $_POST['imgBase64'];
      // $imgDest = "./image/filter/moi.png";
      $imgSrc = $_POST['filterPath'];
      // $imgSrc = "./image/filter/cadre.png";
      $id_user = $_SESSION['id_user'];
      $login = $_SESSION['login'];

      $dest = imagecreatefrompng($imgDest);

      if ($imgSrc != "") {
        $src = imagecreatefrompng($imgSrc);
        imagealphablending($src, true);
        imagecopymerge_alpha($dest, $src, 0, 0, 0, 0, 640, 480, 100);
      }

      if (($idSnap = addImageToFileSystem($bdd, $dest, $login, $id_user)) == 0) {
        echo json_encode(array("key1" => 0, "key2" => "Error while saving the picture to filesystem.", "key3" => 0, "key4" => 0));
        return ;
      }

      $snapPath = "./image/login/" . $login . "/" . $idSnap . ".png";
      if (($idPrimSnap = addImageToDatabase($bdd, $snapPath, $id_user, $idSnap)) == 0)  {
        echo json_encode(array("key1" => 0, "key2" => "Error while inserting the picture in database.", "key3" => 0, "key4" => 0));
        return ;
      }

      $imgEncoded = base64_encode(file_get_contents($snapPath));

// echo $imgEncoded;
      // header('Content-Type: application/json');
      echo json_encode(array("key1" => 1, "key2" => "data:image/png;base64," . $imgEncoded, "key3" => $idSnap, "key4" => $idPrimSnap));

      // header('Content-Type: image/png');
      // imagepng($dest);


      imagedestroy($dest);
      if ($imgSrc != "")
        imagedestroy($src);

    }
    else {
      header('Location: index.php');
    }
?>
