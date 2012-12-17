<?php
require_once("/etc/default/domoserv");
require_once("./bin/geolocation.php");
date_default_timezone_set('Europe/Paris');
$now = date("Y-m-d H:i:s");
//$filename = '/surveillance/log/locate.log';

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
  //print $timestamp."<br>";

  $date = new DateTime();
  $datetimestamp = ($date->getTimestamp()-10*60);  //minus 10 min
  //print $datetimestamp."<br>";
  
  //print $now."<BR>";
  if ( $timestamp >= $datetimestamp) {
    $lastdate= date('Y-m-d H:i:s',$timestamp);
	//print $lastdate;
	
	$dhome = distance($home_lat,$home_long,$latitude,$longitude,K) //K for kilometer, M for miles, N for nautic Miles
	
	if ( $dhome <= 1 ) { //test if the distance is in the range of 0 to 1 kilometer
	// at home
	
	} else {
	// not at home
	
	}
	
  }  
}

/*$somecontent = $now." user:".$_GET["user"]." location:".$_GET["location"]."\n";
// Let's make sure the file exists and is writable first.
 if (is_writable($filename)) {
    // we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'a')) {
         echo "Cannot open file ($filename)";
         exit;
    }
    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }
    echo "Success, wrote ($somecontent) to file ($filename)";
    fclose($handle);
} else {
    echo "The file $filename is not writable";
} */


?>
