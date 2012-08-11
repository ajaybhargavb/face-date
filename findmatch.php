<?php
require_once('AppInfo.php');
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}
require_once('utils.php');
require_once('sdk/src/facebook.php');

$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
));

$user_id = $facebook->getUser();


try
{
$con = mysql_connect("182.72.63.18","fc_team_19","ZHDLvRxM24F4WGN2");
		  if (!$con)
		    {
		    die('Could not connect: ' . mysql_error());
		    }

		  mysql_select_db("fc_team_19", $con);



$check = mysql_query("select userid from users where matcheduserid='".$user_id."'");
		$row1 = mysql_fetch_array($check);
		if($row1)
		{
			$matchdetails1 = $facebook->api(array(
				    'method' => 'fql.query',
				    'query' => "select first_name from user where uid='".$row1['userid']."'"
			  ));

			  foreach ($matchdetails1 as $mat1) {
						$fnam1 = idx($mat1, 'first_name');
				}
				echo $fnam1."|".$row1['userid'];
				mysql_query("UPDATE users set matcheduserid='".$row1['userid']."' where userid='".$user_id."'");
		}

else
{
$arr=array();
$cnt=0;
$indx=0;

 $details = $facebook->api(array(
	    'method' => 'fql.query',
	    'query' => "select name,sex from user where uid='".$user_id."'"
  ));

 foreach ($details as $det) {
			$nam = idx($det, 'name');
			$sx = idx($det, 'sex');
}

		  if (!strcmp($sx,"male"))
		{

		  $result=mysql_query("SELECT userid from users where sex='female' and is_online=1");
		  while($row = mysql_fetch_array($result))
		  		   {
		  		   if(strcmp($row['userid'],$user_id))
		  		   {
		  		   $arr[$cnt]=$row['userid'];
		  		   $cnt++;
		  		   // echo $row['userid'];
		  		   // echo "<br />";
		  		   }
		   $indx = rand (0,$cnt-1);

		   }

		}

		else
		{
		  $result1=mysql_query("SELECT userid from users where sex='male' and is_online=1");
		  while($row = mysql_fetch_array($result1))
		  		   {
		  		   if(strcmp($row['userid'],$user_id))
		  		   {
		  		   $arr[$cnt]=$row['userid'];
		  		   $cnt++;
		  		   //echo $row['userid'];
		  		   //echo "<br />";
		  		   }
		  $indx = rand (0,$cnt-1);
		   }

		}
$matchuserid = $arr[$indx];
//echo $matchuserid;

 $matchdetails = $facebook->api(array(
	    'method' => 'fql.query',
	    'query' => "select first_name from user where uid='".$matchuserid."'"
  ));

  foreach ($matchdetails as $mat) {
			$fnam = idx($mat, 'first_name');
	}
	echo $fnam."|".$matchuserid;

	mysql_query("UPDATE users set matcheduserid='".$matchuserid."' where userid='".$user_id."'");

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


?>