<?php
Function SetRoom($room, $house, $type = NULL)
{
  $query = "INSERT INTO domoserv.room (room_name,house_name)" ;
  if (false === is_null($type){
    $query .= "VALUES (".$house.",".$house.");" ;
  } else {
    $query .= "VALUES (".$room.",".$house.");" ;
  }
  //$query .= "ON DUPLICATE KEY UPDATE ";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}

Function GetRoom($id = NULL, $room = NULL, $house = NULL)
{
  $query = "SELECT room_id,room_name,house_name FROM domoserv.room" ;
  if (false === is_null($id) or false === is_null($room) or false === is_null($house)){
    $query .= " WHERE";
  }
  if (false === is_null($id)){
    $temp = true;
	$query .= " room_id=".$id;
  }
  if (false === is_null($room)){
    if ($temp === true) {
	  $query .= " and";
	}
    $temp = true;
	$query .= " room_name=".$room;
  }
  if (false === is_null($house)){
    if ($temp === true) {
	  $query .= " and";
	}
    $temp = true;
	$query .= " house_name=".$house;
  }
  $query .= ";";
  $result = mysql_query($query) or die('Error, query '.$query.' failed. ' . mysql_error());
  return $result;
}
?>