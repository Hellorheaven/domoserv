<?php
// Two options for connecting to the database:
 
define('HOST_DIRECT', 'put_IP_adress'); // Standard connection
                                                  // Only username and password are encrypted
 
define('HOST_STUNNEL', '127.0.0.1');    // Secure connection, slower performance
                                        // All data is encrypted
                                        // Use '127.0.0.1' and not 'localhost'
 
define('DB_HOST', HOST_DIRECT);         // Choose HOST_DIRECT or HOST_STUNNEL, depending on your application's requirements
 
define('DB_USER', 'put_user_db');    // MySQL account username
define('DB_PASS', 'put_user_pass');    // MySQL account password
define('DB_NAME', 'put_db_name');     // Name of database
 
// Connect to the database
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
if(!$db) {
  // Handle error
  echo "<p>Unable to connect to database</p>";
}
?>
