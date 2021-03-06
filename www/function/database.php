<?php

$iniDomoserv = parse_ini_file('/etc/default/domoserv', 'database');


// Two options for connecting to the database:
 
define('HOST_DIRECT', $iniDomoserv['database']['dbhost']); // Standard connection
                                                  // Only username and password are encrypted
 
define('HOST_STUNNEL', '127.0.0.1');    // Secure connection, slower performance
                                        // All data is encrypted
                                        // Use '127.0.0.1' and not 'localhost'
 
define('DB_HOST', HOST_DIRECT);         // Choose HOST_DIRECT or HOST_STUNNEL, depending on your application's requirements
 
define('DB_USER', $iniDomoserv['database']['dbuser']);    // MySQL account username
define('DB_PASS', $iniDomoserv['database']['dbpass']);    // MySQL account password
define('DB_NAME', $iniDomoserv['database']['dbname']);     // Name of database
define('DB_PORT',  $iniDomoserv['database']['dbport']);     // port of mysql server
 
// Connect to the database
$db = mysql_connect(DB_HOST.":".DB_PORT, DB_USER, DB_PASS, DB_NAME);
 
if(!$db) {
  // Handle error
  echo "<p>Unable to connect to database</p>";
}


?>
