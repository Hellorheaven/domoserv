<?php
function CamCommand($camera, $command, $step = NULL, $deg = NULL )
{
  $iniCam = parse_ini_file('/etc/default/domoserv', $camera );
  $backend = 'http://'.$iniCam[$camera]['ip'].':'.$iniCam[$camera]['port'].'/decoder_control.cgi';
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
  $params['user'] = $iniCam[$camera]['user'];
  $params['pwd'] = $iniCam[$camera]['pwd'];	
	
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
    $result['success'] = true;
    $result['message'] = "Success! ".$url;
  }
  else
  {
    $result['error'] = curl_error($c);
  }

  curl_close($c);

  return $result;

}

?>