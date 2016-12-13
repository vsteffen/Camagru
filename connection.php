<?php
    if (!isset($_SESSION))
        session_start();
    if (isset($_SESSION['rank'])) {
      if ($_SESSION['rank'] == 2)
          header('Location: admin.php');
      if ($_SESSION['rank'] == 1)
          header('Location: profile.php');
    }
?>
<html>
  <head>
  	<title>Camagru - Connection</title>
  	<link rel="stylesheet" href="./css/global.css">
  </head>
  <body>
    <!-- php include 'header.php' ?> -->
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        </br></br></br>
        <form action="verifLogin.php" method="post">
          <div class="row">
            <div class="w-3">
              Login:
            </div>
            <div class="w-9">
                <input type="text" name="login" value="" placeholder="Enter your login"/>
            </div>
          </div>
          </br></br>
          <div class="row">
            <div class="w-3">
              Password:
            </div>
            <div class="w-9">
                <input type="password" name="password" value="" placeholder="Enter your password"/>
            </div>
          </div>
          </br></br>
          <div class="row">
            <div class="w-12">
              <input type="submit" name="submit" value="OK">
            </div>
          </div>
        </form>
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

<form action="verifLogin.php" method="post">
    Identifiant: <input type="text" name="login" value="" placeholder="login"/>
    Mot de passe: <input type="password" name="passwd" value="" placeholder="password"/>
    <input type="submit" name="submit" value="OK">
</form>
