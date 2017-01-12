<?php
  if (isset($_POST) && isset($_POST['submit'])) {
    require_once('config/database.php');
    try
    {
      $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    }
    catch(Exception $e)
    {
      die('Erreur : '.$e->getMessage());
    }
    if ($_POST['submit'] == 'ACTIVE') {
      if ($bdd->exec("UPDATE `users` SET `status` = '1' WHERE `users`.`mail` = '" . $_POST['mail'] . "';"))
        echo "SUCCESS TO ACTIVE ACCOUNT " . $_POST['mail'] . PHP_EOL;
      else
        echo "FAILED TO ACTIVE ACCOUNT " . $_POST['mail'] . PHP_EOL;
    }
  }
?>
<html>
  <body>
    <form action="debug.php" method="post">
      <fieldset>
        <legend>Make account active</legend>
        <!-- <label for="mail-name"></label> -->
        <input type="text" name="mail" value="" placeholder="Email">
        <input type="submit" name="submit" value="ACTIVE">
      </fieldset>
    </form>
  </body>
</html>
