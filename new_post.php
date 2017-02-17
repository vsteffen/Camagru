<?php

    if (!isset($_SESSION))
        session_start();

    if (empty($_SESSION['login']))
        header('Location: connection.php');

    if (isset($_POST['postText']) && !empty($_POST['postText'])
          && isset($_POST['idPrimSnap']) && !empty($_POST['idPrimSnap'])
		  && isset($_POST['id_user']) && !empty($_POST['id_user'])
          && isset($_SESSION['login']) && !empty($_SESSION['login'])
          && isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {

      require_once('config/connect_bdd.php');
      $bdd = connectBDD();

      $newPost = $bdd->prepare('INSERT INTO `posts` (`id_post`, `text`, `timestamps`, `id_snap`, `id_user`) VALUES (NULL, :postText, NOW(), :idPrimSnap, ' . $_SESSION['id_user'] . ');');
      $newPost->execute(array('postText' => $_POST['postText'], 'idPrimSnap' => $_POST['idPrimSnap']));
      if ($newPost->rowCount() == 1) {
		 $lastInsert = $bdd->lastInsertId();
      }
      else {
        echo json_encode(array("status" => 0, "errorMessage" => "Error with database while sending post, please contact the support or try again."));
      }

	  $user = $bdd->prepare('SELECT `mail` FROM `users` WHERE `id_user` = :id_user;');
	  $user->execute(array('id_user' => $_POST['id_user']));
	  if ($userData = $user->fetch()) {

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
		$mail->Subject = "New comment";

		$addressMail = $userData['mail'];

		$mail->Body = "<div style=\"min-width: 500px;margin-right:auto;text-decoration: none;font-family:'Open Sans', Helvetica, Arial;color: black;\">
		  <div style=\"width:100%;
		  padding:10px 0px 10px 0px;
		  background-color:#2F3131;
		  margin-left:auto;
		  color:#ffffff;
		  text-align:center;
		  margin-bottom: 30px;\">
			  <h2>New comment</h2>
		  </div>
		  <div style=\"width: 80%;
		  margin-left:auto;
		  margin-right:auto;
		  font-size: 16px;\">
			Someone commented on your picture! So go see what he said thanks to the link just below!
			<a style=\"text-decoration: none;\" href=\"http://localhost/Camagru/see_snap.php?idPrimSnap=" . $_POST['idPrimSnap'] . "\"><div style=\"background: #1cacea;
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
			  If you don't want to receive more mails, disable this feature in the settings of your picture.
		  </div>
		</div>";
		$mail->AddAddress($addressMail);

		 if(!$mail->Send()) {
		  echo json_encode(array("status" => 0, "errorMessage" => "The message has been posted but there was an error when sending the email. Please contact support for more information with the following debug message : " . PHP_EOL . "Mailer Error: " . $mail->ErrorInfo));
		 }
		 echo json_encode(array("status" => 1, "newPostId" => $lastInsert));
	  }
	  else {
		echo json_encode(array("status" => 0, "errorMessage" => "Invalid user."));
	  }
    }
    else {
      header('Location: index.php');
    }
?>
