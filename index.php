<?php
  if (!isset($_SESSION))
    session_start();
?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Camagru</title>
  	<link rel="stylesheet" href="./css/global.css">
  </head>
  <body>
    <!-- php include 'header.php' ?> -->
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <!-- <div class="header">
        <div class="row">
          <div class="w-7">CAMAGRU</div>
          <div class="w-1"><div class="sep"></div></div>
           <!-- <php include 'header_connexion.php' ?> -->
        <!-- </div> -->
      <!-- </div> -->
      <div class="main">
        </br></br></br></br>
        <div class="row">
          <div class="w-9">8</div>
          <div class="w-3">3</div>
        </div>
        </br></br></br></br>
        <div class="row">
          <div class="w-9">8</div>
          <div class="w-3">3</div>
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
