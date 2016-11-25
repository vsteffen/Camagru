<?php
  require_once("./database.php");

  try {
      $dbh = new PDO($DB_DSN_INSTALL, $DB_USER, $DB_PASSWORD);

      $query = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
                SET time_zone = '+00:00';

                DROP DATABASE IF EXISTS `$DB_NAME`;

                CREATE DATABASE `$DB_NAME` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

                USE `$DB_NAME`;

                DROP TABLE IF EXISTS `users`;
                CREATE TABLE `users` (
                  `id` int(11) NOT NULL,
                  `pseudo` varchar(30) NOT NULL,
                  `pwd` varchar(32) NOT NULL,
                  `mail` varchar(100) NOT NULL,
                  `avatar` varchar(100) NOT NULL,
                  `localisation` varchar(100) NOT NULL,
                  `register` int(11) NOT NULL,
                  `last_log` int(11) NOT NULL,
                  `rank` tinyint(4) DEFAULT '2'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `users`
                  ADD PRIMARY KEY (`id`);

                ALTER TABLE `users`
                  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

      $dbh->exec($query);
      echo "DB created successfully" . PHP_EOL;
  }
  catch (PDOException $e) {
      die("DB ERROR: ". $e->getMessage());
  }
?>
