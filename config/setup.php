<?php
  require_once("./database.php");

  try {
      $dbh = new PDO($DB_DSN_INSTALL, $DB_USER, $DB_PASSWORD);

      $query = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
                SET time_zone = '+00:00';

                DROP DATABASE IF EXISTS `$DB_NAME`;

                CREATE DATABASE `$DB_NAME` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

                USE `$DB_NAME`;

                SET NAMES 'utf8';

                DROP TABLE IF EXISTS `users`;
                CREATE TABLE `users` (
                  `id` int(11) NOT NULL,
                  `login` varchar(30) NOT NULL,
                  `pwd` varchar(64) NOT NULL,
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
                  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

                INSERT INTO `users` (`id`, `login`, `pwd`, `mail`, `avatar`, `localisation`, `register`, `last_log`, `rank`) VALUES
                  (NULL, 'admin', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', 'admin@camagru.com', '', 'Paris', '1', '1', '2'),
                  (NULL, 'member1', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', 'member1@member.com', '', 'Créteil', '1', '1', '1'),
                  (NULL, 'member2', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', 'member2@member.com', '', 'DoudouLand', '1', '1', '1');


                ";

      $dbh->exec($query);
      echo "DB installed successfully" . PHP_EOL;
  }
  catch (PDOException $e) {
      die("DB ERROR: ". $e->getMessage());
  }
?>
