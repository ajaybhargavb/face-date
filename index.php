<?php

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

// Enforce https on production
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');


/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

require_once('sdk/src/facebook.php');

$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
));

$user_id = $facebook->getUser();

$detai = $facebook->api(array(
      'method' => 'fql.query',
      'query' => "select first_name from user where uid='".$user_id."'"
  ));

foreach ($detai as $de) {
      $ffnam = idx($de, 'first_name');
    }


//clear file

    $f = fopen('msg.html',"w+");
    fclose($f);



 //chat
session_start();




// Process login info

     // $name    = isset($_POST['name']) ? $_POST['name'] : "Unnamed";
  $name1 = $ffnam;
  $_SESSION['nickname'] = $ffnam;

      //$nickname = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : "Hidden";
$nickname = $ffnam;





if ($user_id) {
  try {
    // Fetch the viewer's basic information
    $basic = $facebook->api('/me');

$details = $facebook->api(array(
      'method' => 'fql.query',
      'query' => "select name,sex from user where uid='".$user_id."'"
  ));


          //connecting to database

      $con = mysql_connect("182.72.63.18","fc_team_19","ZHDLvRxM24F4WGN2");
      if (!$con)
        {
        die('Could not connect: ' . mysql_error());
        }

      mysql_select_db("fc_team_19", $con);


      foreach ($details as $det) {
      $nam = idx($det, 'name');
      $sx = idx($det, 'sex');

      mysql_query("INSERT INTO users(userid,username,sex) VALUES ('".$user_id."','".$nam."','".$sx."')");
    }

    mysql_close($con);


  } catch (FacebookApiException $e) {
    // If the call fails we check if we still have a user. The user will be
    // cleared if the error is because of an invalid accesstoken
    if (!$facebook->getUser()) {
      header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
      exit();
    }
  }

 





  // This fetches some things that you like . 'limit=*" only returns * values.
  // To see the format of the data you are retrieving, use the "Graph API
  // Explorer" which is at https://developers.facebook.com/tools/explorer/
  $likes = idx($facebook->api('/me/likes?limit=4'), 'data', array());

  // This fetches 4 of your friends.
  $friends = idx($facebook->api('/me/friends?limit=4'), 'data', array());

  // And this returns 16 of your photos.
  $photos = idx($facebook->api('/me/photos?limit=16'), 'data', array());

  // Here is an example of a FQL call that fetches all of your friends that are
  // using this app
  $app_using_friends = $facebook->api(array(
    'method' => 'fql.query',
    'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
  ));
}

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());

$app_name = idx($app_info, 'name', '');

