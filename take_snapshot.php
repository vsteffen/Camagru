<?php
  define("FILTER_ROOT", "./image/filter/", true);

  if (!isset($_SESSION))
      session_start();

  if (!isset($_SESSION['id_user']) || empty($_SESSION['id_user']))
    header('Location: connexion.php');
?>
<html>
  <head>
  	<title>Camagru - Login</title>
    <link rel="stylesheet" href="./css/global.css">
  	<link rel="stylesheet" href="./css/login.css">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div id="photoSection" class="basic-page">
          <div class="videoDiv">
            <img id="filterActive" src="" class="hidden">
            <video id="video"></video>
          </div>
          <button class="btnClassic" id="startbutton">TAKE A PIC!</button>
          <canvas style="display: none" id="canvas"></canvas>
          <div>
            <input type="file" name="image">
          </div>
          <button class="btnClassic" type="submit">SEND</button>
          <!-- <form action="#" method="post" enctype="multipart/form-data"> -->
            <div class="filter-select">
              <div class="filterCase filterCaseSelected"><img id="filter0" src="<?php echo FILTER_ROOT; ?>noFilter.png"></div>
              <div class="filterCase"><img id="filter1" src="<?php echo FILTER_ROOT; ?>cadre.png"></div>
              <div class="filterCase"><img id="filter2" src="<?php echo FILTER_ROOT; ?>laurier.png"></div>
              <div class="filterCase"><img id="filter3" src="<?php echo FILTER_ROOT; ?>banner.png"></div>
              <div class="filterCase"><img id="filter4" src="<?php echo FILTER_ROOT; ?>love.png"></div>
              <div class="filterCase"><img id="filter5" src="<?php echo FILTER_ROOT; ?>cloud.png"></div>
            </div>
          <!-- </form> -->
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
        <script type="text/javascript">
        (function() {

        var streaming		= false,
          video			    = document.querySelector('#video'),
          cover			    = document.querySelector('#cover'),
          canvas		  	= document.querySelector('#canvas'),
          photo		     	= document.querySelector('#photo'),
          photoTaken		= document.querySelector('#photoTaken'),
          startbutton		= document.querySelector('#startbutton'),
          filterSelect	= document.querySelector('.filter-select'),
          filterCase    = document.getElementsByClassName('filterCase'),
          filterActive	= document.querySelector('#filterActive'),
          firstSnap     = 1,
          filterPath    = "",
          width			    = 640,
          height			  = 0;

        var ajaxUpload;

        navigator.getMedia	= ( navigator.getUserMedia ||
                  navigator.webkitGetUserMedia ||
                  navigator.mozGetUserMedia ||
                  navigator.msGetUserMedia);


        navigator.getMedia(
          {
            video: true,
            audio: false
          },
          function(stream) {
            if (navigator.mozGetUserMedia) {
              video.mozSrcObject = stream;
            } else {
              var vendorURL = window.URL || window.webkitURL;
              video.src = vendorURL.createObjectURL(stream);
            }
            video.play();
          },
          function(err) {
            console.log("An error occured! " + err);
          }
        );

        video.addEventListener('canplay', function(ev){
          if (!streaming) {
            height = video.videoHeight / (video.videoWidth/width);
            video.setAttribute('width', width);
            video.setAttribute('height', height);
            canvas.setAttribute('width', width);
            canvas.setAttribute('height', height);
            streaming = true;
          }
        }, false);

        function addImageToWebsite(imgPath, imgId, imgPrimId) {
          var img = document.createElement("img");
          img.src = imgPath;
          img.id = imgPrimId;

          var a = document.createElement("a");
          a.href = "see_snap.php?idPrimSnap=" + imgPrimId;

          if (firstSnap == 1)
          {
            photoTaken.innerHTML = "";
            firstSnap = 0;
          }
          photoTaken.insertBefore(a, photoTaken.childNodes[0]);
          a.appendChild(img);
        }

        function upload(data, filterPath, callback) {
          $.ajax({
              type: "POST",
              url: 'snapshotMerge.php',
              dataType: 'json',
              // cache: false,
              data: {
                imgBase64: data,
                filterPath: filterPath
              },
              success: callback
          });
        }

        function getUploadData(callback) {
          canvas.width = width;
          canvas.height = height;
          canvas.getContext('2d').drawImage(video, 0, 0, width, height);
          var data = canvas.toDataURL('image/png');

          upload(data, filterPath, function(response) {
            ajaxUpload = response;
            if (ajaxUpload.key1 == 1) {
              console.log("Success while uploading the picture.");
              addImageToWebsite(ajaxUpload.key2, ajaxUpload.key3, ajaxUpload.key4);
            }
            else {
              console.log(ajaxUpload.key2);
              alert(ajaxUpload.key1 + "An error occured while uploading the picture. Please contact the support to help you or keep up to date on our twitter!");
            }
          });
        }

        startbutton.addEventListener('click', function(ev){
          getUploadData();
        }, false);

        filterSelect.addEventListener("click", changeFilter, false);

        function changeFilter(e) {
            if (e.target !== e.currentTarget) {
                for (var i in filterCase)
                {
                    filterCase[i].className = "filterCase";
                }
                var node = e.target;
                if (!e.target.id)
                  node = e.target.childNodes[0];
                if (node.id != "filter0")
                {
                  filterPath = node.getAttribute("src");
                  filterActive.setAttribute("src", filterPath);
                  filterActive.className = "";
                }
                else
                {
                  filterActive.setAttribute("src", "");
                  filterActive.className = "hidden";
                  filterPath = "";
                }
                node.parentNode.className = "filterCase filterCaseSelected";
            }
            e.stopPropagation();
        }

        })();
        </script>
