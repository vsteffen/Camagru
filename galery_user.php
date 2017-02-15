<?php
  function getIndexPageSnap($currentPage, $maxSnap, $maxPage, $scope, &$error, $bdd) {
    if ($maxPage < $currentPage) {
      $error[] = "The page specified in the url is incorrect!";
      return -1;
    }
    $beginPage = ($currentPage - 1) * 9;
    $galery = $bdd->query("SELECT * FROM `snapshots` WHERE `scope` " . $scope. " AND `id_snap` <= " . $_SESSION['userPageSnapId'] . " ORDER BY `id_snap` DESC LIMIT " . $beginPage . ", 9;");
    return $galery;
  }

  function getInitialSnapId($scope, $bdd) {
    $firstSnap = $bdd->query("SELECT * FROM `snapshots` WHERE `scope` " . $scope. " ORDER BY `id_snap` DESC LIMIT 1;");
    if ($firstSnapData = $firstSnap->fetch()) {
      $_SESSION['userPageSnapId'] = $firstSnapData['id_snap'];
      $firstSnap->closeCursor();
    }
  }

  function getIndexPageStats($scope, $bdd) {
    if (isset($_GET['page']))
      $GLOBALS['currentPage'] = (int)$_GET['page'];
    else
      $GLOBALS['currentPage'] = 1;

    if (!isset($_SESSION['userPageSnapId'])) {
        getInitialSnapId($scope, $bdd);
    }

    $maxSnapQuery = $bdd->query("SELECT COUNT(*) as `maxSnap` FROM `snapshots` WHERE `scope` " . $scope. " AND `id_snap` <= " . $_SESSION['userPageSnapId'] . ";");
    $maxSnapData = $maxSnapQuery->fetch();
    $GLOBALS['maxSnap'] = $maxSnapData['maxSnap'];
    $maxSnapQuery->closeCursor();

    $GLOBALS['maxPage'] = ceil($GLOBALS['maxSnap'] / 9);
  }

  function getIdUserByLogin($login, $bdd) {
    $user = $bdd->query("SELECT `id_user` FROM `users` WHERE `login` = '" . $login . "' ;");
    if ($userData = $user->fetch())
      $id_user = $userData['id_user'];
    else
      $id_user = 0;
    $user->closeCursor();
    return $id_user;
  }

  if (!isset($_SESSION))
    session_start();

  require_once('config/database.php');
  try
  {
    $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
  }
  catch(Exception $e)
  {
    die('Error : '.$e->getMessage());
  }
  $error = [];
  if (isset($_GET['user']) && !empty($_GET['user'])) {
    $id_userGalery = getIdUserByLogin($_GET['user'], $bdd);
    if (!$id_userGalery) {
      $error[] = "The login specified in the url is incorrect.";
    }
    else {
      if (isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {
        if ($_SESSION['id_user'] == $id_userGalery)
          $scope = ">= 0";
        else
          $scope = "> 0";
      }
      else
        $scope = "= 1";

      $countQuery = $bdd->query("SELECT COUNT(*) as `total` FROM `snapshots` WHERE `scope` " . $scope . " AND `id_user` = " . $id_userGalery . ";");
      $countRowData = $countQuery->fetch();
      $countRow = $countRowData['total'];
      $countQuery->closeCursor();
      if ($countRow == 0) {
        $error[] = "There are no pictures available! Maybe some pictures are viewable if you register on Camgru!";
      }
      else {
        if (isset($_GET['page']) && (int)$_GET['page'] > 1) {
          getIndexPageStats($scope, $bdd);
          $galery = getIndexPageSnap($currentPage, $maxSnap, $maxPage, $scope, $error, $bdd);
        }
        else {
          getInitialSnapId($scope, $bdd);
          getIndexPageStats($scope, $bdd);
          $galery = $bdd->query("SELECT * FROM `snapshots` WHERE `scope` " . $scope . " AND `id_user` = " . $id_userGalery . " ORDER BY `id_snap` DESC LIMIT 0, 9;");
        }
      }
    }
  }
  else
    $error[] = "The login specified in the url is incorrect.";
?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Camagru - Galery user</title>
  	<link rel="stylesheet" href="./css/global.css">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div class="basic-page no-padding">
            <?php
              function getLoginByID($id_user, $bdd) {
                $user = $bdd->query("SELECT `login` FROM `users` WHERE `id_user` = " . $id_user . ";");
                if ($userData = $user->fetch())
                  $login = $userData['login'];
                else
                  $login = "Undefined";
                return $login;
              }

              if (!empty($error))
                echo "  <div id='indexMessage' class='basic-margin'>";
              else
                echo "  <div id='indexMessage'>";
              if (!empty($error)) {
                echo "   <h1><p class='app-title'>Something went wrong ...</p></h1>";
                echo "   <span class='error'>" . $error[0] . "</span>";
              }
              else {
                echo "   <h1><p class='app-title'>Galery of " . $_GET['user'] . "</p></h1>";
                echo "   <span>In this section, you can see photos added by " . $_GET['user'] . ". Click on it to see it in detail!</span>";
              }
              // else {
              //   echo "   <h1><p class='app-title'>Join the Camagru Community</p></h1>";
              //   echo "   <span>Camagru is a community where users can exchange pictures easily and are also being able to add filters to their pictures. <a class='basic-href' href='register.php'>Join us</a>, it only takes a minute!</span>";
              // }
              echo "  </div>";
              if (empty($error)) {
                $thumbnailRow = 1;
                echo " <div class='sepThumbnailHorizontal'></div>";
                echo "<div id='galeryContainer'>";
                while ($galeryData = $galery->fetch()) {
                  if ($thumbnailRow == 4) {
                    echo "<div class='sepThumbnailHorizontal'></div>";
                    $thumbnailRow = 1;
                  }
                  echo "<a href='./see_snap.php?idPrimSnap=" . $galeryData['id_snap'] . "'>
                  <div class=\"thumbnailCase\">
                  <div class=\"thumbnailContent\">
                  <div class=\"thumbnailImg\">
                  <img id=\"snap" . $galeryData['id_snap'] . "\" src=\"" . $galeryData['path'] . "\">
                  </div>";

                  if (!empty($galeryData['title']))
                    echo "<span class=\"thumbnailAuthor\">Title : " . $galeryData['title'] . "</span>";
                  else
                    echo "<span class=\"thumbnailAuthor\">By : " . getLoginByID($galeryData['id_user'], $bdd) . "</span>";

                  echo "<span class=\"thumbnailThumbs\">Thumbs :
                  <div class=\"thumbnailThumbs_up\">" . $galeryData['thumbs_up'] ."</div> / <div class=\"thumbnailThumbs_down\">" . $galeryData['thumbs_down'] . "</div>
                  </span>
                  </div>
                  </div>
                  </a>
                  ";
                  $thumbnailRow++;
                }
                echo "</div>";
                $galery->closeCursor();

              }
            ?>
          <?php
          if (empty($error) && $maxPage > 1) {
            if ($currentPage < 1)
              $currentPage = 1;
            echo "<div id='pagination'>";
            if ($currentPage > 1) {
              echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=1'>&laquo;</a>";
              echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=" . ($currentPage - 1) . "'>&lt;</a>";
            }
            if ($currentPage > 3)
              echo "<a href='javascript:void(0)'>...</a>";
            if ($currentPage > 2)
            echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=" . ($currentPage - 2) . "'>" . ($currentPage - 2) . "</a>";
            if ($currentPage > 1)
              echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=" . ($currentPage - 1) . "'>" . ($currentPage - 1) . "</a>";
            echo "<a class='active' href='javascript:void(0)'>" . $currentPage . "</a>";
            if ($currentPage + 1 <= $maxPage)
              echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=" . ($currentPage + 1) . "'>" . ($currentPage + 1) . "</a>";
            if ($currentPage + 2 <= $maxPage)
              echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=" . ($currentPage + 2) . "'>" . ($currentPage + 2) . "</a>";
            if ($currentPage + 3 <= $maxPage)
              echo "<a href='javascript:void(0)'>...</a>";
            if ($currentPage < $maxPage) {
              echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=" . ($currentPage   + 1) . "'>&gt;</a>";
              echo "<a href='galery_user.php?user=" . $_GET['user'] . "&page=" . $maxPage . "'>&raquo;</a>";
            }
            echo "</div>";
          }
          ?>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
