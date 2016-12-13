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
        </br></br>
      </div>
    </div>
    <footer>
      <div class="site-footer">
        <div class="row">
          <div class="w-12">JE NE SAIS PAS QUOI METTRE LOL</div>
        </div>
      </div>
    </footer>
  </body>
</html>
