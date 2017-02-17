<?php
function delTree($dir) {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }


  require_once("./database.php");

  try {
      $dbh = new PDO($DB_DSN_INSTALL, $DB_USER, $DB_PASSWORD);

      $query = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
                SET time_zone = '+00:00';

                DROP DATABASE IF EXISTS `$DB_NAME`;

                CREATE DATABASE `$DB_NAME` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

                USE `$DB_NAME`;

                SET NAMES 'utf8';

    # ---------------  USERS TABLE ---------------

                DROP TABLE IF EXISTS `users`;
                CREATE TABLE `users` (
                  `id_user` int(11) NOT NULL,
                  `login` varchar(30) NOT NULL,
                  `pwd` varchar(64) NOT NULL,
                  `mail` varchar(100) NOT NULL,
                  `avatar` varchar(100) NOT NULL,
                  `localisation` varchar(100) NOT NULL,
                  `status` tinyint(4) DEFAULT '0',
                  `last_log` int(11) NOT NULL,
                  `rank` tinyint(4) DEFAULT '2'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `users`
                  ADD PRIMARY KEY (`id_user`);
                ALTER TABLE `users`
                  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

                INSERT INTO `users` (`id_user`, `login`, `pwd`, `mail`, `avatar`, `localisation`, `status`, `last_log`, `rank`) VALUES
                  (NULL, 'admin', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', 'admin@camagru.com', '', 'Paris', 1, '1', '2'),
                  (NULL, 'member1', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', 'member1@member.com', '', 'Créteil', 0, '1', '1'),
                  (NULL, 'member2', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', 'member2@member.com', '', 'DoudouLand', 1, '1', '1');


    # ---------------  TOKENS TABLE---------------

                DROP TABLE IF EXISTS `tokens`;
                CREATE TABLE `tokens` (
                  `id_token` int(11) NOT NULL,
                  `usage` tinyint(4) NOT NULL,
                  `content` varchar(32) NOT NULL,
                  `expires` DATETIME NOT NULL,
                  `id_user` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `tokens`
                  ADD PRIMARY KEY (`id_token`);
                ALTER TABLE `tokens`
                  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `tokens`
                  ADD CONSTRAINT fk_tokens_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;


    # ---------------  SNAPSHOTS TABLE ---------------

                DROP TABLE IF EXISTS `snapshots`;
                CREATE TABLE `snapshots` (
                  `id_snap` int(11) NOT NULL,
                  `path` varchar(50) NOT NULL,
                  `id_snap_of_user` int(11) NOT NULL,
                  `title` varchar(30) NOT NULL,
                  `timestamps` DATETIME NOT NULL,
                  `thumbs_up` int(11) NOT NULL,
                  `thumbs_down` int(11) NOT NULL,
                  `scope` tinyint(4) NOT NULL,
                  `id_user` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `snapshots`
                  ADD PRIMARY KEY (`id_snap`);
                ALTER TABLE `snapshots`
                  MODIFY `id_snap` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `snapshots`
                  ADD CONSTRAINT fk_snapshots_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;


    # --------------- POSTS TABLE---------------

                DROP TABLE IF EXISTS `posts`;
                CREATE TABLE `posts` (
                  `id_post` int(11) NOT NULL,
                  `text` varchar(250) NOT NULL,
                  `timestamps` DATETIME NOT NULL,
                  `id_snap` int(11) NOT NULL,
                  `id_user` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `posts`
                  ADD PRIMARY KEY (`id_post`);
                ALTER TABLE `posts`
                  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `posts`
                  ADD CONSTRAINT fk_posts_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;

                ALTER TABLE `posts`
                  ADD CONSTRAINT fk_posts_snap FOREIGN KEY (id_snap) REFERENCES snapshots(id_snap) ON DELETE CASCADE;


    # --------------- TWITTER TABLE --------------

                DROP TABLE IF EXISTS `twitter`;
                CREATE TABLE `twitter` (
                  `authentified` tinyint(2) NOT NULL,
                  `id_twitter` int(10) NOT NULL,
                  `screen_name` varchar(20) NOT NULL,
                  `token` varchar(50) NOT NULL,
                  `token_secret` varchar(50) NOT NULL,
                  `expires` int(11) NOT NULL,
                  `id_user` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `twitter`
                  ADD CONSTRAINT fk_twitter_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;


    # --------------- THUMBS TABLE --------------

                DROP TABLE IF EXISTS `thumbs`;
                CREATE TABLE `thumbs` (
                  `upOrDown` tinyint(2) NOT NULL,
                  `id_snap` int(11) NOT NULL,
                  `id_user` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `thumbs`
                  ADD CONSTRAINT fk_thumbs_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;

                ALTER TABLE `thumbs`
                  ADD CONSTRAINT fk_thumbs_snap FOREIGN KEY (id_snap) REFERENCES snapshots(id_snap) ON DELETE CASCADE;

    # ---------------  EVENTS ---------------

                SET GLOBAL EVENT_SCHEDULER = ON;

                CREATE EVENT clean_token
                  ON SCHEDULE
                    EVERY 1 MINUTE
                  COMMENT 'Clears out token table each 24 hours.'
                  DO BEGIN
                    DELETE FROM u USING `users` u INNER JOIN `tokens` t ON ( u.id_user = t.id_user ) WHERE t.usage = 0 AND t.expires < NOW();
                    DELETE FROM `tokens` where expires < NOW();
                  END;
                ";


      $dbh->exec($query);
      delTree("../image/login");
      mkdir("../image/login");
      mkdir("../image/login/admin");
      mkdir("../image/login/member1");
      mkdir("../image/login/member2");
      echo "DB installed successfully" . PHP_EOL;
  }
  catch (PDOException $e) {
      die("DB ERROR: ". $e->getMessage());
  }
?>
