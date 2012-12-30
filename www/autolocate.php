<?php
require_once("./function/database.php");
require_once("./function/geolocation.php");
require_once("./function/user.php");
date_default_timezone_set('Europe/Paris');
$now = date("Y-m-d H:i:s");

$GUL = GetUsersLatitude(1);

while($UL = mysql_fetch_object($GUL)){ 
  list($latitude, $longitude, $location, $timestamp) = GetUserPosition($UL->userlatitude);
  $date = new DateTime();
  $datetimestamp = ($date->getTimestamp()-2*60);  //minus 2 min

  if ( $timestamp >= $datetimestamp) { 
    $lastdate = date('Y-m-d H:i:s',$timestamp);
    $SUT = SetUserTracking($UL->user_id,$longitude,$latitude,$lastdate);
	$GULo = GetUserLocation($UL->user_id);
    while($ULo = mysql_fetch_object($GULo)){
      $dhome = distance($ULo->latitude,$ULo->longitude,$latitude,$longitude,$ULo->metric); 
      if ( $ULo->urange >= $dhome ) { 
        // at home
        SetIsHome ($UL->user_id, $lastdate, 1);
      } else {
        // not at home
        SetIsHome ($UL->user_id, $lastdate, 0);
      }
	}
  }  
}

mysql_close();

?>
