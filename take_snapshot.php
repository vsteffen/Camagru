<?php
  define("FILTER_ROOT", "./image/filter/", true);

  if (!isset($_SESSION))
      session_start();

  if (!isset($_SESSION['id_user']) || empty($_SESSION['id_user']))
    header('Location: connection.php');
?>
<html>
  <head>
  	<title>Camagru - Take picture</title>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="icon" href="image/ressource/logo2.png">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div id="photoSection" class="basic-page">
          <div class="videoDiv">
            <img id="filterActive" src="" class="hidden">
            <img id="imageUploadFromUser" src="image/ressource/noCamera.png" class="hidden">
            <video id="video"></video>
          </div>
          <button class="btnClassic" id="startbutton">TAKE A PIC!</button>
          <div id="choiceContainer">
            <button id="useCamera">CAMERA</button>
            <span id="betweenChoice">OR</span>
            <input id="imageFromUser" type="file" name="imageFromUser" accept=".gif,.jpeg,.png">
          </div>
          <canvas style="display: none" id="canvas"></canvas>
          <div>
          </div>
          <div class="filter-select">
            <div class="filterCase filterCaseSelected"><img id="filter0" src="<?php echo FILTER_ROOT; ?>noFilter.png"></div>
            <div class="filterCase"><img id="filter1" src="<?php echo FILTER_ROOT; ?>cadre.png"></div>
            <div class="filterCase"><img id="filter2" src="<?php echo FILTER_ROOT; ?>laurier.png"></div>
            <div class="filterCase"><img id="filter3" src="<?php echo FILTER_ROOT; ?>banner.png"></div>
            <div class="filterCase"><img id="filter4" src="<?php echo FILTER_ROOT; ?>love.png"></div>
            <div class="filterCase"><img id="filter5" src="<?php echo FILTER_ROOT; ?>cloud.png"></div>
          </div>
        </div>
        <div id="photoTaken" class="basic-page">
          <p>The picture will appear here!</p>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="script/take_snapshot.js"></script>
