<?php

function checkStatus($status, &$errors, $mail) {
  if ($status == 0) {
    $errors[] = "Your email address hasn't been validated, an email has been sent to the following address: " . $mail . ".</br>Please follow the instructions to confirm your email address.";
  }
  else if ($status == 2) {
    $errors[] = "Your account is blocked, please contact the support team for more information (camagru.support@XXX.com)";
  }
  return ($errors);
}

    if (!isset($_SESSION))
        session_start();
    if (isset($_SESSION['rank'])) {
      if ($_SESSION['rank'] == 2)
          header('Location: admin.php');
      if ($_SESSION['rank'] == 1)
          header('Location: profile.php');
    }
    if (isset($_SESSION['login']) && $_SESSION['login'] != "")
        header('Location: profile.php');

    if (!empty($_POST)) {
      $wrong = [];
      if (empty($_POST['login']) || empty($_POST['password']))
        $wrong[] = "You must fill all the fields to continue.";
      else {
        require_once('config/database.php');
        try
        {
          $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        }
        catch(Exception $e)
        {
          die('Error : '.$e->getMessage());
        }
        echo hash('sha256', $_POST['password']);
        $dataUser = $bdd->query("SELECT * FROM users WHERE login='" . $_POST['login'] . "' OR mail='" . $_POST['login'] . "';");
        if ($data = $dataUser->fetch()) {
          if (empty(checkStatus($data['status'], $wrong, $data['mail']))) {
            if ($data['login'] == $_POST['login'] || $data['mail'] == $_POST['login'] ) {
              if ($data['pwd'] == hash('sha256', $_POST['password'])) {
                $_SESSION['login'] = $data['login'];
                $_SESSION['rank'] = (int)$data['rank'];
                $dataUser->closeCursor();
                header('Location: index.php');
              }
            }
          }
          else
            $statusKO = 1;
        }
        $dataUser->closeCursor();
        if (!isset($statusKO))
          $wrong[] = "Incorrect login or password.";
      }
    }
?>
<html>
  <head>
  	<title>Camagru - Login</title>
    <link rel="stylesheet" href="./css/global.css">
  	<link rel="stylesheet" href="./css/login.css">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        </br></br>
        <div class="login">
          <div class="login-screen">
            <div class="app-title">
              <h1>Login</h1>
            </div>
            <?php
              if (isset($wrong)) {
                  foreach ($wrong as $key => $value) {
                    echo '<p class="no-align error">' . $value . '</p>';
                  }
              }
            ?>
            <div class="login-form">
              <form action="connection.php" method="post">
                <div class="control-group">
                  <input type="text" class="login-field" name="login" value="<?php if (isset($_POST['login'])) { echo $_POST['login']; } ?>" placeholder="Username or Email" id="login-name">
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
