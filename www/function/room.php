<?php
function SetRoom($room, $house, $type = NULL)
{
  $query = "INSERT INTO domoserv.camip (room_name,house_name)" ;
  if ($type == NULL){
  $query .= "VALUES (".$room.",".$house.");" ;
  } else {
  $query .= "VALUES (".$house.",".$house.");" ;
  }
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}



?>