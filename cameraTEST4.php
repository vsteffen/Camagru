<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Get User Media - example 1</title>
</head>
<body>
<video id="v"></video>
</body>
</html>
<script>
(function(){
    function userMedia(){
        return navigator.getUserMedia = navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.msGetUserMedia || null;

    }

    // Now we can use it
    if( userMedia() ){

        var constraints = {
            video: true,
            audio:false
        };

        var media = navigator.getUserMedia(constraints, function(stream){
            var v = document.getElementById('v');

            // URL Object is different in WebKit
            var url = window.URL || window.webkitURL;

            // create the url and set the source of the video element
            v.src = url ? url.createObjectURL(stream) : stream;

            // Start the video
            v.play();
        }, function(error){
            console.log("ERROR");
            console.log(error);
        });
    } else {
        console.log("Browser does not support WebCam integration");
    }
})();
</script>
