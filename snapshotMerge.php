<?php
// http://stackoverflow.com/questions/1397377/combine-2-3-transparent-png-images-on-top-of-each-other-with-php


function addImageToFileSystem($bdd, $img, $user) {
  $lastID = $bdd->query("SELECT `id_snap` FROM `snapshots` ORDER BY `id_snap` DESC LIMIT 1");
  if ($dataLastID = $lastID->fetch()) {
    $id = $dataLastID['id_snap'];
  }
  else {
    $id = 1;
  }
  $lastID->closeCursor();
  $result = imagepng($img, "./image/login/" . $user . "/" . $id . ".png");
  return $result;
}

function addImageToDatabase($bdd) {
  return ;
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

    // if (!isset($_SESSION))
    //     session_start();
    // if (!empty($_SESSION['login']))
    //     header('Location: profile.php');
    //
    //   print("SALUT LOL");
    //   return ;
    // if (isset($_POST['imgBase64']) && !empty($_POST['imgBase64'])
    //       && isset($_POST['filterPath']) && !empty($_POST['filterPath'])) {

      require_once('config/database.php');
      try
      {
        $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
      }
      catch(Exception $e)
      {
        die('Error : '.$e->getMessage());
      }
      $imgDest = "./image/filter/moi.png";
      $imgSrc = "./image/filter/cadre.png";
      // $imgRaw = substr($_POST['imgBase64'], 22);

      // Create image instances
      // $dest = imagecreatefrompng(base64_decode($imgRaw));
      // $src = imagecreatefrompng($_POST['filterPath']);

      $dest = imagecreatefrompng($imgDest);
      $src = imagecreatefrompng($imgSrc);

      // imagecopymerge($dest, $src, 0, 0, 0, 0, 640, 480, 75);
      imagealphablending($src, true);
      imagecopymerge_alpha($dest, $src, 0, 0, 0, 0, 640, 480, 100);

      // imagealphablending($dest, true);
      // imagesavealpha($dest, true);
      // imagecopy($dest, $src, 0, 0, 0, 0, 100, 100);

      if (!addImageToFileSystem($bdd, $dest, "FFPsyko"))
        return "!:error when saving image to filesystem.";

      header('Content-Type: image/png');
      imagepng($dest);

      // $destRaw = base64_encode($dest);
      // print($dest);
      // header('Content-Type: image/png');
      // imagepng($dest);

      imagedestroy($dest);
      imagedestroy($src);

    // }
?>
