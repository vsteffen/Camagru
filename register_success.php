<?php
  if (!isset($_SESSION))
      session_start();

  if (isset($_SESSION['rank'])) {
    if ($_SESSION['rank'] > 0)
      header('Location: index.php');
  }

  if (isset($_GET['login']) || isset($_GET['mail']) || isset($_GET['register'])) {
    if (empty($_GET['login']) || empty($_GET['mail']) || empty($_GET['register']))
      header('Location: index.php');
    if ($_GET['register'] != 'OK')
      header('Location: index.php');
  }
  else
    header('Location: index.php');

  require_once('config/connect_bdd.php');
  $bdd = connectBDD();

// --------------- VERIFY USER -------------
  $dataUser = $bdd->prepare('SELECT status, id_user FROM users WHERE login=:login OR mail=:mail;');
  $dataUser->execute(array('login' => $_GET['login'], 'mail' => $_GET['mail']));
  if ($data = $dataUser->fetch()) {
    if ($data['status'] != 0) {
      $dataUser->closeCursor();
      header('Location: index.php');
    }
    $id_user = $data['id_user'];
  }
  else {
    $dataUser->closeCursor();
    header('Location: index.php');
  }
  $dataUser->closeCursor();

// ---------------- GET TOKEN ---------
  $token_query = $bdd->prepare('SELECT * FROM `tokens` WHERE `usage` = 0 AND `id_user` = :id_user;');
  $token_query->execute(array('id_user' => $id_user));
  if ($dataToken = $token_query->fetch()) {
    $token = $dataToken['content'];
    $tokenExist = 1;
  }
  else
    $token = bin2hex(openssl_random_pseudo_bytes(16));
  $token_query->closeCursor();

// ---------------- MAIL --------------
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
  $mail->Subject = "Welcome to Camagru";

  $addressMail = $_GET['mail'];

  $mail->Body = "<div style=\"min-width: 500px;margin-right:auto;text-decoration: none;font-family:'Open Sans', Helvetica, Arial;color: black;\">
    <div style=\"width:100%;
    padding:10px 0px 10px 0px;
    background-color:#2F3131;
    margin-left:auto;
    color:#ffffff;
    text-align:center;
    margin-bottom: 30px;\">
        <h2>Welcome on Camagru, " . $_GET['login'] . " !</h2>
    </div>
    <div style=\"width: 80%;
    margin-left:auto;
    margin-right:auto;
    font-size: 16px;\">
      Your account is almost ready. We just need you to confirm your email by pressing the button below.
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
        Confirm mail
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
          If you did not create an account in Camagru, ignore this email and no account will be created.
    </div>
  </div>";
  $mail->AddAddress($addressMail);

   if(!$mail->Send()) {
    $error[] = "There was an error when sending the email. Please contact support for more information with the following debug message : " . PHP_EOL . "Mailer Error: " . $mail->ErrorInfo;
   }
   else {
     if (!isset($tokenExist)) {
       $newToken = $bdd->prepare('INSERT INTO `tokens` (`id_token`, `usage`, `content`, `expires`, `id_user`) VALUES (NULL, 0, :token, NOW() + INTERVAL 24 HOUR, :id_user);');
       $newToken->execute(array('token' => $token, 'id_user' => $id_user));
       if ($newToken->rowCount() != 1)
        $error[] = "Error with the database. Please contact the support team.";
     }
     $token_query->closeCursor();
   }
?>
<html>
  <head>
  	<?php
      if (isset($error))
        echo "<title>Camagru - Failed to register</title>";
      else
        echo "<title>Camagru - Successfully registered</title>";
    ?>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
      </br></br>
        <div class="basic-page">
          <div class="app-title">
            <?php
              if (isset($error))
                echo "<h1>Oops something went wrong !</h1>";
              else
                echo "<h1>You have been successfully registered !</h1>";
            ?>
          </div>
          <?php
            if (isset($error)) {
              foreach ($error as $key => $value) {
                echo '<p class="no-align error">' . $value . '</p>';
              }
              echo '<p class="no-align">DEBUG : If you encountered an error when sending the email(which may be possible !!!), you can go to the page <a href="debug.php">DEBUG</a> and manually enter your login to make your account active</p>';
            }
            else {
              echo '<p class="no-align">We sent you a message to your email asking you to validate your email (' . $_GET['mail'] . '). If it\'s not validated before the next 24 hours, your account will be deleted and your mail, your username will be available again.</p>';
            }
          ?>
        </div>
      </br></br>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
