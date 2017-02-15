<?php
  if (!isset($_SESSION))
    session_start();

if (!isset($_SESSION['rank']) || $_SESSION['rank'] !== 2)
  header("Location: index.php");

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
  if (isset($_POST['accountStatus'])) {
    if ($bdd->exec("UPDATE `users` SET `status` = '" . $_POST['accountStatusValue'] . "' WHERE `users`.`mail` = '" . $_POST['accountStatusMail'] . "';"))
      $message = "Success to change the status (set at " . $_POST['accountStatusValue'] . ") for the following mail : " . $_POST['accountStatusMail'] . PHP_EOL;
    else
      $message = "Failed to change the status (try at " . $_POST['accountStatusValue'] . ") for the following mail : " . $_POST['accountStatusMail'] . PHP_EOL;
  }
  if (isset($_POST['accountDelete'])) {
    if ($bdd->exec("DELETE FROM `users` WHERE `login` = '" . $_POST['accountDeleteId'] . "' OR `mail` = '" . $_POST['accountDeleteId'] . "';")) {
      $message = "Success to delete the following account : " . $_POST['accountDeleteId'] . PHP_EOL;
      rmdir("./image/login/" . $_POST['accountDeleteId']);
    }
    else
      $message = "Failed to delete the following account : " . $_POST['accountDeleteId'] . PHP_EOL;
  }
  if (isset($_POST['accountUpgrade'])) {
    if ($bdd->exec("UPDATE `users` SET `rank` = '" . $_POST['accountUpgradeValue'] . "' WHERE `users`.`login` = '" . $_POST['accountUpgradeId'] . "';"))
      $message = "Success to change rank (set at " . $_POST['accountUpgradeValue'] . ") for the following login : " . $_POST['accountUpgradeId'] . PHP_EOL;
    else
      $message = "Failed to change rank (try at " . $_POST['accountUpgradeValue'] . ") for the following login : " . $_POST['accountUpgradeId'] . PHP_EOL;
  }
  if (isset($_POST['createToken'])) {
    function getIdByLogin($login, $bdd) {
      $user = $bdd->query("SELECT `id_user` FROM `users` WHERE `login` = '" . $login . "';");
      if ($userData = $user->fetch())
        $id_user = $userData['id_user'];
      else
        $id_user = 0;
      $user->closeCursor();
      return $id_user;
    }
    $id_user = getIdByLogin($_POST['createTokenUser'], $bdd);
    if (!$id_user)
      $message = "Failed to insert or update token, login '" . $_POST['createTokenUser'] . "' isn't correct" . PHP_EOL;
    else {
      $tokenQuery = $bdd->query("SELECT * FROM `tokens` WHERE `usage` = " . $_POST['createTokenUsage'] . " AND `id_user` = " . $id_user . ";");
      if ($dataToken = $tokenQuery->fetch()) {
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        if ($bdd->exec("UPDATE `tokens` SET `content` = '" . $token . "', `expires` = NOW() WHERE `tokens`.`id_token` = " . $dataToken['id_token'] . ";"))
          $message = "Success to update token (set at " . $_POST['createTokenUsage'] . ") for the following login : " . $_POST['createTokenUser'] . PHP_EOL;
        else
          $message = "Failed to update token (try at " . $_POST['createTokenUsage'] . ") for the following login : " . $_POST['createTokenUser'] . PHP_EOL;
      }
      else {
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        if ($bdd->exec("INSERT INTO `tokens` (`id_token`, `usage`, `content`, `expires`, `id_user`) VALUES (NULL, '" . $_POST['createTokenUsage'] . "', '" . $token . "', NOW() + INTERVAL 24 HOUR, '" . $id_user . "');"))
          $message = "Success to insert token (set at " . $_POST['createTokenUsage'] . ") for the following login : " . $_POST['createTokenUser'] . PHP_EOL;
        else
          $message = "Failed to insert token (try at " . $_POST['createTokenUsage'] . ") for the following login : " . $_POST['createTokenUser'] . PHP_EOL;
      }
      $tokenQuery->closeCursor();
    }
  }


?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Camagru - Administration</title>
  	<link rel="stylesheet" href="./css/global.css">
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div class="basic-page">
          <?php
            if (isset($message))
              echo "<p>" . $message . "</p>";
            else
              echo "<p>The result of the request will be display here!<p>";
          ?>
          <form action="administration.php" method="post">
            <fieldset>
              <legend>Change status of account</legend>
              <input type="text" name="accountStatusMail" value="" placeholder="Email">
              <select class="selectAdmin" name="accountStatusValue">
                <option value="1">Inactive</option>
                <option value="2">Normal</option>
                <option value="3">Blocked</option>
              </select>
              <button class="btnAdmin" type="submit" name="accountStatus">CHANGE</button>
            </fieldset>
          </form>
            </br>
          <form action="administration.php" method="post">
            <fieldset>
              <legend>Delete account</legend>
              <input type="text" name="accountDeleteId" value="" placeholder="Email or login">
              <button class="btnAdmin" type="submit" name="accountDelete">DELETE</button>
            </fieldset>
          </form>
            </br>
          <form action="administration.php" method="post">
            <fieldset>
              <legend>Change rank</legend>
              <input type="text" name="accountUpgradeId" value="" placeholder="Login">
              <select class="selectAdmin" name="accountUpgradeValue">
                <option value="1">Member</option>
                <option value="2">Admin</option>
              </select>
              <button class="btnAdmin" type="submit" name="accountUpgrade">CHANGE</button>
            </fieldset>
          </form>
            </br>
          <form action="administration.php" method="post">
            <fieldset>
              <legend>Create token</legend>
              <input type="text" name="createTokenUser" value="" placeholder="Login">
              <select class="selectAdmin" name="createTokenUsage">
                <option value="0">Account active</option>
                <option value="1">Reset password</option>
              </select>
              <button class="btnAdmin" type="submit" name="createToken">CREATE</button>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
