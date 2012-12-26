<?php 
$file = fopen ("/surveillance/log/locate.log", 'r'); 
while(!feof($file))
   {
   print fgets($file). "<br />";
   }
 fclose($file);
?> 
