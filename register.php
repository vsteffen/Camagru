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
        header('Location: index.php');
    if (!empty($_POST)) {
      $wrong = [];
    if (empty($_POST['login']) || empty($_POST['pass1']) || empty($_POST['pass2'] || empty($_POST['mail'])))
      $wrong[] = "You must fill all the fields to continue.";
    else if ($_POST['pass1'] !== $_POST['pass2'])
      $wrong[] = "Passwords don't match. Please try again.";
    else if (empty(checkEmail($_POST['mail'], $wrong)) && empty(checkLogin($_POST['login'], $wrong)) && empty(checkPassword($_POST['pass1'], $wrong))) {

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      $postMail = htmlentities($_POST['mail']);
      $postLogin = htmlentities($_POST['login']);
      $postPass1 = htmlentities($_POST['pass1']);

      $is_taken = $bdd->prepare('SELECT * FROM `users` WHERE login=:login OR mail=:mail;');
      $is_taken->execute(array('login' => $postLogin, 'mail' => $postMail));

      while ($dataTaken = $is_taken->fetch()) {
        if (!isset($takenMail) && $dataTaken['mail'] == $postMail) {
          $wrong[] = "This email is already associated with an account.";
          $takenMail = 1;
        }
        if (!isset($takenLogin) && $dataTaken['login'] == $postLogin) {
          $wrong[] = "Username is already taken.";
          $takenLogin = 1;
        }
        if (!isset($takenActivated) && $dataTaken['status'] == 0) {
          $wrong[] = "This account isn't active and will be deleted in the next 24 hours after the creation of the account if we have no confirmation by a message who has been sent at \"" . $dataTaken['mail'] . "\".";
          $takenActivated = 1;
        }
      }
      $is_taken->closeCursor();

      if (empty($wrong)) {
        $newUser = $bdd->prepare('INSERT INTO `users` (`id_user`, `login`, `pwd`, `mail`, `avatar`, `localisation`, `status`, `last_log`, `rank`) VALUES (NULL, :login, :pass, :mail, "", "", 0, UTC_TIME(), "1");');
        $newUser->execute(array('login' => $postLogin, 'pass' => hash('sha256', $postPass1), 'mail' => $postMail));

        if ($newUser->rowCount() == 1) {
          header('Location: register_success.php?register=OK&login=' . $postLogin. '&mail=' . $postMail .'');
        }
        $newUser->closeCursor();
        $wrong[] = "Unknown error while registering you in database, please contact Webmaster to report the bug encountered.";
      }
    }
  }
?>
<html>
  <head>
  	<title>Camagru - Register</title>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        </br></br></br>
        <div class="login">
          <div class="loginScreen">
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
            <div class="alignCenter">
              <form action="register.php" method="post">
                <div class="subsection">
                  <input type="text" name="mail" value="<?php if (isset($_POST['mail'])) { echo $_POST['mail']; } ?>" placeholder="Email" id="login-name">
                </div>
                <div class="subsection">
                  <input type="text" name="login" value="<?php if (isset($_POST['login'])) { echo $_POST['login']; } ?>" placeholder="Username" id="login-name">
                </div>
                <div class="subsection">
                  <input type="password" name="pass1" value="" placeholder="Password" id="login-pass">
                </div>
                <div class="subsection">
                  <input type="password" name="pass2" value="" placeholder="Type password again" id="login-pass">
                </div>
                <input class="btn btnClassic" type="submit" name="submit" value="CREATE ACCOUNT">
                </br>
                <a class="smallHref" href="connection.php">Already have an account ? Log in here !</a>
                <a class="smallHref" href="reset_password.php">Lost your password ? Reset it here !</a>
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
