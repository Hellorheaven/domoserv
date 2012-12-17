<?php
function notify($source, $title, $message, $url = NULL, $backend = 'https://notifrier.appspot.com/notifry')
{
  $params = array();
  $params['source'] = $source;
  $params['message'] = $message;
  $params['title'] = $title;
  $params['format'] = 'json';
  if( false === is_null($url) )
  {
    $params['url'] = $url;
  }
	
  $encodedParameters = array();
  foreach( $params as $key => $value )
  {
    $encodedParameters[] = $key . "=" . urlencode($value);
  }
  $body = implode("&", $encodedParameters);

  // Using CURL, send the request to the server.
  $c = curl_init($backend);
  curl_setopt($c, CURLOPT_POST, true);
  curl_setopt($c, CURLOPT_POSTFIELDS, $body);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
  $page = curl_exec($c);

  // Parse the result.
  $result = array('success' => false);
  if( $page !== FALSE )
  {
    // The result is JSON encoded.
    $decoded = json_decode($page, TRUE);
    if( $decoded === FALSE )
    {
      $result['error'] = "Failed to decode server response: " . $page;
    }
    else
    {
      if( isset($decoded['error']) )
      {
        $result['error'] = $decoded['error'];
      }
      else
      {
        $result['success'] = true;
        $result['message'] = "Success! Message size " . $decoded['size'];
      }
    }
  }
  else
  {
    $result['error'] = curl_error($c);
  }

  curl_close($c);

  return $result;
}


?>
