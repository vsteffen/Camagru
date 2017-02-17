<?php

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

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      if (isset($_POST['login']) && !empty($_POST['login'])) {
      $success = 1;

      $user = $bdd->prepare('SELECT `id_user`, `mail`, `status` FROM `users` WHERE login=:login1 OR mail=:login2;');
      $user->execute(array('login1' => $_POST['login'], 'login2' => $_POST['login']));
        if (($userData = $user->fetch()) && $userData['status'] != 0) {
          $tokenQuery = $bdd->query("SELECT * FROM `tokens` WHERE `usage` = 1 AND `id_user` = " . $userData['id_user'] . ";");
          if ($dataToken = $tokenQuery->fetch()) {
            $tokenExist = 1;
          }
          $token = bin2hex(openssl_random_pseudo_bytes(16));

          require_once('./PHPMailer/class.phpmailer.php');
          require_once('./PHPMailer/class.smtp.php');

          $mail = new PHPMailer(); // create a new object
          $mail->IsSMTP(); // enable SMTP
          $mail->Host = "cor-nebula.space";
          // $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
          $mail->SMTPAuth = true; // authentication enabled
          $mail->Username = "camagru@cor-nebula.space";
          $mail->Password = "camagru";
          $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
          $mail->Port = 587; // or 587
          $mail->SMTPOptions = array(
              'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
            )
          );
          $mail->IsHTML(true);
          $mail->SetFrom("camagru@cor-nebula.space");
          $mail->Subject = "Reset password";

          $addressMail = $userData['mail'];

          $mail->Body = "<div style=\"min-width: 500px;margin-right:auto;text-decoration: none;font-family:'Open Sans', Helvetica, Arial;color: black;\">
            <div style=\"width:100%;
            padding:10px 0px 10px 0px;
            background-color:#2F3131;
            margin-left:auto;
            color:#ffffff;
            text-align:center;
            margin-bottom: 30px;\">
                <h2>Password reset request</h2>
            </div>
            <div style=\"width: 80%;
            margin-left:auto;
            margin-right:auto;
            font-size: 16px;\">
              You have recently requested for a reset of your password on Camagru. Please click on the link below to reset it.
              <a style=\"text-decoration: none;\" href=\"http://localhost/Camagru/token_valid.php?token=" . $token . "\"><div style=\"background: #1cacea;
              background-color:#F9BA32;
              -webkit-border-radius: 0;
              -moz-border-radius: 0;
              border-radius: 0px;
              color: #ffffff;
              font-size: 20px;
              padding: 10px 20px 10px 20px;
              width: 150px;
              text-align:center;
              margin: 15px auto 15px auto;\">
                Reset password
            </div></a>
              See you soon, have fun on Camagru !
            </div>
            <div style= \"width:100%;
            padding:10px 0px 10px 0px;
            background-color:#426E86;
            color: white;
            text-align: center;
            font-size: 13px;
            margin-top: 30px;\">
                If you don't have ask this request, you can ignore this message.
            </div>
          </div>";
          $mail->AddAddress($addressMail);

           if(!$mail->Send()) {
            $error[] = "There was an error when sending the email. Please contact support for more information with the following debug message : " . PHP_EOL . "Mailer Error: " . $mail->ErrorInfo;
           }
           else {
             if (!isset($tokenExist)) {
               if (!$bdd->exec("INSERT INTO `tokens` (`id_token`, `usage`, `content`, `expires`, `id_user`) VALUES (NULL, '1', '" . $token . "', NOW() + INTERVAL 24 HOUR, '" . $userData['id_user'] . "');"))
               $error[] = "Error with the database. Please contact the support team.";
             }
             else {
               if (!$bdd->exec("UPDATE `tokens` SET `content` = '" . $token . "', `expires` = NOW() WHERE `tokens`.`id_token` = " . $dataToken['id_token'] . ";"))
                $error[] = "Error with the database. Please contact the support team.";
             }
           }
           $tokenQuery->closeCursor();

        }
      }
    }
?>
<html>
  <head>
  	<title>Camagru - Reset password</title>
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
              <?php
                if (isset($success))
                  echo "<h1>Mail send</h1>";
                else {
                  echo "<h1>Reset password</h1>";
                }
                echo "</div>";
                if (isset($success)) {
                  echo '<p class="no-align">An mail has been send if the login or mail was associated with an account..</p>';
                }
                else {
                  echo '<p class="no-align">Enter your mail or login and we will send you a mail to recover your password.</p>';
                  if (isset($wrong)) {
                    foreach ($wrong as $key => $value) {
                      echo '<p class="no-align error">' . $value . '</p>';
                    }
                  }
                  echo '
                  <div class="alignCenter">
                  <form action="reset_password.php" method="post">
                  <div class="subsection">
                  <input type="text" name="login" value="" placeholder="Mail or login">
                  </div>
                  <input class="btn btnClassic" type="submit" name="submit" value="SEND MAIL">
                  </form>
                  </div>';
                }
            ?>

          </div>
        </div>
        </br></br>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
