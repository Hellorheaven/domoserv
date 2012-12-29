<?php
require_once("./function/database.php");
require_once("./function/geolocation.php");
date_default_timezone_set('Europe/Paris');
$now = date("Y-m-d H:i:s");

// locating each user
$QSUL = "select user_id,userlatitude from domoserv.user where userlatitude is not null;";
$SUL = mysql_query($QSUL) or die('Error, query '.$QSUL.' failed. ' . mysql_error());

while($UL = mysql_fetch_object($SUL)){ 
  // acquire position of the user
  $url = 'https://latitude.google.com/latitude/apps/badge/api?user='.$UL->userlatitude.'&type=json';
  $latitude = file_get_contents($url);
  $json = json_decode($latitude,true);
  $latitude = $json[features][0][geometry][coordinates][0];
  $longitude = $json[features][0][geometry][coordinates][1];
  $location = $json[features][0][properties][reverseGeocode];
  $timestamp = $json[features][0][properties][timeStamp];
  $date = new DateTime();
  $datetimestamp = ($date->getTimestamp()-2*60);  //minus 2 min

  if ( $timestamp >= $datetimestamp) { 
    $lastdate = date('Y-m-d H:i:s',$timestamp);
    $QIUT = "insert into domoserv.usertracking (user_id,longitude,latitude,timestamp) values (".$UL->user_id.",'".$longitude."','".$latitude."','".$lastdate."');";
    $IUT = mysql_query($QIUT) or die('Error, query '.$QIUT.' failed. ' . mysql_error());
	$QSULLLR = "select latitude,longitude,range,metric from userlocation where user_id = ".$RUL->user_id.";";
    $SULLLR = mysql_query($QSULLLR) or die('Error, query '.$QSULLLR.' failed. ' . mysql_error());
    while($ULLLR = mysql_fetch_object($SULLLR)){
      $dhome = distance($ULLLR->latitude,$ULLLR->longitude,$latitude,$longitude,$ULLLR->metric); //K for kilometer, M for miles, N for nautic Miles
      if ( $dhome <= $ULLLR->range ) { //test if the distance is in the correct range
        // at home
        UpdateIsHome ($UL->user_id, $lastdate, 1);
      } else {
        // not at home
        UpdateIsHome ($UL->user_id, $lastdate, 0);
      }
	}
  }  
}

mysql_close();

?>
