<?php
  if (!isset($_SESSION))
    session_start();
?>
<html>
  <head>
  	<title>Account active</title>
  	<link rel="stylesheet" href="./css/global.css">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        </br></br>
        <div class="basic-page">
          <div class="app-title">
            <h1>Your account is active !</h1>
          </div>
          <p class="no-align">Congratulations, you are finally registered in camagru! You can add additional information to your profile in the "Profile" section at the top right when you are <a href="connection.php">logged in</a>.</br></br>Have fun on Camagru ! :)</p>
        </div>
        </br></br>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
