<?php
require_once("./function/database.php");
require_once("./function/geolocation.php");
date_default_timezone_set('Europe/Paris');
$now = date("Y-m-d H:i:s");

// locating each user
for ($i=0;;$i++){
  if (!array_key_exists($i,$id_user)){
    break;
  }
  // acquire position of the user
  $url='https://latitude.google.com/latitude/apps/badge/api?user='.$id_user[$i].'&type=json';
  $latitude=file_get_contents($url);
  $json=json_decode($latitude,true);
  print $id_user[$i]."<br>";
  $latitude = $json[features][0][geometry][coordinates][0];
  $longitude = $json[features][0][geometry][coordinates][1];
  $location = $json[features][0][properties][reverseGeocode];
  $timestamp = $json[features][0][properties][timeStamp];
  $date = new DateTime();
  $datetimestamp = ($date->getTimestamp()-10*60);  //minus 10 min

  if ( $timestamp >= $datetimestamp) { 
    $lastdate= date('Y-m-d H:i:s',$timestamp);
    $UID= "(select user_id from domoserv.user where userlatitude=".$id_user[$i].")";
    $QIUT = "insert into domoserv.usertracking (user_id,longitude,latitude,timestamp) values (".$UID.",'".$longitude."','".$latitude."','".$lastdate."');";
    $IUT = mysql_query($QIUT) or die('Error, query '.$QIUT.' failed. ' . mysql_error());

    $dhome = distance($home_lat,$home_long,$latitude,$longitude,K); //K for kilometer, M for miles, N for nautic Miles

    if ( $dhome <= 1 ) { //test if the distance is in the range of 0 to 1 kilometer
      // at home
      IsHome ($UID, $lastdate, 1)
    } else {
      // not at home
      IsHome ($UID, $lastdate, 0)
    }
  }  
}

function IsHome ($UID, $lastdate, $state){
  $QSH = "select home from domoserv.userhome where user_id in ".$UID.";";
  $SH = mysql_query($QSH) or die('Error, query '.$QSH.' failed. ' . mysql_error());
  if (mysql_result($SH,0) == null) {
    $QUUH = "insert into domoserv.userhome (user_id,timestamp,home) values (".$UID.",'".$lastdate."',".$state.");";
    $UUH = mysql_query($QUUH) or die('Error, query '.$QUUH.' failed. ' . mysql_error());
  } else {
    $QUUH = "update domoserv.userhome set timestamp = '".$lastdate."', home = 0 where user_id in ".$UID."";
    $UUH = mysql_query($QUUH) or die('Error, query '.$QUUH.' failed. ' . mysql_error());
}

mysql_close();

?>
