<?php
  session_start();
  
  function loginForm(){
  echo'
  <div id="loginform">
  <form action="index.php" method="post">
    <p> Please enter your name to contine:</p>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name"/>
    <input type="submit" name="enter" id="enter" value="Enter" />
  </form>
  </div>
  ';
  }
  
if(isset($_POST['enter'])){
  if($_POST['name'] != ""){
    $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
  }else{
    echo '<span class="error">Please type in a name</span>';
  }
}

if(isset($_GET['logout'])){
  //simple exit message
  $fp = fopen("log.html", 'a');
  fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session</i><br></div>");
  
  session_destroy();
  header("Location: index.php"); //redirect user
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>

<title>Chat and Draw</title>

<!--Prevents iPhone, iPad, and touch devices from scrolling or zooming when touched-->
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />

<!--CSS-->
<link rel="stylesheet" type="text/css" href="cssFile.css">

<!--
Load Canvas support for IE8. ExplorerCanvas courtesy Google. 
See: http://code.google.com/p/explorercanvas/
-->
<!--[if lt IE 9]>
<script src="excanvas.js"></script>
<![endif]-->

<!--Load the OrbiterMicro JavaScript library (non-minified version). Use during development.-->
<script type="text/javascript" src="http://unionplatform.com/cdn/OrbiterMicro_latest.js"></script>
<!--Load the OrbiterMicro JavaScript library (minified version). Use for production.-->
<!--<script type="text/javascript" src="http://unionplatform.com/cdn/OrbiterMicro_latest_min.js"></script>-->

<!--Load UnionDraw application code-->
<script src="UnionDraw.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
  
  // jQuery Document
  $(document).ready(function(){
    //To End the Session
    $("#exit").click(function(){
      var exit = confirm("Are you sure you want to exit?");
      if(exit === true){window.location = 'index.php?logout=true';}
    });
    
    $("#submitmsg").click(function(){
      var clientmsg = $("#usermsg").val();
      $.post("post.php", {text: clientmsg});
      $("#usermsg").attr("value", "");
      loadLog();
      return false;
    });
    
    $("#whiteBoardToggle").click(function(){
      $( "#controls" ).toggle();
      $( "#drawCanvas" ).toggle();
    });
    
  });

  //load file containing chat log
  function loadLog(){
    var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll Height before the request
    $.ajax({
      url: "log.html",
      cache : false,
      success : function(html){
        $("#chatbox").html(html); //Insert chat log in the chatbox
        //Autoscroll
        var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll Height after the request
        if(newscrollHeight > oldscrollHeight){
          $("#chatbox").animate({ scrollTop: newscrollHeight}, 'normal'); //autoscroll to bottom
        }
      },
    });
  }

  
  setInterval (loadLog, 2500); //Reload file every 2500ms
</script>
</head>

<body>
  <?php
    if(!isset($_SESSION['name'])){
      loginForm();
    }else{?>
      <div id="wrapper">
        <div id="menu">
            <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
            <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
            <div style="clear:both"></div>
        </div>
         
        <div id="chatbox">
          <?php
            if(file_exists("log.html") && filesize("log.html") > 0){
              $handle = fopen("log.html", "r");
              $contents = fread($handle, filesize("log.html"));
              fclose($handle);
              
              echo $contents;
            }
          ?>
        </div>
        
        <form name="message" action="">
            <input type="text" id="usermsg" size="63" />
            <input type="submit"  id="submitmsg" value="Send" />
        </form>
        <br/>
        <input type="button" id="whiteBoardToggle" value="Whiteboard" />
      </div>
  <?php
  }
  ?>
  
  <!-- This is the code for the whiteboard -->
  <div id="whiteboard">
    <!--Drop down menus for selecting line thickness and color-->
    <div id="controls">
      Size:
      <select id="thickness" class="fixed">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="20">20</option>
      </select>
  
      Color:
      <select id="color">
        <option value="#FFFFFF">#FFFFFF</option>
        <option value="#AAAAAA">#AAAAAA</option>
        <option value="#666666">#666666</option>
        <option value="#000000">#000000</option>
        <option value="#9BA16C">#9BA16C</option>
        <option value="#CC8F2B">#CC8F2B</option>
        <option value="#63631D">#63631D</option>
      </select>
    </div>
    
    <!--The canvas where drawings will be displayed-->
    <canvas id="drawCanvas"></canvas>
    
    <!--A status text field, for displaying connection information-->
    <div id="status"></div>
  </div>
  
</body>
</html>