?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo he($app_name); ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
    <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />

    <!--[if IEMobile]>
    <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
    <![endif]-->

    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content="<?php echo he($app_name); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
    <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
    <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
    <meta property="og:description" content="My first app" />
    <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />

    <script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

    <script type="text/javascript">
      function logResponse(response) {
        if (console && console.log) {
          console.log('The response was', response);
        }
      }

      $(function(){
        // Set up so we handle click on the buttons
        $('#postToWall').click(function() {
          FB.ui(
            {
              method : 'feed',
              link   : $(this).attr('data-url')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        $('#sendToFriends').click(function() {
          FB.ui(
            {
              method : 'send',
              link   : $(this).attr('data-url')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        $('#sendRequest').click(function() {
          FB.ui(
            {
              method  : 'apprequests',
              message : $(this).attr('data-message')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });
      });



	  var min=2;
	  var sec=59;
	  var flag;
    var t;

    function func1()
    {
      document.getElementById('result').innerHTML="";
      var x=document.getElementById("id1");
      var y=document.getElementById("id2");
      x.style.display="block";
      y.style.display="none";
    }

    function func2()
	  {
	   min=2;
	   sec=59;
	   flag=0;
	   var x=document.getElementById("id1");
	   var y=document.getElementById("id2");
	   y.style.display="block";
	   x.style.display="none";
	   myfunc();
      }

	  function myfunc()
      {
      t=setTimeout(function(){myTimer()},1000);
	  }

	  function myTimer()
	  {
	  if(flag==0)
	  {
	  var m,s;

	  if(min<10)
	  m="0"+min;
	  else
	  m=min;
	  if(sec<10)
	  s="0"+sec;
	  else
	  s=sec;


	  document.getElementById("demo").innerHTML=m+":"+s+" mins left";
	  sec=sec-1;
	  if(sec==-1)
	  {
	  min=min-1;
	  if(min==-1)
    {
	  //alert("dead");
    quit();
    }
	  else
	  {
      sec=59;
    }
	  }

	  myfunc();
	  }
	  }

	  function func3()
	  {
	  	  document.getElementById("demo").innerHTML="";
	  	  flag=1;
	  }


      function setonline()
      {
        var xmlhttp;
        document.getElementById("msgg").innerHTML = "Trying to find match, please wait...";
        document.getElementById("chatbtn").innerHTML = "Finding Match...";
        document.getElementById("chatbtn").href="Javascript:";
        document.getElementById("chatbtn").onclick="";
       //document.getElementById("chatbtn").style.pointer-events="none";
       //document.getElementById("chatbtn").style.cursor="default";
      xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function()
    {
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
      {
        findmatch();
      }
    }
    xmlhttp.open("GET","/toggleonline.php",true);
    xmlhttp.send();
   }


   function findmatch()
   {
    document.getElementById("msgg").innerHTML = "Trying to find match, please wait...";
    document.getElementById("chatbtn").innerHTML = "Finding Match...";
    document.getElementById("chatbtn").href="Javascript:";
    document.getElementById("chatbtn").onclick="";
   var xmlhttp;
        xmlhttp=new XMLHttpRequest();
      xmlhttp.onreadystatechange=function()
      {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
          //alert(xmlhttp.responseText);
          if(xmlhttp.responseText=="|")
          {
              document.getElementById("msgg").innerHTML = "Unable to find match. Retry Later";
              document.getElementById("chatbtn").innerHTML = "Retry Chat";
              document.getElementById("chatbtn").onclick = "findmatch();";
              document.getElementById("chatbtn").href = "Javascript:findmatch();";
          }
          else
          {
            func2();
            var arr = xmlhttp.responseText.split("|");
            document.getElementById("msgg").innerHTML = "You are matched with "+arr[0];
            document.getElementById("msg2").innerHTML = "You are matched with "+arr[0];
            document.getElementById('secondperson').src="https://graph.facebook.com/"+arr[1]+"/picture";
            //document.getElementById("chatbtn").innerHTML = "Quit";
            document.getElementById("quitbtn").onclick = "quit();";
            document.getElementById("quitbtn").href = "Javascript:quit();";
            resetstatus();
          }

        }
      }
      xmlhttp.open("GET","/findmatch.php",true);
    xmlhttp.send();
   }

   function resetstatus()
   {
    var xmlhttp;
        xmlhttp=new XMLHttpRequest();
      xmlhttp.onreadystatechange=function()
      {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
        }
      }
      xmlhttp.open("GET","/resetstatus.php",true);
    xmlhttp.send();
   }


function quit()
{
          var xmlhttp;
          document.getElementById("msgg").innerHTML = "Click on chat to find match...";
          document.getElementById("chatbtn").innerHTML = "Chat";
          document.getElementById("chatbtn").href="Javascript:setonline();";
          document.getElementById("chatbtn").onclick="setonline();";
          clearTimeout(t);
         //document.getElementById("chatbtn").style.pointer-events="none";
         //document.getElementById("chatbtn").style.cursor="default";
        xmlhttp=new XMLHttpRequest();
      xmlhttp.onreadystatechange=function()
      {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
          func1();
          document.getElementById('secondperson').src="/images/unknown.jpg";
        }
      }
      xmlhttp.open("GET","/quitreset.php",true);
      xmlhttp.send();
} 


//chat
var httpObject = null;
      var link = "";
      var timerID = 0;
      var nickName = "<?php echo $nickname; ?>";
      //alert(nickName);
      // Get the HTTP Object
      function getHTTPObject(){
         if (window.ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
         else if (window.XMLHttpRequest) return new XMLHttpRequest();
         else {
            alert("Your browser does not support AJAX.");
            return null;
         }
      }

      // Change the value of the outputText field
      function setOutput(){
         if(httpObject.readyState == 4){
            var response = httpObject.responseText;
            var objDiv = document.getElementById("result");
            objDiv.innerHTML += response;
            objDiv.scrollTop = objDiv.scrollHeight;
            var inpObj = document.getElementById("msg");
            inpObj.value = "";
            inpObj.focus();
         }
      }

      // Change the value of the outputText field
      function setAll(){
         if(httpObject.readyState == 4){
            var response = httpObject.responseText;
            var objDiv = document.getElementById("result");
            objDiv.innerHTML = response;
            objDiv.scrollTop = objDiv.scrollHeight;
         }
      }

      // Implement business logic
      function doWork(){
         httpObject = getHTTPObject();
         if (httpObject != null) {
            //alert(document.getElementById('msg').value);
            link = "message.php?nick="+nickName+"&msg="+document.getElementById('msg').value;
            httpObject.open("GET", link , true);
            httpObject.onreadystatechange = setOutput;
            httpObject.send(null);
         }
      }

      // Implement business logic
      function doReload(){
         httpObject = getHTTPObject();
         var randomnumber=Math.floor(Math.random()*10000);
         if (httpObject != null) {
            link = "message.php?all=1&rnd="+randomnumber;
            httpObject.open("GET", link , true);
            httpObject.onreadystatechange = setAll;
            httpObject.send(null);
         }
      }

      function UpdateTimer() {
         doReload();
         timerID = setTimeout("UpdateTimer()", 2000);
      }


      function keypressed(e){
         if(e.keyCode=='13'){
            doWork();
         }
      }

    </script>


  </head>
  <body onload="UpdateTimer();func1();">
    <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          window.location = window.location;
        });

        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>

    <header class="clearfix">
      <?php if (isset($basic)) { ?>
      <p id="picture" style="background-image: url(https://graph.facebook.com/388023674593089/picture)"></p>

      <div>
        <h1>Welcome to <strong>Blind Speed Dating</strong></h1>
        <p class="tagline">
          This is a place to connect with strangers and chat...
          <!--<a href="<?php echo he(idx($app_info, 'link'));?>" target="_top"><?php echo he($app_name); ?></a>-->
        </p>

        <div id="share-app">
          <p>Share your app:</p>
          <ul>
            <li>
              <a href="#" class="facebook-button" id="postToWall" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="plus">Post to Wall</span>
              </a>
            </li>
            <li>
              <a href="#" class="facebook-button speech-bubble" id="sendToFriends" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="speech-bubble">Send Message</span>
              </a>
            </li>
            <li>
              <a href="#" class="facebook-button apprequests" id="sendRequest" data-message="Test this awesome app">
                <span class="apprequests">Send Requests</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <?php } else { ?>
      <div>
        <h1>Welcome</h1>
        <div class="fb-login-button" data-scope="user_likes,user_photos"></div>
      </div>
      <?php } ?>
    </header>

    <section id="get-started">
      <p>Chat with a Stranger</p>

      <div id="id1" style="display:none;">

<br><br>
      <img id="firstperson2" src="https://graph.facebook.com/<?php echo $user_id ?>/picture?type=normal" width="80" height="80"></img>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <img id="secondperson2" src="/images/unknown.jpg" width="80" height="80"></img><br>
	  <span style="font-size:20px" id="msgg">Click on Chat to find match</span><br><br>
	  <a href="javascript:" id="chatbtn" target="_top" class="button" onclick="setonline();">Chat</a>
	 <br><br>

	 </div>

	 <div id="id2" style="display:none;">

	  <img id="firstperson" src="https://graph.facebook.com/<?php echo $user_id ?>/picture" name="firstperson" width="80" height="80"></img>&nbsp;&nbsp;&nbsp;&nbsp;
	  <span style="margin-top:-30px;font-weight:bold" id="msg2">You are connected with stranger</span>&nbsp;&nbsp;&nbsp;&nbsp;
	  <img id="secondperson" src="https://graph.facebook.com/<?php echo $user_id ?>/picture" name="secondperson" width="80" height="80"></img>


	  <br><br><br>
      &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
	  <a href="Javascript:void(0);" target="_top" id="quitbtn" class="button" onclick="quit();">Quit</a>
	  &nbsp;&nbsp;&nbsp;&nbsp;
	  <a href="Javascript:" target="_top" id="reportbtn" class="button">Report</a>
	  &nbsp;&nbsp;&nbsp;&nbsp;
	  <a href="Javascript:" target="_top" id="conbtn" class="button" onclick="func3();">Continue</a>

	  &nbsp;&nbsp;&nbsp;

	  <span id="demo" style="color:white"></span>

	  <br><br>
<!--chat-->
        <?php

        $name1    = $ffnam;
        $_SESSION['nickname'] = $name1;

      ?>

       <div id="result" style="height:150px;overflow:auto;border:2px;">
       <?php
          $data = file("msg.html");
          foreach ($data as $line) {
            echo $line;
          }
       ?>
        </div>


	  <!--<textarea name="chatwindow" id="chatwindow" rows=6 cols=50 readonly></textarea>-->
	  <br><br>

	  <input type="text" name="msg" id="msg" size=47  onkeyup="keypressed(event);"></input>&nbsp;&nbsp;&nbsp;&nbsp;
	  <a href="Javascript:doWork();" target="_top" class="button" onclick="doWork();">Send</a>
      </div>
          </section>
  </body>
</html>
