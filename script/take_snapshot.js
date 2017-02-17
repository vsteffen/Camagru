(function() {

var localStream,
    streaming	  	    = false,
    uploadFile          = false,
    video			    = document.querySelector('#video'),
    cover			    = document.querySelector('#cover'),
    canvas		  	    = document.querySelector('#canvas'),
    photo		        = document.querySelector('#photo'),
    photoTaken		    = document.querySelector('#photoTaken'),
    startbutton		    = document.querySelector('#startbutton'),
    filterSelect	    = document.querySelector('.filter-select'),
    filterCase          = document.getElementsByClassName('filterCase'),
    filterActive	    = document.querySelector('#filterActive'),
    imageFromUser       = document.querySelector('#imageFromUser'),
    imageUploadFromUser = document.querySelector('#imageUploadFromUser'),
    firstSnap           = 1,
    filterPath          = "",
    width			    = 640,
    height			    = 0;

var ajaxUpload;


//https://developer.mozilla.org/en/docs/Web/API/MediaStream


navigator.getUserMedia = navigator.getUserMedia ||
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia;

if (navigator.getUserMedia) {
   navigator.getUserMedia({ audio: false, video: { width: 640, height: 480 } },
      function(stream) {
        localStream = stream;
        //  var video = document.querySelector('#video');
         video.srcObject = stream;
         video.onloadedmetadata = function(e) {
           video.play();
         };
      },
      function(err) {
        video.style.display = 'none';
        imageUploadFromUser.style.display = 'inherit';
        useCamera.style.background = '#FFCA58';
        alert("The following error occurred: " + err.name);
         console.log("The following error occurred: " + err.name);
      }
   );
} else {
  video.style.display = 'none';
  imageUploadFromUser.style.display = 'inherit';
  useCamera.style.background = '#FFCA58';
  alert("getUserMedia not supported");
  console.log("getUserMedia not supported");
}


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

function upload(data, filterPath, streaming, callback) {
  $.ajax({
      type: "POST",
      url: 'snapshotMerge.php',
      dataType: 'json',
      // cache: false,
      data: {
        imgBase64: data,
        filterPath: filterPath,
        streaming: streaming
      },
      success: callback
  });
}

function getUploadData(callback) {
  if (streaming) {
    canvas.width = width;
    canvas.height = height;
    canvas.getContext('2d').drawImage(video, 0, 0, width, height);
    var data = canvas.toDataURL('image/png');
  }
  else {
    var data = imageUploadFromUser.getAttribute('src');
  }
  upload(data, filterPath, streaming, function(response) {
    ajaxUpload = response;
    if (ajaxUpload.key1 == 1) {
      console.log("Success while uploading the picture.");
      addImageToWebsite(ajaxUpload.key2, ajaxUpload.key3, ajaxUpload.key4);
    }
    else if (ajaxUpload.key1 == 2) {
      console.log(ajaxUpload.key2);
      alert(ajaxUpload.key2);
    }
    else {
      console.log(ajaxUpload.key2);
      alert("An error occured while uploading the picture. Please contact the support to help you or keep up to date on our twitter! Error message : " + ajaxUpload.key2);
    }
  });
}

startbutton.addEventListener('click', function(ev){
  if (streaming || uploadFile)
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

  useCamera.addEventListener("click", enableCamera, false);
  function enableCamera() {
    if (!streaming) {
      video.style.display = 'inherit';
      imageUploadFromUser.style.display = 'none';
      uploadFile = false;
      imageFromUser.value = "";
      if (navigator.getUserMedia) {
         navigator.getUserMedia({ audio: false, video: { width: 640, height: 480 } },
            function(stream) {
              localStream = stream;
              streaming = true;
              video.srcObject = stream;
              video.onloadedmetadata = function(e) {
                video.play();
              };
            },
            function(err) {
              video.style.display = 'none';
              imageUploadFromUser.style.display = 'inherit';
              useCamera.style.background = '#FFCA58';
              streaming = false;
              alert("The following error occurred: " + err.name);
              console.log("The following error occurred: " + err.name);
            }
         );
      } else {
        video.style.display = 'none';
        imageUploadFromUser.style.display = 'inherit';
        useCamera.style.background = '#FFCA58';
        streaming = false;
        alert("getUserMedia not supported");
        console.log("getUserMedia not supported");
      }
    }
    else {
      alert("Camera is already enable!");
    }
  }

  imageFromUser.addEventListener("change", handleImage, false);
  function handleImage() {
    var fileList = this.files;
    // var img = document.createElement("img");
    if (fileList[0] !== undefined) {
      if (streaming) {
        if (navigator.mozGetUserMedia) {
          localStream.stop();
        }
        else {
          localStream.getVideoTracks()[0].stop();
        }
      }
      var reader = new FileReader();
      reader.readAsDataURL(fileList[0]);
      reader.onload = function () {
        imageUploadFromUser.setAttribute("src", reader.result);
      };
      reader.onerror = function (error) {
        console.log('Error: ', error);
      };

      if (!uploadFile) {
        video.style.display = 'none';
        imageUploadFromUser.style.display = 'inherit';
        useCamera.style.background = '#FFCA58';
        imageFromUser.style.background = '#F9BA32';
        uploadFile = true;
        streaming = false;
      }
    }

  }

})();
