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
          header('Location: index.php');
    }
    if (isset($_SESSION['login']) && $_SESSION['login'] != "")
        header('Location: index.php');

    if (!empty($_POST)) {
      $wrong = [];
      if (empty($_POST['login']) || empty($_POST['password']))
        $wrong[] = "You must fill all the fields to continue.";
      else {
        require_once('config/connect_bdd.php');
        $bdd = connectBDD();
        $postLogin = htmlentities($_POST['login']);

        $dataUser = $bdd->prepare('SELECT * FROM users WHERE login= :login1 OR mail= :login2;');
        $dataUser->execute(array('login1' => $postLogin, 'login2' => $postLogin));
        if ($data = $dataUser->fetch()) {
          if (empty(checkStatus($data['status'], $wrong, $data['mail']))) {
            if ($data['login'] == $postLogin || $data['mail'] == $postLogin ) {
              if ($data['pwd'] == hash('sha256', $_POST['password'])) {
                $_SESSION['login'] = $data['login'];
                $_SESSION['rank'] = (int)$data['rank'];
                $_SESSION['id_user'] = $data['id_user'];
                $_SESSION['id_snap_to_tweet'] = 0;
                $_SESSION['textTweet'] = "";
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
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        </br></br>
        <div class="login">
          <div class="loginScreen">
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
            <div class="alignCenter">
              <form action="connection.php" method="post">
                <div class="subsection">
                  <input type="text" name="login" value="<?php if (isset($_POST['login'])) { echo $_POST['login']; } ?>" placeholder="Username or Email" id="login-name">
                </div>
                <div class="subsection">
                  <input type="password" name="password" value="" placeholder="Password" id="login-pass">
                </div>
                <input class="btn btnClassic" type="submit" name="submit" value="LOGIN">
                <a class="smallHref" href="reset_password.php">Lost your password? Reset it here!</a>
                </br>
                <a class="smallHref" href="register.php">Don't have an account ? Create one here !</a>
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
