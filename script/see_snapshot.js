(function() {

// http://stackoverflow.com/questions/9083089/use-php-code-in-external-javascript-file

var commentPosted	        = document.querySelector('#commentPosted'),
    newPost   	          = document.querySelector('#newPost'),
    newPostText	          = document.querySelector('#newPostText'),
    newPostSubmit	        = document.querySelector('#newPostSubmit'),
    link_thumb_up	        = document.querySelector('#link_thumb_up'),
    link_thumb_down       = document.querySelector('#link_thumb_down'),
    thumb_up	            = document.querySelector('#thumb_up'),
    thumb_down	          = document.querySelector('#thumb_down'),
    setVisibilityPrivate  = document.querySelector('#setVisibilityPrivate'),
    setVisibilityEveryone	= document.querySelector('#setVisibilityEveryone'),
    setVisibilityMembers	= document.querySelector('#setVisibilityMembers'),
    id_user               = <?php echo $_SESSION['id_user'] ?>,
    idPrimSnap            = <?php echo $_GET['idPrimSnap'] ?>,
    scopeSnap             = <?php echo $scopeSnap ?>,
    num_thumb_up          = <?php echo $thumbs_up?>,
    num_thumb_down        = <?php echo $thumbs_down?>;


  function sendPost(text, idPrimSnap, callback) {
    $.ajax({
        type: "POST",
        url: 'new_post.php',
        dataType: 'json',
        data: {
          postText: text,
          idPrimSnap: idPrimSnap
        },
        success: callback
    });
  }

  newPostSubmit.addEventListener('click', function(ev){
    if (newPostText.value.length > 0) {
      sendPost(newPostText.value, idPrimSnap, function(response) {
        if (response.status == 1) {
          console.log("Success while sending the post.");
          window.location.reload();
          // window.location.href = "see_snap.php?idPrimSnap=" + idPrimSnap + "#" + response.newPostId;
        }
        else {
          console.log(response.errorMessage);
          alert(response.errorMessage);
        }
      });
      // newPost.parentNode.removeChild(newPost);
      // alert("ok");
    }
    else {
      alert("You must put one character at least!");
    }
  }, false);

  function addThumb(upOrDown, idPrimSnap, callback) {
    $.ajax({
        type: "POST",
        url: 'add_thumb.php',
        dataType: 'json',
        data: {
          upOrDown: upOrDown,
          id_snap: idPrimSnap
        },
        success: callback
    });
  }

  link_thumb_up.addEventListener('click', function(ev){
    addThumb(1, idPrimSnap, function(response) {
      if (response.status == 1) {
        console.log("Success to add thumb!");
        num_thumb_up += response.up;
        num_thumb_down += response.down;
        thumb_up.innerHTML = num_thumb_up.toString();
        thumb_down.innerHTML = num_thumb_down.toString();
      }
      else
        console.log("Nothing have been done.");
    });
  }, false);

  link_thumb_down.addEventListener('click', function(ev){
    addThumb(2, idPrimSnap, function(response) {
      if (response.status == 1) {
        console.log("Success to add thumb!");
        num_thumb_up += response.up;
        num_thumb_down += response.down;
        thumb_up.innerHTML = num_thumb_up.toString();
        thumb_down.innerHTML = num_thumb_down.toString();
      }
      else
        console.log("Nothing have been done.");
    });
  }, false);

  function changeScope(action, idPrimSnap, callback) {
    $.ajax({
        type: "POST",
        url: 'change_scope.php',
        dataType: 'json',
        data: {
          action: action,
          id_snap: idPrimSnap
        },
        success: callback
    });
  }

  setVisibilityPrivate.addEventListener('click', function(ev){
    changeScope(11, idPrimSnap, function(response) {
      if (response.status == 1) {
        console.log("Success to set visibility to private!");
        alert("Success to set visibility to private!");
      }
      else {
        console.log(response.message);
        alert(response.message);
      }
    });
  }, false);

  setVisibilityEveryone.addEventListener('click', function(ev){
    changeScope(12, idPrimSnap, function(response) {
      if (response.status == 1) {
        console.log("Success to set visibility to everyone!");
        alert("Success to set visibility to everyone!");
      }
      else {
        console.log(response.message);
        alert(response.message);
      }
    });
  }, false);

  setVisibilityMembers.addEventListener('click', function(ev){
    changeScope(13, idPrimSnap, function(response) {
      if (response.status == 1) {
        console.log("Success to set visibility to members!");
        alert("Success to set visibility to members!");
      }
      else {
        console.log(response.message);
        alert(response.message);
      }
    });
  }, false);

})();
