<ul class="topnav">
  <li><a class="camabruuu" href="index.php">CAMABRUUUUUUU</a></li>
  <li><a href="global_galery.php">HALL OF FAME (or loose)</a></li>
  <?php
      if (!isset($_SESSION['login']) || $_SESSION['login'] == "") {
        $log_bar = '<li class="right"><a href="register.php">Register</a></li>
                    <li class="right"><a href="connection.php">Login</a></li>';
      }
      else {
        $log_bar = '<li class="dropdown right">
                      <a href="javascript:void(0)" class="dropbtn">Profile</a>
                      <div class="dropdown-content">
                        <a href="personal_galery.php">My pictures</a>
                        <a href="account_settings.php">Settings</a>
                        <a href="logout.php">Logout</a>
                      </div>
                    </li>';
      }
      echo $log_bar;
  ?>
</ul>
