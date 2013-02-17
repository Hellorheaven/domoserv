<?php

// Section Home

Function SetIsHome ($UID, $lastdate, $state){
  $query = "INSERT INTO domoserv.userhome (user_id,timestamp,home) VALUES (".$UID.",'".$lastdate."',".$state.")";
  $query .= " ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), timestamp = '".$lastdate."', home = ".$state.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function GetIsHome($UID){
  $query = "SELECT timestamp,home FROM domoserv.userhome WHERE user_id = ".$UID.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function GetHome($state){
  $query = "SELECT user_id,timestamp FROM domoserv.userhome WHERE home = ".$state.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function DeleteIshome($UID){
  $query = "DELETE FROM domoserv.userhome WHERE user_id=".$UID.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}



// Section UserLocation

Function GetGoogleUserLocation($address){
  $url = 'http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false';
  $google_geocode = file_get_contents($url);
  $json = json_decode($google_geocode,true);
  $Lat = $json[results][0][geometry][location][lat];
  $Lon = $json[results][0][geometry][location][lng];
  return array ($Lat,$Lon);
}

Function SetUserLocation($UID, $Lat, $Long, $range, $metric){
  $query = "INSERT INTO domoserv.userlocation (user_id,latitude,Longitude,urange,metric) VALUES (".$UID.",".$Lat.",".$Long.",".$range.",".$metric.")";
  $query .= " ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), latitude=".$Lat.",Longitude=".$Long.",urange=".$range.",metric=".$metric.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function GetUserlocation($UID){
  $query = "SELECT latitude,Longitude,urange,metric FROM domoserv.userlocation WHERE user_id = ".$UID.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function DeleteUserLocation($UID){
  $query = "DELETE FROM domoserv.userlocation WHERE user_id=".$UID.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function UserLocation($UID, $address, $range, $metric){
  list ($Lat,$Lon) = GetGoogleUserLocation($address);
  SetUserLocation($UID,$Lat,$Lon,$range,$metric);
}


// Section user position

Function GetUserPosition($userlatitude){
  $url = 'https://latitude.google.com/latitude/apps/badge/api?user='.$userlatitude.'&type=json';
  $google_latitude = file_get_contents($url);
  $json = json_decode($google_latitude,true);
  $latitude = $json[features][0][geometry][coordinates][0];
  $longitude = $json[features][0][geometry][coordinates][1];
  $location = $json[features][0][properties][reverseGeocode];
  $timestamp = $json[features][0][properties][timeStamp];
  return array ($latitude, $longitude, $location, $timestamp);
}

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::    unit = the unit you desire for$results                               :*/
/*::           WHERE: 'M' is statute miles                                   :*/
/*::                  'K' is kilometers                                      :*/
/*::                  'N' is nautical miles                                  :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/

Function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

// Section usertracking

Function SetUserTracking($UID, $longitude, $latitude, $lastdate){
  $query = "INSERT INTO domoserv.usertracking (user_id,longitude,latitude,timestamp) VALUES (".$UID.",'".$longitude."','".$latitude."','".$lastdate."');";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}



?>