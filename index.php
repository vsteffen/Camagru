<?php
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
  // if (isset($_SESSION['id_user'] && !empty($_SESSION['id_user']))) {
  //   $galery = $bdd->query("")
  // }
?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Camagru</title>
  	<link rel="stylesheet" href="./css/global.css">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div class="basic-page">
          <p>JE SUIS UNE PAGE BASIC LOL</br></br></p>
          <p>JE SUIS UNE PAGE BASIC LOL</br></br></p>
          <p>JE SUIS UNE PAGE BASIC LOL</br></br></p>
          <p>JE SUIS UNE PAGE BASIC LOL</br></br></p>
        </div>
        </br>
        <?php
          if (empty($_SESSION['login']))
            echo "<p>Var login = EMPTY</p>";
          else {
            echo "<p>Var login = \"" . $_SESSION['login'] . "\"</p>";
          }
          if (empty($_SESSION['rank']))
            echo "<p>Var rank = EMPTY</p>";
          else {
            echo "<p>Var rank = \"" . $_SESSION['rank'] . "\"</p>";
          }
        ?>
        </br></br>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
