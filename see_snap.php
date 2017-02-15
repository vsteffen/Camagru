<?php

  function getLoginOfId($bdd, $id_user) {
    $user = $bdd->query("SELECT `login` FROM `users` WHERE `id_user` = $id_user;");
    if ($dataUser = $user->fetch())
      $login = $dataUser['login'];
    else
      $login = "Unknown";
    return $login;
  }

  if (!isset($_SESSION))
      session_start();

  if (!isset($_GET) || empty($_GET['idPrimSnap']))
    header('Location: index.php');
  else {
    $error = [];
    require_once('config/database.php');
    try
    {
      $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    }
    catch(Exception $e)
    {
      $error[] = "Error in database with this message = \"" . $e->getMessage() . "\".";
      exit();
    }
    $snap = $bdd->query("SELECT * FROM `snapshots` WHERE `id_snap` = " . $_GET['idPrimSnap'] . ";");
    if ($snapData = $snap->fetch()) {
      $scopeSnap = $snapData['scope'];
      $scopeOk = 0;
      if ($scopeSnap == 0)
      {
        if ((isset($_SESSION['id_user']) && $_SESSION['id_user'] == $snapData['id_user']) || $_SESSION['rank'] == 2)
          $scopeOk = 1;
      }
      else if ($scopeSnap == 1) {
        $scopeOk = 1;
      }
      else if ($scopeSnap == 2 && !empty($_SESSION['id_user'])) {
        $scopeOk = 1;
      }
      if (!$scopeOk)
        $error[] = "You don't have the permissions to access to this picture.";
      else {
        $userSnap = $bdd->query("SELECT `login` FROM `users` WHERE `id_user` = " . $snapData['id_user'] . ";");
        $loginData = $userSnap->fetch();
        $author = $loginData['login'];
        $userSnap->closeCursor();

        $title = $snapData['title'];
        $path = $snapData['path'];
        $timestamps = $snapData['timestamps'];
        $thumbs_up = $snapData['thumbs_up'];
        $thumbs_down = $snapData['thumbs_down'];
      }
    }
    else {
      $error[] = "This picture linked isn't available. The picture has been removed or the link isn't correct.";
    }
    $snap->closeCursor();
  }
