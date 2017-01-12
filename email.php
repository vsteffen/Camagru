<?php

  $username = "Vivien";
  $token = "TOKEN";

  $mail1 = "<div style=\"min-width: 500px;margin-right:auto;font-family:'Open Sans', Helvetica, Arial;\">
    <div style=\"width:100%;
    padding:10px 0px 10px 0px;
    background-color:#1cacea;
    margin-left:auto;
    color:#ffffff;
    text-align:center;\">
        <h2>Welcome on Camagru, " . $username . " !</h2>
    </div>
    </br></br>
    <div style=\"width: 80%;
    margin-left:auto;
    margin-right:auto;\">
      Your account is almost ready. We just need you to confirm your email by pressing the button below.
      <a style=\"text-decoration: none;\" href=\"token_valid.php?token=" . $token . "\"><div style=\"background: #1cacea;
      background-color:#F9BA32;
      -webkit-border-radius: 0;
      -moz-border-radius: 0;
      border-radius: 0px;
      color: #ffffff;
      font-size: 20px;
      padding: 10px 20px 10px 20px;
      width: 150px;
      text-align:center;
      margin: 15px;\">
        Confirm mail
    </div></a>
      See you soon, have fun on Camagru !
    </div>
    </br></br>
    <div style= \"width:100%;
    padding:10px 0px 10px 0px;
    background-color:#efefef;
    text-align: center;
    font-size: 14px;\">
          If you did not create an account in Camagru, ignore this email and no account will be created.
    </div>
  </div>";

  $mail2 = "";
  echo $mail1;
?>

<!-- <div style="min-width: 500px;margin-right:auto;font-family:'Open Sans', Helvetica, Arial;">
  <div style="width:100%;
  padding:10px 0px 10px 0px;
  background-color:#1cacea;
  margin-left:auto;
  color:#ffffff;
  text-align:center;">
      <h2>Welcome on Camagru, " . $username . " !</h2>
  </div>
  </br></br>
  <div style="width: 80%;
  margin-left:auto;
  margin-right:auto;">
    Your account is almost ready. We just need you to confirm your email by pressing the button below.
    <a style="text-decoration: none;" href="token_valid.php?token=TOKEN"><div style="background: #1cacea;
    background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
    background-image: -moz-linear-gradient(top, #3498db, #2980b9);
    background-image: -ms-linear-gradient(top, #3498db, #2980b9);
    background-image: -o-linear-gradient(top, #3498db, #2980b9);
    background-image: linear-gradient(to bottom, #3498db, #2980b9);
    -webkit-border-radius: 0;
    -moz-border-radius: 0;
    border-radius: 0px;
    color: #ffffff;
    font-size: 20px;
    padding: 10px 20px 10px 20px;
    width: 150px;
    text-align:center;
    margin: 15px;">
      Confirm mail
  </div></a>
    See you soon, have fun on Camagru !
  </div>
  </br></br>
  <div style= "width:100%;
  padding:10px 0px 10px 0px;
  background-color:#efefef;
  text-align: center;
  font-size: 14px;">
        If you did not create an account in Camagru, ignore this email and no account will be created.
  </div>
</div> -->
