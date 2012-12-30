<?php

// Section Latitude
function GetUsersLatitude($state){
  $query = "select user_id,userlatitude from domoserv.user where userlatitude ";
  if ($state == 1) {
    $query .= "is not null;";
  } else {
    $query .= "is null;";
  }
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result; 
}
?>