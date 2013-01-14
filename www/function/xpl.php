<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Xpl-Sender</title>
</head>

<body>
<?php
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);
    
    $broadcast = "255.255.255.255";		// Broadcast address
    $port = 3865;						// xPL UDP assigned port
	$listenOnAddress="ANY_LOCAL";		// This must match the ListenOnAddress in the xPL network settings.
//	$listenOnAddress="192.168.0.3";
	$xPLSource = "domoserv-php.intranet";	// Identifies the source of the message (vendor-device.instance)

	$xPLType = $_GET['type'];
	$xPLTarget = $_GET['target'];
	$xPLSchema = $_GET['schema'];
	$xPLBody = $_GET['body'];
	
    if( !function_exists( 'socket_create' ) )
    {
		trigger_error( 'Sockets are not enabled in this version of PHP', E_USER_ERROR );
	}
	
	// create low level socket
	if( !$socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP ) )
	{
		trigger_error('Error creating new socket',E_USER_ERROR);		
	}

	// Set the socket to broadcast
	if( !socket_set_option( $socket, SOL_SOCKET, SO_BROADCAST, 1 ) )
	{
		trigger_error( 'Unable to set socket into broadcast mode', E_USER_ERROR );
	}
	
	// If the listenOnAddress is not set to ANY_LOCAL, we need to bind the socket (we can use any port).
	if( $listenOnAddress != "ANY_LOCAL" )
	{
		if( !socket_bind( $socket, $listenOnAddress, 0 ) )
		{
			trigger_error('Error binding socket to ListenOnAddress', E_USER_ERROR );
		}
	}

	// Send the message
	$msg = $xPLType."\n{\nhop=1\nsource=".$xPLSource."\ntarget=".$xPLTarget."\n}\n".$xPLSchema."\n{\n".$xPLBody."\n}\n";
	
    if( FALSE === socket_sendto( $socket, $msg, strlen($msg), 0, $broadcast, $port ) ) //Send the message
	{
		trigger_error('Failed to send message', E_USER_ERROR );
	}

	// We're done
	socket_close( $socket );
?>

</body>
</html>