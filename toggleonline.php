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

			$con = mysql_connect("182.72.63.18","fc_team_19","ZHDLvRxM24F4WGN2");
		  if (!$con)
		    {
		    die('Could not connect: ' . mysql_error());
		    }

		  mysql_select_db("fc_team_19", $con);

		  mysql_query("update users set is_online=1 where userid='".$user_id."'");

		   mysql_close($con);






?>