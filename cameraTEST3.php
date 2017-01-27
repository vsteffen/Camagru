<?php define("FILTER_ROOT", "./image/filter/", true);?>
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
        </br></br>
        <!-- <div class="row"> -->
          <!-- <div class="w-7"> -->
          <div id="photoSection">
            <div class="videoDiv">
              <img id="filterActive" src="">
              <video id="video"></video>
            </div>
            <button id="startbutton">Prendre une photo</button>
            <canvas style="display: none" id="canvas"></canvas>
            <form action="#" method="post" enctype="multipart/form-data">
              <div class="filter-select">
                <div class="filterCase filterCaseSelected"><img id="filter0" src="<?php echo FILTER_ROOT; ?>noFilter.png"></div>
                <div class="filterCase"><img id="filter1" src="<?php echo FILTER_ROOT; ?>cadre.png"></div>
                <div class="filterCase"><img id="filter2" src="<?php echo FILTER_ROOT; ?>laurier.png"></div>
                <div class="filterCase"><img id="filter3" src="<?php echo FILTER_ROOT; ?>banner.png"></div>
                <div class="filterCase"><img id="filter4" src="<?php echo FILTER_ROOT; ?>love.png"></div>
                <div class="filterCase"><img id="filter5" src="<?php echo FILTER_ROOT; ?>cloud.png"></div>
              </div>
              <div>
                <input type="file" name="image">
              </div>
              <button type="submit">Envoyer</button>
            </form>
          </div>
          <!-- <div class="w-2 w-solid"></div> -->
          <!-- <div class="w-3"> -->
          <div id="photoTaken">
            <p>The snapshots will appear here!</p>
          </div>
        </div>
          <!-- </div> -->
        <!-- </div> -->
      <!-- </div> -->
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

        function addImageToWebsite(imgPath, imgId) {
          var img = document.createElement("img");
          img.src = imgPath;
          img.id = imgId;
          if (firstSnap == 1)
          {
            photoTaken.innerHTML = "";
            firstSnap = 0;
          }
          photoTaken.insertBefore(img, photoTaken.childNodes[0]);
        }

        function takepicture() {
          canvas.width = width;
          canvas.height = height;
          canvas.getContext('2d').drawImage(video, 0, 0, width, height);
          var data = canvas.toDataURL('image/png');

          $.ajax({
              type: 'POST',
              url: 'snapshotMerge.php',
              data: {
                imgBase64: data,
                filterPath: filterPath
              },
              contentType: "image/png",
              success: function(response){
                  data = "data:image/png;base64," + response;
                  console.log(response);

              }
          });

          addImageToWebsite(data, "12345");
          // console.log(data);
        }


        startbutton.addEventListener('click', function(ev){
          takepicture();
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
                }
                else
                {
                  filterActive.setAttribute("src", "");
                }
                node.parentNode.className = "filterCase filterCaseSelected";
            }
            e.stopPropagation();
        }

        })();
        </script>