?>
<html>
  <head>
  	<title>Camagru - See picture</title>
    <link rel="stylesheet" href="./css/global.css">
  	<link rel="stylesheet" href="./css/login.css">
  </head>
  <body>
    <div class="head_and_main">
      <?php include_once 'header.php' ?>
      <div class="main">
        <div class="basic-page">
          <?php
            if (!empty($error)) {
              echo "<div id='seeSnapError'>" . $error[0] . "</div>";
              $scopeSnap = -1;
              $thumbs_up = -1;
              $thumbs_down = -1;
              echo "<div id='newPostSubmit' class='hidden'></div>";
              echo "<div id='link_thumb_up' class='hidden'></div>";
              echo "<div id='link_thumb_down' class='hidden'></div>";
              echo "<div id='setVisibilityPrivate' class='hidden'></div>";
              echo "<div id='setVisibilityMembers' class='hidden'></div>";
              echo "<div id='setVisibilityEveryone' class='hidden'></div>";
              echo "<input  id=\"inputSetTitle\" class='hidden'>";
              echo "<button id=\"submitSetTitle\" class='hidden'></button>";
            }
            else {
              echo "<div id='seeSnap'>";
              if (!empty($title)) {
                echo "<span id='seeSnapTitle'>Title : " . $title . "</span>";
                echo "<a class='authorHref toRight' href='galery_user.php?user=" . $author . "'<span id='seeSnapAuthor'>Author : " . $author . "</span></br></a>";
              }
              else {
                echo "<a class='authorHref' href='galery_user.php?user=" . $author . "'<span id='seeSnapAuthor'>Author : " . $author . "</span></br></a>";
              }
              echo "<img id='imgSeeSnap' src='". $path . "'></br>
                    <div id='timestampsSeeSnap'>
                      Uploaded date : " . $timestamps . "
                    </div>
                    <div id='thumbSeeSnap'>
                      <a href='javascript:;' id='link_thumb_up'><img src='./image/ressource/thumb_up.png'></a>  <span id='thumb_up'>" . $thumbs_up . "</span>
                      <a href='javascript:;' id='link_thumb_down'><img src='./image/ressource/thumb_down.png'></a>  <span id='thumb_down'>" . $thumbs_down . "</span></br>
                    </div>
                  </div></br>";
              echo "<form action=\"twitterApi.php\" method=\"post\">
                      <div id=\"sectionTweet\">
                        <span>Tweet this picture :</span></br></br>
                        <textarea id=\"textTweet\" name=\"textTweet\" placeholder=\"Your tweet text! (Limited to 140 characters or he can be empty)\" maxlength=\"140\"></textarea>
                        <button id=\"submitTweet\" class=\"btnClassic\">SEND TWEET</button>
                        <input type=\"hidden\" name=\"id_snap_to_tweet\" value=\"" . $_GET['idPrimSnap'] . "\">
                      </div>
                    </form>
                    </br></br>";
            if ($author == $_SESSION['login'] || $_SESSION['rank'] == 2) {
              echo "<div id ='tweetSepPost' class='sepPost'></div>";
              echo "<div id=\"settingsSnapshot\">";
              echo "<div id='setTitle'>
                      <span>Set title :</span>
                      <input  id=\"inputSetTitle\" type=\"text\" name=\"inputSetTitle\" value=\"\" placeholder=\"Title\">
                      <button id=\"submitSetTitle\" class=\"btnClassic\">RENAME</button>
                    </div></br>";
              echo "<div id='visibility'>
                      <span>Set visibility :</span>
                      <button id=\"setVisibilityPrivate\" class=\"btnClassic\">PRIVATE</button>
                      <button id=\"setVisibilityEveryone\" class=\"btnClassic\">EVERYONE</button>
                      <button id=\"setVisibilityMembers\" class=\"btnClassic\">MEMBERS</button>
                    </div></br>";
              echo "<div id='delete'>
                      <span>Delete account :</span>
                      <button id=\"deleteSnapshot\" class=\"btnClassic\">DELETE</button>
                    </div></br>";
              echo "</div>";
            }
            echo "<div id ='firstSepPost' class='sepPost'></div>";

            if (empty($error)) {
              echo "<div id='commentPosted'>";
              $allPosts = $bdd->query("SELECT * FROM `posts` WHERE `id_snap` = " . $_GET['idPrimSnap'] . ";");
              if ($postData = $allPosts->fetch()) {
                $postLogin = getLoginOfId($bdd, $postData['id_user']);
                $textMessage = "<div class=\"post\" id=\"" . $postData['id_post'] ."\">
                                  <span class='postAuthor'>By " . $postLogin . "</span>
                                  <p class='postMessage'>" . $postData['text'] . "</p>
                                  <span class='postDate'>Posted on : ". $postData['timestamps'] ."</span>
                                </div>";
                echo $textMessage;
                while ($postData = $allPosts->fetch()) {
                  $textMessage = "<div class='sepPost'></div>
                                  <div class=\"post\" id=\"" . $postData['id_post'] ."\">
                                    <span class='postAuthor'>By " . $postLogin . "</span>
                                    <p class='postMessage'>" . $postData['text'] . "</p>
                                    <span class='postDate'>Posted on : ". $postData['timestamps'] ."</span>
                                  </div>";
                  echo $textMessage;
                }
              }
              else {
                echo "<div id=\"noMessage\">
                        <p>There are no messages yet. Be the first one!</p>
                      </div>";
              }
              $allPosts->closeCursor();
              echo "</div>";
              echo "<div id ='lastSepPost' class='sepPost'></div>";
            }
            if (empty($error) && empty($_SESSION['id_user'])) {
              echo "<div id='mustConnect'>
              <p>You must <a class='basic-href' href='connection.php'>connect</a> or <a class='basic-href' href='register.php'>register</a> on Camgru to comment this picture!</p>
              </div>";
            }
            else if (empty($error)) {
              echo "<div id=\"newPost\">
                      <textarea id=\"newPostText\"placeholder=\"Express yourself here! (Limited to 250 characters)\" maxlength=\"250\"></textarea>
                    </div>
                    <button id=\"newPostSubmit\" class=\"btnClassic\">Submit</button>";
            }
          }

          ?>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php' ?>
  </body>
</html>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
(function() {

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
    submitSetTitle	      = document.querySelector('#submitSetTitle'),
    inputSetTitle	        = document.querySelector('#inputSetTitle'),
    deleteSnapshot	      = document.querySelector('#deleteSnapshot'),
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

  function renameSnap(title, idPrimSnap, callback) {
    $.ajax({
        type: "POST",
        url: 'renameSnap.php',
        dataType: 'json',
        data: {
          title: title,
          id_snap: idPrimSnap
        },
        success: callback
    });
  }

  submitSetTitle.addEventListener('click', function(ev){
    renameSnap(inputSetTitle.value, idPrimSnap, function(response) {
      if (response.status == 1) {
        console.log("Snapshot has been rename!");
        location.reload();
      }
      else {
        console.log(response.message);
        alert(response.message);
      }
    });
  }, false);

  function deleteSnap(idPrimSnap, callback) {
    $.ajax({
        type: "POST",
        url: 'deleteSnap.php',
        dataType: 'json',
        data: {
          id_snap: idPrimSnap
        },
        success: callback
    });
  }
  deleteSnapshot.addEventListener('click', function(ev){
    if (confirm("Are you sure you want to delete your picture?")) {
      deleteSnap(idPrimSnap, function(response) {
        if (response.status == 1) {
          console.log("Snapshot has been delete!");
          alert("Snapshot has been delete!");
          window.location.replace("index.php");
        }
        else {
          console.log(response.message);
          alert(response.message);
        }
      });
    }
  }, false);

})();
</script>
