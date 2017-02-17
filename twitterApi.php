<?php

  if (!isset($_SESSION))
    session_start();

  if (empty($_SESSION['login']))
      header('Location: connection.php');

  require_once('config/connect_bdd.php');
  $bdd = connectBDD();
?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Camagru - TwitterApi</title>
  	<link rel="stylesheet" href="./css/global.css">
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div class="basic-page">
          <?php
            if (isset($_POST['id_snap_to_tweet'])) {
              $_SESSION['id_snap_to_tweet'] = $_POST['id_snap_to_tweet'];
            }
            if (isset($_POST['textTweet'])) {
              if (strlen(htmlentities($_POST['textTweet'])) > 140) {
                $error = "Tweet too long, 140 characters max";
                $no_button = true;
              }
              else
                $_SESSION['textTweet'] = htmlentities($_POST['textTweet']);
            }
            function debug($var) {
              echo "<pre style='text-align: left;'>";
              var_dump($var);
              echo "</pre>";
            }
            function verifScope($dataSnap, $id_user) {
              if ($dataSnap['scope'] > 0)
                return true;
              else if ($dataSnap['scope'] == 0 && $id_user == $dataSnap['id_user'])
                return true;
              else
                return false;
            }
            function postTweet($bdd, $twitterApi) {
              $snap = $bdd->prepare('SELECT * FROM `snapshots` WHERE `id_snap` = :id_snap_to_tweet;');
              $snap->execute(array('id_snap_to_tweet' => $_SESSION['id_snap_to_tweet']));
              if ($dataSnap = $snap->fetch()) {
                $path = $dataSnap['path'];
                $snap->closeCursor();
                if (verifScope($dataSnap, $_SESSION['id_user'])) {
                  $picture = $twitterApi->uploadMedia($path);
                  if (isset($_SESSION['textTweet']) && strlen($_SESSION['textTweet']) > 0)
                    $message = $_SESSION['textTweet'];
                  else
                    $message = "Picture created on camagru.com !";
                  $parameters = [
                      'status' => $message,
                      'media_ids' => implode(',', [$picture->media_id_string])
                  ];
                  $result = $twitterApi->sendTweet($parameters);
                  $_SESSION['id_snap_to_tweet'] = 0;
                  $_SESSION['textTweet'] = "";
                  return array("status" => 0);
                }
                else
                  return array("status" => 2, "message" => "You're not allowed to access to this picture!");
              }
              else
                return array("status" => 1, "message" => "The picture that you are trying to tweet doesn't exist! Please send a valid id.");
            }
            define("CONSUMER_KEY", "p8GeupH3aj1zWywUzoQssxhJ8");
            define("CONSUMER_SECRET", "CvRyS3BIiwD5VsRwCZlqSGtZ3xG4nQdO3WWMUkPc9Tj1c7zbum");
            require "vendor/autoload.php";
            use Abraham\TwitterOAuth\TwitterOAuth;
            $twitterApi = new vsteffen\twitterApi(
              CONSUMER_KEY,
              CONSUMER_SECRET
            );

            $userTwitter = $bdd->query("SELECT * FROM `twitter` WHERE `id_user` = " . $_SESSION['id_user'] . ";");
            if ($dataUserTwitter = $userTwitter->fetch()) {
              $authentified = 1;
            }
            else {
              $authentified = 0;
              $userTwitter->closeCursor();
            }

            if (!isset($error)) {
              if (isset($_GET['denied'])) {
                $userTwitter->closeCursor();
                $error = "You have denied Camagru access to your twitter account. We will not be able to share your pictures! Please click on the link below if you want to start over.";
              }
              else if ($authentified) {
                $verif_cred = $twitterApi->verifyCredentials($dataUserTwitter['token'], $dataUserTwitter['token_secret']);
                if ($twitterApi->getLastResult() == 200) {
                  $twitterApi->setTimeout(15, 15);
                  $result = postTweet($bdd, $twitterApi);
                  if ($result['status'] == 0 && $twitterApi->getLastResult() == 200) {
                    $message = "Your tweet has been successfully send!";
                  }
                  else {
                    $error = $result['message'];
                    if ($result['status'] == 1)
                      $no_button = 1;
                  }
                }
                else {
                  $bdd->exec("DELETE FROM `twitter` WHERE `id_user` = " . $_SESSION['id_user'] . ";");
                  $error = "An error occurred, please contact the camagru support! Or try to reconnect on your twitter account again;";
                }
              }
              else if (isset($_GET['oauth_token'])) {
                $token = $twitterApi->getAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);
                $verif_cred = $twitterApi->verifyCredentials($token['oauth_token'], $token['oauth_token_secret']);
                if ($twitterApi->getLastResult() == 200) {
                  $bdd->exec("INSERT INTO `twitter` (`authentified`, `id_twitter`, `screen_name`, `token`, `token_secret`, `expires`, `id_user`) VALUES ('1', " . $verif_cred->id . " , '" . $verif_cred->screen_name . "', '" . $token['oauth_token'] . "', '" . $token['oauth_token_secret'] . "', '0', " . $_SESSION['id_user'] . ");");
                  $result = postTweet($bdd, $twitterApi);
                  if ($result['status'] == 0 && $twitterApi->getLastResult() == 200) {
                    $message = "Your tweet has been successfully send!";
                  }
                  else {
                    $error = $result['message'];
                    if ($result['status'] == 1)
                      $no_button = 1;
                  }
                }
                else {
                  $error = "An error occurred, please contact the camagru support! Or try connect on your twitter account again;";
                }
              }
            }

            if (isset($error)) {
              echo "<h1><p class='app-title'>Something went wrong ...</p></h1>";
              echo "<p class='error'>" . $error . "</p>";
              if (!isset($no_button)) {
                $url = $twitterApi->getAuthenticateUrl('http://localhost/Camagru/twitterApi.php');
                echo "<a class='basic-href' href='" . $url . "'><button id='connectTwitter' class='btnClassic' >LOGIN IN TWITTER</button></a>";
              }
            }
            else if (isset($message)) {
              echo "<h1><p class='app-title'>Tweet successfully send!</p></h1>";
              echo "<p>" . $message . "</p>";
            }
            else {
              echo "<h1><p class='app-title'>Login Twitter</p></h1>";
              echo "<p>To share your picture, we need you to login in twitter and authorize Camagru to access to your twitter account.</p>";
              $url = $twitterApi->getAuthenticateUrl('http://localhost/Camagru/twitterApi.php');
              echo "<a class='basic-href' href='" . $url . "'><button id='connectTwitter' class='btnClassic' >LOGIN IN TWITTER</button></a>";
            }
          ?>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
