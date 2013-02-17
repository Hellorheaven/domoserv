<?php
function CamCommand($camid, $command, $step = NULL, $deg = NULL )
{
  $RCam = GetCamIP($camid);
  while($Cam = mysql_fetch_object($RCam)){
    $backend = 'http://'.$Cam->host.':'.$Cam->port.'/decoder_control.cgi';
    $host = $Cam->host;
	$params = array();
    $params['command'] = $command;
    if( false === is_null($step) )
    {
      $params['onestep'] = $step;
	  print $step."<br>";
    }
    if( false === is_null($deg) )
    {
      $params['degree'] = $deg;
      print $step."<br>";
    }
    $params['user'] = $Cam->user;
    $params['pwd'] = base64_decode($Cam->pwd);	
  }

  $encodedParameters = array();
  foreach( $params as $key => $value )
  {
    $encodedParameters[] = $key . "=" . urlencode($value);
  }
  $body = implode("&", $encodedParameters);
  $url=$backend.'?'.$body;


  // Using CURL, send the request to the server.
  $c = curl_init($url);
  $page = curl_exec($c);

  // Parse the result.
  $result = array('success' => false);
  if( $page !== FALSE )
  {
    date_default_timezone_set('Europe/Paris');
    $now = date("Y-m-d H:i:s");  
    $result['success'] = true;
    $result['message'] = $now.": Success in using command ".$command." on host ".$host."\n" ;
  }
  else
  {
    $result['error'] = curl_error($c);
  }
  curl_close($c);
  return $result;
}

Function GetCamIP($CID){
  $query = "select host,port,user,pwd from domoserv.camip where camip_id = ".$CID.";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function SetCamIP($room, $type, $name, $host, $port = 80, $user, $pass, $alarm = 0){
  $pwd=base64_encode($pass);
  $query = "Insert into domoserv.camip (room_id,type_id,name,host,port,user,pwd,alarm)" ;
  $query .= "values (".$room.",".$type.",".$name.",".$host.",".$port.",".$user.",".$pwd.",".$alarm.");" ;
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

?>