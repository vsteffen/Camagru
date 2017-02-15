<?php

  if (!isset($_SESSION))
      session_start();

  if (!isset($_SESSION['id_user']) || !$_SESSION['id_user'])
    header("Location: connection.php");

  require_once('config/database.php');
  try
  {
    $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
  }
  catch(Exception $e)
  {
    die('Error : '.$e->getMessage());
  }
  $error = [];
  if (isset($_POST['accountDelete'])) {
    if ($bdd->exec("DELETE FROM `users` WHERE `id_user` = " . $_SESSION['id_user'] . ";")) {
      rmdir("./image/login/" . $_SESSION['login']);
      $_SESSION['login'] = "";
      $_SESSION['rank'] = 0;
      $_SESSION['id_user'] = 0;
      header("Location: index.php");
    }
    else
      $message[] = "Failed to delete your account! Please contact the support team.";
  }
  if (isset($_POST['localisationValue'])) {
    if (strlen($_POST['localisationValue']) == 0) {
      $message[] = "Failed to change localisation, field was empty! (Must contain at least 1 character).";
    }
    else if ($bdd->exec("UPDATE `users` SET `localisation` = '" . $_POST['localisationValue'] . "' WHERE `users`.`id_user` = '" . $_SESSION['id_user'] . "';"))
      $message[] = "Success to change localisation (set at " . $_POST['localisationValue'] . ").";
    else
      $message[] = "Failed to change localisation (try at " . $_POST['localisationValue'] . ").";
  }
  if (isset($_POST['changePassword'])) {
    function checkPassword($pwd) {
      if (strlen($pwd) < 8) {
        $GLOBALS['message'][] = "Password too short (8)!";
      }
      if (!preg_match("#[0-9]+#", $pwd)) {
        $GLOBALS['message'][] = "Password must include at least one number!";
      }
      if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $GLOBALS['message'][] = "Password must include at least one letter!";
      }
      if (isset($GLOBALS['message']))
        return FALSE;
      return TRUE;
    }
    if (checkPassword($_POST['newPassword'])) {
      if ($_POST['newPassword'] !== $_POST['newAgainPassword'])
        $message[] = "Failed to change password, new passwords aren't the same!";
      else if ($_POST['newPassword'] === $_POST['oldPassword'])
      $message[] = "Failed to change password, new and old passwords are the same!";
      else {
        $dataUser = $bdd->query("SELECT `pwd` FROM users WHERE id_user=" . $_SESSION['id_user'] . ";");
        if ($data = $dataUser->fetch()) {
          if ($data['pwd'] == hash('sha256', $_POST['oldPassword'])) {
            if ($bdd->exec("UPDATE `users` SET `pwd` = '" . hash('sha256', $_POST['newPassword']) . "' WHERE `users`.`id_user` = " . $_SESSION['id_user'] . ";"))
              $message[] = "Success to change password!";
            else
              $message[] = "Failed to change password, error with database!";
          }
          else
            $message[] = "Failed to change password, old password isn't correct!";
        }
        else
          $message[] = "Failed to change password, error with database!";
      }
    }
  }


?>
<!DOCTYPE html>
<html>
  <head>
  	<title>Camagru - Account settings</title>
  	<link rel="stylesheet" href="./css/global.css">
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div class="basic-page">
          <?php
            if (isset($message)) {
              foreach ($message as &$key) {
                echo "<p>" . $key . "</p>";
              }
            }
            else
              echo "<p>The result of the request will be display here!<p>";
          ?>
          <form id="formLocalisation" action="account_settings.php" method="post">
            <fieldset>
              <legend>Change localisation</legend>
              <input type="text" name="localisationValue" value="" placeholder="Localisation">
              <button class="btnAdmin" id="changeLocalisation" type="submit" name="changeLocalisation">CHANGE</button>
            </fieldset>
            </br>
          </form>
          <form id="formPassword" action="account_settings.php" method="post">
            <fieldset id="passwordSection">
              <legend>Change password</legend>
              <input type="password" name="oldPassword" value="" placeholder="Old password">
              <input type="password" name="newPassword" value="" placeholder="New password">
              <input type="password" name="newAgainPassword" value="" placeholder="Type password again">
              <button class="btnBigAdmin" id="changePassword" name="changePassword">RESET</button>
            </fieldset>
            </br>
          </form>
          <form id="formDelete" action="account_settings.php" method="post">
            <fieldset>
              <legend>Delete account</legend>
              <button class="btnBigAdmin" onclick="if (confirm('Are you sure you want to delete your account?')) confirmDeleteAccount(); return false" id="accountDelete" name="accountDelete">DELETE</button>
            </fieldset>
            </br>
          </form>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="script/account_settings.js"></script>
