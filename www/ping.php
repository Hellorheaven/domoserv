<?php 

//Config information
$email = "your@emailaddress.com";
$server = "google.com"; //the address to test, without the "http://"
$port = "80";


//Create a text file to store the result of the ping for comparison
$db = "pingdata.txt";

if (file_exists($db)):
    $previous_status = file_get_contents($db, true);
else:
    file_put_contents($db, "up");
    $previous_status = "up";
endif;

//Ping the server and check if it's up
$current_status =  ping($server, $port, 10);

//If it's down, log it and/or email the owner
if ($current_status == "down"):

    echo "Server is down! ";
    file_put_contents($db, "down");

    if ($previous_status == "down"):
        mail($email, "Server is down", "Your server is down.");
        echo "Email sent.";     
    endif;  

else:

    echo "Server is up! ";
    file_put_contents($db, "up");

    if ($previous_status == "down"):
        mail($email, "Server is up", "Your server is back up.");
        echo "Email sent.";
    endif;

endif;


function ping($host, $port, $timeout)
{ 
  $tB = microtime(true); 
  $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  if (!$fP) { return "down"; } 
  $tA = microtime(true); 
  return round((($tA - $tB) * 1000), 0)." ms"; 
}
