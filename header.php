<ul class="topnav">
  <li><a class="camabruuu" href="index.php">CAMAGRU</a></li>
  <?php
      if (!isset($_SESSION['login']) || $_SESSION['login'] == "") {
        $log_bar = '<li class="right"><a href="register.php">Register</a></li>
                    <li class="right"><a href="connection.php">Login</a></li>';
      }
      else {
        $log_bar = '<li class="dropdown right">
                      <a href="javascript:void(0)" class="dropbtn">Profile</a>
                      <div class="dropdown-content">
                        <a href="galery_user.php?user=' . $_SESSION['login'] . '">My pictures</a>
                        <a href="account_settings.php">Settings</a>
                        <a href="logout.php">Logout</a>
                      </div>
                    </li>
                    <li class="right"><a href="take_snapshot.php">Take a pic!</a></li>';
      }
      echo $log_bar;
      if (isset($_SESSION['rank']) && $_SESSION['rank'] == 2) {
        echo '<li class="right"><a href="administration.php">Administration</a></li>';
      }
  ?>
</ul>
