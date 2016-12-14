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
  	<link rel="stylesheet" href="./css/login.css">
  </head>
  <body>
    <!-- php include 'header.php' ?> -->
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        </br></br></br>
        <div class="login">
          <div class="login-screen">
            <div class="app-title">
              <h1>Login</h1>
            </div>
            <div class="login-form">
              <div class="control-group">
              <input type="text" class="login-field" value="" placeholder="username" id="login-name">
              <label class="login-field-icon fui-user" for="login-name"></label>
              </div>

              <div class="control-group">
              <input type="password" class="login-field" value="" placeholder="password" id="login-pass">
              <label class="login-field-icon fui-lock" for="login-pass"></label>
              </div>

              <a class="btn btn-primary btn-large btn-block" href="#">LOGIN</a>
              <a class="login-link" href="#">Lost your password?</a>
              </br>
              <a class="login-link" href="register.php">Don't have an account ? Create one here !</a>
            </div>
          </div>
        </div>
        </br></br>
      </div>
    </div>
    <footer>
      <div class="site-footer">
        <div class="row">
          <div class="w-4">JE NE SAIS PAS QUOI METTRE LOL</div>
          <div class="w-4 w-solid"></div>
          <div class="w-4">JE NE SAIS PAS QUOI METTRE LOL</div>
        </div>
      </div>
    </footer>
  </body>
</html>
