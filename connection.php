<?php
    if (!isset($_SESSION))
        session_start();
    if (isset($_SESSION['rank'])) {
      if ($_SESSION['rank'] == 2)
          header('Location: admin.php');
      if ($_SESSION['rank'] == 1)
          header('Location: profile.php');
    }
    // if ($_POST['login'])
    //   echo "TRES DROLE" . $_POST['login'] . " LOL 1" . PHP_EOL;
    if (isset($_SESSION['login']) && $_SESSION['login'] != "")
        header('Location: profile.php');
    if (empty($_POST))
      $wrong = 0;
    else if (empty($_POST['login']) || empty($_POST['password']))
      $wrong = 1;
    else {
      echo "TRES DROLE" . $_POST['login'] . " LOL 1" . PHP_EOL;
      require_once('config/database.php');
      try
      {
        $bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
      }
      catch(Exception $e)
      {
        die('Erreur : '.$e->getMessage());
      }
      $dataUser = $bdd->query('SELECT * FROM users WHERE login=\'' . $_POST['login'] . '\';');
      if ($data = $dataUsers->fetch()) {
        if ($dataUser['login'] == $_POST['login']) {
          if ($dataUser['password'] == hash(sha256, $_POST['password'])) {
            $_SESSION['login'] = $dataUser['login'];
            $_SESSION['rank'] = (int)$dataUser['droit'];
            $users->closeCursor();
            header('Location: index.php');
          }
        }
      }
      $users->closeCursor();
      $wrong = 2;
    }
?>
<html>
  <head>
  	<title>Camagru - Login</title>
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
            <?php
              if ($wrong == 1)
                echo '<p class="no-align error">You must fill all the fields to continue</p>';
              if ($wrong == 2)
                echo '<p class="no-align error">Incorrect login or password.</p>';
            ?>
            <div class="login-form">
              <form action="connection.php" method="post">
                <div class="control-group">
                  <input type="text" class="login-field" name="login" value="" placeholder="Username" id="login-name">
                  <label class="login-field-icon fui-user" for="login-name"></label>
                </div>
                <div class="control-group">
                  <input type="password" class="login-field" name="password" value="" placeholder="Password" id="login-pass">
                  <label class="login-field-icon fui-lock" for="login-pass"></label>
                </div>
                <input class="btn btn-primary btn-large btn-block" type="submit" name="submit" value="LOGIN">
                <a class="login-link" href="#">Lost your password?</a>
                </br>
                <a class="login-link" href="register.php">Don't have an account ? Create one here !</a>
              </form>
            </div>
          </div>
        </div>
        </br></br>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
