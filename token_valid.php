<?php
  if (!isset($_SESSION))
      session_start();

  if (isset($_GET['token'])) {
    if (empty($_GET['token']))
      header('Location: index.php');
  }

  require_once('config/connect_bdd.php');
  $bdd = connectBDD();

  if (isset($_POST)) {
    if (isset($_POST['changePassword'])) {
      function checkPassword($pwd) {
        $string = "";
        if (strlen($pwd) < 8) {
          if (!empty($string))
            $string = $string . "</br>";
          $string = $string . " Password too short (8)!";
        }
        if (!preg_match("#[0-9]+#", $pwd)) {
          if (!empty($string))
            $string = $string . "</br>";
          $string = $string . " Password must include at least one number!";
        }
        if (!preg_match("#[a-zA-Z]+#", $pwd)) {
          if (!empty($string))
            $string = $string . "</br>";
          $string = $string . " Password must include at least one letter!";
        }
        if (!empty($string)) {
          $GLOBALS['messageContent'] = $string;
          return FALSE;
        }
        return TRUE;
      }
      if (checkPassword($_POST['newPassword'])) {
        if ($_POST['newPassword'] !== $_POST['newAgainPassword']) {
          $messageTitle = "Something went wrong ...";
          $messageContent = "Failed to change password, new passwords aren't the same!";
          $error = 1;
        }
        else {
          $postTokenQuery = $bdd->prepare('SELECT `id_user`, `id_token` FROM `tokens` WHERE content = :changePassword;');
          $postTokenQuery->execute(array('changePassword' => $_POST['changePassword']));
          if ($dataTokenQuery = $postTokenQuery->fetch()) {
            $updatePassword = $bdd->prepare('UPDATE `users` SET `pwd` = :newPassword WHERE `users`.`id_user` = :id_user;');
            $updatePassword->execute(array('newPassword' => hash('sha256', $_POST['newPassword']), 'id_user' => $dataTokenQuery['id_user']));

            if ($updatePassword->rowCount() == 1) {
                $bdd->exec("DELETE FROM `tokens` WHERE `tokens`.`id_token` = " . $dataTokenQuery['id_token'] . ";");
                $messageTitle = "Password changed";
                $messageContent = "Your password has been changed!";
                $lazy = 1;
            }
            else {
              $messageTitle = "Something went wrong ...";
              $messageContent = "Old password and new password are the same. Please choose a different password.";
              $error = 1;
            }
            $postTokenQuery->closeCursor();
          }
          else {
            $messageTitle = "Something went wrong ...";
            $messageContent = "Token specified is invalid!";
            $error = 1;
          }

        }
      }
      else {
        $messageTitle = "Something went wrong ...";
        $error = 1;
      }
      if (!isset($lazy))
        $messageMore = '<form id="formPassword" action="token_valid.php" method="post">
                            <input class="twoField" type="password" name="newPassword" value="" placeholder="New password">
                            <input class="twoField"type="password" name="newAgainPassword" value="" placeholder="Type password again">
                            <button class="btnBigAdmin"  id="changePassword" name="changePassword" value="' . $_POST['changePassword'] . '">RESET</button>
                        </form>';
      $successPOST;
    }
  }
  if (!isset($successPOST) && isset($_GET['token'])) {
    $result = $bdd->prepare('SELECT * FROM `tokens` WHERE content = :token AND expires > NOW();');
    $result->execute(array('token' => $_GET['token']));
    if ($data = $result->fetch()) {
      if ($data['usage'] == 0) {
        $bdd->exec("UPDATE `users` SET `status` = '1' WHERE `users`.`id_user` = " . $data['id_user'] . ";");
        $bdd->exec("DELETE FROM `tokens` WHERE `tokens`.`id_token` = " . $data['id_token']. ";");
        $user = $bdd->query("SELECT `login` FROM `users` WHERE `id_user` = " . $data['id_user']);
        $dataUser = $user->fetch();
        mkdir("./image/login/" . $dataUser['login']);
        // header('Location: account_active.php');
        $messageTitle = "Your account is active !";
        $messageContent = 'Congratulations, you are finally registered in camagru! You can add additional information to your profile in the "Profile" section at the top right when you are <a href="connection.php">logged in</a>.</br></br>Have fun on Camagru ! :)';
      }
      else if ($data['usage'] == 1) {
        $user = $bdd->query("SELECT `status` FROM `users` WHERE `id_user` = " . $data['id_user'] . ";");
        $dataUser = $user->fetch();
        $resetIdToken = $data['id_token'];
        $messageTitle ="Reset password";
        $messageContent = "Complete the following fields to reset your password.";
        $messageMore = '<form id="formPassword" action="token_valid.php" method="post">
                            <input class="twoField" type="password" name="newPassword" value="" placeholder="New password">
                            <input class="twoField"type="password" name="newAgainPassword" value="" placeholder="Type password again">
                            <button class="btnBigAdmin"  id="changePassword" name="changePassword" value="' . $_GET['token'] . '">RESET</button>
                        </form>';
        $user->closeCursor();
      }
    }
    else {
      $messageTitle = "Something went wrong ...";
      $messageContent = "Token is invalid.";
      $error = 1;
    }
  }

?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Camagru - Manage tokens</title>
  	<link rel="stylesheet" href="./css/global.css">
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div class="basic-page">
          <?php
            if (isset($error)) {
              echo '<div class="app-title">
                        <h1>' . $messageTitle . '</h1>
                      </div>
                      <p class="error no-align">' . $messageContent . '</p>';
              if (isset($messageMore))
                echo $messageMore;
              echo '</div>';
            }
            else {
              echo '<div class="app-title">
                        <h1>' . $messageTitle . '</h1>
                      </div>
                      <p class="no-align">' . $messageContent . '</p>';
              if (isset($messageMore))
                echo $messageMore;
              echo "</div>";
              if (isset($result))
                $result->closeCursor();
            }
          ?>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
