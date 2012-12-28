<?php
require_once("./function/database.php");
require_once("./function/geolocation.php");
date_default_timezone_set('Europe/Paris');
$now = date("Y-m-d H:i:s");

// locating each user
$QUL = "select userlatitude from domoserv.user where userlatitude is not null";
$UL = mysql_query($QUL) or die('Error, query '.$QUL.' failed. ' . mysql_error());

while($RUL = mysql_fetch_object($UL)){ 
  // acquire position of the user
  $url = 'https://latitude.google.com/latitude/apps/badge/api?user='.$RUL->userlatitude.'&type=json';
  $latitude = file_get_contents($url);
  $json = json_decode($latitude,true);
  print $id_user[$i]."<br>";
  $latitude = $json[features][0][geometry][coordinates][0];
  $longitude = $json[features][0][geometry][coordinates][1];
  $location = $json[features][0][properties][reverseGeocode];
  $timestamp = $json[features][0][properties][timeStamp];
  $date = new DateTime();
  $datetimestamp = ($date->getTimestamp()-10*60);  //minus 10 min

  if ( $timestamp >= $datetimestamp) { 
    $lastdate = date('Y-m-d H:i:s',$timestamp);
    $UID = "(select user_id from domoserv.user where userlatitude=".$RUL->userlatitude.")";
    $QIUT = "insert into domoserv.usertracking (user_id,longitude,latitude,timestamp) values (".$UID.",'".$longitude."','".$latitude."','".$lastdate."');";
    $IUT = mysql_query($QIUT) or die('Error, query '.$QIUT.' failed. ' . mysql_error());

    $dhome = distance($home_lat,$home_long,$latitude,$longitude,K); //K for kilometer, M for miles, N for nautic Miles

    if ( $dhome <= 1 ) { //test if the distance is in the range of 0 to 1 kilometer
      // at home
      UpdateIsHome ($UID, $lastdate, 1);
	  echo "at home";
    } else {
      // not at home
      UpdateIsHome ($UID, $lastdate, 0);
	  echo "not at home";
    }
  }  
}

mysql_close();

?>
