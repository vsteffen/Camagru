<?php

function checkPassword($pwd, &$errors) {
  if (strlen($pwd) < 8) {
      $errors[] = "Password too short (8)!";
  }
  if (!preg_match("#[0-9]+#", $pwd)) {
      $errors[] = "Password must include at least one number!";
  }
  if (!preg_match("#[a-zA-Z]+#", $pwd)) {
      $errors[] = "Password must include at least one letter!";
  }
  return ($errors);
}

function checkEmail($mail, &$errors) {
  if (!preg_match("/.+@.+\..+/", $mail))
    $errors[] = "Invalid email!";
  return ($errors);
}

function checkLogin($login, &$errors) {
    if (preg_match("/[^\p{L}-0-9_]/", $login))
      $errors[] = "Username may only contain alphanumeric characters, and '_' character.";
    if (strlen($login) < 3)
      $errors[] = "Login too short (3)!";
    return ($errors);
}

    if (!isset($_SESSION))
        session_start();
    if (!empty($_SESSION['login']))
        header('Location: profile.php');
    if (!empty($_POST)) {
      $wrong = [];
    if (empty($_POST['login']) || empty($_POST['pass1']) || empty($_POST['pass2'] || empty($_POST['mail'])))
      $wrong[] = "You must fill all the fields to continue.";
    else if ($_POST['pass1'] !== $_POST['pass2'])
      $wrong[] = "Passwords don't match. Please try again.";
    else if (empty(checkEmail($_POST['mail'], $wrong)) && empty(checkLogin($_POST['login'], $wrong)) && empty(checkPassword($_POST['pass1'], $wrong))) {
      require_once('config/database.php');
      try
      {
        $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
      }
      catch(Exception $e)
      {
        die('Erreur : '.$e->getMessage());
      }
      if ($bdd->query("SELECT COUNT(*) FROM `users` WHERE mail='" . $_POST['mail'] . "'")->fetchColumn())
        $wrong[] = "This email is already associated with an account.";
      if ($bdd->query("SELECT COUNT(*) FROM `users` WHERE login='" . $_POST['login'] . "'")->fetchColumn())
        $wrong[] = "Username is already taken.";
      if (empty($wrong)) {
        $NewUser = $bdd->exec("INSERT INTO `users` (`id`, `login`, `pwd`, `mail`, `avatar`, `localisation`, `register`, `last_log`, `rank`) VALUES (NULL, '" . $_POST['login'] . "', '" . hash('sha256', $_POST['pass1']) . "', '" . $_POST['mail'] . "', '', '', 0, UTC_TIME(), '1');");
          if ($NewUser == 1)
              header('Location: register_success.php');
          $wrong[] = "Unknown error while registering you in database, please contact Webmaster to report the bug encountered.";
      }
    }
  }
?>
<html>
  <head>
  	<title>Camagru - Register</title>
    <link rel="stylesheet" href="./css/global.css">
  	<link rel="stylesheet" href="./css/login.css">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        </br></br></br>
        <div class="login">
          <div class="login-screen">
            <div class="app-title">
              <h1>Register</h1>
            </div>
            <p class="no-align">You want to join our community? You just need to fill the fields below.</p>
            <?php
              if (isset($wrong)) {
                  foreach ($wrong as $key => $value) {
                    echo '<p class="no-align error">' . $value . '</p>';
                  }
              }
            ?>
            <div class="login-form">
              <form action="register.php" method="post">
                <div class="control-group">
                  <input type="text" class="login-field" name="mail" value="<?php if (isset($_POST['mail'])) { echo $_POST['mail']; } ?>" placeholder="Email" id="login-name">
                  <label class="login-field-icon fui-lock" for="login-name"></label>
                </div>
                <div class="control-group">
                  <input type="text" class="login-field" name="login" value="<?php if (isset($_POST['login'])) { echo $_POST['login']; } ?>" placeholder="Username" id="login-name">
                  <label class="login-field-icon fui-user" for="login-name"></label>
                </div>
                <div class="control-group">
                  <input type="password" class="login-field" name="pass1" value="" placeholder="Password" id="login-pass">
                  <label class="login-field-icon fui-lock" for="login-pass"></label>
                </div>
                <div class="control-group">
                  <input type="password" class="login-field" name="pass2" value="" placeholder="Type password again" id="login-pass">
                  <label class="login-field-icon fui-lock" for="login-pass"></label>
                </div>
                <input class="btn btn-primary btn-large btn-block" type="submit" name="submit" value="CREATE ACCOUNT">
                </br>
                <a class="login-link" href="connection.php">Already have an account ? Log in here !</a>
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